<?php
  session_start();
  $searchResults = '';
  $responseCode = 200;
  $searchResultCount = 0;
  
  function buildAssetLink($assetTypeName,$assetId,$assetType,$assetName,$toolName){
    $assetLink = '<tr><td>';
    $assetLink = $assetLink.'<a href="#'.$toolName.$assetId.'" class="'.$assetTypeName.'Detail" ';
    $assetLink = $assetLink.'onclick="jQuery(\'.assetList\').hide();jQuery(\'.assetDetail\').hide();jQuery(\'.assetContainer\').hide();jQuery(\'#'.$toolName.'s\').toggle();jQuery(\'#'.$toolName.'Type'.$assetType.'\').next().toggle();jQuery(\'#'.$toolName.'Detail'.$assetId.'\').toggle()">';
    $assetLink = $assetLink.$assetName.'</a>';
    $assetLink = $assetLink.'</td></tr>';

    return $assetLink;
  }
  
  function searchAssetField($search,$clause,$field,$assetTypeName,$assetId,$assetType,$assetName,$toolName,&$searchResultCount){
    $fieldMatches = '';
    
    if($clause==='regex'){
    	$assetArray =[];
	    preg_match_all($search, $field, $assetArray);
	
	    if(!empty($assetArray[0])){
	      $fieldMatches = $fieldMatches.buildAssetLink($assetTypeName,$assetId,$assetType,$assetName,$toolName);
        $searchResultCount = $searchResultCount + 1;
	    }
    }else{
      if(($clause==='exact' && $field===$search) || ($clause==='contain' && (strpos($field,$search)>-1))){
        $fieldMatches = $fieldMatches.buildAssetLink($assetTypeName,$assetId,$assetType,$assetName,$toolName);
        $searchResultCount = $searchResultCount + 1;
      }
    }
    return $fieldMatches;
  }
  
  function deepen($arr,&$arrOut){
    foreach($arr as $key => $val){
      if(gettype($val)!=='array'){
        if(($key!=='name') && ($key!=='variableId') && ($key!=='triggerId')){
          array_push($arrOut, $val);
        }
      }else{
        deepen($val,$arrOut);
      }
    }
  }
  
  function searchAssets($clause, $search, $assetTypes, $assetLibrary, $assetName,$toolName,&$searchResultCount){
    $idMatches = '';
    $nameMatches = '';
    $mentions = '';
    $searchOutput = '';
    $searchResultsResponse =[];
    
    foreach($assetTypes as $typeKey => $typeVal){
      foreach($assetLibrary[$assetTypes[$typeKey]] as $assetKey => $assetVal){
        $idMatches = $idMatches.searchAssetField($search,$clause,$assetVal[$assetName.'Id'],$assetName,$assetVal[$assetName.'Id'],$typeVal,$assetVal['name'],$toolName,$searchResultCount);
        $nameMatches = $nameMatches.searchAssetField($search,$clause,$assetVal['name'],$assetName,$assetVal[$assetName.'Id'],$typeVal,$assetVal['name'],$toolName,$searchResultCount);
        $objTmp = [];
        deepen($assetVal,$objTmp);
        $mentions = $mentions.searchAssetField($search,$clause,implode($objTmp,' '),$assetName,$assetVal[$assetName.'Id'],$typeVal,$assetVal['name'],$toolName,$searchResultCount);
      }
    }
    
    if($searchResultCount>0){
      $searchOutput = $searchOutput.'<table class="table table-bordered table-striped" style="float:left;width:auto;margin-right:20px;"><tr>';
    
      if(!empty($idMatches)){
        $searchOutput = $searchOutput.'<th style="width:300px">The IDs of the following '.ucfirst($assetName).'s match "'.$search.'"</th>'.$idMatches;
      }
      
      if(!empty($nameMatches)){
        $searchOutput = $searchOutput.'<th style="width:300px">The names of the following '.ucfirst($assetName).'s match "'.$search.'"</th>'.$nameMatches;
      }
      
      if(!empty($mentions)){
        $searchOutput = $searchOutput.'<th style="width:300px">The following '.ucfirst($assetName).'s contain mentions of "'.$search.'"</th>'.$mentions;
      }
      
      $searchOutput = $searchOutput.'</tr></table>';
    }
  
    return $searchOutput;
  }
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST["searchPhrase"]);
    $clause = trim($_POST["searchClause"]);

    if (empty($search)) {
      $responseCode = 400;
      $searchResults = 'empty';
    }else{
      if($_SESSION["tagLibary"]!==''){
        $searchResults = $searchResults.searchAssets($clause, $search, $_SESSION["tagTypes"], $_SESSION["tagLibrary"], 'tag','tag',$searchResultCount);
      }
      
      if($_SESSION["trigLibary"]!==''){
        $searchResults = $searchResults.searchAssets($clause, $search, $_SESSION["trigTypes"], $_SESSION["trigLibrary"], 'trigger','trig',$searchResultCount);
      } 
      
      if($_SESSION["udvLibary"]!==''){
        $searchResults = $searchResults.searchAssets($clause, $search, $_SESSION["udvTypes"], $_SESSION["udvLibrary"], 'variable','udv',$searchResultCount);
      }
    }
  } else {
    $responseCode = 400;
    $searchResults = '';
  }
  
  http_response_code(200);
  $searchResultsResponse['searchResultCount'] = $searchResultCount;
  $searchResultsResponse['searchResultsMarkup'] = '<div class="container">'.$searchResults.'</div>';
  echo json_encode($searchResultsResponse);
?>