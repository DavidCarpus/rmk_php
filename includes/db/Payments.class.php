<?php
include_once "db.php";

class Payments
{
	var $validationError;
	
	function fetchPayment($paymentID){
		return getBasicSingleDbRecord("Payments", "PaymentID", $paymentID);
	}
	
	function deletePayment($paymentID){
		$sql = "delete from Payments where PaymentID=$paymentID";
		executeSQL($sql);
	}
	
	function validatePayment($values){
		$isCC=false;
		$ccNumber = $values['Number'];
		$ccNumber = str_replace(" ", "",$ccNumber);
		$ccNumber = str_replace("-", "",$ccNumber);
		
		if(is_numeric($ccNumber) ){
			if(strlen($ccNumber) > 6) $isCC=true;
		}
		if($isCC && !$this->validateCC($values) ){
			echo debugStatement($this->validationError);
			return false;
		}
		if($isCC) return true;
		
//		debugStatement("Not CC");
		return true;
	}
	
	function validateCC($values){
		$ccNumber = $values['Number'];
		$ccNumber = str_replace(" ", "",$ccNumber);
		$ccNumber = str_replace("-", "",$ccNumber);
		if(strlen($ccNumber) < 16) { $this->validationError="CC # to short"; return false;}

		$cctype = $this->getCCType($values['Number']);
		if($cctype == "unknown") { $this->validationError="Invalid CC Type"; return false;}
		if(!is_numeric($values['VCode']) ) { $this->validationError="Invalid VCode"; return false;}
		if(!$this->validExpirationDate($values['ExpirationDate']) ) { $this->validationError="Invalid Expration Date"; return false;}
		if(!$this->validateNumber($ccNumber) ) { $this->validationError="Invalid Number"; return false;}
		
//		echo debugStatement("Valid CC");
		return true;
	}
	
	function getCCType($ccNumber){
		if($ccNumber[0] == 4) return "visa";
		if(substr($ccNumber,0,4) == "6011") return "discover";

		if($ccNumber[0] == 5 && ($ccNumber[1] >= 1 && $ccNumber[1] <= 5)) return "mastercard";
		
		return "unknown";
	}
	
	function formatExpirationDate($date){
		$date = str_replace(" ", "-",$date);
		$date = str_replace("/", "-",$date);
		$dateParts = split("-", $date);
		if(strlen($dateParts[0]) <= 2 && strlen($dateParts[1]) == 4) $date = $dateParts[1] . "-" . $dateParts[0] . "-01";
		if(strlen($dateParts[0]) == 2 && strlen($dateParts[1]) == 2) $date = "20" . $dateParts[1] . "-" . $dateParts[0] . "-01";
		return date("Y-m-d", strtotime($date) );
	}

	
	function validExpirationDate($date){
		$date = str_replace(" ", "-",$date);
		$date = str_replace("/", "-",$date);
		$dateParts = split("-", $date);
//		echo debugStatement(dumpDBRecord($dateParts ));
		if(strlen($dateParts[0]) <= 2 && strlen($dateParts[1]) == 4) $date = $dateParts[1] . "-" . $dateParts[0] . "-01";
		if(strlen($dateParts[0]) == 2 && strlen($dateParts[1]) == 2) $date = "20" . $dateParts[1] . "-" . $dateParts[0] . "-01";
		
		$time_diff = (strtotime($date) - time());
//		echo debugStatement($time_diff . " : " . date("Y-m-d", strtotime($date)) . " : " . $date);
		return ($time_diff > 0 );
//		echo debugStatement(date("Y-m-d", strtotime($date)));
	}
	function validateNumber($ccNumber){
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
	
	function saveNewPayment($values){
		echo "saveNewPayment";
		$values['Number'] = str_replace(" ", "",$values['Number']);
		$values['Number'] = str_replace("-", "",$values['Number']);
		if($values['ExpirationDate'] == '') $values['ExpirationDate']=$values['PaymentDate'];
		$payment = array("Invoice"=>$values['Invoice'],"Number"=>$values['Number'],"PaymentDate"=>$values['PaymentDate'],
							"ExpirationDate"=>$values['ExpirationDate'],"Payment"=>$values['Payment'],"VCode"=>$values['VCode']);
		echo debugStatement(dumpDBRecord($payment));
		saveRecord("Payments", "PaymentID", $payment);
//		$sql = insertRecordSQL($payment, "PaymentID", "Payments");
//		echo $sql;
	}
	
	function getLastCC_ForInvoice($invoiceNum){
		$record = getSingleDbRecord("SELECT Number FROM Payments P where P.`Invoice`=$invoiceNum and length(Number) > 10 order by PaymentDate DESC");
		if(sizeof($record) > 0)
			return $record['Number'];
		else
			return "TODO:getLastCC-$invoiceNum";
	}
}
?>