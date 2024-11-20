<?php
namespace TBGravityFormsExtended;

class AdminActions {
	
  /**
   * Approve Entry
   * 
   * Approves a Gravity Forms entry by adding the user to the approved community members table
   * and deleting the entry from Gravity Forms.
   *
   * @param int $entry_id The ID of the Gravity Forms entry to approve.
   * @return bool True if the entry was approved and deleted successfully, false otherwise.
   */
	public function approve_entry($entry_id) {
    global $wpdb;

    $entry = \GFAPI::get_entry($entry_id);

    if (!is_wp_error($entry)) {
      $social_data = [];

      if (!empty($entry['16'])) {
        $social_data['bluesky'] = ltrim($entry['16'], '@');
      }
      if (!empty($entry['7'])) {
        $social_data['instagram'] = ltrim($entry['7'], '@');
      }
      if (!empty($entry['8'])) {
        $social_data['reddit'] = ltrim($entry['8'], '@');
      }
      if (!empty($entry['9'])) {
        $social_data['tiktok'] = ltrim($entry['9'], '@');
      }
      if (!empty($entry['12'])) {
        $social_data['twitch'] = ltrim($entry['12'], '@');
      }
      if (!empty($entry['11'])) {
        $social_data['twitter'] = ltrim($entry['11'], '@');
      }
      if (!empty($entry['14'])) {
        $social_data['youtube'] = ltrim($entry['14'], '@');
      }

      $data = [
        'first_name' => $entry['1.3'],
        'last_name' => $entry['1.6'],
        'user_email' => $entry['5'],
        'country' => $entry['3'],
        'blurb' => $entry['4'],
        'social_data' => json_encode($social_data)
      ];

      $table_name = $wpdb->prefix . 'tb_approved_community_members';
      $wpdb->insert($table_name, $data);

      // Delete the entry from Gravity Forms
      \GFAPI::delete_entry($entry_id);

      return true;
    }

    return false;
  }
	
  /**
   * Denies a Gravity Forms entry by deleting the entry from Gravity Forms.
   *
   * @param int $entry_id The ID of the Gravity Forms entry to deny.
   * @return bool True if the entry was deleted successfully, false otherwise.
   */
	public function deny_entry($entry_id) {
		$result = \GFAPI::delete_entry($entry_id);

    if (is_wp_error($result)) {
      return false;
    }

    return true;
  }

  /**
   * Deletes a member from the approved community members table.
   *
   * @param int $member_id The ID of the member to delete.
   * @return void
   */
  public function delete_member($member_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tb_approved_community_members';
    $wpdb->delete($table_name, ['id' => $member_id]);
  }
}