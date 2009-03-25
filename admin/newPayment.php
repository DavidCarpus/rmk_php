<?php
include_once "../config.php";

include_once INCLUDE_DIR. "db/db.php";
include_once INCLUDE_DIR. "db/db_requests.php";
include_once INCLUDE_DIR. "db/Payments.class.php";
include_once INCLUDE_DIR. "htmlHead.php";

//include_once "../includes/db/db.php";
//include_once "../includes/db/db_requests.php";
//include_once "../includes/htmlHead.php";
//include_once "../includes/db/Payments.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$formValues = getFormValues();
$invoiceNum = $formValues['Invoice'];
$paymentsClass = new Payments();

if($paymentsClass->validatePayment($formValues)){
	if($formValues['ExpirationDate'] == NULL) $formValues['ExpirationDate']=date("Y-m-d");
	$formValues['ExpirationDate'] = $paymentsClass->formatExpirationDate($formValues['ExpirationDate']);
//	echo debugStatement(dumpDBRecord($formValues));
	$paymentsClass->saveNewPayment($formValues);
 	header("Location: "."invoicePaymentsEntryEdit.php?Invoice=$invoiceNum");
} else {
//	echo "Invalid Payment";
	$newURL = "";
	foreach ($formValues as $field=>$value ){
		$newURL .= $field . "=" . urlencode($value) . "&";
	}
	$newURL = substr($newURL,0,strlen($newURL)-1);
	$newLocation  = "invoicePaymentsEntryEdit.php?".$newURL;
	//echo $newLocation;
	header("Location: $newLocation");
}
?>
