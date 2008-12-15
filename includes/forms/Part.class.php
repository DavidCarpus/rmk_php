<?php
include_once "Base.class.php";
include_once INCLUDE_DIR. "db/Parts.class.php";


class Part extends Base
{
	public $partsClass;
	
	function __construct() {
       $this->partsClass = new Parts();
   }
   
   public function entryFormMode($formValues)
   {
   		if(array_key_exists("ERROR", $formValues) && strlen($formValues['ERROR']) > 0){return "validate";}	
			
		if(array_key_exists("submit", $formValues) && !is_numeric($formValues["PartID"])){return "validate";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Update"){return "validate";}
				
		if(array_key_exists("Invoice", $formValues) && is_numeric($formValues["PartID"])){return "edit";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Save"){return "save";}

		return "edit";	
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
	
	function partEditLink($part)
	{
		$strikeStart="";
		$strikeEnd="";
		if(array_key_exists('Active', $part) && ($part['Active'] == 0 || $part['Active'] == '0'))
		{ 
			$strikeStart="<STRIKE>";
			$strikeEnd="</STRIKE>";
//			echo debugStatement("$strikeStart:" . dumpDBRecord($part));
		}
//		if($part['PartCode'] == '1-8')
//			echo debugStatement("$strikeStart:" . dumpDBRecord($part));
		 return "$strikeStart<a href='partEdit.php?PartID=" . $part['PartID'] . "'>" . $part['PartCode'] . "</a>$strikeEnd\n";
//		return $part['PartCode'];
	}
	
	function partFromFormValues($formValues)
	{
		$part = $this->partsClass->blank();
		$part = $this->addFormValues($part,$formValues);
		return $part;
	}
		
	
	function addFormValues($part, $formValues)
	{
		$fields = array('PartCode', 'Description', 'Discountable', 'BladeItem', 'Taxable', 
		"PartID", "PartType", 'Active', "Sheath");
		foreach (array('Discountable', 'BladeItem', 'Taxable', 'Active', 'Sheath') as $boolFld){
			if(array_key_exists($boolFld, $formValues) && $formValues[$boolFld] == 'on') 
				$formValues[$boolFld] = 1;
			if(!array_key_exists($boolFld, $formValues)) 
				$formValues[$boolFld] = 0;
		}

		foreach($fields as $name)
		{
			if(array_key_exists($name, $formValues))
			{
				$part[$name] = $formValues[$name];
			}
		}
		$maxYear=date("Y") + 10; // Current year +10
		for($year = 1988; $year<$maxYear; $year++){
			if(array_key_exists($year, $formValues))
			{
				$part['Prices'][$year] = array('Year'=>$year, 'Price'=>$formValues[$year]);
			}
		}
		return $part;
	}
	
   	public function partPricingTable($formValues){
   		$results="";
		$formName="PartList";
		$results = "";
		$results .=  "<div id='$formName'>\n";
   		
		// **** Print header and load part prices into data array  **********
		$data=array();
		$yearColumns=4;
		$results .= "<span style='font-weight: bold;' class='PartCode'>Part</span>";
		for($year = $formValues['Year']; $yearColumns>0; $yearColumns--,$year++){
			$results .= "<span style='font-weight: bold;' class='Price'>$year</span>";
			$parts = $this->partsClass->fetchParts($year);
			foreach ($parts as $part) {
//				$data[$part['PartCode']][$year] = number_format($part['Price']);
				$data[$part['PartCode']][$year] = $part;
			}
		}
		$results .= "<BR>";
		
		// *********** Display part prices from array  ***************
		foreach($data as $key=>$part)
		{
			$results .= "<span class='PartCode'>" . $this->partEditLink($part[$formValues['Year']]) . "</span>";
			$yearColumns=4;
			for($year = $formValues['Year']; $yearColumns>0; $yearColumns--,$year++){
//				$data[$part['PartCode']][$year] = number_format($part['Price']);
//				echo debugStatement(dumpDBRecords($part));
				$results .= "<span class='Price'>" . number_format($part[$year]['Price'],2) . "</span>";
			}
			$results .= "<BR>";
		}
		$results .= "</div>";
		
		return $results;
	}
	
	function partEdit($partWithPrices)
	{
		$formName="PartEdit";
		$results = "";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='partEdit.php' method='POST'>" . "\n" ;
		
		$errors = array();
		if(array_key_exists("ERROR", $partWithPrices) && count($partWithPrices['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $partWithPrices['ERROR']), true);
		}
			
		$fields = array('PartCode', 'Description', 'Discountable', 'BladeItem', 'Taxable', 'Active', "Sheath" );
		foreach($fields as $name)
		{
			$err=(array_key_exists($name, $errors));
			
			$value = (array_key_exists($name, $partWithPrices)? $partWithPrices[$name]: "");
			if($name == 'Discountable')
			{
				$results .= "<BR>";
			}
			if($name == 'Discountable' || $name == 'BladeItem' || $name == 'Taxable' 
			|| $name == 'Active' || $name == 'Sheath')
			{
				if(!is_numeric($value) && $value == "on") $value=1;
				if(!is_numeric($value) && $value == "off") $value=0;
				if(is_numeric($value) && $value == "-1") $value=1;
				$results .=  $name . ":" . checkbox($name, $name, false, $value) . " &nbsp; &nbsp;";
			}
			else {
				$results .=  $this->textField($name, $this->fieldDesc($name), $err, $value) . "\n";
				$results .= "<BR>";
			}
//			$results .= $name . ":" . $value ;
//			$results .= "<BR>";
		}
		$results .= "<BR>";
		$results .= "<BR>";
		
		if (array_key_exists($name, $partWithPrices))
		{
		foreach($partWithPrices['Prices'] as $price){
				$val = number_format($price['Price'],2);
				$results .=  $this->textField($price['Year'], $price['Year'], $err, $val) . "\n";
//				$results .= $part['Year'] . " - : - " . $part['Price'];
				$results .= "<BR>";
			}
		}
		$hiddenFields = array('PartID', 'PartType');
		foreach($hiddenFields as $name)
		{
			if(array_key_exists($name, $partWithPrices)) 
				$results .=  $this->hiddenField($name, $partWithPrices[$name]);		
		}		
		
		$results .=  $this->button("submit", "Save");
		$results .= "</form>";
		
		$results .= "</div>";
		
		
//		$results .=  debugStatement(dumpDBRecord($partWithPrices));
		
		return $results;
	}
	
	
}
?>