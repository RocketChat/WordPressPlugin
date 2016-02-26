<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.liveitpros.com
 * @since      1.0.0
 *
 * @package    Livechat_Wordpress
 * @subpackage Livechat_Wordpress/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Livechat_Wordpress
 * @subpackage Livechat_Wordpress/includes
 * @author     Sean Alexander Sr <livechat-wordpress.email@liveitpros.com>
 */
class Livechat_Wordpress_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'livechat-wordpress',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
