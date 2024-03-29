<?php
function whs_DatabaseSearch($what='') {
  global $wpdb;
  $table_name = whs_TABLENAME;

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
      <a href="/wp-admin/admin.php?page=whs_search" class="nav-tab nav-tab-active">
        Search
      </a>
      <a href="/wp-admin/admin.php?page=whs_tools" class="nav-tab">
        Tools
      </a>
    </h2>
  </div>
  <?php

  $f['urlrequested']=__('URL Requested','workhorsestat');
  $f['agent']=__('Agent','workhorsestat');
  $f['referrer']=__('Referrer','workhorsestat');
  $f['search']=__('Search terms','workhorsestat');
  $f['searchengine']=__('Search engine','workhorsestat');
  $f['os']=__('Operative system','workhorsestat');
  $f['browser']=__('Browser','workhorsestat');
  $f['spider']=__('Spider','workhorsestat');
  $f['ip']=__('IP','workhorsestat');
?>
  <div class='wrap'><h2><?php _e('Search','workhorsestat'); ?></h2>
  <form method=get><table>
  <?php
    for($i=1;$i<=3;$i++) {
      print "<tr>";
      print "<td>".__('Field','workhorsestat')." <select name=where$i><option value=''></option>";
      foreach ( array_keys($f) as $k ) {
        print "<option value='$k'";
        if($_GET["where$i"] == $k) { print " SELECTED "; }
        print ">".$f[$k]."</option>";
      }
      print "</select></td>";
      if (isset($_GET["groupby$i"])) {
        // must only be a "checked" value if this is set
        print "<td><input type=checkbox name=groupby$i value='checked' "."checked"."> ".__('Group by','workhorsestat')."</td>";
      } else print "<td><input type=checkbox name=groupby$i value='checked' "."> ".__('Group by','workhorsestat')."</td>";

      if (isset($_GET["sortby$i"])) {
         // must only be a "checked" value if this is set
         print "<td><input type=checkbox name=sortby$i value='checked' "."checked"."> ".__('Sort by','workhorsestat')."</td>";
      } else print "<td><input type=checkbox name=sortby$i value='checked' "."> ".__('Sort by','workhorsestat')."</td>";

      print "<td>, ".__('if contains','workhorsestat')." <input type=text name=what$i value='".$_GET["what$i"]."'></td>";
      print "</tr>";
    }
?>
  </table>
  <br>
  <table>
   <tr>
     <td>
       <table>
         <tr><td><input type=checkbox name=oderbycount value=checked <?php print esc_html($_GET['oderbycount']) ?>> <?php _e('sort by count if grouped','workhorsestat'); ?></td></tr>
         <tr><td><input type=checkbox name=spider value=checked <?php print esc_html($_GET['spider']) ?>> <?php _e('include spiders/crawlers/bot','workhorsestat'); ?></td></tr>
         <tr><td><input type=checkbox name=feed value=checked <?php print esc_html($_GET['feed']) ?>> <?php _e('include feed','workhorsestat'); ?></td></tr>
       </table>
     </td>
     <td width=15> </td>
     <td>
       <table>
         <tr>
           <td><?php _e('Limit results to','workhorsestat'); ?>
             <select name=limitquery><?php if($_GET['limitquery'] >0) { print "<option>".esc_html($_GET['limitquery'])."</option>";} ?><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option></select>
           </td>
         </tr>
         <tr><td>&nbsp;</td></tr>
         <tr>
          <td align=right><input class='button button-primary' type=submit value=<?php _e('Search','workhorsestat'); ?> name=searchsubmit></td>
         </tr>
       </table>
     </td>
    </tr>
   </table>
   <input type=hidden name=page value='whs_search'>
   <input type=hidden name=workhorsestat_action value=search>
  </form>

  <br>
<?php

 if(isset($_GET['searchsubmit'])) {
   # query builder
   $qry="";
   # FIELDS
   $fields="";
   for($i=1;$i<=3;$i++) {
     if($_GET["where$i"] != '') {       
       $where_i=$_GET["where$i"];
       if (!array_key_exists($where_i, $f)) $where_i=''; // prevent to use not valid values
       $fields.=$where_i.',';
     }
   }
   $fields=rtrim($fields,",");
   # WHERE
   $where="WHERE 1=1";

   if (!isset($_GET['spider'])) { $where.=" AND spider=''"; }
   else if($_GET['spider'] != 'checked') { $where.=" AND spider=''"; }

   if (!isset($_GET['feed'])) { $where.=" AND feed=''"; }
   else if($_GET['feed'] != 'checked') { $where.=" AND feed=''"; }

   for($i=1;$i<=3;$i++) {   
     if(($_GET["what$i"] != '') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         $what_i=esc_sql($_GET["what$i"]);
         $where.=" AND ".$where_i." LIKE '%".$what_i."%'";
       }  
     }
   }
   # ORDER BY
   $orderby="";
   for($i=1;$i<=3;$i++) {
     if (isset($_GET["sortby$i"]) && ($_GET["sortby$i"] == 'checked') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         $orderby.=$where_i.',';
       }  
     }
   }

   # GROUP BY
   $groupby="";
   for($i=1;$i<=3;$i++) {
     if(isset($_GET["groupby$i"]) && ($_GET["groupby$i"] == 'checked') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         $groupby.=$where_i.',';
       }
     }
   }
   if($groupby != '') {
     $groupby="GROUP BY ".rtrim($groupby,',');
     $fields.=",count(*) as totale";
     if(isset($_GET["oderbycount"]) && $_GET['oderbycount'] == 'checked') { $orderby="totale DESC,".$orderby; }
   }

   if($orderby != '') { $orderby="ORDER BY ".rtrim($orderby,','); }

   $limit_num=intval($_GET['limitquery']); // force to use integer
   $limit="LIMIT ".$limit_num;

   # Results
   print "<h2>".__('Results','workhorsestat')."</h2>";
   $sql="SELECT $fields FROM $table_name $where $groupby $orderby $limit;";
   //print "$sql<br>";
   print "<table class='widefat'><thead><tr>";
   for($i=1;$i<=3;$i++) {
     $where_i=strip_tags($_GET["where$i"]);
     if($where_i != '') { print "<th scope='col'>".ucfirst(htmlspecialchars($where_i, ENT_COMPAT, 'UTF-8'))."</th>"; }
   }
   if($groupby != '') { print "<th scope='col'>".__('Count','workhorsestat')."</th>"; }
     print "</tr></thead><tbody id='the-list'>";
     $qry=$wpdb->get_results($sql,ARRAY_N);
     foreach ($qry as $rk) {
       print "<tr>";
       for($i=1;$i<=3;$i++) {
         print "<td>";
         if($_GET["where$i"] == 'urlrequested') { print whs_DecodeURL($rk[$i-1]); }
         else { if(isset($rk[$i-1])) print $rk[$i-1]; }
         print "</td>";
       }
         print "</tr>";
     }
     print "</table>";
     print "<br /><br /><font size=1 color=gray>sql: $sql</font></div>";
  }
}
?>
