<?php
/**
 * Contributor metabox for allowed post-types.
 *
 * @package WordPress
 */
namespace Contributor\admin\post;

class WP_Contributor_Post {

	/**
	 * Screens.
	 *
	 * @var array
	 */
	private $screens = [
		'post'
	];

	/**
	 * Nonce Key.
	 *
	 * @var string
	 */
	private $nonce_key = 'contributor_nonce';

	/**
	 * Class construct method. Adds actions to their respective WordPress hooks.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
	}

	/**
	 * Hooks into WordPress' add_meta_boxes function.
	 * Goes through screens (post types) and adds the meta box.
	 */
	public function add_meta_boxes() {
		foreach ( $this->screens as $screen ) {
			add_meta_box(
				CB_TAXONOMY,
				__( 'Contributors', CB_TEXT_DOMAIN ),
				[ $this, 'add_meta_box_callback' ],
				$screen,
				'side',
				'high'
			);
		}
	}

	/**
	 * Generates the HTML for the meta box
	 *
	 * @param \WP_Post $post WordPress post object
	 */
	public function add_meta_box_callback( \WP_Post $post ) {
		wp_nonce_field( '_' . $this->nonce_key, $this->nonce_key );
		$users = get_users();
		if ( empty( $users ) ) {
			return;
		}


		foreach ( $users as $user ) :
			if ( ! $user instanceof \WP_User ) {
				continue;
			}
			?>
			<p>
				<input
					type="checkbox" name="<?php echo CB_TAXONOMY; ?>[]" id="contributor_<?php echo $user->ID; ?>"
					value="<?php echo $user->ID; ?>"
					<?php echo ( $this->check_contributor_meta( $post->ID, $user->ID ) ) ? 'checked' : ''; ?> />
				<label for="contributor_author"><?php _e( ucfirst( $user->display_name ), CB_TEXT_DOMAIN ); ?></label></p>
			</p>
			<?php
		endforeach;
	}

	/**
	 * Hooks into WordPress' save_post function.
	 *
	 * @param int      $post_id Post Id.
	 * @param \WP_Post $post    Post Object.
	 */
	public function save_post( int $post_id, \WP_Post $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, $this->nonce_key, FILTER_SANITIZE_STRING );
		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, '_' . $this->nonce_key ) ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		$contributors = filter_input( INPUT_POST, CB_TAXONOMY, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $contributors ) ) {
			$author       = $post->post_author;
			$contributors = [ 0 => $author ];
		}

		update_post_meta( $post_id, CB_TAXONOMY, $contributors );
		wp_set_object_terms( $post_id, $contributors, CB_TAXONOMY );
	}

	/**
	 * Check if user exists in post contributor meta.
	 *
	 * @param int $post_id Post Id.
	 * @param int $user_id User Id.
	 *
	 * @return bool
	 */
	public function check_contributor_meta( int $post_id, int $user_id ): bool {
		if ( empty( $post_id ) ) {
			return false;
		}

		$contributors = get_post_meta( $post_id, CB_TAXONOMY, true );

		return empty( $contributors ) ? ( get_current_user_id() === ( $user_id ) ) : in_array( $user_id, $contributors );
	}
}
