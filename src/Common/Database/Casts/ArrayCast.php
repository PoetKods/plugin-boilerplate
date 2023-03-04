<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class ArrayCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return array
     */
    public function get() : array {
        if ( is_array( $this->value ) ) {
            return $this->value;
        }
        
        $value = unserialize( $this->value );
        return $value ? $value : [];
    }

    /**
     * Set Value
     *
     * @return string
     */
    public function set() {
        if ( ! is_array( $this->value ) ) {
            // throw new InvalidArgumentException(
            //     sprintf(
            //         'Invalid value for %s. Field needs to be an array. %s passed.',
            //         $this->key,
            //         $this->value
            //     )
            // );
            return serialize( array() );
        }
        
        return serialize( $this->value );
    }
}