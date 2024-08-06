<?php
/*
Plugin Name: Site Settings from Ntamas
Plugin URI: https://github.com/ntamasM/SSNT/blob/main/site-settings-from-ntamas.php
Description: A plugin to create custom settings page and manage those settings.
Version: 1.0
Author: Ntamas
Author URI: https://ntamadakis.gr
License: GPL2
Text Domain: ssnt
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'admin/includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'admin/includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/includes/additional-settings-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/includes/bricks-builder-integration.php';

function ssnt_css_and_js($hook)
{
    if (isset($_GET['page']) && ($_GET['page'] === 'ssnt-settings' || $_GET['page'] === 'ssnt-additional-settings')) {
        wp_enqueue_media();
        wp_enqueue_script('ssnt-admin-script', plugins_url('admin/js/ssnt-admin.js', __FILE__), array('jquery'), null, true);

        if ($_GET['page'] === 'ssnt-additional-settings') {
            wp_enqueue_style('ssnt_admin_styles', plugin_dir_url(__FILE__) . 'admin/css/ssnt_admin_settings_styles.css');
        }
    }
}
add_action('admin_enqueue_scripts', 'ssnt_css_and_js');
