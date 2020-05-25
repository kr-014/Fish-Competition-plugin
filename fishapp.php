<?php
/**
 * Plugin Name:  fishapp
 * Plugin URI:   https://github.com/deeplogix/fishapp-web
 * Description:  Easily create & style popups with any content. Theme editor to quickly style your popups. Add forms, social media boxes, videos & more.
 * Version:      1.8.9
 * Author:       fishapp
 * Author URI:   https://github.com/deeplogix/fishapp-web
 * License:      GPL2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  popup-pro
 * Domain Path:  /languages/
 *
 * @package     POPMAKE
 * @category    Core
 * @author      balkrishan sharma
 * @copyright   Copyright (c) 2019, Techspidies
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Autoloader
 *
 * @param $class
 */
function fishappweb_autoloader( $class ) {
	$fishapp_autoloaders = apply_filters( 'fishapp_autoloaders', array(
		array(
			'prefix' => 'Fishapp_',
			'dir'    => dirname( __FILE__ ) . '/classes/',
		),
	) );
	foreach ( $fishapp_autoloaders as $autoloader ) {
		$autoloader = wp_parse_args( $autoloader, array(
			'prefix'  => 'Fishapp_',
			'dir'     => dirname( __FILE__ ) . '/classes/',
			'search'  => '_',
			'replace' => '/',
		) );
		// project-specific namespace prefix
		$prefix = $autoloader['prefix'];

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// no, move to the next registered autoloader
			continue;
		}

		// get the relative class name
		$relative_class = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = $autoloader['dir'] . str_replace( $autoloader['search'], $autoloader['replace'], $relative_class ) . '.php';

		// if the file exists, require it
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

spl_autoload_register( 'fishappweb_autoloader' ); // Register autoloader

/**
 * Main fishappweb_main Class
 *
 * @since 1.0
 */
class fishappweb_main {

	/**
	 * @var string Plugin Name
	 */
	public static $NAME = 'fishapp';

	/**
	 * @var string Plugin Version
	 */
	public static $VER = '1.0.0';

	/**
	 * @var int DB Version
	 */
	public static $DB_VER = 8;

	/**
	 * @var string License API URL
	 */
	public static $API_URL = 'https://github.com/deeplogix/fishapp-web';

	/**
	 * @var string
	 */
	public static $MIN_PHP_VER = '5.2.17';

	/**
	 * @var string
	 */
	public static $MIN_WP_VER = '3.6';

	/**
	 * @var string Plugin URL
	 */
	public static $URL;

	/**
	 * @var string Plugin Directory
	 */
	public static $DIR;

	/**
	 * @var string Plugin FILE
	 */
	public static $FILE;

	/**
	 * Used to test if debug_mode is enabled.
	 *
	 * @var bool
	 */
	public static $DEBUG_MODE = false;

	/**
	 * @var fishapp_Utils_Cron
	 */
	public $cron;

	/**
	 * @var fishapp_Repository_Popups
	 */
	public $popups;

	/**
	 * @var fishapp_Repository_Themes
	 */
	public $themes;

	/**
	 * @var null|fishapp_Model_Popup
	 */
	public $current_popup;

	/**
	 * @var null|fishapp_Model_Theme
	 */
	public $current_theme;

	/**
	 * @var fishappweb_main The one true fishappweb_main
	 */
	private static $instance;

	/**
	 * Main instance
	 *
	 * @return fishappweb_main
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof fishappweb_main ) ) {
			self::$instance = new fishappweb_main;
			self::$instance->setup_constants();
			self::$instance->includes();
			// add_action( 'init', array( self::$instance, 'load_textdomain' ) );
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 */
	private function setup_constants() {
		self::$DIR  = plugin_dir_path( __FILE__ );
		self::$URL  = plugins_url( '/', __FILE__ );
		$assets  = plugins_url( '/', __FILE__ ).'assets/';
		self::$FILE = __FILE__;
		if ( ! defined( 'FISHAPP' ) ) {
			define( 'FISHAPP', self::$FILE );
		}

		if ( ! defined( 'FISHAPP_NAME' ) ) {
			define( 'FISHAPP_NAME', self::$NAME );
		}

		if ( ! defined( 'FISHAPP_SLUG' ) ) {
			define( 'FISHAPP_SLUG', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
		}

		if ( ! defined( 'FISHAPP_DIR' ) ) {
			define( 'FISHAPP_DIR', self::$DIR );
		}

		if ( ! defined( 'FISHAPP_URL' ) ) {
			define( 'FISHAPP_URL', self::$URL );
		}
		if ( ! defined( 'POPASSETS_URL' ) ) {
			define( 'POPASSETS_URL', $assets );
		}

		if ( ! defined( 'FISHAPP_NONCE' ) ) {
			define( 'FISHAPP_NONCE', 'FISHAPP_nonce' );
		}

		if ( ! defined( 'FISHAPP_VERSION' ) ) {
			define( 'FISHAPP_VERSION', self::$VER );
		}

		if ( ! defined( 'FISHAPP_DB_VERSION' ) ) {
			define( 'FISHAPP_DB_VERSION', self::$DB_VER );
		}

		if ( ! defined( 'FISHAPP_API_URL' ) ) {
			define( 'FISHAPP_API_URL', self::$API_URL );
		}
	}

	/**
	 * Include required files
	 */
	private function includes() {
		/** Loads most of our core functions */
		require_once self::$DIR . 'includes/functions.php';
	}


	public function init() {
		Fishapp_Types::init();
		Fishapp_Admin::init();
	}

	/**
	 * Returns true when debug mode is enabled.
	 *
	 * @return bool
	 */
	public static function debug_mode() {
		return true === self::$DEBUG_MODE;
	}

}

/**
 * Initialize the plugin.
 */
fishappweb_main::instance();

/**
 * The code that runs during plugin activation.
 * This action is documented in classes/Activator.php
 */
register_activation_hook( __FILE__, array( 'Fishapp_Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in classes/Deactivator.php
 */
register_deactivation_hook( __FILE__, array( 'Fishapp_Deactivator', 'deactivate' ) );

/**
 * @deprecated 1.7.0
 */
function Fishappweb_initialize() {
	// Disable Unlimited Themes extension if active.
	remove_action( 'Fishappweb_initialize', 'Fishapp_ut_initialize' );

	// Initialize old PUM extensions
	do_action( 'Fishapp_initialize' );
	do_action( 'Fishappweb_initialize' );
}

add_action( 'plugins_loaded', 'Fishappweb_initialize' );

/**
 * The main function responsible for returning the one true fishappweb_main
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $popmake = PopMake(); ?>
 *
 * @since      1.0
 * @deprecated 1.7.0
 *
 * @return object The one true fishappweb_main Instance
 */
function PopMake() {
	return fishappweb_main::instance();
}

/**
 * The main function responsible for returning the one true fishappweb_main
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since      1.8.0
 *
 * @return fishappweb_main
 */
function pum() {
	return fishappweb_main::instance();
}
