<?php

use SourceFlood\View;
use SourceFlood\Validator;

?>

<?php View::startSection('breadcrumbs') ?>
	<a href="<?= admin_url('admin.php?page=workhorse') ?>">Work Horse</a>
	&raquo;
	<a href="<?= admin_url('admin.php?page=workhorse_users') ?>">Users List</a>
	&raquo;
	<span>Create Users</span>
<?php View::endSection('breadcrumbs') ?>

<?php View::startSection('content') ?>
<div class="CreatePost">
	<h2>Create New Users</h2>

	<form action="/wp-admin/admin.php?page=workhorse_users&action=do_create&noheader=true" method="post">
		<table class="form-table">
	        <tr valign="top">
	            <th scope="row">Number of users to create</th>
	            <td>
	                <input type="number" name="users" style="width: 300px" value="<?= Validator::old('users') ?>" required />
	            </td>
	        </tr>
	    </table>
	    
	    <p class="howto">
	    	It will take some time to create all users.
	    </p>

	    <p class="submit">
	    	<input type="submit" class="button-primary" value="<?php _e('Create') ?>" />
	    </p>
	</form>
</div>
<?php View::endSection('content') ?>

<?php echo View::make('layouts.main') ?>