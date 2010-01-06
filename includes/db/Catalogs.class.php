<?php
include_once "db.php";

class Catalogs
{
	public $validationError;
	
	public function getCategories($onlyActive=true){
		if($onlyActive)	return getDbRecords("Select * from catalogcategories where active");
		if(! $onlyActive)	return getDbRecords("Select * from catalogcategories");
	}
	public function getModels($categories_id=0){
		$query="Select * from knifemodels ";
		if($categories_id > 0) $query.="where catalogcategories_id=$categories_id";
		return getDbRecords($query);
	}
	public function getPhotos($categories_id=0){
		$query="Select * from knifephotos ";
		if($categories_id > 0) $query.="where catalogcategories_id=$categories_id";
		return getDbRecords($query);
	}
	public function getCategoriesAndModels($onlyActive=true){
		$results = $this->getCategories($onlyActive);
		foreach ($results as $key=>$category){
			$results[$key]['models'] = $this->getModels($category['catalogcategories_id']);
			$results[$key]['photos'] = $this->getPhotos($category['catalogcategories_id']);
		}
		return $results;
	}
	public function saveModel($values)
	{
		$original = getSingleDbRecord("Select * from knifemodels where knifemodels_id=" . $values['knifemodels_id']);
		foreach ($values as $field=>$val){
			if(!isset($original[$field]))  unset($values[$field]);
		}		
		echo debugStatement(dumpDBRecord($values));
//		return saveRecord("knifemodels", "knifemodels_id", $values);
	}
	public function validateModel($values)
	{
		$valid=true;
		$requiredFields = array('model', 'description', 'price', 'piclabel');
		foreach ($requiredFields as $field){
			if($values[$field] == ""){$this->validationError .= "$field,"; $valid=false;}			
		}
	
		//		if(!is_numeric($values['TotalRetail'])){$this->validationError .= "TotalRetail,"; $valid=false;}
		
		if(strlen($this->validationError) > 0) $this->validationError = substr($this->validationError,0,strlen($this->validationError)-1);
		return $valid;
	}
	
	function processesFileUploads($formValues){
		$error = $_FILES["files"]["error"];
		$source = $_FILES['files']['tmp_name'];

//		echo debugStatement(getBackTrace() . dumpDBRecords($_FILES) );		
		
		if ($error == UPLOAD_ERR_OK) {
			if (!is_uploaded_file($source)) {
				  echo "<HR>\nYou did not upload a file!<HR>\n";
	//			  unlink($source);
			} else {
				$this->processFileUpload($formValues, $source, $_FILES['files']['name']);
			}
		} elseif($error == UPLOAD_ERR_NO_FILE){
			// ignore, field blank
		} elseif($error == UPLOAD_ERR_FORM_SIZE){
			// image to big
			echo "File is to big:".$_FILES['files']['name'];
		}
	}

	function processFileUpload($formValues, $source, $name){
		$fileDir = BASE_IMG_FILEPATH;
		
		$uploadfile = $fileDir . "/";
		$extension=substr($name, strpos($name, "."));
	
		$photoinfo = array();
		$photoinfo['filelocation']=$uploadfile.$name;
		$photoinfo['catalogcategories_id']=$formValues['catalogcategories_id'];
		$photoinfo['photo_labels']=$formValues['photo_labels'];

		$dest = $photoinfo['filelocation'];
	
		if ( $dest != '' ) {
//	        echo "Move. $source => $dest<br />";
			if ( move_uploaded_file( $source, $dest ) ) {
				saveRecord('knifephotos', 'photo_id', $photoinfo);
				chmod($dest, 0755);
	        } else {
	            echo "File could not be stored. $source => $dest<br />";
	        }
	    } 
	}
}
?>