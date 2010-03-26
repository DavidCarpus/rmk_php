<?php
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "Customers.class.php";
include_once FORMS_DIR. "Customer.class.php";

//session_start();

if (!authenticate()){
	session_destroy();
	return;
}

$custForm = new Customer();
$custClass = new Customers();


echo headSegments("RMK Dealers", array("../Style.css", "", "../DataEntry.css"), "../print.css");
?>

<body>

<?php echo "<script type='text/javascript' src='../includes/NewRMK.js?" . time() . "' ></script>"; ?>


<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("RMK"); ?>
		<div class="content">
			<?php 
				$dealers=$custClass->fetchDealers();
				echo $custForm->customerList($dealers);
			?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>