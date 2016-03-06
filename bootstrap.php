<?php

// Libraries
include_once 'includes/SourceFlood/Autoloader.php';

new SourceFlood\Autoloader();

session_start();

// Other parts
include_once 'includes/installer.php';
include_once 'includes/api.php';

// Core parts
include_once 'includes/assets.php';
include_once 'includes/crons.php';
include_once 'includes/filters.php';
include_once 'includes/functions.php';
include_once 'includes/modules.php';
include_once 'includes/menus.php';
include_once 'includes/seo.php';