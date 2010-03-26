<?php /* Created on Feb 11, 2006 */
include_once "../config.php";

include_once INCLUDE_DIR. "htmlHead.php";
include_once DB_INC_DIR. "db.php";

if (!authenticate("../")){
	return;
}

function faqAdminProcessing(){
	$action = getHTMLValue('action');
	if(sizeof($action) == 0)
		$action='list';
		
	switch($action){
		case 'list':
			$faq = fetchAllFAQ();
			echo listFAQ($faq);
			echo addFAQLink();
			break;
		case 'add':
			echo editFAQ(array());
			break;
		case 'delete':
			$id = getHTMLValue('id');
			$faq = getSingleDbRecord("Select * from faq where faq_id=$id");
			echo deleteValidationForm($faq);
			break;
		case 'save':
			$question = getEntryFromPOST(getFAQ_FieldList());
			saveFAQEntry($question);
			$faq = fetchAllFAQ();
			echo listFAQ($faq);
			echo addFAQLink();
			break;
		case 'edit';
			$id = getHTMLValue('id');
			$faq = getSingleDbRecord("Select * from faq where faq_id=$id");
			echo editFAQ($faq);
			break;
		case 'validatedeletion':
			$id = getHTMLValue('id');
			$model = getBasicSingleDbRecord('faq','faq_id',$id);
			deleteRecord('faq', 'faq_id', array('faq_id'=>$id));
			$faq = fetchAllFAQ();
			echo listFAQ($faq);
			echo addFAQLink();
			break;
		default:
			echo "Administrate Frequently Asked Questions.<br />\n";
			echo "Unknown action: " . $action . "<br />";
			dumpPOST_GET();
	}
}
function deleteValidationForm($faq){
	$results = $results .  "<center><h2> Confirm Deletion:</h2></center>";
	$results = $results .  "<center><h2>" . $faq['question'] . "</h2></center>";
	$results = $results .  "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
//			echo hiddenField('catalogcategories_id',$model['catalogcategories_id']);
	$results = $results .  hiddenField('action','validatedeletion');
	$results = $results .  hiddenField('id',$faq['faq_id']);
	$results = $results .  "<center><input type='submit' name='validatedelete' value='Confirm' ></center>" ;
	$results = $results .  "</form>\n";
		
	return $results;	
}

function fetchAllFAQ(){
	return 	getDbRecords("Select * from faq order by faq_id");
}

function getFAQ_FieldList(){
	return array("faq_id", 'question', 'answer');	
}
function saveFAQEntry($question){
	return saveRecord('faq', 'faq_id', $question);
}
function editFAQ($question){
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='post'>" ;
	$results = $results . "<center>";
	$results = $results . textArea('question', 'Question', true, $question['question'], true). "<br />\n" ;
	$results = $results . textArea('answer', 'Answer', true, $question['answer'], true). "<br />\n" ;
	$results = $results . hiddenField('faq_id',$question['faq_id']);		
	$results = $results . hiddenField('action','save');		
	$results = $results . "<input style='color: red;' type='submit' name='submit' value='Submit' >\n" ;
	$results = $results . "</center>";
	$results = $results . "</form >";
	return $results;
}

function listFAQ($faq){
//	if(count($faq) > 0)
//		$results = $results . "<br />";
	foreach($faq as $question){
		$results = $results . editFAQLink($question) . " - ";
		$results = $results . deleteFAQLink($question) . " - " . $question["question"] . "<br />";
	}
	return $results;
}

function addFAQLink(){
	return "<a href='" . $_SERVER['PHP_SELF'] . "?action=add'>Add New Question</a>";
}

function editFAQLink($question){
	return "<a href='" . $_SERVER['PHP_SELF'] . "?action=edit&amp;id=" . $question["faq_id"] . "'>Edit</a>";
}
function deleteFAQLink($question){
	return "<a href='" . $_SERVER['PHP_SELF'] . "?action=delete&amp;id=" . $question["faq_id"] . "'>Remove</a>";
}


echo headSegments("RMK FAQ Administration", array("../Style.css"), "../print.css");

?>

<body>
<?php echo logo_header("admin"); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar("F.A.Q."); ?>
		<div class="content">
			<?php faqAdminProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
</body>
</html>