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
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='get'>"  . "\n";
		$results .=  "<legend>$formName</legend>" . "\n";
		$fields = array('PartDescription', 'Quantity', 'TotalRetail', 'Price', 'Comment');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $entry)) $entry[$name] = "";
			if($name=="TotalRetail" || $name=="Price"  ) $entry[$name] = "$" . number_format($entry[$name] ,2);
			
			$ro = ( ($name == "TotalRetail" ) ? "true" : "false");
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $entry[$name],'',array(),$ro) . "\n";
			if($this->isInternetExploder() && ($name=="TotalRetail"))
					$results .=  "<br />";
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
		
		$url .= "&amp;InvoiceEntryID=" . $entry['InvoiceEntryID']; 

		$url .= "&amp;submit=Edit"; 
		$url .= "'>Edit</a>";
//		return $entry["PartDescription"];
		return $url;
	}
	
	function linkToEntryRemove( $entry ){
		$url = "<a href='invoiceEntryRemove.php?InvoiceEntryID=" . $entry['InvoiceEntryID'] ;
		$url .= "&amp;Invoice=" . $entry['Invoice'];
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
//		$results .= "<br />";
//		$results .= "<span style='clear: left;display: block;'>";
		$results .= "<div id='knifeListItem'>\n";
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
					$results .= "<span class='$field'>" . "$" . number_format($entry[$field] ,2) . "</span>";
				}elseif($field == "FeatureList"){
					$results .= "<span class='FeatureList'>" . $this->knifeEntryAdditions($entry["Additions"]). "&nbsp;</span>";
				}elseif($field == "Part"){
					$results .= "<span class='PartDescription'>" .  $entry["PartDescription"]  . "</span>";
				}elseif($field == "Edit"){
					$results .= "<span class='Admin'>" .  $this->linkToEntryEdit( $entry ) . " " .
														$this->linkToEntryRemove($entry).
								"</span>";
					
				}else{
					$results .= "<span class='$field'>" . $entry[$field] . "&nbsp;</span>";
				}
//				$results .= "\n";
			}
//			$results .= "</P>";
			if($highlight)
				$results .= "</div>";
			$cnt++;
			$results .= "<br />\n";
