<?php
include_once "../config.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once DB_INC_DIR. "Payments.class.php";

class CustomerReports
{
	public $invoiceClass;
	public $customerClass;
	
	public function __construct() {
       $this->invoiceClass = new Invoices();
       $this->customerClass = new Customers();
	}
	
	function invoice($formValues){
		$results= "";
		$invoice = $this->invoiceClass->details($formValues['Invoice']);
		$customer = $this->customerClass->fetchCustomer($invoice['CustomerID']);
		if(!array_key_exists('entries', $invoice))
			$invoice['entries'] = $this->invoiceClass->items($invoice['Invoice']);
		
		
		$results .= "<div id='customerReportHeader'>";
		$results .= $this->dateAndContact($invoice, $customer);
		$results .= "\n";
		$results .= $this->billandShipAddress($invoice, $customer);
		$results .= "\n";
		$results .= "</div>";
		$results .= $this->entriesTable($invoice);
		$results .= $this->invoiceFooter($invoice, $customer);
		
//		$results .= debugStatement(  dumpDBRecord($invoice ) );
		return $results;		
	}
   
	function acknowledgment($formValues){
		$results= "";
		$invoice = $this->invoiceClass->details($formValues['Invoice']);
		$customer = $this->customerClass->fetchCustomer($invoice['CustomerID']);
		if(!array_key_exists('entries', $invoice))
			$invoice['entries'] = $this->invoiceClass->items($invoice['Invoice']);
		
		
		$results .= "<div id='customerReportHeader'>";
		$results .= $this->dateAndContact($invoice, $customer);
		$results .= "\n";
		$results .= $this->billandShipAddress($invoice, $customer);
		$results .= "\n";
		$results .= "</div>";
		$results .= $this->entriesTable($invoice);
		$results .= $this->acknowledgmentFooter($invoice, $customer);
		
//		$results .= debugStatement(  dumpDBRecord($invoice ) );
		return $results;
	}
	
	function acknowledgmentFooter(array $invoice, array $customer){
		$results = ""; 
		$results .= "<div id='acknowledgmentFooter'>";

//		$results .= $this->terms($customer);
		$results .= $this->paymentBlock($invoice, true);
		$results .= $this->payTo();
//		if(strlen($invoice['Comment']) > 0){
//			$results .= "<span class='comment'>" .$invoice['Comment']. "</span>";
//		}
		$results .= $this->invoiceFooterComment($invoice, false);
		
		
		$results .= "</div>";	
		
		return $results;
	}
	
	function invoiceFooter(array $invoice, array $customer){
		$results = ""; 
		$results .= "<div id='invoiceFooter'>";

		$results .= $this->terms($customer);
		$results .= $this->paymentBlock($invoice);
		$results .= $this->payTo();
		$results .= $this->invoiceFooterComment($invoice);
//		if(strlen($invoice['Comment']) > 0){
//			$results .= "<span class='comment'>" .$invoice['Comment']. "</span>";
//		}
		
		$results .= "</div>";	
		
		return $results;
	}
	
	function invoiceFooterComment($invoice, $replaceCarat=true){
		$results = ""; 
		if(strlen($invoice['Comment']) > 0){
			$comment = $invoice['Comment'];
			$caratLoc = strpos($comment, "^");
			if($replaceCarat){
				if($caratLoc > 0){
					$payments = new Payments();
					$CC = $payments->getLastCC_ForInvoice($invoice['Invoice']);
					$comment = str_replace("^", $CC,$comment);
				}
			} else{
				$comment = str_replace("^", "",$comment);
			}
			$results .= "<span class='comment'>$comment</span>";
		}
		return $results;
	}
	
