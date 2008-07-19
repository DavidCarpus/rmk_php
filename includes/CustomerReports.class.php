<?php
include_once "../config.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

class CustomerReports
{
	public $invoiceClass;
	public $customerClass;
	
	public function __construct() {
       $this->invoiceClass = new Invoices();
       $this->customerClass = new Customers();
	}
   
	function acknowledgment($formValues){
		$results= "";
		$invoice = $this->invoiceClass->details($formValues['Invoice']);
		$customer = $this->customerClass->fetchCustomer($invoice['CustomerID']);
		
		
		$results .= "<div id='customerReportHeader'>";
		$results .= $this->dateAndContact($invoice, $customer);
		$results .= $this->billandShipAddress($invoice, $customer);
		$results .= "</div>";
		
//		$results .= debugStatement(  dumpDBRecord($invoice ) );
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
			$results .= "</BR>";
		}
		$results .= "</div>";
		$results .= "<div class='rightHalf'>";
		foreach ($valuesR as $field=>$value) {
			$results .= "<span class='Label'>" . $value[0] . "</span>";
			$results .= "<span class='value'>" . $value[1] . "</span>";
			$results .= "</BR>";
		}
		$results .= "</div>";
		$results .= "</div>";
		return $results;
	}
	
	function billandShipAddress(array $invoice, array $customer){
		$results= "";
		$currAdd = $customer['CurrrentAddress'];

		$results .= "<span id='customerReportAddressHeader'>";
		
		$address = $customer['FirstName'] . " ";
		$address .= $customer['LastName'];
		$address .= "</BR>";
		
		if($currAdd['ADDRESS0'] <> '') $address .= $currAdd['ADDRESS0'] . "</BR>";
		if($currAdd['ADDRESS1'] <> '') $address .= $currAdd['ADDRESS1'] . "</BR>";
		if($currAdd['ADDRESS2'] <> '') $address .= $currAdd['ADDRESS2'] . "</BR>";

		$address .= $currAdd['CITY'] . ", ". $currAdd['STATE'] . " ". $currAdd['ZIP'];

		$results .= "<div class='leftHalf'>";
		$results .= $address  . "</BR>";
		$results .= "</div>";
				
		$billAddress=$address;
		if($invoice['BillingAddressType'] == 1) $billAddress="SHOP SALE";
		if($invoice['ShippingAddressType'] == 2) $billAddress="SAME";
		if($invoice['ShippingAddressType'] == 3) $billAddress="PICK UP";
		
		$results .= "<div class='rightHalf'>";
		$results .= $billAddress . "</BR>";
		$results .= "</div>";
				
		if($currAdd['COUNTRY'] <> '') $results .= $currAdd['COUNTRY'] . "</BR>";

		$results .= "</span>";
		
//		$results .= debugStatement(  dumpDBRecord($customer ) );
		$results .= debugStatement(  dumpDBRecord($invoice ) );
		return $results;
	}
	
}


?>