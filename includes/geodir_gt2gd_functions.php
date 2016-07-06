<?php
/**
 * Contains the main functions related to the plugin
 *
 * @package GeoTheme_To_GeoDirectory
 * @since 1.0.0
 */
 
// MUST have WordPress.
if ( !defined( 'WPINC' ) ) {
    exit( 'Do NOT access this file directly: ' . basename( __FILE__ ) );
}

/**
 * Plugin activation hook.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_activation() {
    // Plugin activation stuff here.
}

/**
 * Plugin deactivation hook.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_deactivation() {
    // Plugin deactivation stuff here.
}

/**
 * Plugin uninstall hook.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_uninstall() {
    // Plugin uninstall stuff here.
}

/**
 * Hook for plugin activated.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_plugin_activated() {
    // Plugin activated stuff here.
}

/**
 * Load the plugin textdomain.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_load_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), 'geodir_gt2gd' );

    load_textdomain( 'geodir_gt2gd', WP_LANG_DIR . '/geodir_gt2gd/geodir_gt2gd-' . $locale . '.mo' );
    load_plugin_textdomain( 'geodir_gt2gd', false, GT2GD_PLUGIN_DIR . '/languages' );

    /**
     * Define language constants.
     */
    require_once( GT2GD_PLUGIN_DIR . '/language.php' );
}

/**
 * Loads on wordpress admin init.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_admin_init() {
    // Do stuff here.
}

/**
 * Enqueue the plugin style & script files.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_enqueue_scripts() {
    wp_register_style( 'gt2gd-progressbar-style', GT2GD_PLUGIN_URL . '/assets/css/bootstrap-progressbar.min.css', array(), '3.3.4' );
    wp_enqueue_style( 'gt2gd-progressbar-style' );
    
    // Register and enqueue the style.
    wp_register_style( 'gt2gd-style', GT2GD_PLUGIN_URL . '/assets/css/style.css', array(), GT2GD_VERSION );
    wp_enqueue_style( 'gt2gd-style' );
    
    wp_register_script( 'gt2gd-progressbar-script', GT2GD_PLUGIN_URL . '/assets/js/bootstrap-progressbar.min.js', array('jquery'), '3.3.4' );
    wp_enqueue_script( 'gt2gd-progressbar-script' );
    
    // Register the script.
    wp_register_script( 'gt2gd-script', GT2GD_PLUGIN_URL . '/assets/js/script.js', array(), GT2GD_VERSION );

    $item = geodir_gt2gd_conversion_items( true, 'first' );

    // Localize the script with new data.
    $javascript_vars = array(
        'plugin_url' => GT2GD_PLUGIN_URL,
        'nonce' => wp_create_nonce( 'geodir_gt2gd_nonce' ),
        'msg_confirm' => esc_attr( __( 'Are you sure you want to start GeoTheme To GeoDirectory conversion? It CAN NOT BE UNDONE so we recommends to take full backup before starting conversion.', 'geodir_gt2gd' ) ),
        'msg_completed' => esc_attr( __( 'No item remain for GeoTheme to GeoDirectory conversion!', 'geodir_gt2gd' ) ),
        'msg_gt2gd_done' => esc_attr( __( 'That\'s it! GeoTheme To GeoDirectory conversion has been completed. You can enjoy with GeoDirectory after disabling GeoTheme :)', 'geodir_gt2gd' ) ),
        'txt_converting' => __( 'Converting...', 'geodir_gt2gd' ),
        'first_item' => !empty($item) && isset($item['id']) ? $item['id'] : ''
    );
    wp_localize_script( 'gt2gd-script', 'GT2GD', $javascript_vars );

    // Enqueued script with localized data.
    wp_enqueue_script( 'gt2gd-script' );
}

/**
 * Set up link in the admin menu.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_admin_menu() {
    add_menu_page( 'GeoTheme To GeoDirectory', 'GT To GD', 'manage_options', 'gt2gd', 'geodir_gt2gd_dashboard', GT2GD_PLUGIN_URL . '/assets/images/favicon.ico', 73.1 );
}

/**
 * Displays notice for the plugin.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_admin_notices() {
    ?>
    <div class="updated settings-error notice-warning" id="gt2gd_notice_msg"><p><?php _e( '<strong>Warning:</strong> GeoTheme To GeoDirectory conversion can not be undone, so please take backup before start conversion.', 'geodir_gt2gd' );?></p></div>
    <?php
}

/**
 * Set up the content for dashboard page to manage conversion.
 *
 * @since 1.0.0
 */
