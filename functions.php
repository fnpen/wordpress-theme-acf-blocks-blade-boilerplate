<?php
/**
 * Theme's function.php main file.
 *
 * @package boilerplate
 */

namespace Boilerplate;

use Boilerplate\Core\AcfPlaceLocalJson;
use Boilerplate\Core\BladeRenderer;
use WP_Block_Type_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BOILERPLATE_PATH', realpath( get_template_directory() ) );
define( 'BOILERPLATE_URL', get_template_directory_uri() );
define( 'BOILERPLATE_THEME_FILE', __FILE__ );
define( 'BOILERPLATE_VERSION', '0.0.1' );

require BOILERPLATE_PATH . '/vendor/autoload.php';
require BOILERPLATE_PATH . '/includes/utils/closure-variable.php';
require BOILERPLATE_PATH . '/includes/utils/enqueue-scripts-from-asset-file.php';
require BOILERPLATE_PATH . '/includes/utils/blocks-registrator.php';

{
	BladeRenderer::register_renderer();
	AcfPlaceLocalJson::enable();
}

// Put blocks to closure.
add_filter( 'theme_blocks', closure_variable( require BOILERPLATE_PATH . '/blocks/blocks-list.php' ) );

add_filter(
	'acf/settings/show_admin',
	function( $show ) {
		return current_user_can( 'manage_options' ) && WP_DEBUG ? $show : false;
	},
	1000
);


add_action(
	'after_setup_theme',
	function() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'build/theme.css' );
	}
);


add_action(
	'wp_enqueue_scripts',
	function() {
		enqueue_scripts_from_asset_file( 'theme', BOILERPLATE_THEME_FILE );
	}
);

/**
 * BUG: https://github.com/WordPress/wordpress-develop/pull/2494
 * Does not include corretly assets when we use composer/installers and symlinks
 */
add_filter(
	'theme_file_path',
	function( $path ) {
		return realpath( $path );
	},
	100,
	1
);


add_filter(
	'render_block_data',
	function( $parsed_block ) {
		$block_type = WP_Block_Type_Registry::get_instance()->get_registered(
			$parsed_block['blockName']
		);

		// if ( $block_type ) {
		// 	$parsed_block['attrs'] = $block_type->prepare_attributes_for_render( $parsed_block['attrs'] );
		// }

		if ( ! empty( $parsed_block['innerBlocks'] ) ) {
			foreach ( $parsed_block['innerBlocks'] as $index => &$inner_block ) {
				$inner_block['attrs']['index'] = $index;
			}
		}

		return $parsed_block;
	},
	100,
	1
);
