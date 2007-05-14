<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "includes/db.php";
include_once "includes/htmlHead.php";
?>
<LINK href="Style.css" rel="stylesheet" type="text/css">
<?php echo logo_header(""); ?>

 <div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		
		<div class="content">
			Randall Made knives are custom-designed for many uses. The models shown in this catalogue have been developed over a period of 68 years from personal experience, extensive research and the study of hundreds of designs submitted by individuals around the world requesting custom-made knives.
			<BR><BR>
			We have incorporated the best of many designs, and we believe that almost any need for a knife can be met with one of our 45 plus models.
			<BR><BR>
			
			<div class='floatleft'><img src='<?php echo getBaseImageDir(); ?>/3_5.jpg'><BR><i>Model #3-5&quot;  Hunter,  brass hilt, antique gold micarta handle, finger grips</i></div>
			
			Each of our models has been thoroughly field-tested and has proven to be properly shaped and designed for its particular use.
			<BR><BR>
			Occasionally we receive requests for longer, shorter, thicker or thinner blades and/or handles. In almost all cases the knife would be thrown out of balance or proportion, while our models have been carefully designed with all of these factors taken into consideration.
			<BR><BR>
			With that in mind, we strongly urge you to evaluate all our models -- and the virtually limitless number of option combinations available. 
		</div>
	</div>

	<?php echo footer(); ?>
</div>