<?php
include_once "db/db.php";

$categories = array(array("label"=>"Outdoorsman Knives",         "tag"=>"outdoorsman"),
					array("label"=>"Skinning and Hunting Knives","tag"=>"hunting"),
					array("label"=>"Saltwater Knives",           "tag"=>"saltwater"),
					array("label"=>"Survival Knives",            "tag"=>"survival"),
					array("label"=>"Military Style Knives",      "tag"=>"military"),
					array("label"=>"Bowie Knives",               "tag"=>"bowie"),
					array("label"=>"Carving Set & Steak Knife",  "tag"=>"carving"),
					array("label"=>"Sheaths",                    "tag"=>"sheaths"),
					array("label"=>"Non-Catalog II Knives",      "tag"=>"noncatalog"),
					array("label"=>"Example Combinations",       "tag"=>"examples"),
					);	


function catalogProcessing(){
	switch (getHTMLValue('action')) {
		case 'viewcategory':		
			echo listModels(getHTMLValue('catalogcategories_id'));
//			echo "Outdoorsman Knives";
			break;
			
		case 'viewmodel':
			$id = getHTMLValue('id');
//			knifemodels_id
			break;
			
		case 'modeldetail':
			echo getModelDetails(getHTMLValue('id'));
//			knifemodels_id
			break;
			
		default:
			echo "<div id='cataloglist'>";
			echo "<B>Catalog Categories</B><BR>";
			echo getCategoryList();
			echo "<a style='text-decoration: none;' href='nonCat2.php'>Non Catalog Knives</a>". "<BR>\n";		
			echo "</div>";
//			echo dumpPOST_GET();
	}
}
function listPhotos($categoryid){
	
}

function listModels($id){
	$category = getSingleDbRecord("Select * from catalogcategories where catalogcategories_id=$id");
//	$results = $results . getCategoryModels($category);
	$results = $results . getShortCategoryList($category);
	return $results;
}

function getCategoryList(){
	$categories = getDbRecords("Select catalogcategories_id as id, category as label from catalogcategories");
	foreach($categories as $category){
		$photos = getDbRecords("Select * from knifephotos where catalogcategories_id=" . $category['id']);
		if(count($photos) > 0 )
			if($category['label'] != "Non-Catalog II Knives")
				$results = $results . viewcategorylink($category) . "<BR>\n";		
	}	
	return $results;
}
function viewcategorylink($category){
	return "<a style='text-decoration: none;' href=" . $_SERVER['PHP_SELF'] . "?action=viewcategory&catalogcategories_id=" . $category["id"] . ">" . $category["label"] ."</a>";
//		$results = $results .  hiddenField('action','viewcategory');		
}

function getCategoryDropdown($tag=''){
	$query = "Select distinct catalogcategories.catalogcategories_id as id, category as label from catalogcategories " .
			" left join knifephotos on catalogcategories.catalogcategories_id = knifephotos.catalogcategories_id " .
			" where photo_id is not null ";
	$query .=  " and category <> 'Non-Catalog II Knives'";
	$categories = getDbRecords($query );
	return selection("catalogcategories_id", $categories, "", $tag, true);
}

function quickCategorySelection($id){
	$results = $results .  "<form action='". $_SERVER['PHP_SELF']. "' method='POST'>" ;
	$results = $results .  "<center>Select Catalog Category:&nbsp;&nbsp;";
	$results = $results .  getCategoryDropdown($id);
	$results = $results .  hiddenField('action','viewcategory');		
	$results = $results .  "</center>";
	$results = $results .  "</form >";
	return $results;
}

