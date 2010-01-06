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
	$results .= "<a href='../CustomerReport.php?Invoice=" . $invNum . "&reportType=ack'>Order Acknowledgement";
	$results .= "</a>";
//	$results .= "<a href='CustInvAck.php?Invoice=" . $invNum . "'>Order Acknowledgement";
//	$results .= "</a>";
	return $results;
}	
function invInvoiceLink($invNum, $dealer){
	if($dealer){
		$results .= "<a href='../CustomerReport.php?Invoice=" . $invNum . "&reportType=dlrinv'>Customer Invoice";
	} else {
		$results .= "<a href='../CustomerReport.php?Invoice=" . $invNum . "&reportType=inv'>Customer Invoice";
	}
	$results .= "</a>";
//	$results .= "<a href='CustInv.php?Invoice=" . $invNum . "'>Customer Invoice";
//	$results .= "</a>";
	return $results;
}
	
function rmkHeaderLinks($header, $dealer)
{
	$results = "<span id='invoiceAckLink'>";
	if(array_key_exists("ACK", $header) ) $results .= invAcknowledgmentLink($header['ACK']) . "&nbsp;&nbsp;";
	if(array_key_exists("INV", $header) ) $results .= invInvoiceLink($header['INV'], $dealer);
	$results .= "<br />\n";
	$results .= "<br />\n";
	$results .= "</span>";
	
	return $results;
}

?>