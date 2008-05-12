<?php

class Parts
{
	private $partPrices=array();
	
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
				where PartPrices.Year = $year";
			$parts =  getDbRecords($query);
			foreach($parts as $part){
				$this->partPrices[$year][$part['PartID']] =  $part;
			}
//			echo $query;
		}
	}
	
}
?>