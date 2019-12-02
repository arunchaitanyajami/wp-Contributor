<?php
/**
 * Wp Contributor Content Filter.
 *
 * @package WordPress
 */

namespace Contributor\inc\content;

class WP_Contributor_Post_Content_Filter {

	/**
	 * WP_Contributor_Post_Content_Filter constructor.
	 */
	public function __construct() {
		add_action( 'the_excerpt', [ $this, 'show_multi_authors' ] );
		add_action( 'the_content', [ $this, 'show_multi_authors' ] );
		add_filter( 'posts_where', array( $this, 'posts_where_filter' ), 100, 2 );
		add_filter( 'posts_join', array( $this, 'posts_join_filter' ), 100, 2 );
	}

	/**
	 * Function for displaying authors on front end.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function show_multi_authors( $content ) {

		$contributors = get_post_meta( get_the_ID(), CB_TAXONOMY, true );
		/* If there are not multiple authors associated , then don't do anything */
		if ( empty( $contributors ) ) {
			return $contributors;
		}

		$markup  = '<div class="contributors-box">' . __( 'Contributors: ', CB_TEXT_DOMAIN );
		$count   = count( $contributors );
		$counter = 1;
		foreach ( $contributors as $contributor ) {

			$authorInfo = get_user_by( 'id', $contributor );
			$author_url = get_author_posts_url( $contributor );
			$authorName = ucfirst( $authorInfo->display_name );
			$comma      = ( $counter == $count ) ? '' : ', ';
			$avatar     = get_avatar_url( $contributor );
			$markup     .= "<span><a href=" . $author_url . "><img width='3%' src=".$avatar."/>$authorName</a>$comma</span>";
			$counter ++;
		}
		$markup .= '</div>';

		return $content . $markup;
	}

	/**
	 * Add where conditional logic for post content filter for the author pages.
	 *
	 * @param $where
	 * @param $query
	 *
	 * @return mixed|string
	 */
	public function posts_where_filter( $where, $query ) {
		global $wpdb;
		if ( is_admin() || ! $query->is_author() ) {
			return $where;
		}

		$authorSlug = get_query_var( 'author_name' );
		$authorData = get_user_by( 'slug', $authorSlug );
		$termSlug   = (string) $authorData->data->ID;
		$getTerm    = get_term_by( 'slug', $termSlug, CB_TAXONOMY );
		$getTerm    = $getTerm->term_id;
		$where      = str_replace(
			"AND ({$wpdb->posts}.post_author = " . $authorData->data->ID . ")",
			'',
			$where
		);
		$where      = "AND ({$wpdb->term_relationships}.term_taxonomy_id IN (" . $getTerm . ") )" . $where;

		return $where;
	}

	/**
	 * Modify the author query posts SQL to include posts based on `contributors` taxonomy.
	 *
	 * @param $join
	 * @param $query
	 *
	 * @return string
	 */
	public function posts_join_filter( $join, $query ) {
		global $wpdb;
		if (
			is_admin() ||
			! $query->is_author() ||
			(
				! empty( $query->query_vars['post_type'] ) &&
				! is_object_in_taxonomy( $query->query_vars['post_type'], CB_TAXONOMY )
			)
		) {
			return $join;
		}

		// Check to see that JOIN hasn't already been added.
		$term_relationship_join = " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
		if ( strpos( $join, trim( $term_relationship_join ) ) === false ) {
			$join .= str_replace( "INNER JOIN", "LEFT JOIN", $term_relationship_join );
		}

		return $join;
	}
}
