<?php
include_once "Base.class.php";

class Customer extends Base
{
	function __construct() {
//       print "In constructor\n";
       $this->name = "MyDestructableClass";
   }
   
	public function summary($request){
		$formName="CustomerSummary";
		
		$results="\n";
		$results .=  "<div id='$formName'>\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='POST'>"  . "\n";
//		$results .=  "<legend>$formsName</legend>" . "\n";
//		customerID as hidden field?
		$fields = array('Prefix', 'FirstName', 'LastName', 'Suffix', 'PhoneNumber', 'EMailAddress');
		foreach( $fields as $name)
		{
			if(!array_key_exists($name, $request)) $request[$name] = "";
			$results .=  $this->textField($name, $this->fieldDesc($name), false, $request[$name]) . "\n";
			if($this->isInternetExploder() && ($name=="Prefix" || $name=="FirstName" || $name=="Suffix" || $name=="EMailAddress"))
					$results .=  "</BR>";
		}
		$results .= "</form>";
		$results .= "</div><!-- End $formName -- >";
		
		return $results;
	}
	
	
}
?>