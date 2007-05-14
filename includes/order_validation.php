<?php
/* Created on Mar 25, 2006 */

function checkDigitCreditCard($cctype, $ccNumber){
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
//	}
//	echo "<BR>" . $ccNumber . "<BR>";
//	echo $sum%10 . "<BR>";
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
	
	
//	echo "<HR>";
//	print_r(date('y'));
//	echo "<HR>";
//	return false;
	return true;
}

function validate_email($email){
   // Create the syntactical validation regular expression
   $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";
   return (eregi($regexp, $email)); // Validate the syntax
}
//$error_code=(ereg("^[0-9]{5}(-[0-9]{4})$",$zip))? NULL:1;

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
?>
