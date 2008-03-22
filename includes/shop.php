<?php
if( isLocalAccess()
|| isDebugAccess()
){
	$db_server = "localhost";
	$db_username="root";
	$db_password="skeet100";
	$db_webDatabase="newrmk";
}

$sortOptions = array("Invoice"=>"Customers.LastName, Customers.FirstName, Invoice desc", 
			"Date Ordered"=>"Customers.LastName, Customers.FirstName, UNIX_TIMESTAMP(DateOrdered) desc");

function fieldDesc($field){
	$lookup = array('invoice_num'=>'Invoice Number','lastname'=>'Last Name',
	'firstname'=>'First Name', 'phonenumber'=>'Phone Number', 'dateordered'=>"Date Ordered",
	'dateestimated'=>"Date Estimated", 'lastname'=>"Last Name", 
	);
	return $lookup[$field];
}
function fields(){
	return array('invoice_num', 'lastname', 'firstname', 'phone'
	);
	
}

function knifeList($form){
	$results = "";
	$week = $form['week'];
	if($week == null) $week=0;
	$query = 
	"select Invoices.Invoice, InvoiceEntries.InvoiceEntryID, 
InvoiceEntryAdditions.AdditionID,
Knife.PartCode as KnifeCode, Addition.PartCode, Knife.BladeItem,
InvoiceEntries.Quantity, InvoiceEntries.Comment,
Customers.LastName,Customers.FirstName,Dealer, Invoices.CustomerID
from Invoices 
left join Customers on Customers.CustomerID = Invoices.CustomerID
left join InvoiceEntries on InvoiceEntries.Invoice = Invoices.Invoice
left join InvoiceEntryAdditions on InvoiceEntryAdditions.EntryID = InvoiceEntries.InvoiceEntryID
inner join Parts as Knife on Knife.PartID = InvoiceEntries.PartID
left join Parts as Addition on Addition.PartID = InvoiceEntryAdditions.PartID
where year(dateestimated ) = year(now()) and week(dateestimated) = week(now()) + $week and Knife.BladeItem
order  by Dealer, LastName, Invoices.Invoice, InvoiceEntries.InvoiceEntryID";
	$records = getDbRecords($query);

	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week -1) . "> Previous Week</a>";
	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
	$daysToMon = date("w")+1;
	if($week != 0)
		$results .= "<B>Week of " . date("m/d/Y", strtotime("+ $week week -$daysToMon days")) . "</B>";
	else
		$results .= "<B>Current Week</B>";
	$results .= "&nbsp;&nbsp;&nbsp;&nbsp;";
	$results .= "<a href=" . $_SERVER['PHP_SELF'] . "?week=" . ($week +1) . "> Next Week</a>";
	
	$results .= "<TABLE border='1'>";
	
	
	$lastInvoice=0;
	$lastInvoiceEntry=0;
	$knifecnt=0;
	$comment="";
	$featureCnt=0;
	$totalKnives=0;
	$dealerSummary=array();
	foreach($records as $record){
		$dealerInvoice = $record['Dealer'];
		$newCustomer = ($dealerSummary['CustomerID'] !=  $record['CustomerID']);
		$newInvoice = ($lastInvoice != $record['Invoice']);
		
		$newKnife=($lastInvoiceEntry != $record['InvoiceEntryID']);
		
		if($record['BladeItem']){
			
		if($newInvoice || $newKnife){			$featureCnt=0;		}
		
		if($newInvoice && ($dealerInvoice && $newCustomer ) 
			|| ($newCustomer && !$dealerInvoice && $dealerSummary['KnifeCount']>0)){
			if($knifecnt > 0){ // Dump the 'constructed' comment and end the row
				$results .= "<TD>" . $comment . "</TD>";
				$results .= "</TR>\n";
				$comment="";
			}
				
			// Dump previous dealer 'summary'
			$results .= "<TR>";			
			$results .= "<TD colspan=1><B>";
			$results .= $dealerSummary['KnifeCount'];
			$results .= "</B></TD>";

			$results .= "<TD colspan=3><B>";
			$results .= $dealerSummary['Name'];
			$results .= "</B></TD>";
			$results .= "</TR>\n";
			// reset summary
			$dealerSummary['CustomerID']=$record['CustomerID'];
			$dealerSummary['Name'] = $record['LastName'] . "," .  $record['FirstName'];
			$dealerSummary['KnifeCount']=0;
		}
					
		if($newInvoice && $dealerInvoice && $newCustomer ){
			// display new dealer info
			$results .= "<TR>";			
			$results .= "<TD colspan=5><B>";
			$results .= $dealerSummary['Name'];
			$results .= "</B></TD>";
			$results .= "</TR>\n";
		}
		
		if($newKnife){
			if($knifecnt > 0){ // Dump the 'constructed' comment and end the row
				$results .= "<TD>" . $comment . "</TD>";
				$results .= "</TR>\n";
			}
			// start new row
			$results .= "<TR>";
			$results .= "<TD>". (($lastInvoice != $record['Invoice'])?invoiceDetailLink($record['Invoice']):"") . "</TD>";
			$results .= "<TD>" . $record['KnifeCode'] . "</TD>";
			$results .= "<TD>" . $record['Quantity'] . "</TD><TD>";
			$knifecnt += $record['Quantity'];
			if($dealerInvoice)
				$dealerSummary['KnifeCount'] += $record['Quantity'];
		}

		if($featureCnt > 0)
			$results .= ", ";
			
		$results .= partCodeForKnifeList($record);
//		$results .= $record['PartCode'];
		
		$lastInvoice = $record['Invoice'];
		$lastInvoiceEntry = $record['InvoiceEntryID'];
		$comment =  $record['Comment'];
		$featureCnt++;
		}
	}
	if($knifecnt > 0){ // Dump the 'constructed' comment and end the row
		$results .= "<TD>" . $comment . "</TD>";
		$results .= "</TR>\n";
	}
	$results .= "</TABLE>";
	return $results;
}

