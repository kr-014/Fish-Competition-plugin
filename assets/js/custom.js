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

jQuery(document).ready( function($) {
    
});


