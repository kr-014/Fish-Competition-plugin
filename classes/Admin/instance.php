<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Fishapp_Admin_ajax
 *
 * @since 1.7.0
 */
class Fishapp_Admin_instance {


	/**
	 * @var array
	 */
	public static $instance = array();

	/**
	 *
	 */
	public static function init() {
		
	}

	public static function rules_display_fields() {

		$instance = array(
			'' => 'Select rule',
			'everywhere' => 'Everywhere',
			'by_shortcode' => 'By Shortcode',
			'post_type' => 'Post Type', 
			);
		$instance['Post'] = array(
					'post_all' => 'All Posts',
					'post_selected' => 'Selected Posts',
					'post_category' => 'Post Category',
				);
		$instance['Page'] = array(
					'page_all' => 'All Pages',
					'page_selected' => 'Selected Pages', 
					'page_template' => 'Page Template', 
				);
		return $instance;
		
	}

	public static function background_mode_fields() {

		$instance = array(
			'' => 'None',
			'cover' => 'Cover',
			'full' => 'Full',
			'contain' => 'Contain',
			'repeat' => 'Repeat', 
			);
		return $instance;
	}


	public static function action_name_fields() {

		$instance = array(
			'' => 'Select Action',
			'load' => 'On load',
			'onclick' => 'On Click',
			'onhover' => 'On Hover', 
			);
		$instance['advance'] = array(
					'onScroll' => 'On Scroll',
				);
		return $instance;
	}
	public static function action_display_class_id() {
		$instance = array(
			'class' => 'Class',
			'id' => 'ID',
			);
		return $instance;
	}

	public static function rules_display_fields_is_not() {
		$instance = array(
			'is' => 'Is',
			'is_not' => 'Is not',
			);
		return $instance;
	}

	public static function select_get_display($name, $class, $id, $instance) {
		$html = '';
		/*
		 * Select Posts with AJAX search
		 */
	 	$html .= '<select name="'.$name.'" class="'.$class.'" id="'.$class.'" style="width:99%;max-width:25em;">';
	 		foreach ($instance as $key => $value) {
	 			if(!is_array($instance[$key])){
	 				$html .= '<option value="'.$key.'">'.$value.'</option>';
	 			} else {
	 				$html .= '<optgroup label="'.$key.'">';
	 				foreach ($instance[$key] as $keyin => $valuein) {
	 					$html .= '<option value="'.$keyin.'">'.$keyin.'</option>';
	 				}
	 				$html .= '</optgroup>';
	 			}
	 			
	 		}
	 	$html .= '</select>';
		return $html;
	}

	public static function input_get_display($instance,$otherattr='') {
		$html = $class = $id = $placeholder = $otherattr = '';
		if(isset($instance['class'])){
			$class = 'class="'.$instance['class'].'"';
		}
		
		if(isset($instance['id'])){
			$id = 'id="'.$instance['id'].'"';
		}
		if(isset($instance['placeholder'])){
			$placeholder = 'placeholder="'.$instance['placeholder'].'"';
		}

		if(isset($instance['otherattr'])){
			$otherattr = 'class="'.$instance['otherattr'].'"';
		}
		if(isset($instance['getval'])){
			$valget = 'value="'.$instance['getval'].'"';
		}
	

		$html .= '<div class="'.$instance['class'].'-wrap">';
		$html .= '<label>'.$instance['label'].'</label><br>';
		if($instance['type']=='textarea'){
			$html .= '<textarea  name="'.$instance['name'].'" '.$class.' '.$id.'   '.$placeholder.' '.$otherattr.' rows="'.$instance['rows'].'" cols="'.$instance['cols'].'" >'.$instance['getval'].'</textarea>';
			if(isset($instance['postfix'])){
				$html .= $instance['postfix'];
			}
		} else if($instance['type']=='radio'){
			$html .= '<div class="options_val">';
			foreach ($instance['options'] as $key => $value) {
				$checked = '';
				if($instance['getval']==$key){
					$checked="checked";
				}
				
				$html .= '<input type="'.$instance['type'].'" name="'.$instance['name'].'" value="'.$key.'" '.$class.' '.$checked.'/>';
				$html .= '<label>'.$value.'</label>';
			}
			$html .= '</div>';
			

		} else {
			if($instance['type']=='checkbox'){
				$checkedbox = '';
				if($instance['getval']=='on'){
					$checkedbox="checked";
				}
			}
			$html .= '<input type="'.$instance['type'].'" name="'.$instance['name'].'" '.$class.' '.$id.'   '.$placeholder.' '.$otherattr.' '.$valget.' '.$checkedbox.'/>';
			if(isset($instance['postfix'])){
				$html .= $instance['postfix'];
			}
		}
		$html .= '</div>';
		return $html;
	}

	public static function textarea_get_display($instance) {
		$html = $class = $id = $placeholder = '';
		if(isset($instance['class'])){
			$class = 'class="'.$instance['class'].'"';
		}
		if(isset($instance['id'])){
			$id = 'id="'.$instance['id'].'"';
		}
		if(isset($instance['placeholder'])){
			$placeholder = 'placeholder="'.$instance['placeholder'].'"';
		}
		$html .= '<div class="'.$class.'-wrap">';
			$html .= '<label>'.$instance['label'].'</label>';
			$html .= '<textarea name="'.$instance['name'].'" '.$class.' '.$id.'  '.$placeholder.' style="width:99%;max-width:25em;"></textarea>';
		$html .= '</div>';
		return $html;
	}

	public static function image_upload_get_display($option,$thumb, $input, $button){
		$image_id = get_option( $option );
		if( intval( $image_id ) > 0 ) {
		    // Change with the image size you want to use
		    $image = wp_get_attachment_image( $image_id, $thumb['size'], false, array( 'id' => $thumb['id'] ) );
		} else {
		    // Some default image
		    $image = '<img id="'.$thumb['id'].'" src="https://some.default.image.jpg" />';
		}

		 echo $image;
		 echo '<input type="hidden" name="'.$input['name'].'" id="'.$input['id'].'" value="'.esc_attr( $image_id ).'" class="'.$input['class'].'" />';
		 echo '<input type="button" class="button-primary" value="Select a image" id="'.$button['id'].'"/>';
	}
	// public static function checkbox_get_display($instance) {
	// 	$html = $class = $id = $placeholder = '';
	// 	if(isset($instance['class'])){
	// 		$class = 'class="'.$instance['class'].'"';
	// 	}
	// 	if(isset($instance['id'])){
	// 		$id = 'id="'.$instance['id'].'"';
	// 	}
	// 	if(isset($instance['placeholder'])){
	// 		$placeholder = 'placeholder="'.$instance['placeholder'].'"';
	// 	}
	// 	$html .= '<textarea name="'.$instance['name'].'" '.$class.' '.$id.'  '.$placeholder.' style="width:99%;max-width:25em;"></textarea>';
	// 	return $html;
	// }

	

	

}
