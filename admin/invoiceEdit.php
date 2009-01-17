<?php
/* Created on Feb 8, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";

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
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}
 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

<?php
$invoiceClass = new Invoices(); 
$customerClass = new Customers();

$invoiceForms = new Invoice();
$invoiceEntryForms = new InvoiceEntry();
$customerForms = new Customer();
$partsFormClass = new Part();

$formValues = getFormValues();

$mode=$invoiceForms->entryFormMode($formValues);
$formValues['mode'] = $mode;

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
		
		$valid = $invoiceClass->validateNew($invoice);
		if(!$valid){
			$invoice['ERROR']= $invoiceClass->validationError;				
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
	
?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php
				 	echo $invoiceForms->invAcknowledgmentLink( $invoice );
				 	echo "</BR>\n";
				 	echo "</BR>\n";
				 	echo $invoiceForms->invNum( $invoice );
					echo "\n";
					echo "\n";
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
						echo $invoiceEntryForms->newInvoiceEntryForm($formValues, $partsFormClass);
					}
//					echo debugStatement(dumpDBRecord($formValues)); // $invoice
//					echo debugStatement(dumpDBRecord($invoice) ); 
//					echo debugStatement(dumpDBRecord($customer) ); 
					?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
