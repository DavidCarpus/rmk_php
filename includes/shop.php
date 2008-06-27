<?php
include_once "forms/Shop.class.php";
include_once "db/Parts.class.php";
include_once "db/Customers.class.php";
include_once "db/Invoices.class.php";

ini_set('session.cache_limiter', 'private');

$address = '192.168.1.101';
if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
	$db_server = "localhost";
	$db_username="root";
	$db_password="skeet100";
	$db_webDatabase="newrmk";
}

$address = '192.168.1.99';
if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
	$db_server = "localhost";
	$db_username="rmkweb";
	$db_password="rmkskeet";
	$db_webDatabase="newrmk";
}

$g_partPrices=array();
$shopForms = new ShopForms();
$parts = new Parts();
$Invoices = new Invoices();

function test(){
	global $parts;
	$part = $parts->fetchPart(10 , 2007);
	return print_r($part,true);
}

//=========================================================
$sortOptions = array("Invoice"=>"Customers.LastName, Customers.FirstName, Invoice desc", 
			"Date Ordered"=>"Customers.LastName, Customers.FirstName, UNIX_TIMESTAMP(DateOrdered) desc",
			"Date Estimated"=>"Customers.LastName, Customers.FirstName, UNIX_TIMESTAMP(DateEstimated) ASC"
);

function fieldDesc($field){
	$lookup = array('invoice_num'=>'Invoice Number','lastname'=>'Last Name',
	'firstname'=>'First Name', 'phonenumber'=>'Phone Number', 'dateordered'=>"Date Ordered",
	'dateestimated'=>"Date Estimated", 'lastname'=>"Last Name",  'firstname'=>"First Name",  'dateshipped'=>"Date Shipped",
	);
	return $lookup[$field];
}

function fields(){
	return array('invoice_num', 'lastname', 'firstname', 'phone'
	);	
}
//=========================================================

function knifeList($form){
	global $g_partPrices;
	global $sortOptions;
	$results="";
	$week =-12;
	if(array_key_exists('week', $form) ) 	$week = $form['week'];
	//~ $week += 1;
	$lastThurs = strtotime("-" . (date('w')-4+7) . " days");
	$startTime = strtotime(date("Y-m-d", $lastThurs) ." " . $week . " weeks");
	$startDate = date("Y-m-d",$startTime);
//	$endDate=date("Y-m-d", strtotime(date("Y-m-d", $startTime) ." +1 week"));
	$endDate=date("Y-m-d", strtotime(date("Y-m-d", $startTime) ." +1 day"));
	
	$query = 
		"Select Invoices.Invoice,Invoices.CustomerID, Dealer, dateestimated, dateordered,
		Customers.LastName,Customers.FirstName
		from Invoices 
		left join Customers on Customers.CustomerID = Invoices.CustomerID
		where dateestimated between '$startDate' and '$endDate' 
		order by Invoices.Invoice";
//		order by Dealer, Invoices.Invoice";

		$records = getDbRecords($query);
//	$results .= $query;
	$custid=0;
	$custInv = array();
	$results .= knifeListNav($week, $startDate, $endDate);
	$results .= "<P>";
	foreach($records as $Invoice){
		if($custid==0) $custid=$Invoice['CustomerID'];
		if($custid != $Invoice['CustomerID']){
			$results .= displayKnifeListInvoices($custInv) . "<BR>";
			$custInv=array();
		}
		$custInv[] = $Invoice;
		$custid=$Invoice['CustomerID'];
	}
	$results .= "</P>";
	return $results;
}

