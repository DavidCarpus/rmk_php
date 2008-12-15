<?php
include_once "db.php";

class Parts
{
   	private $partPrices;
	private $currentlyAvailibleParts;
	
	function __construct() {
       $this->partPrices = array();
       $this->currentlyAvailibleParts = array();
	}	
	
	function fetchCurrYearPart($partID){
		$year=date("Y");
		return $this->fetchPart($partID, $year);		
	}
	
	function fetchAllPart($partid)
	{
		$part =  getBasicSingleDbRecord("Parts", "PartID", $partid);
		
		$query = "Select PartPriceID, Year, Price from PartPrices 
			where PartID = $partid " . 
			" order by Year";
		$prices = getDbRecords($query);
		foreach ($prices as $price){
			$part['Prices'][$price['Year']] =  $price;
		}
		return $part;
	}
	
	function fetchPart($partid, $year){
		$this->fetchParts($year);
		while( !array_key_exists($year, $this->partPrices)  && $year < 2020 ){
			$year++;
		}
		if(array_key_exists($year, $this->partPrices) && array_key_exists($partid, $this->partPrices[$year]) )
			return($this->partPrices[$year][$partid]);	
		else 
			return NULL;
	}
	
	function fetchParts($year){
		if( !array_key_exists($year, $this->partPrices) || count($this->partPrices[$year]) < 1){
			$query = "Select Parts.*, PartPrices.Price  from Parts 
				left join PartPrices on PartPrices.PartID = Parts.PartID 
				where PartPrices.Year = $year
				order by SortField";
			$parts =  getDbRecords($query);
			foreach($parts as $part){
				$this->partPrices[$year][$part['PartID']] =  $part;
			}
//			echo $query;
		}
//		echo "**" . dumpDBRecords($this->partPrices[$year]);
		return $this->partPrices[$year];
	}
	
	function blank(){
		return array('PartCode'=>"",
			'Description'=>"",
			'Discountable'=>0,
			'BladeItem'=>0,
			'Taxable'=>0,
			'Sheath'=>0,
			'Active'=>0,
			'PartType'=>0,
			'SortField'=>"",
			'AskPrice'=>0,
		);
	}
	
	function save($part)
	{
		$prices = $part['Prices'];
		unset($part['Prices']);
		$part = saveRecord("Parts", "PartID", $part);
		
		foreach ($prices as $price)
		{
			$query="Update PartPrices set Price=" . $price['Price'] . 
				" where Year=". $price['Year'] . " and PartID=" . $part['PartID'];
			executeSQL($query);
		}	
		$part['Prices'] = $prices;
		
		return $part;
	}
	
	function currentYearPartPrice($partCode){
		$year=date("Y");
//		echo debugStatement("Fetch price for $partCode");
		if( count($this->currentlyAvailibleParts) < 1){
			$query = "Select Parts.*, PartPrices.Price  from Parts 
				left join PartPrices on PartPrices.PartID = Parts.PartID 
				where PartPrices.Year = $year";
//			echo $query;
			$parts =  getDbRecords($query);
//			echo debugStatement(dumpDBRecords($parts));
			$currPart = NULL;
			foreach($parts as $part){
				$this->currentlyAvailibleParts[$part['PartCode']] =  $part;
				if($part['PartCode'] == $partCode) $currPart=$part;
			}
			return $currPart;
		}
		
		if(!array_key_exists($partCode, $this->currentlyAvailibleParts)){
//			echo debugStatement("Missing Price for partCode1 : $partCode");
			return NULL;
		}

//		echo debugStatement("Retrieve partCode Price : $partCode");
		return $this->currentlyAvailibleParts[$partCode];
	}
}
?>