<?php

/**
 * Get valide user IP even behind proxy or load balancer (Could be fake)
 * added by cHab
 *
 * @return $user_IP
 */
function whs_GetUserIP() {
  $user_IP = "";
  $ip_pattern = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
	$http_headers = array('HTTP_X_REAL_IP',
                        'HTTP_X_CLIENT',
                        'HTTP_X_FORWARDED_FOR',
                        'HTTP_CLIENT_IP',
                        'REMOTE_ADDR'
                      );

  foreach($http_headers as $header) {
    if ( isset($_SERVER[$header]) ) {
      if (function_exists('filter_var') && filter_var($_SERVER[$header], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE) !== false ) {
          $user_IP = $_SERVER[$header];
          break;
      }
      else { // for php version < 5.2.0
        if(preg_match($ip_pattern,$_SERVER[$header])) {
          $user_IP = $_SERVER[$header];
          break;
        }
      }
    }
  }

	return $user_IP;
}

function whs_ConnexionIsSSL() {
	if( !empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) { return TRUE; }
	if( !empty( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && 'off' !== $_SERVER['HTTP_X_FORWARDED_SSL'] ) { return TRUE; }
	return FALSE;
}
//---------------------------------------------------------------------------
// CRON Functions
//---------------------------------------------------------------------------

/**
 * Add Cron intervals : 4 times/day, Once/week, Once/mounth
 * added by cHab
 *
 * @param $schedules
 * @return $schedules
 */
function whs_CronIntervals($schedules) {
  $schedules['fourlybyday'] = array(
   'interval' => 21600, // seconds
   'display' => __('Four time by Day','newstatpress')
  );
  $schedules['weekly'] = array(
   'interval' => 604800,
   'display' => __('Once a Week','newstatpress')
  );
  $schedules['monthly'] = array(
   'interval' => 2635200,
   'display' => __('Once a Month','newstatpress')
  );
  return $schedules;
}
add_filter( 'cron_schedules', 'whs_CronIntervals');





//---------------------------------------------------------------------------
// NOTICE Functions
//---------------------------------------------------------------------------
function whs_CalculateEpochOffsetTime( $t1, $t2, $output_unit ) { //to complete with more output_unit
  $offset_time_in_seconds = abs($t1-$t2);

  if($output_unit=='day')
    $offset_time=$offset_time_in_seconds/86400;
  if($output_unit=='hour')
      $offset_time=$offset_time_in_seconds/3600;
  else {
    $offset_time=$offset_time_in_seconds;
  }

  return $offset_time;
}

function whs_GetDaysInstalled() {
  global $whs_option_vars;
  $name=$whs_option_vars['settings']['name'];
  $settings=get_option($name);
	$install_date	= empty( $settings['install_date'] ) ? time() : $settings['install_date'];
	$num_days_inst	= whs_CalculateEpochOffsetTime($install_date, time(), 'day');
  if( $num_days_inst < 1 )
    $num_days_inst = 1;

	return $num_days_inst;
}

//---------------------------------------------------------------------------
// URL Functions
//---------------------------------------------------------------------------

/**
 * Extract the feed from the given url
 *
 * @param the url to parse
 * @return the extracted url
 *************************************/
function whs_ExtractFeedFromUrl($url) {
  list($null,$q)=explode("?",$url);

  if (strpos($q, "&")!== false)
    list($res,$null)=explode("&",$q);
  else
    $res=$q;

  return $res;
}

function whs_GetUrl() {
	$url  = whs_ConnexionIsSSL() ? 'https://' : 'http://';
  //$url = 'http://';
	$url .= whs_SERVER_NAME.$_SERVER['REQUEST_URI'];
	return $url;
}

/**
* Fix poorly formed URLs so as not to throw errors or cause problems
*
* @return $url
*/
function whs_FixUrl( $url, $rem_frag = FALSE, $rem_query = FALSE, $rev = FALSE ) {
	$url = trim( $url );
	/* Too many forward slashes or colons after http */
	$url = preg_replace( "~^(https?)\:+/+~i", "$1://", $url );
	/* Too many dots */
	$url = preg_replace( "~\.+~i", ".", $url );
	/* Too many slashes after the domain */
	$url = preg_replace( "~([a-z0-9]+)/+([a-z0-9]+)~i", "$1/$2", $url );
	/* Remove fragments */
	if( !empty( $rem_frag ) && strpos( $url, '#' ) !== FALSE ) { $url_arr = explode( '#', $url ); $url = $url_arr[0]; }
	/* Remove query string completely */
	if( !empty( $rem_query ) && strpos( $url, '?' ) !== FALSE ) { $url_arr = explode( '?', $url ); $url = $url_arr[0]; }
	/* Reverse */
	if( !empty( $rev ) ) { $url = strrev($url); }
	return $url;
}

/***
* Get query string array from URL
***/
function whs_GetQueryArgs( $url ) {
	if( empty( $url ) ) { return array(); }
	$query_str = whs_GetQueryString( $url );
	parse_str( $query_str, $args );
	return $args;
}

function whs_GetQueryString( $url ) {
	/***
	* Get query string from URL
	* Filter URLs with nothing after http
	***/
	if( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = whs_FixUrl( $url );
	/* NOW start parsing */
	$parsed = @parse_url($url);
	/* Filter URLs with no query string */
	if( empty( $parsed['query'] ) ) { return ''; }
	$query_str = $parsed['query'];
	return $query_str;
}

function whs_AdminNagNotices() {
	global $current_user;
	$nag_notices = get_user_meta( $current_user->ID, 'newstatpress_nag_notices', TRUE );
	if( !empty( $nag_notices ) ) {
		$nid			= $nag_notices['nid'];
		$style		= $nag_notices['style']; /* 'error'  or 'updated' */
		$timenow	= time();
		$url			= whs_GetUrl();
		$query_args		= whs_GetQueryArgs( $url );
		$query_str		= '?' . http_build_query( array_merge( $query_args, array( 'newstatpress_hide_nag' => '1', 'nid' => $nid ) ) );
		$query_str_con	= 'QUERYSTRING';
		$notice			= str_replace( array( $query_str_con ), array( $query_str ), $nag_notices['notice'] );
		// echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
    ?>
      <div id="nspnotice" class="<?php echo $style; ?>" style="padding:10px">
        <?php if ($nid=="n03") {
        echo "<a id=\"close\" class=\"close\" href=\"$query_str\" target=\"_self\" rel=\"external\"><span class=\"dashicons dashicons-no\"></span>close</a>";
        echo '<h4>'.__('WorkHorseStat News','newstatpress').'</h4>';
        }
        ?>
        <!-- <a  id="close" class="close"><span class="dashicons dashicons-no"></span>close</a> -->
        <p><?php echo $notice ?></p>
      </div>
    <?php
	}
}

function whs_CheckNagNotices() {

  return;
	global $current_user;
	$status	= get_user_meta( $current_user->ID, 'newstatpress_nag_status', TRUE );
	if( !empty( $status['currentnag'] ) ) { add_action( 'admin_notices', 'whs_AdminNagNotices' ); return; }
	if( !is_array( $status ) ) { $status = array(); update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status ); }
	$timenow		= time();
	$num_days_inst	= whs_GetDaysInstalled();
  $votedate=14;
  $donatedate=90;
  $num_days_inst=95; //debug
	$query_str_con	= 'QUERYSTRING';
	/* Notices (Positive Nags) */
  if( empty( $status['news'] ) ) {
    $nid = 'n03';
    $style = 'notice';
    $notice_text=__('In addition of some fixes and optimizations, several new options in this version:','newstatpress');
    $notice_text.="<ul class=\"news\">";
    $notice_text.="<li>".__('Offsets statistics option (see Option Page>General).','newstatpress')."</li>";
    $notice_text.="<li>".__('Sender option for statistics email notification (see Option Page>Email Notification).','newstatpress')."</li>";
    $notice_text.="<li>".__('Picker date and new options for export tool (see Tools Page>Export).','newstatpress')."</li>";
    $notice_text.="</ul>";
    $notice_text.="<i>".__('A big thank you from the team for all those who took the time to evaluate the plugin and those who have supported our work with their donations.','newstatpress')."</i>";
    $status['currentnag'] = TRUE;
    $status['news'] = FALSE;
  }


	if( empty( $status['currentnag'] ) && ( empty( $status['lastnag'] ) || $status['lastnag'] <= $timenow - 1209600 ) ) {
		if( empty( $status['vote'] ) && $num_days_inst >= $votedate ) {
			$nid = 'n01';
      $style = 'notice';

      $notice_text = '<p>'. __( 'It looks like you\'ve been using WorkHorseStat for a while now. That\'s great!', 'newstatpress' ).'</p>';
      $notice_text.= '<p>'. __( 'If you find this plugin useful, would you take a moment to give it a rating on WordPress.org?', 'newstatpress' );
      $notice_text.='<br /><i> ('.__( 'NB: please open a ticket on the support page instead of adding it to your rating commentaries if you wish to report an issue with the plugin, it will be processed more quickly by the team.', 'newstatpress' ).')</i></p>';
      $notice_text.= '<a class=\"button button-primary\" href=\"'.whs_RATING_URL.'\" target=\"_blank\" rel=\"external\">'. __( 'Yes, I\'d like to rate it!', 'newstatpress' ) .'</a>';
      $notice_text.= ' &nbsp; ';
      $notice_text.= '<a class=\"button button-default\" href=\"'.$query_str_con.'\" target=\"_self\" rel=\"external\">'. __( 'I already did!', 'newstatpress' ) .'</a>';

      $status['currentnag'] = TRUE;
      $status['vote'] = FALSE;
		}
		elseif( empty( $status['donate'] ) && $num_days_inst >= $donatedate ) {
			$nid = 'n02';
      $style = 'notice';

      $notice_text = '<p>'. __( 'You\'ve been using WorkHorseStat for several months now. We hope that means you like it and are finding it helpful.', 'newstatpress' ).'</p>';
      $notice_text.= '<p>'. __( 'WorkHorseStat is provided for free and maintained only on free time. If you like the plugin, consider a donation to help further its development', 'newstatpress' ).'</p>';
      $notice_text.= '<a class=\"button button-primary\" href=\"'.whs_DONATE_URL.'\" target=\"_blank\" rel=\"external\">'. __( 'Yes, I\'d like to donate!', 'newstatpress' ) .'</a>';
      $notice_text.= ' &nbsp; ';
      $notice_text.= '<a class=\"button button-default\" href=\"'.$query_str_con.'\" target=\"_self\" rel=\"external\">'. __( 'I already did!', 'newstatpress' ) .'</a>';

			$status['currentnag'] = TRUE;
      $status['donate'] = FALSE;
		}

	}

	if( !empty( $status['currentnag'] ) ) {
		add_action( 'admin_notices', 'whs_AdminNagNotices' );
		$new_nag_notice = array( 'nid' => $nid, 'style' => $style, 'notice' => $notice_text );
		update_user_meta( $current_user->ID, 'newstatpress_nag_notices', $new_nag_notice );
		update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status );
	}
}

