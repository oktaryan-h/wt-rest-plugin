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
	function create($data) {

		$url = get_rest_url() . 'wp/v2/posts/';

		$response = wp_remote_post( 
			$url,
			array(
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
		}
	}

	/**
	 * [delete description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	function delete( $post_id ) {

		$url = get_rest_url() . "wp/v2/posts/{$post_id}";

		$response = wp_remote_request( 
			$url,
			array(
				'method' => 'DELETE',
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
		}
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

		$url = get_rest_url()."wp/v2/posts/?page=1&per_page={$posts}";

		$response = wp_remote_get( 
			$url,
			array(
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'user:1234' ) ),
				'cookies' => array()
			)
		);

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
			echo '<p><a href="' . add_query_arg( array( 'post_id' => $a['id'] ), network_site_url() . 'rest-edit/' ) . '">Edit</a></p>';
			echo '<p><a href="' . add_query_arg( array( 'delete_post' => $a['id'] ), get_permalink() ) . '">Delete</a></p>';
			echo '</div>';
		}

	}

	function html_create_form() {

		if ( isset( $_POST['submit'] ) ) {
			$data = array(
				'title' => ( isset( $_POST['title'] ) ) ? sanitize_text_field( $_POST['title'] ) : '' ,
				'content' => ( isset( $_POST['content'] ) ) ? sanitize_text_field( $_POST['content'] ) : '' ,
				'status' => ( isset( $_POST['status'] ) ) ? sanitize_text_field( $_POST['status'] ) : '' ,
			);
			$this->create( $data );
		}

		?>

		<form action="" method="POST">
			<p>
				Title :
				<input type="text" name="title" value="">
			</p>
			<p>
				Content :
				<input type="textarea" name="content" value="">
			</p>
			<p>
				Publish :
				<select name="status">
					<option value="publish" selected>Publish</option>
					<option value="draft">Draft</option>
				</select>
			</p>
			<p>
				<input type="submit" name="submit" value="Submit">
			</p>
		</form>

		<?php

	}

	function html_edit_form() {

		$post_id = ( isset( $_GET['post_id'] ) ) ? $_GET['post_id'] : 0 ;

		if ( 0 == $post_id ) {
			echo 'No post id defined';
			return;
		}

		if ( isset( $_POST['submit'] ) ) {
			$data = array(
			 'title' => ( isset( $_POST['title'] ) ) ? sanitize_text_field( $_POST['title'] ) : '' ,
			 'content' => ( isset( $_POST['content'] ) ) ? sanitize_text_field( $_POST['content'] ) : '' ,
			 'status' => ( isset( $_POST['status'] ) ) ? sanitize_text_field( $_POST['status'] ) : '' ,
			);
			$this->update( $post_id, $data );
		}

		$url = get_rest_url()."wp/v2/posts/{$post_id}";

		$response = wp_remote_get( 
			$url,
			array(
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( 'user:1234' ) ),
				'cookies' => array()
			)
		);

		$source = wp_remote_retrieve_body( $response );

		$json_decoded = json_decode( $source, true );

			$a = $json_decoded;
			$x['id'] = ( isset( $a['id'] ) ) ? $a['id'] : '';
			$x['title'] = ( isset( $a['title']['rendered'] ) ) ? $a['title']['rendered'] : '';
			$x['content'] = ( isset( $a['content']['rendered'] ) ) ? $a['content']['rendered'] : '';
			$x['status'] = ( isset( $a['status'] ) ) ? $a['status'] : '';
		?>

		<form action="<?php echo add_query_arg( array( 'post_id' => $x['id'] ), the_permalink() ) ?>" method="POST">
			<p>
				Title :
				<input type="text" name="title" value="<?php echo ( isset( $x['title'] ) ) ? $x['title'] : ''; ?>">
			</p>
			<p>
				Content :
				<input type="textarea" name="content" value="<?php echo ( isset( $x['content'] ) ) ? $x['content'] : ''; ?>">
			</p>
			<p>
				Publish :
				<select name="status">
					<option value="publish" <?php echo ( isset( $x['status'] ) && $x['status'] == 'publish' ) ? 'selected' : ''; ?>>Publish</option>
					<option value="draft" <?php echo ( isset( $x['status'] ) && $x['status'] == 'draft' ) ? 'selected' : ''; ?>>Draft</option>
				</select>
			</p>
			<p>
				<input type="submit" name="submit" value="Submit">
			</p>
		</form>

		<?php

	}

	/**
	 * [shortcode description]
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	function shortcode( $atts ) {

		if ( isset( $_GET['delete_post'] ) ) {
			$this->delete( sanitize_text_field( $_GET['delete_post'] ) );
		}

		ob_start();

		//$this->create();

		//$this->update( 41, array('title' => 'rest-FULL') );

		$this->html_show_posts( $atts );

		return ob_get_clean();
	}

	function create_form() {

		ob_start();

		$this->html_create_form();

		return ob_get_clean();
	}

	function edit_form() {

		ob_start();

		$this->html_edit_form();

		return ob_get_clean();
	}


}

$wt_rest = new WT_REST_API;

add_shortcode( 'rest-full', array( $wt_rest, 'shortcode' ) );
add_shortcode( 'rest-create', array( $wt_rest, 'create_form' ) );
add_shortcode( 'rest-edit', array( $wt_rest, 'edit_form' ) );