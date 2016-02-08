<?php
  /********************************************************************************
  * Build a trigger link
  *********************************************************************************/
  function buildTriggerLink($triggerId,$prepend,$append){
    global $trigLibrary, $trigTypes;
    $triggerLink = '';
    
    foreach($trigTypes as $trigTypeKey => $trigTypeVal){
      foreach($trigLibrary[$trigTypeVal] as $trigKey => $trigVal){
        if($trigLibrary[$trigTypeVal][$trigKey]['triggerId']===$triggerId){
          $triggerLink = $triggerLink.$prepend.'<a href="#trig'.$trigLibrary[$trigTypeVal][$trigKey]['triggerId'].'" class="triggerDetail" ';
          $triggerLink = $triggerLink.'onclick="jQuery(\'.assetList\').hide();jQuery(\'.assetDetail\').hide();jQuery(\'.assetContainer\').hide();jQuery(\'#trigs\').toggle();jQuery(\'#trigType'.$trigTypeVal.'\').next().toggle();jQuery(\'#trigDetail'.$trigLibrary[$trigTypeVal][$trigKey]['triggerId'].'\').toggle()">';
          $triggerLink = $triggerLink.$trigLibrary[$trigTypeVal][$trigKey]['name'].'</a>'.$append;
        }
      }
    }
  
    return $triggerLink;
  }
  
  function triggering(){
    global $trigLibrary, $trigTypes;
    $trigs = '';
    $trigTotal = 0;
    $trigTypesTotal = [];
    
    foreach($trigTypes as $typeKey => $typeVal){
      $trigTypesTotal[$typeVal] = 0;
      $trigTotal=$trigTotal+count($trigLibrary[$typeVal]);
      $trigTypesTotal[$typeVal]=count($trigLibrary[$typeVal]); 
    }
    
    foreach($trigTypes as $trigTypekey => $trigTypeVal){
      $trigTypeHuman = translateToHuman($trigTypeVal);
      $trigTypeHuman = $trigTypeHuman !== false ? $trigTypeHuman : $trigTypeVal.' triggers';
  
      $trigs = $trigs.'<a href="#trigType'.$trigTypeVal.'" class="trigTypeLink" id="trigType'.$trigTypeVal.'" data-track="'.$trigTypeVal.'"><h4>'.$trigTypeHuman.' ('.$trigTypesTotal[$trigTypeVal].')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($trigLibrary[$trigTypeVal] as $trigKey => $trig){
        $trigs = $trigs.'<li><a href="#trig'.$trig['triggerId'].'" id="trig'.$trig['triggerId'].'" data-index='.$trigTypeVal.' class="trigDetail">'.$trig['name'].'</a>';
        $trigs = $trigs.'<div style="min-height:100px;display:none;" id="trigDetail'.$trig['triggerId'].'"  class="assetDetail container">';
        
        switch($trig['type']){
          case 'PAGEVIEW':
              $trigs = $trigs.trigDetail($trig,'This is a Pageview trigger. Tags fire based on pageview conditions.','This trigger will <b>fire</b> when a page is viewed');
          break;
  
          case 'CUSTOM_EVENT':
              $trigs = $trigs.trigDetail($trig,'This is a <i>Custom Event trigger</i>. Tags will fire based on customised conditions:','This trigger will <b>fire</b> when the "<b>'.$trig['customEventFilter'][0]['parameter'][1]['value'].'</b>"  event happens,');
          break;
  
          case 'DOM_READY':
              $trigs = $trigs.trigDetail($trig,'This is a trigger that fires when all the HTML, styles and JavaScript have loaded.','This trigger will fire on the "gtm.dom" event - when the document is loaded ');
          break;
  
         case 'JS_ERROR':
            $trigs = $trigs.trigDetail($trig,'This is a JavaScript error trigger.',array_key_exists('filter',$trig) ? 'Tags are fired when a JavaScript error happens' : 'Tags are fired on <b>ALL</b> JavaScript errors.');
          break;
  
          case 'CLICK':
              $trigs = $trigs.trigDetail($trig,'This is a Click trigger. Tags fire based on click conditions.','This trigger will <b>fire</b> when a user clicks any element on the page ');
          break;
  
          case 'LINK_CLICK':
              $trigs = $trigs.trigDetail($trig,'This is a Link Click trigger. Tags fire based on link click conditions.','This trigger will <b>fire</b> when a link is clicked ');
          break;
          
          case 'FORM_SUBMISSION':
              $trigs = $trigs.trigDetail($trig,'This is a form submission trigger.','This trigger will <b>fire</b> when a form is submitted ');
          break;
          
          case 'WINDOW_LOADED':
              $trigs = $trigs.trigDetail($trig,'This is a Window Loaded trigger. Tags fire based on the compete loading of <b>everything</b> in the window - HTML, CSS, JavaScript...everything.','This trigger will <b>fire</b> when the window is loaded');
          break;
  
          default:
              $trigs = $trigs.getDetailsforUnknownTrigger($trig);
        }
      }
      $trigs = $trigs.'</ul></div>';
    }
    
    $trigsOrnotrigs = $trigs!=='' ? 'Your triggers are:</h3><ul>'.$trigs.'</ul>' : 'You have no triggers...</h3>';
    
    echo '<h2><a href="#trigLink" id="trigLink" class="assetListLink" data-track="Triggers"  title="The things that decide when the tags do the work">Triggers ('.$trigTotal.')</a></h2><br /><div id="trigs" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';				
  			
    echo '<div class="container" style="margin:5px" id="triglist"><h3 style="margin:5px;" id="trigHeader">'.$trigsOrnotrigs.'</div></div><br />';
  }
  
  /******************************************************************************
  Trigger Details
  ******************************************************************************/
  function trigDetail($trig, $typeString = '', $event = ''){
    global $usedTriggers, $untaggedTriggers, $tagTypes, $tagLibrary;
      
    $deets = '<h3>Details for trigger <i>'.$trig['name'].'</i></h3>';
    
    $deets = $deets.'<a href="https://tagmanager.google.com/#/container/accounts/'.$trig['accountId'].'/containers/'.$trig['containerId'].'/triggers/'.$trig['triggerId'].'"';
    $deets = $deets.' style="position:relative;float:right;top:-40px;right:10px;" class="triggerEdit">edit</a>';
    
    $deets = $deets.'<table class="table table-bordered table-striped">';
    $deets = $deets.'<tr><th>Name</th><td>'.$trig['name'].'</td></tr>';
    $deets = $deets.'<tr><th>Type</th><td>'.$typeString.'</td></tr>';
    
    $deets = $deets.'<tr><th>How it works</th><td>';
    
    if($trig['type']==='FORM_SUBMISSION' || $trig['type']==='LINK_CLICK'){
      $action = $trig['type']==='FORM_SUBMISSION' ? 'submit' : 'click';
      $deets = $deets.listeningOn($trig,$action);
    
      $deets = $deets.$event;
      $deets = $deets.firingOn($trig);
    
      $deets = $deets.waitAndValidate($trig,$action);
      $deets = $deets.'</td></tr>';
    }else{
      $deets = $deets.$event;
      $deets = $deets.firingOn($trig);
    }

    $deets = $deets.'<tr><th>Where it\'s used</th>';
    
    if(array_search($trig['triggerId'],$usedTriggers)===false){
      $deets = $deets.'<td>This trigger isn\'t used anywhere...</td>';
      array_push($untaggedTriggers,$trig);
    }else{
      $firing = '';
      $blocking = '';
      $tTag = [];
      foreach($tagTypes as $ttKey => $ttVal){
        foreach($tagLibrary[$ttVal] as $tKey => $tag){
          if(array_key_exists('firingTriggerId',$tag)){
            foreach($tag['firingTriggerId'] as $ftKey => $ftVal) {
              if($trig['triggerId']===$ftVal){
                $tTag = findTagById($tag['tagId']);
                
                $firing = $firing.'<li><a href="#tag'.$tTag['tagId'].'" id="ft'.$tTag['tagId'].'" data-asset="tag" data-index='.$tTag['tagId'].' data-type='.$tTag['type'].' class="Detail">'.$tTag['name'].'</a></li>';
              }
            }
          }
          
          if(array_key_exists('blockingTriggerId',$tag)){
            foreach($tag['blockingTriggerId'] as $btKey => $btVal) {
              if($trig['triggerId']===$btVal){
                $tTag = findTagById($tag['tagId']);
                $blocking = $blocking.'<li><a href="#tag'.$tTag['tagId'].'" id="bt'.$tTag['tagId'].'" data-asset="tag" data-index='.$tTag['tagId'].' data-type='.$tTag['type'].' class="Detail">'.$tTag['name'].'</a></li>';
              }
            }
          }
        }
      }

      if($firing!=='' || $blocking!==''){
        $deets = $deets.'<td>';
        
        if($firing!==''){
          $deets = $deets.'This trigger is used by the following tags as a firing trigger:<ul>'.$firing.'<br /></ul>';
        }
        if($blocking!==''){
          $deets = $deets.'This trigger is used by the following tags as a blocking trigger:<ul>'.$blocking.'</ul>';
        }
        
        $deets = $deets.'</td></tr>';
      }
    }
    
    $deets = $deets.'<tr><th>Where it lives</th>';
    if(array_key_exists('parentFolderId',$trig)){
      $deets = $deets.'<td>'.folderisation($trig['parentFolderId'],'trigger').'</td></tr>';
    }else{
      $deets = $deets.'<td>'.folderisation(null,'trigger').'</td></tr>';
    }

    $deets = $deets.'</table>';
    return $deets;
  }
  
  function listeningOn($trig,$action){
    $listenOns = [];
    $listeningDeets = '';
    
    if(array_key_exists('autoEventFilter',$trig)){
      $listenOns = $trig['autoEventFilter'];
      $listeningDeets = 'This trigger will <i>listen</i> for '.$action.'s ';
    
      foreach($listenOns as $listenKey => $clause){
        $deetsDone = false;
  
        if($clause['parameter'][0]['value'] == '{{Page Path}}' || $clause['parameter'][0]['value'] == '{{Page URL}}'){
          if($clause['type'] == 'MATCH_REGEX'){
            if($clause['parameter'][1]['value'] == '.*' || $clause['parameter'][1]['value'] == '.+'){
              $listeningDeets = $listeningDeets.' on every page';
              $deetsDone = true;
            } 
          }        
        }
    
        if(!$deetsDone){
            $listeningDeets = $listeningDeets.'when '.variablise($clause['parameter'][0]['value'],$trig);
            $humanOperator = translateToHuman($clause['type'],$trig);
            $humanOperator = $humanOperator !== false ? $humanOperator : $clause['type'];
            $listeningDeets = $listeningDeets.$humanOperator; 
            $humanPredicate = translateToHuman($clause['parameter'][1]['value'],$trig);
            $humanPredicate = $humanPredicate !== false ? $humanPredicate : $clause['parameter'][1]['value'];
            $listeningDeets = $listeningDeets.'<b>'.$humanPredicate.'</b>';
        }
    
        if((array_search($clause,$listenOns)+1)<count($listenOns)){
            $listeningDeets = $listeningDeets.' <br />AND</br />';
        }else{
            $listeningDeets = $listeningDeets.'.<br /><br />';
        }
      }
      if(empty($listenOns)){
        $listeningDeets = $listeningDeets.' on all pages.';
      }
    }else{
      $listeningDeets = 'This trigger will <i>listen</i> for '.$action.' on all pages.';
    }
  
    return $listeningDeets;
  }
  
  function firingOn($trig){
    $firingDeets = '';
    
    $fireOns = array_key_exists('filter',$trig) ? $trig['filter'] : null;
    if($fireOns!==null && count($fireOns)>0){
      $firingDeets = $firingDeets.'<br />and<br />';
      foreach($fireOns as $fireKey => $clause){
        //translate field

        $fieldToCompare = variablise($clause['parameter'][0]['value'],$trig,true);
        $fieldToCompare = $fieldToCompare !== false ? $fieldToCompare : $clause['parameter'][0]['value'];
        $firingDeets = $firingDeets.$fieldToCompare;
    
        //translate comparison
        if(array_key_exists(2,$clause['parameter']) && $clause['parameter'][2]['key']==='negate'){
          $firingDeets = $firingDeets.' does not '.substr(translateToHuman($clause['type'],$trig),0,strlen(translateToHuman($clause['type'],$trig))-2).' ';
        }else{
          if($clause['type']==='MATCH_REGEX'){
            if(array_key_exists(2,$clause['parameter'])){
              if($clause['parameter'][2]['key']==='ignore_case' && $clause['parameter'][2]['value']==='true'){
                $firingDeets = $firingDeets.translateToHuman('REGEX_IGNORE_CASE',$trig);
              }else{
                $firingDeets = $firingDeets.translateToHuman($clause['type'],$trig);
              }
            }else{
              $firingDeets = $firingDeets.translateToHuman($clause['type'],$trig);
            }
          }
        }
    
        //condition
        $firingDeets = $firingDeets.'<b>'.$clause['parameter'][1]['value'].'</b>';
        
        if((array_search($clause,$fireOns)+1)<count($fireOns)){
            $firingDeets = $firingDeets.' <br />and</br />';
        }else{
            $firingDeets = $firingDeets.'.<br /><br />';
        }
      }
    }else{
      $firingDeets = $firingDeets.'.<br /><br />';
    }
    
    return $firingDeets;
  }
  
  function waitAndValidate($trig,$action){
    $wvDeets = '';
    if($trig['waitForTags']['value']=='true'){
      $wvDeets = $wvDeets.'When a user '.$action.'s, the website will wait for ';
      if(strpos($trig['waitForTagsTimeout']['value'],'{')===false){
        $wvDeets = $wvDeets.'up to '.($trig['waitForTagsTimeout']['value']/1000).' seconds';
      }else{
        $wvDeets = $wvDeets.'the number of seconds specified by the '.variablise($trig['waitForTagsTimeout']['value'],$trig).' variable ';
      }
      $wvDeets = $wvDeets.' before going to the next page.<br /><br />';
    }
  
    if($trig['checkValidation']['value']=='true'){
      $wvDeets = $wvDeets.'When a user '.$action.'s, tags will only fire if the '.$action.' is considered a <i>valid</i> action.<br />';
    }
    
    return $wvDeets;
  }
  
  /******************************************************************************
  Unknown Trigger
  ******************************************************************************/
  function getDetailsforUnknownTrigger($trig){
    $deets = "I don't know about this trigger type so here are the raw details:";
  
    $deets = $deets.json_decode($trig, true);
  
    return $deets;
  }
  
  /******************************************************************************
  find a trigger by ID
  ******************************************************************************/
  function findTrigById($trigId){
    $trig = [];
    global $trigLibrary, $trigTypes;  
      
    foreach($trigTypes as $typeKey => $typeVal){
      foreach($trigLibrary[$typeVal] as $trigKey => $trigVal){
        $trig = $trigVal['triggerId']===$trigId ? $trigVal : $trig;
      }
    }
      
    return $trig;
  }
?>