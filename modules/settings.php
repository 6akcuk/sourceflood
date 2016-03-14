<?php

use SourceFlood\View;
use SourceFlood\Validator;
use SourceFlood\FlashMessage;

function sourceflood_settings() {
	global $wpdb;

	$action = isset($_GET['action']) ? $_GET['action'] : 'index';
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
	$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
	
	if ($action == 'index'):
		View::render('settings.index');
	endif;
}