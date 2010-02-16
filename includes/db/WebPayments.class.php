<?php
include_once "db.php";
include_once "BaseDBObject.class.php";

class WebPayments extends BaseDBObject
{
	public $validationError;
	
	function validateData($values){
		$valid=true;
		$requiredFields = array('phone', 'name', 'invoice','ccnumber', 'ccname', "address1", 'city', 'state', 'zip', 'country');
		foreach ($requiredFields as $field){
			if($values[$field] == ""){$this->validationError .= "$field,"; $valid=false;continue;}			
		}

		$ccErrors=$this->validateCreditCardDataBlock($values);
		if($ccErrors != ''){ $this->validationError .= $ccErrors; $valid=false; }

		if(!$this->is_phone($values['phone'])){$this->validationError .= "phone,"; $valid=false;}
		if(!is_numeric($values['invoice'])){$this->validationError .= "invoice,"; $valid=false;}

		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;		
	}
	
	function saveRequest($formValues){
		$formValues['ordertype']=4;		
		unset($formValues["submit"]);
		
		saveRecord("orders", "orders_id", $formValues);
	}
	
//	function checkCC_Date($date){
//		$date = trim($date." ");
//		if(strlen($date) < 4 || strlen($date) > 5) return false;
//		
//		$date = str_replace("\.","-",$date);			
//		$date = str_replace("/","-",$date);			
//		
//		$date = split("-" ,$date);
//		$currYear = date('y');
//		if($date[1] < $currYear) return false; // cc expired
//		if($date[1] > ($currYear + 5)) return false;
//	
//		if($date[0] > 12) return false;	
//		
//		return true;
//	}
}
?>