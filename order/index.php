<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "orders.php";
include_once INCLUDE_DIR. "email.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

//include_once "../includes/db/db.php";
//include_once "../includes/db/db_requests.php";
//include_once "../includes/htmlHead.php";
//include_once "../includes/orders.php";
?>

<html>
<?php echo headSegment("../Style.css"); ?>
<body>

<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			<?php processOrders(); ?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
</body>
</html>
<?php

function processOrders(){
	$fields = fields();

	switch (getHTMLValue('action')) {
		case 'validateorder':
			$form = getFormValues();

			$errors = validForm($form);

			if(count($errors) > 0){
				displayErrors($errors);
				echo "<HR>";
				echo orderForm($fields, $form);
			}else{
				print getSubmissionValidation($form);
			}
			break;
		case 'uservalidated':
			$form = getFormValues();
//			echo debugStatement(dumpDBRecord($form));
			if(getHTMLValue('submit') == "Edit Quote" || getHTMLValue('submit') == "Edit Order"){
				echo orderForm($fields, $form);
			}else{
//				debugStatement(dumpDBRecord($form));
//				debugStatement(dumpDBRecord($fields));
				$address = '192.168.1.90';
				if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) != $address ){
					saveOrder($fields, $form);
				}
				$ack = "";
				$emailSubject="";
				if($form['ordertype'] == 'Quote'){
					$ack = str_replace("\n","<BR>\n",getQuoteRequestAcknowledgment($fields, $form));
					$emailSubject="Quote Request Acknowledgment";
				} else{
					$ack = str_replace("\n","<BR>\n",getOrderAcknowledgment($fields, $form));
					$emailSubject="Order Request Acknowledgment";
				}
				echo $ack;
				if($form['email'] != ''){
//					echo debugStatement($ack . dumpDBRecord($form));
					$form['from']='BLANK';
					$form['subject'] = $emailSubject;
					$form['customername']="";
					$form['to']=$form['email'];
					$form['message']=$ack;
					saveAndSend($form, true);
				}
			}

			break;			
		default:
//			echo "<h2 align='center'>" .getHTMLValue('action') . "</H2>";
			$form = getFormValues();
			echo orderForm($fields, $form);
	}
}
function getQuoteRequestAcknowledgment($fields, $data){
	$results = "<H2>Quote request acknowledgement</H2>";
	$results = $results . "Thank you for the quote request.\n";
	$results = $results . "Randall will respond to the email address provided. : ";
	$results = $results . "<B>".$data['email']."</B>\n\n";
			
	$entry = array();
	foreach($fields as $field){
		if($data[$field] != ''){
			$results = $results . fieldDesc($field) . " <B>" . $data[$field] . "</B>\n";
		}
	}
	return $results = $results . "\n";	
}

function getOrderAcknowledgment($fields, $data){
	$results = "<H2>Order REQUEST acknowledgement</H2>";
			
//	$results = $results . "Thank you for the order request.\n\n";
//	$results = $results . "An &quot;order acknowledgement&quot; will be forwarded via post office within three weeks.\n\n";
//	$results = $results . "The acknowledgement will outline knife order specifications, the scheduled ship date and also a deposit record.  " .
//			"If you do not receive an &quot;order acknowledgement&quot; it is imperative to contact Randall Knives to verify complete order specs. " .
//			"and deposit records.<HR>";
	
$results = $results . "Thank you for your order request with Randall Made Knives.\n\n";
$results = $results . "Full Name:". $data['name'] . "\n";
$results = $results . "Order request date:". date("F j Y") . "\n";
$results = $results . "Model Number:". $data['model'] . "\n";
$results = $results . "An 'order acknowledgement' will be forwarded via post office within 21 days.\n\n";
$results = $results . "The acknowledgement will outline knife order specifications, the scheduled ship date and also a deposit record.   If you do not receive an order acknowledgement, it is imperative to contact Randall Made Knives to verify complete order specs and deposit records.\n\n";
$results = $results . "<I>All order requests are subject to approval  and confirmation by Randall Made Knives.</I>\n\n";
			
//	$entry = array();
//	foreach($fields as $field){
//		if($data[$field] != ''){
//			$results = $results . fieldDesc($field) . " <B>" . $data[$field] . "</B>\n";
//		}
//	}
//	$results = $results . dumpDBRecord($data);
	return $results = $results . "\n";
}