	function paymentBlock(array $invoice, $shipChargesComment=false){
		
		$costs = $this->invoiceClass->computeCosts($invoice);
	
		$results = ""; 
		$results .= "<div id='paymentBlock'>";
		$results .= "Last Payment Received on";
		$results .= "<br />";
		foreach (array("Total"=>'TotalCost',"SubTotal"=>'Subtotal',"+Shipping"=>'Shipping',
				"+Tax"=>'Taxes',"-Payments"=>'TotalPayments',"Balance"=>'Due') as $label=>$field) {
				$results .= "<span class='Label'>" . $label. "</span>";
				$results .= "<span class='value'>" . "$". number_format($costs[$field] ,2). "</span>";
				$results .= "<br />";
		}
		if($shipChargesComment){
			$results .= "<br />";
			$results .= "Shipping charges determined in year of shipment";
		}
		$results .= "</div>";	
		return $results;	
	}
	
	function terms(array $customer){
		$results = ""; 
		$results .= "<div id='invoiceTerms'>";
		$results .= "Terms:Please pay net 21 days.";
		$results .= "<br />";
		$results .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
		$results .= "Prior to scheduled ship date";
		$results .= "</div>";	
		return $results;	
	}
	
	function payTo(){
		$results = ""; 
		$results .= "<div id='invoicePayTo'>";
		$results .= "Pay To: Randall Made Knives";
		$results .= "<br />";
		$results .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
		$results .= "P.O. Box 1988";
		$results .= "<br />";
		$results .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
		$results .= "Orlando, FL 32802-1988";
		$results .= "</div>";	
		return $results;	
	}
	
	
	
	function dateAndContact(array $invoice, array $customer){
		$results= "";
		$valuesL['Phone'] 		= array("Phone", 		$customer['PhoneNumber']);
		$valuesL['PONumber'] 	= array("PO#", 			$invoice['PONumber']);
		$valuesL['Invoice']		= array("Invoice#", 	$invoice['Invoice']);
		
		$valuesR['DateEstimated']= array("Scheduled Ship Date", date("m/d/Y",strtotime($invoice['DateEstimated'])));
		$valuesR['DateOrdered'] 	= array("Ordered Date",	date("m/d/Y",strtotime($invoice['DateOrdered'])));
		$valuesR['DateShipped'] 	= array("Ship Date", 	date("m/d/Y",strtotime($invoice['DateShipped']))); 

		$results .= "<div id='customerReportInvoiceHeader'>";
		
		$results .= "<div class='leftHalf'>";
		foreach ($valuesL as $field=>$value) {
			$results .= "<span class='Label'>" . $value[0] . "</span>";
			$results .= "<span class='value'>" . $value[1] . "</span>";
			$results .= "<br />";
		}
		$results .= "</div>";
		
		$results .= "<div class='rightHalf'>";
		foreach ($valuesR as $field=>$value) {
			$results .= "<span class='Label'>" . $value[0] . "</span>";
			$results .= "<span class='value'>" . $value[1] . "</span>";
			$results .= "<br />";
		}
		$results .= "</div>";
		
		$results .= "</div><!-- End customerReportInvoiceHeader -->";
		return $results;
	}
	
	function billandShipAddress(array $invoice, array $customer){
		$results= "";
		$currAdd = $customer['CurrrentAddress'];

		$results .= "<span id='customerReportAddressHeader'>";
		
		$address = $customer['FirstName'] . " ";
		$address .= $customer['LastName'];
		$address .= "<br />";
		
		if($currAdd['ADDRESS0'] <> '') $address .= $currAdd['ADDRESS0'] . "<br />";
		if($currAdd['ADDRESS1'] <> '') $address .= $currAdd['ADDRESS1'] . "<br />";
		if($currAdd['ADDRESS2'] <> '') $address .= $currAdd['ADDRESS2'] . "<br />";

		$address .= $currAdd['CITY'] . ", ". $currAdd['STATE'] . " ". $currAdd['ZIP'];

		$results .= "<div class='vertical'>B<br />I<br />L<br />L</div>";
		$results .= "<div class='leftHalf'>";
		
		$results .= $address  . "<br />";
		$results .= "</div><!-- End leftHalf -->";
				
		$billAddress=$address;
		if(array_key_exists('BillingAddressType', $invoice) ){
			if($invoice['BillingAddressType'] == 1) $billAddress="SHOP SALE";
			if($invoice['ShippingAddressType'] == 2) $billAddress="SAME";
			if($invoice['ShippingAddressType'] == 3) $billAddress="PICK UP";
		}
		
		$results .= "<div class='vertical'>S<br />H<br />I<br />P</div>";
		$results .= "<div class='rightHalf'>";
		$results .= $billAddress . "<br />";
		$results .= "</div> <!-- End rightHalf -->";
				
		if($currAdd['COUNTRY'] <> '') $results .= $currAdd['COUNTRY'] . "<br />";

		$results .= "</span>";
		
//		$results .= debugStatement(  dumpDBRecord($customer ) );
		return $results;
	}
	