function getModelDetails($id){
	$model = getSingleDbRecord("Select * from knifemodels where knifemodels_id=$id");
	$query = "Select * from knifephotos where catalogcategories_id=" . $model['catalogcategories_id'] . " and photo_labels  like '%" . $model['piclabel'] . "%'";
	
//	echo $query;
	$photo = getSingleDbRecord($query );

	$style = 'catalogblockdetail';	
//	$results = $results . quickCategorySelection($model['catalogcategories_id']);
	$category = getSingleDbRecord("Select catalogcategories_id as id, category as label from catalogcategories where catalogcategories_id=".$model['catalogcategories_id']);
 

	// display photo
	global $fileDir;
	$image= substr($photo['filelocation'], strlen($fileDir));
	$image= getBaseImageDir() . "/catalog/" . $image;
	$results = $results . "<div style='" . $style . "'>"; 
//	$results = $results . "Test"; 
	
//	$results = $results . "<img width=300 style='padding:20px 20px 0px 0px;' src='$image' align=left >";
	$results = $results . "<img style='padding:0px 20px 0px 0px;' src='$image' align=left >";
//	
	$results = $results .  '<B>'. $model['piclabel'] . " ) " .htmlizeText($model['model']) ."</B><BR>";
	$results = $results .  htmlizeText($model["description"]) . "<BR>";
	if($model["weight"] != '') $results = $results .  "<I>" . $model["weight"] .  "</I> &nbsp; ";
	if($model["price"] != '') $results = $results . $model["price"] .  "";
	if($model["note"] != '') $results = $results . "<HR><i>". htmlizeText($model["note"]) .  "</i>";
	
	//=============================================
	$labels=$photo['photo_labels'];
	$labels = str_replace("  "," ",$labels);			
	$labels = str_replace(" ","','",$labels);			
	$labels = "'" . $labels . "'";
	
	$style = 'catalogblocklist';	

	$query="Select * from knifemodels where catalogcategories_id=" . $model['catalogcategories_id'] . " and piclabel in ( $labels )  order by piclabel";
//	echo $query;
	$models = getDbRecords($query);
		
	$results = $results . "<BR><BR>";
	 
	$results = $results . "<div class=catalogblockdetaillist>";
	$results = $results . "<H4>Other ".viewcategorylink($category)."</H4>";
	foreach($models as $model){
		if($model['knifemodels_id'] != $id){ // only list OTHER models for this photo
			$results = $results .  modelDetailLink($model) ."<BR>";
		} else{
			$results = $results .  "<B>".modelDetailLink($model) ."</B><BR>";
		}
	}
	$results = $results . "</div>\n"; 
	$results = $results . "</div>\n"; 

//	print_r($model);
	return $results;
}

function getShortCategoryList($category){
	$results = $results . quickCategorySelection($category['catalogcategories_id']);
	$id = $category['catalogcategories_id'];
	
	$photos = getDbRecords("Select * from knifephotos where catalogcategories_id=$id order by photo_labels");
//	if(count($photos) > 2) 
	$results = $results . photoList($id);
	foreach($photos as $photo){
		$labels=$photo['photo_labels'];
		$labels = str_replace("  "," ",$labels);			
		$labels = str_replace(" ","','",$labels);			
		$labels = "'" . $labels . "'";
		
		$style = 'catalogblocklist';	
	
		$query="Select * from knifemodels where catalogcategories_id=$id and piclabel in ( $labels ) order by piclabel";
		$models = getDbRecords($query);
		
		// display photo
		global $fileDir;
		$image= substr($photo['filelocation'], strlen($fileDir));
		$image= getBaseImageDir() . "/catalog/" . $image;
		$results = $results . "\n<a name='". $photo['photo_id'] . "'></a>" ;
		$results = $results . "\n<div class='" . $style . "'>"; 
		$tn_Location = substr($image,0, strrpos($image,"/"));
		$tn_Location = $tn_Location."tn_".substr($image,strrpos($image,"/")+1);
		$results = $results . "\n<img src='$tn_Location' align=left >";
//
//		
//		// display associated models (short form)
		foreach($models as $model){
			$results = $results .  modelDetailLink($model) ."<BR>";
		}
//		$results = $results . "Test"; 
		
		$results = $results . "</div>\n"; 
	}
	
	return $results;
}

