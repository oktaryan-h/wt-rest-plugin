<?php

/**
 * Plugin Name: WT REST API Plugin
 * Plugin URI: https://oktaryan.com/wpra
 * Description: WT REST API Plugin on SS
 * Version: 1.0
 * Author: Oktaryan Nh
 * Author URI: https://oktaryan.com
 */

/**
 * 
 */
class WT_REST_API {

	/**
	 * [create description]
	 * @return [type] [description]
	 */
	function create() {

		$url = get_rest_url() . "wp/v2/posts/";

		echo get_res_url();

		$response = wp_remote_post( 
			$url,
			array(
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
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

	/**
	 * [update description]
	 * @param  [type] $post_id [description]
	 * @param  [type] $data    [description]
	 * @return [type]          [description]
	 */
	function update( $post_id, $data ) {

		$url = get_rest_url() . "wp/v2/posts/{$post_id}";

		$response = wp_remote_post( 
			$url,
			array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'user:1234' ) ),
				'body' => $data,
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

	/**
	 * [delete description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	function delete( $post_id ) {

		$url = get_rest_url() . "wp/v2/posts/{$post_id}";

		$response = wp_remote_post( 
			$url,
			array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'user:1234' ) ),
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

	/**
	 * [html_show_posts description]
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	public function html_show_posts( $atts ) {

		//echo get_rest_url();

		$attributes = shortcode_atts( 
			array( 'posts' => 1 ),
			$atts );

		$posts = 0;

		if ( isset ( $attributes['posts'] ) ) {
			$posts = $attributes['posts'];
		}

		$response = wp_remote_get( get_rest_url()."wp/v2/posts/?page=1&per_page={$posts}", array( '' ) );

		$source = wp_remote_retrieve_body( $response );

		$json_decoded = json_decode( $source, true );

		$post_entries = array();

		foreach ( $json_decoded as $a ) {
			$x = array();
			$x['id'] = ( isset( $a['id'] ) ) ? $a['id'] : '';
			$x['title'] = ( isset( $a['title']['rendered'] ) ) ? $a['title']['rendered'] : '';
			$x['content'] = ( isset( $a['content']['rendered'] ) ) ? $a['content']['rendered'] : '';
			$x['date'] = ( isset( $a['date'] ) ) ? $a['date'] : '';
			$post_entries[] = $x;
		}

		foreach ( $post_entries as $a ) {
			echo '<div>';
			echo '<p style="font-weight:600">(' . $a['id'] . ') ' . $a['title'] . '</p>';
			echo '<p>' . $a['content']  . '</p>';
			echo '<p>' . $a['date'] . '</p>';
			echo '</div>';
		}

	}

	/**
	 * [shortcode description]
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	function shortcode( $atts ) {

		ob_start();

		//$this->create();

		//$this->update( 41, array('title' => 'rest-FULL') );

		$this->html_show_posts( $atts );

		return ob_get_clean();
	}
}

$wt_rest = new WT_REST_API;

add_shortcode( 'rest-full', array( $wt_rest, 'shortcode' ) );