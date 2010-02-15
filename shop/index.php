<?php /* Created on Feb 4, 2006 */

include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once FORMS_DIR. "KnifeList.class.php";
include_once FORMS_DIR. "Search.class.php";

include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

if (!authenticate()){
	return;
}

$knifeListForms = new KnifeList();
$searchForms = new Search();

$invoiceDbAccess = new Invoices();
$customerDbAccess = new Customers();

$formValues = getFormValues();
$mode=$knifeListForms->entryFormMode($formValues);

$knifeListInvoices=array();
$customers=array();

switch ($mode) {
	case "browse":
		$formValues['year'] = (array_key_exists("year", $formValues))? $formValues['year']: date("Y");
		$formValues['week'] = (array_key_exists("week", $formValues))? $formValues['week']: date("W")-12;
		if($formValues['week'] < 1){ $formValues['year'] -= 1; $formValues['week'] += 52; }  // wrap to previous year
		
		$knifeListInvoices = $invoiceDbAccess->getKnifeListItems($formValues['year'], $formValues['week']);
//		echo "Rec " . sizeof($knifeListInvoices ) . " invoices.";
		break;
	case "invoicedetail":
		$invoice = $invoiceDbAccess->details( $formValues['invoicedetail'] );
		$invoice = $invoiceDbAccess->addCostsEntriesAndAddresses($invoice);
		$invoice = array_merge($invoice, $customerDbAccess->fetchCustomer($invoice['CustomerID']));
		break;	
	case "search":
		$searchType = $searchForms->getSearchType($formValues);
		$formValues['Older'] = (array_key_exists("Newer", $formValues))? 0: 1;
		$formValues['searchType'] = $searchType;
		if($searchType == 'invoice'){
			$invoice = $invoiceDbAccess->details( $formValues['searchValue'] );
			$invoice = $invoiceDbAccess->addCostsEntriesAndAddresses($invoice);
			$invoice = array_merge($invoice, $customerDbAccess->fetchCustomer($invoice['CustomerID']));
		} else {
			$knifeListInvoices = $invoiceDbAccess->getShopSearchResults($formValues);			
		}
		
//		$knifeListInvoices = $invoiceDbAccess->getKnifeListItems($formValues['year'], $formValues['week']);
		break;	
	default:
		break;
}

echo headSegments("RMK Shop Access", array("../ShopStyle.css", "../Style.css", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "' ></script>"; ?>


<?php echo logo_header("", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo shopToolbar(); ?>
		<div class="content">
			<?php 
				if($mode == 'browse') echo $knifeListForms->knifeListNavigation($formValues);
				if($mode == 'browse') echo $knifeListForms->displayKnifeList($knifeListInvoices);
				if($mode == 'invoicedetail') echo $knifeListForms->invoiceDetail($invoice);
				if($mode == 'invoicesearch' || $mode == 'search') echo $searchForms->searchScreen($formValues);
				if($mode == 'search'){
				  if($searchType == 'invoice') 
				  	echo $knifeListForms->invoiceDetail($invoice);
				  else
				  	echo $knifeListForms->displayInvoiceList($knifeListInvoices);
				}				
				?>
		</div>
	</div>
	<?php //echo logoutLink() . " : " . $_SERVER['PHP_AUTH_USER']. " : " . $_SESSION['session_id']; ?> 
	<?php echo footer(); ?>
</div>

<?php
//echo debugStatement($mode);
//echo debugStatement(dumpDBRecord($formValues));
?>

</body>
</html>