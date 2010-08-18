<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "Orders.class.php";

$orderClass = new Orders();

$request = getFormValues();
$orderClass->updateRMKNote($request['orders_id'],$request['note']);
//updateRMKNote
//$partPrice = $partsClass->currentYearPartPrice($request['partCode']);
// 
//$results = array( "BaseRetail"=>number_format($partPrice['Price'] ,2)	);

if(array_key_exists('debug', $request)) {
	echo debugStatement(dumpDBRecord($request));
	echo debugStatement(dumpDBRecord($results));
} else{
	echo json_encode($request['note']);
}

?>