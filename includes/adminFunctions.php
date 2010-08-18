<?php
/* Created on Feb 4, 2006 */

function adminProcessing(){
	echo "Administrate order/catalog requests and emails.</br>";
}

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
//	$results[] = array("ID"=>35, "Done"=>"2010-03-09", "Text"=>"Shop knife list - add column and list models for each invoice");
//	$results[] = array("ID"=>36, "Done"=>"2010-03-06", "Text"=>"Admin Order processing - Only show CC info on orders and catalog requests for foreign (do not show for US catalog requests)");
//	$results[] = array("ID"=>37, "Done"=>"2010-03-19", "Text"=>"Admin Order processing - Orders and quotes should display ALL info (model, blade length, ...)");
//	$results[] = array("ID"=>38, "Done"=>"2010-03-19", "Text"=>"?? Sort orders to have foreign seperate from US??");
//	$results[] = array("ID"=>39, "Done"=>"2010-03-25", "Text"=>"Admin Order processing - Need option to 'process' ALL catalog requests");
//	$results[] = array("ID"=>40, "Done"=>"2010-03-31", "Text"=>"Admin Order processing - Orders and quotes should be one per page on printable list");
//	$results[] = array("ID"=>41, "Done"=>"", "Text"=>"Labels for foreign should have more lines. Seperate out 'state and zip' on seperate line?");
//	$results[] = array("ID"=>42, "Done"=>"2010-04-05", "Text"=>"Fix 'codes' and apostraphes on Dealer spec letter. Acrobat problem?");
//	$results[] = array("ID"=>43, "Done"=>"2010-04-22", "Text"=>"Balance due letters: Reference invoice #60891, florida sales tax,   amount due reflects $606.00,  does not include the tax due of $45.89 in the total amount due.");
//	$results[] = array("ID"=>44, "Done"=>"2010-04-22", "Text"=>"Balance due letters: The top and left and right margin need to be adjusted.  The top margin needs to be  2.5” to accommodate letterhead.    Left  and right margins could use another 1/2 inch. ");
//	$results[] = array("ID"=>45, "Done"=>"2010-04-22", "Text"=>"Balance due letters: Can you center the payment info at the end of the letter,  “If payment by credit card:  Visa, MC, Disc,    and card number  block of info.");
//	$results[] = array("ID"=>46, "Done"=>"2010-04-22", "Text"=>"Order Processing screen:Shipping addres—remove—we took this out of the payment request form.");
//	$results[] = array("ID"=>47, "Done"=>"2010-04-22", "Text"=>"Order Processing screen:Customer notes---list in order – after the credit card info. – not before.");
//	$results[] = array("ID"=>48, "Done"=>"2010-04-22", "Text"=>"Order payment request – printed form:Billing address   -  line 1,2,3, - separate the lines. At this time the lines 1,2,3 are printing on one line, separate to print on 3 individual lines. See V Rivera Test 4/6/10—account name.");
	$results[] = array("ID"=>49, "Done"=>"2010-05-03", "Text"=>"Order Processing screen:Process and print bar--- not working  -after 1st print  and return to screen, the Payment(red box) still shows qty of 1 ");
	$results[] = array("ID"=>50, "Done"=>"2010-05-03", "Text"=>"Order Processing screen:unprocessed payment.  Also the Process state—still reflects unprocessed.");
	$results[] = array("ID"=>51, "Done"=>"2010-05-04", "Text"=>"Decrease margin on Balance Due letter by about an inch");
	$results[] = array("ID"=>52, "Done"=>"2010-05-04", "Text"=>"Order form, REview request - Fields need to be longer");
	$results[] = array("ID"=>53, "Done"=>"2010-05-04", "Text"=>"Not submitting orders into Database");
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
	$results[] = array("ID"=>66, "Done"=>"2010-07-13", "Text"=>"Payment request should send email at the end of process. Include dollar amount customer entered.");
	$results[] = array("ID"=>67, "Done"=>"2010-07-12", "Text"=>"Admin catalog - make default list the catagories instead of a pull down.");
	$results[] = array("ID"=>68, "Done"=>"2010-07-12", "Text"=>"Add an inch to dealer spec letter top margin.");
	$results[] = array("ID"=>69, "Done"=>"2010-07-13", "Text"=>"add the email address to the payment form as a required field and send email confirm");
	$results[] = array("ID"=>70, "Done"=>"2010-07-13", "Text"=>"Add 'All amounts in USD' next to amount field for payments");
	$results[] = array("ID"=>71, "Done"=>"2010-07-15", "Text"=>"Make payment req show cc info all the time.");
	$results[] = array("ID"=>72, "Done"=>"2010-07-15", "Text"=>"Amounts not showing on email following submission of payment. (DB problem?)");
	$results[] = array("ID"=>73, "Done"=>"2010-07-28", "Text"=>"Customer notes entered by shop on orders/requests not showing up on PDF. Make notes auto save when exiting notes.");
	$results[] = array("ID"=>74, "Done"=>"2010-07-15", "Text"=>"Line up notes and comments on pdf with the rest of the 'text'");
	$results[] = array("ID"=>75, "Done"=>"2010-07-15", "Text"=>"Quote request displays message for orders. Should be seperate message.Thank u for the quote request. We should reply within three business days.");
	$results[] = array("ID"=>76, "Done"=>"2010-07-14", "Text"=>"Orders and quotes showing up as catalog requests?");
	$results[] = array("ID"=>77, "Done"=>"2010-07-15", "Text"=>"Submitting order/quotes crossing wires?");
	$results[] = array("ID"=>78, "Done"=>"2010-07-16", "Text"=>"Processing orders - Need model number and blade length");
	$results[] = array("ID"=>80, "Done"=>"2010-07-16", "Text"=>"Comma seperate seperate lines of address.");
	$results[] = array("ID"=>81, "Done"=>"2010-07-16", "Text"=>"Change text of quote request response");
	$results[] = array("ID"=>82, "Done"=>"2010-07-16", "Text"=>"Catalog Request - 'All countries outside of the US please complete credit card information'");
	$results[] = array("ID"=>83, "Done"=>"2010-07-16", "Text"=>"Send us a message not working");
	$results[] = array("ID"=>84, "Done"=>"2010-07-16", "Text"=>"Clear CC# for processed items after print & process");
	$results[] = array("ID"=>85, "Done"=>"2010-07-16", "Text"=>"Remove label pages from order/quote PDF");
	$results[] = array("ID"=>86, "Done"=>"2010-07-16", "Text"=>"reply emails have programming(HTML)  in the text.");
	$results[] = array("ID"=>87, "Done"=>"2010-05-06", "Text"=>"Minimum payments should be $100");
	$results[] = array("ID"=>88, "Done"=>"2010-08-01", "Text"=>"balance due letter – Sort by INVOICE number");
	$results[] = array("ID"=>89, "Done"=>"2010-08-01", "Text"=>"Order processing - Move extra features afer blade length");
	$results[] = array("ID"=>90, "Done"=>"", "Text"=>"Gary Jul 20 -On the quote form (& maybe other forms) increase the field sizes");	
	$results[] = array("ID"=>91, "Done"=>"2010-08-18", "Text"=>"ORDER FORM -  add the date order placed to the screen and to the printed hard copy.");
	$results[] = array("ID"=>92, "Done"=>"2010-08-18", "Text"=>"ORDER FORM -  add to the credit card info --- name as it appears on the credit card");
	$results[] = array("ID"=>93, "Done"=>"2010-08-18", "Text"=>"PAYMENT REQUEST -  need a return email address on the screen to utilize and also to print on hard copy.");
	$results[] = array("ID"=>94, "Done"=>"2010-08-18", "Text"=>"Gary July 26 - If third line down prints the same as the first address line, delete");
	$results[] = array("ID"=>95, "Done"=>"2010-08-18", "Text"=>"Val call Aug 17 Problem with label for spain order - perez");
	$results[] = array("ID"=>96, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>97, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>98, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>99, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>100, "Done"=>"", "Text"=>"");	
	$results[] = array("ID"=>101, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>102, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>103, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>104, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>105, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>106, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>107, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>108, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>109, "Done"=>"", "Text"=>"");
	$results[] = array("ID"=>110, "Done"=>"", "Text"=>"");	
	
