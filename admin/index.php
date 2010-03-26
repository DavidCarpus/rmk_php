<?php 
include_once "../config.php";

session_start(); 
/* * Created on Feb 4, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/adminFunctions.php";

if (!authenticate()){
	header("Location: "."orders.php");
}
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(""); ?>
		<div class="content">
			<?php  
				echo adminProcessing();
	 		?>
		</div>
	</div>
	<?php //echo logoutLink() . " : " . $_SERVER['PHP_AUTH_USER']. " : " . $_SESSION['session_id']; ?>
	</div>
	<?php echo footer(); ?>
</div>