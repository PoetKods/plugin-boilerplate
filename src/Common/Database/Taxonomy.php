<?php

namespace Pk\Common\Database;

use Exception;
use Pk\Common\Abstracts\Base;
use Pk\Common\Database\Casts\IntegerCast;
use Pk\Common\Database\Casts\StringCast;
use Tightenco\Collect\Support\Collection;

defined( 'ABSPATH' ) || exit;

abstract class Taxonomy extends Base {
    use HasAttributes;

    /**
     * Wp User Instance
     *
     * @var WP_Taxonomy|null
     */
    public $wp_taxonomy = null;

    /**
     * Core fields
     *
     * @var array<string, mixed>
     */
    protected $wp_fields = [
        'term_id'          => 0,
        'name'             => '',
        'slug'             => '',
        'term_group'       => 0,
        'term_taxonomy_id' => 0,
        'taxonomy'         => '',
        'description'      => '',
        'parent'           => 0,
        'count'            => 0,
        'filter'           => 'raw',
    ];

    /**
     * Custom WP Casts
     *
     * @var array
     */
    protected $wp_casts = array(
        'term_id'          => IntegerCast::class,
        'name'             => StringCast::class,
        'slug'             => StringCast::class,
        'term_group'       => IntegerCast::class,
        'term_taxonomy_id' => IntegerCast::class,
        'taxonomy'         => StringCast::class,
        'description'      => StringCast::class,
        'parent'           => IntegerCast::class,
        'count'            => IntegerCast::class,
        'filter'           => StringCast::class,
    );
    
    /**
     * Custom Fields
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * User Constructor
     *
     * @param array $attributes
     */
    public function __construct( array $attributes = [] ) {
        parent::__construct();

        $this->fill( $attributes );
    }

    /**
     * Return Taxonomy Slug
     *
     * @return string
     */
    abstract static public function getTaxonomySlug(): string;
    
    /**
     * Find a taxonomy by slug
     *
     * @param string $slug
     * @return self|false
     */
    public static function getBySlug( string $slug ) {
        return static::find( $slug, 'slug' );
    }

    /**
     * Find a Term or Taxonomy
     *
     * @param string $value
     * @param string $field
     * @return self|false
     */
    public static function find( string $value, string $field = 'id' ) {
        $taxonomy = get_term_by( $field, $value, self::getTaxonomySlug(), ARRAY_A );

        if ( ! $taxonomy ) {
            return false;
        }

        return new static( $taxonomy );
    }

    /**
     * GEt terms for an object
     *
     * @param Model $object
     * @return Collection
     */
    public static function getForObject( $object ) : Collection {
        $elements = get_the_terms( $object->ID, static::getTaxonomySlug() );

        return Collection::make( $elements )->map( function ( $term ) {
            $term = new static( (array) $term );
            return $term;
        } );
    }

    /**
     * Create a new taxonomy
     *
     * @param array $attributes
     * @return self
     */
    public static function create( array $attributes ) {
        $taxonomy = new static( $attributes );
        $taxonomy->save();

        return $taxonomy;
    }

    public function getPermalink(): string {
        $permalink = get_term_link( $this->term_id, $this::getTaxonomySlug() );

        if ( is_wp_error( $permalink ) ) {
            return "";
        }

        return $permalink;
    }
    
    /**
     * Save the term
     *
     * @return self
     */
    public function save() {
        $this->performSave();

        return $this;
    }

    /**
     * Perform save
     *
     * @return self
     */
    protected function performSave() : self {
        foreach ( $this->wp_fields as $key => $value ) {
            $args[ $key ] = $this->valueToStore( $key, $value );
        }

        $term = $this->term_id === 0
            ? wp_insert_term( $args['name'], self::getTaxonomySlug(), $args )
            : wp_update_term( $this->term_id, self::getTaxonomySlug(), $args );

        if ( is_wp_error( $term ) ) {
            throw new Exception(
                sprintf(
                    'Error creating or updating a %s. %s',
                    get_class( $this ),
                    $term->get_error_message()
                )
            );
        }

        // Set the post id if it's zero.
        // It means that the action is creating.
        if ( $args['term_id'] === 0 ) {
            $this->setProp( 'term_id', $term['term_id'] );
        }

        if ( $this->data ) {
            foreach ( $this->data as $key => $value ) {
                $value = $this->valueToStore( $key, $value );
                update_term_meta( $this->term_id, $key, $value );
            }
        }

        return $this;
    }
}