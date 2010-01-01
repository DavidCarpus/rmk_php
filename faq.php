<?php
session_start(); 
/* Created on Feb 4, 2006 */
include_once "includes/db/db.php";
include_once "includes/htmlHead.php";


function listFAQ(){
	$results = $results . "<a name='index'></a>";
	$results = $results . "<B style='font-size:24; text-align:center; display:block;'>Frequently Asked Questions</B>";
	
	$faq = getDbRecords("Select * from faq");
	
	$results = $results . "<a name='index'></a>";
	foreach($faq as $question){
		$results = $results . localHref($question) . "<br />";
	}
	
	$results = $results . "<br /><br />";
	
	foreach($faq as $question){
		$results = $results . "<a name='" . $question['faq_id'] . "'></a>";
		$results = $results . "<br />";
		$results = $results . "<B>" . $question['question'] . "</B><br />";
		$results = $results . "<I>" . $question['answer'] . "</I>" . "<br />" . topLink() ;
	}
//	$results = $results .  "<a href='". $_SERVER['PHP_SELF']. "'>TOP</a>";
//	$results = $results .  topLink();
	return $results;
}

function topLink(){
	return  "<a href='". $_SERVER['PHP_SELF']. "'><IMG SRC='" . getBaseImageDir() . "/top.gif' WIDTH='33' HEIGHT='50' BORDER='0' title='Top'></a>";
//	return  "<a href='#index'><IMG SRC='" . getBaseImageDir() . "/top.gif' WIDTH='33' HEIGHT='50' BORDER='0' title='Top'></a>";
}

function localHref($question){
	return "<a href='#". $question['faq_id'] ."'>" . $question['question'] . "</a>";
//#anchorname
}


?>



<LINK href="Style.css" rel="stylesheet" type="text/css">
<?php echo logo_header(""); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo toolbar(); ?>
		<div class="content">
			<?php echo listFAQ();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
