<?php
include_once "order_validation.php";

function fieldDesc($field){
	$lookup = array('name'=>'Full Name','email'=>'Email Address','address1'=>'Billing Address', 'address2'=>'&nbsp;', 'address3'=>'&nbsp;',
					'city'=>'City','state'=>'State/Province', 'zip'=>'Zip/Postal Code', 'country'=>'Country', 
					'phone'=> 'Phone Number *','fax'=> 'Fax Number','shipaddress1'=> 'Shipping Address', 'ccvcode'=>'VCode (<i>Last 3 digits on signature line</i>)',
//					'shipaddress2'=> '&nbsp;', 'ordertype'=> "Interest",'qty'=> 'Quantity','catqty'=> 'Quantity of Catalogs',
					'shipaddress2'=> '(If other than Billing address)', 'shipaddress3'=> '&nbsp;', 'ordertype'=> "Interest",'catqty'=> 'Quantity of Catalogs',
					'model'=> 'Model','bladelength'=> 'Blade Length', 'note'=> 'Knife features, comments and questions',
					'cctype'=> 'Credit Card Type', 'ccnumber'=> 'Credit Card Number', 'ccexpire'=> 'Expiration Date','ccname'=> 'Name as it appears on card',
					'comment'=>'Comment');
	return $lookup[$field];
}
function fields(){
	return array('orders_id', 'processed', 'comment', 'name', 'email', 'address1', 'address2', 'address3', 'city', 'state', 'zip' , 'country', 
		'phone', 'fax', 'ordertype',
		'shipaddress1', 'shipaddress2', 'shipaddress3', 'orderType', 'qty', 'model', 'bladelength', 'note', 
		'cctype', 'ccnumber', 'ccvcode', 'ccexpire', 'ccname', 'datesubmitted', 'submission_date');
	
}

function displayErrors($errors){
	foreach($errors as $error){
		print "<div style='color:red'>";
		print $error . "<br />\n";
		print "</div>";
	}
}
function addDashesToCCNumber($ccnumber){
	$results="";
	$results=$results.substr($ccnumber,0,4)."-".substr($ccnumber,4,4) ."-";
	$results=$results.substr($ccnumber,8,4)."-".substr($ccnumber,12,4);
	return $results;
}
 
?>
