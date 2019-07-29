<?php

/**
 * Plugin Name: WT REST API Plugin
 * Plugin URI: https://oktaryan.com/wpra
 * Description: WT REST API Plugin on SS
 * Version: 1.0
 * Author: Oktaryan Nh
 * Author URI: https://oktaryan.com
 */

class WT_Wordpress {

	/**
	 * [_post_entries_default_args description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	function _post_entries_default_args( $args = array() ) {
		$default_args = array(
			'title',
			'content',
			'date'
		);
		if ( is_array( $args ) ) {
			if ( empty ( $args ) ) {
				$args = $default_args;
			}
			else {
				$args = array_intersect( $args, $default_args );
			}
		} else {
			$args = array( $args );
		}
		return $args;
	}

	/**
	 * [decode_from_json description]
	 * @param  [type] $source [description]
	 * @return [type]         [description]
	 */
	public function decode_from_json( $source ) {

		return json_decode( $source, true );

	}

	/**
	 * [post_entries description]
	 * @param  [type] $json [description]
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function post_entries( $json, $args = array() ) {

		$args = $this->_post_entries_default_args( $args );

		$json_decoded = $this->decode_from_json( $json );

		$post_entries = array();

		foreach ( $json_decoded as $a ) {
			$x = array();
			$x['id'] = ( isset( $a['id'] ) ) ? $a['id'] : '';
			if ( in_array( 'title', $args ) ) {
				$x['title'] = ( isset( $a['title']['rendered'] ) ) ? $a['title']['rendered'] : '';
			}
			if ( in_array( 'content', $args ) ) {
				$x['content'] = ( isset( $a['content']['rendered'] ) ) ? $a['content']['rendered'] : '';
			}
			if ( in_array( 'date', $args ) ) {
				$x['date'] = ( isset( $a['date'] ) ) ? $a['date'] : '';
			}
			$post_entries[] = $x;
		}

		return $post_entries;

	}

	/**
	 * [html description]
	 * @param  [type]  $json [description]
	 * @param  array   $args [description]
	 * @param  boolean $echo [description]
	 * @return [type]        [description]
	 */
	public function html( $json, $args = array(), $echo = true ) {

		$args = $this->_post_entries_default_args( $args );

		$post_entries = $this->post_entries( $json );

		if ( false == $echo ) {
			ob_start();
		}

		foreach ( $post_entries as $a ) {
			echo '<div>';
			if ( in_array( 'title', $args ) ) {
				echo '<p style="font-weight:600">(' . $a['id'] . ') ' . $a['title'] . '</p>';
			}
			if ( in_array( 'content', $args ) ) {
				echo '<p>' .  $a['content']  . '</p>';
			}
			if ( in_array( 'date', $args ) ) {
				echo '<p>' . $a['date'] . '</p>';
			}
			echo '</div>';
		}

		if ( false == $echo ) {
			return ob_get_clean();
		}
	}
}

/**
 * 
 */
class WT_REST_API {

	/**
	 * [create description]
	 * @return [type] [description]
	 */
	function create() {

		$url = "http://localhost/wordpress/wp-json/wp/v2/posts/";

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

	function update( $post_id, $data ) {

		$url = "http://localhost/wordpress/wp-json/wp/v2/posts/{$post_id}";

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

		$url = "http://localhost/wordpress/wp-json/wp/v2/posts/{$post_id}";

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
	 * [backend description]
	 * @return [type] [description]
	 */
	function backend() {

		$response = wp_remote_get( site_url().'/wp-json/wp/v2/posts/?page=1&per_page=2', array( '' ) );
		echo 'Response:<pre>';
		var_dump($response);
		echo '</pre>';

	}

	/**
	 * [html_show_posts description]
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	public function html_show_posts( $atts ) {

		$attributes = shortcode_atts( 
			array( 'posts' => 1 ),
			$atts );

		$posts = 0;

		if ( isset ( $attributes['posts'] ) ) {
			$posts = $attributes['posts'];
		}

		$response = wp_remote_get( site_url()."/wp-json/wp/v2/posts/?page=1&per_page={$posts}", array( '' ) );

		$wt = new WT_Wordpress;
		$wt->html( wp_remote_retrieve_body( $response ) );

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