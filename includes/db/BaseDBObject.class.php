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
}

?>