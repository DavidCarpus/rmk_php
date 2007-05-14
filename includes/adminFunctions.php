<?php
/* Created on Feb 4, 2006 */

function adminProcessing(){
	echo "Admin:" . $_SESSION['loginValidated'];
//	echo "<table><TR><TD>Test1a</td><td>Test1b</td></TR><TR><TD>Test2a</td><td>Test2b</td></TR></table>";
}

function emailProcessing(){
	$parameters = getFormValues();
	$action = $parameters['action'];
	if($action == '') $action='browse';
	
	switch ($action) {
		case 'browse':
			if($parameters['startid']<=0) $parameters['startid']=999999;
			echo browseEmailsSent($parameters);
			echo emailSentSearchForm($parameters);
			break;
		case 'sentEmailDetail':
			echo emailSentDetail($parameters);
			break;
		case 'searchemails':
			echo searchEmails($parameters);
			echo emailSentSearchForm($parameters);
			break;
		default:
			echo 'Function ' . "' $action '" . ' not implimented';
			break;
	}
	$startid = (getHTMLValue('startid') > 0? getHTMLValue('startid') : 0);
	
}

function searchEmails($parameters){
	$MAX_LIST_LEN=60;
	$filter = "";
//	debugStatement(dumpDBRecord($parameters));

	if($parameters['address'] != ''){
		if($filter != "") $filter .= " AND ";
		$filter .= " ( fromaddress LIKE '%" . $parameters['address'] . "%'";
		$filter .= " OR toaddress LIKE '%" . $parameters['address'] . "%' )";
	}
	
//	if($parameters['requesttype'] > 0){
//		if($filter != "") $filter .= " AND ";
//		$filter .= " ordertype=" . $parameters['requesttype'];
//	}
	
	if($parameters['message'] != ''){
		if($filter != "") $filter .= " AND ";
		$filter .= " messagebody LIKE '%".$parameters['message']."%'";
	}
	if($parameters['startdate'] != ''){
		if($filter != "") $filter .= " AND ";
		$date = date("Y-m-d", strtotime($parameters['startdate']));
		$filter .= " datesubmitted > '$date'";
	}
	if($parameters['enddate'] != ''){
		if($filter != "") $filter .= " AND ";
		$date = date("Y-m-d", strtotime($parameters['enddate'] . "+1 day"));
		$filter .= " datesubmitted < '$date'";
	}
	
	$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
	$query .= " where $filter  order by datesubmitted DESC, email_id DESC LIMIT $MAX_LIST_LEN ";
//	debugStatement($query);

	$emails = getDbRecords($query);
	return displayEmails($emails);

}

function emailSentSearchForm($parameters){
	$results .= "<form  class='printHide' action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;

	$results .= textField('desc', "Description", false, $parameters['desc']) . "<BR>\n" ;
	$results .= textField('message', "Message", false, $parameters['message']) . "<BR>\n" ;
	$results .= textField('address', "Email Address", false, $parameters['address']) . "<BR>\n" ;
	$results .= textField('startdate', "Date - Start", false, $parameters['startdate'], 'date') . "<BR>" ;
	$results .= textField('enddate', "End ", false, $parameters['enddate'], 'date') . "<BR>\n" ;

	$results .= hiddenField('action','searchemails') . "\n";
	$results .= "<BR>\n";
	$results .= "<center><input class='btn' type='submit' name='submit' value='Find' ></center>\n" ;
	$results .= "</form>";
	return $results;
}

function emailSentDetail($parameters){
	$email = getBasicSingleDbRecord('emails', 'email_id', $parameters['email_id']);
	foreach($email as $field=>$value){
		echo "$field == " . str_replace("\n", "<BR>\n",$value)."<BR>\n";
	}
}

function browseEmailsSent($parameters){
	$startid = $parameters['startid'];
	$records = getEmails($startid, 10);
	$results = displayEmails($records);
	return "<B>Not Fully Implemented</B><BR>" . $results;
}

function displayEmails($records){
	$results .= "<table>";
	foreach($records as $email){
		$results .=  linkToEmailSent($email);
		$keyID =$email['email_id'];
	}
	$results .=  "</table>";
//	$keyID -= 1;
//	if(count($records) == 20){
//		$results .=  "<a href='" . $_SERVER['PHP_SELF'] . "?startid=$keyID'>Next 20</a>";
//	}
//	$results .=  " &nbsp;";
//	if(count($records) <= 20 && $records[0]['email_id'] == $startid){
//		$keyID =$records[0]['email_id'] +20;
//		$results .=  "<a href='" . $_SERVER['PHP_SELF'] . "?startid=$keyID'>Previous 20</a>";
//	}
	return $results;
}

function linkToEmailSent($email){
	$results .=  "<tr>";
	$from = split("-", $email['fromaddress']);
	$address = $from[0];
	if($from[0] == "BLANK")
		$address = "TO : " . $email['toaddress'];
		
	$results .=  "<TD><a href='" . $_SERVER['PHP_SELF'] . "?action=sentEmailDetail&email_id=" . $email['email_id'] . "'>" . $address . "</a></TD>";
	$results .=  "<TD>" . date('Y-m-d', $email['date_sent']) . "</TD>";
	$results .=  "<TD>" . $email['messagesubject']. "</TD>";
	$results .=  "</tr>";
	return $results;
}

function getEmails($startID=0, $cnt=20){
	$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
	$query .= " where email_id <=$startID  order by datesubmitted DESC, email_id DESC LIMIT $cnt ";
//	debugStatement($query);
	return getDbRecords($query);	
}

function loginProcessing(){
	$login = getHTMLValue('login');
	$password = getHTMLValue('password');
	
	if(validLogin($login, $password)){
		echo "Login validated <BR>";
		echo " < < < Select area to administrate from menu to the left<BR>";
//		return adminMenu();
		$_SESSION['loginValidated'] = 1;
		$_SESSION['loggedinuser'] = $login;
	} else{
		return loginScreen($login, $password);
	}
}

function validLogin($name, $passwd){
	if($name=='carpus' && $passwd=='duntyr1')
		return true;
//	if($name=='mark' && $passwd=='martin')
//		return true;
	if($name=='gary' && $passwd=='skeet100')
		return true;
	if($name=='valerie' && $passwd=='skeet100')
		return true;
	return false;	
}


function loginScreen($name='', $passwd=''){
	return "<B style='font-size:36; text-align:center; display:block;'>Login</B>" .
			"<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" .
			textField('login', 'User Name', true, $name). "<BR><BR>" .
			"<label for='passwd' class='required'>Password</label><input type='password' name='password' value='".$passwd."'><BR>\n" .
//			textField('password', 'Password', true, $passwd). "<BR>".
			hiddenField('action','Save').
			"<BR>" .
			"<input type='submit' name='submit' value='Login' >" .
			"</form>\n";
}


?>
