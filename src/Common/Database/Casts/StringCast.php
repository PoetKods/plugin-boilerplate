<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

class StringCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return string
     */
    public function get() : string {
        return esc_html( (string) $this->value );
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return string
     */
    public function set() {
        return sanitize_text_field( (string) $this->value );
    }
}