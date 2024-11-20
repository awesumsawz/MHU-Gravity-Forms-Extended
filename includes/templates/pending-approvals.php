<div class="accordion-item">
  <h2 class="accordion-header">
    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pending-collapse" aria-expanded="false" aria-controls="flush-collapseOne">
      Pending Approvals (Scrolls L/R)
    </button>
  </h2>
  <div id="pending-collapse" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExample">
    <div class="accordion-body table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Country</th>
            <th>About</th>
            <th>Social Handles</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="table-group-divider">
          <?php foreach ($entries as $entry): ?>
            <tr>
              <td><?php echo esc_html($entry['1.3']); ?></td>
              <td><?php echo esc_html($entry['1.6']); ?></td>
              <td><?php echo esc_html($entry['5']); ?></td>
              <td><?php echo esc_html($entry['3']); ?></td>
              <td><?php echo esc_html($entry['4']); ?></td>
              <td>
                <?php 
                $bluesky = esc_html(ltrim($entry['16'], '@'));
                $instagram = esc_html(ltrim($entry['7'], '@'));
                $reddit = esc_html(ltrim($entry['8'], '@'));
                $tiktok = esc_html(ltrim($entry['9'], '@'));
                $twitch = esc_html(ltrim($entry['12'], '@'));
                $twitter = esc_html(ltrim($entry['11'], '@'));
                $youtube = esc_html(ltrim($entry['14'], '@'));
                
                $social_array = [];
                $bluesky ? $social_array[] = "<strong>Bluesky:</strong> " . $bluesky : "";
                $instagram ? $social_array[] = "<strong>Instagram:</strong> " . $instagram : "";
                $reddit ? $social_array[] = "<strong>Reddit:</strong> " . $reddit : "";
                $tiktok ? $social_array[] = "<strong>TikTok:</strong> " . $tiktok : "";
                $twitch ? $social_array[] = "<strong>Twitch:</strong> " . $twitch : "";
                $twitter ? $social_array[] = "<strong>Twitter:</strong> " . $twitter : "";
                $youtube ? $social_array[] = "<strong>YouTube:</strong> " . $youtube : "";

                echo !empty($social_array) ? implode("<br/>", $social_array) : "";
                ?>
              </td>
              <td class="approval">
                <?php 
                if (isset($entry['status']) && ($entry['status'] === 'approve' || $entry['status'] === 'deny')):
                  echo $entry['status'] === 'approve' ? '<span class="text-success">User has been added</span>' : "";
                  echo $entry['status'] === 'deny' ? '<span class="text-danger">User has been denied</span>' : "";
                else: 
                ?>
                  <form method="POST">
                    <input type="hidden" name="entry_id" value="<?php echo esc_attr($entry['id']); ?>">
                    <button type="submit" name="approve_entry" class="approve-entry btn btn-success btn-block">Approve</button>
                    <button type="submit" name="deny_entry" class="deny-entry btn btn-danger btn-block">Deny</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>