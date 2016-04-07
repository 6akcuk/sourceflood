<?php

$_NEWSTATPRESS['version']='1.1.9';
$_NEWSTATPRESS['feedtype']='';

global $workhorsestat_dir,
			 $wpdb,
			 $whs_option_vars,
			 $whs_widget_vars;

define('whs_TEXTDOMAIN', 'workhorsestat');
define('whs_PLUGINNAME', 'Work Horse Stat');
define('whs_REQUIRED_WP_VERSION', '3.5');
define('whs_NOTICENEWS', TRUE);
define('whs_TABLENAME', $wpdb->prefix . 'workhorse_statpress');
define('whs_BASENAME', dirname(plugin_basename(__FILE__)));
define('whs_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define('whs_SERVER_NAME', whs_GetServerName() );

$workhorsestat_dir = WP_PLUGIN_DIR .'/'. whs_BASENAME;

$whs_option_vars=array( // list of option variable name, with default value associated
                        // (''=>array('name'=>'','value'=>''))
                        'overview'=>array('name'=>'workhorsestat_el_overview','value'=>'10'),
                        'top_days'=>array('name'=>'workhorsestat_el_top_days','value'=>'5'),
                        'os'=>array('name'=>'workhorsestat_el_os','value'=>'10'),
                        'browser'=>array('name'=>'workhorsestat_el_browser','value'=>'10'),
                        'feed'=>array('name'=>'workhorsestat_el_feed','value'=>'5'),
                        'searchengine'=>array('name'=>'workhorsestat_el_searchengine','value'=>'10'),
                        'search'=>array('name'=>'workhorsestat_el_search','value'=>'20'),
                        'referrer'=>array('name'=>'workhorsestat_el_referrer','value'=>'10'),
                        'languages'=>array('name'=>'workhorsestat_el_languages','value'=>'20'),
                        'spiders'=>array('name'=>'workhorsestat_el_spiders','value'=>'10'),
                        'pages'=>array('name'=>'workhorsestat_el_pages','value'=>'5'),
                        'visitors'=>array('name'=>'workhorsestat_el_visitors','value'=>'5'),
                        'daypages'=>array('name'=>'workhorsestat_el_daypages','value'=>'5'),
                        'ippages'=>array('name'=>'workhorsestat_el_ippages','value'=>'5'),
                        'ip_per_page_newspy'=>array('name'=>'workhorsestat_ip_per_page_newspy','value'=>''),
                        'visits_per_ip_newspy'=>array('name'=>'workhorsestat_visits_per_ip_newspy','value'=>''),
                        'bot_per_page_spybot'=>array('name'=>'workhorsestat_bot_per_page_spybot','value'=>''),
                        'visits_per_bot_spybot'=>array('name'=>'workhorsestat_visits_per_bot_spybot','value'=>''),
                        'autodelete'=>array('name'=>'workhorsestat_autodelete','value'=>''),
                        'autodelete_spiders'=>array('name'=>'workhorsestat_autodelete_spiders','value'=>''),
                        'daysinoverviewgraph'=>array('name'=>'workhorsestat_daysinoverviewgraph','value'=>''),
                        'ignore_users'=>array('name'=>'workhorsestat_ignore_users','value'=>''),
                        'ignore_ip'=>array('name'=>'workhorsestat_ignore_ip','value'=>''),
                        'ignore_permalink'=>array('name'=>'workhorsestat_ignore_permalink','value'=>''),
                        'updateint'=>array('name'=>'workhorsestat_updateint','value'=>''),
                        'calculation'=>array('name'=>'workhorsestat_calculation_method','value'=>'classic'),
                        'menu_cap'=>array('name'=>'workhorsestat_mincap','value'=>'read'),
                        'menuoverview_cap'=>array('name'=>'workhorsestat_menuoverview_cap','value'=>'switch_themes'),
                        'menudetails_cap'=>array('name'=>'workhorsestat_menudetails_cap','value'=>'switch_themes'),
                        'menuvisits_cap'=>array('name'=>'workhorsestat_menuvisits_cap','value'=>'switch_themes'),
                        'menusearch_cap'=>array('name'=>'workhorsestat_menusearch_cap','value'=>'switch_themes'),
                        'menuoptions_cap'=>array('name'=>'workhorsestat_menuoptions_cap','value'=>'edit_users'),
                        'menutools_cap'=>array('name'=>'workhorsestat_menutools_cap','value'=>'switch_themes'),
                        'menucredits_cap'=>array('name'=>'workhorsestat_menucredits_cap','value'=>'read'),
                        'apikey'=>array('name'=>'workhorsestat_apikey','value'=>'read'),
                        'ip2nation'=>array('name'=>'workhorsestat_ip2nation','value'=>'none'),
                        'mail_notification'=>array('name'=>'workhorsestat_mail_notification','value'=>'disabled'),
                        'mail_notification_freq'=>array('name'=>'workhorsestat_mail_notification_freq','value'=>'daily'),
                        'mail_notification_address'=>array('name'=>'workhorsestat_mail_notification_emailaddress','value'=>''),
                        'mail_notification_time'=>array('name'=>'workhorsestat_mail_notification_time','value'=>''),
                        'mail_notification_info'=>array('name'=>'workhorsestat_mail_notification_info','value'=>''),
												'mail_notification_sender'=>array('name'=>'workhorsestat_mail_notification_sender','value'=>'NewsStatPress'),
												'settings'=>array('name'=>'workhorsestat_settings','value'=>''),
												'stats_offsets'=>array('name'=>'workhorsestat_stats_offsets','value'=>'0')
                      );

$whs_widget_vars=array( // list of widget variables name, with description associated
                       array('visits',__('Today visits', 'workhorsestat')),
                       array('yvisits',__('Yesterday visits', 'workhorsestat')),
                       array('mvisits',__('Month visits', 'workhorsestat')),
                       array('wvisits',__('Week visits', 'workhorsestat')),
                       array('totalvisits',__('Total visits', 'workhorsestat')),
                       array('totalpageviews',__('Total pages view', 'workhorsestat')),
                       array('todaytotalpageviews',__('Total pages view today', 'workhorsestat')),
                       array('thistotalvisits',__('This page, total visits', 'workhorsestat')),
                       array('alltotalvisits',__('All page, total visits', 'workhorsestat')),
                       array('os',__('Visitor Operative System', 'workhorsestat')),
                       array('browser',__('Visitor Browser', 'workhorsestat')),
                       array('ip',__('Visitor IP address', 'workhorsestat')),
                       array('since',__('Date of the first hit', 'workhorsestat')),
                       array('visitorsonline',__('Counts all online visitors', 'workhorsestat')),
                       array('usersonline',__('Counts logged online visitors', 'workhorsestat')),
                       array('toppost',__('The most viewed Post', 'workhorsestat'))
                      );


/**
 * Check to update of the plugin
 * Added by cHab
 *
 *******************************/
function whs_UpdateCheck() {

  global $_NEWSTATPRESS;
  $active_version = get_option('workhorsestat_version', '0' );
  $admin_notices = get_option( 'workhorsestat_admin_notices' );

  if( !empty( $admin_notices ) )
    add_action( 'admin_notices', 'whs_AdminNotices' );

  // check version, update installation date and update notice status
  if (version_compare( $active_version, $_NEWSTATPRESS['version'], '<' )) {
    if (version_compare( $active_version, '1.1.0', '<' ))
      whs_Activation('old'); // for old installation > 14 days since nsp 1.1.4
		if(whs_NOTICENEWS) {
			global $current_user;
			$status = get_user_meta( $current_user->ID, 'workhorsestat_nag_status', TRUE );
			$status['news'] = FALSE ;
			update_user_meta( $current_user->ID, 'workhorsestat_nag_status', $status );
		}
		update_option('workhorsestat_version', $_NEWSTATPRESS['version']);
  }

  //check if is compatible with WP Version
	global $wp_version;
  if( version_compare( $wp_version, whs_REQUIRED_WP_VERSION, '<' ) ) {
    deactivate_plugins( whs_PLUGIN_BASENAME );
  $notice_text = sprintf( __( 'Plugin %s deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', 'workhorsestat' ), whs_PLUGINNAME, whs_REQUIRED_WP_VERSION );
 	$new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
  update_option( 'workhorsestat_admin_notices', $new_admin_notice );
	add_action( 'admin_notices', 'whs_AdminNotices' );

	return FALSE;
  }

	whs_CheckNagNotices();

}
add_action( 'admin_init', 'whs_UpdateCheck' );

/**
 * Check and Export if capability of user allow that
 * need here due to header change
 * Updated by cHab
 *
 ***************************************************/
function whs_checkExport() {
	global $whs_option_vars;
	global $current_user;
	get_currentuserinfo();
  if (isset($_GET['workhorsestat_action']) && $_GET['workhorsestat_action'] == 'exportnow') {
		$tools_capability=get_option('workhorsestat_menutools_cap') ;
		if(!$tools_capability) //default value
			$tools_capability=$whs_option_vars['menutools_cap']['value'];
		if ( user_can( $current_user, $tools_capability ) ) {
			require ('includes/whs_tools.php');
    	whs_ExportNow();
  	}
	}
}
add_action('init','whs_checkExport');

/**
 * Installation time update of the plugin
 * Added by cHab
 *
 *******************************/
register_activation_hook( __FILE__, 'whs_Activation' );
function whs_Activation($arg='') {
  global $whs_option_vars;
  $whs_settings = get_option($whs_option_vars['settings']['name']);
  if( empty( $whs_settings['install_time'] ) ) {
  	$whs_settings['install_time'] = time();
    if($arg='old')
      $whs_settings['install_time'] = time()-7776000;
    update_option( 'workhorsestat_settings', $whs_settings );
  }
}

/**
 * Load CSS style, languages files, extra files
 * Added by cHab
 *
 ***********************************************/
 function whs_RegisterPluginStylesAndScripts() {

   //CSS
   $style_path=plugins_url('./css/style.css', __FILE__);

   wp_register_style('WorkHorseStatStyles', $style_path);
   wp_enqueue_style('WorkHorseStatStyles');

	 $style_path2=plugins_url('./css/pikaday.css', __FILE__);

	 wp_register_style('pikaday', $style_path2);
   wp_enqueue_style('pikaday');

   wp_enqueue_style( 'WorkHorseStatStyles', get_stylesheet_uri(), array( 'dashicons' ), '1.0' );


   // JS and jQuery
   $scripts=array('idTabs'=>plugins_url('./js/jquery.idTabs.min.js', __FILE__),
									'moment'=>plugins_url('./js/moment.min.js', __FILE__),
									'pikaday'=>plugins_url('./js/pikaday.js', __FILE__),
                  'WorkHorseStatJs'=>plugins_url('./js/whs_general.js', __FILE__));
   foreach($scripts as $key=>$sc)
   {
       wp_register_script( $key, $sc );
       wp_enqueue_script( $key );
   }

 }
 add_action( 'admin_enqueue_scripts', 'whs_RegisterPluginStylesAndScripts' );


 function whs_load_textdomain() {
   load_plugin_textdomain( 'workhorsestat', false, whs_BASENAME . '/langs' );
 }
 add_action( 'plugins_loaded', 'whs_load_textdomain' );

 if (is_admin()) { //load dashboard and extra functions
   require ('includes/whs_functions-extra.php');
   require ('includes/whs_dashboard.php');

   add_action('wp_dashboard_setup', 'whs_AddDashBoardWidget' );
 }

require ('includes/whs_core.php');
/*************************************
 * Add pages for WorkHorseStat plugin *
 *************************************/
function whs_BuildPluginMenu() {

  global $whs_option_vars;
  global $current_user;
  get_currentuserinfo();

  // Fix capability if it's not defined
  // $capability=get_option('workhorsestat_mincap') ;
  // if(!$capability) //default value
    $capability=$whs_option_vars['menu_cap']['value'];

  $overview_capability=get_option('workhorsestat_menuoverview_cap') ;
  if(!$overview_capability) //default value
    $overview_capability=$whs_option_vars['menuoverview_cap']['value'];

  $details_capability=get_option('workhorsestat_menudetails_cap') ;
  if(!$details_capability) //default value
    $details_capability=$whs_option_vars['menudetails_cap']['value'];

  $visits_capability=get_option('workhorsestat_menuvisits_cap') ;
  if(!$visits_capability) //default value
    $visits_capability=$whs_option_vars['menuvisits_cap']['value'];

  $search_capability=get_option('workhorsestat_menusearch_cap') ;
  if(!$search_capability) //default value
    $search_capability=$whs_option_vars['menusearch_cap']['value'];

  $tools_capability=get_option('workhorsestat_menutools_cap') ;
  if(!$tools_capability) //default value
    $tools_capability=$whs_option_vars['menutools_cap']['value'];

  $options_capability=get_option('workhorsestat_menuoptions_cap') ;
  if(!$options_capability) //default value
    $options_capability=$whs_option_vars['menuoptions_cap']['value'];

  $credits_capability=$whs_option_vars['menucredits_cap']['value'];

  // Display menu with personalized capabilities if user IS NOT "subscriber"
  if ( user_can( $current_user, "edit_posts" ) ) {
    add_submenu_page('workhorse', 'In-House Analytics', 'In-House Analytics', 2, 'whs-main', 'whs_WorkHorseStatMainC');
    //add_menu_page('NewStatPres', 'WorkHorseStat', $capability, 'whs-main', 'whs_WorkHorseStatMainC', plugins_url('workhorsestat/images/stat.png',whs_BASENAME));
    add_submenu_page('whs-main', __('Details','workhorsestat'), __('Details','workhorsestat'), $details_capability, 'whs_details', 'whs_DisplayDetailsC');
    add_submenu_page('whs-main', __('Visits','workhorsestat'), __('Visits','workhorsestat'), $visits_capability, 'whs_visits', 'whs_DisplayVisitsPageC');
    add_submenu_page('whs-main', __('Search','workhorsestat'), __('Search','workhorsestat'), $search_capability, 'whs_search', 'whs_DatabaseSearchC');
    add_submenu_page('whs-main', __('Tools','workhorsestat'), __('Tools','workhorsestat'), $tools_capability, 'whs_tools', 'whs_DisplayToolsPageC');
    add_submenu_page('whs-main', __('Options','workhorsestat'), __('Options','workhorsestat'), $options_capability, 'whs_options', 'whs_OptionsC');
    add_submenu_page('whs-main', __('Credits','workhorsestat'), __('Credits','workhorsestat'), $credits_capability, 'whs_credits', 'whs_DisplayCreditsPageC');
  }
}
add_action('admin_menu', 'whs_BuildPluginMenu');

function whs_WorkHorseStatMainC() {
  require ('includes/whs_overview.php');
  whs_WorkHorseStatMain();
}

function whs_DisplayDetailsC() {
  require ('includes/whs_details.php');
  whs_DisplayDetails();
}

function whs_DisplayCreditsPageC() {
  require ('includes/whs_credits.php');
  whs_DisplayCreditsPage();
}

function whs_OptionsC() {
  require ('includes/whs_options.php');
  whs_Options();
}

function whs_DisplayToolsPageC() {
  require ('includes/whs_tools.php');
  whs_DisplayToolsPage();
}

function whs_DisplayVisitsPageC() {
  require ('includes/whs_visits.php');
  whs_DisplayVisitsPage();
}

function whs_DatabaseSearchC() {
  require ('includes/whs_search.php');
  whs_DatabaseSearch();
}




/**
 * Get the url of the plugin
 *
 * @return the url of the plugin
 ********************************/
function PluginUrl() {
  //Try to use WP API if possible, introduced in WP 2.6
  if (function_exists('plugins_url')) {
    return (plugins_url() .'/workhorse/features/workhorsestat');
    return trailingslashit(plugins_url(basename(dirname(__FILE__))));
  }

  //Try to find manually... can't work if wp-content was renamed or is redirected
  $path = dirname(__FILE__);
  $path = str_replace("\\","/",$path);
  $path = trailingslashit(get_bloginfo('wpurl')) . trailingslashit(substr($path,strpos($path,"wp-content/")));

  return $path;
}

function whs_GetServerName() {
	$server_name = '';
	if(		!empty( $_SERVER['HTTP_HOST'] ) )		{ $server_name = $_SERVER['HTTP_HOST']; }
	elseif(	!empty( $_NEWSTATPRESS_ENV['HTTP_HOST'] ) )		{ $server_name = $_NEWSTATPRESS_ENV['HTTP_HOST']; }
	elseif(	!empty( $_SERVER['SERVER_NAME'] ) )		{ $server_name = $_SERVER['SERVER_NAME']; }
	elseif(	!empty( $_NEWSTATPRESS_ENV['SERVER_NAME'] ) )	{ $server_name = $_NEWSTATPRESS_ENV['SERVER_NAME']; }
	return whs_CaseTrans( 'lower', $server_name );
}

/***TODO rsfb_strlen
* Convert case using multibyte version if available, if not, use defaults
***/
function whs_CaseTrans( $type, $string ) {

	switch ($type) {
		case 'upper':
			return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string, 'UTF-8' ) : strtoupper( $string );
		case 'lower':
			return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
		case 'ucfirst':
			if( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) {
				$strtmp = mb_strtoupper( mb_substr( $string, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $string, 1, NULL, 'UTF-8' );
				/* Added workaround for strange PHP bug in mb_substr() on some servers */
				return rsfb_strlen( $string ) === rsfb_strlen( $strtmp ) ? $strtmp : ucfirst( $string );
			}
			else { return ucfirst( $string ); }
		case 'ucwords':
			return function_exists( 'mb_convert_case' ) ? mb_convert_case( $string, MB_CASE_TITLE, 'UTF-8' ) : ucwords( $string );
			/***
			* Note differences in results between ucwords() and this.
			* ucwords() will capitalize first characters without altering other characters, whereas this will lowercase everything, but capitalize the first character of each word.
			* This works better for our purposes, but be aware of differences.
			***/
		default:
			return $string;
	}
}


/**
 * Calculate offset_time in second to add to epoch format
 * added by cHab
 *
 * @param $t,$tu
 * @return $offset_time
 ***********************************************************/
function whs_calculationOffsetTime($t,$tu) {

  list($current_hour, $current_minute) = explode(":", date("H:i",$t));
  list($publishing_hour, $publishing_minutes) = explode(":", $tu);

  if($current_hour>$publishing_hour)
    $plus_hour=24-$current_hour+$publishing_hour;
  else
    $plus_hour=$publishing_hour-$current_hour;

  if($current_minute>$publishing_minutes) {
    $plus_minute=60-$current_minute+$publishing_minutes;
    if($plus_hour==0)
      $plus_hour=23;
    else
      $plus_hour=$plus_hour-1;
  }
  else
    $plus_minute=$publishing_minutes-$current_minute;

  return $offset_time=$plus_hour*60*60+$plus_minute*60;
}

/**
* Parameters for workhorsestat email notification
* added by cHab
*
***************************************************/
function whs_Set_mail_content_type($content_type) {
  return 'text/html';
}

/**
 * Send an email notification with the overview statistics
 * added by cHab
 *
 * @param $arg : type of mail ('' or 'test')
 * @return $email_confirmation
 *************************************/
function whs_stat_by_email($arg='') {
  global $whs_option_vars, $support_pluginpage, $author_linkpage;
  $date = date('m/d/Y h:i:s a', time());

  add_filter('wp_mail_content_type','whs_Set_mail_content_type');

  $name=$whs_option_vars['mail_notification']['name'];
  $status=get_option($name);
  $name=$whs_option_vars['mail_notification_freq']['name'];
  $freq=get_option($name);

  $userna = get_option('workhorsestat_mail_notification_info');

  //$headers= 'From:WorkHorseStat';
  $blog_title = get_bloginfo('name');
  $subject=sprintf(__('Visits statistics from [%s]','workhorsestat'), $blog_title);
  if($arg=='test')
    $subject=sprintf(__('This is a test from [%s]','workhorsestat'), $blog_title);

  require_once ('includes/api/whs_api_dashboard.php');
  $resultH=whs_ApiDashboard("HTML");

  $name=$whs_option_vars['mail_notification_address']['name'];
  $email_address=get_option($name);

	$name=$whs_option_vars['mail_notification_sender']['name'];
	$sender=get_option($name);
	//$sender=get_option($whs_option_vars['name']);
	if($sender=='')
	 $sender=$whs_option_vars['mail_notification_sender']['value'];


	$support_pluginpage="<a href='".whs_SUPPORT_URL."' target='_blank'>".__('support page','workhorsestat')."</a>";
	$author_linkpage="<a href='".whs_PLUGIN_URL."/?page_id=2' target='_blank'>".__('the author','workhorsestat')."</a>";

	$credits_introduction=__('If you have found this plugin useful and you like it, thank you to take a moment to rate it.','workhorsestat');
	$credits_introduction.=' '.sprintf(__('You can help to the plugin development by reporting bugs on the %s or by adding/updating translation by contacting directly %s.','workhorsestat'), $support_pluginpage, $author_linkpage);
	$credits_introduction.='<br />';
	$credits_introduction.=__('WorkHorseStat is provided for free and is maintained only on free time, you can also consider a donation to support further work, directly on the plugin website or through the plugin (Credits Page).','workhorsestat');

  $warning=__('This option is yet experimental, please report bugs or improvement (see link on the bottom)','workhorsestat');
  $advising=__('You receive this email because you have enabled the statistics notification in the NewStatpress plugin (option menu) from your WP website ','workhorsestat');
  $message = __('Dear','workhorsestat')." $userna, <br /> <br />
             <i>$advising<STRONG>$blog_title</STRONG>.</i>
             <mark>$warning.</mark> <br />
             <br />".
             __('Statistics at','workhorsestat')." $date (".__('server time','workhorsestat').") from  $blog_title: <br />
             $resultH <br /> <br />"
             .__('Best Regards from','workhorsestat')." <i>WorkHorseStat Team</i>. <br />
             <br />
             <br />
             -- <br />
             $credits_introduction";
  $headers = "From: " . $sender . "<workhorsestat@altervista.org> \r\n";
  $email_confirmation = wp_mail($email_address, $subject, $message, $headers);

  remove_filter('wp_mail_content_type','whs_Set_mail_content_type');

  return $email_confirmation;
}



if ( ! wp_next_scheduled( 'whs_mail_notification' ) ) {
  $name=$whs_option_vars['mail_notification']['name'];
  $status=get_option($name);

  if ($status=='enabled') {
    $name=$whs_option_vars['mail_notification_freq']['name'];
    $freq=get_option($name);
    $name=$whs_option_vars['mail_notification_time']['name'];
    $timeuser=get_option($name);
    $crontime_offest=whs_calculationOffsetTime($t=time(),$timeuser);
    $crontime = time() + $crontime_offest ;
    if($freq=='_oneoff')
      wp_schedule_single_event( $crontime, 'whs_mail_notification' );
    else
      wp_schedule_event( $crontime, $freq, 'whs_mail_notification');
  }
}
else {
  $name=$whs_option_vars['mail_notification']['name'];
  $status=get_option($name);

  if ($status=='disabled')
     whs_mail_notification_deactivate();
  elseif ($status=='enabled') {
    if(isset($_POST['saveit']) && $_POST['saveit'] == 'yes') {
      $name=$whs_option_vars['mail_notification_freq']['name'];
      $freq=get_option($name);
      $name=$whs_option_vars['mail_notification_time']['name'];
      $timeuser=get_option($name);
      $crontime_offest=whs_calculationOffsetTime($t=time(),$timeuser);
      // $crontime_offest=0;
      $crontime = time() + $crontime_offest ;
      remove_action( 'whs_mail_notification', 'whs_stat_by_email' );
      $timestamp = wp_next_scheduled( 'whs_mail_notification' );
      wp_unschedule_event( $timestamp, 'whs_mail_notification');
      if($freq=='_oneoff')
        wp_schedule_single_event( $crontime, 'whs_mail_notification' );
      else
        wp_schedule_event( $crontime, $freq, 'whs_mail_notification');
     }
  }
}


function whs_mail_notification_deactivate() {
 wp_clear_scheduled_hook( 'whs_mail_notification' );
}

//Hook mail publi
add_action( 'whs_mail_notification', 'whs_stat_by_email' );




/**
 * Add Settings link to plugins page
 * added by cHab
 *
 */
function whs_AddSettingsLink( $links, $file ) {
	if ( $file != plugin_basename( __FILE__ ))
 		return $links;

 	$settings_link = '<a href="admin.php?page=whs_options">' . __( 'Settings', 'workhorsestat' ) . '</a>';

 	array_unshift( $links, $settings_link );

 	return $links;
 }
 add_filter( 'plugin_action_links', 'whs_AddSettingsLink',10,2);



/**TODO useful or not????
 * PHP 4 compatible mb_substr function
 * (taken in statpress-visitors)
 */
function whs_MySubstr($str, $x, $y = 0) {
	if($y == 0)
		$y = strlen($str) - $x;

	if(function_exists('mb_substr'))
		return mb_substr($str, $x, $y);
	else
		return substr($str, $x, $y);
}

/**
 * Decode the given url
 *
 * @param out_url the given url to decode
 * @return the decoded url
 ****************************************/
function whs_DecodeURL($out_url) {
  if($out_url == '') { $out_url=__('Page','workhorsestat').": Home"; }
  if(substr($out_url,0,4)=="cat=") { $out_url=__('Category','workhorsestat').": ".get_cat_name(substr($out_url,4)); }
  if(substr($out_url,0,2)=="m=") { $out_url=__('Calendar','workhorsestat').": ".substr($out_url,6,2)."/".substr($out_url,2,4); }
  if(substr($out_url,0,2)=="s=") { $out_url=__('Search','workhorsestat').": ".substr($out_url,2); }
  if(substr($out_url,0,2)=="p=") {
    $subOut=substr($out_url,2);
    $post_id_7 = get_post($subOut, ARRAY_A);
    $out_url = $post_id_7['post_title'];
  }
  if(substr($out_url,0,8)=="page_id=") {
    $subOut=substr($out_url,8);
    $post_id_7=get_page($subOut, ARRAY_A);
    $out_url = __('Page','workhorsestat').": ".$post_id_7['post_title'];
  }
  return $out_url;
}


function whs_URL() {
  $urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' );
  if ( $urlRequested == "" ) { // SEO problem!
    $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '' );
  }
  if(substr($urlRequested,0,2) == '/?') { $urlRequested=substr($urlRequested,2); }
  if($urlRequested == '/') { $urlRequested=''; }
  return $urlRequested;
}


