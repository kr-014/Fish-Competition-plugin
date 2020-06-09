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
        
        if(isset($_GET['post_type']) && isset($_GET['post_type'])=='fishapp-competition')
         $post_type = 'fishapp-competition';
  
         //only add filter to post type you want
         if ($post_type == 'fishapp-competition'){
             //query database to get a list of years for the specific post type:
             $values = array();
             $query_years = $wpdb->get_results("SELECT year(post_date) as year from ".$table_prefix."posts
                     where post_type='".$post_type."'
                     group by year(post_date)
                     order by post_date");
             foreach ($query_years as &$data){
                 $values[$data->year] = $data->year;
             }
 
             //give a unique name in the select field
             ?>
             <div class="custom_filter_new">
                <div class="filed_search">                
                    <label for="start_date">Start Data</label>
                    <input type="text" name="start_date" class="start_date filter-field date_field"/>
                </div>
                <div class="filed_search">                
                    <label for="end_date">End Data</label>
                    <input type="text" name="end_date" class="end_date filter-field date_field"/>
                </div>
                <div class="filed_search">                
                    <select type="text" name="Comp_status" class="Comp_status filter-field"/>
                        <option value="">Status</option>
                        <option value="completed">Completed</option>
                        <option value="running">Running</option>
                        <option value="stop">Stop</option>
                    </select>
                </div>
             </div>
             <?php
         }
     });
 
     //this hook will alter the main query according to the user's selection of the custom filter we created above:
     add_filter( 'parse_query', function($query){
         global $pagenow;
         if(isset($_GET['post_type']) && isset($_GET['post_type'])=='fishapp-competition')
         $post_type = 'fishapp-competition';
 
         if ($post_type == 'fishapp-competition' && $pagenow=='edit.php' && isset($_GET['admin_filter_year']) && !empty($_GET['admin_filter_year'])) {
             $query->query_vars['year'] = $_GET['admin_filter_year'];
         }
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
            echo $othere_details['compi_start_date'];
            break;
        case 'end_date' :
            if(isset($othere_details['compi_end_date']) && !empty($othere_details['compi_end_date']))
            echo $othere_details['compi_end_date'];
                break;
        case 'participants' :
            echo '<a href="#">View</a>';
                break; 
        case 'stop_status' :
            echo '<input type="checkbox" name="stop_compi" data-id="'.$post_id.'">';
                break;                
        case 'action_work' :
            echo '<a href="'.get_post_permalink($post_id).'">View</a>';
            echo '<a href="'.get_edit_post_link($post_id,'edit').'">Edit</a>'; 
            break;
    }
}



add_action( 'wp_ajax_stop_comp', '_ajax_handler_for_stop_funtion' );    // If called from admin panel
add_action( 'wp_ajax_nopriv_stop_comp', '_ajax_handler_for_stop_funtion' );


function _ajax_handler_for_stop_funtion() {
    // print_r($_POST);
    echo 'll';
    // Make your response and echo it.

    // Don't forget to stop execution afterward.
    wp_die();
}