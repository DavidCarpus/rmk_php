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

function toDoSort($a, $b)
{
	if($a['Done'] == "" || $b['Done'] == ""){
		if($a['Done'] == "" &&  $b['Done'] != "") return -1; 
		if($b['Done'] == "" &&  $a['Done'] != "") return 1; 
		return ($a['ID'] > $b['ID']);
	}
	if ($a['Done'] == $b['Done']) {
		return ($a['ID'] > $b['ID']);
	}
	return ($a['Done'] > $b['Done']) ? -1 : 1;
}

function toDoItems(){
	$results = array();
//	$results[] = array("ID"=>1, "Done"=>"2007-03-01", "Text"=>"apostrophes as \&rsquo; in emails instead of &rsquo;");
//	$results[] = array("ID"=>2, "Done"=>"2007-03-01", "Text"=>"Create margins on nonCatII text");
//	$results[] = array("ID"=>3, "Done"=>"2007-01-29", "Text"=>"Do not make phone number manditory for catalog requests. (Gary 2006-12-28)");
//	$results[] = array("ID"=>4, "Done"=>"2007-02-06", "Text"=>"Reply  - save  Reply   to a file where we can review");
//	$results[] = array("ID"=>5, "Done"=>"2007-01-24", "Text"=>"Processed orders &#150; remove  option to &lsquo;unprocess&rsquo; a processed item.  In the screen of actual processed item, (cat,quote,order, - once item is processed, we have no need to &lsquo;unprocess&rsquo; **************review  screens----look at list and look at individual item—where unprocess item appears");
//	$results[] = array("ID"=>6, "Done"=>"2007-02-14", "Text"=>"One step print button &#150; create for cat, quote, orders, create one option to send complete list of unprocessed requests to printer, then automatic move the list of unprocessed requests that were printed to the processed list.  Print catalogs on evelopes, print quote and orders on 8&frac12; x 11 paper, one/quote/order per page,  (((KEEP the option to print one at a time as we now do.)))");
//	$results[] = array("ID"=>7, "Done"=>"2007-01-25", "Text"=>"Name and address fields &#150; allow  an apostrophe for names such a O&rsquo;Reily.");
//	$results[] = array("ID"=>8, "Done"=>"2007-02-14", "Text"=>"Process orders, quote, cat  &#150;   we would like to be able to enter the processed items  to mark for whatever reason,   rejected, pending, etc.");
//	$results[] = array("ID"=>9, "Done"=>"2007-02-08", "Text"=>"Non Catalog II &#150; list model, desc, and price.");
	$results[] = array("ID"=>10, "Done"=>"", "Text"=>"Time submitted &#150; when an order,quote,cat,  is sent to processed, the time will vary. We need one permanent time and date stamp. No changes.");
	$results[] = array("ID"=>11, "Done"=>"", "Text"=>"Processed order &#150; purge option.");
	$results[] = array("ID"=>12, "Done"=>"", "Text"=>"Examples of Combinations -  list model, desc, and price.");
	$results[] = array("ID"=>13, "Done"=>"", "Text"=>"Set up so that all catalogue requests are deleted every week. (Gary 2006-10-22)");
	$results[] = array("ID"=>14, "Done"=>"", "Text"=>"&rsquo; in name and address fields uses  &rsquo;");
//	$results[] = array("ID"=>15, "Done"=>"2008-06-25", "Text"=>"Accounting on invoices – FL invoice #55385 – note the $.72 – which is tax added to the invoice on the ups charge. (ups charge is added to invoice when billed in year of shipment.)   Invoice is actually paid in full, no credit due,   and shows $.72 credit on the shop rmk.");
//	$results[] = array("ID"=>16, "Done"=>"2008-03-24", "Text"=>"Accounting on invoices – invoice #55315 and  #55318 – two kn order – shows four knives  billing –  2 kn were deleted and changed to new models. Shows total qty of 4.  (((purged rmk program today)))");
//	$results[] = array("ID"=>17, "Done"=>"2008-03-24", "Text"=>"Accounting on invoices – please format to line up the decimals.");
//	$results[] = array("ID"=>18, "Done"=>"2008-03-24", "Text"=>"Knife order specs – same as accounting on invoices -  #55315 and #55318 – shows four knives, 2 were deleted and change to new models. Shows qty of 4.   (rmk purged today)");
//	$results[] = array("ID"=>19, "Done"=>"2008-03-24", "Text"=>"KNIFE LIST - The KNV  part number is still added into total qty.  ((purged today)) ");
//	$results[] = array("ID"=>20, "Done"=>"2009-03-24", "Text"=>"Blist for 1/31/0 - List by invoice number, then within invoice-Program to list model numbers numerically, then alpha.");
//	$results[] = array("ID"=>21, "Done"=>"2008-05-13", "Text"=>"Dealers – next to the invoice number, same line, display dealer name in italics.");
//	$results[] = array("ID"=>22, "Done"=>"2009-12-30", "Text"=>"End of Blist –  display grand total of knives");
//	$results[] = array("ID"=>23, "Done"=>"2008-03-24", "Text"=>"#55933 – bill to and ship to address are not displayed");
//	$results[] = array("ID"=>24, "Done"=>"2008-03-24", "Text"=>"#67761 – calculate tax on subtotal amt");
//	$results[] = array("ID"=>25, "Done"=>"2008-08-14", "Text"=>"Shop - When searching by invoice, ignore 'older' flag");
//	$results[] = array("ID"=>26, "Done"=>"2010-01-06", "Text"=>"Add 'shop' to menu with same display restrictions as 'admin'");
//	$results[] = array("ID"=>27, "Done"=>"2010-01-22", "Text"=>"Shop system - Add to shop toolbar, link to 'real' website on home.");
//	$results[] = array("ID"=>28, "Done"=>"2010-02-15", "Text"=>"Shop system - add 3 more weeks back.");
//	$results[] = array("ID"=>29, "Done"=>"2010-02-15", "Text"=>"Shop system - Search with an initial:Scott r maynard");
//	$results[] = array("ID"=>30, "Done"=>"2010-02-17", "Text"=>"Shop system - Sort invoices by name when searching.");
//	$results[] = array("ID"=>31, "Done"=>"2010-02-17", "Text"=>"Customer order form - add more spacing between quote and order options");
//	$results[] = array("ID"=>32, "Done"=>"2010-02-17", "Text"=>"Catalog request form - Not pulling 'header' from DB like it should. CA $4 Other $6");
//	$results[] = array("ID"=>33, "Done"=>"2010-02-18", "Text"=>"Admin Order processing - CC numbers should have dashes every 4 characters");
//	$results[] = array("ID"=>34, "Done"=>"2010-02-18", "Text"=>"Admin Order processing - screen and printable list credit info as CC \\n Expiration \\n vcode");
	$results[] = array("ID"=>35, "Done"=>"2010-03-09", "Text"=>"Shop knife list - add column and list models for each invoice");
	$results[] = array("ID"=>36, "Done"=>"2010-03-06", "Text"=>"Admin Order processing - Only show CC info on orders and catalog requests for foreign (do not show for US catalog requests)");
	$results[] = array("ID"=>37, "Done"=>"2010-03-19", "Text"=>"Admin Order processing - Orders and quotes should display ALL info (model, blade length, ...)");
	$results[] = array("ID"=>38, "Done"=>"2010-03-19", "Text"=>"?? Sort orders to have foreign seperate from US??");
	$results[] = array("ID"=>39, "Done"=>"2010-03-25", "Text"=>"Admin Order processing - Need option to 'process' ALL catalog requests");
	$results[] = array("ID"=>40, "Done"=>"2010-03-31", "Text"=>"Admin Order processing - Orders and quotes should be one per page on printable list");
//	$results[] = array("ID"=>41, "Done"=>"", "Text"=>"Labels for foreign should have more lines. Seperate out 'state and zip' on seperate line?");
	$results[] = array("ID"=>42, "Done"=>"2010-04-05", "Text"=>"Fix 'codes' and apostraphes on Dealer spec letter. Acrobat problem?");
	$results[] = array("ID"=>43, "Done"=>"2010-04-22", "Text"=>"Balance due letters: Reference invoice #60891, florida sales tax,   amount due reflects $606.00,  does not include the tax due of $45.89 in the total amount due.");
	$results[] = array("ID"=>44, "Done"=>"2010-04-22", "Text"=>"Balance due letters: The top and left and right margin need to be adjusted.  The top margin needs to be  2.5” to accommodate letterhead.    Left  and right margins could use another 1/2 inch. ");
	$results[] = array("ID"=>45, "Done"=>"2010-04-22", "Text"=>"Balance due letters: Can you center the payment info at the end of the letter,  “If payment by credit card:  Visa, MC, Disc,    and card number  block of info.");
	$results[] = array("ID"=>46, "Done"=>"2010-04-22", "Text"=>"Order Processing screen:Shipping addres—remove—we took this out of the payment request form.");
	$results[] = array("ID"=>47, "Done"=>"2010-04-22", "Text"=>"Order Processing screen:Customer notes---list in order – after the credit card info. – not before.");
	$results[] = array("ID"=>48, "Done"=>"2010-04-22", "Text"=>"Order payment request – printed form:Billing address   -  line 1,2,3, - separate the lines. At this time the lines 1,2,3 are printing on one line, separate to print on 3 individual lines. See V Rivera Test 4/6/10—account name.");
	$results[] = array("ID"=>49, "Done"=>"2010-05-03", "Text"=>"Order Processing screen:Process and print bar--- not working  -after 1st print  and return to screen, the Payment(red box) still shows qty of 1 ");
	$results[] = array("ID"=>50, "Done"=>"2010-05-03", "Text"=>"Order Processing screen:unprocessed payment.  Also the Process state—still reflects unprocessed.");
	$results[] = array("ID"=>51, "Done"=>"2010-05-04", "Text"=>"Decrease margin on Balance Due letter by about an inch");
	$results[] = array("ID"=>52, "Done"=>"2010-05-04", "Text"=>"Order form, REview request - Fields need to be longer");
	$results[] = array("ID"=>53, "Done"=>"2010-05-04", "Text"=>"Not submitting orders into Database");
	$results[] = array("ID"=>54, "Done"=>"", "Text"=>"Eliminate 'duplicates' in the web order processing stuff");
	$results[] = array("ID"=>55, "Done"=>"2010-05-26", "Text"=>"Order processing screen, 'auto refresh'. (Expire contents)");
	$results[] = array("ID"=>56, "Done"=>"2010-07-12*", "Text"=>"FIX date of submissions on orders if possible.");
	$results[] = array("ID"=>57, "Done"=>"2010-05-05", "Text"=>"FL Tax not correct. See Inv#60891");
	$results[] = array("ID"=>58, "Done"=>"2010-05-04", "Text"=>"Re-Edit does not show CC info");
	$results[] = array("ID"=>59, "Done"=>"2010-05-23", "Text"=>"minimum installment payment is $100");
	$results[] = array("ID"=>60, "Done"=>"2010-05-24", "Text"=>"Dealer spec letter - Need to be able to enter just month/year");
	$results[] = array("ID"=>61, "Done"=>"2010-05-26", "Text"=>"Dealer spec letter - need 3 inch top margin");
	$results[] = array("ID"=>62, "Done"=>"2010-07-12", "Text"=>"Credit Card info not displaying for customer to review on order submission.");
	$results[] = array("ID"=>63, "Done"=>"2010-07-12", "Text"=>"Payment request PDF does not have amount");
	$results[] = array("ID"=>64, "Done"=>"2010-07-12*", "Text"=>"Back button from order PDF goes back twice?");
	$results[] = array("ID"=>65, "Done"=>"2010-07-13", "Text"=>"Catalog request - Make CC info only show up if NOT USA request.");
	$results[] = array("ID"=>66, "Done"=>"", "Text"=>"Payment request should send email at the end of process. Include dollar amount customer entered.");
	$results[] = array("ID"=>67, "Done"=>"2010-07-12", "Text"=>"Admin catalog - make default list the catagories instead of a pull down.");
	$results[] = array("ID"=>68, "Done"=>"2010-07-12", "Text"=>"Add an inch to dealer spec letter top margin.");
	$results[] = array("ID"=>69, "Done"=>"", "Text"=>"add the email address to the payment form as a required field and send email confirm");
	$results[] = array("ID"=>70, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>71, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>72, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>73, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>74, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>75, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>76, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>77, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>78, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>79, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>80, "Done"=>"", "Text"=>"");
	
	return $results;
}

function toDoPage(){
	$results = "Web priority list <i>Last Updated July 13, 2010</i><br />";
	$results .= "<B><i>Date</i>*</B> indicates - Unable to reproduce.";
	$results .= "<ul id='toDoList'>";
	$items = toDoItems();
	usort ( $items , "toDoSort" );
	foreach ($items as $item) {
		$task=htmlizeText($item['Text']);
		
		if(strlen($task)>0){
			$results .= "<li>";
			$results .= "<div class='id'>#" . $item['ID'] . "</div>";
			
			if(strlen($item['Done'] > 0)){
				$results .= "<div class='donedate'>" . $item['Done'] . "</div>";
				$results .= "<div class='done'>$task</div>";
			} else {
				$results .= "<div class='desc'>$task</div>";
			}
			
			$results .= "</li>\n";
		}
	}
	$results .= "</ul>";
	
	return $results;	
}
?>
