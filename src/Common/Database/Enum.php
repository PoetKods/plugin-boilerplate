<?php

namespace Pk\Common\Database;

use Exception;
use Pk\Common\Abstracts\Base;

defined( 'ABSPATH' ) || exit;

abstract class Enum extends Base {

    /**
     * Selected option
     *
     * @var mixed
     */
    protected $selectedOption;

    /**
     * Optiones
     *
     * @var array<string, string>
     */
    protected $options = [];

    /**
     * Construct
     *
     * @param mixed $selectedOption
     */
    public function __construct( $selectedOption = null ) {
        parent::__construct();

        $this->selectedOption = ! is_null( $selectedOption )
            ? $selectedOption
            : $this->defaultOption();
    }

    /**
     * Add this options to the ACF
     *
     * @param array $field
     * @return array
     */
    public function addChoicesToACF( $field ) {
        $field['choices']       = $this->options;
        $field['default_value'] = $this->defaultOption();

        return $field;
    }

    /**
     * Validate if is valid
     *
     * @return boolean
     */
    public function validate() {
        if ( ! array_key_exists( $this->selectedOption, $this->options ) ) {
            throw new Exception(
                sprintf(
                    'Option %s is not a valid option for %s',
                    $this->selectedOption, get_class( $this )
                )
            );
        }

        return true;
    }

    /**
     * Default Option
     *
     * @return string
     */
    abstract function defaultOption(): string;

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Get Label for a given value
     *
     * @param string $value
     * @return string
     */
    public function getLabelFor( string $value ) : string {
        if ( array_key_exists( $value, $this->options ) ) {
            return $this->options[ $value ];
        }

        return $this->options[ $this::getDefaultOption() ];
    }

    /**
     * Return default option
     *
     * @return string
     */
    public static function getDefaultOption(): string {
        return (new static)->defaultOption();
    }

    public static function getLabel( string $value ) {
        return (new static)->getLabelFor( $value );
    }
}