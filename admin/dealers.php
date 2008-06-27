<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once FORMS_DIR. "Customer.class.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

$custForm = new Customer();
$custClass = new Customers();

 echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "'></SCRIPT>";
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">
<LINK rel="stylesheet" type="text/css"	 media="print" href="../print.css">	 
<LINK href="../DataEntry.css" rel="stylesheet" media='screen' type="text/css">


<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php 
				$dealers=$custClass->fetchDealers();
				echo $custForm->customerList($dealers);
			?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>