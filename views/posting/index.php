<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<span>SourceFlood</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content'); ?>
<div class="Posting">
	<h1 class="Posting__header">Welcome to Source Flood!</h1>
	<h3 class="Posting__subheader">What would you like to create?</h3>

	<div class="Posting__buttons clearfix"> 
		<a href="/wp-admin/admin.php?page=sourceflood&action=create_post" class="button button-primary Posting__post-button">Post</a>
		<a href="/wp-admin/admin.php?page=sourceflood&action=create_page" class="button button-primary Posting__page-button">Page</a>
	</div>
</div>
<?php View::endSection('content') ?>

<?php echo View::make('layouts.main') ?>