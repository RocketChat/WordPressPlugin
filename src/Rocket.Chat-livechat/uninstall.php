<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       http://rocket.chat
 * @since      1.0.0
 *
 * @package    Rocketchat_Livechat
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'rocketchat-livechat-url' );