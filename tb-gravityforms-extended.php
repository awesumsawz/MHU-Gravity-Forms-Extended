<?php 
/**
 * Plugin Name: Think Bigg - Gravity Forms - Community Database
 * Description: Extends the features of Gravity Forms in order to add users to the community users table
 * Version: 1.0.0
 * Author: Jason Biggs - Think Bigg Dev
 * Author URI: http://www.thinkbigg.dev/
 */

namespace TBGravityFormsExtended;

function run_enqueues() {
  $screen = get_current_screen();
  if ($screen->id === 'tb-tools_page_tb-gravity-forms-extended-plugin') {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), null);
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js', array('jquery'), null, true);
    
    wp_enqueue_style('enqueue-style', plugin_dir_url(__FILE__) . 'dist/css/admin.css', array(), null);
    wp_enqueue_script('enqueue-script', plugin_dir_url(__FILE__) . 'dist/js/main.js', array('jquery', 'bootstrap-js'), null, true);
  }
  
  if (is_page_template('meet-the-community.php')) {
    wp_enqueue_style('meet-the-community-style', plugin_dir_url(__FILE__) . 'dist/css/frontend.css');
  }
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\run_enqueues');

/**
 * Install the community members database table
 */
require_once(dirname(__FILE__) . '/includes/db-install.php');
function activate_plugin() {
  ob_start();
  community_db_install();
  $output = ob_get_clean();
  if (!empty($output)) {
    error_log('Unexpected output during activation: ' . $output);
  }
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\activate_plugin');

/**
 * Include the Template File
 */
add_filter('theme_page_templates', __NAMESPACE__ . '\register_template', 10, 3);
function register_template($templates) {
  $templates[ 'meet-the-community.php'] = 'Meet the Community';
  return $templates;
};
add_filter('template_include', __NAMESPACE__ . '\load_template');

function load_template($template) {
  if (is_page_template('meet-the-community.php')) {
    $plugin_template = plugin_dir_path(__FILE__) . '/meet-the-community.php';
    if (file_exists($plugin_template)) {
      return $plugin_template;
    }
  }
  return $template;
};

/**
 * Build out the admin page
 */
function add_menu_items() {
  global $menu;

  $menu_exists = FALSE;

  $parent_title = "TB Tools";            							/* unique */
  $parent_function = "#";                     				/* unique */
  $this_title = "TB Gravity Forms Extended"; 					/* unique */
  $this_function = __NAMESPACE__ . '\create_admin';  /* unique */

  $parent_slug = str_replace(" ", "-", strtolower($parent_title)) . '-menu-item';
  $this_slug = str_replace(" ", "-", strtolower($this_title)) . '-plugin';

  foreach ($menu as $item) {
    if (strtolower($item[0]) == strtolower($parent_title)) { // TOP-LEVEL MENU
      $menu_exists = TRUE;
    }
  }

  if ($menu_exists) { // If the di_operations menu already exists, add the admin item as a child
    add_submenu_page(
      $parent_slug,
      $this_title . " Page",
      $this_title,
      'manage_options',
      $this_slug,
      $parent_function
    );
  } else { // else, create the parent menu item and then create this as a child item
    add_menu_page(
      $parent_title . " Page",
      $parent_title,
      'manage_options',
      $parent_slug,
      $parent_function
    );
    add_submenu_page(
      $parent_slug,
      $this_title . " Page",
      $this_title,
      'manage_options',
      $this_slug,
      $this_function
    );
    remove_submenu_page(
      $parent_slug,
      $parent_slug
    );
  }
}
function create_admin() {
  include dirname(__FILE__) . '/admin.php';
}
add_action('admin_menu', __NAMESPACE__ . '\add_menu_items');