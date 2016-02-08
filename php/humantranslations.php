<?php
  //********************************************************************************
  // * Translate asset names to human speak
  //*********************************************************************************/
  function translateToHuman($machine,$entity = null, $prepend = false){
      $humanSpeak = '';
      
      if($machine === ''){
        return false;
      }
      
      if(strpos($machine,'{')!==false){
        return variablise($machine,$entity,$prepend);
      }
  
      switch($machine){
        case 'HTML_ID':
            $humanSpeak = '<span title="The ID of the Custom HTML tag." style="background-color: #CCC">HTML ID</span>';
        break;
        
        case 'ENVIRONMENT_NAME':
            $humanSpeak = '<span title="The name of the environment." style="background-color: #CCC">Environment Name</span>';
        break;
        
        case 'PAGE_URL':
            $humanSpeak = '<span title="The full URL of the user\'s page." style="background-color: #CCC">Page URL</span>';
        break;
  
        case 'PAGE_HOSTNAME':
            $humanSpeak = '<span title="The domain name of the user\'s page." style="background-color: #CCC">Page Hostname</span>';
        break;
  
        case 'PAGE_PATH':
            $humanSpeak = '<span title="The part of the url after the Page Hostname." style="background-color: #CCC">Page Path</span>';
        break;
  
        case 'REFERRER':
            $humanSpeak = '<span title="The last page the user was on." style="background-color: #CCC">Referrer</span>';
        break;
  
        case 'EVENT':
            $humanSpeak = '<span title="A GTM event." style="background-color: #CCC">Event</span>';
        break;
  
        case 'CLICK_ELEMENT':
            $humanSpeak = '<span title="The thing on the page that was clicked" style="background-color: #CCC">Click Element</span>';
        break;        
  
        case 'CLICK_CLASSES':
            $humanSpeak = '<span title="The CSS classes on the thing that was clicked." style="background-color: #CCC">Click Classes</span>';
        break;
  
        case 'CLICK_ID':
            $humanSpeak = '<span title="The ID of the thing that was clicked" style="background-color: #CCC">Click ID</span>';
        break;
  
        case 'CLICK_TARGET':
            $humanSpeak = '<span title="The target of the link that was clicked." style="background-color: #CCC">Click Target</span>';
        break;
  
        case 'CLICK_URL':
            $humanSpeak = '<span title="The URL of the link that was clicked." style="background-color: #CCC">Click URL</span>';
        break;
  
        case 'CLICK_TEXT':
            $humanSpeak = '<span title="The text of the link that was clicked." style="background-color: #CCC">Click Text</span>';
        break;
  
        case 'FORM_ELEMENT':
            $humanSpeak = '<span title="The form that was submitted." style="background-color: #CCC">Form Element</span>';
        break;
  
        case 'FORM_CLASSES':
            $humanSpeak = '<span title="The CSS classes on the form that was submitted." style="background-color: #CCC">Form Classes</span>';
        break;
  
        case 'FORM_ID':
            $humanSpeak = '<span title="The ID of the form that was submitted." style="background-color: #CCC">Form ID</span>';
        break;
  
        case 'FORM_TARGET':
            $humanSpeak = '<span title="The target of the form that was submitted." style="background-color: #CCC">Form Target</span>';
        break;
  
        case 'FORM_TARGET':
            $humanSpeak = '<span title="The target of the form that was submitted." style="background-color: #CCC">Form Target</span>';
        break;
  
        case 'FORM_URL':
            $humanSpeak = '<span title="The URL of the form that was submitted." style="background-color: #CCC">Form URL</span>';
        break;
  
        case 'FORM_TEXT':
            $humanSpeak = '<span title="The text of the form that was submitted." style="background-color: #CCC">Form Text</span>';
        break;
  
        case 'ERROR_MESSAGE':
            $humanSpeak = '<span title="The error message of the JavaScript error." style="background-color: #CCC">Error Message</span>';
        break;
  
        case 'ERROR_URL':
            $humanSpeak = '<span title="The URL of the page where the JavaScript error happened." style="background-color: #CCC">Error URL</span>';
        break;
  
        case 'ERROR_LINE':
            $humanSpeak = '<span title="The line number of the JavaScript error." style="background-color: #CCC">Error Line</span>';
        break;
  
        case 'NEW_HISTORY_FRAGMENT':
            $humanSpeak = '<span title="The new part of the URL after # on an AJAX page." style="background-color: #CCC">New History Fragment</span>';
        break;
  
        case 'OLD_HISTORY_FRAGMENT':
            $humanSpeak = '<span title="The old part of the URL after # on an AJAX page." style="background-color: #CCC">Old History Fragment</span>';
        break;
  
        case 'NEW_HISTORY_STATE':
            $humanSpeak = '<span title="The current AJAX page." style="background-color: #CCC">New History State</span>';
        break;
  
        case 'OLD_HISTORY_STATE':
            $humanSpeak = '<span title="The last AJAX page." style="background-color: #CCC">Old History State</span>';
        break;
  
        case 'HISTORY_SOURCE':
            $humanSpeak = '<span title="What changed the AJAX page history?" style="background-color: #CCC">History Source</span>';
        break;
  
        case 'CONTAINER_VERSION':
            $humanSpeak = '<span title="The version of the GTM container on the page." style="background-color: #CCC">Container Version</span>';
        break;
  
        case 'DEBUG_MODE':
            $humanSpeak = '<span title="Is the container in debug mode - true or false." style="background-color: #CCC">Debug Mode</span>';
        break;
  
        case 'RANDOM_NUMBER':
            $humanSpeak = '<span title="A random number between 0 and 2147483647." style="background-color: #CCC">Random Number</span>';
        break;
  
        case 'CONTAINER_ID':
            $humanSpeak = '<span title="The ID of the GTM containter on the page." style="background-color: #CCC">Container ID</span>';
        break;
  
        case 'HTML_ID':
            $humanSpeak = '<span title="The GTM ID of a Custom HTML tag." style="background-color: #CCC">HTML ID</span>';
        break;
  
        case 'CONTAINS':
            $humanSpeak = ' contains ';
        break;
  
        case 'EQUALS':
            $humanSpeak = ' equals ';
        break;
        
        case 'MATCH_REGEX':
            $humanSpeak = ' matches the regular expression (case sensitive) ';
        break;
          
        case 'REGEX_IGNORE_CASE':
            $humanSpeak = ' matches the regular expression (<b>not</b> case sensitive) ';
        break;
        
        case 'STARTS_WITH':
            $humanSpeak = '  starts with';
        break;
        
        case 'ENDS_WITH':
            $humanSpeak = ' ends with ';
        break;
        
        case 'CSS_SELECTOR':
            $humanSpeak = ' matches the CSS selector ';
        break;
        
        case 'NOT_EQUALS':
            $humanSpeak = ' does not equal ';
        break;
        
        case 'NOT_STARTS_WITH':
            $humanSpeak = ' does not start with ';
        break;
        
        case 'NOT_ENDS_WITH':
            $humanSpeak = ' does not end with ';
        break;
        
        case 'NOT_CSS_SELECTOR':
            $humanSpeak = ' does not match the CSS selector ';
        break;
        
        case 'NOT_REGEX':
            $humanSpeak = ' does not match regular expression (case sensitive) ';
        break;
        
        case 'NOT_REGEX_IGNORE_CASE':
            $humanSpeak = ' does not match regular expression (<b>not</b> case sensitive) ';
        break;
        
        case 'LESS_THAN':
            $humanSpeak = ' is less than ';
        break;
        
        case 'LESS_EQUALS':
            $humanSpeak = ' is less than or equal to ';
        break;
  
        case 'GEATER_THAN':
            $humanSpeak = ' is greater than ';
        break;
        
        case 'GREATER_EQUALS':
            $humanSpeak = ' is greater than or equal to ';
        break;
  
        case 'CUSTOM_EVENT':
            $humanSpeak = 'Custom Event triggers';
        break;
  
        case 'PAGEVIEW':
            $humanSpeak = 'Pageview triggers';
        break;
  
        case 'DOM_READY':
            $humanSpeak = 'Dom Ready triggers';
        break;
  
        case 'CLICK':
            $humanSpeak = 'Click triggers';
        break;
  
        case 'LINK_CLICK':
            $humanSpeak = 'Link Click triggers';
        break;
        
        case 'WINDOW_LOADED':
            $humanSpeak = 'Window Load triggers';
        break;
  
        case 'JS_ERROR':
            $humanSpeak = 'JavaScript Error triggers';
        break;
        
        case 'FORM_SUBMISSION':
            $humanSpeak = 'Form submission triggers';
        break;
  
        case 'html':
            $humanSpeak = 'Custom HTML tags';
        break;
        
        case 'img':
            $humanSpeak = 'Image tags';
        break;
        
        case 'jel':
            $humanSpeak = 'JavaScript Error Listener tags';
        break;
        
        case 'lcl':
            $humanSpeak = 'Link Click Listener tags';
        break;
        
        case 'cl':
            $humanSpeak = 'Click Listener tags';
        break;
        
        case 'fsl':
            $humanSpeak = 'Form Submit Listener tags';
        break;
        
        case 'tl':
            $humanSpeak = 'Timer Listener tags';
        break;
        
        case 'hl':
            $humanSpeak = 'History Listener tags';
        break;
  
        case 'flc':
            $humanSpeak = 'Floodlight Counter tags';
        break;
  
        case 'awct':
            $humanSpeak = 'AdWords Conversion tracking tags';
        break;
  
        case 'sp':
            $humanSpeak = 'AdWords Remarketing tags';
        break;
  
        case 'ga':
            $humanSpeak = 'Classic Google Analytics tags';
        break;
  
        case 'ua':
            $humanSpeak = 'Universal Analytics tags';
        break;
  
        case 'fls':
            $humanSpeak = 'Floodlight Sales tags';
        break;
        
        case 'mpm':
            $humanSpeak = 'Mediaplex IFrame tags';
        break;
        
        case 'mpr':
            $humanSpeak = 'Mediaplex Standard ROI tags';
        break;
        
        case 'tc':
            $humanSpeak = 'Turn Conversion Tracking tags';
        break;
        
        case 'asp':
            $humanSpeak = 'AdRoll Smart Pixel tags';
        break;
        
        case 'tdc':
            $humanSpeak = 'Turn Data Collection tags';
        break;
        
        case 'ms':
            $humanSpeak = 'Marin tags';
        break;
  
        case '_ta':
            $humanSpeak = 'AdAdvisor tags';
        break;
        
        case 'bzi':
            $humanSpeak = 'Bizo Insight tags';
        break;
        
        case 'm6d':
            $humanSpeak = 'Dstillery Universal Pixel tags';
        break;
        
        case 'vdc':
            $humanSpeak = 'VisualDNA Conversion tags';
        break;
  
        case 'cts':
            $humanSpeak = 'ClickTale Standard tags';
        break;
        
        case 'cms':
            $humanSpeak = 'comScore Unified Digital Measurement tags';
        break;
        
        case '_fc':
            $humanSpeak = 'Function Call tags';
        break;
        
        case 'adm':
            $humanSpeak = 'Adometry tags';
        break;
        
        case 'ts':
            $humanSpeak = 'Google Trusted Store tags';
        break;
  
        case 'ONCE_PER_EVENT': 
            $humanSpeak = 'This tag only fires once per event.';
        break;
  
        case 'ONCE_PER_LOAD':
            $humanSpeak = 'This tag only fires once per page load.';
        break;
  
        case 'TRACK_EVENT':
            $humanSpeak = 'Event Tracking tag.';
        break;
  
        case 'TRACK_TRANSACTION':
            $humanSpeak = 'Transaction Tracking tag.';
        break;
  
        case 'TRACK_PAGEVIEW':
            $humanSpeak = 'Pageview Tracking tag.';
        break;
  
        case 'TRACK_TIMING':
            $humanSpeak = 'Timing Tracking tag.';
        break;
  
        case 'TRACK_SOCIAL':
            $humanSpeak = 'Social Tracking tag.';
        break;
        
        case 'DECORATE_LINK':
            $humanSpeak = 'Link decoration (Cross Domain) tag.';
        break;
        
        case 'DECORATE_FORM':
            $humanSpeak = 'Form decoration (Cross Domain) tag.';
        break;
        
        case 'LINK':
            $humanSpeak = 'Link decoration (Cross Domain) tag.<br /><br />';
        break;
        
        case 'LINK_BY_POST':
            $humanSpeak = 'Form decoration (Cross Domain) tag.<br /><br />';
        break;
        
        case 'TRACK_APPVIEW':
            $humanSpeak = 'Appview tag.<br /><br />';
        break;
        
        case 'TRACK_EXCEPTION':
            $humanSpeak = 'Exception tag.<br /><br />';
        break;
  
        case '{{Click URL}}':
            $humanSpeak = ' the URL of the link that is clicked ';
  
            //get social details
        break;
  
        case '{{element Title}}':
            $humanSpeak = ' the title of the thing that is clicked ';
  
            //get social details
        break;
  
        case '{{Click Classes}}':
            $humanSpeak = ' the CSS classes of the thing that is clicked ';
  
            //get social details
        break;
  
        case '{{Click ID}}':
            $humanSpeak = ' the ID of the thing that is clicked ';
  
            //get social details
        break;
  
        case '{{Page Path}}':
            $humanSpeak = ' the path of the page the user is on ';
  
            //get social details
        break;
  
        case '{{Page Hostname}}':
            $humanSpeak = ' the domain (name) of the website ';
  
            //get social details
        break;
  
        case 'f':
            $humanSpeak = 'HTTP Referrer';
        break;
  
        case 'v':
            $humanSpeak = 'Data Layer';
        break;
  
        case 'e':
            $humanSpeak = 'Custom Event';
        break;
  
        case 'smm':
            $humanSpeak = 'Lookup Table';
        break;
  
        case 'jsm':
            $humanSpeak = 'Custom JavaScript';
        break;
  
        case 'u':
            $humanSpeak = 'URL';
        break;
        
        case 'k':
            $humanSpeak = '1st Party Cookie';
        break;
        
        case 'aev':
            $humanSpeak = 'Auto Event Variable';
        break;
        
        case 'c':
            $humanSpeak = 'Constant';
        break;
        
        case 'ctv':
            $humanSpeak = 'Container Version Number';
        break;
        
        case 'cid':
            $humanSpeak = 'Container ID';
        break;
        
        case 'dbg':
            $humanSpeak = 'Debug Mode';
        break;
        
        case 'd':
            $humanSpeak = 'DOM Element';
        break;
        
        case 'j':
            $humanSpeak = 'JavaScript Variable';
        break;
        
        case 'r':
            $humanSpeak = 'Random Number';
        break;
        
        case 'HOST':
            $humanSpeak = ' domain name of the website ';
        break;
        
        case 'QUERY':
            $humanSpeak = ' querystring ';
        break;
        
        case 'FRAGMENT':
            $humanSpeak = ' fragment ';
        break;
  
        default:
            $humanSpeak = false;
      }
  
      return $humanSpeak;
  }
  
  //********************************************************************************
  // * Human friendly asset descriptions
  //*********************************************************************************/
  function humanDetails($machine,$entity = null){
      $humanSpeak = '';
      
      switch($machine){
        case 'TRACK_PAGEVIEW':
            $humanSpeak = '<td>This tag will track pageviews.';
            
            //Universal Pageview
            $fields = getParamList($entity,'fieldsToSet');
            if($fields!=='' && count($fields) > 0){
              foreach($fields as $fieldVal){
                if($fieldVal['map']['0']['value']==='page'){
                  $humanSpeak = $humanSpeak.'<br />This tag is setup to handle virtual pageviews using '.variablise($fieldVal['map'][1]['value'],$entity,true).' as the virtual page path.';
                }
              }
            }
            
            //Classic Pageview
            $entityDeets = $entity['parameter'];
  
            if($entityDeets!==null && count($entityDeets) > 0){
              foreach($entityDeets as $edKey => $edVal){
                if(array_key_exists('key',$entityDeets[$edKey]) && $entityDeets[$edKey]['key']==='page'){
                  $humanSpeak = $humanSpeak.'<br />This tag is setup to handle virtual pageviews using '.variablise($entityDeets[$edKey]['value'],$entity,true).' as the virtual page path.';
                }
              }
            }
        break;
        
        case 'TRACK_EVENT':
          $humanSpeak = '<td>This tag will track events using the following details:';
      
          $eCat = getParam($entity,'eventCategory');
          $eAct = getParam($entity,'eventAction');
          $eLab = getParam($entity,'eventLabel');
          $eVal = getParam($entity,'eventValue');
          $eInt = getParam($entity,'nonInteraction');
    
          $humanSpeak = $humanSpeak.'<table class="table table-bordered table-striped">';
          if($eCat!==null && $eCat !==''){$humanSpeak = $humanSpeak.'<tr><th>Event Category</th><td>'.variablise($eCat,$entity,true).'</td></tr>';}
          if($eAct!==null && $eAct!==''){$humanSpeak = $humanSpeak.'<tr><th>Event Action</th><td>'.variablise($eAct,$entity,true).'</td></tr>';}
          if($eLab!==null && $eLab!==''){$humanSpeak = $humanSpeak.'<tr><th>Event Label</th><td>'.variablise($eLab,$entity,true).'</td></tr>';}
          if($eVal!==null && $eVal!==''){$humanSpeak = $humanSpeak.'<tr><th>Event Value</th><td>'.variablise($eVal,$entity,true).'</td></tr>';}
         
          if($eInt === null || $eInt == 'false'){
            $humanSpeak = $humanSpeak.'<tr><td></td><td>This is an interactive Event.<br />It will affect the bounce status of the session.<br />It will affect session duration and time on page metrics.<br />It will appear in real-time reports.</td></tr>';
          }else{
            if($eInt !== 'true'){
              $humanSpeak = $humanSpeak.'<tr><td>Interaction determined by:</td><td>'.variablise($eInt,$entity,true).'</td></tr>';
            }else{
              $humanSpeak = $humanSpeak.'<tr><td></td><td>This is a NON-interactive Event.<br />It won\'t affect the bounce status of the session.<br />It won\'t affect session duration or time on page metrics.<br />It won\'t appear in real-time reports.</td></tr>';
            }
          }
    
          $humanSpeak = $humanSpeak.'</table></td>';
        break;
        
        case 'TRACK_TIMING':
          $humanSpeak = '<td>This tag will measure periods of time.<br />These could be anything from downloading jQuery on the page to the time it takes a user to click a link or button.<br />This is a very flexible tag type and likely involves developers to make it work.<br /><br />The details of the timing event are:';
          
          $tVar = getParam($entity,'timingVar');
          $tCat = getParam($entity,'timingCategory');
          $tVal = getParam($entity,'timingValue');
          $tLab = getParam($entity,'timingLabel');

          $humanSpeak = $humanSpeak.'<table class="table table-bordered table-striped">';
          if($tVar!==null && $tVar !==''){$humanSpeak = $humanSpeak.'<tr><th>Timing Variable</th><td>'.variablise($tVar,$entity,true).'</td></tr>';}
          if($tCat!==null && $tCat!==''){$humanSpeak = $humanSpeak.'<tr><th>Timing Category</th><td>'.variablise($tCat,$entity,true).'</td></tr>';}
          if($tVal!==null && $tVal!==''){$humanSpeak = $humanSpeak.'<tr><th>Timing Value</th><td>'.variablise($tVal,$entity,true).'</td></tr>';}
          if($tLab!==null && $tLab!==''){$humanSpeak = $humanSpeak.'<tr><th>Timing Label</th><td>'.variablise($tLab,$entity,true).'</td></tr>';}
    
          $humanSpeak = $humanSpeak.'</table></td>';
        break;
        
        case 'TRACK_TRANSACTION':
              $humanSpeak = '<td>This tag will track transactions.<br />Make sure your developers have put the transaction data in the "dataLayer" on the correct transaction (Thank You) page.</td>';
        break;

        case 'TRACK_SOCIAL':
            $humanSpeak = '<td>This tag will track social interactions: Likes, Follows, Shares.<br />These actions normally happen when Social Network buttons are clicked by the user.</td>';
        break;
        
        case 'DECORATE_LINK':
            $humanSpeak = '<td>This tag is used to "decorate" links for use in Cross Domain Tracking.<br />This is technical and something your developers will control.</td>';
        break;
        
        case 'DECORATE_FORM':
            $humanSpeak = 'This tag is used to "decorate" forms for use in Cross Domain Tracking.<br />This is technical and something your developers will control.';
        break;
      }
  
    return $humanSpeak;
  }
?>