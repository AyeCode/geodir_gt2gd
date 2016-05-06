<?php
/**
 * Contains the hook actions used for the plugin
 *
 * @package GeoTheme_To_GeoDirectory
 * @since 1.0.0
 */

// MUST have WordPress.
if ( !defined( 'WPINC' ) ) {
    exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

// Admin hooks.
if ( is_admin() ) {
    add_action( 'admin_init', 'geodir_gt2gd_admin_init' );

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { // Ajax mode.
        // Handle ajax request.
        add_action( 'wp_ajax_gt2gd_ajax', 'geodir_gt2gd_ajax' );
        add_action( 'wp_ajax_nopriv_gt2gd_ajax', 'geodir_gt2gd_ajax' );
    } else {		
        add_action( 'admin_menu', 'geodir_gt2gd_admin_menu' );
        
        $is_gt2gd = !empty( $_REQUEST['page'] ) && ( $_REQUEST['page'] == 'gt2gd' || strpos( $_REQUEST['page'], 'gt2gd-' ) === 0 ) ? true : false;
        
        if ( $is_gt2gd ) {
            add_action( 'admin_enqueue_scripts', 'geodir_gt2gd_enqueue_scripts', 10 );
            add_action( 'admin_notices', 'geodir_gt2gd_admin_notices' );
        }
    }
}
