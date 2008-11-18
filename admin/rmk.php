<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";

session_start();
//if(!loggedIn()){
//	$_SESSION['loginValidated'] = 0;
//	session_destroy();
//	header("Location: "."../");
//}

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
				<a href='search.php'>Search system</a>
				<BR>
				<a href='dealers.php'>Dealer List</a>
				<BR>
				<a href='Pricing.php'>Parts (Under Cunstruction)</a>
				<BR>
		</div>
	</div>
	<?php echo footer(); ?>
</div>