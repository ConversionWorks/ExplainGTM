<?php  

  function tagging(){
    $tags = '';
    $tagsOrnotags = '';
    
    global $tagLibrary, $tagTypes, $container, $usedTriggers, $trigLibrary, $trigTypes, $untriggeredTags,$userIdUsed,$legacyTags,$folderArray,$usedUdvs, $udvUsage, $tagP;
    
    $tagTotal = 0;
    $tagTypesTotal = [];
    foreach($tagTypes as $typeKey => $typeVal){
      $tagTypesTotal[$typeVal] = 0;
      $tagTotal=$tagTotal+count($tagLibrary[$typeVal]);
      $tagTypesTotal[$typeVal]=count($tagLibrary[$typeVal]); 
    }

    echo '<h2><a href="#tagLink" id="tagLink" class="assetListLink" data-track="Tags" title="The things that do the work">Tags ('.$tagTotal.')</a></h2><br /><div id="tags" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';
  	
  	foreach($tagTypes as $typeKey => $tagTypeVal){
      $tagTypeHuman = translateToHuman($tagTypes[$typeKey]);
      $tagTypeHuman = $tagTypeHuman !== false ? $tagTypeHuman : $tagTypes[$typeKey].' tags';
  
      $tags = $tags.'<a href="#tagType'.$tagTypes[$typeKey].'" data-track="'.$tagTypes[$typeKey].'" class="typeLink" id="tagType'.$tagTypes[$typeKey].'"><h4>'.$tagTypeHuman.' ('.$tagTypesTotal[$tagTypeVal].')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($tagLibrary[$tagTypes[$typeKey]] as $tagKey => $tagVal){
        $tag = $tagLibrary[$tagTypes[$typeKey]][$tagKey];

        $tags = $tags.'<li><a href="#tag'.$tag['tagId'].'" id="tag'.$tag['tagId'].'" data-index='.$tag['type'].' class="tagDetail">'.$tag['name'].'</a>';
        $tags = $tags.'<div style="min-height:100px;display:none;" id="tagDetail'.$tag['tagId'].'" class="assetDetail container">';
        $tags = $tags.tagDetail($tag).'</div></li>';  
      }
      $tags = $tags.'</div>';
    }
    if(count($tagP)>0){
      $tags = $tags.'<br /><a href="#priorities" id="priorityLink">Firing Priorities</a>';
      $tags = $tags.'<div id="priorities" class="container" style="min-height:100px;display:none;">'.priorityList().'</div><br /><br />';
    }else{
      $tags = $tags.'<br />All tags share the same firing priority.<br /><br />';
    }
  
    $tagsOrnotags = $tags!=='' ? 'Your tags are:</h3><ul>'.$tags.'</ul>' : 'You have no tags...</h3>';
    
    echo '<div class="container" style="margin:5px" id="taglist"><h3 style="margin:5px;" id="tagHeader">'.$tagsOrnotags.'</div><br /></div><br />';
  }
  
  function getGeneralDetails($tag, $typeString = '', $whatItDoes = ''){
    global $tagLibrary, $tagTypes, $container, $usedTriggers, $trigLibrary, $trigTypes, $untriggeredTags,$userIdUsed,$legacyTags,$folderArray,$usedUdvs, $udvUsage;

    $technicalDetails = '';
    $rawHTML = getParam($tag,'html');
    $deets = '<table class="table table-bordered table-striped">';
    $deets = $deets.'<tr><th>Name</th><td>'.$tag['name'].'</td></tr>';
    $deets = $deets.'<tr><th>Type</th><td>'.$typeString.'</td></tr>';
    $deets = $deets.'<tr><th>What it does</th>';
    if($whatItDoes===''){
      if($rawHTML !== ''){
        $containsScript = strpos($rawHTML,'<script')>-1 ? 'contains JavaScript' : 'contains HTML';
        $deets = $deets.'<td>This tag '.$containsScript.'. Being a custom tag, you need to make sure this is carefully reviewed by your developers.<br />';
        $deets = $deets.'<textarea class="form-control" rows="10" cols="60" disabled="disabled">'.$rawHTML.'</textarea></td></tr>';
        
        if(getParam($tag,'supportDocumentWrite')==='true'){
          $deets = $deets.'<tr><th></th><td>This tag supports document.write.</td></tr>';
        }
      }
      
      if(getParam($tag,'trackType')!==''){
        $deets = $deets.humanDetails(getParam($tag,'trackType'),$tag);
      }
      
      if(getParam($tag,'enableEcommerce')==='true'){
          $deets = $deets.'<br />This tag uses Enhanced Ecommerce tracking features.<br />';
          if(getParam($tag,'useEcommerceDataLayer')==='true'){
            $deets = $deets.'The Data Layer is used to record the Enhanced Ecommerce data (<b>Technical!</b>)<br />';
          }else{
            if(getParamTemplate($tag,'ecommerceMacroData')!=='' && array_key_exists('value',getParamTemplate($tag,'ecommerceMacroData'))){
              $deets = $deets.variablise(getParamTemplate($tag,'ecommerceMacroData')['value'],$tag,true).' is used to record the Enhanced Ecommerce data (<b>Technical!</b>)<br />';
            }
          }
          $deets = $deets.'</td></tr>';
      }else{
        $deets = $deets.'</tr>';
      }
      
      if(getParam($tag,'trackingId')!==''){
        $deets = $deets.'<tr><th>The GA property ID is</th><td>'.translateToHuman(getParam($tag,'trackingId'),$tag,true).'</td></tr>';
      }
    }else{
      $deets = $deets.$whatItDoes;
    }
      
    $deets = $deets.'<tr><th>When it does it</th>';
    /******************************************************************************
    Firing triggers
    ******************************************************************************/
    if(array_key_exists('firingTriggerId',$tag) && count($tag['firingTriggerId'])>0){
      $triggerFound = false;
      $triggerLink = '';
      
      $firingTrigArray = [];
  
      if(count($tag['firingTriggerId'])>1){
        $triggerLink = '<td>This tag is fired by the following triggers:<ul>';
        
        foreach($tag['firingTriggerId'] as $tagFiringKey => $tagFiringVal) {
          if(!array_search($tag['firingTriggerId'][$tagFiringKey],$usedTriggers)){
            array_push($usedTriggers,$tag['firingTriggerId'][$tagFiringKey]);
          }
          $triggerFound = false;
          
          foreach($trigTypes as $trigTypeKey => $trigTypeVal) {
            foreach($trigLibrary[$trigTypeVal] as $trigKey => $trigVal){
              
              if($tag['firingTriggerId'][$tagFiringKey] === $trigLibrary[$trigTypeVal][$trigKey]['triggerId']){
                $triggerLink = $triggerLink.buildTriggerLink($tag['firingTriggerId'][$tagFiringKey],'<li>','</li> ',$trigLibrary, $trigTypes);
                $triggerFound = true;
              }
            }
          }

          if($triggerFound===false){
            $triggerLink = $triggerLink.'<li>Built-in <span title="This tag will fire on literally ALL PAGES!" style="background-color: #CCC"><b>All Pages</b></span> trigger.</li>'; 
          }
        }
        $deets = $deets.$triggerLink.'</ul></td></tr>';
      }else{
        if(!array_search($tag['firingTriggerId'][0],$usedTriggers)){
          array_push($usedTriggers,$tag['firingTriggerId'][0]);
        }
        
        $triggerLink = buildTriggerLink($tag['firingTriggerId'][0],'<td>This tag is fired by the ',' trigger. </td></tr>',$trigLibrary, $trigTypes);
  
        if($triggerLink===''){
          $deets = $deets.'<td>This tag is fired by the built-in <span title="This tag will fire on literally ALL PAGES!" style="background-color: #CCC"><b>All Pages</b></span> trigger.</td></tr>';
        }else{
          $deets = $deets.$triggerLink;
        }
      }
    }else{
      $deets = $deets.'<td>This tag <i>appears</i> to have <span title="This tag will NOT fire." style="background-color: #CCC"><b>no firing triggers</b></span>.</td></tr>';
      array_push($untriggeredTags,$tag);
    }
  
    /******************************************************************************
    Blocking triggers
    ******************************************************************************/
    if(array_key_exists('blockingTriggerId',$tag) && count($tag['blockingTriggerId'])>0){
      $deets = $deets.'<tr><th>When it is <b>stopped</b> from doing it</th>';
      $triggerFound = false;
      $blockingTrigArray = [];
      if(count($tag['blockingTriggerId'])>1){
        $triggerLink = '<td>This tag is blocked by the following triggers.<ul>';
        
        foreach($tag['blockingTriggerId'] as $blockingTrigKey => $blockingTrigVal){
          $triggerFound = false;
          foreach($trigTypes as $trigTypeKey => $trigTypeVal) {
            foreach($trigLibrary[$trigTypeVal] as $trigKey => $trigVal){
              if($tag['blockingTriggerId'][$blockingTrigKey] === $trigLibrary[$trigTypeVal][$trigKey]['triggerId']){
                $triggerLink = $triggerLink.buildTriggerLink($tag['blockingTriggerId'][$blockingTrigKey],'<li>','</li> ');
                $triggerFound = true;
              }
            }
          }
          
          if($triggerFound===false){
            $triggerLink = $triggerLink.'<li>Built-in <span title="This tag will be blocked on literally ALL PAGES!" style="background-color: #CCC"><b>All Pages</b></span> trigger.</li>'; 
          }
        }
        $deets = $deets.$triggerLink.'</ul></td></tr>';
      }else{
        $triggerLink = buildTriggerLink($tag['blockingTriggerId'][0],'<td>This tag is <b>BLOCKED</b> by the ',' trigger. </td></tr>',$trigLibrary, $trigTypes);
  
        if($triggerLink === ''){
          $deets = $deets.'<td>This tag is <b>BLOCKED</b> by the built-in <span title="This tag be blocked on literally ALL PAGES!" style="background-color: #CCC"><b>All Pages</b></span> trigger.</td></tr>';
        }else{
          $deets = $deets.$triggerLink;
        }
      }
    }
    
    if(array_key_exists('scheduleStartMs',$tag)){
      $deets = $deets.'<tr><th>Custom firing schedule start</th><td>The tag will only be live from '.date('Y-m-d H:i:s', (int)$tag['scheduleStartMs']/1000);

      $nowDate = new DateTime();
      $startDate = new DateTime();
      $startDate->setTimestamp((int)$tag['scheduleStartMs']/1000);
      $interval = date_diff($nowDate, $startDate);
      
      if($interval->invert===1){
        $deets = $deets.' - this tag went live '.$interval->format('%a days ago').'.</td><tr>';
      }
      
      if($interval->invert===0){
        $deets = $deets.' - this will go live in '.$interval->format('%a days').'.</td><tr>';
      }
    }
    
    if(array_key_exists('scheduleEndMs',$tag)){
      $deets = $deets.'<tr><th>Custom firing schedule end</th><td>The tag will only be live before '.date('Y-m-d H:i:s', (int)$tag['scheduleEndMs']/1000);
      
      $nowDate = new DateTime();
      $endDate = new DateTime();
      $endDate->setTimestamp((int)$tag['scheduleEndMs']/1000);
      $interval = date_diff($nowDate, $endDate);
      
      if($interval->invert===1){
        $deets = $deets.' - this tag ended '.$interval->format('%a days ago').'. </td><tr>';
      }
      
      if($interval->invert===0){
        $deets = $deets.' - this tag will end in '.$interval->format('%a days').'.</td><tr>';
      }
    }
    
    /******************************************************************************
    Sequencing
    ******************************************************************************/
    $deets = $deets.sequence($tag);
    
    /******************************************************************************
    Firing priority
    ******************************************************************************/
    $deets = $deets.priority($tag);
    
    /******************************************************************************
    Firing Options
    ******************************************************************************/
    $humanFiringOption = array_key_exists('tagFiringOption',$tag) ? translateToHuman($tag['tagFiringOption'],$tag) : false;
    if($humanFiringOption !== false){
      $deets = $deets.'<tr><th></th><td>'.$humanFiringOption.'</td></tr>';
    }
    /******************************************************************************
    Published containers
    ******************************************************************************/
    if($tag['liveOnly'] === 'true'){
      $deets = $deets.'<tr><th></th><td>This tag will only fire in published containers</td></tr>';
    }
    
    $deets = $deets.'<tr><th>Where it lives</th>';
    if(array_key_exists('parentFolderId',$tag)){
      $deets = $deets.'<td>'.folderisation($tag['parentFolderId'],'tag').'</td></tr>';
    }else{
      $deets = $deets.'<td>'.folderisation(null,'tag').'</td></tr>';
    }
    
    if($rawHTML!==''){
      $varRefs = getVarRefs($rawHTML,$tag);
      if($varRefs !== ''){
        $technicalDetails = $technicalDetails.'Variables referenced in this tag:<br />'.$varRefs; 
      }
    }
    
    /******************************************************************************
    Fields to Set (UA only)
    ******************************************************************************/
    $technicalDetails = $technicalDetails.fieldsToSet($tag);
    
    /******************************************************************************
    Custom Dimensions
    ******************************************************************************/
    $technicalDetails = $technicalDetails.dimensions($tag);
    
    /******************************************************************************
    Custom Metrics
    ******************************************************************************/
    $technicalDetails = $technicalDetails.metrics($tag);
    
    /******************************************************************************
    Custom Variables
    ******************************************************************************/
    $technicalDetails = $technicalDetails.cvars($tag);
    
    /******************************************************************************
    Mop up gnarly details
    ******************************************************************************/
    $technicalDetails = $technicalDetails.getGnarlyDetails($tag);

    if($technicalDetails!==''){
      $deets = $deets.'<tr><th></th><td>'.$technicalDetails.'</td></tr>';
    }
    
    $deets = $deets.'</table><br />';

    return $deets;
  }
  
  function priorityList(){
    $pDeets = '';
    global $tagP;
    
    $pDeets = '<ul>';
    foreach($tagP as $pKey => $pVal){
      $pDeets = $pDeets.'<li><a href="#p'.$pKey.'" class="pLink">'.$pKey.'</a>';
      $pDeets = $pDeets.'<div id="p'.$pKey.'" style="margin:10px;min-height:50px;width:95%;display:none;"><ul>';
      foreach($tagP[$pKey] as $tpKey => $tpVal){
        $pDeets = $pDeets.'<li><a href="#tag'.$tpVal['tagId'].'" data-p='.$pKey.' id="tagP'.$tpVal['tagId'].'" data-index='.$tpVal['tagId'].' data-type='.$tpVal['type'].' class="tagDetail">'.$tpVal['name'].'</a></li>';
      }
      $pDeets = $pDeets.'</ul></div></li>';
    }
    $pDeets = $pDeets.'</ul>';
    
    return $pDeets;
  }
  
  function tagLinkByName($tagName,$parentId){
    $tagLink = '';
    
    global $tagLibrary, $tagTypes;
    
    foreach($tagTypes as $typeKey => $tagTypeVal){
      foreach($tagLibrary[$tagTypes[$typeKey]] as $tagKey => $tagVal){
        $aTag = $tagLibrary[$tagTypes[$typeKey]][$tagKey];
        if($tagName===$aTag['name']){
          $tagLink = '<a href="#tag'.$aTag['tagId'].'" id="tagS'.$aTag['tagId'].'" data-parent='.$parentId.' data-index='.$aTag['tagId'].' data-type='.$aTag['type'].' class="Detail">'.$aTag['name'].'</a>';
        }  
      }
    }
    
    return $tagLink;
  }
  
  function sequence($tag){
    $sDeets = '';
    
    //setupTag
    if(array_key_exists('setupTag',$tag)){
      $sDeets = $sDeets.'<tr><th></th><td>'.tagLinkByName($tag['setupTag'][0]['tagName'],$tag['tagId']).' will fire before this tag.';
      if(array_key_exists('stopOnSetupFailure',$tag['setupTag'][0]) && $tag['setupTag'][0]['stopOnSetupFailure']===true){
        $sDeets = $sDeets.'<br />This tag will <b>not</b> fire if '.tagLinkByName($tag['setupTag'][0]['tagName'],$tag['tagId']).' fails.';
      }
      $sDeets = $sDeets.'</td></tr>';
    }
    
    //teardownTag
    if(array_key_exists('teardownTag',$tag)){
      $sDeets = $sDeets.'<tr><th></th><td>'.tagLinkByName($tag['teardownTag'][0]['tagName'],$tag['tagId']).' will fire after this tag.';
      if(array_key_exists('stopTeardownOnFailure',$tag['teardownTag'][0]) && $tag['teardownTag'][0]['stopTeardownOnFailure']===true){
        $sDeets = $sDeets.'<br />'.tagLinkByName($tag['teardownTag'][0]['tagName'],$tag['tagId']).' will <b>not</b> fire if this tag fails.';
      }
      $sDeets = $sDeets.'</td></tr>';
    }
    
    return $sDeets;
  }
  
  function priority($tag){
    $pDeets = '';
    $p = 0;
    global $tagP;
    
    if(array_key_exists('priority',$tag)){
      $p = $tag['priority']['value']; 
      $pDeets = $pDeets.'<tr><th>Firing priority</th><th>'.$tag['priority']['value'].'</th></tr>';
    }
    
    if(array_key_exists($p,$tagP)){
      array_push($tagP[$p],$tag);
    }else{
      $tagP[$p] = [];
      array_push($tagP[$p],$tag);
    }
    
    return $pDeets;
  }
  
  function dimensions($tag){
    $dDeets = '';
    
    $cds = getParamList($tag,'dimension');
    
    if($cds!=='' && count($cds)>0){
      $dDeets = '<a class="tagCDs" id="CDs4'.$tag['tagId'].'"><h4>Custom Dimensions:</h4></a>';
      $dDeets = $dDeets.'<div style="margin:10px;min-height:50px;width:95%;display:none;">';
      
      $dDeets = $dDeets.'<table class="table table-bordered table-striped">';
      $dDeets = $dDeets.'<tr><th>Index</th><th>Dimension Value</th>';
      foreach($cds as $cdKey => $cdVal){
        $dDeets = $dDeets.'<tr><th>'.$cds[$cdKey]['map'][0]['value'].'</th>';
        $dDeets = $dDeets.'<td>'.variablise($cds[$cdKey]['map'][1]['value'],$tag,true).'</td></tr>';
      }
      
      $dDeets = $dDeets.'</table></div>';
    }
    
    return $dDeets;
  }
  
  function metrics($tag){
    $mDeets = '';

    $cms = getParamList($tag,'metric');
    
    if($cms!=='' && count($cms) > 0){
      $mDeets = '<a class="tagCMs" id="CMs4'.$tag['tagId'].'"><h4>Custom Metrics:</h4></a>';
      $mDeets = $mDeets.'<div style="margin:10px;min-height:50px;width:95%;display:none;">';
      
      $mDeets = $mDeets.'<h4>Custom Metrics:</h4>'; 
      $mDeets = $mDeets.'<table class="table table-bordered table-striped">';
      $mDeets = $mDeets.'<tr><th>Index</th><th>Metric Value</th>';
          
      foreach($cms as $cmKey => $cmVal){
        $mDeets = $mDeets.'<tr><td>'.$cms[$cmKey]['map'][0]['value'].'</td>';
        $mDeets = $mDeets.'<td>'.variablise($cms[$cmKey]['map'][1]['value'],$tag,true).'</td></tr>';
      }
      
      $mDeets = $mDeets.'</table></div>';
    }
    
    return $mDeets;
  }
  
  function fieldsToSet($tag){
    global $userIdUsed;
    $fDeets = '';
    
    $fields = getParamList($tag,'fieldsToSet');
    
    if($fields!=='' && count($fields) > 0){
      
      $fDeets = '<a class="tagFieldsToSet" id="fieldsToSet4'.$tag['tagId'].'"><h4>Fields to set:</h4></a>';
      $fDeets = $fDeets.'<div style="margin:10px;min-height:50px;width:95%;display:none;">';
      $fDeets = $fDeets.'<table class="table table-bordered table-striped">';
      $fDeets = $fDeets.'<tr><th>Field</th><th>Details</th></tr>';
      
      foreach($fields as $fieldVal){
        $fDeets = $fDeets.'<tr><th>'.$fieldVal['map']['0']['value'].'</th>';
        switch($fieldVal['map']['0']['value']){
          case 'anonymizeIp':
              if($fieldVal['map'][1]['value']==='true'){
                $fDeets = $fDeets.'<td>The user\'s IP address is being hidden.</td></tr>';
              }else{
                $fDeets = $fDeets.'<td>The user\'s IP address is not being hidden.</td></tr>';
              }
          break;
          
          case 'allowLinker':
              if($fieldVal['map'][1]['value']==='true'){
                $fDeets = $fDeets.'<td>This tag is setup to handle cross domain and/or subdomain tracking.<br />';
                $fDeets = $fDeets.'Make sure the cookieDomain field is correctly set along with the Auto Link Domain list.</td></tr>';
              }else{
                $fDeets = $fDeets.'<td>This tag is setup to <b>not</b> handle cross domain and/or subdomain tracking.<br />';
                $fDeets = $fDeets.'allowLinker = "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
              }
          break;
          
          case 'sessionControl':
              if($fieldVal['map'][1]['value']==='start'){
                $fDeets = $fDeets.'<td>This tag is setup to start a new GA session when it fires.</td></tr>';
              }else{
                $fDeets = $fDeets.'<td>The sessionControl field is set to '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
              }
          break;
          
          case 'page':
            $fDeets = $fDeets.'<td>This tag is setup to handle virtual pageviews using '.variablise($fieldVal['map'][1]['value'],$tag,true).' as the virtual page path.</td></tr>';
          break;
          
          case 'referrer':
              $fDeets = $fDeets.'<td>The referrer field is set to '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
          break;
          
          case 'title':
              $fDeets = $fDeets.'<td>The tag will set the document title to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'sampleRate':
              '<td>This tag sets the GA sampling rate to '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
          break;
          
          case 'cookieName':
              '<td>This tag sets the GA cookie name to '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
          break;
          
          case 'cookieDomain':
              if($fieldVal['map'][1]['value']==='auto'){
                $fDeets = $fDeets.'<td>This tag sets the <i>domain</i> of the GA cookie to "auto". This is best practice for cross domain tracking.</td></tr>';
              }else{
                $fDeets = $fDeets.'<td>This tag sets the <i>domain</i> of the GA cookie to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>'; 
              }
          break;
  
          case 'cookiePath':
              $fDeets = $fDeets.'<td>This tag sets the GA cookie path to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'cookieExpires':
              $fDeets = $fDeets.'<td>This tag sets the expiry time for the GA cookie to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
          break;
          
          case 'useBeacon':
              $fDeets = $fDeets.'<td>This tag sends it\'s data to GA using a <i title="Ask your developers about this">Beacon</i> depending on the value of '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
          break;
          
          case 'userId':
              $fDeets = $fDeets.'<td>This tag is decorated with a userId using the value of '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
              
              if(array_search($tag['tagId'],$userIdUsed)===false){
                array_push($userIdUsed,$tag['tagId']);
              }
          break;
          
          case '&uid':
              $fDeets = $fDeets.'<td>This tag is decorated with a userId using the value of '.variablise($fieldVal['map'][1]['value'],$tag,true).'.</td></tr>';
              
              if(array_search($tag['tagId'],$userIdUsed)===false){
                array_push($userIdUsed,$tag['tagId']);
              }
          break;
          
          case 'allowAnchor':
              $fDeets = $fDeets.'<td>The allowAnchor value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". This is true by default. If you set it to false, any campaign parameters after the # in the URL will be ignored.</td></tr>';
          break;
          
          case 'alwaysSendReferrer':
              $fDeets = $fDeets.'<td>The alwaysSendReferrer value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". By default the HTTP referrer URL, which is used to attribute traffic sources, is only sent when the hostname of the referring site differs from the hostname of the current page. Enable this setting only if you want to process other pages from your current host as referrals.</td></tr>';
          break;
          
          case 'campaignContent':
              $fDeets = $fDeets.'<td>The campaign content value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'campaignId':
              $fDeets = $fDeets.'<td>The campaign ID value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'campaignKeyword':
              $fDeets = $fDeets.'<td>The campaign keyword value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'campaignName':
              $fDeets = $fDeets.'<td>The campaign name value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'campaignMedium':
              $fDeets = $fDeets.'<td>The campaign medium value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'campaignSource':
              $fDeets = $fDeets.'<td>The campaign source value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'encoding':
              $fDeets = $fDeets.'<td>The recorded <i>encoding</i> value is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". This specifies the character set used to encode the page / document.</td></tr>';
          break;
          
          case 'expId':
              $fDeets = $fDeets.'<td>If you\'re doing testing, the experiment ID will be set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'expVar':
              $fDeets = $fDeets.'<td>If you\'re doing testing, the experiment variation will be set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'flashVersion':
              $fDeets = $fDeets.'<td>The tag will set the recorded Flash version to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'forceSSL':
              $fDeets = $fDeets.'<td>forceSSL is set to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". Setting forceSSL to true will force http pages to also send all tracking beacons using https.</td></tr>';
          break;
          
          case 'hitCallback':
              $fDeets = $fDeets.'<td>Once the tag has finished, the function specified by "'.variablise($fieldVal['map'][1]['value'],$tag,true).'" will be executed.<br /><br />';
          break;
          
          case 'hostName':
              $fDeets = $fDeets.'<td>The tag will set the recorded hostname (website domain) value to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'javaEnabled':
              $fDeets = $fDeets.'<td>The tag will set the <i>Java enabled</i> value to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'language':
              $fDeets = $fDeets.'<td>The tag will set the recorded value of the user\'s language to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'legacyCookieDomain':
              $fDeets = $fDeets.'<td>The tag will set the legacy cookie domain to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". This is used to define how to use cookies for older versions of GA.</td></tr>';
          break;
          
          case 'linkid':
              $fDeets = $fDeets.'<td>The tag will set the id of a clicked element to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'". This is used in In-Page reports.</td></tr>';
          break;
          
          case 'location':
              $fDeets = $fDeets.'<td>The tag will set the recorded location (full page URL) to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'clientId':
              $fDeets = $fDeets.'The tag will set the clientId (unique, anonymous user identifier) to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'screenColors':
              $fDeets = $fDeets.'The tag will set the recorded screen colors (number of displayable colours) to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'screenName':
              $fDeets = $fDeets.'The tag will set the recorded name of the screen to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'screenResolution':
              $fDeets = $fDeets.'The tag will set the recorded screen resolution to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'siteSpeedSampleRate':
              $fDeets = $fDeets.'The tag will set rate at which speed metrics are sample to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
          
          case 'viewPortSize':
              $fDeets = $fDeets.'The tag will set the view port size (window size) to "'.variablise($fieldVal['map'][1]['value'],$tag,true).'".</td></tr>';
          break;
        
          default:
              $fDeets = $fDeets.variablise($fieldVal['map'][0]['value'],$tag,true).' is set to the value of '.variablise($fieldVal['map'][1]['value'],$tag);
        }
      }
      $fDeets = $fDeets.'</table></div>';
    }
    
    return $fDeets;
  }
  
  function cvars($tag){
    global $udvTypes, $udvLibrary, $bivArray, $udvUsage;
    $cvDeets = '';
    $customVars = getParamList($tag,'customVar');
    
    if($customVars !== '' && count($customVars) > 0){
      $cvDeets = '<a class="tagCVs" id="CVs4'.$tag['tagId'].'"><h4>Custom Variables:</h4></a>';
      $cvDeets = $cvDeets.'<div style="margin:10px;min-height:50px;width:95%;display:none;">';
  
      $cvDeets = $cvDeets.'<table class="table table-bordered table-striped">';
      
      foreach($customVars as $cvKey => $cvVal){
          $cvDeets = $cvDeets.'<tr><th>'.$cvVal['map'][0]['value'].'</th>';
          $cvDeets = $cvDeets.'<td>'.variablise($cvVal['map'][1]['value'],$tag,true).'</td></tr>';
      }
      $cvDeets = $cvDeets.'</table></div>';
    }
    
    return $cvDeets;
  }
  
  function getGnarlyDetails($tag){
    global $udvTypes, $udvLibrary, $bivArray, $udvUsage;
    $gDeets = '';
    $ignored = [
            'trackType',
            'webPropertyId',
            'eventCategory',
            'eventAction',
            'eventLabel',
            'eventValue',
            'timingVar',
            'timingCategory',
            'timingValue',
            'timingLabel',
            'nonInteraction',
            'html',
            'conversionLabel',
            'conversionId',
            'conversionValue',
            'supportDocumentWrite',
            'fieldsToSet',
            'dimension',
            'trackingId',
            'customVar',
            'page',
            'customParams',
            'customParamsFormat',
            'enableEcommerce',
            'useEcommerceDataLayer',
            'ecommerceMacroData',
            'scheduleStartMs',
            'scheduleEndMs'
        ];
    
    $gnarlyDeets = $tag['parameter'];
    
    if($gnarlyDeets!==null && count($gnarlyDeets) > 0){
      foreach($gnarlyDeets as $gdKey => $gdVal){
        if(array_search($gnarlyDeets[$gdKey]['key'],$ignored)===false){
          
          $gDeets = $gDeets.'<tr>';
          $gDeets = $gDeets.'<th>'.$gnarlyDeets[$gdKey]['key'].'</th>';
            
          if($gnarlyDeets[$gdKey]['type']=='LIST' && array_key_exists('list',$gnarlyDeets[$gdKey])){
            if($gnarlyDeets[$gdKey]['list']!==null){
              $gDeets = $gDeets.'<td><table class="table table-bordered table-striped">';
              foreach($gnarlyDeets[$gdKey]['list'][0]['map'] as $gdListKey => $gdListVal){
                $gDeets = $gDeets.'<tr>';
                $gDeets = $gDeets.'<th>'.variablise($gnarlyDeets[$gdKey]['list'][0]['map'][$gdListKey]['key'],$tag, true).'</th>';
                $gDeets = $gDeets.'<td>'.variablise($gnarlyDeets[$gdKey]['list'][0]['map'][$gdListKey]['value'],$tag, true).'</td>';
                $gDeets = $gDeets.'</tr>';
              }
              $gDeets = $gDeets.'</table></td>';  
            }
          }else{
            if(array_key_exists('value',$gnarlyDeets[$gdKey])){
              $gDeets = $gDeets.'<td>'.variablise($gnarlyDeets[$gdKey]['value'],$tag, true).'</td>';
            }else{
              $gDeets = $gDeets.'<td></td>';
            }
          }
        }
      }
      
      if($gDeets!==''){
            $gDeets = '<a class="tagGnarlyDetail" id="gnarlyDeets4'.$tag['tagId'].'"><h4>Gnarly details:</h4></a><div style="margin:10px;min-height:50px;width:95%;display:none;"><table class="table table-bordered table-striped"><tr><th>Field</th><th>Details</th>'.$gDeets.'</tr></table></div>';
      }
    }
    
    return $gDeets;
  }

  /******************************************************************************
  Tag Details
  ******************************************************************************/
  function tagDetail($tag){
    global $legacyTags;
    $detailsInEnglish = '<h3>Details for tag <i>'.$tag['name'].'</i></h3>';

    $detailsInEnglish = $detailsInEnglish.'<a href="https://tagmanager.google.com/#/container/accounts/'.$tag['accountId'].'/containers/'.$tag['containerId'].'/tags/'.$tag['tagId'].'"';
    $detailsInEnglish = $detailsInEnglish.' style="position:relative;float:right;top:-40px;right:10px;" class="tagEdit">edit</a>';
    
    switch($tag['type']){
      case 'ga':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a <b>Classic Google Analytics</b> '.translateToHuman(getParam($tag,'trackType'),$tag));
      break;

      case 'ua':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a <b>Universal Analytics</b> '.translateToHuman(getParam($tag,'trackType'),$tag));
      break;

      case 'awct':
          $detailsInEnglish = $detailsInEnglish.getDetailsforAwct($tag);
      break;

     case 'sp':
          $detailsInEnglish = $detailsInEnglish.getDetailsforSp($tag);
      break;

      case 'html':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a custom HTML tag.');
      break;
      
      case 'img':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is an image tag.','<td>Image tags are used to fire image based tracking pixels');
      break;
      
      case 'lcl':
          $detailsInEnglish = $detailsInEnglish.getDetailsforLcl($tag,$legacyTags);
      break;
      
      case 'fsl':
          $detailsInEnglish = $detailsInEnglish.getDetailsforFsl($tag,$legacyTags);
      break;

      case 'cl':
          $detailsInEnglish = $detailsInEnglish.getDetailsforCl($tag,$legacyTags);
      break;
      
      case 'tl':
          $detailsInEnglish = $detailsInEnglish.getDetailsforTl($tag,$legacyTags);
      break;
      
      case 'hl':
          $detailsInEnglish = $detailsInEnglish.getDetailsforHl($tag,$legacyTags);
      break;

      case 'flc':
          $detailsInEnglish = $detailsInEnglish.getDetailsforFlc($tag);
      break;

      case 'fls':
          $detailsInEnglish = $detailsInEnglish.getDetailsforFls($tag);
      break;
      
      case 'jel':
          $detailsInEnglish = $detailsInEnglish.getDetailsforJel($tag);
      break;
      
      case 'mpm':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Mediaplex IFrame tag.');
      break;
      
      case 'mpr':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Mediaplex ROI tag.');
      break;
      
      case 'tc':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Turn Conversion Tracking tag.');
      break;
      
      case 'asp':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is an AdRoll Smart Pixel tag.');
      break;
      
      case 'tdc':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Turn Data Collection tag.');
      break;
      
      case 'ms':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Marin tag.');
      break;
    
      case '_ta':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Bizo Insight tag.');
      break;
      
      case 'bzi':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a AdAdvisor tag.');
      break;
      
      case 'm6d':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Dstillery Universal Pixel tag.');
      break;
      
      case 'vdc':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a VisualDNA Conversion tag.');
      break;
      
      case 'cts':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a ClickTale Standard tag.');
      break;
      
      case 'cms':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a comScore Unified Digital Measurement tag.');
      break;
      
      case '_fc':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Function Call tag.');
      break;
      
      case 'adm':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is an Adometry tag.','<td>The Adometry tag is used to send conversion data to Adometry.</td></tr>');
      break;
      
      case 'ts':
          $detailsInEnglish = $detailsInEnglish.getGeneralDetails($tag,'This is a Google Trusted Store tag.');
      break;
      
      default:
          $detailsInEnglish = $detailsInEnglish.getDetailsforUnknown($tag);
    }

    return $detailsInEnglish;
  }
  
  /******************************************************************************
  Unknown Tag
  ******************************************************************************/
  function getDetailsforUnknown($tag){
    $deets = '<table class="table table-bordered table-striped">';
    $deets = $deets.'<tr><th>Name</th><td>'.$tag['name'].'</td></tr>';
    $deets = "<tr><th>I don't know about this tag type so here are the raw details:</th>";
    $deets = $deets.'<td><textarea class="form-control" rows="10" cols="60" disabled="disabled">'.json_encode($tag).'</textarea></td></tr>';

    return $deets;
  }
  
  /******************************************************************************
  AdWords Conversion Tracking Tag
  ******************************************************************************/
  function getDetailsforAwct($tag){
    $whatItDoes = '';
    
    $whatItDoes = $whatItDoes.'<td>The AdWords Conversion Tracking tag allows you to keep track of <i>conversions</i> that happen on your site after a user clicks on an AdWords ad.<table class="table table-bordered table-striped">';
    //Conversion Label
    if(getParam($tag,'conversionLabel') !== '' && getParam($tag,'conversionLabel') !== null){
      $whatItDoes = $whatItDoes.'<tr><th>The Conversion Label is</th><td>'.variablise(getParam($tag,'conversionLabel'),$tag,true).'</td></tr>';
    }
    
    //Conversion Value
    if(getParam($tag,'conversionValue') !== '' && getParam($tag,'conversionValue') !== null){
      $whatItDoes = $whatItDoes.'<tr><th>The Conversion Value is</th><td>'.variablise(getParam($tag,'conversionValue'),$tag,true).'</td></tr>';
    }
    
    //Conversion ID
    if(getParam($tag,'conversionId') !== '' && getParam($tag,'conversionId') !== null){
      $whatItDoes = $whatItDoes.'<tr><th>The Conversion ID is</th><td>'.variablise(getParam($tag,'conversionId'),$tag,true).'</td></tr>';
    }
    
    $whatItDoes = $whatItDoes.'</table></td></tr>';

    return getGeneralDetails($tag,'This is an Adwords Conversion Tracking tag.',$whatItDoes);
  }
  
  /******************************************************************************
  AdWords remarketing Tag
  ******************************************************************************/
  function getDetailsforSp($tag){
    $whatItDoes = '';
    $customParams = [];
    
    $whatItDoes = $whatItDoes.'<td>The AdWords Dynamic Remarketing tag allows you build specific lists of users who you can reach through AdWords.<table class="table table-bordered table-striped">';

    //Conversion ID
    $whatItDoes = $whatItDoes.'<tr><th>The Conversion ID is</th><td>'.variablise(getParam($tag,'conversionId'),$tag,true).'</td></tr>';

    //Conversion Label
    $ConversionLabel = getParam($tag,'conversionLabel');
    if($ConversionLabel!==null && $ConversionLabel!==''){
        $whatItDoes = $whatItDoes.'<tr><th>The Conversion Label is</th><td>'.variablise($ConversionLabel,$tag,true).'</td></tr>';
    }

    //Custom Parameters Format
    switch(getParam($tag,'customParamsFormat')){
      case 'USER_SPECIFIED':
        $whatItDoes = $whatItDoes.'<tr><th>Custom Paramaters are Manually Specified:</th>';
 
        $customParams = getParamList($tag,'customParams');
        if(count($customParams) > 0){
          $whatItDoes = $whatItDoes.'<td><table class="table table-bordered table-striped">';
          foreach($customParams as $cpKey => $cpVal){
            $whatItDoes = $whatItDoes.'<tr><td>'.variablise($customParams[$cpKey]['map'][0]['value'],$tag,true).'</td>';
            $whatItDoes = $whatItDoes.'<td>'.variablise($customParams[$cpKey]['map'][1]['value'],$tag,true).'</td></tr>';
          }
          $whatItDoes = $whatItDoes.'</table></td></tr>';
        }
      break;

      case 'DATALAYER':
        $whatItDoes = $whatItDoes.'<tr><th>Custom Paramaters using the dataLayer</th><td></td></tr>';
      break;
    }

    $whatItDoes = $whatItDoes.'</table></td></tr>';

    return getGeneralDetails($tag,'This is an Adwords Remarketing tag.',$whatItDoes);
  }
  
  /******************************************************************************
  Double Click Floodlight Counter Tag
  ******************************************************************************/
  function getDetailsforFlc($tag){
    $whatItDoes = '';
    $countingRaw = '';
    $countingEn = '';
    $customVars = [];

    $whatItDoes = $whatItDoes.'<td>The DoubleClick Floodlight Counter tag allows you to count the number of times that users have visited a particular page after seeing or clicking one of your ads.<table class="table table-bordered table-striped">'; 
    
    //Advertiser ID
    $whatItDoes = $whatItDoes.'<tr><th>The Advertiser ID is</th><td>'.variablise(getParam($tag,'advertiserId'),$tag,true).'</td></tr>';

    //Activity Tag
    $whatItDoes = $whatItDoes.'<tr><th>The Activity Tag is</th><td>'.variablise(getParam($tag,'activityTag'),$tag,true).'</td></tr>';

    //Group Tag String
    $whatItDoes = $whatItDoes.'<tr><th>The Group Tag String is</th><td>'.variablise(getParam($tag,'groupTag'),$tag,true).'</td></tr>';

    //Counting Method
    $countingRaw = getParam($tag,'ordinalType');

    switch($countingRaw){
        case 'STANDARD':
            $countingEn = 'Standard';
        break;

        case 'UNIQUE':
            $countingEn = 'Unique';
        break;

        case 'SESSION':
            $countingEn = 'Per Session';
        break;
    }
    $whatItDoes = $whatItDoes.'<tr><th>The Counting Method is</th><td>'.$countingEn.'</td></tr>';

    //Image tag
    if(getParam($tag,'useImageTag')=='true'){
        $whatItDoes = $whatItDoes.'<tr><th>The tag uses an image tag</th><td></td></tr>';
    }

    $whatItDoes = $whatItDoes.'</table></td></tr>';
    
    return getGeneralDetails($tag,'This is a Double Click Floodlight Counter tag',$whatItDoes);
  }
  
  /******************************************************************************
  Double Click Floodlight Sales Tag
  ******************************************************************************/
  function getDetailsforFls($tag){
    $whatItDoes = '';
    $countingRaw = '';
    $countingEn = '';
    $customVars = [];

    $whatItDoes = $whatItDoes.'<td>The DoubleClick Floodlight Sales tag allows you to keep track of how many items users have purchased, as well as the total value of those purchases.<table class="table table-bordered table-striped">'; 

    //Advertiser ID
    $whatItDoes = $whatItDoes.'<tr><th>The Advertiser ID is</th><td>'.variablise(getParam($tag,'advertiserId'),$tag,true).'</td></tr>';

    //Group Tag String
    $whatItDoes = $whatItDoes.'<tr><th>The Group Tag String is</th><td>'.variablise(getParam($tag,'groupTag'),$tag,true).'</td></tr>';

    //Activity Tag
    $deetwhatItDoess = $whatItDoes.'<tr><th>The Activity Tag String is</th><td>'.variablise(getParam($tag,'activityTag'),$tag,true).'</td></tr>';

    //Counting Method
    $countingRaw = getParam($tag,'countingMethod');

    switch($countingRaw){
        case 'ITEM_SOLD':
            $countingEn = 'Item Sold';
        break;

        case 'TRANSACTIONS':
            $countingEn = 'Transactions';
        break;

    }
    $whatItDoes = $whatItDoes.'<tr><th>The Counting Method is</th><td>'.$countingEn.'</td></tr>';

    if(getParam($tag,'revenue')!=='' || getParam($tag,'orderId')!=='' || getParam($tag,'quantity')!==''){
      $whatItDoes = $whatItDoes.'<tr><th>Unique Parameters</th><td><table class="table table-bordered table-striped">';
    
      if(getParam($tag,'revenue')!==''){
        $whatItDoes = $whatItDoes.'<tr><th>Revenue</th><td>'.variablise(getParam($tag,'revenue'),$tag,true).'</td></tr>';
      }
      
      if(getParam($tag,'orderId')!==''){
        $whatItDoes = $whatItDoes.'<tr><th>OrderId</th><td>'.variablise(getParam($tag,'orderId'),$tag,true).'</td></tr>';
      }
      
      if(getParam($tag,'quantity')!==''){
        $whatItDoes = $whatItDoes.'<tr><th>Quantity</th><td>'.variablise(getParam($tag,'quantity'),$tag,true).'</td></tr>';
      }
      
      $whatItDoes = $whatItDoes.'</table></td></tr>';
    }
    
    if(getParam($tag,'U')!=='' || getParam($tag,'Tran')!==''){
      $whatItDoes = $whatItDoes.'<tr><th>Standard Variables</th><td><table class="table table-bordered table-striped">';
      
      if(getParam($tag,'U')!==''){
        $whatItDoes = $whatItDoes.'<tr><th>U</th><td>'.variablise(getParam($tag,'U'),$tag,true).'</td></tr>';
      }
      
      if(getParam($tag,'Tran')!==''){
        $whatItDoes = $whatItDoes.'<tr><th>Tran</th><td>'.variablise(getParam($tag,'Tran'),$tag,true).'</td></tr>';
      }
      $whatItDoes = $whatItDoes.'</table></td></tr>';
    }

    //Custom Variables
    $customVars = getParamList($tag,'customVariable');
    
    if($customVars!==null && $customVars!=='' && count($customVars) > 0){
        $whatItDoes = $whatItDoes.'<tr><th>Custom Variables:</th><td>';
        $whatItDoes = $whatItDoes.'<table class="table table-bordered table-striped">';
        foreach($customVars as $cvKey => $cvVal){
            $whatItDoes = $whatItDoes.'<tr><td>'.$cvVal['map'][0]['value'].'</td>';
            $whatItDoes = $whatItDoes.'<td>'.variablise($cvVal['map'][1]['value'],$tag,true).'</td></tr>';
        }
        $whatItDoes = $whatItDoes.'</table></td></tr>';
    }

    //Image tag
    if(getParam($tag,'useImageTag')==='true'){
        $whatItDoes = $whatItDoes.'<tr><th>The tag uses an image tag</th><td></td></tr>';
    }

    $whatItDoes = $whatItDoes.'</table></td></tr>';
    
    return getGeneralDetails($tag,'This is a Double Click Floodlight Sales tag.',$whatItDoes);
  }

  /******************************************************************************
  History Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforHl($tag,&$legacyTags){
    $deets = getGeneralDetails($tag,'This is a History listener tag.');
    
    array_push($legacyTags,$tag);
      
    return $deets;
  }
  
  /******************************************************************************
  JavaScript Error Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforJel($tag,&$legacyTags){
    $deets = getGeneralDetails($tag,'This is a JavaScript error listener tag.');
    
    array_push($legacyTags,$tag); 
    
    return $deets;
  }
  
  /******************************************************************************
  Link Click Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforLcl($tag,&$legacyTags){
    $deets = getGeneralDetails($tag,'This is a link click listener tag.<br />This is a <b>legacy</b> tag left over from GTM v1.<br />Consider using auto event variables.');
    
    array_push($legacyTags,$tag);
      
    return $deets;
  }
  
  /******************************************************************************
  Timer Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforTl($tag,&$legacyTags){
      $deets = getGeneralDetails($tag,'This is a timer listener tag.<br />This is a <b>legacy</b> tag left over from GTM v1.<br />Consider using auto event variables.<br />');
      
      array_push($legacyTags,$tag);
      
    return $deets;
  }
  
  /******************************************************************************
  Form Submit Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforFsl($tag,&$legacyTags){
      $deets = getGeneralDetails($tag,'This is a form submit listener tag.<br />This is a <b>legacy</b> tag left over from GTM v1.<br />Consider using auto event variables.<br />');
      
      array_push($legacyTags,$tag);
      
    return $deets;
  }
  
  /******************************************************************************
  Click Listener Tag - V1 LEGACY
  ******************************************************************************/
  function getDetailsforCl($tag,&$legacyTags){
      $deets = getGeneralDetails($tag,'This is a click listener tag.<br />This is a <b>legacy</b> tag left over from GTM v1.<br />Consider using auto event variables.<br />');
      
      array_push($legacyTags,$tag);
      
    return $deets;
  }
  
  /******************************************************************************
  find a tag by ID
  ******************************************************************************/
  function findTagById($tagId){
    $tag = [];
    global $tagLibrary, $tagTypes;  
      
    foreach($tagTypes as $typeKey => $typeVal){
      foreach($tagLibrary[$typeVal] as $tagKey => $tagVal){
        $tag = $tagVal['tagId']===$tagId ? $tagVal : $tag;
      }
    }

    return $tag;
  }
?>