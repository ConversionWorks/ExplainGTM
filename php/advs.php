<?php
  function advs(){
    global $untriggeredTags, $untaggedTriggers, $udvTypes, $udvLibrary, $usedUdvs, $unusedUdvs, $userIdUsed, $tagLibrary, $tagTypes, $variablesThatMessage, $unFolderedTags, $unFolderedTriggers, $trigLibrary, $trigTypes, $unFolderedUdvs, $udvLibrary, $udvTypes, $mismatchArray,$legacyTags;
    
    $advs = '';
    $unFolderedTagCount = 0;
    $unFolderedTriggerCount = 0;
    $unFolderedUdvCount = 0;
    
    function identical_values( $arrayA , $arrayB, $assetType ){ 
        $arrayA[$assetType.'Id'] = '';
        $arrayA['name'] = '';
        $arrayB[$assetType.'Id'] = '';
        $arrayB['name'] = '';
        
        sort( $arrayA ); 
        sort( $arrayB ); 
        
        return $arrayA == $arrayB; 
    } 
    
    function getDoops($types,$library,$asset,&$doops){
      foreach($types as $ttKey => $ttVal){
        foreach($library[$ttVal] as $candidateKey => $candidateVal){
          foreach($library[$ttVal] as $compareKey => $compareVal){
            if($candidateVal[$asset.'Id'] !== $compareVal[$asset.'Id']){
              if(identical_values($candidateVal,$compareVal,$asset)){ 
                if(!array_key_exists($compareVal[$asset.'Id'],$doops[$asset.'s']) && !array_key_exists($candidateVal[$asset.'Id'],$doops[$asset.'s'])){
                  $doops[$asset.'s'][$candidateVal[$asset.'Id']]=[];
                  array_push($doops[$asset.'s'][$candidateVal[$asset.'Id']],$compareVal);
                  array_push($doops[$asset.'s'][$candidateVal[$asset.'Id']],$candidateVal);
                }
              }
            }
          }
        }
      }
    }
    
    //unused variables
    foreach($udvTypes as $udvTypeIndex => $udvTypeVal){
      foreach($udvLibrary[$udvTypes[$udvTypeIndex]] as $udvIndex => $udvVal){
        if(array_search('{{'.$udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex]['name'].'}}',$usedUdvs)===false){
          array_push($unusedUdvs,$udvLibrary[$udvTypes[$udvTypeIndex]][$udvIndex]);
        }
      }
    }
    
    //unclosed script tags
    if(array_key_exists('html',$tagLibrary)){
      foreach($tagLibrary['html'] as $cIndex => $cVal){
        $openRe = "/<script/";
        $closeRe = "/<\/script/";
        $openedArray = [];
        $closedArray = [];
        $mismatch = false;
      
        $rawHTML = getParam($tagLibrary['html'][$cIndex],'html');
  
        preg_match_all($openRe, $rawHTML, $openedArray);
        preg_match_all($closeRe, $rawHTML, $closedArray);
        
        $mismatch = $mismatch===true ? $mismatch : ($openedArray===null && $closedArray!==null);
        $mismatch = $mismatch===true ? $mismatch : ($openedArray!==null && $closedArray===null);
        if($openedArray!==null && $closedArray!==null){
          $mismatch = $mismatch===true ? $mismatch : (count($openedArray)!==count($closedArray));
        }
        
        if($mismatch===true){
          array_push($mismatchArray,$tagLibrary['html'][$cIndex]);
        }
      }
    }
    
    $unFolderedTags = assetsNotInFolders($tagLibrary, $tagTypes, 'tag', 'tag',$unFolderedTagCount);
    $unFolderedTriggers = assetsNotInFolders($trigLibrary, $trigTypes, 'trigger', 'trig', $unFolderedTriggerCount);
    $unFolderedUdvs = assetsNotInFolders($udvLibrary, $udvTypes, 'variable', 'udv',$unFolderedUdvCount);
    
    echo '<h2><a href="#advLink" id="advLink" class="assetListLink" data-track="Advisories" title="Potential issues">Advisories</a></h2><br />';
  	echo '<div id="advs" class="assetContainer" style="width:100%;padding-bottom:10px;display:none;">';
  	
    //Untriggered tags
    if(count($untriggeredTags)>0){
      $advs = $advs.'<ul><a href="#utt" class="typeLink" data-track="Tags with no triggers" id="advtag"><h4>You have tags with no triggers. ('.count($untriggeredTags).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($untriggeredTags as $uttIndex => $uttVal){
        $advs = $advs.'<li><a href="#tag'.$uttVal['tagId'].'" id="adv'.$uttVal['tagId'].'" data-asset="tag" data-index='.$uttVal['tagId'].' data-type='.$uttVal['type'].' class="advDetail">'.$uttVal['name'].'</a>';
      }
      $advs = $advs.'</ul></ul>';
    }
          
    //untagged triggers
    if(count($untaggedTriggers)>0){
      $advs = $advs.'<ul><a href="#autt" class="typeLink" id="advtrig" data-track="Unused triggers"><h4>Unused triggers. ('.count($untaggedTriggers).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($untaggedTriggers as $uttIndex => $uttVal){
        $advs = $advs.'<li><a href="#trig'.$uttVal['triggerId'].'" id="adv'.$uttVal['triggerId'].'" data-asset="trig" data-index='.$uttVal['triggerId'].' data-type='.$uttVal['type'].' class="advDetail">'.$uttVal['name'].'</a>';
      }
      $advs = $advs.'</ul></ul>';
    }
    
    if(count($unusedUdvs)>0){
      $advs = $advs.'<ul><a href="#advudv" class="typeLink" id="advudv" data-track="Unused User Defined Variables"><h4>Unused User Defined Variables. ('.count($unusedUdvs).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($unusedUdvs as $uudvIndex => $uudvVal){
        $advs = $advs.'<li><a href="#udv'.$uudvVal['variableId'].'" id="udv'.$uudvVal['variableId'].'" data-asset="udv" data-index='.$uudvVal['variableId'].' data-type='.$uudvVal['type'].' class="advDetail">'.$uudvVal['name'].'</a>';
      }
      $advs = $advs.'</ul></ul>';
    }
        
    //userId issues
    if(count($userIdUsed)>0 && count($userIdUsed) < count($tagLibrary['ua'])){
      $advs = $advs.'<ul><a href="#advuidin" class="typeLink" id="advuidin" data-track="UA tags with UserId"><h4>The following Universal Analytics tags ARE decorated with userId:</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      
      foreach($userIdUsed as $tagIndex => $tagVal){
        foreach($tagLibrary['ua'] as $libIndex => $libVal){
          if($tagLibrary['ua'][$libIndex]['tagId']===$userIdUsed[$tagIndex]){
            $advs = $advs.'<li><a href="#tag'.$libVal['tagId'].'" id="adv'.$libVal['tagId'].'" data-asset="tag" data-index='.$libVal['tagId'].' data-type="ua" class="advDetail">'.$libVal['name'].'</a>';
          }
        }
      }
      $advs = $advs.'</ul></ul>';
          
      $advs = $advs.'<ul><a href="#advuid" class="typeLink" id="advuid" data-track="UA tags without UserId"><h4>The following Universal Analytics tags are NOT decorated with userId:</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      
      foreach($tagLibrary['ua'] as $tagIndex => $tagVal){
        if(!array_key_exists($tagVal['tagId'],$userIdUsed)){
          $advs = $advs.'<li><a href="#tag'.$tagVal['tagId'].'" id="adv'.$tagVal['tagId'].'" data-asset="tag" data-index='.$tagVal['tagId'].' data-type="ua" class="advDetail">'.$tagVal['name'].'</a>';
        }
      }
      $advs = $advs.'</ul></ul>';
    }
        
    //variables that push...
    if(count($variablesThatMessage)>0){
      $advs = $advs.'<ul><a href="#pushy" class="typeLink" id="pushyudv" data-track="Variables that push"><h4>Custom JavaScript variables push messages onto the dataLayer. You want to check these... ('.count($variablesThatMessage).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($variablesThatMessage as $pushyIndex => $pushyVal){
        $advs = $advs.'<li><a href="#udv'.$pushyVal['variableId'].'" id="adv'.$pushyVal['variableId'].'" data-asset="udv" data-index='.$pushyVal['variableId'].' data-type='.$pushyVal['type'].' class="advDetail">'.$pushyVal['name'].'</a>';
      }
      $advs = $advs.'</ul></div></ul>';
    }
    
    //duplicated assets...
    $doops = [];
    $doops['tags'] = [];
    $doops['triggers'] = [];
    $doops['variables'] = [];
    
    getDoops($tagTypes,$tagLibrary,'tag',$doops);
    getDoops($trigTypes,$trigLibrary,'trigger',$doops);
    getDoops($udvTypes,$udvLibrary,'variable',$doops);
    
    if(count($doops['tags'])>0){
      $advs = $advs.'<ul><a href="#doopTags" class="typeLink" id="doopTags" data-track="Duplicate tags"><h4>These tags look <i><b>mighty</b></i> similar...very much alike. You want to check these...</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($doops['tags'] as $dooptagIndex => $dooptagVal){
        $advs = $advs.'<li><a href="#tag'.$dooptagVal[0]['tagId'].'" id="tag'.$dooptagVal[0]['tagId'].'" data-asset="tag" data-index='.$dooptagVal[0]['tagId'].' data-type='.$dooptagVal[0]['type'].' class="advDetail">'.$dooptagVal[0]['name'].'</a> is identical to <a href="#tag'.$dooptagVal[1]['tagId'].'" id="tag'.$dooptagVal[1]['tagId'].'" data-asset="tag" data-index='.$dooptagVal[1]['tagId'].' data-type='.$dooptagVal[1]['type'].' class="advDetail">'.$dooptagVal[1]['name'].'</a>';
      }
      $advs = $advs.'</ul></div></ul>';
    }
    
    if(count($doops['triggers'])>0){
      $advs = $advs.'<ul><a href="#doopTriggers" class="typeLink" id="doopTriggers" data-track="Duplicate triggers"><h4>These triggers look <i><b>mighty</b></i> similar...very much alike. You want to check these...</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($doops['triggers'] as $dooptrigIndex => $dooptrigVal){
        $advs = $advs.'<li><a href="#trig'.$dooptrigVal[0]['triggerId'].'" id="trig'.$dooptrigVal[0]['triggerId'].'" data-asset="trig" data-index='.$dooptrigVal[0]['triggerId'].' data-type='.$dooptrigVal[0]['type'].' class="advDetail">'.$dooptrigVal[0]['name'].'</a> is identical to <a href="#trig'.$dooptrigVal[1]['triggerId'].'" id="trig'.$dooptrigVal[1]['triggerId'].'" data-asset="trig" data-index='.$dooptrigVal[1]['triggerId'].' data-type='.$dooptrigVal[1]['type'].' class="advDetail">'.$dooptrigVal[1]['name'].'</a>';
      }
      $advs = $advs.'</ul></div></ul>';
    }
    
    if(count($doops['variables'])>0){
      $advs = $advs.'<ul><a href="#doopUdvs" class="typeLink" id="doopUdvs" data-track="Duplicate variables"><h4>These variables look <i><b>mighty</b></i> similar...very much alike. You want to check these...</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($doops['variables'] as $doopudvIndex => $doopudvVal){
        $advs = $advs.'<li><a href="#udv'.$doopudvVal[0]['variableId'].'" id="udv'.$doopudvVal[0]['variableId'].'" data-asset="udv" data-index='.$doopudvVal[0]['variableId'].' data-type='.$doopudvVal[0]['type'].' class="advDetail">'.$doopudvVal[0]['name'].'</a> is identical to <a href="#udv'.$doopudvVal[1]['variableId'].'" id="udv'.$doopudvVal[1]['variableId'].'" data-asset="udv" data-index='.$doopudvVal[1]['variableId'].' data-type='.$doopudvVal[1]['type'].' class="advDetail">'.$doopudvVal[1]['name'].'</a>';
      }
      $advs = $advs.'</ul></div></ul>';
    }
        
    if(count($mismatchArray)>0){
      $advs = $advs.'<ul><a href="#advmm" class="typeLink" id="advmm" data-track="Unclosed script tags in jsm variables"><h4>Mismatched script tags in Custom HTML tags.('.count($mismatchArray).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
      foreach($mismatchArray as $mmIndex => $mmVal){
        $advs = $advs.'<li><a href="#tag'.$mmVal['tagId'].'" id="tag'.$mmVal['tagId'].'" data-asset="tag" data-index='.$mmVal['tagId'].' data-type=html class="advDetail">'.$mmVal['name'].'</a></li>';
      }
      $advs = $advs.'</ul></ul>';
    }
    
    if($unFolderedTagCount>0){
      $advs = $advs.'<ul><a href="#advuftag" class="typeLink" id="advuftag" data-track="Unfoldered tags"><h4>Unfoldered tags. ('.$unFolderedTagCount.')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">'.$unFolderedTags.'</ul></ul>';
    }
      
    if($unFolderedTriggerCount>0){
      $advs = $advs.'<ul><a href="#advuftrig" class="typeLink" id="advuftrig" data-track="Unfoldered triggers"><h4>Unfoldered triggers. ('.$unFolderedTriggerCount.')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">'.$unFolderedTriggers.'</ul></ul>';
    }
      
    if($unFolderedUdvCount>0){
      $advs = $advs.'<ul><a href="#advufudv" class="typeLink" id="advufudv" data-track="Unfoldered udvs"><h4>Unfoldered User Defined Variables. ('.$unFolderedUdvCount.')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">'.$unFolderedUdvs.'</ul></ul>';
    }
    
    if(count($legacyTags)>0){
      $advs = $advs.'<ul><a href="#legacytags" class="typeLink" id="legacytags" data-track="Legacy tags"><h4>Legacy tags. ('.count($legacyTags).')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
  
      foreach($legacyTags as $ltKey => $ltVal){
        $advs = $advs.'<li><a href="#tag'.$ltVal['tagId'].'" id="l'.$ltVal['tagId'].'" data-asset="tag" data-index='.$ltVal['tagId'].' data-type='.$ltVal['type'].' class="advDetail">'.$ltVal['name'].'</a>';
      }
      
      $advs = $advs.'</ul></ul>';
    }
    
 //should be templated tags
    if(array_key_exists('html',$tagLibrary)){
      $templateCount = 0;
      $templateThese = [];
      $templateThese['adm'] = [];
      $templateThese['awct'] = [];
      $templateThese['sp'] = [];
      $templateThese['fls'] = [];
      $templateThese['flc'] = [];
      
      foreach($tagLibrary['html'] as $cIndex => $cVal){
        //adm
        $admRe = "/js\.dmtry\.com\/channel\.js/";
        $admArray = [];
        
        //awct
        $awctRe = "/googleads\.g\.doubleclick\.net\/pagead\/conversion\//";
        $awctArray = [];
        
        //sp - adwords remarketing
        $spRe = "/googleads\.g\.doubleclick\.net\/pagead\/viewthroughconversion\//";
        $spArray = [];
        
        //fl - floodlight tags
        $flRe = "/\.fls\.doubleclick\.net\/activityi/";
        $flArray = [];
        
        $rawHTML = getParam($tagLibrary['html'][$cIndex],'html');
  
        preg_match_all($admRe, $rawHTML, $admArray);
        preg_match_all($awctRe, $rawHTML, $awctArray);
        preg_match_all($spRe, $rawHTML, $spArray);
        preg_match_all($flRe, $rawHTML, $flArray);

        if(count($admArray[0])>0){
          array_push($templateThese['adm'],$tagLibrary['html'][$cIndex]);
          
          $templateCount++;
        }
        
        if(count($awctArray[0])>0){
          array_push($templateThese['awct'],$tagLibrary['html'][$cIndex]);
        
          $templateCount++;
        }
        
        if(count($spArray[0])>0){
          array_push($templateThese['sp'],$tagLibrary['html'][$cIndex]);
        
          $templateCount++;
        }
        
        if(count($flArray[0])>0){
          if(strpos($rawHTML,'qty=')!==false){
            array_push($templateThese['fls'],$tagLibrary['html'][$cIndex]);  
          }else{
            array_push($templateThese['flc'],$tagLibrary['html'][$cIndex]);
          }
        
          $templateCount++;
        }
      }
      
      if($templateCount>0){
        $advs = $advs.'<ul><a href="#templatetags" class="typeLink" id="templatetags" data-track="Tags that should be templated"><h4>Tags that should use templates rather than Custom HTML. ('.$templateCount.')</h4></a><div style="margin:5px;display:none" class="assetList container"><ul style="margin:5px">';
        
        foreach($templateThese as $ttKey => $ttVal){
          foreach($ttVal as $tKey => $tVal){
            $advs = $advs.'<li><a href="#tag'.$tVal['tagId'].'" id="tt'.$tVal['tagId'].'" data-asset="tag" data-index='.$tVal['tagId'].' data-type='.$tVal['type'].' class="advDetail">'.$tVal['name'].'</a> (Use the '.translateToHuman($ttKey,$tVal).' template)';
          }
        }
        
        $advs = $advs.'</ul></ul>';
      }
    }
      
    if($advs!==''){
       echo '<div class="container" style="margin:5px" id="advlist">'.$advs.'</div><br />'; 
    }else{
      echo "jQuery('#advLink').remove()";
    }
  			
  	echo '</div><br />';
  }
?>