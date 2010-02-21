<?php
include_once "pdfCreator/class.ezpdf.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once FORMS_DIR. "Base.class.php";

function isUSZipCode($zipCode)
{
	$zipCode = str_replace("-", "",$zipCode);
	$zipCode = trim($zipCode);
	if(strlen($zipCode) == 5 || strlen($zipCode) == 9 )	return is_numeric($zipCode);
//	echo "UNK zip: $zipCode" . " " . strlen($zipcode) . "<BR>";
	return 0;
}

function webOrderCountry($order)
{
	$country = strtoupper($order['country']);
	if($country  == "USA" || $country  == "US"  || $country  == "U.S.A." 
		|| strncmp($country, "UNITED STATES", 13) == 0
		){
		return "1";
	}
	if(isUSZipCode($order['zip'])) return "1";
	
	if($country  == "CA" || strncmp($country, "CANADA", 13) == 0) return "2";
//	echo $country . " " . $order['state']. " " . $order['zip'] . "<BR>";
	return "3";
}

function orderLabelSort($a, $b)
{
	// Sort First by 'ordertype'
	// Sort Next  by country - US, CA, Other
    if ($a['ordertype'] == $b['ordertype']) {
    	$cntryA = webOrderCountry($a); 
    	$cntryB = webOrderCountry($b); 
    	if ($cntryA == $cntryB) return 0;
    	else ($cntryA < $cntryB) ? -1 : 1;
    }
    return ($a['ordertype'] < $b['ordertype']) ? -1 : 1;
}
	
function orderDetailSort($a, $b)
{
	// Sort First by country - US, CA, Other
	// Sort Next by 'ordertype'
    if ($a['ordertype'] == $b['ordertype']) {
    	return 0;
    }
    return ($a['ordertype'] < $b['ordertype']) ? -1 : 1;
}
	
class CwebOrderReport extends Cezpdf {
	
	public $orderData;
	public $labelFont;
	public $footerFont;
//	public $pageHeight=830;
//	public $pagewidth=612;
	
	public function __construct() {
		parent::__construct('LETTER'); 
		$this->ezSetMargins(10,10,50,50);
		
		$this->labelFont = PDF_FONT_DIR .'Times-Roman.afm';
		$this->footerFont = PDF_FONT_DIR .'Courier.afm';
	}	
	public function setData($orderData){
		$this->orderData = $orderData;				
	}
	
	function orderCmp2($a, $b)
	{
		// Sort First by country - US, CA, Other
		// Sort Next by 'ordertype'
	    if ($a['ordertype'] == $b['ordertype']) {
	    	return 0;
	    }
	    return ($a['ordertype'] < $b['ordertype']) ? -1 : 1;
	}
	
	public function avery5160Alignment()
	{
		$this->setStrokeColor(0,0,0,1);		
		$this->selectFont($this->labelFont);
		
		$tickLength=15;
		$tmpY=$this->ez['pageHeight']-20;
		for ($i=0; $i < 11; $i++){
			$this->line(1, $tmpY, $tickLength, $tmpY);	
			if($i < 10){
				$this->addText(1, $tmpY-12, 12, $tmpY);
			}
			$tmpY -= 78;
		}
		
		$tmpX = 0;
		for ($i=0; $i < 3; $i++){
			$this->line($tmpX, 1, $tmpX, $tickLength);
			$this->addText($tmpX + 5, 10, 12, $tmpX);		
			$tmpX += 214;
		}		
	}
	public function createReport() 	//	Default LTR: 612 x 792
	{
		$this->setStrokeColor(0,0,0,1);		
		$this->selectFont($this->labelFont);		
//		$this->avery5160Alignment();
		
		if(sizeof($this->orderData) > 0){
			$toSort=$this->orderData;
			usort ( $toSort , "orderLabelSort" );
			$this->orderListLabels($toSort);
		}
	}
	
	public function orderListDetailed($data)
	{
		
	}
	
	function printLabel($row, $col, $data)
	{
		$fontSize=12;
		$currentX = 0;;
		$currentY = $this->ez['pageHeight']-20 ;
		
		$currentX += ($col * 214);
		$currentY -= (($row * 77) + $fontSize);
		
		foreach ($data as $row){
			$this->addText($currentX, $currentY, $fontSize, $row);
			$currentY -= $fontSize;
		}
		
	}
	

