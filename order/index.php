<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Order.class.php";
include_once DB_INC_DIR. "Orders.class.php";

include_once INCLUDE_DIR. "email.php";

$formValues = getFormValues();

$orderProcessingForms = new Order();
$ordersDB = new Orders();

$mode=$orderProcessingForms->entryFormMode($formValues);
$submissionResponse = "";

if($mode == 'requestreview'){
	if(!$ordersDB->customerOrderFormValidation($formValues) ){ 
		$formValues['ERROR']=$ordersDB->validationError;	
		$mode='err';
	}
	
}
if($mode == 'requestsubmit'){
	// convert text  string (Order, Quote) to ID
	$orderTypeStr=$formValues['ordertype'] . " Request";
	$formValues['ordertype']=$orderProcessingForms->requestTypeIDFromLabel($orderTypeStr);
	
	$formValues['ccnumber'] = $orderProcessingForms->getUnFormattedCC($formValues['ccnumber']);		
	$ordersDB->saveRequest($formValues);
	
	$emailValues['to']=$formValues['email'];
	$emailValues['from']="webmessages@randallknives.com";
	$emailValues['customername']=$formValues['name'];
	$emailValues['subject']="Your $orderTypeStr with Randall Made Knives";
	if($formValues['ordertype']==1){
		$emailValues['message']=strip_tags($orderProcessingForms->quoteSubmissionResponseText($formValues));
		$submissionResponse=$orderProcessingForms->quoteSubmissionResponse($formValues);
	}
	if($formValues['ordertype']==2){
		$emailValues['message']=strip_tags($orderProcessingForms->orderSubmissionResponseText($formValues));
		$submissionResponse=$orderProcessingForms->orderSubmissionResponse($formValues);
	}
	
	saveAndSend($emailValues,true);

	$mode='submitted';
}


echo headSegments("Order/Quote Request", array("../Style.css"), "../print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='../Customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar("Order Form"); ?>
		<div class="content">
		<?php
			if($mode == 'browse' || $mode == 'err') echo $orderProcessingForms->customerOrderForm($formValues);
			if($mode == 'requestreview' || $mode == 'requestsubmit' ) echo $orderProcessingForms->customerOrderValidation($formValues);
			if($mode == 'submitted'){
				echo str_replace("\n","<BR>\n",$submissionResponse);
			}
		?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
//echo debugStatement($mode);
//echo debugStatement(dumpDBRecord($formValues));
//echo debugStatement(dumpDBRecords($orderData));
?>