<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/kudindmitriy
 * @since      1.0.0
 *
 * @package    Exchange_Rate
 * @subpackage Exchange_Rate/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Exchange_Rate
 * @subpackage Exchange_Rate/includes
 * @author     Dmitry Kudin <kudin.dima@gmail.com>
 */
class Exchange_Rate_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'exchange-rate',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
