<?php
/* * Created on Jan 1, 2006 */
include_once "../config.php";

session_start(); 
/* Created on Feb 4, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/catalog.php";
include_once "../includes/adminFunctions.php";

$oldTime="

4/14 - 2
1/17 - 5
1/5 - 3
1/2 - 2
12/21 - 4
12/20 - 4
12/15 - 1
11/18 - 1
10/6 - 1
8/24 - 4
7/20 - 2
7/19 - 3
7/13 - 3
7/12 - 3
6/26 - 3
6/25 - 5
6/24 - 2
6/23 - 4
6/23 - 5
6/22 - 4
6/21 - 3
6/10 - 2
5/13 - 1
5/12 - 2
5/11 - 3
5/10 - 3
5/5 - 1
5/4 - 3
5/4 - 3
4/29 - 3
4/26 - 3
4/25 - 2
4/23 - 4
4/22 - 1
4/15 - 1
4/15 - 1
4/8 - 8";

function getTimeString(){
	return trim( 
"
12/30 - 2
1/20 - 4
2/15 - 2
2/16 - 2
2/21 - 3
3/18 - 4
3/19 - 8
3/20 - 4
3/21 - 5
3/25 - 2
3/29 - 1
3/30 - 1
5/3 - 1
5/4 - 2
5/23 - 2
5/24 - 3
5/26 - 1
7/12 - 4
7/13 - 4
7/14 - 1
7/15 - 1
7/16 - 4
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
		
		$dates = $dates . $row[0] . "<br />\n"; 
		$hours = $hours . $row[1] . "<br />\n";
		$bill = $bill . ($row[1] * $rate) . "<br />\n";
		$totalHrs = $totalHrs + $row[1];
		$totalHrDisplay = $totalHrDisplay . $greystart . $totalHrs. $greyend . "<br />\n";
		$totalBill = $totalBill . $greystart . ($totalHrs* $rate). $greyend . "<br />\n";
	}
	
	$style = 'display:block; width:90px; left:0px; position: relative; float:left; text-align: center;';	
	$results = $results . "<div id='timesheet'>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Date<br />"; 
	$results = $results . $dates; 
	$results = $results . "</div>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Hours<br />"; 
	$results = $results . $hours; 
	$results = $results . "</div>";

	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Bill<br />"; 
	$results = $results . $bill; 
	$results = $results . "</div>";
	
	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Total Hours<br />"; 
	$results = $results . $totalHrDisplay; 
	$results = $results . "Paid<br />"; 
	$results = $results . "Remaining<br />"; 
	$results = $results . "</div>";
	
	$results = $results . "<div style='" . $style . "'>"; 
	$results = $results . "Total Bill<br />"; 
	$results = $results . $totalBill; 
	$results = $results . $totalPaid . "<br />";
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

if (!authenticate()){
	header("Location: "."../");
}

?>

<LINK href="../Style.css" rel="stylesheet" type="text/css">

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("Billed Time"); ?>
		<div class="content">
			<?php echo getTable2();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
