<?php
/**
 *
 * @link              http://rocket.chat
 * @since             1.0.2
 * @package           Rocketchat_Livechat
 *
 * @wordpress-plugin
 * Plugin Name:       Rocket.Chat LiveChat
 * Plugin URI:        http://rocket.chat
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.2
 * Author:            Marko Banušić
 * Author URI:        http://nezn.am
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rocketchat-livechat
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rocketchat-livechat-activator.php
 */
function activate_rocketchat_livechat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rocketchat-livechat-activator.php';
	Rocketchat_Livechat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rocketchat-livechat-deactivator.php
 */
function deactivate_rocketchat_livechat() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rocketchat-livechat-deactivator.php';
	Rocketchat_Livechat_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rocketchat_livechat' );
register_deactivation_hook( __FILE__, 'deactivate_rocketchat_livechat' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rocketchat-livechat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rocketchat_livechat() {

	$plugin = new Rocketchat_Livechat();
	$plugin->run();

}
run_rocketchat_livechat();
