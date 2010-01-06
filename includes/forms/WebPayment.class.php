<?php
include_once "Base.class.php";

class WebPayment extends Base
{
	public $validCCTypes = array('Mastercard' , 'Visa', 'Discover');
	
	public function entryFormMode($formValues)
	{
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Submit Payment"){return "submit";}
//		if(array_key_exists("statusUpdate", $formValues) && $formValues["statusUpdate"] == "UpdateStatus"){return "updatestatus";}
		return "browse";	
	}
	public function basicPaymentForm($formValues){
		$formName="basicPaymentForm";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
		
	   	$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
		}
				
		$fields = array('phonenum'=>'Phone Number', 'invoice'=>'Invoice/Order Number', 
			'ccnumber'=>'Credit Card Number', 'cctype'=>"CreditCard Type",
			'expiration'=>'Expiration Date', 'vcode'=>"VCODE",
			'ccname'=>'Name as it appears on card'
		);
		foreach($fields as $name=>$label)
		{
			$value = $formValues[$name];
			$err = array_key_exists($name, $errors);
			if($name == 'cctype'){
				$results .= $this->optionField($name, $label, $this->validCCTypes, $formValues['cctype'], $err) . "<br/>\n";			
			} else {
				$results .=  $this->textField($name, $label, $err, $value) . "<br/>\n";
			}
		}
	
		$results .=  $this->button("submit", "Submit Payment");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		
		return $results;
	}
}

?>