<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Order.class.php";
include_once DB_INC_DIR. "Orders.class.php";

$formValues = getFormValues();

$orderProcessingForms = new Order();
$ordersDB = new Orders();

$mode=$orderProcessingForms->entryFormMode($formValues);

if($mode == 'requestreview'){
	if(!$ordersDB->customerOrderFormValidation($formValues) ){ 
		$formValues['ERROR']=$ordersDB->validationError;	
		$mode='err';
	}
}
if($mode == 'requestsubmit'){
	$formValues['STATUS']=$mode;
}

echo headSegments("Order/Quote Request", array("../Style.css"), "../print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='../includes/customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar("Order Form"); ?>
		<div class="content">
		<?php
			if($mode == 'browse' || $mode == 'err') echo $orderProcessingForms->customerOrderForm($formValues);
			if($mode == 'requestreview' || $mode == 'requestsubmit' ) echo $orderProcessingForms->customerOrderValidation($formValues);			
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