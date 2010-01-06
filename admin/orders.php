<?php /* Created on Jan 2, 2010 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "Order.class.php";
include_once DB_INC_DIR. "Orders.class.php";

if (!authenticate("../")){
	return;
}

$formValues = getFormValues();
$orderProcessingForms = new Order();
$orders = new Orders();

$orderData = array();
$orderCounts = array();
$mode=$orderProcessingForms->entryFormMode($formValues);

switch ($mode) {
	case "browse":
		break;
	case "updatestatus":
		$orders->updateStatus($formValues['orders_id'], $formValues['processed'], $orderProcessingForms->htmlizeFormValue($formValues['comment']));
		$orderData = $orders->search($formValues);
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

<?php echo logo_header("admin"); ?>

 <div class="mainbody">
	<div class="centerblock">
	 	<?php echo adminToolbar(); ?>
		<div class="content">
		<?php
			echo $orderProcessingForms->displayUnprocessedCounts($orderCounts);
			echo $orderProcessingForms->searchForm($formValues);
			echo $orderProcessingForms->listItems($orderData, $formValues);
		?>
		</div>	
	 	<?php echo footer(); ?>
	</div>
</div>

<?php
echo debugStatement($mode);
echo debugStatement(dumpDBRecord($formValues));
?>