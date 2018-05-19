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
			$data = $this->getUserData();
			if ($data->ROLE == "admin")
				$this->admin = true;
			else $this->admin = false;
		}
		return $this->admin;
	}

	function register($data = array(),$autoconfirm = false) {
		if($this->exists($data["email"]))
			return false;
		if ($autoconfirm) $date = null;
		else $date = date("Y-m-d H:i:s"); ;
		$stmt = $this->db->prepare("INSERT INTO $this->userTable VALUES (NULL,?,?,?,?,?,?,'user',false,?,NULL)");

		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		if ($data["schoolID"] == "") 
			$data["schoolID"] = $this->addSchool($data["schoolName"],$this->getAddressID($data["schoolCity"],$data["schoolPSC"],$data["schoolAddress"]));
		
		$address = $this->getAddressID($data["city"],$data["PSC"],$data["address"]);
		$stmt->bind_param('sssiiss', $data["firstname"], $data["surname"], $data["email"], $data["schoolID"], $address, hash('sha256',$data["password"]),$date);

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
		session_start();
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
	
	function getResult($query) {
		$result = $this->db->query($query);

		if ($result === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}
		return $result;
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
        $stmt = $this->db->prepare("INSERT INTO teams VALUES (NULL,?)");

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

    function deleteTeam($teamID) { // deletne team a vrati TRUE ak bol uspesny
        $stmt = $this->db->prepare("DELETE FROM teams WHERE ID=?");

        if ($stmt === false) {
            trigger_error($this->db->error, E_USER_ERROR);
            return;
        }

        $stmt->bind_param('i', $teamID);

        /* Execute the prepared Statement */
        $status = $stmt->execute();
        /* BK: always check whether the execute() succeeded */
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        } else{
            $stmt->close();
            return true;
		}
    }

    function addToTeam($userID, $teamID){      // prida pouzivatela do timu

        $stmt = $this->db->prepare("INSERT INTO users_teams VALUES (?,?)");

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

	// nastavy userovy aktivnu trasu na NULL
	function resetActiveRouteForUser($userId) {
		$stmt = $this->db->prepare("UPDATE `users` SET `ACTIVE_ROUTE`= NULL WHERE `ID` = ?");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param("i",$userId);
		$stmt->execute();
		$stmt->close();
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

	// vrati celkovu vzdialenost (v metroch) odjazdenu majtelom trasy pre zadanu (private) trasu
	function getPrivateRouteProgress($routeId) {
    	$stmt = $this->db->prepare("SELECT `routes`.`ID` as RID, `routes`.`TYPE` as TYPE, SUM(`trainings`.`LENGTH`) as LENGTH FROM `routes` JOIN `trainings` ON `routes`.`ID` = `trainings`.`ROUTE_ID` WHERE `routes`.ID = ? AND `trainings`.`USER_ID` = (SELECT `routes`.`OWNER` WHERE `routes`.`ID` = ?) GROUP BY `routes`.`ID`");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		// ano je tam 2x to iste ID, lebo raz je pre vyber trasy a raz v sub-query pre vyber jej majtela
		$stmt->bind_param('ii', $routeId, $routeId);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		return $result;
	}

	// vrati pole obsahujuce spojene NAME, id pouzivatela UID a LENGTH nim prejdenu vzdialenost pre zadanu (public) trasu
	// zaznamy su usporiadane podla LENGTH zostupne (descending)
	// POZOR! pole je indexovane netypicky! pole[STLPEC][RIADOk] (viac mi to tak vyhovuje pri praci s nim)
	function getPublicRouteProgress($routeId) {
		$stmt = $this->db->prepare("SELECT CONCAT(`users`.`FIRSTNAME`,\" \", `users`.`SURNAME`) AS NAME, `users`.`ID` as UID, SUM(`trainings`.`LENGTH`) AS LENGTH FROM `trainings` JOIN `users` ON `trainings`.`USER_ID` = `users`.`ID` WHERE `trainings`.`ROUTE_ID` = ? GROUP BY `users`.`ID` ORDER BY LENGTH DESC");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('i', $routeId);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}

		$querryResult = $stmt->get_result();

		$row = $querryResult->fetch_assoc();

		$result = null;

		// nacitam vsetky vysledky
		if($row != false) {

			$result = array(
				"NAME" => array(),
				"UID" => array(),
				"LENGTH" => array()
			);

			array_push($result["NAME"], $row["NAME"]);
			array_push($result["UID"], $row["UID"]);
			array_push($result["LENGTH"], $row["LENGTH"]);

			while(($row = $querryResult->fetch_assoc()) != false) {
				array_push($result["NAME"], $row["NAME"]);
				array_push($result["UID"], $row["UID"]);
				array_push($result["LENGTH"], $row["LENGTH"]);
			}
		}

		$stmt->close();

		return $result;
	}
	function getAllowedRoutes($userID){
		$stmt = $this->db->prepare("SELECT active.ID FROM ((SELECT routes.ID, routes.LENGTH, COALESCE(SUM(trainings.LENGTH),0) AS RUN FROM `routes` LEFT JOIN trainings ON routes.ID = trainings.ROUTE_ID WHERE routes.TYPE = 'súkromná' AND routes.OWNER = ? AND (trainings.USER_ID = routes.OWNER OR trainings.USER_ID IS NULL) GROUP BY routes.ID) 
        		UNION (SELECT routes.ID, routes.LENGTH, COALESCE(SUM(trainings.LENGTH),0) AS RUN FROM `routes` LEFT JOIN trainings ON routes.ID = trainings.ROUTE_ID WHERE routes.TYPE = 'verejná' AND (trainings.USER_ID = ? OR ? NOT IN (SELECT USER_ID FROM trainings WHERE ROUTE_ID = routes.ID)) GROUP BY routes.ID)
                UNION (SELECT o.ROUTE_ID, o.LENGTH, p.RUN FROM (SELECT teams.ID as TEAM_ID, teams.ROUTE_ID, routes.LENGTH FROM routes INNER JOIN teams ON teams.ROUTE_ID = routes.ID INNER JOIN users_teams ON teams.ID = users_teams.TEAM_ID WHERE users_teams.USER_ID = ?) o INNER JOIN (SELECT COALESCE(SUM(trainings.LENGTH),0) AS RUN, users_teams.TEAM_ID FROM trainings RIGHT JOIN users_teams on trainings.USER_ID = users_teams.USER_ID GROUP BY users_teams.TEAM_ID) p ON o.TEAM_ID = p.TEAM_ID)) AS active WHERE active.RUN < active.LENGTH");
		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('iii', $userID, $userID, $userID);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}

		$array = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		
		$stmt->close();
		return array_column($array,"ID");
	}
	// vrati pole obsahujuce mena MEMBERS, id teamu TID a LENGTH nim prejdenu vzdialenost pre zadanu (stafetovu) trasu
	// zaznamy su usporiadane podla LENGTH zostupne (descending)
	// POZOR! pole je indexovane netypicky! pole[STLPEC][RIADOk] (viac mi to tak vyhovuje pri praci s nim)
	function getRelayRouteProgress($routeId) {
		$stmt = $this->db->prepare("SELECT users_teams.TEAM_ID AS TID, GROUP_CONCAT(users.FIRSTNAME, \" \", users.SURNAME SEPARATOR \", \") AS MEMBERS, COALESCE(lengths.lng, 0) AS LENGTH FROM teams
JOIN users_teams ON users_teams.TEAM_ID=teams.ID
JOIN users ON users.ID=users_teams.USER_ID
LEFT JOIN 	(SELECT users_teams.TEAM_ID, COALESCE(SUM(trainings.LENGTH), 0) AS lng FROM trainings
		JOIN routes ON (routes.ID=trainings.ROUTE_ID AND routes.TYPE=\"Štafeta\")
		JOIN users_teams ON users_teams.USER_ID=trainings.USER_ID
      	WHERE trainings.ROUTE_ID=?
		GROUP BY TEAM_ID) AS lengths ON lengths.TEAM_ID=teams.ID
WHERE teams.ROUTE_ID=?
GROUP BY teams.ID ORDER BY LENGTH DESC");
		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('ii', $routeId, $routeId);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}

		$querryResult = $stmt->get_result();

		$row = $querryResult->fetch_assoc();

		$result = null;

		// nacitam vsetky vysledky
		if($row != false) {

			$result = array(
				"MEMBERS" => array(),
				"TID" => array(),
				"LENGTH" => array()
			);

			array_push($result["MEMBERS"], $row["MEMBERS"]);
			array_push($result["TID"], $row["TID"]);
			array_push($result["LENGTH"], $row["LENGTH"]);

			while(($row = $querryResult->fetch_assoc()) != false) {
				array_push($result["MEMBERS"], $row["MEMBERS"]);
				array_push($result["TID"], $row["TID"]);
				array_push($result["LENGTH"], $row["LENGTH"]);
			}
		}

		$stmt->close();

		return $result;
	}

	// vrati ID timu daneho pouzivatela pre danu trat, ak trat nie je stafetova vrati prazdny querry
	function getUserTeam($userId, $routeId) {
		$stmt = $this->db->prepare("SELECT `teams`.`ID`  FROM `teams` JOIN `users_teams` ON `users_teams`.`TEAM_ID` = `teams`.`ID` WHERE `teams`.`ROUTE_ID` = ? AND `users_teams`.`USER_ID` = ?");

		if ($stmt === false) {
			trigger_error($this->db->error, E_USER_ERROR);
			return;
		}

		$stmt->bind_param('ii', $routeId, $userId);

		$status = $stmt->execute();
		if($status === false) {
			trigger_error($stmt->error, E_USER_ERROR);
		}
		$result = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		return $result;
	}

	function loadCSV($fileName) {
		if (!$this->isAdmin()) return false;
		$csv = new CsvImporter($fileName, true);
		$csv->customHeader(array("id","surname","firstname","email","schoolname","schooladdress","address","PSC","city"));
		$data = $csv->get();
		foreach ($data as $user) {
			$address = explode(", ",$user["schooladdress"]);
			$school = array(
				"city" => $address[0],
				"address" => $address[1],
				"psc" => $address[2],
				"name" => $user["schoolname"]
			);
			$user["schoolID"]=$this->getSchoolID($school);
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
