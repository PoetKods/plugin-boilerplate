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

namespace Pk\App\General;

use Pk\Common\Abstracts\Base;

/**
 * Class PostTypes
 *
 * @package Pk\App\General
 * @since 1.0.0
 */
class PostTypes extends Base {

	/**
	 * Post type data
	 */
	public const POST_TYPE = [
		'id'       => 'example-post-type',
		'archive'  => 'example-post-types',
		'title'    => 'Example Posts',
		'singular' => 'Example Post',
		'icon'     => 'dashicons-format-chat',
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
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Register post type
	 *
	 * @since 1.0.0
	 */
	public function register() {
		register_post_type( $this::POST_TYPE['id'],
			[
				'labels'             => [
					'name'           => $this::POST_TYPE['title'],
					'singular_name'  => $this::POST_TYPE['singular'],
					'menu_name'      => $this::POST_TYPE['title'],
					'name_admin_bar' => $this::POST_TYPE['singular'],
					'add_new'        => sprintf( /* translators: %s: post type singular title */ __( 'New %s', 'pk' ), $this::POST_TYPE['singular'] ),
					'add_new_item'   => sprintf( /* translators: %s: post type singular title */ __( 'Add New %s', 'pk' ), $this::POST_TYPE['singular'] ),
					'new_item'       => sprintf( /* translators: %s: post type singular title */ __( 'New %s', 'pk' ), $this::POST_TYPE['singular'] ),
					'edit_item'      => sprintf( /* translators: %s: post type singular title */ __( 'Edit %s', 'pk' ), $this::POST_TYPE['singular'] ),
					'view_item'      => sprintf( /* translators: %s: post type singular title */ __( 'View %s', 'pk' ), $this::POST_TYPE['singular'] ),
					'all_items'      => sprintf( /* translators: %s: post type title */ __( 'All %s', 'pk' ), $this::POST_TYPE['title'] ),
					'search_items'   => sprintf( /* translators: %s: post type title */ __( 'Search %s', 'pk' ), $this::POST_TYPE['title'] ),
				],
				'public'             => true,
				'publicly_queryable' => true,
				'has_archive'        => $this::POST_TYPE['archive'],
				'show_ui'            => true,
				'rewrite'            => [
					'slug'       => $this::POST_TYPE['archive'],
					'with_front' => true,
				],
				'show_in_menu'       => true,
				'query_var'          => true,
				'capability_type'    => 'post',
				'menu_icon'          => $this::POST_TYPE['icon'],
				'supports'           => [ 'title', 'editor', 'thumbnail' ],
			]
		);
	}
}
