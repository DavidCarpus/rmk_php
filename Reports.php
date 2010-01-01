<?php
include_once "config.php";

include_once INCLUDE_DIR. "pdfCreator/class.ezpdf.php";
include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

//session_start();

$formValues = getFormValues();

if (isset($formValues['d']) && $formValues['d']==2){
  echo '<html><body>';
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice']."&d=1>Debug</a>";
  echo "<br />";
  echo "<a href=" . $_SERVER['PHP_SELF'] . "?Invoice=".$formValues['Invoice'].">View</a>";
  echo '</body></html>';
  return;
}

function invoiceAck($invNum)
{
	$mainFont = PDF_FONT_DIR. 'Times-Roman.afm';
	$codeFont = PDF_FONT_DIR. 'Courier.afm';
	
	$invoiceClass = new Invoices();
	$customerClass = new Customers();

	$invoice = $invoiceClass->details($invNum);
	$customer = $customerClass->fetchCustomer($invoice['CustomerID']);
	
//	echo dumpDBRecord($invoice);
	$pageHeight=810; $pagewidth=612;
//	612 x 792
	
	$pdf = new Cezpdf();
	$pdf -> ezSetMargins(50,70,50,50);
	$pdf->selectFont($mainFont);

	$spacing=12;
	
	$row=$pageHeight;
	addTextBlock($pdf, 30, $pageHeight, array("Phone", "PO#", "Invoice#"));
	addTextBlock($pdf, 330, $pageHeight, array("Scheduled Ship Date", "Ordered Date", "Ship Date"));
	addTextBlock($pdf, 90, $pageHeight, array($customer['PhoneNumber'], $invoice['PONumber'], $invoice['Invoice']));
	addTextBlock($pdf, 450, $pageHeight, array(
			date("m/d/Y",strtotime($invoice['DateEstimated'])), 
			date("m/d/Y",strtotime($invoice['DateOrdered'])), 
			date("m/d/Y",strtotime($invoice['DateShipped']))));
	
	$pdf->addObject(pageHeaderFooter($pdf),'all');

	addTextBlock($pdf, 30, $pageHeight-50, array("B", "I", "L", "L"));
	addTextBlock($pdf, 330, $pageHeight-50, array("S", "H", "I", "P"));
	$pdf->line(42, $pageHeight-38, 42, $pageHeight-(38+12*5));
	$pdf->line(342, $pageHeight-38, 342, $pageHeight-(38+12*5));
	
	$row=$pageHeight-50;
	$currAdd = $customer['CurrrentAddress'];
	$addressBlock = array();
	
	$addressBlock[] = ltrim($customer['FirstName'] . " " .$customer['LastName']);
	if($currAdd['ADDRESS0'] <> '') $addressBlock[] = $currAdd['ADDRESS0'];
	if($currAdd['ADDRESS1'] <> '') $addressBlock[] = $currAdd['ADDRESS1'];
	if($currAdd['ADDRESS2'] <> '') $addressBlock[] = $currAdd['ADDRESS2'];
	$addressBlock[] = $currAdd['CITY'] . ", ". $currAdd['STATE'] . " ". $currAdd['ZIP'];
	
	addTextBlock($pdf, 45, $row, $addressBlock);
	
	if(array_key_exists('BillingAddressType', $invoice) ){
		if($invoice['BillingAddressType'] == 1) $pdf->addText(345,$row, 12, "SHOP SALE");
		if($invoice['BillingAddressType'] == 2) $pdf->addText(345,$row, 12, "SAME");
		if($invoice['BillingAddressType'] == 3) $pdf->addText(345,$row, 12, "PICK UP");
	} else {
		addTextBlock($pdf, 345, $row, $addressBlock);
	}
		
	$pdf->ezSetY($pageHeight-120);
	$pdf->selectFont($mainFont);
	$pdf->ezTable(knifeTable($invoice), "", "", array('width'=>400));
	
	$pdf->selectFont($mainFont);
	
	$pdf->addText(40,130, 12, "Pay To:");
	addTextBlock($pdf, 90, 130 , array("Randall Made Knives", "P.O. Box 1988", "Orlando, FL 32802-1988"));
	$pdf->addText(300, 142, 12, "Last Payment Received on:");
	addTextBlock($pdf, 342, 130 , array("Total", "SubTotal", "+Shipping", "+Tax", "-Payments", "Balance"));
	
	$invoiceClass = new Invoices();
	$costs = $invoiceClass->computeCosts($invoice);

	addTextBlock($pdf, 480, 130, array(
		"$". number_format($costs['TotalCost'],2),
		"$". number_format($costs['Subtotal'],2),
		"$". number_format($costs['Shipping'],2),
		"$". number_format($costs['Taxes'],2),
		"$". number_format($costs['TotalPayments'],2),
		"$". number_format($costs['Due'],2)
	), true);
	
	return $pdf;
}

