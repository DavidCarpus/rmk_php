<?php
/* Created on Feb 16, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/htmlHead.php";

session_start();
if(!loggedIn()){
	$_SESSION['loginValidated'] = 0;
	session_destroy();
	header("Location: "."../");
}

function webCopyAdminProcessing(){
	$action = getHTMLValue('action');
	$section = getHTMLValue('section');
	if(sizeof($action) == 0)
		$action='list';
		
	switch($action){
		case 'list':
			echo listCopySections(getCopySections());
			break;
		case 'editcopy':
			echo editCopy($section);
			break;
		case 'save':
			$copy = getEntryFromPOST(getCopy_FieldList());
//			print_r($copy);
			saveCopy($copy);
			echo listCopySections(getCopySections());
			break;
		default:
			echo "webCopyAdminProcessing.<br>\n";
			echo "Unknown action: " . $action . "<br>";
			dumpPOST_GET();
	}
}

function getCopy_FieldList(){
	return array('webcopy_id', 'page', 'prefix', 'postfix');
}

function getPageCopy($section){
	return getSingleDbRecord("Select * from webcopy where page='$section'");
}

function saveCopy($copy){
	return saveRecord('webcopy', 'webcopy_id', $copy);
}

function editCopy($section){
	$copy = getPageCopy($section);
	
	$results = $results . "<H2>".getPageDescription($section)."</H2>";
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
	$results = $results . "<center>";
	$results = $results . textArea('prefix', 'Prefix', true, $copy['prefix'], true). "<BR>\n" ;
	$results = $results . textArea('postfix', 'Postfix', true, $copy['postfix'], true). "<BR>\n" ;
	$results = $results . hiddenField('page',$copy['page']);		
	$results = $results . hiddenField('page',$copy['page']);		
	$results = $results . hiddenField('action','save');		
	$results = $results . hiddenField('webcopy_id',$copy['webcopy_id']);		
	$results = $results . "<input style='color: red;' type='submit' name='submit' value='Submit' >\n" ;
	$results = $results . "</center>";
	$results = $results . "</form >";
	return $results;
}

function listCopySections($sections){
	foreach($sections as $section){
		$results = $results . 	getEditCopyLink($section) . "<BR>";
	}	
	return $results;
}
function getEditCopyLink($section){
	return "<a href='" . $_SERVER['PHP_SELF'] . "?action=editcopy&section=" . $section['page'] . "'>" . getPageDescription($section['page']) . "</a>";
}

function getCopySections(){
	return 	getDbRecords("Select distinct(page) from webcopy");
//	return array("order", "faq", "knifecare");
}

function getPageDescription($page){
	$translate = array("order"=> "Order Form", "catalog"=>"Catalog Request");
	$results = $translate[$page];
	if($results=="") 
		$results=$page;
	return $results;
}

?>
<html>
<?php echo headSegment("../Style.css"); ?>
<body>
<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php webCopyAdminProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
