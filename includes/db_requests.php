<?php
/* * Created on Mar 24, 2006 */

$order_types=array(
	array('id'=>1, 'label'=>'Quote'),
	array('id'=>2, 'label'=>'Order'),
	array('id'=>3, 'label'=>'Catalog Request'),
	);
	
	
function saveOrder($fields, $data){
	$entry = array();
//	print "<HR>";print_r($fields);print "<HR>";
	foreach($fields as $field){
		if($data[$field] != '')
			$entry[$field] = $data[$field];
	}
	$entry['ordertype'] = getOrderTypeID($entry['ordertype']);
//	debugStatement(dumpDBRecord($entry));
//	debugStatement(dumpDBRecord(getFormValues()));

	return saveRecord('orders', 'orders_id', $entry);
}

function getOrderTypeID($value){
	global $order_types;
	foreach($order_types as $type){
		if($type['label']==$value) return $type['id']; 
	}	
}
function getOrderTypeFromID($id){
	global $order_types;
	foreach($order_types as $type){
		if($type['id']==$id) return $type['label']; 
	}	
}
function getOrderTypes(){
	global $order_types;	
	return $order_types;
}

function getSingleRequest($order_id){
	$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders where orders_id=".$order_id;
//	echo $query;
	return getSingleDbRecord($query);
}

function getRequests($whereClause=""){
	$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders ".$whereClause;
//	debugStatement($query;
	return getDbRecords($query);
}
//----------------------------------------------------
function getQuoteRequests($filter=''){
	$whereClause = " where ordertype=1";
	if($filter != ''){
		$whereClause = $whereClause . " and " . $filter;
	}
	return getRequests($whereClause);
}
function getOrderRequests($filter=''){
	$whereClause = " where ordertype=2";
	if($filter != ''){
		$whereClause = $whereClause . " and " . $filter;
	}
	return getRequests($whereClause);
}
function getCatalogRequests($filter=''){
	$whereClause = " where ordertype=3";
	if($filter != ''){
		$whereClause = $whereClause . " and " . $filter;
	}
	return getRequests($whereClause);
}


function getRequestCount($ordertype, $processed){
	$processed = ($processed ? " processed=1 ": " processed=0 ");
	$ordertype = " ordertype=$ordertype";
	$query = "Select count(*) as cnt from orders WHERE $processed AND $ordertype";
	$record = getSingleDbRecord($query);
//	print_r($record);
	return $record['cnt']; 
//	echo $query;
//	return getDbRecords($query);
}
//----------------------------------------------------

function getOrders($filter){
	return getOrderRequests($filter);
}
function fetchOrders($filter=""){
	return getOrders($filter);
}

?>
