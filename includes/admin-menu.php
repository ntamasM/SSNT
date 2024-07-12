<?php
// Add settings menu
function ssnt_add_admin_menu() {
    add_menu_page(
        'SSNT Plugin Settings', // Page title
        'SSNT Settings',        // Menu title
        'edit_posts',           // Capability
        'ssnt-settings',        // Menu slug
        'ssnt_settings_page',   // Callback function
        'dashicons-admin-generic', // Icon
        2                       // Position
    );

    add_submenu_page(
        'ssnt-settings',            // Parent slug
        'Site Settings from Ntamas', // Page title
        'Site Settings',             // Menu title
        'edit_posts',                // Capability
        'ssnt-settings',             // Menu slug
        'ssnt_settings_page'         // Callback function
    );

    add_submenu_page(
        'ssnt-settings',                // Parent slug
        'SSNT Additional Settings',     // Page title
        'Additional Settings',          // Menu title
        'manage_options',               // Capability
        'ssnt-additional-settings',     // Menu slug
        'ssnt_additional_settings_page' // Callback function
    );
}
add_action( 'admin_menu', 'ssnt_add_admin_menu' );