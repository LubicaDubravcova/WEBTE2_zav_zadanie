<?php
$defaultPass = "defaultPass1234";
class DBConn {
    private $dbHost = "localhost";
    private $userTable = "users";
	private $dbName = "zaverecne";
	private $dbLogin = "batman";
	private $dbPass = "robin";
    private $db; //pripojenie cez mysqli, na ziskavanie dat pouzivajte toto
	
    function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbLogin, $this->dbPass, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
				$conn->set_charset("utf8");
                $this->db = $conn;
			}
        }
	}
	
	function __destruct(){
        $this->db->close();
	}
	
	private function removeConscriptionNumber($address){
		$slashPos = strrpos($address, "/");
		if ($slashPos === false) return $address;
		return substr($address,0,strrpos($address, " "))." ".substr($address,$slashPos+1);
	}
	
	function getDB() {
		return $this->db;
	}
	
    function exists($email) {
		$stmt = $this->db->prepare("SELECT id FROM $this->userTable WHERE email = ?");
	
		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('s',$email);
		
		$stmt->execute();
		$stmt->store_result();
		$alreadyExists = ($stmt->num_rows != 0);
		$stmt->close();
		return $alreadyExists;
	}
	
	function isAdmin(){
		if (!isset($this->admin)) {
			if ($this->getUserData()["role"] == "admin")
				$this->admin = true;
			else $this->admin = false;
		}
		return $this->admin;
	}

	function register($data = array(),$autoconfirm = false) {
		if($this->exists($data["email"]))
			return false;
		if ($autoconfirm) $date = "NULL";
		else $date = date("Y-m-d H:i:s"); ;
		$stmt = $this->db->prepare("INSERT INTO $this->userTable VALUES (NULL,?,?,?,?,?,?,'user',false,'$date',NULL)");

		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		if ($data["schoolID"] == "") 
			$data["schoolID"] = $this->addSchool($data["schoolName"],$this->getAddressID($data["schoolCity"],$data["schoolPSC"],$data["schoolAddress"]));
		$address = $this->getAddressID($data["city"],$data["PSC"],$data["address"]);
		$stmt->bind_param('sssiis', $data["firstname"], $data["surname"], $data["email"], $data["schoolID"], $address, hash('sha256',$data["password"]));

		/* Execute the prepared Statement */
		$status = $stmt->execute();
		/* BK: always check whether the execute() succeeded */
		if ($status === false) {
		  trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();
		return $date;
	}
	
	function getUserData() {
		$stmt = $this->db->prepare("SELECT * FROM $this->userTable WHERE id = ?");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}

		$stmt->bind_param('i',$_SESSION["login"]["id"]);

		/* BK: always check whether the execute() succeeded */
		if ($stmt->execute() === false) {
		  trigger_error($stmt->error, E_USER_ERROR);
		}
		$result = $stmt->get_result()->fetch_object();
		$stmt->close();
		if (strcmp($result->PASSWORD,$_SESSION["login"]["password"])==0) {
			return $result;
		}
		return false;
	}
	
	function login($data) {
		$stmt = $this->db->prepare("SELECT id, password, confirmtime FROM $this->userTable WHERE email = ?");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}

		$stmt->bind_param('s',$data["email"]);

		/* BK: always check whether the execute() succeeded */
		if ($stmt->execute() === false) {
		  trigger_error($stmt->error, E_USER_ERROR);
		}
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		if (!is_null($result["confirmtime"])) return true;
		if (strcmp($result["password"],hash('sha256',$data["password"])) == 0) {
			return $result;
		}
		return false;
	}
	
	function activate($data){
		$stmt = $this->db->prepare("UPDATE $this->userTable SET confirmtime = NULL WHERE email = ? AND confirmtime = ?");
	
		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('ss',$data["email"],$data["timestamp"]);
		
		$stmt->execute();
		$stmt->store_result();
		$success = ($stmt->affected_rows != 0);
		$stmt->close();
		return $success;
	}
	
	function loadSchools($file) { //Load schools into DB, single use only
		$csv = new CsvImporter($file, true);
		
		$csv->customHeader(array("name","city","address","psc"));
		$data = $csv->get();
		foreach ($data as $school) {
			$this->getSchoolID($school);
		}
	}
	
	function getAssoc($query, $column = false, $singleLine = false) { //Get assoc from DB based on query, params: $query - query to execute, $column - returns single column as an array, $singleLine - returns single row, if combined with $column, returns single value
		$result = $this->db->query($query);

		if ($result === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		if ($singleLine) {
			if ($column)
				return $result->fetch_assoc()[$column];
			$result->fetch_assoc();
		}
		if ($column)
			return array_column($result->fetch_all(MYSQLI_ASSOC),$column);
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	function insertQuery($query){
		$result = $this->db->query($query);
	}
	
	function getSchoolID($school) { //get id of specified school, if it doesnt exist, add new.
		$stmt = $this->db->prepare("SELECT id FROM schools WHERE ADDRESS_ID = ?");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		$addressID = $this->getAddressID($school["city"],$school["psc"],$school["address"]);
		$stmt->bind_param('i', $addressID);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		
		if ($result["id"] != NULL) 
			return $result["id"];
		
		//----------------------------- if it doesnť exist
		return $this->addSchool($school["name"],$addressID);
	}
	
	private function addSchool($name, $addressID) { //pouzivat len v pripade ze skola neexistuje, nekontroluje existenciu skoly, lebo ta sa kontroluje pri getSchoolID
		$stmt = $this->db->prepare("INSERT INTO schools VALUES (NULL,?,?)");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		
		$stmt->bind_param('si', $name, $addressID);

		/* Execute the prepared Statement */
		$status = $stmt->execute();
		/* BK: always check whether the execute() succeeded */
		if ($status === false) {
		  trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();
		return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
	}

    function createTeam($routeID) { // vytvori team a vrati jeho ID
        //TODO pridat na vstup trasu a vlozit ju ked bude trasa dorobena (momentalne nic take neexistuje)
        $stmt = $this->db->prepare("INSERT INTO teams VALUES (NULL,?)"); //TODO miesto 1 dat "?"

        if ($stmt === false) {
            trigger_error($this->db->error, E_USER_ERROR);
            return;
        }

        $stmt->bind_param('i', $routeID);

        /* Execute the prepared Statement */
        $status = $stmt->execute();
        /* BK: always check whether the execute() succeeded */
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        $stmt->close();
        return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
    }

    function addToTeam($userID, $teamID){      // prida pouzivatela do timu

        $stmt = $this->db->prepare("INSERT INTO users_teams VALUES (NULL,?,?)");

        if ($stmt === false) {
            trigger_error($this->db->error, E_USER_ERROR);
            return;
        }

        $stmt->bind_param('ii', $userID, $teamID);

        /* Execute the prepared Statement */
        $status = $stmt->execute();
        /* BK: always check whether the execute() succeeded */
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        $stmt->close();
        return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
    }

    function dropTeamMembers($teamID){      // prida pouzivatela do timu

        $stmt = $this->db->prepare("DELETE FROM users_teams WHERE TEAM_ID=?");

        if ($stmt === false) {
            trigger_error($this->db->error, E_USER_ERROR);
            return;
        }

        $stmt->bind_param('i',$teamID);

        /* Execute the prepared Statement */
        $status = $stmt->execute();
        /* BK: always check whether the execute() succeeded */
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        $stmt->close();
        return true;
    }

	private function findAddress($psc, $address) {
		$stmt = $this->db->prepare("SELECT id FROM addresses WHERE PSC = ? AND address LIKE CONCAT('%',?)");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		
		$address=$this->removeConscriptionNumber($address);
		
		$stmt->bind_param('is',$psc, $address);
		
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $result["id"];
	}
	
	function getAddressID($city, $psc, $address) { //get address, if doesnt exist, insert
		if (($ret = $this->findAddress($psc, $address)) != NULL)
			return $ret; //if exists, return ID, else insert and return ID
		$stmt = $this->db->prepare("INSERT INTO addresses VALUES (NULL,?,?,?,?,?)");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		$loc = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDnIZzek1rOnBI-s3zMr68zd9QMCZiMLrw&address=".urlencode($address.", ".$city)));
		$stmt->bind_param('ssidd', $city, $this->removeConscriptionNumber($address), $psc, $loc->results[0]->geometry->location->lat, $loc->results[0]->geometry->location->lng);

		/* Execute the prepared Statement */
		$status = $stmt->execute();
		/* BK: always check whether the execute() succeeded */
		if ($status === false) {
		  trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();
		return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
	}

	function createRoute($name, $path, $type, $userFK, $length) {
		$stmt = $this->db->prepare("INSERT INTO routes VALUES (NULL,?,?,?,?,?)");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('sdsii', $path, $length, $name, $type, $userFK);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();

		return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
	}

	function getRouteData($routeID) {
    	$stmt = $this->db->prepare("SELECT PATH, LENGTH, NAME, TYPE, OWNER FROM routes WHERE ID = ?");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('i', $routeID);
		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		return $result;
	}

	// nastavy aktualne prihlasenemu pouzivatelovi trasu so zadanym ID ako aktivnu
	function setActiveRoute($routeID) {
    	// ziskat user ID
		$userData = $this->getUserData();

		$stmt = $this->db->prepare("UPDATE users SET ACTIVE_ROUTE = ? WHERE ID = ?");
		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('ii', $routeID, $userData->ID);
		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();

		return $userData->ID;
	}

	function addTraining($length, $date = null, $time_start = null, $time_end = null, $lat_start = null, $lng_start = null, $lat_end = null, $lng_end = null, $rating = null, $note = null, $userID, $routeID) {
		$stmt = $this->db->prepare("INSERT INTO `trainings`VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?)");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('dsssddddisii', $length, $date, $time_start, $time_end, $lat_start, $lng_start, $lat_end, $lng_end, $rating, $note, $userID, $routeID);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$stmt->close();

		return $this->db->query("SELECT LAST_INSERT_ID();")->fetch_array()[0];
	}

	function loadCSV($fileName) {
		if (!$this->isAdmin()) return false;
		$csv = new CsvImporter($fileName, true);
		$csv->customHeader(array("id","firstname","surname","email","schoolname","schooladdress","address","psc","city"));
		$data = $csv->get();
		foreach ($data as $user) {
			$address = explode(", ",$user["schooladdress"]);
			$school = array(
				"city" => $address[0],
				"address" => $address[1],
				"psc" => $address[2],
				"name" => $user["schoolname"]
			);
			$user["school"]=$this->getSchoolID($school);
			$user["password"]=hash('sha256',$defaultPass);
			$this->register($user, true);
		}
	}
}

class CsvImporter 
{ 
    private $fp; 
    private $parse_header; 
    private $header; 
    private $delimiter; 
    private $length; 
	
    function __construct($file_name, $parse_header=false, $delimiter=";", $length=8000) 
    {
        $this->fp = fopen($file_name, "r"); 
        $this->parse_header = $parse_header; 
        $this->delimiter = $delimiter; 
        $this->length = $length; 
        $this->lines = $lines; 

        if ($this->parse_header) 
        { 
           $this->header = fgetcsv($this->fp, $this->length, $this->delimiter); 
        } 

    } 
	
    function __destruct() 
    { 
        if ($this->fp) 
        { 
            fclose($this->fp); 
        } 
    } 
	
    function get($max_lines=0) 
    { 
        //if $max_lines is set to 0, then get all the data 

        $data = array(); 

        if ($max_lines > 0) 
            $line_count = 0; 
        else 
            $line_count = -1; // so loop limit is ignored 

        while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE) 
        {
			//$row = array_map("utf8_encode", $row);
            if ($this->parse_header) 
            { 
                foreach ($this->header as $i => $heading_i) 
                { 
                    $row_new[$heading_i] = $row[$i]; 
                } 
                $data[] = $row_new; 
            } 
            else 
            { 
                $data[] = $row; 
            } 

            if ($max_lines > 0) 
                $line_count++; 
        } 
        return $data; 
    }
	
	function customHeader($keys){
		$this->header = $keys;
	}
} 
?>
