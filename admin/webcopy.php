<?php /* Created on Feb 16, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "db.php";

if (!authenticate("../")){
	return;
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
			echo "webCopyAdminProcessing.<br />\n";
			echo "Unknown action: " . $action . "<br />";
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
	if(strlen($copy['page']) == 0) $copy['page']=$section;
	
	$results = $results . "<H2>".getPageDescription($section)."</H2>";
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
	$results = $results . "<center>";
	$results = $results . textArea('prefix', 'Prefix', true, $copy['prefix'], true). "<br />\n" ;
	$results = $results . textArea('postfix', 'Postfix', true, $copy['postfix'], true). "<br />\n" ;
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
		$results = $results . 	getEditCopyLink($section) . "<br />";
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

echo headSegments("Web Copy/Text", array("../Style.css"), "../print.css");
?>

<body>


<?php echo "<script type='text/javascript' src='../includes/customer.js?" . time() . "' ></script>"; ?>

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("Copy/Text"); ?>
		<div class="content">
			<?php webCopyAdminProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
