<?php
/* Created on Feb 8, 2006 */
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

$formValues['Year'] = $partsDB->maxPartPriceYear() - 3;
 
echo headSegments("RMK Part Pricing", array("../Style.css", "", "../CustomerReports.css"), "../CustomerReportsPrint.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../admin/NewRMK.js?" . time() . "' ></script>"; ?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php 	
//					echo "Pricing";
//					echo "<br />";
					echo $Parts->partPricingTable($formValues);
//					echo debugStatement(dumpDBRecord($formValues));;
			?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>