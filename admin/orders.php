<?php /* Created on Jan 2, 2010 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Order.class.php";
include_once DB_INC_DIR. "Orders.class.php";
include_once FORMS_DIR. "Email.class.php";
include_once DB_INC_DIR. "Emails.class.php";
include_once INCLUDE_DIR. "class.phpmailer.php";

include_once INCLUDE_DIR. "pdfReports.php";

if (!authenticate("../")){
	return;
}

$formValues = getFormValues();
$orderProcessingForms = new Order();
$orders = new Orders();
$emailProcessingForms = new Email();
$emailsDB = new Emails();

$orderData = array();
$orderCounts = array();
$mode=$orderProcessingForms->entryFormMode($formValues);

switch ($mode) {
	case "browse":
		break;
	case "email":
		$orderData = $orders->getSingleRequest($formValues['orders_id']);
		if(array_key_exists("search_criteria", $formValues)){
			$criteria = explode(",", $formValues['search_criteria']);
//			echo dumpDBRecord($criteria);
			$orderData['search_criteria']=$orderProcessingForms->getDataAsHiddenFields($formValues, $criteria);
			$orderData['search_criteria'].= $orderProcessingForms->hiddenField("search_criteria", $formValues['search_criteria'])."\n";
		}
		$orderData['ordertypestring']=$orderProcessingForms->requestTypeFromID($orderData['ordertype']);
//		echo dumpDBRecord($orderData);
		break;
	case "updatestatus":
//		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header("Expires: 0"); // Date in the past
//		header("pragma: no-cache"); 
		$orders->updateStatus($formValues['orders_id'], $formValues['processed'], $orderProcessingForms->htmlizeFormValue($formValues['comment']));
		// get original search
		$formValues = $orderProcessingForms->originalSearchCritera($formValues);
		$formValues['submitButton']="Search";
		$orderData = $orders->search($formValues);
		break;
	case "submitEmail":
		$orderData=$emailProcessingForms->addFormValues($orderData,$formValues );
		if($emailsDB->validateSentEmail($formValues))
		{
//			$orderData['searchCriteria']=$orderProcessingForms->getCurrentSearchCriteria($formValues);
//			$orderData['ordertypestring']=$orderProcessingForms->requestTypeFromID($orderData['ordertype']);			
			// TODO: Send email and store in DB
			$emailsDB->saveAndSend($formValues);
//			if(isDebugMachine() ){ 
//				print $emailsDB->emailSent;
//			}
			$formValues = $orderProcessingForms->originalSearchCritera($formValues);
			$orderData = $orders->search($formValues);
			$mode='search';
		} else {
			echo "Adding email form data";
			$orderData['ERROR']=$emailsDB->validationError;
			// TODO: Merge form values into $orderData array.
			$orderData['search_criteria']=$orderProcessingForms->getCurrentSearchCriteria($formValues);
			$orderData['ordertypestring']=$orderProcessingForms->requestTypeFromID($orderData['ordertype']);			
		}
		break;
	case "generatePDF":
//		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header("Expires: 0"); // Date in the past
//		header("pragma: no-cache"); 
		$orderData = $orders->search($formValues, true);
		$pdf = new CwebOrderReport();
		$pdf->setData($orderData);

		$nolabels=false;
		if($formValues['requesttype']==1) $nolabels=true; // Quote requests
		if($formValues['requesttype']==2) $nolabels=true; // Order Requests
		
		$pdf->createReport($nolabels);

//		if(0){
//			echo debugStatement(dumpDBRecords($orderData));
//			$pdfcode = $pdf->output(1);
//			$pdfcode = str_replace("\n","\n<br />",htmlspecialchars($pdfcode));		  
//			echo '<html><body>';		  
//			echo trim($pdfcode);		  
//			echo '</body></html>';
//		} else {
			$params= array('Content-Disposition'=>'WebOrders.pdf');
			$pdf->stream($params);
//		}
		break;
	case "processAndPrint":
//		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header("Expires: 0"); // Date in the past
//		header("pragma: no-cache"); 
		$orderData = $orders->search($formValues, true);
		$orders->markAllProcessed($orderData, $orderProcessingForms->statusIDFromDesc("Processed"));
		$orders->clearOldCCNumbers();
		$pdf = new CwebOrderReport();
		$pdf->setData($orderData);
		$nolabels=false;
		if($formValues['requesttype']==1) $nolabels=true; // Quote requests
		if($formValues['requesttype']==2) $nolabels=true; // Order Requests
		
		$pdf->createReport($nolabels);
		$params= array('Content-Disposition'=>'WebOrders.pdf');
		$pdf->stream($params);
		break;
		
	case "search":
//		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//		header("Expires: 0"); // Date in the past
//		header("pragma: no-cache"); 
		$orderData = $orders->search($formValues);
//<meta http-equiv="cache-control" content="no-cache"> <!-- tells browser not to cache -->
//<meta http-equiv="expires" content="0"> <!-- says that the cache expires 'now' -->
//<meta http-equiv="pragma" content="no-cache"> <!-- says not to use cached stuff, if there is any -->
		break;
	default:
		break;
}
$orderCounts = $orders->getUnprocessedCounts($orderProcessingForms->requestTypeOptions);

echo headSegments("RMK Order/Quote Processing", array("../Style.css"), "../print.css");
?>

<body>

<?php echo logo_header("admin"); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo adminToolbar("Orders"); ?>
		<div class="content">
		<?php
			if($mode=='email' || $mode=='submitEmail'){
				echo	$emailProcessingForms->emailForRequest($orderData);
				echo	$orderProcessingForms->listItem($orderData, "", true);
			} else {
//				echo debugStatement(dumpDBRecords($orderData));
				echo $orderProcessingForms->displayUnprocessedCounts($orderCounts);
				echo $orderProcessingForms->searchForm($formValues);
				echo $orderProcessingForms->listItems($orderData, $formValues);
			}
		?>
		</div>	
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
