<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";
include_once DB_INC_DIR. "InvoiceEntries.class.php";
include_once DB_INC_DIR. "Parts.class.php";
include_once DB_INC_DIR. "Invoices.class.php";
include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "Invoice.class.php";
include_once FORMS_DIR. "Customer.class.php";
include_once FORMS_DIR. "InvoiceEntry.class.php";
include_once FORMS_DIR. "Part.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$formValues = getFormValues();
$invoiceNum = $formValues['Invoice'];

$invoiceEntryClass = new InvoiceEntries();
$invoiceClass = new Invoices();
$customerClass = new Customers();
$partsFormClass = new Part();

$invoiceForms = new Invoice();
$customerForms = new Customer();
$invoiceEntryForms = new InvoiceEntry();

$invoice = $invoiceClass->details( $invoiceNum );
$customer = $customerClass->fetchCustomerForInvoice( $invoiceNum );
$entries = $invoiceClass->itemsWithAdditions( $invoiceNum );
$invoice["KnifeCount"] = $invoiceClass->computeKnifeCount($entries);
$formValues['DefaultDiscount']=$customer['Discount'];

$mode=$invoiceEntryForms->invEntryFormMode($formValues);

//echo "Invoice Item : $mode<BR>";
//echo dumpDBRecord($formValues);
//echo debugStatement("Mode:$mode");


function editForm(){
		global $invoiceForms,$customerForms,$invoiceEntryForms;
		global $invoice,$customer,$entries, $partsFormClass;
		global $formValues;
		
	$results .= "<html>\n";
//	$results .= headSegment();

	$results = "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
	$results .= "<LINK href='../Style.css'' rel='stylesheet' type='text/css'>";
	$results .= "<LINK href='../print.css'' rel='stylesheet' type='text/css' media='print'>";
	$results .= "<LINK href='../DataEntry.css'' rel='stylesheet' type='text/css' media='screen'>";
	$results .= "\n<body  onLoad='defaultField(\"form_InvoiceEntryEdit\",\"PartDescription\");'>\n";
	$results .= logo_header("admin", "..");
	$results .= "<div class='mainbody'>";
	$results .= "<div class='centerblock'>";
	$results .= adminToolbar();
	$results .= "<div class='content'>";
	$results .=  "Edit Mode";
	$results .= $invoiceForms->invNum( $invoice);
	$results .= "\n";
	$results .= "\n";
	$results .= $customerForms->tiny( $customer );
	$results .= "\n";
	$results .= "\n";
	$results .= $invoiceForms->details( $invoice, "view"  );
	$results .= $invoiceEntryForms->knifeListTable( $entries, $formValues["invoiceentryid"] );
	$formValues['entries'] = $entries;
	$results .= $invoiceEntryForms->invoiceEntryEditForm($formValues, $partsFormClass);
	$results .="</div></div>";
//	echo dumpDBRecords($entries);

	
	$results .= footer();
	$results .= "</body>";
	$results .= "</html>";
		
	return $results;
}

switch ($mode) {
	case "validate":
		if($invoiceEntryClass->validateNewEdit($formValues)){
			$invoiceEntryClass->save($formValues);
		 	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
		}
	break;
	case "new":
	case "edit":
		echo editForm();
	break;
		
	case "add":
	case "update":
		
		if($invoiceEntryClass->validateNewEdit($formValues)){
			$invoiceEntryClass->save($formValues);
		 	header("Location: "."invoiceEdit.php?Invoice=$invoiceNum");
		} else {
			$formValues['ERROR']=$invoiceEntryClass->validationError;
			$newURL = "";
			foreach ($formValues as $field=>$value ){
				$newURL .= $field . "=" . urlencode($value) . "&";
			}
			$newURL = substr($newURL,0,strlen($newURL)-1);
			$newLocation  = "newInvoiceEntry.php?".$newURL;
//			debugStatement("newLocation : $newLocation");
			$newLocation  = "newInvoiceEntry.php?".$newURL;
			header("Location: $newLocation");
		}
		break;
}
?>
