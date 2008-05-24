<?php
/* Created on Feb 8, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/db/db_requests.php";
include_once "../includes/htmlHead.php";
include_once "../includes/adminFunctions.php";

include_once "../includes/forms/Shop.class.php";
include_once "../includes/forms/Invoice.class.php";
include_once "../includes/forms/Customer.class.php";
include_once "../includes/db/Parts.class.php";
include_once "../includes/db/Customers.class.php";
include_once "../includes/db/Invoices.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}
 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
// echo "<script type='text/javascript' src='./js/ajax-dynamic-content.js'></script>";
// echo "<script type='text/javascript' src='./js/ajax.js'></script>";
// echo "<script type='text/javascript' src='./js/ajax-tooltip.js'></script>";
 //<input class='btn' type='submit' onclick="getTime(myForm);" name='submit' value='Search' >

 //<LINK href="../Style.css" rel="stylesheet" media='screen' type="text/css">
 

?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">

<?php
$invoiceForms = new Invoice();
$customerForms = new Customer();
$invoiceClass = new Invoices(); 
$customerClass = new Customers();

$formValues = getFormValues();
$invoiceNum = $formValues['Invoice'];
$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$invoice = $invoiceClass->details( $invoiceNum );
$entries = $invoiceClass->items($invoiceNum);
foreach($entries as $entry)
	$invoice["KnifeCount"] += $entry['Quantity'];

?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 	echo $invoiceForms->invNum( $invoice );
					echo "\n";
					echo "\n";
					echo $customerForms->summary( $customer );
					echo "\n";
					echo "\n";
					echo $invoiceForms->details( $invoice );
					echo "\n";
					echo "\n";
					$entries = $invoiceClass->itemsWithAdditions( $invoiceNum ); // 56031
					echo $invoiceForms->knifeListTable( $entries );

//					echo debugStatement(dumpDBRecord($invoice));;
					?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
