<?php
class Base
{
	
	public function textField($name, $label, $required=false, $value='', $class=''){
		$value = htmlizeFormValue($value);
		if($class != '') $class = " class='$class'";
		if($required)
			return "<label for='$name' class='required'>$label</label><input id='$name' name='$name' value='$value'>";
		else
			return "<label $class for='$name' >$label</label><input $class id='$name' name='$name' value='$value'>";
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
}
?>