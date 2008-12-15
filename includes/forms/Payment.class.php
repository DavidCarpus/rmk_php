<?php
include_once "Base.class.php";

class Payment extends Base
{
	public function details($payments){
		$formName="InvoicePaymentDetail";
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>" . "\n" ;
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >\n";
		
		return $results;
	}
	
	function confirmPaymentDelete($payment){
		$formName="InvoicePaymentDeleteConfirm";
		$fields = array("Payment" => "Payment" ,"Number" => "Number" ,"PaymentDate" => "PaymentDate" ,
						"VCode" => "VCode" ,"ExpirationDate" => "ExpirationDate" );
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='GET'>" . "\n" ;
		foreach($fields  as $label=>$field){
			$value = $payment[$field];
			if($label == "PaymentDate" || $label == "ExpirationDate"){
				$value=substr($payment[$field], 0, 10);
			}else if($label == "Payment"){
				$value="$" . number_format($payment[$field] ,2) ;
			}
			$results .= "<span class='label'>$label</span><span class='value'>$value</span>";
			$results .= "<BR>\n";			
		}
		$results .=  "<input type='hidden' name='PaymentID' value='". $payment['PaymentID'] . "'>";
		$results .=  "<BR>" . $this->button("submit", "Confirm Payment Deletion");		
		$results .= "</form>\n";
		$results .= "</div><!-- End $formName -- >\n";
				
		return $results;
	}
		

	function invoiceFinanceTable($costs ){
		$formName="InvoiceFinanceSummary";
		$fields = array("Total" => "TotalCost" , "Discount" => "Discount", "SubTotal" => "Subtotal" , 
						"Shipping" => "Shipping" , "Tax" => "Taxes", "Payments" => "TotalPayments", "Due" => "Due");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $label=>$field){
			if($field == "Discount"  ) 
				$value = $costs["TotalCost"] - $costs["Subtotal"];
			else
				$value = $costs[$field];
			
			if($label == "Shipping" || $label == "Tax" ) $label = "+" . $label;
			elseif($label == "Payments" || $label == "Discount" ) $label = "-" . $label;
			else $label = " " . $label;
			
			$results .= "<span style='font-weight: bold;' class='label'>$label</span>";
			$class = "value";
			if($value < 0) $class = "negValue";
			$results .= "<span class='$class'>$ " .  number_format($value ,2). "</span>";
			$results .= "<BR>";
		}
		
		$results .= "\n</div><!-- End $formName -- >\n";
//		$results .= dumpDBRecord($costs);
		return $results;
	}
	
	function newPaymentEntryForm($invoiceNumber, $formValues=array()){
		$formName="NewInvoicePayment";
		$editMode=false;
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Submit"){
			$editMode=true;
		}
			
		$results="";

		$results .=  "\n<form name='$formName' action='newPayment.php' method='GET' onsubmit='return newPaymentSubmit(this)'>" . "\n" ;
		if($editMode){
			$results .=  "\n<div class='$formName"."Edit'>" . "\n";
		} else{
			$results .=  "\n<div class='$formName' id='NewInvoicePaymentDiv'>" . "\n";
		}

		$fields = array("PaymentDate" , "Payment", "Number" , "VCode" , "ExpirationDate");
		foreach($fields as $name)
		{
			$value="";
			if(array_key_exists($name, $formValues)){
				$value=$formValues[$name];
			} else if($name == "PaymentDate"){
				$value=date("Y-m-d");
			}
			$results .=  "<input class='$name' name='$name' value='$value'>";
		}
		$results .=  "<input type='hidden' name='Invoice' value='$invoiceNumber'>";
		$results .= "\n</div><!-- End $formName -- >\n";
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Submit"){
			$results .=  "<BR>" . $this->button("submit", "Submit");
		} else {
			$results .=  "<BR>" . $this->button("submit", "Add Payment");
		}
		$results .= "</form>";
		
		return $results;
	}
	
	function paymentListTable( $invoiceNumber, $payments, $formValues=array() ){
		$formName="InvoicePayments";
		$fields = array("PaymentDate" , "Payment", "Number" , "VCode" , "ExpirationDate", "Admin");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $field){
			if($field == "ExpirationDate")
				$results .= "<span class='Header$field'>Expires</span>";
			else if($field == "PaymentDate")
				$results .= "<span class='Header$field'>Date</span>";
			else
				$results .= "<span class='Header$field'>$field</span>";
		}
		$results .= "</BR>";
		$results .= "<span id='invoicePayments'>";
		$cnt=1;
		foreach ($payments as $payment){
			if($cnt%2)
				$results .= "<div class='invoicePaymentsHL'>";
			foreach($fields  as $field){
				$results .= "<span class='$field'>";
				if($field == "Admin"){
					$results .= "&nbsp;";
					$results .= "<a href='invoicePaymentsDelete.php?PaymentID=" . $payment['PaymentID'] . "'>Remove</a>";
				}else if($field == "Payment"){
					$results .= "$" . number_format($payment[$field] ,2) ;
				}else if($field == "PaymentDate")	{
					$results .= substr($payment[$field], 0, 10) ;
				}else if($field == "ExpirationDate") {
					$date = substr($payment[$field], 0, 10);
					if($date == "0000-00-00") $date ="";
					$results .= $date;
				} else{
					$results .= $payment[$field] ;
				}
				
				$results .= "&nbsp;</span>\n";
			}
			if($cnt%2)
				$results .= "</div>";
			$cnt++;
			$results .= "</BR>";
		}
		$results .= "</span>";

		$results .= "</BR>";
		$results .= $this->newPaymentEntryForm($invoiceNumber, $formValues);
		$results .= "\n</div><!-- End $formName -- >\n";
//		$results .= dumpDBRecords($payments);
		
//		return count($entries) . " Entries";
		return $results;
	}
	
}
?>