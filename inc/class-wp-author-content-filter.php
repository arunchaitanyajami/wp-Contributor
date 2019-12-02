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
	public function show_multi_authors( string $content ) : string {

		$contributors = get_post_meta( get_the_ID(), CB_TAXONOMY, true );
		/* If there are not multiple authors associated , then don't do anything. */
		if ( empty( $contributors ) ) {
			return $contributors;
		}

		$markup  = '<div class="contributors-box">' . __( 'Contributors: ', CB_TEXT_DOMAIN );
		$count   = count( $contributors );
		$counter = 1;
		foreach ( $contributors as $contributor ) {

			$author_info = get_user_by( 'id', $contributor );
			if ( ! $author_info instanceof \WP_User ) {
				continue;
			}

			$author_url = get_author_posts_url( $contributor );
			$markup     .= sprintf(
				'<span style="margin-right: 5px"><a href="%1$s"><img width="15px" src="%2$s">%3$s</a>%4$s</span>',
				esc_url( $author_url ),
				esc_url( get_avatar_url( $contributor ) ),
				esc_attr( ucfirst( $author_info->display_name ) ),
				( $counter === $count ) ? '' : ', '
			);
			$counter ++;
		}
		$markup .= '</div>';

		return $content . $markup;
	}

	/**
	 * Add where conditional logic for post content filter for the author pages.
	 *
	 * @param string $where WP SQL post WHERE string.
	 * @param \WP_Query $query WP Query object
	 *
	 * @return string
	 */
	public function posts_where_filter( string $where, \WP_Query $query ): string {
		global $wpdb;
		if ( is_admin() || ! $query->is_author() ) {
			return $where;
		}

		$author_slug  = get_query_var( 'author_name' );
		$author_data = get_user_by( 'slug', $author_slug );
		if ( ! $author_data instanceof \WP_User ) {
			return $where;
		}

		$getTerm = get_term_by( 'slug', $author_data->data->ID, CB_TAXONOMY );
		$where   = str_replace(
			"AND ({$wpdb->posts}.post_author = " . $author_data->data->ID . ")",
			'',
			$where
		);
		$where   = "AND ({$wpdb->term_relationships}.term_taxonomy_id IN (" . $getTerm->term_id . ") )" . $where;

		return $where;
	}

	/**
	 * Modify the author query posts SQL to include posts based on `contributors` taxonomy.
	 *
	 * @param string $join  Post Join Sql
	 * @param \WP_Query $query Wp query object
	 *
	 * @return string
	 */
	public function posts_join_filter( string $join, \WP_Query $query ): string {
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
