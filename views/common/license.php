<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<span>Work Horse</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content'); ?>
	<div class="LicenseWarning">
		Your license is expired. Please, buy new license.
	</div>
<?php View::endSection('content') ?>

<?php echo View::make('layouts.main') ?>