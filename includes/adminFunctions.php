<?php
/* Created on Feb 4, 2006 */

function adminProcessing(){
	echo "Administrate order/catalog requests and emails.</br>";
//	emailProcessing();
}

//function emailProcessing(){
//	$parameters = getFormValues();
//	$action = $parameters['action'];
//	if($action == '') $action='browse';
//	
//	switch ($action) {
//		case 'browse':
//			if($parameters['startid']<=0) $parameters['startid']=999999;
//			echo browseEmailsSent($parameters);
//			echo emailSentSearchForm($parameters);
//			break;
//		case 'sentEmailDetail':
//			echo emailSentDetail($parameters);
//			break;
//		case 'searchemails':
//			echo searchEmails($parameters);
//			echo emailSentSearchForm($parameters);
//			break;
//		default:
//			echo 'Function ' . "' $action '" . ' not implimented';
//			break;
//	}
//	$startid = (getHTMLValue('startid') > 0? getHTMLValue('startid') : 0);
//	
//}
//
//function searchEmails($parameters){
//	$MAX_LIST_LEN=60;
//	$filter = "";
////	debugStatement(dumpDBRecord($parameters));
//
//	if($parameters['address'] != ''){
//		if($filter != "") $filter .= " AND ";
//		$filter .= " ( fromaddress LIKE '%" . $parameters['address'] . "%'";
//		$filter .= " OR toaddress LIKE '%" . $parameters['address'] . "%' )";
//	}
//	
////	if($parameters['requesttype'] > 0){
////		if($filter != "") $filter .= " AND ";
////		$filter .= " ordertype=" . $parameters['requesttype'];
////	}
//	
//	if($parameters['message'] != ''){
//		if($filter != "") $filter .= " AND ";
//		$filter .= " messagebody LIKE '%".$parameters['message']."%'";
//	}
//	if($parameters['startdate'] != ''){
//		if($filter != "") $filter .= " AND ";
//		$date = date("Y-m-d", strtotime($parameters['startdate']));
//		$filter .= " datesubmitted > '$date'";
//	}
//	if($parameters['enddate'] != ''){
//		if($filter != "") $filter .= " AND ";
//		$date = date("Y-m-d", strtotime($parameters['enddate'] . "+1 day"));
//		$filter .= " datesubmitted < '$date'";
//	}
//	
//	$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
//	$query .= " where $filter  order by datesubmitted DESC, email_id DESC LIMIT $MAX_LIST_LEN ";
////	debugStatement($query);
//
//	$emails = getDbRecords($query);
//	return displayEmails($emails);
//
//}
//
//function emailSentSearchForm($parameters){
//	$results .= "<form  class='printHide' action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
//
//	$results .= textField('desc', "Description", false, $parameters['desc']) . "<br />\n" ;
//	$results .= textField('message', "Message", false, $parameters['message']) . "<br />\n" ;
//	$results .= textField('address', "Email Address", false, $parameters['address']) . "<br />\n" ;
//	$results .= textField('startdate', "Date - Start", false, $parameters['startdate'], 'date') . "<br />" ;
//	$results .= textField('enddate', "End ", false, $parameters['enddate'], 'date') . "<br />\n" ;
//
//	$results .= hiddenField('action','searchemails') . "\n";
//	$results .= "<br />\n";
//	$results .= "<center><input class='btn' type='submit' name='submit' value='Find' ></center>\n" ;
//	$results .= "</form>";
//	return $results;
//}

//function emailSentDetail($parameters){
//	$email = getBasicSingleDbRecord('emails', 'email_id', $parameters['email_id']);
//	foreach($email as $field=>$value){
//		echo "$field == " . str_replace("\n", "<br />\n",$value)."<br />\n";
//	}
//}
//
//function browseEmailsSent($parameters){
//	$startid = $parameters['startid'];
//	$records = getEmails($startid, 10);
//	$results = displayEmails($records);
//	return "<B>Not Fully Implemented</B><br />" . $results;
//}

//function displayEmails($records){
//	$results .= "<table>";
//	foreach($records as $email){
//		$results .=  linkToEmailSent($email);
//		$keyID =$email['email_id'];
//	}
//	$results .=  "</table>";
////	$keyID -= 1;
////	if(count($records) == 20){
////		$results .=  "<a href='" . $_SERVER['PHP_SELF'] . "?startid=$keyID'>Next 20</a>";
////	}
////	$results .=  " &nbsp;";
////	if(count($records) <= 20 && $records[0]['email_id'] == $startid){
////		$keyID =$records[0]['email_id'] +20;
////		$results .=  "<a href='" . $_SERVER['PHP_SELF'] . "?startid=$keyID'>Previous 20</a>";
////	}
//	return $results;
//}

