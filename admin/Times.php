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
1/20 - 1
3/1 - 2
2/27 - 4
2/20 - 4
2/15 - 1
2/14 - 1
2/13 - 1
2/12 - 3
2/7 - 0.5
2/6 - 2
2/5 - 1
2/2 - 2
2/1 - 4
1/29 - 5
1/25 - 1
1/24 - 2
1/23 - 2
1/19 - 1
12/04 - 3
12/03 - 1
11/28 - 1
11/27 - 2
11/26 - 2
11/2 - 2
10/24 - 3
10/23 - 1
10/18 - 1
10/16 - 3
10/12 - 1
10/5 - 1
10/6 - 1
9/25 - 3
9/22 - 2
8/11 - 2
7/13 - 2
7/12 - 1
6/15 - 1
6/1 ? - 2
5/20 - 5
5/16 - 3
5/11 - 1
5/5 - 1
4/30 - 2
4/29 - 2
4/27 - 1
4/23 - 2
4/17 - 2
4/15 - 5
4/10 - 1
4/5 - 2
4/4 - 1
4/3 - 1
3/29 - 0.5
3/28 - 2
3/27 - 2
3/26 - 2
3/25 - 1
3/25 - 2
3/24 - 3
3/23 - 3
2/12 - 4
2/11 - 6
2/9 - 4
2/8 - 1
2/7 - 3
2/6 - 6
2/5 - 4
2/4 - 8
");

}

function getTable2(){
	$contents = array_reverse(getTimes("Times.txt"));

	$totalHrs=0;
	$rate=30;
	$totalPaid=4260;
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