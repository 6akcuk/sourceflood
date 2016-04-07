<?php

use SourceFlood\License;

// Libraries
include_once 'autoloader.php';

// Load license status
License::checkThatLicenseIsValid();

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
include_once 'includes/settings.php';

// Features
include_once 'features/workhorsestat/workhorsestat.php';