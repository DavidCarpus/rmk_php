<?php
/* Created on Feb 4, 2006 */

//if($locationPrefix == '/rmk')
	$fileDir = '/var/www/html/rmk/images/catalog';
//else
//	$fileDir = '/home/rmk/public_html';


function dumpServerVariables(){
	echo getServerVariables();
}

function getServerVariables(){
	$results = "";
  $crlf=chr(13).chr(10);
  $br='<br />'.$crlf;
  $p='<br /><br />'.$crlf;
 
  foreach ($_SERVER as $key => $datum)
    $results .= $key.' : '.$datum.$br;
 
  $results .= $p;
  return $results;
}

function getHTMLValue($key){
	if($_POST[$key] != null) return $_POST[$key];
	if($_GET[$key] != null) return $_GET[$key];
	return null;
}

function debugStatement($statement){
	if(!isDebugMachine()) return;

	print "<HR>". $statement . "<BR><HR>";
}

function dumpDBRecord($record){
	$results = "";
	foreach(array_keys($record) as $key){
		$results .= "<B>$key</B>=>".$record[$key]."<BR>\n";
	}
		
	return $results;
}

function dumpPOST_GET(){
	if(!isDebugMachine()) return;
	
	$results = "";
	$results .= "<div style='clear:both'>";
	foreach(array_keys($_POST) as $key){
		$value = $_POST[$key];
		$results .= "<B> $key </B> : $value <BR>\n";
	}
	foreach(array_keys($_GET) as $key){
		$value = $_GET[$key];
		$results .= "<B> $key </B> : $value <BR>\n";
	}
	$results .= "</div>";
	return $results;
}
function checkbox($name, $label, $required=false, $value=''){
	if($value == 'on') $checked='checked=1';
	if($value == 1) $checked='checked=1';
	
	if($required)
		return "<input type='checkbox' id='$name'  name='$name' $checked>";
	else
//		return "<label for='$name' >$label</label><input type='checkbox' id='$name' name='$name' $checked>";
		return "<input type='checkbox' id='$name' name='$name' $checked>";
		
//	echo "<label for='realtorblast' class='required'>Realtor Newsletter</label><input type='checkbox' name='realtorblast'>";
		
}
function htmlizeFormValue($original){
		$results = $original;
//		$results = str_replace("'", "&apos;", $results);
//		$results = str_replace("`", "&apos;", $results); 
		$results = str_replace("'", "&#39;", $results);
		$results = str_replace("`", "&#39;", $results);
		return $results; 
}
function de_htmlizeFormValue($original){
//		$results = str_replace("&apos;", "'", $original); 
		$results = str_replace("&#39;", "'", $original); 
		return $results; 
}

function hiddenField($name, $value) {
	return "<INPUT TYPE='hidden' NAME='".$name."' value='".htmlizeFormValue($value)."'>";
}
function textField($name, $label, $required=false, $value='', $class=''){
	$value = htmlizeFormValue($value);
	if($class != '') $class = " class='$class'";
	if($required)
		return "<label for='$name' class='required'>$label</label><input id='$name' name='$name' value='$value'>";
	else
		return "<label $class for='$name' >$label</label><input $class id='$name' name='$name' value='$value'>";
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
		
	return $results;
}

function getFormValues(){
	$result = array();
	foreach(array_keys($_POST) as $key){
		$value = $_POST[$key];
		$result[$key] = $value;
	}
	foreach(array_keys($_GET) as $key){
		$value = $_GET[$key];
		$result[$key] = $value;
	}
	return $result;
}
function loggedIn(){
	return ($_SESSION['loginValidated'] == 1);
}

function selection($name, $values, $label, $selected="", $autosubmit=false){
//	print "Selected?:$selected";
	if($label != '')
		$results .= "<label for='$name' >$label</label>";
	$results = $results."<select size='1' name='$name'";
	if($autosubmit){
		$results = $results." onChange='submit()' ";
	}
	$results = $results.">";
	if($autosubmit){
		$results = $results."<option value='0' ></option>";
	}
		
	if(count($values) > 0){
		foreach($values as $value){
//			print_r($value);
//			print "<BR>";
			$results = $results."<option value='" . $value['id'] . "'";
			if($value['id'] == $selected)
				$results = $results." SELECTED ";
			$results = $results.">".$value['label']."</option>";
		}
	}
	$results = $results."</select>";
	return $results;
}

function headSegment($stylesheet="Style.css"){
	return 	"<head><meta http-equiv='Content-Language' content='en' />" .
			"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />" .
			"<LINK href='$stylesheet' rel='stylesheet' type='text/css'></head>\n";
}