function photoList($id){
	$query = "Select * from knifephotos where catalogcategories_id=$id order by photo_labels";
	$photos = getDbRecords($query);
	$cnt=1;
	foreach($photos as $photo){
		$labels=$photo['photo_labels'];
		$labels = str_replace("  "," ",$labels);			
		$labels = str_replace(" ","','",$labels);			
		$labels = "'" . $labels . "'";

		$query="Select * from knifemodels where catalogcategories_id=$id and piclabel in ( $labels ) order by piclabel";
//		print $query;
		$results = $results . photoQuickLink($photo['photo_id'], $id, $cnt++).  "<BR>";
		$models = getDbRecords($query);
//		$results = $results . "<UL>";
		foreach($models as $model){
//			$results = $results .   "<LI>" . $model['model'] .  "</LI>";
			$results = $results .   " &nbsp; &nbsp; " . modelDetailLink($model).  "<BR>";
		}
//		$results = $results . "</UL>";
	}
	return $results;
	
}
function photoQuickLink($id, $categoryid, $cnt){
	return "<a href='catalog.php?action=viewcategory&catalogcategories_id=$categoryid#". $id ."'>" . "Photo " . $cnt . " : " . "</a>";
	
}

function modelDetailLink($knifemodel){
//	$results = $results .  "<B>" . $knifemodel["model"] .  "</B>";
//	return "<a href=" . $_SERVER['PHP_SELF'] . "?action=editmodel&id=" . $model["knifemodels_id"] . ">Edit</a>";
	$results = $results .  "<a href='" . $_SERVER['PHP_SELF'] . "?action=modeldetail&id=" . $knifemodel["knifemodels_id"] . "'>" . $knifemodel["piclabel"] . " ) " . $knifemodel["model"] .  "</a>\n";
	return $results;
}

function getCategoryModels($category){
	$adminArea = (strpos($_SERVER['PHP_SELF'],"admin/") != FALSE );
	if($adminArea )
		return getAdminCategoryModels($category);
	
	$results = $results . "<BR>";
	$results = $results . quickCategorySelection($category['catalogcategories_id']);
	$id = $category['catalogcategories_id'];
	
	$models = getDbRecords("Select * from knifemodels where catalogcategories_id=$id");
	
	foreach($models as $model){
		if($model['piclabel'] != '')
			$results = $results .  "<B>" . $model['piclabel'] .  "</B>) ";
		$results = $results .  "<B>" . $model["model"] .  "</B>" . " &nbsp; " .  $model["description"] . "<BR>";
		$results = $results .  "<B>";
		if($model["weight"] != '') $results = $results .  "<I>" . $model["weight"] .  "</I> &nbsp; ";
		if($model["price"] != '') $results = $results . $model["price"] .  "";
		$results = $results .  "</B><BR>";
//			$results = $results .  "<B><I>" . $model["weight"] .  "</I>" . " &nbsp; " .  $model["price"] .  "</B><BR>";
		$results = $results .  "<BR>\n";
	}
	$photos = getDbRecords("Select * from knifephotos where catalogcategories_id=$id");
	
	return $results ;
}
function getAdminCategoryModels($category){
	$results = $results . "<BR>";
	$results = $results . quickCategorySelection($category['catalogcategories_id']);
	$id = $category['catalogcategories_id'];
	$models = getDbRecords("Select * from knifemodels where catalogcategories_id=$id order by piclabel");
	foreach($models as $model){
		$results = $results .  editModelLink($model) .  " &nbsp; " . deleteModelLink($model)  .  " &nbsp; ";
		if($model['piclabel'] != '')
			$results = $results .  "<B>" . $model['piclabel'] .  "</B>) ";
		$results = $results .  "<B>" .  $model["model"] . "</B>";
//			$results = $results .  " &nbsp; " .  $model["description"];
		$results = $results .  "<BR>\n";
	}
	$results = $results . "<BR>" . addModelLink($category);

	$results = $results .  "<BR>" .adminCategoryPhotos($category);
	
	return $results ;
}
function adminCategoryPhotos($category){
	$photos = getDbRecords("Select * from knifephotos where catalogcategories_id=" . $category['catalogcategories_id']);

	$results = $results . "<HR><B>Photos</B><BR>";

	foreach($photos as $photo){
		$results = $results . removeModelPhotoLink($photo['photo_id']). " - ";
		$results = $results . editModelPhotoLink($photo['photo_id']). " - ";
		
		$results = $results  .$photo['photo_labels'];
		$results = $results . "<BR>";
//		$results = $results . print_r($photo, true);
	}	
	$results = $results . "<BR>" . addCategoryPhotoLink($category) . "<BR>";
	return $results;
}


?>
