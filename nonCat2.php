<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "includes/db.php";
include_once "includes/htmlHead.php";
?>
<LINK href="Style.css" rel="stylesheet" type="text/css">

<style type="text/css">

#imap {display:block; width:800px; height:600px; background:url(images/catalog/cat2-big.jpg) no-repeat; position:relative; margin:0px 0 0px 0px;}

#imap a#titlex {display:block; width:400px; height:0; padding-top:240px; overflow:hidden; position:absolute; left:0; top:0; 
	background:transparent url(masters/small.jpg) no-repeat 400px 400px; cursor:default;}
* html #imap a#titlex {height:240px; height:0;}

#imap a#titlex:hover {background-position: 0 0; z-index:10;}
<?php
//----------     1    2    3    4   5     6    7    8    9   10   11   12   13   14   15   16
$left =  array(170, 160, 130, 120, 90,  50,   30, 470, 450, 430, 400, 370, 350, 320, 290, 270);
$top =   array( 85, 160, 230, 290, 360, 430, 500,  70, 125, 180, 240, 300, 360, 420, 490, 570);
$desc = array("<BR><P style=' margin:0px 20px 20px 20px;''><B>#12-9&quot; #14 grind Sportsman&rsquo;s Bowie</B> 9&quot; blade with #14 grind, 1-5/8&quot; wide, of &frac14;&quot; stock (Stainless shown).  Top cutting edge sharpened (approx. 3-&frac34;&quot;).  5&quot; (approx.) leather handle, brass lugged hilt and duralumin butt cap.  Made to meet the demand for a heavy-duty sporting knife of the Bowie type.  This Bowie available with #25 handle.   (#25 handle, nickel silver forward curved hilt with duralumin butt cap shown.)  Supplied with Model C sheath. (Wt. 18-20 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#16-7&quot;Special Fighter</B>	7&quot;blade of &frac14;&quot;stainless.  Top cutting edge sharpened approx. 3 inches.  (Model #1 blade) Extra heavy 7/8&quot;wide tang runs through channel in handle.  Brass #1 double hilt.  Approx. 4 &frac12;&quot;handle of black micarta.  (option: Green micarta)   Wrist thong.  (Note-standard with finger grip handle; single finger grip and border patrol available at additional charge.) Supplied with Model A sheath. (Wt. 14 oz.)#16-7&quot;Special Fighter</P>",	
				"<P style=' margin:0px 20px 20px 20px;''><B>Trailblazer</B> 5 &frac34;&quot; blade of &frac14;&quot; stock.    Brass single hilt.   New 13-spacer configuration includes red micarta sections,    stag handle,   brass 1/16&quot; butt plate.  An ideal all-around  heavy-duty sportsman&rsquo;s knife.    Options available: stainless blade, nickel silver hilt and butt plate,   or #25 handle configuration with single brass hilt  or single nickel silver hilt.   Supplied with Model A sheath.  (Wt. 9-11 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Combat Companion </B>5&quot; blade of &frac14;&quot; stock. (Stainless shown.) Top cutting edge sharpened.  Brass double hilt.   4 &frac12;&quot; leather handle.  Duralumin butt cap.  A light-weight military design knife. Supplied with Model A sheath. (Wt. 9 oz.</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Combat Companion</B> 5&quot; blade of &frac14;&quot; stock.  Top cutting edge sharpened.  Brass double hilt.   (Shown with black micarta, commando-shaped handle.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Denmark Special</B> 4&frac12;&quot; blade of 3/16&quot; stock. Raised point design. (Stainless shown.)  Brass hilt.  Approx. 4&frac12;&quot; stag handle.   (Brass/red spacers shown.)  Excellent small hunting and fishing knife.   Supplied with Model B sheath.  (Wt. 5.5 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#8-4&quot; Old Style Trout and Bird</B> 4&quot; blade of 3/16&quot; stock.  (Stainless shown.)  Clip point blade.  Top cutting edge sharpened.  Reduced brass hilt.  (Reduced nickel silver hilt shown.)  4&frac14;&quot; stag handle.  Supplied with Model B sheath.  (Wt. 4-5 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Fireman Special</B> 4&quot; blade of &frac14;&quot; stainless.  Extra-heavy beveled blade.  Thumb notches.  Brass single hilt.  Extra-heavy 7/8&quot; wide tang runs through channel in handle.  Approx. 4&frac12;&quot; handle of black micarta.  Wrist thong.  (Note-standard with finger grip handle; single finger grip and border patrol available at additional charge.) Supplied with high-ride Model B sheath. (Wt. 11 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#26-4&quot; Pathfinder</B> 4&quot; drop point blade of 3/16&quot; stock. (Stainless shown.) Approx. 4 3/8&quot; handle length. (Photo illustrates #25 handle configuration of  brass hilt, leather/stag handle with brass/black  spacers and brass butt cap).  (Butt cap--rounded shown.) Nickel silver hilt and duralumin butt cap available upon request.  Supplied with cover sheath.  (Wt. 6 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#2-4&quot; Stiletto</B> 4&quot; blade of 3/16&quot; stock. (Stainless shown.) Brass double hilt.  Approx. 4&quot; leather handle. Duralumin butt cap.  (Brass/black spacers, concave walnut handle and brass butt cap-domed shown).  Supplied with Model B sheath. (Wt. 4 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#8-4&quot; Trout and Bird</B> 4&quot; blade of 3/16&quot; stock. (Stainless shown.) Top cutting edge sharpened approximately 2 inches.  4 3/8&quot;-5/8&quot; leather handle.    Especially suitable for small fish and feathered game.  Supplied with Model B sheath. (Wt. 4</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>GTR Special</B> 3 &frac12;&quot; blade of 3/16&quot; stainless.  Brass hilt.  Approx. 4&quot; stag handle.  Suitable for small hunting and fishing needs.  Supplied with Model B sheath.   (Wt. 4 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Gambler</B> 4&quot; blade of 3/16&quot; stainless.  Top cutting edge sharpened. Thumb notches. Reduced-only nickel silver hilt.  Approx. 3 &frac34;&quot; concave black micarta handle.  Supplied with Concealment sheath. (Wt. 2 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#11-3 &frac14;&quot; Alaskan Skinner</B> 3&frac14;&quot; drop point blade.   1-1/8&quot; wide of 3/16&quot; stock.  (Stainless shown.)  4 &frac14;&quot; stag handle. Brass hilt. (Not available in leather.)  Top of blade in front of hilt notched for thumb placement. Excellent small skinning knife. Supplied with Model B sheath.  (Wt. 3&frac12;&quot; – 4&frac12;&quot; oz.)  </P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>Cattleman</B> 3 &frac14;&quot; blade of 3/16&quot; stock.  Sheep&rsquo;s foot design blade.  Thumb notches.  Reduced-only brass hilt.  Approx. 4&quot; flat-sided stag handle.   (Stainless is recommended for marine use.)   Supplied with cover sheath.  (Wt. 4 oz.)</P>",
				"<P style=' margin:0px 20px 20px 20px;''><B>#10-3&quot; Salt Fisherman</B> 3&quot; drop point of 1/8&quot; stainless.  Thumb notches. Fully hand ground.  Contoured 3&quot; black micarta handle. Wrist thong.  Also available with red micarta  or  rosewood.  Supplied with cover sheath.  (Wt. 3 oz.)</P>",
				)
