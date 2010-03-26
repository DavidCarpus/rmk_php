<?php
/* Created on Feb 4, 2006 */

//if($locationPrefix == '/rmk')
	$fileDir = '/var/www/html/rmk/images/catalog';
//else
//	$fileDir = '/home/rmk/public_html';

$debugMachineRoot="/rmk";


function authenticate($failRedirect="../"){
	return true;
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="RMK Admin"');
	    header('HTTP/1.0 401 Unauthorized');
	    header("Location: ".$failRedirect);
//	    return false;
	    echo 'Text to send if user hits Cancel button';
	    exit;
	} else {
		$formValues=getFormValues();
//		echo debugStatement(dumpDBRecord($formValues));
		if (isset($formValues['logout']) && $formValues['logout'] > 0) {
			if(session_id() != ""){
				unset($_SESSION['session_id']);
				session_destroy();
			}
//			echo debugStatement(dumpDBRecord($_SERVER));
//			header('WWW-Authenticate: Basic realm="RMK Admin"');
//		    header('HTTP/1.0 401 Unauthorized');
//		    return false;
		} else {
			session_start();
			if(!isset($_SESSION['session_id'])){
				session_regenerate_id();
				$_SESSION['session_id'] = session_id();
			}
		}
//		    session_regenerate_id();
		//		    $_SESSION['session_id'] = session_id();
		return true;
//	    echo "<p>Hello {$_SERVER['PHP_AUTH_USER']}.</p>";
//	    echo "<p>You entered {$_SERVER['PHP_AUTH_PW']} as your password.</p>";
	}
//	header("Location: ".$failRedirect);
}

function logoutLink(){
	$currURL="http";
	if($_SERVER['HTTPS'])	$currURL='https';
	$currURL.="://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
//	return $currURL;
	if(! strrpos($currURL,"logout=1")){
		if(strrpos($currURL,"php?")){
			$currURL.= "&logout=1";
		} else {
			$currURL.= "?logout=1";
		}
	}
	return  "<a href='$currURL'>Logout</a>";;
//	 $_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_PW']
}




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
	if(! (isDebugMachine() || isDebugAccess() ) ) return;
	return "<div class='debug'><hr />". $statement . "<br /><hr /></div>";
}
function debugStatementHeaded($descStatement, $statement){
	if(! (isDebugMachine() || isDebugAccess() ) ) return;
	
	return "<div class='debug'><b>$descStatement</b><br /><hr />$statement<hr /></div>";
}

function dumpBackTrace(){
	global $g_lastDebug ;
	$debug  = getBackTrace();
	if ($g_lastDebug != $debug){
		echo debugStatement(debug_backtrace());
		$g_lastDebug = $debug ;
	}
}

function getBackTrace(){
	$trace = debug_backtrace();
	$stmt = "";
	$cnt=0;
	foreach($trace as $func){
		if($cnt++ > 0){
			//~ $stmt .=  print_r($func, true) . "<br />";
			$file = $func['file'] ;
			$file = str_replace("/var/www/html/Intranet/", "", $file);
			$stmt .=  $file ."(" .  $func['line'] . ")-" .$func['function'] ."<br />";
		}
	}
	return debugStatement($stmt);
}

function dumpDBRecord($record){
	$results = "";
	if(count($record) <= 0 || !is_array($record)){
		dumpBackTrace();
		return;
	}
	
	foreach(array_keys($record) as $key){
		$results .= "<b>$key</b> => ".$record[$key]."<br />\n";
	}
		
	return $results;
}

function dumpDBRecords($records){
	$results = "";
	foreach($records as $record){
		$results .= dumpDBRecord($record)."<br />\n";
	}
		
	return $results;
}

