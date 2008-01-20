<?php
/* Created on Feb 5, 2006*/
include_once "../includes/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/catalog.php";
include_once "../includes/adminFunctions.php";

session_start();
if(!loggedIn()){
	$_SESSION['loginValidated'] = 0;
	session_destroy();
	header("Location: "."../");
}


function catalogAdminProcessing(){
	$action = getHTMLValue('action');
	if(sizeof($action) == 0)
		$action='list';
		
	switch($action){
		case 'viewcategory':
			$id = getHTMLValue('catalogcategories_id');
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
			print getCategoryModels($category);
			break;
		case 'list':
			echo "<BR><BR><BR>";
//			echo quickCategorySelection(0);
			print getCategoryList();
			break;
		case 'addmodel':
			$id = getHTMLValue('catalogcategories_id');
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
			echo "<center><h2>" . $category['category'] . "</h2></center>";
			echo editModel(array('catalogcategories_id'=>$id));
			break;
		case 'editmodel':
			$id = getHTMLValue('id');
			$model = getBasicSingleDbRecord('knifemodels','knifemodels_id',$id);
			echo editModel($model);
			break;
		case 'deletemodel':
			$id = getHTMLValue('id');
			$model = getBasicSingleDbRecord('knifemodels','knifemodels_id',$id);
			echo deleteValidationForm($model);
			break;
		case 'deletemodelphoto':
			$id = getHTMLValue('id');
			$photo = getBasicSingleDbRecord('knifephotos','photo_id',$id);
			echo deletePhotoValidationForm($photo);
			break;
		case 'validatedeletion':
			$id = getHTMLValue('id');
			$model = getBasicSingleDbRecord('knifemodels','knifemodels_id',$id);
			deleteRecord('knifemodels', 'knifemodels_id', array('knifemodels_id'=>$id));
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$model['catalogcategories_id']);
			echo getCategoryModels($category);
			break;
		case 'validatephotodeletion':
			$id = getHTMLValue('id');
			$photo = getBasicSingleDbRecord('knifephotos','photo_id',$id);
			deleteRecord('knifephotos', 'photo_id', array('photo_id'=>$id));
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$photo['catalogcategories_id']);
			echo getCategoryModels($category);
			break;			
		case 'Save':
			$id = getHTMLValue('catalogcategories_id');
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
//			echo "<center><h2>" . $category['category'] . "</h2></center>";
			saveModelEntry(getEntryFromPOST(array('model','description','weight','price','note','knifemodels_id','catalogcategories_id', 'piclabel')));
			echo getCategoryModels($category);
			break;
		case 'addphoto':
			$id = getHTMLValue('catalogcategories_id');
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
			echo addEditPhotoForm($category, array());
			break;
		case 'editmodelphoto':
			$id = getHTMLValue('id');
			$photo = getBasicSingleDbRecord('knifephotos','photo_id',$id);
			$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$photo['catalogcategories_id']);
			echo addEditPhotoForm($category, $photo);
			break;
		case 'savemodelphoto':
			$id = getHTMLValue('photo_id');
			if($id > 0){ // editing, only update pic_label
				$photo = getBasicSingleDbRecord('knifephotos','photo_id',$id);
				$photo['photo_labels'] = getHTMLValue('photo_labels');
				$photo = saveRecord('knifephotos', 'photo_id', $photo);
				$id = getHTMLValue('catalogcategories_id');
				$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
				print getCategoryModels($category);
			} else{
				processesFileUploads(getFormValues());
				$id = getHTMLValue('catalogcategories_id');
				$category = getBasicSingleDbRecord('catalogcategories','catalogcategories_id',$id);
				print getCategoryModels($category);
			}
			break;
			
		default:
			echo "Unknown action: " . $action . "<br>";
			dumpPOST_GET();
	}
}
//function deleteValidationForm($faq){
//	$results = $results .  "<center><h2> Confirm Deletion:</h2></center>";
//	$results = $results .  "<center><h2>" . $faq['question'] . "</h2></center>";
//	$results = $results .  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
////			echo hiddenField('catalogcategories_id',$model['catalogcategories_id']);
//	$results = $results .  hiddenField('action','validatedeletion');
//	$results = $results .  hiddenField('id',$faq['faq_id']);
//	$results = $results .  "<center><input type='submit' name='validatedelete' value='Confirm' ></center>" ;
//	$results = $results .  "</form>\n";
//		
//	return $results;	
//}
function addEditPhotoForm($category, $photo){
	$results = $results .  "<center><h2>Photo: " . $category['category'] . "</h2></center>";
	$results = $results .  "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='POST'>";
	
	$results = $results . textField('photo_labels', 'Picture labels', true, $photo['photo_labels']). "<BR>\n" ;
	if(!($photo['photo_id'] > 0)){
		$results = $results . "<BR>";
		$results = $results . "<label for='files' >File:</label>";	
		$results = $results . "<input type='file' name='files'/><BR>";
		$results = $results . hiddenField('MAX_FILE_SIZE','2000000');
	}
	
	$results = $results . "<BR>";
	$results = $results . hiddenField('action','savemodelphoto');
	$results = $results . hiddenField('catalogcategories_id',$category['catalogcategories_id']);
	$results = $results . hiddenField('photo_id',$photo['photo_id']);
	$results = $results .  "<center><input type='submit' name='savemodelphoto' value='Save' ></center>" ;
	$results = $results .  "</form>\n";
	return $results;
//	print_r($category);
}

