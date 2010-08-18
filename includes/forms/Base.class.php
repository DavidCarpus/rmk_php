<?php
class Base
{
	var $formMode='get';
	
	var $consecutiveID=0;
	
	public $creditCardOptions = array("Mastercard", "Visa", "Discover");
	
	public $requestTypeOptions = array(
						array('id'=>"-1", 'label'=>""),							
						array('id'=>"1", 'label'=>"Quote Request"),							
						array('id'=>"2", 'label'=>"Order Request"),
						array('id'=>"3", 'label'=>"Catalog Request"),
						array('id'=>"4", 'label'=>"Payment Request")
						);
	
	public function requestTypeFromID($id){
   	 foreach ($this->requestTypeOptions as $option){
   	 	if($option['id'] == $id) return $option['label'];
   	 }
   }   
   public function requestTypeIDFromLabel($text){
   	 foreach ($this->requestTypeOptions as $option){
   	 	if($option['label'] == $text) return $option['id'];
   	 }
   }
	
   
	function isInternetExploder(){
		return strstr($_SERVER['HTTP_USER_AGENT'], "MSIE");	
	}
	
   function fieldDesc($field){
		$lookup = array('name'=>'Full Name','email'=>'Email Address',
						'address1'=>'Billing Address', 'address2'=>'&nbsp;', 'address3'=>'&nbsp;',
						'ADDRESS1'=>'Billing Address', 'ADDRESS2'=>'&nbsp;', 'ADDRESS3'=>'&nbsp;',
						'city'=>'City','state'=>'State/Province', 'zip'=>'Zip/Postal Code', 
						'CITY'=>'City','STATE'=>'State/Province', 'ZIP'=>'Zip/Postal Code', 
						'country'=>'Country', 
						'COUNTRY'=>'Country', 
						'phone'=> 'Phone Number *','fax'=> 'Fax Number',
						'shipaddress1'=> 'Shipping Address', 
						'shipaddress2'=> '(If other than Billing address)', 'shipaddress3'=> '&nbsp;', 
						'ordertype'=> "Interest",'catqty'=> 'Quantity of Catalogs',
						'model'=> 'Model','bladelength'=> 'Blade Length', 
						'note'=> 'Knife features, comments and questions',
						'cctype'=> 'Credit Card Type', 'ccnumber'=> 'Credit Card Number','ccexpire'=> 'Expiration Date', 
						'ccvcode'=>'VCode (<i>Last 3 digits on signature line</i>)', 'ccname'=> 'Name as it appears on card',
						'comment'=>'Comment',
						'Invoice'=>'Inv. #',
						'Prefix'=>'Title','LastName'=>'Last Name','FirstName'=>'First Name','Suffix'=>'Suffix',
						'PhoneNumber'=> 'Phone Number','EMailAddress'=>'Email Address',
						'DateOrdered'=>'Ordered','DateEstimated'=>'Estimated','DateShipped'=>'Shipped', "KnifeCount"=>"Knives",
						 'TotalRetail'=>'Retail', 'ShippingAmount'=>'Shipping', 'PONumber'=>'PO#', 
						 "ShippingInstructions"=>"Shipping Info", "Quantity"=>"Quantity",  "PartDescription"=>"Part",
						 "Price"=>"Price", "Comment"=>"Comment", "searchValue"=>"Search Value",
						 'PartCode'=> "PartCode", 'Description'=>"Description", 'Discountable'=>'Discountable', 
						 'BladeItem'=>'BladeItem', 'Taxable'=>'Taxable', 
						 'Memo'=>'Memo', 'Dealer'=>'Dealer', 'Terms'=>'Terms', 'Discount'=>'Discount',
						 'ZONE'=>'Zone', 'TaxPercentage'=>'Tax Rate',
						 'fromaddress'=>'From Email', 'toaddress'=>'To Email','start_datesubmitted'=>'Sent (Start)','end_datesubmitted'=>'Sent (End)','messagesubject'=>'Subject',
		);
//array('PartDescription', 'Quantity', 'TotalRetail', 'Price', 'Comment')
						//				'qty'=> 'Quantity', , ""=>""
		return $lookup[$field];
	}
	function htmlizeFormValue($original){
			$results = $original;
	//		$results = str_replace("'", "&apos;", $results);
	//		$results = str_replace("`", "&apos;", $results); 
			$results = str_replace("'", "&#39;", $results);
			$results = str_replace("`", "&#39;", $results);
			$results = stripcslashes($results);
			return $results; 
	}

//	public function textField($name, $label, $required=false, $value='', $class='', 
// 				$jscriptArray=array(), $readonly=false){

