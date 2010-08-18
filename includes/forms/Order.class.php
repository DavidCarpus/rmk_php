<?php
include_once "Base.class.php";

class Order extends Base
{

	public $statusOptions = array(
							array('id'=>"-1", 'label'=>""),							
							array('id'=>"0", 'label'=>"Unprocessed"),
							array('id'=>"1", 'label'=>"Processed"),
							array('id'=>"2", 'label'=>"Accepted"),
							array('id'=>"3", 'label'=>"Denied"),
							array('id'=>"4", 'label'=>"Deferred"),
							);
 	public $searchCriteriaFields = array('requesttype', 'status', 'name', 'phone', 'startdate', 'enddate');
 							
	function __construct() {
       $this->name = "forms_Order";
   }
   
   public function statusFromID($id){
   	 foreach ($this->statusOptions as $option){
   	 	if($option['id'] == $id) return $option['label'];
   	 }
   }
   
   public function statusIDFromDesc($text){
   	 foreach ($this->statusOptions as $option){
   	 	if($option['label'] == $text) return $option['id'];
   	 }
   }
   
   public function entryFormMode($formValues)
   {
		if(array_key_exists("submitButton", $formValues) && strrpos($formValues["submitButton"],"mail:") )	{return "email";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Search"){return "search";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Send"){return "submitEmail";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Review Request"){return "requestreview";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Submit Request"){return "requestsubmit";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Printable List"){return "generatePDF";}
		if(array_key_exists("submitButton", $formValues) && $formValues["submitButton"] == "Process and Print Unprocessed"){return "processAndPrint";}
		
		if(array_key_exists("statusUpdate", $formValues) && $formValues["statusUpdate"] == "UpdateStatus"){return "updatestatus";}
		return "browse";	
   }
   
   function displayUnprocessedCounts($records)
   {
   		$results="";
   		
		$results .=  "<div id='unprocessedrequests'>" . "\n";
   		$results .= "<div class='header'>" ."Unprocessed Requests: " . "</div>";
   		foreach ($records as $label=>$value){
			$results .=  "<div class='unprocessedrequest'>" . "\n";
   			$label =str_replace(" Request", "", $label);
   			$results .= "<div class='label'>" .$label . "</div>";
   			if($value >0){
   				$href = $_SERVER['PHP_SELF'];
   				$href .= "?requesttype=" . $this->requestTypeIDFromLabel($label . " Request");
   				$href .= "&amp;submitButton=Search";
   				$href .= "&amp;status=0";
   				$value = "<a href='$href'>$value</a>";
   			}
   			$results .= "<div class='value'>" .$value . "</div>";
   			$results .= "</div><!-- End unprocessedrequest -->\n";
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
			$options=array();
			$value = $searchValues[$name];
			if($name == 'requesttype'){
				$results .= $this->selection($name, $this->requestTypeOptions, $label, $value);
			} else	if($name == 'status'){
				$results .= $this->selection($name, $this->statusOptions, $label, $value);
			} else{
				$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
			}
//			$results .=  "\n";
		}
//		$results .= hiddenField('startid',$parameters['startid']) . "\n";
//		$results .= hiddenField('action','searchorders') . "\n";
	
		$results .=  $this->button("submitButton", "Search");
		$results .=  $this->button("submitButton", "Printable List");
		$results .=  $this->button("submitButton", "Process and Print Unprocessed");
		$results .= "\n</form>";
		$results .= "</div><!-- End $formName -->\n";

		return $results;		
	}
	public function originalSearchCritera($formValues){
		$results=array();
		if(!array_key_exists("search_criteria", $formValues)) return $results;
		
		$criteria=explode(",", $formValues['search_criteria']);
		foreach ($criteria as $field){
			$results[$field] = $formValues[$field];
		}
		return $results;
	}
	public function getSearchCriteriaAsHiddenFields($formValues, $fields){
		foreach($fields as $name){
			
		}
	}
//	public function getCurrentSearchCriteriaFields($formValues){
//		$fields = array('requesttype', 'status', 'name', 'phone', 'startdate', 'enddate');
//		$results="";
//		foreach($fields as $name)
//		{
//			if($formValues[$name] != ""){
//				$results .= $name . ",";
////				echo  $this->htmlizeFormValue($formValues[$name]) . ":";
//			}
//		}
//		return $results;		
//	}
	
//	public function getCurrentSearchCriteria($formValues){
//		$fields = array('requesttype', 'status', 'name', 'phone', 'startdate', 'enddate');
//		return $this->getDataAsHiddenFields($formValues, $fields);
//	}
   function listItems($records,$formValues){
		$results = "<div id='RequestList'>";
		if(sizeof($records) == MAX_EMAIL_LIST_LEN )
		{
			$results .= "<div class='maxcnt'>Only displaying oldest " . MAX_EMAIL_LIST_LEN . " records. Narrow search criteria if needed.</div>";
		}
		
//		$searchCriteriaFields
//		$searchCriteria=$this->getFieldsSearchCriteria($formValues);
//		echo "searchCriteria:" . $this->getCurrentSearchCriteriaFields($formValues);
		
		foreach($records as $record){
			$results .=  $this->listItem($record,$formValues);
		}
		$results .=  "</div>";
//		$results = ($this->enteredSearchFields($formValues, $this->searchCriteriaFields)) . $results;
//		$results .=  $searchCriteria;
		return $results;
	}

	public function listItem($record, $formValues, $displayOnly=false){
		$results ="";
		$orderType  = $this->requestTypeFromID($record['ordertype']);

//		echo "searchFields:" . $this->getCurrentSearchCriteriaFields($formValues);
//		foreach ($record as $name=>$value){
//			$results .=  "<div class='$name'>$name : $value</div>";
//		}
		$formName="RequestUpdate";

		$shortType=str_replace(" Request", "Request", $orderType);
		$results .=  "\n<div class='$shortType'><!-- Start $shortType -->\n";
//		$results .= $shortType;
		
		if(!$displayOnly){
			$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='post'>"  . "\n";
			$results .= $this->getDataAsHiddenFields($formValues, $this->searchCriteriaFields);
			$results .= $this->hiddenField("statusUpdate", "UpdateStatus")."\n";
			$results .= $this->hiddenField("orders_id", $record['orders_id'])."\n";
			$results .= $this->hiddenField("search_criteria", 
					($this->enteredSearchFields($formValues, $this->searchCriteriaFields)))."\n";
		} 
		
//		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='get'>"  . "\n";
		
		$fields=array("email"=>"Email Address", "processed"=>"Process State", "name" => 'Full Name',  
						"address1"=>"Billing Address", "shipaddress1"=>"Shipping Address",
						"csz" => 'city_state_zip_cntry', "phone"=> "Phone Number","invoice"=> "Invoice",
						 "CC" => "Credit Card Info","note" => 'Customer Notes', 
						"datesubmitted"=>"Request Submitted", "ordertype"=>"Request Type", 
		);
		if($record['ordertype'] == $this->requestTypeIDFromLabel("Quote Request")
			|| $record['ordertype'] == $this->requestTypeIDFromLabel("Order Request")){
				$fields['model']="Model";
				$fields['qty']="Quantity";
				$fields['bladelength']="Blade";
			}
		
		foreach ($fields as $name=>$label){
			$value="";
			if(array_key_exists($name, $record)) $value=$record[$name];
			if($name == 'ordertype'){
				$value = $this->requestTypeFromID($record['ordertype']);
			}
			if($name == 'processed'){
				$value = $this->statusFromID($record['processed']) . (($displayOnly) ?   ":" . $record['comment']: "");
			}
			if($name == 'address1'){
				if($record['address2'] != ''){
					$value .=  ", " . $record['address2'];
				}
				if($record['address3'] != ''){
					$value .=  ", " . $record['address3'];
				}
			}
			if($name == 'shipaddress1'){
				if($record['shipaddress2'] != ''){
					$value .=  ", " . $record['shipaddress2'];
				}
				if($record['shipaddress3'] != ''){
					$value .=  ", " . $record['shipaddress3'];
				}
			}
			// 'Hide' ship address for Payments
			if($name == 'shipaddress1' && $shortType == "PaymentRequest"){
				$value ="";
			}
			if($name == 'csz'){
				$value=$record['city'] . ", " . $record['state'] . " " . $record['zip']. " " . $record['country'];
			}
			if($name == 'CC'){
				$ccnumber = $this->getFormattedCC($record['ccnumber']);
				$value=substr($record['cctype'],0,1) . ": " . $ccnumber . "\n"; 
				$value.= "EXP: " . $record['ccexpire']. "\n";
				$value .= "#" . $record['ccvcode']. "\n";
				$value.= $record['ccname'];
				if(strlen( trim($record['cctype'] . $record['ccnumber'] . $record['ccvcode'])) == 0) $value="NONE";
				if($value == "NONE" && 	$orderType="Catalog Request") $value="";
			}
//			if(!$displayOnly){
//				if($name == 'email' && $value != ''){
//					$value =  $this->button("submitButton", "Email:$value");
//				}
//			}
			if($value != ''){
				$options=array();
				if($name == 'CC' || $name == 'note'){
					$results .=  $this->textArea($name, $label, $value, $options ,"" ,"" ,"" ,"");
					
				} else if($name == 'processed' && !$displayOnly){
					$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
					$results .= $this->selection($name, $this->statusOptions, "", $record['processed'], true);
					$results .=  $this->textArea('comment', "RMK Notes", $record['comment'], $options ,"" ,"" ,"" ,"");
					
				} else if($name == 'email' && $value != '' && !$displayOnly){
					$results .=  $this->button("submitButton", "Email:$value");
										
				} else {
					$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
				}
//				$results .=  "<div class='req_label'>$label:</div>";
//				$results .=  "<div class='req_val'>$value</div>";
//				$results .=  "<br/>\n";
			}
		}
		
		if(!$displayOnly){
			$results .=  "\n</form><!-- End $formName -->\n";
		}
		$results .=  "</div><!-- End $shortType -->\n";
		return $results;
	}
	
	public function enteredSearchFields($formValues, $possibleFields){
		$results=array();
		foreach ($possibleFields as $field){
			if(array_key_exists($field, $formValues) && $formValues[$field] != "") $results[]= $field;
		}
		return implode(",", $results);
	}
	
	public function customerOrderValidation($formValues){
		$formName="customerOrderValidation";
		
		if(array_key_exists("STATUS", $formValues) && $formValues['STATUS'] == 'requestsubmit'){
				$results .= "\n<div class='statusmessage'>";
				$results .=  "Request Submitted";
				$results .= "\n</div>";				
		}
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='post'>"  . "\n";
		
		$results .=  "<div id='$formName"."_main'>" . "\n";
		
		$fields=array("name"=>"Full Name", "email"=>"Email Address", "address1"=>"Billing Address", 
			"address2"=>"&nbsp;", "address3"=>"&nbsp;", "city"=>"City", "state"=>"State/Province", 
			"zip"=>"Zip/Postal Code",	"country"=>"Country", "phone"=>"Phone Number", 
			"shipaddress1"=>"Shipping Address (If other than Billing address)", 
			"shipaddress2"=>"&nbsp;", "shipaddress3"=>"&nbsp;",  
			"ordertype"=>"Interest", "qty"=>"&nbsp;", "model"=>"Model",  "bladelength"=>"Blade Length", 
			"note"=>"Knife features, comments and questions", "cctype"=>"Credit Card Type", 
			"ccnumber"=>"Credit Card Number", "ccexpire"=>"Expiration Date",  
			"ccvcode"=>"VCode <i>(Last 3 digits on signature line)</i>", "ccname"=>"Name as it appears on card"
		);

		foreach ($fields as $name=>$label){
			$value=$formValues[$name];	
			$options=array();			
			$options['readonly']=true;
			if($value != ''){
				if($name=='qty'){
					$results .=  $this->hiddenField($name, $value);
				} else	if($name == 'note'){
					$results .=  $this->textArea($name, $label, $value, $options ,"" ,"" ,"" ,"");									
				} else {
					$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
				}
			}
		}
		if(!array_key_exists("STATUS", $formValues) || $formValues['STATUS'] != 'requestsubmit'){
			$results .=  $this->button("submitButton", "Submit Request");
			$results .=  $this->button("submitButton", "Edit Request");
		}
		$results .= "</div>";
		
		$results .= "</div>";
		$results .= "</form><!-- End $formName -->\n";
		return $results;
	}
	
	public function customerOrderForm($formValues){
		$formName="customerOrderForm";
		$results ="";	
		
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='post'>"  . "\n";
		
		$results .= $this->formPrefix('order');
		
		$results .=  "<div id='$formName"."_main'>" . "\n";
		
		
		$results .= "<div style='color:red'>* These fields are required. <br/>Your order/quote will not process if they are empty.</div>";
		$results .= "<br />\n" ;
		
			
		$errors = $this->retrieveErrorArray($formValues);
		
		$fields=array("name"=>"Full Name *", "email"=>"Email Address *", "address1"=>"Billing Address *", 
			"address2"=>"&nbsp;", "address3"=>"&nbsp;", "city"=>"City *", "state"=>"State/Province *", 
			"zip"=>"Zip/Postal Code *",	"country"=>"Country *", "phone"=>"Phone Number *", 
			"shipaddress1"=>"Shipping Address ", 
			"shipaddress2"=>"(If other than Billing address)", "shipaddress3"=>"&nbsp;",  
			"ordertype"=>"Interest *", "qty"=>"&nbsp;", "model"=>"Model",  "bladelength"=>"Blade Length", 
			"note"=>"Knife features, comments and questions", 
		);

		foreach ($fields as $name=>$label){
			$value=$formValues[$name];
			$options=array();			
			if(array_key_exists($name, $errors)) $options['error']=true;
			
			if($name == 'qty'){
				$results .= $this->hiddenField($name, "1");
			} else	if($name == 'note'){
				$results .=  $this->textArea($name, $label, $value, $options ,"" ,"" ,"" ,"");				
//				$results .= $this->textArea($name, $label, $err, $value);
			} else	if($name == 'ordertype'){
				$options['js']="onclick='customerOrderTypeToggle($name, $formName);'";
				$results .=  $this->optionField($name, $label, array('Quote','Order'), $value, $options);
			} else{
				$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
			}
		}
		
		$results .= "</div>";
		
		
		$hiddenCC=true;
		if($formValues['ordertype'] == "Order"){
			$hiddenCC=false;
		}
		$results .= $this->creditCardFormBlock($formValues, $this->creditCardOptions, false, $hiddenCC);
		
		
		$results .=  $this->button("submitButton", "Review Request");
		
		$results .= "</div>";
		$results .= "</form>";
		$results .= $this->disseminatePolicy();
		
		$results .= $this->formPostfix('order');

		return $results;
	}
	
	public function orderSubmissionResponseText($formValues){
		$results="";
		$results .= "Thank you for your order request with Randall Made Knives.\n\n";
		$results .= "Full Name:". $formValues['name'] . "\n";
		$results .= "Order request date:". date("F j Y") . "\n";
		$results .= "Model Number:". $formValues['model'] . "\n\n";
		$results .= "An 'order acknowledgement' will be forwarded via post office within 21 days.\n\n";
		$results .= "The acknowledgement will outline knife order specifications, ";
		$results .= "the scheduled ship date and also a deposit record. ";
		$results .= "If you do not receive an order acknowledgement, it is imperative to contact Randall Made Knives "; 
		$results .= "to verify complete order specs and deposit records.\n\n";
		$results .= "<I>All order requests are subject to approval  and confirmation by Randall Made Knives.</I>\n\n";
		return $results;		
	}
	public function quoteSubmissionResponseText($formValues){
		$results="";
		$results .= "Thank you for your quote request with Randall Made Knives.\n\n";
		$results .= "Full Name:". $formValues['name'] . "\n";
		$results .= "Quote request date:". date("F j Y") . "\n";
		$results .= "Model Number:". $formValues['model'] . "\n\n";
		$results .= " A reply will be forwarded in approximately three business days.\n\n";
		return $results;				
	}

	public function orderSubmissionResponse($formValues){
		$responseDiv="orderSubmissionResponse";
		$results="";
		$results .=  "<div id='$responseDiv'>" . "\n";
		$results .= $this->orderSubmissionResponseText($formValues);
		$results .= "</div><!-- End $responseDiv -->\n";
		return $results;		
	}
	
	public function quoteSubmissionResponse($formValues){
		$responseDiv="quoteSubmissionResponse";
		$results="";
		$results .=  "<div id='$responseDiv'>" . "\n";
		$results .= $this->quoteSubmissionResponseText($formValues);
		$results .= "</div><!-- End $responseDiv -->\n";
				
		return $results;		
	}
}
?>