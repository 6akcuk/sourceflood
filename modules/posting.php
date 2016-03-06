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
			'post_type' => 'required|post_type',

			// Dripfeed
			'dripfeed_x' => 'required_if:dripfeed_enabler|numeric'
		))) {
			wp_redirect('/wp-admin/admin.php?page=sourceflood&action=create_post');
			exit;
		}

		$name = $_POST['name'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$post_type = $_POST['post_type'];

		$project_data = array(
			'title' => $title,
			'content' => $content,
			'post_type' => $post_type
		);

		$iterations = [
			// title
			Spintax::count(Spintax::parse($title)),
			// content
			Spintax::count(Spintax::parse($content))
		];

		$options_data = array();

		// On-Page SEO
		if (isset($_POST['on_page_seo'])) {
			$options_data['custom_title'] = $_POST['custom_title'];
			$options_data['custom_description'] = $_POST['custom_description'];
			$options_data['custom_keywords'] = $_POST['custom_keywords'];

			$iterations[] = Spintax::count(Spintax::parse($options_data['custom_title']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['custom_description']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['custom_keywords']));
		}

		// Local SEO
		if (isset($_POST['local_seo_enabler'])) {
			$options_data['local_geo_country'] = $_POST['local_country'];
			$options_data['local_geo_locations'] = json_decode(stripslashes($_POST['local_geo_locations']), true);
		}

		// Dripfeed Feature
		if (isset($_POST['dripfeed_enabler'])) {
			$options_data['dripfeed_type'] = $_POST['dripfeed_type'];
			$options_data['dripfeed_x'] = $_POST['dripfeed_x'];
		}

		$data = array(
			'name' => $name,
			'content' => json_encode($project_data),
			'options' => json_encode($options_data),
			'iteration' => 1,
			'spintax_iterations' => max($iterations),
			'max_iterations' => max($iterations) * sizeof($options_data['local_geo_locations'])
		);
		
		$project_id = $model->create($data);

		FlashMessage::success('Project successfully created. It will generate '. $data['max_iterations'] .' posts/pages.');
		wp_redirect('/wp-admin/admin.php?page=sourceflood');
		exit;

	endif;
}