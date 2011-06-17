<?php
  /*
   Plugin Name: Daily Stat
   Plugin URI: http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/
   Description: Improved real time stats for your blog
   Version: 1.0.1
   Author: luciole135
   Author URI: http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/
   */
  
  $_DAILYSTAT['version'] = '1.0.1';
  $_DAILYSTAT['feedtype'] = '';
  
  
  function iri_add_pages()
  {
      // Create table if it doesn't exist
      global $wpdb;
      $table_name = $wpdb->prefix. 'dailystat';
      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
          iri_dailystat_CreateTable();
      
      // add submenu
      $mincap = get_option('dailystat_mincap');
      if ($mincap == '')
          $mincap = 'level_8';


      add_menu_page('Daily stat', 'Daily stat', $mincap, __FILE__, 'iridailystat',WP_CONTENT_URL .'/plugins/daily/images/stat.png');
      add_submenu_page(__FILE__, __('Visitor Spy', 'dailystat'), __('Visitor Spy', 'dailystat'), $mincap,'dailystat/action=spyvisitors', 'luc_dailystatSpyvisitors');
	  add_submenu_page(__FILE__, __('Bot Spy', 'dailystat'), __('Bot Spy', 'dailystat'), $mincap,'dailystat/action=spybot', 'luc_dailystatSpybot');
	  add_submenu_page(__FILE__, __('Yesterday ', 'dailystat'), __('Yesterday ', 'dailystat'), $mincap,'dailystat/action=yesterday', 'luc_dailystat_yesterday');
	  add_submenu_page(__FILE__, __('Referrer', 'dailystat'), __('Referrer', 'dailystat'), $mincap, 'dailystat/action=referrer', 'luc_dailystat_referrer');
	  add_submenu_page(__FILE__, __('Statistics', 'dailystat'), __('Statistics', 'dailystat'), $mincap,'dailystat/action=details','iridailystatDetails');
      add_submenu_page(__FILE__, __('Options', 'dailystat'), __('Options', 'dailystat'), $mincap,'dailystat/action=options', 'iridailystatOptions');
      
  }
  
  function permalinksEnabled()
  {
      global $wpdb;
      
      $result = $wpdb->get_row('SELECT `option_value` FROM `' . $wpdb->prefix . 'options` WHERE `option_name` = "permalink_structure"');
      if ($result->option_value != '')
          return true;
      else
          return false;
  }
  
  function my_substr($str, $x, $y = 0)
  {
  	if($y == 0)
  		$y = strlen($str) - $x;
 	if(function_exists('mb_substr'))
 		return mb_substr($str, $x, $y);
 	else
 		return substr($str, $x, $y);
  }
  
  
  function iridailystat()
  { 
      if ($_GET['dailystat-action'] == 'spyvisitors')
          luc_dailystatSpy("spyvisitors");// Spy of visitors
	   elseif ($_GET['dailystat-action'] == 'spybot')
          luc_dailystatSpy("spybot");// Spy of bots
	   elseif ($_GET['dailystat-action'] == 'visitors')
          luc_dailystatArticles("visitors");
	   elseif ($_GET['dailystat-action'] == 'referrer')
          luc_dailystatArticles("referrer");
	  elseif ($_GET['dailystat-action'] == 'details')
          iridailystatDetails();
      elseif ($_GET['dailystat-action'] == 'options')
          iridailystatOptions();
      elseif ($_GET['dailystat-action'] == 'overview')
          iridailystatMain();
      else
          iridailystatMain();
  }
  
  function iridailystatOptions()
  {
      if ($_POST['saveit'] == 'yes')
      {
          update_option('dailystat_mincap', $_POST['dailystat_mincap']);
		  update_option('dailystat_number_display_ip_spy_visitor', $_POST['dailystat_number_display_ip_spy_visitor']);
          update_option('dailystat_number-display_visit_spy_visitor', $_POST['dailystat_number-display_visit_spy_visitor']);
          // update database too
          iri_dailystat_CreateTable();
          print "<br /><div class='updated'><p>" . __('Saved', 'dailystat') . "!</p></div>";
      }
      else
      {
?>
  <div class='wrap'><h2><?php
          _e('Options', 'dailystat');
?></h2>
  <form method=post><table width=100%>

  <tr><td><?php
          _e('Minimum capability to view stats', 'dailystat');
?>
  <select name="dailystat_mincap">
<?php
          iri_dropdown_caps(get_option('dailystat_mincap'));
?>
  </select> 
  <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank"><?php
          _e("more info", 'dailystat');
?></a>
  </td></tr>
 <tr><td>
  <h4><?php
          _e('Visitors spy : ', 'dailystat');
?></h4>
 <?php
            _e('IP displayed', 'dailystat');
?>
  <select name="dailystat_number_display_ip_spy_visitor">
  <option value="20" <?php
          if (get_option('dailystat_number_display_ip_spy_visitor') == 20)
              print "selected";
?>>20</option>
  <option value="50" <?php
          if (get_option('dailystat_number_display_ip_spy_visitor') == 50)
              print "selected";
?>>50</option>
  <option value="100" <?php
          if (get_option('dailystat_number_display_ip_spy_visitor') == 100)
              print "selected";
?>>100</option>
   </select></td></tr>
   <tr><td><?php
            _e('Visits per IP', 'dailystat');
?>
  <select name="dailystat_number-display_visit_spy_visitor">
  <option value="20" <?php
          if (get_option('dailystat_number-display_visit_spy_visitor') == 20)
              print "selected";
?>>20</option>
  <option value="50" <?php
          if (get_option('dailystat_number-display_visit_spy_visitor') == 50)
              print "selected";
?>>50</option>
  <option value="100" <?php
          if (get_option('dailystat_number-display_visit_spy_visitor') == 100)
              print "selected";
?>>100</option>
  </select></td></tr>

  
  <tr><td><br><input type=submit value="<?php
          _e('Save options', 'dailystat');
?>"></td></tr>
  </tr>
  </table>
  <input type=hidden name=saveit value=yes>
  <input type=hidden name=page value=dailystat><input type=hidden name=dailystat-action value=options>
  </form>
  </div>
<?php
          } // chiude saveit
      }
      
      function iri_dropdown_caps($default = false)
      {
          global $wp_roles;
          $role = get_role('administrator');
          foreach ($role->capabilities as $cap => $grant)
          {
              print "<option ";
              if ($default == $cap)
                  print "selected ";
              
              print ">$cap</option>";
          }
      }
      
      function iridailystatMain()
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          
          // OVERVIEW table
          $unique_color = "#114477";
          $web_color = "#3377B6";
          $rss_color = "#f38f36";
          $spider_color = "#83b4d8";
          
          $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
          $today = gmdate('Ymd', current_time('timestamp'));
          
          print "<div class='wrap'><h2>" . __('Overview', 'dailystat') . "</h2>";
          print "<table class='widefat'><thead><tr>
	<th scope='col'></th>
	<th scope='col'>". __('Yesterday','dailystat'). "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')-86400) ."</font></th>
	<th scope='col'>". __('Today','dailystat'). "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')) ."</font></th>
	</tr></thead>
	<tbody id='the-list'>";
          
          //###############################################################################################
		  // VISITORS ROW
		  //YESTERDAY
		   print "<tr><td><div style='background:$unique_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'dailystat') . "</td>";
          $qry_y = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors
                                   FROM $table_name
                                   WHERE feed='' AND spider=''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($yesterday) . "'");
          print "<td>" . $qry_y->visitors . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("SELECT count(DISTINCT ip) AS visitors
                                   FROM $table_name
                                   WHERE feed='' AND spider=''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($today) . "'");
          print "<td>" . $qry_t->visitors . "</td>\n";
          print "</tr>";
          
          //###############################################################################################
          // PAGEVIEWS ROW
           print "<tr><td><div style='background:$web_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews', 'dailystat') . "</td>";
          //YESTERDAY
          $qry_y = $wpdb->get_row("SELECT count(ip) as pageview
                                   FROM $table_name
                                   WHERE feed='' AND spider='' AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($yesterday) . "'");
          print "<td>" . $qry_y->pageview . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("SELECT count(ip) as pageview
                                   FROM $table_name
                                   WHERE feed='' AND spider='' AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($today) . "'");
          print "<td>" . $qry_t->pageview . "</td>\n";
          print "</tr>";
          //###############################################################################################
          // SPIDERS ROW
          print "<tr><td><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spiders', 'dailystat') . "</td>";
          //YESTERDAY
          $qry_y = $wpdb->get_row("SELECT count(ip) as spiders
                                   FROM $table_name
                                   WHERE feed='' AND spider<>''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($yesterday) . "'");
          print "<td>" . $qry_y->spiders . "</td>\n";
          
          //TODAY
          $qry_t = $wpdb->get_row("SELECT count(ip) as spiders
                                   FROM $table_name
                                   WHERE feed='' AND spider<>''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($today) . "'");
          print "<td>" . $qry_t->spiders . "</td>\n";
          print "</tr>";
          //###############################################################################################
          // FEEDS ROW
         print "<tr><td><div style='background:$rss_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Feeds', 'dailystat') . "</td>";
          $qry_y = $wpdb->get_row("SELECT count(ip) as feeds
                                   FROM $table_name
                                   WHERE feed<>'' AND spider=''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($yesterday) . "'");
          print "<td>" . $qry_y->feeds . "</td>\n";
          
          $qry_t = $wpdb->get_row("SELECT count(ip) as feeds
                                   FROM $table_name
                                   WHERE feed<>'' AND spider=''  AND ip IS NOT NULL AND date = '" . mysql_real_escape_string($today) . "'");
          print "<td>" . $qry_t->feeds . "</td>\n";
          print "</tr></table><br />\n\n";
   
             //###################################################################################################
          
             $querylimit = "LIMIT 20";
             // Tabella Last hits
             print "<div class='wrap'><h2>" . __('Last hits', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('IP', 'dailystat') . "</th><th scope='col'>" . __('Threat', 'dailystat') . "</th><th scope='col'>" . __('Domain', 'dailystat') . "</th><th scope='col'>" . __('Page', 'dailystat') . "</th><th scope='col'>" . __('OS', 'dailystat') . "</th><th scope='col'>" . __('Browser', 'dailystat') . "</th><th scope='col'>" . __('Feed', 'dailystat') . "</th></tr></thead>";
             print "<tbody id='the-list'>";
          
             $fivesdrafts = $wpdb->get_results("SELECT * FROM $table_name WHERE (os<>'' OR feed<>'')  AND ip IS NOT NULL order by id DESC $querylimit");
             foreach ($fivesdrafts as $fivesdraft)
            {
              print "<tr>";
              print "<td>" . irihdate($fivesdraft->date) . "</td>";
              print "<td>" . $fivesdraft->time . "</td>";
              print "<td>" . $fivesdraft->ip . "</td>";
              print "<td>" . $fivesdraft->threat_score;
              if ($fivesdraft->threat_score > 0)
              {
                  print "/";
                  if ($fivesdraft->threat_type == 0)
                      print "Sp"; // Spider
                  else
                  {
                      if (($fivesdraft->threat_type & 1) == 1)
                          print "S"; // Suspicious
                      if (($fivesdraft->threat_type & 2) == 2)
                          print "H"; // Harvester
                      if (($fivesdraft->threat_type & 4) == 4)
                          print "C"; // Comment spammer
                  }
              }
              print "<td>" . $fivesdraft->nation . "</td>";
              print "<td>" . iri_dailystat_Abbrevia(iri_dailystat_Decode($fivesdraft->urlrequested), 30) . "</td>";
              print "<td>" . $fivesdraft->os . "</td>";
              print "<td>" . $fivesdraft->browser . "</td>";
              print "<td>" . $fivesdraft->feed . "</td>";
              print "</tr>";
            }
             print "</table></div>";
          
          // Last Search terms
          print "<div class='wrap'><h2>" . __('Last search terms', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('Terms', 'dailystat') . "</th><th scope='col'>" . __('Engine', 'dailystat') . "</th><th scope='col'>" . __('Result', 'dailystat') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested,search,searchengine FROM $table_name WHERE search<>''  AND ip IS NOT NULL ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . urldecode($rk->search) . "</a></td><td>" . $rk->searchengine . "</td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'dailystat') . "</a></td></tr>\n";
          }
          print "</table></div>";
          
          // Referrer
          print "<div class='wrap'><h2>" . __('Last referrers', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('URL', 'dailystat') . "</th><th scope='col'>" . __('Result', 'dailystat') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested FROM $table_name WHERE ((referrer NOT LIKE '" . get_option('home') . "%') AND (referrer <>'') AND (searchengine='') AND ip IS NOT NULL ) ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . iri_dailystat_Abbrevia($rk->referrer, 80) . "</a></td><td><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'dailystat') . "</a></td></tr>\n";
          }
          print "</table></div>";
          
          // Last pages
          print "<div class='wrap'><h2>" . __('Last pages', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('Page', 'dailystat') . "</th><th scope='col'>" . __('What', 'dailystat') . "</th></tr></thead>";
          print "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,urlrequested,os,browser,spider FROM $table_name WHERE spider='' AND feed='' AND ip IS NOT NULL ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td><td>" . $rk->time . "</td><td>" . iri_dailystat_Abbrevia(iri_dailystat_Decode($rk->urlrequested), 60) . "</td><td> " . $rk->os . " " . $rk->browser . " " . $rk->spider . "</td></tr>\n";
          }
          print "</table></div>";
          
          // Last Spiders
          print "<div class='wrap'><h2>" . __('Last spiders', 'dailystat') . "</h2>";
          print "<table class='widefat'><thead><tr>";
          print "<th scope='col'>" . __('Date', 'dailystat') . "</th>";
          print "<th scope='col'>" . __('Time', 'dailystat') . "</th>";
          print "<th scope='col'>" . __('Spider', 'dailystat') . "</th>";
          print "<th scope='col'>" . __('Page', 'dailystat') . "</th>";
          print "<th scope='col'>" . __('Agent', 'dailystat') . "</th>";
          print "</tr></thead><tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,agent,spider,urlrequested,agent FROM $table_name WHERE spider<>'' AND ip IS NOT NULL  ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              print "<tr><td>" . irihdate($rk->date) . "</td>";
              print "<td>" . $rk->time . "</td>";
              print "<td>" . $rk->spider . "</td>";
              print "<td>" . iri_dailystat_Abbrevia(iri_dailystat_Decode($rk->urlrequested), 30) . "</td>";
              print "<td> " . $rk->agent . "</td></tr>\n";
          }
          print "</table></div>";
          print "<br />";
          print "&nbsp;<i>" . __('dailystat table size', 'dailystat') . ": <b>" . iritablesize($wpdb->prefix. 'dailystat') . "</b></i><br />";
          print "&nbsp;<i>" . __('dailystat current time', 'dailystat') . ": <b>" . current_time('mysql') . "</b></i><br />";
          print "&nbsp;<i>" . __('RSS2 url', 'dailystat') . ": <b>" . get_bloginfo('rss2_url') . ' (' . iri_dailystat_extractfeedreq(get_bloginfo('rss2_url')) . ")</b></i><br />";
          print "&nbsp;<i>" . __('ATOM url', 'dailystat') . ": <b>" . get_bloginfo('atom_url') . ' (' . iri_dailystat_extractfeedreq(get_bloginfo('atom_url')) . ")</b></i><br />";
          print "&nbsp;<i>" . __('RSS url', 'dailystat') . ": <b>" . get_bloginfo('rss_url') . ' (' . iri_dailystat_extractfeedreq(get_bloginfo('rss_url')) . ")</b></i><br />";
          print "&nbsp;<i>" . __('COMMENT RSS2 url', 'dailystat') . ": <b>" . get_bloginfo('comments_rss2_url') . ' (' . iri_dailystat_extractfeedreq(get_bloginfo('comments_rss2_url')) . ")</b></i><br />";
          print "&nbsp;<i>" . __('COMMENT ATOM url', 'dailystat') . ": <b>" . get_bloginfo('comments_atom_url') . ' (' . iri_dailystat_extractfeedreq(get_bloginfo('comments_atom_url')) . ")</b></i><br />";
      }
      
 function iridailystatDetails()
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          
          $querylimit = "LIMIT 10";
           // Top Pages
          iriValueTable("urlrequested", __('Top pages', 'dailystat'), 5, "", "urlrequested", "AND feed='' and spider=''");
		  
		   // Search terms
          iriValueTable("search", __('Top search terms', 'dailystat'), 20, "", "", "AND search<>''");
          
          // Top referrer
          iriValueTable("referrer", __('Top referrer', 'dailystat'), 10, "", "", "AND referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%'");
		  
          // O.S.
          iriValueTable("os", __('O.S.', 'dailystat'), 0, "", "", "AND feed='' AND spider='' AND os<>''");
          
          // Browser
          iriValueTable("browser", __('Browser', 'dailystat'), 0, "", "", "AND feed='' AND spider='' AND browser<>''");
          
          // Feeds
          iriValueTable("feed", __('Feeds', 'dailystat'), 5, "", "", "AND feed<>''");
          
          // SE
          iriValueTable("searchengine", __('Search engines', 'dailystat'), 10, "", "", "AND searchengine<>''");
          
          // Countries
          iriValueTable("nation", __('Countries (domains)', 'dailystat'), 10, "", "", "AND nation<>'' AND spider=''");
          
          // Spider
          iriValueTable("spider", __('Spiders', 'dailystat'), 10, "", "", "AND spider<>''");
          
  
          /* Maddler 04112007: required patching iriValueTable */
      }
	  function luc_dailystatSpyvisitors()
	  {
	  luc_dailystatSpy("spyvisitors");
	  }
      function luc_dailystatSpybot()
	  {
	  luc_dailystatSpy("spybot");
	  }
	  
      function luc_dailystatSpy($action)
      {   global $wpdb;
          $table_name = $wpdb->prefix . "dailystat";
		  // number of IP or bot by page
		  $LIMIT = get_option('dailystat_number_display_ip_spy_visitor');
		  $LIMIT_PROOF = get_option('dailystat_number-display_visit_spy_visitor');
            if ($LIMIT == 0)
              $LIMIT = 20;
			if ($LIMIT_PROOF == 0)
              $LIMIT_PROOF = 20;
          if ($action=="spybot") // Bot Spy 
		      {$LIMIT = 10;
               $LIMIT_PROOF = 30;
			   }
          if(isset($_GET['pp']))
          { 
          	$pagePeriode = $_GET['pp'];// Get Current page from URL
          	if($pagePeriode <= 0) // Page is less than 0 then set it to 1
          	   $pagePeriode = 1;
          }
          else  // URL does not show the page set it to 1
          	$pagePeriode = 1;
          
          	// Create MySQL Query String
			if ($action=="spyvisitors") 
		          $strqry = "SELECT id FROM $table_name WHERE spider='' GROUP BY ip";
				 
		    else  $strqry = "SELECT id FROM $table_name WHERE spider<>'' GROUP BY spider";
			     
			$query = $wpdb->get_results($strqry);
			
			$TOTALROWS = $wpdb->num_rows;
			$NumOfPages = $TOTALROWS / $LIMIT;
			$LimitValue = ($pagePeriode * $LIMIT) - $LIMIT;
			
          if ($action=="spyvisitors") 
		   // Visitor Spy
		    {print "<div class='wrap'><h2>" . __('Visitor Spy', 'dailystat') . "</h2>";
			$sql = "SELECT ip,nation,os,browser,agent,max(id) as MaxId FROM $table_name WHERE spider='' GROUP BY ip ORDER BY MaxId DESC LIMIT $LimitValue, $LIMIT";
			}
		  else 
		  // Bot Spy
		    {print "<div class='wrap'><h2>" . __('Bot Spy', 'dailystat') . "</h2>";
			$sql = "SELECT spider,max(id) as MaxId FROM $table_name WHERE spider<>'' GROUP BY spider ORDER BY MaxId DESC LIMIT $LimitValue, $LIMIT";
			}
		  // selection of visit, group by ip, order by most recently visit (last id in the table)
		  
          $qry = $wpdb->get_results($sql);
?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<div align="center">
<div id="paginating" align="center">Pages:
<?php
luc_printperiodelink($NumOfPages,$pagePeriode,$action);
?>
</div>
<table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">
<?php
          foreach ($qry as $rk)
          {   print "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";
		   if ($action=="spyvisitors")
		    {// Visitor Spy
   			 
              print "<IMG SRC='http://api.hostip.info/flag.php?ip=" . $rk->ip . "' border=0 width=18 height=12>";
              print " <strong><span><font size='2' color='#7b7b7b'>" . $rk->ip . "</font></span></strong> ";
              print "<span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;' onClick=ttogle('" . $rk->ip . "');>" . __('more info', 'dailystat') . "</span></div>";
              print "<div id='" . $rk->ip . "' name='" . $rk->ip . "'>" . $rk->os . ", " . $rk->browser;
              //    print "<br><iframe style='overflow:hide;border:0px;width:100%;height:15px;font-family:helvetica;paddng:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://showip.fakap.net/txt/".$rk->ip."></iframe>";
              print "<br><iframe style='overflow:hide;border:0px;width:100%;height:40px;font-family:helvetica;paddng:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=" . $rk->ip . "></iframe>";
              if ($rk->nation)
                  print "<br><small>" . gethostbyaddr($rk->ip) . "</small>";
              
              print "<br><small>" . $rk->agent . "</small>";
              print "</div>";
              print "<script>document.getElementById('" . $rk->ip . "').style.display='none';</script>";
              print "</td></tr>";
			  $qry2 = $wpdb->get_results("SELECT * FROM $table_name WHERE ip='" . $rk->ip . "' order by id DESC LIMIT $LIMIT_PROOF");
			}
			else 
			{ // Bot Spy
              print " <strong><span><font size='4' color='#7b7b7b'>" . $rk->spider . "</font></span></strong> "; 
              print "</td></tr>";
			  $qry2 = $wpdb->get_results("SELECT date,time,urlrequested FROM $table_name WHERE spider='" . $rk->spider . "' order by id DESC LIMIT $LIMIT_PROOF");
			}
			  
              foreach ($qry2 as $details)
              {   print "<tr>";
			      print "<td valign='top' width='170'><div><font size='1' color='#3B3B3B'><strong>" . irihdate($details->date) . " " . $details->time . "</strong></font></div></td>";
                  print "<td><div><a href='" . irigetblogurl() . ((strpos($details->urlrequested, 'index.php') === FALSE) ? $details->urlrequested : '') . "' target='_blank'>" . iri_dailystat_Decode($details->urlrequested) . "</a>";
			  if ($action=="spyvisitors")// Visitor Spy
		         {
                   if ($details->searchengine != '')
                      print "<br><small>" . __('arrived from', 'dailystat') . " <b>" . $details->searchengine . "</b> " . __('searching', 'dailystat') . " <a href='" . $details->referrer . "' target=_blank>" . urldecode($details->search) . "</a></small>";
                   elseif ($details->referrer != '' && strpos($details->referrer, get_option('home')) === false)
                      print "<br><small>" . __('arrived from', 'dailystat') . " <a href='" . $details->referrer . "' target=_blank>" . $details->referrer . "</a></small>";
                  }
				   print "</div></td>";
                   print "</tr>\n";
              }  
         }
?>		  
</table>
<?php
luc_printperiodelink($NumOfPages,$pagePeriode,$action);
?>
</div>
<?php
      }
	  
	  function luc_dailystat_yesterday()
	  {
	  luc_dailystatArticles("yesterday");
	  }
	  function luc_dailystat_referrer()
	  {
      luc_dailystatArticles("referrer");
	  }
	  
	  function luc_dailystatArticles($action)
	  {  global $wpdb;
         $table_name = $wpdb->prefix . "dailystat";
		 $unique_color = "#114477";
         $web_color = "#3377B6";
         $rss_color = "#f38f36";
         $spider_color = "#83b4d8";
		 $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
			
			// pa is the display pages Articles
			if(isset($_GET['pa']))
            { 
          	$pageArticles = $_GET['pa'];// Get Current page Articles from URL
          	if($pageArticles <= 0) // Article is less than 0 then set it to 1
          	   $pageArticles = 1;
            }
            else  // URL does not show the Article set it to 1
          	{$pageArticles = 1;}
			
			if ($action == "yesterday")
			$strqry = "SELECT  post_name
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
							 ON t.urlrequested LIKE CONCAT('%', p.post_name, '%' )
		                     WHERE p.post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 GROUP BY post_name
							 ";
			else  $strqry = "SELECT referrer
			                FROM $table_name 
                            WHERE referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%' AND searchengine='' 
						    GROUP BY referrer
						    ";					   
		    $query = $wpdb->get_results($strqry);
             
		    $TOTALROWSArticles = $wpdb->num_rows;
			$LIMITArticles = 50;
		    $NumberOfPagesArticles = $TOTALROWSArticles / $LIMITArticles;
		    $LimitValueArticles = ($pageArticles * $LIMITArticles) - $LIMITArticles;
			$query = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = 'permalink_structure'");
			$permalink = $query->option_value;
			$permalink = explode("%", $permalink);
			

			if ($action == "yesterday") 
		      {
				print "<div class='wrap'><h2>" . __('Yesterday ', 'dailystat')."</div>";
			   luc_printpagelink ($action,$NumberOfPagesArticles,$pageArticles);
			   
				print "<table class='widefat'><thead><tr>
	
	<th scope='col'>". __('page and post','dailystat'). "</th>
	<th scope='col'>". __('Views','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'>". __('Visitors','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'>". __('Feeds','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'>". __('Spider','dailystat'). "<br /><font size=1></font></th>
	</tr></thead>";
	            }
			else //* $action=="referrer"  
			   { print "<div class='wrap'><h2>" . __('Referrer ', 'dailystat')."</div>";
			   	 luc_printpagelink ($action,$NumberOfPagesArticles,$pageArticles);
			     print "<table class='widefat'><thead><tr>
	<th scope='col'>". __('URL','dailystat'). "</th>
	<th scope='col'>". __('number','dailystat'). "<br /><font size=1></font></th>
	</tr></thead>";
	            }

			  if ($action == 'yesterday') // sort post or page by most views
				    {$strqry="SELECT post_name, total, urlrequested
					       FROM
						   ((SELECT 'page_accueil' as post_name, count(ip) as total, urlrequested
							         FROM $wpdb->posts as p1
									 JOIN $table_name as t1 ON urlrequested ='' 
									 WHERE  post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
									 AND spider='' AND feed='' 
									 GROUP BY post_name)
                        UNION ALL
						(SELECT post_name, count(ip) as total, urlrequested
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 AND spider='' AND feed='' 
							 GROUP BY post_name) 
							) views		 
						GROUP BY post_name 
		                ORDER BY total DESC LIMIT $LimitValueArticles, $LIMITArticles";
						 $qry_visitors =$wpdb->get_results("SELECT  post_name, total
					       FROM
						   ((SELECT 'page_accueil' as post_name, count(DISTINCT ip) as total
							         FROM $wpdb->posts as p1
									 JOIN $table_name as t1 ON urlrequested ='' 
									 WHERE  post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
									 AND spider='' AND feed='' 
									 GROUP BY post_name)
                        UNION ALL
						(SELECT post_name, count(DISTINCT ip) as total
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 AND spider='' AND feed='' 
							 GROUP BY post_name) 
							) visitors		 
					    GROUP BY post_name");
				$qry_feeds=$wpdb->get_results("SELECT post_name, total
					     FROM
						   ((SELECT 'page_accueil' as post_name, count(ip) AS total
	                         FROM $table_name 
	                         WHERE (urlrequested LIKE '%".$permalink[0]."feed%' OR urlrequested LIKE '%".$permalink[0]."comment%') 
						     AND spider='' AND feed<>'' AND date = $yesterday
                             GROUP BY post_name)
                        UNION ALL
						    (SELECT post_name, count(ip) as total
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON t.urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE  p.post_status = 'publish' AND spider='' AND feed<>'' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 
							 GROUP BY post_name	)			
							) feeds		 
						GROUP BY post_name");	
				$qry_spiders =$wpdb->get_results("SELECT  post_name, total
					       FROM
						   ((SELECT 'page_accueil' as post_name, count(ip) as total
							         FROM $wpdb->posts as p1
									 JOIN $table_name as t1 ON urlrequested ='' 
									 WHERE  post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
									 AND spider<>'' AND feed='' 
									 GROUP BY post_name)
                        UNION ALL
						(SELECT post_name, count(ip) as total
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 AND spider<>'' AND feed='' 
							 GROUP BY post_name) 
							) visitors		 
					    GROUP BY post_name");
				   foreach ($qry_visitors as $url)
				   {$visitors[$url->post_name]=$url->total;
				   }
				   foreach ($qry_feeds as $url)
				   {$feeds[$url->post_name]=$url->total;
				   }
				   foreach ($qry_spiders as $url)
				   {$spiders[$url->post_name]=$url->total;
						}
						}
				else {$strqry = "SELECT count(referrer) as total, referrer
			                 FROM $table_name 
						     WHERE referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'  AND searchengine='' 
							  
						     GROUP BY referrer 
						     ORDER by total DESC LIMIT $LimitValueArticles, $LIMITArticles";
				      }
		      $query = $wpdb->get_results($strqry);
			 	   
	foreach ($query as $url)
		{ if ($action == "yesterday")
		   {   print "<tr><td>". get_bloginfo('url'). $url->urlrequested ."</td>";
			   print "<td>" . $url->total . "</td>\n";
			   print "<td>" . $visitors[$url->post_name] . "</td>\n";
			   print "<td>" . $feeds[$url->post_name] . "</td>\n";
			   print "<td>" . $spiders[$url->post_name]. "</td>\n";
			   print "</tr>";
		  }
		     // $action == referrer
			  else {print "<tr><td><h4><a href='" . $url->referrer. "' target='_blank'>$url->referrer</a></h4></td>";
					print "<td>" . $url->total . "</td>\n";
					}
		}	
            print '</tr></table>';
            print '</div>';	
		
	  luc_printpagelink ($action,$NumberOfPagesArticles,$pageArticles);
	  print '</div>';
	  }
	  
		 function luc_printperiodelink($a,$b,$action)
       {  // For all pages ($a) Display first 10 pages, 10 pages before current page($b), 10 pages after current page , each 25 pages and the 5 last pages for($action)
          
           $GUIL1 = FALSE;
           $GUIL2 = FALSE;// suspension points  not writed
           $N = ceil($a);
           for ($i = 1; $i <= $N; $i++) 
             if ($i <= $N)
               { // $page is not the last page
                if($i == $b)  
	               print " [{$i}] "; // $page is current page
	            else 
	            { // Not the current page Hyperlink them
	              if (($i <= 10) or (($i >= $b-10) and ($i <= $b+10)) or ($i >= $N-4) or is_int($i/25)) 
				  
				  { if ($action == "overview")
				      print '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/dailystat.php/&pp=' . $i .'">' . $i . '</a> ';
					else  
	                  print '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/action='.$action.'/&pp=' . $i . '&pa=1'.'">' . $i . '</a> ';
					  }
	              else 
	                 { if ($GUIL1 == FALSE) 
	                     {print "... "; $GUIL1 = TRUE;
		                  }
	              if (($i == $N-5) and ($GUIL2 == FALSE)) 
	                 { print " ... "; $GUIL2 = TRUE;
	                  } // suspension points writed
	                 }
	             }
                }
        }
		
	  function luc_printpagelink($action,$NumberOfPagesArticles,$pageArticles)		
        {  
			 // For all pages ($a$NumberOfPagesArticles) Display first 10 pages, 10 pages before current page($pageArticles), 10 pages after current page , 5 last pages 
            $GUIL1 = FALSE;// suspension points not writed
            $GUIL2 = FALSE;

            $NA = ceil($NumberOfPagesArticles);
		    print '<table width="100%" border="0"><tr></tr></table>';
			print "Pages and Posts : ";
			 
            for ($j = 1; $j <= $NA; $j++) 
            if ($j <= $NA)
              { // $i is not the last Articles page
               if($j == $pageArticles)  // $i is current page
	              print " [{$j}] ";
	           else { // Not the current page Hyperlink them
	                 if (($j <= 10) or (($j >= $pageArticles-10) and ($j <= $pageArticles+10)) or ($j >= $NA-5)or is_int($i/25)) 
					 print '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/action='.$action.'&pa='. $j . '">' . $j . '</a> ';
	                 else 
	                   { if ($GUIL1 == FALSE) 
					      {print "... "; $GUIL1 = TRUE;}
	                   if (($j == $pageArticles+11) and ($GUIL2 == FALSE)) 
					      { print " ... "; $GUIL2 = TRUE;}
	                    // suspension points writed
	                   }
	                 }
               }
		}
		
      function iri_dailystat_Abbrevia($s, $c)
      {
          $res = "";
          if (strlen($s) > $c)
              $res = "...";
          return my_substr($s, 0, $c) . $res;
      }
      
      function iri_dailystat_Where($ip)
      {
          $url = "http://api.hostip.info/get_html.php?ip=$ip";
          $res = file_get_contents($url);
          if ($res === false)
              return(array('', ''));
			  
          $res = str_replace("Country: ", "", $res);
          $res = str_replace("\nCity: ", ", ", $res);
          $nation = preg_split('/\(|\)/', $res);
          print "( $ip $res )";
          return(array($res, $nation[1]));
      }
      
      
      function iri_dailystat_Decode($out_url)
      {
      	if(!permalinksEnabled())
      	{
	          if ($out_url == '')
	              $out_url = __('Page', 'dailystat') . ": Home";
	          if (my_substr($out_url, 0, 4) == "cat=")
	              $out_url = __('Category', 'dailystat') . ": " . get_cat_name(my_substr($out_url, 4));
	          if (my_substr($out_url, 0, 2) == "m=")
	              $out_url = __('Calendar', 'dailystat') . ": " . my_substr($out_url, 6, 2) . "/" . my_substr($out_url, 2, 4);
	          if (my_substr($out_url, 0, 2) == "s=")
	              $out_url = __('Search', 'dailystat') . ": " . my_substr($out_url, 2);
	          if (my_substr($out_url, 0, 2) == "p=")
	          {
	              $post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
	              $out_url = $post_id_7['post_title'];
	          }
	          if (my_substr($out_url, 0, 8) == "page_id=")
	          {
	              $post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
	              $out_url = __('Page', 'dailystat') . ": " . $post_id_7['post_title'];
	          }
	        }
	        else
	        {
	        	if ($out_url == '')
	              $out_url = __('Page', 'dailystat') . ": Home";
	          else if (my_substr($out_url, 0, 9) == "category/")
	              $out_url = __('Category', 'dailystat') . ": " . get_cat_name(my_substr($out_url, 9));
	          else if (my_substr($out_url, 0, 2) == "s=")
	              $out_url = __('Search', 'dailystat') . ": " . my_substr($out_url, 2);
	          else if (my_substr($out_url, 0, 2) == "p=") // not working yet 
	          {
	              $post_id_7 = get_post(my_substr($out_url, 2), ARRAY_A);
	              $out_url = $post_id_7['post_title'];
	          }
	          else if (my_substr($out_url, 0, 8) == "page_id=") // not working yet
	          {
	              $post_id_7 = get_page(my_substr($out_url, 8), ARRAY_A);
	              $out_url = __('Page', 'dailystat') . ": " . $post_id_7['post_title'];
	          }
	        }
          return $out_url;
      }
      
      function iri_dailystat_URL()
      {
          $urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
          if ($urlRequested == "")
              // SEO problem!
              $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '');
          if (my_substr($urlRequested, 0, 2) == '/?')
              $urlRequested = my_substr($urlRequested, 2);
          if ($urlRequested == '/')
              $urlRequested = '';
          return $urlRequested;
      }
      
      function irigetblogurl()
      {
      	$prsurl = parse_url(get_bloginfo('url'));
      	return $prsurl['scheme'] . '://' . $prsurl['host'] . ((!permalinksEnabled()) ? $prsurl['path'] . '/?' : '');
      }
      
      // Converte da data us to default format di Wordpress
      function irihdate($dt = "00000000")
      {
          return mysql2date(get_option('date_format'), my_substr($dt, 0, 4) . "-" . my_substr($dt, 4, 2) . "-" . my_substr($dt, 6, 2));
      }
      
      function iritablesize($table)
      {
          global $wpdb;
          $res = $wpdb->get_results("SHOW TABLE STATUS LIKE '$table'");
          foreach ($res as $fstatus)
          {
              $data_lenght = $fstatus->Data_length;
              $data_rows = $fstatus->Rows;
          }
          return number_format(($data_lenght / 1024 / 1024), 2, ",", " ") . " MB ($data_rows records)";
      }
      
      function irirgbhex($red, $green, $blue)
      {
          $red = 0x10000 * max(0, min(255, $red + 0));
          $green = 0x100 * max(0, min(255, $green + 0));
          $blue = max(0, min(255, $blue + 0));
          // convert the combined value to hex and zero-fill to 6 digits
          return "#" . str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT);
      }
	  
      function iriValueTable($fld, $fldtitle, $limit = 0, $param = "", $queryfld = "", $exclude = "")
      {
          /* Maddler 04112007: param addedd */
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          
          if ($queryfld == '')
              $queryfld = $fld;
          print "<div class='wrap'><h2>$fldtitle</h2><table style='width:100%;padding:0px;margin:0px;' cellpadding=0 cellspacing=0><thead><tr><th style='width:400px;background-color:white;'></th><th style='width:150px;background-color:white;'><u>" . __('Visits', 'dailystat') . "</u></th><th style='background-color:white;'></th></tr></thead>";
          print "<tbody id='the-list'>";
          $rks = $wpdb->get_var("SELECT count($param $queryfld) as rks FROM $table_name WHERE 1=1 $exclude;");
          if ($rks > 0)
          {
              $sql = "SELECT count($param $queryfld) as pageview, $fld FROM $table_name WHERE 1=1 $exclude  GROUP BY $fld ORDER BY pageview DESC";
              if ($limit > 0)
                  $sql = $sql . " LIMIT $limit";
              $qry = $wpdb->get_results($sql);
              $tdwidth = 450;
              $red = 131;
              $green = 180;
              $blue = 216;
              $deltacolor = round(250 / count($qry), 0);
              //      $chl="";
              //      $chd="t:";
              foreach ($qry as $rk)
              {
                  $pc = round(($rk->pageview * 100 / $rks), 1);
                  if ($fld == 'date')
                      $rk->$fld = irihdate($rk->$fld);
                  if ($fld == 'urlrequested')
                      $rk->$fld = iri_dailystat_Decode($rk->$fld);
                  
                  if ($fld == 'search')
                  	$rk->$fld = urldecode($rk->$fld);
                  
                  print "<tr><td style='width:400px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>" . my_substr($rk->$fld, 0, 50);
                  if (strlen("$rk->fld") >= 50)
                      print "...";
                  print "</td><td style='text-align:center;'>" . $rk->pageview . "</td>";
                  print "<td><div style='text-align:right;padding:2px;font-family:helvetica;font-size:7pt;font-weight:bold;height:16px;width:" . number_format(($tdwidth * $pc / 100), 1, '.', '') . "px;background:" . irirgbhex($red, $green, $blue) . ";border-top:1px solid " . irirgbhex($red + 20, $green + 20, $blue) . ";border-right:1px solid " . irirgbhex($red + 30, $green + 30, $blue) . ";border-bottom:1px solid " . irirgbhex($red - 20, $green - 20, $blue) . ";'>$pc%</div>";
                  print "</td></tr>\n";
                  $red = $red + $deltacolor;
                  $blue = $blue - ($deltacolor / 2);
              }
          }
          print "</table>\n";
          print "</div>\n";
      }
      
      function iriDomain($ip)
      {
          $host = gethostbyaddr($ip);
          if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $host))
              return "";
          else
              return my_substr(strrchr($host, "."), 1);
      }
      
      function iriGetQueryPairs($url)
      {
          $parsed_url = parse_url($url);
          $tab = parse_url($url);
          $host = $tab['host'];
          if (key_exists("query", $tab))
          {
              $query = $tab["query"];
              $query = str_replace("&amp;", "&", $query);
              $query = urldecode($query);
              $query = str_replace("?", "&", $query);
              return explode("&", $query);
          }
          else
          {
              return null;
          }
      }
      
      function iriGetOS($arg)
      {
          $arg = str_replace(" ", "", $arg);
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/os.dat');
          foreach ($lines as $line_num => $os)
          {
              list($nome_os, $id_os) = explode("|", $os);
              if (strpos($arg, $id_os) === false)
                  continue;
              // riconosciuto
              return $nome_os;
          }
          return '';
      }
      
      function iriGetBrowser($arg)
      {
          $arg = str_replace(" ", "", $arg);
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/browser.dat');
          foreach ($lines as $line_num => $browser)
          {
              list($nome, $id) = explode("|", $browser);
              if (strpos($arg, $id) === false)
                  continue;
              // riconosciuto
              return $nome;
          }
          return '';
      }
      
	  function iriCheckBanIP($arg)
      {
          if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/banips.dat'))
              $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/banips.dat');
          else
              $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/banips.dat');
         
        if ($lines !== false)
        {
            foreach ($lines as $banip)
              {
               if (@preg_match('/^' . rtrim($banip, "\r\n") . '$/', $arg))
                   return true;
                  // riconosciuto, da scartare
              }
          }
          return false;
      }
      
      function iriGetSE($referrer = null)
      {
          $key = null;
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/searchengines.dat');
          foreach ($lines as $line_num => $se)
          {
              list($nome, $url, $key) = explode("|", $se);
              if (strpos($referrer, $url) === false)
                  continue;
              // trovato se
              $variables = iriGetQueryPairs($referrer);
              $i = count($variables);
              while ($i--)
              {
                  $tab = explode("=", $variables[$i]);
                  if ($tab[0] == $key)
                      return($nome . "|" . urlencode($tab[1]));
              }
          }
          return null;
      }
      
      function iriGetSpider($agent = null)
      {
          $agent = str_replace(" ", "", $agent);
          $key = null;
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/spider.dat');
          if (file_exists(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'))
              $lines = array_merge($lines, file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '-custom/spider.dat'));
          foreach ($lines as $line_num => $spider)
          {
              list($nome, $key) = explode("|", $spider);
              if (strpos($agent, $key) === false)
                  continue;
              // trovato
              return $nome;
          }
          return null;
      }
      
      function iri_dailystat_CreateTable()
      {
          global $wpdb;
          global $wp_db_version;
          $table_name = $wpdb->prefix. 'dailystat';
          $sql_createtable = "CREATE TABLE " . $table_name . " (
  id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
  date TINYTEXT,
  time TINYTEXT,
  ip TINYTEXT,
  urlrequested TEXT,
  agent TEXT,
  referrer TEXT,
  search TEXT,
  nation TINYTEXT,
  os TINYTEXT,
  browser TINYTEXT,
  searchengine TINYTEXT,
  spider TINYTEXT,
  feed TINYTEXT,
  user TINYTEXT,
  timestamp TINYTEXT,
  threat_score SMALLINT,
  threat_type SMALLINT,
  UNIQUE KEY id (id)
  );";
          if ($wp_db_version >= 5540)
              $page = 'wp-admin/includes/upgrade.php';
          else
              $page = 'wp-admin/upgrade-functions.php';
          require_once(ABSPATH . $page);
          dbDelta($sql_createtable);
      }
      
