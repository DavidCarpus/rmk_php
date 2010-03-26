<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once FORMS_DIR. "WeeklyReports".".class.php";
include_once DB_INC_DIR. "Invoices" . ".class.php";
include_once INCLUDE_DIR. "class.phpmailer.php";

include_once INCLUDE_DIR. "pdfReports.php";

//if (!authenticate("../")){
//	return;
//}
$formValues = getFormValues();

$rptForm = new WeeklyReports();
$invClass = new Invoices();

$invoices =array();
$pastDueLetter=array();
$mode = $rptForm->entryFormMode($formValues);
if($mode == 'display_orders'){
	$invoices = $invClass->getPastDueInvoices($formValues['date']);
	$pastDueLetter = getSingleDbRecord("Select * from webcopy where page='pastdue'");
}
if($mode == 'Report'){
	$invoices = $invClass->getPastDueInvoices($formValues['date']);
	$pastDueLetter = getSingleDbRecord("Select * from webcopy where page='pastdue'");
	$pdf = new CDealerSpecLetter();
	$pdf->setData($pastDueLetter, $invoices);
	$pdf->createReport();
	$params= array('Content-Disposition'=>'BalanceDueLetter.pdf');
	$pdf->stream($params);
}

echo headSegments("RMK Balance Due Letters", array("../Style.css"), "../print.css");
?>

<body>

<?php echo logo_header("admin"); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo adminToolbar("RMK"); ?>
		<div class="content">
		<?php
			if($mode == "get_date") echo $rptForm->getDealerSpecLetterDate($formValues);
			if($mode == 'display_orders'){
				echo $rptForm->getDealerSpecLetterDate($formValues);
				foreach ($invoices as $invoice) {
					echo "<hr />";
//					echo dumpDBRecord($invoice);
					$letter = $pastDueLetter['prefix'] .$pastDueLetter['postfix'];
					$letter = substitureLetterFields( $letter, $invoice);
					$letter = str_replace("\n", "<br /> ", $letter);
					echo $letter; 
					echo "<br />";
				}
			}
			?>
		</div>	<!-- End content --> 
	 	<?php echo footer(); ?>
	</div>
</div>


<?php
echo debugStatement($mode);
echo debugStatement(dumpDBRecord($formValues));
//echo debugStatement(dumpDBRecords($orderData));
?>

</body>
</html>