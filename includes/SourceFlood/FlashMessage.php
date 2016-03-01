<?php

namespace SourceFlood;

use SourceFlood\View;

class FlashMessage
{
	public static function success($message)
	{
		self::message($message, 'success');
	}

	public static function message($message, $type = 'success') 
	{
		$_SESSION['sourceflood.flashmessage.message'] = $message;
		$_SESSION['sourceflood.flashmessage.type'] = $type;
	}

	public static function handle() 
	{
		$message = $_SESSION['sourceflood.flashmessage.message'];
		$type = $_SESSION['sourceflood.flashmessage.type'];

		unset($_SESSION['sourceflood.flashmessage.message']);
		unset($_SESSION['sourceflood.flashmessage.type']);

		View::render('flashmessage.message', compact('message', 'type'));
	}
}