jQuery(function($){
    jQuery('.date_field').datepicker();
    $('input[name="stop_compi"]').click(function(){
        var checked = $(this).is(':checked');
        var compid = $(this).attr('data-id');
        var ajax_url = 'http://localhost/finishingapp/wp-admin/admin-ajax.php';
        console.log(checked);
        if(checked) {
            if(confirm('Are you sure you want to Stop this Competition?')){
            $(this).attr("checked", "checked");    
                jQuery.ajax({
                    url: ajax_url,
                    data: {
                        action: 'stop_comp',
                        'comp_id': compid,
                        'check': 1
                    },
                    type: 'POST'
                },function(response) {
                    console.log('The server responded: ', response);
                }); 
            } else { return false; }   
                
            
        } else {
            if(confirm('Are you sure you want to resume the Competition?')){
                $(this).removeAttr('checked');
                jQuery.ajax({
                    url: ajax_url,
                    data: {
                        action: 'stop_comp',
                        'comp_id': compid,
                        'check': 0
                    },
                    type: 'POST'
                },function(response) {
                    console.log('The server responded: ', response);
                }); 
            } else {return false;}
        }
        
        
       
    
    });
    
});

jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */
	$('body').on('click', '.upload_image_button', function(e){
		e.preventDefault();
        console.log('sdsd');
    		var button = $(this),
    		    custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uncomment the next line if you want to attach image to the current post
				// uploadedTo : wp.media.view.settings.post.id, 
				type : 'image'
			},
			button: {
				text: 'Use this image' // button label text
			},
			multiple: true // for multiple image selection set to true
		}).on('select', function() { // it also has "open" and "close" events 
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			// $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
			/* if you sen multiple to true, here is some code for getting the image IDs*/
			var attachments = custom_uploader.state().get('selection'),
			    attachment_ids = new Array(),
			    i = 0;
			attachments.each(function(attachment) {
                 attachment_ids[i] = attachment['id'];
                 $('.gallery_image').append('<span data-id="'+attachment.id+'"><span class="del_icon" delete-id="'+attachment.id+'">x</span><img class="true_pre_image" src="' + attachment['attributes'].url + '" style="max-width:100px;display:block;" /></span>').next().val(attachment.id).next().show();
				
				i++;
            });
            var exitval = $('#participant_upload_image').val();
            if(exitval!=''){
                exitval = exitval+','+attachment_ids;
            } else {
                exitval = attachment_ids;
            }
            
            $('#participant_upload_image').val(exitval);
            console.log( attachment_ids );
			
		})
		.open();
	});
    
    $('body').on('click', '.del_icon', function(){
        var deleteid = $(this).attr('delete-id');
        $(this).parent('span').remove();
        $('#participant_upload_image').val($('#participant_upload_image').val().replace(deleteid,''));
    });


	/*
	 * Remove image event
	 */
	$('body').on('click', '.remove_image_button', function(){
		$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
		return false;
	});
 
});