	public function textField($name, $label, $value, $options, $unused1, $unused2, $unused3, $unused4){
		$value = $this->htmlizeFormValue($value);
//		if($class != '') $class = " class='$class'";
		$labelJscript = $fieldJscript = ""; 
		if(isset($options['jscript'])){
//			echo $name . ":" . ($options['jscript']["field"]);
			$jscriptArray =$options['jscript']; 
			$labelJscript = (array_key_exists("label", $jscriptArray) ?  $jscriptArray["label"]: "");
			$fieldJscript = (array_key_exists("field", $jscriptArray) ?  $jscriptArray["field"]: "");
		}
		$ro = (isset($options['readonly'])) ? "readonly='readonly'" : "";
		$error = (isset($options['error'])) ? "class='required'" : "";
		$class = (isset($options['class'])) ? "class='" . $options['class'] ."'" : "";
		
		$id=$name . "_" . $this->consecutiveID++;
		$results = "\n<div class='entryfield'>";
		$results .= "<label for='$id' $labelJscript $error>$label</label>".
				"<input id='$id' $ro $class class='$name' name='$name' $fieldJscript value='$value' />";
//		$results .= "<label class='$name' $labelJscript $error>$label";
//		$results .= "<input $ro $class class='$name' name='$name' $fieldJscript value='$value' />";
//		$results .= "</label>";
		$results .= "</div>";
		return $results;
	}
	
	public function hiddenField($name, $value) {
		return "<input type='hidden' name='".$name."' value='".$this->htmlizeFormValue($value)."' />";
//		return "<input type='hidden' name='".$name."' value='$value' />";
	}
	
	function optionField($name, $label, $values, $default='' , $options){
		$required = isset($options['required']);
//		$value = $this->htmlizeFormValue($value);
		
//		echo $name . ":" . dumpDBRecord($options);
		
		$error = (isset($options['error'])) ? "class='required'" : "";
		$results="<label for='$name' $error>$label</label>";

		$js= (isset($options['js']) ? $options['js']: "");
		foreach($values as $value){
			$chked = ($default == $value) ? "checked" : "";
			$results .= "<span class='optionblock'>";
			$results .= "<input name='$name' value='$value' $js type='radio' class='option' $chked />$value&nbsp;&nbsp;&nbsp;";
			$results .= "</span>";
		}

		$results = "\n<div class='entryfield'>". $results ."</div>";		
		return $results;
	}
	
	function textArea($name, $label, $value, $options, $unused1, $unused2, $unused3, $unused4){
//	function textArea($name, $label, $value, $options, $required=false, $large=false){
		$results="";
		$value = $this->htmlizeFormValue($value);
		$ro = (isset($options['readonly'])) ? "readonly='readonly'" : "";
		$error = (isset($options['error'])) ? "class='required'" : "";
		$class = (isset($options['class'])) ? "class='" . $options['class'] ."'" : "class='$name'";
		$js= (isset($options['js']) ? $options['js']: "");
		
		$id=$name . "_" . $this->consecutiveID++;
		
		$results = "\n<div class='entryfield'>";
		$results .= "<label  for='$id' $error>$label</label>";
		$results .= "<textarea  id='$id' rows='2' cols='20' $js $class name='$name'>$value</textarea>";
		$results .= "</div>";
		
//		if($large)
//			$results = "<div class='largearea'>" . $results . "</div>";
		
		return $results;
	}

	function button($name, $value, $class="btn"){
		return "<input class='btn' type='submit' name='$name' value='$value' />" ;
	}
	
	function checkbox($name, $label, $value, $options, $unused1, $unused2, $unused3, $unused4 ){
//	function checkbox($name, $label, $required=false, $value=''){
		$checked="";
//		echo debugStatement("Value:$value - chk:$checked");
		if($value=="on" || (is_numeric($value) && $value == 1)) 	$checked="checked='checked'";
//		echo debugStatement("Value:$value - chk:$checked #" . is_numeric($value));
		$error = (isset($options['error'])) ? "class='required'" : "";
		
		$results = "\n<div class='entryfield'>";
		$results .= "<label for='$name' $error >$label</label>";		
		$results .= "<input type='checkbox' id='$name' name='$name' class='checkbox' $checked />\n";			
		$results .= "</div>";
		return $results;
	}

	function selection($name, $values, $label, $selected="", $autosubmit=false){
//		echo debugStatement(print_r($values));
		$results = "";
		$results = "\n<div class='entryfield'>";
		$id=$name . "_" . $this->consecutiveID++;
		
		if($label != '')
			$results .= "<label for='$id' >$label</label>";
		$results .= "<select id='$id' size='1' name='$name'";
		if($autosubmit){
//			$results .= " onchange=\"submit();\" ";
//			$results .= " onchange='alert(\"test\");'";
			$results .= " onchange='form.submit();'";
		}
		$results = $results.">";
		if($autosubmit){
			$results = $results."<option value='0' ></option>";
		}
			
		if(count($values) > 0){
			foreach($values as $value){
				$results = $results."<option value='" . $value['id'] . "'";
				if($value['id'] == $selected)
					$results = $results." selected='selected' ";
				$results = $results.">".$value['label']."</option>";
			}
		}
		$results = $results."</select>";
		$results .= "</div>";
		return $results;
	}
	
