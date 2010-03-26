<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "config.php";

include_once "includes/db/db.php";
include_once "includes/htmlHead.php";

// echo dumpServerVariables(); 
echo headSegments("Randall Made Knives", array("Style.css"), "print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='includes/customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar("Home"); ?>
		<div class="content" align=center>
			<img src='<?php echo getBaseImageDir(); ?>/main.jpg'>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