/**
 * Convert data us to default format di Wordpress
 *
 * @param dt: date to convert
 * @return converted data
 ****************************************************/
function whs_hdate($dt = "00000000") {
  return mysql2date(get_option('date_format'), substr($dt,0,4)."-".substr($dt,4,2)."-".substr($dt,6,2));
}



function workhorsestat_hdate($dt = "00000000") {
  return mysql2date(get_option('date_format'), whs_MySubstr($dt, 0, 4) . "-" . whs_MySubstr($dt, 4, 2) . "-" . whs_MySubstr($dt, 6, 2));
}




//---------------------------------------------------------------------------
// GET DATA from visitors Functions
//---------------------------------------------------------------------------


/**TODO clean $accepted
 * Extracts the accepted language from browser headers
 */
function whs_GetLanguage($accepted){
  if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){

    // Capture up to the first delimiter (, found in Safari)
    preg_match("/([^,;]*)/", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $array_languages);

    // Fix some codes, the correct syntax is with minus (-) not underscore (_)
    return str_replace( "_", "-", strtolower( $array_languages[0] ) );
  }
  return 'xx';  // Indeterminable language
}

// function whs_GetLanguage($accepted) {
//   return substr($accepted,0,2);
// }


function whs_GetQueryPairs($url){
  $parsed_url = parse_url($url);
  $tab=parse_url($url);
  $host = $tab['host'];
  if(key_exists("query",$tab)){
    $query=$tab["query"];
    return explode("&",$query);
  } else {return null;}
}


