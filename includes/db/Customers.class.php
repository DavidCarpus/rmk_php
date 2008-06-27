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
}
?>