<?php
/*******************************************************************************
 * Copyright (c) 2018, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (is_admin()){
    //this hook will create a new filter on the admin area for the specified post type
    
    add_action( 'restrict_manage_posts', function(){
         global $wpdb, $table_prefix;
        if(isset($_GET['post_type']) && $_GET['post_type']=='fishapp-competition'){
         $post_type = 'fishapp-competition';
             ?>
             <div class="custom_filter_new">
                <div class="filed_search">                
                    
                    <input type="text" name="start_date" class="start_date filter-field date_field" value="<?php if(isset($_GET['start_date']))echo $_GET['start_date'];?>" placeholder="Start Data"/>
                </div>
                <div class="filed_search">                
                    <input type="text" name="end_date" class="end_date filter-field date_field" value="<?php if(isset($_GET['end_date']))echo $_GET['end_date'];?>" placeholder="End Data"/>
                </div>
                <div class="filed_search">                
                    <select type="text" name="Comp_status" class="Comp_status filter-field"/>
                        <option value="">Status</option>
                        <option value="completed" <?php if( $_GET['Comp_status'] == 'completed') { echo 'selected="selected"'; } else{echo '';} ?>>Completed</option>
                        <option value="running" <?php if( $_GET['Comp_status'] == 'running') { echo 'selected="selected"'; } else{echo '';} ?>>Running</option>
                        <option value="stop" <?php if( $_GET['Comp_status'] == 'stop') { echo 'selected="selected"'; } else{echo '';} ?>>Stop</option>
                    </select>
                </div>
             </div>
             <?php
        }
     });
 
     //this hook will alter the main query according to the user's selection of the custom filter we created above:
     add_filter( 'parse_query', function($query){
         global $pagenow;
        
        if(isset($_GET['post_type']) && isset($_GET['post_type'])=='fishapp-competition') {
         $post_type = 'fishapp-competition';
         $meta_query_args = array();
         if((isset($_GET['start_date']) && !empty($_GET['start_date'])) || 
         (isset($_GET['end_date']) && !empty($_GET['end_date'])) || 
         (isset($_GET['Comp_status']) && !empty($_GET['Comp_status']))){
            
            $meta_query_args['relation'] = 'AND';
            if((isset($_GET['start_date']) && !empty($_GET['start_date']))) 
            {   
                $meta_query_args[] = array(
                    'key'     => 'competition_start_date',
                    'value' => strtotime($_GET['start_date']),
                    'compare' => '<='
                );
                $meta_query_args[] = array(
                    'key'     => 'competition_end_date',
                    'value' => strtotime($_GET['start_date']),
                    'compare' => '>'
                );
            }
            if((isset($_GET['end_date']) && !empty($_GET['end_date']))) 
            {   
                $meta_query_args[] = array(
                    'key'     => 'competition_end_date',
                    'value' => strtotime($_GET['end_date']),
                    'compare' => '<='
                );
            }
            $valueststuss = 0;
            if(isset($_GET['Comp_status']) && !empty($_GET['Comp_status'])) 
            {   
                if($_GET['Comp_status']=='stop'){
                    $valueststuss = 1;
                }
                $meta_query_args[] = array(
                    'key'     => 'option_compi_end_date',
                    'value' => $valueststuss,
                    'compare' => '='
                );
            }

            $query->query_vars['meta_query'] =  $meta_query_args;
        }
            
        }
        if(isset($_GET['post_type']) && isset($_GET['post_type'])=='fishapp-participants') {
            $meta_query_args = array();
            if(isset($_GET['comp']) && !empty($_GET['comp'])){
                $meta_query_args[] = array(
                    'key'     => 'participant_comp_details',
                    'value' => $_GET['comp'],
                    'compare' => '='
                );
                $query->query_vars['meta_query'] =  $meta_query_args;
            }
        }

        //  if ($post_type == 'fishapp-competition' && $pagenow=='edit.php' && isset($_GET['admin_filter_year']) && !empty($_GET['admin_filter_year'])) {
        //      $query->query_vars['year'] = $_GET['admin_filter_year'];
        //  }
     });
 }


 // Update the columns shown on the custom post type edit.php view - so we also have custom columns
add_filter('manage_fishapp-competition_posts_columns' , 'fishapp_competition_columns');
function fishapp_competition_columns($columns){
    // Remove Author and Comments from Columns and Add custom column 1, custom column 2 and Post Id
    return array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'start_date' => __('Start Data'),
        'end_date' => __('End Data'),
        'participants' => __('Participants'),
        'stop_status' => __('Stop'),
        'action_work' => __('Action'),
        );
                    //return $columns;
}

// this fills in the columns that were created with each individual post's value
add_action( 'manage_fishapp-competition_posts_custom_column' , 'fill_fishapp_competition_columns', 10, 2 );
function fill_fishapp_competition_columns( $column, $post_id ) {
    // Fill in the columns with meta box info associated with each post
    $othere_details = get_post_meta( $post_id ,'competition_othere_settings' , true ); 
    switch ( $column ) {
        case 'start_date' :
            if(isset($othere_details['compi_start_date']) && !empty($othere_details['compi_start_date']))
            //echo $othere_details['compi_start_date'];
            echo date('m/d/Y', get_post_meta( $post_id ,'competition_start_date' , true ));
            break;
        case 'end_date' :
            if(isset($othere_details['compi_end_date']) && !empty($othere_details['compi_end_date']))
            //echo $othere_details['compi_end_date'];
            echo date('m/d/Y', get_post_meta( $post_id ,'competition_end_date' , true ));
                break;
        case 'participants' :
            echo '<a href="'.site_url().'/wp-admin/edit.php?post_type=fishapp-participants&comp='.$post_id.'">View</a>';
            echo '<a href="'.site_url().'/wp-admin/edit.php?page=drawer&comp='.$post_id.'">Draw</a>'; 
                break; 
        case 'stop_status' :
            $checks = '';
            $comp_stop_true = get_post_meta($post_id,'option_compi_end_date',true);
            if($comp_stop_true==1){
                $checks = 'checked="checked"';
            }
            echo '<input type="checkbox" name="stop_compi" data-id="'.$post_id.'" '.$checks.'>';
                break;                
        case 'action_work' :
            echo '<a href="'.get_post_permalink($post_id).'">View</a>';
            echo '<a href="'.get_edit_post_link($post_id,'edit').'">Edit</a>'; 
            break;
    }
}



add_filter('manage_fishapp-participants_posts_columns' , 'fishapp_participants_columns');
function fishapp_participants_columns($columns){
    // Remove Author and Comments from Columns and Add custom column 1, custom column 2 and Post Id
    return array(
        'cb' => '<input type="checkbox" />',
        'image' => __('Image'),
        'participants' => __('Participant'),
        'compi_rel' => __('Competition'),
        'compi_points' => __('Points'),
        'photos'       => __('Photos'),
        'videos'       => __('Videos'),
        'action_work' => __('Action'),
        );
                    //return $columns;
}

// this fills in the columns that were created with each individual post's value
add_action( 'manage_fishapp-participants_posts_custom_column' , 'fill_fishapp_participants_columns', 10, 2 );
function fill_fishapp_participants_columns( $column, $post_id ) {
    // Fill in the columns with meta box info associated with each post
    
    switch ( $column ) {
        case 'image' :
            echo '<img src="'.get_avatar_url($post_id).'" width="50px"/>';
            break;
        case 'participants' :
            $author_id = get_post_field( 'post_author', $post_id );
            $author_name = get_the_author_meta('user_nicename', $author_id);
            echo $author_name;
                break;
        case 'compi_rel' :
            $compi_id = get_post_meta($post_id,'participant_comp_details',true);
            
            echo '<a href="'.site_url().'/wp-admin/post.php?post='.$compi_id.'&action=edit">'.get_the_title($compi_id).'</a>';
                break; 
        case 'compi_points' :
            echo 'NaN';
                break; 
        case 'photos' :
            echo count(get_post_meta($post_id,'participant_upload_image',true));
                break; 
        case 'videos' :
            echo count(get_post_meta($post_id,'participant_upload_image',true));
                break;                
        case 'action_work' :
            echo '<a href="'.get_post_permalink($post_id).'">View</a>';
            echo '<a href="'.get_edit_post_link($post_id,'edit').'">Edit</a>'; 
            break;
    }
}


function ibenic_add_media_custom_field( $form_fields, $post ) {
    $field_value_latitude = get_post_meta( $post->ID, 'latitude', true );
    $field_value_longitude = get_post_meta( $post->ID, 'longitude', true ); 
    $field_value_approve_status = get_post_meta($post->ID, "approve_status", true);

    $form_approve_html = '<select name="approve_status" class="approve_status">';
    if($field_value_approve_status === "inreview") {
        $form_approve_html .= '<option value="inreview" selected>In Review</option>';
    } else {
        $form_approve_html .= '<option value="inreview">In Review</option>';
    }
    if($field_value_approve_status === "reject") {
        $form_approve_html .= '<option  value="reject" selected>Reject</option>';
    } else {
        $form_approve_html .= '<option value="reject">Reject</option>';
    }

    if($field_value_approve_status === "approved") {
        $form_approve_html .= '<option  value="approved" selected>Approved</option>';
    } else {
        $form_approve_html .= '<option value="approved">Approved</option>';
    }
    $form_approve_html .= '</select>';


    $form_fields['latitude'] = array(
        'value' => $field_value_latitude ? $field_value_latitude : '',
        'label' => __( 'latitude ' ),
        'helps' => __( 'Enter Latitude' ),
        'input'  => 'text'
    );
    $form_fields['longitude'] = array(
        'value' => $field_value_longitude ? $field_value_longitude : '',
        'label' => __( 'longitude ' ),
        'helps' => __( 'Enter Longitude' ),
        'input'  => 'text'
    );
    $form_fields['approve_status'] = array(
        'label' => __( 'Approve Status'),
        'helps' => __( 'Approve Status' ),
        'input'  => 'html',
        'html'  => $form_approve_html
    );

    

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'ibenic_add_media_custom_field', null, 2 );

function ibenic_save_attachment( $attachment_id ) {
    if ( isset( $_REQUEST['attachments'][ $attachment_id ]['latitude'] ) ) {
        $latitude_val = $_REQUEST['attachments'][ $attachment_id ]['latitude'];
        update_post_meta( $attachment_id, 'latitude', $latitude_val );
    }
    if ( isset( $_REQUEST['attachments'][ $attachment_id ]['longitude'] ) ) {
        $longitude_val = $_REQUEST['attachments'][ $attachment_id ]['longitude'];
        update_post_meta( $attachment_id, 'longitude', $longitude_val );
    }
    if ( isset( $_REQUEST['approve_status'] ) ) {
        $approve_status = $_REQUEST['approve_status'];
        update_post_meta( $attachment_id, 'approve_status', $approve_status );
    }
}
add_action( 'edit_attachment', 'ibenic_save_attachment' );







function wpse28782_remove_menu_items() {
    //if( !current_user_can( 'administrator' ) ):
        remove_menu_page( 'edit.php?post_type=fishapp-participants' );
   // endif;
}
add_action( 'admin_menu', 'wpse28782_remove_menu_items' );




add_action( 'wp_ajax_stop_comp', '_ajax_handler_for_stop_funtion' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_stop_comp', '_ajax_handler_for_stop_funtion' );
function _ajax_handler_for_stop_funtion() {
    if(isset($_POST['comp_id']) && isset($_POST['check'])) {
        update_post_meta($_POST['comp_id'],'option_compi_end_date',$_POST['check']);
    }
    wp_die();
}

add_action( 'wp_ajax_video_file_participants_action', 'video_file_participants_action' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_video_file_participants_action', 'video_file_participants_action' );
function video_file_participants_action() {
    $attachment_id = $_POST['vid'];

    if ( isset($_POST['data'][0]['value'] ) ) {
        $latitude_val = $_POST['data'][0]['value'];
        update_post_meta( $attachment_id, 'latitude', $latitude_val );
    }
    if ( isset($_POST['data'][1]['value'] ) ) {
        $longitude_val = $_POST['data'][1]['value'];
        update_post_meta( $attachment_id, 'longitude', $longitude_val );
    }
    if ( isset($_POST['data'][2]['value'] ) ) {
        $approve_status = $_POST['data'][2]['value'];
        update_post_meta( $attachment_id, 'approve_status', $approve_status );
    }
    wp_die();
}






add_action('admin_footer', 'modal_wrapper');
function modal_wrapper()
{
    $HTML = '';
    $HTML .= '<div class="modal-wrapper">';
        $HTML .= '<div class="modal">';
            $HTML .= '<span class="close-modal del_icon">X</span>';
            $HTML .= '<div id="modal-content">';
                $HTML .= '<div class="video_section" >';
                $HTML .= '<span class="video_section_load"></span>';
                $HTML .= '<form name="approval_setting" class="approval_setting" data_attavhvid="">';
                $HTML .= '<input type="text" name="latitude" class="latitude" placeholder="Latitude"/>';
                $HTML .= '<input type="text" name="longitude" class="longitude"  placeholder="Longitude"/>';
                $HTML .= '<select name="approve_status" class="approve_status"><option value="inreview">In Review</option><option value="reject">Reject</option><option value="approved">Approved</option></select>';
                $HTML .= '<a class="approval_setting_submit">Save</a>';
                $HTML .= '</form>';
                $HTML .= '</div>';
            $HTML .= '</div>';
        $HTML .= '</div>';
    $HTML .= '</div>';

    echo $HTML;

}