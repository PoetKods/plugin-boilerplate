<?php
/**
 * Ajax base caller
 * 
 * @package insidearm-core
 */

namespace Pk\Common\Http;

use Exception;
use Pk\Common\Abstracts\Base;
use Throwable;
use Tightenco\Collect\Support\Collection;

defined( 'ABSPATH' ) || exit;


abstract class Ajaxable extends Base {

    /**
     * It will load the actions using this format
     * 
     * ['action_name' => ['pivate', 'public']]
     *
     * @var array<string, array>
     */
    protected $actions = [];

    /**
     * Actions prefix
     *
     * @var string
     */
    protected $prefix = "ia_";

    protected $params = [];

    /**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->endpoints();

        if ( $this->actions ) {
            foreach ( $this->actions as $action => $visibility ) {
                $action = sprintf( "%s%s", $this->prefix, $action );

                if ( in_array( 'public', $visibility ) ) {
                    add_action( "wp_ajax_nopriv_{$action}", [ $this, "process_response" ] );
                }

                if ( in_array( 'private', $visibility ) ) {
                    add_action( "wp_ajax_{$action}", [ $this, "process_response" ] );
                }
            }
        }
	}

    /**
     * Process request
     *
     * @return void
     */
    public function process_response() {
        try {
            if ( ! isset( $_REQUEST['action'] ) ) {
                throw new Exception( 'Invalid Request. Action does not exists' );
            }

            $action   = sanitize_text_field( $_REQUEST['action'] );
            $callback = $this->getRealActionName( $action );

            return $this->{$callback}();
        } catch (Throwable $e) {
            return $this->error([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verifiy action nonce request
     *
     * @param string $nonce_name
     * @param string $nonce_field
     * @return void
     */
    public function verifyRequest( string $nonce_name, string $nonce_field = '_wpnonce' ) {
        if ( ! check_ajax_referer( $nonce_name, $nonce_field, false ) ) {
            throw new Exception( 'Invalid request. Form as expired' );
        }
    }

    /**
     * Return the real action name deleting the action prefix
     *
     * @param string $action_name
     * @return string
     */
    protected function getRealActionName( string $action_name ) : string {
        $action_name = explode( $this->prefix, $action_name );

        return end( $action_name );
    }

    /**
     * Send JSON Error
     *
     * @param array $data
     * @return void
     */
    public function error( array $data = [] ) {
        return wp_send_json_error( $data );
    }

    /**
     * Send JSON Success
     *
     * @param array $data
     * @return void
     */
    public function success( array $data = [] ) {
        return wp_send_json_success( $data );
    }

    /**
     * Initialize endpoints
     *
     * @return void
     */
    abstract public function endpoints(): void;

    /**
     * Register an action as private
     *
     * @param string $callback
     * @return void
     */
    public function asPrivate( string $callback ): void {
        $this->registerEndpoint( $callback, ['private'] );
    }

    /**
     * Register a public action
     *
     * @param string $callback
     * @return void
     */
    public function asPublic( string $callback ) {
        $this->registerEndpoint( $callback, ['public', 'private'] );
    }

    /**
     * Register an endpoint
     *
     * @param string $callback
     * @param array $visibility
     * @return void
     */
    public function registerEndpoint( string $callback, array $visibility ): void {
        if ( array_key_exists( $callback, $this->actions ) ) {
            return;
        }

        $this->actions[ $callback ] = $visibility;
    }

    /**
     * Load and retrieve actions
     *
     * @return array
     */
    public function loadAndRetrieveActions(): array {
        $actions = [];

        $this->endpoints();
        if ( $this->actions ) {
            foreach ( $this->actions as $action => $access ) {
                $actions[ $action ] = sprintf( '%s%s', $this->prefix, $action );
            }
        }

        return $actions;
    }
    
    public static function getActions(): array {
        return (new static)->loadAndRetrieveActions();
    }
}