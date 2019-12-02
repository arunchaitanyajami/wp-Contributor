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
require_once 'inc/admin/class-rtcamp-contributor-post.php';
require_once 'inc/admin/class-rtcamp-contributor-taxonomy.php';
require_once 'inc/class-rtcamp-author-content-filter.php';

use Contributor\admin\post\Rtcamp_Contributor_Post;
use Contributor\inc\admin\taxonomy\Rtcamp_Contributor_taxonomy;

add_action(
	'plugins_loaded',
	function() {
		// disable for posts
		add_filter( 'use_block_editor_for_post', '__return_false', 10 );
		// disable for post types
		add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

		new Rtcamp_Contributor_taxonomy();
		new Rtcamp_Contributor_Post();
		new Rtcamp_Contributor_Post_Content_Filter();
	}
);
