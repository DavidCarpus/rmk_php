<?php
class Customers
{
	
	function fetchCustomer($custID){
		$query = "Select * from Customers where CustomerID=$custID";
		$customer = getBasicSingleDbRecord("Customers","CustomerID",$custID);
		if($customer && $customer['CurrrentAddress']){
			$address = getBasicSingleDbRecord("Address", "AddressID", $customer['CurrrentAddress']);
			$customer['CurrrentAddress'] = $address ;
		}
		return $customer;
	}
	
}
?>