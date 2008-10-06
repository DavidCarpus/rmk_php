<?php
include_once "Base.class.php";
include_once INCLUDE_DIR. "db/Customers.class.php";
include_once FORMS_DIR. "Customer.class.php";

include_once INCLUDE_DIR. "db/Invoices.class.php";
include_once FORMS_DIR. "Invoice.class.php";

class Search extends Base
{
	function searchScreen($formValues){
			$formName="searchScreen";
			$this_page = basename($_SERVER['REQUEST_URI']);
			if(!array_key_exists('searchValue', $formValues)) $formValues['searchValue'] = "";
			$results="";
			$results .=  "<div id='$formName'>";
			$results .=  "<form name='$formName' action='search.php' method='GET'>" ;
			$JS = array();
	//		$JS['field'] = "onBlur=\"search($formName);\"";
			$results .=  $this->textField('searchValue', $this->fieldDesc('searchValue'), false, $formValues['searchValue'],"",$JS) ;
			if(array_key_exists('customerID', $formValues)){
				$results .=  "<input type='hidden' name='customerID' value='". $formValues['customerID'] . "'>";
			}
			$results .= "</form>";
			$results .= "</div><!-- End $formName -- >\n";
			return $results;
	}
	
	function getSearchType($formValues){
		if(array_key_exists('customerID', $formValues) && is_numeric($formValues['customerID'])) return "customerid";
		
		$searchValue = $formValues['searchValue'];
		if(is_numeric($searchValue)){
			// invoice number search
			return "invoice";
		}
		$searchValue = $formValues['searchValue'];
		$searchValue = str_replace(" ", "",$searchValue);
		$searchValue = str_replace("-", "",$searchValue);
		$searchValue = str_replace("(", "",$searchValue);
		$searchValue = str_replace(")", "",$searchValue);
		
		if(is_numeric($searchValue)){
			// invoice number search
			return "phone";
		}
		
		$searchValue = $formValues['searchValue'];
//		return "Search by customer name";
		$names = explode(",",$searchValue);
		if(count($names) > 1){
			return "last,first";
		}
		$names = explode(" ",$searchValue);
		if(count($names) > 1){
			return "first last";
		}
		return "last";			
	}
	function getSearchResults($formValues){
		// invoice numbers should already be taken care of
		// get customers matching criteria entered
		$searchType = $this->getSearchType($formValues);
//		echo $searchType;
		$custClass = new Customers();
		switch ($searchType) {
//			case "invoice":
//				return "Search by invoice #";
//			break;
			case "phone":
//				echo "Search by Phone Number : " . $formValues['searchValue'];
				return $custClass->fetchCustomersByPhone($formValues['searchValue']);
//				return "Search by Phone Number";
			break;
			case "last,first":
				$searchValue = $formValues['searchValue'];
				$names = explode(",",$searchValue);
				return $custClass->fetchCustomersByFirstAndLast($names[1], $names[0]);
//				return "Search by customer last,first name";
			break;
			case "first last":
				$searchValue = $formValues['searchValue'];
				$names = explode(" ",$searchValue);
				return $custClass->fetchCustomersByFirstAndLast($names[0], $names[1]);
//				return "Search by customer last,first name";
			break;
			case "last":
				return $custClass->fetchCustomersByLname($formValues['searchValue']);
//				return "Search by customer last name";
			break;
			case "customerid":{
				$results = array();
				$results[] = $custClass->fetchCustomer($formValues['customerID']);
				//				debugStatement(dumpDBRecords($results));
				return $results;
//				return "Search by customer last name";
			}
			break;
			
				
			default:
				return "unknown search Criteria";
			break;
		}		
	}
	function displaySearchResults($searchResults, $formValues){
		$customerForms = new Customer();
		$formName="mainSearchResults";
		$results="";
		$results .=  "<div id='$formName'>";
		if(count($searchResults) > 1){
			$results .= $customerForms->customerList($searchResults);
		} else {
			$invoiceForms = new Invoice();
			$invoiceDB = new Invoices();
			$results .= $customerForms->summary( $searchResults[0] );
			$older = (array_key_exists('filter', $formValues) && $formValues['filter'] == 'Older');
			$invoices = $invoiceDB->getCustomerInvoices($searchResults[0]['CustomerID'], $older);
			$results .= $invoiceForms->getCustomerInvoiceList($invoices);

			$results .=  "<div id='customerInvListBtns'>";
			$results .=  "<form name='$formName' action='search.php' method='GET'>" ;
			$results .=  "<input type='hidden' name='searchValue' value='" . $formValues["searchValue"] . "'>";
			if(array_key_exists('customerID', $formValues)){
				$results .=  "<input type='hidden' name='customerID' value='". $formValues['customerID'] . "'>";
			}
			$filter = "Older";
			if($older) $filter = "Newer";
			$results .=  $this->button("filter", $filter);
			$results .= "</form>";
			$results .= "</div><!-- End customerInvListBtns -- >\n";
		}
		$results .= "</div><!-- End $formName -- >\n";
		return $results;
	}
	
}
?>