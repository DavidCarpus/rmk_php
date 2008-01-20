<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "includes/db.php";
include_once "includes/htmlHead.php";
include_once "includes/catalog.php";
?>

<html>
<?php echo headSegment(); ?>
<body>
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			<?php catalogProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
