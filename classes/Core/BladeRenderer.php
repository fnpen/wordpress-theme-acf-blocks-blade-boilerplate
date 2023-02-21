<?php
/**
 * Add support to use Blade engine instead of php automatically and custom helpers.
 *
 * @package boilerplate
 */

namespace Boilerplate\Core {
	use Boilerplate\Core\BladeClosure;
	use eftec\bladeone\BladeOne;
	use Exception;

	/**
	 * Injects support for Blade templates.
	 *
	 * @package Boilerplate\Core
	 */
	class BladeRenderer {
		/**
		 * Instance of class.
		 *
		 * @var mixed
		 */
		private static $instance;

		/**
		 * Instance of Blade renderer.
		 *
		 * @var BladeWp
		 */
		private $blade;

		/**
		 * Current attributes.
		 *
		 * @var array
		 */
		private $current = [];

		/**
		 * Adds support for blade files.
		 */
		public static function register_renderer() {
			add_filter( 'block_type_metadata', __NAMESPACE__ . '\\block_type_metadata', 101, 1 );
			add_filter( 'block_type_metadata_settings', __NAMESPACE__ . '\\block_type_metadata_settings', 100, 2 );

			/**
			 * Add support for PHP SSR block templates.
			 *
			 * @param array $settings Block settings.
			 * @param array $metadata Block meta data.
			 * @return array
			 */
			function block_type_metadata_settings( $settings, $metadata ) {
				if ( isset( $metadata['render'] ) && preg_match( '/\.blade\.php$/', $metadata['render'] ) ) {
					$settings['render_callback'] = [ new BladeClosure( dirname( $metadata['file'] ) . '/' . $metadata['render'] ), 'render' ];
				}

				return $settings;
			}

			/**
			 * Add support for ACF-based SSR block templates.
			 *
			 * @param array $metadata Block meta data.
			 * @return array
			 */
			function block_type_metadata( $metadata ) {
				if ( ! function_exists( 'acf_is_acf_block_json' ) || ! acf_is_acf_block_json( $metadata ) ) {
					return $metadata;
				}

				if ( isset( $metadata['acf']['renderTemplate'] ) && preg_match( '/\.blade\.php$/', $metadata['acf']['renderTemplate'] ) ) {
					$metadata['acf']['renderCallback'] = [ new BladeClosure( dirname( $metadata['file'] ) . '/' . $metadata['acf']['renderTemplate'] ), 'output' ];
					unset( $metadata['acf']['renderTemplate'] );
				}

				return $metadata;
			}
		}

		/**
		 * Returns singleton instance.
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * If the given value is not an array and not null, wrap it in one.
		 * https://github.com/laravel/framework/blob/6b782b0f94a285b275eba92076227f46d6a3c66a/src/Illuminate/Collections/Arr.php#L860
		 *
		 * @param  mixed $value Array or value.
		 * @return array
		 */
		public static function wrap( $value ) {
			if ( is_null( $value ) ) {
				return [];
			}

			return is_array( $value ) ? $value : [ $value ];
		}

		/**
		 * Conditionally compile classes from an array into a CSS class list.
		 * https://github.com/laravel/framework/blob/6b782b0f94a285b275eba92076227f46d6a3c66a/src/Illuminate/Collections/Arr.php#L764
		 *
		 * @param  array $array Array of classes with conditions.
		 * @return string
		 */
		public static function to_css_classes( $array ) {
			$class_list = static::wrap( $array );

			$classes = [];

			foreach ( $class_list as $class => $constraint ) {
				if ( is_numeric( $class ) ) {
					$classes[] = $constraint;
				} elseif ( $constraint ) {
					$classes[] = $class;
				}
			}

			return implode( ' ', $classes );
		}

		/**
		 * Outputs all attributes from supported features and defined classes.
		 *
		 * @param array $classes Array of additional classes with conditions.
		 * @return void
		 */
		public function echo_wrapper_attributes( $classes = [] ) {
			$classes = static::to_css_classes( $classes );

			$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $classes ] );

			/* // phpcs:ignore Squiz.PHP.CommentedOutCode.Found,Squiz.Commenting.InlineComment.InvalidEndChar
			if( ! empty($this->current['id'] ) ) {
				echo ' id="' . esc_attr($this->current['id']) . '"';
			}
			*/

			echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output class attribute string using conditions.
		 *
		 * @param array $array Array of classes with conditions.
		 * @return void
		 */
		public function echo_classes( $array ) {
			$classes = static::to_css_classes( $array );
			if ( ! empty( $classes ) ) {
				echo ' class="' . esc_attr( $classes ) . '" '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Converts inner blocks structure to template.
		 *
		 * @param array $inner_block Inner block structure.
		 * @return array
		 */
		public function example_to_template( $inner_block ) {
			return [ $inner_block['name'], $inner_block['attributes'] ];
		}

		/**
		 * Return <InnerBlocks/> markup to support inner blocks with allowed blocks.
		 *
		 * @param bool                  $allowed_blocks List of allowed blocks.
		 * @param array|'example'|false $template Template array or 'example' to use example from metadata as template.
		 * @param string                $orientation Orientation of layout.
		 * @return void
		 */
		public function inner_blocks( $allowed_blocks = false, $template = false, $orientation = 'vertical' ) {
			$attrs = [];

			if ( ! is_admin() ) {
				echo '<InnerBlocks />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}

			if ( $allowed_blocks && is_array( $allowed_blocks ) ) {
				$attrs[] = 'allowedBlocks="' . esc_attr( wp_json_encode( $allowed_blocks ) ) . '"';
			}

			if ( $template ) {
				if ( 'example' === $template && ! empty( $this->current['example'] ) && ! empty( $this->current['example']['innerBlocks'] ) ) {
					$template = array_map( [ $this, 'example_to_template' ], $this->current['example']['innerBlocks'] );
				}
				if ( is_array( $template ) ) {
					$attrs[] = 'template="' . esc_attr( wp_json_encode( $template ) ) . '"';
				}
			}

			$attrs[] = 'orientation="' . esc_attr( $orientation ) . '"';

			$attrs = implode( ' ', $attrs );

			echo "<InnerBlocks {$attrs} />"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Initialize Blade renderer.
		 *
		 * @return void
		 */
		public function __construct() {
			$cache = WP_CONTENT_DIR . '/cache/blade/';

			wp_mkdir_p( $cache );

			$blade             = new BladeWp( BOILERPLATE_PATH, $cache, ! WP_DEBUG ? BladeOne::MODE_AUTO : BladeOne::MODE_DEBUG );
			$blade->pipeEnable = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			$blade->directiveRT( 'wrapperAttributes', [ $this, 'echo_wrapper_attributes' ] );
			$blade->directiveRT( 'class', [ $this, 'echo_classes' ] );
			$blade->directiveRT( 'innerBlocks', [ $this, 'inner_blocks' ] );

			$this->blade = $blade;
		}

		/**
		 * Do render template with params using Blade.
		 *
		 * @param string $template Relative path to template file.
		 * @param array  $variables Variables array.
		 * @return string
		 * @throws Exception Rendering error.
		 */
		public function run( $template, $variables ) {
			$this->current = $variables;

			return $this->blade->run( $template, $variables );
		}
	}
}