function getSubmissionValidation($form){
	$labelstyle = 'width:250px; font-size:16; left:0px; text-align: right; ';	
	$valuestyle = 'width:150px; font-size:16; left:0px; text-align: left; ' ;	
	
//	$results = $results .  "<HR>";
	$results = $results .  "<h2 align='center'>Confirm " .$form['ordertype'] . "</H2>";

	
	$form['ccnumber'] = str_replace(" ", "",$form['ccnumber']);
	$form['ccnumber'] = str_replace("-", "",$form['ccnumber']);


	$results = $results . "<Table border=1>";
	$fields = "";
	foreach($form as $label=>$value){
		$desc = fieldDesc($label);
		$display=false;
		if(!($value == '' || $value == null)) $display=true;
		if($value == 'Submit Request' || $value == 'validateorder'|| $label == 'qty') $display=false;
//		if(!($desc == '' || $desc == '&nbsp;')){
		if($display){
			$results = $results . "<TR>";
			$results = $results . "<TD>";
			$results = $results .  "<I style='$labelstyle'>" . $desc . "</I>";
			$results = $results . "</TD>";
//			$results = $results .  " - ";
			$results = $results . "<TD>";
			$results = $results .  "<B style='$valuestyle'>" . $value . "</B>";
			$results = $results . "</TD>";
//			$results = $results .  "<BR>\n";
			$fields = $fields . hiddenField($label,$value) . "\n";
			$results = $results . "</TR>";
//		} else{
//			$results = $results . "<B><I>$desc - $value</I></B>";
		}
	}
	$results = $results . "</Table>";
	
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
	$results = $results . $fields;
	$results = $results . hiddenField('action','uservalidated') . "\n";
	$results = $results . "<input class='btn' type='submit' name='submit' value='Confirm " .$form['ordertype'] . "' >\n" ;
	$results = $results . "<input class='btn' type='submit' name='submit' value='Edit " .$form['ordertype'] . "' >\n" ;
	$results = $results . "</form>" ;

	if($form['ordertype'] == 'Order'){
		$results = $results . "<B>Amount to be charged:" . "\n";
		$amt = $form['qty'] * 50;
		$form['state'] = strtoupper($form['state']);
//		$taxed=false;
//		if($form['state'] == 'FL' || $form['state'] == 'FLORIDA'){
//			$taxed = true;
//		}
//		if($taxed )
//			$amt = $amt * 1.06;
			
		$results = $results . "$" . $amt . " </B><I>(";
		$results = $results . "$50x" . $form['qty'];
//		if($taxed )
//			$results = $results . " + 6% FL State tax ";
		$results = $results . ")</I>";
	}

	$results = $results .  "<HR>";
	
	return $results;
}


function validForm($form){
	$requiredFields = array('name', 'email','address1','city', 'state', 'zip' , 'country', 'phone', 'ordertype');
	$invalidFields = array();
	foreach($requiredFields as $required){
		if($form[$required] == ''){
			$desc = fieldDesc($required);
			if($desc == '') $desc = $required;
			array_push($invalidFields, "Required Field '$desc' is blank");
		}
	}
	if($form['ordertype'] == 'Order'){ // Order, more fields required
		$requiredFields = array('qty', 'model','bladelength', 'cctype', 'ccnumber','ccexpire','ccname');
		foreach($requiredFields as $required){
			if($form[$required] == ''){
				$desc = fieldDesc($required);
				if($desc == '') $desc = $required;
				array_push($invalidFields, "For Orders - Required Field '$desc' is blank");
			}
		}
//		if( !($form['qty'] == 1)){
//			array_push($invalidFields, "Quantity is limited to 1");
//		}
		if(!checkDigitCreditCard($form['cctype'], $form['ccnumber'])){
			array_push($invalidFields, "Invalid Credit Card Number");
		}
		if(!checkCC_Date($form['ccexpire'])){
			array_push($invalidFields, "Invalid Credit Card Expiration Date. Format mm/yy");
		}
		
	}
	
	if(!is_phone($form['phone']))
		array_push($invalidFields, "Not a phone number");

	if(!validate_email($form['email']))
		array_push($invalidFields, "Email is not a valid format");
	return $invalidFields;
}




function requiredTextField($field, $values){
	return textField($field, fieldDesc($field), true, $values[$field]);
}
function optionalTextField($field, $values){
	return textField($field, fieldDesc($field), false, $values[$field]);
}

