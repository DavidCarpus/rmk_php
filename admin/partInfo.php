<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "Parts.class.php";

$partsClass = new Parts();

$request = getFormValues();
$partPrice = $partsClass->currentYearPartPrice($request['partCode']);
 
$results = array( "BaseRetail"=>number_format($partPrice['Price'] ,2)	);

if(array_key_exists('debug', $request)) {
	echo debugStatement(dumpDBRecord($request));
	echo debugStatement(dumpDBRecord($results));
} else{
	echo json_encode($results);
}
?>