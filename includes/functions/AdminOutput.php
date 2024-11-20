<?php
namespace TBGravityFormsExtended;

class AdminOutput {
  public function render_pending_approvals($entries) {
    include dirname(__DIR__) . '/templates/pending-approvals.php';
  }

  public function render_active_community($approved_members) {
    include dirname(__DIR__) . '/templates/active-community.php';
  }
}