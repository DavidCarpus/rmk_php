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
	
	public function paymentSubmissionResponse($formValues){
		$responseDiv="paymentSubmissionResponse";
		$results="";
		$results .=  "<div id='$responseDiv'>" . "\n";
		
		$results .= "We have received yor payment request and should be processing it within 5 business days.";
		$results .= dumpDBRecord($formValues);
		
		$results .= "</div><!-- End $responseDiv -->\n";
		
		return $results;		
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
//		echo "Errors:" . dumpDBRecord($errors);
				
		$fields = array('phone'=>'Phone Number', 'invoice'=>'Invoice/Order Number' );
		foreach($fields as $name=>$label)
		{
			$value = $formValues[$name];
			$options=array();
			if(array_key_exists($name, $errors)) $options['error']=true;

			$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
		}
		
		$results .= $this->creditCardFormBlock($formValues, $this->creditCardOptions, false);
		
		$fields = array("address1"=>"Billing Address", "address2"=>"&nbsp;", "address3"=>"&nbsp;", 
			"city"=>"City", "state"=>"State/Province", "zip"=>"Zip/Postal Code",	"country"=>"Country"
		);
		foreach($fields as $name=>$label)
		{
			$value = $formValues[$name];
			$options=array();
			if(array_key_exists($name, $errors)) $options['error']=true;

			$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
		}
		
		$results .=  $this->button("submit", "Submit Payment");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		
		return $results;
	}
}

?>