function iri_dailystat_is_feed($url) {
   if (stristr($url,get_bloginfo('comments_atom_url')) != FALSE) { return 'COMMENT ATOM'; }
   elseif (stristr($url,get_bloginfo('comments_rss2_url')) != FALSE) { return 'COMMENT RSS'; }
   elseif (stristr($url,get_bloginfo('rdf_url')) != FALSE) { return 'RDF'; }
   elseif (stristr($url,get_bloginfo('atom_url')) != FALSE) { return 'ATOM'; }
   elseif (stristr($url,get_bloginfo('rss_url')) != FALSE) { return 'RSS'; }
   elseif (stristr($url,get_bloginfo('rss2_url')) != FALSE) { return 'RSS2'; }
   elseif (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
   elseif (stristr($url,'/feed') != FALSE) { return 'RSS2'; }
   return '';
}

function iri_dailystat_extractfeedreq($url)
{
		if(!strpos($url, '?') === FALSE)
		{
        list($null, $q) = explode("?", $url);
    	list($res, $null) = explode("&", $q);
        }
    else
    {
    	$prsurl = parse_url($url);
    	$res = $prsurl['path'] . $$prsurl['query'];
    }
    
    return $res;
}
      
      function iriStatAppend()
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          global $userdata;
          global $_dailystat;
          get_currentuserinfo();
          $feed = '';
          
          // Time
          $timestamp = current_time('timestamp');
          $vdate = gmdate("Ymd", $timestamp);
          $vtime = gmdate("H:i:s", $timestamp);
          
          // IP
          $ipAddress = $_SERVER['REMOTE_ADDR'];
          if (iriCheckBanIP($ipAddress) === true)
              return '';
          
          // Determine Threats if http:bl installed
          $threat_score = 0;
          $threat_type = 0;
          $httpbl_key = get_option("httpbl_key");
          if ($httpbl_key !== false)
          {
              $result = explode( ".", gethostbyname( $httpbl_key . "." .
                  implode ( ".", array_reverse( explode( ".",
                  $ipAddress ) ) ) .
                  ".dnsbl.httpbl.org" ) );
              // If the response is positive
              if ($result[0] == 127)
              {
                  $threat_score = $result[2];
                  $threat_type = $result[3];
              }
          }
          
          // URL (requested)
          $urlRequested = iri_dailystat_URL();
          if (eregi(".ico$", $urlRequested))
              return '';
          if (eregi("favicon.ico", $urlRequested))
              return '';
          if (eregi(".css$", $urlRequested))
              return '';
          if (eregi(".js$", $urlRequested))
              return '';
          if (stristr($urlRequested, "/wp-content/plugins") != false)
              return '';
          if (stristr($urlRequested, "/wp-content/themes") != false)
              return '';
          
          $referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
          $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
          $spider = iriGetSpider($userAgent);
          
          if (($spider != '') and (get_option('dailystat_donotcollectspider') == 'checked'))
              return '';
          
          if ($spider != '')
          {
              $os = '';
              $browser = '';
          }
          else
          {
              // Trap feeds
              $prsurl = parse_url(get_bloginfo('url'));
              $feed = iri_dailystat_is_feed($prsurl['scheme'] . '://' . $prsurl['host'] . $_SERVER['REQUEST_URI']);
              // Get OS and browser
              $os = iriGetOS($userAgent);
              $browser = iriGetBrowser($userAgent);
              list($searchengine, $search_phrase) = explode("|", iriGetSE($referrer));
          }
          // Auto-delete visits older than yesterday...
              $today = gmdate('Ymd', current_time('timestamp'));
			  if ($today <> get_option('today')) 
			  {$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
			   $results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $yesterday . "'");
               $results  = $wpdb->query('OPTIMIZE TABLE '. $table_name);
			   update_option('today', $today);
			  };
              
          if ((!is_user_logged_in()) or (get_option('dailystat_collectloggeduser') == 'checked'))
          {
              if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
                  iri_dailystat_CreateTable();
              
              $insert = "INSERT INTO " . $table_name . " (date, time, ip, urlrequested, agent, referrer, search,nation,os,browser,searchengine,spider,feed,user,threat_score,threat_type,timestamp) " . "VALUES ('$vdate','$vtime','$ipAddress','" . mysql_real_escape_string($urlRequested) . "','" . mysql_real_escape_string(strip_tags($userAgent)) . "','" . mysql_real_escape_string($referrer) . "','" . mysql_real_escape_string(strip_tags($search_phrase)) . "','" . iriDomain($ipAddress) . "','" . mysql_real_escape_string($os) . "','" . mysql_real_escape_string($browser) . "','$searchengine','$spider','$feed','$userdata->user_login',$threat_score,$threat_type,'$timestamp')";
              $results = $wpdb->query($insert);
          }
      }

	  
      function dailystat_Print($body = '')
      {
          print iri_dailystat_Vars($body);
      }
      
      function iri_dailystat_Vars($body)
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
		  $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
          $today = gmdate('Ymd', current_time('timestamp'));

          if (strpos(strtolower($body), "%visitstoday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE date = $today and spider='' and feed='';");
              $body = str_replace("%visitstoday%", $qry[0]->visitors, $body);
          }
		   if (strpos(strtolower($body), "%visitsyesterday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE date = $yesterday and spider='' and feed='';");
              $body = str_replace("%visitsyesterday%", $qry[0]->visitors, $body);
          }
		   if (strpos(strtolower($body), "%pageviewstoday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE date = $today and spider='' and feed='';");
              $body = str_replace("%pageviewstoday%", $qry[0]->pageviews, $body);
          }
		   if (strpos(strtolower($body), "%pageviewsyesterday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE date = $yesterday and spider='' and feed='';");
              $body = str_replace("%pageviewsyesterday%", $qry[0]->pageviews, $body);
          }
          if (strpos(strtolower($body), "%thistodaypageviews%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(iri_dailystat_URL()) . "';");
              $body = str_replace("%thistodaypageviews%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thisyesterdaypageviews%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $yesterday AND urlrequested='" . mysql_real_escape_string(iri_dailystat_URL()) . "';");
              $body = str_replace("%thisyesterdaypageviews%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thistodayvisits%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(iri_dailystat_URL()) . "';");
              $body = str_replace("%thistodayvisits%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thisyesterdayvisits%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $yesterday AND urlrequested='" . mysql_real_escape_string(iri_dailystat_URL()) . "';");
              $body = str_replace("%thisyesterdayvisits%", $qry[0]->pageviews, $body);
          }
		  
          if (strpos(strtolower($body), "%os%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $os = iriGetOS($userAgent);
              $body = str_replace("%os%", $os, $body);
          }
          if (strpos(strtolower($body), "%browser%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $browser = iriGetBrowser($userAgent);
              $body = str_replace("%browser%", $browser, $body);
          }
          if (strpos(strtolower($body), "%ip%") !== false)
          {
              $ipAddress = $_SERVER['REMOTE_ADDR'];
              $body = str_replace("%ip%", $ipAddress, $body);
          }
          if (strpos(strtolower($body), "%visitorsonline%") !== false)
          {
              $to_time = current_time('timestamp');
              $from_time = strtotime('-4 minutes', $to_time);
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE spider='' and feed='' AND timestamp BETWEEN $from_time AND $to_time;");
              $body = str_replace("%visitorsonline%", $qry[0]->visitors, $body);
          }
          if (strpos(strtolower($body), "%usersonline%") !== false)
          {
              $to_time = current_time('timestamp');
              $from_time = strtotime('-4 minutes', $to_time);
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as users FROM $table_name WHERE spider='' and feed='' AND user<>'' AND timestamp BETWEEN $from_time AND $to_time;");
              $body = str_replace("%usersonline%", $qry[0]->users, $body);
          }
          if (strpos(strtolower($body), "%toppost%") !== false)
          {
              $qry = $wpdb->get_results("SELECT urlrequested, count(ip) as totale FROM $table_name WHERE spider='' AND feed='' AND urlrequested <>'' GROUP BY urlrequested ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%toppost%", iri_dailystat_Decode($qry[0]->urlrequested), $body);
          }
          if (strpos(strtolower($body), "%topbrowser%") !== false)
          {
              $qry = $wpdb->get_results("SELECT browser,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY browser ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topbrowser%", iri_dailystat_Decode($qry[0]->browser), $body);
          }
          if (strpos(strtolower($body), "%topos%") !== false)
          {
              $qry = $wpdb->get_results("SELECT os,count(id) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY os ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topos%", iri_dailystat_Decode($qry[0]->os), $body);
          }
         
      	  if (strpos(strtolower($body), "%latesthits%") !== false)
			{
				$qry = $wpdb->get_results("SELECT search FROM $table_name WHERE search <> '' ORDER BY id DESC LIMIT 10");
				$body = str_replace("%latesthits%", urldecode($qry[0]->search), $body);
				for ($counter = 0; $counter < 10; $counter += 1)
				{
					$body .= "<br>". urldecode($qry[$counter]->search);
				}
			}
          
          return $body;
      }
      
      function iri_dailystat_TopPosts($limit = 5, $showcounts = 'checked')
      {
          global $wpdb;
          $res = "\n<ul>\n";
          $table_name = $wpdb->prefix. 'dailystat';
          $qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY urlrequested ORDER BY totale DESC LIMIT $limit;");
          foreach ($qry as $rk)
          {
              $res .= "<li><a href='" . irigetblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . iri_dailystat_Decode($rk->urlrequested) . "</a></li>\n";
              if (strtolower($showcounts) == 'checked')
              {
                  $res .= " (" . $rk->totale . ")";
              }
          }
          return "$res</ul>\n";
      }
      
      function widget_dailystat_init($args)
      {
          if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control'))
              return;
          // Multifunctional dailystat pluging
          function widget_dailystat_control()
          {
              $options = get_option('widget_dailystat');
              if (!is_array($options))
                  $options = array('title' => 'dailystat', 'body' => 'Visits today: %visitstoday%');
              if ($_POST['dailystat-submit'])
              {
                  $options['title'] = strip_tags(stripslashes($_POST['dailystat-title']));
                  $options['body'] = stripslashes($_POST['dailystat-body']);
                  update_option('widget_dailystat', $options);
              }
              $title = htmlspecialchars($options['title'], ENT_QUOTES);
              $body = htmlspecialchars($options['body'], ENT_QUOTES);
              // the form
              echo '<p style="text-align:right;"><label for="dailystat-title">' . __('Title:') . ' <input style="width: 250px;" id="dailystat-title" name="dailystat-title" type="text" value="' . $title . '" /></label></p>';
              echo '<p style="text-align:right;"><label for="dailystat-body"><div>' . __('Body:', 'widgets') . '</div><textarea style="width: 288px;height:100px;" id="dailystat-body" name="dailystat-body" type="textarea">' . $body . '</textarea></label></p>';
              echo '<input type="hidden" id="dailystat-submit" name="dailystat-submit" value="1" /><div style="font-size:7pt;">%visitstoday% %visitsyesterday% %pageviewstoday% %pageviewsyesterday% %thistodaypageviews% %thisyesterdaypageviews% %thistodayvisits% %thisyesterdayvisits%
%os% %browser% %ip% %visitorsonline% %usersonline% %toppost% %topbrowser% %topos% %latesthits%</div>';
          }
		  
       function widget_dailystat($args)
          {
              extract($args);
              $options = get_option('widget_dailystat');
              $title = $options['title'];
              $body = $options['body'];
              echo $before_widget;
              print($before_title . $title . $after_title);
              print iri_dailystat_Vars($body);
              echo $after_widget;
          }
          register_sidebar_widget('dailystat', 'widget_dailystat');
          register_widget_control(array('dailystat', 'widgets'), 'widget_dailystat_control', 300, 210);
          
          // Top posts
          function widget_dailystattopposts_control()
          {
              $options = get_option('widget_dailystattopposts');
              if (!is_array($options))
              {
                  $options = array('title' => 'dailystat TopPosts', 'howmany' => '5', 'showcounts' => 'checked');
              }
              if ($_POST['dailystattopposts-submit'])
              {
                  $options['title'] = strip_tags(stripslashes($_POST['dailystattopposts-title']));
                  $options['howmany'] = stripslashes($_POST['dailystattopposts-howmany']);
                  $options['showcounts'] = stripslashes($_POST['dailystattopposts-showcounts']);
                  if ($options['showcounts'] == "1")
                  {
                      $options['showcounts'] = 'checked';
                  }
                  update_option('widget_dailystattopposts', $options);
              }
              $title = htmlspecialchars($options['title'], ENT_QUOTES);
              $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
              $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
              // the form
              echo '<p style="text-align:right;"><label for="dailystattopposts-title">' . __('Title', 'dailystat') . ' <input style="width: 250px;" id="dailystat-title" name="dailystattopposts-title" type="text" value="' . $title . '" /></label></p>';
              echo '<p style="text-align:right;"><label for="dailystattopposts-howmany">' . __('Limit results to', 'dailystat') . ' <input style="width: 100px;" id="dailystattopposts-howmany" name="dailystattopposts-howmany" type="text" value="' . $howmany . '" /></label></p>';
              echo '<p style="text-align:right;"><label for="dailystattopposts-showcounts">' . __('Visits', 'dailystat') . ' <input id="dailystattopposts-showcounts" name="dailystattopposts-showcounts" type=checkbox value="checked" ' . $showcounts . ' /></label></p>';
              echo '<input type="hidden" id="dailystat-submitTopPosts" name="dailystattopposts-submit" value="1" />';
          }
		  
     function widget_dailystattopposts($args)
          {
              extract($args);
              $options = get_option('widget_dailystattopposts');
              $title = htmlspecialchars($options['title'], ENT_QUOTES);
              $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
              $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
              echo $before_widget;
              print($before_title . $title . $after_title);
              print iri_dailystat_TopPosts($howmany, $showcounts);
              echo $after_widget;
          }
          register_sidebar_widget('dailystat TopPosts', 'widget_dailystattopposts');
          register_widget_control(array('dailystat TopPosts', 'widgets'), 'widget_dailystattopposts_control', 300, 110);
      }
      
		// a custom function for loading localization
    function dailystat_load_textdomain() 
	{
		//check whether necessary core function exists
		if ( function_exists('load_plugin_textdomain') ) {
		//load the plugin textdomain
		load_plugin_textdomain('dailystat', 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/locale');
		}
	}
		
		// call the custom function on the init hook
		add_action('init', 'dailystat_load_textdomain');
      
      add_action('admin_menu', 'iri_add_pages');
      add_action('plugins_loaded', 'widget_dailystat_init');
      //add_action('wp_head', 'iriStatAppend');
      add_action('send_headers', 'iriStatAppend');
      
      register_activation_hook(__FILE__, 'iri_dailystat_CreateTable');
?>