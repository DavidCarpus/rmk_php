<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once FORMS_DIR. "Search.class.php";
include_once FORMS_DIR. "Customer.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

<?php

$formValues = getFormValues();
$searchForms = new Search();
if(array_key_exists('searchValue', $formValues) && $searchForms->getSearchType($formValues) == 'invoice'){
	$invoiceNum = $formValues['searchValue'];
	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
}

?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 	
				echo $searchForms->searchScreen($formValues);
				
				$customers = $searchForms->getSearchResults($formValues);
				if(count($customers) == 0){
					$customerForms = new Customer();
					echo $customerForms->newCustomerForm($formValues);
				} else {				
					echo $searchForms->displaySearchResults($customers, $formValues);
				}
				
//				echo debugStatement(dumpDBRecord($formValues));
					?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>