function whs_AdminNotices() {
	$admin_notices = get_option('newstatpress_admin_notices');
	if( !empty( $admin_notices ) ) {
		$style 	= $admin_notices['style']; /* 'error' or 'updated' */
		$notice	= $admin_notices['notice'];
    $query_str_con	= 'QUERYSTRING';
    echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
	}
	delete_option('newstatpress_admin_notices');
}

add_action( 'admin_init', 'whs_HideNagNotices', -10 );
function whs_HideNagNotices() {
	// if( !whs_is_user_admin() ) { return; }
	$ns_codes		= array( 'n01' => 'vote',
                       'n02' => 'donate',
                       'n03' => 'news' );
	if( !isset( $_GET['newstatpress_hide_nag'], $_GET['nid'], $ns_codes[$_GET['nid']] ) || $_GET['newstatpress_hide_nag'] != '1' ) { return; }
	global $current_user;
	$status			= get_user_meta( $current_user->ID, 'newstatpress_nag_status', TRUE );
	$timenow		= time();
	$url			= whs_GetUrl();
	$query_args		= whs_GetQueryArgs( $url ); unset( $query_args['newstatpress_hide_nag'],$query_args['nid'] );
	$query_str		= http_build_query( $query_args ); if( $query_str != '' ) { $query_str = '?'.$query_str; }
	$redirect_url	= whs_FixUrl( $url, TRUE, TRUE ) . $query_str;
	$status['currentnag'] = FALSE;
  if ($_GET['nid']!="n03")
    $status['lastnag'] = $timenow;
  $status[$ns_codes[$_GET['nid']]] = TRUE;
	update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status );
	update_user_meta( $current_user->ID, 'newstatpress_nag_notices', array() );
	wp_redirect( $redirect_url );
	exit;
}


