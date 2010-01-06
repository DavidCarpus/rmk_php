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
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Search"){return "search";}
		return "browse";	
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
			
		$link=$_SERVER['SCRIPT_NAME'] . "?action=detail&email_id=" . $email['email_id'];
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
			$results .=  $this->textField($name, $this->fieldDesc($name), $err, $value) . "<br/>\n";
		}
		$results .=  $this->button("submit", "Search");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";

		return $results;		
	}
}
?>