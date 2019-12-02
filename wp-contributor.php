<?php
/*
Plugin Name: Rt Camp Contributor
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Plugin to display more than one author-name on a post.
Version: 1.0
Author: achaitanyajami
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'CB_TEXT_DOMAIN', 'contributor' );

/**
 * Include Required Files.
 */
require_once 'inc/admin/class-wp-contributor-post.php';
require_once 'inc/admin/class-wp-contributor-taxonomy.php';
require_once 'inc/class-wp-author-content-filter.php';

use Contributor\admin\post\Wp_Contributor_Post;
use Contributor\inc\admin\taxonomy\WP_Contributor_taxonomy;
use Contributor\inc\content\WP_Contributor_Post_Content_Filter;

add_action(
	'plugins_loaded',
	function() {
		// disable for posts
		add_filter( 'use_block_editor_for_post', '__return_false', 10 );
		// disable for post types
		add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

		new WP_Contributor_taxonomy();
		new Wp_Contributor_Post();
		new WP_Contributor_Post_Content_Filter();
	}
);