/**
 * Get OS from the given argument
 *
 * @param arg the argument to parse for OS
 * @return the OS find in configuration file
 *******************************************/
function whs_GetOs($arg) {
  global $workhorsestat_dir;

  $arg=str_replace(" ","",$arg);
  $lines = file($workhorsestat_dir.'/def/os.dat');
  foreach($lines as $line_num => $os) {
    list($nome_os,$id_os)=explode("|",$os);
    if(strpos($arg,$id_os)===FALSE) continue;
    return $nome_os;     // fount
  }
  return '';
}

/**
 * Get Browser from the given argument
 *
 * @param arg the argument to parse for Brower
 * @return the Browser find in configuration file
 ************************************************/
function whs_GetBrowser($arg) {
  global $workhorsestat_dir;

  $arg=str_replace(" ","",$arg);
  $lines = file($workhorsestat_dir.'/def/browser.dat');
  foreach($lines as $line_num => $browser) {
    list($nome,$id)=explode("|",$browser);
    if(strpos($arg,$id)===FALSE) continue;
    return $nome;     // fount
  }
  return '';
}

/**
 * Check if the given ip is to ban
 *
 * @param arg the ip to check
 * @return '' id the address is banned
 */
function whs_CheckBanIP($arg){
  global $workhorsestat_dir;

  $lines = file($workhorsestat_dir.'/def/banips.dat');
  foreach($lines as $line_num => $banip) {
    if(strpos($arg,rtrim($banip,"\n"))===FALSE) continue;
    return ''; // this is banned
  }
  return $arg;
}