//---------------------------------------------------------------------------
// OTHER Functions
//---------------------------------------------------------------------------


function whs_load_time()
{
	echo "<font size='1'>Page generated in " . timer_stop(0,2) . "s ".get_num_queries()." SQL queries</font> <br> In-house analytics plugin for WorkHorse is a fork of NewStatPress.";
}



/**
 * Display tabs pf navigation bar for menu in page
 *
 * @param menu_tabs list of menu tabs
 * @param current current tabs
 * @param ref page reference
 */
function whs_DisplayTabsNavbarForMenuPage($menu_tabs, $current, $ref) {
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $menu_tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class tab$tab' href='?page=$ref&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}


// function whs_DisplayTabsNavbarForMenuPages($menu_tabs, $current, $ref) {
//
//     echo "<div id='usual1' class='icon32 usual'><br></div>";
//     echo "<h2  class='nav-tab-wrapper'>";
//     foreach( $menu_tabs as $tab => $name ){
//         $class = ( $tab == $current ) ? ' nav-tab-active selected' : '';
//         echo "<a class='nav-tab$class' href='#$tab'>$name</a>";
//     }
//     echo '</h2>';
// }








//---------------------------------------------------------------------------
// TABLE Functions
//---------------------------------------------------------------------------

function whs_TableSize($table) {
  global $wpdb;
  $res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table'");
  foreach ($res as $fstatus) {
    $data_lenght = $fstatus->Data_length;
    $data_rows = $fstatus->Rows;
  }
  return number_format(($data_lenght/1024/1024), 2, ",", " ")." Mb ($data_rows ". __('records','newstatpress').")";
}


?>
