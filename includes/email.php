<?php
/* Created on Mar 24, 2006 */

function emailRequestProcessing(){
	$action = getHTMLValue('action');
	if($action=='') $action = 'emailForm';
	 
	$form = getFormValues();

	switch ($action) {
		case "emailForm":			
			echo emailForm($form);
			break;
		case 'emailsubmitted':
			$errors = validateForm($form);
			if($errors != ''){
				echo $errors;
				echo emailForm($form);
			} else {
				saveAndSend($form);
			}
			break;
		default:
			echo "Email Processing<br />Coming soon:" . $action;
			dumpPOST_GET();
	}
}

function emailedFromAdmin($parameters){
	if($parameters['orderid'] <= 0) return false;
	
	$errors = validateForm($parameters);
	if($errors != ''){
		echo $errors;
		return false; // even if we are admin, we sill need to have a valid form
	}
	saveAndSend($parameters, true);
	return true;
//	debugStatement(dumpDBRecord($parameters));
//	return false;
}
function adminOrderWebAddress($parameters){
	echo debugStatement(dumpDBRecord($parameters));
	return "admin/orders.php?action=details&orders_id=".$parameters['orderid'];
//	return "../";
}

function saveAndSend($form, $quiet=false){
	require_once("class.phpmailer.php");
		
	$mail = new PHPMailer();
	$from = $form['from'];
	$mail->From = $from;

	$mail->Subject = $form['subject'];
//	$mail->FromName = "Randall Made Knives (Website)";
	$mail->FromName = "Randall Made Knives (Website) - " . $form['customername'];
		
	$mail->IsHTML(false);
	
	$mail->ClearAddresses();
	$mail->AddAddress($form['to']);
	$mail->Body = $form['message'];
	
	$dbRecord = array();
	$dbRecord['fromaddress'] = $form['from'] . "-" . $form['customername'];
	$dbRecord['toaddress'] = $form['to'];
	$dbRecord['messagesubject'] = $form['subject'];
	$dbRecord['messagebody'] = de_htmlizeFormValue($form['message']);

//	if(!isDevelopmentMachine()){
		saveRecord("emails", "email_id", $dbRecord);
//	}
	
	if(isDevelopmentMachine() ){ 
		print "The following message will be 'sent':<br />";
//		print  "<HR>".$form['message'] . "<HR>" . $dbRecord['messagebody'] . "<HR>";
		print  "<HR>". $dbRecord['messagebody'] . "<HR>";
		return;
	}
		
	if(!isDevelopmentMachine() && !$mail->Send()){ 
		$error = $error . 'Unable to send email to:' . $form['to'] . "<br />\n";
	}else{
		if(!$quiet){
			print "The following message was 'sent':<br />";
			print  "<HR>".$form['message'] . "-" . $dbRecord['messagebody'] . "<HR>";
		}
	}
//	print_r($form);
	

	if(strlen($error) > 0)
		return $error;
}

function isDevelopmentMachine(){
	if($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org') return true;
	if($_SERVER['HTTP_HOST'] == 'localhost') return true;
	if($_SERVER['REMOTE_ADDR'] =='70.118.199.240') return true;
	if($_SERVER['REMOTE_ADDR'] =='192.168.1.90') return true;
	return false;
}


function validateForm($form){
	if(! validate_email($form['from']) && $form['from']!='BLANK') 
		$results = $results . " Invalid From EmailAddress.<br />\n";
	if(! validate_email($form['to']) && $form['to']!='BLANK') 	
		$results = $results . " Invalid To EmailAddress.<br />\n";
	if($form['message']=='' ) 
		$results = $results . " Blank Message.<br />\n";
	if($form['subject']=='' ) 
		$results = $results . " Blank Subject.<br />\n";
	return $results;
//	dumpPOST_GET();
}

function emailForm($form){
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;

	$results = $results . textField('customername', "From Name:", false, $form['customername']);

	$from = $form['from'];
	if($from == '') $from=getCurrentUserEmail();
	if($from != 'BLANK')
		$results = $results . textField('from', "From email address:", false, $from);
	else
		$results = $results . hiddenField('from','BLANK') . "\n";

	
//	$results = $results . "<br />";
	$to = $form['to'];
	if($to == 'webmessages')	$to .= "@randallknives.com";
		 
	if($to != 'BLANK'){
		$results = $results . hiddenField('to',$to) . "\n";
//		$results = $results . textField('to', "To", false, $to);
		$results = $results . "<br />";
	}else{
		$results = $results . hiddenField('to','BLANK') . "\n";
	}
		
	$results = $results . textField('subject', "Subject", false, $form['subject']);
	$results = $results . "<br />";
	$results = $results . textArea('message', "Message", false, $form['message'], true);
	$results = $results . "<br />";
		
	$results = $results . hiddenField('action','emailsubmitted') . "\n";
	$results = $results . "<input class='btn' type='submit' name='submit' value='Send' >\n" ;

	$orderID = $form['orderid'];
	if($orderID > 0){
		$order = getSingleRequest($orderID);
		$results .= orderDetail($order);	
		$results = $results . hiddenField('orderid',"$orderID") . "\n";
	}
	
	$results = $results . "</form>" ;
	
	
	return $results;	
}

function getCurrentUserEmail(){
	$emails = array("carpus"=>"csdave2000@yahoo.com");
	$email = $emails[$_SESSION['loggedinuser']];
//	if($email=='') $email="test";
	return $email;
}

//function validate_email($email){
//   $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$"; // syntactical validation regular expression
//   return (eregi($regexp, $email)); // Validate the syntax
//}

function dateAsEDT($dbDate){
	$shift = -4;
	$value = $dbDate;
	$value=$value + ($shift * 60 * 60);
	$value=gmdate('m/d/Y h:i:s a', $value);
	return $value;
}

function orderDetail($order){
	$fields = fields();
	$cols=array('left'=>"", 'right'=>"");

	
	$results = $results . "<br /><div class='orderdetail' style='width:700px'>";
	$results = $results . "<table border=1>";

	foreach($fields as $field){
		$desc = fieldDesc($field);
		if($field == 'submission_date') $desc="Request Submitted";
		switch ($field) {
			case 'orders_id':
				$value = $order[$field];
				break;
			case 'shipaddress3':
				$desc = ' ';
				$value = $order[$field];
				break;
			case 'address2':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'address3':
				$desc = 'Address cont.';
				$value = $order[$field];
				break;
			case 'ordertype':
			case 'processed':
				$desc='';
				break; // skip
			case 'email':
				$value=$order[$field];
				break;
			case 'ccnumber':// skip
				if(trim($order[$field]) != '')
				$value = addDashesToCCNumber($order[$field]);
				break;
			case 'submission_date':
				$value = dateAsEDT($order[$field]);
				break;
			default:
				$value = $order[$field];
				break;
		}
	$leftstyle = 'line-height:18px; width:210px; left:0px; font-style: italic; text-decoration: none;';		
	$rightstyle = 'line-height:18px; width:490px; left:10px;  font-weight: bold;';		

		if($desc != '&nbsp;' && $desc != '' && trim($value) != ''){
			$results = $results . "<TR>";
			$results = $results . "<TD style='$leftstyle'>" . $desc. "</TD>";
			$results = $results . "<TD  style='$rightstyle'>" . $value . "</TD>";
			$results = $results . "</TR>";
		}
	}
	$results = $results . "</table>";
	$results = $results . "</div>";
	
	return $results;
}


?>
