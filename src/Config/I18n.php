<?php
/**
 * PK
 *
 * @package   pk
 * @author    PoetKods <david@poetkods.com>
 * @copyright 2023 PK
 * @license   MIT
 * @link      https://poetkods.com
 */

declare( strict_types = 1 );

namespace Pk\Config;

use Pk\Common\Abstracts\Base;

/**
 * Internationalization and localization definitions
 *
 * @package Pk\Config
 * @since 1.0.0
 */
final class I18n extends Base {
	/**
	 * Load the plugin text domain for translation
	 * @docs https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain
	 *
	 * @since 1.0.0
	 */
	public function load() {
		load_plugin_textdomain(
			$this->plugin->textDomain(),
			false,
			dirname( plugin_basename( PK_PLUGIN_FILE ) ) . '/languages' // phpcs:disable ImportDetection.Imports.RequireImports.Symbol -- this constant is global
		);
	}
}
