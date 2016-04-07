<?php

/****** List of Functions available ******
 *
 * whs_DisplayToolsPage()
 * whs_RemovePluginDatabase()
 * whs_IP2nationDownload()
 * whs_ExportNow()
 * whs_Export()
 *****************************************/

/**
 * Display the tools page using tabs
 */
function whs_DisplayToolsPage() {

  global $pagenow;
  $page='whs_tools';
  $ToolsPage_tabs = array( 'IP2nation' => __('IP2nation','workhorsestat'),
                            'update' => __('Update','workhorsestat'),
                            'export' => __('Export','workhorsestat'),
                            'optimize' => __('Optimize','workhorsestat'),
                            'repair' => __('Repair','workhorsestat'),
                            'remove' => __('Remove','workhorsestat')
                          );

  $default_tab='IP2nation';

  ?>
  <div class="wrap">
    <h2 class="nav-tab-wrapper">
      <a href="/wp-admin/admin.php?page=whs-main" class="nav-tab">Overview</a>
      <a href="/wp-admin/admin.php?page=whs_details" class="nav-tab">
        Details
      </a>
      <a href="/wp-admin/admin.php?page=whs_visits" class="nav-tab">
        Visits
      </a>
      <a href="/wp-admin/admin.php?page=whs_search" class="nav-tab">
        Search
      </a>
      <a href="/wp-admin/admin.php?page=whs_tools" class="nav-tab nav-tab-active">
        Tools
      </a>
    </h2>
  </div>
  <?php

  print "<div class='wrap'><h2>".__('Database Tools','workhorsestat')."</h2>";

  if ( isset ( $_GET['tab'] ) ) whs_DisplayTabsNavbarForMenuPage($ToolsPage_tabs,$_GET['tab'],$page);
  else whs_DisplayTabsNavbarForMenuPage($ToolsPage_tabs, $default_tab, $page);

  if ( $pagenow == 'admin.php' && $_GET['page'] == $page ) {

    if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
    else $tab = $default_tab;

    switch ($tab) {

      case 'IP2nation' :
      whs_IP2nation();
      break;

      case 'export' :
      whs_Export();
      break;

      case 'update' :
      whs_Update();
      break;

      case 'optimize' :
      whs_Optimize();
      break;

      case 'repair' :
      whs_Repair();
      break;

      case 'remove' :
      whs_RemovePluginDatabase();
      break;
    }
  }
}

function whs_IndexTableSize($table) {
  global $wpdb;
  $res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table'");
  foreach ($res as $fstatus) {
    $index_lenght = $fstatus->Index_length;
  }
  return number_format(($index_lenght/1024/1024), 2, ",", " ")." Mb";
}


/**
 * IP2nation form function
 *
 *************************/
