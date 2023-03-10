<?php
/**
 * Enqueue assets using *.asset.php file.
 *
 * @package boilerplate
 */

namespace Boilerplate {
	/**
	 * Enqueue file by wp-scripts entry name.
	 *
	 * @param mixed $name Name of entry.
	 * @param mixed $plugin_file Path to folder or file.
	 * @param bool  $only_styles Output only style.
	 *
	 * @return void
	 */
	function enqueue_scripts_from_asset_file( $name, $plugin_file, $only_styles = false ) {
		$script_asset_path = dirname( $plugin_file ) . "/build/$name.asset.php";
		if ( file_exists( $script_asset_path ) ) {
			$script_asset        = include $script_asset_path;
			$script_dependencies = $script_asset['dependencies'] ?? [];

			if ( ! $only_styles ) {
				if ( in_array( 'wp-media-utils', $script_dependencies, true ) ) {
					wp_enqueue_media();
				}

				if ( in_array( 'wp-react-refresh-runtime', $script_asset['dependencies'], true ) && ! constant( 'SCRIPT_DEBUG' ) ) {
					wp_die( esc_html__( 'SCRIPT_DEBUG should be true. You use `hot` mode, it requires `wp-react-refresh-runtime` which availably only when SCRIPT_DEBUG is enabled.', 'wp-modern-settings-page-boilerplate' ) );
				}

				wp_enqueue_script( "wp-modern-settings-page-boilerplate-$name", get_template_directory_uri() . "/build/$name.js", $script_dependencies, $script_asset['version'], true );
			}

			$style_dependencies = [];

			if ( in_array( 'wp-components', $script_dependencies, true ) ) {
				$style_dependencies[] = 'wp-components';
			}

			wp_enqueue_style( "wp-modern-settings-page-boilerplate-$name", get_template_directory_uri() . "/build/$name.css", $style_dependencies, $script_asset['version'], 'all' );
		}
	}
}
