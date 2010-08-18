<?php
/* Created on Feb 8, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";
include_once INCLUDE_DIR. "links.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once DB_INC_DIR. "Parts.class.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "Shop.class.php";
include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Payment.class.php";
include_once FORMS_DIR. "Customer.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$invoiceForms = new Invoice();
$customerForms = new Customer();
$paymentForms = new Payment();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();

$formValues = getFormValues();
$invoiceNum = $formValues['Invoice'];
if($invoiceNum == 0 ) 	$invoiceNum = $formValues['invoice_num'];

$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$invoice = $invoiceClass->details( $invoiceNum );
$entries = $invoiceClass->items($invoiceNum);
$payments = $invoiceClass->fetchInvoicePayments($invoiceNum);
$costs = $invoiceClass->computeCosts($invoice);
$invoice["KnifeCount"] = 0;
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];

	
echo headSegments("RMK Edit Invoice Payments", array("../Style.css", "", "../DataEntry.css"), "../print.css");
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
					echo "\n\n";
					echo $customerForms->displayWithFlags( $customer );
					echo "\n\n";
					echo $invoiceForms->details( $invoice, true );
					echo "\n\n";
					echo $paymentForms->paymentListTable($invoiceNum, $payments, $formValues);
					echo "\n\n";
					echo $paymentForms->invoiceFinanceTable($costs);
//					echo debugStatement(dumpDBRecord($costs));
					
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>