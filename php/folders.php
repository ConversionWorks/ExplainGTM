<?php
  function buildFolderArray($containerFolders){
    $fArray = [];
    foreach($containerFolders as $folder){
        array_push($fArray,$folder);
    }
    return $fArray;
  }

  //********************************************************************************
  // * Build a folder link
  //*********************************************************************************/
  function buildFolderLink($folder){
    $folderLink = '';
  
    $folderLink = $folderLink.'<a href="#folder'.$folder['folderId'].'" class="folderDetail" ';
    $folderLink = $folderLink.'onclick="jQuery(\'.assetList\').hide();jQuery(\'.assetDetail\').hide();jQuery(\'.assetContainer\').hide();jQuery(\'#folders\').toggle();jQuery(\'#folderDetail'.$folder['folderId'].'\').toggle()">';
    $folderLink = $folderLink.$folder['name'].'</a>';
    
    return $folderLink;
  }

  //******************************************************************************
  //  Folderisation
  //******************************************************************************/
  function folderisation($parentFolderId,$assetType){
    $folderDeets = '';
    global $folderArray;
      
    foreach($folderArray as $folderKey => $folderVal){
      if($folderArray[$folderKey]['folderId']===$parentFolderId){
            $folderDeets = $folderDeets.'This '.$assetType.' lives in the '.buildFolderLink($folderArray[$folderKey]).' folder.'; 
        }
    }
    
    if($folderDeets===''){
      $folderDeets = $folderDeets.'This '.$assetType.' is not placed in a folder.';
    }
    
    return $folderDeets;
  }

  //******************************************************************************
  //  Get the assets in a folder
  //******************************************************************************/
  function assetsInFolder($assetLibrary, $assetTypes, $assetType, $dataasset, $folderId){
    $assetsInFolders = '';
    
    foreach($assetTypes as $typeKey => $typeVal) {
      foreach($assetLibrary[$typeVal] as $assetKey => $assetVal) {
        $asset = $assetLibrary[$typeVal][$assetKey];
        if(array_key_exists('parentFolderId',$asset)){
          if($asset['parentFolderId']===$folderId){
            $assetsInFolders = $assetsInFolders.'<li><a href="#'.$dataasset.$asset[$assetType.'Id'].'" id="folderAsset'.$asset[$assetType.'Id'].'" data-asset="'.$dataasset.'" data-type="'.$typeVal.'" data-index='.$asset[$assetType.'Id'].' class="Detail">'.$asset['name'].'</a></li>';
          }
        }
      }
    }
    
    return $assetsInFolders;
  }

  //******************************************************************************
  //  Get the assets NOT in a folder
  //******************************************************************************/
  function assetsNotInFolders($assetLibrary, $assetTypes, $assetType, $dataasset, &$assetsNotInFolders){
    $notInFolders = '';
    foreach($assetTypes as $typeKey => $typeVal) {
      foreach($assetLibrary[$typeVal] as $assetKey => $assetVal) {
        if(!array_key_exists('parentFolderId',$assetVal)){
          $notInFolders = $notInFolders.'<li><a href="#'.$dataasset.$assetVal[$assetType.'Id'].'" id="nonfolderAsset'.$assetVal[$assetType.'Id'].'" data-asset="'.$dataasset.'" data-type="'.$typeVal.'" data-index='.$assetVal[$assetType.'Id'].' class="Detail">'.$assetVal['name'].'</a></li>';
          $assetsNotInFolders = $assetsNotInFolders + 1;
        }
      }
    }
  
    return $notInFolders;
  }
      
  function foldering(){
    global $folderArray,$container, $tagLibrary, $tagTypes, $trigLibrary, $trigTypes, $udvLibrary, $udvTypes;
  	
    if(count($container['containerVersion']['folder'])>0){
      $folders = '';
      $tagsInFolders = '';
      $trigsInFolders = '';
      $udvsInFolders = '';
      
      echo '<h2><a href="#folderLink" id="folderLink" class="assetListLink" data-track="Folders" title="Organisation for your things">Folders ('.count($folderArray).')</a></h2><br />';
  	  echo '<div id="folders" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';
  		
  		foreach($folderArray as $folder){
        $folders = $folders.'<a href="#folder'.$folder['folderId'].'" id="folder'.$folder['folderId'].'" class="folderDetail"><h4>';
        $folders = $folders.$folder['name'].'</h4></a>';
        $folders = $folders.'<div style="margin:5px;display:none" class="assetList container" id="folderDetail'.$folder['folderId'].'">';
        
        $tagsInFolders = '';
        $tagsInFolders = assetsInFolder($tagLibrary, $tagTypes, 'tag', 'tag', $folder['folderId']);
        if($tagsInFolders!==''){
          $folders = $folders.'<h4>Tags:</h4><ul style="margin:5px">'.$tagsInFolders.'</ul>';  
        }
        
        $trigsInFolders = '';
        $trigsInFolders = assetsInFolder($trigLibrary, $trigTypes, 'trigger', 'trig', $folder['folderId']);
        if($trigsInFolders!==''){
          $folders = $folders.'<h4>Triggers:</h4><ul style="margin:5px">'.$trigsInFolders.'</ul>';  
        }
        
        $udvsInFolders = '';
        $udvsInFolders = assetsInFolder($udvLibrary, $udvTypes, 'variable', 'udv', $folder['folderId']);
        if($udvsInFolders!==''){
          $folders = $folders.'<h4>User Defined Variables:</h4><ul style="margin:5px">'.$udvsInFolders.'</ul>';  
        }
        
        if($udvsInFolders === '' && $trigsInFolders === '' && $tagsInFolders === ''){
          $folders = $folders.'<h4>Empty</h4>';
        }    
        $folders = $folders.'</div>';
      }
    }
    
    if($folders!==''){
      $foldersOrnofolders = 'Your folders are:</h3><ul>'.$folders.'</ul>';
    }else{
      $foldersOrnofolders = 'You have no folders...</h3>';
    }
    echo '<div class="container" style="margin:5px" id="folderlist"><h3 style="margin:5px;" id="folderHeader">'.$foldersOrnofolders.'</div><br /></div><br />';
  }
?>