function orderForm($fields, $values){
	global $fields;
	$prefix = "Current order deliveries are being scheduled in approximately 54 MONTHS. Order limit is two knives every other month per household. We strongly recommend obtaining the catalog to view all features before placing an order. A deposit of US$50.00 per knife is required to place an order. Deposits are not transferable and non-refundable. Credit card user name must match the individual&rsquo;s name placing the order. Shipping charges are determined by Randall Made Knives based on weight, value and destination in the year of delivery. Mimimum age to order is 16 years old. EFFECTIVE MARCH 1, 2006, RANDALL WILL LIMIT ORDERS TO A SINGLE KNIFE EVERY THREE MONTHS PER HOUSEHOLD.";

	$copy = getSingleDbRecord("Select * from webcopy where page='order'");
	if(count($copy) > 0){
		$prefix = $copy['prefix'];
	}

	$results = $results . htmlizeText($prefix);

	
	$results = $results . "<BR><BR>";
	$results = $results . "<div style='color:red'>These fields are required. Your order/quote will not process if they are empty.</div>". "<BR>\n" ;
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;

	
	$results = $results . requiredTextField('name', $values). "<BR>\n" ;
	$results = $results . requiredTextField('email', $values). "<BR>\n" ;
	$results = $results . requiredTextField('address1', $values). "<BR>\n" ;
	$results = $results . requiredTextField('address2', $values). "<BR>\n" ;
	$results = $results . requiredTextField('address3', $values). "<BR>\n" ;
	$results = $results . requiredTextField('city', $values). "<BR>\n" ;
	$results = $results . requiredTextField('state', $values). "<BR>\n" ;
	$results = $results . requiredTextField('zip', $values). "<BR>\n" ;
	$results = $results . requiredTextField('country', $values). "<BR>\n" ;
	$results = $results . requiredTextField('phone', $values). "<BR>\n" ;
//	$results = $results . optionalTextField('fax', $values). "<BR>\n" ;
	$results = $results . "It is the policy of Randall Made Knives NOT to disseminate names, addresses, or phone numbers to any person, organization or company.<BR>";
	$results = $results . optionalTextField('shipaddress1', $values). "<BR>\n" ;
	$results = $results . optionalTextField('shipaddress2', $values). "<BR>\n" ;
	$results = $results . optionalTextField('shipaddress3', $values). "<BR>\n" ;
	$results = $results . "<BR>";
	$results = $results . optionField('ordertype', "Interest", array('Quote','Order'), $values['ordertype'], true). "<BR>\n" ;
//	$results = $results . optionalTextField('qty', $values). "<BR>\n" ;
	$results = $results . hiddenField('qty','1') . "\n";
	$results = $results . optionalTextField('model', $values). "<BR>\n" ;
	$results = $results . optionalTextField('bladelength', $values). "<BR>\n" ;
	$results = $results . textArea('note', fieldDesc('note'), false, $values['note']). "<BR>\n" ;
	$results = $results . "<BR>";
	$results = $results . optionField('cctype', "Creditcard Type", array('Mastercard' , 'Visa', 'Discover'), $values['cctype'], false). "<BR>\n" ;
	$results = $results . optionalTextField('ccnumber', $values). "<BR>\n" ;
	
	$results = $results . optionalTextField('ccexpire', $values). "<BR>\n" ;
	$results = $results . optionalTextField('ccvcode', $values). "<BR><BR><BR>\n" ;
	$results = $results . optionalTextField('ccname', $values). "<BR>\n" ;
	$results = $results . "<BR>";
	$results = $results . "<center><input class='btn' type='submit' name='submit' value='Submit Request' ></center>\n" ;
	$results = $results . hiddenField('action','validateorder') . "\n";
	$results = $results . "</form>\n";

	$results = $results . "<BR>";	
	
	$postfix = "*Important: Your phone number will act as your customer account number.". "\n" .
		"Randall Made Knives' \"Certification Guarantee\" entitles you to a full product refund should you elect to return the unused knife. (See Catalog, page 40.)\n" .
		"When ordering more than one knife (order limit is two every other month/household) requiring different descriptions, please use a separate submission for each order. Order name and credit card name must be identical. Randall does not accept second party credit cards.";

	if(count($copy) > 0){
		$postfix = $copy['postfix'];
	}
	$results = $results . htmlizeText($postfix);	
		
	return $results ;
}

?>
