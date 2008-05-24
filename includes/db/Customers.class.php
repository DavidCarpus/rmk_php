<?php
include_once "db.php";

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
	
	function fetchCustomerForInvoice($invnum){
		$query = "Select C.* from Invoices I inner join Customers C on C.CustomerID=I.CustomerID where I.Invoice=$invnum";
		return getSingleDbRecord($query);
	}
}
?>