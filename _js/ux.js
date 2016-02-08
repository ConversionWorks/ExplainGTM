/********************************************************************************
 * UX
*********************************************************************************/
var clickHandlersSet = false;
if(!clickHandlersSet){
    jQuery("#tags" ).on( "click", ".typeLink", function() {
        jQuery(this).next().toggle();
    });

    jQuery("#tags" ).on( "click", ".tagDetail", function() {
        jQuery(this).next().toggle();
    });

    jQuery("#tags" ).on( "click", ".tagGnarlyDetail", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#tags" ).on( "click", ".tagFieldsToSet", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#tags" ).on( "click", ".tagCDs", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#tags" ).on( "click", ".tagCMs", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#tags" ).on( "click", ".tagCVs", function() {
        jQuery(this).next().toggle();
    });

    jQuery("#trigs").on("click", ".trigTypeLink", function(){
        jQuery(this).next().toggle();
    });

    jQuery("#trigs" ).on( "click", ".trigDetail", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#folders" ).on( "click", ".folderDetail", function() {
        jQuery(this).next().toggle();
    });

    jQuery("#udvs" ).on( "click", ".udvTypeLink", function() {
        jQuery(this).next().toggle();
    });

    jQuery("#udvs" ).on( "click", ".udvDetail", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#advs" ).on( "click", ".typeLink", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#priorityLink" ).click(function() {
        jQuery('#priorities').toggle();
    });
    
    jQuery("#tags" ).on( "click", ".pLink", function() {
        jQuery(this).next().toggle();
    });
    
    jQuery("#folders" ).on( "click", ".Detail", function() {
        jQuery('#folder' + jQuery(this).attr('data-asset')).next().toggle();
        jQuery('#folders').toggle();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 's').show();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 'Type' + jQuery(this).attr('data-type')).next().show();
        jQuery('#' + jQuery(this).attr('data-asset') + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#advs" ).on( "click", ".Detail", function() {
        jQuery('#folder' + jQuery(this).attr('data-asset')).next().toggle();
        jQuery('#folders').toggle();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 's').show();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 'Type' + jQuery(this).attr('data-type')).next().show();
        jQuery('#' + jQuery(this).attr('data-asset') + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#advs" ).on( "click", ".advDetail", function() {
        jQuery('#adv' + jQuery(this).attr('data-asset')).next().toggle();
        jQuery('#advs').toggle();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 's').show();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 'Type' + jQuery(this).attr('data-type')).next().show();
        jQuery('#' + jQuery(this).attr('data-asset') + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#udvs" ).on( "click", ".usageDetail", function() {
        jQuery('#udv' + jQuery(this).attr('data-udv')).next().toggle();
        jQuery('#udvs').toggle();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 's').show();
        
        jQuery('#' + jQuery(this).attr('data-asset') + 'Type' + jQuery(this).attr('data-type')).next().show();
        jQuery('#' + jQuery(this).attr('data-asset') + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#priorities" ).on( "click", ".tagDetail", function() {
        jQuery('#p' + jQuery(this).attr('data-p')).toggle();
        jQuery('#priorities').toggle();
        
        jQuery('#tags').show();
        
        jQuery('#tagType' + jQuery(this).attr('data-type')).next().show();
        jQuery('#tag' + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#tags" ).on( "click", ".Detail", function() {
        jQuery('#tagDetail' + jQuery(this).attr('data-parent')).toggle();
        
        jQuery('#tagType' + jQuery(this).attr('data-type')).next().show();
        jQuery('#tag' + jQuery(this).attr('data-index')).next().show();
    });
    
    jQuery("#trigs" ).on( "click", ".Detail", function() {
        jQuery('#trigs').toggle();
        jQuery('#tags').toggle();
        
        console.log(jQuery(this).attr('data-type'));
        console.log(jQuery(this).attr('data-index'));
                
        jQuery('#tagType' + jQuery(this).attr('data-type')).next().show();
        jQuery('#tag' + jQuery(this).attr('data-index')).next().show();
    });
    
    clickHandlersSet = true;
}

jQuery('#back').click(function(){
    jQuery('#containerNav').show();
    jQuery('#processing').val('');
});

jQuery('#tagLink').click(function(){
    jQuery('#tags').toggle();
});

jQuery('#trigLink').click(function(){
    jQuery('#trigs').toggle();
});

jQuery('#folderLink').click(function(){
    jQuery('#folders').toggle();
});

jQuery('#bivLink').click(function(){
    jQuery('#bivs').toggle();
});

jQuery('#udvLink').click(function(){
    jQuery('#udvs').toggle();
});

jQuery('#advLink').click(function(){
    jQuery('#advs').toggle();
});

jQuery('#containerNotesLink').click(function(){
  jQuery('#containerNotes').toggle();
});

jQuery('.contractall').click(function(){
    jQuery('#tags').hide();
    jQuery('#trigs').hide();
    jQuery('#folders').hide();
    jQuery('#bivs').hide();
    jQuery('#udvs').hide();
    jQuery('#advs').hide();
    jQuery('.assetList').hide();
    jQuery('.assetDetail').hide();
    jQuery('.tagGnarlyDetail').next().hide();
    jQuery('#containerNotes').hide();
});

jQuery('.expandall').click(function(){
    jQuery('#tags').show();
    jQuery('#trigs').show();
    jQuery('#folders').show();
    jQuery('.folderDetail').show();
    jQuery('#bivs').show();
    jQuery('#udvs').show();
    jQuery('#advs').show();
    jQuery('.assetList').show();
    jQuery('.assetDetail').show();
    jQuery('.tagGnarlyDetail').next().show();
    jQuery('#containerNotes').show();
});

jQuery('.searchLink').click(function(){
    jQuery('#searchContainer').toggle();
});

jQuery('#searchClauseRegex').click(function(){
    jQuery('#searchPhrase').val('/(.*)/');
});

jQuery('#searchClauseContain').click(function(){
    jQuery('#searchPhrase').val('');
});

jQuery('#searchClauseExact').click(function(){
    jQuery('#searchPhrase').val('');
});

jQuery(function() {
    var form = $('#searchForm');
    var searchResults = $('#searchResults');

    jQuery(form).submit(function(event) {
      event.preventDefault();
      jQuery(searchResults).html('');
      var formData = jQuery(form).serialize();
      
      jQuery.ajax({
        type: 'POST',
        url: jQuery(form).attr('action'),
        data: formData
      })
      .done(function(response) {
        response = JSON.parse(response);
        jQuery(searchResults).html(response['searchResultsMarkup']);
        jQuery(searchResults).show();
        window.dataLayer.push({
          'pageVirtual':'/gtm2en/php/gtm2en.php?q='+jQuery('#searchPhrase').val(),
          'searchResultCount':response['searchResultCount'],
          'event':'vpv'
        });
      })
      .fail(function(data) {
        if (data.responseText !== '') {
          jQuery(searchResults).text(':FAIL'+data.responseText);
        } else {
          jQuery(searchResults).text('Oops!');
        }
      });
    });
});