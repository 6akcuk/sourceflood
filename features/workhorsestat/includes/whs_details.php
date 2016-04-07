<?php

/**
 * Display details page
 */
function whs_DisplayDetails() {
  global $wpdb;
  $table_name = whs_TABLENAME;

  //$querylimit="LIMIT 10";

  ?>
  <div class="wrap">
    <h2 class="nav-tab-wrapper">
      <a href="/wp-admin/admin.php?page=whs-main" class="nav-tab">Overview</a>
      <a href="/wp-admin/admin.php?page=whs_details" class="nav-tab nav-tab-active">
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

  # Top days
  whs_GetDataQuery2("date", __('Top days','workhorsestat') ,(get_option('workhorsestat_el_top_days')=='') ? 5:get_option('workhorsestat_el_top_days'), FALSE);
  # O.S.
  whs_GetDataQuery2("os",__('OSes','workhorsestat') ,(get_option('workhorsestat_el_os')=='') ? 10:get_option('workhorsestat_el_os'),"","","AND feed='' AND spider='' AND os<>''");

  # Browser
  whs_GetDataQuery2("browser",__('Browsers','workhorsestat') ,(get_option('workhorsestat_el_browser')=='') ? 10:get_option('workhorsestat_el_browser'),"","","AND feed='' AND spider='' AND browser<>''");

  # Feeds
  whs_GetDataQuery2("feed",__('Feeds','workhorsestat') ,(get_option('workhorsestat_el_feed')=='') ? 5:get_option('workhorsestat_el_feed'),"","","AND feed<>''");

  # SE
  whs_GetDataQuery2("searchengine",__('Search engines','workhorsestat') ,(get_option('workhorsestat_el_searchengine')=='') ? 10:get_option('workhorsestat_el_searchengine'),"","","AND searchengine<>''");

  # Search terms
  whs_GetDataQuery2("search",__('Top search terms','workhorsestat') ,(get_option('workhorsestat_el_search')=='') ? 20:get_option('workhorsestat_el_search'),"","","AND search<>''");

  # Top referrer
  whs_GetDataQuery2("referrer",__('Top referrers','workhorsestat') ,(get_option('workhorsestat_el_referrer')=='') ? 10:get_option('workhorsestat_el_referrer'),"","","AND referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'");

  # Languages
  whs_GetDataQuery2("nation",__('Countries','workhorsestat').'/'.__('Languages','workhorsestat') ,(get_option('workhorsestat_el_languages')=='') ? 20:get_option('workhorsestat_el_languages'),"","","AND nation<>'' AND spider=''");

  # Spider
  whs_GetDataQuery2("spider",__('Spiders','workhorsestat') ,(get_option('workhorsestat_el_spiders')=='') ? 10:get_option('workhorsestat_el_spiders'),"","","AND spider<>''");

  # Top Pages
  whs_GetDataQuery2("urlrequested",__('Top pages','workhorsestat') ,(get_option('workhorsestat_el_pages')=='') ? 5:get_option('workhorsestat_el_pages'),"","urlrequested","AND feed='' and spider=''");

  # Top Days - Unique visitors
  whs_GetDataQuery2("date",__('Top days','workhorsestat').' - '.__('Unique visitors','workhorsestat') ,(get_option('workhorsestat_el_visitors')=='') ? 5:get_option('workhorsestat_el_visitors'),"distinct","ip","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */

  # Top Days - Pageviews
  whs_GetDataQuery2("date",__('Top days','workhorsestat').' - '.__('Pageviews','workhorsestat'),(get_option('workhorsestat_el_daypages')=='') ? 5:get_option('workhorsestat_el_daypages'),"","urlrequested","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */

  # Top IPs - Pageviews
  whs_GetDataQuery2("ip",__('Top IPs','workhorsestat').' - '.__('Pageviews','workhorsestat'),(get_option('workhorsestat_el_ippages')=='') ? 5:get_option('workhorsestat_el_ippages'),"","urlrequested","AND feed='' and spider=''"); /* Maddler 04112007: required patching iriValueTable */
}
?>
