<?php
/*
Plugin Name: WP Contributor Plugin
Plugin URI: https://github.com/arunchaitanyajami/wp-Contributor
Description: Plugin to display more than one author-name on a post.
Version: 1.0
Author: achaitanyajami
Author URI: https://github.com/arunchaitanyajami/
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define Constants.
 */
define( 'CB_TEXT_DOMAIN', 'contributor' );
define( 'CB_TAXONOMY', 'contributors' );

/**
 * Include Required Files.
 */
require_once 'inc/admin/class-wp-contributor-post.php';
require_once 'inc/admin/class-wp-contributor-taxonomy.php';
require_once 'inc/class-wp-author-content-filter.php';

use Contributor\admin\post\WP_Contributor_Post;
use Contributor\inc\admin\taxonomy\WP_Contributor_Taxonomy;
use Contributor\inc\content\WP_Contributor_Post_Content_Filter;

/**
 * Fire all the required function once the plugin is loaded.
 */
function wp_contributor_load_plugin(){
	new WP_Contributor_Taxonomy();
	new Wp_Contributor_Post();
	new WP_Contributor_Post_Content_Filter();
}
add_action( 'plugins_loaded', 'wp_contributor_load_plugin' );
