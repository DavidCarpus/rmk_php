<?php
session_start(); 
include_once "includes/htmlHead.php";
include_once "includes/orders.php";
include_once "includes/db.php";
include_once "includes/db_requests.php";


function catalogRequestProcessing(){
	$fields = fields();
	
	switch (getHTMLValue('action')) {
		case 'validateorder':
			$form = getFormValues();
			$errors = validForm($form);
			if(count($errors) > 0){
				displayErrors($errors);
				echo "<HR>";
				echo requestForm($fields, $form);
			}else{
				echo getSubmissionValidation($form);
			}
			break;
		case 'uservalidated':
			$form = getFormValues();
			$button = trim(getHTMLValue('submit'));
			if($button == "Edit Catalog Request"){
				echo requestForm($fields, $form);
			}else{
				saveOrder($fields, $form);
				echo "Catalog request received.  Please allow 21 days for postal delivery.";
			}
			break;			
		default:
//			echo "<h2 align='center'>" .getHTMLValue('action') . "</H2>";
			$form = getFormValues();
			echo requestForm($fields, $form);
//			dumpPOST_GET();
	}
}

function getSubmissionValidation($form){
	$labelstyle = 'width:250px; font-size:16; left:0px; text-align: right; ';	
	$valuestyle = 'width:150px; font-size:16; left:0px; text-align: left; ' ;	

	$form['ccnumber'] = str_replace(" ", "",$form['ccnumber']);
	$form['ccnumber'] = str_replace("-", "",$form['ccnumber']);
	
//	$results = $results .  "<HR>";
	$results = $results .  "<h2 align='center'>Confirm " .$form['ordertype'] . "</H2>";
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;

	foreach($form as $label=>$value){
		$desc = fieldDesc($label);
		$display=false;
		if(!($value == '' || $value == null)) $display=true;
		if($value == 'Submit Catalog Request' || $value == 'validateorder'|| $label == 'qty') $display=false;
		if($display){
			$results = $results . hiddenField($label,$value) . "\n";
			$results = $results .  "<I style='$labelstyle'>" . $desc . "</I> - <B style='$valuestyle'>" . $value . "</B><BR>";
		}
	}
	
	$results = $results . hiddenField('action','uservalidated') . "\n";
	$results = $results . "<input class='btn' type='submit' name='submit' value='Confirm " .$form['ordertype'] . "' >\n" ;
	$results = $results . "<input class='btn' type='submit' name='submit' value='Edit " .$form['ordertype'] . "' >\n" ;
	
	$results = $results . "</form>" ;

	$results = $results .  "<HR>";
	
	return $results;
}

function validForm($form){
	$requiredFields = array('name', 'email','address1','city', 'state', 'zip' );
	$invalidFields = array();
	foreach($requiredFields as $required){
		if($form[$required] == ''){
			array_push($invalidFields, "Required Field '" . fieldDesc($required) . "' is blank");
		}
	}
	if(!validate_email($form['email']))
		array_push($invalidFields, "Email is not a valid format");
		
	if(nonUSorCanada($form['country'])){ // Non US, more fields required
		$requiredFields = array('cctype', 'ccnumber','ccexpire','ccname');
		foreach($requiredFields as $required){
			if($form[$required] == ''){
				array_push($invalidFields, "For outside the United States - Required Field '" . fieldDesc($required) . "' is blank");
			}
		}
		if(!checkDigitCreditCard($form['cctype'], $form['ccnumber'])){
			array_push($invalidFields, "Invalid Credit Card Number");
		}
		if(!checkCC_Date($form['ccexpire'])){
			array_push($invalidFields, "Invalid Credit Card Expiration Date");
		}
		
	}
	
	return $invalidFields;
}

function nonUSorCanada($country){
	if($country == '') return false; // assume if none entered, US
	$country = strtoupper($country);
	if($country == 'US') return false; 
	if($country == 'USA') return false; 
	if($country == 'UNITED STATES') return false; 
	if($country == 'CA') return false; 
	if($country == 'CANADA') return false;
	return true; 
}

function requestForm($fields, $request){
	$prefix = "All current catalog and non-catalog information with price lists will be sent via airmail. \n" .
			  "There is a charge for catalogs mailed <B>outside</B> the United States.\n" .
			  "USA address - no charge\n" .
			  "Canada - US$3.00\n" .
			  "All Other Countries - US$5.00\n" .
			  "Please use this Secure Form, or if you prefer, print this form and fax it to us (407) 855-9054. " .
			  "Minimum age to order is 16 years old.\n\n";

	$copy = getSingleDbRecord("Select * from webcopy where page='catalog'");
	if(count($copy) > 0){
		$prefix = $copy['prefix'];
	}
	
	$results = $results . htmlizeText($prefix);
	
	$results = $results . "<BR><BR>";
	$results = $results . "<div style='color:red'>These fields are required. Your request will not process if they are empty.</div>". "<BR>\n" ;
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;

	$results = $results . textField('name', fieldDesc('name'), true, $request['name']). "<BR>\n" ;
	$results = $results . textField('email', fieldDesc('email'), true, $request['email']). "<BR>\n" ;
	$results = $results . textField('address1', fieldDesc('address1'), true, $request['address1']). "<BR>\n" ;
	$results = $results . textField('address2', fieldDesc('address2'), false, $request['address2']). "<BR>\n" ;
	$results = $results . textField('address3', fieldDesc('address3'), false, $request['address3']). "<BR>\n" ;
	$results = $results . textField('city', fieldDesc('city'), true, $request['city']). "<BR>\n" ;
	$results = $results . textField('state', fieldDesc('state'), true, $request['state']). "<BR>\n" ;
	$results = $results . textField('country', fieldDesc('country'), false, $request['country']). "<BR>\n" ;
	$results = $results . textField('zip', fieldDesc('zip'), true, $request['zip']). "<BR>\n" ;
	
	$phoneDesc = fieldDesc('phone');
 	$phoneDesc = str_replace("*", "",$phoneDesc); // strip off for catalog requests. Not appropriate.
	
	$results = $results . textField('phone', $phoneDesc, false, $request['phone']). "<BR>\n" ;
//	$results = $results . textField('fax', fieldDesc('fax'), false, $request['fax']). "<BR>\n" ;
	
	$results = $results . "<BR>\nFor International catalog orders only:<BR>\n";

	$results = $results . optionField('cctype', "Creditcard Type", array('Mastercard' , 'Visa', 'Discover'), $request['cctype'], false). "<BR>\n" ;
	$results = $results . textField('ccnumber', fieldDesc('ccnumber'), false, $request['ccnumber']). "<BR>\n" ;
	$results = $results . textField('ccexpire', fieldDesc('ccexpire'), false, $request['ccexpire']). "<BR>\n" ;
	$results = $results . textField('ccname', fieldDesc('ccname'), false, $request['ccname']). "<BR>\n" ;
	$results = $results . textField('ccvcode', fieldDesc('ccvcode'), false, $request['ccvcode']). "<BR><BR><BR>\n" ;

//	$results = $results . textField('qty', fieldDesc('catqty'), false, $request['qty']). "<BR>\n" ;
	$results = $results . "<center><input class='btn' type='submit' name='submit' value='Submit Catalog Request' ></center>\n" ;
	$results = $results . hiddenField('action','validateorder') . "\n";
	$results = $results . hiddenField('ordertype','Catalog Request') . "\n";

	if(count($copy) > 0)
		$results = $results . $copy['postfix'];

	return $results;
}

?>
<LINK href="Style.css" rel="stylesheet" type="text/css">
<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			<?php echo catalogRequestProcessing(); ?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

