<?php
include_once "Base.class.php";

class ShopForms extends Base
{
	function __construct() {
//       print "In constructor\n";
       $this->name = "MyDestructableClass";
   }
   
	public function orderSearchForm($request){
		global $sortOptions;
		$results="";
		$results .=  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
		$results .=  textField('invoice_num', fieldDesc('invoice_num'), false, $request['invoice_num']). "<BR>\n" ;
		$results .= textField('lastname', fieldDesc('lastname'), false, $request['lastname']). "<BR>\n" ;
		$sortField="sortby";
		$request[$sortField];
		$values="";
		foreach($sortOptions as $id=>$sql){
			$selection = array("id"=>$id, 'label'=>$id);
			$values[] = $selection;
		}
		$results .= selection($sortField, $values, "Sort By", $selected=$request[$sortField], $autosubmit=false);
		$results .= "<center><input class='btn' type='submit' name='submit' value='Search' ></center>\n" ;
		$results .=  "</form>";
		return $results;
	}
	
}
?>