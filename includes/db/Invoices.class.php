<?php
class Invoices
{
	function totalPayments($invNum){
		return 0+getIntFromDB("Select sum(Payment) from Payments where Invoice=$invNum");
	}

//	function nonTaxableTotal($invNum, $year){
//		$query = "select sum(PartPrices.Price) from InvoiceEntries
//		left join InvoiceEntryAdditions on InvoiceEntryAdditions.EntryID = InvoiceEntryID
//		left join Parts on Parts.PartID = InvoiceEntryAdditions.PartID
//		left join PartPrices on PartPrices.PartID = InvoiceEntryAdditions.PartID
//		where Invoice=$invNum and year=$year and taxable=0;";
//		return 0+getIntFromDB($query);
//	}

	function items($invNum){
		$query = "Select * from InvoiceEntries IE left join Parts P on P.PartID = IE.PartID  where Invoice=$invNum order by SortField";
		
		return getDbRecords($query);
	}

	function additions($entryID){
		$query = "Select * from InvoiceEntryAdditions where EntryID=$entryID order by AdditionID";
		//		echo "<HR>" . $query . "<HR>";
		return getDbRecords($query);
	}

	function computeCosts($invoice){
		$results = array();

//		echo "<HR>";
//		var_dump($invoice);
//		echo "<HR>";
		
		if(!array_key_exists('entries', $invoice))
		$Invoice['entries'] = $Invoices->items($Invoice['Invoice']);
		//		$invoice['entries'] = $this->items($invoice['Invoice']);
		$year = date("Y", strtotime($invoice['dateestimated']));

		$results['TotalPayments']=$this->totalPayments($invoice['Invoice']);
//		$results['NonTaxable']=$this->nonTaxableTotal($invoice['Invoice'], $year);

		$results['Discount']=$invoice['DiscountPercentage'];
		//		if($results['Discount'] > 0)
		$results['Discount']  /= 100;
		$results['TaxRate'] = $invoice['TaxPercentage'];
		if($results['TaxRate'] > 0) $results['TaxRate']  /= 100;
		
		// for each entry
		$results['TotalCost']  = 0;
		foreach($invoice['entries'] as $entry){
			if(!array_key_exists('additions', $entry))
			$entry['additions'] = $this->additions($entry['InvoiceEntryID']);
			// compute cost of entry (shold be done at 'entry'
//			echo dumpDBRecord($entry );
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

		$results['Due']= $results['Subtotal'] + $results['Taxes'] + $results['Shipping']  - $results['TotalPayments'];
		if($results['Due'] < 0.01)
			$results['Due'] = 0;
			
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

}

?>