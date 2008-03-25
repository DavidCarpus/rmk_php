<?php
/* * Created on Jan 1, 2006 */
session_start(); 
/* Created on Feb 4, 2006 */
include_once "../includes/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/catalog.php";
include_once "../includes/adminFunctions.php";

function getTimeString(){
	return trim( 
"
3/24 - 11
3/23 - 4
3/5 - 1
2/27 - 4
2/26 - 6
2/24 - 4
2/24 - 2
2/10 - 4
2/7 - 2
2/4 - 2
2/3 - 2
2/2 - 2
1/30 - 6
1/29 - 3
1/28 - 8
1/27 - 1
1/20 - 1
3/1 - 2
2/27 - 4
2/20 - 4
");

}

function getTable2(){
	$contents = array_reverse(getTimes("Times.txt"));

	$totalHrs=0;
	$rate=30;
	$totalPaid=0;
	$charges=0;
	
	foreach($contents as $row){
		$charges = $charges + ($row[1] * $rate);
		
		$greystart = $greyend = "";
		if($charges <= $totalPaid){
			$greystart = "<span style='background-color:grey'>";
			$greyend = "</span>";
		}
		
		$dates = $dates . $row[0] . "<BR>\n"; 
		$hours = $hours . $row[1] . "<BR>\n";
		$bill = $bill . ($row[1] * $rate) . "<BR>\n";
		$totalHrs = $totalHrs + $row[1];
		$totalHrDisplay = $totalHrDisplay . $greystart . $totalHrs. $greyend . "<BR>\n";
		$totalBill = $totalBill . $greystart . ($totalHrs* $rate). $greyend . "<BR>\n";
	}
	
	$style = 'display:block; width:90px; left:0px; position: relative; float:left; text-align: center;';	
	$results = $results . "<div id='timesheet'>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Date<BR>"; 
	$results = $results . $dates; 
	$results = $results . "</div>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Hours<BR>"; 
	$results = $results . $hours; 
	$results = $results . "</div>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Bill<BR>"; 
	$results = $results . $bill; 
	$results = $results . "</div>";
	
	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Total Hours<BR>"; 
	$results = $results . $totalHrDisplay; 
	$results = $results . "Paid<BR>"; 
	$results = $results . "Remaining<BR>"; 
	$results = $results . "</div>";
	
	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Total Bill<BR>"; 
	$results = $results . $totalBill; 
	$results = $results . $totalPaid . "<BR>";
	$results = $results . ($charges - $totalPaid);
	$results = $results . "</div>";
	
	$results = $results . "</div id='timesheet'>";
	return $results;  
}


function getTimes($file){
//	$originalContents = file_get_contents($file);
	$originalContents = getTimeString();
	$originalContents = split("\n", $originalContents);
	$contents = array();
	foreach($originalContents as $row){
		$row = split("-", $row);
		array_push($contents, $row);
	}
	return $contents;
}


if(!loggedIn()){
	session_destroy();
	header("Location: "."../");
}
?>

<LINK href="../Style.css" rel="stylesheet" type="text/css">

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php echo getTable2();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
