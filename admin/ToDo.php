<?php /* * Created on Dec 27, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once INCLUDE_DIR. "adminFunctions.php";
include_once DB_INC_DIR. "db.php";

if (!authenticate()){
	return;
}
echo headSegments("RMK To Do", array("../Style.css"), "../print.css");
?>

<body>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php  
				echo toDoPage();
	 		?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>


</body>
</html>