//$width = array(250, 250, 250, 250, 250, 250, 250, 250, 250);
?>
#imap dd {position:absolute; padding:0; margin:0;}
<?php
for($i=0; $i< count($left); $i++ ) {
	echo "#imap #knife".($i+1)." {left:".$left[$i]."px; top:".$top[$i]."px; z-index:20;}\n";
}
?>
#imap a {display:block; width:158px; height:28px; text-decoration:none; z-index:20; }
#imap a em {display:none;}
#imap a span, #imap a:visited span {display:none;}
<?php
//echo "#imap";
for($i=0; $i< count($left); $i++ ) {
	echo "#imap a#link_knife".($i+1).":hover";
	if(($i+1) != count($left)) echo ",";
}
echo "{border:1px solid #fc0;}\n";
//#imap a:hover span {position:absolute; display:block; color:#666; width:330px; height:270px; 
//	line-height:1.8em; font-size:0.9em; text-align:justify;}
?>
#imap a:hover span {position:absolute; display:block; color:#134242; width:470px; line-height:1em; text-align:justify;  margin:90px 190px 90px 90px;}
<?php
for($i=0; $i< count($left); $i++ ) {
	// draw block text and image
	if($i==0)
		echo "#imap a#link_knife".($i+1).":hover span {left:". (- $left[$i]) . "px; top:".(-$top[$i]+60)."px; color:#000; background:#dbdbc0;}\n";
	else if($i<3)
		echo "#imap a#link_knife".($i+1).":hover span {left:". (- $left[$i]) . "px; top:".(-$top[$i]+160)."px; color:#000; background:#dbdbc0;}\n";
	else if($i<7)
		echo "#imap a#link_knife".($i+1).":hover span {left:". (150 - $left[$i]) . "px; top:".(-$top[$i])."px; color:#000; background:#dbdbc0;}\n";
	else
		echo "#imap a#link_knife".($i+1).":hover span {left:". (- ($left[$i]+105)) . "px; top:".(-$top[$i])."px; color:#000; background:#dbdbc0;}\n";
}
//#imap a span { margin:90px 90px 90px 90px;}
?>

#imap a:hover span img {float:left; border:20px solid #134242;}

#imap a span:first-line {font-weight:bold; font-style:italic;}
#info h3 {margin:0 0 0 75px; font-size:1.2em; font-weight:normal; font-family:georgia, "times new roman", serif; letter-spacing:0.1em; 
	padding-bottom:5px; border-bottom:1px solid #134242; width:750px;}
#info .para {width:300px; margin:0px 0px 0px 75px;}

</style>

<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
<?php
if (ereg( 'MSIE ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version)) {
	echo "<div class='contentIE'>";
} else{
	echo "<div class='content'>";
}
?>
<div class="imapblock">
<dl id="imap">
<dt><a id="titlex" href="#nogo">Non Catalog II knives</a></dt>
<?php
for($i=0; $i< count($left); $i++ ) {
//	echo "#imap #knife".($i+1)." {left:".$left[$i]."px; top:".$top[$i]."px; z-index:20;}\n";
	echo "<dd id='knife".($i+1)."' ><a id='link_knife".($i+1)."' title='Knife ".($i+1)."' href='noncat2_".($i+1).".php''>".
		"<em></em><span><img src='images/catalog/cat2-".($i+1).".jpg' alt='Knife ".($i+1)." - close-up' title='Knife ".($i+1)."' /><BR>".
		$desc[$i]."</span></a></dd>\n";
}
?>
</dl>
</div>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>