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
	
}
?>