<?php
/* Created on Feb 8, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";
include_once INCLUDE_DIR. "CustomerReports.class.php";

include_once DB_INC_DIR. "db.php";
include_once DB_INC_DIR. "db_requests.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}
 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
 $formValues = getFormValues();
 $custReportsClass = new CustomerReports();
 
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK href="../CustomerReports.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../CustomerReportsPrint.css">	 

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 	
					echo $custReportsClass->invoice($formValues);
//					echo debugStatement(dumpDBRecord($formValues));;
			?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
