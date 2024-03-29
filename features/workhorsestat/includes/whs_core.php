<?php

/**
 * Display data in table extracted from the given query
 *
 * @param fld GROUP BY argument of query
 * @param fldtitle title of field
 * @param limit quantity of elements to extract
 * @param param extra arguemnt for query (like DISTINCT)
 * @param queryfld field of query
 * @param exclude WHERE argument of query
 * @param print TRUE if the table is to print in page
 * @return return the HTML output accoding to the sprint state
 */
function whs_GetDataQuery2($fld, $fldtitle, $limit = 0, $param = "", $queryfld = "", $exclude= "", $print = TRUE) {
  global $wpdb;
  $table_name = whs_TABLENAME;

  if ($queryfld == '') {
    $queryfld = $fld;
  }

  $text .= "<div class='wrap'>
						<table class='widefat'>
						<thead>
							<tr>
								<th scope='col' class='keytab-head'><h2>$fldtitle</h2></th>
								<th scope='col' style='width:20%;text-align:center;'>".__('Visits','newstatpress')."</th>
							</tr>
						</thead>\n";

  $rks = $wpdb->get_var("
     SELECT count($param $queryfld) as rks
     FROM $table_name
     WHERE 1=1 $exclude;
  ");

  if($rks > 0) {
    $sql="
      SELECT count($param $queryfld) as pageview, $fld
      FROM $table_name
      WHERE 1=1 $exclude
      GROUP BY $fld
      ORDER BY pageview DESC
    ";
    if($limit > 0) {
      $sql=$sql." LIMIT $limit";
    }
    $qry = $wpdb->get_results($sql);
    $tdwidth=450;

    // Collects data
    $data=array();
    foreach ($qry as $rk) {
      $pc=round(($rk->pageview*100/$rks),1);
      if($fld == 'nation') { $rk->$fld = strtoupper($rk->$fld); }
      if($fld == 'date') { $rk->$fld = whs_hdate($rk->$fld); }
      if($fld == 'urlrequested') { $rk->$fld = whs_DecodeURL($rk->$fld); }
      $data[substr($rk->$fld,0,250)]=$rk->pageview;
    }
  }

  // Draw table body
  $text .= "<tbody id='the-list'>";
  if($rks > 0) {  // Chart!

    if($fld == 'nation') { // Nation chart
      $charts=plugins_url('newstatpress')."/includes/geocharts.html".whs_GetGoogleGeo($data);
    }
    else { // Pie chart
      $charts=plugins_url('newstatpress')."/includes/piecharts.html".whs_GetGooglePie($fldtitle, $data);
    }

    foreach ($data as $key => $value) {
      $text .= "<tr><td class='keytab'>".$key."</td><td class='valuetab'>".$value."</td></tr>\n";
    }

    $text .= "<tr><td colspan=2 style='width:50%;'>
        <iframe src='".$charts."' class='framebox'>
          <p>[_e('This section requires a browser that supports iframes.]','newstatpress')</p>
        </iframe></td></tr>";
  }
  $text .= "</tbody>
	</table>
	</div>
	<br />\n";

  if ($print)
		print $text;
  else
		return $text;
}

/**
 * Get google url query for geo data
 *
 * @param data_array the array of data_array
 * @return the url with data
 */
function whs_GetGoogleGeo($data_array) {
  if(empty($data_array)) { return ''; }
  // get hash
  foreach($data_array as $key => $value ) {
    $values[] = $value;
    $labels[] = $key;
  }
  return "?cht=Country&chd=".(implode(",",$values))."&chlt=Popularity&chld=".(implode(",",$labels));
}

/**
 * Get google url query for pie data
 *
 * @param data_array the array of data_array
 * @param title the title to use
 * @return the url with data
 */
function whs_GetGooglePie($title, $data_array) {
  if(empty($data_array)) { return ''; }
  // get hash
  foreach($data_array as $key => $value ) {
    $values[] = $value;
    $labels[] = $key;
  }

  return "?title=".$title."&chd=".(implode(",",$values))."&chl=".urlencode(implode("|",$labels));
}

/**
 * Replace a content in page with NewStatPress output
 * Used format is: [NewStatPress: type]
 * Type can be:
 *  [NewStatPress: Overview]
 *  [NewStatPress: Top days]
 *  [NewStatPress: O.S.]
 *  [NewStatPress: Browser]
 *  [NewStatPress: Feeds]
 *  [NewStatPress: Search Engine]
 *  [NewStatPress: Search terms]
 *  [NewStatPress: Top referrer]
 *  [NewStatPress: Languages]
 *  [NewStatPress: Spider]
 *  [NewStatPress: Top Pages]
 *  [NewStatPress: Top Days - Unique visitors]
 *  [NewStatPress: Top Days - Pageviews]
 *  [NewStatPress: Top IPs - Pageviews]
 *
 * @param content the content of page
 ******************************************************/
function whs_Shortcode($content = '') {
  ob_start();
  $TYPEs = array();
  $TYPE = preg_match_all('/\[NewStatPress: (.*)\]/Ui', $content, $TYPEs);

  foreach ($TYPEs[1] as $k => $TYPE) {
					echo $TYPE;
    switch ($TYPE) {
      case "Overview":
				require_once ('api/whs_api_dashboard.php');
				$replacement=whs_ApiDashboard("HTML");
        break;
      case "Top days":
				$replacement=whs_GetDataQuery2("date", __('Top days','newstatpress') ,(get_option('newstatpress_el_top_days')=='') ? 5:get_option('newstatpress_el_top_days'), FALSE);
        // $replacement=whs_GetDataQuery2("date","Top days", (get_option('newstatpress_el_top_days')=='') ? 5:get_option('newstatpress_el_top_days'), FALSE);
        break;
      case "O.S.":
        $replacement=whs_GetDataQuery2("os",__('OSes','newstatpress'),(get_option('newstatpress_el_os')=='') ? 10:get_option('newstatpress_el_os'),"","","AND feed='' AND spider='' AND os<>''", FALSE);
        break;
      case "Browser":
        $replacement=whs_GetDataQuery2("browser",__('Browsers','newstatpress') ,(get_option('newstatpress_el_browser')=='') ? 10:get_option('newstatpress_el_browser'),"","","AND feed='' AND spider='' AND browser<>''", FALSE);
        break;
      case "Feeds":
        $replacement=whs_GetDataQuery2("feed",__('Feeds','newstatpress'), (get_option('newstatpress_el_feed')=='') ? 5:get_option('newstatpress_el_feed'),"","","AND feed<>''", FALSE);
        break;
      case "Search Engine":
        $replacement=whs_GetDataQuery2("searchengine",__('Search engines','newstatpress') ,(get_option('newstatpress_el_searchengine')=='') ? 10:get_option('newstatpress_el_searchengine'),"","","AND searchengine<>''", FALSE);
        break;
      case "Search terms":
        $replacement=whs_GetDataQuery2("search",__('Top search terms','newstatpress') ,(get_option('newstatpress_el_search')=='') ? 20:get_option('newstatpress_el_search'),"","","AND search<>''", FALSE);
        break;
      case "Top referrer":
        $replacement= whs_GetDataQuery2("referrer",__('Top referrers','newstatpress') ,(get_option('newstatpress_el_referrer')=='') ? 10:get_option('newstatpress_el_referrer'),"","","AND referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'", FALSE);
        break;
      case "Languages":
        $replacement=whs_GetDataQuery2("nation",__('Countries','newstatpress').'/'.__('Languages','newstatpress') ,(get_option('newstatpress_el_languages')=='') ? 20:get_option('newstatpress_el_languages'),"","","AND nation<>'' AND spider=''", FALSE);
        break;
      case "Spider":
        $replacement=whs_GetDataQuery2("spider",__('Spiders','newstatpress') ,(get_option('newstatpress_el_spiders')=='') ? 10:get_option('newstatpress_el_spiders'),"","","AND spider<>''", FALSE);
        break;
      case "Top Pages":
        $replacement=whs_GetDataQuery2("urlrequested",__('Top pages','newstatpress') ,(get_option('newstatpress_el_pages')=='') ? 5:get_option('newstatpress_el_pages'),"","urlrequested","AND feed='' and spider=''", FALSE);
        break;
      case "Top Days - Unique visitors":
        $replacement=whs_GetDataQuery2("date",__('Top days','newstatpress').' - '.__('Unique visitors','newstatpress') ,(get_option('newstatpress_el_visitors')=='') ? 5:get_option('newstatpress_el_visitors'),"distinct","ip","AND feed='' and spider=''", FALSE);
        break;
      case "Top Days - Pageviews":
        $replacement=whs_GetDataQuery2("date",__('Top days','newstatpress').' - '.__('Pageviews','newstatpress') ,(get_option('newstatpress_el_daypages')=='') ? 5:get_option('newstatpress_el_daypages'),"","urlrequested","AND feed='' and spider=''", FALSE);
        break;
      case "Top IPs - Pageviews":
        $replacement=whs_GetDataQuery2("ip",__('Top IPs','newstatpress').' - '.__('Pageviews','newstatpress') ,(get_option('newstatpress_el_ippages')=='') ? 5:get_option('newstatpress_el_ippages'),"","urlrequested","AND feed='' and spider=''", FALSE);
        break;
      default:
        $replacement="";
    }
    $content = str_replace($TYPEs[0][$k], $replacement, $content);
  }
  ob_get_clean();
  return $content;
}
add_filter('the_content', 'whs_Shortcode');

?>
