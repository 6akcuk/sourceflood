<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=sourceflood') ?>">SourceFlood</a>
	&raquo;
	<span>Shortcodes List</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
	<h2>
		Shortcodes List
		<a href="<?= admin_url('admin.php?page=sourceflood_shortcodes&action=create') ?>" class="add-new-h2">Add New</a>
	</h2>

	<ul class="subsubsub">
		<li class="all">
			<a href="<?= admin_url('admin.php?page=sourceflood_shortcodes&type=all') ?>" class="<?php if ($type == 'all') echo 'current' ?>">
				All 
				<span class="count">(<?= $all ?>)</span>
			</a>
			|
		</li>
		<li class="static">
			<a href="<?= admin_url('admin.php?page=sourceflood_shortcodes&type=static') ?>" class="<?php if ($type == 'static') echo 'current' ?>">
				Static 
				<span class="count">(<?= $static ?>)</span>
			</a>
			|
		</li>
		<li class="dynamic">
			<a href="<?= admin_url('admin.php?page=sourceflood_shortcodes&type=dynamic') ?>" class="<?php if ($type == 'dynamic') echo 'current' ?>">
				Dynamic 
				<span class="count">(<?= $dynamic ?>)</span>
			</a>
		</li>
	</ul>

	<form method="get">
		<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<td class="check-column"></td>
			<th>Shortcode</th>
			<th>Type</th>
			<th>Created At</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($shortcodes as $code): ?>
			<tr>
				<td></td>
				<td class="column-title has-row-actions">
					<strong>
						<a class="row-title" href="<?= admin_url('admin.php?page=sourceflood_shortcodes&action=edit&id='. $code->id) ?>"><?= $code->shortcode ?></a>
					</strong>
					<p>
						<?= $code->content ?>
					</p>
					<div class="row-actions">
						<span class="edit">
							<a href="<?= admin_url('admin.php?page=sourceflood_shortcodes&action=edit&id='. $code->id) ?>" title="Edit this item">Edit</a>
							|
						</span>
						<span class="trash">
							<a class="submitdelete" href="<?= admin_url('admin.php?page=sourceflood_shortcodes&action=delete&id='. $code->id .'&noheader=true') ?>">Trash</a>
						</span>
					</div>
				</td>
				<td><?= $code->type ?></td>
				<td>
					<?php
						$date = new DateTime($code->created_at);

						echo $date->format('d/m/Y H:i:s');
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>
	</form>
<?php View::endSection('content') ?>

<?php View::make('layouts.main') ?>