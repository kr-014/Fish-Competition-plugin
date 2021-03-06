<?php
class Fishapp_Admin {

	public static function init() {
		Fishapp_Admin_Pages::init();
		Fishapp_Admin_fields::init();
		Fishapp_Admin_ajax::init();
		Fishapp_Admin_instance::init();
		add_action('admin_enqueue_scripts', array( __CLASS__, 'admin_style' ));
	}

	function admin_style() {
		wp_localize_script( 'FrontEndAjax', 'ajax', array(
			'url' => admin_url( 'admin-ajax.php' )
		) );
		wp_enqueue_media();
		
		wp_enqueue_style('admin-styles', POPASSETS_URL.'/css/admin.css');
		wp_enqueue_script( 'jQuery Custom', POPASSETS_URL.'/js/custom.js');
		wp_enqueue_script( 'jQuery UI Datepicker', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js');
		wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
	}
	  

	/**
	 * Prevent user from deleting the current default popup_theme
	 *
	 * @param $allcaps
	 * @param $caps
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function prevent_default_theme_deletion( $allcaps, $caps, $args ) {
		global $wpdb;
		if ( isset( $args[0] ) && isset( $args[2] ) && $args[2] == fishapp_get_option( 'default_theme' ) && $args[0] == 'delete_post' ) {
			$allcaps[ $caps[0] ] = false;
		}

		return $allcaps;
	}

	/**
	 * Post-installation
	 *
	 * Runs just after plugin installation and exposes the
	 * popmake_after_install hook.
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function after_install() {

		if ( ! is_admin() ) {
			return;
		}

		$already_installed = get_option( '_fishapp_installed' );

		// Exit if not in admin or the transient doesn't exist
		if ( false === $already_installed ) {
			do_action( 'fishapp_after_install' );

			update_option( '_fishapp_installed', true );
		}
	}
}
