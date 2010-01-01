<?php
/* Created on Feb 8, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";
include_once INCLUDE_DIR. "orders.php";
include_once INCLUDE_DIR. "order_administration.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once FORMS_DIR. "InvoiceEntry.class.php";

$statusOptions = array(	array('id'=>"0", 'label'=>"Unprocessed"),
						array('id'=>"1", 'label'=>"Processed"),
						array('id'=>"2", 'label'=>"Accepted"),
						array('id'=>"3", 'label'=>"Denied"),
						array('id'=>"4", 'label'=>"Deferred"),
						);
$requestTypeOptions = array(array('id'=>"1", 'label'=>"Quote Request"),
							array('id'=>"2", 'label'=>"Order Request"),
							array('id'=>"3", 'label'=>"Catalog Request"),
						);
$orderTypes = array("1"=>"Quote Request", "2"=>"Order Request", "3"=>" CatalogRequest" );

session_start();
if(!loggedIn()){
	$_SESSION['loginValidated'] = 0;
	session_destroy();
	header("Location: "."../");
}
?>
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../Style.css" rel="stylesheet" media='screen' type="text/css">

<?php echo logo_header("admin"); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo adminToolbar(); ?>
		<div class="content">
			<?php processAdminOrders3(); ?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php

function printAction(){
//	$action = getHTMLValue('action');
//	if($action == 'details' || $action == 'previousorder' || $action == 'nextorder') return true;
	return false;
}
function processAdminOrders3(){
	global $orderTypes;
	
	$parameters = getFormValues();
	$action = $parameters['action'];
	if($action == ''){
		$action = 'selecttype';
		$parameters['status']=1;
//		$parameters['requesttype']=2;
	} 
//	if($parameters['startid'] == '') 
//		$parameters['startid'] = getIntFromDB("Select max(orders_id) as cnt from orders");

	switch ($action) {
		case 'selecttype':
			echo getOrderTypeSelection();
			echo "<br />";			
			echo orderSearchForm($parameters);
			break;

		case 'list':
			echo getDefaultRequests($parameters);
			break; 
		case 'searchform':
			echo orderSearchForm($parameters);
			break;
	
		case 'updateorders':
//			debugStatement(dumpDBRecord($parameters));
			echo "<br />";
			$query = "Update orders set processed=" . $parameters['status'] . " WHERE orders_id in (" . $parameters['order_list'] . ")";
//			debugStatement($query . "<br /><br />");
			executeSQL($query);
			echo getOrderTypeSelection();
			echo "<br />";			
			echo orderSearchForm($parameters);
			
//			$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders WHERE orders_id in (" . $parameters['order_list'] . ")";
//			$records = getDbRecords($query);
//			$orderIDs = split(",", $parameters['order_list']);
//			foreach($records as $order){
//				echo dumpDBRecord($order) . "<br /><br />\n";
//			}
			break;
	
		case 'updateorder':
			$parameters['processed'] = $parameters['status'];
			updateOrder(fields(), $parameters);
			$request = getSingleRequest($parameters['orders_id']);
			echo '<div class="adminOrderType">' . $orderTypes[$request['ordertype']] . "</div>";
			echo editableOrderDetail($request);
			break;
	
		case 'searchorders':
//			debugStatement(dumpDBRecord($parameters));			
			echo searchOrders($parameters);
			echo orderSearchForm($parameters);
			break;
	
		case 'details':
			$request = getSingleRequest($parameters['orders_id']);
			echo '<div class="adminOrderType">' . $orderTypes[$request['ordertype']] . "</div>";
//			debugStatement(dumpDBRecord($request));
			echo editableOrderDetail($request);
			break;
		default:
			echo "Order Processing - Undeveloped function:" . $action . "<br />\n";
			$record = getFormValues();
			debugStatement(dumpDBRecord($record));
			break;
	}
}
function searchOrders($parameters){
	$MAX_LIST_LEN=60;
	$filter = "";
//	debugStatement(dumpDBRecord($parameters));

	if($parameters['status'] != ''){
		if($filter != "") $filter .= " AND ";
		$filter .= " processed=" . $parameters['status'];
	}
	
	if($parameters['requesttype'] > 0){
		if($filter != "") $filter .= " AND ";
		$filter .= " ordertype=" . $parameters['requesttype'];
	}
	
	if($parameters['name'] != ''){
		if($filter != "") $filter .= " AND ";
		$filter .= " name LIKE '%".$parameters['name']."%'";
	}
	if($parameters['phone'] != ''){
		if($filter != "") $filter .= " AND ";
		$filter .= " phone LIKE '%".$parameters['phone']."%'";
	}
	if($parameters['startdate'] != ''){
		if($filter != "") $filter .= " AND ";
		$date = date("Y-m-d", strtotime($parameters['startdate']));
		$filter .= " datesubmitted > '$date'";
	}
	if($parameters['enddate'] != ''){
		if($filter != "") $filter .= " AND ";
		$date = date("Y-m-d", strtotime($parameters['enddate'] . "+1 day"));
		$filter .= " datesubmitted < '$date'";
	}
	
	if($filter != "") $filter = " WHERE $filter";
	
//	debugStatement($filter);
	$cnt = getIntFromDB("Select count(*) from orders $filter"); 
	if($cnt > $MAX_LIST_LEN){
		return "<H2>Criteria returned > $MAX_LIST_LEN records ($cnt). Please narrow down search criteria.</H2><br />\n";
	}
	$requests = getRequests($filter);
//	debugStatement(count($requests));
	return getOrderList2($requests);
}

function getDefaultRequests($parameters){
	$whereStatement ="";
	$type = $parameters['requesttype_id'];
	$startid =  0;
	$status = $parameters['status'];
	
	if($startid > 0) $whereStatement = addToWhereStatement($whereStatement, "orders_id < $startid");
	$whereStatement = addToWhereStatement($whereStatement, " processed = $status ");
	$whereStatement = addToWhereStatement($whereStatement, " ordertype = $type ");
	$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders WHERE $whereStatement ORDER BY orders_id DESC LIMIT 40";
	$records = getDbRecords($query);
//	debugStatement(count($records) . " - " . $query);
	$prepend="";
	if(count($records) > 20){
		$prepend = "Only displaying first 40 records. Processes 40 at a time.<br />\n";
	}
	return $prepend . getOrderList2($records);
}

function getOrderList2($records){
	global $orderTypes;
	
	$orderlist = "";
	$blockSize=4;
	foreach($records as $record){
		$orderlist .= $record['orders_id'] . ",";
		
		switch ($record['ordertype']) {
			case 3: // catalog request
				if($blockSize > 3) $blockSize=3;
				break;
			case 1: // quotes
				if($blockSize > 1) $blockSize=1;
				break;	
			case 2: // orders
				if($blockSize > 1) $blockSize=1;
				break;	
			default:
				break;
		}
	}
	if(strstr($orderlist, ",") != false){
		$orderlist = substr($orderlist , 0, strlen($orderlist )-1);
	}

	
	$cnt=0;
	$sameStatus=true;
	$currStatus=-1;
	$defaultStatusSet=-1;
//	debugStatement("blockSize : $blockSize");

//	$blockSize=2;
	
	foreach($records as $record){
//		debugStatement("sameStatus:$sameStatus currStatus:$currStatus defaultStatusSet:$defaultStatusSet");
//		debugStatement(dumpDBRecord($record));
		if($defaultStatusSet < 0 || ($sameStatus && $defaultStatusSet==$record['processed']) ){
			$currStatus = $record['processed'];
			switch ($currStatus) {
				case 0: // unprocessed
					$currStatus=0;
					$defaultStatusSet = 1; // -> processed
					break;
				case 1: // processed
					$currStatus=1;
					$defaultStatusSet = 4; // -> deferred
					break;
				case 2: // accepted
					$currStatus=2;
					$defaultStatusSet = 0; // -> processed
					break;
				default:
					break;
			}
		}
		if($currStatus != $record['processed']){
			$sameStatus=false;
		}
		
		$requestType="Unknown: $type";
		switch ($record['ordertype']) {
			case 1: // quotes
				$requestType = "Quote Request";
				break;	
			case 2: // orders
				$requestType = "Order Request";
				break;	
			case 3: // catalog request
				$requestType = "Catalog Request";
				break;
			default:
		}
		
		if($cnt%$blockSize == 0){
			$results .= "<div class='noPageBreak'>";
		}
		$results .= "<div class='requestTypeHeader'>$requestType</div>"; 
//		$results .= editableOrderDetail($record);
		$results .= orderDetail($record);

		$cnt++;
		if($cnt%$blockSize == 0){
			$results .= "</div>";
		}
		
	}

//	$sameStatus=true;
//	$currStatus=-1;
//	debugStatement("sameStatus:$sameStatus currStatus:$currStatus defaultStatusSet:$defaultStatusSet");
	
	if($sameStatus && $defaultStatusSet >= 0){
		global $statusOptions;
		$prepend="";
		foreach($statusOptions as $option){
			if($option['id'] == $currStatus){
				$prepend = "All these requests are :<B>" . $option['label'] . "</B><br />\n";
				break;
			}
		}
		$prepend .= bulkSetStatusForm($orderlist, $defaultStatusSet);
		$results = $prepend . $results;
	}


	return $results;
}



function bulkSetStatusForm($orderlist, $defaultStatusSet){
	global $statusOptions;
	$statusOptionsNoUnprocess = array();
	foreach($statusOptions as $option){
		if($option['label'] != 'Unprocessed')
			$statusOptionsNoUnprocess[] = $option;
	}
	$results .= "<form class='printHide' action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
//	$results .= "<label for='status' >Set status of below to:</label>";
	$results .= "<input class='btn' type='submit' name='submit' value='To change, click here after selecting new status to right.' >" ;
	$results .= selection("status", $statusOptionsNoUnprocess , "", $defaultStatusSet, false). "\n" ;
	$results .= hiddenField('order_list',$orderlist) . "\n";
	$results .= hiddenField('action','updateorders') . "\n";
	$results .= "</form>";
	return $results;
}

function addToWhereStatement($whereStatement, $newClause){
	if($whereStatement != "") $whereStatement .= " AND ";
	$whereStatement .= $newClause;
	return $whereStatement;
}


function updateOrder($fields, $data){
	$entry = array();
//	debugStatement(dumpDBRecord($fields));
	foreach($fields as $field){
		if($data[$field] != '')
			$entry[$field] = $data[$field];
	}
//	debugStatement(dumpDBRecord($entry));
	return saveRecord('orders', 'orders_id', $entry);
}

function editableOrderDetail($order){
	$fields = fields();
	$cols=array('left'=>"", 'right'=>"");
	
	$results = $results . "<br /><div class='orderdetail' style='width:700px'>";
	$results = $results . "<table border=1>";

	foreach($fields as $field){
		$desc = fieldDesc($field);
		switch ($field) {
			case 'orders_id':
//				$desc='order id';
				$value = $order[$field];
				break;
			case 'shipaddress3':
				$desc = ' ';
				$value = $order[$field];
				break;
			case 'address2':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'address3':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'ordertype':
			case 'email':
				$value=linkToEmailPage($order);
				break;
			case 'ccnumber':// skip
				if(trim($order[$field]) != '')
				$value = addDashesToCCNumber($order[$field]);
				break;
			case 'comment':
				$desc='';
				break;
			case 'submission_date':
				$desc="Request Submitted";
				$value = dateAsEDT($order['submission_date']);
				break;
			default:
				$value = $order[$field];
				break;
		}
	$leftstyle = 'line-height:18px; width:210px; left:0px; font-style: italic; text-decoration: none;';		
	$rightstyle = 'line-height:18px; width:490px; left:10px;  font-weight: bold;';		

		if($desc != '&nbsp;' && $desc != '' && trim($value) != ''){
			$results .= "<TR>";
			$results .= "<TD style='$leftstyle'>" . $desc. "</TD>";
			$results .= "<TD  style='$rightstyle'>" . $value . "</TD>";
			$results .= "</TR>";
		}
	}
	$results .=  "</table>";

	global $statusOptions;
	$results .= '<br />';
	$results .= "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
	$results .= "<label for='status' >Status</label>";
	$results .= selection("status", $statusOptions , "", $order['processed'], false). "<br />\n" ;
	$results .= "<label for='comment' >Comment/Note</label>";
	$results .= textArea('comment', "", false, $value=$order['comment']); 
	$results .= "<center><input class='btn' type='submit' name='submit' value='Update' ></center>\n" ;
	$results .= hiddenField('orders_id',$order['orders_id']) . "\n";
	$results .= hiddenField('action','updateorder') . "\n";
	$results .= "</form>";

	$results .=  "</div>";
	
	return $results;	
}

function orderSearchForm($parameters){
	global $requestTypeOptions;
	global $statusOptions;
	
	$results .= "<form  class='printHide' action='". $_SERVER['PHP_SELF']. "' method='post'>" ;

	$results .= "<label for='requesttype' >RequestType</label>";
	$results .= selection("requesttype", $requestTypeOptions , "", $parameters['requesttype'], true). "<br />\n" ;

	$results .= "<label for='status' >Status</label>";
	$results .= selection("status", $statusOptions , "", $parameters['status'], false). "<br />\n" ;

	$results .= textField('name', fieldDesc('name'), false, $parameters['name']) . "<br />\n" ;
	$results .= textField('phone', fieldDesc('phone'), false, $parameters['phone']) . "<br />\n" ;
	$results .= textField('startdate', "Date - Start", false, $parameters['startdate'], 'date') . "<br />" ;
	$results .= textField('enddate', "End ", false, $parameters['enddate'], 'date') . "<br />\n" ;
//	$results .= textField('phone', fieldDesc('phone'), false, "") . "<br />\n" ;
	


	$results .= hiddenField('startid',$parameters['startid']) . "\n";
	$results .= hiddenField('action','searchorders') . "\n";
	$results .= "<br />\n";
	$results .= "<center><input class='btn' type='submit' name='submit' value='Find' ></center>\n" ;
	$results .= "</form>";
	return $results;
}

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function dateAsEDT($dbDate){
//	$shift = -4;
	$shift = -5;
	$value = $dbDate;
	$value=$value + ($shift * 60 * 60);
	$value=gmdate('m/d/Y h:i:s a', $value);
	return $value;
}

function getOrderTypeSelection($default='', $processed=''){
	$results ="";
	$results = $results .  "<center><B>Select UnProcessed Order Type:</B><br />";
	foreach(getOrderTypes() as $ordertype){
		$results = $results .  "<a href='" . $_SERVER['PHP_SELF'] . 
			"?action=list&requesttype_id=" . $ordertype['id'] . "&status=0'>" . $ordertype['label'] .
			"</a> ( ".
			getRequestCount($ordertype['id'], false).
			" )<br />";
	}
	$results = $results .  "</center>";
	
	$results = $results .  "<HR>";
	
	$results .= "<B>For other than UnProcessed, Search:</B><br />";
//	$results .= orderSearchForm();
	
//	$results = $results .  "<center><B>Select Processed Order Type:</B><br />";
////	$parameters['status']
//	foreach(getOrderTypes() as $ordertype){
//		$results = $results .  "<a href='" . $_SERVER['PHP_SELF'] . 
//			"?action=list&requesttype_id=" . ($ordertype['id']) . "&status=1'>" . $ordertype['label'] .
//			"</a> ( ".
//			getRequestCount($ordertype['id'], true).
//			" )<br />";
//	}

//	$results .= "</center>";

//	$results .= basicOrderSearch();
	
	return $results;
}

function basicOrderSearch(){
	$results .= "<br />\n";
	$results .= "<br />\n";
	
	$results .= "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
	$results .= textField('name', fieldDesc('name'), false, "") . "<br />\n" ;
	$results .= textField('phone', fieldDesc('phone'), false, "") . "<br />\n" ;
	$results .= hiddenField('action','searchorders') . "\n";
	$results .= "<br />\n";
	$results .= "<center><input class='btn' type='submit' name='submit' value='Search' ></center>\n" ;
	$results .= "</form>\n";
	return $results;
}

function orderDetail($order){

//	$results = detailNavigationHeader($order);	
//	$results = $results . "<HR>". print_r($order) . "<HR>";

	
	$fields = fields();
	$cols=array('left'=>"", 'right'=>"");

	
	$results = $results . "<div class='orderdetail' style='width:700px; padding: 0px 0px 5px 0px; '>";
	$results = $results . "<table border=1>";

	foreach($fields as $field){
		$desc = fieldDesc($field);
		switch ($field) {
			case 'orders_id':
//				$desc='order id';
				$value = $order[$field];
				break;
			case 'shipaddress3':
				$desc = ' ';
				$value = $order[$field];
				break;
			case 'address2':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'address3':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'ordertype':
			case 'processed':
				$desc='';
				break; // skip
			case 'email':
				$value=linkToEmailPage($order);
				break;
			case 'ccnumber':// skip
				if(trim($order[$field]) != '')
				$value = addDashesToCCNumber($order[$field]);
				break;
			case 'submission_date':
				$desc="Request Submitted";
				$value = dateAsEDT($order['submission_date']);
				break;
			case 'name':
				$value = linkToOrderDetail($order);
				break;
			default:
				$value = $order[$field];
				break;
		}
//	$leftstyle = 'line-height:18px;display:block; width:190px; left:0px; position: relative; float:left; text-align: right; font-style: italic; text-decoration: none;';		
//	$rightstyle = 'line-height:18px;display:block; width:190px; left:10px; position: relative; float:right; text-align: left; font-weight: bold;';		
	$leftstyle = 'line-height:18px; width:210px; left:0px; font-style: italic; text-decoration: none;';		
	$rightstyle = 'line-height:18px; width:460px; left:10px;  font-weight: bold;';		

		if($desc != '&nbsp;' && $desc != '' && trim($value) != ''){
			$results = $results . "<TR>";
			$results = $results . "<TD style='$leftstyle'>" . $desc. "</TD>";
			$results = $results . "<TD  style='$rightstyle'>" . $value . "</TD>";
			$results = $results . "</TR>";
		}
	}
	$results = $results . "</table>";
	$results = $results . "</div>";
	
	return $results;
}

function linkToOrderDetail($request){
	return "<a href='" . $_SERVER['PHP_SELF'] . "?action=details&orders_id=" . $request['orders_id'] . "'>" . $request['name'] . "</a>";
}

function linkToEmailPage($request){
	$message = "Regarding Your " . getOrderTypeString($request['ordertype']) . " Request";
	$email = $request['email'];
	return "<a href='../email.php?to=$email&subject=$message&from=BLANK&orderid=" . $request['orders_id'] . "'>$email</a>";
}


function getOrderTypeString($typeid){
	switch ($typeid) {
		case 1:
			return  'Quote';
		case 2:
			return  'Order';
		case 3:
			return  'Catalog Request';
		default:
			return 'Unknown';
	}
}

function printableOrders($orders, $start, $cnt){
	$fields = fields();
	
	if($cnt == -1) $cnt = count($orders);
	
	$order=$orders[$start];	
	$label = " - " . getOrderTypeString($order['ordertype']) . " - ";

	$results = $results . "<div id=adminOrderType>";
	if($cnt == 1)  $results = $results . navigateOrderLink($order, 'prev');
	$results = $results . $label;
//	$results = $results .  $order['interest'];
	if($cnt == 1)  $results = $results . navigateOrderLink($order,'next');
	$results = $results . "</div>";	

	for($index=$start; $index < $cnt; $index++){
		$order=$orders[$index];
		foreach($fields as $field){
			$desc = fieldDesc($field);
			switch ($field) {
				case 'ordertype':
				case 'orders_id':
				case 'processed':
					$desc='';
					break; // skip
					
				case 'ccnumber':// skip
					if(trim($order[$field]) != '')
					$value = addDashesToCCNumber($order[$field]);
					break;
				default:
					$value = $order[$field];
					break;
			}
			if($desc != '&nbsp;' && $desc != '' && $desc != ' ' && trim($value) != '')
				$results = $results . $desc . " - <B>" . $value. "</B><br />";
		}
	}	
	return $results;
}


function linkToOrderDetails($order, $type=''){
	if($type=='')
		return "<a href='" . $_SERVER['PHP_SELF'] . "?action=details&orders_id=" . $order['orders_id'] . "'>" . $order['name'] . "</a>";
	else
		return "<a href='" . $_SERVER['PHP_SELF'] . "?action=details&type=".$type."&requesttype_id=$type&orders_id=" . $order['orders_id'] . "'>" . $order['name'] . "</a>";
}


?>