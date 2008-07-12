<?php
include_once "db.php";
include_once INCLUDE_DIR. "db/Parts.class.php";

class InvoiceEntries
{
	public $partsClass;
	public $validationError;
	
	public function __construct() {
       $this->partsClass = new Parts();
   }	
	
	public function details( $entryID )
	{
		return getBasicSingleDbRecord("InvoiceEntries", "InvoiceEntryID", $entryID);
	}

	function getEnteredFeatures($formValues){
		$features=array();
		for($i=1; $i<=8; $i++){
			$code = $formValues["Addition_$i"];
			$price = $formValues["Addition_Price_$i"];
			if(strlen($code > 0) || strlen($price) > 0){
				$partPrice = $this->partsClass->currentYearPartPrice($code);
				$features[$partPrice["PartID"]] = array(
									"PartID"=>$partPrice["PartID"], 
									"Discountable"=>$partPrice["Discountable"], 
									"PartCode"=>$formValues["Addition_$i"], 
									"Price"=>$formValues["Addition_Price_$i"],
									"EntryID"=>$formValues["InvoiceEntryID"]
				);			
			}
		}
		return $features;
	}
	
	function validateNew($values){
		$valid = true;
		$this->validationError="";
//		echo debugStatement(dumpDBRecord($values));
		$features = $this->getEnteredFeatures($values);
		// TODO: check entered features
		
		if(count($features) > 0){
			foreach ($features as $feature) {
				$price = $this->partsClass->currentYearPartPrice($feature['PartCode']);
				if($price == NULL) {$this->validationError .= "FeatureList,"; $valid=false;}
//				echo $feature . "<BR>";
//				echo debugStatement(dumpDBRecord($price));
			}
		}
		$price = $this->partsClass->currentYearPartPrice($values['PartDescription']);
		if($price == NULL) {$this->validationError .= "PartDescription,"; $valid=false;}

		if(!is_numeric($values['Quantity'])){$this->validationError .= "Quantity,"; $valid=false;}
		if(!is_numeric($values['BaseRetail'])){$this->validationError .= "BaseRetail,"; $valid=false;}
		if(!is_numeric($values['Invoice'])) {$this->validationError .= "Invoice,"; $valid=false;}
		
		// trim extra comma
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
	}
	
	function save($values){
		$fields = array("Quantity" , "TotalRetail" , "Invoice" , "Comment", "InvoiceEntryID");
		$invoiceEntry = array();
		foreach($fields  as $field){
			if($field == "TotalRetail" && substr($values[$field],0,1) == "$") 
				$values[$field] = substr($values[$field], 1);
				
			$invoiceEntry[$field] = $values[$field];
		}
		$part = $this->partsClass->fetchCurrYearPart($values["PartID"]);
		if($part == NULL){
			$part = $this->partsClass->currentYearPartPrice($values["PartDescription"]);
		}
//		echo debugStatement(dumpDBRecord($part));
		$invoiceEntry["PartDescription"] = $part['PartCode'];
		$invoiceEntry["PartID"] = $part['PartID'];
		$invoiceEntry["Price"] = $values['BaseRetail'];
		$invoiceEntry["TotalRetail"] = $values['BaseRetail'];
		$features = $this->getEnteredFeatures($values);
		
//		echo debugStatement(dumpDBRecords($features));
		foreach ($features as $feature) {
			$invoiceEntry["TotalRetail"] += $feature['Price'];
		}
		$invoiceEntry["TotalRetail"] *= $invoiceEntry["Quantity"];
		
		$invoiceEntry = saveRecord("InvoiceEntries", "InvoiceEntryID", $invoiceEntry);
		echo debugStatement(dumpDBRecord($invoiceEntry));
		$this->updateFeatures($invoiceEntry, $values);
	}
	
	function removeInvoiceItem($entryID, $entries, $invoice){
		$invoiceEntry=NULL;
		
		$invoiceRetail = $invoice['TotalRetail'];
		
		foreach ($entries as $entry) {
			if($entry['InvoiceEntryID'] == $entryID){
				$invoiceEntry = $entry;
				break;
			}	
		}
		if(	$invoiceEntry == NULL){ return "System Error"; }

		// Remove additions for this item from system
//		executeSQL("Delete from InvoiceEntryAdditions where EntryID=" . $invoiceEntry['InvoiceEntryID']);
		foreach ($record['Additions'] as $addition) {
			// remove addition from item
			deleteRecord("InvoiceEntryAdditions", "EntryID", $addition);
			// reduce items total retail
			$invoiceEntry['TotalRetail'] -= $addition['Price'];
			$invoiceEntry['Price'] -= $addition['Price'] * $invoice['DiscountPercentage'];
			$invoice['TotalRetail'] -= $addition['Price'];
		}
		
		// remove $invoiceEntry from database
		$nonItemfields = array("PartCode", "Description" , "Discountable" , "BladeItem" , "Sheath", "Active", "PartType", "SortField", "AskPrice");
		foreach ($nonItemfields as $field) {
			unset($invoiceEntry[$field]);
		}		
		deleteRecord("InvoiceEntries", "InvoiceEntryID", $invoiceEntry);
//		echo debugStatement(dumpDBRecord($record));
		
		// reduce invoice total retial
		$invoice['TotalRetail'] -= $invoiceEntry['TotalRetail'];
		// update invoice to record new pricing
		saveRecord("Invoices", "Invoice", $invoice);
//		echo debugStatement(dumpDBRecord($invoice));
	}
	
	
	function updateFeatures($invoiceEntry, $values){
		$features = $this->getEnteredFeatures($values);

//		echo debugStatement(dumpDBRecords($features));
		
//		get additions for invEntryid
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
//		//	add any additions in list but not in DB
//		echo debugStatement(dumpDBRecords($features));
		foreach ($features as $feature) {
//			$price = $this->partsClass->currentYearPartPrice($feature);
			$record['AdditionID'] = 0;
			$record['EntryID'] = $invoiceEntry['InvoiceEntryID'];
			$record['PartID'] =$feature['PartID'];
			$record['Price'] = $feature['Price'];
			$record['Discounted'] = $feature['Discountable'];
			saveRecord("InvoiceEntryAdditions", "AdditionID", $record);
//			echo debugStatement("REcord:" . dumpDBRecord($record));
		}
//		select IE.Invoice, IE.PartDescription, TotalRetail, IE.Quantity,
//((Select sum(price) from InvoiceEntryAdditions IA where IE.InvoiceEntryID = IA.EntryID)+ IE.Price )*IE.Quantity
			 
	}
	
}

?>