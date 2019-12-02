<?php
/**
 * Contributor Taxonomy.
 */
namespace Contributor\inc\admin\taxonomy;

class WP_Contributor_taxonomy {

	/**
	 * Rtcamp_Contributor_taxonomy constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_taxonomy' ] );
	}

	/**
	 * Register Contributors Taxonomy.
	 */
	public function register_taxonomy() {

		$labels = [
			'name'                       => _x( 'Contributors', 'Taxonomy General Name', CB_TEXT_DOMAIN ),
			'singular_name'              => _x( 'Contributor', 'Taxonomy Singular Name', CB_TEXT_DOMAIN ),
			'menu_name'                  => __( 'Contributors', CB_TEXT_DOMAIN ),
			'all_items'                  => __( 'All Contributors', CB_TEXT_DOMAIN ),
			'parent_item'                => __( 'Parent Contributor', CB_TEXT_DOMAIN ),
			'parent_item_colon'          => __( 'Parent Contributor:', CB_TEXT_DOMAIN ),
			'new_item_name'              => __( 'New Contributor Name', CB_TEXT_DOMAIN ),
			'add_new_item'               => __( 'Add New Contributor', CB_TEXT_DOMAIN ),
			'edit_item'                  => __( 'Edit Contributor', CB_TEXT_DOMAIN ),
			'update_item'                => __( 'Update Contributor', CB_TEXT_DOMAIN ),
			'view_item'                  => __( 'View Contributor', CB_TEXT_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate Contributors with commas', CB_TEXT_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove Contributors', CB_TEXT_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used', CB_TEXT_DOMAIN ),
			'popular_items'              => __( 'Popular Contributors', CB_TEXT_DOMAIN ),
			'search_items'               => __( 'Search Contributors', CB_TEXT_DOMAIN ),
			'not_found'                  => __( 'Not Found', CB_TEXT_DOMAIN ),
			'no_terms'                   => __( 'No Contributors', CB_TEXT_DOMAIN ),
			'items_list'                 => __( 'Contributors list', CB_TEXT_DOMAIN ),
			'items_list_navigation'      => __( 'Contributors list navigation', CB_TEXT_DOMAIN ),
		];

		$args = [
			'labels'                => $labels,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => false,
			'show_admin_column'     => false,
			'show_in_nav_menus'     => true,
			'show_tagcloud'         => true
		];
		register_taxonomy( CB_TAXONOMY, [ 'post' ], $args );
	}

}
