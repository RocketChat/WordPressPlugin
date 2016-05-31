
Editing:  
/home/livewp/public_html/wp-content/plugins/livechat-wordpress/livechat-wordpress.php
 Encoding:    Re-open Use Code Editor     Close  Save Changes

<?php

/*

 * Plugin Name:	Rocket.Chat LIVEChat for WordPress

 * Plugin URI:	https://github.com/liveitpros/livechat-wordpress

 * Description:	Rocket.Chat LIVEChat Integration with WordPress

 * Version: 0.1

 * Author : Sean Alexander Sr <livechat-wordpress.email@liveitpros.com>

 * License:           GPL-2.0+

 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 */



define(WP_DEBUG, true);

session_start();



/*

 * Rocket.Chat LIVEChat plugin class

 *

 * This is the main plugin class, handles all the plugin options, WordPress

 * hooks and filters, as well as options validation. Rocket.Chat LIVEChat tag generator

 *

 */



final class RocketChat_LIVEChat

{



	private $settings = array();

	private $setting_liveChat = array();

	private $ticketInfoContainer = array();

	private $store;



	/*

	 *  Class Constructor

	 */

	public function __construct()

	{

		//Calling Hooks ...........

		add_action('admin_menu', array(&$this, 'rocketchat_livechat_admin_menu'));

		add_action('admin_init', array(&$this, 'rocketchat_livechat_admin_init'));

		add_action('wp_head', array(&$this, 'enqueue_scripts'));

		add_shortcode('rocketchat_livechat_helpdesk', array(&$this, '_display_contact_ticket_form'));



		//Custom actions calls.....

		add_action('wp_ajax_ticketPostDiv', array(&$this, 'ticketPostDiv'));

		add_action('wp_ajax_post_reply_action', array(&$this, 'post_reply_action'));

		add_action('wp_ajax_update_ticket_properties', array(&$this, 'update_ticket_properties'));



		if ($settings = get_option('rocketchat_livechat_settings')) {

			$this->settings = get_option('rocketchat_livechat_settings');



			if (empty($this->settings['rocketchat_livechat_url']) || empty($this->settings['rocketchat_livechat_key']) || empty($this->settings['rocketchat_livechat_secret'])) {

				add_action('admin_notices', array(&$this, 'my_admin_notice'));

			}

			$this->rocketchat_livechat_API = new RocketChat_LIVEChatApi($settings['rocketchat_livechat_url'], $settings['rocketchat_livechat_key'], $settings['rocketchat_livechat_secret']);

		}



		if (isset($_REQUEST['tid']) && isset($_REQUEST['tURL'])) {

			add_action('admin_notices', array(&$this, 'comment_section_notices'));

		}





//		$this->AccessFormProcess();



		$this->setting_liveChat = get_option('rocketchat_livechat_tag_settings');





		$this->setup();





		add_action('wp_dashboard_setup', array($this, '_rocketchat_livechat_dashboard_widget_setup'));

	}



	/*

	 * Display the rocketchat_livechat validation

	 */

	public function my_admin_notice()

	{

		echo '<div class="updated"><p>It is required to fill the API key, Secret key, Rocket.Chat LIVEChat URL ! There is an issue in establishing connection with Rocket.Chat, Please provide the full details !</p></div>';

	}



	/*

	 * Display the Comment Section notices

	 */

	public function comment_section_notices()

	{

		echo '<div class="updated"><p>Your comment has been converted to ticket successfully ! To View your ticket Click here  <a href="' . urldecode($_REQUEST['tURL']) . '" target = "_blank">#' . $_REQUEST['tid'] . '</a></p></div>';

	}



	/*

	 *  Setup function loads the default settings, stores the current user details

	 */

	public function setup()

	{

		// Load up the settings, set the Rocket.Chat LIVEChat URL and initialize the API object.

		$this->_load_settings();



		global $current_user;

		wp_get_current_user();

		if ($current_user->ID) {

			$this->user = $current_user;

		}

	}



