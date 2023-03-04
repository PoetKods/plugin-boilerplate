<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class FloatCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return int
     */
    public function get() : int {
        return (float) $this->value;
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return integer
     */
    public function set() {
        return (float) $this->value;
    }
}