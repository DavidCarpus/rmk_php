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

function toDoItems(){
	$results = array();
//	$results[] = array("Done"=>"2007-03-01", "Text"=>"apostrophes as \&rsquo; in emails instead of &rsquo;");
//	$results[] = array("Done"=>"2007-03-01", "Text"=>"Create margins on nonCatII text");
//	$results[] = array("Done"=>"2007-01-29", "Text"=>"Do not make phone number manditory for catalog requests. (Gary 2006-12-28)");
//	$results[] = array("Done"=>"2007-02-06", "Text"=>"Reply  - save  Reply   to a file where we can review");
//	$results[] = array("Done"=>"01-24", "Text"=>"Processed orders &#150; remove  option to &lsquo;unprocess&rsquo; a processed item.  In the screen of actual processed item, (cat,quote,order, - once item is processed, we have no need to &lsquo;unprocess&rsquo; **************review  screens----look at list and look at individual item—where unprocess item appears");
//	$results[] = array("Done"=>"2007-02-14", "Text"=>"One step print button &#150; create for cat, quote, orders, create one option to send complete list of unprocessed requests to printer, then automatic move the list of unprocessed requests that were printed to the processed list.  Print catalogs on evelopes, print quote and orders on 8&frac12; x 11 paper, one/quote/order per page,  (((KEEP the option to print one at a time as we now do.)))");
//	$results[] = array("Done"=>"01-25", "Text"=>"Name and address fields &#150; allow  an apostrophe for names such a O&rsquo;Reily.");
//	$results[] = array("Done"=>"2007-02-14", "Text"=>"Process orders, quote, cat  &#150;   we would like to be able to enter the processed items  to mark for whatever reason,   rejected, pending, etc.");
//	$results[] = array("Done"=>"2007-02-08", "Text"=>"Non Catalog II &#150; list model, desc, and price.");
	$results[] = array("Done"=>"", "Text"=>"Time submitted &#150; when an order,quote,cat,  is sent to processed, the time will vary. We need one permanent time and date stamp. No changes.");
	$results[] = array("Done"=>"", "Text"=>"Processed order &#150; purge option.");
	$results[] = array("Done"=>"", "Text"=>"Examples of Combinations -  list model, desc, and price.");
	$results[] = array("Done"=>"", "Text"=>"Set up so that all catalogue requests are deleted every week. (Gary 2006-10-22)");
	$results[] = array("Done"=>"", "Text"=>"&rsquo; in name and address fields uses  &rsquo;");
	$results[] = array("Done"=>"2008-06-25", "Text"=>"Accounting on invoices – FL invoice #55385 – note the $.72 – which is tax added to the invoice on the ups charge. (ups charge is added to invoice when billed in year of shipment.)   Invoice is actually paid in full, no credit due,   and shows $.72 credit on the shop rmk.");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"Accounting on invoices – invoice #55315 and  #55318 – two kn order – shows four knives  billing –  2 kn were deleted and changed to new models. Shows total qty of 4.  (((purged rmk program today)))");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"Accounting on invoices – please format to line up the decimals.");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"Knife order specs – same as accounting on invoices -  #55315 and #55318 – shows four knives, 2 were deleted and change to new models. Shows qty of 4.   (rmk purged today)");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"KNIFE LIST - The KNV  part number is still added into total qty.  ((purged today)) ");
	$results[] = array("Done"=>"", "Text"=>"Blist for 1/31/0 - List by invoice number, then within invoice-Program to list model numbers numerically, then alpha.");
	$results[] = array("Done"=>"", "Text"=>"Dealers – next to the invoice number, same line, display dealer name in italics.");
	$results[] = array("Done"=>"", "Text"=>"End of Blist –  display grand total of knives");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"#55933 – bill to and ship to address are not displayed");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"#67761 – calculate tax on subtotal amt");
	$results[] = array("Done"=>"", "Text"=>"Shop - When searching by invoice, ignore 'older' flag");
	$results[] = array("Done"=>"", "Text"=>"");
	$results[] = array("Done"=>"", "Text"=>"");
	
	return $results;
}

function toDoPage(){
	$results = "Web priority list <I>Last Updated Mar 1, 2007</I><BR>";
	$results .= "<OL id='toDoList'>";
	$items = toDoItems();
	foreach ($items as $item) {
		if(strlen($item['Text'])>0){
			$results .= "<LI>";
	
			if(strlen($item['Done'] > 0)){
				$results .= "<span class='done'>" . $item['Text'] . "</span>";
			} else {
				$results .= "<span>" . $item['Text'] . "</span>";
			}
			
			if(strlen($item['Done'] > 0)){
				$results .= "<B>";
				$results .= $item['Done'];
				$results .= "</B>";
			}
			$results .= "</LI>";
		}
	}
	$results .= "</OL>";
	
	return $results;	
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
