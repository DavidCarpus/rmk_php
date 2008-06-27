<?php
/* Created on Feb 8, 2006 */
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
include_once FORMS_DIR. "Payment.class.php";
include_once FORMS_DIR. "Customer.class.php";

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

	
?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 	
					echo $invoiceForms->invNum( $invoice );
					echo "\n\n";
					echo $customerForms->tiny( $customer );
					echo "\n\n";
					echo $invoiceForms->details( $invoice );
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
