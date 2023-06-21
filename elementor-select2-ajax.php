<?php
/**
 * Plugin Name: Elementor Select2 Ajax
 * Plugin URI:  http://habibportfolio.com/
 * Version:     1.0.0
 * Author:      Habib
 * Author URI:  http://habibportfolio.com/
 * Text Domain: elementor-select2-ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Emoji One Area Control.
 *
 * Include control file and register control class.
 *
 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
 *
 * @return void
 * @since 1.0.0
 */
function register_custom_elementor_control( $controls_manager ) {

	require_once( __DIR__ . '/controls/emojionearea.php' );
	require_once( __DIR__ . '/controls/rtajaxselect.php' );

	$controls_manager->register( new \Elementor_EmojiOneArea_Control() );
	$controls_manager->register( new \RTSelect2() );

}

add_action( 'elementor/controls/register', 'register_custom_elementor_control' );

/**
 * Register Test Widget.
 *
 * Include widget file and register widget class.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 *
 * @return void
 * @since 1.0.0
 */
function register_test_widget( $widgets_manager ) {

	require_once( __DIR__ . '/widgets/test-widget.php' );

	$widgets_manager->register( new \Elementor_Test_Widget() );

}

add_action( 'elementor/widgets/register', 'register_test_widget' );

add_action( 'wp_ajax_rt_select2_object_search', 'select2_ajax_posts_filter_autocomplete' );
add_action( 'wp_ajax_nopriv_rt_select2_object_search', 'select2_ajax_posts_filter_autocomplete' );

function select2_ajax_posts_filter_autocomplete() {

	$post_type   = 'post';
	$source_name = 'post_type';
	$paged       = $_POST['page'] ?? 1;

	if ( ! empty( $_POST['post_type'] ) ) {
		$post_type = sanitize_text_field( $_POST['post_type'] );
	}

	if ( ! empty( $_POST['source_name'] ) ) {
		$source_name = sanitize_text_field( $_POST['source_name'] );
	}

	$search  = ! empty( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
	$results = $post_list = [];
	switch ( $source_name ) {
		case 'taxonomy':
			$args = [
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'search'     => $search,
				'number'     => '5',
			];

			if ( $post_type !== 'all' ) {
				$args['taxonomy'] = $post_type;
			}

			$post_list = wp_list_pluck( get_terms( $args ), 'name', 'term_id' );
			break;
		case 'user':
			$users = [];

			foreach ( get_users( [ 'search' => "*{$search}*" ] ) as $user ) {
				$user_id           = $user->ID;
				$user_name         = $user->display_name;
				$users[ $user_id ] = $user_name;
			}

			$post_list = $users;
			break;
		default:
			$post_list = get_query_data( $post_type, 30, $search, $paged );
	}

	$pagination = false;
	if ( ! empty( $post_list ) ) {
		$pagination = true;
		foreach ( $post_list as $key => $item ) {
			$results[] = [ 'text' => $item, 'id' => $key ];
		}
	}
	wp_send_json( [ 'results' => $results, 'pagination' => [ 'more' => $pagination ] ] );
//	wp_send_json( [ 'results' => $results ] );
}


//Set saved data to select2
add_action( 'wp_ajax_rt_select2_get_title', 'select2_ajax_get_posts_value_titles' );
add_action( 'wp_ajax_nopriv_rt_select2_get_title', 'select2_ajax_get_posts_value_titles' );
function select2_ajax_get_posts_value_titles() {

	if ( empty( $_POST['id'] ) ) {
		wp_send_json_error( [] );
	}

	if ( empty( array_filter( $_POST['id'] ) ) ) {
		wp_send_json_error( [] );
	}
	$ids         = array_map( 'intval', $_POST['id'] );
	$source_name = ! empty( $_POST['source_name'] ) ? sanitize_text_field( $_POST['source_name'] ) : '';

	switch ( $source_name ) {
		case 'taxonomy':
			$args = [
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'include'    => implode( ',', $ids ),
			];

			if ( $_POST['post_type'] !== 'all' ) {
				$args['taxonomy'] = sanitize_text_field( $_POST['post_type'] );
			}

			$response = wp_list_pluck( get_terms( $args ), 'name', 'term_id' );
			break;
		case 'user':
			$users = [];

			foreach ( get_users( [ 'include' => $ids ] ) as $user ) {
				$user_id           = $user->ID;
				$user_name         = $user->display_name;
				$users[ $user_id ] = $user_name;
			}

			$response = $users;
			break;
		default:
			$post_info = get_posts( [
				'post_type' => sanitize_text_field( $_POST['post_type'] ),
				'include'   => implode( ',', $ids )
			] );
			$response  = wp_list_pluck( $post_info, 'post_title', 'ID' );
	}

	if ( ! empty( $response ) ) {
		wp_send_json_success( [ 'results' => $response ] );
	} else {
		wp_send_json_error( [] );
	}
}

//Helper Func

function get_query_data( $post_type = 'any', $limit = 10, $search = '', $paged = 1 ) {
	global $wpdb;
	$where = '';
	$data  = [];

	if ( - 1 == $limit ) {
		$limit = '';
	} elseif ( 0 == $limit ) {
		$limit = "limit 0,1";
	} else {
		$offset = 0;
		if ( $paged ) {
			$offset = ( $paged - 1 ) * $limit;
		}
		$limit = $wpdb->prepare( " limit %d, %d", esc_sql( $offset ), esc_sql( $limit ) );
	}

	if ( 'any' === $post_type ) {
		$in_search_post_types = get_post_types( [ 'exclude_from_search' => false ] );
		if ( empty( $in_search_post_types ) ) {
			$where .= ' AND 1=0 ';
		} else {
			$where .= " AND {$wpdb->posts}.post_type IN ('" . join( "', '",
					array_map( 'esc_sql', $in_search_post_types ) ) . "')";
		}
	} elseif ( ! empty( $post_type ) ) {
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_type = %s", esc_sql( $post_type ) );
	}

	if ( ! empty( $search ) ) {
		$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", '%' . esc_sql( $search ) . '%' );
	}

	$query   = "select post_title,ID  from $wpdb->posts where post_status = 'publish' {$where} {$limit}";
	$results = $wpdb->get_results( $query );

	if ( ! empty( $results ) ) {
		foreach ( $results as $row ) {
			$data[ $row->ID ] = $row->post_title;
		}
	}

	return $data;
}