<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once FORMS_DIR. "Search.class.php";
include_once FORMS_DIR. "Customer.class.php";

if (!authenticate()){
	return;
}

$formValues = getFormValues();
$searchForms = new Search();
$searchType = $searchForms->getSearchType($formValues);
$formValues['searchType'] = $searchType;
if($searchType == 'invoice'){
//if(array_key_exists('searchValue', $formValues) && $searchForms->getSearchType($formValues) == 'invoice'){
	$invoiceNum = $formValues['searchValue'];
	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
}

?>
<?php
if(strlen($searchType) > 0)
{
	$customers = $searchForms->getSearchResults($formValues);
}
if(count($customers) == 0 && strlen($formValues['searchValue'])>0){
	header("Location: "."customerEdit.php?LastName=" . $formValues['searchValue']);
	return;
}

//echo debugStatement(dumpDBRecord($formValues));
//echo debugStatement(dumpDBRecords($customers));

echo headSegments("RMK Search", array("../Style.css", "", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../admin/NewRMK.js?" . time() . "' ></script>"; ?>

<?php echo logo_header("admin", ".."); ?>

<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php 	
				echo $searchForms->searchScreen($formValues);
				
				if(count($customers) > 0){
					echo $searchForms->displaySearchResults($customers, $formValues);
				}
//				echo debugStatement(dumpDBRecord($formValues));
					?>
		</div>
	</div>
	<?php echo footer(); ?>
	
</div>

</body>
</html>

	