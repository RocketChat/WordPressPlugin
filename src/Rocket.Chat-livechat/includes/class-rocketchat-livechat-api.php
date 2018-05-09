<?php
/**
 * The file contains main REST API logic
 *
 *
 * @link       http://rocket.chat
 * @since      1.1.0
 *
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 */

/**
 * The REST API class.
 *
 * This class contains methods for accessing all of the REST API endpoints
 *
 *
 * @since      1.1.0
 * @package    Rocketchat_Livechat
 * @subpackage Rocketchat_Livechat/includes
 * @author     Marko Banušić <mbanusic@gmail.com>
 */

class Rocketchat_Livechat_REST_API {

	public function __construct() {

	}

	/**
	 * Method to create authenticated request to REST API
	 *
	 * @param string $end_point,
	 * @param array|string|bool $payload
	 *
	 * @return array
	 */
	private function make_request( $end_point, $payload = false ) {
		$url = trailingslashit( get_option( 'rocketchat-livechat-url' ) );
		$url .= 'api/' . $end_point;
		$user_id = get_option( 'rocketchat-livechat-user-id' );
		$auth_token = get_option( 'rocketchat-livechat-auth-token' );
		if ( !$user_id || !$auth_token ) {
			//TODO:add notice and redirect to options page
			wp_redirect( admin_url( 'options-general.php?page=rocketchat-livechat' ) );
			exit();
		}
		$data = array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'X-User-Id' => $user_id,
				'X-Auth-Token' => $auth_token,
			)
		);
		if ( $payload ){
			$data['body'] = json_encode( $payload );
		}
		$r = wp_remote_post( $url, $data );

		return json_decode( $r['body'], true );
	}

	public function login( $username, $password, $url ) {
		$data = array(
			'body' => build_query( array( 'user' => sanitize_text_field( $username ), 'password' => sanitize_text_field( $password ) ) )
		);
		$r = wp_remote_post( esc_url( trailingslashit( $url ) . 'api/login' ), $data );
		$body = json_decode( $r['body'], true );
		var_dump($body);
		if ( 'success' == $body['status'] ) {
			update_option( 'rocketchat-livechat-user-id', sanitize_text_field( $body['data']['userId'] ) );
			update_option( 'rocketchat-livechat-auth-token', sanitize_text_field( $body['data']['authToken'] ) );
			return true;
		}
		else {
			//TODO:handle failure
		}
		return false;
	}
}