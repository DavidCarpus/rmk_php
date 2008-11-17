<?php
include_once "db.php";

class Invoices
{
	public $validationError;
	
	function blank(){
		$currDate = time();
		$span=4*12;
//		$currDate = strtotime("2008/11/09");
		$estDate = strtotime(date("Y-m-d", $currDate)  . " + $span months" ); // Add 'backlog' time
		$days = (5-date("N", $estDate));
		$days = ($days < 0? $days+7:$days);
		$estDate = strtotime(date("Y-m-d", $estDate) . "+ $days days"); // move est date to Fri

		return array('DateOrdered'=>date("Y-m-d", $currDate),
			'DateEstimated'=>date("Y-m-d", $estDate),
			'DateShipped'=>"",
			'TotalRetail'=>0,
			'ShippingAmount'=>0,
			'PONumber'=>"",
			'ShippingInstructions'=>"",
			'KnifeCount'=>0,
		);
	}
	function addFormValues($invoice, $formValues)
	{
		$fields = array('DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 
			'ShippingAmount', "PONumber", "ShippingInstructions", "KnifeCount", 'Invoice');
		foreach($fields as $name)
		{
			if(array_key_exists($name, $formValues))
			{
				$invoice[$name] = $formValues[$name];
			}
		}
		return $invoice;
	}
	
	function validateNew($values)
	{
		$valid = true;
		$this->validationError="";
		
		// strip $ from values
		$values['TotalRetail'] = preg_replace("/\\$/", '', $values['TotalRetail']);
		$values['ShippingAmount'] = preg_replace("/\\$/", '', $values['ShippingAmount']);
		
		if(!is_numeric($values['TotalRetail'])){$this->validationError .= "TotalRetail,"; $valid=false;}
		if(!is_numeric($values['ShippingAmount'])){$this->validationError .= "ShippingAmount,"; $valid=false;}
		
		// trim extra comma
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
	}
	
	function save($invoice)
	{
		unset($invoice["KnifeCount"]);
		if(strlen($invoice["DateShipped"]) <=0) 		unset($invoice["DateShipped"]);
		return saveRecord("Invoices", "Invoice", $invoice);
	}
	
	function details($invNum)
	{
		$query = "Select * from Invoices where Invoice=$invNum";
		$invoice = getSingleDbRecord($query);	
		if(!array_key_exists('entries', $invoice))
			$invoice['entries'] = $this->items($invoice['Invoice']);
		$costs = $this->computeCosts($invoice);
//		debugStatement(dumpDBRecord($costs));
//		$invoice['TotalRetail'] = "$" . number_format($costs['TotalCost'] ,2);
//		$invoice['ShippingAmount'] = "$" . number_format($costs['Shipping'] ,2);

		$invoice['TotalRetail'] = $costs['TotalCost'];
		$invoice['ShippingAmount'] = $costs['Shipping'];
		
		return 	$invoice;
	}
	
	function totalPayments($invNum){
		return 0+getIntFromDB("Select sum(Payment) from Payments where Invoice=$invNum");
	}

	function fetchInvoicePayments($invNum){
		$query = "Select * from Payments where Invoice=$invNum";
		return getDbRecords($query);
	}
	
	function items($invNum){
		$query = "Select IE.*, P.BladeItem, P.PartCode, P.PartType from InvoiceEntries IE ".
				" left join Parts P on P.PartID = IE.PartID ".
//				" left join PartTypes PPT on P.PartType=PPT.PartTypeID ".
				" where Invoice=$invNum order by SortField";
//		echo debugStatement($query);
		return getDbRecords($query);
	}
	
	function computeKnifeCount($entries ){
		$cnt=0;
		foreach($entries as $entry){
			if($entry['PartType'] <> 99)
			{
				$cnt += $entry['Quantity'];
				echo debugStatement(dumpDBRecord($entry) );
			}
		}
		return $cnt;
	}
	function knifeListHelpItems($invNum){
		$query = "select sum(Quantity) as Cnt, P.PartCode from InvoiceEntries IE
					Left join Parts P on P.PartID = IE.PartID
					where Invoice=$invNum
					group by P.PartCode
					order by Cnt DESC";
		return getDbRecords($query);
	}
	
	function updateComment($invNum, $newComment)
	{
		$query = "Select * from Invoices where Invoice=$invNum";
		$invoice = getSingleDbRecord($query);	
		$invoice['Comment'] = $newComment;
		updateField("Invoices", "Invoice", $invoice, "Comment");	
	}
	
	function itemsWithAdditions($invNum){
		$query = "Select IE.*, P.BladeItem, P.PartType from InvoiceEntries IE left join Parts P on P.PartID = IE.PartID  where Invoice=$invNum order by SortField";
//		echo $query;
		$entries = getDbRecords($query);
		foreach ($entries as $key=>$entry){
			$entries[$key]['Additions'] = $this->additions($entry['InvoiceEntryID']);
		}
		
		return $entries;
	}
		
//	function fetchEntryAdditions($invEntryID){
//		$query = "Select * from InvoiceEntryAdditions  IEA left join Parts P on P.PartID = IEA.PartID where EntryID=$invEntryID order by AdditionID";
//		return getDbRecords($query);
//	}

