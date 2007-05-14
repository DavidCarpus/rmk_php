<?php
/* Created on Mar 25, 2006 */

function getNextOrder($order){
	
	$id=$order['orders_id'];
	$id="orders_id > $id";
	$type=$order['ordertype'];
	$type="and ordertype = $type";
	$processed = $order['processed'];
	$processed = " and processed=$processed";
	
	$query="select *,UNIX_TIMESTAMP(datesubmitted) as submission_date  from orders where  $id $type $processed order by orders_id LIMIT 1";

//	$query="select * from orders where orders_id > $id and ordertype = $type order by orders_id LIMIT 1";
//	print $query . "<BR>\n";
	$order = getSingleDbRecord($query);
	return $order;
}
function getPrevOrder($order){
	$id=$order['orders_id'];
	$id="orders_id < $id";
	$type=$order['ordertype'];
	$type="and ordertype = $type";
	$processed = $order['processed'];
	$processed = " and processed=$processed";
	
	$query="select *,UNIX_TIMESTAMP(datesubmitted) as submission_date  from orders where  $id $type $processed order by orders_id DESC LIMIT 1";
//	print $query . "<BR>\n";
	$order = getSingleDbRecord($query);
	return $order;
}
function navigateOrderLink($order, $direction){
	$id = $order['orders_id'];
	$type = $order['ordertype'];

//	print"<HR>";
//	print_r($order);
//	print"<HR>";
	$prev = getPrevOrder($order, false);
	$hasPrev = ! ($prev == null || count($prev) <= 0 || $prev['orders_id'] == $id); 
	$next = getNextOrder($order, false);
	$hasNext = ($next != null && $next['orders_id'] != $id); 
	
	echo "<HR>" .$direction . " - P:" . $hasPrev . " - pid:" . $prev['orders_id'] . " - n:" . $hasNext . " - nid:" . $next['orders_id'] . "<HR>";
	if($direction == 'prev'){
		if($hasPrev)
			return "<span class='adminOrderNavigation'><a href='" . $_SERVER['PHP_SELF'] . "?action=processandprevious&orders_id=$id'>Process current/View Previous</a>&nbsp;&nbsp;</span>";
		elseif(! $hasNext)
			return "<span class='adminOrderNavigation'><a href='" . $_SERVER['PHP_SELF'] . "?action=process&orders_id=$id'>Process current</a>&nbsp;&nbsp;</span>";
	}
	if($direction == 'next'){
		if($hasNext)
			return "<span class='adminOrderNavigation'>&nbsp;&nbsp;<a href='" . $_SERVER['PHP_SELF'] . "?action=processandnext&orders_id=$id'>Process current/View Next</a></span>";
		else
			return "<span class='adminOrderNavigation'><a href='" . $_SERVER['PHP_SELF'] . "?action=process&orders_id=$id'>Process current</a>&nbsp;&nbsp;</span>";
	}
}

function toggleRecordProcessed($order){
//	echo "Mark order processed:" . $orderid;
	
	$query="update orders set processed=(not processed ) where orders_id=" . $order['orders_id'];
//	echo $query;
	executeSQL($query);
//	$order['processed'] = !$order['processed'];
//	return $order;
}

?>
