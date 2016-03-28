<div class="wrap">

	<div class="Breadcrumbs">
		<?= SourceFlood\View::section('breadcrumbs') ?>
	</div>

	<?php
		SourceFlood\FlashMessage::handle();
	?>

	<?php
		use SourceFlood\License;
		
		if (License::$status && License::$status->expire_in && !License::$status->expired):
	?>
	<div class="LicenseNotifier">
		Your license will expire soon: <strong><?= License::$status->expire_in ?></strong>
	</div>
	<?php endif; ?>
	
	<?= SourceFlood\View::section('content') ?>
</div>