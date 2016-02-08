<?php
  session_start();
  error_reporting(E_ALL);
  ini_set('display_errors',1);
?>
<?php require 'machinetranslations.php'; ?>
<?php require 'humantranslations.php'; ?>
<?php require 'gtmProcess.php'; ?>
<?php require 'containerdeets.php'; ?>
<?php require 'tags.php'; ?>
<?php require 'triggers.php'; ?>
<?php require 'folders.php'; ?>
<?php require 'bivs.php'; ?>
<?php require 'udvs.php'; ?>
<?php require 'advs.php'; ?>

<html>
	<head>
		<title>GTM2en</title>		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<link rel="stylesheet" href="../_css/gtm2en.css" />
		<link href="../bs/css/bootstrap.min.css" rel="stylesheet">
		<style>
      body {padding-top: 50px;}
    </style>
		
		<!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KD7H66');</script>
<!-- End Google Tag Manager -->
	</head>
	<body>
	  <div class="jumbotron">
  		<div id="containerNav" class="container">	
    	  <?php
    	    if(array_key_exists('contRef',$_POST)){
    	      $contRef = $_POST['contRef'];
    	    }
    	    if(array_key_exists('container',$_POST) && $_POST['container']!==null){
            $container = json_decode($_POST['container'], true);
            
            if($container!==null){
              if(array_key_exists('variable',$container['containerVersion'])){
                $udvLibrary = buildLibrary($container['containerVersion']['variable'],$udvTypes);
                $_SESSION["udvLibrary"] = $udvLibrary;
                $_SESSION["udvTypes"] = $udvTypes;
              }
              
              if(array_key_exists('trigger',$container['containerVersion'])){
                $trigLibrary = buildLibrary($container['containerVersion']['trigger'],$trigTypes);
                $_SESSION["trigLibrary"] = $trigLibrary;
                $_SESSION["trigTypes"] = $trigTypes;
              }
              
              if(array_key_exists('tag',$container['containerVersion'])){
                $tagLibrary = buildLibrary($container['containerVersion']['tag'],$tagTypes);
                $_SESSION["tagLibrary"] = $tagLibrary;
                $_SESSION["tagTypes"] = $tagTypes;
              }
              
              if(array_key_exists('enabledBuiltInVariable',$container['containerVersion']['container'])){
                $bivArray = $container['containerVersion']['container']['enabledBuiltInVariable'];
                $_SESSION["bivArray"] = $bivArray;
              }
              if(array_key_exists('folder',$container['containerVersion'])){
                $folderArray = buildFolderArray($container['containerVersion']['folder']);
                $_SESSION["folderArray"] = $folderArray;
              }
              
              echo containerdeets($container);
        ?>
              <script>
                window.dataLayer.push({
                'containerId':'<?php echo $container['containerVersion']['container']['publicId']?>',
                'containerVersion':'<?php echo $container['containerVersion']['containerVersionId']==='0'?'Draft':$container['containerVersion']['containerVersionId']?>',
                'event':'processContainer'
              });
              </script>
              
              <a href="#searchLink" id="searchLink" class="searchLink">Search</a><br />
              <div id="searchContainer" style="display:none;">
                <form id="searchForm" method="POST" action="search.php">
                  <div class="control-label">
                    <label>I need to find assets in this container:</label>
                  </div>
                  <div class="col-sm-4" style="padding-left:20px">
                    <label class="radio">
                      <input type="radio" name="searchClause" id="searchClauseContain" value="contain" checked>
                      that contain
                    </label>
                    <label class="radio">
                      <input type="radio" name="searchClause" id="searchClauseExact" value="exact">
                      exactly matching
                    </label>
                    <label class="radio">
                      <input type="radio" name="searchClause" id="searchClauseRegex" value="regex">
                      matching the regular expression (Advanced)
                    </label>
                  </div>
                  <div class="form-group">
                    <input type="text" class="form-control input-sm" name ="searchPhrase" id="searchPhrase" value="">
                  </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-default" style="margin-top:10px" value="Search"> - <a href="javascript:jQuery(searchResults).html('');jQuery('#searchPhrase').val('');jQuery('#searchContainer').toggle();">Clear</a> 
                  </div>
                </form>
                
                <div id="searchResults" class="container" style="min-height:20px"></div>
              </div>
              
              <div>
                <input type="submit" value="Expand everything" class="expandall btn btn-default" id="expandalltop" style="margin:10px"/>&nbsp;&nbsp;&nbsp;
          			<input type="submit" value="Shrink everything" class="contractall btn btn-default" id="contractalltop" style="margin:10px"/><br />
        			</div>
        			
        			<?php
        			  if(array_key_exists('tag',$container['containerVersion'])){
        			    tagging();
        			  }
        			 ?>
        			
        			<?php 
        			  if(array_key_exists('trigger',$container['containerVersion'])){
        			    triggering(); 
        			  }
        			?>
        			
        			<?php
        			  if(array_key_exists('folder',$container['containerVersion'])){
        			    foldering();
        			  }
        			 ?>
        			
        			<?php
        			  if(array_key_exists('enabledBuiltInVariable',$container['containerVersion']['container'])){
        			    biving();
        			  }
        			 ?>
        			
        			<?php
        			  if(array_key_exists('variable',$container['containerVersion'])){
        			    udving(); 
        			  }
        			 ?>
        			
        			<?php advs(); ?>
        			
        			<div>
          			<input type="submit" value="Expand everything" class="expandall btn btn-default" id="expandallbottom" style="margin:10px"/>&nbsp;&nbsp;&nbsp;
          			<input type="submit" value="Shrink everything" class="contractall btn btn-default" id="contractallbottom" style="margin:10px"/>
          	  </div>
        	<?php
        	    $assetRe = "/\/containers\/\d+\/(tags|triggers|variables)\/(\d+)/";
        	    $assetArray =[];
        	    $autoAsset = [];
        	    preg_match_all($assetRe, $contRef, $assetArray);
        	    if(!empty($assetArray[1])){
          	    switch($assetArray[1][0]){
                  case 'tags':
                      $autoAsset = findTagById($assetArray[2][0]);
                      echo "<script>";
                      echo "jQuery('#tags').show();";
                      echo "jQuery('#tagType".$autoAsset['type']."').next().show();";
                      echo "jQuery('#tag".$autoAsset['tagId']."').next().show();";
                      echo "document.location.href = '#tag".$autoAsset['tagId']."';";
                      echo "</script>";
                  break;
                  
                  case 'triggers':
                      $autoAsset = findTrigById($assetArray[2][0]);
                      echo "<script>";
                      echo "jQuery('#trigs').show();";
                      echo "jQuery('#trigType".$autoAsset['type']."').next().show();";
                      echo "jQuery('#trig".$autoAsset['triggerId']."').next().show();";
                      echo "document.location.href = '#trig".$autoAsset['triggerId']."';";
                      echo "</script>";
                  break;
                  
                  case 'variables':
                      $autoAsset = findUdvById($assetArray[2][0]);
                      echo "<script>";
                      echo "jQuery('#udvs').show();";
                      echo "jQuery('#udvType".$autoAsset['type']."').next().show();";
                      echo "jQuery('#udv".$autoAsset['variableId']."').next().show();";
                      echo "document.location.href = '#udv".$autoAsset['variableId']."';";
                      echo "</script>";
                  break;
          	    }
        	    }
            }else{
              echo 'Oops - something went screwy there. Hit "back" and have another go...';
            }
    	    }else{ ?>
            <script>
              document.location.href = 'http://www.conversionworks.co.uk/gtm2en/gtm2en.html';
            </script> 
    	   <?php }?>
      </div>
    </div>
	  <script src="../bs/js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="../_js/ux.js<?php echo '?v=' . filemtime( '../_js/ux.js' ); ?>"></script>
	</body>
</html>