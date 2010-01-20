<?php /* Created on Jan 2, 2010 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Order.class.php";
include_once DB_INC_DIR. "Orders.class.php";
include_once FORMS_DIR. "Email.class.php";
include_once DB_INC_DIR. "Emails.class.php";
include_once INCLUDE_DIR. "class.phpmailer.php";

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
	case "search":
		$orderData = $orders->search($formValues);
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
	 	<?php echo adminToolbar(); ?>
		<div class="content">
		<?php
			if($mode=='email' || $mode=='submitEmail'){
				echo	$emailProcessingForms->emailForRequest($orderData);
				echo	$orderProcessingForms->listItem($orderData, "", true);
			} else {
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
