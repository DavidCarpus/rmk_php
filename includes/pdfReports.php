<?php
include_once "pdfCreator/class.ezpdf.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once FORMS_DIR. "Base.class.php";
include_once INCLUDE_DIR. "utils.php";

//function isUSZipCode($zipCode)
//{
//	$zipCode = str_replace("-", "",$zipCode);
//	$zipCode = trim($zipCode);
//	if(strlen($zipCode) == 5 || strlen($zipCode) == 9 )	return is_numeric($zipCode);
//	//	echo "UNK zip: $zipCode" . " " . strlen($zipcode) . "<BR>";
//	return 0;
//}

function webOrderCountry($order)
{
	$country = trim(strtoupper($order['country']));
	if($country  == "USA" || $country  == "US"  || $country  == "U.S.A."
	|| strncmp($country, "UNITED STATES", 13) == 0
	){
		return "1";
	}
	if($country  == "FRANCE"  ){
		return "3";
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
		if ($cntryA == $cntryB){
			return strcmp(strtoupper($a['state']),$b['state']);
			//    		return 0;
		} else if($cntryA < $cntryB){
			return -1;
		} else {
			return 1;
		}
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
	
	function startPage($margin){
		if($this->y < $this->ez['pageHeight']-$margin-10){
			$this->newPage();
			$this->ezSetY($this->ez['pageHeight']-$margin);
		}		
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
			//			foreach ($toSort as $row){
			//				echo $row['ordertype'] . "-" . $row['country'] . "-" . webOrderCountry($row) . "<BR>";
			//			}
			$this->orderListLabels($toSort);			
			$this->orderListDetailed($toSort);
				
		}
	}
	public function getJoinedAddress($request, $shipping=false){
		$address="";
		$ship = ($shipping? 'ship': '');
		if(strlen($request[$ship.'address1']) > 0) $address .= $request[$ship.'address1'] . " ";
		if(strlen($request[$ship.'address2']) > 0) $address .= $request[$ship.'address2'] . " ";
		if(strlen($request[$ship.'address3']) > 0) $address .= $request[$ship.'address3'] . " ";
		return $address;
	}
	public function getJoinedCSZ($request){
		$address = $request['city'] . ", " . stateAbbreviationLookup($request['state']) . ", " . $request['zip'];
		$country = strtoupper($request['country']);
		if(strlen($country) > 0){
			$address .= " (" . $country . ")";
		}
		return $address;
	}

	public function orderListDetailedQuote($request){ // ordertype=1
		$pointSize=14;

		if($this->y < 200){		$this->startPage(20);	}

		$this->ezText("<b>Quote Request</b>",16);
		$this->ezSetY($this->y - $pointSize);

		$fields = array();
		$fields[] = array("Name", $request['name']);
		$fields[] = array("Email", $request['email']);
		$fields[] = array("Billing Address", $this->getJoinedAddress($request));
		$fields[] = array("City, State, Zip Code", $this->getJoinedCSZ($request));

		$address = $this->getJoinedAddress($request, true);
		if(strlen($address) > 0){
			$fields[] = array("Shipping Address", $address);
		}
		$fields[] = array("Model", $request['model']);
		$fields[] = array("Length", $request['bladelength']);
		$fields[] = array("Features", $request['note']);

		foreach ($fields as $field) {
			$this->addText(30, $this->y, $pointSize, "<b>" . $field[0] . "</b>");
			if($field[0] == 'Features'){
				$this->ezText($field[1],$pointSize);
			} else {
				$this->addText(170, $this->y, $pointSize, $field[1]);
			}
			$this->ezSetY($this->y - ($pointSize));
		}

		$this->ezSetY($this->y - ($pointSize*1.5));
		//				$this->ezText(dumpDBRecord($request),$pointSize);
	}

	public function orderListDetailedOrder($request){ // ordertype=2
		// name, email, billing address, country, shipping address
		// model, blade len, features, cc info, submission date
		$pointSize=14;
		$this->startPage(20);

		$this->ezSetY($this->y - $pointSize);
		$this->ezText("<b>Order Request</b>",$pointSize);
		$this->ezSetY($this->y - $pointSize);

		$fields = array();
		$fields[] = array("Name", $request['name']);
		$fields[] = array("Email", $request['email']);
		$fields[] = array("Billing Address", $this->getJoinedAddress($request));
		$fields[] = array("City, State, Zip Code", $this->getJoinedCSZ($request));
		$fields[] = array("Telephone Number", $this->getFormattedPhoneNum($request['phone']));
			
		$address = $this->getJoinedAddress($request, true);
		if(strlen($address) > 0){
			$fields[] = array("Shipping Address", $address);
		}
		$fields[] = array("Model", $request['model']);
		$fields[] = array("Length", $request['bladelength']);
		$fields[] = array("Features", $request['note']);

		$fields[] = array("Credit Card Number", $this->getFormattedCC($request['ccnumber']));
		$fields[] = array("Expiration Date", $request['ccexpire']);
		$fields[] = array("VCODE", $request['ccvcode']);
		$fields[] = array("RMK Comments", $request['comment']);
		
		foreach ($fields as $field) {
			$this->addText(30, $this->y, $pointSize, "<b>" . $field[0] . "</b>");
			if($field[0] == 'Features' || $field[0] == 'RMK Comments'){
				$this->ezText($field[1],$pointSize);
			} else {
				$this->addText(170, $this->y, $pointSize, $field[1]);
			}
			$this->ezSetY($this->y - (1.5*$pointSize));
		}
		//		$this->ezText(dumpDBRecord($request),8);
//		$this->newPage();
//		$this->ezSetY($this->ez['pageHeight']-20);
	}

	public function orderListDetailedCatalog($request){ // ordertype=3
		if(webOrderCountry($request) > 1) { // NOT a US,
			$this->ezText("<b>Non US Catalog Request</b>",14);
			$this->ezSetY($this->y - $pointSize);
			
			$fields = array();
			$fields[] = array("Account Name", $request['name']);
			$fields[] = array("Billing Address", $this->getJoinedAddress($request));
			$fields[] = array("City, State, Zip Code", $this->getJoinedCSZ($request));
//			$fields[] = array("Telephone Number", $this->getFormattedPhoneNum($request['phone']));
			$fields[] = array("Credit Card Number", $this->getFormattedCC($request['ccnumber']));
			$fields[] = array("Expiration Date", $request['ccexpire']);
			$fields[] = array("VCODE", $request['ccvcode']);
			$pointSize=12;
			$this->ezSetY($this->y - ($pointSize));
			foreach ($fields as $field) {
				$this->addText(30, $this->y, $pointSize, "<b>" . $field[0] . "</b>");
				$this->addText(170, $this->y, $pointSize, $field[1]);
				$this->ezSetY($this->y - ($pointSize));
			}
			if($this->y < 150){ 	$this->startPage(20);  }
			
		}
	}

	public function orderListDetailedPayment($request){ // ordertype=4
		$pointSize=16;

		$this->startPage(20);
		
		$this->ezSetY($this->y - $pointSize*1.5);
		$this->addText(30, $this->y, $pointSize*1.5, "<b>Order Payment Request</b>");
		$this->addText(370, $this->y, $pointSize*1, "<b>Printed</b>");
		$this->addText(450, $this->y, $pointSize*1, date("M j o g:i a"));
		$this->ezSetY($this->y - $pointSize*6);

		$fields = array();
		$fields[] = array("Account Name", $request['name']);
//		$fields[] = array("Billing Address", $this->getJoinedAddress($request));
		$padding = "                ";
		if(strlen($request['address1']) > 0) $fields[] = array("Billing Address", $request['address1']);
		if(strlen($request['address2']) > 0) $fields[] = array($padding, $request['address2']);
		if(strlen($request['address3']) > 0) $fields[] = array($padding, $request['address3']);
		
		$fields[] = array("City, State, Zip Code", $this->getJoinedCSZ($request));
		$fields[] = array("Telephone Number", $this->getFormattedPhoneNum($request['phone']));
		$fields[] = array("Invoice Number", $request['invoice']);
		$fields[] = array("Credit Card Number", $this->getFormattedCC($request['ccnumber']));
		$fields[] = array("Expiration Date", $request['ccexpire']);
		$fields[] = array("VCODE", $request['ccvcode']);
		$fields[] = array("CC Name on Card", $request['ccname']);
		$fields[] = array("Customer Notes", $request['note']);
		$fields[] = array("RMK Comments", $request['comment']);
		
		foreach ($fields as $field) {
			$this->addText(30, $this->y, $pointSize, "<b>" . $field[0] . "</b>");
			
			if($field[0] == 'RMK Comments' || $field[0] == 'Customer Notes'){
				$this->ezText($field[1],$pointSize);
			} else {
				$this->addText(200, $this->y, $pointSize, $field[1]);
//				$this->addText(170, $this->y, $pointSize, $field[1]);
			}
			$this->ezSetY($this->y - (1.5*$pointSize));
		}

//		$this->newPage();
//		$this->ezSetY($this->ez['pageHeight']-20);
	}

	public function orderListDetailed($data)
	{
		$firstNonUSCatalog=true;
		$this->startPage(20);
//		$this->ezSetY($this->ez['pageHeight']-29);

		//		$this->newPage();
		foreach ($data as $order) {
			$order = $this->cleanUpOrder($order);
			switch ($order['ordertype']) {
				case 1:
					$this->orderListDetailedQuote($order);
					break;
				case 2:
					$this->orderListDetailedOrder($order);
					break;
				case 3:
					if(($firstNonUSCatalog && webOrderCountry($request) > 1)){
						$firstNonUSCatalog = false;
						$this->startPage(20);
					}
					$this->orderListDetailedCatalog($order);
					$firstCatalogOrPayment=false;
					break;
				case 4:
					$this->orderListDetailedPayment($order);
					$firstCatalogOrPayment=false;
					break;
				default:
					;
					break;
			}
		}
	}

	function cleanUpOrder($order){
		if(webOrderCountry($order) == 1){
			$order['country'] = "";
		}

		foreach (array('name','address1','address2','address3','city','state','ccname') as $field){
			$order[$field] = ucwords(strtolower($order[$field]));
		}

		if(strlen($order['state']) <=3 ){
			$order['state'] = strtoupper($order['state']);
		}

		return $order;
	}

	function printLabel($row, $col, $data)
	{
		$fontSize=12;
		$currentX = 0;;
		$currentY = $this->ez['pageHeight']-20 ;

		$currentX += ($col * 214);
		$currentY -= (($row * 77) + $fontSize);

		if($col == 0){
			$currentX += 20;
		}

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
			$order = $this->cleanUpOrder($order);
			if($order['ordertype'] != 4 &&  // Do not print labels for payments Per Val Email 2010-03-30 
					$order['ordertype'] != 1 ){// Do not print labels for payments Per Val call 2010-03-31 
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
				if(webOrderCountry($order) == 1){
					$order=$this->fixUS_Address($order);
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
			}
			$lastType = $order['ordertype'];
		}
		if($row > 0 || $col > 0){
			$this->ezSetY($this->ez['pageHeight']-90);
			$this->startPage(20);
		}
	}

	function fixUS_Address($order) {
		$order['country'] = "";
		//		$order['state'] = strtoupper($order['state']);

		return $order;
	}

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
		$results[] = $order['city'] . " " . stateAbbreviationLookup($order['state']) . " " . $order['zip'];
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
	public function getFormattedCC($creditCard) {
		$results="";
		$split = str_split($creditCard, 4);
		foreach ($split as $part) {
			$results .= $part . "-";
		}
		// trim last "-"
		$results = substr($results, 0 ,strlen($results)-1);
		return $results;
	}
	public function getFormattedPhoneNum($phone) {
		$phone = preg_replace("/[^0-9]/", "", $phone);

		if(strlen($phone) == 7)
		return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
		elseif(strlen($phone) == 10)
		return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
		else
		return $phone;
	}
}

