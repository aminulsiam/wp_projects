<?php

class WP_Portfolio_Admin {

	/**
	 * Wp_Pool_Admin constructor.
	 * Write all admin hooks
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'pf_enqueue_styles' ), 10 );
		add_action( 'admin_enqueue_scripts', [ $this, 'pf_enqueue_scripts' ] );


		add_action( 'init', [ $this, 'pf_register_projects_cpt' ] );
		add_action( 'add_meta_boxes', [ $this, 'pf_add_meta_box' ] );
		add_action( 'save_post', [ $this, 'pf_save_metadata' ] );


	}

	/**
	 * Register Projects Custom Post Types
	 *
	 * @no_param
	 */
	public function pf_register_projects_cpt() {

		$labels = array(
			'name'               => _x( 'Projects', 'pf' ),
			'add_new_item'       => _x( 'Add New Project', 'pf' ),
			'all_items'          => __( 'All Projects', 'pf' ),
			'view_item'          => __( 'View Project', 'pf' ),
			'add_new'            => __( 'Add New', 'pf' ),
			'edit_item'          => __( 'Edit Project', 'pf' ),
			'update_item'        => __( 'Update Project', 'pf' ),
			'search_items'       => __( 'Search Project', 'pf' ),
			'not_found'          => __( 'Not Found', 'pf' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'pf' ),

		);

		$args = array(
			'public'    => true,
			'labels'    => $labels,
			'menu_icon' => 'dashicons-layout',
			'supports'  => array( 'title', 'editor', 'thumbnail' ),
			'rewrite'   => array( 'slug' => 'service_management' ),

		);
		register_post_type( 'projects', $args );

	} //end method pf_register_projects_cpt

	/**
	 *
	 */
	public function pf_add_meta_box() {
		add_meta_box(
			'pf_projects',
			'Projects Additional Information',
			[ $this, 'pf_render_metabox' ],
			'projects',
			'normal',
			'default'
		);
	} //end method pf_add_meta_in_projects_cpt

	/**
	 * Projects Meta Box Renderer
	 *
	 * @param $post
	 */
	public function pf_render_metabox( $post ) {

		$pf_ex_url      = get_post_meta( $post->ID, 'pf_ex_url', true );
		$pf_title       = get_post_meta( $post->ID, 'pf_title', true );
		$pf_description = get_post_meta( $post->ID, 'pf_description', true );

		?>
        <form action="" method="post">
            <div class="container">

                <label for="pf_ex_url"><b>External Url</b></label>
                <input type="text" placeholder="Enter External Url" name="pf_ex_url" id="ex_url"
                       value="<?php esc_attr_e( $pf_ex_url ); ?>" required>

                <label for="psw"><b>Title</b></label>
                <input type="text" placeholder="Enter Title" name="pf_title" id="pf_title"
                       value="<?php esc_attr_e( $pf_title ); ?>" required>

                <label for="psw"><b>Description</b></label>
                <textarea name="pf_description" id="" cols="30" rows="10" placeholder="Write Description">
                    <?php esc_attr_e( $pf_description ); ?>
                </textarea>

                <label for="psw"><b>Thumbnail Image</b></label>
                <input type="file" accept="image/*" id="preview-images" name="preview-images" multiple>

                <span id="preview-container"></span>

				<?php wp_nonce_field( 'pf_meta_box_nonce', 'pf_meta_box_nonce' ); ?>

            </div>

        </form>
		<?php
	} //end function pf_render_metabox


	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function pf_save_metadata( $post_id ) {

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return $post_id;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['pf_meta_box_nonce'] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( 'pf_meta_box_nonce', 'pf_meta_box_nonce' ) ) {
			return $post_id;
		}

		$pf_ex_url      = isset( $_POST['pf_ex_url'] ) ? sanitize_text_field( $_POST['pf_ex_url'] ) : "";
		$pf_title       = isset( $_POST['pf_title'] ) ? sanitize_text_field( $_POST['pf_title'] ) : "";
		$pf_description = isset( $_POST['pf_description'] ) ? sanitize_text_field( $_POST['pf_description'] ) : "";

		update_post_meta( $post_id, 'pf_ex_url', $pf_ex_url );
		update_post_meta( $post_id, 'pf_title', $pf_title );
		update_post_meta( $post_id, 'pf_description', $pf_description );


	} //end pf_save_metadata

	/**
	 * Register all styles
	 */
	public
	function pf_enqueue_styles() {
		wp_enqueue_style( 'pf-admin-css', plugins_url( '/assets/css/wppool-admin.css', __FILE__ ), array(), time(), 'all' );
	}

	/**
	 * Register all scripts
	 */
	public
	function pf_enqueue_scripts() {

		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';

		$current_screen = get_current_screen();


		wp_enqueue_script( 'pf-admin', plugins_url( '/assets/js/pf-admin.js', __FILE__ ), 'jquery', time(), true );
	}


} //end main class

new WP_Portfolio_Admin();
