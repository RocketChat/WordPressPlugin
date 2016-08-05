<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://nezn.am
 * @since      1.0.0
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/public
 * @author     Marko Banušić <mbanusic@gmail.com>
 */
class Rocketchat_Livechat_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function livechat_tag() {
		$livechat_url = get_option('rocketchat-livechat-url');
		if ( $livechat_url ) {
			$livechat_url = trailingslashit( $livechat_url );
			?>
			<!-- Start of Rocket.Chat Livechat Script -->
			<script type="text/javascript">
				(function (w, d, s, u) {
					w.RocketChat = function (c) {
						w.RocketChat._.push(c)
					};
					w.RocketChat._ = [];
					w.RocketChat.url = u;
					var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
					j.async = true;
					j.src = '<?php echo esc_url( $livechat_url ); ?>packages/rocketchat_livechat/assets/rocket-livechat.js';
					h.parentNode.insertBefore(j, h);
				})(window, document, 'script', '<?php echo esc_url( $livechat_url ) ?>livechat');
			</script>
			<!-- End of Rocket.Chat Livechat Script -->
			<?php
		}
	}

}
