<?php
include_once "Base.class.php";

class InvoiceEntry extends Base
{
	function __construct() {   }
   
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
		$url .= "&PartID=" . $entry['PartID']; 
		$url .= "&Quantity=" . $entry['Quantity']; 
		$url .= "&FeatureList=" . $this->knifeEntryAdditionsList($entry["Additions"]); 
		$url .= "&Comment=" . $entry['Comment']; 
		$url .= "&TotalRetail=" . $entry['TotalRetail']; 
		
		$url .= "&InvoiceEntryID=" . $entry['InvoiceEntryID']; 

		$url .= "&submit=New+Item"; 
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
	
	function knifeListTable( $entries, $formValues=array() ){
		$formName="InvoiceKnifeList";
		$fields = array("Part", "Quantity" , "TotalRetail" , "FeatureList" , "Comment", "Edit");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $field){
			if($field == "Part")
				$results .= "<span style='font-weight: bold;' class='PartDescription'>Part</span>";
			else if($field == "Edit")
				$results .= "<span style='font-weight: bold;' class='Admin'>Edit</span>";
			else
				$results .= "<span style='font-weight: bold;' class='$field'>$field</span>";
		}
		$results .= "\n";
//		$results .= "</BR>";
//		$results .= "<span style='clear: left;display: block;'>";
		$results .= "<span id='knifeListItem'>\n";
		$cnt=1;
		foreach ($entries as $entry){
			if($cnt%2)
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
			if($cnt%2)
				$results .= "</div>";
			$cnt++;
			$results .= "</BR>\n";
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
	
	function newInvoiceEntryForm($formValues, Part $partsFormClass ){
		$formName="NewInvoiceEntry";
		
		$mode=$this->invEntryFormMode($formValues);
		
		if(!array_key_exists("InvoiceEntryID", $formValues)) $formValues["InvoiceEntryID"]="";

		$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
		}
				
		$results="";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='newInvoiceEntry.php' method='GET'>" . "\n" ;
		
		if($mode=='none'){
			$results .=  "<input type='hidden' name='Invoice' value='" . $formValues["Invoice"] . "'>";
			$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $formValues["InvoiceEntryID"] . "'>";
			$results .=  "<BR>" . $this->button("submit", "New Item");
			$results .= "</form>";
			$results .= "\n</div><!-- End $formName -- >\n";
			return $results;			
		}
		
		if($mode=='add' || $mode=='edit'){
			$results .=  "<span id='$formName'>" . "\n";
		}

		$results .=  "<BR>";
		$fields = array("PartID" , "Quantity", "TotalRetail" , "FeatureList" , "Comment");
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $formValues))		$formValues[$name]="";

//			$JS['field'] = "onBlur=\"invoiceNumber($formName);\"";
//			$results .=  $this->textField('invoice_num', $this->fieldDesc('Invoice'), false, $invoice['Invoice'],"",$JS) ;
			
			$results .= "<span class='$name'>";
			if($name == "PartID"){
				$results .= $partsFormClass->knifeChoices($formValues[$name]);
			}elseif($name == "TotalRetail"){
				$results .=  $this->textField($name, $name, false, $formValues[$name],  $name, array(), false);
			} else{
				$err=(array_key_exists($name, $errors));
//				textField($name, $label, $required=false, $value='', $class='', $jscriptArray=array(), $readonly=false)
				$results .=  $this->textField($name, $name, $err, $formValues[$name],  $name, array(), false);
			}
			$results .= "</span><BR>\n";
		}
		$results .=  "<input type='hidden' name='Invoice' value='" . $formValues["Invoice"] . "'>";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $formValues["InvoiceEntryID"] . "'>";
		
		$results .= "</span><!-- End $formName -- >\n";
		
		if($mode=='add' || $mode=='new' || $mode=='validate' ){
			$results .=  "<BR>" . $this->button("submit", "Submit");
		}
		if($mode=='edit'){
			$results .=  "<BR>" . $this->button("submit", "Update");
		}
		$results .= "</form>";
		$results .= "\n</div><!-- End $formName -- >\n";
//		$results .= $partsFormClass->knifeChoices();
//		$results .= dumpDBRecord($formValues);
		
		return $results;
	}
	
	function removeEntryForm($entryID, $entries){
		$formName="RemoveInvoiceEntry";
		
		$results="";
		$results .=  "<div id='$formName'>\n";
		$record=NULL;
		foreach ($entries as $entry) {
			if($entry['InvoiceEntryID'] == $entryID){
				$record = $entry;
				break;
			}	
		}
		if(	$record == NULL){ return "System Error"; }

		$results .=  "<form name='$formName' action='invoiceEntryRemove.php' method='GET'>" . "\n" ;
		
		$fields = array("PartDescription" , "Description", "TotalRetail" , "FeatureList" , "Comment");
		foreach($fields as $name)
		{
			if(!array_key_exists($name, $formValues))		$formValues[$name]="";
			$results .= "<span class='$name'>";
			$results .=  $this->textField($name, $name, $err, $record[$name],  $name, array(), false);
			$results .= "</span><BR>\n";
		}
		$results .=  "<input type='hidden' name='Invoice' value='" . $record["Invoice"] . "'>";
		$results .=  "<input type='hidden' name='InvoiceEntryID' value='" . $record["InvoiceEntryID"] . "'>";
		$results .=  $this->button("submit", "Remove item from Invoice");
		$results .=  "<BR>";
		$results .= "</form>";
		$results .= "\n</div><!-- End $formName -- >\n";
		
		return $results;
		
	}
}
?>