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
			'EMailAddress' => '' 
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
		return $customer;
	}

	function validate($values){
		$valid = true;
		$this->validationError="";
		
		// strip $ from values
		$values['Discount'] = preg_replace("/\\%/", '', $values['Discount']);
		
		if(!is_numeric($values['Discount'])){$this->validationError .= "Discount,"; $valid=false;}
		if(!is_numeric($values['Terms'])){$this->validationError .= "Terms,"; $valid=false;}
		
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
		return saveRecord("Customers", "CustomerID", $customer);
	}
}
?>