	/*

	 *  Load Settings It returns the default settings

	 *

	 */

	private function _load_settings()

	{



		$this->settings = get_option('rocketchat_livechat_settings', false);



		$this->livechat_tag_settings = get_option('rocketchat_livechat_livechat_tag-settings', false);



		//Save Default setting

		$this->default = array(

			'form_title'     => __('Rocket.Chat LIVChat Form Submission', 'rocketchat_livechat'),

			'fullname'       => __('Name', 'rocketchat_livechat'),

			'email'          => __('Email', 'rocketchat_livechat'),

			'tickettype'     => __('Select Ticket Type', 'rocketchat_livechat'),

			'ticketpriority' => __('Select Ticket Priority', 'rocketchat_livechat'),

			'department'     => __('Select Department', 'rocketchat_livechat'),

			'subject'        => __('Subject', 'rocketchat_livechat'),

			'contents'       => __('Description', 'rocketchat_livechat'),



		);

	}



	/*

	 * Admin Initialization

	 * Registration of different sections has been done here.

	 * All the options are stored in the $this->settings

	 * array which is kept under the 'rocketchat_livechat_settings' key inside

	 * the WordPress database.

	 *

	 */

	public function rocketchat_livechat_admin_init()

	{



		// Scripts and style sheet

		add_action('admin_print_styles', array($this, 'rocketchat_livechat_admin_print_styles'));





		//Check for the zcomments

		add_filter('comment_row_actions', array(&$this, '_add_comment_row_actions'), 10, 2);

		add_filter('manage_edit-comments_columns', array(&$this, '_add_comments_columns_filter'), 10, 1);

		add_action('manage_comments_custom_column', array(&$this, '_add_comments_columns_action'), 10, 1);





		//Gathering API information form setup

		register_setting('rocketchat_livechat_settings', 'rocketchat_livechat_settings', '');

		add_settings_section('api_setting_form', __('Access Details', 'rocketchat_livechat'), array(&$this, '_settings_section_api_setting_form'), 'rocketchat_livechat_settings');

		add_settings_field('rocketchat_livechat_url', __('Your Rocket.Chat API URL', 'rocketchat_livechat'), array(&$this, '_settings_field_rocketchat_livechat_url'), 'rocketchat_livechat_settings', 'api_setting_form');

		add_settings_field('rocketchat_livechat_key', __('API Key', 'rocketchat_livechat'), array(&$this, '_settings_field_api_key'), 'rocketchat_livechat_settings', 'api_setting_form');

		add_settings_field('rocketchat_livechat_secret', __('API Secret', 'rocketchat_livechat'), array(&$this, '_settings_api_secret'), 'rocketchat_livechat_settings', 'api_setting_form');

		add_settings_section('api_setting_form', __('Access Details', 'rocketchat_livechat'), array(&$this, '_settings_section_api_setting_form'), 'rocketchat_livechat_settings');





		//Rocket.Chat LIVEChat Settings

		register_setting('rocketchat_livechat_tag_settings', 'rocketchat_livechat_tag_settings', '');

		add_settings_section('rocketchat_livechat_tag_form', __('Live Chat tag generator', 'rocketchat_livechat'), array(

			&$this, 'rocketchat_livechat_tag_settings_description'

		), 'rocketchat_livechat_tag_settings');

		add_settings_field('rocketchat_livechat_tag', __('Paste the tag generated by Rocket.Chat LIVEChat here: ', 'rocketchat_livechat'), array(

			&$this, '_livechat_tag_textarea'

		), 'rocketchat_livechat_tag_settings', 'rocketchat_livechat_tag_form');

		add_settings_section('rocketchat_livechat_note', __('Note :', 'rocketchat_livechat'), array(&$this, '_rocketchat_livechat_note'), 'rocketchat_livechat_tag_settings');





		//Processes the forms

		$this->_process_formData();

	}



	/*

	 * Rocket.Chat LIVEChat admin menu

	 */

