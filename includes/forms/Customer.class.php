<?php
include_once "Base.class.php";

class Customer extends Base
{
	function __construct() {
       $this->name = "forms_Customer";
   }
   
	public function summary($request){
		$formName="CustomerSummary";
		
		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>"  . "\n";
//		$results .=  "<legend>$formsName</legend>" . "\n";
//		customerID as hidden field?
		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix', 'PhoneNumber', 'EMailAddress');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $request[$name]) . "\n";
			if($this->isInternetExploder() && ($name=="Prefix" || $name=="FirstName" || $name=="Suffix" || $name=="EMailAddress"))
					$results .=  "</BR>";
		}
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
		
		return $results;
	}
	
	public function tiny($request){
		$formName="CustomerFullName";
		
		$results="\n";
		$results .=  "<div id='$formName'>\n";

		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .= $request[$name] . " ";
		}
		
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
		return $results;
	}
	
	public function linkToCustomer($customer){
		$url = "<a href='search.php?customerID=" . urlencode($customer['CustomerID']);
		$url .= "'>" .  $customer['PhoneNumber'] . "</a>";
//		return $entry["PartDescription"];
		return $url;
	}
	
	public function customerList($customers){
		$formName="CustomerList";
		$fields = array("FirstName" , "LastName", "PhoneNumber" , "Dealer");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $field){
			$results .= "<span class='Header$field'>$field</span>";
		}
		$results .= "</BR>";
		$cnt=1;
		foreach ($customers as $customer){
			if($cnt%2)
				$results .= "<div class='CustomerListsHL'>";
			foreach($fields  as $field){
				$results .= "<span class='$field'>";
				if($field == 'PhoneNumber'){
					$results .= $this->linkToCustomer($customer);
				}else{
					$results .= $customer[$field] ;
				}
				$results .= "&nbsp;</span>\n";
			}
			if($cnt%2)
				$results .= "</div>";
			$cnt++;
			$results .= "</BR>";
		}

		$results .= "\n</div><!-- End $formName -- >\n";
//		$results .= dumpDBRecords($payments);
		
//		return count($entries) . " Entries";
		return $results;
	}
	
	function newCustomerForm($formValues){
		$formName="NewCustomer";
		if(array_key_exists('searchValue', $formValues)) $formValues['LastName'] = $formValues['searchValue'];
		
		$results="";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='customerEdit.php' method='GET'>"  . "\n";
//		$results .=  "<legend>$formsName</legend>" . "\n";
//		customerID as hidden field?
		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix', 'PhoneNumber', 'EMailAddress');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $formValues[$name]) . "\n";
//			if($this->isInternetExploder() && ($name=="Prefix" || $name=="FirstName" || $name=="Suffix" || $name=="EMailAddress"))
			$results .=  "</BR>";
		}
		$results .=  "<BR>" . $this->button("submit", "New Customer?");		
		
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
//		$results .= debugStatement(dumpDBRecord($formValues));
		
		return $results;
//		$results = "New Customer Form";
//		$results .= "</BR>";
//		$results .= dumpDBRecord($formValues);
//		return $results;
	}
}
?>