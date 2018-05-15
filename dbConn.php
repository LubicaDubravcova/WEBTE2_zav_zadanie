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
	
	function truncAll() {
		$this->db->query("SET FOREIGN_KEY_CHECKS=0");
		$this->db->query("TRUNCATE `schools`");
		$this->db->query("TRUNCATE `users`");
		$this->db->query("SET FOREIGN_KEY_CHECKS=1");
	}
	
    function exists($email) {
		$stmt = $this->db->prepare("SELECT id FROM $this->userTable WHERE email = ?");
	
		if ($stmt === false) {
		  trigger_error($this->db->error, E_USER_ERROR);
		  return;
		}

		$stmt->bind_param('i',$email);
		
		$stmt->execute();
		$stmt->store_result();
		$alreadyExists = ($stmt->num_rows != 0);
		$stmt->close();
		return $alredyExists;
	}
	
	function register($data = array()) {
		if($this->exists($data["email"])) 
			return false;
		$stmt = $this->db->prepare("INSERT INTO $this->userTable VALUES (NULL,?,?,?,?,?,?)");
	
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
	}
	
	function login($data = array()) {
		$stmt = $this->db->prepare("SELECT * FROM $this->userTable WHERE email = ?");
	
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
		if (strcmp($result["password"],hash('sha256',$data["password"]))) {
			return $result;
		}
		return false;
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
	
	function loadCSV($fileName) {
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
			$this->register($user);
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