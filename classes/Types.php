<?php

class Fishapp_Types {

	/**
	 * Hook the initialize method to the WP init action.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 1 );
		
		
	}

	/**
	 * Register post types
	 */
	public static function register_post_types() {

		
		if ( ! post_type_exists( 'fishapp-competition ' ) ) {
			$labels = Fishapp_Types::post_type_labels( __( 'Competition', 'fishapp-competition' ), __( 'Competition', 'fishapp-competition' ) );

			$labels['menu_name'] = __( 'Competition', 'fishapp-competition' );

			$fishapp_args = apply_filters( 'fishapp_competition_post_type_args', array(
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'rewrite'             => true,
				'exclude_from_search' => true,
				'show_in_nav_menus'   => true,
				'show_ui'             => true,
				'menu_icon'           => FISHAPP_URL . '/assets/images/admin/dashboard-icon.png',
				'menu_position'       => 20.292892729,
				'supports' => array('title','editor','thumbnail')
				
			) );
			register_post_type( 'fishapp-competition', apply_filters( 'fishapp_competition_post_type_args', $fishapp_args ) );
		}
		if ( ! post_type_exists( 'fishapp-participants ' ) ) {
			$labels = Fishapp_Types::post_type_labels( __( 'Participants', 'fishapp-participants' ), __( 'Participants', 'fishapp-participants' ) );

			$labels['menu_name'] = __( 'participants', 'fishapp-participants' );

			$fishapp_args = apply_filters( 'fishapp_participants_post_type_args', array(
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'rewrite'             => true,
				'exclude_from_search' => true,
				'show_in_nav_menus'   => true,
				'show_ui'             => true,
				'menu_icon'           => FISHAPP_URL . '/assets/images/admin/dashboard-icon.png',
				'menu_position'       => 20.292892729,
				'supports' => array('title','editor','thumbnail')
				
			) );
			register_post_type( 'fishapp-participants', apply_filters( 'fishapp_participants_post_type_args', $fishapp_args ) );
		}
	}

	/**
	 * @param $singular
	 * @param $plural
	 *
	 * @return mixed
	 */
	public static function post_type_labels( $singular, $plural ) {
		$labels = apply_filters( 'fishapp_competition_labels', array(
			'name'               => '%2$s',
			'singular_name'      => '%1$s',
			'add_new_item'       => _x( 'Add New %1$s', 'Post Type Singular: "Competition", "Competition Theme"', 'Competition-maker' ),
			'add_new'            => _x( 'Add %1$s', 'Post Type Singular: "Competition", "Competition Theme"', 'Competition-maker' ),
			'edit_item'          => _x( 'Edit %1$s', 'Post Type Singular: "Competition", "Competition Theme"', 'Competition-maker' ),
			'new_item'           => _x( 'New %1$s', 'Post Type Singular: "Competition", "Competition Theme"', 'Competition-maker' ),
			'all_items'          => _x( 'All %2$s', 'Post Type Plural: "Competitions", "Competition Themes"', 'Competition-maker' ),
			'view_item'          => _x( 'View %1$s', 'Post Type Singular: "Competition", "Competition Theme"', 'Competition-maker' ),
			'search_items'       => _x( 'Search %2$s', 'Post Type Plural: "Competitions", "Competition Themes"', 'Competition-maker' ),
			'not_found'          => _x( 'No %2$s found', 'Post Type Plural: "Competitions", "Competition Themes"', 'Competition-maker' ),
			'not_found_in_trash' => _x( 'No %2$s found in Trash', 'Post Type Plural: "Competitions", "Competition Themes"', 'Competition-maker' ),
		) );

		foreach ( $labels as $key => $value ) {
			$labels[ $key ] = sprintf( $value, $singular, $plural );
		}

		return $labels;
	}
}