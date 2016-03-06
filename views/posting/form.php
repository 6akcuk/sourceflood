<?php
use SourceFlood\Validator;
?>

<input type="hidden" name="post_type" value="<?= $post_type ?>">

<div id="poststuff" class="PostForm">
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

	<div class="PostForm__boxes">

		<!-- On-Page SEO -->
		<div class="postbox">
			<h2 class="hndle"><span>SourceFlood SEO Options</span></h2>
			<div class="inside">
				<?php
					$old_on_page_seo = Validator::old('on_page_seo', 0);
				?>
				<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="on-page-seo">Enable SourceFlood On-Page Customizer:</label>
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
					$old_local_seo_enabler = Validator::old('local_seo_enabler', 0);
				?>
				<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="local-seo-enabler">Enable SourceFlood Local SEO Feature:</label>
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
			</div>
		</div>

		<!-- DripFeed Property -->
		<div class="postbox">
			<h2 class="hndle"><span>SourceFlood Dripfeed Property</span></h2>
			<div class="inside">
				<?php
					$old_dripfeed_enabler = Validator::old('dripfeed_enabler', 0);
				?>
				<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="dripfeed-enabler">Enable SourceFlood Dripfeed Feature:</label>
						</th>
						<td>
							<input id="dripfeed-enabler" name="dripfeed_enabler" type="checkbox" value="1" <?= $old_dripfeed_enabler == 1 ? 'checked' : ''; ?>>
						</td>
					</tr>
				</tbody>
				</table>

				<div id="dripfeed-wrap" style="display: <?= $old_dripfeed_enabler == 1 ? 'block' : 'none'; ?>;">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="dripfeed-type">Dripfeed Type:</label>
							</th>
							<td>
								<?php $old_dripfeed_type = Validator::old('dripfeed_type') ?>
								<select id="dripfeed-type" name="dripfeed_type">
									<option value="per-day"<?= $old_dripfeed_type == 'per-day' ? ' selected' : '' ?>>X posts/pages per day</option>
									<option value="over-days"<?= $old_dripfeed_type == 'over-days' ? ' selected' : '' ?>>Whole project dripped over X days</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>
								<label for="dripfeed-x">X Parameter:</label>
							</th>
							<td class="<?= (Validator::hasError('dripfeed_x')) ? 'PostForm--error' : '' ?>">
								<input type="text" id="dripfeed-x" name="dripfeed_x" value="<?= Validator::old('dripfeed_x') ?>">
								<?php if (Validator::hasError('dripfeed_x')): ?>
								<span class="PostForm__error"><?= Validator::get('dripfeed_x') ?></span>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>