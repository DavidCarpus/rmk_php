<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once DB_INC_DIR. "Payments.class.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "Payment.class.php";
include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Customer.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$formValues = getFormValues();
$paymentsClass = new Payments();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();

$invoiceForms = new Invoice();
$customerForms = new Customer();
$paymentForms = new Payment();

$payment = $paymentsClass->fetchPayment($formValues['PaymentID']);
$invoice = $invoiceClass->details( $payment['Invoice'] );
$customer = $customerClass->fetchCustomerForInvoice( $payment['Invoice'] );

$entries = $invoiceClass->items($payment['Invoice'] );
$invoice["KnifeCount"] = 0;
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];
	
if(array_key_exists('submit', $formValues) && $formValues['submit'] == "Confirm Payment Deletion"){
	$paymentsClass->deletePayment( $formValues['PaymentID']);
 	header("Location: "."invoicePaymentsEntryEdit.php?Invoice=".$payment['Invoice'] );
}

?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

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
					echo $invoiceForms->details( $invoice, "view" );
					echo "\n\n";
					echo $paymentForms->confirmPaymentDelete($payment);
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>