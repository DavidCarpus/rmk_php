<?php
function isCarpusServer(){
	$address = '192.168.1.99';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		return true;
	}
	$address = '192.168.1.90';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		return true;
	}
	$address = 'carpus.homelinux.org';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		return true;
	}
	return false;
}
function isShopServer(){
	$address = '192.168.1.110';
	if(substr($_SERVER['SERVER_ADDR'] ,0,strlen($address)) == $address ){
		return true;
	}
	return false;
}
if(isShopServer()){
define("INCLUDE_DIR", "/var/www/html/test/includes/");
define("FORMS_DIR", "/var/www/html/test/includes/forms/");
define("DB_INC_DIR", "/var/www/html/test/includes/db/");
define("PDF_FONT_DIR", "/var/www/html/test/includes/pdfCreator/fonts/");

} else if(isCarpusServer()){
define("INCLUDE_DIR", "/var/www/rmk/includes/");
define("FORMS_DIR", "/var/www/rmk/includes/forms/");
define("DB_INC_DIR", "/var/www/rmk/includes/db/");
define("PDF_FONT_DIR", "/var/www/rmk/includes/pdfCreator/fonts/");
	
} else{
	
	
}

define("MAX_EMAIL_LIST_LEN", 90);
define("BASE_IMG_URL", "/test/images/");
define("BASE_IMG_FILEPATH", "/var/www/html/test/images/catalog");
?>