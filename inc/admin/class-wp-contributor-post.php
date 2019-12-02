<?php
namespace Contributor\admin\post;

class Wp_Contributor_Post {

	/**
	 * Screens.
	 *
	 * @var array
	 */
	private $screens = [
		'post',
	];

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
				'contributors',
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
	 * @param object $post WordPress post object
	 */
	public function add_meta_box_callback( $post ) {
		wp_nonce_field( '_contributor_nonce', 'contributor_nonce' );
		$users = $this->get_all_users();
		if ( empty( $users ) ) {
			return;
		}


		foreach ( $users as $user ) {
			if ( ! $user instanceof \WP_User ) {
				continue;
			}
			?>
			<p>
				<input
					type="checkbox" name="contributors[]" id="contributor_<?php echo $user->ID; ?>"
					value="<?php echo $user->ID; ?>"
					<?php echo ( $this->check_contributor_meta( $post->ID, $user->ID ) ) ? 'checked' : ''; ?> />
				<label for="contributor_author"><?php _e( ucfirst( $user->display_name ), CB_TEXT_DOMAIN ); ?></label></p>
			</p>
			<?php
		}
	}

	/**
	 * Hooks into WordPress' save_post function.
	 *
	 * @param int    $post_id Post Id.
	 * @param object $post    Post Object.
	 */
	public function save_post( int $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce_key = 'contributor_nonce';
		$nonce     = filter_input( INPUT_POST, $nonce_key, FILTER_SANITIZE_STRING );
		echo $nonce;
		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, '_contributor_nonce' ) ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		$contributors = filter_input( INPUT_POST, 'contributors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $contributors ) ) {
			$author       = $post->post_author;
			$contributors = [ 0 => $author ];
		}

		update_post_meta( $post_id, 'contributors', $contributors );
		wp_set_object_terms( $post_id, $contributors, 'contributors' );
	}

	public function check_contributor_meta( $post_id, $user_id ) {
		$contributors = get_post_meta( $post_id, 'contributors', true );

		return empty( $contributors ) ? ( get_current_user_id() === $user_id ) : in_array( $user_id, $contributors );
	}

	private function get_all_users(){
		return get_users();
	}
}
