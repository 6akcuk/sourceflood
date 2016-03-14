<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=workhorse') ?>">Work Horse</a>
	&raquo;
	<a href="<?= admin_url('admin.php?page=workhorse_shortcodes') ?>">Shortcodes List</a>
	&raquo;
	<span>Edit Shortcode</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
	<form action="<?= admin_url('admin.php?page=workhorse_shortcodes&action=do_edit&id='. $shortcode->id .'&noheader=true') ?>" method="post">
		<?php View::render('shortcodes.form', compact('shortcode')) ?>

		<div class="Posting__buttons">
			<button class="button button-primary">Save</button>
		</div>
	</form>
<?php View::endSection('content') ?>

<?php View::make('layouts.main') ?>