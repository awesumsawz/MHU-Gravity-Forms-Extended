<?php
namespace TBGravityFormsExtended;

global $wpdb;

include dirname(__FILE__) . '/includes/functions/AdminActions.php';
include dirname(__FILE__) . '/includes/functions/AdminOutput.php';

$admin_actions = new AdminActions();
$admin_output = new AdminOutput();

// Query to get approved community members
$table_name = $wpdb->prefix . 'tb_approved_community_members';
$approved_members = $wpdb->get_results("SELECT * FROM $table_name");

// Query to get Gravity Forms entries
$form_id = 1;
$entries = \GFAPI::get_entries($form_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = '';

    if (isset($_POST['approve_entry'])) {
        $entry_id = intval($_POST['entry_id']);
        $action = 'approve';
    } elseif (isset($_POST['deny_entry'])) {
        $entry_id = intval($_POST['entry_id']);
        $action = 'deny';
    } elseif (isset($_POST['delete_member'])) {
        $member_id = intval($_POST['member_id']);
        $admin_actions->delete_member($member_id);
        update_member_status($approved_members, $member_id, 'deleted');
    }

    if ($action) {
        $method = $action . '_entry';
        if ($admin_actions->$method($entry_id)) {
            update_entry_status($entries, $entry_id, $action);
        }
    }
}

function update_entry_status(&$entries, $entry_id, $status) {
    foreach ($entries as &$entry) {
        if ($entry['id'] == $entry_id) {
            $entry['status'] = $status;
            break;
        }
    }
}

function update_member_status(&$members, $member_id, $status) {
    foreach ($members as &$member) {
        if ($member->id == $member_id) {
            $member->status = $status;
            break;
        }
    }
}

echo '<main class="tb-gf-extended">';
echo '<div class="accordion accordion-flush">';

$admin_output->render_pending_approvals($entries);
$admin_output->render_active_community($approved_members);

echo '</div>';
echo '</main>';