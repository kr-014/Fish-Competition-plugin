jQuery(function($){


     $('.select2element').select2();

    $('.overlay_color').wpColorPicker();
    jQuery('.overlay_opacity').change(function(){
        var opct = jQuery(this).val()/10;
        jQuery('.opect-show').html(opct);
        
    });

     $('.popopbg_color').wpColorPicker();
    jQuery('.popupbg_opacity').change(function(){
        var opctbg = jQuery(this).val()/10;
        jQuery('.opectbg-show').html(opctbg);
        
    });
    $('.popopbg_mode').select2();






    $('.display_rules_selected').change(function(){
        var iddd = $(this).parent().closest('.popuppro-display_rules_continer').attr('id');

        // console.log(iddd);
        $('#'+iddd+' .display_rules_isnot_part3').hide();
        $('#'+iddd+' .shortcode-section').hide();
        $('#'+iddd+' .post-selected-part').hide();
        $('#'+iddd+' .post-type-part').hide();
        $('#'+iddd+' .page-selected-part').hide();
        $('#'+iddd+' .post-cat-part').hide();
        $('#'+iddd+' .page-template-part').hide();
        
        if($(this).val() ==''){
            $('#'+iddd+' .display_rules_isnot_part2').hide();
        } else {
            $('#'+iddd+' .display_rules_isnot_part2').show();
        }
        if($(this).val() !=''){
            var getpopupid = $('.getpopupid').val();
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl,
                data : {action: "select_rule_display", selected : $(this).val(), popid:getpopupid},
                success: function(response) {
                    if(response.isnot =='hide'){
                        $('#'+iddd+'.display_rules_isnot_part2').hide();
                    }
                    if(response.selected =='by_shortcode'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .shortcode-section').show();
                    } 
                    if(response.selected =='post_selected'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .post-selected-part').show();
                    } 
                    if(response.selected =='page_selected'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .page-selected-part').show();
                    } 
                    if(response.selected =='post_type'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .post-type-part').show();
                    } 
                    if(response.selected =='post_category'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .post-cat-part').show();
                    } 
                    if(response.selected =='page_template'){
                        $('#'+iddd+' .display_rules_isnot_part3').show();
                        $('#'+iddd+' .page-template-part').show();
                    } 

                    
                    
                    
                    
                    
                }, 
            });
        }
    });
    $('.display_rules_selected').select2();
    $('.get_postcat').select2();
    $('.get_posttype').select2();
    $('.display_rules_isnot').select2();
    $('.get_allpost').select2({
        ajax: {
                url: ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, // search query
                        action: 'get_allpost' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function( data ) {
                var options = [];
                if ( data ) {
                    // data is the array of arrays, and each of them contains ID and the Label of the option
                    $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                        options.push( { id: text[0], text: text[1]  } );
                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: 3 // the minimum of symbols to input before perform a search
    });


    $('.get_allpage').select2({
        ajax: {
                url: ajaxurl, // AJAX URL is predefined in WordPress admin
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    return {
                        q: params.term, // search query
                        action: 'get_allpage' // AJAX action for admin-ajax.php
                    };
                },
                processResults: function( data ) {
                var options = [];
                if ( data ) {
                    // data is the array of arrays, and each of them contains ID and the Label of the option
                    $.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                        options.push( { id: text[0], text: text[1]  } );
                    });
                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: 3 // the minimum of symbols to input before perform a search
    });


    $('.add-popuprule').click(function(){
        var rowcclick= $(this).attr('row-rules');
        var getpopupid = $('.getpopupid').val();    

        $.ajax({
            type : "post",
            dataType : "json",
            url : ajaxurl,
            data : {action: "get_more_rule", row : rowcclick, popid:getpopupid},
            success: function(response) {

                $( "#display-rules-"+rowcclick ).after( response );
                console.log(response);
                
            }, 
        });
    });






$('.display_action_by').select2();
$('.action_triger_selected').select2();
$('.action_triger_selected').change(function(){
        var iddd = $(this).parent().closest('.popuppro-display_action_continer').attr('id');

        // console.log(iddd);
        $('#'+iddd+' .display_action_basic_isnot_part2').hide();
        $('#'+iddd+' .action-action_by_part').hide();
        $('#'+iddd+' .action-by_classid_part').hide();
        $('#'+iddd+' .action-delay_part').hide();
        
        if($(this).val() ==''){
            $('#'+iddd+' .display_action_basic_isnot_part2').hide();
        } else {
            $('#'+iddd+' .display_action_basic_isnot_part2').show();
        }
        if($(this).val() !=''){
            var getpopupid = $('.getpopupid').val();
            $.ajax({
                type : "post",
                dataType : "json",
                url : ajaxurl,
                data : {action: "select_rule_display", selected : $(this).val(), popid:getpopupid},
                success: function(response) {
                    
                    if(response.selected =='load'){
                        $('#'+iddd+' .display_action_basic_isnot_part3').show();
                        $('#'+iddd+' .action-delay_part').show();
                    } 
                    if(response.selected =='onclick'){
                        $('#'+iddd+' .display_action_basic_isnot_part3').show();
                        $('#'+iddd+' .action-action_by_part').show();
                        $('#'+iddd+' .action-by_classid_part').show();
                    } 
                    if(response.selected =='onhover'){
                        $('#'+iddd+' .display_action_basic_isnot_part3').show();
                        $('#'+iddd+' .action-action_by_part').show();
                        $('#'+iddd+' .action-by_classid_part').show();
                    } 
                }, 
            });
        }
    });

    $('.add-popupaction').click(function(){
        var rowcclickac= $(this).attr('row-actions');
        var getpopupid = $('.getpopupid').val();    

        $.ajax({
            type : "post",
            dataType : "json",
            url : ajaxurl,
            data : {action: "get_more_action", row : rowcclickac, popid:getpopupid},
            success: function(response) {
                $( "#display-action-"+rowcclickac ).after( response );                
            }, 
        });
    });
});

jQuery(document).ready( function($) {
        jQuery('input#popbgimage_media_manager').click(function(e) {

             e.preventDefault();
             var image_frame;
             if(image_frame){
                 image_frame.open();
             }
             // Define image_frame as wp.media object
             image_frame = wp.media({
                           title: 'Select Media',
                           multiple : false,
                           library : {
                                type : 'image',
                            }
                       });

                       image_frame.on('close',function() {
                          // On close, get selections and save to the hidden input
                          // plus other AJAX stuff to refresh the image preview
                          var selection =  image_frame.state().get('selection');
                          var gallery_ids = new Array();
                          var my_index = 0;
                          selection.each(function(attachment) {
                             gallery_ids[my_index] = attachment['id'];
                             my_index++;
                          });
                          var ids = gallery_ids.join(",");
                          jQuery('input#popupbg_image').val(ids);
                          Refresh_Image(ids,'popupbg_image');
                       });

                      image_frame.on('open',function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        var selection =  image_frame.state().get('selection');
                        var ids = jQuery('input#popupbg_image').val().split(',');
                        ids.forEach(function(id) {
                          var attachment = wp.media.attachment(id);
                          attachment.fetch();
                          selection.add( attachment ? [ attachment ] : [] );
                        });

                      });

                    image_frame.open();
        });
    });

