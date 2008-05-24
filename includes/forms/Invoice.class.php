<?php
include_once "Base.class.php";

class Invoice extends Base
{
	function __construct() {
//       print "In constructor\n";
       $this->name = "MyDestructableClass";
   }
   
	public function invNum($request){
		$formName="InvoiceNum";
		if(!array_key_exists('invoice_num', $request)) $request['invoice_num'] = "";
		$results="";
//		$results .=  "<legend>$formName</legend>";
		$results .=  "<div id='$formName'>";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
		$JS['field'] = "onBlur=\"invoiceNumber($formName);\"";
		$results .=  $this->textField('invoice_num', $this->fieldDesc('invoice_num'), false, $request['invoice_num'],"",$JS) ;
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >\n";
		return $results;
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

	public function details($invoice){
		$formName="InvoiceDetails";
		if(!array_key_exists('invoice_num', $invoice)) $invoice['invoice_num'] = "";
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>" . "\n" ;
		$results .=  "<legend>$formName</legend>" . "\n";
		$fields = array('DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 'ShippingAmount', "PONumber", "ShippingInstructions", "KnifeCount");
		foreach($fields as $name)
		{
			if( strncmp($name, "Date", 4) == 0 && strlen($invoice[$name]) > 9) // Trim off timestamp
			{
				$invoice[$name] = substr($invoice[$name], 0, 10);
			}
			if(!array_key_exists($name, $invoice)) $invoice[$name] = "";

//			$results .=  "\n";
//			$JS['label']="onmouseover='showHint(this.htmlFor);'";
			$JS['label']=$this->helpTextJS("ajax-tooltip.html");
			
//			if( $name=="KnifeCount" ) $JS['field'] = " disabled='true' " . $this->helpTextJS($formName, $name, "Test Help text<BR>Line2");
			if( $name=="KnifeCount" ) $JS['field'] = $this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']);
			if( $name=="KnifeCount" ) $JS['label'] = $this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']);
			
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $invoice[$name],'',$JS) . "\n";
			if($this->isInternetExploder() && ( $name=="DateShipped"  || $name=="PONumber" ) )
					$results .=  "</BR>";
		}
		
		
		//		PO#, TotalRetail, Shipping$, ShippingInfo, ShippingLocation, discount
//		totalPayments, totalknives		
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >\n";
		
		return $results;
	}
	
	function knifeListTable( $entries ){
		$formName="InvoiceKnifeList";
		$fields = array("Part", "Quantity" , "TotalRetail" , "FeatureList" , "Comment");
		$results = "";
		$results .=  "<div id='$formName'>\n";
		foreach($fields  as $field){
			if($field == "Part")
				$results .= "<span style='font-weight: bold;' class='PartDescription'>Part</span>";
			else
				$results .= "<span style='font-weight: bold;' class='$field'>$field</span>";
		}
//		$results .= "\n</BR>";
//		$results .= "</BR>";
//		$results .= "<span style='clear: left;display: block;'>";
		$results .= "<span id='knifeListItem'>";
		$cnt=1;
		foreach ($entries as $entry){
			if($cnt%2)
				$results .= "<div class='InvoiceKnifeListHL'>";
			foreach($fields  as $field){
				if($field == "TotalRetail")
					$results .= "<span class='$field'>" . "$" . number_format($entry[$field] ,2) . "</span>\n";
				elseif($field == "FeatureList")
					$results .= "<span class='FeatureList'>" . $this->knifeEntryAdditions($entry["Additions"]). "</span>\n";
				elseif($field == "Part")
					$results .= "<span class='PartDescription'>" .  $entry['PartDescription'] . "</span>\n";
				else
					$results .= "<span class='$field'>" . $entry[$field] . "&nbsp;</span>\n";
			}
//			$results .= "</P>";
			if($cnt%2)
				$results .= "</div>";
			$cnt++;
			$results .= "</BR>";
		}
		$results .= "</span>";
//		foreach ($entries as $entry){
//			$results .= dumpDBRecord($entry);
//			$results .= "</BR>";
//		}
		$results .= "\n</div><!-- End $formName -- >\n";
//		return count($entries) . " Entries";
		return $results;
	}
	
}
?>