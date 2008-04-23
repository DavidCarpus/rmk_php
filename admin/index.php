<?php 
session_start(); 
/* * Created on Feb 4, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/adminFunctions.php";
if(loggedIn()){
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
				if(!loggedIn()){
		 			echo loginProcessing();
				} else{
					echo adminProcessing();
				}
	 		?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>