jQuery(document).ready( function($) {
        jQuery('input#popclose_icon_media_manager').click(function(e) {

             e.preventDefault();
             var image_frame;
             if(image_frame){
                 image_frame.open();
             }
             // Define image_frame as wp.media object
             image_frame = wp.media({
                           title: 'Select Media',
                           multiple : false,
                           library : {
                                type : 'image',
                            }
                       });

                       image_frame.on('close',function() {
                          // On close, get selections and save to the hidden input
                          // plus other AJAX stuff to refresh the image preview
                          var selection =  image_frame.state().get('selection');
                          var gallery_ids = new Array();
                          var my_index = 0;
                          selection.each(function(attachment) {
                             gallery_ids[my_index] = attachment['id'];
                             my_index++;
                          });
                          var ids = gallery_ids.join(",");
                          jQuery('input#popup_close_icon').val(ids);
                          var cid = 'popup_close_icon';
                          Refresh_Image(ids,cid);
                       });

                      image_frame.on('open',function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        var selection =  image_frame.state().get('selection');
                        var ids = jQuery('input#popup_close_icon').val().split(',');
                        ids.forEach(function(id) {
                          var attachment = wp.media.attachment(id);
                          attachment.fetch();
                          selection.add( attachment ? [ attachment ] : [] );
                        });

                      });

                    image_frame.open();
        });
    });

        // Ajax request to refresh the image preview
        function Refresh_Image(the_id,cid){
                var data = {
                    action: 'myprefix_get_image',
                    id: the_id
                };

                jQuery.get(ajaxurl, data, function(response) {

                    if(response.success === true) {
                        jQuery('#'+cid).replaceWith( response.data.image );
                    }
                });
        }


