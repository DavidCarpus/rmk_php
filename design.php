<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "config.php";

include_once "includes/db/db.php";
include_once "includes/htmlHead.php";

echo headSegments("The Design", array("Style.css"), "print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='includes/customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar("The Design"); ?>
		
		<div class="content">
			Randall Made knives are custom-designed for many uses. The models shown in this catalogue have been developed over a period of 68 years from personal experience, extensive research and the study of hundreds of designs submitted by individuals around the world requesting custom-made knives.
			<br /><br />
			We have incorporated the best of many designs, and we believe that almost any need for a knife can be met with one of our 45 plus models.
			<br /><br />
			
			<div class='floatleft'><img src='<?php echo getBaseImageDir(); ?>/designpic.jpg'><br /></div>
			
			Each of our models has been thoroughly field-tested and has proven to be properly shaped and designed for its particular use.
			<br /><br />
			Occasionally we receive requests for longer, shorter, thicker or thinner blades and/or handles. In almost all cases the knife would be thrown out of balance or proportion, while our models have been carefully designed with all of these factors taken into consideration.
			<br /><br />
			With that in mind, we strongly urge you to evaluate all our models -- and the virtually limitless number of option combinations available. 
		</div>
	</div>

	<?php echo footer(); ?>
</div>