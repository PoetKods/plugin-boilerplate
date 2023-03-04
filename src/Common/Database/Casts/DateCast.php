<?php

namespace Pk\Common\Database\Casts;

defined( 'ABSPATH' ) || exit;

use Carbon\Carbon;

class DateCast extends Cast {

    /**
     * Retrieve the value
     *
     * @return Carbon
     */
    public function get() : Carbon {
        return Carbon::parse( $this->value );
    }

    /**
     * Set Value
     *
     * @param string $value
     * @return string
     */
    public function set() : string {
        if ( $this->value instanceof Carbon ) {
            return $this->value->format( insidearm_core()->getDateFormat() );
        }
        
        return Carbon::parse( $this->value )->format( insidearm_core()->getDateFormat() );
    }
}