//function linkToEmailSent($email){
//	$results .=  "<tr>";
//	$from = split("-", $email['fromaddress']);
//	$address = $from[0];
//	if($from[0] == "BLANK")
//		$address = "TO : " . $email['toaddress'];
//		
//	$link=$_SERVER['PHP_SELF'] . "?action=sentEmailDetail&email_id=" . $email['email_id'];
//	$results .=  "<TD><a href='$link'>" . $address . "</a></TD>";
//	$results .=  "<TD>" . date('Y-m-d', $email['date_sent']) . "</TD>";
//	$results .=  "<TD>" . $email['messagesubject']. "</TD>";
//	$results .=  "</tr>";
//	return $results;
//}

//function getEmails($startID=0, $cnt=20){
//	$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
//	$query .= " where email_id <=$startID  order by datesubmitted DESC, email_id DESC LIMIT $cnt ";
////	debugStatement($query);
//	return getDbRecords($query);	
//}


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
	$results[] = array("Done"=>"2009-03-24", "Text"=>"Blist for 1/31/0 - List by invoice number, then within invoice-Program to list model numbers numerically, then alpha.");
	$results[] = array("Done"=>"2008-05-13", "Text"=>"Dealers – next to the invoice number, same line, display dealer name in italics.");
	$results[] = array("Done"=>"2009-12-30", "Text"=>"End of Blist –  display grand total of knives");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"#55933 – bill to and ship to address are not displayed");
	$results[] = array("Done"=>"2008-03-24", "Text"=>"#67761 – calculate tax on subtotal amt");
	$results[] = array("Done"=>"2008-08-14", "Text"=>"Shop - When searching by invoice, ignore 'older' flag");
	$results[] = array("Done"=>"2010-01-06", "Text"=>"Add 'shop' to menu with same display restrictions as 'admin'");
	$results[] = array("Done"=>"2010-02-15", "Text"=>"Shop system - add 3 more weeks back.");
	$results[] = array("Done"=>"2010-02-17", "Text"=>"Shop system - Sort invoices by name when searching.");
	$results[] = array("Done"=>"2010-02-15", "Text"=>"Shop system - Search with an initial:Scott r maynard");
	$results[] = array("Done"=>"2010-01-22", "Text"=>"Shop system - Add to shop toolbar, link to 'real' website on home.");
	$results[] = array("Done"=>"2010-02-17", "Text"=>"Customer order form - add more spacing between quote and order options");
	$results[] = array("Done"=>"2010-02-17", "Text"=>"Catalog request form - Not pulling 'header' from DB like it should. CA $4 Other $6");
	$results[] = array("Done"=>"2010-02-18", "Text"=>"Admin Order processing - CC numbers should have dashes every 4 characters");
	$results[] = array("Done"=>"2010-02-18", "Text"=>"Admin Order processing - screen and printable list credit info as CC \\n Expiration \\n vcode");
	$results[] = array("Done"=>"", "Text"=>"Admin Order processing - Only show CC info on orders and catalog requests for foreign (do not show for US catalog requests)");
	$results[] = array("Done"=>"", "Text"=>"Admin Order processing - Orders and quotes should be one per page on printable list");
	$results[] = array("Done"=>"", "Text"=>"Admin Order processing - Orders and quotes should display ALL info (model, blade length, ...)");
	$results[] = array("Done"=>"", "Text"=>"Admin Order processing - Need option to 'process' ALL catalog requests");
	$results[] = array("Done"=>"", "Text"=>"Shop knife list - add column and list models for each invoice");
	$results[] = array("Done"=>"", "Text"=>"?? Sort orders to have foreign seperate from US??");
	$results[] = array("Done"=>"", "Text"=>"");
	$results[] = array("Done"=>"", "Text"=>"");
	
	return $results;
}

function toDoPage(){
	$results = "Web priority list <i>Last Updated Feb 17, 20107</i><br />";
	$results .= "<ol id='toDoList'>";
	$items = toDoItems();
	foreach ($items as $item) {
		if(strlen($item['Text'])>0){
			$results .= "<li>";
	
			if(strlen($item['Done'] > 0)){
				$results .= "<span class='done'>" . $item['Text'] . "</span>";
			} else {
				$results .= "<span>" . $item['Text'] . "</span>";
			}
			
			if(strlen($item['Done'] > 0)){
				$results .= "<b>";
				$results .= $item['Done'];
				$results .= "</b>";
			}
			$results .= "</li>\n";
		}
	}
	$results .= "</ol>";
	
	return $results;	
}
?>
