<?php
/**
 * Extends Blade class to support core WordPress functionality.
 *
 * @package boilerplate
 */

namespace Boilerplate\Core {
	use eftec\bladeone\BladeOne;

	/**
	 * Extend BladeOne class to override some features.
	 *
	 * @package Boilerplate\Core
	 */
	class BladeWp extends BladeOne {
		/**
		 * The "regular" / legacy echo string format.
		 *
		 * @var string
		 */
		protected $echoFormat = '\esc_html(%s??\'\')'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	}
}
