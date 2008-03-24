<?php

session_start(); 
/* Created on Feb 4, 2006 */
include_once "../includes/db.php";
include_once "../includes/db_requests.php";
include_once "../includes/htmlHead.php";
if($carpusServer){ 
	include_once "../includes/shop2.php";
}else{ 
	include_once "../includes/shop.php";
}
?>


<html>
<?php echo headSegment("../Style.css"); ?>
<body>
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo shopToolbar(); ?>
		<div class="content">
			<?php echo "Content"; ?>
		</div>
	</div>
</div>

