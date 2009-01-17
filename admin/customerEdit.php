<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

include_once DB_INC_DIR. "Customers.class.php";

include_once FORMS_DIR. "Customer.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$formValues = getFormValues();
$customerClass = new Customers();
$customerForms = new Customer();


$mode=$customerForms->entryFormMode($formValues);
$formValues['mode'] = $mode;

switch ($mode) {
	case "edit";
		$customer = $customerClass->fetchCustomer($formValues['CustomerID']);
//		echo debugStatement(dumpDBRecord($customer));
		break;
	case "new";
		$customer = $customerClass->blank();
		$customer = $customerClass->addFormValues($customer, $formValues);
		break;
	case "validate":
		$customer = $customerClass->blank();
		$customer = $customerClass->addFormValues($customer, $formValues);
		$customer['CustomerID'] = $formValues['CustomerID'];
		
		$valid = $customerClass->validate($customer);
		if(!$valid){
			$customer['ERROR']= $customerClass->validationError;				
		} else {
			if($formValues['Dealer']=='on') $customer['Dealer']=1;
			if(!array_key_exists('CustomerID', $formValues) || $customer['CustomerID'] <= 0){
				unset($customer['CustomerID']);
			}
			$customer = $customerClass->save($customer);
//			echo debugStatement(dumpDBRecord($customer));
//			echo debugStatement(dumpDBRecord($customer['CurrentAddress']));
			
			$mode="edit";
			header("Location: "."search.php?CustomerID=" . $customer['CustomerID']);
			return;
		}
		
		break;
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
				echo $customerForms->newCustomerForm( $customer );
//				echo debugStatement(dumpDBRecord($formValues));
				?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
