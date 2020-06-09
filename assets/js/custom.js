jQuery(function($){
    jQuery('.date_field').datepicker();
    $('input[name="stop_compi"]:checkbox').click(function(){
        var checked = $(this).is(':checked');
        var compid = $(this).attr('data-id');
        var ajax_url = 'http://localhost/finishingapp/wp-admin/admin-ajax.php';
        if(checked) {
            if(confirm('Are you sure you want to resume the Competition?')){  
                $(this).attr("checked", "checked");
                jQuery.ajax({
                    url: ajax_url,
                    data: {
                        action: 'stop_comp',
                        'foobar_id': compid
                    },
                    type: 'POST'
                },function(response) {
                    console.log('The server responded: ', response);
                }); 

                
            }
        } else if(confirm('Are you sure you want to Stop this Competition?')){
            $(this).removeAttr('checked');
            jQuery.ajax({
                url: ajax_url,
                data: {
                    action: 'stop_comp',
                    'foobar_id': compid
                },
                type: 'POST'
            },function(response) {
                console.log('The server responded: ', response);
            }); 
        }
        // var rstop = confirm("Are you sure you want to Stop this Competition?");
        // var rresum = confirm("Are you sure you want to resume the Competition?");
        // if (rstop == true && rresum == true) {
            
        // }
        
    });
    
});

jQuery(document).ready( function($) {
    
});


