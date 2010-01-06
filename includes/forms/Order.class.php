<?php
include_once "Base.class.php";

class Order extends Base
{
	public $requestTypeOptions = array(
							array('id'=>"-1", 'label'=>""),							
							array('id'=>"1", 'label'=>"Quote Request"),							
							array('id'=>"2", 'label'=>"Order Request"),
							array('id'=>"3", 'label'=>"Catalog Request")
							);
	public $statusOptions = array(
							array('id'=>"-1", 'label'=>""),							
							array('id'=>"0", 'label'=>"Unprocessed"),
							array('id'=>"1", 'label'=>"Processed"),
							array('id'=>"2", 'label'=>"Accepted"),
							array('id'=>"3", 'label'=>"Denied"),
							array('id'=>"4", 'label'=>"Deferred"),
							);
													
	function __construct() {
       $this->name = "forms_Order";
   }
   
   public function requestTypeFromID($id){
   	 foreach ($this->requestTypeOptions as $option){
   	 	if($option['id'] == $id) return $option['label'];
   	 }
   }
   public function statusFromID($id){
   	 foreach ($this->statusOptions as $option){
   	 	if($option['id'] == $id) return $option['label'];
   	 }
   }
   
   public function entryFormMode($formValues)
   {
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Search"){return "search";}
		if(array_key_exists("statusUpdate", $formValues) && $formValues["statusUpdate"] == "UpdateStatus"){return "updatestatus";}
		return "browse";	
   }
   function displayUnprocessedCounts($records)
   {
   		$results="";
   		
		$results .=  "<div id='unprocessedrequests'>" . "\n";
   		$results .= "<div class='header'>" ."Unprocessed Requests: " . "</div>";
   		foreach ($records as $label=>$value){
   			$label =str_replace(" Request", "", $label);
   			$results .= "<div class='label'>" .$label . "</div>";
   			$results .= "<div class='value'>" .$value . "</div>";
   		}
   		$results .= "</div><!-- End unprocessedrequests -->\n";
   		return $results ;
   }
   
	function searchForm($searchValues)
	{
		$formName="OrdersSearch";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
		
		$fields = array('requesttype'=>'Request Type', 'status'=>'Status', 'name'=>'Name', 
					'phone'=>'Phone Number', 'startdate'=>'Date - Start', 'enddate'=>'Date - End');
		foreach($fields as $name=>$label)
		{
			$value = $searchValues[$name];
			if($name == 'requesttype'){
				$results .= $this->selection($name, $this->requestTypeOptions, $label, $value);
			} else	if($name == 'status'){
				$results .= $this->selection($name, $this->statusOptions, $label, $value);
			} else{
				$results .=  $this->textField($name, $label, $err, $value) . "<br/>\n";
			}
		}
//		$results .= hiddenField('startid',$parameters['startid']) . "\n";
//		$results .= hiddenField('action','searchorders') . "\n";
	
		$results .=  $this->button("submit", "Search");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";

		return $results;		
	}
	function getCurrentSearchCriteria($formValues){
		$fields = array('requesttype', 'status', 'name', 'phone', 'startdate', 'enddate');
		$results="";
		foreach($fields as $name)
		{
			if($formValues[$name] != ""){
				$results .= $this->hiddenField($name, $formValues[$name]);
//				echo  $this->htmlizeFormValue($formValues[$name]) . ":";
			}
		}
		return $results;
	}
   function listItems($records,$formValues){
		$results = "<div id='RequestList'>";
		if(sizeof($records) == MAX_EMAIL_LIST_LEN )
		{
			$results .= "<div class='maxcnt'>Only displaying oldest " . MAX_EMAIL_LIST_LEN . " records. Narrow search criteria if needed.</div>";
		}
		
		$searchCriteria=$this->getCurrentSearchCriteria($formValues);
		
		foreach($records as $record){
			$results .=  $this->listItem($record,$searchCriteria);
		}
		$results .=  "</div>";
		$results .=  $searchCriteria;
		return $results;
	}
	
