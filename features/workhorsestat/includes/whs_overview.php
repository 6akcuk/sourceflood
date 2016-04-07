<?php

/**
 * Show overwiew
 *
 *****************/
function whs_WorkHorseStatMain() {
  global $wpdb;
  $table_name = whs_TABLENAME;

  ?>
  <div class="wrap">
    <h2 class="nav-tab-wrapper">
      <a class="nav-tab nav-tab-active">Overview</a>
      <a href="/wp-admin/admin.php?page=whs_details" class="nav-tab">
        Details
      </a>
      <a href="/wp-admin/admin.php?page=whs_visits" class="nav-tab">
        Visits
      </a>
      <a href="/wp-admin/admin.php?page=whs_search" class="nav-tab">
        Search
      </a>
      <a href="/wp-admin/admin.php?page=whs_tools" class="nav-tab">
        Tools
      </a>
    </h2>
  </div>
  <?php

  //whs_NoticeNew(1);
  whs_MakeOverview('main');

  $_workhorsestat_url=PluginUrl();

  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('workhorsestat_el_overview')=='') ? 10:get_option('workhorsestat_el_overview'));

  $lasthits = $wpdb->get_results("
    SELECT *
    FROM $table_name
    WHERE (os<>'' OR feed<>'')
    ORDER bY id DESC LIMIT $querylimit
  ");
  $lastsearchterms = $wpdb->get_results("
    SELECT date,time,referrer,urlrequested,search,searchengine
    FROM $table_name
    WHERE search<>''
    ORDER BY id DESC LIMIT $querylimit
  ");

  $lastreferrers = $wpdb->get_results("
    SELECT date,time,referrer,urlrequested
    FROM $table_name
    WHERE
     ((referrer NOT LIKE '".get_option('home')."%') AND
      (referrer <>'') AND
      (searchengine='')
     ) ORDER BY id DESC LIMIT $querylimit
  ");

  ?>
  <!-- Last hits table -->
  <div class='wrap'>
    <h2> <?php echo  __('Last hits',whs_TEXTDOMAIN); ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',whs_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Time',whs_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('IP',whs_TEXTDOMAIN); ?></th>
          <th scope='col'><?php echo __('Country',whs_TEXTDOMAIN).'/'.__('Language',whs_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Page',whs_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Feed',whs_TEXTDOMAIN); ?></th>
          <th></th>
          <th scope='col' style='width:120px;'><?php _e('OS',whs_TEXTDOMAIN); ?></th>
          <th></th>
          <th scope='col' style='width:120px;'><?php _e('Browser',whs_TEXTDOMAIN); ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
      foreach ($lasthits as $fivesdraft) {
        print "<tr>";
        print "<td>". whs_hdate($fivesdraft->date) ."</td>";
        print "<td>". $fivesdraft->time ."</td>";
        print "<td>". $fivesdraft->ip ."</td>";
        print "<td>". $fivesdraft->nation ."</td>";
        print "<td>". whs_Abbreviate(whs_DecodeURL($fivesdraft->urlrequested),30) ."</td>";
        print "<td>". $fivesdraft->feed . "</td>";

        if($fivesdraft->os != '') {
          $img=$_workhorsestat_url."/images/os/".str_replace(" ","_",strtolower($fivesdraft->os)).".png";
          print "<td class='browser'><img class='img_browser' SRC='$img'></td>";
        }
        else {
            print "<td></td>";
        }
        print "<td>".$fivesdraft->os . "</td>";

        if($fivesdraft->browser != '') {
          $img=str_replace(" ","",strtolower($fivesdraft->browser)).".png";
          print "<td><img class='img_browser' SRC='".$_workhorsestat_url."/images/browsers/$img'></td>";
        }
        else {
           print "<td></td>";
        }
        print "<td>".$fivesdraft->browser."</td></tr>\n";
        // print "</tr>";
      }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Search terms table -->
  <div class='wrap'>
    <h2><?php _e('Last search terms',whs_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Time',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Terms',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Engine',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Result',whs_TEXTDOMAIN) ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($lastsearchterms as $rk) {
          print "<tr>
                  <td>".whs_hdate($rk->date)."</td><td>".$rk->time."</td>
                  <td><a href='".$rk->referrer."' target='_blank'>".$rk->search."</a></td>
                  <td>".$rk->searchengine."</td><td><a href='".get_bloginfo('url').$extra.$rk->urlrequested."' target='_blank'>". __('page viewed',whs_TEXTDOMAIN). "</a></td>
                </tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Referrers table -->
  <div class='wrap'>
    <h2><?php _e('Last referrers',whs_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Time',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('URL',whs_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Result',whs_TEXTDOMAIN) ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($lastreferrers as $rk) {
          print "<tr><td>".whs_hdate($rk->date)."</td><td>".$rk->time."</td><td><a href='".$rk->referrer."' target='_blank'>".whs_Abbreviate($rk->referrer,80)."</a></td><td><a href='".get_bloginfo('url').$extra.$rk->urlrequested."'  target='_blank'>". __('page viewed',whs_TEXTDOMAIN). "</a></td></tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

<?php
  # Last Agents
  print "<div class='wrap'><h2>".__('Last agents',whs_TEXTDOMAIN)."</h2><table class='widefat nsp'><thead><tr><th scope='col'>".__('Agent',whs_TEXTDOMAIN)."</th><th scope='col'></th><th scope='col' style='width:120px;'>". __('OS',whs_TEXTDOMAIN). "</th><th scope='col'></th><th scope='col' style='width:120px;'>". __('Browser',whs_TEXTDOMAIN).'/'. __('Spider',whs_TEXTDOMAIN). "</th></tr></thead>";
  print "<tbody id='the-list'>";
  $qry = $wpdb->get_results("
    SELECT agent,os,browser,spider
    FROM $table_name
    GROUP BY agent,os,browser,spider
    ORDER BY id DESC LIMIT $querylimit
  ");
  foreach ($qry as $rk) {
    print "<tr><td>".$rk->agent."</td>";
    if($rk->os != '') {
      $img=str_replace(" ","_",strtolower($rk->os)).".png";
      print "<td><IMG class='img_browser' SRC='".$_workhorsestat_url."/images/os/$img'> </td>";
    } else {
        print "<td></td>";
      }
    print "<td>". $rk->os . "</td>";
    if($rk->browser != '') {
      $img=str_replace(" ","",strtolower($rk->browser)).".png";
      print "<td><IMG class='img_browser' SRC='".$_workhorsestat_url."/images/browsers/$img'></td>";
    } else {
        print "<td></td>";
      }
    print "<td>".$rk->browser." ".$rk->spider."</td></tr>\n";
  }
  print "</table></div>";


  # Last pages
  print "<div class='wrap'><h2>".__('Last pages',whs_TEXTDOMAIN)."</h2><table class='widefat nsp'><thead><tr><th scope='col'>".__('Date',whs_TEXTDOMAIN)."</th><th scope='col'>".__('Time',whs_TEXTDOMAIN)."</th><th scope='col'>".__('Page',whs_TEXTDOMAIN)."</th><th scope='col' style='width:17px;'></th><th scope='col' style='width:120px;'>".__('OS',whs_TEXTDOMAIN)."</th><th style='width:17px;'></th><th scope='col' style='width:120px;'>".__('Browser',whs_TEXTDOMAIN)."</th></tr></thead>";
  print "<tbody id='the-list'>";
  $qry = $wpdb->get_results("
    SELECT date,time,urlrequested,os,browser,spider
    FROM $table_name
    WHERE (spider='' AND feed='')
    ORDER BY id DESC LIMIT $querylimit
  ");
  foreach ($qry as $rk) {
    print "<tr><td>".whs_hdate($rk->date)."</td><td>".$rk->time."</td><td>".whs_Abbreviate(whs_DecodeURL($rk->urlrequested),60)."</td>";
    if($rk->os != '') {
      $img=str_replace(" ","_",strtolower($rk->os)).".png";
      print "<td><IMG class='img_browser' SRC='".$_workhorsestat_url."/images/os/$img'> </td>";
    } else {
        print "<td></td>";
      }
    print "<td>". $rk->os . "</td>";
    if($rk->browser != '') {
      $img=str_replace(" ","",strtolower($rk->browser)).".png";
      print "<td><IMG class='img_browser' SRC='".$_workhorsestat_url."/images/browsers/$img'></td>";
    } else {
        print "<td></td>";
      }
    print "<td>".$rk->browser." ".$rk->spider."</td></tr>\n";
  }
  print "</table></div>";


  # Last Spiders
  print "<div class='wrap'><h2>".__('Last spiders',whs_TEXTDOMAIN)."</h2><table class='widefat nsp'><thead><tr><th scope='col'>".__('Date',whs_TEXTDOMAIN)."</th><th scope='col'>".__('Time',whs_TEXTDOMAIN)."</th><th scope='col'></th><th scope='col'>".__('Spider',whs_TEXTDOMAIN)."</th><th scope='col'>".__('Agent',whs_TEXTDOMAIN)."</th></tr></thead>";
  print "<tbody id='the-list'>";
  $qry = $wpdb->get_results("
    SELECT date,time,agent,os,browser,spider
    FROM $table_name
    WHERE (spider<>'')
    ORDER BY id DESC LIMIT $querylimit
  ");
  foreach ($qry as $rk) {
    print "<tr><td>".whs_hdate($rk->date)."</td><td>".$rk->time."</td>";
    if($rk->spider != '') {
      $img=str_replace(" ","_",strtolower($rk->spider)).".png";
      print "<td><IMG class='img_os' SRC='".$_workhorsestat_url."/images/spider/$img'> </td>";
    } else print "<td></td>";
    print "<td>".$rk->spider."</td><td> ".$rk->agent."</td></tr>\n";
  }
  print "</table></div>";

  print "<br />";
  print "&nbsp;<i>StatPress table size: <b>".whs_TableSize(whs_TABLENAME)."</b></i><br />";
  print "&nbsp;<i>StatPress current time: <b>".current_time('mysql')."</b></i><br />";
  print "&nbsp;<i>RSS2 url: <b>".get_bloginfo('rss2_url').' ('.whs_ExtractFeedFromUrl(get_bloginfo('rss2_url')).")</b></i><br />";
  whs_load_time();
}

/**
 * Abbreviate the given string to a fixed length
 *
 * @param s the string
 * @param c the number of chars
 * @return the abbreviate string
 ***********************************************/
function whs_Abbreviate($s,$c) {
  $s=__($s);
  $res=""; if(strlen($s)>$c) { $res="..."; }
  return substr($s,0,$c).$res;
}


?>
