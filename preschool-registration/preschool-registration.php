<?php
/**
 * Plugin Name: PreSchool Registration
 * Description: Simple plugin for pre-school registration
 * Author: Anonymous
 * Version: 1.0.0
 * Author URI: http://www.example.com
 */

if(!defined('ABSPATH')){
    echo 'No Access';
    exit();
}

class preschool_registration
{

    public function __construct()
    {
        add_action('init', array($this, 'create_custom_post_type'));
        add_action('rest_api_init', array($this, 'register_rest_api'));
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }

    }

    public function create_custom_post_type()
    {
            $args = array(
                'labels' => array(
                    'name' => __( 'Registrations' ),
                    'singular_name' => __( 'Registration' )
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array( 'title', 'editor', 'custom_fields' ),
                'rewrite' => array('slug' => 'registrations'),
                'menu_icon' => 'dashicons-edit-page',
            );
            register_post_type('registrations', $args);
    }

    public function init_metabox(){
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ));
        add_action( 'save_post_registrations', array( $this, 'save_metabox' ), 10, 2 );
    }

    public function add_metabox(){
        add_meta_box('pr_metabox_name', 'Pre-School Information', array($this, 'pr_metabox_display'), 'registrations');
    }

    public function pr_metabox_display($post){;
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_content_offesrnonce_action', 'custom_content_offesrnonce' );
        $pr_name = get_post_meta( $post->ID, 'pr-name', true );
        $pr_address = get_post_meta( $post->ID, 'pr-address', true );
        $pr_time = get_post_meta( $post->ID, 'pr-time', true );
        $pr_location = get_post_meta( $post->ID, 'pr-location', true );

        if( empty( $pr_name ) ) $pr_name = '';
        if( empty( $pr_address ) ) $pr_address = '';
        if( empty( $pr_time ) ) $pr_time = '';
        if( empty( $pr_location ) ) $pr_location = '';

        include plugin_dir_path(__FILE__).'meta_display.php';
    }

    public function save_metabox( $post_id, $post) {

        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['custom_content_offesrnonce'] ) ? $_POST['custom_content_offesrnonce'] : '';
        $nonce_action = 'custom_content_offesrnonce_action';

        // Check if a nonce is set.
        if ( ! isset( $nonce_name ) )
            return;

        // Check if a nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
            return;

        // Check if it's not an autosave.
        if ( wp_is_post_autosave( $post_id ) )
            return;

        // Sanitize user input.
        $pr_name = isset( $_POST[ 'pr-name' ] ) ? sanitize_text_field( $_POST[ 'pr-name' ] ) : '';
        $pr_address = isset( $_POST[ 'pr-address' ] ) ? sanitize_text_field( $_POST[ 'pr-address' ] ) : '';
        $pr_time = isset( $_POST[ 'pr-time' ] ) ? sanitize_text_field( $_POST[ 'pr-time' ] ) : '';
        $pr_location = isset( $_POST[ 'pr-location' ] ) ? sanitize_text_field( $_POST[ 'pr-location' ] ) : '';

        // Update the meta field in the database.
        update_post_meta( $post_id, 'pr-name', $pr_name );
        update_post_meta( $post_id, 'pr-address', $pr_address );
        update_post_meta( $post_id, 'pr-time', $pr_time );
        update_post_meta( $post_id, 'pr-location', $pr_location );

    }

    public function register_rest_api(){
        register_rest_route('wp/v2', 'preschool-registration/(?P<id>\d+)', array(
            'method' => 'GET',
            'callback' => array($this, 'get_registration'),
            'permission_callback' => '__return_true'
        ));
//        register_rest_route('wp/v2', 'preschool-registration(?P<registration_time>\d{4}-\d{2}-\d{2}+)', array(
//            'method' => 'GET',
//            'callback' => array($this, 'get_registration_time'),
//            'permission_callback' => '__return_true'
//        ));
        register_rest_route('wp/v2', 'preschool-registration', array(
            'method' => 'GET',
            'callback' => array($this, 'get_registrations'),
            'permission_callback' => '__return_true'
        ));
    }

//    public function get_registration_time($request){
//        $time = $request->get_param('registration_time');
//        $data = [];
//        $i=0;
//        $args = [
//            'post_type' => 'registrations'
//        ];
//
//        $posts = get_posts($args);
//        foreach ($posts as $post){
//            $reg_time = get_post_meta($post->ID, 'pr-time', true);
//            if($reg_time == $time){
//                $data[$i]['id'] = $post->ID;
//                $data[$i]['regitration_title'] = $post->post_title;
//                $data[$i]['pre_school'] = get_post_meta($post->ID, 'pr-name', true);
//                $data[$i]['address'] = get_post_meta($post->ID, 'pr-address', true);
//                $data[$i]['registration_time'] = get_post_meta($post->ID, 'pr-time', true);
//                $data[$i]['location'] = get_post_meta($post->ID, 'pr-location', true);
//            }
//            $i++;
//        }
//        return $data;
//    }

    public function get_registration($request){
//        global $wpdb;

//        $table_name = $wpdb->prefix.'posts';
//        $results = $wpdb->get_results("SELECT * from $table_name where ID = $id");
//        return $results;
        $data = [];
        $id = $request['id'];
        $i=0;
        $args = [
            'post_type' => 'registrations'
        ];

        $posts = get_posts($args);
        foreach ($posts as $post){
            if($id == $post->ID){
                $data[$i]['id'] = $post->ID;
                $data[$i]['registration_title'] = $post->post_title;
                $data[$i]['pre_school'] = get_post_meta($post->ID, 'pr-name', true);
                $data[$i]['address'] = get_post_meta($post->ID, 'pr-address', true);
                $data[$i]['registration_time'] = get_post_meta($post->ID, 'pr-time', true);
                $data[$i]['location'] = get_post_meta($post->ID, 'pr-location', true);
        }
    }
        return $data;
    }
    public function get_registrations($request){
//        global $wpdb;
//        $table_name = $wpdb->prefix.'posts';
//        $results = $wpdb->get_results("SELECT * from $table_name where post_type = 'registrations'");
//        return $results;
        $data = [];
        $single = [];
        $time = $request->get_param('registration_time');
//        return $time;
        $i = 0;
        $args = [
            'post_type' => 'registrations'
        ];

        $posts = get_posts($args);
        foreach ($posts as $post){
            $reg_time = get_post_meta($post->ID, 'pr-time', true);
            if($reg_time === $time){
                $single[$i]['id'] = $post->ID;
                $single[$i]['registration_title'] = $post->post_title;
                $single[$i]['pre_school'] = get_post_meta($post->ID, 'pr-name', true);
                $single[$i]['address'] = get_post_meta($post->ID, 'pr-address', true);
                $single[$i]['registration_time'] = get_post_meta($post->ID, 'pr-time', true);
                $single[$i]['location'] = get_post_meta($post->ID, 'pr-location', true);
                return $single;
            }else{
                $data[$i]['id'] = $post->ID;
                $data[$i]['registration_title'] = $post->post_title;
                $data[$i]['pre_school'] = get_post_meta($post->ID, 'pr-name', true);
                $data[$i]['address'] = get_post_meta($post->ID, 'pr-address', true);
                $data[$i]['registration_time'] = get_post_meta($post->ID, 'pr-time', true);
                $data[$i]['location'] = get_post_meta($post->ID, 'pr-location', true);
            }
            $i++;
        }

        return $data;
    }
}

new preschool_registration;