function geodir_gt2gd_dashboard() {
    $items = geodir_gt2gd_conversion_items(false);

    $is_event_active = geodir_gt2gd_is_active( 'geodir_event_manager' );

    $gd_post_types = __( 'Places', 'geodir_gt2gd' );
    if ( $is_event_active ) {
        $gd_post_types .= ', ' . __( 'Events', 'geodir_gt2gd' );
    }

    $first_item = geodir_gt2gd_conversion_items( true, 'first' );

    $active_text = '<font class="gt2gd-found">' . __( '- Found', 'geodir_gt2gd' ) . '</font>';
    $not_active_text = '<font class="gt2gd-not-found">' . __( '- Not Found', 'geodir_gt2gd' ) . '</font>';
    ?>
    <div class="wrap gt2gd-wrap">
        <h1><?php echo esc_html( __( 'GeoTheme To GeoDirectory Conversion', 'geodir_gt2gd' ) ); ?></h1>
        <div class="gt2gd-cols metabox-holder">
            <div class="postbox-container gt2gd-col gt2gd-col-1">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h3 class="hndle ui-sortable-handle"><span><?php _e( 'Convert GeoTheme To GeoDirectory', 'geodir_gt2gd' );?></span></h3>
                        <div class="inside gt2gd-box">
                            <h4><?php _e( 'Post Types:', 'geodir_gt2gd' );?> <font class="gt2gd-ptypes"><?php echo $gd_post_types;?></font></h4>
                            <ul class="gt2gd-steps">
                                <li class="gt2gd-hstatus"><?php _e( 'Item', 'geodir_gt2gd' );?><span class="gt2gd-time"></span><span class="gt2gd-status"><?php _e( 'Status', 'geodir_gt2gd' );?></span></li>
                                <?php foreach ( $items as $id => $item ) { $class = !empty($item['progress']) ? ' gt2gd-progress' : ''; ?>
                                <li id="gt2gd-<?php echo $id;?>" class="gt2gd-s<?php echo $item['status'] . $class;?>"><?php echo $item['title'];?><span class="gt2gd-time"></span><span class="gt2gd-status"><?php echo $item['status_title'];?></span>
                                <?php if (!empty($item['progress']) && !empty($item['total'])) { ?><div class="progress progress-striped"><div class="progress-bar progress-bar-success active" role="progressbar" data-transitiongoal="0" aria-valuemin="0" aria-valuemax="<?php echo (int)$item['total'];?>"></div></div>
                                <?php } ?>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <h3 class="hndle ui-sortable-handle gt2gd-actions">
                        <?php if (!empty($first_item)) { ?>
                        <span><input type="submit" id="gt2gd_submit" name="gt2gd_submit" class="button-primary" value="<?php esc_attr_e( 'Start GT To GD Conversion', 'geodir_gt2gd' );?>"></span>
                        <?php } else { ?>
                        <span class="gt2gd-fmsg"><?php _e( '<strong>Info:</strong> It seems GeoTheme to GeoDirectory conversion was finished or there is no item found for GeoTheme to GeoDirectory conversion.', 'geodir_gt2gd' );?></span>
                        <?php } ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="postbox-container gt2gd-col gt2gd-col-2">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h3 class="hndle ui-sortable-handle"><span><?php _e( 'About GeoTheme To GeoDirectory Conversion', 'geodir_gt2gd' );?></span></h3>
                        <div class="inside gt2gd-reqs">
                            <div class="gt2gd-widget">
                                <h4><?php _e( 'Requirements:', 'geodir_gt2gd' );?></h4>
                                <ul>
                                    <li><?php _e( '- GeoTheme must be installed & should be disabled.', 'geodir_gt2gd' );?></li>
                                    <li><?php _e( '- Fresh installation of GeoDirectory plugin.', 'geodir_gt2gd' );?> <?php echo (geodir_gt2gd_is_active('geodirectory') ? $active_text : $not_active_text);?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="inside gt2gd-reqs">
                            <div class="gt2gd-widget">
                                <h4><?php _e( 'Recommended plugins installed/active on site for better conversion:', 'geodir_gt2gd' );?></h4>
                                <ul>
                                    <li><?php _e( '- GeoDirectory Events', 'geodir_gt2gd' );?><?php echo (geodir_gt2gd_is_active('geodir_event_manager') ? $active_text : $not_active_text);?></li>
                                    <li><?php _e( '- GeoDirectory Location Manager', 'geodir_gt2gd' );?><?php echo (geodir_gt2gd_is_active('geodir_location_manager') ? $active_text : $not_active_text);?></li>
                                    <li><?php _e( '- GeoDirectory Payment Manager', 'geodir_gt2gd' );?><?php echo (geodir_gt2gd_is_active('geodir_payment_manager') ? $active_text : $not_active_text);?></li>
                                    <li><?php _e( '- GeoDirectory Review Rating Manager', 'geodir_gt2gd' );?><?php echo (geodir_gt2gd_is_active('geodir_review_rating_manager') ? $active_text : $not_active_text);?></li>
                                    <li><?php _e( '- GeoDirectory Claim Listing', 'geodir_gt2gd' );?><?php echo (geodir_gt2gd_is_active('geodir_claim_listing') ? $active_text : $not_active_text);?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="inside gt2gd-reqs">
                            <div class="gt2gd-widget">
                                <span class="gt2gd-info"><?php _e( '<b>Info:</b> To start GeoTheme To GeoDirectory conversion, first disable GeoTheme and install & enable GeoDirectory and its addons.', 'geodir_gt2gd' );?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Get the items to convert from GeoTheme to GeoDirectory.
 *
 * @since 1.0.0
 *
 * @param bool $convertable If True then retrieves only convertable items. Default: True.
 * @param string $single Should be from first, last or null.
 * @return null|array Items array.
 */
function geodir_gt2gd_conversion_items( $convertable = true, $single = false ) {
    $listings = geodir_gt2gd_count_total_listings();
    
    $items = array();
    $items['locations'] = array( 'id' => 'locations', 'title' => __( 'Locations', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['prices'] = array( 'id' => 'prices', 'title' => __( 'Prices', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['categories'] = array( 'id' => 'categories', 'title' => __( 'Categories', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['tags'] = array( 'id' => 'tags', 'title' => __( 'Tags', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['listings'] = array( 'id' => 'listings', 'title' => __( 'Listings', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...', 'progress' => true, 'total' => $listings );
    $items['reviews'] = array( 'id' => 'reviews', 'title' => __( 'Reviews & Ratings', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['invoices'] = array( 'id' => 'invoices', 'title' => __( 'Invoices', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );
    $items['claims'] = array( 'id' => 'claims', 'title' => __( 'Claim Listings', 'geodir_gt2gd' ), 'status' => 'start', 'status_title' => '...' );

    $convertable_items = array();

    if (!geodir_gt2gd_is_active('geodir_location_manager')) {
        $items['locations']['status'] = 'na';
        $items['locations']['status_title'] = __( 'n/a', 'geodir_gt2gd' );
    }

    if (!geodir_gt2gd_is_active('geodir_payment_manager')) {
        $items['prices']['status'] = 'na';
        $items['prices']['status_title'] = __( 'n/a', 'geodir_gt2gd' );
        $items['invoices']['status'] = 'na';
        $items['invoices']['status_title'] = __( 'n/a', 'geodir_gt2gd' );
    }

    if (!geodir_gt2gd_is_active('geodir_claim_listing')) {
        $items['claims']['status'] = 'na';
        $items['claims']['status_title'] = __( 'n/a', 'geodir_gt2gd' );
    }

    foreach ($items as $id => $item) {
        if ( get_option( 'geodir_gt2gd_done_' . $id ) ) {
            $item['status'] = 'done';
            $item['status_title'] = __( 'Done', 'geodir_gt2gd' );
        }
        
        if (!geodir_gt2gd_is_active('geodirectory')) {
            $item['status'] = 'na';
            $item['status_title'] = __( 'n/a', 'geodir_gt2gd' );
        }
        
        if ( $convertable && $item['status'] != 'na' && $item['status'] != 'done' ) {
            $convertable_items[$id] = $item;
        }
        
        $items[$id] = $item;
    }

    if ( $convertable ) {
        $items = $convertable_items;
    }

    if ( $single ) {
        $item_values = array_values( $items );
        
        if ( $single == 'first' ) {
            $items = isset($item_values[0]) ? $item_values[0] : array();
        } else if ( $single == 'last' ) {
            $items = $item_values[count($items) - 1];
        }
    }

    return $items;
}

/**
 * Get the next item of current item to convert.
 *
 * @since 1.0.0
 *
 * @param string $item Item name. Ex: listings.
 * @param bool $convertable If True then retrieves only convertable item. Default: True.
 * @return null|array Items array or empty result.
 */
function geodir_gt2gd_next_item( $item, $convertable = true ) {
    $items = geodir_gt2gd_conversion_items( $convertable );

    if ( !isset($items[$item]) ) {
        return NULL;
    }

    $total_items = count( $items );
    $item_keys = array_keys( $items );
    $current_index = array_search( $item, $item_keys );

    if ( $current_index == $total_items - 1 ) {
        $item = $items[$item];
    } else {
        $next_item = $item_keys[$current_index + 1];
        $item = isset( $items[$next_item] ) ? $items[$next_item] : NULL;
    }

    return $item;
}

/**
 * Find GeoTheme installed or not.
 *
 * @since 1.0.0
 *
 * @global string $wp_version Wordpress version.
 *
 * @param bool $installed True if installed.
 * @return bool True if installed.
 */
function geodir_gt2gd_gt_installed( $installed ) {
    global $wp_version;

    if ( $wp_version >= 3.4 ) {
        $theme = wp_get_theme( 'GeoTheme' );
                
        if ( $theme->exists() ) {
            $installed = true;
        }
    } else if ( $wp_version < 3.4 ) {
        $theme_name = get_current_theme();
                
        if ( $theme_name == 'GeoTheme' ) {
            $installed = true;
        }
    }
    return $installed;
}

/**
 * Handle the ajax request for entire plugin.
 *
 * @since 1.0.0
 *
 * @return string Json data.
 */
function geodir_gt2gd_ajax() {    
    // try to set higher limits for export
    @set_time_limit(0);
    @ini_set('max_input_time', 0);
    @ini_set('max_execution_time', 0);
    @ini_set('max_execution_time', 0);
    @ini_set('memory_limit', '512M');
    error_reporting(0);

    $json = array();

    if ( !current_user_can( 'manage_options' ) ) {
        wp_send_json( $json );
        exit;
    }
    
    $task = isset( $_REQUEST['task'] ) ? $_REQUEST['task'] : NULL;
    $nonce = isset( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : NULL;
    $item = isset( $_REQUEST['_item'] ) ? $_REQUEST['_item'] : NULL;
    $first = !empty( $_REQUEST['_f'] ) ? true : false;

    if ( !wp_verify_nonce( $nonce, 'geodir_gt2gd_nonce' ) ) {
        wp_send_json( $json );
        exit;
    }

    $items = geodir_gt2gd_conversion_items();
    if ( !isset($items[$item]) ) {
        wp_send_json( $json );
        exit;
    }

    $total_items = count( $items );
    $next_item = geodir_gt2gd_next_item( $item );

    $json['error'] = NULL;
    $json['next'] = NULL;

    if ( !empty( $next_item ) ) {
        $convert_status = geodir_gt2gd_convert_item( $item, $items[$item] );
        
        if ( $convert_status['status'] == 'done' ) {
            update_option( 'geodir_gt2gd_done_' . $item, 1 );
        }
        
        $json['status'] = $convert_status['status'];
        $json['status_txt'] = $convert_status['status_txt'];
        if (!empty($convert_status['done'])) {
            $json['done'] = $convert_status['done'];
        }
        
        if ($json['status'] == 'batch') {
            $json['next'] = $item;
        } else if ( $next_item['id'] != $item ) {
            $json['next'] = $next_item['id'];
        } else {
            $json['next'] = 'done';
        }
    } else {
        $json['error'] = wp_sprintf( __( 'Requested item "%s" not supported!', 'geodir_gt2gd' ), $item );
        $json['status'] = 'fail';
        $json['status_txt'] = __( 'Fail', 'geodir_gt2gd' );
    }

    wp_send_json( $json );
    exit;
}

/**
 * Start conversion for given item.
 *
 * @since 1.0.0
 *
 * @param string $item Item name. Ex: listings.
 * @param array $item_data Details of item being converted.
 * @return array Status of item conversion.
 */
function geodir_gt2gd_convert_item( $item, $item_data = array() ) {
    $status = array();
    $status['status'] = 'fail';
    $status['status_txt'] = __( 'Fail', 'geodir_gt2gd' );
    
    $return = false;
    $batch = 0;

    switch ( $item ) {
        case 'locations': {
            $return = geodir_gt2gd_convert_locations();
        }
        break;
        case 'prices': {
            $return = geodir_gt2gd_convert_prices();
        }
        break;
        case 'categories': {
            $return = geodir_gt2gd_convert_categories();
        }
        break;
        case 'tags': {
            $return = geodir_gt2gd_convert_tags();
        }
        break;
        case 'listings': {
            $total = !empty($item_data['total']) ? $item_data['total'] : 0;
            $max = $total > 1000 ? min(ceil($total * 10 / 100), 1000) : 100;
            $min = max(ceil($max * 10 / 100), 10);
            
            $limit = rand($min, $max);
            $limit = apply_filters('geodir_gt2gd_convert_listings_limit', $limit);
            
            if (($listings = (int)geodir_gt2gd_count_listings('place')) > 0) {
                $done = geodir_gt2gd_convert_batch_listings('place', $limit);
                $status['done'] = $done;
                
                if ($listings - $done > 0) {
                    $batch = $listings - $done;
                } else if (geodir_gt2gd_is_active('geodir_event_manager') && ($listings = (int)geodir_gt2gd_count_listings('event')) > 0) {
                    $batch = $listings;
                }
            } else if (geodir_gt2gd_is_active('geodir_event_manager') && ($listings = (int)geodir_gt2gd_count_listings('event')) > 0) {
                $done = geodir_gt2gd_convert_batch_listings('event', $limit);
                $status['done'] = $done;
                
                if ($listings - $done > 0) {
                    $batch = $listings - $done;
                }
            }
            
            if (!$batch > 0) {
                $return = true;
            }
        }
        break;
        case 'reviews': {
            $return = geodir_gt2gd_convert_reviews();
        }
        break;
        case 'invoices': {
            $return = geodir_gt2gd_convert_invoices();
        }
        break;
        case 'claims': {
            $return = geodir_gt2gd_convert_claims();
        }
        break;
    }
    
    if ($batch > 0) {
        $status['status'] = 'batch';
        $status['status_txt'] = __( 'Converting...', 'geodir_gt2gd' );
        return $status;
    }

    if ( $return ) {
        $status['status'] = 'done';
        $status['status_txt'] = __( 'Done', 'geodir_gt2gd' );
    }

    return $status;
}

/**
 * Get the city info for city id.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $city_id City id.
 * @return array City info.
 */
function geodir_get_gt_location_info($city_id) {
    global $wpdb;

    $sql = "SELECT *, cityname as city FROM " . $wpdb->prefix . "multicity WHERE cityname NOT LIKE 'EVERYWHERE' AND city_id = '" . (int)$city_id . "' ORDER BY sortorder ASC, is_default DESC";
    $row = $wpdb->get_row( $sql, ARRAY_A );

    $return = array();
    if (!empty($row)) {
        $gd_default_location = (array)geodir_get_default_location();
        $gt_region_country = geodir_gt2gd_gt_region_country($row['city_id']);
        
        if (!empty($gt_region_country)) {
            $return = array();
            $return['location_id'] = $row['city_id'];
            $return['city'] = $row['city'];
            $return['city_slug'] = $row['city_slug'];
            $return['city_latitude'] = $row['lat'];
            $return['city_longitude'] = $row['lng'];
            $return['is_default'] = (int)$row['is_default'];
            $return['city_meta'] = $row['city'];
            $return['city_desc'] = $row['meta_desc'];
            $return['region'] = !empty($gt_region_country['region']) ? $gt_region_country['region'] : $gd_default_location['region'];
            $return['region_slug'] = !empty($gt_region_country['region']) ? $gt_region_country['region_slug'] : $gd_default_location['region_slug'];
            $return['country'] = !empty($gt_region_country['country']) ? $gt_region_country['country'] : $gd_default_location['country'];
            $return['country_slug'] = !empty($gt_region_country['country']) ? $gt_region_country['country_slug'] : $gd_default_location['country_slug'];
        }
    }

    return $return;
}

/**
 * GeoTheme to GeoDirectory locations conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_locations() {
    global $wpdb;

    $gd_default_location = (array)geodir_get_default_location();

    // cities
    $sql = "SELECT *, cityname as city FROM " . $wpdb->prefix . "multicity WHERE cityname NOT LIKE 'EVERYWHERE' ORDER BY sortorder ASC, is_default DESC";
    $rows = $wpdb->get_results( $sql, ARRAY_A );

    $default_location_id = (int)$wpdb->get_var( "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE is_default = '1'" );

    if ( !empty( $rows ) ) {
        foreach ( $rows as $row ) {
            $gt_region_country = geodir_gt2gd_gt_region_country( $row['city_id'] );
            
            $save_location = array();
            $save_location['location_id'] = $row['city_id'];
            $save_location['city'] = $row['city'];
            $save_location['city_slug'] = $row['city_slug'];
            $save_location['city_latitude'] = $row['lat'];
            $save_location['city_longitude'] = $row['lng'];
            if ( !$default_location_id > 0 ) {
                $save_location['is_default'] = (int)$row['is_default'];
            }
            $save_location['city_meta'] = $row['city'];
            $save_location['city_desc'] = isset($row['meta_desc']) ? $row['meta_desc'] : '';
            
            $save_location['region'] = !empty( $gt_region_country['region'] ) ? $gt_region_country['region'] : $gd_default_location['region'];
            $save_location['region_slug'] = !empty( $gt_region_country['region'] ) ? $gt_region_country['region_slug'] : $gd_default_location['region_slug'];
            $save_location['country'] = !empty( $gt_region_country['country'] ) ? $gt_region_country['country'] : $gd_default_location['country'];
            $save_location['country_slug'] = !empty( $gt_region_country['country'] ) ? $gt_region_country['country_slug'] : $gd_default_location['country_slug'];
            
            $exists = geodir_get_location_by_id( array(), $row['city_id'] );
            
            if ( !empty( $exists ) ) {
                $wpdb->update( POST_LOCATION_TABLE, $save_location, array( 'location_id' => $row['city_id'] ) );
            } else {
                $wpdb->insert( POST_LOCATION_TABLE, $save_location );
            }
            
            $seo_info = geodir_location_seo_by_slug($save_location['city_slug'], 'city', $save_location['country_slug'], $save_location['region_slug']);
            
            $seo_data = array();
            $seo_data['seo_title'] = $save_location['city_meta'];
            $seo_data['seo_desc'] = $save_location['city_desc'];
            
            $date = date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                
            if ( !empty( $seo_info ) ) {
                $seo_data['date_updated'] = $date;
                
                $wpdb->update( LOCATION_SEO_TABLE, $seo_data, array( 'seo_id' => $seo_info->seo_id ) );
            } else {
                $seo_data['location_type'] = 'city';
                $seo_data['city_slug'] = $save_location['city_slug'];
                $seo_data['region_slug'] = $save_location['region_slug'];
                $seo_data['country_slug'] = $save_location['country_slug'];
                $seo_data['date_created'] = $date;
                
                $wpdb->insert( LOCATION_SEO_TABLE, $seo_data );
            }
        }
        
        $default_location_id = (int)$wpdb->get_var( "SELECT location_id FROM " . POST_LOCATION_TABLE . " WHERE is_default = '1'" );
        if ( $default_location_id > 0 ) {
            geodir_location_set_default( $default_location_id );
        }
    }
    return true;
}

/**
 * GeoTheme to GeoDirectory price packages conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_prices() {
    global $wpdb;

    $where = !geodir_gt2gd_is_active( 'geodir_event_manager' ) ? "WHERE post_type != 'event'" : "";

    // gt prices
    $sql = "SELECT * FROM " . $wpdb->prefix . "price " . $where . " ORDER BY sort_order ASC";
    $rows = $wpdb->get_results( $sql, ARRAY_A );

    if ( !empty( $rows ) ) {
        $post_types = array();
        
        foreach ( $rows as $row ) {
            $post_type = $row['post_type'] == 'listing' ? 'place' : $row['post_type'];
            $is_default = 0;
            
            if ( !in_array( $post_type, $post_types ) ) {
                $post_types[] = $post_type;
                $is_default = 1;
            }
            
            $title_desc = $row['title_desc'];
            
            if ( trim( $title_desc ) == '' ) {
                $days = $row['days'] > 0 ? $row['days'] : __( 'unlimited', 'geodir_gt2gd' );
                $amount = geodir_payment_price( $row['amount'] );
                
                $title_desc = wp_sprintf( __( '%s : number of publish days are %s (<span id="%s">%s</span>)', 'geodir_gt2gd' ), $row['title'], $days, sanitize_title( $row['title'] ), $amount );
            }
            
            $price = array();
            $price['pid'] = $row['pid'];
            $price['title'] = $row['title'];
            $price['amount'] = $row['amount'];
            $price['cat'] = $row['cat'] != '' ? trim( $row['cat'], ',' ) : '';
            $price['status'] = $row['status'];
            $price['days'] = $row['days'];
            $price['is_default'] = $is_default;
            $price['is_featured'] = $row['is_featured'];
            $price['title_desc'] = $title_desc;
            $price['image_limit'] = $row['image_limit'];
            $price['cat_limit'] = $row['cat_limit'];
            $price['post_type'] = geodir_gt2gd_gd_post_type( $post_type );
            $price['link_business_pkg'] = $row['link_business_pkg'];
            $price['recurring_pkg'] = ($row['recurring_pkg']) ? '0' : '1';//( $post_type == 'event' || $post_type == 'gd_event' ) ? 1 : 0;
            $price['reg_desc_pkg'] = $row['reg_desc_pkg'];
            $price['reg_fees_pkg'] = $row['reg_fees_pkg'];
            $price['downgrade_pkg'] = $row['downgrade_pkg'];
            $price['sub_active'] = $row['sub_active'];
            $price['display_order'] = $row['sort_order'];
            $price['sub_units'] = $row['sub_units'];
            $price['sub_units_num'] = $row['sub_units_num'];
            $price['google_analytics'] = $row['google_analytics'];
            
            $package_info = geodir_get_package_info_by_id( $price['pid'] );
            
            if ( !empty( $package_info ) ) {
                $wpdb->update( $wpdb->prefix . 'geodir_price', $price, array( 'pid' => $package_info->pid ) );
            } else {
                $wpdb->insert( $wpdb->prefix . 'geodir_price', $price );
            }
        }
    }

    // add all default customs fields to all the new price packages.

    //places
    $place_pids = $wpdb->get_results("SELECT pid FROM " . $wpdb->prefix . "geodir_price WHERE post_type='gd_place'");

    if(!empty($place_pids)){
        $place_pids_arr = array();
        foreach($place_pids as $place_pid){
            $place_pids_arr[]=$place_pid->pid;
        }

        $place_packages =  implode(",",$place_pids_arr);

        $wpdb->query("UPDATE " . $wpdb->prefix . "geodir_custom_fields SET packages='$place_packages' WHERE post_type='gd_place' AND is_admin='1'");
    }

    //events
    $event_pids = $wpdb->get_results("SELECT pid FROM " . $wpdb->prefix . "geodir_price WHERE post_type='gd_event'");

    if(!empty($event_pids)){
        $event_pids_arr = array();
        foreach($event_pids as $event_pid){
            $event_pids_arr[]=$event_pid->pid;
        }

        $event_packages =  implode(",",$event_pids_arr);

        $wpdb->query("UPDATE " . $wpdb->prefix . "geodir_custom_fields SET packages='$event_packages' WHERE post_type='gd_event' AND is_admin='1'");
    }



    return true;
}

/**
 * GeoTheme to GeoDirectory listing categories conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_categories() {
    global $wpdb;

    // places categories
    $gt_taxonomy_place = 'placecategory';
    $gd_taxonomy_place = 'gd_placecategory';

    $sql = "UPDATE " . $wpdb->term_taxonomy . " SET taxonomy = '" . $gd_taxonomy_place . "' WHERE taxonomy = '" . $gt_taxonomy_place . "'";
    $wpdb->query( $sql );

    $sql = "SELECT option_name FROM " . $wpdb->options . " WHERE option_name LIKE 'tax_meta_place_%'";
    $rows = $wpdb->get_results( $sql );
    if ( !empty( $rows ) ) {
        foreach ( $rows as $row ) {
            $option_name = str_replace( 'tax_meta_place_', 'tax_meta_gd_place_', $row->option_name );
            
            $sql = "UPDATE " . $wpdb->options . " SET option_name = '" . $option_name . "' WHERE option_name = '" . $row->option_name . "'";
            $wpdb->query( $sql );
        }
    }

    update_option( 'gd_placecategory_children', get_option( 'placecategory_children' ) );

    // event categories
    if ( geodir_gt2gd_is_active( 'geodir_event_manager' ) ) {
        $gt_taxonomy_event = 'eventcategory';
        $gd_taxonomy_event = 'gd_eventcategory';
        
        $sql = "UPDATE " . $wpdb->term_taxonomy . " SET taxonomy = '" . $gd_taxonomy_event . "' WHERE taxonomy = '" . $gt_taxonomy_event . "'";
        $wpdb->query( $sql );
        
        $sql = "SELECT option_name FROM " . $wpdb->options . " WHERE option_name LIKE 'tax_meta_event_%'";
        $rows = $wpdb->get_results( $sql );
        if ( !empty( $rows ) ) {
            foreach ( $rows as $row ) {
                $option_name = str_replace( 'tax_meta_event_', 'tax_meta_gd_event_', $row->option_name );
                
                $sql = "UPDATE " . $wpdb->options . " SET option_name = '" . $option_name . "' WHERE option_name = '" . $row->option_name . "'";
                $wpdb->query( $sql );
            }
        }
        
        update_option( 'gd_eventcategory_children', get_option( 'eventcategory_children' ) );
    }
    
    $wpdb->flush();
    if (!empty($wpdb->queries)) {
        $wpdb->queries = array();
    }

    return true;
}

/**
 * GeoTheme to GeoDirectory listing tags conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_tags() {
    global $wpdb;

    // places tags
    $gt_taxonomy_place = 'place_tags';
    $gd_taxonomy_place = 'gd_place_tags';

    $sql = "UPDATE " . $wpdb->term_taxonomy . " SET taxonomy = '" . $gd_taxonomy_place . "' WHERE taxonomy = '" . $gt_taxonomy_place . "'";
    $wpdb->query( $sql );

    // event tags
    if ( geodir_gt2gd_is_active( 'geodir_event_manager' ) ) {
        $gt_taxonomy_event = 'event_tags';
        $gd_taxonomy_event = 'gd_event_tags';
        
        $sql = "UPDATE " . $wpdb->term_taxonomy . " SET taxonomy = '" . $gd_taxonomy_event . "' WHERE taxonomy = '" . $gt_taxonomy_event . "'";
        $wpdb->query( $sql );
    }

    return true;
}

/**
 * GeoTheme to GeoDirectory listings conversion.
 *
 * @since 1.0.2
 *
 * @global object $wpdb WordPress Database object.
 * @global array $gt2gd_locations Array of location info.
 *
 * @param string $gt_post_type The post type.
 * @param int $limit Limit of GT to GD listing conversion in one batch. Default 100.
 * @return int No. of converted listings.
 */
function geodir_gt2gd_convert_batch_listings($gt_post_type, $limit = 100) {
    global $wpdb, $gt2gd_locations;

    $is_location_active = geodir_gt2gd_is_active('geodir_location_manager');
    $is_payment_active = geodir_gt2gd_is_active('geodir_payment_manager');
    
    $gd_default_location = (array)geodir_get_default_location();
    $default_marker_icon = get_option('geodir_default_marker_icon');
    
    $gt2gd_locations = array();

    // GT => GD custom fields
    $gd_post_type = geodir_gt2gd_gd_post_type($gt_post_type);
    $custom_fields = geodir_gt2gd_convert_custom_fields( $gd_post_type );
    
    $gt_rows = geodir_gt2gd_get_listings($gt_post_type, $limit);

    $taxonomy_category = $gd_post_type . 'category';
    $taxonomy_tags = $gd_post_type . '_tags';
    
    $done = 0;
    if (!empty($gt_rows)) {
        foreach ($gt_rows as $key => $row) {
            $done++;
            
            $row = (array)$row;
            $post_id = (int)$row['ID'];
            $row['post_id'] = $post_id;

            $sql = "SELECT t.term_id, t.name, t.slug, tt.taxonomy FROM " . $wpdb->terms . " AS t INNER JOIN " . $wpdb->term_taxonomy . " AS tt ON tt.term_id = t.term_id INNER JOIN " . $wpdb->term_relationships . " AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE ( tt.taxonomy = '" . $taxonomy_category . "' OR tt.taxonomy = '" . $taxonomy_tags . "' ) AND tr.object_id = '" . $post_id . "' ORDER BY tr.term_order, tr.term_taxonomy_id, t.name ASC";
            $terms = $wpdb->get_results($sql);
            $wpdb->flush();
            
            $default_category = 0;
            $post_category = '';
            $post_tags = '';
            $post_tags = '';
            
            if ( !empty( $terms ) ) {
                $category_terms = array();
                $post_tags = array();
            
                foreach ($terms as $term) {
                   $term = (array)$term;
                   if ($term['taxonomy'] == $taxonomy_category) {
                        $category_terms[] = $term['term_id'];
                    }
                    
                    if ($term['taxonomy'] == $taxonomy_tags) {
                        $post_tags[] = $term['name'];
                    }
                }
                
                if (!empty($category_terms)) {
                    $post_category = ',' . implode(',', $category_terms) . ',';
                    $default_category = $category_terms[0];
                }
                
                $post_tags = !empty( $post_tags ) ? implode( ',', $post_tags ) : '';
            }
            
            $row['post_city_id'] = !empty($row['post_city_id']) ? trim($row['post_city_id']) : get_post_meta($post_id, 'post_city_id', true);
            $row['address'] = !empty($row['address']) ? $row['address'] : get_post_meta($post_id, 'address', true);
            $row['geo_latitude'] = !empty($row['geo_latitude']) ? $row['geo_latitude'] : get_post_meta($post_id, 'geo_latitude', true);
            $row['geo_longitude'] = !empty($row['geo_longitude']) ? $row['geo_longitude'] : get_post_meta($post_id, 'geo_longitude', true);
            $row['featured_image'] = isset($row['featured_image']) ? $row['featured_image'] : '';
            $row['image_ids'] = isset($row['image_ids']) ? $row['image_ids'] : '';
            
            $post_zip = $row['address'] != '' ? geodir_gt2gd_extract_zipcode( $row['address'], true ) : '';
            
            $marker_json = '';
            if ( $default_category > 0 ) {
                $term_icon_url = get_tax_meta($default_category, 'ct_cat_icon', false, $gd_post_type);
                $term_icon = (isset($term_icon_url['src']) && $term_icon_url['src'] != '') ? $term_icon_url['src'] : $default_marker_icon;
                
                $marker_json = '{';
                $marker_json .= '"id":"' . $post_id . '",';
                $marker_json .= '"lat_pos": "' . $row['geo_latitude'] . '",';
                $marker_json .= '"long_pos": "' . $row['geo_longitude'] . '",';
                $marker_json .= '"marker_id":"' . $post_id . '_' . $default_category . '",';
                $marker_json .= '"icon":"' . $term_icon . '",';
                $marker_json .= '"group":"catgroup' . $default_category . '"';
                $marker_json .= '}';
            }
            
            $post_images = geodir_gt2gd_gt_parse_attachments($row['featured_image'], $row['image_ids']);
            $featured_image = !empty($post_images) && !empty($post_images[0]['file']) ? '/' . ltrim($post_images[0]['file'], "/") : '';
            
            $post_location_id = $row['post_city_id'];
            if ($is_location_active) {
                if (!empty($gt2gd_locations[$post_location_id])) {
                    $location_info = $gt2gd_locations[$post_location_id];
                } else {
                    $location_info = (array)geodir_get_location_by_id( array(), $post_location_id );
                    $gt2gd_locations[$post_location_id] = $location_info;
                }
            } else {
                if (!empty($gt2gd_locations[$post_location_id])) {
                    $location_info = $gt2gd_locations[$post_location_id];
                } else {
                    $location_info = (array)geodir_get_gt_location_info($post_location_id);
                    $gt2gd_locations[$post_location_id] = $location_info;
                }
                $post_location_id = $gd_default_location['location_id'];
            }
            
            if (empty($location_info)) {
                $location_info = $gd_default_location;
                $post_location_id = $gd_default_location['location_id'];
            }
            
            $post_city = $location_info['city'];
            $post_region = $location_info['region'];
            $post_country = $location_info['country'];
            $post_locations = '[' . $location_info['city_slug'] . '],[' . $location_info['region_slug'] . '],[' . $location_info['country_slug'] . ']';
            
            $data = array();
            $data['post_id'] = $post_id;
            $data['post_title'] = !empty($row['post_title']) ? $row['post_title'] : get_the_title($post_id);
            $data['post_status'] = $row['post_status'];
            $data['default_category'] = $default_category;
            $data['post_tags'] = $post_tags;
            $data['post_location_id'] = $post_location_id;
            $data['marker_json'] = $marker_json;
            $data['claimed'] = get_post_meta($post_id, 'claimed', true);

            $data['is_featured'] = isset( $row['is_featured'] ) && $row['is_featured'] !== '' ? $row['is_featured'] : get_post_meta($post_id, 'is_featured', true);
            $data['featured_image'] = $featured_image;
            $data['paid_amount'] = get_post_meta($post_id, 'paid_amount', true);
            $data['package_id'] = $is_payment_active  ? ( isset( $row['package_pid'] ) && $row['package_pid'] !== '' ? $row['package_pid'] : get_post_meta($post_id, 'package_pid', true) ) : 0;
            $data['alive_days'] = get_post_meta($post_id, 'alive_days', true);
            $data['paymentmethod'] = get_post_meta($post_id, 'paymentmethod', true);
            $data['expire_date'] = get_post_meta( $post_id, 'expire_date', true );
            if ($gt_post_type == 'event') {
                $recurring_data = geodir_gt2gd_gt_event_data($row);
                
                geodir_gt2gd_create_event_schedules($recurring_data, $post_id);
                
                $recurring_dates = maybe_serialize($recurring_data);
                
                $data['is_recurring'] = $recurring_data['is_recurring'];
                $data['recurring_dates'] = $recurring_dates;
                $data['event_reg_desc'] = !empty($row['reg_desc']) ? trim($row['reg_desc']) : get_post_meta($post_id, 'reg_desc', true);
                $data['geodir_link_business'] = isset( $row['a_businesses'] ) ? $row['a_businesses']  :'';
            }
            $data['submit_time'] = strtotime( $row['post_date'] );
            $data['post_locations'] = $post_locations;
            $data['post_dummy'] = get_post_meta($post_id, 'tl_dummy_content', true);
            $data[$taxonomy_category] = $post_category;
            $data['post_address'] = $row['address'];
            $data['post_city'] = $post_city;
            $data['post_region'] = $post_region;
            $data['post_country'] = $post_country;
            $data['post_zip'] = trim( $post_zip );
            $data['post_latitude'] = $row['geo_latitude'];
            $data['post_longitude'] = $row['geo_longitude'];
            if ($gt_post_type != 'event') {
                $data['post_mapzoom'] = !empty($row['map_zoom']) ? trim($row['map_zoom']) : get_post_meta($post_id, 'map_zoom', true);
            }
            $data['geodir_timing'] = !empty($row['timing']) ? trim($row['timing']) : get_post_meta($post_id, 'timing', true);
            $data['geodir_contact'] = !empty($row['contact']) ? trim($row['contact']) : get_post_meta($post_id, 'contact', true);
            $data['geodir_email'] = !empty($row['email']) ? trim($row['email']) : get_post_meta($post_id, 'email', true);
            $data['geodir_website'] = !empty($row['website']) ? trim($row['website']) : get_post_meta($post_id, 'website', true);
            $data['geodir_twitter'] = !empty($row['twitter']) ? trim($row['twitter']) : get_post_meta($post_id, 'twitter', true);
            $data['geodir_facebook'] = !empty($row['facebook']) ? trim($row['facebook']) : get_post_meta($post_id, 'facebook', true);
            $data['geodir_video'] = !empty($row['video']) ? trim($row['video']) : get_post_meta($post_id, 'video', true);
            $data['geodir_special_offers'] = !empty($row['proprty_feature']) ? trim($row['proprty_feature']) : get_post_meta($post_id, 'proprty_feature', true);
            if ($is_location_active) {
                $data['post_neighbourhood'] = !empty($row['post_hood_id']) ? trim($row['post_hood_id']) : get_post_meta($post_id, 'post_hood_id', true);
            }
            
            $sql = "SELECT COUNT(r.rating_id) AS reviews, AVG(r.rating_rating) AS rating FROM " . $wpdb->prefix . "ratings AS r INNER JOIN " . $wpdb->prefix . "comments AS c ON r.comment_id = c.comment_ID WHERE c.comment_parent = 0 AND r.rating_rating > 0 AND rating_postid = " . (int)$post_id;
            $post_review = $wpdb->get_row( $sql );
            
            $overall_rating = 0;
            $rating_count = 0;
            if ( !empty( $post_review ) ) {
                $overall_rating = round( $post_review->rating, 1 );
                $rating_count = $post_review->reviews;
            }
            
            $data['overall_rating'] = $overall_rating;
            $data['rating_count'] = $rating_count;
            update_post_meta($post_id, 'overall_rating', $overall_rating);
            update_post_meta($post_id, 'rating_count', $rating_count);
            
            $gt_meta_fields = array();
            
            if (!empty($custom_fields)) {
                foreach ($custom_fields as $custom_field) {
                    $gt_meta_fields[] = $custom_field;
                    $custom_field_san = preg_replace("/[^a-zA-Z0-9]+/", "", $custom_field);
                    $data[$custom_field_san] = trim(get_post_meta($post_id, $custom_field, true));
                }
            }

            $gd_listing_table = $wpdb->prefix . 'geodir_' . $gd_post_type . '_detail';
            
            $sql = "SELECT COUNT(*) FROM " . $gd_listing_table . " WHERE post_id = '". (int)$post_id ."'";
            $exists_row = $wpdb->get_var( $sql );
            
            if ( !empty( $exists_row ) ) {
                $return = $wpdb->update( $gd_listing_table, $data, array( 'post_id' => (int)$post_id ) );
            } else {
                $return = $wpdb->insert( $gd_listing_table, $data );
            }
            
            if ($return) {
                $gt_meta_keys = array('address', 'add_feature', 'alive_days', 'a_businesses', 'claimed', 'contact', 'contact_show', 'coupon_used', 'email', 'email_show', 'end_date', 'end_time', 'expire_date', 'facebook', 'geo_latitude', 'geo_longitude', 'height', 'is_featured', 'map_view', 'map_zoom', 'package_pid', 'paid_amount', 'paymentmethod', 'post_city_id', 'timing', 'tl_dummy_content', 'twitter', 'video', 'website', 'proprty_feature', 'pt_dummy_content', 'recurring', 'recurring_dates', 'recurring_limit', 'reg_desc', 'reg_fees', 'web_show', 'st_date', 'st_time');
                
                if (!empty($gt_meta_fields)) {
                    $gt_meta_keys = array_merge($gt_meta_keys, $gt_meta_fields);
                }
                
                $gt_meta_keys = "'" . implode("','", $gt_meta_keys) . "'";
                
                $wpdb->query("DELETE FROM " . $wpdb->postmeta . " WHERE post_id = '" . (int)$post_id . "' AND meta_key IN(" . $gt_meta_keys . ")");
            }
            
            // save attachments
            if (!empty($post_images)) {
                $wpdb->query("DELETE FROM " . $wpdb->prefix . "geodir_attachments WHERE post_id = '" . $post_id . "'");
                
                $menu_order = 1;
                foreach ($post_images as $post_image) {
                    if (!empty($post_image['file'])) {
                        $attachment = array();
                        $attachment['post_id'] = $post_id;
                        $attachment['title'] = $post_image['attachment_title'];
                        $attachment['file'] = '/' . ltrim($post_image['file'], "/");
                        $attachment['mime_type'] = $post_image['attachment_mime_type'];
                        $attachment['menu_order'] = $menu_order;
                        
                        $wpdb->insert( $wpdb->prefix . 'geodir_attachments', $attachment );
                        
                        $menu_order++;
                    }
                }
            }
            
            $wpdb->query("UPDATE " . $wpdb->posts . " SET post_type = '" . $gd_post_type . "' WHERE ID = '" . $post_id . "'");
            $wpdb->flush();
            if (!empty($wpdb->queries)) {
                $wpdb->queries = array();
            }
            
            // clear the post cache so the post_type is updated
            clean_post_cache($post_id);

            // update the category post meta
            geodir_set_postcat_structure($post_id , $taxonomy_category, $default_category, '');

            // set the featured image
            geodir_set_wp_featured_image($post_id);
        }
    }

    return $done;
}

/**
 * GeoTheme to GeoDirectory reviews and ratings conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_reviews() {
    global $wpdb;

    $is_event_active = geodir_gt2gd_is_active( 'geodir_event_manager' );
    $is_review_active = geodir_gt2gd_is_active('geodir_review_rating_manager');

    $gt_post_types = array( 'gd_place' );
    if ( $is_event_active ) {
        $gt_post_types[] = 'gd_event';
    }

    $gt_reviews = array();

    $sql = "SELECT c.comment_ID, c.comment_post_ID, c.comment_approved, c.comment_date, c.comment_content, c.user_id, r.rating_id, r.rating_ip, r.rating_rating, p.post_type, p.post_title, p.post_status FROM " . $wpdb->comments . " AS c INNER JOIN " . $wpdb->prefix . "ratings AS r ON r.comment_id = c.comment_ID LEFT JOIN " . $wpdb->posts . " AS p ON p.ID = c.comment_post_ID WHERE c.comment_parent = 0 AND (p.post_type = 'place' || p.post_type = 'gd_place' || p.post_type = 'event' || p.post_type = 'gd_event')";
    $rows = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $gd_post_type = geodir_gt2gd_gd_post_type($row['post_type']);
            if (!in_array($gd_post_type, $gt_post_types)) {
                continue;
            }
            
            $gd_listing_table = $wpdb->prefix . 'geodir_' . $gd_post_type . '_detail';
            
            $sql = "SELECT post_city, post_region, post_country, post_latitude, post_longitude FROM " . $gd_listing_table . " WHERE post_id = '" . (int)$row['comment_post_ID'] . "'";
            $location = $wpdb->get_row($sql, ARRAY_A);
            
            $post_city = $post_region = $post_country = $post_latitude = $post_longitude = '';
            if (!empty($location)) {
                $post_city = $location['post_city'];
                $post_region = $location['post_region'];
                $post_country = $location['post_country'];
                $post_latitude = $location['post_latitude'];
                $post_longitude = $location['post_longitude'];
            }
            
            $comment_content = trim($row['comment_content']);
            
            $comment_images = array();
            if ($comment_content != '') {
                // Extract contents of tags
                $comment_text = str_replace("&#215;", "x", $comment_content);
                preg_match_all('/\[(img|file)\]([^\]]*)\[\/\\1\]/i', $comment_text, $matches, PREG_SET_ORDER);

                if (!empty($matches)) {
                    foreach($matches as $match) {
                        if (!empty($match[2]) && $match[1] == 'img') {
                            $comment_images[] = $match[2];
                        }
                    }
                }
                
                $comment_content = preg_replace('#(\\[img\\]).+(\\[\\/img\\])#', '', $comment_content);
            }
            $total_images = count($comment_images);
            $comment_images = !empty($comment_images) ? implode('|', $comment_images) : '';
            
            $data = array();
            $data['post_id'] = $row['comment_post_ID'];
            $data['post_title'] = $row['post_title'];
            $data['post_type'] = geodir_gt2gd_gd_post_type($row['post_type']);
            $data['user_id'] = $row['user_id'];
            $data['comment_id'] = $row['comment_ID'];
            $data['rating_ip'] = $row['rating_ip'];
            //$data['ratings'] = '';
            $data['overall_rating'] = $row['rating_rating'];
            $data['comment_images'] = $comment_images;
            //$data['wasthis_review'] = '';
            $data['status'] = (int)$row['comment_approved'];
            $data['post_status'] = $row['post_status'] == 'publish' ? 1 : 0;
            $data['post_date'] = $row['comment_date'];
            $data['post_city'] = $post_city;
            $data['post_region'] = $post_region;
            $data['post_country'] = $post_country;
            if ($is_review_active) {
                $data['read_unread'] = '1';
                $data['total_images'] = $total_images;
            }
            $data['post_latitude'] = $post_latitude;
            $data['post_longitude'] = $post_longitude;
            $data['comment_content'] = $comment_content;
            
            $sql = "SELECT id FROM " . $wpdb->prefix . "geodir_post_review WHERE post_id = '". (int)$data['post_id'] ."' AND comment_id = '". (int)$data['comment_id'] ."'";
            $exists_row = $wpdb->get_var( $sql );
            
            if ( !empty( $exists_row ) ) {
                $return = $wpdb->update($wpdb->prefix . 'geodir_post_review', $data, array('id' => (int)$exists_row));
            } else {
                $return = $wpdb->insert($wpdb->prefix . 'geodir_post_review', $data);
            }
        }
    }

    return true;
}

/**
 * GeoTheme to GeoDirectory payment invoices conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_invoices() {
    global $wpdb;

    $sql = "SELECT p.ID, p.post_name AS listing_id, pd.post_title AS listing_title FROM " . $wpdb->posts . " AS p INNER JOIN " . $wpdb->postmeta . " AS pm ON ( p.ID = pm.post_id ) INNER JOIN " . $wpdb->posts . " AS pd ON ( pd.ID = p.post_name ) WHERE ( pm.meta_key = '_status' ) AND p.post_type = 'invoice' AND pm.meta_value != '' AND pm.meta_value != 'Free' GROUP BY p.ID ORDER BY p.ID ASC";
    $rows = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $invoice_id = $row['ID'];
            $invoice_info = (array)get_post($invoice_id);
            
            $type = get_post_meta($invoice_id, '_status', true);
            $type = $type != '' ? strtolower($type) : '';
            
            $package_id = get_post_meta($invoice_id, 'package_pid', true);
            $sql = "SELECT * FROM " . $wpdb->prefix . "price WHERE `pid` = '" . $package_id . "'";
            $package_info = $wpdb->get_row($sql, ARRAY_A);
            if (empty($package_info)) {
                continue;
            }
            
            $status = 'pending';
            if ($type == 'subscription-payment' || $type == 'subscription-active' || $type == 'paid') {
                $status = 'confirmed';
            } else if ($type == 'subscription-canceled' || $type == 'canceled') {
                $status = 'canceled';
            }
            
            $subscription = 0;
            if ($type == 'subscription-payment' || $type == 'subscription-active' || $type == 'subscription-canceled') {
                $subscription = 1;
            }
            
            $expire_date = $package_info['days'] > 0 ? date_i18n( 'Y-m-d', strtotime($invoice_info['post_date'] . ' + ' . $package_info['days'] . ' days')) : 'Never';
            
            $data = array();
            $data['id'] = $invoice_id;
            $data['type'] = 'paid';
            $data['post_id'] = $row['listing_id'];
            $data['post_title'] = $row['listing_title'];
            $data['post_action'] = 'add';
            $data['invoice_type'] = 'add_listing';
            $data['invoice_callback'] = 'add_listing';
            //$data['invoice_data'] = '';
            $data['package_id'] = $package_id;
            $data['package_title'] = $package_info['title'];
            $data['amount'] = $package_info['amount'];
            $data['alive_days'] = $package_info['days'];
            $data['expire_date'] = $expire_date;
            $data['user_id'] = $invoice_info['post_author'];
            //$data['coupon_code'] = '';
            //$data['discount'] = '0';
            $data['tax_amount'] = '0.00';
            $data['paied_amount'] = get_post_meta($invoice_id, 'paid_amount', true);
            $data['paymentmethod'] = get_post_meta($invoice_id, 'paymentmethod', true);
            $data['status'] = $status;
            $data['subscription'] = $subscription;
            //$data['HTML'] = NULL;
            $data['is_current'] = '1';
            $data['date'] = $invoice_info['post_date'];
            $data['date_updated'] = $invoice_info['post_modified'];
            
            $sql = "SELECT id FROM " . $wpdb->prefix . "geodir_invoice WHERE id = '". (int)$invoice_id ."'";
            $exists_row = $wpdb->get_var( $sql );
            
            if ( !empty( $exists_row ) ) {
                $return = $wpdb->update($wpdb->prefix . 'geodir_invoice', $data, array('id' => (int)$exists_row));
            } else {
                $return = $wpdb->insert($wpdb->prefix . 'geodir_invoice', $data);
            }
        }
    }

    return true;
}

/**
 * GeoTheme to GeoDirectory claim listings data conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @return bool Conversion status.
 */
function geodir_gt2gd_convert_claims() {
    global $wpdb;

    $sql = "SELECT * FROM " . $wpdb->prefix . "claim ORDER BY pid ASC";
    $rows = $wpdb->get_results($sql, ARRAY_A);

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $claim_id = $row['pid'];
            
            $data = array();
            $data['pid'] = $claim_id;
            $data['list_id'] = $row['list_id'];
            $data['list_title'] = $row['list_title'];
            $data['user_id'] = $row['user_id'];
            $data['user_name'] = $row['user_name'];
            $data['user_email'] = $row['user_email'];
            $data['user_fullname'] = $row['user_fullname'];
            $data['user_number'] = $row['user_number'];
            $data['user_position'] = $row['user_position'];
            $data['user_comments'] = $row['user_comments'];
            $data['admin_comments'] = $row['admin_comments'];
            $data['claim_date'] = $row['claim_date'];
            $data['org_author'] = $row['org_author'];
            $data['org_authorid'] = $row['org_authorid'];
            $data['rand_string'] = $row['rand_string'];
            $data['status'] = $row['status'];
            $data['user_ip'] = $row['user_ip'];
            //$data['upgrade_pkg_id'] = '';
            //$data['upgrade_pkg_data'] = '';
            
            $gd_table = $wpdb->prefix . 'geodir_claim';
            
            $sql = "SELECT pid FROM " . $gd_table . " WHERE pid = '". (int)$claim_id ."'";
            $exists_row = $wpdb->get_var( $sql );
            
            if ( !empty( $exists_row ) ) {
                $return = $wpdb->update($gd_table, $data, array('pid' => (int)$claim_id));
            } else {
                $return = $wpdb->insert($gd_table, $data);
            }
        }
    }

    return true;
}

/**
 * Retrieve region & country data for city.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $post_city_id City id.
 *
 * @return null|array City info or null.
 */
function geodir_gt2gd_gt_region_country( $post_city_id ) {
    global $wpdb;

    if ( !$post_city_id > 0 ) {
        return NULL;
    }

    $return = array();

    // region
    $sql = "SELECT regionname as region, region_slug FROM " . $wpdb->prefix . "multiregion WHERE FIND_IN_SET( '" . $post_city_id . "', cities ) ORDER BY sortorder ASC, is_default DESC";
    $row = $wpdb->get_row( $sql, ARRAY_A );

    $return['region'] = !empty($row['region']) ? $row['region'] : '';
    $return['region_slug'] = !empty($row['region_slug']) ? $row['region_slug'] : '';

    // country
    $sql = "SELECT countryname as country, country_slug FROM " . $wpdb->prefix . "multicountry WHERE FIND_IN_SET( '" . $post_city_id . "', cities ) ORDER BY sortorder ASC, is_default DESC";
    $row = $wpdb->get_row( $sql, ARRAY_A );

    $return['country'] = !empty($row['country']) ? $row['country'] : '';
    $return['country_slug'] = !empty($row['country_slug']) ? $row['country_slug'] : '';

    return $return;
}

/**
 * Convert post type to GeoDirectory post type.
 *
 * @since 1.0.0
 *
 * @param string $post_type The post type. Ex: place.
 *
 * @return string Post type. Ex: gd_place.
 */
function geodir_gt2gd_gd_post_type( $post_type ) {
    if ( strpos( $post_type, 'gd_' ) === false || strpos( $post_type, 'gd_' ) > 0 ) {
        $post_type = 'gd_' . $post_type;
    }

    return $post_type;
}

/**
 * Parse the zip code from address.
 *
 * @since 1.0.0
 *
 * @param string $address Full address.
 * @param bool $remove_statecode If True then returns zip code without state code.
 *
 * @return string|null Zip code or null.
 */
function geodir_gt2gd_extract_zipcode( $address, $remove_statecode = false ) {
    if ( $address == '' ) {
        return NULL;
    }
    preg_match( "/\b[A-Z]{2}\s+\d{5}(-\d{4})?\b/", $address, $matches );
    if ( !empty( $matches ) ) {
        return $remove_statecode ? preg_replace( "/[^\d\-]/", "", geodir_gt2gd_extract_zipcode( $matches[0] ) ) : $matches[0];
    }
    return NULL;
}

/**
 * Convert date format to store in database.
 *
 * PHP date() function doesn't work well with d/m/Y format
 * so this function validate and convert date to store in db.
 *
 * @since 1.0.0
 *
 * @param string $date Date in Y-m-d or d/m/Y format.
 * @return doesn't Date.
 */
function geodir_gt2gd_date_to_ymd( $date ) {
    if (strpos($date, '/') !== false) {
        $date = str_replace('/', '-', $date); // PHP doesn't work well with dd/mm/yyyy format.
    }

    $date = date_i18n('Y-m-d', strtotime($date));
    return $date;
}

/**
 * Find that given plugin active or not.
 *
 * @since 1.0.0
 *
 * @param string $plugin Plugin name. Ex: "geodir_event_manager".
 *
 * @return bool True if plugin active.
 */
function geodir_gt2gd_is_active( $plugin ) {
    $active = false;

    if ( is_plugin_active( $plugin . '/' . $plugin . '.php' ) ) {
        $active = true;
    } else {
    }

    return $active;
}

/**
 * GeoTheme to GeoDirectory custom fields conversion.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $gd_post_type The post type. Ex: "gd_place".
 *
 * @return array Converted custom fields names.
 */
function geodir_gt2gd_convert_custom_fields( $gd_post_type ) {
    global $wpdb;

    // places custom fields
    $custom_fields = geodir_gt2gd_gt_custom_fields( $gd_post_type );

    $return_fields = array();
    if ( !empty( $custom_fields ) ) {
        remove_all_filters( 'get_terms' );
        $gd_place_terms = (array)get_terms( $gd_post_type . 'category', array( 'hide_empty' => 0, 'orderby' => 'id', 'fields' => 'ids' ) );

        foreach ( $custom_fields as $custom_field ) {
            $gt_field_type = $custom_field->ctype;
            
            $data_type = $gt_field_type == 'text' ? 'TEXT' : 'VARCHAR';
            $field_type = $gt_field_type == 'link' ? 'url' : $gt_field_type;
            
            $cat_sort = trim( $custom_field->cat_sort, ',' ) != '' ? explode( ',', trim( $custom_field->cat_sort, ',' ) ) : array();
            $cat_sort = array_intersect( $cat_sort, $gd_place_terms );
            
            $cat_filter = trim( $custom_field->cat_filter, ',' ) != '' ? explode( ',', trim( $custom_field->cat_filter, ',' ) ) : array();
            $cat_filter = array_intersect( $cat_filter, $gd_place_terms );

            // fix values having space after comma,
            if (($gt_field_type=='select' || $gt_field_type=='multiselect' ) && isset( $custom_field->option_values) &&  $custom_field->option_values) {
                $temp_option_values = $custom_field->option_values;
                $temp_option_values = explode(",", $temp_option_values);
                
                if (is_array($temp_option_values)) {
                    $custom_field->option_values = implode(",", array_map('trim', $temp_option_values));
                }
            }

            $save_field = array();
            $save_field['post_type'] = $gd_post_type;
            $save_field['data_type'] = $data_type;
            $save_field['field_type'] = $field_type;
            $save_field['admin_title'] = $custom_field->admin_title;
            $save_field['admin_desc'] = $custom_field->admin_desc;
            $save_field['site_title'] = $custom_field->site_title;
            $save_field['htmlvar_name'] = preg_replace("/[^a-zA-Z0-9]+/", "", $custom_field->htmlvar_name);//sanaise htmlvar_name
            $save_field['default_value'] = trim( $custom_field->default_value, ' ' );
            $save_field['sort_order'] = $custom_field->sort_order;
            $save_field['option_values'] = $custom_field->option_values;
            $save_field['clabels'] = $custom_field->clabels;
            $save_field['is_active'] = $custom_field->is_active;
            $save_field['show_on_listing'] = $custom_field->show_on_listing;
            $save_field['show_on_detail'] = $custom_field->show_on_detail;
            $save_field['is_default'] = $custom_field->show_on_detail; // show in sidebar
            $save_field['packages'] = $custom_field->extrafield1;
            $save_field['cat_sort'] = implode(',', $cat_sort);
            $save_field['cat_filter'] = implode(',', $cat_filter);
            
            $sql = "SELECT id FROM " . $wpdb->prefix . "geodir_custom_fields WHERE htmlvar_name = '". $save_field['htmlvar_name'] ."' AND post_type = '". $save_field['post_type'] . "'";
            $exists_field = $wpdb->get_var( $sql );
            
            if ( !empty( $exists_field ) ) {
                $wpdb->update( $wpdb->prefix . 'geodir_custom_fields', $save_field, array( 'id' => $exists_field ) );
            } else {
                $wpdb->insert( $wpdb->prefix . 'geodir_custom_fields', $save_field );
            }
            
            switch ( $gt_field_type ) {
                case 'checkbox':
                    $field_data_type = "TINYINT( 1 ) NOT NULL";
                    if(isset($save_field['default_value']) && strlen($save_field['default_value'])>1){$save_field['default_value'] = 0;}
                break;
                case 'multiselect':
                    $field_data_type = "VARCHAR( 500 ) NULL";
                break;
                case 'textarea':
                    $field_data_type = "TEXT NULL";
                break;
                default:
                    $field_data_type = "VARCHAR( 254 ) NULL";
                break;
            }
            
            if ( $field_data_type ) {
                if ($save_field['default_value'] != '') {
                    $field_data_type .= " DEFAULT '" . $save_field['default_value'] . "'";
                }
                
                geodir_add_column_if_not_exist( $wpdb->prefix . 'geodir_' . $save_field['post_type'] . '_detail', $save_field['htmlvar_name'], $field_data_type );
            }
            
            $return_fields[] = $custom_field->htmlvar_name;
        }
    }

    return $return_fields;
}

/**
 * Geotheme custom fields table.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type The post type.
 * @return object Queried object.
 */
function geodir_gt2gd_gt_custom_fields( $post_type ) {
    global $wpdb;
        
    remove_all_filters( 'get_terms' );
    $terms = get_terms( $post_type . 'category', array( 'hide_empty' => 0, 'orderby' => 'id', 'fields' => 'ids' ) );

    if ( !( !empty( $terms ) && is_array( $terms ) ) ) {
        return NULL;
    }

    $where = array();
    foreach ( $terms as $term_id ) {
        $where[] = "FIND_IN_SET( '" . $term_id . "', cat_filter )";
    }

    $where = "WHERE ( " . implode( " OR ", $where ) . " ) ";

    // this seems wrong to filter here, we should grab all
    $where = " ";
     
    $table = $wpdb->prefix . 'geotheme_custom_post_fields';

    $rows = array();

    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) == $table ) {
        $sql = "SELECT * FROM " . $table . " " . $where ." ORDER BY sort_order ASC";
        $rows = $wpdb->get_results( $sql );
    }

    return $rows;
}

/**
 * Parse the listing images from image ids.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param int $featured_image Feature image id.
 * @param string $image_ids Listing images ids. Ex: "103,108".
 *
 * @return array Listing images data.
 */
function geodir_gt2gd_gt_parse_attachments( $featured_image, $image_ids ) {
    global $wpdb;
                
    $images_array = array();
    if ( $featured_image > 0 ) {
        $images_array[] = $featured_image;
    }

    if ( $image_ids != '' ) {
        $image_ids = explode( ',', $image_ids );
        $images_array = array_merge( $images_array, $image_ids );
    }

    if ( empty( $images_array ) ) {
        return NULL;
    }

    $images_array = array_unique( $images_array );

    $post_images = array();
    foreach ( $images_array as $image_id ) {
        if ( !(int)$image_id > 0 ) {
            continue;
        }
        
        $attachment = wp_get_attachment_metadata( $image_id );
        if ( !empty( $attachment ) ) {
            $attachment['attachment_id'] = $image_id;
            
            $sql = "SELECT post_title, post_mime_type FROM " . $wpdb->posts . " WHERE ID = '" . $image_id . "' AND post_type = 'attachment'";
            $row = $wpdb->get_row($sql, ARRAY_A);
            
            $attachment['attachment_title'] = !empty($row) && isset($row['post_title']) ? $row['post_title'] : '';
            $attachment['attachment_mime_type'] = !empty($row) && isset($row['post_mime_type']) ? $row['post_mime_type'] : '';
            
            $post_images[] = $attachment;
        }
    }

    return $post_images;
}

/**
 * Retrieves the geotheme event data to export.
 *
 * @since 1.0.0
 *
 * @param array $post Post array.
 * @param object $post_info Geotheme Post object.
 * @return array Geotheme Event data array.
 */
function geodir_gt2gd_gt_event_data( $post_info ) {
    if (empty($post_info['st_date'])) {
        $post_info['st_date'] = get_post_meta($post_info['post_id'], 'st_date', true);
    }
    if (empty($post_info['st_time'])) {
        $post_info['st_time'] = get_post_meta($post_info['post_id'], 'st_time', true);
    }
    if (empty($post_info['end_date'])) {
        $post_info['end_date'] = get_post_meta($post_info['post_id'], 'end_date', true);
    }
    if (empty($post_info['end_time'])) {
        $post_info['end_time'] = get_post_meta($post_info['post_id'], 'end_time', true);
    }
    if (empty($post_info['recurring'])) {
        $post_info['recurring'] = get_post_meta($post_info['post_id'], 'recurring', true);
    }
    if (empty($post_info['recurring_limit'])) {
        $post_info['recurring_limit'] = get_post_meta($post_info['post_id'], 'recurring_limit', true);
    }
    if (empty($post_info['recurring_dates'])) {
        $post_info['recurring_dates'] = get_post_meta($post_info['post_id'], 'recurring_dates', true);
    }

    $recurring = isset( $post_info['recurring'] ) ? trim( $post_info['recurring'] ) : '';
    $recurring_limit = $post_info['recurring_limit'];
    $recurring_data = $post_info['recurring_dates'];

    $event_date = '';
    $event_enddate = '';
    $starttime = '';
    $endtime = '';
    $is_recurring_event = '';
    $recurring_dates = '';
    $event_duration_days = '';
    $is_whole_day_event = '';
    $event_starttimes = '';
    $event_endtimes = '';
    $recurring_type = '';
    $recurring_interval = '';
    $recurring_week_days = '';
    $recurring_week_nos = '';
    $max_recurring_count = '';
    $recurring_end_date = '';

    $recurring_types = array( 'week', 'two_week', 'month', 'year', 'custom recursion' );
    $is_recurring_event = in_array( $recurring, $recurring_types ) ? true : false;

    $st_date = $post_info['st_date'] != '' && $post_info['st_date'] != '0000-00-00' ? geodir_gt2gd_date_to_ymd( $post_info['st_date'] ) : '';
    $end_date = $post_info['end_date'] != '' && $post_info['end_date'] != '0000-00-00' ? geodir_gt2gd_date_to_ymd( $post_info['end_date'] ) : $st_date;

    $st_time = $post_info['st_time'] != '' ? date_i18n( 'H:i', strtotime( $post_info['st_time'] ) ) : '';
    $end_time = $post_info['end_time'] != '' ? date_i18n( 'H:i', strtotime( $post_info['end_time'] ) ) : '';

    $event_date_time = strtotime( $st_date );
    $event_enddate_time = strtotime( $end_date );

    $event_date = $st_date != '' ? date_i18n( 'd/m/Y', $event_date_time ) : $event_date;
    $event_enddate = $end_date != '' ? date_i18n( 'd/m/Y', $event_enddate_time ) : $event_date;

    $starttime = $st_time != '' ? $st_time : $starttime;
    $endtime = $end_time != '' ? $end_time : $endtime;

    $is_whole_day_event = ($st_time == '00:00' || $st_time == '') && $end_time == $st_time ? true : false;
    if ( $is_recurring_event ) {
        if ( $event_date_time > 0 && $event_enddate_time > $event_date_time ) {
            $event_duration_days = (int)abs( ( $event_enddate_time - $event_date_time ) / 86400 ) + 1;
        }
        
        $event_duration_days = $event_duration_days > 0 ? $event_duration_days : 1;
        $recurring_interval = 1;
        $max_recurring_count = $recurring_limit > 0 ? $recurring_limit : '';
        
        if ( !$max_recurring_count ) {
            $recurring_end_date = date_i18n( 'Y-m-d', $event_date_time + ( 86400 * 365 * 2 ) ); // 2 years from start date.
        }
        
        $recurring_type = $recurring;
        
        if ( $recurring == 'two_week' ) {
            $recurring_type = 'week';
            $recurring_interval = 2;
        } else if ( $recurring == 'custom recursion' ) {
            $recurring_type = 'custom';
            
            $recurring_dates = $event_date;
            $event_enddate = '';
            $event_duration_days = 1;
            $recurring_interval = '';
            $max_recurring_count = '';
            $recurring_end_date = '';
            
            $recurring_data = $recurring_data != '' ? maybe_unserialize( $recurring_data ) : array();
        
            if ( !empty( $recurring_data ) && !empty($recurring_data['event_recurring_dates'])) {
                $event_recurring_dates = explode( ',', $recurring_data['event_recurring_dates'] );
                $different_times = !empty( $recurring_data['different_times'] ) ? true : false;
                
                $is_whole_day_event = $is_whole_day_event && !$different_times ? true : false;
                
                if (!empty($event_recurring_dates)) {
                    $recurring_dates = array();
                    
                    foreach ($event_recurring_dates as $date) {
                        $recurring_dates[] = date_i18n( 'd/m/Y', strtotime( $date ) );
                    }
                    
                    $recurring_dates = implode(",", $recurring_dates);
                }
                
                $event_starttimes = $starttime;
                $event_endtimes = $endtime;
        
                if (!empty($recurring_data['starttimes'])) {
                    $times = array();
                    
                    foreach ($recurring_data['starttimes'] as $time) {
                        $times[] = $time != '00:00:00' ? date_i18n( 'H:i', strtotime( $time ) ) : '00:00';
                    }
                    
                    $event_starttimes = implode(",", $times);
                }
                
                if (!empty($recurring_data['endtimes'])) {
                    $times = array();
                    
                    foreach ($recurring_data['endtimes'] as $time) {
                        $times[] = $time != '00:00:00' ? date_i18n( 'H:i', strtotime( $time ) ) : '00:00';
                    }
                    
                    $event_endtimes = implode(",", $times);
                }
                
                if (!$different_times) {
                    $event_starttimes = '';
                    $event_endtimes = '';
                }
            }
        }
    }

    if ($is_whole_day_event) {
        $starttime = '';
        $endtime = '';
        $event_starttimes = '';
        $event_endtimes = '';
    }

    $data = array();
    $data['event_date'] = $event_date;
    $data['event_enddate'] = $event_enddate;
    $data['starttime'] = $starttime;
    $data['endtime'] = $endtime;
    $data['is_recurring_event'] = (int)$is_recurring_event;
    $data['recurring_dates'] = $recurring_dates;
    $data['event_duration_days'] = $event_duration_days;
    $data['is_whole_day_event'] = (int)$is_whole_day_event;
    $data['event_starttimes'] = $event_starttimes;
    $data['event_endtimes'] = $event_endtimes;
    $data['recurring_type'] = $recurring_type;
    $data['recurring_interval'] = $recurring_interval;
    $data['recurring_week_days'] = $recurring_week_days;
    $data['recurring_week_nos'] = $recurring_week_nos;
    $data['max_recurring_count'] = $max_recurring_count;
    $data['recurring_end_date'] = $recurring_end_date;

    $data = geodir_gt2gd_gt_process_event_data($data);
    return $data;
}

/**
 * Parse recurring data from event data.
 *
 * @since 1.0.0
 *
 * @param array $event_data Event data.
 *
 * @return array Event data.
 */
function geodir_gt2gd_gt_process_event_data($event_data) {
    $is_recurring = isset($event_data['is_recurring_event']) && (int)$event_data['is_recurring_event'] ? true : false;
    $event_date = isset($event_data['event_date']) && $event_data['event_date'] != '' ? geodir_imex_get_date_ymd($event_data['event_date']) : '';
    $event_enddate = isset($event_data['event_enddate']) && $event_data['event_enddate'] != '' ? geodir_imex_get_date_ymd($event_data['event_enddate']) : $event_date;
    $all_day = isset($event_data['is_whole_day_event']) && !empty($event_data['is_whole_day_event']) ? true : false;
    $starttime = isset($event_data['starttime']) && !$all_day ? $event_data['starttime'] : '';
    $endtime = isset($event_data['endtime']) && !$all_day ? $event_data['endtime'] : '';

    $repeat_type = '';
    $different_times = '';
    $starttimes = '';
    $endtimes = '';
    $repeat_days = '';
    $repeat_weeks = '';
    $event_recurring_dates = '';
    $repeat_x = '';
    $duration_x = '';
    $repeat_end_type = '';
    $max_repeat = '';
    $repeat_end = '';

    if ($is_recurring) {
        $repeat_type = $event_data['recurring_type'];
        
        if ($repeat_type == 'custom') {
            $starttimes = !$all_day && !empty($event_data['event_starttimes']) ? explode(",", $event_data['event_starttimes']) : array();
            $endtimes = !$all_day && !empty($event_data['event_endtimes']) ? explode(",", $event_data['event_endtimes']) : array();
            
            if (!empty($starttimes) || !empty($endtimes)) {
                $different_times = true;
            }
            
            $recurring_dates = isset($event_data['recurring_dates']) && $event_data['recurring_dates'] != '' ? explode(",", $event_data['recurring_dates']) : array();
            if (!empty($recurring_dates)) {
                $event_recurring_dates = array();
                
                foreach ($recurring_dates as $recurring_date) {
                    $recurring_date = trim($recurring_date);
                    
                    if ($recurring_date != '') {
                        $event_recurring_dates[] = geodir_imex_get_date_ymd($recurring_date);
                    }
                }
                
                $event_recurring_dates = array_unique($event_recurring_dates);
                $event_recurring_dates = implode(",", $event_recurring_dates);
            }
        } else {
            $duration_x = !empty($event_data['event_duration_days']) ? (int)$event_data['event_duration_days'] : 1;
            $repeat_x = !empty($event_data['recurring_interval']) ? (int)$event_data['recurring_interval'] : 1;
            $max_repeat = !empty($event_data['max_recurring_count']) ? (int)$event_data['max_recurring_count'] : 1;
            $repeat_end = !empty($event_data['recurring_end_date']) ? geodir_imex_get_date_ymd($event_data['recurring_end_date']) : '';
            
            $repeat_end_type = $repeat_end != '' ? 1 : 0;
            $max_repeat = $repeat_end != '' ? '' : $max_repeat;
            
            $week_days = array_flip(array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'));
            
            $a_repeat_days = isset($event_data['recurring_week_days']) && trim($event_data['recurring_week_days'])!='' ? explode(',', trim($event_data['recurring_week_days'])) : array();
            $repeat_days = array();
            if (!empty($a_repeat_days)) {
                foreach ($a_repeat_days as $repeat_day) {
                    $repeat_day = strtolower(trim($repeat_day));
                    
                    if ($repeat_day != '' && isset($week_days[$repeat_day])) {
                        $repeat_days[] = $week_days[$repeat_day];
                    }
                }
                
                $repeat_days = array_unique($repeat_days);
            }
            
            $a_repeat_weeks = isset($event_data['recurring_week_nos']) && trim($event_data['recurring_week_nos']) != '' ? explode(",", trim($event_data['recurring_week_nos'])) : array();
            $repeat_weeks = array();
            if (!empty($a_repeat_weeks)) {
                foreach ($a_repeat_weeks as $repeat_week) {
                    $repeat_weeks[] = (int)$repeat_week;
                }
                
                $repeat_weeks = array_unique($repeat_weeks);
            }
        }
    }

    if (isset($event_data['recurring_dates'])) {
        unset($event_data['recurring_dates']);
    }

    $event_data['is_recurring'] = $is_recurring;
    $event_data['event_date'] = $event_date;
    $event_data['event_start'] = $event_date;
    $event_data['event_end'] = $event_enddate;
    $event_data['all_day'] = $all_day;
    $event_data['starttime'] = $starttime;
    $event_data['endtime'] = $endtime;

    $event_data['repeat_type'] = $repeat_type;
    $event_data['different_times'] = $different_times;
    $event_data['starttimes'] = $starttimes;
    $event_data['endtimes'] = $endtimes;
    $event_data['repeat_days'] = $repeat_days;
    $event_data['repeat_weeks'] = $repeat_weeks;
    $event_data['event_recurring_dates'] = $event_recurring_dates;
    $event_data['repeat_x'] = $repeat_x;
    $event_data['duration_x'] = $duration_x;
    $event_data['repeat_end_type'] = $repeat_end_type;
    $event_data['max_repeat'] = $max_repeat;
    $event_data['repeat_end'] = $repeat_end;

    $event_data = geodir_gt2gd_event_recurring_data($event_data);

    return $event_data;
}

/**
 * Get the recurring data from event data.
 *
 * @since 1.0.0
 *
 * @param array $data Event data.
 *
 * @return array Event data.
 */
function geodir_gt2gd_event_recurring_data($data) {
    $format = geodir_event_date_format();
    $default_start = date_i18n($format, current_time('timestamp'));

    // recurring event
    $is_recurring = (int)$data['is_recurring'];
    $event_date = isset($data['event_date']) ? trim($data['event_date']) : (isset($data['event_recurring_dates']) ? $data['event_recurring_dates'] : '');
    $event_start = isset($data['event_start']) ? trim($data['event_start']) : $event_date;
    $event_end = isset($data['event_end']) ? trim($data['event_end']) : '';
    $all_day = isset($data['all_day']) && !empty($data['all_day']) ? true : false;
    $starttime = isset($data['starttime']) && !$all_day ? trim($data['starttime']) : '';
    $endtime = isset($data['endtime']) && !$all_day ? trim($data['endtime']) : '';
    $repeat_days = array();
    $repeat_weeks = array();

    // recurring event
    if ($is_recurring) {
        $repeat_type = isset($data['repeat_type']) && in_array($data['repeat_type'], array('day', 'week', 'month', 'year', 'custom')) ? $data['repeat_type'] : 'custom'; // day, week, month, year, custom
        $different_times = isset($data['different_times']) && !empty($data['different_times']) ? true : false;
        $starttimes = $different_times && !$all_day && isset($data['starttimes']) ? $data['starttimes'] : array();
        $endtimes = $different_times && !$all_day && isset($data['endtimes']) && !empty($data['endtimes']) ? $data['endtimes'] : array();

        // week days
        if ($repeat_type == 'week' || $repeat_type == 'month') {
            $repeat_days = isset($data['repeat_days']) ? $data['repeat_days'] : $repeat_days;
        }
        
        // by week
        if ($repeat_type == 'month') {
            $repeat_weeks = isset($data['repeat_weeks']) ? $data['repeat_weeks'] : $repeat_weeks;
        }
            
        if ($repeat_type == 'custom') {
            $event_recurring_dates = isset($data['event_recurring_dates']) ? trim($data['event_recurring_dates']) : '';
            $event_recurring_dates = geodir_event_parse_dates($event_recurring_dates);
                        
            if ($different_times == 1) {
                $starttime = '';
                $endtime = '';
            }
            
            $event_start = !empty($event_recurring_dates[0]) ? $event_recurring_dates[0] : $default_start;
            $event_end = $event_start;
            $duration_x = 1;
            
            $repeat_x = 1;
            $repeat_end_type = 0;
            $max_repeat = 1;
            $repeat_end = '';
            
            $event_recurring_dates = !empty($event_recurring_dates) ? implode(',', $event_recurring_dates) : $event_start;
        } else {
            $repeat_x = isset($data['repeat_x']) ? trim($data['repeat_x']) : '';
            $duration_x = isset($data['duration_x']) ? trim($data['duration_x']) : 1;
            $repeat_end_type = isset($data['repeat_end_type']) ? trim($data['repeat_end_type']) : 0;
            $event_end = '';
            
            $max_repeat = $repeat_end_type != 1 && isset($data['max_repeat']) ? (int)$data['max_repeat'] : 1;
            $repeat_end = $repeat_end_type == 1 && isset($data['repeat_end']) ? $data['repeat_end'] : '';
                        
            $repeat_x = $repeat_x > 0 ? (int)$repeat_x : 1;
            $duration_x = $duration_x > 0 ? (int)$duration_x : 1;
            $max_repeat = $max_repeat > 0 ? (int)$max_repeat : 1;
            
            if ($repeat_end_type == 1 && !geodir_event_is_date($repeat_end)) {
                $repeat_end = '';
            }
            
            if (!geodir_event_is_date($event_start)) {
                $event_start = $default_start;
            }
            
            $event_recurring_dates = geodir_event_date_occurrences($repeat_type, $event_start, $event_end, $repeat_x, $max_repeat, $repeat_end, $repeat_days, $repeat_weeks);
            $event_recurring_dates = !empty($event_recurring_dates) ? implode(",", $event_recurring_dates) : '';
        }
    } else {
        if (!geodir_event_is_date($event_start)) {
            $event_start = $default_start;
        }
                
        if (strtotime($event_end) < strtotime($event_start)) {
            $event_end = $event_start;
        }
        
        $event_recurring_dates = $event_start;
        
        $starttimes = array();
        $endtimes = array();
        
        $repeat_type = '';
        $repeat_x = '';
        $duration_x = '';
        $repeat_end_type = '';
        $max_repeat = '';
        $repeat_end = '';
        $different_times = false;
    }

    $recurring_data = array();
    $recurring_data['is_recurring'] = $is_recurring;
    $recurring_data['event_start'] = $event_start;
    $recurring_data['event_end'] = $event_end;
    $recurring_data['event_recurring_dates'] = $event_recurring_dates;
    $recurring_data['all_day'] = $all_day;
    $recurring_data['starttime'] = $starttime;
    $recurring_data['endtime'] = $endtime;
    $recurring_data['different_times'] = $different_times;
    $recurring_data['starttimes'] = $starttimes;
    $recurring_data['endtimes'] = $endtimes;
    $recurring_data['repeat_type'] = $repeat_type;
    $recurring_data['repeat_x'] = $repeat_x;
    $recurring_data['duration_x'] = $duration_x;
    $recurring_data['repeat_end_type'] = $repeat_end_type;
    $recurring_data['max_repeat'] = $max_repeat;
    $recurring_data['repeat_end'] = $repeat_end;
    $recurring_data['repeat_days'] = $repeat_days;
    $recurring_data['repeat_weeks'] = $repeat_weeks;
        
    return $recurring_data;
}

/**
 * Creates the event schedule dates for given schedule info.
 *
 * @since 1.0.0
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param array $event_schedule_info Event schedule data.
 * @param int $post_id Event listing id.
 *
 * @return bool True if success else False.
 */
function geodir_gt2gd_create_event_schedules($event_schedule_info, $post_id) {
    global $wpdb;

    if (empty($event_schedule_info) || $post_id == '') {
        return false;
    }

    $format = geodir_event_date_format();
    $default_start = date_i18n( $format, current_time( 'timestamp' ) );

    $wpdb->query("DELETE FROM " . $wpdb->prefix . "geodir_event_schedule WHERE event_id='" . $post_id . "'");

    $event_recurring_dates = array();
    if ( isset( $event_schedule_info['event_recurring_dates'] ) && !empty( $event_schedule_info['event_recurring_dates'] ) ) {
        if ( is_array( $event_schedule_info['event_recurring_dates'] ) ) {
            $event_recurring_dates = $event_schedule_info['event_recurring_dates'];
        } else {
            $event_recurring_dates = explode( ',', $event_schedule_info['event_recurring_dates'] );
        }
    }

    // all day
    $all_day = isset( $event_schedule_info['all_day'] ) && !empty( $event_schedule_info['all_day'] ) ? true : false;
    $different_times = isset( $event_schedule_info['different_times'] ) && !empty( $event_schedule_info['different_times'] ) ? true : false;
    $starttime = !$all_day && isset( $event_schedule_info['starttime'] ) ? $event_schedule_info['starttime'] : '';
    $endtime = !$all_day && isset( $event_schedule_info['endtime'] ) ? $event_schedule_info['endtime'] : '';
    $starttimes = !$all_day && isset( $event_schedule_info['starttimes'] ) ? $event_schedule_info['starttimes'] : array();
    $endtimes = !$all_day && isset( $event_schedule_info['endtimes'] ) ? $event_schedule_info['endtimes'] : array();

    if ( $event_schedule_info['is_recurring'] ) {
        if ( !empty( $event_recurring_dates ) ) {
            $duration = isset( $event_schedule_info['duration_x'] ) && (int)$event_schedule_info['duration_x'] > 0 ? (int)$event_schedule_info['duration_x'] : 1;
            $repeat_type = isset( $event_schedule_info['repeat_type'] ) ? $event_schedule_info['repeat_type'] : 'custom';
                                    
            $recurring = 1;
            $duration--;
        
            $c = 0;
            foreach( $event_recurring_dates as $key => $date ) {
                if ( $repeat_type == 'custom' && $different_times ) {
                    $duration = 0;
                    $starttime = isset( $starttimes[$c] ) ? $starttimes[$c] : '';
                    $endtime = isset( $endtimes[$c] ) ? $endtimes[$c] : '';
                }
                
                if ( $all_day == 1 ) {
                    $starttime = '';
                    $endtime = '';
                }
                
                $event_enddate = date_i18n( 'Y-m-d', strtotime( $date . ' + ' . $duration . ' day' ) );
                $sql = $wpdb->prepare( "INSERT INTO  " . $wpdb->prefix . "geodir_event_schedule (event_id, event_date, event_enddate, event_starttime, event_endtime, recurring, all_day) VALUES (%d, %s, %s, %s, %s, %d, %d)", array( $post_id, $date, $event_enddate, $starttime, $endtime, $recurring, $all_day ) ) ;
                $wpdb->query( $sql );
                $c++;
            }
        }
    } else {
        $start_date = isset( $event_schedule_info['event_start'] ) ? $event_schedule_info['event_start'] : '';
        $end_date = isset( $event_schedule_info['event_end'] ) ? $event_schedule_info['event_end'] : $start_date;
                
        if ( !geodir_event_is_date( $start_date ) && !empty( $event_recurring_dates ) ) {
            $start_date = $event_recurring_dates[0];
        }
        
        if ( !geodir_event_is_date( $start_date ) ) {
            $start_date = $default_start;
        }
        
        if ( strtotime( $end_date ) < strtotime( $start_date ) ) {
            $end_date = $start_date;
        }
        
        if ( $starttime == '' && !empty( $starttimes ) ) {
            $starttime = $starttimes[0];
            $endtime = $endtimes[0];
        }
        
        if ( $all_day ) {
            $starttime = '';
            $endtime = '';
        }
        $recurring = 0;
        
        $sql = $wpdb->prepare( "INSERT INTO  " . $wpdb->prefix . "geodir_event_schedule (event_id, event_date, event_enddate, event_starttime, event_endtime, recurring, all_day) VALUES (%d, %s, %s, %s, %s, %d, %d)", array( $post_id, $start_date, $end_date, $starttime, $endtime, $recurring, $all_day ) ) ;
        $wpdb->query( $sql );
    }

    return true;
}

/**
 * Get the total posts counts for geotheme post types.
 *
 * @since 1.0.2
 *
 * @return int Total geotheme post types posts count.
 */
function geodir_gt2gd_count_total_listings() {
    $listings = (int)geodir_gt2gd_count_listings('place');
    
    if (geodir_gt2gd_is_active('geodir_event_manager')) {
        $listings += (int)geodir_gt2gd_count_listings('event');
    }
    
    return $listings;
}

/**
 * Get the posts counts for the current post type.
 *
 * @since 1.0.2
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type Post type.
 * @return int Posts count.
 */
function geodir_gt2gd_count_listings( $post_type ) {
    global $wpdb;
        
    $table = $wpdb->prefix . 'gt_' . $post_type . '_detail';

    $query = $wpdb->prepare( "SELECT COUNT({$wpdb->posts}.ID) FROM {$wpdb->posts} INNER JOIN {$table} ON {$table}.post_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.post_type = %s", $post_type );

    $count = (int)$wpdb->get_var( $query );

    return $count;
}

/**
 * Get the posts for the current post type.
 *
 * @since 1.0.2
 *
 * @global object $wpdb WordPress Database object.
 *
 * @param string $post_type The post type.
 * @param int $per_page Per page limit. Default 0.
 * @param int $page_no Page number. Default 0.
 * @return array Array of posts data.
 */
function geodir_gt2gd_get_listings( $post_type, $per_page = 0, $page_no = 1 ) {
    global $wpdb;
        
    $table = $wpdb->prefix . 'gt_' . $post_type . '_detail';

    $limit = '';
    if ( $per_page > 0 && $page_no > 0 ) {
        $offset = ( $page_no - 1 ) * $per_page;
        
        if ( $offset > 0 ) {
            $limit = " LIMIT " . $offset . "," . $per_page;
        } else {
            $limit = " LIMIT " . $per_page;
        }
    }

    $query = $wpdb->prepare( "SELECT {$table}.*, {$wpdb->posts}.ID, {$wpdb->posts}.post_status, {$wpdb->posts}.post_date FROM {$wpdb->posts} INNER JOIN {$table} ON {$table}.post_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.post_type = %s ORDER BY {$wpdb->posts}.ID ASC" . $limit, $post_type );

    $query = apply_filters( 'geodir_gt2gd_get_listings_query', $query, $post_type );
    $results = (array)$wpdb->get_results( $query );

    return apply_filters( 'geodir_gt2gd_get_listings', $results, $post_type );
}
