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

namespace Pk\Common\Abstracts;

use Pk\Config\Plugin;

/**
 * The Base class which can be extended by other classes to load in default methods
 *
 * @package Pk\Common\Abstracts
 * @since 1.0.0
 */
abstract class Base {
	/**
	 * @var array : will be filled with data from the plugin config class
	 * @see Plugin
	 */
	protected $plugin = [];

	/**
	 * Base constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->plugin = Plugin::init();
	}
}
