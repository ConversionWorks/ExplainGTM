<?php
  $container = '';
  $contRef = '';
  $untriggeredTags = [];
  $usedTriggers = [];
  $usedUdvs = [];
  $udvUsage = [];
  $unusedUdvs = [];
  $untaggedTriggers = [];
  $userIdUsed = [];
  $variablesThatMessage = [];
  $mismatchArray = [];
  $legacyTags = [];

  $tagLibrary = [];
  $tagTypes = [];
  $tagP = [];
  
  $trigLibrary = [];
  $trigTypes = [];

  $udvLibrary = [];
  $udvTypes = [];
  
  $folderArray = [];
  $bivArray = [];
  
  function buildLibrary($entities,&$entityTypes){
    $entityLibrary = [];
    foreach($entities as $eKey => $eVal) {
      if(!in_array($eVal['type'],$entityTypes)){
          $entityLibrary[$eVal['type']] = [];
          array_push($entityTypes,$eVal['type']);
      }
      array_push($entityLibrary[$eVal['type']],$entities[$eKey]);
    }
    return $entityLibrary;
  }
  
  //********************************************************************************
  // * Parsing
  //*********************************************************************************/
  //********************************************************************************
  // * Variablise
  //*********************************************************************************/
  
  function biVariablise($variable){
    global $bivArray;
    
    $machineVar = translateToMachine($variable);
    
    foreach($bivArray as $bivKey => $bivVal){
      if($machineVar === $bivVal){
          $variable = translateToHuman($bivArray[$bivKey], $variable);
      }
    }
    return $variable;
  }
  
  function usageRecorded($variableList,$entity,$entityId,$udvId = 0){
    $recorded = false;
            
    foreach($variableList as $i => $v){
      if($v[$entityId] === $entity[$entityId]){
        $recorded = true;
      }
    } 
    
    return $recorded;
  }
  
  function surgicalVariablise($variable,$entity){
    $varFound = false;
    global $udvTypes,$udvLibrary,$udvUsage, $bivArray,$usedUdvs;
      
    foreach($udvTypes as $udvTypeKey => $udvTypeVal){
      foreach($udvLibrary[$udvTypeVal] as $udvK => $udvV){
        if($variable === '{{'.$udvV['name'].'}}'){
          $variable = '<a href="#udv'.$udvV['variableId'].'" class="variableDetail" ';
          $variable = $variable.'onclick="jQuery(\'.assetList\').hide();jQuery(\'.assetDetail\').hide();jQuery(\'.assetContainer\').hide();jQuery(\'#udvs\').toggle();jQuery(\'#udvType'.$udvTypeVal.'\').next().toggle();jQuery(\'#udvDetail'.$udvV['variableId'].'\').toggle()">';
          $variable = $variable.$udvV['name'].'</a>';
          $varFound = true;
          
          $udvUsageIndex = $udvV['variableId'];
          if(array_key_exists($udvUsageIndex,$udvUsage)===false){
            $udvUsage[$udvUsageIndex] = [];
            $udvUsage[$udvUsageIndex]['tags'] = [];
            $udvUsage[$udvUsageIndex]['triggers'] = [];
            $udvUsage[$udvUsageIndex]['variables'] = [];
          }
          
          if(array_key_exists('variableId',$entity)!==false){
            if(usageRecorded($udvUsage[$udvUsageIndex]['variables'],$entity,'variableId',$udvUsageIndex)===false){
              array_push($udvUsage[$udvUsageIndex]['variables'],$entity);
            }
          }
          
          if(array_key_exists('tagId',$entity)!==false){
            if(usageRecorded($udvUsage[$udvUsageIndex]['tags'],$entity,'tagId')===false){
              array_push($udvUsage[$udvUsageIndex]['tags'],$entity);
            }
          }
          
          if(array_key_exists('triggerId',$entity)!==false){
            if(usageRecorded($udvUsage[$udvUsageIndex]['triggers'],$entity,'triggerId')===false){
              array_push($udvUsage[$udvUsageIndex]['triggers'],$entity);
            }
          }
          
          if(array_search('{{'.$udvV['name'].'}}',$usedUdvs)===false){
            array_push($usedUdvs,'{{'.$udvV['name'].'}}');
          }
        }
      }
    }

    if(!$varFound){
      $variable = biVariablise($variable, $bivArray);
    }
    
    return $variable;
  }
  
  function variablise($variable, $entity, $prepend = false){
    $varRefRe = "/{{.*?}}/";
    $varArray = [];
    global $udvUsage;
    
    if(strpos($variable,'{{')!==false){
      preg_match_all($varRefRe, $variable, $varArray);
      
      foreach($varArray[0] as $matchKey => $matchVal){
        $variable = str_replace($matchVal,surgicalVariablise($matchVal,$entity),$variable);
      }
    }
    
    if($prepend!==false){
      if(count($varArray)>1){
        $variable = 'The variables: '.$variable;
      }
      if(count($varArray)===1){
        $variable = 'The variable: '.$variable;
      }
    }
    return $variable;
  }
  
  function getParamTemplate($entity,$templateName){
    $templateListRet = '';

    foreach($entity['parameter'] as $key => $paramT){
      if($paramT['key']===$templateName && $paramT['type']==='TEMPLATE'){
        $templateListRet = $paramT;
      }
    }
  
    return $templateListRet;
  }
  
  function getParamList($entity,$listName){
    $paramListRet = '';
    
    foreach($entity['parameter'] as $key => $params){
      foreach($entity['parameter'][$key] as $pKey => $lists){
        if($lists==='LIST' && $entity['parameter'][$key]['key']===$listName){
          $paramListRet = array_key_exists('list',$entity['parameter'][$key]) ? $entity['parameter'][$key]['list'] : '';
        }
      }
    }
  
    return $paramListRet;
  }
  
  function getParam($entity,$paramName){
    $paramRet = '';
      
    foreach($entity['parameter'] as $key => $pIndex){
        if($entity['parameter'][$key]['key']==$paramName){
            $paramRet = $entity['parameter'][$key]['value'];
        }
     }
  
     return $paramRet;
  }
  
  function getVarRefs($raw,$entity){
    global $usedUdvs;
    
    $varRefRe = "/{{.*?}}/";
    $varArray = [];
    $varDeets = '';
    
    if(strpos($raw,'{{')){
      preg_match_all($varRefRe, $raw, $varArray);
  
      foreach($varArray[0] as $matchIndex => $matchVal){
        $varDeets = $varDeets.'<li>'.variablise($varArray[0][$matchIndex],$entity).'</li>';
          
        //remember something has used the variable.
        if(array_search($varArray[0][$matchIndex],$usedUdvs)===false){
          array_push($usedUdvs,$varArray[0][$matchIndex]);
        } 
      }
      
      if($varDeets !== ''){
        $varDeets = '<ul>'.$varDeets.'</ul><br />'; 
      }
    }
  
    return $varDeets;
  }
?>