<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class IntegerCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return int
     */
    public function get() : int {
        return absint( (int) $this->value );
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return integer
     */
    public function set() {
        return absint( (int) $this->value );
    }
}