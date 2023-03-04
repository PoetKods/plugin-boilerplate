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

namespace Pk\Common\Database\User;

use Exception;
use Pk\Common\Abstracts\Base;
use Pk\Common\Database\HasAttributes;
use Pk\Common\Database\Casts\DateCast;
use Pk\Common\Database\Casts\IntegerCast;
use Pk\Common\Database\Casts\StringCast;
use Pk\Common\Database\Casts\EmailCast;
use Pk\Common\Database\Casts\PasswordCast;
use WP_User;

abstract class BaseUser extends Base {
    use HasAttributes;

    /**
     * Wp User Instance
     *
     * @var WP_User|null
     */
    public $wp_user = null;

    /**
     * WP Fields
     *
     * @var array
     */
    protected $wp_fields = array(
        'ID'                  => 0,
        'user_login'          => '',
        'user_pass'           => '',
        'user_nicename'       => '',
        'user_email'          => '',
        'user_url'            => '',
        'user_registered'     => '',
        'user_activation_key' => '',
        'user_status'         => '',
        'display_name'        => 0,
        'spam'                => 0,
        'deleted'             => ''
    );

    /**
     * Custom WP Casts
     *
     * @var array
     */
    protected $wp_casts = array(
        'ID'                  => IntegerCast::class,
        'user_login'          => StringCast::class,
        'user_pass'           => PasswordCast::class,
        'user_nicename'       => StringCast::class,
        'user_email'          => EmailCast::class,
        'user_url'            => StringCast::class,
        'user_registered'     => DateCast::class,
        'user_activation_key' => StringCast::class,
        'user_status'         => IntegerCast::class,
        'display_name'        => StringCast::class,
        'spam'                => IntegerCast::class,
        'deleted'             => IntegerCast::class,
    );

    /**
     * User Constructor
     *
     * @param array $attributes
     */
    public function __construct( array $attributes = [] ) {
        parent::__construct();

        $this->fill( $attributes );
    }

    /**
     * Return WPUser instance
     *
     * @return WP_User
     */
    public function getWpUser(): WP_User {
        if ( is_null( $this->wp_user ) ) {
            $this->wp_user = get_user_by( 'id', $this->ID );
        }
        
        return $this->wp_user;
    }

    /**
     * Create user
     *
     * @param array $attributes
     * @return User
     */
    public static function create( array $attributes ) : self {
        $user = new self( $attributes );
        $user->save();

        return $user;
    }

    /**
     * Save user
     *
     * @return self
     */
    public function save() : self {
        return $this->performSave();
    }

    /**
     * Perform save
     *
     * @return self
     */
    protected function performSave() : self {
        $meta_fields = []; // or custom fields

        foreach ( $this->wp_fields as $key => $value ) {
            $args[ $key ] = $this->valueToStore( $key, $value );
        }
        
        if ( $this->data ) {
            foreach ( $this->data as $key => $value ) {
                $meta_fields[ $key ] = $this->valueToStore( $key, $value );
            }
        }

        // $attributes = array_merge( $this->wp_fields, $this->data );
        $attributes = array_merge( $this->wp_fields, [
            'meta_input' => $meta_fields
        ] );
        $user_id    = $this->ID === 0
            ? wp_insert_user( $attributes )
            : wp_update_user( $attributes );

        if ( is_wp_error( $user_id ) ) {
            throw new Exception( $user_id->get_error_message() );
        }

        // Set the post id if it's zero.
        // It means that the action is creating.
        if ( $attributes['ID'] === 0 ) {
            $this->setProp( 'ID', $user_id );
            $this->refresh();
        }

        return $this;
    }

    /**
     * Find a model
     *
     * @param string|int $object_id
     * @return static|boolean
     */
    public static function find( $value, $field = 'id' ) {
        $userdata = WP_User::get_data_by( $field, $value );

        if ( ! $userdata ) {
            return false;
        }
        
        $user = new static( (array) $userdata );
        $user->refreshData();
        $user->wp_user = new WP_User;
        $user->wp_user->init( $userdata );
        
        return $user;
    }

    /**
     * Refresh content from database
     *
     * @return static|null
     */
    public function refresh() {
        /**
         * Obviously to refresh an isntance is necessary have an ID.
         * Otherwise it will return null
         */
        if ( $this->ID === 0 ) {
            return null;
        }

        // Retrieve the post updated as array
        $object = WP_User::get_data_by( $this->ID, 'id' );

        // Return null if it does not exists
        if ( ! $object ) {
            return null;
        }

        $this->wp_user = new WP_User;
        $this->wp_user->init( $object );

        // Fill the wp fields first
        $this->fill( (array) $object );
        $this->refreshData();

        return $this;
    }

    public function refreshData() : self {
        // Now let us take a look on post meta fields
        if ( $this->data ) {
            foreach ( $this->data as $key => $value ) {
                // if ( ! $this->shouldRetrieveData( $key ) ) {
                //     continue;
                // }
                
                $this->data[ $key ] = get_user_meta( $this->ID, $key, true );
            }
        }

        return $this;
    }
}