<?php
/* Created on Feb 4, 2006 */
//$dbh=null;
$dbconfig=array("server"=>"www.randallknives.com",
				"dbh"=>null,
				"username"=>"uplzcvgw_rmkweb",
				"password"=>"rmkskeet",
				"webDatabase"=>"uplzcvgw_rmk",
	);

setDB_Globals();
	
function getDbRecords($query){
	global $dbconfig;
	$dbh=getDBConnection();
	mysql_select_db ($dbconfig['webDatabase']); 
	
	$results = array();
	$dbresults = mysql_query($query);
	if($dbresults == null)
		return $results;
	$num = mysql_num_rows($dbresults);
//	echo debugStatement($num . $query);
	for($i=0; $i<$num; $i++){
		$results[$i] = mysql_fetch_array($dbresults, MYSQL_ASSOC);
	}
	return $results;	
} 

function getIntFromDB($query){
	$record = getSingleDbRecord($query);
	$keys = array_keys($record);
	return $record[$keys[0]];
}

function getBasicSingleDbRecord($tableName, $keyField, $keyValue){
	$query = "Select * from " . $tableName . " where $keyField=".$keyValue;
	return getSingleDbRecord($query);
}

function getSingleDbRecord($query){
	global $dbconfig;
	$dbh=getDBConnection();
//	dumpDB_ConnData();
	mysql_select_db ($dbconfig['webDatabase']); 
	
	$results = array();
	$dbresults = mysql_query($query);
	if($dbresults == null)
		return $results;
	if(mysql_num_rows($dbresults) >0){
		return mysql_fetch_array($dbresults, MYSQL_ASSOC);
	}
}

function getDBConnection(){
	global $dbconfig;	
	
	if($dbconfig['dbh'] == null)
		$dbconfig['dbh']=mysql_connect ($dbconfig['server'], $dbconfig['username'], $dbconfig['password'], MYSQL_CLIENT_SSL) or die ('I cannot connect to the database because: ' . mysql_error());
//		$dbh=mysql_connect ("randallmade.dyndns.org", "carpus", "duntyr1", MYSQL_CLIENT_SSL) or die ('I cannot connect to the database because: ' . mysql_error());
	return $dbconfig['dbh'];
}

function executeSQL($sql){
	global $dbconfig;
		$dbConn = getDBConnection();
	mysql_select_db ($dbconfig['webDatabase']);
	$result = mysql_query($sql) or die("Unable to execute SQL:" . $sql);	
}

function saveRecord($tableName, $keyField, $record){
	if($record[$keyField] > 0){
		$sql = updateRecordSQL($record, $keyField, $tableName);
		executeSQL($sql);
	}else{
		$sql = insertRecordSQL($record, $keyField, $tableName);
		executeSQL($sql);
//		debugStatement($sql);
//		echo "<HR>$sql<HR>";
		$record[$keyField] = mysql_insert_id();		
	}
	return $record;
}

function updateField($tableName, $keyField, $record, $editedField ){
	$updateData[$keyField] = $record[$keyField];
	$updateData[$editedField] = $record[$editedField];
	$sql = updateRecordSQL($updateData, $keyField, $tableName);
	executeSQL($sql);
}

function deleteRecord($tableName, $keyField, $record){
	$sql = "delete from $tableName where $keyField=".$record[$keyField];
	executeSQL($sql);
}

function getEntryFromPOST($fields){
	$entry = array();	
	foreach($fields as $key){
		$entry[$key] = $_POST[$key];
	}
	return $entry;
}

