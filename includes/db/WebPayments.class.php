<?php
include_once "db.php";
include_once "BaseDBObject.class.php";

class WebPayments extends BaseDBObject
{
	public $validationError;
	
	function validateData($values){
		$valid=true;
		$requiredFields = array('phonenum','invoice','ccnumber','cctype', 'expiration', 'vcode', 'ccname');
		foreach ($requiredFields as $field){
			if($values[$field] == ""){$this->validationError .= "$field,"; $valid=false;continue;}			
		}
	
		if(!$this->validCreditCard($values['cctype'],$values['ccnumber'])){
			$this->validationError .= "ccnumber,"; 
			$this->validationError .= "cctype,"; 
			$valid=false; 
		}
		if(!$this->checkCC_Date($values['expiration'])){$this->validationError .= "expiration,"; $valid=false;}
		if(!$this->is_phone($values['phonenum'])){$this->validationError .= "phonenum,"; $valid=false;}
		if(!is_numeric($values['vcode'])){$this->validationError .= "vcode,"; $valid=false;}
		if(!is_numeric($values['invoice'])){$this->validationError .= "invoice,"; $valid=false;}
		//		if(!is_numeric($values['TotalRetail'])){$this->validationError .= "TotalRetail,"; $valid=false;}
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;		
	}
	
	function checkCC_Date($date){
		$date = trim($date." ");
		if(strlen($date) < 4 || strlen($date) > 5) return false;
		
		$date = str_replace("\.","-",$date);			
		$date = str_replace("/","-",$date);			
		
		$date = split("-" ,$date);
		$currYear = date('y');
		if($date[1] < $currYear) return false; // cc expired
		if($date[1] > ($currYear + 5)) return false;
	
		if($date[0] > 12) return false;	
		
		return true;
	}
}
?>