function logo_header($section, $prefix="."){
//	$prefix = ".";
//	if(strpos($_SERVER['PHP_SELF'],"order/") != FALSE)
//		$prefix = "..";
//	if(strpos($_SERVER['PHP_SELF'],"admin/") != FALSE)
//		$prefix = "..";
		
	$results = "";
	$headerid="";
	if($section=='admin') $headerid='admin';
//	if($section=='admin')
////		$results = $results .  "<div id='adminheader'><H1>Admin Header</H1></div>\n";
//		$results = $results .  "<div id='adminheader'><img src='" . getBaseImageDir() . "/logo.gif'></div>\n";
//	else	
////		$results = $results .  "<div id='header'><H1>NEW and Improved RMK Website</H1></div>";
//		$results = $results .  "<div id='header'><img src='" . getBaseImageDir() . "/logo.gif'></div>\n";
		$image1=getBaseImageDir() . "/logo.gif";
		$image2=getBaseImageDir() . "/logoknife.gif";
		$image3=getBaseImageDir() . "/Globe.gif";
		if(getCurrPage() <> "index.php")
			$image3=getBaseImageDir() . "/GlobeStill.gif";
			
		$results = $results .  "<div class='".$headerid."titlebar' >";
//		$results = $results .  "<div id='".$headerid."header' style='text-align:left;'>";
		$results = $results .  "<img ALIGN='top' src='$image3'>";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
//		$results = $results .  "</div>\n";
		
		$results = $results .  "<img ALIGN='top' src='$image1' >";
//		$results = $results .  "&reg;<SMALL><SUP>TM</SUP></SMALL>";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		
//		$results = $results .  "<img src='$image3'>";
		$results = $results .  "</div>\n";

		

	return $results;
}
function adminToolbar(){
	$prefix = getToolbarPrefix();
	$menu = array(
//	array('', ''),
				array('catalog.php', 'Catalog'),
				array('Times.php', 'Billed Time'),
				array('orders.php', 'Orders'),
				array('faq.php', 'F.A.Q.'),
				array('webcopy.php', 'Copy/Text'),
				array('ToDo.php', 'ToDo'),
				array('emailReview.php', 'Emails'),
				array('../', 'Home'),
				);
	$results = "";
	$currPage=getCurrPage();
	
//	$results = $results . $currPage;
	
	$results = $results . "<div class='leftnavigation'>\n";
	foreach($menu as $option){
		$realprefix = $prefix;
		$realprefix = str_replace("http://", "https://", $realprefix);
		if($option[1] == 'Home'){
			$realprefix = str_replace("https://", "http://", $realprefix);
		}
		$selectedStyle="";
		if(strtolower($currPage)==strtolower($option[0]))
			$selectedStyle="id='selected'";
			
		$results = $results . "<a $selectedStyle href='$realprefix/admin/" . $option[0] . "'>" . $option[1] . "</a>\n";
	}
	if($_SESSION['loginValidated'] == 1)
		$results = $results . "<a href='$prefix/admin/logout.php'>LOGOUT</a>\n";
	else
		$results = $results . "<a href='$prefix/admin/logout.php'>LOGIN</a>\n";

	//########### Development machine not set up for secure pages at this point  #####
	if($_SERVER['HTTP_HOST'] == "carpus.homelinux.org"){ 
		$results = str_replace("https://", "http://", $results);
	}
		
	$results = $results . "</div>\n";

	return $results;
//		return "<a href='Times.php'>Billed Time</a>";
	
}
function shopToolbar(){
	if(!isLocalAccess() && !isDebugAccess())
		return "";
//		return $_SERVER['REMOTE_ADDR'];
		
	$prefix = getToolbarPrefix();
	$menu = array(
//	array('', ''),
				array('knife_list.php', 'Knife List'),
				array('view_invoice.php', 'View Order'),
				);
	$results = "";
	$currPage=getCurrPage();
	
//	$results = $results . $currPage;
	
	$results = $results . "<div class='leftnavigation'>\n";
	foreach($menu as $option){
		$realprefix = $prefix;
		$realprefix = str_replace("http://", "https://", $realprefix);
		if($option[1] == 'Home'){
			$realprefix = str_replace("https://", "http://", $realprefix);
		}
		$selectedStyle="";
		if(strtolower($currPage)==strtolower($option[0]))
			$selectedStyle="id='selected'";
			
		$results = $results . "<a $selectedStyle href='$realprefix/shop/" . $option[0] . "'>" . $option[1] . "</a>\n";
	}
	
	//########### Development machine not set up for secure pages at this point  #####
	if($_SERVER['HTTP_HOST'] == "carpus.homelinux.org"){ 
		$results = str_replace("https://", "http://", $results);
	}
		
	$results = $results . "</div>\n";

	return $results;
}