/**
 * Get the search engines
 *
 * @param refferer the url to test
 * @return the search engine present in the url
 */
function whs_GetSE($referrer = null){
  global $workhorsestat_dir;

  $key = null;
  $lines = file($workhorsestat_dir.'/def/searchengines.dat');
  foreach($lines as $line_num => $se) {
    list($nome,$url,$key)=explode("|",$se);
    if(strpos($referrer,$url)===FALSE) continue;

    # find if
    $variables = whs_GetQueryPairs(html_entity_decode($referrer));
    $i = count($variables);
    while($i--){
      $tab=explode("=",$variables[$i]);
      if($tab[0] == $key){return ($nome."|".urldecode($tab[1]));}
    }
  }
  return null;
}

/**
 * Get the spider from the given agent
 *
 * @param agent the agent string
 * @return agent the fount agent
 *************************************/
function whs_GetSpider($agent = null){
  global $workhorsestat_dir;

  $agent=str_replace(" ","",$agent);
  $key = null;
  $lines = file($workhorsestat_dir.'/def/spider.dat');
  foreach($lines as $line_num => $spider) {
    list($nome,$key)=explode("|",$spider);
    if(strpos($agent,$key)===FALSE) continue;
    # fount
    return $nome;
  }
  return null;
}

/**
 * Get the previous month in 'YYYYMM' format
 *
 * @return the previous month
 */
function whs_Lastmonth() {
  $ta = getdate(current_time('timestamp'));

  $year = $ta['year'];
  $month = $ta['mon'];

  --$month; // go back 1 month

  if( $month === 0 ): // if this month is Jan
    --$year; // go back a year
    $month = 12; // last month is Dec
  endif;

  // return in format 'YYYYMM'
  return sprintf( $year.'%02d', $month);
}