	public function rocketchat_livechat_admin_menu()

	{

		add_menu_page('Rocket.Chat LIVEChat for Wordpress', 'Rocket.Chat LIVEChat', 'manage_options', 'rocketchat_livechat_support', array(

			&$this, '_admin_menu_contents'), plugins_url('livechat-wordpress/images/icon.png'), 99);

		add_submenu_page('liveitpros-support', 'Rocket.Chat LIVEChat Settings', __('Rocket.Chat LIVEChat Settings', 'support'), 'manage_options', 'rocketchat_livechat_support', array(&$this, '_admin_menu_contents'));

		add_submenu_page('liveitpros-support', 'Rocket.Chat LIVEChat Tag', __('Rocket.Chat LIVEChat Tag', 'support'), 'manage_options', 'rocketchat_livechat_tag_settings', array(

			&$this, '_livechat_form_contents'

		));

	}



	/*

	 * Rocket.Chat LIVEChat Admin Print Styles

	 */

	public function livechat_admin_print_styles()

	{

		wp_enqueue_style('livechat-admin', plugins_url('/css/admin.css', __FILE__));

		wp_enqueue_style('my-script', plugins_url('/css/general.css', __FILE__));

		wp_enqueue_script('livechat-admin', plugins_url('/js/livechat.js', __FILE__), array('jquery'));

		wp_localize_script('livechat-admin', 'livechat', array('plugin_url' => admin_url() . "edit-comments.php"));

		wp_enqueue_script('my-script', plugins_url('/js/popup.js', __FILE__), array('jquery'));

	}



	/*

	 * It displays admin menu contents

	 * It comes up with the entire form for saving settings

	 */

	public function _admin_menu_contents()

