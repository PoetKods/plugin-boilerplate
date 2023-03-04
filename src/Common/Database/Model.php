<?php
/**
 * InsideARM Core
 *
 * @package   insidearm-core
 * @author    Logic Cadence <david@logiccadence.com>
 * @copyright 2023 InsideARM Core
 * @license   MIT
 * @link      https://logiccadence.com
 */

declare( strict_types = 1 );

namespace Pk\Common\Database;

use Pk\Common\Abstracts\Base;
use Pk\Common\Database\Casts\ContentCast;
use Pk\Common\Database\Casts\DateCast;
use Pk\Common\Database\Casts\IntegerCast;
use Pk\Common\Database\Casts\StringCast;

abstract class Model extends Base {
    use HasAttributes;
    use InteractsWithWP;

    /**
     * WP Fields
     *
     * @var array
     */
    protected $wp_fields = array(
        'ID'             => 0,
        'post_title'     => '',
        'post_name'      => '',
        'post_content'   => '',
        'post_excerpt'   => '',
        'post_date'      => '',
        'post_author'    => 1,
        'post_status'    => 'publish',
        'post_password'  => '',
        'post_modified'  => '',
        'post_parent'    => 0,
        'menu_order'     => 0,
        'post_mime_type' => ''
    );

    /**
     * Custom WP Casts
     *
     * @var array
     */
    protected $wp_casts = array(
        'ID'             => IntegerCast::class,
        'post_title'     => StringCast::class,
        'post_name'      => StringCast::class,
        'post_content'   => ContentCast::class,
        'post_excerpt'   => StringCast::class,
        'post_author'    => IntegerCast::class,
        'post_date'      => DateCast::class,
        'post_status'    => StringCast::class,
        'post_password'  => StringCast::class,
        'post_modified'  => DateCast::class,
        'post_parent'    => IntegerCast::class,
        'menu_order'     => IntegerCast::class,
        'post_mime_type' => StringCast::class
    );

    /**
     * Original WP Post instance
     *
     * @var WP_Post
     */
    protected $post;

    /**
     * Initialize it
     *
     * @param array $attributes
     */
    public function __construct( $attributes = array() ) {
        parent::__construct();
        
        if ( $attributes ) {
            $this->setProps( $attributes );
        }
    }

    /**
     * Convert data to an array
     *
     * @return array
     */
    public function toArray() : array {
        return array_merge(
            array_map( function ( $item ) {
                return array(
                    $item => $this->getProp( $item ),
                );
            }, $this->wp_fields ),
            array_map( function ( $item ) {
                return array(
                    $item => $this->getProp( $item ),
                );
            }, $this->data ),
        );
    }

    /**
     * Post Type Slug
     *
     * @return string
     */
    abstract public function getPostTypeSlug(): string;
}