/**
 * Create or update the table
 *
 * @param action to do: update, create
 *************************************/
 function whs_BuildPluginSQLTable($action) {

   global $wpdb;
   global $wp_db_version;
   $table_name = whs_TABLENAME;
   $charset_collate = $wpdb->get_charset_collate();
   $index_list=array(array('Key_name'=>"spider_nation", 'Column_name'=>"(spider, nation)"),
                     array('Key_name'=>"ip_date", 'Column_name'=>"(ip, date)"),
                     array('Key_name'=>"agent", 'Column_name'=>"(agent)"),
                     array('Key_name'=>"search", 'Column_name'=>"(search)"),
                     array('Key_name'=>"referrer", 'Column_name'=>"(referrer)"),
                     array('Key_name'=>"feed_spider_os", 'Column_name'=>"(feed, spider, os)"),
                     array('Key_name'=>"os", 'Column_name'=>"(os)"),
                     array('Key_name'=>"date_feed_spider", 'Column_name'=>"(date, feed, spider)"),
                     array('Key_name'=>"feed_spider_browser", 'Column_name'=>"(feed, spider, browser)"),
                     array('Key_name'=>"browser", 'Column_name'=>"(browser)")
                     );
   // Add by chab
   // IF the table is already created then DROP INDEX for update
   if ($action=='')
     $action='create';

   $sql_createtable = "
    CREATE TABLE ". $table_name . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      date int(8),
      time time,
      ip varchar(39),
      urlrequested varchar(250),
      agent varchar(250),
      referrer varchar(512),
      search varchar(250),
      nation varchar(2),
      os varchar(30),
      browser varchar(32),
      searchengine varchar(16),
      spider varchar(32),
      feed varchar(8),
      user varchar(16),
      timestamp timestamp DEFAULT 0,
      UNIQUE KEY id (id)";

   if ($action=='create') {
     foreach ($index_list as $index)
     {
       $Key_name=$index['Key_name'];
       $Column_name=$index['Column_name'];
       $sql_createtable.=", INDEX $Key_name $Column_name";
     }
   }
   elseif ($action=='update') {
       foreach ($index_list as $index)
       {
         $Key_name=$index['Key_name'];
         $Column_name=$index['Column_name'];
         if ($wpdb->query("SHOW INDEXES FROM $table_name WHERE Key_name ='$Key_name'")=='') {
           $sql_createtable.=",\n INDEX $Key_name $Column_name";
         }
       }
   }
   $sql_createtable.=") $charset_collate;";


  //  echo $sql_createtable;

  if($wp_db_version >= 5540) $page = 'wp-admin/includes/upgrade.php';
  else $page = 'wp-admin/upgrade'.'-functions.php';

  require_once(ABSPATH . $page);
  dbDelta($sql_createtable);
}

/**
 * Get if this is a feed
 *
 * @param url the url to test
 * @return the kind of feed that is found
 *****************************************/
function whs_IsFeed($url) {
  if (stristr($url,get_bloginfo('rdf_url')) != FALSE) { return 'RDF'; }
  if (stristr($url,get_bloginfo('rss2_url')) != FALSE) { return 'RSS2'; }
  if (stristr($url,get_bloginfo('rss_url')) != FALSE) { return 'RSS'; }
  if (stristr($url,get_bloginfo('atom_url')) != FALSE) { return 'ATOM'; }
  if (stristr($url,get_bloginfo('comments_rss2_url')) != FALSE) { return 'COMMENT'; }
  if (stristr($url,get_bloginfo('comments_atom_url')) != FALSE) { return 'COMMENT'; }
  if (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
  if (stristr($url,'/feed/') != FALSE) { return 'RSS2'; }
  return '';
}

/**
 * Insert statistic into the database
 *
 ************************************/
function whs_StatAppend() {

  global $wpdb;
  $table_name = whs_TABLENAME;
  global $userdata;
  global $_STATPRESS;

  get_currentuserinfo();
  $feed='';

  // Time
  $timestamp  = current_time('timestamp');
  $vdate  = gmdate("Ymd",$timestamp);
  $vtime  = gmdate("H:i:s",$timestamp);
  $timestamp = date('Y-m-d H:i:s', $timestamp);

  // IP
  $ipAddress = $_SERVER['REMOTE_ADDR']; // BASIC detection -> to delete if it works
  // $ipAddress = htmlentities(whs_GetUserIP());

  // Is this IP blacklisted from file?
  if(whs_CheckBanIP($ipAddress) == '') { return ''; }

  // Is this IP blacklisted from user?
  $to_ignore = get_option('workhorsestat_ignore_ip', array());
  foreach($to_ignore as $a_ip_range){
    list ($ip_to_ignore, $mask) = @explode("/", trim($a_ip_range));
    if (empty($mask)) $mask = 32;
    $long_ip_to_ignore = ip2long($ip_to_ignore);
    $long_mask = bindec( str_pad('', $mask, '1') . str_pad('', 32-$mask, '0') );
    $long_masked_user_ip = ip2long($ipAddress) & $long_mask;
    $long_masked_ip_to_ignore = $long_ip_to_ignore & $long_mask;
    if ($long_masked_user_ip == $long_masked_ip_to_ignore) { return ''; }
  }

  if(get_option('workhorsestat_cryptip')=='checked') {
    $ipAddress = crypt($ipAddress,whs_TEXTDOMAIN);
  }

  // URL (requested)
  $urlRequested=whs_URL();
  if (preg_match("/.ico$/i", $urlRequested)) { return ''; }
  if (preg_match("/favicon.ico/i", $urlRequested)) { return ''; }
  if (preg_match("/.css$/i", $urlRequested)) { return ''; }
  if (preg_match("/.js$/i", $urlRequested)) { return ''; }
  if (stristr($urlRequested,"/wp-content/plugins") != FALSE) { return ''; }
  if (stristr($urlRequested,"/wp-content/themes") != FALSE) { return ''; }
  if (stristr($urlRequested,"/wp-admin/") != FALSE) { return ''; }
  $urlRequested=esc_sql($urlRequested);

  // Is a given permalink blacklisted?
  $to_ignore = get_option('workhorsestat_ignore_permalink', array());
    foreach($to_ignore as $a_filter){
    if (!empty($urlRequested) && strpos($urlRequested, $a_filter) === 0) { return ''; }
  }

  $referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
  $referrer=esc_sql($referrer);
  $referrer=esc_html($referrer);

  $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
  $userAgent=esc_sql($userAgent);
  $userAgent=esc_html($userAgent);

  $spider=whs_GetSpider($userAgent);

  if(($spider != '') and (get_option('workhorsestat_donotcollectspider')=='checked')) { return ''; }

  # ininitalize to empty
  $searchengine='';
  $search_phrase='';

  if($spider != '') {
    $os=''; $browser='';
  } else {
      // Trap feeds
      $feed=whs_IsFeed(get_bloginfo('url').$_SERVER['REQUEST_URI']);
      // Get OS and browser
      $os=whs_GetOs($userAgent);
      $browser=whs_GetBrowser($userAgent);

     $exp_referrer=whs_GetSE($referrer);
     if (isset($exp_referrer)) {
       list($searchengine,$search_phrase)=explode("|",$exp_referrer);
     }
    }

  // Country (ip2nation table) or language
  $countrylang="";
  if($wpdb->get_var("SHOW TABLES LIKE 'ip2nation'") == 'ip2nation') {
    $sql='SELECT *
          FROM ip2nation
          WHERE ip < INET_ATON("'.$ipAddress.'")
          ORDER BY ip DESC
          LIMIT 0,1';
    $qry = $wpdb->get_row($sql);
    $countrylang=$qry->country;
  }

  if($countrylang == '') {
    $countrylang=whs_GetLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
  }

  // Auto-delete visits if...
  if(get_option('workhorsestat_autodelete') != '') {
    $int = filter_var(get_option('workhorsestat_autodelete'), FILTER_SANITIZE_NUMBER_INT);
    # secure action
    if ($int>=1) {
      $t=gmdate('Ymd', current_time('timestamp')-86400*$int*30);

      $results =$wpdb->query( "DELETE FROM " . $table_name . " WHERE date < '" . $t . "'");
    }
  }

  // Auto-delete spiders visits if...
  if(get_option('workhorsestat_autodelete_spiders') != '') {
    $int = filter_var(get_option('workhorsestat_autodelete_spiders'), FILTER_SANITIZE_NUMBER_INT);

    # secure action
    if ($int>=1) {
      $t=gmdate('Ymd', current_time('timestamp')-86400*$int*30);

      $results =$wpdb->query(
         "DELETE FROM " . $table_name . "
          WHERE date < '" . $t . "' and
                feed='' and
                spider<>''
         ");
    }
  }

  if ((!is_user_logged_in()) OR (get_option('workhorsestat_collectloggeduser')=='checked')) {
    if (is_user_logged_in() AND (get_option('workhorsestat_collectloggeduser')=='checked')) {
      $current_user = wp_get_current_user();

      // Is a given name to ignore?
      $to_ignore = get_option('workhorsestat_ignore_users', array());
      foreach($to_ignore as $a_filter) {
        if ($current_user->user_login == $a_filter) { return ''; }
      }
    }

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      whs_BuildPluginSQLTable();
    }

    $login = $userdata ? $userdata->user_login : null;

    $insert =
      "INSERT INTO " . $table_name . "(
        date,
        time,
        ip,
        urlrequested,
        agent,
        referrer,
        search,
        nation,
        os,
        browser,
        searchengine,
        spider,
        feed,
        user,
        timestamp
       ) VALUES (
        '$vdate',
        '$vtime',
        '$ipAddress',
        '$urlRequested',
        '".addslashes(strip_tags($userAgent))."',
        '$referrer','" .
        addslashes(strip_tags($search_phrase))."',
        '".$countrylang."',
        '$os',
        '$browser',
        '$searchengine',
        '$spider',
        '$feed',
        '$login',
        '$timestamp'
       )";
    $results = $wpdb->query( $insert );
  }
}
add_action('send_headers', 'whs_StatAppend');

