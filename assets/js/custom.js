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

    $('.approval_setting_submit').click(function(){
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
                 $('.gallery_image').append('<span data-id="'+attachment.id+'"><span class="del_icon" delete-id="'+attachment.id+'">x</span><img view-id="'+attachment.id+'" class="true_pre_image" src="' + attachment['attributes'].url + '" style="max-width:100px;display:block;" /></span>').next().val(attachment.id).next().show();
				
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
    
    jQuery('.gallery_image img.true_pre_image').click(function(e) {

        e.preventDefault();
        var selected = jQuery(this).attr('view-id');
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

                //   image_frame.on('close',function() {
                //      // On close, get selections and save to the hidden input
                //      // plus other AJAX stuff to refresh the image preview
                //      var selection =  image_frame.state().get('selection');
                //      var gallery_ids = new Array();
                //      var my_index = 0;
                //      selection.each(function(attachment) {
                //         gallery_ids[my_index] = attachment['id'];
                //         my_index++;
                //      });
                //      var ids = gallery_ids.join(",");
                //      jQuery('#participant_upload_image').val(ids);
                //      Refresh_Image(ids);
                //   });

                 image_frame.on('open',function() {
                   // On open, get the id from the hidden input
                   // and select the appropiate images in the media manager
                //    var selection =  image_frame.state().get('selection');
                //    var ids = jQuery('#participant_upload_image').val().split(',');
                //    ids.forEach(function(id) {
                //      var attachment = wp.media.attachment(id);
                //      attachment.fetch();
                //      selection.add( attachment ? [ attachment ] : [] );
                //    });

                var selection =  image_frame.state().get('selection');
                var id = selected;
                
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                

                 });

               image_frame.open();
    });


});




jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */
	$('body').on('click', '.upload_video_button', function(e){
		e.preventDefault();
    		var button = $(this),
    		    custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				// uncomment the next line if you want to attach image to the current post
				// uploadedTo : wp.media.view.settings.post.id, 
				type : 'video'
			},
			button: {
				text: 'Use this video' // button label text
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
                 $('.gallery_Videos').append('<span data-id="'+attachment.id+'"><span class="del_icon" delete-id="'+attachment.id+'">x</span><img view-id="'+attachment.id+'" class="true_pre_image" src="' + attachment['attributes'].url + '" style="max-width:100px;display:block;" /></span>').next().val(attachment.id).next().show();
				
				i++;
            });
            var exitval = $('#participant_upload_videos').val();
            if(exitval!=''){
                exitval = exitval+','+attachment_ids;
            } else {
                exitval = attachment_ids;
            }
            
            $('#participant_upload_videos').val(exitval);
            console.log( attachment_ids );
			
		})
		.open();
	});
    
    $('body').on('click', '.del_icon', function(){
        var deleteid = $(this).attr('delete-id');
        $(this).parent('span').remove();
        $('#participant_upload_videos').val($('#participant_upload_videos').val().replace(deleteid,''));
    });


	/*
	 * Remove image event
	 */
	$('body').on('click', '.remove_image_button', function(){
		$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
		return false;
    });
    
    


});

jQuery(document).ready(function($) {
    // Close modal 
    $('.close-modal').click(function() {
      $('.modal').toggleClass('show');
    });
  
    // Detect windows width function
    var $window = $(window);
  
    function checkWidth() {
      var windowsize = $window.width();
      if (windowsize > 767) {
        // if the window is greater than 767px wide then do below. we don't want the modal to show on mobile devices and instead the link will be followed.
  
        $(".gallery_Videos .true_pre_image").click(function(e) {
            var video_url = $(this).attr('video-url');
          var modalContent = $("#modal-content");
          var latlong_aproveform = '<form name="approval_setting" class="approval_setting">';
            latlong_aproveform += '<input type="text" name="latitude" class="latitude" placeholder="Latitude"/>';
            latlong_aproveform += '<input type="text" name="longitude" class="longitude"  placeholder="Longitude"/>';
            latlong_aproveform += '<select name="approve_status" class="approve_status"><option value="inreview">In Review</option><option value="reject">Reject</option><option value="approved">Approved</option></select>';
            latlong_aproveform += '<span class="approval_setting_submit">Save</span>';
            latlong_aproveform += '</form>';
          var videoget = '<div class="video_section" ><video width="60%" height="340" controls> <source src="'+video_url+'" type="video/mp4"> <source src="'+video_url+'" type="video/ogg"> Your browser does not support the video tag. </video>';
          videoget += latlong_aproveform;
          videoget += '</div>';
         // var post_link = $('#modal-content').html(); // get content to show in modal
          //var post_link = $(this).attr("href"); // this can be used in WordPress and it will pull the content of the page in the href
          
          e.preventDefault(); // prevent link from being followed
          
          $('.modal').addClass('show', 500, "easeOutSine"); // show class to display the previously hidden modal
          modalContent.html("loading..."); // display loading animation or in this case static content
          modalContent.html(videoget); // for dynamic content, change this to use the load() function instead of html() -- like this: modalContent.load(post_link + ' #modal-ready')
          $("html, body").animate({ // if you're below the fold this will animate and scroll to the modal
            scrollTop: 0
          }, "slow");
          return false;
        });
      }
    };
  
    checkWidth(); // excute function to check width on load
    $(window).resize(checkWidth); // execute function to check width on resize
  });