function whs_IP2nation() {



  // Install or Remove if requested by user
  if (isset($_POST['installation']) && $_POST['installation'] == 'install' ) {
    $install_result=whs_IP2nationInstall();
  }
  elseif (isset($_POST['installation']) && $_POST['installation'] == 'remove' ) {
    $install_result=whs_IP2nationRemove();
  }

  // Display message if present
  if (isset($install_result) AND $install_result !='') {
    print "<br /><div class='updated'><p>".__($install_result,'workhorsestat')."</p></div>";
  }

  global $whs_option_vars;
  global $wpdb;

  //Create IP2nation variable if not exists: value 'none' by default or date when installed
  $installed=get_option($whs_option_vars['ip2nation']['name']);
  if ($installed=="") {
    add_option( $whs_option_vars['ip2nation']['name'], $whs_option_vars['ip2nation']['value'],'','yes');
  }

  echo "<br /><br />";
     $file_ip2nation= WP_PLUGIN_DIR . '/' .dirname(plugin_basename(__FILE__)) . '/includes/ip2nation.sql';
     $date=date('d/m/Y', filemtime($file_ip2nation));

     $table_name = "ip2nation";
     if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
       $value_remove="none";
       $class_inst="desactivated";
       $installed=$whs_option_vars['ip2nation']['value'];
     }
     else {
         $value_remove="remove";
         $class_inst="";
         $installed=get_option($whs_option_vars['ip2nation']['name']);
         if($installed=='none')
          $installed=__('unknow','workhorsestat');
     }

    // Display status
    $i=sprintf(__('Last version available: %s','workhorsestat'), $date);
    echo $i.'<br />';
     if ($installed!="none") {
       $i=sprintf(__('Last version installed: %s','workhorsestat'), $installed);
       echo $i.'<br /><br />';
       _e('To update the IP2nation database, just click on the button bellow.','workhorsestat');
       if($installed==$date) {
         $button_name='Update';
         $value_install='none';
         $class_install="desactivated";
       }
       else {
         $button_name='Install';
       }
     }
     else {
       _e('Last version installed: none ','workhorsestat');
       echo '<br /><br />';
       _e('To download and to install the IP2nation database, just click on the button bellow.','workhorsestat');
       $button_name='Install';
     }


    ?>

    <br /><br />
      <form method=post>
       <input type=hidden name=page value=workhorsestat>

       <input type=hidden name=workhorsestat_action value=ip2nation>
       <button class='<?php echo $class_install ?> button button-primary' type=submit name=installation value=install>
         <?php _e($button_name,'workhorsestat'); ?>
       </button>

       <input type=hidden name=workhorsestat_action value=ip2nation>
       <button class='<?php echo $class_inst ?> button button-primary' type=submit name=installation value=<?php echo $value_remove ?> >
         <?php _e('Remove','workhorsestat'); ?>
       </button>
      </form>
    </div>

    <div class='update-nag help'>

    <?php
    _e('What is ip2nation?','workhorsestat');
    echo "<br/>";
    _e('ip2nation is a free MySQL database that offers a quick way to map an IP to a country. The database is optimized to ensure fast lookups and is based on information from ARIN, APNIC, RIPE etc. You may install the database using the link to the left. (see: <a href="http://www.ip2nation.com/">http://www.ip2nation.com</a>)','workhorsestat');
    echo "<br/><br />
          <span class='strong'>"
            .__('Note: The installation may take some times to complete.','workhorsestat').
         "</span>";

    ?>
    </div>
<?php
}

// add by chab
/**
 * Download and install IP2nation
 *
 * @return the status of the operation
 *************************************/
function whs_IP2nationDownload() {

  //Request to make http request with WP functions
  if( !class_exists( 'WP_Http' ) ) {
    include_once( ABSPATH . WPINC. '/class-http.php' );
  }

  // Definition $var
  $timeout=300;
  $db_file_url = 'http://www.ip2nation.com/ip2nation.zip';
  $upload_dir = wp_upload_dir();
  $temp_zip_file = $upload_dir['basedir'] . '/ip2nation.zip';

  //delete old file if exists
  unlink( $temp_zip_file );

  $result = wp_remote_get ($db_file_url, array( 'timeout' => $timeout ));

  //Writing of the ZIP db_file
  if ( !is_wp_error( $result ) ) {
    //Headers error check : 404
    if ( 200 != wp_remote_retrieve_response_code( $result ) ){
      $install_status = new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $result ) ) );
    }

    // Save file to temp directory
    // ******To add a md5 routine : to check the integrity of the file
    $content = wp_remote_retrieve_body($result);
    $zip_size = file_put_contents ($temp_zip_file, $content);
    if (!$zip_size) { // writing error
      $install_status=__('Failure to save content locally, please try to re-install.','workhorsestat');
    }
  }
  else { // WP_error
    $error_message = $result->get_error_message();
    echo '<div id="message" class="error"><p>' . $error_message . '</p></div>';
  }

  // require PclZip if not loaded
  if(! class_exists('PclZip')) {
    require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
  }

  // Unzip Db Archive
  $archive = new PclZip($temp_zip_file);
  $workhorsestat_includes_path = WP_PLUGIN_DIR . '/' .dirname(plugin_basename(__FILE__)) . '/includes';
  if ($archive->extract(PCLZIP_OPT_PATH, $workhorsestat_includes_path , PCLZIP_OPT_REMOVE_ALL_PATH) == 0) {
    $install_status=__('Failure to unzip archive, please try to re-install','workhorsestat');
  }
  else {
    $install_status=__('Installation of IP2nation database was successful','workhorsestat');
  }

  // Remove Zip file
  unlink( $temp_zip_file );
  return $install_status;
}

