<?php
include_once "Base.class.php";

class KnifeList extends Base
{
	function __construct() {   $this->name = "KnifeListForms";  }
   
   public function entryFormMode($formValues)
   {
   		if(array_key_exists("invoicedetail", $formValues) && is_numeric($formValues['invoicedetail'])) return "invoicedetail";
   		if(array_key_exists("invoicesearch", $formValues) ) return "invoicesearch";
		if(array_key_exists("searchValue", $formValues)){return "search";}
//		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Search"){return "search";}
		return "browse";	
   }
   
   public function knifeListNavigation($formValues){
   		$results="";   		
   		$year = $formValues['year'];
   		$week = $formValues['week'];
   		if($week < 1){ // wrap to previous year
			$year--;
			$week=52 + $week;
		}
		if($week > 52){ // wrap to next year
			$year++;
			$week=$week-52;
		}
		
		$startOfYear = strtotime("1/1/$year -". date("w", strtotime("1/1/$year")) . "days");
		
		$startOfGivenWeek=strtotime("+$week week", $startOfYear );
		$endOfGivenWeek=strtotime("+6 days", $startOfGivenWeek );
		$shipDateOfGivenWeek=strtotime("+4 days", $startOfGivenWeek );
		
   		$results .= "\n<div class='knifeListNavigation'>";
   		
   		$results .= "<a href='" . $_SERVER['PHP_SELF'] . "?year=$year&amp;week=" . ($week -1) . "'> Previous Week</a>";
   		$results .= " : " . date("F j Y", $shipDateOfGivenWeek);
   		$results .= "<a href='" . $_SERVER['PHP_SELF'] . "?year=$year&amp;week=" . ($week +1) . "'> Next Week</a>";
   		
   		$results .= "</div><!-- End knifeListNavigation -->\n";
   		return $results;
   }
   
   public function displayKnifeList($knifeListInvoices){
   		$results="";
   		$lastCustomer=0;
   		$prevCustomerDealer=false;
   		$results .= "\n<div class='knifeListDiv'>";
   		$results .= "\n";
   		foreach($knifeListInvoices as $invoice){
   			$bladeCount=$this->bladeItemCount($invoice);
   			if($bladeCount > 0) {
	   			if($lastCustomer != $invoice['CustomerID']){
	   				if($lastCustomer > 0) $results .= "</div><!-- End CustomerID -->\n";
	   				if($prevCustomerDealer) $results .= "<div class='endDealer'>&nbsp;</div>\n";
	   				$results .= "<div class='customer '>";
	   				if($invoice['Dealer']){
	   					$results .= "<div class='customerName'>" .  $invoice['LastName'] . "," . $invoice['FirstName'] . "</div>\n";
	   					$prevCustomerDealer=true;
	   				} else {
	   					$prevCustomerDealer=false;
	   				}
	   			}
				$results .= "<div class='invoice '>" . $this->linkToInvoiceDetail($invoice) . " Blades:$bladeCount\n";
				
				$results .= $this->invoiceKnifeListBlock($invoice);
	
	   			$lastCustomer = $invoice['CustomerID'];
	   			$results .= "</div><!-- End Invoice -->\n";
   			}
   		}
   		$results .= "</div><!-- End CustomerID -->\n";
   		$results .= $this->knifeListSummary($knifeListInvoices);
   		$results .= "</div><!-- End knifeListDiv -->\n";
   		return $results;
   }
   
   function invoiceKnifeListBlock($invoice){
   		$results = ""; 
   		foreach ($invoice['entries'] as $entry){
   			$results .= "<div class='invoiceEntry bdr' >";
   			$results .= "<div class='qty bdr'>" . $entry['Quantity'] . "</div>";
   			$results .= "<div class='partdesc bdr'>" . $entry['PartDescription'] . "</div>"; 
   			$results .= "<div class='features bdr'>" . $this->invoiceEntryFeatureString($entry) . "</div>"; 
   			$results .= "<div class='comment bdr'>" . $entry['Comment'] . "</div>";
   			$results .= "</div><!-- End invoiceEntry -->\n";
   		}
   		return $results;
   }
   
   function knifeListSummary($knifeListInvoices){
   		
   		$cnts=array();
   		foreach($knifeListInvoices as $invoice){
   			foreach ($invoice['entries'] as $entry){
   				if($entry['BladeItem']){
	   				if(! array_key_exists($entry['PartDescription'], $cnts)) $cnts[$entry['PartDescription']] = 0;
   					$cnts[$entry['PartDescription']] += 1;
   				}
   			}
   		}

   		
   		$results="\n<div class='summary'>";
   		ksort($cnts);
   		foreach ($cnts as $desc=>$cnt){
   			$results .= "<div class='desc'>$desc</div><div class='cnt'>$cnt</div>\n";   			
   		}
   		
   		
   		$results .= "</div><!-- End summary -->\n";
   		return $results;
   }
   
   function linkToInvoiceDetail($invoice){
    	$href = $_SERVER['PHP_SELF']. "?invoicedetail=" . $invoice['Invoice'];
   		return "<a href='$href'>" . $invoice['Invoice'] . "</a>";
   }
   
