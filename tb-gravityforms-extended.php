<?php
/**
 * Plugin Name: Think Bigg - Gravity Forms - Community Database
 * Description: Extends the features of Gravity Forms to add users to the community users table.
 * Version: 1.0.0
 * Author: Jason Biggs - Think Bigg Dev
 * Author URI: http://www.thinkbigg.dev/
 */

namespace TBGravityFormsExtended;

defined('ABSPATH') || exit;

// Define constants for versioning and paths
define('TB_GF_PLUGIN_VERSION', '1.0.0');
define('TB_GF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TB_GF_PLUGIN_DIR', plugin_dir_path(__FILE__));

/** Autoload dependencies */
require_once TB_GF_PLUGIN_DIR . 'includes/db-install.php';

/**
 * Enqueue Scripts and Styles
 */
function enqueue_scripts_and_styles() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // Admin scripts and styles
    if ($screen && $screen->id === 'tb-tools_page_tb-gravity-forms-extended-plugin') {
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js', ['jquery'], '5.3.3', true);
        wp_enqueue_style('tb-admin-style', TB_GF_PLUGIN_URL . 'dist/css/admin.css', [], TB_GF_PLUGIN_VERSION);
        wp_enqueue_script('tb-admin-script', TB_GF_PLUGIN_URL . 'dist/js/main.js', ['jquery', 'bootstrap-js'], TB_GF_PLUGIN_VERSION, true);
    }

    // Frontend scripts and styles
    if (is_page_template('meet-the-community.php')) {
        wp_enqueue_style('tb-frontend-style', TB_GF_PLUGIN_URL . 'dist/css/frontend.css', [], TB_GF_PLUGIN_VERSION);
    }
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts_and_styles');
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts_and_styles');

/**
 * Plugin Activation Hook
 */
function activate_plugin() {
    ob_start();
    try {
        community_db_install();
    } catch (\Exception $e) {
        error_log('Error during activation: ' . $e->getMessage());
    } finally {
        $output = ob_get_clean();
        if (!empty($output)) {
            error_log('Unexpected output during activation: ' . $output);
        }
    }
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\activate_plugin');

/**
 * Register and Load Page Template
 */
function register_template($templates) {
    $templates['meet-the-community.php'] = __('Meet the Community', 'tb-gravity-forms-extended');
    return $templates;
}
add_filter('theme_page_templates', __NAMESPACE__ . '\register_template');

function load_template($template) {
    if (is_page_template('meet-the-community.php')) {
        $plugin_template = TB_GF_PLUGIN_DIR . 'meet-the-community.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', __NAMESPACE__ . '\load_template');

/**
 * Add Admin Menu Items
 */
function add_menu_items() {
    $parent_title = __('TB Tools', 'tb-gravity-forms-extended');
    $this_title = __('TB Gravity Forms Extended', 'tb-gravity-forms-extended');
    $parent_slug = sanitize_title($parent_title) . '-menu-item';
    $this_slug = sanitize_title($this_title) . '-plugin';
    $capability = 'manage_options';

    global $menu;
    $menu_exists = array_filter($menu, function ($item) use ($parent_title) {
        return strtolower($item[0]) === strtolower($parent_title);
    });

    if ($menu_exists) {
        add_submenu_page($parent_slug, $this_title, $this_title, $capability, $this_slug, __NAMESPACE__ . '\render_admin_page');
    } else {
        add_menu_page($parent_title, $parent_title, $capability, $parent_slug, '', 'dashicons-admin-generic');
        add_submenu_page($parent_slug, $this_title, $this_title, $capability, $this_slug, __NAMESPACE__ . '\render_admin_page');
        remove_submenu_page($parent_slug, $parent_slug);
    }
}
add_action('admin_menu', __NAMESPACE__ . '\add_menu_items');

/**
 * Render Admin Page
 */
function render_admin_page() {
    include TB_GF_PLUGIN_DIR . 'admin.php';
}