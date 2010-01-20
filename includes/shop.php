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
	$results .= "<p>";
	foreach($records as $Invoice){
		if($custid==0) $custid=$Invoice['CustomerID'];
		if($custid != $Invoice['CustomerID']){
			$results .= displayKnifeListInvoices($custInv) . "<br />";
			$custInv=array();
		}
		$custInv[] = $Invoice;
		$custid=$Invoice['CustomerID'];
	}

	$results .= knifeListSummary($records);
	$results .= "</p>";
	
	return $results;
}

function knifeListSummary($invoices)
{
	global $parts, $Invoices;
	$results = ""; 
	$totals=array();
	foreach($invoices as $Invoice){
		if(!array_key_exists('entries', $Invoice))
			$Invoice['entries'] = $Invoices->items($Invoice['Invoice']);
			
		foreach($Invoice['entries'] as $entry){
			if($entry['BladeItem']){
				if(!array_key_exists($entry['PartCode'], $totals))
					$totals[$entry['PartCode']]=0;
				$totals[$entry['PartCode']] += $entry['Quantity'];
			}
		}
	}
	ksort($totals);
	$colCnt=1;
	$results ="\n\n<table class='knifelistsummary' border='1'>";
	$results .= "<tr>";
	foreach($totals as $partCode=>$cnt){
		$results .= "<td class='quantity' align=right>$cnt</td><td class='partcode'>$partCode</td>";
		if(($colCnt++)%6 == 0)
			$results .= "</tr><tr>";
	}
	$results .="</table>";
	return $results;
}

function displayKnifeListInvoices($invoices){
	global $parts, $Invoices;
	
	
	$results ="\n\n<table class='knifelist' border='1'>";
	if(count($invoices) > 0 && $invoices[0]['Dealer']){
		$results .= "<tr>";
		$results .= "<th colspan='4'>" . $invoices[0]['FirstName'] . " " . $invoices[0]['LastName'] . "</th>";
		$results .= "</tr>";
	}
	
	$bladeListItems=0;
	
	foreach($invoices as $Invoice){
		$knifeCount=0;

//		echo dumpDBRecord( $Invoice );
		
//		if($Invoice['Dealer']){
//			$results .= "<tr>";
//			$results .= "<td class='invoice'>" . invoiceDetailLink($Invoice['Invoice']). "</td>";
//			$results .= "<td colspan='3'>" . $invoices[0]['FirstName'] . " " . $invoices[0]['LastName'] . "</td>";
//			$results .= "</tr>";
//		} else{
			$results .= "<tr>";
			$results .= "<td class='invoice'>" . invoiceDetailLink($Invoice['Invoice']). "</td>";
			$results .= "<td colspan='3'></td>";
			$results .= "</tr>\n";
//		}
			
		$year = date("Y", strtotime($Invoice['dateordered']));
		
		if(!array_key_exists('entries', $Invoice))
			$Invoice['entries'] = $Invoices->items($Invoice['Invoice']);
			
		$alt=1;
		foreach($Invoice['entries'] as $entry){
			$alt = (!$alt);
//			echo dumpDBRecord( $entry );
			
			if($entry['BladeItem']){
				$bladeListItems++;
				$shadeTag="";
				if($alt) $shadeTag="class='shade'";
				$results .= "<tr " . $shadeTag . ">";
				$results .= "<td class='quantity'>" . $entry['Quantity']  . "</td>";
				$results .= "<td class='partcode'>" . $entry['PartCode']  . "</td>";
				$knifeCount += $entry['Quantity'] ;

				$results .= knifeEntryAdditions_TableCell($entry['InvoiceEntryID'] , $year);

				$comment = $entry['Comment'];
				if(strlen($comment) <= 0) $comment=" ";
				$results .= "<td class='comment'>$comment</td>";
				$results .= "</tr>\n";
			}
		}
		if(count($Invoice['entries']) > 1){
			$results .= "<tr class='summary'>";
			$results .= "<td  class='quantity' >" . $knifeCount. "</td>" ;
			$results .= "<td > Total (". $Invoice['Invoice']. ")</td>" ;
			$results .= "<td colspan='2'></td>";
			$results .= "</tr>\n";
		}

	}
	$results .="</table>";
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
//	$results .= "Week of <b>" .  substr($startDate,5)  . "</b> to <b>" .   substr($endDate,5) . "</b> (".substr($endDate,0,4). ")";
	$results .= "Week of <b>" .  $startDate  . "</b>";
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
	
	$results .= "<table>";
	$results .=  "<tr>";
	$results .=  "<th>". $record['LastName'] . "," .  $record['FirstName'];
	if($record['Dealer'])
		$results .=  "<b><I size=+4>Dealer</I></b> ";
	$results .=  "</th>";
	$results .=  "</tr>";
	

	$results .= "<tr><td><b>".fieldDesc('phonenumber')."</b></td><td>" . $record['PhoneNumber'] . "</td></tr>";
	$results .= "<tr><td><b>".fieldDesc('invoice_num')."</b></td><td>" . $record['Invoice'] . "</td></tr>";
	$results .= "<tr><td><b>".fieldDesc('dateordered')."</b></td><td>" . $record['dateordered'] . "</td></tr>";
	$results .= "<tr><td><b>".fieldDesc('dateestimated')."</b></td><td>" . $record['dateestimated'] . "</td></tr>";
	$results .= "<tr><td><b>Shipping</b></td><td>" . $record['ShippingInstructions'] . "</td></tr>";

	if($record['dateshipped']  != NULL){
		$results .= "<tr><td><b>".fieldDesc('dateshipped')."</b></td><td><b><I>" . $record['dateshipped'] . "</I></b></td></tr>";
	}
	$results .= "<tr>";
	$results .= "<td><b>Bill To</b></td><td>$billing</td>";
	$results .= "<td><b>Ship To</b></td><td>$shipping</td>";
	$results .= "<tr>";

	$results .=  "</table>";
	$results .=  "<br />";
	
	$record['entries'] = $Invoices->items($record['Invoice']);
	
	$results .=  displayKnifeListInvoices(array($record));
	
	$costs = $Invoices->computeCosts($record);

	$results .=  "<br />";
	$results .=  " vvvvvvvvvvvvv This is preliminary (still needs double checking) vvvvvvvvvvvvvvvvvvvv";
	$results .= "<table>";
	$results .=  "<tr><td><b>TotalCost</b></td><td align=right>$ " .  number_format($costs['TotalCost'] ,2). "</td></tr>";
	$results .=  "<tr><td><b>Subtotal</b></td><td align=right>$ " .  number_format($costs['Subtotal'] ,2). "</td></tr>";
	$results .=  "<tr><td><b>Shipping</b></td><td align=right>$ " .  number_format($costs['Shipping'] ,2). "</td></tr>";
	$results .=  "<tr><td><b>Taxes</b></td><td align=right>$ " .  number_format($costs['Taxes'] ,2). "</td></tr>";
	$results .=  "<tr><td><b>TotalPayments</b></td><td align=right>$ " .  number_format($costs['TotalPayments'] ,2). "</td></tr>";
	$results .=  "<tr><td><b>Due</b></td><td align=right>$ " .  number_format($costs['Due'] ,2). "</td></tr>";
	$results .=  "</table>";
	
//	$results .=  dumpDBRecord($costs);
	return $results;
}