//TODO integrate error check
function whs_IP2nationInstall() {

  global $wpdb;
  global $whs_option_vars;

  $file_ip2nation= WP_PLUGIN_DIR . '/' .dirname(plugin_basename(__FILE__)) . '/includes/ip2nation.sql';

  $sql = file_get_contents($file_ip2nation);
  $sql_array = explode (";",$sql);
  foreach ($sql_array as $val) {
    $wpdb->query($val);

  }
  $date=date('d/m/Y', filemtime($file_ip2nation));
  // echo $date;
  update_option($whs_option_vars['ip2nation']['name'], $date);
  $install_status=__('Installation of IP2nation database was successful','workhorsestat');

 return $install_status;
}

//TODO integrate error check
function whs_IP2nationRemove() {

  global $wpdb;

  $sql = "DROP TABLE IF EXISTS ip2nation;";
  $wpdb->query($sql);
  $sql ="DROP TABLE IF EXISTS ip2nationCountries;";
  $wpdb->query($sql);

  update_option($whs_option_vars['ip2nation']['name'], $whs_option_vars['ip2nation']['value']);

  $install_status=__('IP2nation database was remove successfully','workhorsestat');

 return $install_status;
}


/**
 * Export form function
 */
 function whs_Export() {
   $export_description=__('The export tool allows you to save your statistics in a local file for a date interval defined by yourself.','workhorsestat');
   $export_description.="<br />";
   $export_description.=__('You can define the filename and the file extension, and also the fields delimiter used to separate the data.','workhorsestat');
   $export_description2=__('Note: the parameters chosen will be saved automatically as default values.','workhorsestat');

   $delimiter_description=__('default value : semicolon','workhorsestat');
   $extension_description=__('default value : CSV (readable by Excel)','workhorsestat');
   $filename_description=__('If the field remain blank, the default value is \'BLOG_TITLE-workhorsestat\'.','workhorsestat');
   $filename_description.="<br />";
   $filename_description.=__('The date interval will be added to the filename (i.e. BLOG_TITLE-workhorsestat_20160229-20160331.csv).','workhorsestat');

   $export_option=get_option('workhorsestat_exporttool');
?>
<!--TODO chab, check if the input format is ok  -->
  <div class='wrap'>
    <!-- <h3><?php //_e('Export stats to text file','workhorsestat'); ?> (csv)</h3> -->
    <p><?php echo $export_description; ?></p>
    <p><i><?php echo $export_description2; ?></i></p>

    <form method=get>
    <table class='form-tableH'>
      <tr>
        <th class='padd' scope='row' rowspan='3'>
          <?php _e('Date interval','workhorsestat'); ?>
        </th>
      </tr>
      <tr>
        <td><?php _e('From:','workhorsestat'); ?> </td>
        <td>
          <div class="input-container">
          <div class="icon-ph"><span class="dashicons dashicons-calendar-alt"></span>        </div>

          <input class="pik" id="datefrom" type="text" size="10" required maxlength="8" minlength="8" name="from" placeholder='<?php _e('YYYYMMDD','workhorsestat');?>'>
          <!-- <input type="submit" class="search" value="\f145" /> -->
          </div>

        </td>
      </tr>
      <tr>
        <td><?php _e('To:','workhorsestat'); ?> </td>
        <td>
          <div class="input-container">
          <div class="icon-ph"><span class="dashicons dashicons-calendar-alt"></span>        </div>
          <input class="pik" id="dateto" type="text" size="10" required maxlength="8" minlength="8" name="to" placeholder='<?php _e('YYYYMMDD','workhorsestat');?>'></td>
        </div>

      </tr>
    </table>
    <table class='form-tableH'>
      <tr>
            <th class='padd' scope='row' rowspan='2'>
              <?php _e('Filename','workhorsestat'); ?>
            </th>
          </tr>
      <tr>
        <td>
          <input class="" id="filename" type="text" size="30" maxlength="30" name="filename" placeholder='<?php _e('enter a filename','workhorsestat');?>' value="<?php echo $export_option['filename'];?>">
          <p class="description"><?php echo $filename_description ?></p>
        </td>
      </tr>
    </table>
    <table class='form-tableH'>
      <tr>
            <th class='padd' scope='row' rowspan='2'>
              <?php _e('File extension','workhorsestat'); ?>
            </th>
          </tr>
      <tr>
        <td>
          <select name=ext>
            <option <?php if($export_option['ext']=='csv') echo 'selected';?>
>csv</option>
            <option <?php if($export_option['ext']=='txt') echo 'selected';?>
>txt</option>
          </select>
          <p class="description"><?php echo $extension_description ?></p>
        </td>
      </tr>
    </table>
    <table class='form-tableH'>
      <tr>
            <th class='padd' scope='row' rowspan='2'>
              <?php _e('Fields delimiter','workhorsestat'); ?>
            </th>
          </tr>
      <tr>
        <td><select name=del>
          <option <?php if($export_option['del']==',') echo 'selected';?>
>,</option>
          <option <?php if($export_option['del']=='tab') echo 'selected';?>>tab</option>
          <option <?php if($export_option['del']==';') echo 'selected';?>>;</option>
          <option <?php if($export_option['del']=='|') echo 'selected';?>>|</option></select>
          <p class="description"><?php echo $delimiter_description ?></p>

        </td>
      </tr>
    </table>
    <input class='button button-primary' type=submit value=<?php _e('Export','workhorsestat'); ?>>
    <input type=hidden name=page value=workhorsestat><input type=hidden name=workhorsestat_action value=exportnow>
    </form>
  </div>
<?php
}

