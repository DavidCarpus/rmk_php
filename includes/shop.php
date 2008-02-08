<?php
if($_SERVER['HTTP_HOST'] == '192.168.1.101'){
	$db_server = "localhost";
	$db_username="root";
	$db_password="skeet100";
	$db_webDatabase="newrmk";
}

function knifeList(){
	$results = "";
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
where year(dateestimated ) = year(now()) and week(dateestimated) = week(now()) and Knife.BladeItem
order  by Dealer, LastName, Invoices.Invoice, InvoiceEntries.InvoiceEntryID";
	$records = getDbRecords($query);
	$lastInvoice=0;
	$lastInvoiceEntry=0;
	$knifecnt=0;
	$results .= "<TABLE border='1'>";
	$newKnife=false;
	$comment="";
	$lastDealerID=0;
	$featureCnt=0;
	$dealerKnifeCount=0;
	foreach($records as $record){
		$dealerInvoice = $record['Dealer'];
		$newDealer = ($lastDealerID !=  $record['CustomerID']);
		$newInvoice = ($lastInvoice != $record['Invoice']);
		
		$newKnife=false;
		
		if($record['BladeItem']){
			
		if($newInvoice || $lastInvoiceEntry != $record['InvoiceEntryID']){
			$newKnife=true;
			$featureCnt=0;
		}
		if($newInvoice && $dealerInvoice && $newDealer ){
			$results .= "<TR>";
			
			$results .= "<TD colspan=4><B>";
			$results .= $record['LastName'] . "," .  $record['FirstName'];
			$results .= "</B></TD>";
			
			$results .= "</TR>";
			// Dump dealer 'summary'
//			$results .= "<TR>";
//			
//			$results .= "<TD colspan=3><B>";
//			$results .= $record['LastName'] . "," .  $record['FirstName'];
//			$results .= "</B></TD>";
//			$results .= "<TD><B>";
//			$results .= $dealerKnifeCount;
//			$results .= "</B></TD>";
//			
//			$results .= "</TR>";				
//			$dealerKnifeCount += $record['KnifeCode'];	

			$lastDealerID =  $record['CustomerID'];
			$dealerKnifeCount=0;
		}
		
		if($newKnife){
			if($knifecnt > 0){ // Dump the 'constructed' comment and end the row
				$results .= "<TD>" . $comment . "</TD>";
				$results .= "</TR>\n";
			}
			// start new row
			$results .= "<TR>";
			$results .= "<TD>". (($lastInvoice != $record['Invoice'])?$record['Invoice']:"") . "</TD>";
			$results .= "<TD>" . $record['KnifeCode'] . "</TD>";
			$results .= "<TD>" . $record['Quantity'] . "</TD><TD>";
			$knifecnt += $record['Quantity'];
		}

		if($featureCnt > 0)
			$results .= ", ";
			
		$results .= $record['PartCode'];
		
		$lastInvoice = $record['Invoice'];
		$lastInvoiceEntry = $record['InvoiceEntryID'];
		$comment =  $record['Comment'];
		$featureCnt++;
		
//		$results .= "</TR>";
		//		echo dumpDBRecord($record);
		//		$results .= print_r($record,true);
		}
	}
	$results .= "</TABLE>";
	return $results;
}


function TBD(){
	return "To Be Done";
}


?>