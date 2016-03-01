<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=sourceflood') ?>">SourceFlood</a>
	&raquo;
	<span>Projects List</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
	<h2>
		Projects List
		<a href="<?= admin_url('admin.php?page=sourceflood') ?>" class="add-new-h2">Add New</a>
	</h2>

	<form method="get">
		<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<td class="check-column"></td>
			<th>Name</th>
			<th>Current Iteration</th>
			<th>Created At</th>
			<th>Finished</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($projects as $project): ?>
			<tr>
				<td></td>
				<td class="column-title has-row-actions">
					<strong>
						<a class="row-title"><?= $project->name ?></a>
					</strong>
					<div class="row-actions">
						<span class="trash">
							<a class="submitdelete" href="<?= admin_url('admin.php?page=sourceflood_projects&action=delete&id='. $project->id .'&noheader=true') ?>">Delete project and all posts/pages</a>
						</span>
					</div>
				</td>
				<td><?= $project->iteration ?></td>
				<td>
					<?php
						$date = new DateTime($project->created_at);

						echo $date->format('d/m/Y H:i:s');
					?>
				</td>
				<td>
					
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>
	</form>
<?php View::endSection('content') ?>

<?php View::make('layouts.main') ?>