<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once DB_INC_DIR. "InvoiceEntries.class.php";

include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Customer.class.php";
include_once FORMS_DIR. "InvoiceEntry.class.php";


session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$formValues = getFormValues();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();
$invoiceEntries = new InvoiceEntries();

$invoiceForms = new Invoice();
$customerForms = new Customer();
$customerForms = new Customer();
$invoiceEntryForms = new InvoiceEntry();

$invoiceNum = $formValues['Invoice'];
$invoice = $invoiceClass->details( $invoiceNum );
$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$entries = $invoiceClass->itemsWithAdditions( $invoiceNum ); // 56031
$mode=$invoiceForms->entryFormMode($formValues);
//$results .= "\n<body  onLoad='defaultField(\"form_InvoiceEntryEdit\",\"PartDescription\");'>\n";
if(array_key_exists('submit', $formValues) && $formValues['submit'] == "Remove item from Invoice"){
	$invoiceEntries->removeInvoiceItem($formValues['InvoiceEntryID'], $entries, $invoiceNum);
	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
} else {
?>
<script type='text/javascript' src='../includes/NewRMK.js?<?php echo time() ?>'></SCRIPT>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

<body  onLoad='defaultField("form_RemoveInvoiceEntry","PartDescription");'>

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
					echo $invoiceForms->details( $invoice, $mode );
					echo "\n\n";
					echo $invoiceEntryForms->removeEntryForm($formValues, $entries);
					echo "\n\n";
					echo $invoiceEntryForms->knifeListTable( $entries, $formValues['InvoiceEntryID'] );
//					echo debugStatement(dumpDBRecord($formValues));
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
<?php } ?>