function setDB_Globals(){
	global $dbconfig;

	if($_SERVER['HTTP_HOST'] == 'www.randallknives.com')
		$dbconfig['server'] = "localhost";
	
	$address = '192.168.1.101';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$dbconfig['server'] = "localhost";
		$dbconfig['username']="root";
		$dbconfig['password']="skeet100";
		$dbconfig['webDatabase']="newrmk";
		$dbconfig['address']=$address;
	}
		
	$address = '192.168.1.99';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$dbconfig['server'] = "localhost";
		$dbconfig['username']="rmkweb";
		$dbconfig['password']="rmkskeet";
		$dbconfig['webDatabase']="newrmk";
		$dbconfig['address']=$address;
	}
	
	$address = '192.168.1.90';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$dbconfig['server'] = "localhost";
		$dbconfig['username']="rmkweb";
		$dbconfig['password']="rmkskeet";
		$dbconfig['webDatabase']="newrmk";	
		$dbconfig['address']=$address;
	}
	
	$address = '127.0.0.1';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$dbconfig['server'] = "localhost";
		$dbconfig['username']="rmkweb";
		$dbconfig['password']="rmkskeet";
		$dbconfig['webDatabase']="newrmk";
		$dbconfig['address']=$address;
		
	}
//		$dbconfig['server'] = "www.randallknives.com";
//		$dbconfig['username']="uplzcvgw_rmkweb";
//		$dbconfig['password']="rmkskeet";
//		$dbconfig['webDatabase']="uplzcvgw_rmk";
//		$dbconfig['address']=$address;
	//	dumpDB_ConnData();
}

function dumpDB_ConnData(){
	global $dbconfig, $dbConn;
	
	if($_SERVER['REMOTE_ADDR'] == '97.100.243.22') var_dump($dbconfig);
	if($_SERVER['REMOTE_ADDR'] == '192.168.1.90') var_dump($dbconfig);
	var_dump($dbConn);
}

//----------------------------------------------------
//--- Query Generation
//----------------------------------------------------
function insertRecordSQL($data, $idfield, $table){
	$dbConn = getDBConnection();
	$results = "INSERT INTO $table set ";
	foreach(array_keys($data) as $key){
		if($key != $idfield)
//			$data[$key] = str_replace("\\","",$data[$key]);
			$results = $results." $key = '".escape_MySQL($data[$key])."' ,";
//			$results = $results." $key = '".mysql_real_escape_string($data[$key], $dbConn)."' ,";
	}
	$results = substr($results, 0,strlen($results)-1);
	return $results;
}

function updateRecordSQL($data, $idfield, $table){
	$dbConn = getDBConnection();
	$results = "UPDATE $table set ";
	foreach(array_keys($data) as $key){
		if($key != $idfield){
//			$data[$key] = str_replace("\\","",$data[$key]);
//			$results = $results." $key = '".mysql_real_escape_string($data[$key])."' ,";
			$results = $results." $key = '".escape_MySQL($data[$key])."' ,";
		}
	}
	$results = $results." WHERE $idfield = '".$data[$idfield]."'";
	$results = str_replace(", WHERE", " WHERE",$results);
//	print $results ; 
	return $results;
}

function escape_MySQL($string){
	$string = str_replace("\\","",$string);
//	$string = str_replace(chr(189), "1/2", $string);
//	$string = str_replace(chr(188), "1/4", $string);
//	$string = str_replace(chr(190), "3/4", $string);
	$string = str_replace("'","\'",$string);
	$string = str_replace("\"","\\\"",$string);
	return $string;
}

function htmlizeText($text){
	$text = str_replace("\\","",$text);			
	$text = str_replace("\n","<BR>\n",$text);
	$text = str_replace("\'", "&rsquo;", $text);
	$text = str_replace("\"", "&quot;", $text);
	$text = str_replace(" 1/4,", " ".chr(189)." ", $text);
	$text = str_replace(" 1/4\",", " ".chr(189)."\" ", $text);
	$text = str_replace(chr(189), " &frac12;", $text);
	$text = str_replace(chr(188), " &frac14;", $text);
	$text = str_replace(" ,1/4", " 1/4", $text);
	$text = str_replace(" ,1/2", " 1/2", $text);
	$text = str_replace("–", "-", $text);
	return $text;
}

?>
