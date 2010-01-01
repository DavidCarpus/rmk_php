<?php
include_once "config.php";

include_once INCLUDE_DIR. "pdfReports.php";
include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once DB_INC_DIR. "InvoiceEntries.class.php";

$formValues = getFormValues();

if (isset($formValues['d']) && $formValues['d']==2){
  echo '<html><body>';
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice']."&d=1>Debug</a>";
  echo "<br />";
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice']."&reportType=ack>View Ack</a>";
  echo "<br />";
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice']."&reportType=inv>Extended Inv</a>";
  echo "<br />";
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice']."&reportType=dlrinv>View Inv</a>";
  echo '</body></html>';
  return;
}

$invoiceClass = new Invoices();
$invoiceEntriesClass = new InvoiceEntries();
$customerClass = new Customers();

$invoice = $invoiceClass->details($formValues['Invoice']);
$customer = $customerClass->fetchCustomer($invoice['CustomerID']);
	
$pdf = new CcustomerReport($invoice, $customer);
if (isset($formValues['reportType']) && $formValues['reportType']=='ack'){
	$pdf->createOrderAcknowledgment();
}

if (isset($formValues['reportType']) && $formValues['reportType']=='inv'){
	foreach ($invoice['entries'] as $ndx=>$entry)
	{
		if(!array_key_exists('features', $entry))
			$invoice['entries'][$ndx]['features'] = $invoiceEntriesClass->features($entry['InvoiceEntryID']);	
//		echo dumpDBRecord($ndx);
//		echo $ndx;
	}
//	echo dumpDBRecords($invoice['entries']);	
	$pdf->setInvoice($invoice);
	$pdf->createCustomerInvoice();
}
if (isset($formValues['reportType']) && $formValues['reportType']=='dlrinv'){
	$pdf->createDealerInvoice();
}
//$pdf->newPage();
//echo dumpDB_ConnData();

if (isset($formValues['d']) && $formValues['d']==1){
  $pdfcode = $pdf->output(1);
  $pdfcode = str_replace("\n","\n<br />",htmlspecialchars($pdfcode));
  echo '<html><body>';
  echo trim($pdfcode);
  echo '</body></html>';
} else {
	if (isset($formValues['reportType']) && $formValues['reportType']=='ack'){
		$params= array('Content-Disposition'=>'OrderAcknowledgment_' . $formValues['Invoice'] . ".pdf");
	}
	if (isset($formValues['reportType']) && $formValues['reportType']=='inv'){
		$params= array('Content-Disposition'=>'OrderInvoice_' . $formValues['Invoice'] . "_EXT.pdf");
	}
	if (isset($formValues['reportType']) && $formValues['reportType']=='dlrinv'){
		$params= array('Content-Disposition'=>'OrderInvoice_' . $formValues['Invoice'] . ".pdf");
	}
	$pdf->stream($params);
}

?>