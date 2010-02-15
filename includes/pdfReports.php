<?php
include_once "pdfCreator/class.ezpdf.php";
include_once DB_INC_DIR. "Invoices.class.php";

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