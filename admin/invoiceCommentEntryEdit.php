<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once DB_INC_DIR. "Parts.class.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "Shop.class.php";
include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Customer.class.php";
include_once FORMS_DIR. "InvoiceEntry.class.php";

session_start();

$invoiceForms = new Invoice();
$customerForms = new Customer();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();
$invoiceEntryForms = new InvoiceEntry();

$formValues = getFormValues();
$invoiceNum = $formValues['Invoice'];
$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$invoice = $invoiceClass->details( $invoiceNum );
$entries = $invoiceClass->items($invoiceNum);
$invoice["KnifeCount"] = 0;
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];

if(array_key_exists('submit', $formValues)){
	$invoiceClass->updateComment($invoiceNum, $formValues['Comment']);
	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
}

echo headSegments("RMK Remove Item from Invoice", array("../Style.css", "", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "' ></script>"; ?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php 	
					echo $invoiceForms->invNum( $invoice );
					echo "\n\n";
					echo $customerForms->displayWithFlags( $customer );
					echo "\n\n";
					echo $invoiceForms->details( $invoice, "view" );
					echo "\n\n</HR>";
					echo $invoiceForms->editComment( $invoice );
					echo "\n\n</HR>";
					
					$entries = $invoiceClass->itemsWithAdditions( $invoiceNum ); // 56031
					echo $invoiceEntryForms->knifeListTable( $entries, 0 );				
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
