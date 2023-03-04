<?php
/**
 * Query Functions
 * 
 * @package pk
 */

use Pk\App\General\Posts\Post;
use Tightenco\Collect\Support\Collection;

if ( ! function_exists( 'pk_get_post' ) ) {
    /**
     * Retrieve a post
     *
     * @param integer|string|WP_Post $post
     * @return Post|null
     */
    function pk_get_post( $post = 0 ) {
        $post = Post::find( $post );

        return $post;
    }
}

if ( ! function_exists( 'pk_get_posts' ) ) {
    /**
     * Retrieve posts
     *
     * @param array
     * @return Collection
     */
    function pk_get_posts( $args = [] ) : Collection {
        return Post::where( $args );
    }
}