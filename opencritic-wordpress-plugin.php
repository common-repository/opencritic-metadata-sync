<?php
/**
 * Plugin Name: OpenCritic Metadata Sync
 * Plugin URI:  https://portal.opencritic.com/wordpress-plugin
 * Description: Submit review metadata to OpenCritic when new reviews are published.
 * Version: 1.0.0
 * Text Domain:opencritic
 * Author: OpenCritic
 * Author URI: https://opencritic.com
 * @package Custom work addons for Woo PDF Vouchers
 * @category Core
 * @author OpenCritic
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/


if( !defined( 'WP_OPENCRITIC_DIR' ) ) {
  define( 'WP_OPENCRITIC_DIR', dirname( __FILE__ ) );      // Plugin dir
}
if( !defined( 'WP_OPENCRITIC_VERSION' ) ) {
  define( 'WP_OPENCRITIC_VERSION', '1.0.0' );      // Plugin Version
}
if( !defined( 'WP_OPENCRITIC_URL' ) ) {
  define( 'WP_OPENCRITIC_URL', plugin_dir_url( __FILE__ ) );   // Plugin url
}
if( !defined( 'WP_OPENCRITIC_INC_DIR' ) ) {
  define( 'WP_OPENCRITIC_INC_DIR', WP_OPENCRITIC_DIR.'/includes' );   // Plugin include dir
}

if( !defined( 'WP_OPENCRITIC_ADMIN_DIR' ) ) {
  define( 'WP_OPENCRITIC_ADMIN_DIR', WP_OPENCRITIC_INC_DIR.'/admin' );  // Plugin admin dir
}

// Global variables
global $Wp_Oc_Admin,$vou_Bedu_Scripts;

// Admin class handles most of admin panel functionalities of plugin
include_once( WP_OPENCRITIC_INC_DIR.'/class-wp-opencritic-scripts.php' );
$vou_Bedu_Scripts = new vou_Bedu_Scripts();
$vou_Bedu_Scripts->add_hooks();


// Admin class handles most of admin panel functionalities of plugin
include_once( WP_OPENCRITIC_ADMIN_DIR.'/class-wp-opencritic-admin.php' );
$Wp_Oc_Admin = new Wp_Oc_Admin();
$Wp_Oc_Admin->add_hooks();


// Admin class handles most of admin panel functionalities of plugin
include_once( WP_OPENCRITIC_INC_DIR.'/wp-opencritic-misc-function.php' );
//wp_opencritic_search_game();