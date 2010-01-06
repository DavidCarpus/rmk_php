<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "db.php";
include_once INCLUDE_DIR. "adminFunctions.php";

if (!authenticate()){
	header("Location: "."orders.php");
}

?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php  
				echo adminProcessing();
	 		?>
		</div>
	</div>
	<?php echo logoutLink() . " : " . $_SERVER['PHP_AUTH_USER']. " : " . $_SESSION['session_id']; echo footer(); ?>
</div>

