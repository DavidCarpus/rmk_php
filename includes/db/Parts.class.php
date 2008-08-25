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