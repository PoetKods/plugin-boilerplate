<?php
/**
 * PK
 *
 * @package   pk
 * @author    Logic Cadence <david@poetkods.com>
 * @copyright 2023 PK Core
 * @license   MIT
 * @link      https://poetkods.com
 */

declare( strict_types = 1 );

namespace Pk\App\General\Users;

use Pk\Common\Database\Casts\ArrayCast;
use Pk\Common\Database\Casts\BooleanCast;
use Pk\Common\Database\Casts\DateCast;
use Pk\Common\Database\Casts\EmailCast;
use Pk\Common\Database\Casts\PasswordCast;
use Pk\Common\Database\User\BaseUser;
use Pk\Common\Utils\DjangoPasswordChecker;
use WP_User;

/**
 * Class PostTypes
 *
 * @package Pk\App\General
 * @method User fillDjangoData($djangoData)
 * @since 1.0.0
 */
class User extends BaseUser {
    /**
     * User Profile Data
     *
     * @var array
     */
    protected $data = [];

    protected $casts = array();

    /**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		/**
		 * This general class is always being instantiated as requested in the Bootstrap class
		 *
		 * @see Bootstrap::__construct
		 *
		 * Add plugin code here
		 */
	}
}