	function listItem($record,$searchCriteria){
		$results ="";
		$orderType  = $this->requestTypeFromID($record['ordertype']);

//		foreach ($record as $name=>$value){
//			$results .=  "<div class='$name'>$name : $value</div>";
//		}
		$formName="RequestUpdate";

		$shortType=str_replace(" Request", "Request", $orderType);
		$results .=  "\n<div id='$shortType'>\n";
		
		$results .=  "<form action='" . $_SERVER['SCRIPT_NAME'] . "' method='get'>\n" ;
		$results .=  $searchCriteria;
		$results .= $this->hiddenField("statusUpdate", "UpdateStatus")."\n";
		$results .= $this->hiddenField("orders_id", $record['orders_id'])."\n";
		
		
		$fields=array("processed"=>"Process State", "ordertype"=>"Request Type",
						"name" => 'Full Name', "email"=>"Email Address", 
						"address1"=>"Billing Address", "shipaddress1"=>"Shipping Address",
						"csz" => 'city_state_zip_cntry', "phone"=> "Phone Number",
						 "note" => 'Knife features, comments and questions', "CC" => "Credit Card Info",
						"datesubmitted"=>"Request Submitted", 
		);
		
		foreach ($fields as $name=>$label){
			$value=$record[$name];
			if($name == 'ordertype'){
				$value = $orderType;
			}
			if($name == 'processed'){
				$value = $this->statusFromID($record['processed']) . ":";
				$value .= $this->selection($name, $this->statusOptions, "", $record['processed'], true);
				$value .= "<br/>";
				$value .= $this->textArea('comment', "", false, $record['comment']);
			}
			if($name == 'address1'){
				$value .=  $record['address2'];
				$value .=  $record['address3'];
			}
			if($name == 'shipaddress1'){
				$value .=  $record['address2'];
				$value .=  $record['address3'];
			}
			if($name == 'csz'){
				$value=$record['city'] . ", " . $record['state'] . " " . $record['zip']. " " . $record['country'];
			}
			if($name == 'CC'){
				$value=$record['cctype'] . " : " . $record['ccnumber'] . " (" . $record['ccvcode']. ") ";
				$value.= " EXP: " . $record['ccexpire'];
				$value.=" : " . $record['ccname'];
				if($value == " :  ()  EXP:  : ") $value="NONE";
				if($value == "NONE" && 	$orderType="Catalog Request") $value="";
			}
			if($value != ''){
				$results .=  "<div class='req_label'>$label:</div>";
//				$results .=  "<div class='$name'>$value</div>";
				$results .=  "<div class='req_val'>$value</div>";
				$results .=  "<br/>\n";
			}
		}
		$results .=  "</form>";
		$results .=  "</div><br/>";
		return $results;
		
	}
	
//	function itemDetail($record){
//		$results ="";
//		$orderType  = $this->requestTypeFromID($record['ordertype']);
//		
//		$fields=array("processed"=>"Process State", "ordertype"=>"Request Type",
//						"name" => 'Full Name', "email"=>"Email Address", 
//						"address1"=>"Billing Address", "shipaddress1"=>"Shipping Address",
//						"csz" => 'city_state_zip_cntry', "phone"=> "Phone Number",
//						 "note" => 'Knife features, comments and questions', "CC" => "Credit Card Info",
//						"datesubmitted"=>"Request Submitted", 
//		);
//
//		foreach ($fields as $name=>$label){
//			$value=$record[$name];
//			if($name == 'ordertype'){
//				$value = $orderType;
//			}
//			if($name == 'processed'){
//				$value = $this->statusFromID($record['processed']);
//			}
//			
//			if($name == 'address1'){
//				$value .=  $record['address2'];
//				$value .=  $record['address3'];
//			}
//			if($name == 'shipaddress1'){
//				$value .=  $record['address2'];
//				$value .=  $record['address3'];
//			}
//			if($name == 'csz'){
//				$value=$record['city'] . ", " . $record['state'] . " " . $record['zip']. " " . $record['country'];
//			}
//			if($name == 'CC'){
//				$value=$record['cctype'] . " : " . $record['ccnumber'] . " (" . $record['ccvcode']. ") ";
//				$value.= " EXP: " . $record['ccexpire'];
//				$value.=" : " . $record['ccname'];
//				if($value == " :  ()  EXP:  : ") $value="NONE";
//				if($value == "NONE" && 	$orderType="Catalog Request") $value="";
//			}
//			if($value != ''){
//				$results .=  "<div class='$name'>$label : $value</div>";
//			}
//		}
//		$results .= "<br />";
//		return $results;
//	}
}
?>