<?php
include_once "db.php";
include_once "BaseDBObject.class.php";
include_once INCLUDE_DIR. "utils.php";

class WebPayments extends BaseDBObject
{
	public $validationError;
	
	function validateData($values){
		$valid=true;
		$requiredFields = array('phone', 'name', 'email', 'invoice','ccnumber', 'ccname', "address1", 'city', 'state', 'zip');
		if(! isUSZipCode($values['zip'])) $requiredFields[]='country';
		foreach ($requiredFields as $field){
			if($values[$field] == ""){$this->validationError .= "$field,"; $valid=false;continue;}			
		}
		

		$ccErrors=$this->validateCreditCardDataBlock($values);
		if($ccErrors != ''){ $this->validationError .= $ccErrors; $valid=false; }

		$values['amount'] = $this->fixCurrencyForDB($values['amount']);
		
		if(!$this->is_phone($values['phone'])){$this->validationError .= "phone,"; $valid=false;}
		if(!is_numeric($values['invoice'])){$this->validationError .= "invoice,"; $valid=false;}
		if(!is_numeric($values['amount'])){$this->validationError .= "amount,"; $valid=false;}
		
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;		
	}
	
	function saveRequest($formValues){
		$formValues['ordertype']=4;		
		unset($formValues["submit"]);
		$ccNumber = $formValues['ccnumber'];
		$ccNumber = str_replace(" ", "",$ccNumber);
		$ccNumber = str_replace("-", "",$ccNumber);
		$formValues['amount'] = $this->fixCurrencyForDB($formValues['amount']);
	
		$formValues['ccnumber'] = $ccNumber;
				
		return saveRecord("orders", "orders_id", $formValues);
	}
}
?>