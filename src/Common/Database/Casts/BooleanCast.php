<?php

namespace Pk\Common\Database\Casts;

use InvalidArgumentException;

defined( 'ABSPATH' ) || exit;

class BooleanCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return bool
     */
    public function get() : bool {
        return '1' === $this->value;
    }

    /**
     * Set Value
     *
     * @return string
     */
    public function set() {
        if ( 'string' === gettype( $this->value ) ) {
            $this->value = '1' === $this->value;
        }
        
        if ( 'boolean' !== gettype( $this->value ) ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value for %s. Field needs to be boolean. %s passed.',
                    $this->key,
                    $this->value
                )
            );
        }
        
        return $this->value ? '1' : '0';
    }
}