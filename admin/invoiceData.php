<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";

include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "InvoiceEntry.class.php";
include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Payment.class.php";
include_once FORMS_DIR. "Part.class.php";
include_once FORMS_DIR. "Customer.class.php";

$invoiceClass = new Invoices();
$customerClass = new Customers();
$paymentForms = new Payment();
$partsFormClass = new Part();

$customerForm = new Customer();

$request = getFormValues();

$InvoiceNum = $request['Invoice'];

$customer = $customerClass->fetchCustomerForInvoice( $InvoiceNum );
$invoice = $invoiceClass->details( $InvoiceNum );
//$costs = $invoiceClass->computeCosts($invoice);
$entries = $invoiceClass->itemsWithAdditions($InvoiceNum);
$payments = $invoiceClass->fetchInvoicePayments($InvoiceNum);
$costs = $invoiceClass->computeCosts($invoice);

$invoiceForms = new Invoice();
$invoiceEntryForms = new InvoiceEntry();
$invoice["KnifeCount"] =0;
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];
	
$invInfo = array();
foreach( array('DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 'ShippingAmount', 
				"PONumber", "ShippingInstructions", "KnifeCount", "Comment","TaxPercentage") as $attrib)
{
	$invInfo[$attrib] = $invoice[$attrib];
	if($invInfo[$attrib]==NULL) $invInfo[$attrib]="";
	if( strncmp($attrib, "Date", 4) == 0 && strlen($invoice[$attrib]) > 10) // Trim off timestamp, if still there
	{
		$invInfo[$attrib] = substr($invInfo[$attrib], 0, 10);
	}
	if($attrib == "TotalRetail" || $attrib = "ShippingAmount"){
		$invInfo[$attrib] = "$" . number_format($invoice[$attrib] ,2);
	}
}
$invInfo["Invoice"]= $InvoiceNum;
$flags = array();
//$flags["comment"] = (strlen($invoice["Comment"]) > 0);
//$flags["TaxPercentage"] = $invoice["TaxPercentage"];
//$flags["invoice_num"] = $invoiceNum;

$custInfo = array();
foreach( array("Prefix","FirstName","LastName","Suffix","PhoneNumber","EMailAddress") as $attrib)
{
	$custInfo[$attrib] = $customer[$attrib];
	if($custInfo[$attrib]==NULL) $custInfo[$attrib]="";
}
$custInfo["FullName"]="";
foreach( array('Prefix', 'FirstName', 'LastName', 'Suffix') as $attrib)
{
	$custInfo["FullName"] .= $customer[$attrib] . " ";
}

$CustomerSummaryDisp = $customerForm->display($customer);
$CustomerFlags = $customerForm->customerFlags($customer);
$invoiceAckLink=$invoiceForms->invoiceReportLinks($invoice);

$newInvoiceEntryFormValues["Invoice"]=$InvoiceNum;
//$newInvoiceEntryFormValues["submit"]="New Item";

$results = array(	"InvoiceDetails"=>$invInfo, 
					"CustomerSummary"=>$custInfo, 
					"CustomerSummaryDisp"=>$CustomerSummaryDisp, 
					"CustomerFlags"=>$CustomerFlags, 
					"invoiceAckLink"=>$invoiceAckLink, 
//					"Flags"=>$flags, 
					"InvoiceKnifeList"=> $invoiceEntryForms->knifeListTable($entries, 0),
					"InvoicePayments"=> $paymentForms->paymentListTable($InvoiceNum, $payments),
					"InvoiceFinanceSummary"=> $paymentForms->invoiceFinanceTable($costs),
					"NewInvoiceEntry"=>$invoiceEntryForms->newInvoiceEntryForm($newInvoiceEntryFormValues, $partsFormClass)
);
if(array_key_exists('debug', $request)) {
	echo debugStatement(dumpDBRecord($invInfo));
	echo debugStatement(dumpDBRecord($custInfo));
	echo debugStatement($results['InvoiceKnifeList']);
	echo debugStatement($results['InvoicePayments']);
	echo debugStatement($results['InvoiceFinanceSummary']);
	echo debugStatement($results['NewInvoiceEntry']);
	echo debugStatement($results['CustomerSummaryDisp']);
	echo debugStatement($results['CustomerFlags']);
} else{
	echo json_encode($results);
}
?>