<?php
/* Created on Feb 8, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";
include_once INCLUDE_DIR. "links.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";
include_once DB_INC_DIR. "Parts.class.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Parts.class.php";

include_once FORMS_DIR. "Shop.class.php";
include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "InvoiceEntry.class.php";
include_once FORMS_DIR. "Customer.class.php";
include_once FORMS_DIR. "Part.class.php";

session_start();

$invoiceClass = new Invoices(); 
$customerClass = new Customers();

$invoiceForms = new Invoice();
$invoiceEntryForms = new InvoiceEntry();
$customerForms = new Customer();
$partsFormClass = new Part();

$formValues = getFormValues();

$mode=$invoiceForms->entryFormMode($formValues);
$formValues['mode'] = $mode;
//echo debugStatement(dumpDBRecord($formValues));

switch ($mode) {
	case "new":
		$invoice=$invoiceClass->blank();
		$customer = $customerClass->fetchCustomer($formValues['CustomerID']);
		$invoice['CustomerID'] = $customer['CustomerID'];
		break;
	case "edit":
		$invoiceNum = $formValues['Invoice'];
		if($invoiceNum == 0 ) 	$invoiceNum = $formValues['invoice_num'];
		$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
		$invoice = $invoiceClass->details( $invoiceNum );
		$entries = $invoiceClass->items($invoiceNum);
		$invoice['CustomerID'] = $customer['CustomerID'];
		$invoice["KnifeCount"] = $invoiceClass->computeKnifeCount($entries);
		break;
	case "validate":
		$invoice=$invoiceClass->blank();
		$invoice = $invoiceClass->addFormValues($invoice, $formValues);
		$customer = $customerClass->fetchCustomer($formValues['CustomerID']);
		$invoice['TotalRetail'] = preg_replace("/\\$/", '', $invoice['TotalRetail']);
		$invoice['ShippingAmount'] = preg_replace("/\\$/", '', $invoice['ShippingAmount']);
		$invoice['TotalRetail'] = preg_replace("/\\,/", '', $invoice['TotalRetail']);
		$invoice['ShippingAmount'] = preg_replace("/\\,/", '', $invoice['ShippingAmount']);
		$invoice['CustomerID'] = $formValues['CustomerID'];
		$invoiceNum = $invoice['Invoice'];
		
//		echo debugStatement(__FILE__ .":". __FUNCTION__.":" . dumpDBRecord($invoice));
		$valid = $invoiceClass->validateNew($invoice);
		if(!$valid){
			$invoice['ERROR']= $invoiceClass->validationError;	
			$mode="err";			
		} else {
			$knifeCnt = $invoice['KnifeCount']; // Need to save since it is 'unset' upon saving			
			$invoice = $invoiceClass->save($invoice);
			$invoice['KnifeCount'] = $knifeCnt;
			$entries = $invoiceClass->items($invoiceNum);
			unset($formValues["submit"]);
			$mode="edit";
		}
		break;	
	default:
		break;
}

echo headSegments("RMK Edit Invoice", array("../Style.css", "", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "' ></script>"; ?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php
					echo rmkHeaderLinks(array("ACK"=>$invoiceNum,"INV"=>$invoiceNum), $customer['Dealer']);
				 	echo $invoiceForms->invNum( $invoice );
					echo "\n";
					echo "\n";
//					debugStatement( $invoiceNum );
					echo $customerForms->displayWithFlags( $customer );					
					echo "\n";
					echo "\n";
					echo $invoiceForms->details( $invoice, $mode );
					echo "\n";
					echo "\n";
					if($mode == "edit")
					{
						echo $invoiceForms->buttonLinks( $invoice );					
						echo "\n";
						echo "\n";
						$entries = $invoiceClass->itemsWithAdditions( $invoice['Invoice'] ); // 56031
						echo $invoiceEntryForms->knifeListTable( $entries, 0 );
						$formValues['Invoice'] = $invoice['Invoice'];
						echo $invoiceEntryForms->InvoiceEntryEditForm($formValues, $partsFormClass);
					}
//					echo debugStatement(dumpDBRecord($formValues)); // $invoice
//					echo debugStatement(dumpDBRecord($invoice) ); 
//					echo debugStatement(dumpDBRecord($customer) ); 
					?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
