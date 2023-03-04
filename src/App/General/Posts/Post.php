<?php
/**
 * Post
 *
 * @package   insidearm-core
 * @author    Logic Cadence <david@logiccadence.com>
 * @copyright 2023 InsideARM Core
 * @license   MIT
 * @link      https://logiccadence.com
 */

declare( strict_types = 1 );

namespace Pk\App\General\Posts;

use PK\App\General\Taxonomies\Category;
use Pk\App\General\Taxonomies\Tag;
use Pk\Common\Database\Model;
use Tightenco\Collect\Support\Collection;

/**
 * Class PostTypes
 *
 * @package InsidearmCore\App\General
 * @since 1.0.0
 */
class Post extends Model {
    /**
	 * Post type data
	 */
	public const POST_TYPE = [
		'id' => 'post',
	];

    /**
     * Custom Data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Undocumented variable
     *
     * @var Collection|null
     */
    protected $_tags = null;

    /**
     * Undocumented variable
     *
     * @var Collection|null
     */
    protected $_categories = null;

    /**
     * Field casting
     *
     * @var array
     */
    protected $casts = array();

    /**
     * Get Tags
     *
     * @return Collection
     */
    public function getTags(): Collection {
        if ( is_null( $this->_tags ) ) {
            $this->_tags = Tag::getForObject( $this );
        }
        
        return $this->_tags;
    }

    /**
     * Get Tags
     *
     * @return Collection
     */
    public function getCategories(): Collection {
        if ( is_null( $this->_categories ) ) {
            $this->_categories = Category::getForObject( $this );
        }
        
        return $this->_categories;
    }

    /**
     * Get Post Excerpt
     *
     * @param integer $num_words
     * @param string $more
     * @return void
     */
    public function getExcerpt( $num_words = 30, $more = '' ) {
        $excerpt = $this->post_excerpt;
        
        if ( ! $excerpt ) {
            $excerpt = wp_strip_all_tags( $this->post_content );
        }

        return wp_trim_words( esc_html( $excerpt ), $num_words, $more );
    }

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
        add_filter('acf/fields/post_object/query', [ $this, 'acfPostQuery' ], 10, 3);
	}

    /**
     * ACF Post query
     *
     * @param [type] $args
     * @param [type] $field
     * @param [type] $post_id
     * @return void
     */
    public function acfPostQuery( $args, $field, $post_id ) {
        $args['posts_per_page'] = 40;
        $args['orderby']        = 'date';
        $args['order']          = 'DESC';

        return $args;
    }

    /**
     * Post Type Slug
     *
     * @return string
     */
    public function getPostTypeSlug(): string {
        return $this::POST_TYPE['id'];
    }
}