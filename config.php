<?php
$system="";
$address="12.153.188.121";

foreach (array('192.168.1.99', '192.168.1.90', 'carpus.homelinux.org') as $address) {
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$system="CARPUS_SERVER";
		break;
	}
}
// , '12.153.188.121' // Hilton Garden Inn 'external' address for HTMLValidator
foreach (array('192.168.1.3', '127.0.0.1', 'localhost') as $address) {
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$system="CARPUS_LAPTOP";
		break;
	}
}

foreach (array('192.168.1.110') as $address) {
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$system="SHOP_SERVER";
	}
	break;
}
foreach (array('208.76.82.94') as $address) {
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		$system="LIVE_SERVER";
	}
	break;
}

if($system=="" && ($_SERVER['HTTP_HOST'] == 'localhost')){
	$system="CARPUS_LAPTOP";
}

function isCarpusServer(){
	global $system;
	return $system=="CARPUS_SERVER";
}
function isCarpusLaptop(){
	global $system;
	return $system=="CARPUS_LAPTOP";
	}
function isShopServer(){
	global $system;
	return $system=="SHOP_SERVER";
}
function isLiveServer(){
	global $system;
	return $system=="LIVE_SERVER";
}
//echo $system;
if(isShopServer()){
define("INCLUDE_DIR", "/var/www/html/test/includes/");
define("FORMS_DIR", "/var/www/html/test/includes/forms/");
define("DB_INC_DIR", "/var/www/html/test/includes/db/");
define("PDF_FONT_DIR", "/var/www/html/test/includes/pdfCreator/fonts/");
define("BASE_IMG_URL", "/test/images/");

} else if(isCarpusServer() || isCarpusLaptop() ){
define("INCLUDE_DIR", "/var/www/rmk/includes/");
define("FORMS_DIR", "/var/www/rmk/includes/forms/");
define("DB_INC_DIR", "/var/www/rmk/includes/db/");
define("PDF_FONT_DIR", "/var/www/rmk/includes/pdfCreator/fonts/");
define("BASE_IMG_URL", "/rmk/images/");

} else if(isLiveServer() ){
define("INCLUDE_DIR", "/home/uplzcvgw/public_html/includes/");
define("FORMS_DIR", "/home/uplzcvgw/public_html/includes/forms/");
define("DB_INC_DIR", "/home/uplzcvgw/public_html/includes/db/");
define("PDF_FONT_DIR", "/home/uplzcvgw/public_html/includes/pdfCreator/fonts/");
define("BASE_IMG_URL", "/home/uplzcvgw/public_html/images/");

} else{
	echo "Unknown system config: " . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['HTTP_HOST'];
	exit;
}

define("MAX_EMAIL_LIST_LEN", 90);
define("BASE_IMG_FILEPATH", "/var/www/html/test/images/catalog");
?>
