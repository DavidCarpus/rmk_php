<?php
/* Created on Jan 6, 2009 */

include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";
include_once FORMS_DIR. "Part.class.php";
include_once DB_INC_DIR. "Parts.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}
$formValues = getFormValues();
$Parts = new Part();
$partsDB = new Parts();

$mode=$Parts->pricingEntryFormMode($formValues);
$formValues['mode'] = $mode;

switch ($mode) {
	case "entry":
		break;
	case "validate":
   		$partPrices = $Parts->extractNewPricingFromSubmission($formValues);
		// validate all entries are blank or numeric
		$valid = $Parts->validateNewPricing($partPrices);
		if(!$valid){
			echo "Not Valid";
			$formValues['ERROR']= $Parts->validationError;	
			$formValues['Year'] = $formValues['submit'];
		} else { // If they all are
//			echo "Valid, Redirecting";
   			foreach ($partPrices as $partPrice) { // save them all
   				$partsDB->savePrice($partPrice);
   			}
//			redirect to 'pricing.php'
			header("Location: "."Pricing.php");
			return;
		}
		break;
}
echo "<script type='text/javascript' src='../admin/NewRMK.js?" . time() . "'></SCRIPT>";

?>

<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK href="../CustomerReports.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../CustomerReportsPrint.css">	 

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php 	
//					echo "Pricing - $mode";
//					echo "<br />";
					echo $Parts->newPricingTable($formValues);				
			?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>