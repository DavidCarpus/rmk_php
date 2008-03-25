<?php
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

//=========================================================
$sortOptions = array("Invoice"=>"Customers.LastName, Customers.FirstName, Invoice desc", 
			"Date Ordered"=>"Customers.LastName, Customers.FirstName, UNIX_TIMESTAMP(DateOrdered) desc");

function fieldDesc($field){
	$lookup = array('invoice_num'=>'Invoice Number','lastname'=>'Last Name',
	'firstname'=>'First Name', 'phonenumber'=>'Phone Number', 'dateordered'=>"Date Ordered",
	'dateestimated'=>"Date Estimated", 'lastname'=>"Last Name",  'dateshipped'=>"Date Shipped",
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
	$startDay = (date("d")- date('w') + 7*$week);
	$startDate  = date("Y-m-d", mktime(0, 0, 0, date("m")  ,$startDay, date("Y")));
	$endDate=date("Y-m-d", mktime(0, 0, 0, date("m")  , $startDay+7, date("Y")));

	$query = 
		"Select Invoices.Invoice,Invoices.CustomerID, Dealer, dateestimated,
		Customers.LastName,Customers.FirstName
		from Invoices 
		left join Customers on Customers.CustomerID = Invoices.CustomerID
		where dateestimated between '$startDate' and '$endDate' 
		order by Dealer, LastName, Invoices.Invoice";
	$records = getDbRecords($query);
	$custid=0;
	$custInv = array();
	$results .= knifeListNav($week);
	foreach($records as $Invoice){
		if($custid==0) $custid=$Invoice['CustomerID'];
		if($custid != $Invoice['CustomerID']){
			$results .= displayKnifeListInvoices($custInv) . "<BR>";
			$custInv=array();
		}
		$custInv[] = $Invoice;
		$custid=$Invoice['CustomerID'];
	}
	return $results;
}

function displayKnifeListInvoices($invoices){
	$results ="\n\n<TABLE class='knifelist' border='1'>";
	if(count($invoices) > 0 && $invoices[0]['Dealer']){
	$results .= "<TR>";
	$results .= "<TH colspan='4'>" . $invoices[0]['FirstName'] . " " . $invoices[0]['LastName'] . "</TH>";
	$results .= "</TR>";
	}
	
	foreach($invoices as $Invoice){
		$knifeCount=0;
		$results .= "<TR>";
		$results .= "<TD class='invoice'>" . invoiceDetailLink($Invoice['Invoice']). "</TD>";
		$results .= "<TD class='quantity'>Qty.</TD>";
		$results .= "<TD colspan='2'></TD>";
		
		$results .= "</TR>\n";
		$year = date("Y", strtotime($Invoice['dateestimated']));
		$entries = fetchInvoiceEntries($Invoice['Invoice']);
		$alt=1;
		foreach($entries as $entry){
			$alt = (!$alt);
			$part = fetchPart($entry['PartID'] , $year);
			if($part['PartCode'] != "KNV"){
				$shadeTag="";
				if($alt) $shadeTag="class='shade'";
				$results .= "<TR " . $shadeTag . ">";
				$results .= "<TD class='partcode'>" . $part['PartCode']  . "</TD>";
				$results .= "<TD class='quantity'>" . $entry['Quantity']  . "</TD>";
				$knifeCount += $entry['Quantity'] ;

				$results .= knifeEntryAdditions_TableCell($entry['InvoiceEntryID'] , $year);

				$comment = $entry['Comment'];
				if(strlen($comment) <= 0) $comment=" ";
				$results .= "<TD class='comment'>$comment</TD>";
				$results .= "</TR>\n";
			}
		}
		if(count($entries) > 1){
			$results .= "<TR class='summary'>";
			$results .= "<TD > Total (". $Invoice['Invoice']. ")</TD>" ;
			$results .= "<TD >" . $knifeCount. "</TD>" ;
			$results .= "<TD colspan='2'></TD>";
			$results .= "</TR>\n";
		}

	}
	$results .="</TABLE>";
	return $results;
}
function invoiceDetailLink($invNum){
	return "<a href=view_invoice.php?invoice_num=$invNum>$invNum</a>";
}
function knifeListNav($week){
	$startDay = (date("d")- date('w') + 7*$week);
	$startDate  = date("m/d", mktime(0, 0, 0, date("m")  ,$startDay, date("Y")));
	$endDate=date("m/d", mktime(0, 0, 0, date("m")  , $startDay+7, date("Y")));
	$year = date("Y", mktime(0, 0, 0, date("m")  , $startDay, date("Y")));

	$results = "\n";
	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week -1) . "> Previous Week</a>";

	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
	$results .= "<B>Week of " .  $startDate  . " - " .  $endDate  . " (" . $year . ")</B>";
	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	
	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week +1) . "> Next Week</a>";
	$results .= "\n";
	return $results;
}
//================================================
//================================================
function orderDisplay(){
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
		$results .= orderSearchForm($form);
	}
	$results .= displaySearchResults($form);
	return $results;
}
function orderSearchForm($request){
	global $sortOptions;
	$results="";
	$results .=  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
	$results .=  textField('invoice_num', fieldDesc('invoice_num'), false, $request['invoice_num']). "<BR>\n" ;
	$results .= textField('lastname', fieldDesc('lastname'), false, $request['lastname']). "<BR>\n" ;
	$sortField="sortby";
	$request[$sortField];
	$values="";
	foreach($sortOptions as $id=>$sql){
		$selection = array("id"=>$id, 'label'=>$id);
		$values[] = $selection;
	}
	$results .= selection($sortField, $values, "Sort By", $selected=$request[$sortField], $autosubmit=false);
	$results .= "<center><input class='btn' type='submit' name='submit' value='Search' ></center>\n" ;
	$results .=  "</form>";
	return $results;
}
function displaySearchResults($form)
{
	$query=orderSearchQueryForShop($form);
	if($query=="") return "";
	$records = getDbRecords($query);
	if(count($records) == 0){
		return "No Matching invoices!";
	}
	if(count($records) == 1){
		$record = $records[0];
		return displayInvoiceDetailsForShop($record);
	} else {
		return displayInvoiceList($records);
	}
}
function displayInvoiceDetailsForShop($record){
	$results = "";

	$shipping .=  invoiceShipAddress($record);
	
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
	if($record['dateshipped']  != NULL)
		$results .= "<TR><TD><B>".fieldDesc('dateshipped')."</B></TD><TD><B><I>" . $record['dateshipped'] . "</I></B></TD></TR>";
	$results .= "<TR><TD><B>Shipping</B></TD><TD>" . $record['ShippingInstructions'] . "</TD></TR>";
	$results .= "<TR><TD><B>Ship To</B></TD><TD>$shipping</TD></TR>";
	$results .=  "</TABLE>";
	$results .=  "</BR>";
	
	//~ $results .=  dumpDBRecord($record) . "</BR>";

	$results .=  invKnifeList($record);
	
	$costs = computeInvoiceCosts($record);

	$results .=  "</BR>";
	$results .=  " vvvvvvvvvvvvv This is preliminary (still needs double checking) vvvvvvvvvvvvvvvvvvvv";
	$results .= "<TABLE>";
	$results .=  "<TR><TD><B>TotalCost</B></TD><TD>$ " .  number_format($costs['TotalCost'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Subtotal</B></TD><TD>$ " .  number_format($costs['Subtotal'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Shipping</B></TD><TD>$ " .  number_format($costs['Shipping'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Taxes</B></TD><TD>$ " .  number_format($costs['Taxes'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>TotalPayments</B></TD><TD>$ " .  number_format($costs['TotalPayments'] ,2). "</TD></TR>";
	$results .=  "<TR><TD><B>Due</B></TD><TD>$ " .  number_format($costs['Due'] ,2). "</TD></TR>";
	$results .=  "</TABLE>";
	
	//~ $results .=  dumpDBRecord(computeInvoiceCosts($record));
	return $results;
}


function invKnifeList($invoice){
	$records = fetchInvoiceEntries($invoice['Invoice']);
	$year = date("Y", strtotime($invoice['dateestimated']));
	$results .= "<Table border=1>";
	foreach($records as $record){
		$part=fetchPart($record['PartID'] , $year);
		$results .= "<TR>";
		$results .= "<TD>" . $record['Quantity'] . "</TD>";
		$results .= "<TD>" . $part['PartCode'] . "</TD>";
		
		$results .= knifeEntryAdditions_TableCell($record['InvoiceEntryID'] , $year);
		
		$results .= "<TD>" . $record['Comment'] . "</TD>";
		$results .= "</TR>";
	}
	$results .= "</Table>";
	return $results;
}
function displayInvoiceList($records){
	$results .= "<TABLE border=1>";
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
	$additions = fetchEntryAdditions($entryID);
	$sheaths = "  MA1 MA2 MAB MB MBB MC MC1 MCB MCR MFB 24B NHS FCH WS BLK LHS LS1 LS2 OK DK ";
	$etching = "  ET1  ET2  ETC ETV NPN NPB EN1 EN2 EN3 EN4 EN5 MED  ";
	$results = "";
	$results .= "<TD  class='additions'>";
	$totalAdds=count($additions);
	$cnt=0;
	if($totalAdds == 0) $results .= " ";
	foreach($additions as $addition){
		$part=fetchPart($addition['PartID'] , $year);
		$code=" ".$part['PartCode'] . " ";
		$isSheath = ( strpos($sheaths, $code) > 0);
		$isEtch = ( strpos($etching, $code ) > 0);
		
		if($isSheath ) $results .= "<span class='sheath'>";
		if($isEtch ) $results .= "<span class='etch'>";
		$results .= $part['PartCode'];
		//~ $results .= $part['PartCode'] . ",";
		//~ if(++$cnt == $totalAdds)
			//~ $results = substr($results, 0, strlen($results)-1);
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
	$query = "Select * from InvoiceEntryAdditions where EntryID=$invEntryID";
	return getDbRecords($query);
}

function fetchInvoiceEntries($invNum){
	$query = "Select * from InvoiceEntries where Invoice=$invNum";
	return getDbRecords($query);
}
function fetchInvoicePayments($invNum){
	$query = "Select * from Payments where Invoice=$invNum";
	return getDbRecords($query);
}

function fetchPart($partid, $year){
	global $g_partPrices;
	fetchParts($year);
	return($g_partPrices[$year][$partid]);	
}

function fetchCustomer($custID){
	$query = "Select * from Customers where CustomerID=$custID";
	$customer = getBasicSingleDbRecord("Customers","CustomerID",$custID);
	if($customer && $customer['CurrrentAddress']){
		$address = getBasicSingleDbRecord("Address", "AddressID", $customer['CurrrentAddress']);
		$customer['CurrrentAddress'] = $address ;
	}
	return $customer;
}

function fetchParts($year){
	global $g_partPrices;
	if(count($g_partPrices[$year]) < 1){
		$query = "Select Parts.*, PartPrices.Price  from Parts 
			left join PartPrices on PartPrices.PartID = Parts.PartID 
			where PartPrices.Year = $year";
		$parts =  getDbRecords($query);
		foreach($parts as $part){
			$g_partPrices[$year][$part['PartID']] =  $part;
		}
	}
}

//=================================================
//=================================================
function orderSearchQueryForShop($form)
{
	if($form['lastname']==NULL && $form['invoice_num'] == NULL) return "";
	
	$query .= "Select Invoice, date_format(DateOrdered, '%M %e %Y') as dateordered, 
			date_format(DateEstimated, '%M %e %Y') as dateestimated,
			date_format(DateShipped, '%M %e %Y') as dateshipped,  
			Invoices.ShippingInstructions,
			Customers.LastName , Customers.FirstName , Customers.Dealer,
			Invoices.*, Customers.* 
			from Invoices
			left join Customers on Customers.CustomerID = Invoices.CustomerID
			";
	if($form['invoice_num'] > 0){
		$query .= "where Invoice=" . $form['invoice_num'];
		$query .= " ORDER BY Invoice desc ";
	} else{		
//		$query .= "where CONCAT(if(isnull(Customers.LastName),'',Customers.LastName), ' ' ,
//					if(isnull(Customers.FirstName),'',Customers.FirstName)) like '%" . $form['name'] ."%'";
		$query .= "where Customers.LastName like '%" . $form['lastname'] ."%'";
		if($form['sortby'] != NULL){
			global $sortOptions;
			$query .= " ORDER BY " . $sortOptions[$form['sortby']];
		} else{
			$query .= " ORDER BY Customers.LastName, Customers.FirstName, Invoice desc ";
		}
	}
	return $query;
}
function totalInvoicePayments($invNum){
	$query = "Select sum(Payment) from Payments where Invoice=$invNum";
	//~ echo $query;
	return 0+getIntFromDB($query);
}
function invoiceNonTaxableTotal($invNum, $year){
	$query = "select sum(PartPrices.Price) from InvoiceEntries 
		left join InvoiceEntryAdditions on InvoiceEntryAdditions.EntryID = InvoiceEntryID 
		left join Parts on Parts.PartID = InvoiceEntryAdditions.PartID 
		left join PartPrices on PartPrices.PartID = InvoiceEntryAdditions.PartID  
		where Invoice=$invNum and year=$year and taxable=0;";
	//~ echo $query;
	return 0+getIntFromDB($query);
}

function computeInvoiceCosts($invoice){
	$results = array();
	$entries = fetchInvoiceEntries($invoice['Invoice']);
	$year = date("Y", strtotime($invoice['dateestimated']));

	$results['TotalPayments']=totalInvoicePayments($invoice['Invoice'], $year);
	$results['NonTaxable']=invoiceNonTaxableTotal($invoice['Invoice']);

	//~ echo dumpDBRecord($invoice);
	// for each entry
	$results['TotalCost']  = 0;
	foreach($entries as $entry){
		// compute cost of entry (shold be done at 'entry'
		$results['TotalCost'] += 0+$entry['Price'];
	}
	$results['Discount']=$invoice['DiscountPercentage'];
	if($results['Discount'] > 0) $results['Discount']  /= 100;
	$results['Subtotal']= $results['TotalCost']  * (1-$results['Discount']);
	$results['Shipping'] = $invoice['ShippingAmount'];
	$results['TaxRate'] = $invoice['TaxPercentage'];
	if($results['TaxRate'] > 0) $results['TaxRate']  /= 100;
	
	$taxable = $results['Subtotal']  - $results['NonTaxable'] ;
	$results['Taxes']= $taxable * $results['TaxRate'] ;
	$results['Due']= $results['Subtotal'] + $results['Taxes'] + $results['Shipping']  - $results['TotalPayments'];
	unset($results['Discount']);
		
	return $results;
}

function invoiceShipAddress($invoice){
	if( $invoice['ShippingInfo'] != NULL && strlen($invoice['ShippingInfo']) > 0){
		return $invoice['ShippingInfo'];
	}
	// Get customer current address
	$customer = fetchCustomer($invoice['CustomerID']);
	$address = $customer['CurrrentAddress'];
	$results = "";
	$results .= $address['ADDRESS0'];
	if(strlen($address['ADDRESS0']) > 0) $results .= "<BR>";
	$results .= $address['ADDRESS1'];
	if(strlen($address['ADDRESS1']) > 0) $results .= "<BR>";
	$results .= $address['ADDRESS2'];
	if(strlen($address['ADDRESS2']) > 0) $results .= "<BR>";
	$results .= $address['CITY'] . " ";
	$results .= $address['STATE'] . " ";
	$results .= $address['ZIP'] . " ";
	
	return $results;
}
?>