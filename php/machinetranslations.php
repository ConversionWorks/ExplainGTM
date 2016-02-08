<?php
  /********************************************************************************
   * Translate humanised Built In Variables back to machine speak
  *********************************************************************************/
  function translateToMachine($human){
    $machineSpeak = '';
    
    if($human === null || $human == ''){
        return false;
    }
    
    //if($human==='{{Page Path}}'){
    //  echo 'PP blah!!!!';
    //}
  
    switch($human){
      case '{{HTML ID}}':
          $machineSpeak = 'HTML_ID';
      break;
      
      case '{{Environment Name}}':
          $machineSpeak = 'ENVIRONMENT_NAME';
      break;
      
      case '{{Page URL}}':
          $machineSpeak = 'PAGE_URL';
      break;
      
      case '{{Page Path}}':
          $machineSpeak = 'PAGE_PATH';
      break;
  
      case '{{Click URL}}':
          $machineSpeak = 'CLICK_URL';
      break;
      
      case '{{Form ID}}':
          $machineSpeak = 'FORM_ID';
      break;
  
      case '{{Debug Mode}}':
          $machineSpeak = 'DEBUG_MODE';
      break;
      
      case '{{Click ID}}':
          $machineSpeak = 'CLICK_ID';
      break;
      
      case '{{Container ID}}':
          $machineSpeak = 'CONTAINER_ID';
      break;
  
      case '{{Form Classes}}':
          $machineSpeak = 'FORM_CLASSES';
      break;
      
      case '{{Click Text}}':
          $machineSpeak = 'CLICK_TEXT';
      break;
  
      case '{{Click Element}}':
          $machineSpeak = 'CLICK_ELEMENT';
      break;
  
      case '{{Page Hostname}}':
          $machineSpeak = 'PAGE_HOSTNAME';
      break;
  
      case '{{Form URL}}':
          $machineSpeak = 'FORM_URL';
      break;
  
      case '{{Error Message}}':
          $machineSpeak = 'ERROR_MESSAGE';
      break;
  
      case '{{Form Element}}':
          $machineSpeak = 'FORM_ELEMENT';
      break;
  
      case '{{Error Line}}':
          $machineSpeak = 'ERROR_LINE';
      break;
  
      case '{{Form Text}}':
          $machineSpeak = 'FORM_TEXT';
      break;
  
      case '{{Form Target}}':
          $machineSpeak = 'FORM_TARGET';
      break;
  
      case '{{Referrer}}':
          $machineSpeak = 'REFERRER';
      break;
  
      case '{{Error URL}}':
          $machineSpeak = 'ERROR_URL';
      break;
  
      case '{{Event}}':
          $machineSpeak = 'EVENT';
      break;
  
      case '{{Click Classes}}':
          $machineSpeak = 'CLICK_CLASSES';
      break;
  
      case '{{Click Target}}':
          $machineSpeak = 'CLICK_TARGET';
      break;
    }
    
    return $machineSpeak;
  }
?>