<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "links.php";

include_once FORMS_DIR. "Catalog.class.php";
include_once DB_INC_DIR. "Catalogs.class.php";

if (!authenticate("../")){
	return;
}

$catalogs = new Catalogs();
$catalogAdminForms = new Catalog();

$formValues = getFormValues();
$categories=$catalogs->getCategoriesAndModels();
$mode=$catalogAdminForms->entryFormMode($formValues);

if($mode == 'save'){
	if(!$catalogs->validateModel($formValues) ){ 
		$categories['ERROR']=$catalogs->validationError;	
		$mode='err';
	} else {
		$catalogs->saveModel($formValues);
		$categories=$catalogs->getCategoriesAndModels();
		$mode='edit';
	}
}

if($mode == 'uploadphoto'){
	$catalogs->processesFileUploads($formValues);
}

echo headSegments("RMK Catalog Maintenance", array("../Style.css"), "../print.css");
?>

<body>
<?php echo logo_header("admin"); ?>
 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo adminToolbar("Catalog"); ?>
		<div class="content">
		<?php
			echo $catalogAdminForms->categorySelectForm($formValues,$categories);
			if($mode=='edit' || $mode=='err') echo $catalogAdminForms->editModelForm($formValues,$categories);
			if($mode=='category') echo $catalogAdminForms->editCategoryForm($formValues,$categories);
			if($mode=='addphoto') echo $catalogAdminForms->addPhotoForm($formValues,$categories);

			if($mode=='update_Cat') echo $mode . debugStatement(dumpDBRecord($formValues));
		?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
echo debugStatement($mode);
echo debugStatement(dumpDBRecord($formValues));
?>

</body>
</html>