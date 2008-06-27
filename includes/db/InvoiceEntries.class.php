<?php
include_once "db.php";
include_once INCLUDE_DIR. "db/Parts.class.php";

class InvoiceEntries
{
	public $partsClass;
	public $validationError;
	
	function __construct() {
       $this->partsClass = new Parts();
   }	
	
	function details( $entryID )
	{
		return getBasicSingleDbRecord("InvoiceEntries", "InvoiceEntryID", $entryID);
	}

	function validateNew($values){
		$valid = true;
		$this->validationError="";
//		echo debugStatement(dumpDBRecord($values));
		$featureList = $values['FeatureList'];
		if(strlen($featureList) > 0){
			$featureList = str_replace(" ", ",",$featureList);
			$featureList = str_replace("-", ",",$featureList);
			$features = split(",", $featureList);
			
			foreach ($features as $feature) {
				$price = $this->partsClass->currentYearPartPrice($feature);
				if($price == NULL) {$this->validationError .= "FeatureList,"; $valid=false;}
	//			echo $feature . "<BR>";
	//			echo debugStatement(dumpDBRecord($price));
			}
		}
		if(!is_numeric($values['Quantity'])){$this->validationError .= "Quantity,"; $valid=false;}
		if(!is_numeric($values['Invoice'])) {$this->validationError .= "Invoice,"; $valid=false;}
		
		// trim extra comma
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
	}
	
	function save($values){
		$fields = array("PartID", "Quantity" , "TotalRetail" , "Invoice" , "Comment", "InvoiceEntryID");
		$invoiceEntry = array();
		foreach($fields  as $field){
			if($field == "TotalRetail" && substr($values[$field],0,1) == "$") 
				$values[$field] = substr($values[$field], 1);
				
			$invoiceEntry[$field] = $values[$field];
		}
		$part = $this->partsClass->fetchCurrYearPart($invoiceEntry["PartID"]);
		$invoiceEntry["PartDescription"] = $part['PartCode'];
//		echo debugStatement(dumpDBRecord($invoiceEntry));
		$invoiceEntry = saveRecord("InvoiceEntries", "InvoiceEntryID", $invoiceEntry);
		$this->updateFeatures($invoiceEntry, $values['FeatureList']);
	}
	
	function removeInvoiceItem($entryID, $entries, $invoice){
		$record=NULL;
		
		$invoiceRetail = $invoice['TotalRetail'];
		
		foreach ($entries as $entry) {
			if($entry['InvoiceEntryID'] == $entryID){
				$record = $entry;
				break;
			}	
		}
		if(	$record == NULL){ return "System Error"; }

		// Remove additions for this item from system
		foreach ($record['Additions'] as $addition) {
			// remove addition from item
			deleteRecord("InvoiceEntryAdditions", "EntryID", $addition);
			// reduce items total retail
			$record['TotalRetail'] -= $addition['Price'];
			$record['Price'] -= $addition['Price'] * $invoice['DiscountPercentage'];
			$invoice['TotalRetail'] -= $addition['Price'];
			
//			echo debugStatement(dumpDBRecord($addition));
		}
		
		// remove item from database
		$nonItemfields = array("PartCode", "Description" , "Discountable" , "BladeItem" , "Sheath", "Active", "PartType", "SortField", "AskPrice");
		foreach ($nonItemfields as $field) {
			unset($record[$field]);
		}		
		deleteRecord("InvoiceEntries", "InvoiceEntryID", $record);
//		echo debugStatement(dumpDBRecord($record));
		
		// reduce invoice total retial
		$invoice['TotalRetail'] -= $record['TotalRetail'];
		// update invoice to record new pricing
		saveRecord("Invoices", "Invoice", $invoice);
//		echo debugStatement(dumpDBRecord($invoice));
	}
	
	
	function updateFeatures($invoiceEntry, $featureList){
		//Split featurelist into array
		$featureList = str_replace(" ", ",",$featureList);
		$featureList = str_replace("-", ",",$featureList);
		$features=array_fill_keys(explode(",", $featureList), 0);
		
		//Get ID for each partcode
		foreach ($features as $feature=>$value) {
			$price = $this->partsClass->currentYearPartPrice($feature);
			$features[$feature] = $price['PartID'];
		}
		//get additions for invEntryid
		$query = "Select * from InvoiceEntryAdditions where EntryID=".$invoiceEntry['InvoiceEntryID'];
		$currentAdditions = getDbRecords($query);

		//	delete from db any additions removed
//		echo debugStatement(dumpDBRecords($currentAdditions));
		foreach ($currentAdditions as $currentAddition) {
			if(!in_array($currentAddition['PartID'],$features)){
				deleteRecord("InvoiceEntryAdditions", "AdditionID", $currentAddition);
			} else { // addition was in the DB, remove from 'features' array, remainder will need to be added
				unset($features[array_search($currentAddition['PartID'],$features)]);			
			}
		}
		//	add any additions in list but not in DB
//		var_dump($features);
		foreach ($features as $feature=>$partID) {
			$price = $this->partsClass->currentYearPartPrice($feature);
			if(is_null($price['PartID']) && $price['PartID'] > 0){
				$record['AdditionID'] = 0;
				$record['EntryID'] = $invoiceEntry['InvoiceEntryID'];
				$record['PartID'] =$price['PartID'];
				$record['Price'] = $price['Price'];
				$record['Discounted'] = $price['Discountable'];
				saveRecord("InvoiceEntryAdditions", "AdditionID", $record);
			}
//			echo debugStatement(dumpDBRecord($price));
//			echo debugStatement(dumpDBRecord($record));
		}
		
			 
	}
	
}

?>