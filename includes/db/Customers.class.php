<?php
include_once "db.php";

class Customers
{
	public $validationError;
	
	function blank(){
		return array(
			'PhoneNumber' => '',
			'LastName' => '',
			'FirstName' =>'',
			'Balance' => 0,
			'Discount' => 0,
			'Dealer' => 0,
			'Flag' => 0,
			'Memo' => '',
			'Prefix' => '',
			'Suffix' => '',
			'Terms' =>  '',
			'TaxNumber' =>'',
			'EMailAddress' => '', 
			'CurrentAddress'=>array('AddressID'=> 0, 
					'AddressType'=> 0, 
					'CustomerID'=> 0, 
					'PrimaryCustomerAddress'=> 0, 
					'CorrectedAddressID'=> 0, 
					'TimesUsed'=> 0)
			
			//'CreditCardNumber' =>
			//'CreditCardExpiration' =>
			//'CurrrentAddress' => Array
			);
	}
	
	function addFormValues($customer, $formValues){
		$fields = array('PhoneNumber', 'LastName', 'FirstName', 'Balance', 
			'Discount', "Dealer", "Flag", "Memo", 'Prefix', 'Prefix', 'Terms', 'TaxNumber', 'EMailAddress');
		foreach($fields as $name)
		{
			if(array_key_exists($name, $formValues))
			{
				$customer[$name] = $formValues[$name];
			}
		}
		$fields = array('AddressID', 'AddressType', 'CustomerID', 'PrimaryCustomerAddress', 'CorrectedAddressID', 'TimesUsed',
					'ADDRESS1', 'ADDRESS2', 'CITY', 'STATE', 'ZIP', 'COUNTRY', 'ZONE');		
		foreach($fields as $name)
		{
			if(array_key_exists($name, $formValues))
			{
				$customer['CurrentAddress'][$name] = $formValues[$name];
			}
		}
		return $customer;
	}
	function isValidPhoneNumber($number){
		if(strlen($number) < 7) return false;
		return true;
	}
	
	function validate($values){
		$valid = true;
		$this->validationError="";
		
		// strip $ from values
		$values['Discount'] = preg_replace("/\\%/", '', $values['Discount']);
		
		if(!is_numeric($values['Discount'])){$this->validationError .= "Discount,"; $valid=false;}
		if(!is_numeric($values['Terms'])){$this->validationError .= "Terms,"; $valid=false;}
		if(!$this->isValidPhoneNumber($values['PhoneNumber'])){$this->validationError .= "PhoneNumber,"; $valid=false;}
		
		// trim extra comma
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
		
	}
	
	function fetchCustomer($custID){
		$query = "Select * from Customers where CustomerID=$custID";
		$customer = getBasicSingleDbRecord("Customers","CustomerID",$custID);
		if($customer && $customer['CurrrentAddress']){
			$address = getBasicSingleDbRecord("Address", "AddressID", $customer['CurrrentAddress']);
			$customer['CurrrentAddress'] = $address ;
		} else {
			echo "System Error: Cannot retrieve Customer: $custID";
		}
		return $customer;
	}
	
	function fetchCustomerForInvoice($invnum){
		$query = "Select C.* from Invoices I inner join Customers C on C.CustomerID=I.CustomerID where I.Invoice=$invnum";
		return getSingleDbRecord($query);
	}
	
	function fetchDealers(){
		$query = "Select C.* from Customers C where Dealer order by LastName";
		return getDbRecords($query);
	}
	function fetchCustomersByLname($lastName){
		$lastName = trim($lastName); 
		$query = "Select C.* from Customers C where C.LastName like '%$lastName%'";
		$query .= " order by C.LastName, C.FirstName";
//		echo $query;
		return getDbRecords($query);
	}
	function fetchCustomersByFirstAndLast($firstName, $lastName){
		$firstName = trim($firstName); 
		$lastName = trim($lastName); 
		$query = "Select C.* from Customers C where C.LastName like '%$lastName%' and C.FirstName like '%$firstName%'";
//		echo $query;
		return getDbRecords($query);
	}
	
	function fetchCustomersByPhone($phone)
	{
		$phone = trim($phone); 
		$query = "Select C.* from Customers C where C.PhoneNumber like '%$phone%'";
//		echo $query;
		return getDbRecords($query);
	}
	function save($customer)
	{
		// remember the address Data
		$address = $customer['CurrentAddress'];
		unset($customer['CurrentAddress']);
		// Save customer
		$customer = saveRecord("Customers", "CustomerID", $customer);
		
		// Update address' customer ID and save
		$address['CustomerID']=$customer['CustomerID'];
		$address = saveRecord("Address", "AddressID", $address);
		// reset the customers 'current' address to what was saved
		$customer['CurrentAddress']=$address;
		return $customer;
	}
}
?>