/**
 * Export the NewStatPress data
 */
function whs_ExportNow() {
  global $wpdb;
  $table_name = whs_TABLENAME;
  if($_GET['filename']=='')
    $filename=get_bloginfo('title' )."-workhorsestat_".$_GET['from']."-".$_GET['to'].".".$_GET['ext'];
  else
    $filename=$_GET['filename']."_".$_GET['from']."-".$_GET['to'].".".$_GET['ext'];

  $ti['filename']=$_GET['filename'];
  $ti['del']=$_GET['del'];
  $ti['ext']=$_GET['ext'];
  update_option('workhorsestat_exporttool', $ti);

  header('Content-Description: File Transfer');
  header("Content-Disposition: attachment; filename=$filename");
  header('Content-Type: text/plain charset=' . get_option('blog_charset'), true);
  $qry = $wpdb->get_results(
    "SELECT *
     FROM $table_name
     WHERE
       date>='".(date("Ymd",strtotime(substr($_GET['from'],0,8))))."' AND
       date<='".(date("Ymd",strtotime(substr($_GET['to'],0,8))))."';
    ");
  $del=substr($_GET['del'],0,1);
  if ($del=="t") {
    $del="\t";
  }
  print "date".$del."time".$del."ip".$del."urlrequested".$del."agent".$del."referrer".$del."search".$del."nation".$del."os".$del."browser".$del."searchengine".$del."spider".$del."feed\n";
  foreach ($qry as $rk) {
    print '"'.$rk->date.'"'.$del.'"'.$rk->time.'"'.$del.'"'.$rk->ip.'"'.$del.'"'.$rk->urlrequested.'"'.$del.'"'.$rk->agent.'"'.$del.'"'.$rk->referrer.'"'.$del.'"'.$rk->search.'"'.$del.'"'.$rk->nation.'"'.$del.'"'.$rk->os.'"'.$del.'"'.$rk->browser.'"'.$del.'"'.$rk->searchengine.'"'.$del.'"'.$rk->spider.'"'.$del.'"'.$rk->feed.'"'."\n";
  }
  die();
}

/**
 * Generate HTML for remove menu in Wordpress
 */
function whs_RemovePluginDatabase() {

  if(isset($_POST['removeit']) && $_POST['removeit'] == 'yes') {
    global $wpdb;
    $table_name = whs_TABLENAME;
    $results =$wpdb->query( "DELETE FROM " . $table_name);
    print "<br /><div class='remove'><p>".__('All data removed','workhorsestat')."!</p></div>";
  }
  else {
      ?>

        <div class='wrap'><h3><?php _e('Remove NewStatPress database','workhorsestat'); ?></h3>
          <br />

        <form method=post>
              <?php _e('To remove the Newstatpress database, just click on the button bellow.','workhorsestat');?>
          <br /><br />
        <input class='button button-primary' type=submit value="<?php _e('Remove','workhorsestat'); ?>" onclick="return confirm('<?php _e('Are you sure?','workhorsestat'); ?>');" >
        <input type=hidden name=removeit value=yes>
        </form>
        <div class='update-nag help'>
          <?php
            _e("This operation will remove all collected data by NewStatpress. This function is useful at people who did not want use the plugin any more or who want simply purge the stored data.","workhorsestat");
          ?>
          <br />
          <span class='strong'>
          <?php _e("If you have doubt about this function, don't use it.","workhorsestat"); ?>
        </span>
       </div>
       <div class='update-nag warning'><p>
     <?php _e('Warning: pressing the below button will make all your stored data to be erased!',"workhorsestat"); ?>
   </p></div>
        </div>
      <?php
  }
}

/**
 * Get the days a user has choice for updating the database
 *
 * @return the number of days of -1 for all days
 */
function whs_DurationToDays() {

  // get the number of days for the update
  switch (get_option('workhorsestat_updateint')) {
    case '1 week':
      $days=7; break;
    case '2 weeks':
      $days=14; break;
    case '3 weeks':
      $days=21; break;
    case '1 month':
      $days=30; break;
    case '2 months':
      $days=60; break;
    case '3 months':
      $days=90; break;
    case '6 months':
      $days=180; break;
    case '9 months':
      $days=270; break;
    case '12 months':
      $days=365; break;
    default :
      $days=-1; // infinite in the past, for all day
  }

  return $days;
}

/**
 * Extract the feed from the given url
 *
 * @param url the url to parse
 * @return the extracted url
 *************************************/
function whs_ExtractFeedReq($url) {
  list($null,$q)=explode("?",$url);
  if (strpos($q, "&")!== false) list($res,$null)=explode("&",$q);
  else $res=$q;
  return $res;
}

/**
 * Update form function
 *
 ***********************/
function whs_Update() {
  // database update if requested by user
  if (isset($_POST['update']) && $_POST['update'] == 'yes' ) {
    whs_UpdateNow();
    die;
  }
  ?>
  <div class='wrap'>
   <h3><?php _e('Database update','workhorsestat'); ?></h3>
       <?php _e('To update the workhorsestat database, just click on the button bellow.','workhorsestat');?>
   <br /><br />
   <form method=post>
    <input type=hidden name=page value=workhorsestat>
    <input type=hidden name=update value=yes>
    <input type=hidden name=workhorsestat_action value=update>
    <button class='button button-primary' type=submit><?php _e('Update','workhorsestat'); ?></button>
   </form>
  </div>

  <div class='update-nag help'>

  <?php

  _e('Update the database is particularly useful when the ip2nation data and definitions data (OS, browser, spider) have been updated. An option in future will allow an automatic update of the database..','workhorsestat');
  echo "<br/><br />
        <span class='strong'>"
          .__('Note: The update may take some times to complete.','workhorsestat').
       "</span>";

  ?>
  </div>

  <?php
}

/**
 * Performes database update with new definitions
 */
function whs_UpdateNow() {
  global $wpdb;
  global $workhorsestat_dir;

  $table_name = whs_TABLENAME;

  $wpdb->flush();     // flush for counting right the queries
  $start_time = microtime(true);

  $days=whs_DurationToDays();  // get the number of days for the update

  $to_date  = gmdate("Ymd",current_time('timestamp'));

  if ($days==-1)
    $from_date= "19990101";   // use a date where this plugin was not present
  else
    $from_date = gmdate('Ymd', current_time('timestamp')-86400*$days);

  $_workhorsestat_url=PluginUrl();

  $wpdb->show_errors();

  //add by chab
  //$var requesting the absolute path
  $img_ok = $_workhorsestat_url.'images/ok.gif';
  // $ip2nation_db = $workhorsestat_dir.'/includes/ip2nation.sql';

  print "<div class='wrap'><h2>".__('Database Update','workhorsestat')."</h2><br />";

  print "<table class='widefat nsp'><thead><tr><th scope='col'>".__('Updating...','workhorsestat')."</th><th scope='col' style='width:400px;'>".__('Size','workhorsestat')."</th><th scope='col' style='width:100px;'>".__('Result','workhorsestat')."</th><th></th></tr></thead>";
  print "<tbody id='the-list'>";

  # update table
  whs_BuildPluginSQLTable('update');

  echo "<tr>
          <td>". __('Structure','workhorsestat'). " $table_name</td>
          <td>".whs_TableSize($wpdb->prefix."statpress")."</td>
          <td><img class'update_img' src='$img_ok'></td>
        </tr>";

  print "<tr><td>". __('Index','workhorsestat'). " $table_name</td>";
  print "<td>".whs_IndexTableSize($wpdb->prefix."statpress")."</td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  # Update Feed
  print "<tr><td>". __('Feeds','workhorsestat'). "</td>";
  $wpdb->query("
    UPDATE $table_name
    SET feed=''
    WHERE date BETWEEN $from_date AND $to_date;"
  );

  # not standard
  $wpdb->query("
    UPDATE $table_name
    SET feed='RSS2'
    WHERE
      urlrequested LIKE '%/feed/%' AND
      date BETWEEN $from_date AND $to_date;"
  );

  $wpdb->query("
    UPDATE $table_name
    SET feed='RSS2'
    WHERE
      urlrequested LIKE '%wp-feed.php%' AND
      date BETWEEN $from_date AND $to_date;"
  );

  # standard blog info urls
  $s=whs_ExtractFeedReq(get_bloginfo('comments_atom_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='COMMENT'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
   );
  }
  $s=whs_ExtractFeedReq(get_bloginfo('comments_rss2_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='COMMENT'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
    );
  }
  $s=whs_ExtractFeedReq(get_bloginfo('atom_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='ATOM'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
    );
  }
  $s=whs_ExtractFeedReq(get_bloginfo('rdf_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='RDF'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
    );
  }
  $s=whs_ExtractFeedReq(get_bloginfo('rss_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='RSS'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
    );
  }
  $s=whs_ExtractFeedReq(get_bloginfo('rss2_url'));
  if($s != '') {
    $wpdb->query("
      UPDATE $table_name
      SET feed='RSS2'
      WHERE
        INSTR(urlrequested,'$s')>0 AND
        date BETWEEN $from_date AND $to_date;"
    );
  }

  $wpdb->query("
    UPDATE $table_name
    SET feed = ''
    WHERE
      isnull(feed) AND
      date BETWEEN $from_date AND $to_date;"
  );

  print "<td></td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  # Update OS
  print "<tr><td>". __('OSes','workhorsestat'). "</td>";
  $wpdb->query("
    UPDATE $table_name
    SET os = ''
    WHERE date BETWEEN $from_date AND $to_date;"
  );
  $lines = file($workhorsestat_dir.'/def/os.dat');
  foreach($lines as $line_num => $os) {
    list($nome_os,$id_os)=explode("|",$os);
    $qry="
      UPDATE $table_name
      SET os = '$nome_os'
      WHERE
        os='' AND
        replace(agent,' ','') LIKE '%".$id_os."%' AND
        date BETWEEN $from_date AND $to_date;";
    $wpdb->query($qry);
  }
  print "<td></td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  # Update Browser
  print "<tr><td>". __('Browsers','workhorsestat'). "</td>";
  $wpdb->query("
    UPDATE $table_name
    SET browser = ''
    WHERE date BETWEEN $from_date AND $to_date;"
  );
  $lines = file($workhorsestat_dir.'/def/browser.dat');
  foreach($lines as $line_num => $browser) {
    list($nome,$id)=explode("|",$browser);
    $qry="
      UPDATE $table_name
      SET browser = '$nome'
      WHERE
        browser='' AND
        replace(agent,' ','') LIKE '%".$id."%' AND
        date BETWEEN $from_date AND $to_date;";
    $wpdb->query($qry);
  }
  print "<td></td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  # Update Spider
  print "<tr><td>". __('Spiders','workhorsestat'). "</td>";
  $wpdb->query("
    UPDATE $table_name
    SET spider = ''
    WHERE date BETWEEN $from_date AND $to_date;"
  );
  $lines = file($workhorsestat_dir.'/def/spider.dat');
  foreach($lines as $line_num => $spider) {
    list($nome,$id)=explode("|",$spider);
    $qry="
      UPDATE $table_name
      SET spider = '$nome',os='',browser=''
      WHERE
        spider='' AND
        replace(agent,' ','') LIKE '%".$id."%' AND
        date BETWEEN $from_date AND $to_date;";
    $wpdb->query($qry);
  }
  print "<td></td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  # Update Search engine
  print "<tr><td>". __('Search engines','workhorsestat'). " </td>";
  $wpdb->query("
    UPDATE $table_name
    SET searchengine = '', search=''
    WHERE date BETWEEN $from_date AND $to_date;");
  $qry = $wpdb->get_results("
    SELECT id, referrer
    FROM $table_name
    WHERE
      length(referrer)!=0 AND
      date BETWEEN $from_date AND $to_date");
  foreach ($qry as $rk) {
    list($searchengine,$search_phrase)=explode("|",whs_GetSE($rk->referrer));
    if($searchengine <> '') {
      $q="
        UPDATE $table_name
        SET searchengine = '$searchengine', search='".addslashes($search_phrase)."'
        WHERE
          id=".$rk->id." AND
          date BETWEEN $from_date AND $to_date;";
      $wpdb->query($q);
    }
  }
  print "<td></td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  $end_time = microtime(true);
  $sql_queries=$wpdb->num_queries;

  # Final statistics
  print "<tr><td>". __('Final Structure','workhorsestat'). " $table_name</td>";
  print "<td>".whs_TableSize($wpdb->prefix."statpress")."</td>"; // todo chab : to clean
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  print "<tr><td>". __('Final Index','workhorsestat'). " $table_name</td>";
  print "<td>".whs_IndexTableSize($wpdb->prefix."statpress")."</td>"; // todo chab : to clean
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  print "<tr><td>". __('Duration of the update','workhorsestat'). "</td>";
  print "<td>".round($end_time - $start_time, 2)." sec</td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  print "<tr><td>". __('This update was done in','workhorsestat'). "</td>";
  print "<td>".$sql_queries." " . __('SQL queries','workhorsestat'). "</td>";
  print "<td><img class'update_img' src='$img_ok'></td></tr>";

  print "</tbody></table></div><br>\n";
  $wpdb->hide_errors();
}

/**
 * Optimize form function
 */
function whs_Optimize() {

  // database update if requested by user
  if (isset($_POST['optimize']) && $_POST['optimize'] == 'yes' ) {
    whs_OptimizeNow();
    die;
  }
  ?>
  <div class='wrap'>
    <h3><?php _e('Optimize table','workhorsestat'); ?></h3>
    <?php _e('To optimize the statpress table, just click on the button bellow.','workhorsestat');?>
    <br /><br />
    <form method=post>
      <input type=hidden name=page value=workhorsestat>
      <input type=hidden name=optimize value=yes>
      <input type=hidden name=workhorsestat_action value=optimize>
      <button class='button button-primary' type=submit><?php _e('Optimize','workhorsestat'); ?></button>
    </form>

    <div class='update-nag help'>
      <?php _e('Optimize a table is an database operation that can free some server space if you had lot of delation (like with prune activated) in it.','workhorsestat');?>
      <br /><br />
      <span class='strong'>
        <?php _e('Be aware that this operation may take a lot of server time to finish the processing (depending on your database size). So so use it only if you know what you are doing.','workhorsestat');?>
      </span>
    </div>
  </div>
  <?php
}

/**
 * Repair form function
 */
function whs_Repair() {
  // database update if requested by user
  if (isset($_POST['repair']) && $_POST['repair'] == 'yes' ) {
    whs_RepairNow();
    die;
  }
  ?>
  <div class='wrap'>
   <h3><?php _e('Repair table','workhorsestat'); ?></h3>
       <?php _e('To repair the statpress table if damaged, just click on the button bellow.','workhorsestat');?>
   <br /><br />
   <form method=post>
    <input type=hidden name=page value=workhorsestat>
    <input type=hidden name=repair value=yes>
    <input type=hidden name=workhorsestat_action value=repair>
    <button class='button button-primary' type=submit><?php _e('Repair','workhorsestat'); ?></button>
   </form>

   <div class='update-nag help'>
     <?php _e('Repair is an database operation that can fix a corrupted table.','workhorsestat');?>
    <br /><br />
    <span class='strong'>
    <?php _e('Be aware that this operation may take a lot of server time to finish the processing (depending on your database size). So so use it only if you know what you are doing.','workhorsestat');?>
    </span>
   </div>
  </div><?php
}

function whs_OptimizeNow() {
  global $wpdb;
  $table_name = whs_TABLENAME;

  $wpdb->query("OPTIMIZE TABLE $table_name");
  print "<br /><div class='optimize'><p>".__('Optimization finished','workhorsestat')."!</p></div>";
}

function whs_RepairNow() {
  global $wpdb;
  $table_name = whs_TABLENAME;

  $wpdb->query("REPAIR TABLE $table_name");
  print "<br /><div class='repair'><p>".__('Repair finished','workhorsestat')."!</p></div>";
}


?>
