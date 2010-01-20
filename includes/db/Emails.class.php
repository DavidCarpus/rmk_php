<?php
include_once "db.php";
include_once "BaseDBObject.class.php";

class Emails  extends BaseDBObject
{
	public $validationError;
	public $emailSent;
	
	public function validateSentEmail($values){
		$valid=true; 
		if(!$this->validateEmail($values['fromaddress'])){$this->validationError .= "fromaddress,"; $valid=false;}
		if(!$this->validateEmail($values['toaddress'])){$this->validationError .= "toaddress,"; $valid=false;}
		if($values['messagesubject']==''){$this->validationError .= "messagesubject,"; $valid=false;}
		if($values['message']==''){$this->validationError .= "message,"; $valid=false;}
		
//		echo "Validate:</BR>" . dumpDBRecord($values) . $this->validationError;
		return $valid;
	}

	
	function fetchEmails($startid=999999, $cnt=10){
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
		$query .= " where email_id <=$startid  order by datesubmitted DESC, email_id DESC LIMIT $cnt ";
		return getDbRecords($query);		
	}
	
	function fetchEmail($emailID){
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject, messagebody from emails ";
		$query .= " where email_id =$emailID";
//		echo debugStatement($query);
		return getDbRecords($query);		
	}
	
	function searchEmails($searchValues){
		$filter=array();
		if($searchValues['fromaddress'] != ''){
			$filter[] = " ( fromaddress LIKE '%" . $searchValues['fromaddress'] . "%' )";
		}
		if($searchValues['toaddress'] != ''){
			$filter[] = " ( toaddress LIKE '%" . $searchValues['toaddress'] . "%' )";
		}
		if($searchValues['messagesubject'] != ''){
			$filter[] = " ( messagesubject LIKE '%" . $searchValues['messagesubject'] . "%' )";
		}
		
		if($searchValues['start_datesubmitted'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['start_datesubmitted']));
			$filter[] = " ( datesubmitted > '$date' )";
		}
		if($searchValues['end_datesubmitted'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['end_datesubmitted']));
			$filter[] = " ( datesubmitted < '$date')";
		}
		if(sizeof($filter) == 0) return;
		
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject, messagebody from emails ";
		$query .= " where " . $filter[0];
		for($filterIndex=1; $filterIndex < sizeof($filter); $filterIndex++)
		{
			$query .= " and " . $filter[$filterIndex];			
		}
		$query .= " order by datesubmitted DESC, email_id DESC LIMIT " . MAX_EMAIL_LIST_LEN;
		
//		echo debugStatement("Search emails filter:<br/>" . dumpDBRecord($filter));
//		echo debugStatement("Search emails filter:<br/>$query" );
		return getDbRecords($query);		
	}

	
	public function saveAndSend($form){
			
		$mail = new PHPMailer();
		$mail->From = $form['fromaddress'];
	
		$mail->Subject = $form['messagesubject'];
	//	$mail->FromName = "Randall Made Knives (Website)";
		$mail->FromName = "Randall Made Knives (Website) - " . $form['customername'];
			
		$mail->IsHTML(false);
		
		$mail->ClearAddresses();
		$mail->AddAddress($form['to']);
		$mail->Body = $form['message'];
		
		$dbRecord = array();
		$dbRecord['fromaddress'] = $form['fromaddress'] ;
		$dbRecord['toaddress'] = $form['toaddress']. "-" . $form['customername'];
		$dbRecord['messagesubject'] = $form['messagesubject'];
		$dbRecord['messagebody'] = de_htmlizeFormValue($form['message']);
	
	//	if(!isDevelopmentMachine()){
			saveRecord("emails", "email_id", $dbRecord);
	//	}
		$this->emailSent = "The following message would be 'sent':" .dumpDBRecord($dbRecord);

			
//		if(!isDevelopmentMachine() && !$mail->Send()){ 
//			$this->validationError .= 'Unable to send email to:' . $form['to'];
//		}else{
//			if(!$quiet){
//				print "The following message was 'sent':<br />";
//				print  "<HR>".$dbRecord['messagesubject'] . "-" . $dbRecord['messagebody'] . "<HR>";
//			}
//		}
	//	print_r($form);
		
	
		if(strlen($this->validationError) > 0)
			return $this->validationError;
	}	
	
	
	
}
?>