	function orderListLabels($data){
		$row=0;
		$col=0;
		$lastType=0;
		
		foreach ($data as $order) {
			if($lastType != $order['ordertype'] && $lastType > 0){
				if($col > 0){
					$row++;
					$col=0;
				}
				$currentY = $this->ez['pageHeight']-20 - (($row * 77) + $fontSize);
				$this->line(1, $currentY, 600, $currentY);
				if($row > 10){
					$this->newPage();
					$row=0;
				}
			}
			$fields = $this->getLabelRequestFields($order);
//			$fields[] = $col;			
			$this->printLabel($row,$col,$fields);
			$col++;
			if($col >=3 ){
				$row++;
				$col=0;
			}
			if($row > 10){
				$this->newPage();
				$row=0;
			}
			$lastType = $order['ordertype'];	
		}		
	}
	
//	function orderList()
//	{
//			if($lastType != $order['ordertype']){
//				$orderType = $baseFormsClass->requestTypeFromID($order['ordertype']);	
////				if($nextY < 100){
////					$currentY=760;
////					$this->ezText(" ");
////				}	
//				if ($col==1){ // shift to 'new' row
//					$currentY -= $leftFieldCount*12;
//				}
//				$this->addText(210, $currentY, 12, $orderType);
//				$currentY -= 12;
//				$col=0;
//				$lastType = $order['ordertype'];				
//			}
//	}

	
	function getLabelRequestFields($order)
	{
		$results=array();
		
		$text="";
		if(strlen($order['name']) > 0) $text .= $order['name'];
//		if(strlen($order['email']) > 0 && strlen($text) > 0) $text .= " - ";
//		if(strlen($order['email']) > 0) $text .= $order['email'];
		if(strlen($text) > 0) 			$results[] = $text;
//		if(strlen($order['email']) > 0) $results[] = $order['email'];
		
		$text="";
		if(strlen($order['address1']) > 0) $results[] = $order['address1'];
		if(strlen($order['address2']) > 0) $results[] = $order['address2'];
		if(strlen($order['address3']) > 0) $results[] = $order['address3'];
		$results[] = $order['city'] . " " . $order['state'] . " " . $order['zip'];
		$country = strtoupper($order['country']);
		$isUSA = $country  == "USA" || strncmp($country, "UNITED STATES", 13) == 0;
		if(! $isUSA){	
			$results[] = $order['country'];
		}	
		
//		$results[] = $order['ordertype'];
		
//		switch ($order['ordertype']) {		
//			case 4:
//				$ccText = $order['cctype'];
//				$ccText .=  " : " . $order['ccnumber'] . ":" . $order['ccvcode'];
//				$ccText .=  " (" . $order['ccexpire'] . ")";
//			break;
//		}
		return $results;
	}	
}

class CcustomerReport extends Cezpdf {
	
	public $invoice;
	public $customer;
	public $mainFont;
	public $footerFont;
	
	public function createOrderAcknowledgment() 	//	Default LTR: 612 x 792
	{
		$pageHeight=810; $pagewidth=612;
		$this->ezSetMargins(50,70,50,50);

		$this->addObject($this->pageHeaderFooter("Order Acknowledgment:".$this->invoice['Invoice']),'all');
		$this->contactsAndDates(740, $this->customer, $this->invoice);
		$this->addressBlock();
			
		$this->ezSetY(630);
		$this->knifeList();
		
		$this->selectFont($this->mainFont);
		$this->termsBlock(30, 120);
		$this->invoiceNoteBlock(30, 80,  $this->invoice);
//		$this->payBlock(30, 80);
	}
	
	public function createDealerInvoice()
	{
		$pageHeight=810; $pagewidth=612;
		$this->ezSetMargins(50,70,50,50);
		
		$this->addObject($this->pageHeaderFooter("Invoice:".$this->invoice['Invoice']),'all');
		$this->contactsAndDates(740, $this->customer, $this->invoice);
		$this->addressBlock();
			
		$this->ezSetY(630);
		$this->knifeList();
		
		$this->selectFont($this->mainFont);
		$this->termsBlock(30, 120);
		$this->payBlock(30, 80);
		$this->paymentsBlock(350, 120);
	}
	
	public function createCustomerInvoice()
	{
		$pageHeight=810; $pagewidth=612;
		$this->ezSetMargins(90,70,50,50);
		
		$this->addObject($this->pageHeaderFooter("Invoice:".$this->invoice['Invoice']),'all');
		$this->contactsAndDates(740, $this->customer, $this->invoice);
		$this->addressBlock();
			
		$this->ezSetY(630);
		$this->knifeList(true);
		
		$this->selectFont($this->mainFont);
//		$this->termsBlock(30, 120);
		$this->payBlock(30, 120);
		$this->invoiceNoteBlock(30, 80,  $this->invoice);
		$this->paymentsBlock(350, 120);
	}
//=======================================================================================	
//=======================================================================================	