function displayKnifeListInvoices($invoices){
	global $parts, $Invoices;
	
	
	$results ="\n\n<TABLE class='knifelist' border='1'>";
	if(count($invoices) > 0 && $invoices[0]['Dealer']){
		$results .= "<TR>";
		$results .= "<TH colspan='4'>" . $invoices[0]['FirstName'] . " " . $invoices[0]['LastName'] . "</TH>";
		$results .= "</TR>";
	}
	
	$bladeListItems=0;
	
	foreach($invoices as $Invoice){
		$knifeCount=0;

//		echo dumpDBRecord( $Invoice );
		
//		if($Invoice['Dealer']){
//			$results .= "<TR>";
//			$results .= "<TD class='invoice'>" . invoiceDetailLink($Invoice['Invoice']). "</TD>";
//			$results .= "<TD colspan='3'>" . $invoices[0]['FirstName'] . " " . $invoices[0]['LastName'] . "</TD>";
//			$results .= "</TR>";
//		} else{
			$results .= "<TR>";
			$results .= "<TD class='invoice'>" . invoiceDetailLink($Invoice['Invoice']). "</TD>";
			$results .= "<TD colspan='3'></TD>";
			$results .= "</TR>\n";
//		}
			
		$year = date("Y", strtotime($Invoice['dateordered']));
		
		if(!array_key_exists('entries', $Invoice))
			$Invoice['entries'] = $Invoices->items($Invoice['Invoice']);
			
		$alt=1;
		foreach($Invoice['entries'] as $entry){
			$alt = (!$alt);
			if($entry['BladeItem']){
				$bladeListItems++;
				$shadeTag="";
				if($alt) $shadeTag="class='shade'";
				$results .= "<TR " . $shadeTag . ">";
				$results .= "<TD class='quantity'>" . $entry['Quantity']  . "</TD>";
				$results .= "<TD class='partcode'>" . $entry['PartCode']  . "</TD>";
				$knifeCount += $entry['Quantity'] ;

				$results .= knifeEntryAdditions_TableCell($entry['InvoiceEntryID'] , $year);

				$comment = $entry['Comment'];
				if(strlen($comment) <= 0) $comment=" ";
				$results .= "<TD class='comment'>$comment</TD>";
				$results .= "</TR>\n";
			}
		}
		if(count($Invoice['entries']) > 1){
			$results .= "<TR class='summary'>";
			$results .= "<TD  class='quantity' >" . $knifeCount. "</TD>" ;
			$results .= "<TD > Total (". $Invoice['Invoice']. ")</TD>" ;
			$results .= "<TD colspan='2'></TD>";
			$results .= "</TR>\n";
		}

	}
	$results .="</TABLE>";
	if($bladeListItems > 0)
		return $results;
	else
		return "";
}

function invoiceDetailLink($invNum){
	return "<a href=view_invoice.php?invoice_num=$invNum>$invNum</a>";
}

function knifeListNav($week, $startDate, $endDate){
	$results = "\n";
	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week -1) . "> Previous Week</a>";

	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
//	$results .= "Week of <B>" .  substr($startDate,5)  . "</B> to <B>" .   substr($endDate,5) . "</B> (".substr($endDate,0,4). ")";
	$results .= "Week of <B>" .  $startDate  . "</B>";
	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week +1) . "> Next Week</a>";
	$results .= "\n";
	return $results;
}
//================================================
//================================================

function orderDisplay(){
	global $shopForms;
	$form = getFormValues();
	$linkedIn=true;
	$results="";
	if( array_key_exists('submit', $form)  ) $linkedIn=false;
	// Fresh search form?
	if( !array_key_exists('submit', $form)  &&  !array_key_exists('invoice_num', $form) ) $linkedIn=false;
	//~ $results .=  $linkedIn . dumpDBRecord($form) . "</HR>";
	if($linkedIn){ 
		// Linked in do not display form
	} else{
		$results .= $shopForms->orderSearchForm($form);
//		$results .= orderSearchForm($form);
	}
	$results .= displaySearchResults($form);
	return $results;
}

function displaySearchResults($form)
{
	$query=orderSearchQueryForShop($form);
	if($query=="") return "";
	$records = getDbRecords($query);
//	debugStatement($query);
	if(count($records) == 0){
		return "No Matching invoices!";
	}
	$results="";
	if(count($records) == 1){
		$record = $records[0];
		$results .= displayInvoiceDetailsForShop($record);
	} else {
		$results .= displayInvoiceList($records);
	}
	return $results;
}