function dumpPOST_GET(){
	if(!isDebugMachine()) return;
	
	$results = "";
	$results .= "<div style='clear:both'>";
	foreach(array_keys($_POST) as $key){
		$value = $_POST[$key];
		$results .= "<B> $key </B> : $value <br />\n";
	}
	foreach(array_keys($_GET) as $key){
		$value = $_GET[$key];
		$results .= "<B> $key </B> : $value <br />\n";
	}
	$results .= "</div>";
	return $results;
}
function checkbox($name, $label, $required=false, $value=''){
	if($value == 'on') $checked='checked="checked"';
	if($value == 1) $checked='checked="checked"';
	if($value == 0) $checked='';
	
	if($required)
		return "<input type='checkbox' id='$name'  name='$name' $checked />";
	else
//		return "<label for='$name' >$label</label><input type='checkbox' id='$name' name='$name' $checked>";
		return "<input type='checkbox' id='$name' name='$name' $checked />";
		
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
	$results = "";
	if($label != '')
		$results .= "<label for='$name' >$label</label>";
	$results .= "<select size='1' name='$name'";
	if($autosubmit){
		$results .= " onChange='submit()' ";
	}
	$results = $results.">";
	if($autosubmit){
		$results = $results."<option value='0' ></option>";
	}
		
	if(count($values) > 0){
		foreach($values as $value){
//			print_r($value);
//			print "<br />";
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
	$printStyle = str_replace(".css", "_Print.css", $stylesheet);
	return 	"<head><meta http-equiv='Content-Language' content='en' />" .
			"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />" .
			"<LINK href='$stylesheet' rel='stylesheet' type='text/css'></head>\n".
			"<LINK href='$printStyle' media='print' rel='stylesheet' type='text/css'>".
			"<meta http-equiv='Content-type' content='text/html;charset=UTF-8' /></head>\n"
			;
}
function headSegments($title="rmk", $stylesheets=array("Style.css"), $printStyle){
	
	$results="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' " . 
			"'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n".
			"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n" .
			"<head><meta http-equiv='Content-Language' content='en' />\n" .
			"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />\n";
	
	$results .=	"<title>$title</title>\n";
	
	foreach ($stylesheets as $style)
	{
			$results .= "<link href='$style' rel='stylesheet' type='text/css' />\n";
	}
	$results .=	"<link rel='stylesheet' type='text/css'	 media='print' href='$printStyle' />\n";
	
	$results .=	"<meta http-equiv='Content-type' content='text/html;charset=UTF-8' />\n";
	$results .=	"</head>\n";
	return $results;
	
//		$printStyle = str_replace(".css", "_Print.css", $stylesheet);
//	return 	"<head><meta http-equiv='Content-Language' content='en' />" .
//			"<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />" .
//			"<LINK href='$stylesheet' rel='stylesheet' type='text/css'></head>\n".
//			"<LINK href='$printStyle' media='print' rel='stylesheet' type='text/css'>".
//			"<meta http-equiv='Content-type' content='text/html;charset=UTF-8' /></head>\n"
//			;
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
		$results = $results .  "<img align='top' src='$image3' alt='globe' />";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
//		$results = $results .  "</div>\n";
		
		$results = $results .  "<img align='top' src='$image1'  alt='logo' />";
//		$results = $results .  "&reg;<SMALL><SUP>TM</SUP></SMALL>";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		$results = $results .  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ";
		
//		$results = $results .  "<img src='$image3'>";
		$results = $results .  "</div>\n";

		

	return $results;
}
function adminToolbar($currMenu){
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
				array('rmk.php', 'RMK'),
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
		if(strtolower($currMenu)==strtolower($option[1]))
			$selectedStyle="id='selected'";
			
		$results = $results . "<a $selectedStyle href='$realprefix/admin/" . $option[0] . "'>" . $option[1] . "</a>\n";
	}
//	if(isset($_SERVER['PHP_AUTH_USER']) && ! empty($_SESSION['session_id'])){
////		$results .= logoutLink();
//	} else{
//		$results .= "<a href='$prefix/admin/'>LOGIN</a>\n";
//	}

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
//	$menu = array(
//				array('knife_list.php', 'Knife List'),
//				array('view_invoice.php', 'View Order'),
//				array('../index.php', 'Home'),
//				);
	$menu = array(
				array('.', 'Knife List'),
				array('./index.php?invoicesearch', 'View Order'),
				array('../index.php', 'Home'),
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
	if($_SERVER['HTTP_HOST'] == "carpus.homelinux.org" ||
			$_SERVER['HTTP_HOST'] == "localhost"){ 
		$results = str_replace("https://", "http://", $results);
	}
		
	$results = $results . "</div>\n";

	return $results;
}


function toolbar($currentMenu){
	$prefix = getToolbarPrefix();
	$results ="";

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
				array('order/payments.php', 'Payment Form'),
//				array('admin/', 'Administrate'),
				array('index.php', 'Home'),
				);
	$currPage=getCurrPage();
	
	$results .=  "\n<div class='leftnavigation'>\n";
//	$results = $results . "<HR>Test<HR>";
//	$results = $results . $currPage;
	
	
	//~ $debugMachineRoot
	foreach($menu as $option){
		$realprefix = $prefix;
		if($option[0] == 'order/index.php' || 
			$option[0] == 'order/payments.php'  || 
			$option[0] == 'catalogrequest.php'
			){
			$realprefix = str_replace("http://", "https://", $realprefix);
		}
		$selectedStyle="";
		if(strtolower($currentMenu)==strtolower($option[1]))
			$selectedStyle="id='selected'";
		
		$results = $results . "<a $selectedStyle href='$realprefix/" . $option[0] . "'>" . $option[1] . "</a>\n";
	}
	if(isDebugMachine()  
		|| isLocalAccess()
		|| $_SERVER['REMOTE_ADDR'] == gethostbyname("randallmade.dyndns.org") 
			){
		$prefix = str_replace("http://", "https://", $prefix);
		$results = $results . "<a href='$prefix/admin/'>Administrate</a>\n";
		$results = $results . "<a href='$prefix/shop/'>Shop</a>\n";
	}
	
	//########### Development machine not set up for secure pages at this point  #####
	if(isDebugMachine()){ 
		$results = str_replace("https://", "http://", $results);
	}
	
	$results = $results . "</div>\n";
//	$results = $results . $_SERVER['PHP_SELF'] . "<br />\n";
	return $results;
}

function getToolbarPrefix(){
	global $debugMachineRoot;
	$prefix = $_SERVER['HTTP_HOST'];
			
	if(isDebugMachine() || isCarpusServer() || isCarpusLaptop() ){
		$secureLocation = false;
		$prefix = $prefix . $debugMachineRoot;
	}
//	if(isShopServer() ){
//		$secureLocation = true;
//		$prefix = $prefix . "/rmk/";
//	}
//	if(isShopTestServer() ){
//		$secureLocation = true;
//		$prefix = $prefix . "/testrmk/";
//	}
	if($_SERVER['HTTP_HOST'] == '72.18.130.57'){
		$secureLocation = false;
		$prefix = $prefix . "/~uplzcvgw";
//		$prefix = $prefix . $_SERVER['HTTP_HOST'];
	}
	if(strstr($_SERVER['PHP_SELF'],"uplzcvgw") != FALSE){
		$secureLocation = false;
		$prefix = $prefix . "/~uplzcvgw";
	}
	if(strstr($_SERVER['PHP_SELF'],"/test/") != FALSE){
		$secureLocation = false;
		$prefix = $prefix . "/test";
	}
	if(isShopServer() && strstr($_SERVER['PHP_SELF'],"/rmk/") != FALSE){
		$secureLocation = false;
		$prefix = $prefix . "/rmk";
	}
	
	if(array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on')
		$secureLocation = true;
		
	if($secureLocation)
		$prefix = "https://" . $prefix;
	else
		$prefix = "http://" . $prefix;
		
	return $prefix;
}

function getImagePath($fileName){
	return getBaseImageDir() . "/$fileName";
}

function getBaseImageDir(){
	if(isCarpusServer()){
		return  "/rmk/images";
	}
//	if($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org')		return  "/rmk/images";
//	if($_SERVER['HTTP_HOST'] == 'localhost')		return  "/rmk/images";
//	if($_SERVER['HTTP_HOST'] == 'localhost')		return  "/rmk/images";
//	if($_SERVER['SERVER_ADDR'] == '192.168.1.99')		return  "/rmk/images";
	if($_SERVER['SERVER_ADDR'] == '192.168.1.101')		return  "/images";
	if($_SERVER['HTTP_HOST'] == '72.18.130.57')				return  "/~uplzcvgw/images";
	return  getToolbarPrefix()."/images";
	
//	return "/images";
}

function footer(){
return "\n<div class='footer'>" .
		"<hr />\n" .
		"<b>Randall Made Knives</b><br />\n" .
		"4857 South Orange Blossom Trail<br />\n" .
		"Orlando, Florida 32839<br />\n" .
		"Phone: 407-855-8075<br />\n" .
		"Fax: 407-855-9054<br />\n" .
		"<a href='" . getToolbarPrefix() . "/sendMessage.php'>Send Us a message</a><br />\n" .
		"</div>";	
}

function getCurrPage(){
	$currPage=$_SERVER['PHP_SELF'];
	if(substr($currPage,0,5) == "/rmkweb/") $currPage = substr($currPage,7);
	if(substr($currPage,0,6) == "admin/") $currPage = substr($currPage,6); // strip the "admin" from the front
	if(substr($currPage,0,1) == "/") $currPage = substr($currPage,1);
	return $currPage;
}

function isDebugMachine(){
//	var_dump($_SERVER);
	$address = '192.168.1.90';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
//		echo "<HR>DEBUG MACHINE<HR>";
		return true;
	}
//		return true;
	
	//~ if($_SERVER['REMOTE_ADDR'] =='70.118.199.240'){
		//~ echo "<HR>DEBUG MACHINE<HR>";
		//~ return true;
	//~ }
	return ($_SERVER['HTTP_HOST'] == 'carpus.homelinux.org'
			|| $_SERVER['HTTP_HOST'] == "localhost");	
}

function isShopTestServer(){
	$currPage=$_SERVER['PHP_SELF'];
	if(strrpos($currPage,"testrmk") > 0) return true;
	return false;
}

$carpusServer=isCarpusServer();
$shopServer=isShopServer();
$shopTestServer=isShopTestServer();

function isLocalAccess(){
	if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') return true;
	if(substr($_SERVER['REMOTE_ADDR'],0,10) == "192.168.1."){
		return true;
	}
	if($_SERVER['REMOTE_ADDR'] == gethostbyname("randallmade.dyndns.org")
	 ||	$_SERVER['REMOTE_ADDR'] == gethostbyname("carpus.homelinux.org") 
	 ||	$_SERVER['REMOTE_ADDR'] == gethostbyname("grandall.dyndns.org") 
	 ){
//	 ||	$_SERVER['REMOTE_ADDR'] == '67.8.255.165'
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

	function creditCardBlockForLetters(){
		$block = "";
		$block .= str_repeat(" ", 10) . "If payment by credit card:  Visa, Mastercard, Discover" . "\n";
		$block .= "\n";
		$block .= str_repeat(" ", 10) . "Card number:\t" . "_______________________________" . "\n";
		$block .= str_repeat(" ", 10) . "'V' Code (numbers on signature line):\t" . "__________" . "\n";
		$block .= str_repeat(" ", 10) . "Expiration date:\t" . "__________" . "\n";
		$block .= str_repeat(" ", 10) . "Name as appears on credit card:\t" . "______________________________" . "\n";
		$block .= str_repeat(" ", 10) . "Billing Address:\t" . "______________________________" . "\n";
		$block .= str_repeat(" ", 36)  . "______________________________" . "\n";
		$block .= str_repeat(" ", 36)  . "______________________________" . "\n";
		$block .= str_repeat(" ", 10) . "Signature:\t" . "______________________________" . "\n";
		
		return $block;
	}

   function substitureLetterFields($letter, $fields)
   {
   		foreach (array("first_name"=>"FirstName", "last_name"=>"LastName", "reserved_spaces"=>"Quantity", 
   			"scheduled_ship_date"=>"EstShip", "invoice"=>"Invoice", "due"=>"Due", "ordered_date"=>"DateOrdered") 
   			as $textField=>$dataField)
   		{
   			$data = $fields[$dataField];
   			if($dataField == "Due") $data = "$" . number_format($fields['Due'] ,2);
			$letter = str_replace("[[$textField]]", $data, $letter);
   		}
		// Address Block substitution
		$address = "";
		if(strlen($fields['Address0']) > 0) $address .= $fields['Address0'] . "\n";
		if(strlen($fields['Address1']) > 0) $address .= $fields['Address1'] . "\n";
		if(strlen($fields['Address2']) > 0) $address .= $fields['Address2'] . "\n";
		$address .= $fields['City'] . ", " . $fields['State'] . " " . $fields['Zip'];
//		if(strlen($fields['Country']) > 0) 
		$address .= "\n". $fields['Country'];
		$letter = str_replace("[[address_block]]", $address, $letter);		
		
		// Date(s) substitution
		$letter = str_replace("[[curr_date]]", date("F j, Y" ), $letter);
		
		$holdDate = strtotime( $fields['EstShip'] . " +30 days");		
		$letter = str_replace("[[hold_date]]", date("F j, Y", $holdDate), $letter);


		$letter = str_replace("[[cc_payment_block]]", creditCardBlockForLetters(), $letter);
		
//		$letter = str_replace("\n", "<br /> ", $letter);
		return $letter;
   }
?>
