<?php

function isUSZipCode($zipCode)
{
	$zipCode = str_replace("-", "",$zipCode);
	$zipCode = trim($zipCode);
	if(strlen($zipCode) == 5 || strlen($zipCode) == 9 )	return is_numeric($zipCode);
	//	echo "UNK zip: $zipCode" . " " . strlen($zipcode) . "<BR>";
	return 0;
}

?>