	function additions($entryID){
		$query = "Select IA.*, P.PartCode from InvoiceEntryAdditions IA left join Parts P on P.PartID=IA.PartID where EntryID=$entryID order by AdditionID";
		//		echo "<HR>" . $query . "<HR>";
		return getDbRecords($query);
	}

	function computeCosts($invoice){
		$results = array();

//		echo "<HR>";
//		var_dump($invoice);
//		echo "<HR>";
		
		if(!array_key_exists('entries', $invoice))
			$invoice['entries'] = $this->items($invoice['Invoice']);
		//		$invoice['entries'] = $this->items($invoice['Invoice']);
		$year = 0;
		if(array_key_exists('dateestimated', $invoice))
			$year = date("Y", strtotime($invoice['dateestimated']));
		if(array_key_exists('DateEstimated', $invoice))
			$year = date("Y", strtotime($invoice['DateEstimated']));
		
		$results['TotalPayments']=$this->totalPayments($invoice['Invoice']);
//		$results['NonTaxable']=$this->nonTaxableTotal($invoice['Invoice'], $year);

		$results['Discount']=$invoice['DiscountPercentage'];
		//		if($results['Discount'] > 0)
		$results['Discount']  /= 100;
		$results['TaxRate'] = $invoice['TaxPercentage'];
		if($results['TaxRate'] > 0) $results['TaxRate']  /= 100;
		
		// for each entry
		$results['TotalCost']  = 0;
		$results['NonDiscountable'] = 0;
		$results['Taxes'] = 0;
		foreach($invoice['entries'] as $entry){
			if(!array_key_exists('additions', $entry))
			$entry['additions'] = $this->additions($entry['InvoiceEntryID']);
			// compute cost of entry (shold be done at 'entry'
//			echo debugStatement(dumpDBRecord($entry ));
			$results['TotalCost'] += 0+$entry['TotalRetail'];
			$results['NonDiscountable'] += 0+$entry['NonDiscountable'];
			if($entry['Taxable'])
			{
				$discountedRetail = ($entry['TotalRetail'] - $entry['NonDiscountable']) * (1-$results['Discount'])+$entry['NonDiscountable'];
				
				$results['Taxes'] += $discountedRetail * $results['TaxRate'];
			}
		}
		$results['Subtotal']= ($results['TotalCost'] - $results['NonDiscountable']) * (1-$results['Discount']) + $results['NonDiscountable'];
		$results['Shipping'] = $invoice['ShippingAmount'];
		if(substr($results['Shipping'],0,1) == "$") $results['Shipping'] = substr($results['Shipping'],1);

		$results['Taxes'] += $results['Shipping'] * $results['TaxRate'];
		$results['Due']= $results['Subtotal'] + $results['Taxes'] + $results['Shipping']  - $results['TotalPayments'];
		if($results['Due'] > -0.01 && $results['Due'] < 0.01 )
			$results['Due'] = 0;
			
//		echo debugStatement(dumpDBRecord($results));
			
		unset($results['Discount']);
		unset($results['NonDiscountable']);
		unset($results['NonTaxable']);
		
		
		return $results;
	}

	function billingAddressString($invoice){
		$customers = new Customers();
		$customer = $customers->fetchCustomer($invoice['CustomerID']);
		return $this->addressString($customer['CurrrentAddress']);
	}

	function shipAddressString($invoice){
		$customers = new Customers();
		if( $invoice['ShippingInfo'] != NULL && strlen($invoice['ShippingInfo']) > 0){
			return str_replace("|", "<BR>", $invoice['ShippingInfo']);
		}
		// Get customer current address
		$customer = $customers->fetchCustomer($invoice['CustomerID']);
//		var_dump($customer);
		return $this->addressString($customer['CurrrentAddress']);
	}

	function addressString($address){
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
		$results = str_replace("\|", "<BR>", $results);
		return $results;
	}

	public function getCustomerInvoices($CustomerID, $older=false, $sort="invoice DESC"){
		if($older){
			$query = "Select * from Invoices where CustomerID=$CustomerID";
		} else {
			$years = 5;
			$query = "SELECT * FROM Invoices I where customerid = $CustomerID and dateordered > date_sub(now(), INTERVAL '$years' year)";
		}
		$query .= " order by $sort";

		$invoices = getDbRecords($query);
		foreach ($invoices as $key=>$invoice) {
//			$costs = $this->computeCosts($invoice);
//			$invoice['TotalRetail'] = $costs['TotalCost'];
//			$invoice['1'] = $costs['Due'];
			
			$invoice['Payments'] = $this->totalPayments($invoice['Invoice']); 
			$invoice['Shipping'] = $invoice['ShippingAmount'];
			$invoice['Due']= ($invoice['SubTotal'] + $invoice['ShippingAmount'] )* (1.0 + ($invoice['TaxPercentage']/100.0)) ;
			$invoice['Due'] -= $invoice['Payments'];
			
			$invoices[$key] = $invoice;
		}
		return $invoices;
	}
	
}

?>