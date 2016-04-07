<?php

/**
 * Show statistics in dashboard
 *
 *******************************/
function whs_BuildDashboardWidget() {
  global $newstatpress_dir;

  $api_key=get_option('newstatpress_apikey');
  $newstatpress_url=PluginUrl();
  $url=$newstatpress_url."/includes/api/external.php";

  wp_register_script('wp_ajax_whs_js_dashbord', plugins_url('./js/whs_dashboard.js', __FILE__), array('jquery'));
  wp_enqueue_script('jquery');
  wp_enqueue_script('wp_ajax_whs_js_dashbord');
  wp_localize_script( 'wp_ajax_whs_js_dashbord', 'ExtData', array(
    'Url' => $url,
    'Key' => md5(gmdate('m-d-y H i').$api_key)
  ));


  ///whs_MakeOverview('dashboard');
  /*
    echo "<script type=\"text/javascript\">
           $.post(\"$url\", {
             VAR: \"dashboard\",
             KEY: \"".md5(gmdate('m-d-y H i').$api_key)."\",
             PAR: \"\",
             TYP: \"HTML\"
           },
           function(data,status){
             $( \"#whs_loader-dashboard\").hide();
             $( \"#whs_result-dashboard\" ).html( data );
           }, \"html\");
         </script>";
         */
    echo "<div id=\"whs_result-dashboard\"><img id=\"whs_loader-dashboard\" src=\"$newstatpress_url/images/ajax-loader.gif\"></div>";
  ?>
  <ul class='whs_dashboard'>
    <li>
      <a href='admin.php?page=whs_details'><?php _e('Details','newstatpress')?></a> |
    </li>
    <li>
      <a href='admin.php?page=whs_visits'><?php _e('Visits','newstatpress')?></a> |
    </li>
    <li>
      <a href='admin.php?page=whs_options'><?php _e('Options','newstatpress')?>
      </li>
  </ul>
  <?php
}

// Create the function use in the action hook
function whs_AddDashBoardWidget() {

  global $wp_meta_boxes;
  $title=__('NewStatPress Overview','newstatpress');

  //Add the dashboard widget if user option is 'yes'
  if (get_option('newstatpress_dashboard')=='checked')
    wp_add_dashboard_widget('dashboard_NewsStatPress_overview', $title, 'whs_BuildDashboardWidget');
  else unset($wp_meta_boxes['dashboard']['side']['core']['wp_dashboard_setup']);

}
?>