/*
 * 
 * March 29
Payment section – drop the cover page, no need.
Order Payment Request page --   insert space between the heading ‘order payment request’  and the ‘account info’ – at least of couple of inches  since we have the whole page..
 
When entering the info on payment request form -  ‘Comments’  box is missing.
 
When you tab from the last box which is comments, tab to ‘review bar’, then review page.
After reviewing page, the ‘send bar’  should be more visible. When I was testing it was at the bottom of page, out of sight.
 * 	
 */
	
	$results[] = array("ID"=>10, "Done"=>"", "Text"=>"Time submitted &#150; when an order,quote,cat,  is sent to processed, the time will vary. We need one permanent time and date stamp. No changes.");
	$results[] = array("ID"=>11, "Done"=>"", "Text"=>"Processed order &#150; purge option.");
	$results[] = array("ID"=>12, "Done"=>"", "Text"=>"Examples of Combinations -  list model, desc, and price.");
	$results[] = array("ID"=>13, "Done"=>"", "Text"=>"Set up so that all catalogue requests are deleted every week. (Gary 2006-10-22)");
	$results[] = array("ID"=>14, "Done"=>"", "Text"=>"&rsquo; in name and address fields uses  &rsquo;");
	$results[] = array("ID"=>54, "Done"=>"", "Text"=>"Eliminate 'duplicates' in the web order processing stuff");
	$results[] = array("ID"=>73, "Done"=>"", "Text"=>"Customer notes entered by shop on orders/requests not showing up on PDF. Make notes auto save when exiting notes.");
	$results[] = array("ID"=>79, "Done"=>"", "Text"=>"Formatting of labels for catalog requests off.");
	
	return $results;
}

function toDoPage(){
	$items = toDoItems();
	usort ( $items , "toDoSort" );
	$lastFinish="";
	foreach ($items as $item) {
		if(strlen($item['Done'] > 0)){ $lastFinish = $item['Done']; break; }
	}
	
//	$results = "Web priority list <i>Last Updated July 16, 2010</i><br />";
	$lastFinish = Date("M j,Y", strtotime($lastFinish));
	$results = "Web priority list <i>Last Updated <B>$lastFinish</B></i><br />";
	$results .= "<B><i>Date</i>*</B> indicates - Unable to reproduce.";
	$results .= "<ul id='toDoList'>";
	foreach ($items as $item) {
		$task=htmlizeText($item['Text']);
		
		if(strlen($task)>0){
			$results .= "<li>";
			$results .= "<div class='id'>#" . $item['ID'] . " </div> ";
			
			if(strlen($item['Done'] > 0)){
				$results .= "<div class='donedate'> " . $item['Done'] . "</div> ";
				$results .= "<div class='done'> $task</div>";
			} else {
				$results .= "<div class='desc'> $task</div>";
			}
			
			$results .= "</li>\n";
		}
	}
	$results .= "</ul>";
	
	return $results;	
}
?>