function displayInvoiceList($records){
	$results = "<table border=1>";
	$results .= "<tr><th>Invoice</th><th>Last Name</th><th>First Name</th>";
	$results .= "<th>Ordered</th><th>Estimated</th><th>Shipped</th>";
	foreach($records as $record){
		$results .= "<tr>";
		$results .= "<td>" . invoiceDetailLink($record['Invoice']) . "</td>";
		$results .= "<td>" . ($record['LastName']) . "</td>";
		$results .= "<td>" . ($record['FirstName']) . "</td>";
		$results .= "<td>" . ($record['dateordered']) . "</td>";
		$results .= "<td>" . ($record['dateestimated']) . "</td>";
		$results .= "<td>" . ($record['dateshipped']) . "</td>";
//		$results .= "<td>" . ($record['DateOrdered']) . "</td>";
		$results .= "</tr>";
//		$results .= dumpDBRecord($record);
	}
	$results .= "</table>";
	return $results;
}




function knifeEntryAdditions_TableCell($entryID, $year){
	global $parts, $Invoices;
//	$additions = fetchEntryAdditions($entryID);
	$additions =  $Invoices->additions($entryID);
	
	$sheaths = "  MA1 MA2 MAB MB MBB MC MC1 MCB MCR MFB 24B NHS FCH WS BLK LHS LS1 LS2 OK DK ";
	$etching = "  ET1  ET2  ETC ETV NPN NPB EN1 EN2 EN3 EN4 EN5 MED  ";
	$results = "";
	$results .= "<td  class='additions'>";
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
	$results .= "</td>";
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
	if (!array_key_exists('searchOlder', $form) 
		&& !array_key_exists('invoice_num', $form) ) //  || $form['searchOlder'] == "on"
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