function addTextBlock($pdf, $x, $y, $textArray, $rightAlign=false)
{
	$fontSize=12;
	$maxLen=0;
	if($rightAlign){
		foreach ($textArray as $addressLine)
		{
			$len=$pdf->getTextWidth($fontSize, $addressLine);
			if($len < $maxLen) $maxLen=$len;
		}
		
	}
	$pdf->ezSetY($y);
	foreach ($textArray as $addressLine)
	{
		$adjustedX=$x;
		if($rightAlign){
			$len=$pdf->getTextWidth($fontSize, $addressLine);
			$adjustedX = $x+$maxLen-$len;
		}
		$pdf->addText($adjustedX, $y, $fontSize, $addressLine);
		$y -= $fontSize;
	}
}

function knifeTable($invoice)
{
	$table=array();
	foreach ($invoice['entries'] as $entry){
		$record=array();
		$record['Quantity'] = $entry['Quantity'];
		$record['Model'] = $entry['PartCode'];
		$record['Description'] = getInvEntryDesc($entry);
		$record['Price'] = number_format($entry['TotalRetail']/$entry['Quantity'] ,2);
		$record['Extended'] = number_format($entry['TotalRetail'] ,2);
		$table[] = $record; 
	}
	return $table;
}

function getInvEntryDesc($entry){
	$invoiceClass = new Invoices();
	$additions =  $invoiceClass->additions($entry['InvoiceEntryID']);
	$results ="";
	$totalAdds=count($additions);
	$cnt=0;
	foreach($additions as $addition){
		$code= $addition['PartCode'];
		if($addition['Price'] <= 0)
			$code = strtolower($code);
		$results .= $code ;

		if(++$cnt < $totalAdds)
			$results .= ",";
	}

	return $results;
}
	
function pageHeaderFooter($pdf)
{
$footerFont = './includes/pdfCreator/fonts/Courier.afm';
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0,0,0,1);
$pdf->line(20,40,578,40);
$pdf->line(20,822,578,822);
$pdf->selectFont($footerFont);
$pdf->addText(50,30,10,'http://www.randallknives.com');
$pdf->restoreState();
$pdf->closeObject();
return $all;
}

function makePDF($pdf)
{
$mainFont = './includes/pdfCreator/fonts/Times-Roman.afm';
$codeFont = './includes/pdfCreator/fonts/Courier.afm';
	
// make a new pdf object
$pdf -> ezSetMargins(50,70,50,50);

$pdf->addObject(pageHeaderFooter($pdf),'all');

$pdf->ezSetDy(-100);

// select a font
$pdf->selectFont($mainFont);

// select the font
$pdf->selectFont($mainFont);
$pdf->addText(30,600,30,'Hello World');
$pdf->selectFont($codeFont);
$pdf->addText(150,550,10,"the quick brown fox <b>jumps</b> <i>over</i> the lazy dog!",-10);
}   

$invNum=$formValues['Invoice'];
$pdf = invoiceAck($invNum);

if (isset($formValues['d']) && $formValues['d']==1){
  $pdfcode = $pdf->output(1);
//  $end_time = getmicrotime();
  $pdfcode = str_replace("\n","\n<br />",htmlspecialchars($pdfcode));
  echo '<html><body>';
  echo trim($pdfcode);
  echo '</body></html>';
} else {
  $params= array('Content-Disposition'=>'OrderAcknowledgment_' . $invNum . ".pdf");
  $pdf->stream($params);
}

?>
