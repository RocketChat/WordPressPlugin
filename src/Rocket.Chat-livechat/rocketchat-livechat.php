<?php
/**
 *
 * @link              http://rocket.chat
 * @since             1.0.0
 * @package           Rocketchat_Livechat
 *
 * @wordpress-plugin
 * Plugin Name:       Rocket.Chat LiveChat
 * Plugin URI:        http://rocket.chat
 * Description:       Plugin to provide Rocket.Chat Live Chat functionality to WordPress
 * Version:           1.0.1
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
 * We check if the user have added his rocket.chat instance URL
 */

register_activation_hook( __FILE__, 'Rocketchat_Livechat_admin_notice_Rocketchat_Livechat_activation_hook' );

function Rocketchat_Livechat_admin_notice_Rocketchat_Livechat_activation_hook() {
    set_transient( 'Rocketchat-Livechat-admin-notice-wptimelapse', true, 5 );
}

add_action( 'admin_notices', 'Rocketchat_Livechat_admin_notice_Rocketchat_Livechat_notice' );

function Rocketchat_Livechat_admin_notice_Rocketchat_Livechat_notice(){

	  /* Delete transient, only if the Rocket.chat URL is present. */
			delete_transient( 'rocketchat-admin-notice-livechat' );

    /* Check transient or license, if available display notice */
    if( get_transient( 'rocketchat-admin-notice-livechat' ) || empty(get_option('rocketchat-livechat-url')) ){
        ?>
        <div class="notice notice-info is-dismissible">
            <p>Thank you for using Rocket.Chat Livechat! <strong><a href="<?php echo get_admin_url() ;?>options-general.php?page=rocketchat-livechat">Please activate your Rocket.Chat URL</a></strong>.</p>
			
        </div>
        <?php
    }
}

 // End of the URL check

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

function add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=rocketchat-livechat">' . __( 'Settings', 'plugin_textdomain' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}

add_filter(  'plugin_action_links_' . plugin_basename(__FILE__), 'add_settings_link'  );
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
