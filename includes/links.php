<?php

//function invoiceReportLinks($invoice){
//	$results = "<span id='invoiceAckLink'>";
//	
////	$results .= invAcknowledgmentLink($invNum); 
////	$results .= " &nbsp; &nbsp; ";
//	$results .= invInvoiceLink($invNum);
//	$results .= "</span>";
//	return $results;
//}

function invAcknowledgmentLink($invNum){
	$results .= "<a href='CustInvAck.php?Invoice=" . $invNum . "'>Order Acknowledgement";
	$results .= "</a>";
	return $results;
}	
function invInvoiceLink($invNum){
	$results .= "<a href='CustInv.php?Invoice=" . $invNum . "'>Customer Invoice";
	$results .= "</a>";
	return $results;
}
	
function rmkHeaderLinks($header)
{
	$results = "<span id='invoiceAckLink'>";
	if(array_key_exists("ACK", $header) ) $results .= invAcknowledgmentLink($header['ACK']) . "&nbsp;&nbsp;";
	if(array_key_exists("INV", $header) ) $results .= invInvoiceLink($header['INV']);
	$results .= "<br />\n";
	$results .= "<br />\n";
	$results .= "</span>";
	
	return $results;
}

?>