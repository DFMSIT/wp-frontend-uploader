<?php
/**
 * WP Frontend Uploader
 *
 * Server Side Frontend Render
 *
 * @package TECstock
 * @author Kaleb Heitzman <kalebheitzman@gmail.com>
 * @since 1.0.0
 */

/**
 * Register Gutenberg block on server-side.
 *
 * Register the block on server-side to ensure that the block
 * scripts and styles for both frontend and backend are
 * enqueued when the editor loads.
 *
 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
 * @since 1.16.0
 */
function wp_frontend_uploader_scripts() {

	global $post;
	if ( has_block( 'kh/wp-frontend-uploader', $post ) ) {

		// get the media script and register it
		$script = plugin_dir_url( __FILE__ ) . 'uploader.js';
		wp_register_script(
			'wp-frontend-uploader-js',
			$script,
			array( 'wp-api' ),
			false,
			'1.0.0',
			true
		);

		// get attributes to localize.
		$attributes = $block['attrs'];
		$js_vars = array(
			'endpoint'   => esc_url_raw( rest_url( '/wp/v2/media/' ) ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'attributes' => $attributes,
			'media'      => [],
			'progress'   => 0,
		);

		// localize needed vars to script
		wp_localize_script(
			'wp-frontend-uploader-js',
			'wpFrontendUploader',
			$js_vars
		);

		// enqueue the media grid script
		wp_enqueue_script( 'wp-frontend-uploader-js' );

	}
}

// initialize render scripts
add_action( 'wp_enqueue_scripts', 'wp_frontend_uploader_scripts' );

/**
 * TECstock Media Grid
 *
 * @param array $attributes
 * @param string $content
 * @return string HTML content.
 */
function wp_frontend_uploader_block( $attributes ) {

	$output = '';
	ob_start(); ?>
		<div id="wp-frontend-uploader-progress-bar">
			<div class="wrapper">

				<div class="progress-bar">
					<div class="progress-bar-fill" data-element="progress-bar" style="width: 0%;">
						<div class="progress-status" data-element="progress-status">0%</div>
					</div>
				</div>

				<button disabled data-element="submit-button">Upload</button>

			</div>
		</div>
		<div class="<?php echo $attributes['className']; ?>">

			<div id="wp-frontend-uploader">
				<form>
					<div class="icon">
						<i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
					</div>
					<p>Drop files here or click to upload.</p>
					<input
						type="file"
						id="fileElem"
						multiple
						accept="image/*"
						onchange="handleFiles(this.files)"
					/>
					<label class="button" for="fileElem">Select some files</label>
				</form>
			</div><!--#wp-frontend-uploader-->

			<div id="wp-frontend-uploader-edit" class="hide">

			</div><!--#wp-frontend-uploader-edit-->

		</div>
	<?php

	$output .= ob_get_clean();

	return $output;
}

/**
 * Register TECstock Media Grid Block
 *
 * @return void
 */
function register_wp_frontend_uploader_block() {

	register_block_type(
		'kh/wp-frontend-uploader',
		array(
			'render_callback' => 'wp_frontend_uploader_block'
		)
	);
}

// initialize render php
add_action( 'init', 'register_wp_frontend_uploader_block' );