//			$results .= $highlightEntryID . "++".  $entry['InvoiceEntryID'];
		}
		$results .= "</div>";
	
		$results .= "\n</div><!-- End $formName -->\n";
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

		if(array_key_exists("entries", $formValues)){	
		foreach ($formValues['entries'] as $entry) {
			if($entry['InvoiceEntryID'] == $id){
				$currEntry=$entry;
			}
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
		$updateRetailJS = " onkeyup='return featureFieldEdit(\"form_$formName\", this, event);'";
		$recomputeRetailJS = " onkeyup='return recomputeTotalRetail(document.getElementById(\"form_$formName\"), -1);'";
		
		foreach ($currEntry['Additions'] as $addition) {
			$options['jscript']=array("field"=>$updateRetailJS);
			$results .=  $this->textField("Addition_$i", "Feature $i", $addition['PartCode'],  $options, "", "", "", "");

			$options['jscript']=array("field"=>$recomputeRetailJS);
			$results .=  $this->textField("Addition_Price_$i", "Price", $addition['Price'],  $options, "", "", "", "");
			$results .=  "<br />\n";
			$i++;
		}
		
		for(; $i<= 8; $i++){
			$results .=  $this->textField("Addition_$i", "Feature $i", "",  $options, "", "", "", "");
			$results .=  $this->textField("Addition__Price)$i", "Price", "",  $options, "", "", "", "");
			$results .=  "<br />\n";
		}
		$results .=  "</span>" . "\n";
//		$results .=  $this->textField($name, $name, $err, $formValues[$name],  "", array(), false);
//		$results .= debugStatement(dumpDBRecord($currEntry) . "<br />--------<br />" . dumpDBRecords($currEntry['Additions']));
		
		return $results;
	}
	
	function getEntryFromHttpValues($formValues ){
		$entry = array();;
		if(array_key_exists("InvoiceEntryID", $formValues) && $formValues["InvoiceEntryID"] > 0){
			$dbEntry = $this->invEntryClass->details($formValues["InvoiceEntryID"]);
//			echo debugStatement(__FILE__. ":" .__FUNCTION__ . ":" . dumpDBRecord($dbEntry));
			foreach (array("InvoiceEntryID", "Invoice", "PartID", "PartDescription", "Quantity", 
							"TotalRetail" , "Comment") as $field) {
				$entry[$field] = $dbEntry[$field];
			}
			if($entry["Discount"] == "" && $dbEntry["DiscountPercentage"] != "") $entry["Discount"] = $dbEntry["DiscountPercentage"]; 			
//			echo debugStatement(__FILE__. ":" .__FUNCTION__ . " : " . dumpDBRecord($entry));
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
	
	function invoiceEntryEditForm($formValues, Part $partsFormClass ){
		$formName="InvoiceEntryEdit";
		
		$mode=$this->invEntryFormMode($formValues);
		
//		if(!array_key_exists("InvoiceEntryID", $formValues)){		$formValues["InvoiceEntryID"]="";		}

		$values = $this->getEntryFromHttpValues($formValues);
//		echo debugStatement(__FILE__. ":" .__FUNCTION__ . ":" . dumpDBRecord($formValues));
		if(array_key_exists("entries", $formValues))	$values['entries'] = $formValues['entries']; 
		if(array_key_exists("submit", $formValues))	$values['submit'] = $formValues['submit']; 
		//		return;
				
		$errors = $this->retrieveErrorArray($formValues);
				
		$results="";
		$results .=  "<a name='$formName'></a>\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form id='form_$formName' name='$formName' action='newInvoiceEntry.php' method='get'>" . "\n" ;
		$results .=  "<input type='hidden' name='Invoice' value='" . $values["Invoice"] . "' />";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $values["InvoiceEntryID"] . "' />";
		
		if($mode=='none'){
			$results .=  "<br />" . $this->button("submit", "New Item");
			$results .= "</form>";
			$results .= "\n</div><!-- End $formName -->\n";
			return $results;			
		}
		
//		if($mode=='add' || $mode=='edit'){
//			$results .=  "<div id='$formName'>" . "\n";
//		}

		if(!array_key_exists("Quantity", $values) || $values["Quantity"]=="")
		{
			$values["Quantity"]="1";
			$values["Discount"]=$formValues["DefaultDiscount"];
		}
		if($values["Discount"] == "" && $values["DiscountPercentage"] != "") $values["Discount"] = $values["DiscountPercentage"]; 
				
		$fields = array("Quantity", "PartDescription" , "BaseRetail",  "Discount", "FeatureList" , "InvoiceEntry_TotalRetail" , "Comment");
		
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $values))		$values[$name]="";

			$options=array();
//			$options=array('jscript'=>array("field"=>$JS));
//			$options['readonly']=$readOnly;
			if((array_key_exists($name, $errors))) $options['error']=$err;
			
			$results .= "<span id='span_$name'>";
			if($name == "FeatureList"){
				$results .= $this->invoiceEntryFeaturesFields($formName, $values);
			} else 	if($name == "BaseRetail" || $name == "Quantity"){
				$options['jscript']=array("field"=>" onkeyup='updateRetail(\"form_$formName\")';");
				$results .=  $this->textField($name, $name, $values[$name], $options, "", "", "", "");
			} else if($name == "PartDescription"){
//				$js['field']="onkeyup='newPart(\"form_$formName\", this);' onblur=newPart(\"form_$formName\", this)";
//				$results .=  $this->textField($name, $name, $err, $values[$name],  "", $js, false);
				$options['jscript']=array("field"=>"onkeyup='newPart(\"form_$formName\", this)' onblur='newPart(\"form_$formName\", this)'");
				$results .=  $this->textField($name, $name, $values[$name], $options, "", "", "", "");
//			} else if($name == "TotalRetail"){
//				$results .=  $this->textField($name, $name, $err, number_format($values[$name],2),  "", array(), false);
			} else if($name == "InvoiceEntry_TotalRetail"){
				$results .=  $this->textField($name, "TotalRetail", number_format($values[$name],2), $options, "", "", "", "");
//				$results .=  $this->textField($name, "TotalRetail", $err, number_format($values[$name],2),  "", array(), false);
			} else if($name == "Discount"){
				$results .=  $this->textField($name, $name, $values[$name], $options, "", "", "", "");
//				$results .=  $this->textField($name, $name, $err, $values[$name],  "", array(), false);
			} else{
				$results .=  $this->textField($name, $name, $values[$name], $options, "", "", "", "");
//				$results .=  $this->textField($name, $name, $err, $values[$name],  "", array(), false);
			}
			$results .= "</span>\n";
			if($name == "Discount" || $name == "FeatureList"){
				$results .= "<br />\n";		
			}
		}
//		$results .= "</div><!-- End $formName -->\n";
		
		if($mode=='add' || $mode=='new' || $mode=='validate' ){
			$results .=  "<br />" . $this->button("submit", "Submit");
		}
		if($mode=='edit'){
			$results .=  "<br />" . $this->button("submit", "Update");
		}
		$results .= "</form></div>";
		$results .= "\n</div><!-- End $formName -->\n";

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

		$results .=  "<form id='form_$formName' name='$formName' action='invoiceEntryRemove.php' method='get'>" . "\n" ;
		
		$fields = array("PartDescription" , "Quantity",  "InvoiceEntryRemove_TotalRetail" , "FeatureList" , "Comment");
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $values))		$values[$name]="";
			$results .= "<span class='$name'>";
			if($name == "InvoiceEntryRemove_TotalRetail"){
				$results .=  $this->textField($name, "TotalRetail",  number_format($values["TotalRetail"],2), $options, "", "", "", "");
//				$results .=  $this->textField($name, "TotalRetail", $err, number_format($values["TotalRetail"],2),  "", array(), false);
			}
			else{
				$results .=  $this->textField($name, $name,  $values[$name], $options, "", "", "", "");
//				$results .=  $this->textField($name, $name, $err, $values[$name],  '', array(), false);
			}
			$results .= "</span><br />\n";
		}
		$results .=  "<input type='hidden' name='Invoice' value='" . $values["Invoice"] . "' />";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $values["InvoiceEntryID"] . "' />";
		$results .=  $this->button("submit", "Remove item from Invoice");
		$results .=  "<br />";
		$results .= "</form>";
		$results .= "\n</div><!-- End $formName -->\n";
		
		return $results;
		
	}
}
?>