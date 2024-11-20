<?php
namespace TBGravityFormsExtended;

function community_db_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'tb_approved_community_members';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id int(10) NOT NULL AUTO_INCREMENT,
            submit_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            user_email varchar(200) NOT NULL,
            country varchar(100) NOT NULL,
            social_data JSON NOT NULL,
            blurb TEXT NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        error_log('SQL Query: ' . $sql);

        if ($wpdb->last_error) {
            error_log('Error creating table: ' . $wpdb->last_error);
        } else {
            error_log('Table created successfully or already exists.');
        }
    } else {
        error_log('Table already exists.');
    }
}

//? what if the table doesn't get created when the plugin is first activated?
// deactivate/reactivate plugin; check for debug errors and attempt correction; history of jquery migrate causing issues with activation; ensure the table creation script is working as intended by checking debug;
//? how do I reset the table?
// delete the existing first-party table, disable the plugin, and then enable the plugin. This will get the 'require_once' that will include the install script to fire again.

?>