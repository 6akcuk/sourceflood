<?php
use SourceFlood\Validator;
?>

<input type="hidden" name="post_type" value="<?= $post_type ?>">

<div class="PostForm">
	<div class="PostForm__name-wrap<?php if (Validator::hasError('name')) echo ' PostForm--error' ?>">
		<input type="text" name="name" class="PostForm__name" placeholder="Project name here">
		<?php if (Validator::hasError('name')): ?>
		<span class="PostForm__error"><?= Validator::get('name') ?></span>
		<?php endif; ?>
	</div>

	<div class="PostForm__title-wrap<?php if (Validator::hasError('title')) echo ' PostForm--error' ?>">
		<input type="text" name="title" class="PostForm__title" placeholder="Enter title here">
		<?php if (Validator::hasError('title')): ?>
		<span class="PostForm__error"><?= Validator::get('title') ?></span>
		<?php endif; ?>
	</div>

	<div class="PostForm__body-wrap<?php if (Validator::hasError('content')) echo ' PostForm--error' ?>">
		<?php wp_editor('', 'content', array(
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