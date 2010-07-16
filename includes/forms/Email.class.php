<?php
include_once "Base.class.php";

class Email extends Base
{
	function __construct() {
       $this->name = "forms_Emails";
   }
   public function entryFormMode($formValues)
   {
// 'browse': 'detail': 'search':	
// 		if(array_key_exists("ERROR", $formValues) && strlen($formValues['ERROR']) > 0){return "validate";}	
//				
//		if(array_key_exists("Invoice", $formValues) && is_numeric($formValues["Invoice"])){return "edit";}
		if(array_key_exists("action", $formValues) && $formValues["action"] == "detail"){return "detail";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Search"){return "search";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Send"){return "submitEmail";}
		return "browse";	
   }
	
	public function addFormValues($emailData, $formValues)
	{
		$fields = array('fromaddress', 'toaddress', 'messagesubject', 'message', 'customername');
		foreach($fields as $name)
		{
			if(array_key_exists($name, $formValues))
			{
				$emailData[$name] = $formValues[$name];
			}
		}
		return $emailData;
	}
	
   function emailDetail($email)
   {
   		$results="<div id='EmailDetailView'>";
   		$fields = array('fromaddress'=>"From", 'toaddress'=>'TO', 'datesubmitted'=>'Submitted', 
   			'messagesubject'=>'Subject', 'messagebody'=>'Message');
		foreach($fields as $name=>$label)
		{
			$value = str_replace("\n", "<br />\n",$email[$name]);
			$results .= "$label = $value<br />\n";
		}
		$results .=  "</div>";
		return $results;
   }
   
   function listEmails($records){
		$results = "<div id='EmailList'>";
		foreach($records as $email){
			$results .=  $this->linkToEmailSent($email);
			$keyID =$email['email_id'];
		}
		$results .=  "</div>";
		return $results;
	}
	
	function linkToEmailSent($email){
		$from = split("-", $email['fromaddress']);
		$address = $from[0];
		if($from[0] == "BLANK")
			$address = "TO : " . $email['toaddress'];
			
		$link=$_SERVER['SCRIPT_NAME'] . "?action=detail&amp;email_id=" . $email['email_id'];
		$results .=  "<a href='$link'>" . $address . "</a>";
		$results .=  "<div class='date'>" . date('Y-m-d', $email['date_sent']) . "</div>";
		$results .=  "<div class='subject'>" . $email['messagesubject']. "</div>\n";
		return $results;
	}
	
	function searchForm($searchValues)
	{
		$formName="EmailSearch";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='emailReview.php' method='post'>\n" ;
		
		$fields = array('fromaddress', 'toaddress', 'start_datesubmitted', 'end_datesubmitted', 'messagesubject');
		foreach($fields as $name)
		{
			$value = $searchValues[$name];	
			$results .=  $this->textField($name, $this->fieldDesc($name),  $value, $options, "", "", "", "");
//			$results .=  $this->textField($name, $this->fieldDesc($name), $err, $value) . "<br/>\n";
		}
		$results .=  $this->button("submit", "Search");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";

		return $results;		
	}
//		$emails = array("carpus"=>"csdave2000@yahoo.com");
//	$email = $emails[$_SESSION['loggedinuser']];
////	if($email=='') $email="test";
//	return $email;
	
	function sendEmailForm($emailData, $hideToAddress=true){
		$formName="sendEmailForm";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='".$_SERVER['PHP_SELF']."' method='post'>\n" ;

		$errors = $this->retrieveErrorArray($emailData);
		
		$fields = array('fromaddress', 'toaddress', 'messagesubject', 'message', 'customername');
		foreach($fields as $name)
		{
			$value = $emailData[$name];	
//			$err=(array_key_exists($name, $errors));
			if(array_key_exists($name, $errors)) $options['error']=true;
			
			if($name == 'message'){
				$results .=  $this->textArea('message', "Message", $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
			} else if($name == 'customername'){
				$results .= $this->hiddenField($name, $value);
			} else if($name == 'toaddress'){
				if($hideToAddress){
					if($value == '')
						$value="webmessages@randallknives.com";
					$results .= $this->hiddenField($name, $value);
				} else{
					$results .=  $this->textField($name, $this->fieldDesc($name), $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
				}
			} else {
				$results .=  $this->textField($name, $this->fieldDesc($name), $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
			}
		}
		$results .=  $emailData['dumpText'];
		$results .=  $this->button("submitButton", "Send");		
		$results .= "</form>";
//		$results .= dumpDBRecord($emailData);
		$results .= "</div><!-- End $formName -->\n";
		return $results;	
	}
	
	function currentUserEmailFromLoggedIn(){
		switch ($_SERVER['PHP_AUTH_USER']){
			case "test": return "testing@randallknives.com";
			case "valerie": return "valerie@randallknives.com";
			case "gary": return "grandall@randallknives.com";
		}
		
	}
	
	function emailForRequest($request){
		$formName="emailForRequest";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='".$_SERVER['PHP_SELF']."' method='post'>\n" ;

		
		$defaultSubjAndMsg="Regarding Your " . $request['ordertypestring'];
		$emailData=array();
		$emailData['fromaddress'] = ($request['fromaddress'] != ""?$request['fromaddress']:$this->currentUserEmailFromLoggedIn());
		$emailData['toaddress'] = ($request['toaddress'] != ""?$request['toaddress']:$request['email']);
		$emailData['messagesubject'] = ($request['messagesubject'] != ""?$request['messagesubject']:$defaultSubjAndMsg);
		$emailData['message'] = ($request['message'] != ""?$request['message']:$defaultSubjAndMsg. ",\n");
		$emailData['customername'] = $request['name'];
		
//		$emailData['fromaddress']=$this->currentUserEmailFromLoggedIn();
//		$emailData['toaddress']=$request['email'];
//		$emailData['messagesubject']="Regarding Your " . $request['ordertypestring'];
//		$emailData['message']="Regarding Your " . $request['ordertypestring']. ",\n";

		
		$emailData['dumpText']= $request['search_criteria']."\n";
		
		$results .= $this->sendEmailForm($emailData, false);
//		$results .= dumpDBRecord($request);
		
		return $results;	
	}
}
?>