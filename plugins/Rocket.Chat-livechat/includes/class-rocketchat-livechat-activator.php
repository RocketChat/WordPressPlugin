<?php

/**
 * Fired during plugin activation
 *
 * @link       http://rocket.chat
 * @since      1.0.0
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 * @author     Marko Banušić <mbanusic@gmail.com>
 */
class Rocketchat_Livechat_Activator {

	public static function activate() {
		add_option( 'rocketchat-livechat-url' );
	}

}
