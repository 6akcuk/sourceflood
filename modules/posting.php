<?php

use SourceFlood\View;
use SourceFlood\Spintax;
use SourceFlood\Validator;
use SourceFlood\Models\Task;
use SourceFlood\FlashMessage;

function sourceflood_posting()
{
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$model = new Task();

	// Main posting page
	if ($action == 'index'):
		View::render('posting.index');
	// Create post page
	elseif ($action == 'create_post'):
		View::render('posting.create-post');
	elseif ($action == 'create_page'):
		View::render('posting.create-page');
	elseif ($action == 'do_create_post'):
		if (!Validator::validate($_POST, array(
			'name' => 'required',
			'title' => 'required',
			'content' => 'required',
			'post_type' => 'required|post_type'
		))) {
			wp_redirect('/wp-admin/admin.php?page=sourceflood&action=create_post');
			exit;
		}

		$name = $_POST['name'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$post_type = $_POST['post_type'];

		$data = array(
			'name' => $name,
			'content' => json_encode(array(
				'title' => $title,
				'content' => $content,
				'post_type' => $post_type
			)),
			'iteration' => 1
		);

		$project_id = $model->create($data);

		// Temp Feature
		$spintax = Spintax::parse($content);
		$iterations = Spintax::count($spintax);

		for ($i = 1; $i <= $iterations; $i++) {
			$post_id = wp_insert_post([
	            'post_title' => $title,
	            'post_name' => sanitize_title($title),
	            'post_author' => get_current_user_id(),
	            'post_content' => Spintax::make($content, $i, $spintax),
	            'post_status' => 'publish',
	            'post_type' => $post_type,
	            'comment_status' => 'closed',
	            'ping_status' => 'closed'
			]);

			add_post_meta($post_id, 'sourceflood_project_id', $project_id);
		}

		/*$post_id = wp_insert_post([
            'post_title' => $title,
            'post_name' => sanitize_title($title),
            'post_author' => get_current_user_id(),
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => $post_type,
            'comment_status' => 'closed',
            'ping_status' => 'closed'
		]);*/

		FlashMessage::success('Project successfully created. Temp: '. $iterations .' created');
		wp_redirect('/wp-admin/admin.php?page=sourceflood');
		exit;

	endif;
}