<?php
include_once "Base.class.php";

class InvoiceEntry extends Base
{
	function __construct() {   }
   
	public function details( $entry ){
		$formName="InvoiceEntryDetails";

		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>"  . "\n";
		$results .=  "<legend>$formName</legend>" . "\n";
		$fields = array('PartDescription', 'Quantity', 'TotalRetail', 'Price', 'Comment');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $entry)) $entry[$name] = "";
			if($name=="TotalRetail" || $name=="Price"  ) $entry[$name] = "$" . number_format($entry[$name] ,2);
			
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $entry[$name]) . "\n";
			if($this->isInternetExploder() && ($name=="TotalRetail"))
					$results .=  "</BR>";
		}
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
		
		return $results;
//		return dumpDBRecord($entry);
		
	}
}
?>