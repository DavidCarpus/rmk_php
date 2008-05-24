<?php
class Base
{
	function isInternetExploder(){
		return strstr($_SERVER['HTTP_USER_AGENT'], "MSIE");	
	}
	
	function fieldDesc($field){
		$lookup = array('name'=>'Full Name','email'=>'Email Address',
						'address1'=>'Billing Address', 'address2'=>'&nbsp;', 'address3'=>'&nbsp;',
						'city'=>'City','state'=>'State/Province', 'zip'=>'Zip/Postal Code', 
						'country'=>'Country', 
						'phone'=> 'Phone Number *','fax'=> 'Fax Number',
						'shipaddress1'=> 'Shipping Address', 
						'shipaddress2'=> '(If other than Billing address)', 'shipaddress3'=> '&nbsp;', 
						'ordertype'=> "Interest",'catqty'=> 'Quantity of Catalogs',
						'model'=> 'Model','bladelength'=> 'Blade Length', 
						'note'=> 'Knife features, comments and questions',
						'cctype'=> 'Credit Card Type', 'ccnumber'=> 'Credit Card Number','ccexpire'=> 'Expiration Date', 
						'ccvcode'=>'VCode (<i>Last 3 digits on signature line</i>)', 'ccname'=> 'Name as it appears on card',
						'comment'=>'Comment',
						'invoice_num'=>'Invoice Number',
						'Prefix'=>'Title','LastName'=>'Last Name','FirstName'=>'First Name','Suffix'=>'Suffix',
						'PhoneNumber'=> 'Phone Number','EMailAddress'=>'Email Address',
						'DateOrdered'=>'Ordered','DateEstimated'=>'Estimated','DateShipped'=>'Shipped', "KnifeCount"=>"Knives",
						 'TotalRetail'=>'Retail', 'ShippingAmount'=>'Shipping', 'PONumber'=>'PO#', 
						 "ShippingInstructions"=>"Shipping Info", "Quantity"=>"Quantity",  "PartDescription"=>"Part",
						 "Price"=>"Price", "Comment"=>"Comment"
						 
						);
//array('PartDescription', 'Quantity', 'TotalRetail', 'Price', 'Comment')
						//				'qty'=> 'Quantity', , ""=>""
		return $lookup[$field];
	}

	public function textField($name, $label, $required=false, $value='', $class='', $jscriptArray=array()){
		$value = htmlizeFormValue($value);
		if($class != '') $class = " class='$class'";
//		var_dump($jscriptArray);
		$labelJscript = (array_key_exists("label", $jscriptArray) ?  $jscriptArray["label"]: "");
		$fieldJscript = (array_key_exists("field", $jscriptArray) ?  $jscriptArray["field"]: "");
//		$labelJscript = $jscriptArray["label"];
//		$fieldJscript = $jscriptArray["field"];
		if($required)
			return "<label for='$name' $labelJscript class='required'>$label</label><input id='$name' name='$name' $fieldJscript value='$value'>";
		else
			return "<label $class $labelJscript for='$name' class='label-$name' >$label</label><input $class id='$name' name='$name' $fieldJscript value='$value'>";
	}
	
	public function hiddenField($name, $value) {
		return "<INPUT TYPE='hidden' NAME='".$name."' value='".htmlizeFormValue($value)."'>";
	}
	
	function optionField($name, $label, $values, $default='' , $required=false){
		$value = htmlizeFormValue($value);
		if($required)
			$results = "<label for='$name' class='required'>$label</label>";
		else
			$results="<label for='$name'>$label</label>";
		foreach($values as $value){
			if($default == $value)
				$results = $results. "&nbsp;&nbsp;<input name='$name' value='$value' type='radio' class='option' checked>$value";
			else 
				$results = $results. "&nbsp;&nbsp;<input name='$name' value='$value' type='radio' class='option'>$value"; 
	//		$results = $results. "type='radio'";
		}
		return $results;
	//	return print_r($values, true);
	}
	
	function textArea($name, $label, $required=false, $value='', $large=false){
		$results="";
		
		if($required)
			$results = "<label for='$name' class='required'>$label</label><textarea $class id='$name' name='$name'>$value</textarea>";
		else
			$results = "<label for='$name' >$label</label><textarea $class id='$name' name='$name'>$value</textarea>";
	
		if($large)
			$results = "<div class='largearea'>" . $results . "</div>";
			
	}

	function checkbox($name, $label, $required=false, $value=''){
		if($value == 'on') $checked='checked=1';
		if($value == 1) $checked='checked=1';
		
		if($required)
			return "<input type='checkbox' id='$name'  name='$name' class='checkbox' $checked>\n";
		else
			return "<input type='checkbox' id='$name' name='$name' class='checkbox' $checked>\n";
	}

	function helpTextJS($url){
//		return "onmouseover='ajax_showTooltip(\"ajax-tooltip.html\",this);return false'" .
//				" onmouseout='ajax_hideTooltip()'";
//		return "onmouseover='ajax_showTooltip(\"$url\",this);return false'" .
//				" onmouseout='ajax_hideTooltip()'";
		return "";
	}
}
?>