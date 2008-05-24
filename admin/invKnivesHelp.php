<?php
include_once "../includes/htmlHead.php";
include_once "../includes/db/db.php";
include_once "../includes/db/db_requests.php";
include_once "../includes/db/Invoices.class.php";

$formValues = getFormValues();
$invoiceNum = $formValues['invoice_num'];

$invoiceClass = new Invoices();
$entries = $invoiceClass->knifeListHelpItems($invoiceNum);

//echo debugStatement(dumpDBRecords($entries));
echo "<TABLE border=1>";
echo "<TR><TH>Part</TH><TH>Quantity</TH><TH>Part</TH><TH>Quantity</TH><TR>";

$cnt = 0;
foreach($entries as $entry){
	echo "<TD>" . $entry['PartCode'] . "</TD>";
	echo "<TD>" . $entry['Cnt'] . "</TD>";
	if($cnt%2 == 1) echo "</TR><TR>";
	echo "\n";
	$cnt++;
}
echo "<TABLE>";

?>