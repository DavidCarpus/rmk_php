<?php
include_once "Base.class.php";

class Catalog extends Base
{
	function __construct() {
       $this->name = "forms_Catalog";
   }
   
   public function entryFormMode($formValues)
   {
//		if(array_key_exists("Invoice", $formValues) && is_numeric($formValues["Invoice"])){return "edit";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Save"){return "save";}

		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Update Category")
			{return "update_Cat";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Add Photo")
			{return "addphoto";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Upload Photo")
			{return "uploadphoto";}
			
		if(array_key_exists("catalogcategories_id", $formValues) && $formValues["catalogcategories_id"] > 0 &&
			array_key_exists("knifemodels_id", $formValues)  && $formValues["knifemodels_id"] > 0 )
				{return "edit";}

		if(array_key_exists("catalogcategories_id", $formValues) && $formValues["catalogcategories_id"] > 0 &&
			(!array_key_exists("knifemodels_id", $formValues)  || $formValues["knifemodels_id"] == 0 ))
				{return "category";}

				
//		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Search"){return "search";}
		return "browse";	
		 
   }
   
   public function categorySelectForm($formValues, $categories)
   {
   		$formName="AdminCategorySelect";
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
		
		$options=array();
		$models=array();
		foreach ($categories as $category){
			$options[] = array('id'=>$category['catalogcategories_id'], 'label'=>$category['category']);
			if($formValues['catalogcategories_id'] == $category['catalogcategories_id']) $models=$category['models'];
		}
		
		$results .= $this->selection("catalogcategories_id", $options, "Catalog Category", $formValues['catalogcategories_id'], true);
		
		$options=array();
		foreach ($models as $model){
			$options[] = array('id'=>$model['knifemodels_id'], 'label'=>$model['model']);
		}
		$results .= $this->selection("knifemodels_id", $options, "Model", $formValues['knifemodels_id'], true);
//		echo debugStatement(dumpDBRecords($options));
		
//		echo debugStatement(dumpDBRecords($options));
		//		$results .= hiddenField('startid',$parameters['startid']) . "\n";
//		$results .= hiddenField('action','searchorders') . "\n";
	
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";

		return $results;
   }
   
   function getCategoryFromArray($categories, $categoryID)
   {
   		foreach ($categories as $category){
   			if($category['catalogcategories_id'] == $categoryID) return $category;    
   		}
   }
   
   function getModelFromArray($models, $modelID)
   {
   		foreach ($models as $model){
   			if($model['knifemodels_id'] == $modelID) return $model;    
   		}
   }
   
   public function addPhotoForm($formValues, $categories)
   {
   	   	$formName="addPhotoForm";
   	   	$category = $this->getCategoryFromArray($categories, $formValues['catalogcategories_id']);
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form enctype='multipart/form-data name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='post'>\n" ;
   	   	$results .= "<label for='files' >File:</label>";	
		$results .= "<input type='file' name='files'/><br />";
		$results .=  $this->textField($label, "Labels", false, $photoRec['photo_labels']) . "<br/>\n";	
		$results .=  $this->button("submit", "Upload Photo");	
		$results .= $this->hiddenField('MAX_FILE_SIZE', '2000000');				
		$results .= $this->hiddenField('catalogcategories_id', $formValues['catalogcategories_id']);				
		$results .= "</form>";

		
		$results .= "</div><!-- End $formName -->\n";	
		
//		$results .=  dumpDBRecord($category);
		return $results;
   }
   
   public function editCategoryForm($formValues, $categories)
   {
   	   	$formName="editCategoryForm";
   	   	$category = $this->getCategoryFromArray($categories, $formValues['catalogcategories_id']);
   	   	
   		$errors = array();
		if(array_key_exists("ERROR", $categories) && count($categories['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $categories['ERROR']), true);
		}
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='get'>\n" ;

//		$results .=  " Edit 'Title' of Category <br/>";
		$results .=  $this->textField("category", "Category", false, $category['category']) . "<br/>\n";
		
   		

		$results .=  "<div class='photoblocks'>" . "\n";
		foreach ($category['photos'] as $photoRec){
			$results .=  "<div class='editphotoblock'>" . "\n";
			$loc = "tn_" . substr($photoRec['filelocation'], strrpos($photoRec['filelocation'], "/")+1);
			$loc= BASE_IMG_URL . "/catalog/$loc";
			$loc = "<img src='$loc' >";	
			$results .= $loc;
			$label=	"photo label_" . $photoRec['photo_id'];
			$results .=  $this->textField($label, "Labels", false, $photoRec['photo_labels']) . "<br/>\n";
//			$results .= $photoRec['photo_labels'];	
			$results .= "</div><!-- End photoblock -->\n";
		}
		$results .= "</div><!-- End photoblocks -->\n";
		
		$results .= $this->hiddenField('catalogcategories_id', $formValues['catalogcategories_id']);				
		
		$results .=  $this->button("submit", "Update Category");
		$results .=  $this->button("submit", "Add Photo");
		$results .= "</form>";
		
		
		$results .= "</div><!-- End $formName -->\n";	
		
//		$results .=  dumpDBRecord($category);
		return $results;
   }
   
   public function editModelForm($formValues, $categories)
   {
   	   	$formName="editModelForm";
   	   	
   		$errors = array();
		if(array_key_exists("ERROR", $categories) && count($categories['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $categories['ERROR']), true);
		}
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='" . $_SERVER['SCRIPT_NAME'] . "' method='get'>\n" ;
		
   		$category = $this->getCategoryFromArray($categories, $formValues['catalogcategories_id']);
   		if($category == "") return;
   		$model = $this->getModelFromArray($category['models'], $formValues['knifemodels_id']);
   		if($model == "") return;
   		
//      	$results .=  debugStatement(dumpDBRecord($model));
//   		$results .=  debugStatement("Model?:" . dumpDBRecord($formValues));
   		
   		$fields = array('model'=>'Model', 'description'=>'Description', 'weight'=>'Weight', 
   				'price'=>'Price', 'note'=>'Note', 'piclabel'=>'Picture label', 
   				'catalogcategories_id'=>"", 'knifemodels_id'=>"", 'active'=>'active');
   		
		foreach($fields as $name=>$label)
		{
			$value = $model[$name];
			$err=(array_key_exists($name, $errors));
			$required=false;
			if(isset($formValues[$name])) $value =$formValues[$name];
//			if($name != 'note'){ $required=true;}
			
			if($name == 'description' || $name == 'note'){
				$results .= $this->textArea($name, $label, $err, $value);
			} else if($name == 'catalogcategories_id' || $name == 'knifemodels_id' || $name == 'active'){
				$results .= $this->hiddenField($name, $value[$name]);				
			} else{
				$results .=  $this->textField($name, $label, $err, $value) . "<br/>\n";
			}
		}
		$results .=  $this->button("submit", "Save");
//		$results .=  debugStatement(dumpDBRecord($errors));
		
   		$results .= "</form>";
   		
		$results .= "</div><!-- End $formName -->\n";		
			
		$results .=  "<div id='photoblock'>" . "\n";
  //		$results .=	$category['photos'];
		foreach ($category['photos'] as $photoRec){
//			$results .= debugStatement(dumpDBRecords($category['photos']));
			$loc = "tn_" . substr($photoRec['filelocation'], strrpos($photoRec['filelocation'], "/")+1);
			$loc= BASE_IMG_URL . "/catalog/$loc";
			$loc = "<img src='$loc' >";	
			$results .= $loc;			
//			$results .= $photoRec['photo_labels'];			
		}
		$results .= "</div><!-- End photoblock -->\n";		
		
   		
		return $results;
   }
}

?>