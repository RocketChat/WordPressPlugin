<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://rocket.chat
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
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public function register_settings() {
		register_setting(
			'rocketchat-livechat-options', 'rocketchat-livechat-username', array(
			$this,
			'sanitize_text'
		)
		);
		register_setting(
			'rocketchat-livechat-options', 'rocketchat-livechat-user-id', array(
			$this,
			'sanitize_text'
		)
		);
		register_setting(
			'rocketchat-livechat-options', 'rocketchat-livechat-auth-token', array(
			$this,
			'sanitize_text'
		)
		);
		register_setting(
			'rocketchat-livechat-options', 'rocketchat-livechat-url', array(
			$this,
			'sanitize_url'
		)
		);

		add_settings_section( 'rocketchat-livechat-options-head', 'Rocket.Chat LiveChat API settings', '', 'rocketchat-livechat-options' );
		add_settings_field(
			'rocketchat-livechat-url', __( 'URL of LiveChat', 'rocketchat-livechat' ), array(
			$this,
			'settings_text'
		), 'rocketchat-livechat-options', 'rocketchat-livechat-options-head', array(
			'id'   => 'rocketchat-livechat-url',
			'desc' => __( 'Please enter the URL to your Rocket.Chat instance (e.g. https://chat.domain.tld/)', 'rocketchat-livechat' ),
			'size' => 100
		)
		);
		add_settings_field(
			'rocketchat-livechat-username', __( 'Username', 'rocketchat-livechat' ), array(
			$this,
			'settings_text'
		), 'rocketchat-livechat-options', 'rocketchat-livechat-options-head', array(
			'id'   => 'rocketchat-livechat-username',
			'desc' => __( 'Enter your username', 'rocketchat-livechat' )
		)
		);
		add_settings_field(
			'rocketchat-livechat-password', __( 'Password', 'rocketchat-livechat' ), array(
			$this,
			'settings_text'
		), 'rocketchat-livechat-options', 'rocketchat-livechat-options-head', array(
				'id'   => 'rocketchat-livechat-password',
				'desc' => __( 'Enter your password', 'rocketchat-livechat' ),
				'type' => 'password'
			)
		);


	}

	public function menu() {
		add_options_page(
			__( 'Rocket.Chat LiveChat', 'rocketchat-livechat' ), __('Rocket.Chat LiveChat', 'rocketchat-livechat' ), 'manage_options', 'rocketchat-livechat', array(
			$this,
			'options'
		)
		);
	}

	public function options() {
		?>
		<div class="wrap">
		<h2><?php _e('Rocket.Chat options', 'rocketchat-livechat' ) ?></h2>
		<?php
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'rocketchat-livechat-options' ) {
			// we will only save URL and username, password will be asked only initially for getting auth token
			if ( isset( $_POST['rocketchat-livechat-url'] ) && $_POST['rocketchat-livechat-url'] ) {
				update_option( 'rocketchat-livechat-url', esc_url( $_POST['rocketchat-livechat-url'] ) );
			}
			if ( isset( $_POST['rocketchat-livechat-username'] ) && $_POST['rocketchat-livechat-username'] ) {
				update_option( 'rocketchat-livechat-username', sanitize_text_field( $_POST['rocketchat-livechat-username'] ) );
			}
			if ( isset( $_POST['rocketchat-livechat-password'] ) && $_POST['rocketchat-livechat-password'] ) {
				//user entered password, so we can make an auth request
				$c = new Rocketchat_Livechat_REST_API();
				$r = $c->login( $_POST['rocketchat-livechat-username'], $_POST['rocketchat-livechat-password'], $_POST['rocketchat-livechat-url'] );
				//TODO:notice of success/failure
			}
		}
		?>
		<form method="POST"><?php
			settings_fields( 'rocketchat-livechat-options' );
			do_settings_sections( 'rocketchat-livechat-options' );
			submit_button();
			?></form>

		<div>
			<p>User ID: <?php echo esc_html( get_option( 'rocketchat-livechat-user-id' ) ) ?></p>
			<p>Auth Token: <?php echo esc_html( get_option( 'rocketchat-livechat-auth-token' ) ) ?></p>
			<?php //TODO: button to clear data ?>
		</div>
		</div><?php
	}

	public function sanitize_url( $url ) {
		return esc_url_raw( sanitize_text_field( $url ) );
	}

	public function sanitize_text( $text ) {
		return sanitize_text_field( $text );
	}

	/**
	 * Custom method for generating input field
	 *
	 * @param array $args
	 */
	public function settings_text( $args ) {
		$id = $args['id'];
		if ( ! $id ) {
			return;
		}
		$default_options = array(
			'type'  => 'text',
			'size'  => 20,
			'class' => '',
			'desc'  => ''
		);
		$args            = wp_parse_args( $args, $default_options );
		$option          = '';
		if ( 'password' != $args['type'] ) {
			$option = get_option( $id );
		}
		?><input type="<?php echo esc_attr( $args['type'] ) ?>"
		         name="<?php echo esc_attr( $id ) ?>"
		         value="<?php echo esc_attr( $option ) ?>"
		         id="<?php echo esc_attr( $id ) ?>"
		         size="<?php echo esc_attr( $args['size'] ) ?>"
		         class="<?php echo esc_attr( $args['class'] ) ?>" ><?php
		if ( $args['desc'] ) {
			?><p
				class="description"><?php echo esc_html( $args['desc'] ); ?></p><?php
		}
	}
}
