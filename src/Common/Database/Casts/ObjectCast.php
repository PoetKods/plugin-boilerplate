<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class ObjectCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return int
     */
    public function get() : int {
        return (object) unserialize( $this->value );
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return integer
     */
    public function set() {
        return serialize( (array) $this->value );
    }
}