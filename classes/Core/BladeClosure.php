<?php
/**
 * Closure Blade for using it as render function using hooks with ACF support.
 *
 * @package boilerplate
 */

namespace Boilerplate\Core {
	/**
	 * Tiny helper with magic method to get attribute value.
	 *
	 * @package Boilerplate\Core
	 */
	class Attributes {
		/**
		 * Data with attributes.
		 *
		 * @var array
		 */
		private $data = [];

		/**
		 * Creates instance of class
		 *
		 * @param array $data Array with attributes.
		 * @return void
		 */
		public function __construct( $data ) {
			$this->data = $data;
		}

		/**
		 * Returns value by name.
		 *
		 * @param mixed $name Field name.
		 * @return mixed.|string
		 */
		public function __get( $name ) {
			return isset( $this->data[ $name ] ) ? $this->data[ $name ] : '';
		}
	}

	/**
	 * Tiny ACF helper with magic method to get field value.
	 *
	 * @package Boilerplate\Core
	 */
	class Acf { // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound,Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
		/**
		 * Returns value by field name.
		 *
		 * @param mixed $name Field name.
		 * @return mixed.|string
		 */
		public function __get( $name ) {
			return get_field( $name ) ?? acf_get_field( $name )['default_value'];
		}
	}

	/**
	 * Closures render using class to hold predefined path of template.
	 *
	 * @package Boilerplate\Core
	 */
	class BladeClosure { // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound,Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
		/**
		 * Path to template.
		 *
		 * @var string|string[]|null
		 */
		private $template;

		/**
		 * Define and process path.
		 *
		 * @param mixed $path Template path.
		 * @return void
		 */
		public function __construct( $path ) {
			$view_path = BOILERPLATE_PATH;

			$this->template = preg_replace( "|^{$view_path}/|", '', $path );
		}

		/**
		 * Output template result.
		 *
		 * @param mixed $attributes Template variables.
		 * @param mixed $content Content of block.
		 * @param mixed $block Block data.
		 * @return void
		 */
		public function output( $attributes, $content, $block ) {
			echo $this->render( $attributes, $content, $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}


		/**
		 * Compile template result.
		 *
		 * @param mixed $attributes Template variables.
		 * @param mixed $content Content of block.
		 * @param mixed $block Block data.
		 * @return string
		 */
		public function render( $attributes, $content, $block ) {
			if ( function_exists( 'acf_register_block' ) ) {
				$attributes['acf'] = new Acf();
			}

			$attributes['content'] = $content;

			return BladeRenderer::get_instance()->run( $this->template, array_merge( [], $attributes, [ 'attr' => new Attributes( $attributes ) ] ) );
		}
	}
}
