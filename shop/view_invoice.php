<?php

session_start(); 
/* Created on Feb 4, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/db/db_requests.php";
include_once "../includes/htmlHead.php";
include_once "../includes/shop.php";
?>


<html>
<?php echo headSegment("../ShopStyle.css"); ?>
<body>
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo shopToolbar(); ?>
		<div class="content">
			<?php echo orderDisplay(); ?>
		</div>
	</div>
</div>

