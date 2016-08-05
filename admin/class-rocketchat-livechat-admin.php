<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://nezn.am
 * @since      1.0.0
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/admin
 * @author     Marko Banušić <mbanusic@gmail.com>
 */
class Rocketchat_Livechat_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function register_settings() {
		register_setting('rocketchat-livechat-options', 'rocketchat-livechat-url', array( $this, 'sanitize_url' ) );

		add_settings_section( 'rocketchat-livechat-options-head', 'Rocket.Chat LiveChat API settings', '', 'rocketchat-livechat-options' );
		add_settings_field( 'rocketchat-livechat-url', 'URL of LiveChat', array( $this, 'settings_text' ), 'rocketchat-livechat-options', 'rocketchat-livechat-options-head', array( 'id' => 'rocketchat-livechat-url', 'desc' => 'Please enter the URL to your Rocket.Chat instance (e.g. https://chat.domain.tld/)' ) );
	}

	public function menu() {
		add_options_page( 'Rocket.Chat LiveChat', 'Rocket.Chat LiveChat', 'manage_options', 'rocketchat-livechat', array( $this, 'options' ) );
	}

	public function options() {
		?><div class="wrap">
		<h2>Rocket.Chat options</h2>
		<?php
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'rocketchat-livechat-options' ) {
			if ( isset( $_POST['rocketchat-livechat-url'] ) && $_POST['rocketchat-livechat-url'] ) {
				update_option( 'rocketchat-livechat-url', esc_url( $_POST['rocketchat-livechat-url'] ) );
			}
		}
		?>
		<form method="POST"><?php
			settings_fields( 'rocketchat-livechat-options' );
			do_settings_sections( 'rocketchat-livechat-options' );
			submit_button();
			?></form>
		</div><?php
	}

	public function sanitize_url( $url ) {
		return esc_url_raw( sanitize_text_field(  $url ) );
	}

	public function settings_text( $args ) {
		$id = $args['id'];
		if ( !$id ) {
			return;
		}
		$option = get_option( $id );
		?><input type="text" name="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( $option ) ?>" id="<?php echo esc_attr( $id ) ?>" size="100" ><?php
		if ( isset($args['desc'] ) && $args['desc'] ) {
			?><p class="description"><?php echo esc_html( $args['desc'] ); ?></p><?php
		}
	}
}
