<?php
  function udving(){
    global $udvLibrary, $udvTypes;
    
    $udvs = '';
    $udvTotal = 0;
    $udvTypesTotal = [];
    foreach($udvTypes as $udvTypeKey => $udvTypeVal){
      $udvTypesTotal[$udvTypeVal] = 0;
      $udvTotal=$udvTotal+count($udvLibrary[$udvTypeVal]);
      $udvTypesTotal[$udvTypeVal]=count($udvLibrary[$udvTypeVal]); 
    }
    
    echo '<h2><a href="#udvLink" id="udvLink" class="assetListLink" data-track="User Defined Variables" title="Data your developers have surfaced for you">User-defined variables ('.$udvTotal.')</a></h2><br />';
  	echo '<div id="udvs" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';	
  	
  	foreach($udvTypes as $udvTypeIndex => $udvTypeVal){
      $udvTypeHuman = translateToHuman($udvTypes[$udvTypeIndex]);
      $udvTypeHuman = $udvTypeHuman !== false ? $udvTypeHuman : $udvTypes[$udvTypeIndex] + ' variables';
  
      $udvs = $udvs.'<a href="#udvType'.$udvTypes[$udvTypeIndex].'" class="udvTypeLink" data-track="'.$udvTypes[$udvTypeIndex].'" id="udvType'.$udvTypes[$udvTypeIndex].'"><h4>'.$udvTypeHuman.' ('.$udvTypesTotal[$udvTypeVal].')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($udvLibrary[$udvTypes[$udvTypeIndex]] as $udvIndex => $udvVal){
        $udv = $udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex];

        $udvs = $udvs.'<li><a href="#udv'.$udv['variableId'].'" id="udv'.$udv['variableId'].'" data-index='.$udvTypes[$udvTypeIndex].' class="udvDetail">'.$udv['name'].'</a>';
        $udvs = $udvs.'<div style="min-height:100px;display:none;" id="udvDetail'.$udv['variableId'].'"  class="assetDetail container">';
        
        switch($udv['type']){
          case 'v':
              $udvs = $udvs.udvDetail($udv,'This is a "Data Layer" user defined variable (<b>Technical</b>).','If the "'.getParamTemplate($udv,'name')['value'].'" variable is found on the dataLayer, "'.$udv['name'].'" will return the value.<br /><br />In case you\'re interested, this is a <i>version '.getParam($udv,'dataLayerVersion').' variable</i>. This probably matters more to your developers though.');
          break;
    
          case 'u':
              $udvs = $udvs.udvDetail($udv,'This is a "URL" user defined variable.');
          break;
    
          case 'e':
              $udvs = $udvs.udvDetail($udv,'This is a Custom Event user defined variable.','This variable will return the name of the event on the dataLayer.<br />Why not just use the \'Event\' built in variable? It does the same thing.');
          break;
    
          case 'smm':
              $udvs = $udvs.udvDetail($udv,'This is a "Lookup Table" user defined variable.','The value returned by "'.$udv['name'].'" will depend on the value of the <i>input</i> variable.<br /><br />If the value of the <i>input</i> variable is matched <b>exactly</b> with a value in the left hand side of the table, the value on the right hand side will be returned.<br /><br />');
          break;
    
          case 'jsm':
              $udvs = $udvs.udvDetail($udv,'This is a "Custom JavaScript" user defined variable. (<b>Technical!</b>).','');
          break;
    
          case 'f':
              $udvs = $udvs.udvDetail($udv,'This is an HTTP Referrer user defined variable.');
          break;
          
          case 'k':
              $udvs = $udvs.udvDetail($udv,'This is a "1st Party Cookie" user defined variable.','The variable will return the value of the 1st party cookie "'.variablise(getParamTemplate($udv,'name')['value'],$udv).'".');
          break;
          
          case 'aev':
              $udvs = $udvs.udvDetail($udv,'This is an "Auto Event" user defined variable.');
          break;
          
          case 'c':
              $udvs = $udvs.udvDetail($udv,'This is a "Constant" user defined variable.','The variable returns the value "'.getParam($udv,'value').'".');
          break;
          
          case 'ctv':
              $udvs = $udvs.udvDetail($udv,'This is a "Container Version" user defined variable.','The version number of the GTM container used on the page will be returned.<br />Consider using the "Container Version" Built In Variable instead.');
          break;
          
          case 'cid':
              $udvs = $udvs.udvDetail($udv,'This is a "Container ID" user defined variable.','The ID of the GTM container used on the page will be returned.<br />Consider using the "Container ID" Built In Variable instead.');
          break;
          
          case 'dbg':
              $udvs = $udvs.udvDetail($udv,'This is a "debug mode" user defined variable.','If the container is viewed in GTM Preview mode, this variable returns a value of true, otherwise it returns a value of false.');
          break;
          
          case 'd':
              $udvs = $udvs.udvDetail($udv,'This is a "DOM Element" user defined variable.','The variable returns data about HTML elements on the page.');
          break;
          
          case 'j':
              $udvs = $udvs.udvDetail($udv,'This is a JavaScript user defined variable. (<b>Technical</b>)','If the JavaScript variable "'.getParamTemplate($udv,'name')['value'].'" exists on the page, "'.$udv['name'].'" will return the value of the variable.');
          break;
          
          case 'r':
              $udvs = $udvs.udvDetail($udv,'This is a "Randon Number" user defined variable.');
          break;
    
          default:
              $udvs = $udvs.getUdvDetailsforUnknown($udv);
        }
      }
      $udvs = $udvs.'</ul></div>';
    }

    $udvsOrnoudvs = $udvs!=='' ? 'Your user defined variables are:</h3><ul>'.$udvs.'</ul>' : 'You have no user defined variables...</h3>';
    
    echo '<div class="container" style="margin:5px" id="udvlist"><h3 style="margin:5px;" id="udvHeader">'.$udvsOrnoudvs.'</div></div><br />';
  }
  
  /******************************************************************************
  User Defined Variable Details
  ******************************************************************************/
  function udvDetail($udv,$typeString = '',$usage = ''){
    $attributeName = '';
    $selectorType = '';
    
    $deets = '<h3>Details for User-defined variable <i>'.$udv['name'].'</i></h3>';

    $deets = $deets.'<a href="https://tagmanager.google.com/#/container/accounts/'.$udv['accountId'].'/containers/'.$udv['containerId'].'/variables/'.$udv['variableId'].'"';
    $deets = $deets.' style="position:relative;float:right;top:-40px;right:10px;" class="udvEdit">edit</a>';
    
    $deets = $deets.'<table class="table table-bordered table-striped">';
    $deets = $deets.'<tr><th>Name</th><td>'.$udv['name'].'</td></tr>';
    $deets = $deets.'<tr><th>What it\'s for</th><td>'.$typeString.'</td></tr>';
    
    $deets = $deets.'<tr><th>What it does</th>';
    $deets = $deets.'<td>'.$usage;
    
    if($udv['type']==='d'){
      if(getParamTemplate($udv,'attributeName')!==''){
        $attributeName = array_key_exists('value',getParamTemplate($udv,'attributeName')) ? getParamTemplate($udv,'attributeName')['value'] : '';
      }
      
      if(getParamTemplate($udv,'selectorType')!==''){
        $selectorType = array_key_exists('value',getParamTemplate($udv,'selectorType')) ? getParamTemplate($udv,'selectorType')['value'] : '';
      }
      
      if($selectorType !== ''){
        $selectorType = getParamTemplate($udv,'selectorType')['value'];
      
        if($selectorType==='CSS'){
          $elementSelector = getParamTemplate($udv,'elementSelector')['value'];
          $deets = $deets.'<br /><br />This variable will extract the "<b>'.variablise($attributeName,$udv).'</b>" attribute from the first element on the page that matches the CSS selector "<b>'.variablise($elementSelector,$udv).'</b>".<br /><br />';
          $deets = $deets.'<b>Warning</b>: This is not supported in IE7. IE8 and only supports CSS 2.1 selectors.<br />';
        }
        
        if($selectorType==='ID'){
          $elementId = getParamTemplate($udv,'elementId')['value'];
          $deets = $deets.'<br /><br />This variable will extract the "<b>'.variablise($attributeName,$udv).'</b>" attribute from the first element on the page with the ID "<b>'.variablise($elementId,$udv).'</b>".<br /><br />';
          $deets = $deets.'Of course, there should only be one element with this ID so if there are multiple elements with the same ID, have a word with your front-end development team.<br />';
        }
      }
    }
    
    if($udv['type']==='aev'){
      $varType = getParamTemplate($udv,'varType')['value'];
      $hostSource = 'Element URL';
  
      switch($varType){
        case 'ELEMENT':
          $deets = $deets.'The variable returns the value of the "gtm.element" key on the data layer. If populated by an Auto-Event, the result will be the DOM element that triggered the event.<br /><br />In other words, the <i>thing</i> on the page that was clicked or the form that was submitted.<br /><br />';
        break;
        
        case 'ATTRIBUTE':
          $deets = $deets.'The variable returns the value of the "'.getParamTemplate($udv,'attribute')['value'].'" attribute for the element that triggered the last click or form submit event.<br /><br /><br />';
        break;
        
        case 'CLASSES':
          $deets = $deets.'The variable returns the value of the "gtm.elementClasses" key on the data layer. If populated by an Auto-Event, the result will be the "class" attribute of the DOM element that triggered the event (the <i>thing</i> that was clicked or the form that was submitted).<br /><br />';
        break;
        
        case 'ID':
          $deets = $deets.'The variable returns the value of the "gtm.elementId" key on the data layer. If populated by an Auto-Event, the result will be the "id" attribute of the DOM element that triggered the event (the <i>thing</i> that was clicked or the form that was submitted).<br /><br />';
        break;
        
        case 'TARGET':
          $deets = $deets.'The variable returns the value of the "gtm.elementTarget" key on the data layer. If populated by an Auto-Event, the result will be the "target" attribute of the DOM element that triggered the event (the <i>thing</i> that was clicked or the form that was submitted).<br /><br />';
        break;
        
        case 'TEXT':
          $deets = $deets.'The variable returns the value of the "gtm.element" key on the data layer and its text content if there is any.<br />If populated by a click or link-click Auto-Event, the text content will be the "innerText" or the "textContent" attribute of the DOM element that triggered the event.<br /><b>Technical:</b> The text will be trimmed and normalised (white-spaces will be consolidated) to account for browsers variations.<br /><br />';
        break;
        
        case 'URL':
          $deets = $deets.'The variable returns the value of the "gtm.elementUrl" key on the data layer.<br />If populated by an Auto-Event, the result will be the "href" or "action" attribute of the DOM element that triggered the event, depending on the type of element (the <i>thing</i> that was clicked or the form that was submitted).<br /><br />';
          $URLComponent = getParamTemplate($udv,'component')!=='' ? getParamTemplate($udv,'component')['value'] : '';
            
          if($URLComponent !== ''){
            switch($URLComponent){
              case 'HOST':
                $deets = $deets.'The "hostname" part of the '.$hostSource; 
                if(getParam($udv,'stripWww')!==null&&getParam($udv,'stripWww')==='true'){
                  $deets = $deets.' - minus the www part - ';
                }
                $deets = $deets.' will be returned.<br />';
              break;
              
              case 'PATH':        
                $deets = $deets.'The path of the '.$hostSource.' will be returned.<br />';
                
              if(strlen(getParamList($udv,'defaultPages'))>0){
                  $deets = $deets.'The list of default pages to ignore is:<ul>';
                  $defaultPages = getParamList($udv,'defaultPages');
                  
                  foreach($defaultPages as $defPageIndex => $defPageVal){
                    $deets = $deets.'<li>'.$defaultPages[$defPageIndex]['value'].'</li>';
                  }
                  $deets = $deets.'</ul><br />';
                  $deets = $deets.'The last non-directory segment in the path will be stripped if it matches any of the default pages. For instance, if a default page is "index.html" and the URL is "http://a.com/x/index.html", the variable value will be "/x/".<br />';
                }
              break;
              
              case 'QUERY':
                $queryKey = '';
                
                if(getParamTemplate($udv,'queryKey')!==''){
                  $queryKey = variablise(getParamTemplate($udv,'queryKey')['value'],$udv);
                }
                
                $deets = $deets.'The value of the querystring variable (the part after the ? on the '.$hostSource.') "'.$queryKey.'" will be returned.<br />';
              break;
              
              case 'FRAGMENT':
                $deets = $deets.'The fragment (the part after the # on the '.$hostSource.') will be returned.<br />';
              break;
              
              case 'PORT':
                $deets = $deets.'The port (80 or 443 for example) of the '.$hostSource.' will be returned.<br />';
              break;
              
              case 'PROTOCOL':
                $deets = $deets.'The protocol (http or https) of the '.$hostSource.' will be returned.<br />';
              break;
              
              case 'URL':
                $deets = $deets.'The full URL of the '.$hostSource.' will be returned.<br />';
              break;
            }
          }
        break;
        
        case 'HISTORY_NEW_URL_FRAGMENT':
          $deets = $deets.'The value is determined by reading the "gtm.newUrlFragment" key from the data layer. If populated by an Auto-Event, the result will be the new URL fragment set on a history change event (Used by AJAX forms - <b>Technical</b>).<br />';
        break;
        
        case 'HISTORY_OLD_URL_FRAGMENT':
          $deets = $deets.'The value is determined by reading the "gtm.oldUrlFragment" key from the data layer. If populated by an Auto-Event, the result will be the old URL fragment set on the previous history change event (Used by AJAX forms - <b>Technical</b>).<br />';
        break;
        
        case 'HISTORY_NEW_STATE':
          $deets = $deets.'The value is determined by reading the "gtm.newHistoryState" key from the data layer. If populated by an Auto-Event, the result will be the new history state set on a history change event (Used by AJAX forms - <b>Technical</b>).<br />';
        break;
        
        case 'HISTORY_OLD_STATE':
          $deets = $deets.'The value is determined by reading the "gtm.oldHistoryState" key from the data layer. If populated by an Auto-Event, the result will be the old history state set on the previous history change event (Used by AJAX forms - <b>Technical</b>).<br />';
        break;
        
        case 'HISTORY_CHANGE_SOURCE':
          $deets = $deets.'The value is determined by reading the "gtm.historyChangeSource" key from the data layer. If populated by an Auto-Event, the result will be the source of the gtm.historyChange event, which can be: "popstate", "pushState", "replaceState" or "polling" (Used by AJAX forms - <b>Technical</b>).<br />';
        break;
      }
    }
    
    if($udv['type']==='k'){
      if(getParam($udv,'decodeCookie')==='true'){
        $deets = $deets."<br /><br />The value of the cookie will be URI-decoded. This is a nice thing to do to make your data human readable.<br />";
      }else{
        $deets = $deets."<br /><br />The value of the cookie will not be URI-decoded.<br />";
        $deets = $deets."If enabled, the value of the cookie 'xxx%3Dyyy' would become 'xxx=yyy'.<br />";
        $deets = $deets."This might be a nice thing to do to make your data human readable.<br />";
      }
    }
    
    if($udv['type']==='f'){
      if(array_key_exists('parameter',$udv)){
        $component = getParam($udv,'component');
        if($component==='QUERY'||$component==='FRAGMENT'){
          $deets = $deets.'It returns the '.translateToHuman($component,$udv).' part of the referring page URL using the ';
          if($component==='QUERY'){
            $deets = $deets.getParam($udv,'queryKey').' querystring parameter.<br />';
          }else{
            $deets = $deets.' part of the URL after the "#" without the leading "#".<br />';
          }
          
        }else{
          $deets = $deets.'It returns the '.translateToHuman($component,$udv).' the user just came from.<br />';
        }
        
        if(getParam($udv,'stripWww')==='true'){
          $deets = $deets.'The returned value will have the "www" part of the hostname stripped off.';
        }
      }else{
        $deets = $deets.'It returns the full URL of the last page the user was on.<br /><br />';
        $deets = $deets.'Why not just use the \'Referrer\' built in variable? It does the same thing.';
      }
    }
    
    if($udv['type']==='u'){
      $hostSource = 'URL';
      $URLComponent = getParamTemplate($udv,'component')['value'];
      $hostSource = getParamTemplate($udv,'customUrlSource') !== '' ? translateToHuman(getParamTemplate($udv,'customUrlSource')['value'],$udv) : $hostSource;
      
      switch($URLComponent){
        case 'HOST':
          $deets = $deets.'The "hostname" part of the '.$hostSource; 
          if(getParam($udv,'stripWww')!==null&&getParam($udv,'stripWww')==='true'){
            $deets = $deets.' - minus the www part - ';
          }
          $deets = $deets.' will be returned.<br />';
        break;
        
        case 'PATH':        
          $deets = $deets.'The path of the '.$hostSource.' will be returned.<br />';
          
          if(strlen(getParamList($udv,'defaultPages'))>0){
            $deets = $deets.'The list of default pages to ignore is:<ul>';
            $defaultPages = getParamList($udv,'defaultPages');
            
            foreach($defaultPages as $defPageIndex => $defPageVal){
              $deets = $deets.'<li>'.$defaultPages[$defPageIndex]['value'].'</li>';
            }
            $deets = $deets.'</ul><br />';
          }
        break;
        
        case 'QUERY':
          $queryKey = '';
          
          if(getParamTemplate($udv,'queryKey')!==''){
            $queryKey = variablise(getParamTemplate($udv,'queryKey')['value'],$udv);
          }
          
          $deets = $deets.'The value of the querystring variable (the part after the ? on the '.$hostSource.') "'.$queryKey.'" will be returned.<br /><br />';
        break;
        
        case 'FRAGMENT':
          $deets = $deets.'The fragment (the part after the # on the '.$hostSource.') will be returned.<br />';
        break;
        
        case 'PORT':
          $deets = $deets.'The port (80 or 443 for example) of the '.$hostSource.' will be returned.<br />';
        break;
        
        case 'PROTOCOL':
          $deets = $deets.'The protocol (http or https) of the '.$hostSource.' will be returned.<br />';
        break;
        
        case 'URL':
          $deets = $deets.'The full URL of the '.$hostSource.' will be returned.<br />';
        break;
      }      
    }
    
    if($udv['type']==='smm'){
      $deets = $deets.'The <i>input</i> variable for the lookup table is '.variablise(getParamTemplate($udv,'input')['value'].'.<br /><br />',$udv);
          
      $deets = $deets.'<table class="table table-bordered table-striped"><tr><th>'.variablise(getParamTemplate($udv,'input')['value'].'',$udv).'</th><th>Returned value</th></tr>';
      $lookup = getParamList($udv,'map');
      
      foreach($lookup as $lRow => $lRowVal){
        $deets = $deets.'<tr><td>'.variablise($lookup[$lRow]['map'][0]['value'],$udv).'</td>';
        $deets = $deets.'<td>'.variablise($lookup[$lRow]['map'][1]['value'],$udv).'</td></tr>';
      }
    
      $deets = $deets.'</table>';
    
      if(getParamTemplate($udv,'defaultValue')!== '' && getParamTemplate($udv,'defaultValue') !== null){
        $deets = $deets.'The default value returned if no matches are found in the table is "'.variablise(getParamTemplate($udv,'defaultValue')['value'],$udv).'".<br />';
      }
    }
    
    if($udv['type']==='jsm'){
      $varRefs = '';
    
      $rawJS = getParam($udv,'javascript');
      
      $varRefs = getVarRefs($rawJS,$udv);
      
      findPushyVars($udv);
      
      if($varRefs !== ''){
        $deets = $deets.'Variables referenced in this variable:<br />'.$varRefs; 
      }
      
      if($rawJS !== ''){
          $deets = $deets.'<p style="margin:5px;"><textarea class="form-control" rows="10" cols="60" disabled="disabled">'.$rawJS.'</textarea></p><br />';
      }
    }
    
    if($udv['type']!=='c' && $udv['type']!=='smm'){
      $deets = $deets.defaultValue($udv);
    }
    
    $deets = $deets.'<tr><th>Where it\'s used</th>';
    $deets = $deets.getUsage($udv);
    
    $deets = $deets.'<tr><th>Where it lives</th>';
    if(array_key_exists('parentFolderId',$udv)){
      $deets = $deets.'<td>'.folderisation($udv['parentFolderId'],'User Defined Variable').'</td></tr>';
    } else {
      $deets = $deets.'<td>'.folderisation(null,'User Defined Variable').'</td></tr>';
    } 

    $deets = $deets.'</table>';
    return $deets;
  }
  
  function defaultValue($udv){
    $defaultValue = '';
    
    if(!array_key_exists('setDefaultValue',$udv)){
      $defaultValue = '<br />The variable does not have a default value.</td></tr>';
    }else{
      $defaultValue = '<br />The default value is "'.getParam($udv,'defaultValue').'"</td></tr>';
    }
    
    return $defaultValue;
  }
  
  /******************************************************************************
  Does the variable try to push messages onto the dataLayer?
  ******************************************************************************/
  function findPushyVars($udv){
    $pushyRe = "/dataLayer.push/";
    $pushyArray = [];
    
    global $variablesThatMessage;
    
    $raw = getParam($udv,'javascript');
    preg_match_all($pushyRe, $raw, $pushyArray);
    
    if(strpos($raw,'dataLayer')>-1 && count($pushyArray)>0){
      array_push($udv,$variablesThatMessage);
    }
  }
  
  /******************************************************************************
  Where is the variable used?
  ******************************************************************************/
  function getUsageEntity($udv,$entity){
    $euDeets = '';
    $entityAsset = '';
    $entityId = '';
    global $udvUsage;
    
    if(array_key_exists($entity,$udvUsage[$udv['variableId']]) && !empty($udvUsage[$udv['variableId']][$entity]) && count($udvUsage[$udv['variableId']][$entity]) > 0){
      $euDeets = $euDeets.'This '.translateToHuman($udv['type'],$udv).' variable is used in the following '.$entity.':<ul>';
      
      foreach($udvUsage[$udv['variableId']][$entity] as $usedKey => $usedVal){
        if($entity==='variables'){
          $entityAsset = 'udv';
          $entityId = $udvUsage[$udv['variableId']][$entity][$usedKey]['variableId'];
        }
        if($entity==='triggers'){
          $entityAsset = 'trig';
          $entityId = $udvUsage[$udv['variableId']][$entity][$usedKey]['triggerId'];
        }
        
        if($entity==='tags'){
          $entityAsset = 'tag';
          $entityId = $udvUsage[$udv['variableId']][$entity][$usedKey]['tagId'];
        }
        $entityType = $udvUsage[$udv['variableId']][$entity][$usedKey]['type'];
        $entityName = $udvUsage[$udv['variableId']][$entity][$usedKey]['name'];
        
        $euDeets = $euDeets.'<li><a href="#'.$entityAsset.$entityId.'" id="usage'.$entityId.'" data-udv="'.$udv['variableId'].'" data-asset="'.$entityAsset.'" data-index='.$entityId.' data-type='.$entityType.' class="usageDetail">'.$entityName.'</a></li>';
      }
      $euDeets = $euDeets.'</ul><br />';
    }
    
    return $euDeets;
  }
  
  function getUsage($udv){
    $uDeets = '';
    
    global $udvUsage,$udvTypes,$udvLibrary;
  
    foreach($udvTypes as $udvTypeIndex => $udvTypeVal){
      foreach($udvLibrary[$udvTypes[$udvTypeIndex]] as $udvIndex => $udvs){
        if($udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex]['variableId']!==$udv['variableId'] && strpos(json_encode($udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex]),$udv['name'])>-1){
          if(!array_key_exists($udv['variableId'],$udvUsage)){
            $udvUsage[$udv['variableId']] = [];
            $udvUsage[$udv['variableId']]['tags'] = [];
            $udvUsage[$udv['variableId']]['triggers'] = [];
            $udvUsage[$udv['variableId']]['variables'] = [];
          }
          
          if(usageRecorded($udvUsage[$udv['variableId']]['variables'],$udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex],'variableId')===false){
            array_push($udvUsage[$udv['variableId']]['variables'],$udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex]);
          }
        }
      }
    }

    if(array_key_exists($udv['variableId'],$udvUsage)!==false){
      $uDeets = $uDeets.getUsageEntity($udv,'tags');
      $uDeets = $uDeets.getUsageEntity($udv,'triggers');
      $uDeets = $uDeets.getUsageEntity($udv,'variables');
    }else{
      $uDeets = 'This variable is <b>unused</b>.';
    }
    
    $uDeets = '<td>'.$uDeets.'</td></tr>';
    return $uDeets;
  }
  
  /******************************************************************************
  Unknown User Defined Variable
  ******************************************************************************/
  function getUdvDetailsforUnknown($udv){
    $deets = "I don't know about this variable type so here are the raw details:<br />";
  
    $deets = $deets.getUsage($udv);
    
    $deets = $deets.json_encode($udv);
  
    return $deets;
  }
  
  /******************************************************************************
  find a udv by ID
  ******************************************************************************/
  function findUdvById($udvId){
    $udv = [];
    global $udvLibrary, $udvTypes;  
      
    foreach($udvTypes as $typeKey => $typeVal){
      foreach($udvLibrary[$typeVal] as $udvKey => $udvVal){
        $udv = $udvVal['variableId']===$udvId ? $udvVal : $udv;
      }
    }
      
    return $udv;
  }
?>