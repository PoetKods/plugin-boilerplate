<?php
/**
 * InsideARM Core
 *
 * @package   insidearm-core
 * @author    Logic Cadence <david@logiccadence.com>
 * @copyright 2023 InsideARM Core
 * @license   MIT
 * @link      https://logiccadence.com
 */

declare( strict_types = 1 );

namespace Pk\Config;

use Pk\Common\Traits\Singleton;

/**
 * Plugin data which are used through the plugin, most of them are defined
 * by the root file meta data. The data is being inserted in each class
 * that extends the Base abstract class
 *
 * @see Base
 * @package InsidearmCore\Config
 * @since 1.0.0
 */
final class Settings {
    /**
	 * Singleton trait
	 */
    use Singleton;

    /**
     * Settings
     *
     * @var array<string>
     */
    protected $settings = [];

    /**
     * Settings with its values
     *
     * @var array<string, mixed>
     */
    protected $setting_values = [];

    /**
     * Constructor
     */
    public function __construct() {
        foreach ( $this->settings as $setting ) {
            $this->setting_values[ $setting ] = get_field( $setting, 'option' );
        }
    }

    /**
     * Setting Value
     *
     * @param string $setting
     * @return void
     */
    public function get( string $setting ) {
        if ( ! in_array( $setting, $this->settings ) ) {
            return false;
        }

        return $this->setting_values[ $setting ];
    }
}