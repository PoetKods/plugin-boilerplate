<?php

namespace Pk\Common\Database;

use Tightenco\Collect\Support\Collection;
use WP_Query;

defined( 'ABSPATH' ) || exit;

class Paginator {

    /**
     * Model
     *
     * @var string
     */
    protected $model;

    /**
     * Query arguments
     *
     * @var array
     */
    protected $args;

    /**
     * Items
     *
     * @var Collection
     */
    protected $items;

    /**
     * Max num of pages
     *
     * @var int
     */
    protected $max_num_pages;

    /**
     * Posts found on current query
     *
     * @var int
     */
    protected $found_posts;

    /**
     * Current page querying.
     *
     * @var int
     */
    protected $page;

    public function __construct( string $model, array $args = array() ) {
        $this->model = $model;
        $this->args  = $args;
        $this->build();
    }

    /**
     * Build Paginator
     *
     * @return void
     */
    public function build() {
        $instance   = new $this->model;
        $query_args = $instance->parse_arguments( $this->args );
        $query      = new WP_Query( $query_args );

        $this->items         = Collection::make( $query->posts )->map( function ( $post ) {
            $post = new $this->model( (array) $post );
            $post->refresh();

            return $post;
        });
        $this->max_num_pages = $query->max_num_pages;
        $this->found_posts   = $query->found_posts;
        $this->page          = isset( $this->args['paged'] )
            ? $this->args['paged']
            : 1;

        wp_reset_postdata();
    }

    /**
     * Get items found
     *
     * @return Collection
     */
    public function getItems() : Collection {
        return $this->items;
    }

    /**
     * Get max num pages
     *
     * @return integer
     */
    public function getTotalPages() : int {
        return $this->max_num_pages;
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    public function getFoundPosts() : int {
        return $this->found_posts;
    }

    /**
     * Has previos page?
     *
     * @return boolean
     */
    public function hasPreviousPage() : bool {
        return $this->page !== 1;
    }

    /**
     * Has more pages?
     *
     * @return boolean
     */
    public function hasMorePages() : bool {
        return $this->page < $this->max_num_pages;
    }
}