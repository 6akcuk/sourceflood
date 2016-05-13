<?php

use SourceFlood\View;
use SourceFlood\License;
use SourceFlood\Spintax;
use SourceFlood\Validator;
use SourceFlood\LiteSpintax;
use SourceFlood\Models\Task;
use SourceFlood\FlashMessage;

function sourceflood_posting()
{
	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$model = new Task();

	if (!License::checkThatLicenseIsValid()) {
		View::render('common.license');
		return;
	}

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

			'max_posts' => 'numeric',

			// Dripfeed
			'dripfeed_x' => 'required_if:dripfeed_enabler|numeric',

			// Image EXIF
			//'exif_locations' => 'required_if:exif_enabler'

			// Channel
			'city_channel_title' => 'required_if:city_channel_page',
			'city_channel_content' => 'required_if:city_channel_page',
			'state_channel_title' => 'required_if:state_channel_page',
			'state_channel_content' => 'required_if:state_channel_page'
		))) {
			wp_redirect('/wp-admin/admin.php?page=workhorse&action=create_post');
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
		$geo_iterations = 1;

		if (isset($_POST['local_seo_enabler'])) {
			// Search tags and remove non-used locations
			$tags = workhorse_search_geotags(array(
				$title, $content, $_POST['custom_title'], $_POST['custom_description'], $_POST['custom_keywords'], $_POST['permalink'], $_POST['tags']
			));

			$options_data['local_geo_country'] = $_POST['local_country'];
			$options_data['local_geo_locations'] = json_decode(stripslashes($_POST['local_geo_locations']), true);
			$options_data['local_geo_locations'] = workhorse_expand_geodata($options_data['local_geo_country'], $options_data['local_geo_locations'], $tags);

			$geo_iterations = sizeof($options_data['local_geo_locations']);
			if ($geo_iterations == 0) $geo_iterations = 1;

			if (isset($_POST['local_randomize'])) {
				shuffle($options_data['local_geo_locations']);
			}

			// Categorization
			if (isset($_POST['enable_categorization'])) {
				$tags_order = array('country', 'state', 'city', 'zip');
				
				foreach ($tags_order as $tag) {
					if (!in_array($tag, $tags)) continue;

					$options_data['categorization'][] = $tag;
				}
			}
			
			//$options_data['local_geo_locations'] = array_unique($options_data['local_geo_locations']);
		}

		// Schema SEO
		if (isset($_POST['schema'])) {
			$options_data['schema_business'] = $_POST['schema_business'];
			$options_data['schema_description'] = $_POST['schema_description'];
			$options_data['schema_email'] = $_POST['schema_email'];
			$options_data['schema_telephone'] = $_POST['schema_telephone'];
			$options_data['schema_social'] = $_POST['schema_social'];
			$options_data['schema_rating_object'] = $_POST['schema_rating_object'];
			$options_data['schema_rating'] = $_POST['schema_rating'];
			$options_data['schema_rating_count'] = $_POST['schema_rating_count'];
			$options_data['schema_address'] = $_POST['schema_address'];

			if (isset($_POST['hide_schema'])) {
				$options_data['hide_schema'] = true;
			}

			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_business']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_description']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_email']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_social']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_address']));
			$iterations[] = Spintax::count(Spintax::parse($options_data['schema_rating_object']));
		}

		// Dripfeed Feature
		if (isset($_POST['dripfeed_enabler'])) {
			$options_data['dripfeed_type'] = $_POST['dripfeed_type'];
			$options_data['dripfeed_x'] = $_POST['dripfeed_x'];
		}

		// Image EXIF
		if (isset($_POST['exif_enabler'])) {
			$options_data['exif_locations'] = $_POST['exif_locations'];
		}
		if (isset($_POST['use_post_location'])) {
			$options_data['use_post_location'] = true;
		}

		// Permalink
		if ($_POST['permalink']) {
			$options_data['permalink'] = $_POST['permalink'];
		}
		if ($_POST['permalink_prefix']) {
			$options_data['permalink_prefix'] = sanitize_title($_POST['permalink_prefix']);
		}

		// Tags
		if ($_POST['tags']) {
			$options_data['tags'] = $_POST['tags'];
		}
		if (isset($_POST['noindex_tags'])) {
			$options_data['noindex_tags'] = true;
		}

		// Distribute
		if (isset($_POST['distribute'])) {
			$options_data['distribute'] = true;
		}

		// Channel pages
		if (isset($_POST['state_channel_page'])) {
			$project_data['state_channel_title'] = $_POST['state_channel_title'];
			$project_data['state_channel_page'] = $_POST['state_channel_content'];
		}
		if (isset($_POST['city_channel_page'])) {
			$project_data['city_channel_title'] = $_POST['city_channel_title'];
			$project_data['city_channel_page'] = $_POST['city_channel_content'];
		}

		// Math maximum number of posts
		if (isset($_POST['local_seo_enabler'])) {
			$max = ($_POST['max_posts'] <= 0) ? $geo_iterations : intval($_POST['max_posts']);
		} else {
			$max = ($_POST['max_posts'] <= 0) ? Spintax::count(Spintax::parse($title)) : intval($_POST['max_posts']);
		}

		$data = array(
			'name' => $name,
			'content' => json_encode($project_data),
			'options' => json_encode($options_data),
			'iteration' => 0,
			'spintax_iterations' => max($iterations),
			//'max_iterations' => max($iterations) * $geo_iterations
			'max_iterations' => $max
		);

		$project_id = $model->create($data);

		FlashMessage::success('Project successfully created. It will generate '. $data['max_iterations'] .' posts/pages.');
		wp_redirect('/wp-admin/admin.php?page=workhorse_projects&highlight='. $project_id);
		exit;

	endif;
}