function partCodeForKnifeList($record){
	
	if($record['PartCode'] == "MCB"
		|| $record['PartCode'] == "MAB"
		|| $record['PartCode'] == "ET1"
		|| $record['PartCode'] == "ET2"
		) 
		return "<B>" . $record['PartCode'] . "</B>";
		
	return $record['PartCode'];	
}
function invoiceDetailLink($invNum){
	return "<a href=view_invoice.php?invoice_num=$invNum>$invNum</a>";
}

function TBD(){
	return "To Be Done";
}

function orderDisplay(){
	$form = getFormValues();
	$linkedIn=false;
	if($form['submit']==NULL && $form['invoice_num'] != NULL && $form['invoice_num'] > 0) $linkedIn=true;
//	$results .=  dumpDBRecord($form) . "</HR>";
//	return "Test" . ($linkedIn) . $results;
	if($linkedIn){ 
		// Linked in
		// do not display form
	} else{
			$results .= orderSearchForm($form);
	}
	$results .= displaySearchResults($form);
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

function orderSearchForm($request){
	global $sortOptions;
//	$results .=  dumpDBRecord($request) . "</BR>";
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
//	if(isDebugAccess())	return $query;
	$records = getDbRecords($query);
	if(count($records) == 0){
		return "No Matching invoices!";
	}
	if(count($records) == 1){
		$record = $records[0];
		return displayInvoiceDetailsForShop($record);
	} else {
//		if(isDebugAccess())	return $query . "<HR>". displayInvoiceList($records);
		return displayInvoiceList($records);
//		return orderSearchQueryForShop($form);
	}
}

function displayInvoiceDetailsForShop($record){
	$results .=  "<B>".fieldDesc('lastname') . "," . fieldDesc('firstname')."</B> " . $record['LastName'] . "," .  $record['FirstName'];
	if($record['Dealer'])
		$results .=  "<B><I size=+4>Dealer</I></B> ";
	$results .=  "<BR>";
	
	$results .=  "<B>".fieldDesc('phonenumber')."</B> " . $record['PhoneNumber'];
	$results .=  "<BR>";
	$results .=  "<B>".fieldDesc('invoice_num')."</B> " . $record['Invoice'];
	$results .=  "<BR>";
	$results .=  "<B>".fieldDesc('dateordered')."</B> " . $record['dateordered'];
	$results .=  "<BR>";
	$results .=  "<B>".fieldDesc('dateestimated')."</B> " . $record['dateestimated'];
	$results .=  "<BR>";
	$results .=  "<B>Shipping</B> " . $record['ShippingInstructions'];
	$results .=  "<HR>";
	$results .=  invoiceKnifeList($record['Invoice']);
//	$results .=  dumpDBRecord($record);
	return $results;
}
function invoiceKnifeList($invNum)
{
	$query="select InvoiceEntries.InvoiceEntryID, Parts.PartCode, InvoiceEntries.Quantity, InvoiceEntries.Comment
#,333333InvoiceEntries.*, Parts.PartCode
from InvoiceEntries 
left join Parts on Parts.PartID = InvoiceEntries.PartID
where Invoice =  $invNum order by Parts.PartCode";
//	$results .= $query;
	$results .= "<Table border=1>";
	$records = getDbRecords($query);
	foreach($records as $record){
		$results .= "<TR>";
		$results .= "<TD>" . $record['Quantity'] . "</TD>";
		$results .= "<TD>" . $record['PartCode'] . "</TD>";
		$results .= "<TD>" . getKnifeFeatureString($record['InvoiceEntryID']) . "</TD>";
		
		$results .= "<TD>" . $record['Comment'] . "</TD>";
//		$results .=  dumpDBRecord($record);
		$results .= "</TR>";
	}
	$results .= "</Table>";
	return $results;
}

function getKnifeFeatureString($invEntryID)
{
		$query="select Parts.PartCode
from InvoiceEntryAdditions
left join Parts on Parts.PartID = InvoiceEntryAdditions.PartID
where EntryID =  $invEntryID order by Parts.PartCode";
//	$results .= $query;
	$records = getDbRecords($query);
	foreach($records as $record){
		$results .=  $record['PartCode'] . ", ";
	}
	return $results;
}

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
?>