<?php

/**
 * Plugin Name: WT REST API Plugin
 * Plugin URI: https://oktaryan.com/wpra
 * Description: WT REST API Plugin on SS
 * Version: 1.0
 * Author: Oktaryan Nh
 * Author URI: https://oktaryan.com
 */

class WT_REST_API {

	function create() {

		$url = "http://localhost/wordpress/wp-json/wp/v2/posts/";

		$response = wp_remote_post( 
			$url,
			array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
//				'headers' => array( 'Authorization' => 'Basic user:1234' ),
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'user:1234' ) ),
				'body' => array(
					'title' => 'The Title from REST',
					'content' => 'The content',
					'status' => 'publish',
				),
				'cookies' => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			echo 'Response:<pre>';
			print_r( $response );
			echo '</pre>';
		}

	}

	function backend() {

		$response = wp_remote_get( site_url().'/wp-json/wp/v2/posts/?page=1&per_page=2', array( '' ) );
		echo 'Response:<pre>';
		var_dump($response);
		echo '</pre>';

	}

	/**
	 * The HTML form code to display in user form.
	 */
	public function html_show_users( $atts ) {


	}

	function shortcode( $atts ) {

		ob_start();

		$this->create();

		$this->backend();

		// $this->html_show_users( $atts );

		return ob_get_clean();
	}
}

$wt_role = new WT_REST_API;

//register_activation_hook( __FILE__, array( $wt_role, 'install' ) );

add_shortcode( 'rest-full', array( $wt_role, 'shortcode' ) );