<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: WPBookList BookFinder Extension
Plugin URI: https://www.jakerevans.com
Description: Bookfinder for WPBookList Extensions that wish to insert a new tab in one of the WPBookList Submenu pages
Version: 1.2.0
Author: Jake Evans - Forward Creation
Author URI: https://www.jakerevans.com
License: GPL2
*/ 

/*
CHANGELOG
= 1.0.1 =
	1. Fixed link to the API tab
	2. Introduced nosleep.js to prevent computers from sleeping during upload process
= 1.0.2 =
	1. Fixed a bug that prevented Pages from being automatically created
= 1.1.0 =
	1. Added support for creating WooCommerce Products in conjunction with the StoreFront Extension
= 1.2.0 =
	1. Fixed bug that prevented correct checkmarked books from being added
*/

global $wpdb;
require_once('includes/bookfinder-functions.php');
require_once('includes/bookfinder-ajaxfunctions.php');

// Root plugin folder URL of this extension
define('BOOKFINDER_ROOT_URL', plugins_url().'/wpbooklist-bookfinder/');

// Grabbing database prefix
define('BOOKFINDER_PREFIX', $wpdb->prefix);

// Root plugin folder directory for this extension
define('BOOKFINDER_ROOT_DIR', plugin_dir_path(__FILE__));

// Root Image Icons URL of this extension
define('BOOKFINDER_ROOT_IMG_ICONS_URL', BOOKFINDER_ROOT_URL.'assets/img/');

// Root Classes Directory for this extension
define('BOOKFINDER_CLASS_DIR', BOOKFINDER_ROOT_DIR.'includes/classes/');

// Root JS Directory for this extension
define('BOOKFINDER_JS_DIR', BOOKFINDER_ROOT_DIR.'assets/js/');

// Root JS URL for this extension
define('BOOKFINDER_JS_URL', BOOKFINDER_ROOT_URL.'assets/js/');

// Root CSS URL for this extension
define('BOOKFINDER_ROOT_CSS_URL', BOOKFINDER_ROOT_URL.'assets/css/');

// Adding the front-end ui css file for this extension
add_action('wp_enqueue_scripts', 'wpbooklist_jre_bookfinder_frontend_ui_style');

// Adding the admin css file for this extension
add_action('admin_enqueue_scripts', 'wpbooklist_jre_bookfinder_admin_style' );

/* For bookfinder books by ISBN number
add_action( 'admin_footer', 'wpbooklist_bookfinder_action_javascript' );
add_action( 'wp_ajax_wpbooklist_bookfinder_action', 'wpbooklist_bookfinder_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_bookfinder_action', 'wpbooklist_bookfinder_action_callback' );
*/

// Adding the front-end library shortcode
add_shortcode('wpbooklist_bookfinder', 'wpbooklist_jre_bookfinder_shortcode_function');

// For adding books
add_action( 'wp_footer', 'wpbooklist_bookfinder_addbooks_action_javascript' );
add_action( 'admin_footer', 'wpbooklist_bookfinder_addbooks_action_javascript' );
add_action( 'wp_ajax_wpbooklist_bookfinder_addbooks_action', 'wpbooklist_bookfinder_addbooks_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_bookfinder_addbooks_action', 'wpbooklist_bookfinder_addbooks_action_callback' );

// For searching for books
add_action( 'wp_footer', 'wpbooklist_bookfinder_search_action_javascript' );
add_action( 'admin_footer', 'wpbooklist_bookfinder_search_action_javascript' );
add_action( 'wp_ajax_wpbooklist_bookfinder_search_action', 'wpbooklist_bookfinder_search_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_bookfinder_search_action', 'wpbooklist_bookfinder_search_action_callback' );

// For displaying in colorbox
add_action( 'wp_footer', 'wpbooklist_bookfinder_colorbox_action_javascript' );
add_action( 'admin_footer', 'wpbooklist_bookfinder_colorbox_action_javascript' );
add_action( 'wp_ajax_wpbooklist_bookfinder_colorbox_action', 'wpbooklist_bookfinder_colorbox_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_bookfinder_colorbox_action', 'wpbooklist_bookfinder_colorbox_action_callback' );


// Code for adding file that prevents computer sleep during the bookfinder process
add_action('admin_enqueue_scripts', 'wpbooklist_jre_bookfinder_sleep_script' );
/*
 * Function that utilizes the filter in the core WPBookList plugin, resulting in a new tab. Possible options for the first argument in the 'Add_filter' function below are:
 *  - 'wpbooklist_add_tab_books'
 *  - 'wpbooklist_add_tab_display'
 *
 *
 *
 * The instance of "Bookfinder" in the $extra_tab array can be replaced with whatever you want - but the 'bookfinder' instance MUST be your one-word descriptor.
*/
add_filter('wpbooklist_add_tab_books', 'wpbooklist_bookfinder_tab');
function wpbooklist_bookfinder_tab($tabs) {
 	$extra_tab = array(
		'bookfinder'  => __("BookFinder", 'plugin-textdomain'),
	);
 
	// combine the two arrays
	$tabs = array_merge($tabs, $extra_tab);
	return $tabs;
}

?>