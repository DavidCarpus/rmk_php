<?php
include_once "config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Catalog.class.php";
include_once DB_INC_DIR. "CatalogRequests.class.php";

$formValues = getFormValues();
$catalogAdminForms = new Catalog();
$catalogRequestDB = new CatalogRequests();

$mode=$catalogAdminForms->entryFormMode($formValues);

if($mode == 'request_Cat'){
	if(!$catalogRequestDB->validateCustomerCatalogRequest($formValues) ){ 
		$formValues['ERROR']=$catalogRequestDB->validationError;	
		$mode='err';
	} else {
		$response = $catalogRequestDB->saveRequest($formValues);
		$mode='requestSubmitted';
//		$categories=$catalogs->getCategoriesAndModels();
//		$mode='edit';
	}
}

echo headSegments("Catalog Request", array("Style.css"), "print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='../includes/customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo toolbar(); ?>
		<div class="content">
		<?php
			if($mode != 'requestSubmitted')	echo $catalogAdminForms->customerCatalogRequest($formValues);
			if($mode == 'requestSubmitted')	echo $response;
			?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
echo debugStatement($mode);
echo debugStatement(dumpDBRecord($formValues));
//echo debugStatement(dumpDBRecords($orderData));
?>