<?php
/**
 * This is the main GeoTheme To GeoDirectory plugin file, here we declare and call the important stuff
 *
 * @package GeoTheme_To_GeoDirectory
 * @since 1.0.0
 */
 
/*
Plugin Name: GeoTheme To GeoDirectory
Plugin URI: http://wpgeodirectory.com/
Description: GeoTheme To GeoDirectory plugin provides tool to convert GeoTheme directory listings in to GeoDirectory directory listings.
Version: 1.0.0
Author: GeoDirectory
Author URI: http://wpgeodirectory.com/
License: GPLv3
*/

// MUST have WordPress.
if ( !defined( 'WPINC' ) ) {
    exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

// Define constants.
define( 'GT2GD_VERSION', '1.0.0' );
define( 'GT2GD_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) );
define( 'GT2GD_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) );

if ( is_admin() ) {
    if ( !function_exists( 'is_plugin_active' ) ) {
        /**
         * Include WordPress plugin core file to use core functions to check for active plugins.
         */
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    /**
     * Include any functions needed for upgrades.
     *
     * @since 1.0.0
     */
    require_once( GT2GD_PLUGIN_DIR . '/upgrade.php' );
}

// Load plugin textdomain.
add_action( 'plugins_loaded', 'geodir_gt2gd_load_textdomain' );

/**
 * Include the main functions related to the plugin.
 */
require_once( GT2GD_PLUGIN_DIR . '/includes/geodir_gt2gd_functions.php' );
/**
 * Include the hook actions used for the plugin.
 */
require_once( GT2GD_PLUGIN_DIR . '/includes/geodir_gt2gd_actions.php' );

if ( is_admin() ) {
    register_activation_hook( __FILE__ , 'geodir_gt2gd_activation' );
    register_deactivation_hook( __FILE__ , 'geodir_gt2gd_deactivation' );
    register_uninstall_hook( __FILE__, 'geodir_gt2gd_uninstall' );
}

add_action( 'activated_plugin', 'geodir_gt2gd_plugin_activated' );
