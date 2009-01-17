<?php
include_once "Base.class.php";
include_once INCLUDE_DIR. "db/InvoiceEntries.class.php";
include_once INCLUDE_DIR. "db/Parts.class.php";

class InvoiceEntry extends Base
{
	private $invEntryClass;
	private $partsClass;
	
	function __construct() {
		$this->invEntryClass = new InvoiceEntries();
		$this->partsClass = new Parts(); 
	}
   
	public function details( $entry, $formValues=array() ){
		$formName="InvoiceEntryDetails";

		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='GET'>"  . "\n";
		$results .=  "<legend>$formName</legend>" . "\n";
		$fields = array('PartDescription', 'Quantity', 'TotalRetail', 'Price', 'Comment');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $entry)) $entry[$name] = "";
			if($name=="TotalRetail" || $name=="Price"  ) $entry[$name] = "$" . number_format($entry[$name] ,2);
			
			$ro = ( ($name == "TotalRetail" ) ? "true" : "false");
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $entry[$name],'',array(),$ro) . "\n";
			if($this->isInternetExploder() && ($name=="TotalRetail"))
					$results .=  "</BR>";
		}
		$results .= "</form>";
		
		$results .= "</div><!-- End $formName -- >";
//		$results .= dumpDBRecord($entry);
		return $results;
//		return dumpDBRecord($entry);
		
	}
	
	public function knifeEntryAdditions($additions){
		global $parts;
		$sheaths = "  MA1 MA2 MAB MB MBB MC MC1 MCB MCR MFB 24B NHS FCH WS BLK LHS LS1 LS2 OK DK ";
		$etching = "  ET1  ET2  ETC ETV NPN NPB EN1 EN2 EN3 EN4 EN5 MED  ";
		$results = "";
		$totalAdds=count($additions);
		$cnt=0;
		if($totalAdds == 0) $results .= " ";
		foreach($additions as $addition){
			$code=" ".$addition['PartCode'] . " ";
			$isSheath = ( strpos($sheaths, $code) > 0);
			$isEtch = ( strpos($etching, $code ) > 0);
			
			if($isSheath ) $results .= "<span class='sheath'>";
			if($isEtch ) $results .= "<span class='etch'>";
			$results .= $addition['PartCode'];
	
			if($isSheath || $isEtch )  $results .= "</span>";
			if(++$cnt < $totalAdds)
				$results .= ",";
		}
		return $results;
	}
	
	public function knifeEntryAdditionsList($additions){
		$results = "";
		$totalAdds=count($additions);
		$cnt=0;
		foreach($additions as $addition){
			$code=" ".$addition['PartCode'] . " ";
			$results .= $addition['PartCode'];
	
			if(++$cnt < $totalAdds)
				$results .= ",";
		}
		return $results;
	}
	
	
	function linkToEntryEdit( $entry ){
		$url = "<a href='newInvoiceEntry.php?Invoice=" . $entry['Invoice'];
//		$url .= "&PartID=" . $entry['PartID']; 
//		$url .= "&Quantity=" . $entry['Quantity']; 
//		$url .= "&FeatureList=" . $this->knifeEntryAdditionsList($entry["Additions"]); 
//		$url .= "&Comment=" . urlencode($entry['Comment']); 
//		$url .= "&TotalRetail=" . $entry['TotalRetail']; 
		
		$url .= "&InvoiceEntryID=" . $entry['InvoiceEntryID']; 

		$url .= "&submit=Edit"; 
		$url .= "'>Edit</a>";
//		return $entry["PartDescription"];
		return $url;
	}
	
	function linkToEntryRemove( $entry ){
		$url = "<a href='invoiceEntryRemove.php?InvoiceEntryID=" . $entry['InvoiceEntryID'] ;
		$url .= "&Invoice=" . $entry['Invoice'];
		$url .=  "'>Remove</a>";
//		return $entry["PartDescription"];
		return $url;
	}
	
	function knifeListTable( $entries, $highlightEntryID, $formValues=array() ){
		$formName="InvoiceKnifeList";
		$fields = array("Part", "Quantity" , "TotalRetail" , "FeatureList" , "Comment", "Edit");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $field){
			if($field == "Part")
				$results .= "<span style='font-weight: bold;' class='PartDescription'>Part</span>";
			else if($field == "Edit")
				$results .= "<span style='font-weight: bold;' class='Admin'>Edit</span>";
			else if($field == "TotalRetail")
				$results .= "<span style='font-weight: bold;' class='TotalRetail'>Retail</span>";
			else if($field == "FeatureList")
				$results .= "<span style='font-weight: bold;' class='FeatureList'>Features</span>";
			else
				$results .= "<span style='font-weight: bold;' class='$field'>$field</span>";
		}
		$results .= "\n";