	function helpTextJS($url){
//		return "onmouseover='ajax_showTooltip(\"ajax-tooltip.html\",this);return false'" .
//				" onmouseout='ajax_hideTooltip()'";
//		return "onmouseover='ajax_showTooltip(\"$url\",this);return false'" .
//				" onmouseout='ajax_hideTooltip()'";
		return "";
	}
	public function formPrefix($page){
		$results = "";
   		$copy = getSingleDbRecord("Select * from webcopy where page='$page'");
		if(count($copy) > 0){
			$results .= "<div id='prefix'>";
			$results .= htmlizeText($copy['prefix']);
			$results .= "</div>";
		}
		return $results;
	}
	public function formPostfix($page){
		$results = "";
   		$copy = getSingleDbRecord("Select * from webcopy where page='$page'");
		if(count($copy) > 0){
			$results .= "<div id='postfix'>";
			$results .= htmlizeText($copy['postfix']);
			$results .= "</div>";
		}
		return $results;
	}
	
	public function disseminatePolicy(){
		$results="";
		$results .= "<div id='disseminatePolicy'>";
		$results .= "It is the policy of Randall Made Knives NOT to disseminate names, addresses, or phone numbers ";
		$results .= "to any person, organization or company.";
		$results .= "</div>";	
		return $results;
	}
	
	public function creditCardFormBlock($formValues, $ccTypeOptions, $getAmount, $hide=true){
		$results="";
		
		$errors = $this->retrieveErrorArray($formValues);

		$hideStyle = ($hide)? "style='display: none;'": "style='display: block;'"; 
		$results .= "<div id='ccdata' $hideStyle>";
		$ccFields=array("cctype"=>"Credit Card Type", 
			"ccnumber"=>"Credit Card Number", "ccexpire"=>"Expiration Date",  
			"ccvcode"=>"VCode <i>(Last 3 digits on signature line)</i>", "ccname"=>"Name as it appears on card",
		);
		if($getAmount)
 			$ccFields["amount"] = "Amount";

		foreach ($ccFields as $name=>$label){
			$value="";
			if(array_key_exists($name, $formValues)) $value=$formValues[$name];
			$options=array();			
			if(array_key_exists($name, $errors)) $options['error']=true;
			
			if($name == 'cctype'){
				$results .= $this->optionField($name, $label, $ccTypeOptions, $value, $options);
			} else	if($name == 'amount'){
				$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"");
//				$results .=  "<span style='font-size: 12px; display: block; float:left;'><i>Min $100 on CC payments</i></span>";
				$results .=  "<span class='minPayment'><i>Min $100 on CC payments. All amounts in USD</i></span>";
				
				$results .=  "<br/>\n";
//				$img = "<img align='top' src='" . getImagePath("memo.png") . "' border='0' alt='Min $100 on CC payments' />";
//
////				$results .= "<a onmouseover='details(\"google\")' href='http://www.google.com/'\" target='_blank' class='menulink'>Google</a>";
//				
//				
//				$results .= "<span class='helptext'>";
////				$results .= "<a href='#'target='_blank'>";
//				$results .= "$img<span>Min $100 on CC payments</span>";
//				$results .= "</span><!-- End HelpText -->\n";				} else	if($name == 'qty'){
			} else	if($name == 'qty'){
				$results .= $this->hiddenField($name, "1");
			} else	if($name == 'note'){
				$results .= $this->textArea($name, $label, $err, $value);
			} else{
				$results .=  $this->textField($name, $label, $value, $options ,"" ,"" ,"" ,"") . "<br/>\n";
//				$results .=  $this->textField($name, $label, $err, $value) . "<br/>\n";
			}
		}
		$results .= "</div>";	
		return $results;	
	}
	
	public function getDataAsHiddenFields($formValues, $fields){
//		$fields = array('requesttype', 'status', 'name', 'phone', 'startdate', 'enddate');
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
	
	public function retrieveErrorArray($formValues){
		$errors = array();
		if(array_key_exists("ERROR", $formValues) && count($formValues['ERROR']) > 0){
//			$errors=array_fill_keys(explode(",", $formValues['ERROR']), true);
			$errs = explode(",", $formValues['ERROR']);
			foreach ($errs as $err){
				$errors[$err] = $err;
			}
		}
		return $errors;		
	}
	public function getUnFormattedCC($ccNumber) {
		$ccNumber = str_replace(" ", "",$ccNumber);
		$ccNumber = str_replace("-", "",$ccNumber);
		return $ccNumber;		
	}
	public function getFormattedCC($creditCard) {
		$results="";
		$split = str_split($creditCard, 4);
		foreach ($split as $part) {
			$results .= $part . "-";
		}
		// trim last "-"
		$results = substr($results, 0 ,strlen($results)-1);
		return $results;
	}
}
?>