class CDealerSpecLetter extends Cezpdf {
	public $reportData;
	public $letterData;
	public $labelFont;
	public $footerFont;

	public function __construct() {
		parent::__construct('LETTER');
		$this->ezSetMargins(110,10,80,80);

		$this->labelFont = PDF_FONT_DIR .'Times-Roman.afm';
		$this->footerFont = PDF_FONT_DIR .'Courier.afm';
	}
	function startPage($margin){
		if($this->y < $this->ez['pageHeight']-$margin-10){
			$this->newPage();
			$this->ezSetY($this->ez['pageHeight']-$margin);
		}		
	}
	
	public function setData($dealerSpecLetter, $reportData){
		$this->reportData = $reportData;
		$this->letterData = $dealerSpecLetter;
	}
	public function createReport() 	//	Default LTR: 612 x 792
	{
		$this->setStrokeColor(0,0,0,1);
		$this->selectFont($this->labelFont);
//		$this->avery5160Alignment();

//		echo sizeof($this->reportData) ;
		if(sizeof($this->reportData) > 0){
			$this->ezSetY($this->ez['pageHeight']-110-10);
			$this->dump();
		}
	}
	function dump(){
		foreach ($this->reportData as $record) {
			$this->startPage(110);
			$letter = $this->letterData['prefix'] . $this->letterData['postfix'];
			$letter = substitureLetterFields($letter, $record );
			$this->ezText($letter );
//			$this->ezText(dumpDBRecord($record),10);;
		}
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

function stateAbbreviationLookup($state){
$lookupTable = array("ALABAMA"=>"AL", "ALASKA"=>"AK", "AMERICAN SAMOA"=>"AS", "ARIZONA"=>"AZ", "ARKANSAS"=>"AR", 
"CALIFORNIA"=>"CA", "COLORADO"=>"CO", "CONNECTICUT"=>"CT", "DELAWARE"=>"DE", "DISTRICT OF COLUMBIA"=>"DC", 
"FEDERATED STATES OF MICRONESIA"=>"FM", "FLORIDA"=>"FL", "GEORGIA"=>"GA", "GUAM"=>"GU", "HAWAII"=>"HI", 
"IDAHO"=>"ID", "ILLINOIS"=>"IL", "INDIANA"=>"IN", "IOWA"=>"IA", "KANSAS"=>"KS", "KENTUCKY"=>"KY", "LOUISIANA"=>"LA", 
"MAINE"=>"ME", "MARSHALL ISLANDS"=>"MH", "MARYLAND"=>"MD", "MASSACHUSETTS"=>"MA", "MICHIGAN"=>"MI", 
"MINNESOTA"=>"MN", "MISSISSIPPI"=>"MS", "MISSOURI"=>"MO", "MONTANA"=>"MT", "NEBRASKA"=>"NE", "NEVADA"=>"NV", 
"NEW HAMPSHIRE"=>"NH", "NEW JERSEY"=>"NJ", "NEW MEXICO"=>"NM", "NEW YORK"=>"NY", "NORTH CAROLINA"=>"NC", 
"NORTH DAKOTA"=>"ND", "NORTHERN MARIANA ISLANDS"=>"MP", "OHIO"=>"OH", "OKLAHOMA"=>"OK", "OREGON"=>"OR", 
"PALAU"=>"PW", "PENNSYLVANIA"=>"PA", "PUERTO RICO"=>"PR", "RHODE ISLAND"=>"RI", "SOUTH CAROLINA"=>"SC", 
"SOUTH DAKOTA"=>"SD", "TENNESSEE"=>"TN", "TEXAS"=>"TX", "UTAH"=>"UT", "VERMONT"=>"VT", "VIRGIN ISLANDS"=>"VI", 
"VIRGINIA"=>"VA", "WASHINGTON"=>"WA", "WEST VIRGINIA"=>"WV", "WISCONSIN"=>"WI", "WYOMING"=>"WY");

$abbreviation = $lookupTable[strtoupper($state)];
if(strlen($abbreviation) == 0) $abbreviation = $state;
return $abbreviation;
}
?>