<?php
include_once "Base.class.php";


class WeeklyReports extends Base
{
	var $formMode='get';

	function __construct() {
       $this->name = "forms_Catalog";
   }
   public function entryFormMode($formValues)
   {
   		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "View" 
   			&& array_key_exists("date", $formValues) && strlen($formValues["date"]) > 5){return "display_orders";}
   		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Report" 
   			&& array_key_exists("date", $formValues) && strlen($formValues["date"]) > 5){return "Report";}
   			
		return "get_date";	
   }
   
   public function getDealerSpecLetterDate($formValues)
   {
   		$formName="dealerSpecLetterDate";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
		
	   	$errors = $this->retrieveErrorArray($formValues);
	   	
	   	if($formValues['date'] == '')	$formValues['date'] = date("m/d/y", strtotime("+8 week"));

	   	
		$fields = array('date'=>'Estimated Ship Date' );
		foreach($fields as $name=>$label)
		{
			$value = $formValues[$name];
			$options=array();
			if(array_key_exists($name, $errors)) $options['error']=true;

			$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
		}
		
		$results .=  $this->button("submit", "View");
		$results .=  $this->button("submit", "Report");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		
		return $results;
   }

   
}
?>
