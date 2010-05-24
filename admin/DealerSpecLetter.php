<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once FORMS_DIR. "WeeklyReports".".class.php";
include_once DB_INC_DIR. "Invoices" . ".class.php";
include_once INCLUDE_DIR. "class.phpmailer.php";

include_once INCLUDE_DIR. "pdfReports.php";

if (!authenticate("../")){
	return;
}
$formValues = getFormValues();

$rptForm = new WeeklyReports();
$invClass = new Invoices();

$invoices =array();
$dealerSpecLetter=array();

$mode = $rptForm->entryFormMode($formValues);

if($mode == 'display_orders'){
	$invoices = $invClass->getUnSpecefiedDealerOrders($formValues['date']);
	$dealerSpecLetter = getSingleDbRecord("Select * from webcopy where page='dealerspec'");
}
if($mode == 'Report'){
	$invoices = $invClass->getUnSpecefiedDealerOrders($formValues['date']); 
	$dealerSpecLetter = getSingleDbRecord("Select * from webcopy where page='dealerspec'");
	$pdf = new CDealerSpecLetter();
	$pdf->setData($dealerSpecLetter, $invoices);
	$pdf->createReport();
	$params= array('Content-Disposition'=>'DealerSpecLetter.pdf');
	$pdf->stream($params);
}

echo headSegments("RMK Dealer Spec Letters", array("../Style.css"), "../print.css");
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
					$letter = $dealerSpecLetter['prefix'] .$dealerSpecLetter['postfix'];
					$letter = substitureLetterFields( $letter, $invoice);
					$letter = str_replace("\n", "<br /> ", $letter);
					echo "<hr />";
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