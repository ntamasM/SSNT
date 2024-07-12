<?php
/*
Plugin Name: Site Settings from Ntamas
Plugin URI: https://ntamadakis.gr
Description: A plugin to create custom settings page and manage those settings.
Version: 1.0
Author: Ntamas
Author URI: https://ntamadakis.gr
License: GPL2
Text Domain: ssnt
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-menu.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/additional-settings-page.php';
// wp_enqueue_style('ssnt_admin_styles', plugin_dir_url(__FILE__) . 'css/ssnt_admin_styles.css');

add_action('admin_enqueue_scripts', 'ssnt_css_and_js');

function ssnt_css_and_js($hook)
{
    $current_screen = get_current_screen();
    
    if ( strpos($current_screen->base, 'ssnt-additional-settings') === false) {
        return;
    } else {
        wp_enqueue_style('ssnt_admin_styles', plugin_dir_url(__FILE__) . 'css/ssnt_admin_settings_styles.css');
    }
}