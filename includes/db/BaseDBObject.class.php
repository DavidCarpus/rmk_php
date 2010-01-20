<?php
class BaseDBObject
{
	public function validCreditCard($cctype, $ccNumber){
		$ccNumber = str_replace(" ", "",$ccNumber);
		$ccNumber = str_replace("-", "",$ccNumber);
	//	if(strlen($ccNumber) < 16) return false;
	
		// Prefix Check
		if($cctype == 'Visa'){
			if($ccNumber[0] != 4) return false;
		} else if($cctype == 'Discover'){
			if($ccNumber[0] != 6) return false;
			if($ccNumber[1] != 0) return false;
			if($ccNumber[2] != 1) return false;
			if($ccNumber[3] != 1) return false;
		} else if($cctype == 'Mastercard'){
			if($ccNumber[0] != 5) return false;
			if(!($ccNumber[1] >= 1 && $ccNumber[1] <= 5)) return false;
		}
	
		
	//	if($cctype == 'Visa' || $cctype == 'Mastercard'){
		$sum = strlen($ccNumber)-1;
		$sum=0;
		$i = strlen($ccNumber)-2;
		for(; $i>=0; $i=$i-2){
			$double = $ccNumber[$i] * 2;
			$sum = $sum + $double%10;
			if(strlen($double) > 1){
				$sum = $sum + floor($double/10);
			}
			$sum = $sum + ($ccNumber[$i+1]);
		}
		if($i + 1 == 0)
			$sum = $sum + ($ccNumber[$i+1]);
		return ($sum%10 == 0);
	}	
	
	function is_phone ($Phone = ""){
		$Num = $Phone;
		$Num = ereg_replace("([     ]+)","",$Num);  // strip spaces
		$Num = eregi_replace("(\(|\)|\-|\+)","",$Num);
		if( (0+$Num) == 0) return false;
		
		if ( (strlen($Num)) < 7)   return false;
		
		// 000 000 000 0000
		 // CC  AC PRE SUFX = max 13 digits
		if( (strlen($Num)) > 13)        return false;
	
	    return true;
	}
	
	function validateEmail($email){
//		return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	 // Obtained from http://www.linuxjournal.com/article/9585		
	  // First, we check that there's one @ symbol, 
	  // and that the lengths are right.
	  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
	    // Email invalid because wrong number of characters 
	    // in one section or wrong number of @ symbols.
	    return false;
	  }
	  // Split it into sections to make life easier
	  $email_array = explode("@", $email);
	  $local_array = explode(".", $email_array[0]);
	  for ($i = 0; $i < sizeof($local_array); $i++) {
	    if
	(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
	↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
	$local_array[$i])) {
	      return false;
	    }
	  }
	  // Check if domain is IP. If not, 
	  // it should be valid domain name
	  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
	    $domain_array = explode(".", $email_array[1]);
	    if (sizeof($domain_array) < 2) {
	        return false; // Not enough parts to domain
	    }
	    for ($i = 0; $i < sizeof($domain_array); $i++) {
	      if
	(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
	↪([A-Za-z0-9]+))$",
	$domain_array[$i])) {
	        return false;
	      }
	    }
	  }
//	  return true;
	 // We are going to require an @ since these will not be 'local' email addresses 
	  	if(strrpos($email,"@")){
//			echo "Validated email: $email ";
	  		return true;
	  	}
//	  	echo "Email not valid: $email ";
	  	return false;
		}
		
	function validateCreditCardDataBlock($formValues){
		$errs="";
		if($formValues['ccnumber'] == "" || !$this->validCreditCard($formValues['cctype'], $formValues['ccnumber'])){
			$errs .= "cctype,"; 
			$errs .= "ccnumber,"; 
		}
		if($formValues['ccvcode'] == "" || !is_numeric($formValues['ccvcode'])){
			$errs .= "ccvcode,"; 
		}
		if(! $this->checkCC_Date($formValues['ccexpire'])){
			$errs .= "ccexpire,";
		}
		return $errs;
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