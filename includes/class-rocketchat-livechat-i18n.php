<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://rocket.chat
 * @since      1.0.0
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 * @author     Marko Banušić <mbanusic@gmail.com>
 */
class Rocketchat_Livechat_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'rocketchat-livechat',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
