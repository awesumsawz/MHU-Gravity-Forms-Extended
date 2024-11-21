<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#active-collapse" aria-expanded="false" aria-controls="flush-collapseTwo">
            Active Community
        </button>
    </h2>
    <div id="active-collapse" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExample">
        <div class="accordion-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    usort($approved_members, function($a, $b) {
                        return strcmp($a->last_name, $b->last_name);
                    });

                    foreach ($approved_members as $member): ?>
                        <tr>
                            <td><?php echo esc_html($member->first_name); ?></td>
                            <td><?php echo esc_html($member->last_name); ?></td>
                            <td><?php echo esc_html($member->user_email); ?></td>
                            <td class="approval">
                                <?php if (isset($member->status) && $member->status === 'deleted'): ?>
                                    <span class="text-danger">User has been deleted</span>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="member_id" value="<?php echo esc_attr($member->id); ?>">
                                        <button type="submit" name="delete_member" class="delete-member btn btn-danger btn-block">Delete</button>
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