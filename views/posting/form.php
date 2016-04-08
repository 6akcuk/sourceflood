<?php
use SourceFlood\Validator;

wp_enqueue_script('post');
?>

<input type="hidden" name="post_type" value="<?= $post_type ?>">
<?php
	$word_ai = get_option('workhorse_word_ai_key');

	if ($word_ai && $word_ai_email = get_option('workhorse_word_ai_email')):
?>
<input type="hidden" id="word-ai-email" name="word_ai_email" value="<?= $word_ai_email ?>">
<input type="hidden" id="word-ai-key" name="word_ai_key" value="<?= $word_ai ?>">
<?php endif; ?>

<div id="poststuff" class="PostForm">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<div class="PostForm__name-wrap<?php if (Validator::hasError('name')) echo ' PostForm--error' ?>">
				<input type="text" name="name" class="PostForm__name" placeholder="Project name here" value="<?= Validator::old('name') ?>">
				<?php if (Validator::hasError('name')): ?>
				<span class="PostForm__error"><?= Validator::get('name') ?></span>
				<?php endif; ?>
			</div>

			<div class="PostForm__title-wrap<?php if (Validator::hasError('title')) echo ' PostForm--error' ?>">
				<input type="text" id="title" name="title" class="PostForm__title" placeholder="Enter title here" value="<?= Validator::old('title') ?>">
				<?php if (Validator::hasError('title')): ?>
				<span class="PostForm__error"><?= Validator::get('title') ?></span>
				<?php endif; ?>

				<div id="edit-slug-box">
					<?php
						$old_permalink = Validator::old('permalink');
					?>
					<input type="hidden" name="permalink" value="<?= $old_permalink ?>">
					<strong>Permalink:</strong>
					<span><?= workhorse_permalink($old_permalink) ?></span>
					<a id="edit-permalink" class="button button-small" aria-label="Edit permalink">Edit</a>
					<a id="save-permalink" class="button button-small" style="display: none">OK</a>
					<a id="cancel-permalink" class="cancel button-link" style="display: none">Cancel</a>
				</div>
			</div>

			<div class="PostForm__body-wrap<?php if (Validator::hasError('content')) echo ' PostForm--error' ?>">
				<?php wp_editor(Validator::old('content'), 'content', array(
					'_content_editor_dfw' => '',
					'drag_drop_upload' => true,
					'tabfocus_elements' => 'content-html,save-post',
					'editor_height' => 300,
					'tinymce' => array(
						'resize' => false,
						'add_unload_trigger' => false,
					),
				)); ?>
				<?php if (Validator::hasError('content')): ?>
				<span class="PostForm__error"><?= Validator::get('content') ?></span>
				<?php endif; ?>
			</div>

			<div class="PostForm__buttons">
				<button class="button button-primary">Create Project</button>
			</div>
		</div>

		<div id="postbox-container-1" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<!-- Options -->
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle ui-sortable-handle"><span>Work Horse Options</span></h2>
					<div class="inside">
						<p>
							<label for="max-posts">
								<strong>Max Posts:</strong> <br>
								<em>
									Maximum number of posts to generate. <br>
									Input `0` if you want to generate all available posts from spintax.
								</em>
							</label>
							<input type="number" id="max-posts" name="max_posts" value="0">
						</p>

						<p>
							<strong>Distribute among users randomly:</strong> <br>
							<em>
								Distribute posts among <a href="<?= admin_url('admin.php?page=workhorse_users') ?>">Work Horse users</a> randomly.
							</em>
							<br>
							<input type="checkbox" id="distribute" name="distribute" value="1" <?= Validator::old('distribute') == 1 ? 'checked' : ''; ?>>
							<label for="distribute">Distribute</label>
						</p>
					</div>
				</div>

				<!-- DripFeed Property -->
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle ui-sortable-handle"><span>Work Horse Dripfeed Property</span></h2>
					<div class="inside">
						<?php
							$old_dripfeed_enabler = Validator::old('dripfeed_enabler', 0);
						?>
						<label for="dripfeed-enabler" class="selectit">
							<input id="dripfeed-enabler" name="dripfeed_enabler" type="checkbox" value="1" <?= $old_dripfeed_enabler == 1 ? 'checked' : ''; ?>>
							Enable Feature
						</label>
						
						<div id="dripfeed-wrap" style="display: <?= $old_dripfeed_enabler == 1 ? 'block' : 'none'; ?>;">
							<p>
								<label for="dripfeed-type"><strong>Dripfeed Type:</strong></label>
								<?php $old_dripfeed_type = Validator::old('dripfeed_type') ?>
								<select id="dripfeed-type" name="dripfeed_type">
									<option value="per-day"<?= $old_dripfeed_type == 'per-day' ? ' selected' : '' ?>>X posts/pages per day</option>
									<option value="over-days"<?= $old_dripfeed_type == 'over-days' ? ' selected' : '' ?>>Whole project dripped over X days</option>
								</select>
							</p>
								
							<p class="<?= (Validator::hasError('dripfeed_x')) ? 'PostForm--error' : '' ?>">
								<label for="dripfeed-x"><strong>X Parameter:</strong></label>
								<input type="text" id="dripfeed-x" name="dripfeed_x" value="<?= Validator::old('dripfeed_x') ?>">
								<?php if (Validator::hasError('dripfeed_x')): ?>
								<span class="PostForm__error"><?= Validator::get('dripfeed_x') ?></span>
								<?php endif; ?>
							</p>
						</div>
					</div>
				</div>

				<!-- Tags -->
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle ui-sortable-handle"><span>Work Horse Tags</span></h2>
					<div class="inside">
						<input type="hidden" name="tags">
						<p>
							<input type="text" id="tagsinput" size="16" autocomplete="off" value="<?= Validator::old('tags') ?>">
							<a id="add-tags" class="button">Add</a>
						</p>
						<p class="howto">Separate tags with commas</p>
						<div id="tags" class="tagchecklist"></div>

						<label for="noindex_tags" class="selectit">
							<input id="noindex_tags" name="noindex_tags" type="checkbox" value="1" <?= Validator::old('noindex_tags') == 1 ? 'checked' : ''; ?>>
							Noindex tags
						</label>
						<p class="howto">Helps fight duplicate content on tag pages; not recommended</p>

					</div>
				</div>

				<!-- Images Scraper -->
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle ui-sortable-handle"><span>Work Horse Images</span></h2>
					<div class="inside">
						<?php
							$google_api_key = get_option('workhorse_google_api_key');
							$old_exif_enabler = Validator::old('exif_enabler', 0);

							if (!empty($google_api_key)):
						?>
						<p>
							<label for="exif-enabler" class="selectit">
								<input id="exif-enabler" name="exif_enabler" type="checkbox" value="1" <?= $old_exif_enabler == 1 ? 'checked' : ''; ?>>
								Enable Image EXIF
							</label>
						</p>
						<?php else: ?>
							<div class="PixabayKeyWarning">
								Please, enter Google Maps API Key in <a href="/wp-admin/admin.php?page=workhorse_settings">Plugin Settings</a>.
							</div>
						<?php endif; ?>

						<p id="exif-wrap" style="display: <?= $old_exif_enabler == 1 ? 'block' : 'none' ?>">
							<a href="/index.php?api=workhorse&action=exif" onclick="return ImageEXIF.start(this)">Set Locations For Images</a>
						</p>

						<p>
							<?php
								$pixabay_key = get_option('workhorse_pixabay_key');
								$old_local_seo_enabler = Validator::old('local_seo_enabler', 0);
								
								if (!empty($pixabay_key)):
							?>
							<input type="hidden" id="pixabay-api-key" value="<?= $pixabay_key ?>">
							<a href="/wp-content/plugins/workhorse/imagescraper.php" title="Image Scraper" onclick="return ImageScraper.start(this)">Launch Images Scraper</a>
							<?php else: ?>
								<div class="PixabayKeyWarning">
									Please, enter Pixabay API Key in <a href="/wp-admin/admin.php?page=workhorse_settings">Plugin Settings</a>.
								</div>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="PostForm__boxes">

					<!-- On-Page SEO -->
					<div class="postbox">
						<button type="button" class="handlediv button-link" aria-expanded="true">
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
						<h2 class="hndle"><span>Work Horse SEO Options</span></h2>
						<div class="inside">
							<?php
								$old_on_page_seo = Validator::old('on_page_seo', 0);
							?>
							<table class="form-table">
							<tbody>
								<tr>
									<th>
										<label for="on-page-seo">Enable Work Horse On-Page Customizer:</label>
									</th>
									<td>
										<input id="on-page-seo" name="on_page_seo" type="checkbox" value="1"<?= $old_on_page_seo == 1 ? ' checked' : '' ?>>
									</td>
								</tr>
							</tbody>
							</table>

							<div id="on-page-seo-wrap" style="display: <?= $old_on_page_seo == 1 ? 'block' : 'none' ?>;">
								<table class="form-table">
								<tbody>
									<tr>
										<th>
											<label for="custom-title">Title:</label>
										</th>
										<td>
											<input id="custom-title" name="custom_title" type="text" class="full-width" value="<?= Validator::old('custom_title') ?>">
										</td>
									</tr>
									<tr>
										<th>
											<label for="custom-description">Description:</label>
										</th>
										<td>
											<textarea id="custom-description" name="custom_description" class="full-width"><?= Validator::old('custom_description') ?></textarea>
										</td>
									</tr>
									<tr>
										<th>
											<label for="custom-keywords">Keywords:</label>
										</th>
										<td>
											<textarea id="custom-keywords" name="custom_keywords" class="full-width"><?= Validator::old('custom_keywords') ?></textarea>
										</td>
									</tr>
								</tbody>
								</table>
							</div>

							<?php
								
							?>
							<table class="form-table">
							<tbody>
								<tr>
									<th>
										<label for="local-seo-enabler">Enable Work Horse Local SEO Feature:</label>
									</th>
									<td>
										<input id="local-seo-enabler" name="local_seo_enabler" type="checkbox" value="1" <?= $old_local_seo_enabler == 1 ? 'checked' : ''; ?>>
									</td>
								</tr>
							</tbody>
							</table>

							<div id="local-seo-wrap" style="display: <?= $old_local_seo_enabler == 1 ? 'block' : 'none'; ?>;">
							<table class="form-table">
								<tbody>
									<tr>
										<th>
											<label for="local-randomize">Randomize Results:</label>
										</th>
										<td>
											<input id="local-randomize" name="local_randomize" type="checkbox" value="1" <?= Validator::old('local_randomize') == 1 ? 'checked' : '' ?>>
										</td>
									</tr>
									<tr>
										<th>
											<label for="local-country">Country:</label>
										</th>
										<td>
											<select id="local-country" name="local_country">
												<option value>- Select Country -</option>
												<option value="us">United States</option>
												<option value="uk">United Kingdom</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>
											<label>Choose locations:</label> <br>
											<small>
												Press 'Shift + Left Mouse' to select all tree nodes
											</small>
										</th>
										<td>
											<div id="jstree"></div>
										</td>
									</tr>
								</tbody>
								</table>
							</div>

							<?php
								$old_schema = Validator::old('schema', 0);
							?>
							<table class="form-table">
							<tbody>
								<tr>
									<th>
										<label for="schema">Enable Work Horse Schema:</label>
									</th>
									<td>
										<input id="schema" name="schema" type="checkbox" value="1"<?= $old_schema == 1 ? ' checked' : '' ?>>
									</td>
								</tr>
							</tbody>
							</table>

							<div id="schema-wrap" style="display: <?= $old_schema == 1 ? 'block' : 'none' ?>;">
								<table class="form-table">
								<tbody>
									<tr>
										<th>
											<label for="schema-business">Business Name:</label>
										</th>
										<td>
											<input id="schema-business" name="schema_business" type="text" class="full-width" value="<?= Validator::old('schema_business') ?>">
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-description">Description:</label>
										</th>
										<td>
											<textarea id="schema-description" name="schema_description" class="full-width"><?= Validator::old('schema_description') ?></textarea>
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-email">E-mail:</label>
										</th>
										<td>
											<input type="text" id="schema-email" name="schema_email" class="full-width" value="<?= Validator::old('schema_email') ?>">
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-telephone">Telephone:</label>
										</th>
										<td>
											<input type="tel" id="schema-telephone" name="schema_telephone" class="full-width" value="<?= Validator::old('schema_telephone') ?>">
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-social">Social pages:</label>
										</th>
										<td>
											<textarea id="schema-social" name="schema_social" class="full-width"><?= Validator::old('schema_social') ?></textarea>
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-rating">Rating:</label>
										</th>
										<td>
											<input id="schema-rating" name="schema_rating" type="text" class="full-width" value="<?= Validator::old('schema_rating') ?>">
										</td>
									</tr>
									<tr>
										<th>
											<label for="schema-address">Address:</label>
										</th>
										<td>
											<textarea id="schema-address" name="schema_address" class="full-width"><?= Validator::old('schema_address') ?></textarea>
										</td>
									</tr>
								</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>