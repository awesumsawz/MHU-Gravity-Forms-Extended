<?php
/**
 * Template Name: Meet the Community
 * Description: A template to display the community members.
 * 
 * (future) add search functionality
 */

global $wpdb;

$bannerBackgroundImage = get_field('banner_background')['image'];

if (!empty($bannerBackgroundImage)) {
	$bannerBackgroundImage = get_field('banner_background')['image'];
	$bannerBackgroundAttributionName = get_field('banner_background')['attribution_name'];
	$bannerBackgroundAttributionWebText = get_field('banner_background')['attribution_website_text'];
	$bannerBackgroundAttributionWebURL = get_field('banner_background')['attribution_website_url'];
	$bannerBackgroundAttributionSocialText = get_field('banner_background')['attribution_social_text'];
	$bannerBackgroundAttributionSocialURL = get_field('banner_background')['attribution_social_url'];	
}

$table_name = $wpdb->prefix . 'tb_approved_community_members';
$approved_members = $wpdb->get_results("SELECT * FROM $table_name");

$userList = [];
foreach ($approved_members as $member) {
  $social_data = json_decode($member->social_data, true);
  $userList[] = [
    "name" => $member->first_name . ' ' . $member->last_name,
    "country" => $member->country,
    "about" => $member->blurb,
    "email" => $member->user_email,
    "social" => $social_data
  ];
}

get_header();
?>

<main class="community">
	<section id="banner" style="background-image: url('<?php echo $bannerBackgroundImage ?>')">
		<h1><?php echo get_the_title(); ?></h1>
		<?php
		if (isset($bannerBackgroundAttributionName)):
			?>
			<div class="imageAttribution">
				<div class="trigger" onclick="toggleImageAttribution(this)"><iconify-icon icon="bi:info-circle-fill"></iconify-icon></div>
				<div class="content" style="display: none;">
					<div>Image Courtesy of</div>
					<div><?php echo $bannerBackgroundAttributionName ?></div>
					<?php
					if (isset($bannerBackgroundAttributionSocialText)):
						?>
						<div><a href="<?php echo $bannerBackgroundAttributionSocialURL ?>"><?php echo $bannerBackgroundAttributionSocialText ?></a></div>
						<?php
					endif;
					if (isset($bannerBackgroundAttributionWebText)):
						?>
						<div><a href="<?php echo $bannerBackgroundAttributionWebURL ?>"><?php echo $bannerBackgroundAttributionWebText ?></a></div>
						<?php
					endif;
					?>
				</div>
			</div>
			<?php
		endif;
		?>
		<div class="overlay"></div>
	</section>

	<section id="body">
		<div class="join">
			<button type="button" class="btn btn-primary join-open" data-bs-toggle="modal" data-bs-target="#joinModal">
				Join Us!
			</button>
			<div class="modal fade join-modal" id="joinModal" tabindex="-1" aria-labelledby="joinModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
					<div class="modal-content">
						<div class="modal-header">
							<h2 class="modal-title">Join the Community</h2>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon icon="vaadin:close"></iconify-icon></button>
						</div>
						<div class="modal-body">
							<?php echo do_shortcode('[gravityform id="1" title="false"]') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="user-list">
			<div class="accordion">
				<div class="accordion" id="usersAccordion">
					<?php 
					$user_list_alphabet = [];
					$grouped_users = [];
				
					foreach ($userList as $user) {
						$nameParts = explode(" ", $user['name']);
						$lastName = end($nameParts);
						$firstLetter = strtoupper($lastName[0]);
					
						if (!in_array($firstLetter, $user_list_alphabet)) {
							$user_list_alphabet[] = $firstLetter;
						}
					
						$grouped_users[$firstLetter][] = $user;
					}
				
					sort($user_list_alphabet); 

					foreach ($user_list_alphabet as $letter):
						?>
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $letter?>" aria-expanded="false" aria-controls="collapse<?php echo $letter ?>">
									<div class="letter"><?php echo $letter ?></div>
									<div class="icon"><iconify-icon icon="ooui:expand"></iconify-icon></div>
								</button>
							</h2>
							<div id="collapse<?php echo $letter ?>" class="accordion-collapse collapse" data-bs-parent="#usersAccordion">
								<div class="accordion-body">
									<?php
									if (isset($grouped_users[$letter])):
										foreach ($grouped_users[$letter] as $user):
											$modalId = str_replace(" ", "_", strtolower($user['name'])) . "_modal";
											?>
											<div class="user">
												<button type="button" class="btn btn-primary user-button" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId ?>">
													<?php echo htmlspecialchars($user['name']) ?>
												</button>
												<div class="modal fade" id="<?php echo $modalId ?>" tabindex="-1" aria-labelledby="<?php echo $modalId ?>Label" aria-hidden="true">
													<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
														<div class="modal-content">
															<div class="modal-header">
																<h3 class="modal-title fs-5" id="<?php echo $modalId ?>Label"><?php echo htmlspecialchars($user['name']) ?></h3>
																<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon icon="vaadin:close"></iconify-icon></button>
															</div>
															<div class="modal-body">
																<div class="container text-left">
																	<div class="row">
																		<div class="col-md-2"><strong>Country</strong></div>
																		<div class="col"><?php echo htmlspecialchars($user['country']) ?></div>
																	</div>
																	<div class="row">
																		<div class="col-md-2"><strong>About</strong></div>
																		<div class="col"><?php echo htmlspecialchars($user['about']) ?></div>
																	</div>
																	<div class="row">
																		<div class="col-md-2"><strong>Email</strong></div>
																		<div class="col"><?php echo htmlspecialchars($user['email']) ?></div>
																	</div>
																	<?php
																	ksort($user['social']);

																	foreach ($user['social'] as $platform => $handle):
																		?>
																		<div class="row">
																			<div class="col-md-2"><strong><?php echo ucfirst($platform) ?></strong></div>
																			<div class="col"><?php echo htmlspecialchars($handle)?></div>
																		</div>
																		<?php
																	endforeach;
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<?php
										endforeach;
									endif;
									?>
								</div>
							</div>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
		</div>
	</section>
	<section id="disclaimer">
		<div class="text-center">
			<p class="fs-1">
				<em>If you would like to have your information removed or updated, please contact us at admin@modelhorseuniversity.com</em>
			</p>
		</div>
	</section>
</main>

<?php
get_footer();