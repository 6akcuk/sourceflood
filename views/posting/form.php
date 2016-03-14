<?php
use SourceFlood\Validator;

wp_enqueue_script('post');
?>

<input type="hidden" name="post_type" value="<?= $post_type ?>">

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
				<input type="text" name="title" class="PostForm__title" placeholder="Enter title here" value="<?= Validator::old('title') ?>">
				<?php if (Validator::hasError('title')): ?>
				<span class="PostForm__error"><?= Validator::get('title') ?></span>
				<?php endif; ?>
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
				<button class="button button-primary">Submit</button>
			</div>
		</div>

		<div id="postbox-container-1" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
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

				<!-- Images Scraper -->
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="toggle-indicator" aria-hidden="true"></span>
					</button>
					<h2 class="hndle ui-sortable-handle"><span>Work Horse Images Scraper</span></h2>
					<div class="inside">
						<p>
							<?php
								$pixabay_key = get_option('sourceflood_pixabay_key');
								$old_local_seo_enabler = Validator::old('local_seo_enabler', 0);

								if (!empty($pixabay_key)):
							?>
							<input type="hidden" id="pixabay-api-key" value="<?= $pixabay_key ?>">
							<a href="/wp-content/plugins/workhorse/imagescraper.php" title="Image Scraper" onclick="return ImageScraper.start(this)">Launch Images Scraper</a>
								<p>
									<label for="exif-enabler" class="selectit">
										<input id="exif-enabler" name="exif_enabler" type="checkbox" value="1" <?= Validator::old('exif_enabler', 0) == 1 ? 'checked' : ''; ?> <?= $old_local_seo_enabler == 0 ? 'disabled' : '' ?>>
										Enable Image EXIF
									</label>
								</p>
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