   function bladeItemCount($invoice){
   		$cnt=0;
   		foreach ($invoice['entries'] as $entry){
			if($entry['BladeItem']) $cnt+= $entry['Quantity'];  
//			echo debugStatement(dumpDBRecord($entry)); 			
   		}
   		return $cnt;
   }
   
   
   function invoiceEntryFeatureString($entry){
   		$sheaths = "  MA1 MA2 MAB MB MBB MC MC1 MCB MCR MFB 24B NHS FCH WS BLK LHS LS1 LS2 OK DK ";
		$etching = "  ET1  ET2  ETC ETV NPN NPB EN1 EN2 EN3 EN4 EN5 MED  ";
   	
   		$results ="";
   		foreach ($entry['Additions'] as $feature){
			$isSheath = ( strpos($sheaths, $feature['PartCode']) > 0);
			$isEtch = ( strpos($etching, $feature['PartCode'] ) > 0);
   			
			if($isSheath ) $results .= "<span class='sheath'>";
			if($isEtch ) $results .= "<span class='etch'>";
			$results .= $feature['PartCode'];
			$results .= ",";
			if($isSheath || $isEtch )  $results .= "</span>";
		
//   			$results .= $feature['PartCode'] . ":" . $feature['PartCode'];
//   			$results .= dumpDBRecord($feature);
   		}
   		if($results == "") $results ="&nbsp;";
   		return $results;
   }
   public function invoiceFinanceSummary($invoiceCosts){
   		$results ="";
   		$results .= "\n<div class='invoiceFinanceSummary'>\n";
   		
   		foreach($invoiceCosts as $name=>$value)
		{
			$label = $name;
			$value =  number_format($value ,2);
			$results .= "<div class='$name lbl'>$label</div><div class='data $name rightAlign'>$value</div>\n";
		}
//		$results .= dumpDBRecord($invoiceCosts);
		
		$results .= "</div><!-- End invoiceFinanceSummary -->\n";
   		
		
		return $results;
   }
   public function invoiceSearchForm($formValues){
		$formName="shopInvoiceSearchForm";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
   	   	
		
		$fields = array('Invoice'=>'Invoice Number', 'firstname'=>'First Name', 'lastname'=>'Last Name');
		foreach($fields as $name=>$label)
		{
			$value = $formValues[$name];
			$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
		}		
		
		$results .=  $this->button("submitButton", "Search");
		
		$results .= "\n</form></div><!-- End $formName -->\n";

		return $results;
   }
   
   public function invoiceDetail($invoice){
   		$results="";
   		$fields=array("LastName"=>"Name", "Dealer"=>"Dealer", "PhoneNumber"=>"Phone Number", 
   				"Invoice"=>"Invoice Number", "DateOrdered"=>"Date Ordered", "DateEstimated"=>"Date Estimated",
   				 "ShippingInstructions"=>"Shipping", "ShippingAddress"=>"Ship To", "BillingAddress"=>"Bill To" );
   		
   		foreach($fields as $name=>$label)
		{
			$value = $invoice[$name];
			if($name == 'Dealer'){
				if($value) $results .= "<div class='data $name'>Dealer</div>\n";
				continue;
			} 
			if($name == 'DateOrdered' || $name == 'DateEstimated'){
				$value = date("F j Y", strtotime($value));
			} 
			if($name == 'BillingAddress'){
				$results .= "<div class='BillingAddress_lbl'>$label</div><div class='data $name'>$value</div>\n";
				continue;
			} 
			$results .= "<div class='$name lbl'>$label</div><div class='data $name'>$value</div>\n";
//			$results .= $field . ":" . $invoice[$field];			
		}
		
		$results .= "\n<div class='invoiceKnifeListBlock'>\n";
		$results .= $this->invoiceKnifeListBlock($invoice);
		$results .= "</div><!-- End invoiceKnifeListBlock -->\n";

		$results .= $this->invoiceFinanceSummary($invoice['Costs']);
		
		$results = "\n<div class='shopInvoiceDetail'>\n$results</div><!-- End shopInvoiceDetail -->\n";
//		$results .= dumpDBRecord($invoice);			
		
   		return $results;
   }
   
function displayInvoiceList($records){
	$results = "";
	$results .=  "<div id='displayInvoiceList'>" . "\n";
	
	$fields=array("Invoice"=>"Invoice", "Name"=>"Name" ,
		"dateordered"=>"Ordered", "dateestimated"=>"Estimated", "dateshipped"=>"Shipped", );
	
	foreach($fields as $name=>$label){
		$results .= "<div class='lbl data $name'>$label</div>\n";
	}
	foreach($records as $record){
		foreach($fields as $name=>$label){
			$value=$record[$name];
			if($name == 'Invoice') $value=$this->linkToInvoiceDetail($record);
			if($name == 'Name') $value = $record['FirstName'] . " " . $record['LastName'];
			$results .= "<div class='bdr data $name'>$value</div>\n";
		}
	}
	$results .= "</div><!-- End displayInvoiceList -->\n";
	return $results;
}
}

?>