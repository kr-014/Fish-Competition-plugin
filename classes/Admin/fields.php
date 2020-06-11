<?php
/*******************************************************************************
 * Copyright (c) 2017, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Fishapp_Admin_fields
 *
 * @since 1.7.0
 */
class Fishapp_Admin_fields {


	/**
	 * @var array
	 */
	public static $fields = array();

	/**
	 *
	 */
	public static function init() {
		
 
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_fish_catching_matrix' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_Bonus_points' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_competition_settings' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_competition_other_settings' ),10, 5);
		add_action( 'save_post', array(__CLASS__, 'save_fishapp_competition' ),10);
		add_action( 'init', array(__CLASS__, 'add_metaboxs_for_participants' ));
	}


	public static function add_metabox_for_fish_catching_matrix($post_type, $post) {
		add_meta_box(
	        'fish_catching_matrix_meta_box', // $id
	        'Fish catching Matrix', // $title
	        array(__CLASS__, 'show_fish_catching_matrix_meta_box' ), // $callback
	        'fishapp-competition', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	public static function show_fish_catching_matrix_meta_box($post){
		$fish_catching_atrix_val = Fishapp_Admin_fields::get_fishapp_competition_meta_value('fish_catching_atrix');

		$fish_catching_atrix_arr = array('label'=>'Fish biology','type'=>'textarea','name'=>'fish_catching_atrix[fish_biology]','id'=>'fish_biology','class'=>'fish_biology',
		'rows'=>"4", 'cols'=>"50", 'getval' =>$fish_catching_atrix_val['fish_biology'] );
		$Length = array('label'=>'Length','type'=>'number','name'=>'fish_catching_atrix[length]','id'=>'length','class'=>'length', 'getval' =>$fish_catching_atrix_val['length']);
		echo Fishapp_Admin_instance::input_get_display($fish_catching_atrix_arr);
		echo Fishapp_Admin_instance::input_get_display($Length);
    }

	public static function add_metabox_for_Bonus_points($post_type, $post) {
		add_meta_box(
	        '_Bonus_points_meta_box', // $id
	        'Bonus points', // $title
	        array(__CLASS__, 'show_Bonus_points_meta_box' ), // $callback
	        'fishapp-competition', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	public static function show_Bonus_points_meta_box($post){
		
		$Bonus_points = Fishapp_Admin_fields::get_fishapp_competition_meta_value('Bonus_points');
		$fish_length_matrix = array('label'=>'Fish length Matrix',
		'type'=>'number',
		'name'=>'Bonus_points[fish_length_matrix]',
		'id'=>'fish_length_matrix',
		'class'=>'fish_length_matrix',
		'getval' =>$Bonus_points['fish_length_matrix']);
		echo Fishapp_Admin_instance::input_get_display($fish_length_matrix);
    }



	public static function add_metabox_for_competition_settings($post_type, $post) {
		add_meta_box(
	        'competition_settings_meta_box', // $id
	        'Settings', // $title
	        array(__CLASS__, 'show_competition_settings_meta_box' ), // $callback
	        'fishapp-competition', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	public static function show_competition_settings_meta_box($post){
		$competition_settings = Fishapp_Admin_fields::get_fishapp_competition_meta_value('competition_settings');
		
        $photo_visibility = array('label'=>'Photo Visibility','type'=>'radio','name'=>'competition_settings[photo_visibility]','id'=>'photo_visibility','class'=>'photo_visibility',
									'getval' =>$competition_settings['photo_visibility']);
		$photo_visibility ['options'] = array('public'=>'Public','competition'=>'Competition');

		$video_upload_allowed = array('label'=>'Video upload allowed?','type'=>'checkbox','name'=>'competition_settings[video_upload_allowed]','id'=>'video_upload_allowed','class'=>'video_upload_allowed',
		'getval' =>$competition_settings['video_upload_allowed']);
		$top_winners = array('label'=>'Top winners','type'=>'number','name'=>'competition_settings[top_winners]','id'=>'top_winners','class'=>'top_winners',
		'getval' =>$competition_settings['top_winners']);
		$Price_information = array('label'=>'Price information','type'=>'number','name'=>'competition_settings[price_information]','id'=>'price_information','class'=>'price_information',
		'getval' =>$competition_settings['price_information']);

		echo Fishapp_Admin_instance::input_get_display($photo_visibility);
		echo Fishapp_Admin_instance::input_get_display($video_upload_allowed);
		echo '<div class="win_set">';
		echo '<br><strong class="setting_main_head">Winners settings</strong>';
		echo Fishapp_Admin_instance::input_get_display($top_winners);
		echo Fishapp_Admin_instance::input_get_display($Price_information);
		echo '</div>';

	}
	

	public static function add_metabox_for_competition_other_settings($post_type, $post) {
		add_meta_box(
	        'competition_other_settings_meta_box', // $id
	        'Othere Settings', // $title
	        array(__CLASS__, 'show_competition_othere_settings_meta_box' ), // $callback
	        'fishapp-competition', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	public static function show_competition_othere_settings_meta_box($post){
		$com_othere_set = Fishapp_Admin_fields::get_fishapp_competition_meta_value('competition_othere_settings');
		$live_streaming_allowed = array('label'=>'Live streaming allowed?','type'=>'checkbox','name'=>'competition_othere_settings[live_streaming_allowed]','id'=>'Live_streaming_allowed','class'=>'Live_streaming_allowed' ,
		'getval' =>$com_othere_set['live_streaming_allowed']);
		$daily_catching_limit = array('label'=>'Daily catching limit','type'=>'number','name'=>'competition_othere_settings[daily_catching_limit]','id'=>'daily_catching_limit','class'=>'daily_catching_limit',
		'getval' =>$com_othere_set['daily_catching_limit']);
		$daily_photo_uploads_limit_per_User = array('label'=>'Daily photo uploads limit/User','type'=>'number','name'=>'competition_othere_settings[daily_photo_uploads_limit_per_User]','id'=>'daily_photo_uploads_limit_per_User','class'=>'daily_photo_uploads_limit_per_User',
		'getval' =>$com_othere_set['daily_photo_uploads_limit_per_User']);
		$compi_start_date = array('label'=>'Start Date','type'=>'input','name'=>'competition_othere_settings[compi_start_date]','id'=>'compi_start_date','class'=>'compi_start_date date_field',
		'getval' =>$com_othere_set['compi_start_date']);
		$compi_end_date = array('label'=>'End Date','type'=>'input','name'=>'competition_othere_settings[compi_end_date]','id'=>'compi_end_date','class'=>'compi_end_date date_field',
		'getval' =>$com_othere_set['compi_end_date']);
		// $option_compi_end_date = array('label'=>'Option to start and end the competition','type'=>'input','name'=>'competition_othere_settings[option_compi_end_date]','id'=>'option_compi_end_date','class'=>'option_compi_end_date',
		// 'getval' =>$com_othere_set['option_compi_end_date']);
		echo Fishapp_Admin_instance::input_get_display($daily_catching_limit);
		echo Fishapp_Admin_instance::input_get_display($daily_photo_uploads_limit_per_User);
		echo Fishapp_Admin_instance::input_get_display($live_streaming_allowed);
        echo '<div class="com_date">';
		echo '<br><strong class="setting_main_head">Start Date and end Date</strong>';
		echo '<div class="compi_start_end_date">';
			echo Fishapp_Admin_instance::input_get_display($compi_start_date);
			echo Fishapp_Admin_instance::input_get_display($compi_end_date);
		echo '</div>';
		echo '</div>';
		// echo Fishapp_Admin_instance::input_get_display($option_compi_end_date);

    }
    public static function move_metabox_after_title () {
	    global $post, $wp_meta_boxes;
	    do_meta_boxes( get_current_screen(), 'advanced', $post );
	    unset( $wp_meta_boxes[get_post_type( $post )]['advanced'] );
	}
    //saving meta info (used for both traditional and quick-edit saves)
    public static function save_fishapp_competition($post_id){
		
    	if(!empty($_POST)){
			if(get_post_type($post_id) == "fishapp-participants"){
				update_post_meta($post_id,'participant_upload_image',array_filter(explode(",",$_POST['participant_upload_image'])));
				update_post_meta($post_id,'participant_comp_details',$_POST['compi_list']);
			}
			if(get_post_type($post_id) == "fishapp-competition"){
				update_post_meta($post_id,'fish_catching_atrix',$_POST['fish_catching_atrix']);
				update_post_meta($post_id,'Bonus_points',$_POST['Bonus_points']);
				update_post_meta($post_id,'competition_settings',$_POST['competition_settings']);
				update_post_meta($post_id,'competition_othere_settings',$_POST['competition_othere_settings']);
				update_post_meta($post_id,'competition_start_date',strtotime($_POST['competition_othere_settings']['compi_start_date']));
				update_post_meta($post_id,'competition_end_date',strtotime($_POST['competition_othere_settings']['compi_end_date']));
			}
    	}
	}
	
	public static function get_fishapp_competition_meta_value($key){
		global $post;
		$post_id= $post->ID;
		$dataval = '';
		if(!empty(get_post_meta($post_id,$key,true))){
			$dataval = get_post_meta($post_id,$key,true);
		}
		return $dataval;
	}

	public static function add_metaboxs_for_participants(){
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_participants_photos' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_participants_compi_details' ),10, 5);
	}

	
	public static function add_metabox_for_participants_compi_details($post_type, $post) {
		add_meta_box(
	        'participants_compi_details', // $id
	        'Competition Details', // $title
	        array(__CLASS__, 'show_participants_compi_details_meta_box' ), // $callback
	        'fishapp-participants', // $screen
	        'side', // $context
	        'low' // $priority
	      );
	}
	
	public static function show_participants_compi_details_meta_box($post){
		$argscompi_list = array(
			'numberposts' => -1,
			'post_type'   => 'fishapp-competition'
		  );
		  $compi_lists = get_posts( $argscompi_list );
		  $select_val = '';
		  if(!empty(Fishapp_Admin_fields::get_fishapp_competition_meta_value('participant_comp_details'))){
			$select_val = Fishapp_Admin_fields::get_fishapp_competition_meta_value('participant_comp_details');
		  }
		  
		echo '<select class="compi_list" name="compi_list">';
			echo '<option>select</option>';
			foreach($compi_lists as $listval){
				$selecttext = '';
				if($select_val == $listval->ID) {
					$selecttext = 'selected';
				}

					echo '<option value="'.$listval->ID.'" '.$selecttext.'>'.$listval->post_title.'</option>';
				
				
			}
		echo '</select>';
	}
	
	public static function add_metabox_for_participants_photos($post_type, $post) {
		add_meta_box(
	        'participants_photos_meta_box', // $id
	        'Photos', // $title
	        array(__CLASS__, 'show_participants_photos_meta_box' ), // $callback
	        'fishapp-participants', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	public static function show_participants_photos_meta_box($post){
		$meta_key = 'participant_upload_image';
		$valuephotos = '';
		if(!empty(Fishapp_Admin_fields::get_fishapp_competition_meta_value('participant_upload_image'))){
			$valuephotos = implode(',',Fishapp_Admin_fields::get_fishapp_competition_meta_value('participant_upload_image'));
		}
		echo Fishapp_Admin_fields::_image_uploader_field( $meta_key, $valuephotos );
    }
		/*
	* @param string $name Name of option or name of post custom field.
	* @param string $value Optional Attachment ID
	* @return string HTML of the Upload Button
	*/
	public function _image_uploader_field( $name, $value = '') {
		
		
		//get_post_meta('participant_upload_image',true)
		
		$image = ' button">Upload image';
		$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
		$display = 'none'; // display state ot the "Remove image" button
		$imagehtml = '';
		foreach(explode(",",$value) as $valattach){
			if( $image_attributes = wp_get_attachment_image_src( $valattach, $image_size ) ) {
				$imagehtml .= '<span data-id="'.$valattach.'"><span class="del_icon" delete-id="'.$valattach.'">x</span><img class="true_pre_image" src="' .$image_attributes[0]. '" style="max-width:100px;display:block;" /></span>';
				// $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
				// $display = 'inline-block';
		
			} 
		}
		
	
		return '
		<div>
			<a href="#" class="upload_image_button' . $image . '</a>
			<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . esc_attr( $value ) . '" />
			<a href="#" class="remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
			<div class="gallery_image">'.$imagehtml.'</div>
		</div>';
	}

    //gets singleton instance
    // public static function getInstance(){
    //     if(is_null(self::$instance)){
    //         self::$instance = new self();
    //     }
    //     return self::$instance;
    // }
}
