<?php
/**
 * Contains functions related to GeoTheme To GeoDirectory upgrade plugin
 *
 * @package GeoTheme_To_GeoDirectory
 * @since 1.0.0
 */
 
// MUST have WordPress.
if ( !defined( 'WPINC' ) ) {
	exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

if ( get_option( 'geodir_gt2gd_db_version' ) != GT2GD_VERSION ) {
	add_action( 'plugins_loaded', 'geodir_gt2gd_upgrade_all' );
	
	update_option( 'geodir_gt2gd_db_version',  GT2GD_VERSION );
}

/**
 * Handles upgrade for all geotheme to geodirectory versions.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_upgrade_all() {
	// Do stuff here.
}