	private function termsBlock($x,$y)
	{
		$this->addText($x, $y, 12, "Terms:");
		$this->addTextBlock($x+50, $y , array("Please Pay Net 21 Days", "Prior to the Scheduled Ship Date."));
		$this->rectangle($x-2,$y+12,220,-40);	
	}
	private function payBlock($x,$y)
	{
		$this->addText($x, $y, 12, "Pay To:");
		$this->addTextBlock($x+50, $y , array("Randall Made Knives", "P.O. Box 1988", "Orlando, FL 32802-1988"));
		$this->rectangle($x-2,$y+12, 220,-40);		
	}
	private function invoiceNoteBlock($x,$y, $invoice)
	{
//		echo dumpDBRecord($invoice);
		$this->saveState();
		$this->ezSetY($y+10);
		$this->ezSetMargins(50,0,$x,$this->ez['pageWidth']-300);
		$this->ezText($invoice['Comment'], 9);
		$this->restoreState();
//		$this->ezSetMargins(50,70,50,50);
	}
	
	private function paymentsBlock($x,$y)
	{
//		echo dumpDBRecord($this->invoice);

		$taxLabel="+Tax";
		$taxPercent=$this->invoice['TaxPercentage'];
		if($taxPercent > 0 && $taxPercent < 1) $taxPercent *= 100;
		if($taxPercent > 0) $taxLabel .= " ($taxPercent%)";
		
		$this->addTextBlock($x, $y , array("Total", "SubTotal", "+Shipping", $taxLabel, "-Payments", "Balance"));
		$invoiceClass = new Invoices();
		$costs = $invoiceClass->computeCosts($this->invoice);
	
		$this->addTextBlock($x+170, $y, array(
			"$". number_format($costs['TotalCost'],2),
			"$". number_format($costs['Subtotal'],2),
			"$". number_format($costs['Shipping'],2),
			"$". number_format($costs['Taxes'],2),
			"$". number_format($costs['TotalPayments'],2),
			"$". number_format($costs['Due'],2)
		), true);
		
		$lastPayment = $invoiceClass->lastPaymentDate($this->invoice['Invoice']);
		if($lastPayment > 0)
		{
			$lastPayment= "Last Payment Received on:" . date("m/d/Y", $lastPayment);
		}
		else
		{
			$lastPayment="No payments received.";
		}
		
		$this->addText($x-42, $y+10, 12, $lastPayment);		
		$this->addText($x-42, $y-75, 12, "Shipping charges determined in year of shipment");		
	}
	
	private function contactsAndDates($horizontalLocation, $customer, $invoice)
	{
		$this->selectFont($this->mainFont);
		
		$this->addTextBlock(30, $horizontalLocation, array("Phone", "PO#", "Invoice#"));
		
		$this->addTextBlock(330, $horizontalLocation, array("Scheduled Ship Date", "Ordered Date", "Ship Date"));
		$this->addTextBlock(90, $horizontalLocation, array($customer['PhoneNumber'], $invoice['PONumber'], $invoice['Invoice']));
		$this->addTextBlock(450, $horizontalLocation, array(
				date("m/d/Y",strtotime($invoice['DateEstimated'])), 
				date("m/d/Y",strtotime($invoice['DateOrdered'])), 
				date("m/d/Y",strtotime($invoice['DateShipped']))));
		$this->line(20, 710, 578, 710);
	}
	
	private function knifeList($extended=false)
	{
		$this->selectFont($this->mainFont);
		$tableData=array();
		$knifeTableParams=array();
		$knifeTableParams['width']=550;
		
		if($extended){
			$tableData=$this->extendedKnifeTable($this->invoice);
		} else{
			$tableData=$this->knifeTable($this->invoice);			
			$knifeTableParams['cols']=array(
				'Qty.'=>array('width'=>30),
				'Model'=>array('width'=>90),
				'Price'=>array('width'=>70),
				'Extended'=>array('width'=>80)
			);
		}
//		$this->transaction('start');
		$this->ezTable($tableData, "", "", $knifeTableParams);
	}
	
	public function addressBlock()
	{
		$this->selectFont($this->codeFont);
		$y=690;
		
		$this->addTextBlock(30, $y, array("B", "I", "L", "L"));
		$this->addTextBlock(330, $y, array("S", "H", "I", "P"));
		$this->line(42, $y+12, 42, $y-(12*4));
		$this->line(342, $y+12, 342, $y-(12*4));
		
		$currAdd = $this->customer['CurrrentAddress'];
		$addressBlock = array();
		
		$addressBlock[] = ltrim($this->customer['FirstName'] . " " .$this->customer['LastName']);
		if($currAdd['ADDRESS0'] <> '') $addressBlock[] = $currAdd['ADDRESS0'];
		if($currAdd['ADDRESS1'] <> '') $addressBlock[] = $currAdd['ADDRESS1'];
		if($currAdd['ADDRESS2'] <> '') $addressBlock[] = $currAdd['ADDRESS2'];
		$addressBlock[] = $currAdd['CITY'] . ", ". $currAdd['STATE'] . " ". $currAdd['ZIP'];
		
		$this->addTextBlock(45, $y, $addressBlock);
		
		if(array_key_exists('BillingAddressType', $this->invoice) ){
			if($this->invoice['BillingAddressType'] == 1) $this->addText(345,$y, 12, "SHOP SALE");
			if($this->invoice['BillingAddressType'] == 2) $this->addText(345,$y, 12, "SAME");
			if($this->invoice['BillingAddressType'] == 3) $this->addText(345,$y, 12, "PICK UP");
		} else {
			$this->addTextBlock(345, $y, $addressBlock);
		}
	}
	
