<?php
include_once "../includes/htmlHead.php";
include_once "../includes/db/Invoices.class.php";
include_once "../includes/db/Customers.class.php";
include_once "../includes/forms/Invoice.class.php";

$invoiceClass = new Invoices();
$customerClass = new Customers();

$request = getFormValues();

$invoiceNum = $request['invoice_num'];

$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$invoice = $invoiceClass->details( $invoiceNum );
//$costs = $invoiceClass->computeCosts($invoice);
$entries = $invoiceClass->itemsWithAdditions($invoiceNum);
$invoiceForms = new Invoice();
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];
	
$invInfo = array();
foreach( array('DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 'ShippingAmount', "PONumber", "ShippingInstructions", "KnifeCount") as $attrib)
{
	$invInfo[$attrib] = $invoice[$attrib];
	if($invInfo[$attrib]==NULL) $invInfo[$attrib]="";
	if( strncmp($attrib, "Date", 4) == 0 && strlen($invoice[$attrib]) > 10) // Trim off timestamp, if still there
	{
		$invInfo[$attrib] = substr($invInfo[$attrib], 0, 10);
	}
}

$custInfo = array();
foreach( array("Prefix","FirstName","LastName","Suffix","PhoneNumber","EMailAddress") as $attrib)
{
	$custInfo[$attrib] = $customer[$attrib];
	if($custInfo[$attrib]==NULL) $custInfo[$attrib]="";
}
$results = array("InvoiceDetails"=>$invInfo, "CustomerSummary"=>$custInfo, "InvoiceKnifeList"=> $invoiceForms->knifeListTable($entries));
echo json_encode($results);	
?>