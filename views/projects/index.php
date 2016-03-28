<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=workhorse') ?>">Work Horse</a>
	&raquo;
	<span>Projects List</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
	<h2>
		Projects List
		<a href="<?= admin_url('admin.php?page=workhorse') ?>" class="add-new-h2">Add New</a>
	</h2>

	<form method="get">
		<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<td style="width: 80px"></td>
			<th>Name</th>
			<th>Current Post</th>
			<th>Max Posts</th>
			<th>Created At</th>
			<th>Last Update</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($projects as $project): ?>
			<tr<?= $highlight == $project->id ? ' class="WHProject--highlight"' : '' ?>>
				<td>
					<?php if ($project->iteration < $project->max_iterations): ?>
					<a href="/wp-admin/admin.php?page=workhorse_builder&id=<?= $project->id ?>" class="button button-primary" target="_blank">Build posts</a>
					<?php endif; ?>
				</td>
				<td class="column-title has-row-actions">
					<strong>
						<a class="row-title"><?= $project->name ?></a>
					</strong>
					<div class="row-actions">
						<span class="trash">
							<a class="submitdelete" href="<?= admin_url('admin.php?page=workhorse_projects&action=delete&id='. $project->id .'&noheader=true') ?>" onclick="return confirm('This action will delete project and all generated posts/pages')">Delete project and all posts/pages</a>
						</span>
					</div>
				</td>
				<td><?= $project->iteration ?></td>
				<td><?= $project->max_iterations ?></td>
				<td>
					<?php
						$date = new DateTime($project->created_at);

						echo $date->format('d/m/Y H:i:s');
					?>
				</td>
				<td>
					<?php
						$date = new DateTime($project->updated_at);

						echo $date->format('d/m/Y H:i:s');
					?>
				</td>
				<td>
					<strong><?= ($project->iteration >= $project->max_iterations) ? 'Finished' : 'Processing' ?></strong>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>
	</form>
<?php View::endSection('content') ?>

<?php View::make('layouts.main') ?>