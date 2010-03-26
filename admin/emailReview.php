<?php  /* * Created on Jan 29, 2007 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
//include_once INCLUDE_DIR. "adminFunctions.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "Emails.class.php";

include_once FORMS_DIR. "Email.class.php";

if (!authenticate()){
	return;
}

$formValues = getFormValues();

$emailForms = new Email();
$emails = new Emails();

$emailData = array();
$mode=$emailForms->entryFormMode($formValues);
switch ($mode) {
	case "browse":
		$startID = (!isset($formValues['startid'])? 999999: $formValues['startid']);
		$emailData = $emails->fetchEmails($startID, 10);
		break;
	case "detail":
		$emailData = $emails->fetchEmail($formValues['email_id'], 1);
		break;
	case "search":
		$emailData = $emails->searchEmails($formValues);
		break;
	default:
		break;
}

echo headSegments("RMK Email Processing", array("../Style.css"), "../print.css");
?>

<body>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("Emails"); ?>
		<div class="content">
			<?php  
//			echo $mode;
			echo $emailForms->searchForm($formValues);
			echo "<br/>";
			if($mode == "browse") echo $emailForms->listEmails($emailData);
			if($mode == "search") echo $emailForms->listEmails($emailData);
			if($mode == "detail") echo $emailForms->emailDetail($emailData[0]);
//			echo debugStatement(dumpDBRecords($emailData));
//			echo debugStatement(dumpServerVariables());
	 		?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>