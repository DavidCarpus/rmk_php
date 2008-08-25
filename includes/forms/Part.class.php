<?php
include_once "Base.class.php";
include_once INCLUDE_DIR. "db/Parts.class.php";


class Part extends Base
{
	public $partsClass;
	
	function __construct() {
       $this->partsClass = new Parts();
   }
   
	public function knifeChoices($default=0){
		$formName="CustomerSummary";
		$parts = $this->partsClass->fetchParts(2008);
		$values = array();
		$cnt=0;
		foreach ($parts as $part) {
			$id = $part['PartID'];
			$values[$cnt]['label'] = $part['PartCode'];
			$values[$cnt]['id'] = $id;
			$cnt++;
		}	
		return $this->selection("PartID", $values, "Item", $default);
	}
	
   	public function partPricing(){
   		$parts = $this->partsClass->fetchParts(2008);
   		$results="";
		foreach ($parts as $part) {
			$id = $part['PartID'];
			$price = "$" . number_format($part['Price'] ,2) ;
   			$results .= $part['PartCode'];
//   			$results .= " - ";
//   			$results .= $part['Description'];
   			$results .= " - $price";
   			$results .= "<BR>";
		}	
		return $results;
	}
	
	
	
	
}
?>