	{



		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'convert_comments' || $_REQUEST['c']) {

			$this->display_comment_conversion_form();

		} else {

			?>

			<div class="wrap">

				<div id="rocketchat-livechat-icon32" class="icon32"><br></div>

				<h2><?php _e('Rocket.Chat LIVEChat for WordPress Settings', 'rocketchat_livechat'); ?></h2>



				<form method="post" action="options.php">

					<input type="hidden" name="rocketchat_livechat_settings[saved]"/>

					<?php wp_nonce_field('update-options'); ?>

					<?php settings_fields('rocketchat_livechat_settings'); ?>

					<?php do_settings_sections('rocketchat_livechat_settings'); ?>

					<p class="submit" align="left">

						<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Details', 'rocketchat_livechat'); ?>"/>

					</p>

				</form>

			</div>

		<?php

		}

	}



	/*

	 * Setting of api_setting_section_api_form

	 * It outputs the description of heading which displays while saving main Rocket.Chat LIVEChat API settings

	 *

	 */

	public function _settings_section_api_setting_form()

	{

		_e('Please enter your Rocket.Chat LIVEChat API Key, Secret Key, Rocket.Chat LIVEChat URL for further connection !', 'rocketchat_livechat');

	}





	/*

	 *  It returns a textbox which outputs the Rocket.Chat LIVEChat URL input box

	 */

	public function _settings_field_rocketchat_livechat_url()

	{


		if (!empty($this->settings) && array_key_exists('rocketchat_livechat_url', $this->settings)) {

			$rocketchat_livechat_url = htmlspecialchars($this->settings['rocketchat_livechat_url']);

		}

		?>

		<input type="text" name="rocketchat_livechat_settings[rocketchat_livechat_url]" id="rocketchat_livechat_settings[rocketchat_livechat_url]" size="60" value="<?php echo $rocketchat_livechat_url; ?>"/> <?php

	}



	/*

	 *  It returns a textbox which outputs the Rocket.Chat URL input box

	 */

	public function _settings_field_api_key()

	{

		if (!empty($this->settings) && array_key_exists('rocketchat_livechat_key', $this->settings)) {

			$rocketchat_livechat_key = htmlspecialchars($this->settings['rocketchat_livechat_key']);

		}

		?>

		<input type="text" name="rocketchat_livechat_settings[rocketchat_livechat_key]" id="rocketchat_livechat_settings[rocketchat_livechat_key]" size="60" value="<?php echo $rocketchat_livechat_key; ?>"/> <?php

	}



	/*

	 * It returns a textbox which outputs the Rocket.Chat LIVEChat URL input box

	 */

	public function _settings_api_secret()

	{

		if (!empty($this->settings) && array_key_exists('rocketchat_livechat_secret', $this->settings)) {

			$rocketchat_livechat_secret = htmlspecialchars($this->settings['rocketchat_livechat_secret']);

		}

		?> <input type="text" name="rocketchat_livechat_settings[rocketchat_livechat_secret]" id="rocketchat_livechat_settings[rocketchat_livechat_secret]" size="60" value="<?php echo $rocketchat_livechat_secret; ?>"/> <?php

	}



	/*

	 *  It returns a textbox which outputs the Rocket.Chat LIVEChat Form Title input box

	 */

	public function _settings_field_form_title()

	{

		if (!empty($this->settings) && array_key_exists('form_title', $this->settings) && !empty($this->settings['form_title']) && $this->settings['form_title'] <> '0') {

			$form_value = $this->settings['form_title'];

		} else {

			$form_value = $this->default['form_title'];

		}

		?>

		<input type="text" size="40" name="rocketchat_livechat_settings[form_title]" value="<?php echo $form_value; ?>" placeholder="Rocket.Chat LIVEChat Form"/>

	<?php

	}



	/*

	 * It returns a textbox which displays the Fullname input box

	 */

	public function _settings_field_fullname()

	{

		if (!empty($this->settings) && array_key_exists('fullname', $this->settings) && !empty($this->settings['fullname']) && $this->settings['fullname'] <> '0') {

			$fullname = $this->settings['fullname'];

		} else {

			$fullname = $this->default['fullname'];

		}

		?>

		<input type="text" size="40" name="rocketchat_livechat_settings[fullname]" value="<?php echo $fullname; ?>" placeholder="Please enter your fullname"/>

	<?php

	}



	/*

	 * It returns a textbox which displays the Email Address input box

	 */

	public function _settings_field_email()

	{

		if (!empty($this->settings) && array_key_exists('email', $this->settings) && !empty($this->settings['email']) && $this->settings['email'] <> '0') {

			$email = $this->settings['email'];

		} else {

			$email = $this->default['email'];

		}

		?>

		<input type="text" size="40" name="rocketchat_livechat_settings[email]" value="<?php echo $email; ?>" placeholder="Your Email Address"/>

	<?php

	}



	/* Rocket.Chat LIVEChat Dashboard Widget

	 * It displays a ticket view list on the frontend of rocketchat_livechat widget setup

	 */

	public function _rocketchat_livechat_dashboard_widget_setup()

	{

		if ($this->settings) {

			wp_add_dashboard_widget('rocketchat_livechat_dashboard_viewticket_widget', __('Rocket.Chat LIVEChat', 'rocketchat_livechat'), array(&$this, '_admin_view_tickets_bar'));

		}

	}



	/* Livechat Form Contents

	 * A form for submission of LiveChat Tag Generator

	 */

	public function _livechat_form_contents()

	{

		?>

		<div class="wrap">

			<?php echo "<h2>" . __('Rocket.Chat LIVEChat setup', 'setup') . "</h2>"; ?>

			<form method="post" action="options.php">

				<?php wp_nonce_field('update-options'); ?>

				<?php settings_fields('rocketchat_livechat_tag_settings'); ?>

				<?php do_settings_sections('rocketchat_livechat_tag_settings'); ?>

				<p class="submit">

					<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Tag', 'rocketchat_livechat'); ?>"/>

				</p>

			</form>

		</div>

	<?php

	}





	/* Live Chat Textarea box

	 * It displays a textarea box in form

	 */

	public function _livechat_tag_textarea()

	{

		if ($this->livechat_tag_settings) {

			if (array_key_exists('rocketchat_livechat_tag', $this->livechat_tag_settings)) {

				$rocketchat_livechat_tag = htmlspecialchars($this->livechat_tag_settings['rocketchat_livechat_tag']);

			}

		}

		?>

		<textarea rows="10" cols="100" name="rocketchat_livechat_tag_settings[rocketchat_livechat_tag]" id="rocketchat_livechat_tag_settings[rocketchat_livechat_tag]"><?php echo $rocketchat_livechat_tag; ?></textarea>

	<?php

	}



	/*

	 * It displays the Rocket.Chat LIVEChat tag description Heading

	 */

	public function rocketchat_livechat_tag_settings_description()

	{

		_e('The Rocket.Chat LIVEChat places a convenient chat tab on your pages that allow your visitors to initiate a chat !', 'rocketchat_livechat');

	}





	/*

	 *  It returns a Current Logged in user 'User ID'

	 */

	public function get_current_UserID()

	{

		$userDetails = $this->_return_current_user_details();



		return $userDetails['result']['user']['0']['id'];

	}







	

	/*

	 * Function which sets the rocketchat_livechat_notice which displays warnings or errors

	 */

	private function _set_rocketchat_livechat_notice($context, $text, $type = 'note')

	{

		if (isset($this->notices[$context . '_' . $type])) {

			$this->notices[$context . '_' . $type][] = $text;

		} else {

			$this->notices[$context . '_' . $type] = array($text);

		}

	}



	/* Function which

	 * places the notices on wherever you require just pass the content type

	 */

	private function _print_notices($context)

	{

		echo '<div>';

		foreach (array('note', 'confirm', 'alert') as $type) {

			if (isset($this->notices[$context . '_' . $type])) {

				$notices = $this->notices[$context . '_' . $type];

				foreach ($notices as $notice)

					?>

					<div id="message" class="updated">

					<p><?php echo $notice; ?></p>

				</div>

			<?php

			}

		}

		echo '</div>';

	}









	/*

	 *  Rocket.Chat LIVEChat tag code gathering

	 */

	public function rocketchat_livechat_tag_code()

	{

		echo stripslashes($this->setting_liveChat['rocketchat_livechat_tag']);

	}



	/*

	 * Rocket.Chat LIVEChat Note setting description

	 */

	public function _rocketchat_livechat_note()

	{

		_e('After setting your options, include the following template snippet anywhere in your theme to display the Rocket.Chat LIVEChat Tag Icon: <p><code><</code><code>?php if(function_exists(\'the_rocketchat_liveChatTag\')) the_rocketchat_liveChatTag(); ?</code><code>></code></p>', 'rocketchat_livechat');

	}





	/*

	 *  It prints the errorMessage like admin notices if connectivity is not proper

	 */

	public function errorMessagePrint($_code)

	{

		echo "<div class='frontend_errormessage'> <strong>ERROR : </strong>" . $_code . " ! There must be some issue with the helpdesk connectivity ! Not able to fetch data !</div>";

	}



	/*

	 * It includes all the scripts or classes

	 */

	public function enqueue_scripts()

	{

		wp_enqueue_style('my-script', plugins_url('/css/admin.css', __FILE__));

		wp_enqueue_script('my-script', plugins_url('/js/rocketchat_livechat.js', __FILE__), array('jquery'));

		wp_localize_script('my-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

	}







}



// Register the Rocket.Chat LIVEChat class initialization during WordPress' init action. Globally available through $rocketchat_livechat.



add_action('init', create_function('', 'global $rocketchat_livechat; $rocketchat_livechat = new RocketChat_LIVEChat();'));



/*

 * LiveChat Tag template tag

 * @global $rocketchat_livechat_support

 *

 */

function the_rocketchat_liveChatTag()

{

	global $rocketchat_livechat;



	if ($rocketchat_livechat) {

		$rocketchat_livechat->rocketchat_livechat_tag_code();

	}

} 

?>
