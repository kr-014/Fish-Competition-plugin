<?php
/*******************************************************************************
 * Copyright (c) 2017, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Fishapp_Admin_ajax
 *
 * @since 1.7.0
 */
class Fishapp_Admin_ajax {


	/**
	 * @var array
	 */
	public static $Admin_ajax = array();

	/**
	 *
	 */


	public static function init() {
		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'rudr_select2_enqueue' ));
		add_action( 'wp_ajax_select_rule_display', array(__CLASS__,'rselect_rule_display_ajax_callback' ));

		add_action( 'wp_ajax_get_allpost', array(__CLASS__,'rget_allpost_ajax_callback' ));

		add_action( 'wp_ajax_get_allpage', array(__CLASS__,'rget_allpage_ajax_callback' ));
		add_action( 'wp_ajax_get_more_rule', array(__CLASS__,'get_addmore_rule_display' ));
		add_action( 'wp_ajax_get_more_action', array(__CLASS__,'get_addmore_action_display' ));

		add_action( 'wp_ajax_myprefix_get_image', array(__CLASS__,'myprefix_get_image' ));
	}


	public static function myprefix_get_image() {
	    if(isset($_GET['id']) ){
	        $image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'myprefix-preview-image' ) );
	        $data = array(
	            'image'    => $image,
	        );
	        wp_send_json_success( $data );
	    } else {
	        wp_send_json_error();
	    }
	}



	public static function get_addmore_action_display() {
		$post_object->ID = $_POST['popid'];
		$instanceacction = Fishapp_Admin_instance::action_name_fields();
    	$instanceclassID = Fishapp_Admin_instance::action_display_class_id();
		$data_arow = $_POST['row']+1;
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
			$html .= "<script src='".POPASSETS_URL."/js/popupproplugin.js'></script>"; 
		$html .= '</div>';
		echo json_encode($html);
		wp_die();
	}
	public static function get_addmore_rule_display() {

		$post_object->ID = $_POST['popid'];
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

		$data_row = $_POST['row']+1;

		//echo '<pre>'; print_r($templates); echo '</pre>';
    	$displaylast = $displayshort = $displayisnot = $selectedpostdisplay = $selectedpagedisplay = $displaypostype = $displaypostcat = $displaypageteplate = 'none';
		$html = '';
		$html .= '<div class="display_matabox_sections_continer display_rules_continer popuppro-display_rules_continer" id="display-rules-'.$data_row.'" data_row="'.$data_row.'">';
			$html .= '<input type="hidden" value="'.$post_object->ID.'" class="getpopupid">';
			$html .= '<span class="colum-2 display_rules_isnot_part1">';
				$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][rules_basic]','display_rules_selected','display_rules_selected',$instancebasic);
			$html .= '</span>';
			$html .= '<span class="colum-2 display_rules_isnot_part2" style="display:'.$displayisnot.'">';
			$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][rules_isnot]','display_rules_isnot','display_rules_isnot',$instanceisnot);
			$html .= '</span>';
			$html .= '<span class="colum-2 display_rules_isnot_part3" style="display:'.$displaylast.'">';
				$html .= '<span class="shortcode-section" style="display:'.$displayshort.'"">[popuppro id='.$post_object->ID.']</span>';
				/*
				 * Select Posts with AJAX search
				 */
				$html .= '<span class="post-selected-part" style="display:'.$selectedpostdisplay.'">
				<label for="get_allpost">Posts:</label><br />
				<select id="get_allpost"  class="get_allpost" name="popupprorules['.$data_row.'][get_allpost]" multiple="multiple" style="width:99%;max-width:25em;">';
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
				<select id="get_allpage" name="popupprorules['.$data_row.'][get_allpage]" class="get_allpage" multiple="multiple" style="width:99%;max-width:25em;">';
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
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_posttype]','get_posttype','get_posttype',$posttypes);
				$html .= '</span>';
				$html .= '<span class="post-cat-part" style="display:'.$displaypostcat.'"">';
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_postcat]','get_postcat','get_postcat',$allcat);
				$html .= '</span>';
				$html .= '<span class="page-template-part" style="display:'.$displaypageteplate.'"">';
					$html .= Fishapp_Admin_instance::select_get_display('popupprorules['.$data_row.'][get_page_template]','get_page_template','get_page_template',$templ);
				$html .= '</span>';
			$html .= '</span>';
				
			$html .= '<span class="colum-2 display_rules_isnot_part4">';
				$html .= "<a href='javascript:void(0);' class='button button-primary button-large add-popuprule' row-rules='".$data_row."'>Add</a>";
			$html .= '</span>';
			$html .= "<script src='".POPASSETS_URL."/js/popupproplugin.js'></script>"; 
		$html .= '</div>';

		echo json_encode($html);
		wp_die();
	}

	public static function rudr_select2_enqueue() {
		wp_enqueue_style('customcss', POPASSETS_URL.'/css/customcss.css' );
		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
		wp_enqueue_script('popupproplugin', POPASSETS_URL.'/js/popupproplugin.js', array( 'jquery', 'select2','wp-color-picker'  ), '', true  ); 
	}

	public static function rget_allpost_ajax_callback(){
		$search_results = new WP_Query( array( 
			's'=> $_GET['q'], // the search query
			'post_status' => 'publish', // if you don't want drafts to be returned
			'post_type' => 'post', 
			'ignore_sticky_posts' => 1,
			'posts_per_page' => 50 // how much to show at once
		) );
		if( $search_results->have_posts() ) :
			while( $search_results->have_posts() ) : $search_results->the_post();
				// shorten the title a little
				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
				$return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
			endwhile;
		endif;
		echo json_encode( $return);
		die;
	}

	public static function rget_allpage_ajax_callback(){
		$search_results = new WP_Query( array( 
			's'=> $_GET['q'], // the search query
			'post_status' => 'publish', // if you don't want drafts to be returned
			'post_type' => 'page', 
			'ignore_sticky_posts' => 1,
			'posts_per_page' => 50 // how much to show at once
		) );
		if( $search_results->have_posts() ) :
			while( $search_results->have_posts() ) : $search_results->the_post();
				// shorten the title a little
				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
				$return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
			endwhile;
		endif;
		echo json_encode( $return);
		die;
	}


	public static function rselect_rule_display_ajax_callback(){
		global $post;
		$result = array();


		$result = $_POST;
		if($_POST['selected']=='post_selected'){
			 $result = $_POST;

		}	

		if($_POST['selected']=='by_shortcode'){
			$result = $_POST;
			$result['isnot'] = 'hide';
			echo json_encode($result);
			die;
		}
		echo json_encode( $result );
		die;
	}

}