/**
 * Generate the Ajax code for the given variable
 *
 * @param var variable to get
 * @param limit optional limit value for query
 * @param flag optional flag value for checked
 * @param url optional url address
 ************************************************/
function whs_generateAjaxVar($var, $limit=0, $flag='', $url='') {
  global $workhorsestat_dir;

  $res = "<span id=\"".$var."\">_</span>
          <script type=\"text/javascript\">

            var xmlhttp_".$var." = new XMLHttpRequest();

            xmlhttp_".$var.".onreadystatechange = function() {
              if (xmlhttp_".$var.".readyState == 4 && xmlhttp_".$var.".status == 200) {
                document.getElementById(\"".$var."\").innerHTML=xmlhttp_".$var.".responseText;
              }
            }

            var url=\"".plugins_url(whs_TEXTDOMAIN)."/includes/api/variables.php?VAR=".$var."&LIMIT=".$limit."&FLAG=".$flag."&URL=".$url."\";

            xmlhttp_".$var.".open(\"GET\", url, true);
            xmlhttp_".$var.".send();
          </script>
         ";
  return $res;
}

/**
 * Return the expanded vars into the give code. API to use for users.
 */
function WorkHorseStat_Print($body='') {
  return whs_ExpandVarsInsideCode($body);
}


/**
 * Expand vars into the give code
 *
 * @param body the code where to look for variables to expand
 * @return the modified code
 ************************************************************/
function whs_ExpandVarsInsideCode($body) {
  global $wpdb;
  $table_name = whs_TABLENAME;

  $vars_list=array('visits',
                   'yvisits',
                   'mvisits',
                   'wvisits',
                   'totalvisits',
                   'totalpageviews',
                   'todaytotalpageviews',
                   'alltotalvisits'
                  );

  # look for $vars_list
  foreach($vars_list as $var) {
    if(strpos(strtolower($body),"%$var%") !== FALSE) {
      $body = str_replace("%$var%", whs_GenerateAjaxVar($var), $body);
    }
  }

  # look for %thistotalvisits%
  if(strpos(strtolower($body),"%thistotalvisits%") !== FALSE) {
    $body = str_replace("%thistotalvisits%", whs_GenerateAjaxVar("thistotalvisits", 0, '', whs_URL()), $body);
  }

  # look for %since%
  if(strpos(strtolower($body),"%since%") !== FALSE) {
    $qry = $wpdb->get_results(
      "SELECT date
       FROM $table_name
       ORDER BY date
       LIMIT 1;
      ");
    $body = str_replace("%since%", whs_hdate($qry[0]->date), $body);
  }

  # look for %os%
  if(strpos(strtolower($body),"%os%") !== FALSE) {
    $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    $os=whs_GetOs($userAgent);
    $body = str_replace("%os%", $os, $body);
  }

  # look for %browser%
  if(strpos(strtolower($body),"%browser%") !== FALSE) {
    $browser=whs_GetBrowser($userAgent);
    $body = str_replace("%browser%", $browser, $body);
  }

  # look for %ip%
  if(strpos(strtolower($body),"%ip%") !== FALSE) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $body = str_replace("%ip%", $ipAddress, $body);
  }

  # look for %visitorsonline%
  if(strpos(strtolower($body),"%visitorsonline%") !== FALSE) {
    $act_time = current_time('timestamp');
    $from_time = date('Y-m-d H:i:s', strtotime('-4 minutes', $act_time));
    $to_time = date('Y-m-d H:i:s', $act_time);
    $qry = $wpdb->get_results(
      "SELECT count(DISTINCT(ip)) AS visitors
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         date = '".gmdate("Ymd", $act_time)."' AND
         timestamp BETWEEN '$from_time' AND '$to_time';
      ");
    $body = str_replace("%visitorsonline%", $qry[0]->visitors, $body);
  }

  # look for %usersonline%
  if(strpos(strtolower($body),"%usersonline%") !== FALSE) {
    $act_time = current_time('timestamp');
    $from_time = date('Y-m-d H:i:s', strtotime('-4 minutes', $act_time));
    $to_time = date('Y-m-d H:i:s', $act_time);
    $qry = $wpdb->get_results(
      "SELECT count(DISTINCT(ip)) AS users
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         date = '".gmdate("Ymd", $act_time)."' AND
         user<>'' AND
         timestamp BETWEEN '$from_time' AND '$to_time';
      ");
    $body = str_replace("%usersonline%", $qry[0]->users, $body);
  }

  # look for %toppost%
  if(strpos(strtolower($body),"%toppost%") !== FALSE) {
    $qry = $wpdb->get_results(
      "SELECT urlrequested,count(*) AS totale
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         urlrequested LIKE '%p=%'
       GROUP BY urlrequested
       ORDER BY totale DESC
       LIMIT 1;
      ");
    $body = str_replace("%toppost%", whs_DecodeURL($qry[0]->urlrequested), $body);
  }

  # look for %topbrowser%
  if(strpos(strtolower($body),"%topbrowser%") !== FALSE) {
    $qry = $wpdb->get_results(
       "SELECT browser,count(*) AS totale
        FROM $table_name
        WHERE
          spider='' AND
          feed=''
        GROUP BY browser
        ORDER BY totale DESC
        LIMIT 1;
       ");
    $body = str_replace("%topbrowser%", whs_DecodeURL($qry[0]->browser), $body);
  }

  # look for %topos%
  if(strpos(strtolower($body),"%topos%") !== FALSE) {
    $qry = $wpdb->get_results(
      "SELECT os,count(*) AS totale
       FROM $table_name
       WHERE
         spider='' AND
         feed=''
       GROUP BY os
       ORDER BY totale DESC
       LIMIT 1;
      ");
    $body = str_replace("%topos%", whs_DecodeURL($qry[0]->os), $body);
  }

  # look for %topsearch%
  if(strpos(strtolower($body),"%topsearch%") !== FALSE) {
    $qry = $wpdb->get_results(
      "SELECT search, count(*) AS csearch
       FROM $table_name
       WHERE
         search<>''
       GROUP BY search
       ORDER BY csearch DESC
       LIMIT 1;
      ");
    $body = str_replace("%topsearch%", whs_DecodeURL($qry[0]->search), $body);
  }

  return $body;
}

// TODO : if working, move the contents into the caller instead of this function
/**
 * Get top posts
 *
 * @param limit: the number of post to show
 * @param showcounts: if checked show totals
 * @return result of extraction
 *******************************************/
function whs_TopPosts($limit=5, $showcounts='checked') {
  return whs_GenerateAjaxVar("widget_topposts", $limit, $showcounts);
}


/**
 * Build NewsStatPress Widgets: Stat and TopPosts
 *
 ************************************************/
function whs_WidgetInit($args) {
  if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') ) return;

  // Statistics Widget control
  function whs_WidgetStats_control() {
    global $whs_widget_vars;
    $options = get_option('widget_workhorsestat');
    if ( !is_array($options) ) $options = array('title'=>'WorkHorseStat Stats', 'body'=>'Visits today: %visits%');
    if ( isset($_POST['workhorsestat-submit']) && $_POST['workhorsestat-submit'] ) {
      $options['title'] = strip_tags(stripslashes($_POST['workhorsestat-title']));
      $options['body'] = stripslashes($_POST['workhorsestat-body']);
      update_option('widget_workhorsestat', $options);
    }
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $body = htmlspecialchars($options['body'], ENT_QUOTES);

     // the form
    echo "<p>
            <label for='workhorsestat-title'>". __('Title:', 'workhorsestat') ."</label>
            <input class='widget-title' id='workhorsestat-title' name='workhorsestat-title' type='text' value='$title' />
          </p>
          <p>
            <label for='workhorsestat-body'>". _e('Body:', 'workhorsestat') ."</label>
            <textarea class='widget-body' id='workhorsestat-body' name='workhorsestat-body' type='textarea' placeholder='Example: Month visits: %mvisits%...'>$body</textarea>
          </p>
          <input type='hidden' id='workhorsestat-submit' name='workhorsestat-submit' value='1' />
          <p>". __('Stats available: ', 'workhorsestat') ."<br/ >
          <span class='widget_varslist'>";
          foreach($whs_widget_vars as $var) {
              echo "<a href='#'>%$var[0]%  <span>"; _e($var[1], 'workhorsestat'); echo "</span></a> | ";
          }
    echo "</span></p>";
  }

  function whs_WidgetStats($args) {
    extract($args);
    $options = get_option('widget_workhorsestat');
    $title = $options['title'];
    $body = $options['body'];
    echo $before_widget;
    print($before_title . $title . $after_title);
    print whs_ExpandVarsInsideCode($body);
    echo $after_widget;
  }
  wp_register_sidebar_widget('WorkHorseStat', 'WorkHorseStat Stats', 'whs_WidgetStats');
  wp_register_widget_control('WorkHorseStat', array('WorkHorseStat','widgets'), 'whs_WidgetStats_control', 300, 210);

  // Top posts Widget control
  function whs_WidgetTopPosts_control() {
    $options = get_option('widget_workhorsestattopposts');
    if ( !is_array($options) ) {
      $options = array('title'=>'WorkHorseStat TopPosts', 'howmany'=>'5', 'showcounts'=>'checked');
    }
    if ( isset($_POST['workhorsestattopposts-submit']) && $_POST['workhorsestattopposts-submit'] ) {
      $options['title'] = strip_tags(stripslashes($_POST['workhorsestattopposts-title']));
      $options['howmany'] = stripslashes($_POST['workhorsestattopposts-howmany']);
      $options['showcounts'] = stripslashes($_POST['workhorsestattopposts-showcounts']);
      if($options['showcounts'] == "1") {
        $options['showcounts']='checked';
      }
      update_option('widget_workhorsestattopposts', $options);
    }
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
    $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
    // the form
    echo "<p style='text-align:right;'>
            <label for='workhorsestattopposts-title'>". __('Title','workhorsestat') . "
            <input style='width: 250px;' id='workhorsestat-title' name='workhorsestattopposts-title' type='text' value=$title />
            </label>
          </p>
          <p style='text-align:right;'>
            <label for='workhorsestattopposts-howmany'>". __('Limit results to','workhorsestat') ."
            <input style='width: 100px;' id='workhorsestattopposts-howmany' name='workhorsestattopposts-howmany' type='text' value=$howmany />
            </label>
          </p>";
    echo '<p style="text-align:right;"><label for="workhorsestattopposts-showcounts">' . __('Visits','workhorsestat') . ' <input id="workhorsestattopposts-showcounts" name="workhorsestattopposts-showcounts" type=checkbox value="checked" '.$showcounts.' /></label></p>';
    echo '<input type="hidden" id="workhorsestat-submitTopPosts" name="workhorsestattopposts-submit" value="1" />';
  }
  function whs_WidgetTopPosts($args) {
    extract($args);
    $options = get_option('widget_workhorsestattopposts');
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
    $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
    echo $before_widget;
    print($before_title . $title . $after_title);
    print whs_TopPosts($howmany,$showcounts);
    echo $after_widget;
  }
  wp_register_sidebar_widget('WorkHorseStat TopPosts', 'WorkHorseStat TopPosts', 'whs_WidgetTopPosts');
  wp_register_widget_control('WorkHorseStat TopPosts', array('WorkHorseStat TopPosts','widgets'), 'whs_WidgetTopPosts_control', 300, 110);
}
add_action('plugins_loaded', 'whs_WidgetInit');


function whs_CalculateVariation($month,$lmonth) {

  $target = round($month / (
    (date("d", current_time('timestamp')) - 1 +
    (date("H", current_time('timestamp')) +
    (date("i", current_time('timestamp')) + 1)/ 60.0) / 24.0)) * date("t", current_time('timestamp'))
  );

  $monthchange = null;
  $added = null;

  if($lmonth <> 0) {
    $percent_change = round( 100 * ($month / $lmonth ) - 100,1);
    $percent_target = round( 100 * ($target / $lmonth ) - 100,1);

    if($percent_change >= 0) {
      $percent_change=sprintf("+%'04.1f", $percent_target);
      $monthchange = "<td class='coll'><code style='color:green'>($percent_change%)</code></td>";
    }
    else {
      $percent_change=sprintf("%'05.1f", $percent_change);
      $monthchange = "<td class='coll'><code style='color:red'>($percent_change%)</code></td>";
    }

    if($percent_target >= 0) {
      $percent_target=sprintf("+%'04.1f", $percent_target);
      $added = "<td class='coll'><code style='color:green'>($percent_target%)</code></td>";
    }
    else {
      $percent_target=sprintf("%'05.1f", $percent_target);
      $added = "<td class='coll'><code style='color:red'>($percent_target%)</code></td>";
    }
  }
  else {
    $monthchange = "<td></td>";
    $added = "<td class='coll'></td>";
  }

  $calculated_result=array($monthchange,$target,$added);
  return $calculated_result;
}

function whs_MakeOverview($print ='dashboard') {

  global $wpdb, $whs_option_vars;
  $table_name = whs_TABLENAME;

  $overview_table='';
	global $whs_option_vars;
	$offsets = get_option($whs_option_vars['stats_offsets']['name']);
	//$offsets['alltotalvisits']=1000;
  // $since = WorkHorseStat_Print('%since%');
  $since = whs_ExpandVarsInsideCode('%since%');
  $lastmonth = whs_Lastmonth();
  $thisyear = gmdate('Y', current_time('timestamp'));
  $thismonth = gmdate('Ym', current_time('timestamp'));
  $yesterday = gmdate('Ymd', current_time('timestamp')-86400);
  $today = gmdate('Ymd', current_time('timestamp'));
  $tlm[0]=substr($lastmonth,0,4); $tlm[1]=substr($lastmonth,4,2);

  $thisyearHeader = gmdate('Y', current_time('timestamp'));
  $lastmonthHeader = gmdate('M, Y',gmmktime(0,0,0,$tlm[1],1,$tlm[0]));
  $thismonthHeader = gmdate('M, Y', current_time('timestamp'));
  $yesterdayHeader = gmdate('d M', current_time('timestamp')-86400);
  $todayHeader = gmdate('d M', current_time('timestamp'));

  // build head table overview
  if ($print=='main') {
    $overview_table.="<div class='wrap'><h2>". __('Overview','workhorsestat'). "</h2>";
    $overview_table.="<table class='widefat center nsp'>
              <thead>
              <tr class='sup'>
                <th></th>
                <th>". __('Total since','workhorsestat'). "</th>
                <th scope='col'>". __('This year','workhorsestat'). "</th>
                <th scope='col'>". __('Last month','workhorsestat'). "</th>
                <th scope='col' colspan='2'>". __('This month','workhorsestat'). "</th>
                <th scope='col' colspan='2'>". __('Target This month','workhorsestat'). "</th>
                <th scope='col'>". __('Yesterday','workhorsestat'). "</th>
                <th scope='col'>". __('Today','workhorsestat'). "</th>
              </tr>
              <tr class='inf'>
                <th></th>
                <th><span>$since</span></th>
                <th><span>$thisyearHeader</span></th>
                <th><span>$lastmonthHeader</span></th>
                <th colspan='2'><span > $thismonthHeader </span></th>
                <th colspan='2'><span > $thismonthHeader </span></th>
                <th><span>$yesterdayHeader</span></th>
                <th><span>$todayHeader</span></th>
              </tr></thead>
              <tbody class='overview-list'>";
  }
  elseif ($print=='dashboard') {
   $overview_table.="<table class='widefat center nsp'>
                      <thead>
                      <tr class='sup dashboard'>
                      <th></th>
                          <th scope='col'>". __('M-1','workhorsestat'). "</th>
                          <th scope='col' colspan='2'>". __('M','workhorsestat'). "</th>
                          <th scope='col'>". __('Y','workhorsestat'). "</th>
                          <th scope='col'>". __('T','workhorsestat'). "</th>
                      </tr>
                      <tr class='inf dashboard'>
                      <th></th>
                          <th><span>$lastmonthHeader</span></th>
                          <th colspan='2'><span > $thismonthHeader </span></th>
                          <th><span>$yesterdayHeader</span></th>
                          <th><span>$todayHeader</span></th>
                      </tr></thead>
                      <tbody class='overview-list'>";
  }

  // build body table overview
  $overview_rows=array('visitors','visitors_feeds','pageview','feeds','spiders');

  foreach ($overview_rows as $row) {

    switch($row) {

      case 'visitors' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors','workhorsestat');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
      break;

      case 'visitors_feeds' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors through Feeds','workhorsestat');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider='' AND agent<>''";
        break;

      case 'pageview' :
        $row2='date';
        $row_title=__('Pageviews','workhorsestat');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
      break;

      case 'spiders' :
        $row2='date';
        $row_title=__('Spiders','workhorsestat');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider<>''";
      break;

      case 'feeds' :
        $row2='date';
        $row_title=__('Pageviews through Feeds','workhorsestat');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider=''";
      break;
    }

    // query requests
    $qry_total = $wpdb->get_row($sql_QueryTotal);
    $qry_tyear = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$thisyear%'");


    if (get_option($whs_option_vars['calculation']['name'])=='sum') {

      // alternative calculation by mouth: sum of unique visitors of each day
      $tot=0;
      $t = getdate(current_time('timestamp'));
      $year = $t['year'];
      $month = sprintf('%02d', $t['mon']);
      $day= $t['mday'];
      $totlm=0;

      for($k=$t['mon'];$k>0;$k--)
      {
        //current month

      }
      for($i=0;$i<$day;$i++)
      {
        $qry_daylmonth = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$lastmonth$i%'");
        $qry_day=$wpdb->get_row($sql_QueryTotal. " AND date LIKE '$year$month$i%'");
        $tot+=$qry_day->$row;
        $totlm+=$qry_daylmonth->$row;

      }
      // echo $totlm." ,";
      $qry_tmonth->$row=$tot;
      $qry_lmonth->$row=$totlm;

    }
    else { // classic
      $qry_tmonth = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$thismonth%'");
      $qry_lmonth = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$lastmonth%'");
    }


    $qry_y = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$yesterday'");
    $qry_t = $wpdb->get_row($sql_QueryTotal. " AND date LIKE '$today'");

    $calculated_result=whs_CalculateVariation($qry_tmonth->$row, $qry_lmonth->$row);

			switch($row) {

				case 'visitors' :
												$qry_total->$row=$qry_total->$row+$offsets['alltotalvisits'];
												break;
				case 'visitors_feeds' :
												$qry_total->$row=$qry_total->$row+$offsets['visitorsfeeds'];
												break;
				case 'pageview' :
												$qry_total->$row=$qry_total->$row+$offsets['pageviews'];
												break;

				case 'spiders' :
												$qry_total->$row=$qry_total->$row+$offsets['spy'];
												break;
				case 'feeds' :
												$qry_total->$row=$qry_total->$row+$offsets['pageviewfeeds'];
												break;
			}

    // build full current row
    $overview_table.="<tr><td class='row_title $row'>$row_title</td>";
    if ($print=='main')
      	$overview_table.="<td class='colc'>".$qry_total->$row."</td>\n";
    if ($print=='main')
      $overview_table.="<td class='colc'>".$qry_tyear->$row."</td>\n";
    $overview_table.="<td class='colc'>".$qry_lmonth->$row."</td>\n";
    $overview_table.="<td class='colr'>".$qry_tmonth->$row. $calculated_result[0] ."</td>\n";
    if ($print=='main')
      $overview_table.="<td class='colr'> $calculated_result[1] $calculated_result[2] </td>\n";
    $overview_table.="<td class='colc'>".$qry_y->$row."</td>\n";
    $overview_table.="<td class='colc'>".$qry_t->$row."</td>\n";
    $overview_table.="</tr>";
  }

  if ($print=='dashboard'){
    $overview_table.="</tr></table>";
  }

  if ($print=='main'){
    $overview_table.= "</tr></table>\n";

    // print graph
    //  last "N" days graph  NEW
    $gdays=get_option('workhorsestat_daysinoverviewgraph'); if($gdays == 0) { $gdays=20; }
    $start_of_week = get_option('start_of_week');

    $maxxday = 0;
    for($gg=$gdays-1;$gg>=0;$gg--) {

      $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

      $qry_visitors  = $wpdb->get_row("SELECT count(DISTINCT ip) AS total FROM $table_name WHERE feed='' AND spider='' AND date = '$date'");
      $visitors[$gg] = $qry_visitors->total;

      $qry_pageviews = $wpdb->get_row("SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider='' AND date = '$date'");
      $pageviews[$gg]= $qry_pageviews->total;

      $qry_spiders   = $wpdb->get_row("SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider<>'' AND date = '$date'");
      $spiders[$gg]  = $qry_spiders->total;

      $qry_feeds     = $wpdb->get_row("SELECT count(date) AS total FROM $table_name WHERE feed<>'' AND spider='' AND date = '$date'");
      $feeds[$gg]    = $qry_feeds->total;

      $total= $visitors[$gg] + $pageviews[$gg] + $spiders[$gg] + $feeds[$gg];
      if ($total > $maxxday) $maxxday= $total;
    }

    if($maxxday == 0) { $maxxday = 1; }
    # Y
    $gd=(90/$gdays).'%';

    $overview_graph="<table class='graph'><tr>";

    for($gg=$gdays-1;$gg>=0;$gg--) {

      $scale_factor=2; //2 : 200px in CSS

      $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

      $px_visitors = $scale_factor*(round($visitors[ $gg]*100/$maxxday));
      $px_pageviews= $scale_factor*(round($pageviews[$gg]*100/$maxxday));
      $px_spiders  = $scale_factor*(round($spiders[  $gg]*100/$maxxday));
      $px_feeds    = $scale_factor*(round($feeds[    $gg]*100/$maxxday));

      $px_white = $scale_factor*100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;

      $overview_graph.="<td width='$gd' valign='bottom'>";

      $overview_graph.="<div class='overview-graph'>
        <div style='border-left:1px; background:#ffffff;width:100%;height:".$px_white."px;'></div>
        <div class='visitors_bar' style='height:".$px_visitors."px;' title='".$visitors[$gg]." ".__('Visitors','workhorsestat')."'></div>
        <div class='web_bar' style='height:".$px_pageviews."px;' title='".$pageviews[$gg]." ".__('Pageviews','workhorsestat')."'></div>
        <div class='spiders_bar' style='height:".$px_spiders."px;' title='".$spiders[$gg]." ".__('Spiders','workhorsestat')."'></div>
        <div class='feeds_bar' style='height:".$px_feeds."px;' title='".$feeds[$gg]." ".__('Feeds','workhorsestat')."'></div>
        <div style='background:gray;width:100%;height:1px;'></div>";
        if($start_of_week == gmdate('w',current_time('timestamp')-86400*$gg)) $overview_graph.="<div class='legend-W'>";
        else $overview_graph.="<div class='legend'>";
        $overview_graph.=gmdate('d', current_time('timestamp')-86400*$gg) . ' ' . gmdate('M', current_time('timestamp')-86400*$gg) .     "</div></div></td>\n";
    }
    $overview_graph.="</tr></table></div>";

    $overview_table=$overview_table.$overview_graph;
  }

  if ($print!=FALSE) print $overview_table;
  else return $overview_table;
}