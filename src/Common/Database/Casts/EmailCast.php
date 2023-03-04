<?php

namespace Pk\Common\Database\Casts;

use InvalidArgumentException;

defined( 'ABSPATH' ) || exit;

class EmailCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return int
     */
    public function get() : string {
        return (string) $this->value;
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return integer
     */
    public function set() {
        if ( ! filter_var( $this->value, FILTER_VALIDATE_EMAIL ) ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid value. Field needs to be a valid email. %s passed.',
                    $this->value
                )
            );
        }
        
        return sanitize_email( $this->value );
    }
}