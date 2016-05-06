<?php

use SourceFlood\View;
use SourceFlood\License;
use SourceFlood\Validator;
use SourceFlood\Models\Task;
use SourceFlood\FlashMessage;

function sourceflood_projects() {
	global $wpdb;

	if (!License::checkThatLicenseIsValid()) {
		View::render('common.license');
		return;
	}

	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
	$model = new Task();

	if ($action == 'index'):
		// Filters
		$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'created_at';
		$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

		$highlight = isset($_GET['highlight']) ? $_GET['highlight'] : null;
		
		$where = array();
		$params = array();

		$sql = 'SELECT * FROM '. $model->getTable();
		if (sizeof($where)) {
			$sql .= ' WHERE '. implode(' AND ', $where);
		}

		$sqlTotal = 'SELECT COUNT(id) AS total FROM '. $model->getTable();
		if (sizeof($where)) {
			$sqlTotal .= ' WHERE '. implode(' AND ', $where);
		}

		$sqlTotal = $wpdb->prepare($sqlTotal, $params);

		$sql .= " ORDER BY $orderBy $order";
		$sql .= " LIMIT %d, %d";

		$params[] = $offset;
		$params[] = $limit;

		$sql = $wpdb->prepare($sql, $params);

		// Data
		$projects = $wpdb->get_results($sql);
		$total_row = $wpdb->get_row($sqlTotal);
		$total = $total_row->total;
		
		View::render('projects.index', compact('projects', 'total', 'order', 'orderBy', 'highlight'));

	elseif ($action == 'delete'):

		$id = $_GET['id'];
		$task = $model->find($id);
		
		// Delete all posts from this project
		$wpdb->query($wpdb->prepare("DELETE FROM ". $wpdb->prefix ."posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'sourceflood_project_id' AND meta_value = %s)", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ". $wpdb->prefix ."postmeta WHERE meta_key = 'sourceflood_project_id' AND meta_value = %s", $id));

		$model->delete($id);

		FlashMessage::success('Project and all posts/pages deleted.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_projects');
		exit;

	elseif ($action == 'delete_posts'):

		$id = $_GET['id'];

		// Delete all posts from this project
		$wpdb->query($wpdb->prepare("DELETE FROM ". $wpdb->prefix ."posts WHERE ID IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'sourceflood_project_id' AND meta_value = %s)", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ". $wpdb->prefix ."postmeta WHERE meta_key = 'sourceflood_project_id' AND meta_value = %s", $id));

		$model->update(array('iteration' => 0), $id);

		FlashMessage::success('All posts/pages deleted.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_projects');
		exit;

	elseif ($action == 'stop'):

		$id = $_GET['id'];

		$model->update(array('deleted_at' => '1970-01-01 11:11:11'), $id);

		FlashMessage::success('Project stopped. You can continue process by clicking Build posts');
		wp_redirect('/wp-admin/admin.php?page=workhorse_projects');
		exit;

	elseif ($action == 'export_urls'):

		$id = $_GET['id'];

		@set_time_limit(0);

		$urls = "";
		$posts = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'sourceflood_project_id' AND meta_value = %s", $id));
		foreach ($posts as $post) {
			$urls .= get_permalink($post->post_id) ."\r\n";
		}

		file_put_contents("project-$id.txt", $urls);

		header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename("project-$id.txt"));
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize("project-$id.txt"));
	    readfile("project-$id.txt");
		exit;

	endif;
}