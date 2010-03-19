<?php
include_once "Base.class.php";
include_once INCLUDE_DIR. "db/Parts.class.php";


class Part extends Base
{
	public $partsClass;
	public $validationError;
	
	function __construct() {
       $this->partsClass = new Parts();
       $this->validationError="";
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
   public function pricingEntryFormMode($formValues){
   		if(array_key_exists("ERROR", $formValues) && strlen($formValues['ERROR']) > 0){return "validate";}	
		if(array_key_exists("Year", $formValues)){return "entry";}
   		
		if(array_key_exists("submit", $formValues)){return "validate";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Save"){return "save";}
		
		return "validate";	
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
			$yearPriceField="price_" . $year;
			if(array_key_exists($yearPriceField, $formValues))
			{
				$part['Prices'][$year] = array('Year'=>$year, 'Price'=>$formValues[$yearPriceField]);
			}
		}
		return $part;
	}
	
   	public function newPricingTable($formValues){
		$results = "";
		$results .=  "<div id='PartList'>\n";
		
   		$errors = $this->retrieveErrorArray($formValues);
		
		$formName="PartList";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>" . "\n" ;
		
		$yearColumns=3;
		$minYear=$formValues['Year']-$yearColumns;
		$data=$this->getPartPricingArray($minYear, $yearColumns);
		$results .= "<span style='font-weight: bold;' class='PartCode'>Part</span>";
		for ($year= $formValues['Year']-$yearColumns; $yearColumns>0; $yearColumns--,$year++) {
			$results .= "<span style='font-weight: bold;' class='Price'>$year</span>";;
		}
		$results .=  $this->button("submit", $formValues['Year']);
		$results .= "<br />";
		
   			// *********** Display part prices from array  ***************
		foreach($data as $key=>$part)
		{
//			echo debugStatement(print_r($part,true));
			$firstYearPart = $part[$minYear];
			$results .= "<span class='PartCode'>" . $this->partEditLink($firstYearPart) . "</span>";
			$yearColumns=3;
			for($year = $minYear; $yearColumns>0; $yearColumns--,$year++){
				$results .= "<span class='Price'>" . number_format($part[$year]['Price'],2) . "</span>";
//				$results .= "<span class='Price'>$year</span>";
			}
			$fieldName = $firstYearPart['PartID'] . "_" . $formValues['Year'];
			
			
			$adjustment=$part[$year-1]['Price'] - $part[$year-2]['Price'];
			
			if(array_key_exists($fieldName, $formValues)){
				$newValue = $formValues[$fieldName];
				// if error, highlight adjustment
				if(array_key_exists($fieldName, $errors))				
					$adjustment = "<span style='color:#FF0000;'> ERROR</span> ";
			} else{			
				$newValue = $part[$year-1]['Price'] + $adjustment;
				$newValue = number_format($newValue,2);
			}
			$field =  $this->textField($fieldName, "",  $newValue, $options, "", "", "", "");
//			$field =  $this->textField($fieldName, "", false, $newValue) . "\n";
			
			$results .= "<span class='Price'>$field</span>  $adjustment";
//			$results .= "<span class='Price'>" . $this->partOptionFlags($firstYearPart)."</span>";
			$results .= "<br />";
		}
		
//		$results .=  $formName;
		$results .= "</form>";
		$results .= "</div>";
		
		return $results;
   	}
   	
   	function extractNewPricingFromSubmission($formValues){
   		$results = array();
   		foreach ($formValues as $key=>$value) {
   			if(strpos($key, "_") > 0){
   				$tmp=split("_", $key);
   				$part['PartID'] = $tmp[0];
   				$part['Year'] = $tmp[1];
   				$part['Price'] = $value;
   				$results[$part['PartID']] = $part;
   			}
   		}
   		return $results;
   	}
   	
   	function validateNewPricing($partPrices){
   		$valid=true;
   		$this->validationError ="";
   		foreach ($partPrices as $part) {
   			if( ! is_numeric($part['Price'])) {
   				$fieldName = $part['PartID'] . "_" . $part['Year'];
   				$this->validationError .= $fieldName . ","; 
   				$valid = false;
   			}
//   			echo (dumpDBRecord($part)). "<br />";
   		}
   		// trim extra comma
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
   		return $valid;
   	}
   	
   	function getPartPricingArray($minYear, $numYears=4){
   		$results=array();
		$yearColumns=$numYears;
//		echo $minYear;
		for($year = $minYear; $yearColumns>0; $yearColumns--,$year++){
			$parts = $this->partsClass->fetchParts($year);
			foreach ($parts as $part) {
//				$data[$part['PartCode']][$year] = number_format($part['Price']);
				$results[$part['PartCode']][$year] = $part;
			}
		}
		return $results;   		
   	}
	
   	public function partOptionFlags($part){
		$results = "";
   		$results .= ($part['Discountable']? "D": "&nbsp;&nbsp;");
		$results .= ($part['BladeItem']? "B": "&nbsp;&nbsp;");
		$results .= ($part['Taxable']? "T": "&nbsp;&nbsp;");
		$results .= ($part['Sheath']? "S": "&nbsp;&nbsp;");
		$results .= ($part['Active']? "&nbsp;&nbsp;":"<B>X</B>");
		return $results;   		
   	}
   	
   	public function partPricingTable($formValues){
		$formName="PartList";
		$results = "";
		$results .=  "<div id='$formName'>\n";
   		
		// **** Print header and load part prices into data array  **********
		$yearColumns=4;
		$data=$this->getPartPricingArray($formValues['Year'], $yearColumns);
		$results .= "<span style='font-weight: bold;' class='PartCode'>Part</span>";
		for ($year= $formValues['Year']; $yearColumns>0; $yearColumns--,$year++) {
			$results .= "<span style='font-weight: bold;' class='Price'>$year</span>";;
		}
		$results .= "<br />\n";
		
		// *********** Display part prices from array  ***************
		foreach($data as $key=>$part)
		{
			$firstYearPart = $part[$formValues['Year']];
			$results .= "<span class='PartCode'>" . $this->partEditLink($firstYearPart) . "</span>";
			$yearColumns=4;
			for($year = $formValues['Year']; $yearColumns>0; $yearColumns--,$year++){
				$results .= "<span class='Price'>" . number_format($part[$year]['Price'],2) . "</span>";
			}
			$results .= "<span class='Price'>" . $this->partOptionFlags($firstYearPart)."</span>";
			$results .= "<br />\n";
		}
		$results .= "</div>";
		$year = $this->partsClass->maxPartPriceYear()+1;
		
		
		$results = "<a href='AddPricingForYear.php?Year=$year'>Add Pricing for $year</a>" . $results;
		
		return $results;
	}
	
	function partEdit($partWithPrices)
	{
		$formName="PartEditForm";
		$results = "";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='partEdit.php' method='post'>" . "\n" ;
		
		$errors = $this->retrieveErrorArray($partWithPrices);
			
		$fields = array('PartCode', 'Description' );
		foreach($fields as $name)
		{
			$options=array();
			if((array_key_exists($name, $errors))) $options['error']=1;
			$value = (array_key_exists($name, $partWithPrices)? $partWithPrices[$name]: "");
			
			$results .=  $this->textField($name, $this->fieldDesc($name),  $value, $options, "", "", "", "");
			
		}
		
		$results .=  "<div class='partFlags'>\n";
		$fields = array( 'Discountable', 'BladeItem', 'Taxable', 'Active', "Sheath" );
		foreach($fields as $name)
		{
			$options=array();
			if((array_key_exists($name, $errors))) $options['error']=1;			
			
			$value = (array_key_exists($name, $partWithPrices)? $partWithPrices[$name]: "");
		
			if($name == 'Discountable' || $name == 'BladeItem' || $name == 'Taxable' 
			|| $name == 'Active' || $name == 'Sheath')
			{
				if(!is_numeric($value) && $value == "on") $value=1;
				if(!is_numeric($value) && $value == "off") $value=0;
				if(is_numeric($value) && $value == "-1") $value=1;
				
				$results .=  $this->checkbox($name, $name, $value, "","","","","");
			}
		}
		$results .= "</div>";
		
		$results .=  "<div class='partEditPrices'>\n";
		$options['class'] = "partEditPrice";
		if (array_key_exists($name, $partWithPrices))
		{
		foreach($partWithPrices['Prices'] as $price){
				$val = number_format($price['Price'],2);
				$results .=  $this->textField("price_".$price['Year'], $price['Year'], $val, $options, "", "", "", "");
//				$results .=  $this->textField("price_".$price['Year'], $price['Year'], $err, $val) . "\n";
//				$results .= $part['Year'] . " - : - " . $part['Price'];
//				$results .= "<br />";
			}
		}
		$results .= "</div>";
		
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