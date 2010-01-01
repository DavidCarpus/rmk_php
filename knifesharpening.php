<?php
/* Created on Feb 12, 2006 */
session_start(); 

include_once "includes/db/db.php";
include_once "includes/htmlHead.php";
?>
<html>
<?php echo headSegment(); ?>
<body>
<?php echo logo_header(""); ?>

<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			Each Randall Made knife is ground carefully by hand, and a section of knife blade when it leaves the grinder looks something like Figure 1.
			<br /><br />
			The edge placed on the knife by the grinder, however, is useless for ordinary work. It is too fine and would break down easily. It may be strengthened by beveling the edge slightly on each side, as shown in Figure 2. (Bevels are exaggerated in the drawing to make them visible.) The beveled edge, produced with a hone, serves two purposes: It stiffens and sharpens the edge.
			<br /><br />
			<img width=411 height=63 class='centered' src='images/knifedrawing1.gif'>
			<br />
			To keep your knife sharp use the following procedures:
			<br /><br />
			We recommend using two hones, one with a medium or coarse grit on which to start the honing process and the other with a fine grit for finishing.
			<br /><br />
			<img src="images/knife_drawing2.gif" class='floatleft'  height="180" width="119">
			First, put a few drops of kerosene, machine oil or saliva on the hone and lay the blade diagonally upon it, as in Figure 3.
			<br /><br />
			Now raise the side of the blade to an angle of about 20 degrees with the surface of the hone, as in Figure 4. Keeping the edge of the blade to the hone and the side of the blade away from it at the 20 degree angle, sweep the edge acrose the hone, holding the diagonal position and sharpening from hilt to point in one stroke, as shown in Figure 5.
			<br /><br />
			Turn the blade over and repeat the operation, alternating one stroke at a time on each side. Use even, sweeping strokes and lessen the pressure as the edge is restored.
			<br /><br />
			It is essential to keep the side of the blade at the same angle to the surface of the hone--on both sides of the blade--because if it varies you won't get a good edge.
			<br /><br />
			Remove any "wire edges" by giving the blade a few light, final sweeps across the hone on each side with the blade held at a high angle of about 60 degrees.
			<br /><br />
			On an extremely dull blade with a thickened edge, place the blade flat on a coarse hone and restore the original blade bevels (Figure 1); then attain the final cutting edge (Figure 2) with a fine-grit hone.
			<br /><br />
			Bear In Mind: Edge-holding ability and keenness of edge do not exactly go hand in hand, but there can be a happy medium. The finer (thinner) the V towards the cutting edge, the less the edge-holding ability of the steel. If you expect to do heavy-duty cutting, have this V thicker than that for cutting flesh, skin, etc.
			<br /><br />
			Please remember that a coarser hone removes more metal, shortening the life of the knife. For the same reason, we don't advise using power equipment to sharpen your knife. 
			<br />
			<img  height="123" width="408"  class='centered'  src='images/knifedrawing3.gif'>
			<br />
		</div>
	</div>
	<?php echo footer(); ?>
</div>
	
</body>
</html>