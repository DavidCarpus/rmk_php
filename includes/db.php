<?php
/* Created on Feb 4, 2006 */
$dbh=null;
$db_server = "www.randallknives.com";
$db_username="uplzcvgw_rmkweb";
$db_password="rmkskeet";
$db_webDatabase="uplzcvgw_rmk";

setDB_Globals();
	
//if($_SERVER['HTTP_HOST'] == 'www.randallknives.com')
//	$db_server = "localhost";
//	
//$address = '192.168.1.101';
//if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
//	$db_server = "localhost";
//	$db_username="root";
//	$db_password="skeet100";
//	$db_webDatabase="newrmk";
//}
//	
//$address = '192.168.1.99';
//if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
//	$db_server = "localhost";
//	$db_username="rmkweb";
//	$db_password="rmkskeet";
//	$db_webDatabase="newrmk";
//}
//
//$address = '192.168.1.90';
//if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
//	$db_server = "localhost";
//	$db_username="rmkweb";
//	$db_password="rmkskeet";
//	$db_webDatabase="newrmk";	
//}
//
//$address = '127.0.0.1';
//if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
//	$db_server = "localhost";
//	$db_username="rmkweb";
//	$db_password="rmkskeet";
//	$db_webDatabase="newrmk";
//}


//function debugStatement($data){
//	//~ $debugUser = (auth_get_current_user_id()==2 || auth_get_current_user_id()==31 ); // carpus or debug
//	//~ $debugUser = $debugUser || (auth_get_current_user_id()==36 && isTestSystem()); // velez
//	$debugUser = 1;
//
//	if(count($data) > 0 && $debugUser ) {
//		echo "\n\n\n<HR size=2 color=red>";
//		if(count($data) > 1){
//			foreach($data as $component){
//				print_r($component);
//				print "<BR>";
//			}
//		}else{
//			print_r($data);
//		}
//		echo "<HR size=2 color=red>\n\n";
//	}
//}

function getDbRecords($query){
	$dbh=getDBConnection();
	global $db_webDatabase;
	mysql_select_db ($db_webDatabase); 
	
	$results = array();
	$dbresults = mysql_query($query);
	if($dbresults == null)
		return $results;
	$num = mysql_num_rows($dbresults);
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
	$dbh=getDBConnection();
	global $db_webDatabase;
	mysql_select_db ($db_webDatabase); 
	
	$results = array();
	$dbresults = mysql_query($query);
	if($dbresults == null)
		return $results;
	if(mysql_num_rows($dbresults) >0){
		return mysql_fetch_array($dbresults, MYSQL_ASSOC);
	}
}

function getDBConnection(){
	global $db_server;
	global $db_username;
	global $db_password;
	global $dbh;
	
	if($dbh == null)
		$dbh=mysql_connect ($db_server, $db_username, $db_password, MYSQL_CLIENT_SSL) or die ('I cannot connect to the database because: ' . mysql_error());
//		$dbh=mysql_connect ("randallmade.dyndns.org", "carpus", "duntyr1", MYSQL_CLIENT_SSL) or die ('I cannot connect to the database because: ' . mysql_error());
	return $dbh;
}
function executeSQL($sql){
	global $db_webDatabase;
	$dbConn = getDBConnection();
	mysql_select_db ($db_webDatabase);
	$result = mysql_query($sql) or die("Unable to execute SQL:" . $sql);	
}

function saveRecord($tableName, $keyField, $record){
	if($record[$keyField] > 0){
		$sql = updateRecordSQL($record, $keyField, $tableName);
		executeSQL($sql);
	}else{
		$sql = insertRecordSQL($record, $keyField, $tableName);
		executeSQL($sql);
		debugStatement($sql);
//		echo "<HR>$sql<HR>";
		$record[$keyField] = mysql_insert_id();		
	}
	return $record;
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
	global $db_server;
	global $db_username;
	global $db_password;
	global $db_webDatabase;

	if($_SERVER['HTTP_HOST'] == 'www.randallknives.com')
		$db_server = "localhost";
	
	$address = '192.168.1.101';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$db_server = "localhost";
		$db_username="root";
		$db_password="skeet100";
		$db_webDatabase="newrmk";
	}
		
	$address = '192.168.1.99';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$db_server = "localhost";
		$db_username="rmkweb";
		$db_password="rmkskeet";
		$db_webDatabase="newrmk";
	}
	
	$address = '192.168.1.90';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$db_server = "localhost";
		$db_username="rmkweb";
		$db_password="rmkskeet";
		$db_webDatabase="newrmk";	
	}
	
	$address = '127.0.0.1';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$db_server = "localhost";
		$db_username="rmkweb";
		$db_password="rmkskeet";
		$db_webDatabase="newrmk";
	}
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
