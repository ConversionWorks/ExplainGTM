<?php
  function containerdeets($container){
    echo '<div id="containerTopDeets"><h2>' . $container['containerVersion']['container']['name'] . '</h2>';
    echo '<h3>' . $container['containerVersion']['container']['publicId'];
    echo '<span style="font-size:12px"> - ';
    if($container['containerVersion']['containerVersionId']==='0'){
      echo 'Draft version';
    }else{
      echo 'version ' . $container['containerVersion']['containerVersionId'];
    }
    echo '</span></h3></div>';
  
    if($container['containerVersion']['container']['notes']!==''){
      echo '<a href="#containerNotes" id="containerNotesLink">Notes</a><br />';
      echo '<div id="containerNotes" style="border:2px solid green;display:none">';
      echo '<textarea class="form-control" cols="150" rows="30">' . $container['containerVersion']['container']['notes'] . '</textarea></div>';
    }
  }
?>