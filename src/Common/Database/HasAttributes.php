<?php

namespace Pk\Common\Database;

use Exception;
use Pk\Common\Database\Casts\StringCast;

defined( 'ABSPATH' ) || exit;

trait HasAttributes {

    /**
     * Custom Data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Required fields.
     * 
     * Helpful for validations
     *
     * @var array
     */
    protected $required_fields = array();

    /**
     * Field casting
     *
     * @var array
     */
    protected $casts = array();

    /**
     * Fill Attributes
     *
     * @param array $attributes
     * @return self
     */
    public function fill( array $attributes = array() ) : self {
        $this->setProps( $attributes );

        return $this;
    }

    /**
     * Set Properties to instance
     *
     * @param array $attributes
     * @return void
     */
    protected function setProps( array $attributes = array() ) {
        foreach ( $attributes as $key => $value ) {
            $this->setProp( $key, $value );
        }
    }

    /**
     * Get a prop value
     *
     * @param string $prop
     * @return mixed
     */
    public function getProp( string $prop ) {
        if ( $this->isNotValidProp( $prop ) ) {
            return null;
        }
        
        $cast  = $this->getPropCast( $prop );
        $value = $this->getPropValue( $prop );

        return $cast::make( $prop, $value )->get();
    }

    /**
     * Get value to store casted
     *
     * @param string $prop
     * @param mixed $value
     * @return void
     */
    public function valueToStore( string $prop, $value ) {
        $cast = $this->getPropCast( $prop );

        return $cast::make( $prop, $value )->set();
    }

    /**
     * Set a property
     *
     * @param string $prop
     * @param mixed $value
     * @return void
     */
    public function setProp( string $prop, $value ) {
        if ( $this->isWpProp( $prop ) ) {
            $this->wp_fields[ $prop ] = $value;
        } else if ( $this->isProp( $prop ) ) {
            $this->data[ $prop ] = $value;
        }
    }

    /**
     * Has the prop a cast?
     *
     * @param string $prop
     * @return bool
     */
    protected function propHasCast( string $prop ) {
        return in_array( $prop, array_keys( $this->casts ) );
    }

    /**
     * Get a prop cast
     *
     * @param string $prop
     * @return Cast
     */
    protected function getPropCast( string $prop ) {
        if ( $this->isWpProp( $prop ) ) {
            return $this->wp_casts[ $prop ];
        }
        
        return $this->propHasCast( $prop )
            ? $this->casts[ $prop ]
            : StringCast::class;
    }

    /**
     * REturn the property value
     *
     * @param string $prop
     * @return mixed
     */
    protected function getPropValue( string $prop ) {
        return $this->isWpProp( $prop )
            ? $this->wp_fields[ $prop ]
            : $this->data[ $prop ];
    }

    /**
     * Is The prop a valid prop?
     *
     * @param string $prop
     * @return boolean
     */
    public function isProp( string $prop ) {
        return in_array( $prop, array_keys( $this->data ) );
    }

    /**
     * Is Wp Props?
     *
     * @param string $prop
     * @return boolean
     */
    protected function isWpProp( string $prop ) {
        return in_array( $prop, array_keys( $this->wp_fields ) );
    }

    /**
     * Is not this prop a valid property?
     *
     * @param string $prop
     * @return boolean
     */
    protected function isNotProp( string $prop ) {
        return ! $this->isProp( $prop );
    }

    /**
     * Is the prop a valid prop?
     *
     * @param string $prop
     * @return boolean
     */
    protected function isValidProp( string $prop ) {
        return $this->isWpProp( $prop ) || $this->isProp( $prop );
    }

    /**
     * Is not a valid prop?
     *
     * @param string $prop
     * @return boolean
     */
    protected function isNotValidProp( string $prop ) {
        return ! $this->isValidProp( $prop );
    }

    /**
     * Get a property magically
     *
     * @param string $key
     * @return mixed
     */
    public function __get( string $key ) {
        return $this->getProp( $key );
    }

    /**
     * Set A value magically
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set( string $key, $value ) {
        $this->setProp( $key, $value );
    }

    /**
     * Validate props
     *
     * @return void
     */
    public function validateProps() {
        if ( ! $this->required_fields ) {
            return;
        }

        $invalid_fields = array();
        foreach ( $this->required_fields as $field ) {
            if ( $this->isWpProp( $field ) && ! $this->wp_fields[ $field ] ) {
                $invalid_fields[] = $field;
            }

            if ( $this->isProp( $field ) && ! $this->data[ $field ] ) {
                $invalid_fields[] = $field;
            }
        }

        if ( $invalid_fields ) {
            throw new Exception(
                sprintf(
                    'Fields are required: %s',
                    implode( ',', $invalid_fields )
                )
            );
        }
    }
}