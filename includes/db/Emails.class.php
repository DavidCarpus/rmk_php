<?php
include_once "db.php";

class Emails
{
	var $validationError;
	
	function fetchEmails($startid=999999, $cnt=10){
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject from emails ";
		$query .= " where email_id <=$startid  order by datesubmitted DESC, email_id DESC LIMIT $cnt ";
		return getDbRecords($query);		
	}
	function fetchEmail($emailID){
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject, messagebody from emails ";
		$query .= " where email_id =$emailID";
//		echo debugStatement($query);
		return getDbRecords($query);		
	}
	
	function searchEmails($searchValues){
		$filter=array();
		if($searchValues['fromaddress'] != ''){
			$filter[] = " ( fromaddress LIKE '%" . $searchValues['fromaddress'] . "%' )";
		}
		if($searchValues['toaddress'] != ''){
			$filter[] = " ( toaddress LIKE '%" . $searchValues['toaddress'] . "%' )";
		}
		if($searchValues['messagesubject'] != ''){
			$filter[] = " ( messagesubject LIKE '%" . $searchValues['messagesubject'] . "%' )";
		}
		
		if($searchValues['start_datesubmitted'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['start_datesubmitted']));
			$filter[] = " ( datesubmitted > '$date' )";
		}
		if($searchValues['end_datesubmitted'] != ''){
			$date = date("Y-m-d H:i:s", strtotime($searchValues['end_datesubmitted']));
			$filter[] = " ( datesubmitted < '$date')";
		}
		if(sizeof($filter) == 0) return;
		
		$query = "Select email_id, fromaddress, toaddress, UNIX_TIMESTAMP(datesubmitted) as date_sent, messagesubject, messagebody from emails ";
		$query .= " where " . $filter[0];
		for($filterIndex=1; $filterIndex < sizeof($filter); $filterIndex++)
		{
			$query .= " and " . $filter[$filterIndex];			
		}
		$query .= " order by datesubmitted DESC, email_id DESC LIMIT " . MAX_EMAIL_LIST_LEN;
		
//		echo debugStatement("Search emails filter:<br/>" . dumpDBRecord($filter));
//		echo debugStatement("Search emails filter:<br/>$query" );
		return getDbRecords($query);		
	}
	
}
?>