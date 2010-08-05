<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "WebPayment.class.php";
include_once DB_INC_DIR. "WebPayments.class.php";

include_once INCLUDE_DIR. "email.php";

$formValues = getFormValues();
$paymentForms = new WebPayment();
$paymentDB = new WebPayments();

$mode=$paymentForms->entryFormMode($formValues);

if($mode == 'review'){
	if(!$paymentDB->validateData($formValues) ){ 
		$formValues['ERROR']=$paymentDB->validationError;	
		$mode='err';
	}
}
if($mode == 'submit'){
	if(!$paymentDB->validateData($formValues) ){ 
		$formValues['ERROR']=$paymentDB->validationError;	
		$mode='err';
	} else {
		$formValues['ordertype']=$paymentForms->requestTypeIDFromLabel("Payment Request");
		$formValues['ccnumber'] = $paymentForms->getUnFormattedCC($formValues['ccnumber']);
		$formValues['amount'] = $paymentDB->fixCurrencyForDB($formValues['amount']);		
		$paymentDB->saveRequest($formValues);
	
		$emailValues['to']=$formValues['email'];
		$emailValues['from']="webmessages@randallknives.com";
		$emailValues['customername']=$formValues['name'];
		$emailValues['subject']="Your order payment for Randall Made Knives";
		$emailValues['message']=$paymentForms->paymentSubmissionResponseText($formValues);

		saveAndSend($emailValues,true);
		$mode='submitted';
	}
}
echo headSegments("Order Payment Submissions", array("../Style.css"), "../print.css");
?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar("Payment Form"); ?>
		<div class="content">
		<?php
			if($mode == 'browse' || $mode == 'err') echo $paymentForms->basicPaymentForm($formValues);
			if($mode == 'review') echo $paymentForms->reviewPaymentRequest($formValues);
			if($mode == 'submitted'){
				echo str_replace("\n","<BR>\n",$paymentForms->paymentSubmissionResponse($formValues));
			} 
//			if($mode == 'submitted') echo $paymentForms->basicPaymentForm($formValues);
			?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
//echo $_SERVER['REMOTE_ADDR'];
//echo isDebugAccess();
//echo debugStatement(dumpDBRecord($dbconfig));
//echo debugStatement($mode);
//echo debugStatement(dumpDBRecord($formValues));
?>