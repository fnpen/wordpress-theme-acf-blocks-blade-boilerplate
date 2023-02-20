<?php
/**
 * Closures variable to function, which we can get calling the function.
 *
 * @package boilerplate
 */

namespace Boilerplate {
	use Closure;

	/**
	 * Closures any variable to method.
	 *
	 * @param mixed $var Variable to closure.
	 * @return Closure(): mixed
	 */
	function closure_variable( $var ) {
		return function() use ( $var ) {
			return $var;
		};
	}
}
