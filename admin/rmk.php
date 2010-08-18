<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";


if (!authenticate()){
	return;
}

echo headSegments("RMK Order Managment System", array("../Style.css", "", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "' ></script>"; ?>


<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
				<a href='search.php'>Search system</a>
				<br />
				<a href='dealers.php'>Dealer List</a>
				<br />
				<a href='DealerSpecLetter.php'>Dealer Specification Letter</a>
				<br />
				<a href='BalanceDueLetter.php'>Balance Due Letter</a>
				<br />
				<a href='Pricing.php'>Models/Features</a>
		</div>
	</div>
	<?php //echo logoutLink() . " : " . $_SERVER['PHP_AUTH_USER']. " : " . $_SESSION['session_id']; ?> 
	<?php echo footer(); ?>
</div>
	
