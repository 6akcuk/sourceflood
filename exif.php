<?php

use SourceFlood\View;

define('SOURCEFLOOD_ROOT', dirname(__FILE__));

include_once 'autoloader.php';

View::render('exif.index');