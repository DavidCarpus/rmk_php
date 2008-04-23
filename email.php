<?php session_start();
/* * Created on Mar 24, 2006 */

include_once "includes/htmlHead.php";
include_once "includes/email.php";
include_once "includes/orders.php";
include_once "includes/db/db_requests.php";
include_once "includes/db/db.php";
				
$parameters = array_merge($_POST, $_GET);
if(loggedIn() && emailedFromAdmin($parameters)){
	header("Location: ".adminOrderWebAddress($parameters));
//	debugStatement(adminOrderWebAddress($parameters));
	return;
}

?>
<LINK href="Style.css" rel="stylesheet" type="text/css">
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			<?php echo emailRequestProcessing();?>
		</div>
	</div>
	<?php echo footer();	?>
</div>


