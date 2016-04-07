<?php

use SourceFlood\View;
use SourceFlood\FlashMessage;

function workhorse_noindex() {
	global $wpdb;

	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 50;
	$offset = isset($_GET['paged']) ? $_GET['paged'] * $limit - $limit : 0;
	
	if ($action == 'index'):
		$results = $wpdb->get_row("SELECT COUNT(*) AS total FROM {$wpdb->prefix}termmeta WHERE meta_key = 'workhorse_noindex_tag'");

		$tags = $wpdb->get_results("SELECT t.* FROM {$wpdb->prefix}termmeta AS m INNER JOIN {$wpdb->prefix}terms AS t ON t.term_id = m.term_id WHERE m.meta_key = 'workhorse_noindex_tag' LIMIT $offset, $limit");

		$pages = ceil($results->total / $limit);
		$page = floor($offset / $limit) + 1;

		View::render('noindex.index', compact('tags', 'results', 'page', 'pages'));
	elseif ($action == 'remove'):
		$id = (int) $_GET['id'];

		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}termmeta WHERE term_id = %s AND meta_key = 'workhorse_noindex_tag'", array($id)));

		FlashMessage::success('Noindex tag removed.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_noindex');
		exit;

	endif;
}