function deleteValidationForm($model){
	$results = $results .  "<center><h2> Confirm Deletion:</h2></center>";
	$results = $results .  "<center><h2>" . $model['model'] . "</h2></center>";
	$results = $results .  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
//			echo hiddenField('catalogcategories_id',$model['catalogcategories_id']);
	$results = $results .  hiddenField('action','validatedeletion');
	$results = $results .  hiddenField('id',$model['knifemodels_id']);
	$results = $results .  "<center><input type='submit' name='validatedelete' value='Confirm' ></center>" ;
	$results = $results .  "</form>\n";
		
	return $results;	
}

function deletePhotoValidationForm($photo){
//	$photo = getBasicSingleDbRecord('knifephotos','photo_id',$photo['photo_id']);
	global $fileDir;
	$image= substr($photo['filelocation'], strlen($fileDir));
	$image= getBaseImageDir() . "/catalog/" . $image;
	
	$results = $results .  "<center><h2> Confirm Deletion:</h2></center>";
	$results = $results . "<img style='padding:0px 20px 0px 0px;' src='$image' align=left >";
	$results = $results .  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
//			echo hiddenField('catalogcategories_id',$model['catalogcategories_id']);
	$results = $results .  hiddenField('action','validatephotodeletion');
	$results = $results .  hiddenField('id',$photo['photo_id']);
	$results = $results .  "<center><input type='submit' name='validatedeletephoto' value='Confirm' ></center>" ;
	$results = $results .  "</form>\n";
	return $results;	
}

function editModel($model){
//	$results = $results . "Form to edit/add new Model<BR>\n";
	$results = $results . "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
	$results = $results . textField('model', 'Model', true, $model['model']). "<BR>\n" ;
	$results = $results . textArea('description', 'Description', true, $model['description'], true). "<BR>\n" ;
	$results = $results . textField('weight', 'Weight', true, $model['weight']). "<BR>\n" ;
	$results = $results . textField('price', 'Price', true, $model['price']). "<BR>\n" ;
	$results = $results . textArea('note', 'Note', true, $model['note']). "<BR>\n" ;
	$results = $results . textField('piclabel', 'Picture label', true, $model['piclabel']). "<BR>\n" ;
	$results = $results . hiddenField('action','Save') . "\n";
	$results = $results . hiddenField('knifemodels_id',$model['knifemodels_id']) . "\n";
	$results = $results . hiddenField('catalogcategories_id',$model['catalogcategories_id']) . "\n";
	$results = $results . "<input style='color: red;' type='submit' name='submit' value='Submit' >\n" ;
	$results = $results . "</form>\n";	

	$results = $results . "<HR>";
	
	return $results;
}
function saveModelEntry($entry){
	return saveRecord('knifemodels', 'knifemodels_id', $entry);
}

function categoryModelLink($category){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=viewcategory&catalogcategories_id=" . 
				$category["catalogcategories_id"] . ">" .  
				$category["category"] . "</a>";
}

function addModelLink($category){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=addmodel&catalogcategories_id=" . 
				$category["catalogcategories_id"] . ">Add New Model</a>";
}

function addCategoryPhotoLink($category){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=addphoto&catalogcategories_id=" . 
				$category["catalogcategories_id"] . ">Add New Photo</a>";
}

function editModelLink($model){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=editmodel&id=" . $model["knifemodels_id"] . ">Edit</a>";
}
function deleteModelLink($model){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=deletemodel&id=" . $model["knifemodels_id"] . ">Remove</a>";
}

function removeModelPhotoLink($photo_id){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=deletemodelphoto&id=$photo_id>Remove</a>";
}
function editModelPhotoLink($photo_id){
	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=editmodelphoto&id=$photo_id>Edit</a>";
}



function processesFileUploads($formValues){
	$error = $_FILES["files"]["error"];
	$source = $_FILES['files']['tmp_name'];
	
	if ($error == UPLOAD_ERR_OK) {
		if (!is_uploaded_file($source)) {
			  echo "<HR>\nYou did not upload a file!<HR>\n";
//			  unlink($source);
		} else {
			processFileUpload($formValues, $source, $_FILES['files']['name']);
		}
	} elseif($error == UPLOAD_ERR_NO_FILE){
		// ignore, field blank
	} elseif($error == UPLOAD_ERR_FORM_SIZE){
		// image to big
		echo "File is to big:".$_FILES['files']['name'];
	}
}

function processFileUpload($formValues, $tmp_name, $name){
	global $fileDir;
//	print_r($formValues);
	
	$uploadfile = $fileDir . "/";
	$source = $tmp_name;
	$extension=substr($name, strpos($name, "."));

	$photoinfo = array();
	$photoinfo['filelocation']=$uploadfile.$name;
	$photoinfo['catalogcategories_id']=$formValues['catalogcategories_id'];
	$photoinfo['photo_labels']=$formValues['photo_labels'];
	
	
	$dest = $photoinfo['filelocation'];
	
//	echo "<HR>" . $dest . "<HR>";
	if ( $dest != '' ) {
//		echo "source=>dest: ---  $source => $dest<BR>$primary<HR>";
        if ( move_uploaded_file( $tmp_name, $dest ) ) {
			saveRecord('knifephotos', 'photo_id', $photoinfo);
			chmod($dest, 0755);
//            echo 'File successfully stored.<BR>';
        } else {
            echo "File could not be stored. $source => $dest<BR>";
        }
    } 
}


?>
<html>
<?php echo headSegment(); ?>
<body>
<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php catalogAdminProcessing();?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>

</body>
</html>