function toolbar(){
	$prefix = getToolbarPrefix();

	$menu = array(
//				array('', ''),
				array('catalog.php', 'Catalog'),
				array('design.php', 'The Design'),
				array('construction.php', 'Knife Construction'),
				array('knifecare.php', 'Knife Care'),
				array('history.php', 'The History'),
				array('faq.php', 'F.A.Q.'),
				array('catalogrequest.php', 'Catalog Request'),
				array('order/index.php', 'Order Form'),
//				array('admin/', 'Administrate'),
				array('index.php', 'Home'),
				);
	$currPage=getCurrPage();
	
	$results = $results . "\n<div class='leftnavigation'>\n";
//	$results = $results . "<HR>Test<HR>";
//	$results = $results . $currPage;
	
	
	
	foreach($menu as $option){
		$realprefix = $prefix;
		if($option[0] == 'order/index.php' || $option[0] == 'catalogrequest.php'){
			$realprefix = str_replace("http://", "https://", $realprefix);
		}
		$selectedStyle="";
		if(strtolower($currPage)==strtolower($option[0]))
			$selectedStyle="id='selected'";
		
		$results = $results . "<a $selectedStyle href='$realprefix/" . $option[0] . "'>" . $option[1] . "</a>\n";
	}
	if($_SERVER['REMOTE_ADDR'] == gethostbyname("carpus.homelinux.org") ||
		$_SERVER['REMOTE_ADDR'] == gethostbyname("randallmade.dyndns.org") 
			){
		$prefix = str_replace("http://", "https://", $prefix);
		$results = $results . "<a href='$prefix/admin/'>Administrate</a>\n";
	}
	
	//########### Development machine not set up for secure pages at this point  #####
	if($_SERVER['HTTP_HOST'] == "carpus.homelinux.org"){ 
		$results = str_replace("https://", "http://", $results);
	}
	
	$results = $results . "</div>\n";
//	$results = $results . $_SERVER['PHP_SELF'] . "<BR>\n";
	return $results;
}

function getToolbarPrefix(){
	$prefix = $prefix . $_SERVER['HTTP_HOST'];
	
//	echo "<HR>"; print_r($_SERVER); echo "<HR>";
		
	if($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org'){
		$secureLocation = false;
		$prefix = $prefix . "/rmkweb";
//		$prefix = $prefix . $_SERVER['HTTP_HOST'];
	}
	if($_SERVER['HTTP_HOST'] == '72.18.130.57'){
		$secureLocation = false;
		$prefix = $prefix . "/~uplzcvgw";
//		$prefix = $prefix . $_SERVER['HTTP_HOST'];
	}
	if(strstr($_SERVER['PHP_SELF'],"uplzcvgw") != FALSE){
		$secureLocation = false;
		$prefix = $prefix . "/~uplzcvgw";
	}
		
//	if($_SERVER['HTTP_HOST'] == 'server121.tchmachines.com'){
//		$secureLocation = false;
//		$prefix = $prefix . "/~uplzcvgw";
////		$prefix = $prefix . $_SERVER['HTTP_HOST'];
//	}
	if($_SERVER['HTTPS'] == 'on')
		$secureLocation = true;
		
	if($secureLocation)
		$prefix = "https://" . $prefix;
	else
		$prefix = "http://" . $prefix;
		
	return $prefix;
}

function getBaseImageDir(){
	if($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org')		return  "/rmkweb/images";
	if($_SERVER['HTTP_HOST'] == '72.18.130.57')				return  "/~uplzcvgw/images";
	return  getToolbarPrefix()."/images";
	
//	return "/images";
}

function footer(){
return "\n<div class='footer'>" .
		"<HR>\n" .
		"<B>Randall Made Knives</B><BR>\n" .
		"4857 South Orange Blossom Trail<BR>\n" .
		"Orlando, Florida 32839<BR>\n" .
		"Phone: 407-855-8075<BR>\n" .
		"Fax: 407-855-9054<BR>\n" .
		"<a href='" . getToolbarPrefix() . "/email.php?to=webmessages'>Send Us a message</a><BR>\n" .
		"</div class='footer'>\n\n";	
}

function getCurrPage(){
	$currPage=$_SERVER['PHP_SELF'];
	if(substr($currPage,0,5) == "/rmkweb/") $currPage = substr($currPage,7);
	if(substr($currPage,0,6) == "admin/") $currPage = substr($currPage,6); // strip the "admin" from the front
	if(substr($currPage,0,1) == "/") $currPage = substr($currPage,1);
	return $currPage;
}

function isDebugMachine(){
	if($_SERVER['REMOTE_ADDR'] =='70.118.199.240'){
		echo "<HR>DEBUG MACHINE<HR>";
		return true;
	}
	return ($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org');	
}

function isLocalAccess(){
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') return true;
	if(substr($_SERVER['REMOTE_ADDR'],0,10) == "192.168.1."){
		return true;
	}
	if($_SERVER['REMOTE_ADDR'] == gethostbyname("randallmade.dyndns.org")
	 ||	$_SERVER['REMOTE_ADDR'] == '67.8.255.165'
	 ||	$_SERVER['REMOTE_ADDR'] == gethostbyname("carpus.homelinux.org") 
	){
				return true;
			}
	
	return false;
}
function isDebugAccess(){
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') return true;
	
	return ($_SERVER['REMOTE_ADDR'] == '97.100.243.22'
		||	$_SERVER['REMOTE_ADDR'] == '67.8.252.151'
		|| $_SERVER['REMOTE_ADDR'] == '198.73.165.1'
	);
}
?>
