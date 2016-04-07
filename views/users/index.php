<?php
use SourceFlood\View;
?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=workhorse') ?>">Work Horse</a>
	&raquo;
	<span>Users List</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
	<h2>
		Users List
		<a href="<?= admin_url('admin.php?page=workhorse_users&action=create') ?>" class="add-new-h2">Create Users</a>
	</h2>
	
	<p>
		<span class="displaying-num"><?= $results['avail_roles']['workhorse_user'] ?> users</span>
		|
		<span class="pagination-links">
			<?= paginate_links(array(
				'total' => $pages,
				'current' => $page,
				'format' => '&paged=%#%',
				'base' => admin_url('admin.php?page=workhorse_users%_%')
			)) ?>
		</span>
	</p>
	<form method="get">
		<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<th>Name</th>
			<th>E-Mail</th>
		</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user): ?>
			<tr>
				<td class="column-title has-row-actions">
					<strong>
						<a class="row-title"><?= $user->display_name ?></a>
					</strong>
					<div class="row-actions">
						
					</div>
				</td>
				<td>
					<?= $user->user_email ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>
	</form>

	<p>
		<span class="displaying-num"><?= $results['avail_roles']['workhorse_user'] ?> users</span>
		|
		<span class="pagination-links">
			<?= paginate_links(array(
				'total' => $pages,
				'current' => $page,
				'format' => '&paged=%#%',
				'base' => admin_url('admin.php?page=workhorse_users%_%')
			)) ?>
		</span>
	</p>
<?php View::endSection('content') ?>

<?php View::make('layouts.main') ?>