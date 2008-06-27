<?php
include_once "../includes/db/Invoices.class.php";
include_once "../includes/htmlHead.php";

include_once "../includes/forms/Shop.class.php";
include_once "../includes/forms/Invoice.class.php";
include_once "../includes/forms/Customer.class.php";
include_once "../includes/forms/InvoiceEntry.class.php";
include_once "../includes/db/Parts.class.php";
include_once "../includes/db/Customers.class.php";
include_once "../includes/db/Invoices.class.php";

session_start();

?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

<?php
 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";

 $invoiceForms = new Invoice();
$customerForms = new Customer();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();
$invoiceEntryClass = new InvoiceEntry();

$formValues = getFormValues();

$entryID = $formValues['InvoiceEntryID'];
$entry = getBasicSingleDbRecord("InvoiceEntries", "InvoiceEntryID", $entryID);

$invoiceNum = $entry['Invoice'];
$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$invoice = $invoiceClass->details( $invoiceNum );
//$entries = $invoiceClass->items($invoiceNum);


?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 	echo $invoiceForms->invNum($invoice);
					echo "\n";
					echo "\n";
					echo $customerForms->tiny( $customer );
					echo "\n";
					echo "\n";
					echo $invoiceForms->details( $invoice );
					echo "\n";
					echo "\n";
//					$entries = $invoiceClass->itemsWithAdditions( $invoiceNum ); // 56031
//					echo $invoiceForms->knifeListTable( $entries );

					echo $invoiceEntryClass->details($entry);
					?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>