	function entriesTable($invoice){
		$results="";
		if(!array_key_exists('entries', $invoice))
			$invoice['entries'] = $this->invoiceClass->items($invoice['Invoice']);
		
		$results .= "\n<div id='InvoiceEntriesTable'>\n";

		$results .= "<span id='InvoiceEntriesTable_Header'>";
		$results .= "<span class='Quantity'>			Qty</span>";
		$results .= "<span class='Model'>				Model</span>";
		$results .= "<span class='PartDescription'>		Description</span>";
		$results .= "<span class='Price'>				Price</span>";
		$results .= "<span class='Extended'>			Extended</span>";
		$results .= "</span>";
		$cnt=0;
		$lncnt=0;
		$pg=1;
		$lineCountPageBreak=23;
		foreach ($invoice['entries'] as $entry) {
			$hl = ($cnt%2==0 ? "HL_": "");
			$invEntryDesc=$this->getInvEntryDesc($entry);
			$results .= "<span class='". $hl . "Quantity'>" . 		$entry['Quantity'] . "</span>";
			$results .= "<span class='". $hl . "Model'>" . 			$entry['PartDescription'] . "</span>";
			$results .= "<span class='". $hl . "PartDescription'>" .$invEntryDesc	 . "</span>";
			$results .= "<span class='". $hl . "Price'>" . 			number_format($entry['TotalRetail'] ,2) . "</span>";
			$results .= "<span class='". $hl . "Extended'>" . 		number_format(($entry['Quantity'] * $entry['TotalRetail']),2) . "</span>";

			if(strlen($entry['Comment']) > 0){
//				$results .= "\n";
				$results .= "<br />\n";
				$results .= "<span class='". $hl . "Quantity'>&nbsp;</span>";
				$results .= "<span class='". $hl . "Model'>** Note **</span>";
				$results .= "<span class='". $hl . "PartDescription'>" . $entry['Comment'] . "</span>";
				$results .= "<span class='". $hl . "Price'>&nbsp;</span>";
				$results .= "<span class='". $hl . "Extended'>&nbsp;</span>";
				$lncnt++;
			}
			if(strlen($invEntryDesc) > 30)
				$lncnt++;
//			$results .= "<span class='". $hl . "Quantity'>" . 		$entry['Quantity'] . "</span>";
			
//			$results .= dumpDBRecord($entry);
				
			$results .= "<br />". "\n";
			if($lncnt > 1 && ($lncnt > $lineCountPageBreak) )
			{
				$results .= "</div> <!-- End InvoiceEntriesTable -->\n";
				$results .= "\n<div id='InvoiceEntriesTable'>\n";
				$lineCountPageBreak=29;
				$lncnt=0;
			}
			$cnt++;
			$lncnt++;
		}
		$results .= "</div> <!-- End InvoiceEntriesTable -->\n";
		return $results;
	}
	
	function getInvEntryDesc($entry){
		$additions =  $this->invoiceClass->additions($entry['InvoiceEntryID']);
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
		if($totalAdds==0)
			$results .= "&nbsp;";
		return $results;
	}
	
}


?>