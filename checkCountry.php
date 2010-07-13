<?php
include_once "config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "CatalogRequests.class.php";

$catalogRequestDB = new CatalogRequests();


$request = getFormValues();



if(array_key_exists('debug', $request)) {
	echo dumpDBRecord($request);
} 
else
{
//	echo json_encode($request);
}

if($catalogRequestDB->creditCardRequired($request)){
	echo "true";
} else {
	echo "false";
}

?>