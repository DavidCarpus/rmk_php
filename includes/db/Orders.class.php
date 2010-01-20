<?php
include_once "db.php";

class Orders
{
	public $validationError;	
	
	public function customerOrderFormValidation($formValues){
		$valid=true;
		
		$requiredFields = array();
		if($formValues["ordertype"] == "Order"){
			$requiredFields = array("name", "email", "address1", "city", "state", "zip", "country", "phone",
									"cctype", "ccnumber", "ccexpire", "ccvcode", "ccname", );			
		}else{
			$requiredFields = array("name", "email", "address1", "city", "state", "zip", "country", "phone", "ordertype");
		}
		
		foreach ($requiredFields as $field){
			if($formValues[$field] == ""){$this->validationError .= "$field,"; $valid=false;continue;}			
		}	
		
		return $valid;
	}
	
	function search($searchValues){
		$filter=array();
		if($searchValues['requesttype'] > 0){
			$filter[] = " ordertype=" . $searchValues['requesttype'];
		}
		if($searchValues['status'] >= 0){
			$filter[] = " processed=" . $searchValues['status'];
		}
		if($searchValues['name'] != ''){
			$filter[] = " ( name LIKE '%" . $searchValues['name'] . "%' )";
		}
		if($searchValues['startdate'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['startdate']));
			$filter[] = " ( datesubmitted > '$date' )";
		}
		if($searchValues['enddate'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['enddate']));
			$filter[] = " ( datesubmitted < '$date')";
		}
		if(sizeof($filter) == 0) return;
		
		$queryFilter = " where " . $filter[0];
		for($filterIndex=1; $filterIndex < sizeof($filter); $filterIndex++)
		{
			$queryFilter .= " and " . $filter[$filterIndex];			
		}
		
		$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders";
		$query .= "$queryFilter";
		$query .= " order by datesubmitted DESC, orders_id DESC LIMIT " . MAX_EMAIL_LIST_LEN;
		
//		echo debugStatement("Search orders values:<br/>" . dumpDBRecord($searchValues));
//		echo debugStatement("Search orders:<br/>$query" );
		
		return getDbRecords($query);		
	}
	function updateStatus($id, $newStatus, $comment="")
	{
		$query = "";
		if($comment == ""){
			$query .= "update orders set processed = $newStatus where orders_id=$id";
		} else {
			$query .= "update orders set processed = $newStatus, comment = '$comment' where orders_id=$id";			
		}
//		echo debugStatement("updateStatus:<br/>$query" );
		executeSQL($query);
	}
	
	public function getUnprocessedCounts($requestTypeOptions)
	{
		$results = array();
		foreach ($requestTypeOptions as $reqType)
		{
			if($reqType['label'] != ""){
				$query = "Select count(*) as cnt from orders WHERE processed=0 AND ordertype=" . $reqType['id'];
				$record = getSingleDbRecord($query);
				$results[$reqType['label']] = $record['cnt'];
			} 	
		}
		return $results;
//		$query = "Select count(*) as cnt from orders WHERE $processed AND $ordertype";
//		$record = getSingleDbRecord($query);
//		return $record['cnt']; 
	}
	public function getSingleRequest($requestID)
	{
		$query = "Select *, UNIX_TIMESTAMP(datesubmitted) as submission_date from orders where orders_id=$requestID";
		return getSingleDbRecord($query);
	}

}

?>