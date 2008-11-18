<?php
include_once "../config.php";


include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Part.class.php";
include_once INCLUDE_DIR. "db/Parts.class.php";

session_start();

$formValues = getFormValues();

$Parts = new Part();
$PartsDB = new Parts();


$mode = $Parts->entryFormMode($formValues);

//$invoice = $invoiceClass->addFormValues($invoice, $formValues);

$part=$PartsDB->blank();
switch ($mode) {
	case "edit":
		$part = $PartsDB->fetchAllPart($formValues['PartID']);
		break;
	case "save":
		$part = $Parts->addFormValues($part, $formValues);
		break;
	default:
		echo debugStatement("Unknown mode: $mode");
		break;
}
//echo debugStatement(dumpDBRecord($formValues) );
//echo debugStatement(dumpDBRecord($part) ); 
//echo debugStatement(dumpDBRecords($part['Prices']) ); 
		
 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">


<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php
				echo $Parts->partEdit($part);
//				echo debugStatement(dumpDBRecord($formValues) ); 
//				echo debugStatement(dumpDBRecords($parts) ); 
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>