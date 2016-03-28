<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<span>Work Horse</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content'); ?>
<div class="Posting">
	<h1 class="Posting__header">All posts/pages was generated!</h1>
</div>
<?php View::endSection('content') ?>

<?php echo View::make('layouts.main') ?>