//		$results .= "</BR>";
//		$results .= "<span style='clear: left;display: block;'>";
		$results .= "<span id='knifeListItem'>\n";
		$cnt=1;
		$highlight=false;
		foreach ($entries as $entry){
			$highlight=false;
			if($highlightEntryID > 0 && $highlightEntryID == $entry['InvoiceEntryID'])
				$highlight=true;
			if($highlightEntryID <= 0 && $cnt%2)
				$highlight=true;
				
			if($highlight)
				$results .= "<div class='InvoiceKnifeListHL'>\n";
			foreach($fields  as $field){
				if($field == "TotalRetail"){
					$results .= "<span class='$field'>" . "$" . number_format($entry[$field] ,2) . "</span>\n";
				}elseif($field == "FeatureList"){
					$results .= "<span class='FeatureList'>" . $this->knifeEntryAdditions($entry["Additions"]). "&nbsp;</span>\n";
				}elseif($field == "Part"){
					$results .= "<span class='PartDescription'>" .  $entry["PartDescription"]  . "</span>\n";
				}elseif($field == "Edit"){
					$results .= "<span class='Admin'>" .  $this->linkToEntryEdit( $entry ) . " " .
														$this->linkToEntryRemove($entry).
								"</span>\n";
					
				}else{
					$results .= "<span class='$field'>" . $entry[$field] . "&nbsp;</span>\n";
				}
			}
//			$results .= "</P>";
			if($highlight)
				$results .= "</div>";
			$cnt++;
			$results .= "</BR>\n";
//			$results .= $highlightEntryID . "++".  $entry['InvoiceEntryID'];
		}
		$results .= "</span>";
	
		$results .= "\n</div><!-- End $formName -- >\n";