	public function knifeTable($invoice)
	{
		$table=array();
		foreach ($invoice['entries'] as $entry){
			$record=array();
			$record['Qty.'] = $entry['Quantity'];
			$record['Model'] = $entry['PartCode'];
			$record['Description'] = $this->getInvEntryDesc($entry);
			$record['Price'] = number_format($entry['TotalRetail']/$entry['Quantity'] ,2);
			$record['Extended'] = number_format($entry['TotalRetail'] ,2);
			$table[] = $record; 
		}
		return $table;
	}
	
	public function extendedKnifeTable($invoice)
	{
		$table=array();
		foreach ($invoice['entries'] as $entry){
			$record=array();
			$record['Qty.'] = $entry['Quantity'];
			$record['Model'] = $entry['PartCode'];
			$record['Description'] = $entry['LongDescription'];
			$record['Price'] = number_format($entry['TotalRetail']/$entry['Quantity'] ,2);
			$record['Extended'] = number_format($entry['TotalRetail'] ,2);
			$table[] = $record;
			 
			foreach ($entry['features'] as $feature)
			{
				$record=array();
				$record['Model'] = $feature['PartCode'];
				$record['Description'] = $feature['Description'];
				$record['Price'] = number_format($feature['Price'] ,2);
				$record['Extended'] = number_format($feature['Price']*$entry['Quantity'] ,2);

				$table[] = $record; 
			}
			if($entry['Comment'])
			{
				$record=array();
				$record['Model'] = "**NOTE**";
				$record['Description'] = $entry['Comment'];
				$table[] = $record;				
			}
		}
		return $table;
	}
	
	public function __construct($invoice, $customer) {
		parent::__construct(); 
		$this->invoice = $invoice;
		$this->customer = $customer;
		
		$this->mainFont = PDF_FONT_DIR .'Times-Roman.afm';
		$this->codeFont = PDF_FONT_DIR .'Courier.afm';
		$this->footerFont = $this->mainFont;
	}	
	
	public Function setInvoice($invoiceData)
	{
		$this->invoice = $invoiceData;
	}
	
	public function addTextBlock($x, $y, $textArray, $rightAlign=false)
	{
		$fontSize=12;
		$maxLen=0;
		if($rightAlign){
			foreach ($textArray as $textLine)
			{
				$len=$this->getTextWidth($fontSize, $textLine);
				if($len < $maxLen) $maxLen=$len;
			}
			
		}	
		$this->ezSetY($y);
		foreach ($textArray as $addressLine)
		{
			$adjustedX=$x;
			if($rightAlign){
				$len=$this->getTextWidth($fontSize, $addressLine);
				$adjustedX = $x+$maxLen-$len;
			}
			$this->addText($adjustedX, $y, $fontSize, $addressLine);
			$y -= $fontSize;
		}
	}

	public function getInvEntryDesc($entry){
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
	
	
	function pageHeaderFooter($reportTitle="Report")
	{
		$all = $this->openObject();
		$this->saveState();
		$this->setStrokeColor(0,0,0,1);
		$this->line(20, 822, 578, 822);
		$this->line(20, 755, 578, 755);
		$this->line(20,40,578,40);
		
		$this->selectFont($this->mainFont);
		$this->ezSetY(820);
		$this->ezText("RANDALL MADE KNIVES", 16, array('justification' => 'center'));
		$this->ezText("Phone: (407) 855-8075", 10, array('justification' => 'center'));
		$this->ezText("Fax: (407) 855-9054", 10, array('justification' => 'center'));
		$this->ezText($reportTitle, 16, array('justification' => 'center'));
		
		$this->selectFont($this->footerFont);
		$this->addText(20,30,10,'Thank you for your order');
		$this->addText(230,30,10,'http://www.randallknives.com');
		$this->addText(410,30,10,'Deposits are not transferable or refundable');
		$this->restoreState();
		$this->closeObject();
		return $all;
	}

}
?>