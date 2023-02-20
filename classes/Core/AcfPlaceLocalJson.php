<?php
/**
 * Add support for ACF Pro UI to place and read local json files from block's folder.
 *
 * @package boilerplate
 */

namespace Boilerplate\Core {
	/**
	 * All related methods in one class
	 *
	 * @package Boilerplate\Core
	 */
	class AcfPlaceLocalJson {
		/**
		 * Entry method to enable all features.
		 *
		 * @return void
		 */
		public static function enable() {
			if ( defined( 'ACF' ) ) {
				new self();
			}
		}

		/**
		 * Attach all hooks.
		 *
		 * @return void
		 */
		public function __construct() {
			add_filter( 'acf/settings/load_json', [ $this, 'load_json' ] );
			add_action( 'acf/update_field_group', [ $this, 'update_field_group' ] );

			// Use fs modification time instead of json field.
			if ( WP_DEBUG && is_admin() && acf_get_setting( 'show_admin' ) ) {
				$local_json = acf_get_instance( 'ACF_Local_JSON' );
				// Replace default loaded to load modified from file.
				remove_action( 'acf/include_fields', [ $local_json, 'include_fields' ] );
				add_action( 'acf/include_fields', [ $this, 'include_fields' ] );
			}
		}

		/**
		 * Return paths of all custom blocks.
		 *
		 * @param mixed $paths Default paths.
		 * @return mixed
		 */
		public function load_json( $paths ) {
			unset( $paths[0] );

			foreach ( apply_filters( 'theme_blocks', [] ) as $block ) {
				if ( file_exists( $block . '/fields.json' ) ) {
					$paths[] = $block;
				}
			}

			return $paths;
		}

		/**
		 * Write local json to block's folder.
		 *
		 * @param mixed $field_group Group fields definition.
		 * @return false|void
		 */
		public function update_field_group( $field_group ) {
			$local_json = acf_get_instance( 'ACF_Local_JSON' );

			// Bail early if disabled.
			if ( ! $local_json->is_enabled() ) {
				return false;
			}

			if ( ! isset( $field_group['location'] ) ) {
				return false;
			}

			foreach ( $field_group['location'] as $location_rule ) {
				foreach ( $location_rule as $location_rule2 ) {
					if ( 'block' === $location_rule2['param'] && '==' === $location_rule2['operator'] ) {
						$block_name = $location_rule2['value'];

						$metadata = \WP_Block_Type_Registry::get_instance()->get_registered( $block_name );

						$field_group['fields'] = acf_get_fields( $field_group );
						$path                  = $metadata->path;

						$file = untrailingslashit( $path ) . '/fields.json';
						if ( ! is_writable( $path ) ) {
							return false;
						}

						// Append modified time.
						if ( $field_group['ID'] ) {
							$field_group['modified'] = get_post_modified_time( 'U', true, $field_group['ID'] );
						} else {
							$field_group['modified'] = strtotime( 'now' );
						}

						// Prepare for export.
						$field_group = acf_prepare_field_group_for_export( $field_group );

						// Save and return true if bytes were written.
						file_put_contents( $file, acf_json_encode( $field_group ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
					}
				}
			}
		}

		/**
		 * Includes all local JSON fields and use fs modification time instead of json field.
		 *
		 * @return  void
		 */
		public function include_fields() {
			$local_json = acf_get_instance( 'ACF_Local_JSON' );

			// Bail early if disabled.
			if ( ! $local_json->is_enabled() ) {
				return;
			}

			// Get load paths.
			$files = $local_json->scan_field_groups();
			foreach ( $files as $key => $file ) {
				$json = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

				// Set modified by file modification time.
				$json['modified'] = filemtime( $file );

				$json['local']      = 'json';
				$json['local_file'] = $file;
				acf_add_local_field_group( $json );
			}
		}
	}
}
