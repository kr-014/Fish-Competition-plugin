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

		
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_header_popup_posts' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_footer_popup_posts' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_display_rules_popup_posts' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_event_popup_posts' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_adcondition_popup_posts' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_design_popup' ),10, 5);
		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_close_popup' ),10, 5);

		add_action( 'add_meta_boxes', array(__CLASS__, 'add_metabox_for_template' ),10, 5);

		add_action( 'save_post', array(__CLASS__, 'save_popuppro' ),10);

		add_action('edit_form_after_title',  array( __CLASS__, 'move_metabox_after_title' ) );
	}




	/**
	 * Creates the Fields under the Popup add/edit menu and assigns their
	 * links to global variables
	 */
	public static function add_metabox_for_header_popup_posts($post_type, $post) {
		add_meta_box(
	        'popup_header_meta_box', // $id
	        'Header', // $title
	        array(__CLASS__, 'show_header_section_meta_box' ), // $callback
	        'popup_pro', // $screen
	        'advanced', // $context
	        'high' // $priority
	      );

	}
	//metabox output function, displays our fields, prepopulating as needed
    public static function show_header_section_meta_box($post){
    	global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true );
      	echo '<input type="text" name="your_meta_box_nonce" value="">';
    }



    public static function add_metabox_for_footer_popup_posts($post_type, $post) {
		add_meta_box(
	        'popup_footer_meta_box',
	        'Footer',
	        array(__CLASS__, 'show_footer_section_meta_box' ),
	        'popup_pro',
	        'normal',
	        'high'
	      );

	}
	//metabox output function, displays our fields, prepopulating as needed
    public static function show_footer_section_meta_box($post){
    	global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true );
      	echo '<input type="text" name="your_meta_box_nonce" value="">';
    }



    public static function add_metabox_for_display_rules_popup_posts($post_type, $post) {
		add_meta_box(
	        'popup_display_rules_meta_box',
	        'Display Rules',
	        array(__CLASS__, 'show_display_rules_section_meta_box' ),
	        'popup_pro', 
	        'normal', 
	        'high' 
	      );
	}
    public static function show_display_rules_section_meta_box($post_object){
    	global $post;
    	$instancebasic = Fishapp_Admin_instance::rules_display_fields();
    	$instanceisnot = Fishapp_Admin_instance::rules_display_fields_is_not();
    	$appended_posts = get_post_meta( $post_object->ID, 'get_allpost',true );
    	$appended_pages = get_post_meta( $post_object->ID, 'get_allpage',true );
    	
		    $args       = array(
		    	'public' => true,
		    	'exclude_from_search'=>false
			);
		$post_types = get_post_types( $args, 'objects' );
		if( !empty($post_types) ) {
			foreach( $post_types as $pkey => $pvalue ) {
				if($pkey!='attachment'){
					$posttypes[$pvalue->name] = $pvalue->label;
				}
			}
		}
		$all_categories = get_categories(array('hide_empty'=> 0));
		$allcat[''] = 'Select category';
		foreach ($all_categories as $ckey => $cvalue) {
			$allcat[$cvalue->term_id] = $cvalue->name;
		}

		$templates = get_page_templates();
		$templ['default'] = 'Default Template';
		foreach ($templates as $key => $value) {
			$templ[$key]=$key;
		}

		$data_row = 1;

		//echo '<pre>'; print_r($templates); echo '</pre>';
    	$displaylast = $displayshort = $displayisnot = $selectedpostdisplay = $selectedpagedisplay = $displaypostype = $displaypostcat = $displaypageteplate = 'none';
		$html = '';
		$html .= '<div class="display_matabox_sections_continer display_rules_continer popuppro-display_rules_continer" id="display-rules-'.$data_row.'" data_row="'.$data_row.'">';
			$html .= '<input type="hidden" value="'.$post_object->ID.'" class="getpopupid">';
			$html .= '<span class="colum-2 display_rules_isnot_part1">';
				$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][rules_basic]','display_rules_selected','display_rules_selected'.$data_row,$instancebasic);
			$html .= '</span>';
			$html .= '<span class="colum-2 display_rules_isnot_part2" style="display:'.$displayisnot.'">';
			$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][rules_isnot]','display_rules_isnot','display_rules_isnot'.$data_row,$instanceisnot);
			$html .= '</span>';
			$html .= '<span class="colum-2 display_rules_isnot_part3" style="display:'.$displaylast.'">';
				$html .= '<span class="shortcode-section" style="display:'.$displayshort.'"">[popuppro id='.$post_object->ID.']</span>';
				/*
				 * Select Posts with AJAX search
				 */
				$html .= '<span class="post-selected-part" style="display:'.$selectedpostdisplay.'">
				<label for="get_allpost">Posts:</label><br />
				<select id="get_allpost_'.$data_row.'"  class="get_allpost" name="popupprorules['.$data_row.'][get_allpost]" multiple="multiple" style="width:99%;max-width:25em;">';
				if( $appended_posts ) {
					foreach( $appended_posts as $post_id ) {
						$title = get_the_title( $post_id );
						// if the post title is too long, truncate it and add "..." at the end
						$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
						$html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
					}
				}
				$html .= '</select></span>';


				/*
				 * Select Pages with AJAX search
				 */
				$html .= '<span class="page-selected-part" style="display:'.$selectedpagedisplay.'">
				<select id="get_allpage_'.$data_row.'" name="popupprorules['.$data_row.'][get_allpage]" class="get_allpage" multiple="multiple" style="width:99%;max-width:25em;">';
				if( $appended_pages ) {
					foreach( $appended_pages as $post_id ) {
						$title = get_the_title( $post_id );
						// if the post title is too long, truncate it and add "..." at the end
						$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
						$html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
					}
				}
				$html .= '</select></span>';
	 	


				$html .= '<span class="post-type-part" style="display:'.$displaypostype.'"">';
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_posttype]','get_posttype','get_posttype'.$data_row,$posttypes);
				$html .= '</span>';
				$html .= '<span class="post-cat-part" style="display:'.$displaypostcat.'"">';
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_postcat]','get_postcat','get_postcat'.$data_row,$allcat);
				$html .= '</span>';
				$html .= '<span class="page-template-part" style="display:'.$displaypageteplate.'"">';
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_page_template]','get_page_template','get_page_template'.$data_row,$templ);
				$html .= '</span>';
			$html .= '</span>';
				
			$html .= '<span class="colum-2 display_rules_isnot_part4">';
				$html .= "<a href='javascript:void(0);' class='button button-primary button-large add-popuprule' row-rules='".$data_row."'>Add</a>";
			$html .= '</span>';
		$html .= '</div>';
		echo $html;
    }


    public static function add_metabox_for_event_popup_posts($post_type, $post) {
		add_meta_box(
	        'popup_event_meta_box',
	        'Popup Action',
	        array(__CLASS__, 'show_event_section_meta_box' ),
	        'popup_pro',
	        'normal',
	        'high'
	      );
	}
    public static function show_event_section_meta_box($post_object){
    	$instanceacction = Fishapp_Admin_instance::action_name_fields();
    	$instanceclassID = Fishapp_Admin_instance::action_display_class_id();
		$data_arow = 1;
    	$displayaction_by = $displayIDclass = $displaydelay = $displayaction_by2 = 'none';
		$html = '';
		$html .= '<div class="display_matabox_sections_continer display_action_continer popuppro-display_action_continer" id="display-action-'.$data_arow.'" data_arow="'.$data_arow.'">';
			$html .= '<span class="colum-2 display_action_basic_isnot_part1">';
				$html .= Fishapp_Admin_instance::select_get_display('popupproactions['.$data_arow.'][action_basic]','action_triger_selected','action_triger_selected'.$data_arow,$instanceacction);
			$html .= '</span>';

			$html .= '<span class="colum-2 display_action_basic_isnot_part2" style="display:'.$displayaction_by.'">';
				$html .= '<span class="action-action_by_part" style="display:'.$displayIDclass.'">';	
					$html .= Fishapp_Admin_instance::select_get_display('popupproactions['.$data_arow.'][action_by]','display_action_by','display_action_by'.$data_arow,$instanceclassID);
				$html .= '</span>';
				$html .= '<span class="action-delay_part" style="display:'.$displaydelay.'">';
					$html .= '<input type="text" placeholder="Delay" name="popupproactions['.$data_arow.'][delay_part]" class="delay_part" id="delay_part.'.$data_arow.'.">';
				$html .= '</span>';
			$html .= '</span>';

			$html .= '<span class="colum-2 display_action_basic_isnot_part3" style="display:'.$displayaction_by2.'">';
				$html .= '<span class="action-by_classid_part" style="display:'.$displayIDclass.'">';
					$html .= '<input type="text" placeholder="Class or ID" name="popupproactions['.$data_arow.'][by_classid]" class="by_classid" id="by_classid.'.$data_arow.'.">';
				$html .= '</span>';
			$html .= '</span>';	

			$html .= '<span class="colum-2 display_rules_isnot_part4">';
				$html .= "<a href='javascript:void(0);' class='button button-primary button-large add-popupaction' row-actions='".$data_arow."'>Add</a>";
			$html .= '</span>';
		$html .= '</div>';
		echo $html;
    }
    


    public static function add_metabox_for_adcondition_popup_posts($post_type, $post) {
		add_meta_box(
	        'popup_adcondition_meta_box', // $id
	        'Advance Condition', // $title
	        array(__CLASS__, 'show_adcondition_section_meta_box' ), // $callbackadd_metabox_for_advance_condition_popup_posts
	        'popup_pro', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
    public static function show_adcondition_section_meta_box($post){
    	global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true );
      	echo '<input type="text" name="your_meta_box_nonce" value="">';
    }

    public static function move_metabox_after_title () {
	    global $post, $wp_meta_boxes;
	    do_meta_boxes( get_current_screen(), 'advanced', $post );
	    unset( $wp_meta_boxes[get_post_type( $post )]['advanced'] );
	}



	public static function add_metabox_for_design_popup($post_type, $post) {
		add_meta_box(
	        'popup_design_option_meta_box', // $id
	        'Design', // $title
	        array(__CLASS__, 'show_design_option_meta_box' ), // $callback
	        'popup_pro', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}
	
	public static function show_design_option_meta_box($post){
		$padding = array('label'=>'Padding','type'=>'number','name'=>'popupprodesign[popup_padding]','id'=>'popup_padding','class'=>'popup_padding');
		$zindex = array('label'=>'Popup z-index','type'=>'number','name'=>'popupprodesign[popup_zindex]','id'=>'popup_zindex','class'=>'popup_zindex');
		$chk_overlay = array('label'=>'Enable popup overlay','type'=>'checkbox','name'=>'popupprodesign[chk_overlay]','id'=>'chk_overlay','class'=>'chk_overlay');
		$overlay_class = array('label'=>'Overlay custom css class:','type'=>'text','name'=>'popupprodesign[overlay_class]','id'=>'overlay_class','class'=>'overlay_class');
		$overlay_color = array('label'=>'Change color:','type'=>'text','name'=>'popupprodesign[overlay_color]','id'=>'overlay_color','class'=>'overlay_color');
		$overlay_opacity = array(
			'label'=>'Opacity:',
			'type'=>'range',
			'name'=>'popupprodesign[overlay_opacity]',
			'id'=>'overlay_opacity',
			'class'=>'overlay_opacity',
			'otherattr' => 'min="0" max="10"',
			'postfix'=> '<span class="opect-show"></span>',
			);

		$chk_bg = array('label'=>'Show background:','type'=>'checkbox','name'=>'popupprodesign[chk_bg]','id'=>'chk_bg','class'=>'chk_bg');
		$popopbg_color = array('label'=>'Color:','type'=>'text','name'=>'popupprodesign[popopbg_color]','id'=>'popopbg_color','class'=>'popopbg_color');

		$popupbg_opacity = array(
			'label'=>'Opacity:',
			'type'=>'range',
			'name'=>'popupprodesign[popupbg_opacity]',
			'id'=>'popupbg_opacity',
			'class'=>'popupbg_opacity',
			'otherattr' => 'min="0" max="10"',
			'postfix'=> '<span class="opectbg-show"></span>',
			);

		echo Fishapp_Admin_instance::input_get_display($padding);
		echo Fishapp_Admin_instance::input_get_display($zindex);
		echo Fishapp_Admin_instance::input_get_display($chk_overlay);
		echo Fishapp_Admin_instance::input_get_display($overlay_class);
		echo Fishapp_Admin_instance::input_get_display($overlay_color);
		echo Fishapp_Admin_instance::input_get_display($overlay_opacity);

		echo 'Background options';

		echo Fishapp_Admin_instance::input_get_display($chk_bg);
		echo Fishapp_Admin_instance::input_get_display($popopbg_color);

		echo Fishapp_Admin_instance::input_get_display($popupbg_opacity);

		echo Fishapp_Admin_instance::select_get_display('popupprodesign[popopbg_mode]','popopbg_mode select2element','popopbg_mode',Fishapp_Admin_instance::background_mode_fields());

		$thumb = array('size'=>'medium','id'=>'popup-bg-image');
		$button = array('id'=>'popbgimage_media_manager');
		$input = array('name'=>'popupprodesign[popupbg_image]','id'=>'popupbg_image','class'=>'regular-text');
		echo Fishapp_Admin_instance::image_upload_get_display('popupbg_image',$thumb,$input,$button);

		$design_mode_option = array('label'=>'Mode:','type'=>'radio','name'=>'popupprodesign[design_mode_option]','id'=>'design_mode_option','class'=>'design_mode_option');
		$design_mode_option ['options'] = array('responsive'=>'Responsive Mode','custom'=>'Custom Mode');
		echo Fishapp_Admin_instance::input_get_display($design_mode_option);


		$mode_res_size = array(
    		'10'=>'10%',
    		'20'=>'10%',
    		'30'=>'10%',
    		'40'=>'10%',
    		'50'=>'10%',
    		'60'=>'10%',
    		'70'=>'10%',
    		'80'=>'10%',
    		'90'=>'10%',
    		'100'=>'10%',
    		'full'=>'Full Screen',
    		);
		echo 'Size:';
		echo Fishapp_Admin_instance::select_get_display('popupprodesign[mode_res_size]','mode_res_size select2element','mode_res_size',$mode_res_size);

		$width_custom_mode = array('label'=>'Width:','type'=>'number','name'=>'popupprodesign[width_custom_mode]','id'=>'width_custom_mode','class'=>'width_custom_mode');
		$height_custom_mode = array('label'=>'Height:','type'=>'number','name'=>'popupprodesign[height_custom_mode]','id'=>'height_custom_mode','class'=>'height_custom_mode');
		echo Fishapp_Admin_instance::input_get_display($width_custom_mode);
		echo Fishapp_Admin_instance::input_get_display($height_custom_mode);

		$width_max_mode = array('label'=>'Max Width:','type'=>'number','name'=>'popupprodesign[width_max_mode]','id'=>'width_max_mode','class'=>'width_max_mode');
		$height_max_mode = array('label'=>'Max Height:','type'=>'number','name'=>'popupprodesign[height_max_mode]','id'=>'height_max_mode','class'=>'height_max_mode');
		echo Fishapp_Admin_instance::input_get_display($width_max_mode);
		echo Fishapp_Admin_instance::input_get_display($height_max_mode);

		$width_min_mode = array('label'=>'Min Width:','type'=>'number','name'=>'popupprodesign[width_min_mode]','id'=>'width_min_mode','class'=>'width_min_mode');
		$height_min_mode = array('label'=>'Min Height:','type'=>'number','name'=>'popupprodesign[height_min_mode]','id'=>'height_min_mode','class'=>'height_min_mode');
		echo Fishapp_Admin_instance::input_get_display($width_min_mode);
		echo Fishapp_Admin_instance::input_get_display($height_min_mode);
    }





    public static function add_metabox_for_close_popup($post_type, $post) {
		add_meta_box(
	        'popup_design_close_meta_box', // $id
	        'Close Settings', // $title
	        array(__CLASS__, 'show_close_option_meta_box' ), // $callback
	        'popup_pro', // $screen
	        'normal', // $context
	        'high' // $priority
	      );
	}

    public static function show_close_option_meta_box($post){
    	$chk_close_esc = array('label'=>'Dismiss on "esc" key:','type'=>'checkbox','name'=>'popupproclose[chk_close_esc]','id'=>'chk_close_esc','class'=>'chk_close_esc');
    	$chk_close_show = array('label'=>'Show "close" button:','type'=>'checkbox','name'=>'popupproclose[chk_close_show]','id'=>'chk_close_show','class'=>'chk_close_show');
    	$chk_close_delay = array('label'=>'Button delay:','type'=>'number','name'=>'popupprodesign[chk_close_delay]','id'=>'chk_close_delay','class'=>'chk_close_delay');
    	$close_position = array(
    		'bottom-right'=>'Bottom Right',
    		'top-right'=>'Top Right',
    		);

    	$close_position_x = array('label'=>'Right:','type'=>'number','name'=>'popupprodesign[close_position_x]','id'=>'close_position_x','class'=>'close_position_x');
    	$close_position_y = array('label'=>'Bottom:','type'=>'number','name'=>'popupprodesign[close_position_y]','id'=>'close_position_y','class'=>'close_position_y');


    	$close_icon_w = array('label'=>'Width:','type'=>'number','name'=>'popupprodesign[close_icon_w]','id'=>'close_icon_w','class'=>'close_icon_w');
    	$close_icon_h = array('label'=>'Height:','type'=>'number','name'=>'popupprodesign[close_icon_h]','id'=>'close_icon_h','class'=>'close_icon_h');

    	$chk_ovly_Dismiss = array('label'=>'Dismiss on overlay click:','type'=>'checkbox','name'=>'popupproclose[chk_ovly_Dismiss]','id'=>'chk_ovly_Dismiss','class'=>'chk_ovly_Dismiss');
    	$chk_close_disable = array('label'=>'Disable popup closing:','type'=>'checkbox','name'=>'popupproclose[chk_close_disable]','id'=>'chk_close_disable','class'=>'chk_close_disable');

    	
    	echo Fishapp_Admin_instance::input_get_display($chk_close_esc);
    	echo Fishapp_Admin_instance::input_get_display($chk_close_show);
    	echo Fishapp_Admin_instance::input_get_display($chk_close_delay);
    	echo 'Button position:';
    	echo Fishapp_Admin_instance::select_get_display('popupproclose[close_position]','close_position select2element','close_position',$close_position);
    	echo Fishapp_Admin_instance::input_get_display($close_position_x);
    	echo Fishapp_Admin_instance::input_get_display($close_position_y);

    	echo 'Button image:';
    	$thumb = array('size'=>'medium','id'=>'popup-close-icon');
		$button = array('id'=>'popclose_icon_media_manager');
		$input = array('name'=>'popupproclose[popup_close_icon]','id'=>'popup_close_icon','class'=>'regular-text');
		echo Fishapp_Admin_instance::image_upload_get_display('popup_close_icon',$thumb,$input,$button);
		echo Fishapp_Admin_instance::input_get_display($close_icon_w);
		echo Fishapp_Admin_instance::input_get_display($close_icon_h);
    	
    	echo Fishapp_Admin_instance::input_get_display($chk_ovly_Dismiss);
    	echo Fishapp_Admin_instance::input_get_display($chk_close_disable);
    }
    

	public static function add_metabox_for_template($post_type, $post) {
		add_meta_box(
	        'popup_template_meta_box', // $id
	        'Template', // $title
	        array(__CLASS__, 'show_template_section_meta_box' ), // $callback
	        'popup_pro', // $screen
	        'normal', // $context
	        'high' // $priority
	      );

	}

	public static function show_template_section_meta_box($post){
    	global $post;
        $meta = get_post_meta( $post->ID, 'your_fields', true );
      	echo '<input type="text" name="your_meta_box_nonce" value="">';
    }




    //saving meta info (used for both traditional and quick-edit saves)
    public static function save_popuppro($post_id){
    	if(!empty($_POST)){
    		echo '<pre>'; print_r($_POST); die;
    	}
    }
    //gets singleton instance
    // public static function getInstance(){
    //     if(is_null(self::$instance)){
    //         self::$instance = new self();
    //     }
    //     return self::$instance;
    // }





}
