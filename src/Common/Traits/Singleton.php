<?php
/**
 * PK
 *
 * @package   pk
 * @author    PoetKods <david@poetkods.com>
 * @copyright 2023 PK
 * @license   MIT
 * @link      https://poetkods.com
 */

declare( strict_types = 1 );

namespace Pk\Common\Traits;

/**
 * The singleton skeleton trait to instantiate the class only once
 *
 * @package Pk\Common\Traits
 * @since 1.0.0
 */
trait Singleton {
	private static $instance;

	final private function __construct() {
	}

	final private function __clone() {
	}

	final private function __wakeup() {
	}

	/**
	 * @return self
	 * @since 1.0.0
	 */
	final public static function init(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
