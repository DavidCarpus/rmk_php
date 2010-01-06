<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "WebPayment.class.php";
include_once DB_INC_DIR. "WebPayments.class.php";

$formValues = getFormValues();
$paymentForms = new WebPayment();
$paymentDB = new WebPayments();

$mode=$paymentForms->entryFormMode($formValues);

if($mode == 'submit'){
	if(!$paymentDB->validateData($formValues) ){ 
		$formValues['ERROR']=$paymentDB->validationError;	
		$mode='err';
//	} else {
//		$catalogs->saveModel($formValues);
//		$categories=$catalogs->getCategoriesAndModels();
//		$mode='edit';
	}
}
echo headSegments("Order Payment Submissions", array("../Style.css"), "../print.css");
?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar(); ?>
		<div class="content">
		<?php
			echo $paymentForms->basicPaymentForm($formValues);
		?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
echo debugStatement($mode);
echo debugStatement(dumpDBRecord($formValues));
?>