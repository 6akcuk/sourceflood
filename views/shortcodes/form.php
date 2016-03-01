<?php
use SourceFlood\Validator;
?>

<div class="BasicForm__row<?php if (Validator::hasError('shortcode')) echo ' PostForm--error' ?>">
	<input type="text" name="shortcode" placeholder="Shortcode" value="<?= isset($shortcode) ? $shortcode->shortcode : '' ?>">
	<?php if (Validator::hasError('shortcode')): ?>
	<span class="PostForm__error"><?= Validator::get('shortcode') ?></span>
	<?php endif; ?>
</div>

<?php if (!isset($shortcode)): ?>
<div class="BasicForm__row<?php if (Validator::hasError('type')) echo ' PostForm--error' ?>">
	<select name="type">
		<option value="">- Select Shortcode Type -</option>
		<option value="static">Static</option>
		<option value="dynamic">Dynamic</option>
	</select>
	<?php if (Validator::hasError('type')): ?>
	<span class="PostForm__error"><?= Validator::get('type') ?></span>
	<?php endif; ?>
</div>
<?php endif; ?>

<div class="BasicForm__row<?php if (Validator::hasError('content')) echo ' PostForm--error' ?>">
	<textarea name="content" placeholder="Content"<?php if (isset($shortcode) && $shortcode->type == 'static') echo ' disabled' ?>><?= isset($shortcode) ? $shortcode->content : '' ?></textarea>
	<?php if (Validator::hasError('content')): ?>
	<span class="PostForm__error"><?= Validator::get('content') ?></span>
	<?php endif; ?>
</div>