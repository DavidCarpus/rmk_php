<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "config.php";

include_once "includes/db/db.php";
include_once "includes/htmlHead.php";

// echo dumpServerVariables(); 
?>
<html>
<?php echo headSegment(); ?>
<body>


<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content" align=center>
			<?php isDebugMachine(); ?>
			<img src='<?php echo getBaseImageDir(); ?>/main.jpg'>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
