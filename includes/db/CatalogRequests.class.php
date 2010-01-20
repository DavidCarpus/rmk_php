<?php
include_once "db.php";

include_once "BaseDBObject.class.php";

class CatalogRequests  extends BaseDBObject
{
	public $validationError;
	
	public function validateCustomerCatalogRequest($formValues){
		$valid=true;
		$requiredFields = array('name', 'email', 'address1', 'city', 'state', 'country', 'zip', 'phone');
		foreach ($requiredFields as $field){
			if($formValues[$field] == ""){$this->validationError .= "$field,"; $valid=false;}			
		}
		if($this->creditCardRequired($formValues)){
			$ccErrors=$this->validateCreditCardDataBlock($formValues);
			if($ccErrors != ''){
				$this->validationError .= $ccErrors;
				$valid=false;
			}
		}
//		$this->validCreditCard()		
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
	}
	
	public function creditCardRequired($formValues){		
		$country = $formValues['country'];
		$country = strtoupper($country);
		if($country == 'US' || $country == 'USA' ) return false;

		if($country == 'CA' || $country == 'CANADA' ) return true;
		
		$zip= $formValues['zip'];
		$zip = str_replace("-", "",$zip);
		if($zip != '' && !is_numeric($zip)){return true;}
		
		return false;
	}

	function saveRequest($formValues){
		$formValues['ordertype']=3;		
		unset($formValues["submit"]);
//		echo dumpDBRecord($formValues);
		
		saveRecord("orders", "orders_id", $formValues);
		
		$requestResponse = "Your Catalog request has been submitted.";
		
		return $requestResponse; 
	}
}
?>