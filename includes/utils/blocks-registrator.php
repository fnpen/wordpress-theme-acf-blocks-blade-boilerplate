<?php
/**
 * Registers all custom blocks.
 *
 * @package boilerplate
 */

namespace Boilerplate {
	add_action( 'init', __NAMESPACE__ . '\\register_blocks' );

	/**
	 * Registers all block types from closure var.
	 */
	function register_blocks() {
		foreach ( apply_filters( 'theme_blocks', [] ) as $block ) {
			register_block_type( $block );
		}
	}
}
