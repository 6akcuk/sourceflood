<?php

use SourceFlood\View;
use SourceFlood\Validator;
use SourceFlood\FlashMessage;
use SourceFlood\Models\Shortcode;

add_action('init', 'sourceflood_init_shortcodes');

function sourceflood_init_shortcodes() {
	$model = new Shortcode();

	$shortcodes = $model->all();

	foreach ($shortcodes as $shortcode) {
		add_shortcode($shortcode->shortcode, 'sourceflood_handle_shortcode');
	}
}

function sourceflood_handle_shortcode($attributes, $content = null, $called = null) {
	$model = new Shortcode();

	/*var_dump($called);
	var_dump($attributes);
	exit;*/

	$shortcode = $model->getByShortcode($called);
	if (isJSON($shortcode->content)) {
		$images = json_decode($shortcode->content);
		$image = $images[array_rand($images)];

		return '<img src="'. $image->url .'" alt="'. $image->tags .'">';
	} else return $shortcode->content;
}

function sourceflood_shortcodes() {
	global $wpdb;

	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
	$model = new Shortcode();

	if ($action == 'index'):

		// Filters
		$type = isset($_GET['type']) ? $_GET['type'] : 'all';
		$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'shortcode';
		$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

		$where = array();
		$params = array();


		if ($type != 'all') {
			$params[] = $type;
			$where[] = '`type` = %s';
		}

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
		$shortcodes = $wpdb->get_results($sql);
		$total_row = $wpdb->get_row($sqlTotal);
		$total = $total_row->total;
		
		$all = $model->count();
		$static = $model->countStatic();
		$dynamic = $model->countDynamic();

		View::render('shortcodes.index', compact('shortcodes', 'total', 'all', 'static', 'dynamic', 'type', 'order', 'orderBy'));

	elseif ($action == 'create'):

		View::render('shortcodes.create');

	elseif ($action == 'do_create'):

		if (!Validator::validate($_POST, array(
			'shortcode' => 'required|unique:'. $model->getTable(),
			'type' => 'required',
			'content' => 'required'
		))) {
			wp_redirect('/wp-admin/admin.php?page=workhorse_shortcodes&action=create');
			exit;
		}

		$id = $model->create($_POST);

		FlashMessage::success('Shortcode created.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_shortcodes');
		exit;

	elseif ($action == 'edit'):

		$id = $_GET['id'];
		$shortcode = $model->find($id);

		View::render('shortcodes.edit', compact('shortcode'));

	elseif ($action == 'do_edit'):

		$id = $_GET['id'];
		$shortcode = $model->find($id);

		if (!Validator::validate($_POST, array(
			'shortcode' => 'required|unique:'. $model->getTable() .',shortcode,'. $id,
			'content' => 'if_not:dynamic,'. $shortcode->type
		))) {
			wp_redirect('/wp-admin/admin.php?page=workhorse_shortcodes&action=edit&id='. $id);
			exit;
		}

		$model->update($_POST, $id);

		FlashMessage::success('Shortcode updated.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_shortcodes&action=edit&id='. $id);
		exit;

	elseif ($action == 'delete'):

		$id = $_GET['id'];
		$model->delete($id);

		FlashMessage::success('Shortcode deleted.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_shortcodes');
		exit;
		
	endif;
}