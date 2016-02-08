<?php
  function biving(){
    global $bivArray;
    
    if(count($bivArray)>0){
      echo '<h2><a href="#bivLink" id="bivLink" class="assetListLink" data-track="Built in Variables" title="Automatically available data from GTM">Built in variables ('.count($bivArray).')</a></h2><br />';
      echo '<div id="bivs" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';
      
      foreach($bivArray as $biv){
        $trans = translateToHuman($biv);
        if($trans!==''&&$trans!==false){
          echo '<li>'.$trans.'</li>';
        }
      }
  		echo '</div><br />';
    }
  }
?>