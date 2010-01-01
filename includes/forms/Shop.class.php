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
		$results .=  "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
		$results .=  "<P>" . $this->textField('invoice_num', fieldDesc('invoice_num'), false, $request['invoice_num']). "</P>\n" ;
		$results .= "<P>" . $this->textField('firstname', fieldDesc('firstname'), false, $request['firstname']). "</P>\n" ;
		$results .= "<P>" . $this->textField('lastname', fieldDesc('lastname'), false, $request['lastname']). "</P>\n" ;
		$sortField="sortby";
		$request[$sortField];
		$values="";
		foreach($sortOptions as $id=>$sql){
			$selection = array("id"=>$id, 'label'=>$id);
			$values[] = $selection;
		}
		$results .= selection($sortField, $values, "Sort By", $selected=$request[$sortField], $autosubmit=false);
		$results .= " Display older invoices" . $this->checkbox('searchOlder', fieldDesc('searchOlder'), false, $request['searchOlder']). "\n" ;
		$results .= "<center><input class='btn' type='submit' name='submit' value='Search' ></center>\n" ;
		$results .=  "</form>";
		return $results;
	}
	
}
?>