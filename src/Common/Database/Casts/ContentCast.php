<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class ContentCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return string
     */
    public function get() : string {
        return (string) $this->value;
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return string
     */
    public function set() {
        return (string) $this->value;
    }
}