function displayInvoiceDetailsForShop($record){
	global $Invoices;
	$results = "";

	$shipping =  $Invoices->shipAddressString($record);
	$billing = $Invoices->billingAddressString($record);
	
	$results .= "<TABLE>";
	$results .=  "<TR>";
	$results .=  "<TH>". $record['LastName'] . "," .  $record['FirstName'];
	if($record['Dealer'])
		$results .=  "<B><I size=+4>Dealer</I></B> ";
	$results .=  "</TH>";
	$results .=  "</TR>";
	

	$results .= "<TR><TD><B>".fieldDesc('phonenumber')."</B></TD><TD>" . $record['PhoneNumber'] . "</TD></TR>";
	$results .= "<TR><TD><B>".fieldDesc('invoice_num')."</B></TD><TD>" . $record['Invoice'] . "</TD></TR>";
	$results .= "<TR><TD><B>".fieldDesc('dateordered')."</B></TD><TD>" . $record['dateordered'] . "</TD></TR>";
	$results .= "<TR><TD><B>".fieldDesc('dateestimated')."</B></TD><TD>" . $record['dateestimated'] . "</TD></TR>";
	$results .= "<TR><TD><B>Shipping</B></TD><TD>" . $record['ShippingInstructions'] . "</TD></TR>";

	if($record['dateshipped']  != NULL){
		$results .= "<TR><TD><B>".fieldDesc('dateshipped')."</B></TD><TD><B><I>" . $record['dateshipped'] . "</I></B></TD></TR>";
	}
	$results .= "<TR>";
	$results .= "<TD><B>Bill To</B></TD><TD>$billing</TD>";
	$results .= "<TD><B>Ship To</B></TD><TD>$shipping</TD>";
	$results .= "<TR>";

	$results .=  "</TABLE>";
	$results .=  "</BR>";
	
	$record['entries'] = $Invoices->items($record['Invoice']);
	
	$results .=  displayKnifeListInvoices(array($record));
	
	$costs = $Invoices->computeCosts($record);

	$results .=  "</BR>";
	$results .=  " vvvvvvvvvvvvv This is preliminary (still needs double checking) vvvvvvvvvvvvvvvvvvvv";
	$results .= "<TABLE>";
	$results .=  "<TR><TD><B>TotalCost</B></TD><TD align=right>$ " .  number_format($costs['TotalCost'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Subtotal</B></TD><TD align=right>$ " .  number_format($costs['Subtotal'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Shipping</B></TD><TD align=right>$ " .  number_format($costs['Shipping'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Taxes</B></TD><TD align=right>$ " .  number_format($costs['Taxes'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>TotalPayments</B></TD><TD align=right>$ " .  number_format($costs['TotalPayments'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Due</B></TD><TD align=right>$ " .  number_format($costs['Due'] ,2). "</TD></TR>";
	$results .=  "</TABLE>";
	
//	$results .=  dumpDBRecord($costs);
	return $results;
}

function displayInvoiceList($records){
	$results = "<TABLE border=1>";
	$results .= "<TR><TH>Invoice</TH><TH>Last Name</TH><TH>First Name</TH>";
	$results .= "<TH>Ordered</TH><TH>Estimated</TH><TH>Shipped</TH>";
	foreach($records as $record){
		$results .= "<TR>";
		$results .= "<TD>" . invoiceDetailLink($record['Invoice']) . "</TD>";
		$results .= "<TD>" . ($record['LastName']) . "</TD>";
		$results .= "<TD>" . ($record['FirstName']) . "</TD>";
		$results .= "<TD>" . ($record['dateordered']) . "</TD>";
		$results .= "<TD>" . ($record['dateestimated']) . "</TD>";
		$results .= "<TD>" . ($record['dateshipped']) . "</TD>";
//		$results .= "<TD>" . ($record['DateOrdered']) . "</TD>";
		$results .= "</TR>";
//		$results .= dumpDBRecord($record);
	}
	$results .= "</TABLE>";
	return $results;
}




function knifeEntryAdditions_TableCell($entryID, $year){
	global $parts;
	$additions = fetchEntryAdditions($entryID);
	$sheaths = "  MA1 MA2 MAB MB MBB MC MC1 MCB MCR MFB 24B NHS FCH WS BLK LHS LS1 LS2 OK DK ";
	$etching = "  ET1  ET2  ETC ETV NPN NPB EN1 EN2 EN3 EN4 EN5 MED  ";
	$results = "";
	$results .= "<TD  class='additions'>";
	$totalAdds=count($additions);
	$cnt=0;
	if($totalAdds == 0) $results .= " ";
	foreach($additions as $addition){
		$code=" ".$addition['PartCode'] . " ";
		$isSheath = ( strpos($sheaths, $code) > 0);
		$isEtch = ( strpos($etching, $code ) > 0);
		
		if($isSheath ) $results .= "<span class='sheath'>";
		if($isEtch ) $results .= "<span class='etch'>";
		$results .= $addition['PartCode'];

		if($isSheath || $isEtch )  $results .= "</span>";
		if(++$cnt < $totalAdds)
			$results .= ",";
	}
	//~ $results = substr($results, 0, strlen($results)-1);
	$results .= "</TD>";
	return $results;
}
//================================================
//================================================
function fetchEntryAdditions($invEntryID){
	$query = "Select * from InvoiceEntryAdditions  IEA left join Parts P on P.PartID = IEA.PartID where EntryID=$invEntryID order by AdditionID";
	return getDbRecords($query);
}



//function fetchInvoicePayments($invNum){
//	$query = "Select * from Payments where Invoice=$invNum";
//	return getDbRecords($query);
//}




//=================================================
//=================================================
function orderSearchQueryForShop($form)
{
	if(!array_key_exists('lastname', $form) && !array_key_exists('invoice_num', $form)) return "";
	
	$query = "Select Invoice, date_format(DateOrdered, '%M %e %Y') as dateordered, 
			date_format(DateEstimated, '%M %e %Y') as dateestimated,
			date_format(DateShipped, '%M %e %Y') as dateshipped,  
			Invoices.*, Customers.* 
			from Invoices
			left join Customers on Customers.CustomerID = Invoices.CustomerID
			";
			//~ Invoices.ShippingInstructions, Customers.LastName , Customers.FirstName , Customers.Dealer,
			
	$selectionCriteria = "";
	$orderCriteria = "";
	if($form['invoice_num'] > 0){
		$criteria .= "Invoice=" . $form['invoice_num'] . " AND ";
		$orderCriteria .= " ORDER BY Invoice desc ";
	}
	if (array_key_exists('firstname', $form) && $form['firstname'] <> ""){
		$criteria .= "Customers.FirstName like '%" . $form['firstname'] ."%'"  . " AND ";
		if($form['sortby'] != NULL){
			global $sortOptions;
			$orderCriteria = " ORDER BY " . $sortOptions[$form['sortby']];
		} else{
			$orderCriteria = " ORDER BY Customers.LastName, Customers.FirstName, Invoice desc ";
		}
	}
	if (array_key_exists('lastname', $form) && $form['lastname'] <> ""){
		$criteria .= "Customers.LastName like '%" . $form['lastname'] ."%'"  . " AND ";
		if($form['sortby'] != NULL){
			global $sortOptions;
			$orderCriteria = " ORDER BY " . $sortOptions[$form['sortby']];
		} else{
			$orderCriteria = " ORDER BY Customers.LastName, Customers.FirstName, Invoice desc ";
		}
	}
	if (!array_key_exists('searchOlder', $form)) //  || $form['searchOlder'] == "on"
	{
		$minDate = date("Y-m-d",strtotime(date("Y-m-d", time()) ." -1 year"));
		$criteria .= "DateEstimated > '$minDate' AND ";
		
	}
		
	
	
	$criteria = trim($criteria);
	if(endsWith($criteria, "AND"))
	{
		$criteria = substr( $criteria, 0, strlen( $criteria ) - 4 ); 
	}
//	print $query . "where " . $criteria . $orderCriteria;
//	print dumpDBRecord($form);
	return $query . "where " . $criteria . $orderCriteria;
}

function endsWith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

?>
