<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "config.php";

include_once DB_INC_DIR. "db.php";
include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "catalog.php";

echo headSegments("Online Catalog", array("Style.css"), "print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='Customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar("Catalog"); ?>
		<div class="content">
			<?php catalogProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
