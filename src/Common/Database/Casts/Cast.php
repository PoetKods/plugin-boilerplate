<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class Cast {

    /**
     * Cast Value
     *
     * @var string
     */
    protected $value;

    protected $key;

    /**
     * Initialize Cast
     *
     * @param mixed $value
     */
    public function __construct( $key, $value ) {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Retrieve the value
     *
     * @return mixed
     */
    public function get() {
        return $this->value;
    }

    /**
     * Set Value
     *     
     * @return mixed
     */
    public function set() {
        return $this->value;
    }

    /**
     * Get Value
     *
     * @return mixed
     */
    public static function make( $key, $value ) {
        $cast = new static( $key, $value );

        return $cast;
    }
}