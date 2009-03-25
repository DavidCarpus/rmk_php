<?php
include_once "Base.class.php";

class Customer extends Base
{
	function __construct() {
       $this->name = "forms_Customer";
   }
   
   function displayWithFlags($cust){
   		$results="";
   		$results .= "<span style='display: block; float: left; width: 600; clear:right;'>\n\n";
   		$results .= $this->display( $cust );
		$results .= $this->customerFlags( $cust );
		$results .= "</span><!-- End displayWithFlags -- >\n\n";
		return $results;
   }
   
   public function display($request) {
		$formName="CustomerSummaryDisp";
   		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$name = $request["Prefix"] . " " .$request["FirstName"] . " " .$request["LastName"] . " " .$request["Suffix"];
		$custID=urlencode($request['CustomerID']);

		$url = "<a href='customerEdit.php?CustomerID=$custID'>$name</a>\n";
		$results .= $url;

		if($request != NULL && array_key_exists("EMailAddress", $request))
			$results .= $request["EMailAddress"] . "</BR>\n";
		if($request != NULL && array_key_exists("PhoneNumber", $request))
			$results .= $request["PhoneNumber"] . "</BR>\n";
	
		$results .= "</div><!-- End $formName -- >\n";
		
//		$results .= dumpDBRecord($request);
		return $results;
   }
   
   function customerFlags($request){
   		$results =  "<span id='CustomerFlags'>\n";
		if($request != NULL && array_key_exists('Memo', $request) && strlen($request['Memo'])>1){
			$custID = $request['CustomerID'];
			$results .= "<span class='helptext'>";
			$results .= "<a href='customerEdit.php?CustomerID=$custID'>";
			$img = "<img ALIGN='top' src='" . getImagePath("memo.png") . "' border=0>";
			$results .= "$img<span>" . $request["Memo"] . "</span></a>";
			$results .= "</span><!-- End HelpText -- >\n";
		}
		$results .= "</span><!-- End CustomerFlags -- >\n";
		return $results;
   }
   
	public function summary($request, $readonly=false){
		$formName="CustomerSummary";
		
		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>"  . "\n";
//		$results .=  "<legend>$formsName</legend>" . "\n";
//		CustomerID as hidden field?
		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix', 'PhoneNumber', 'EMailAddress');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $request[$name],'',array(), $readonly) . "\n";
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
//			if($request != NULL && !array_key_exists($name, $request)) $request[$name] = "";
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .= $request[$name] . " ";
		}
		
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
		return $results;
	}
	
	public function linkToCustomer($customer){
		$url = "<a href='search.php?CustomerID=" . urlencode($customer['CustomerID']);
		$url .= "'>" .  $customer['PhoneNumber'] . "</a>";
//		return $entry["PartDescription"];
		return $url;
	}
	
	public function entryFormMode($formValues){
		if($formValues['submit']=='New Customer?') return 'validate';
		if(!array_key_exists('CustomerID', $formValues)) return 'new';
				
		if(!array_key_exists('submit', $formValues)) return 'edit';
		if($formValues['submit']=='Update Customer') return 'validate';
		
		return 'unk';
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
	
	function addressForm($formValues){
		$results ="";
//		$results .= debugStatement(dumpDBRecord($formValues));
		
		$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
		}
//		$results .= debugStatement(dumpDBRecord($formValues));
		
		$results .=  "</BR></BR>";
		$fields = array('ADDRESS1', 'ADDRESS2', 'CITY', 'STATE', 'ZIP', 'COUNTRY', 'ZONE');
		foreach( $fields as $name)
		{
			$err=(array_key_exists($name, $errors));
			
			if(!array_key_exists($name, $formValues)) $formValues[$name] = "";
			$results .=  $this->textField($name, $this->fieldDesc($name), $err, $formValues[$name]) . "\n";
			$results .=  "</BR>";
		}
		$fields = array('AddressID', 'AddressType', 'CustomerID', 'PrimaryCustomerAddress', 'CorrectedAddressID', 'TimesUsed');
		foreach( $fields as $name)
		{
			$results .=  $this->hiddenField($name, $formValues[$name]);
		}
		return $results;
	}
	
	function newCustomerForm($formValues){
		$formName="CustomerEdit";
		if(array_key_exists('searchValue', $formValues)) $formValues['LastName'] = $formValues['searchValue'];
		
		$results="";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='customerEdit.php' method='GET'>"  . "\n";

		$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
		}
		
		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix', 'PhoneNumber', 'EMailAddress', 'Memo', 'Terms', 'Discount');
		foreach( $fields as $name)
		{
			$err=(array_key_exists($name, $errors));
			
			if(!array_key_exists($name, $formValues)) $formValues[$name] = "";
			if($name == 'Memo'){
//				$results .=  $this->textArea($name, $label, $required=false, $value='', $large=false);
				$results .=  $this->textArea($name, $this->fieldDesc($name), $err, $formValues[$name], true);
			}
			else{
				$results .=  $this->textField($name, $this->fieldDesc($name), $err, $formValues[$name]) . "\n";
			}
			if($name == 'LastName' && array_key_exists('CustomerID', $formValues) && $formValues['CustomerID'] > 0){
				$results .= "<a href='search.php?CustomerID=" . $formValues['CustomerID'] ."'>Customer Invoices</a>";
			}
			
//			if($this->isInternetExploder() && ($name=="Prefix" || $name=="FirstName" || $name=="Suffix" || $name=="EMailAddress"))
			$results .=  "</BR>";
		}
		$isDealer = ($formValues['Dealer']=='1' || $formValues['Dealer']==1)?1:0;
//				echo debugStatement("Dealer?: $isDealer : " . $formValues['Dealer']);
		$results .=  $this->checkbox('Dealer', 'Dealer', (array_key_exists('Dealer', $errors)), $isDealer );
		$results .=  "</BR>";
		$results .=  $this->textField('TaxNumber', 'TaxNumber', false, $formValues['TaxNumber']) . "\n";
		
		$formValues['CurrrentAddress']['ERROR']=$formValues['ERROR'];
		$results .= $this->addressForm($formValues['CurrrentAddress']);

		if(!array_key_exists('CustomerID', $formValues))
		{
			$results .=  "<BR>" . $this->button("submit", "New Customer?");
		}
		else
		{		
			$results .=  $this->hiddenField('CustomerID', $formValues['CustomerID']);
			$results .=  "<BR>" . $this->button("submit", "Update Customer");		
		}

		$results .=  debugStatement("Flag?</BR>Balance?</BR>CreditCardNumber/CreditCardExpiration?");

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