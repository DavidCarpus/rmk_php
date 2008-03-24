<?php

session_start(); 
/* Created on Feb 4, 2006 */
include_once "../includes/db.php";
include_once "../includes/db_requests.php";
include_once "../includes/htmlHead.php";

include_once "../includes/shop2.php";
//~ if($carpusServer){ include_once "../includes/shop2.php";}
//~ else{ include_once "../includes/shop.php";}
?>


<html>
<?php echo headSegment("../ShopStyle.css"); ?>
<body>
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo shopToolbar(); ?>
		<div class="content">
			<?php echo knifeList(getFormValues()); ?>
		</div>
	</div>
</div>

