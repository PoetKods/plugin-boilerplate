<?php
/**
 * PK
 *
 * @package   pk
 * @author    Logic Cadence <david@poetkods.com>
 * @copyright 2023 PK
 * @license   MIT
 * @link      https://poetkods.com
 */

declare( strict_types = 1 );

namespace PK\App\General\Taxonomies;

use PK\Common\Database\Taxonomy;

/**
 * Class PostTypes
 *
 * @package PK\App\General\Taxonomies
 * @since 1.0.0
 */
class Tag extends Taxonomy {

	/**
	 * Post type data
	 */
	public const TAXONOMY = [
		'id'          => 'post_tag',
        'register_to' => ['post']
	];

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		/**
		 * This general class is always being instantiated as requested in the Bootstrap class
		 *
		 * @see Bootstrap::__construct
		 *
		 * Add plugin code here
		 */
		
	}

    /**
     * Return taxonomy slug
     *
     * @return string
     */
    public static function getTaxonomySlug(): string {
        return self::TAXONOMY['id'];
    }
}