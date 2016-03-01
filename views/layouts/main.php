<div class="wrap">

	<?php if (!sourceflood_configured()): ?>
		<?php SourceFlood\View::make('sourceflood.misconfigured') ?>
	<?php endif; ?>

	<div class="Breadcrumbs">
		<?= SourceFlood\View::section('breadcrumbs') ?>
	</div>

	<?php
		SourceFlood\FlashMessage::handle();
	?>
	
	<?= SourceFlood\View::section('content') ?>
</div>