<?php
include_once "config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "class.phpmailer.php";
include_once FORMS_DIR. "Email.class.php";
include_once DB_INC_DIR. "Emails.class.php";

$formValues = getFormValues();
$emailProcessingForms = new Email();
$emailsDB = new Emails();

$mode=$emailProcessingForms->entryFormMode($formValues);

switch ($mode) {
	case "submitEmail":
//		$orderData=$emailProcessingForms->addFormValues($orderData,$formValues );
		if($emailsDB->validateSentEmail($formValues))
		{
//			$orderData['searchCriteria']=$orderProcessingForms->getCurrentSearchCriteria($formValues);
//			$orderData['ordertypestring']=$orderProcessingForms->requestTypeFromID($orderData['ordertype']);			
			// TODO: Send email and store in DB
			$emailsDB->saveAndSend($formValues);
		} else {
			$formValues['ERROR']=$emailsDB->validationError;
			echo "Errors Encountered.";
			// TODO: Merge form values into $orderData array.
//			$orderData['searchCriteria']=$orderProcessingForms->getCurrentSearchCriteria($formValues);
//			$orderData['ordertypestring']=$orderProcessingForms->requestTypeFromID($orderData['ordertype']);			
		}
		break;
	default:
		break;
}
echo headSegments("Send us a message", array("Style.css"), "print.css");
?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar("Home"); ?>
		<div class="content">
		<?php
			echo $emailProcessingForms->sendEmailForm($formValues);
		?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
echo debugStatement("Mode:" . $mode);
echo debugStatement(dumpDBRecord($formValues));
//echo debugStatement(dumpDBRecords($orderData));
?>