//		return count($entries) . " Entries";
		return $results;
	}
	
	function invEntryFormMode($formValues){
		if(array_key_exists("ERROR", $formValues) && strlen($formValues['ERROR']) > 0){return "validate";}	
			
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Update"){return "update";}
		if(array_key_exists("InvoiceEntryID", $formValues) && is_numeric($formValues["InvoiceEntryID"])){return "edit";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "New Item"){return "new";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Submit"){return "validate";}
		//		if(array_key_exists("InvoiceEntryID", $formValues)){$mode="edit";}
		return "none";
	}
	

	function invoiceEntryFeaturesFields($formName, $formValues){
		$id = $formValues['InvoiceEntryID'];
		$results = "";
		$currEntry=null;
		foreach ($formValues['entries'] as $entry) {
			if($entry['InvoiceEntryID'] == $id){
				$currEntry=$entry;
			}
		}
		if($formValues['submit'] != 'Edit'){
//			echo "get additions from form fields?";
			$currEntry['Additions'] = array();
			for($i=1; $i<= 8; $i++){
				$partCode = $partPrice = "";
				if(array_key_exists("Addition_$i", $formValues))		$partCode = $formValues["Addition_$i"];
				if(array_key_exists("Addition_Price_$i", $formValues))		$partPrice = $formValues["Addition_Price_$i"];

				$currEntry['Additions'][] = array("PartCode" => $partCode, "Price"=> $partPrice);			
			}
			
		}
		$i=1;
		$results .=  "\n<span class='featureEntry'>\n";
		$js['field']=" onkeyup='return featureFieldEdit(\"form_$formName\", this, event);'";
		foreach ($currEntry['Additions'] as $addition) {
			$results .=  $this->textField("Addition_$i", "Feature $i", false, $addition['PartCode'],  "", $js, false);
			$results .=  $this->textField("Addition_Price_$i", "Price", false, $addition['Price'],  "", array(), false);
			$results .=  "<BR>\n";
			$i++;
		}
		
		for(; $i<= 8; $i++){
			$results .=  $this->textField("Addition_$i", "Feature $i", false, "",  "", $js, false);
			$results .=  $this->textField("Addition_Price_$i", "Price", false, "",  "", array(), false);
			$results .=  "<BR>\n";
		}
		$results .=  "</span>" . "\n";
//		$results .=  $this->textField($name, $name, $err, $formValues[$name],  "", array(), false);
//		$results .= debugStatement(dumpDBRecord($currEntry) . "</BR>--------</BR>" . dumpDBRecords($currEntry['Additions']));
		
		return $results;
	}
	
	function getEntryFromHttpValues($formValues ){
		$entry = array();;
		if(array_key_exists("InvoiceEntryID", $formValues) && $formValues["InvoiceEntryID"] > 0){
			$dbEntry = $this->invEntryClass->details($formValues["InvoiceEntryID"]);
//		echo debugStatement(dumpDBRecord($dbEntry));
			foreach (array("InvoiceEntryID", "Invoice", "PartID", "PartDescription", "Quantity", 
							"TotalRetail" , "Comment") as $field) {
				$entry[$field] = $dbEntry[$field];
			}
			$entry['BaseRetail'] = number_format($dbEntry['Price'],2);
		} else {
			$entry["InvoiceEntryID"]="";
			$entry["Invoice"]=$formValues['Invoice'];
			$entry["PartDescription"]=array_key_exists("InvoiceEntryID", $formValues)?$formValues['PartDescription']:"";
			$partPrice = $this->partsClass->currentYearPartPrice($entry['PartDescription']);
			if($partPrice != NULL)
				$entry['BaseRetail'] = number_format($partPrice['Price'] ,2);
			}
			
		return $entry;
	}
	
	function newInvoiceEntryForm($formValues, Part $partsFormClass ){
		$formName="NewInvoiceEntry";
		
		$mode=$this->invEntryFormMode($formValues);
		
//		if(!array_key_exists("InvoiceEntryID", $formValues)){		$formValues["InvoiceEntryID"]="";		}

		$values = $this->getEntryFromHttpValues($formValues);
//		echo debugStatement(dumpDBRecord($values));
//		return;
				
		$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
		}
				
		$results="";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form id='form_$formName' name='$formName' action='newInvoiceEntry.php' method='GET'>" . "\n" ;
		$results .=  "<input type='hidden' name='Invoice' value='" . $values["Invoice"] . "'>";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $values["InvoiceEntryID"] . "'>";
		
		if($mode=='none'){
			$results .=  "<BR>" . $this->button("submit", "New Item");
			$results .= "</form>";
			$results .= "\n</div><!-- End $formName -- >\n";
			return $results;			
		}
		
		if($mode=='add' || $mode=='edit'){
			$results .=  "<span id='$formName'>" . "\n";
		}

		if(!array_key_exists("Quantity", $values))		$values["Quantity"]="1";
		if($values["Quantity"]=="") $values["Quantity"]="1";
		
		$fields = array("Quantity", "PartDescription" , "BaseRetail",  "FeatureList" , "TotalRetail" , "Comment");
		
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $values))		$values[$name]="";

			$err=(array_key_exists($name, $errors));

			
			$results .= "<span id='span_$name'>";
			if($name == "FeatureList"){
				$results .= $this->invoiceEntryFeaturesFields($formName, $values);
			} else 	if($name == "BaseRetail" || $name == "Quantity"){
				$js['field']=" onkeyup='return updateRetail(\"form_$formName\");'";
				$results .=  $this->textField($name, $name, $err, $values[$name],  "", $js, false);
			} else if($name == "PartDescription"){
				$js['field']="onkeyup='return newPart(\"form_$formName\", this);' onblur='return newPart(\"form_$formName\", this);'";
				$results .=  $this->textField($name, $name, $err, $values[$name],  "", $js, false);
			} else if($name == "TotalRetail"){
				$results .=  $this->textField($name, $name, $err, number_format($values[$name],2),  "", array(), false);
			} else{
				$results .=  $this->textField($name, $name, $err, $values[$name],  "", array(), false);
			}
			$results .= "</span>\n";
			if($name == "TotalRetail" || $name == "FeatureList"){
				$results .= "</BR>\n";		
			}
		}
		$results .= "</span><!-- End $formName -- >\n";
		
		if($mode=='add' || $mode=='new' || $mode=='validate' ){
			$results .=  "<BR>" . $this->button("submit", "Submit");
		}
		if($mode=='edit'){
			$results .=  "<BR>" . $this->button("submit", "Update");
		}
		$results .= "</form>";
		$results .= "\n</div><!-- End $formName -- >\n";

		return $results;
	}
	
	function removeEntryForm($formValues, $entries){
		$formName="RemoveInvoiceEntry";
		$entryID = $formValues['InvoiceEntryID'];
		
		$results="";
		$results .=  "<div id='$formName'>\n";
//		$record=NULL;
//		foreach ($entries as $entry) {
//			if($entry['InvoiceEntryID'] == $entryID){
//				$record = $entry;
//				break;
//			}	
//		}
		$values = $this->getEntryFromHttpValues($formValues);
		foreach ($entries as $entry) {
			if($entry['InvoiceEntryID'] == $entryID){
				$values['FeatureList'] = "";
				foreach ($entry['Additions'] as $addition) {
					$values['FeatureList'] .= $addition['PartCode'] . ",";
				}
				$values['FeatureList'] = substr($values['FeatureList'], 0, strlen($values['FeatureList'])-1);
				break;
			}	
		}
//		echo debugStatement(dumpDBRecord($values));
		if(	$values == NULL){ return "System Error"; }

		$results .=  "<form name='$formName' action='invoiceEntryRemove.php' method='GET'>" . "\n" ;
		
		$fields = array("PartDescription" , "Quantity",  "TotalRetail" , "FeatureList" , "Comment");
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $values))		$values[$name]="";
			$results .= "<span class='$name'>";
			$results .=  $this->textField($name, $name, $err, $values[$name],  $name, array(), false);
			$results .= "</span><BR>\n";
		}
		$results .=  "<input type='hidden' name='Invoice' value='" . $values["Invoice"] . "'>";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $values["InvoiceEntryID"] . "'>";
		$results .=  $this->button("submit", "Remove item from Invoice");
		$results .=  "<BR>";
		$results .= "</form>";
		$results .= "\n</div><!-- End $formName -- >\n";
		
		return $results;
		
	}
}
?>