<?php
class Base
{
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

	public function textField($name, $label, $required=false, $value='', $class='', $jscriptArray=array(), $readonly=false){
		$value = $this->htmlizeFormValue($value);
		if($class != '') $class = " class='$class'";
//		var_dump($jscriptArray);
		$labelJscript = (array_key_exists("label", $jscriptArray) ?  $jscriptArray["label"]: "");
		$fieldJscript = (array_key_exists("field", $jscriptArray) ?  $jscriptArray["field"]: "");
//		$labelJscript = $jscriptArray["label"];
//		$fieldJscript = $jscriptArray["field"];
		$ro = "";
		if($readonly == 'true') $ro="readonly='readonly'";

		if($required)
			return "<label for='$name' $labelJscript class='required'>$label</label>\n".
					"<input $ro $class id='$name' name='$name' $fieldJscript value='$value' />";
		else
			return "<label $class $labelJscript for='$name' class='label-$name' >$label</label>\n".
					"<input $ro $class id='$name' name='$name' $fieldJscript value='$value' />";
	}
	
	public function hiddenField($name, $value) {
		return "<input type='hidden' name='".$name."' value='".$this->htmlizeFormValue($value)."' />";
//		return "<input type='hidden' name='".$name."' value='$value' />";
	}
	
	function optionField($name, $label, $values, $default='' , $required=false){
		$value = $this->htmlizeFormValue($value);
		if(strlen($label) > 0){
			$rqdClass = ($required) ? "class='required'" : "";
			$results="<label for='$name' $rqdClass>$label</label>";
		}
		foreach($values as $value){
			$chked = ($default == $value) ? "checked" : "";
			$results .= "<span class='optionblock'>";
			$results .= "<input name='$name' value='$value' type='radio' class='option' $chked />$value";
			$results .= "</span>";
		}
		return $results;
	}
	
	function textArea($name, $label, $required=false, $value='', $large=false){
		$results="";
		$value = $this->htmlizeFormValue($value);
		if($required)
			$results = "<label for='$name' class='required'>$label</label><textarea $class id='$name' name='$name'>$value</textarea>";
		else
			$results = "<label for='$name' >$label</label><textarea id='$name' name='$name'>$value</textarea>";
	
		if($large)
			$results = "<div class='largearea'>" . $results . "</div>";
		
		return $results;
	}

	function button($name, $value, $class="btn"){
		return "<input class='btn' type='submit' name='$name' value='$value' />" ;
	}
	
	function checkbox($name, $label, $required=false, $value=''){
		$results="";
		$checked="";
//		echo debugStatement("Value:$value - chk:$checked");
		if(is_numeric($value) && $value == 1) 	$checked='checked=1';
		else if(!is_numeric($value) && $value=="on")	$checked='checked=1';
//		echo debugStatement("Value:$value - chk:$checked #" . is_numeric($value));
		
		if($required)
			$results .= "<label for='$name' class='required'>$label</label>";
		else
			$results .= "<label for='$name' >$label</label>";
		
		if($required)
			$results .= "<input type='checkbox' id='$name'  name='$name' class='checkbox' $checked>\n";
		else
			$results .= "<input type='checkbox' id='$name' name='$name' class='checkbox' $checked>\n";
			
		return $results;
	}

	function selection($name, $values, $label, $selected="", $autosubmit=false){
//		echo debugStatement(print_r($values));
		$results = "";
		if($label != '')
			$results .= "<label for='$name' >$label</label>";
		$results .= "<select size='1' name='$name'";
		if($autosubmit){
			$results .= " onchange=\"submit();\" ";
		}
		$results = $results.">";
		if($autosubmit){
			$results = $results."<option value='0' ></option>";
		}
			
		if(count($values) > 0){
			foreach($values as $value){
				$results = $results."<option value='" . $value['id'] . "'";
				if($value['id'] == $selected)
					$results = $results." selected ";
				$results = $results.">".$value['label']."</option>";
			}
		}
		$results = $results."</select>";
		return $results;
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