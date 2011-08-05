<?php
	  
  function luc_add_pages()
  {
      // Create table if it doesn't exist
      global $wpdb;
      $table_name = $wpdb->prefix. 'dailystat';
	  
      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
          luc_dailystat_CreateTable();
      
      // add submenu
      $mincap = get_option('dailystat_mincap');
      if ($mincap == '')
          $mincap = 'switch_themes';


      add_menu_page('Daily stat', 'Daily stat', $mincap, __FILE__, 'luc_main',WP_CONTENT_URL .'/plugins/daily-stat/images/stat.png');
      add_submenu_page(__FILE__, __('Visitor Spy', 'dailystat'), __('Visitor Spy', 'dailystat'), $mincap,'dailystat/action=spyvisitors', 'luc_spy_visitors');
	  add_submenu_page(__FILE__, __('Bot Spy', 'dailystat'), __('Bot Spy', 'dailystat'), $mincap,'dailystat/action=spybot', 'luc_spy_bot');
	  add_submenu_page(__FILE__, __('Yesterday ', 'dailystat'), __('Yesterday ', 'dailystat'), $mincap,'dailystat/action=yesterday', 'luc_yesterday');
	  add_submenu_page(__FILE__, __('Referrer', 'dailystat'), __('Referrer', 'dailystat'), $mincap, 'dailystat/action=referrer', 'luc_dailystat_referrer');
	  add_submenu_page(__FILE__, __('Statistics', 'dailystat'), __('Statistics', 'dailystat'), $mincap,'dailystat/action=details','luc_statistics');
      add_submenu_page(__FILE__, __('Options', 'dailystat'), __('Options', 'dailystat'), $mincap,'dailystat/action=options', 'luc_Options');
      
  }
  
  function luc_Options()
  { ?><div class='wrap'><h2><?php  _e('Options', 'dailystat'); ?></h2>
	  <form method=post>
     <?php if ($_POST['saveit'] == 'yes')
      {   update_option('dailystat_collect_logged_user', $_POST['dailystat_collect_logged_user']);
          update_option('dailystat_not_collect_spider', $_POST['dailystat_not_collect_spider']);
          update_option('dailystat_mincap', $_POST['dailystat_mincap']);
		  update_option('dailystat_ip_per_page_spyvisitor', $_POST['dailystat_ip_per_page_spyvisitor']);
          update_option('dailystat_visit_per_visitor_spyvisitor', $_POST['dailystat_visit_per_visitor_spyvisitor']);
          // update database too
          luc_dailystat_CreateTable();
		  echo "<br /><div class='wrap' align ='center'><h2>" . __('Recorded !', 'dailystat') . "<h2>
		  <IMG style='border:0px;width:20px;height:20px;' SRC='".plugins_url('daily-stat/images/ok.gif', dirname(dirname(__FILE__)))."'></div>";
        
      }
	  echo "<tr><td><input type=checkbox name='dailystat_collect_logged_user' id='dailystat_collect_logged_user' value='checked'" . get_option('dailystat_collect_logged_user') . "><label for='dailystat_collect_logged_user'>Collect data about logged users</label></td></tr><br>
            <tr><td><input type=checkbox name='dailystat_not_collect_spider' id='dailystat_not_collect_spider' value='checked'" . get_option('dailystat_not_collect_spider') . "><label for='dailystat_not_collect_spider'>Do not collect spiders visits</label></td></tr>";
       ?>
	  <table width=100%>
          <tr><td><?php _e('Minimum capability to view stats', 'dailystat'); ?>
          <select name="dailystat_mincap"><?php luc_dropdown_caps(get_option('dailystat_mincap')); ?></select>
		    <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank"><?php _e("more info", 'dailystat'); ?></a>
          </td></tr>
          <tr><td>
		  <h4><?php _e('Visitors spy : ', 'dailystat'); ?></h4><?php _e('Visitors displayed per page', 'dailystat'); ?>
		  <select name="dailystat_ip_per_page_spyvisitor">
		      <option value="20" <?php if (get_option('dailystat_ip_per_page_spyvisitor') == 20) 
		                                   echo "selected"; ?>>20</option>
			  <option value="50" <?php if (get_option('dailystat_ip_per_page_spyvisitor') == 50) 
			                               echo "selected"; ?>>50</option>
              <option value="100" <?php if (get_option('dailystat_ip_per_page_spyvisitor') == 100)
                                           echo "selected"; ?>>100</option>
         </select>
		 </td></tr>
         <tr><td><?php _e('Visits per visitor', 'dailystat'); ?>
         <select name="dailystat_visit_per_visitor_spyvisitor">
		      <option value="20" <?php if (get_option('dailystat_visit_per_visitor_spyvisitor') == 20)
                                           echo "selected"; ?>>20</option>
              <option value="50" <?php if (get_option('dailystat_visit_per_visitor_spyvisitor') == 50)
                                           echo "selected"; ?>>50</option>
              <option value="100" <?php if (get_option('dailystat_visit_per_visitor_spyvisitor') == 100)
                                           echo "selected"; ?>>100</option>
         </select>
		 </td></tr>
		 <tr><td><br><input type=submit class="button-primary" value="<?php _e('Save options', 'dailystat'); ?>">
		 </td></tr>
         </table>
         <input type=hidden name=saveit value=yes>
         <input type=hidden name=page value=dailystat><input type=hidden name=dailystat-action value=options>
         </form>
         </div>
<?php
           // chiude saveit
      }
	  
      function luc_main()
        {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          $action = 'overview';
          // OVERVIEW table
          $visitors_color = "#114477";
		  $rss_visitors_color = "#FFF168";
          $pageviews_color = "#3377B6";
          $rss_pageviews_color = "#f38f36";
          $spider_color = "#83b4d8";
          $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
          $today = gmdate('Ymd', current_time('timestamp'));
          $tlm[0] = my_substr($lastmonth, 0, 4);
          $tlm[1] = my_substr($lastmonth, 4, 2);
          
          echo "<div class='wrap'><h2>" . __('Overview', 'dailystat') . "</h2>";
          echo "<table class='widefat'><thead><tr>
	<th scope='col'></th>
	<th scope='col'>". __('Yesterday','dailystat'). "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')-86400) ."</font></th>
	<th scope='col'>". __('Today','dailystat'). "<br /><font size=1>" . gmdate('d M, Y', current_time('timestamp')) ."</font></th>
	</tr></thead>
	<tbody id='the-list'>";
          
           //###############################################################################################
		    //###############################################################################################
          // VISITORS ROW
		  //YESTERDAY
		  $qry_y=requete_main("DISTINCT ip","feed=''","spider=''","date LIKE '".$yesterday."%'"); 
          //TODAY
		  $qry_t=requete_main("DISTINCT ip","feed=''","spider=''","date LIKE '".$today."%'");  
          echo "<tr><td><div style='background:$visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors', 'dailystat') . "</td>
          <td>".$qry_y."</td>\n
          <td>".$qry_t."</td>\n</tr>";
          
		  // VISITORS ROW
		
          //###############################################################################################
          // VISITORS FEEDS ROW
		   //YESTERDAY
		  $qry_y=requete_main("DISTINCT ip","feed<>''","spider=''","date LIKE '".$yesterday."%'"); 
         //TODAY
		  $qry_t=requete_main("DISTINCT ip","feed<>''","spider=''","date LIKE '".$today."%'");  
          echo "<tr><td><div style='background:$rss_visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Visitors RSS Feeds', 'dailystat') . "</td>    
		  <td>".$qry_y."</td>\n
		  <td>".$qry_t."</td>\n</tr>";
        
          //###############################################################################################
          // PAGEVIEWS ROW
		  //YESTERDAY
		  $qry_y=requete_main("*","feed=''","spider=''","date LIKE '".$yesterday."%'"); 
          //TODAY
		  $qry_t=requete_main("*","feed=''","spider=''","date LIKE '".$today."%'");  
          echo "<tr><td><div style='background:$pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews', 'dailystat') . "</td>     
          <td>".$qry_y."</td>\n 
          <td>".$qry_t."</td>\n</tr>";
          
		   //###############################################################################################
          // PAGEVIEWS FEEDS ROW
		  //YESTERDAY
		  $qry_y=requete_main("*","feed<>''","spider=''","date LIKE '".$yesterday."%'"); 
          //TODAY
		  $qry_t=requete_main("*","feed<>''","spider=''","date LIKE '".$today."%'");  
           echo "<tr>
		  <td><div style='background:$rss_pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Pageviews RSS Feeds', 'dailystat') . "</td>       
		  <td>".$qry_y."</td>\n 
		  <td>".$qry_t."</td>\n</tr>";
         
          //###############################################################################################
          // SPIDERS ROW
		   //YESTERDAY
		  $qry_y=requete_main("*","feed = ''","spider<>''","date LIKE '".$yesterday."%'"); 
          //TODAY
		  $qry_t=requete_main("*","feed = ''","spider<>''","date LIKE '".$today."%'");  
          echo "<tr>
		  <td><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>" . __('Spiders', 'dailystat') . "</td> 
          <td>".$qry_y."</td>\n       
          <td>".$qry_t."</td>\n</tr></tr></table><br />\n\n";
		
             //###################################################################################################
          
            $querylimit = "LIMIT 50";
             // Tabella Last Hits
             echo "<div class='wrap' width='100%' border='0'>
			 <h2>" . __('Last Hits', 'dailystat') . "</h2>
			 <table class='widefat' width='100%' border='0'>
			 <thead><tr>
			 <th scope='col'>" . __('Date', 'dailystat') . "</th>
			 <th scope='col'>" . __('Time', 'dailystat') . "</th>
			 <th scope='col'>" . __('IP', 'dailystat') . "</th>
			 <th scope='col'>" . __('Domain', 'dailystat') . "</th>
			 <th scope='col'>" . __('Page', 'dailystat') . "</th>
			 <th></th>
			 <th scope='col'>" . __('OS', 'dailystat') . "</th>
			 <th></th>
			 <th scope='col'>" . __('Browser', 'dailystat') . "</th><th scope='col'>" . __('Feed', 'dailystat') . "</th>
			 </tr></thead>";
             echo "<tbody id='the-list' width='100%' border='0'>";

             $fivesdrafts = $wpdb->get_results("SELECT * FROM $table_name WHERE (os<>'' OR browser <>'')  AND ip IS NOT NULL order by id DESC $querylimit");
             foreach ($fivesdrafts as $fivesdraft)
             {echo "<tr>";
              echo "<td>" . luc_hdate($fivesdraft->date) . "</td>";
              echo "<td>" . $fivesdraft->time . "</td>";
              echo "<td>" . $fivesdraft->ip . "</td>";
              echo "<td>" . $fivesdraft->nation . "</td>";
              echo "<td>" . luc_dailystat_Abbrevia(luc_dailystat_Decode($fivesdraft->urlrequested), 50) . "</td>";
			  if($fivesdraft->os != '') { $img=str_replace(" ","_",strtolower($fivesdraft->os));
			                              $img=str_replace('.','',$img).".png";
			                              echo "<td><IMG style='border:0px;width:16px;height:16px;' SRC='" .plugins_url('daily-stat/images/os/'.$img, dirname(dirname(__FILE__))). "'/images/os/$img'></td>";
										} 
			  else 
			     echo "<td></td>";
              echo "<td>" . $fivesdraft->os . "</td>";
			  if($fivesdraft->browser <> '') { $img=str_replace(" ","_",strtolower($fivesdraft->browser));
			                                   $img=str_replace('.','',$img).".png";
			                                   echo "<td><IMG style='border:0px;width:16px;height:16px;' SRC='".plugins_url('daily-stat/images/browsers/'.$img, dirname(dirname(__FILE__))). "'/images/browsers/$img'></td>";
		                                      } 
		      else  
			      echo "<td></td>";
              echo "<td>" . $fivesdraft->browser . "</td>";
              echo "<td>" . $fivesdraft->feed . "</td>";
              echo "</tr>";
            }
             echo "</table></div>";
          
          // Last Search terms
          echo "<div class='wrap'><h2>" . __('Last search terms', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('Terms', 'dailystat') . "</th><th scope='col'>" . __('Engine', 'dailystat') . "</th><th scope='col'>" . __('Result', 'dailystat') . "</th></tr></thead>";
          echo "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested,search,searchengine FROM $table_name WHERE search<>''   ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              echo "<tr>
			  <td>" . luc_hdate($rk->date) . "</td>
			  <td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . urldecode($rk->search) . "</a></td>
			  <td>" . $rk->searchengine . "</td><td><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'dailystat') . "</a></td>
			  </tr>\n";
          }
          echo "</table></div>";
          
          // Referrer
          echo "<div class='wrap'><h2>" . __('Last referrers', 'dailystat') . "</h2><table class='widefat'><thead><tr><th scope='col'>" . __('Date', 'dailystat') . "</th><th scope='col'>" . __('Time', 'dailystat') . "</th><th scope='col'>" . __('URL', 'dailystat') . "</th><th scope='col'>" . __('Result', 'dailystat') . "</th></tr></thead>";
          echo "<tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,referrer,urlrequested FROM $table_name WHERE ((referrer NOT LIKE '" . get_option('home') . "%') AND (referrer <>'') AND (searchengine='')  ) ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              echo "<tr><td>" . luc_hdate($rk->date) . "</td><td>" . $rk->time . "</td><td><a href='" . $rk->referrer . "'>" . luc_dailystat_Abbrevia($rk->referrer, 80) . "</a></td><td><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . __('page viewed', 'dailystat') . "</a></td></tr>\n";
          }
          echo "</table></div>";
		  
          // Feeds 
            echo "<div class='wrap'>
			<h2>" . __('Last Feeds', 'dailystat') . "</h2>
			<table class='widefat'>
			<thead><tr>
			<th scope='col'>" . __('Date', 'dailystat') . "</th>
			<th scope='col'>" . __('Time', 'dailystat') . "</th>
			<th scope='col'>" . __('IP', 'dailystat') . "</th>
			<th scope='col'>" . __('Page', 'dailystat') . "</th>
			<th scope='col'>" . __('Feed', 'dailystat') . "</th>
			<th scope='col'>" . __('User', 'dailystat') . "</th>
			</tr></thead>";
            echo "<tbody id='the-list'>";
            $qry = $wpdb->get_results("SELECT date,time,ip,referrer,urlrequested,feed,user FROM $table_name WHERE feed<>''  AND ip IS NOT NULL ORDER BY id DESC $querylimit");
            foreach ($qry as $rk)
             {
              echo "<tr>
			  <td>".luc_hdate($rk->date)."</td>
			  <td>".$rk->time."</td>
			  <td>".$rk->ip."</td>
			  <td>".$rk->urlrequested."</td>
			  <td>".$rk->feed."</a></td>
			  <td>".$rk->user."</td>
			  </tr>\n";
             }
            echo "</table></div>";
			
          
          // Last Spiders
          echo "<div class='wrap'><h2>" . __('Last spiders', 'dailystat') . "</h2>";
          echo "<table class='widefat'><thead><tr>";
          echo "<th scope='col'>" . __('Date', 'dailystat') . "</th>";
          echo "<th scope='col'>" . __('Time', 'dailystat') . "</th>";
          echo "<th scope='col'>" . __('Spider', 'dailystat') . "</th>";
          echo "<th scope='col'>" . __('Page', 'dailystat') . "</th>";
          echo "<th scope='col'>" . __('Agent', 'dailystat') . "</th>";
          echo "</tr></thead><tbody id='the-list'>";
          $qry = $wpdb->get_results("SELECT date,time,agent,spider,urlrequested,agent FROM $table_name WHERE spider<>''   ORDER BY id DESC $querylimit");
          foreach ($qry as $rk)
          {
              echo "<tr><td>" . luc_hdate($rk->date) . "</td>";
              echo "<td>" . $rk->time . "</td>";
              echo "<td>" . $rk->spider . "</td>";
              echo "<td>" . luc_dailystat_Abbrevia(luc_dailystat_Decode($rk->urlrequested), 30) . "</td>";
              echo "<td> " . $rk->agent . "</td></tr>\n";
          }
          echo "</table></div>";
          echo "<br />";
          echo "&nbsp;<i>" . __('dailystat table size', 'dailystat') . ": <b>" . luc_tablesize($wpdb->prefix. 'dailystat') . "</b></i><br />";
          echo "&nbsp;<i>" . __('dailystat current time', 'dailystat') . ": <b>" . current_time('mysql') . "</b></i><br />";
          echo "&nbsp;<i>" . __('RSS2 url', 'dailystat') . ": <b>" . get_bloginfo('rss2_url') . ' (' . luc_dailystat_extractfeedreq(get_bloginfo('rss2_url')) . ")</b></i><br />";
          echo "&nbsp;<i>" . __('ATOM url', 'dailystat') . ": <b>" . get_bloginfo('atom_url') . ' (' . luc_dailystat_extractfeedreq(get_bloginfo('atom_url')) . ")</b></i><br />";
          echo "&nbsp;<i>" . __('RSS url', 'dailystat') . ": <b>" . get_bloginfo('rss_url') . ' (' . luc_dailystat_extractfeedreq(get_bloginfo('rss_url')) . ")</b></i><br />";
          echo "&nbsp;<i>" . __('COMMENT RSS2 url', 'dailystat') . ": <b>" . get_bloginfo('comments_rss2_url') . ' (' . luc_dailystat_extractfeedreq(get_bloginfo('comments_rss2_url')) . ")</b></i><br />";
          echo "&nbsp;<i>" . __('COMMENT ATOM url', 'dailystat') . ": <b>" . get_bloginfo('comments_atom_url') . ' (' . luc_dailystat_extractfeedreq(get_bloginfo('comments_atom_url')) . ")</b></i><br />";
     
	  }
	  
         function requete_main($count,$feed,$spider,$date)
	 {   global $wpdb;
	     $table_name = $wpdb->prefix . "dailystat";
	     $qry = $wpdb->get_var("SELECT count($count) 
                                        FROM $table_name
                                        WHERE ip IS NOT NULL AND $feed AND $spider AND $date");
		 return $qry;							
	}

 function luc_statistics()
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
          
          $querylimit = "LIMIT 10";
		  # Top days
          luc_ValueTable2("date","Top days",5);
 
		   // Search terms
          luc_ValueTable2("search", __('Top search terms', 'dailystat'), 20, "", "", "AND search<>''");
          
          // Top referrer
          luc_ValueTable2("referrer", __('Top referrer', 'dailystat'), 10, "", "", "AND referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%'");
		  
          // O.S.
          luc_ValueTable2("os", __('O.S.', 'dailystat'), 0, "", "", "AND feed='' AND spider='' AND os<>''");
          
          // Browser
          luc_ValueTable2("browser", __('Browser', 'dailystat'), 0, "", "", "AND feed='' AND spider='' AND browser<>''");
          
          // Feeds
          luc_ValueTable2("feed", __('Feeds', 'dailystat'), 5, "", "", "AND feed<>''");
          
          // SE
          luc_ValueTable2("searchengine", __('Search engines', 'dailystat'), 10, "", "", "AND searchengine<>''");
          
          // Countries
          luc_ValueTable2("nation", __('Countries (domains)', 'dailystat'), 10, "", "", "AND nation<>'' AND spider=''");
          
          // Spider
          luc_ValueTable2("spider", __('Spiders', 'dailystat'), 10, "", "", "AND spider<>''");
          
      }
	  function luc_page_periode()
	  { global $wpdb;
	  // pp is the display page periode 
	    if(isset($_GET['pp']))
          { // Get Current page periode from URL
          	$periode = $_GET['pp'];
          	if($periode <= 0)
          	// Periode is less than 0 then set it to 1
          	 $periode = 1;
          }
          else
           // URL does not show the page set it to 1
			$periode = 1;
			
			return $periode;	
	   }
			
	  function luc_page_posts()
			{global $wpdb;
			// pa is the display pages Articles
			if(isset($_GET['pa']))
            { 
          	$pageA = $_GET['pa'];// Get Current page Articles from URL
          	if($pageA <= 0) // Article is less than 0 then set it to 1
          	   $pageA = 1;
            }
            else  // URL does not show the Article set it to 1
          	$pageA = 1;
			
			return $pageA;
			}
			
      function luc_spy_bot()
      {   global $wpdb;
	      $action = 'spybot';
          $table_name = $wpdb->prefix . "dailystat";
		  // number of IP or bot by page
		  $LIMIT = get_option('dailystat_ip_per_page_spyvisitor');
		  $LIMIT_PROOF = get_option('dailystat_visit_per_visitor_spyvisitor');

		      $LIMIT = 5;
               $LIMIT_PROOF = 40;
			 if ($LIMIT_PROOF == 0) $LIMIT_PROOF = 30;
			$pa = luc_page_posts();
			$LimitValue = ($pa * $LIMIT) - $LIMIT;
			
			// Number of distinct spiders 
			$Num = $wpdb->get_var("SELECT count(distinct spider) FROM $table_name WHERE spider<>''");
			$NA = ceil($Num/$LIMIT);
		
          	// Number of distinct spider between $currentdate and $limidate  
			$NumberSpiders = $wpdb->get_var("SELECT count(distinct spider) FROM $table_name WHERE spider<> '' AND date BETWEEN $limitdate AND $currentdate");
			$NumSpiders = ceil($NumberSpiders/ $LIMIT);

		    echo "<div class='wrap'><h2>" . __('Bot Spy', 'dailystat') . "</h2>";
			
            // selection of spider, group by spider, order by most recently visit (last id in the table)
			$sql = "SELECT *
			        FROM $table_name as T1
					JOIN
			       (SELECT spider,max(id) as MaxId FROM $table_name WHERE spider<>'' GROUP BY spider ORDER BY MaxId DESC LIMIT $LimitValue, $LIMIT ) as T2
				    ON T1.spider = T2.spider					 
			        ORDER BY MaxId DESC, timestamp DESC
			";
			
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
<div id="paginating" align="center">
<?php
luc_print_pa_link ($NA,$pa,$action);
?>
</div>
<table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">
<?php
          $spider="robot";
		  $num_row=0;
          foreach ($qry as $rk)
          {   echo "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";
             // Bot Spy
			if ($robot <> $rk->spider)  
			{echo " <strong><span><font size='4' color='#7b7b7b'>" . $rk->spider . "</font></span></strong></td></tr><tr>"; 
			 echo "<td valign='top' width='170'><div><font size='1' color='#3B3B3B'><strong>" . luc_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>";
             echo "<td><div><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "' target='_blank'>" . luc_dailystat_Decode($rk->urlrequested) . "</a>";
			 $robot=$rk->spider;
			 $num_row=1;
			 }
			 elseif ($num_row < $LIMIT_PROOF)
			 {echo "<tr>";
			  echo "<td valign='top' width='170'><div><font size='1' color='#3B3B3B'><strong>" . luc_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>";
              echo "<td><div><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "' target='_blank'>" . luc_dailystat_Decode($rk->urlrequested) . "</a>";
			 $num_row+=1;
			 }
		echo "</div></td></tr>\n";
         }
		echo "</table>"; 
		luc_print_pa_link ($NA,$pa,$action);
        echo "</div></table></div>";
      }
	  
	  function luc_spy_visitors()
      {   global $wpdb;
	      $action = 'spyvisitors';
          $table_name = $wpdb->prefix . "dailystat";
		  // number of IP or bot by page
		  $LIMIT = get_option('dailystat_ip_per_page_spyvisitor');
		  $LIMIT_PROOF = get_option('dailystat_visit_per_visitor_spyvisitor');
            if ($LIMIT == 0)
              $LIMIT = 20;
			if ($LIMIT_PROOF == 0)
              $LIMIT_PROOF = 20;
			   
            $pp = luc_page_periode();	
			
          	// Number of distinct ip (unique visitors)
			$NumIP = $wpdb->get_var("SELECT count(distinct ip) FROM $table_name WHERE spider=''");
			$NP = ceil($NumIP/$LIMIT);
            $LimitValue = ($pp * $LIMIT) - $LIMIT;
        
			$sql = "SELECT *
			FROM $table_name as T1
			JOIN
			(SELECT ip,max(id) as MaxId FROM $table_name WHERE spider='' GROUP BY ip ORDER BY MaxId DESC LIMIT $LimitValue, $LIMIT ) as T2
			ON T1.ip = T2.ip
			ORDER BY MaxId DESC, id DESC
			";
			$qry = $wpdb->get_results($sql);	
			echo "<div class='wrap'><h2>" . __('Visitor Spy', 'dailystat') . "</h2>";
?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<div align="center">
<div id="paginating" align="center">
</div>
<table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">
<?php
          $ip = 0;
		  $num_row=0;
		  echo'<div align="center">
               <div id="paginating" align="center">';
		  luc_print_pp_link($NP,$pp,$action);
		  echo'</div>
              <table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">';    
          foreach ($qry as $rk)
          {echo "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";
              // Visitor Spy
   		   if ($ip <> $rk->ip)
             {echo "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";
              echo "<IMG SRC='http://api.hostip.info/flag.php?ip=" . $rk->ip . "' border=0 width=18 height=12>";
              echo " <strong><span><font size='2' color='#7b7b7b'>" . $rk->ip . "</font></span></strong> ";
              echo "<span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;' onClick=ttogle('" . $rk->ip . "');>" . __('more info', 'dailystat') . "</span></div>";
              echo "<div id='" . $rk->ip . "' name='" . $rk->ip . "'>" . $rk->os . ", " . $rk->browser;
              //    echo "<br><iframe style='overflow:hide;border:0px;width:100%;height:15px;font-family:helvetica;paddng:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://showip.fakap.net/txt/".$rk->ip."></iframe>";
              echo "<br><iframe style='overflow:hide;border:0px;width:100%;height:40px;font-family:helvetica;paddng:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=" . $rk->ip . "></iframe>";
              if ($rk->nation)
                  echo "<br><small>" . gethostbyaddr($rk->ip) . "</small><br><small>" . $rk->agent . "</small></div>";
              echo "<script>document.getElementById('" . $rk->ip . "').style.display='none';</script></td></tr><tr>";
              echo "<td valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>" . luc_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>";
              echo "<td><div><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "' target='_blank'>" . luc_dailystat_Decode($rk->urlrequested) . "</a>";
                  if ($rk->searchengine != '')
                      echo "<br><small>" . __('arrived from', 'dailystat') . " <b>" . $rk->searchengine . "</b> " . __('searching', 'dailystat') . " <a href='" . $rk->referrer . "' target=_blank>" . urldecode($rk->search) . "</a></small>";
                  elseif ($rk->referrer != '' && strpos($rk->referrer, get_option('home')) === false)
                      echo "<br><small>" . __('arrived from', 'dailystat') . " <a href='" . $rk->referrer . "' target=_blank>" . $rk->referrer . "</a></small>";
                  echo "</div></td></tr>\n";
			  $ip=$rk->ip;
			  $num_row = 1;
			   }
		   elseif ($num_row < $LIMIT_PROOF)
			   {  echo "<tr><td valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>" . luc_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>";
                  echo "<td><div><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "' target='_blank'>" . luc_dailystat_Decode($rk->urlrequested) . "</a>";
                  if ($rk->searchengine != '')
                      echo "<br><small>" . __('arrived from', 'dailystat') . " <b>" . $rk->searchengine . "</b> " . __('searching', 'dailystat') . " <a href='" . $rk->referrer . "' target=_blank>" . urldecode($rk->search) . "</a></small>";
                  elseif ($rk->referrer != '' && strpos($rk->referrer, get_option('home')) === false)
                      echo "<br><small>" . __('arrived from', 'dailystat') . " <a href='" . $rk->referrer . "' target=_blank>" . $rk->referrer . "</a></small>";
				  $num_row += 1;
                  echo "</div></td></tr>\n";
				}
		   }
		echo "</div></td></tr>\n</table>";   
        luc_print_pp_link($NP,$pp,$action);
        echo "</div>";

      }
	    
	 function luc_yesterday()
	  { global $wpdb;
         $table_name = $wpdb->prefix . "dailystat";
		 $action = "yesterday";
		 $unique_color = "#114477";
         $web_color = "#3377B6";
         $rss_posts_color = "#f38f36";
	     $rss_visitors_color = "#FFF168";
         $spider_color = "#83b4d8";
		 $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
		 
		$pa = luc_page_posts();
		$permalink = luc_permalink();
			
		$strqry = "SELECT  post_name
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
							 ON t.urlrequested LIKE CONCAT('%', p.post_name, '%' )
		                     WHERE p.post_status = 'publish' AND (p.post_type = 'page' OR p.post_type = 'post') AND t.date = $yesterday
							 GROUP BY post_name
							 ";
			$query = $wpdb->get_results($strqry);
             
		    $NumberPosts = $wpdb->num_rows;
			$NumberDisplayPost = 100;
		    $NA = ceil($NumberPosts / $NumberDisplayPost);
		    $LimitValueArticles = ($pa * $NumberDisplayPost) - $NumberDisplayPost;
			  			  
			$qry_visitors =requete_yesterday ("count(distinct ip) as total","urlrequested =''","spider='' AND feed=''");	   
	
			$qry_visitors_feeds =requete_yesterday ("count(distinct ip) AS total","(urlrequested LIKE '%".$permalink[0]."feed%' OR urlrequested LIKE '%".$permalink[0]."comment%') ","spider='' AND feed<>''");
				     
			$qry_pageviews=$wpdb->get_results("
			               SELECT post_name, total, urlrequested
					       FROM
						   ((SELECT 'page_accueil' as post_name, count(ip) as total, urlrequested
							         FROM $wpdb->posts as p
									 JOIN $table_name as t ON t.urlrequested ='' 
									 WHERE  p.post_status = 'publish' AND (p.post_type = 'page' OR p.post_type = 'post') AND t.date = $yesterday
									 AND t.spider='' AND t.feed='' 
									 GROUP BY post_name)
                        UNION ALL
						    (SELECT post_name, count(ip) as total, urlrequested
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON t.urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE p.post_status = 'publish' AND (p.post_type = 'page' OR p.post_type = 'post') AND t.date = $yesterday
							 AND t.spider='' AND t.feed='' 
							 GROUP BY post_name) 
						UNION 
						    (SELECT post_name, NULL as total, urlrequested
							   FROM	$wpdb->posts as p
                               JOIN $table_name as t
							   ON t.urlrequested LIKE CONCAT('%', p.post_name, '%' )
							   WHERE  p.post_status = 'publish' AND (p.post_type = 'page' OR p.post_type = 'post') 
							   GROUP BY post_name)
							) views		 
						GROUP BY post_name 
		                ORDER BY total DESC LIMIT $LimitValueArticles, $NumberDisplayPost");
						
			$qry_pageviews_feeds =requete_yesterday ("count(ip) AS total","(urlrequested LIKE '%".$permalink[0]."feed%' OR urlrequested LIKE '%".$permalink[0]."comment%')"," spider='' AND feed<>''");
						
			$qry_spiders =requete_yesterday ("count(ip) as total","urlrequested =''","spider<>'' AND feed=''");
				   
		  foreach ($qry_visitors as $url)
				   {$visitors[$url->post_name]=$url->total;
				    $total_visitors += $url->total;
				   }	
				   foreach ($qry_visitors_feeds as $url)
				   {$visitors_feeds[$url->post_name]=$url->total;
				    $total_visitors_feeds += $url->total;
				   }
				    foreach ($qry_pageviews as $url)
				   {$pageviews[$url->post_name]=$url->total;
				   $total_pageviews += $url->total;
				   }	
				    foreach ($qry_pageviews_feeds as $url)
				   {$pageviews_feeds[$url->post_name]=$url->total;
				    $total_pageviews_feeds += $url->total;
				   }
				   foreach ($qry_spiders as $url)
				   {$spiders[$url->post_name]=$url->total;
				    $total_spiders += $url->total;
			   }
		 	$qry_total_visitors = $wpdb->get_row("SELECT count(distinct ip) AS total
                                   FROM $table_name
                                   WHERE feed='' AND spider='' AND date = $yesterday");
	$qry_total_visitors_feeds = $wpdb->get_row("SELECT count(distinct ip) as total
                                   FROM $table_name
                                   WHERE feed<>'' AND spider='' AND date = $yesterday");			
	    $total_visitors=$qry_total_visitors->total;
        $total_visitors_feeds=$qry_total_visitors_feeds->total;	
		echo "<div class='wrap'><h2>" . __('Yesterday ', 'dailystat'). gmdate('d M, Y', current_time('timestamp')-86400) ."</div>";

		luc_print_pa_link ($NA,$pa,$action);
		
			   
		echo "<table class='widefat'>
	<thead><tr>
	<th scope='col'>". __('URL','dailystat'). "</th>
	<th scope='col'><div style='background:$visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>". __('Visitors','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$rss_visitors_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>". __('Visitors Feeds','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>". __('Views','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$rss_pageviews_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>". __('Views Feeds','dailystat'). "<br /><font size=1></font></th>
	<th scope='col'><div style='background:$spider_color;width:10px;height:10px;float:left;margin-top:4px;margin-right:5px;'></div>". __('Spider','dailystat'). "<br /><font size=1></font></th>
	</tr></thead>";		   
				   
		echo "<tr>";		   
		echo "<th scope='col'>All URL</th>"; 
		echo "<th scope='col'>". __($total_visitors,'dailystat'). "</th>";
		echo "<th scope='col'>". __($total_visitors_feeds,'dailystat'). "</th>";
		echo "<th scope='col'>". __($total_pageviews,'dailystat'). "</th>";
		echo "<th scope='col'>". __($total_pageviews_feeds,'dailystat'). "</th>";
		echo "<th scope='col'>". __($total_spiders,'dailystat'). "</th>
		      </tr>";
			  
		foreach ($qry_pageviews as $url)
			  {if ($url->urlrequested == '')
				    $out_url = "Page : Home";
			   else  
			        $out_url=$permalink[0].$url->post_name;
					   
               echo "<tr>
			         <td>".$out_url  ."</td>"; 
			   echo "<td>" . $visitors[$url->post_name] . "</td>";
			   echo "<td>" . $visitors_feeds[$url->post_name] . "</td>";
			   echo "<td>" . $pageviews[$url->post_name] . "</td>";
			   echo "<td>" . $pageviews_feeds[$url->post_name] . "</td>";
			   echo "<td>" . $spiders[$url->post_name]. "</td>";
			   echo "</tr>";
		       };	   
        echo '</table>';

		luc_print_pa_link ($NA,$pa,$action);
}

    function requete_yesterday ($count,$where_one,$where_two)
	{global $wpdb;
            $table_name = $wpdb->prefix . "dailystat";
			$yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
			$permalink = luc_permalink();
			  $qry =$wpdb->get_results("SELECT  post_name, total
					       FROM
						   ((SELECT 'page_accueil' as post_name, $count
							         FROM $table_name 
									 WHERE $where_one  
									 AND $where_two 
									 AND date = $yesterday
									 GROUP BY post_name)
                        UNION ALL
						(SELECT post_name, $count
		                     FROM $wpdb->posts as p
		                     JOIN $table_name as t
                             ON urlrequested LIKE CONCAT('%', p.post_name, '%' ) 
		                     WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') AND date = $yesterday
							 AND $where_two 
							 GROUP BY post_name) 
							) req	 
					    GROUP BY post_name");
		    return $qry;								  
	}
	
	
	
	function luc_dailystat_referrer()
	  {  global $wpdb;
         $table_name = $wpdb->prefix . "dailystat";
		 $action = "referrer";
		 $visitors_color = "#114477";
		 $rss_visitors_color = "#FFF168";
         $pageviews_color = "#3377B6";
         $rss_pageviews_color = "#f38f36";
         $spider_color = "#83b4d8";
		 $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
		 $pa = luc_page_posts();
			
		 $strqry = "SELECT distinct(referrer) 
			                FROM $table_name 
                            WHERE referrer<>'' AND referrer NOT LIKE '%" . get_bloginfo('url') . "%' AND searchengine='' 
						    ";					   
		 $NumberArticles = $wpdb->get_var($strqry);
		 $LIMITArticles = 100;
		 $NA = $NumberArticles / $LIMITArticles;
		 $LimitValueArticles = ($pa * $LIMITArticles) - $LIMITArticles;
	
		 echo "<div class='wrap'><h2>" . __('Referrer ', 'dailystat')."</div>";
		 luc_print_pa_link ($NA,$pa,$action);
		 echo "<table class='widefat'><thead><tr>
	           <th scope='col'>". __('URL','dailystat'). "</th>
	           <th scope='col'>". __('Total','dailystat'). "<br /><font size=1></font></th>
	           </tr></thead>";
	            
         $strqry = "SELECT count(referrer) as total, referrer
			        FROM $table_name 
					WHERE referrer<>'' AND referrer NOT LIKE '%".get_bloginfo('url')."%'  AND searchengine='' 
					GROUP BY referrer 
					ORDER by total DESC LIMIT $LimitValueArticles, $LIMITArticles";
				      
		 $query = $wpdb->get_results($strqry);
		 foreach ($query as $url)
			       {echo "<tr><td><h4><a href='" . $url->referrer. "' target='_blank'>$url->referrer</a></h4></td>";
					echo "<td>" . $url->total . "</td>\n";
					}
	               
         echo '</tr></table></div>';
		
	     luc_print_pa_link ($NA,$pa,$action);
	     echo '</div>';
	  
	  }
	  
	function luc_permalink()
	      { global $wpdb;
            $table_name = $wpdb->prefix . "statpress";
	        $permalink = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'permalink_structure'");
	        $permalink = explode("%", $permalink);
	        return $permalink;
	       }
	  
	function luc_print_pp_link($NA,$pp,$action)
       {  // For all pages ($NA) Display first 10 pages, 10 pages before current page($pp), 10 pages after current page , each 25 pages and the 5 last pages for($action)
          
           $GUIL1 = FALSE;
           $GUIL2 = FALSE;// suspension points  not writed
		   if ($NA >1)
           {for ($i = 1; $i <= $NA; $i++) 
             if ($i <= $NA)
               { // $page is not the last page
                if($i == $pp)  
	               echo " [{$i}] "; // $page is current page
	            else 
	            { // Not the current page Hyperlink them
	              if (($i <= 10) or (($i >= $pp-10) and ($i <= $pp+10)) or ($i >= $NA-4) or is_int($i/25)) 
				  
				  { if ($action == "overview")
				      echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/dailystat.php/&pp=' . $i .'">' . $i . '</a> ';
					else  
	                  echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/action='.$action.'/&pp=' . $i . '&pa=1'.'">' . $i . '</a> ';
					  }
	              else 
	                 { if ($GUIL1 == FALSE) 
	                     {echo "... "; $GUIL1 = TRUE;
		                  }
	              if (($i == $NA-5) and ($GUIL2 == FALSE)) 
	                 { echo " ... "; $GUIL2 = TRUE;
	                  } // suspension points writed
	                 }
	             }
                }
			}	
        }
		
	function luc_print_pa_link ($NA,$pa,$action)		
        {  // For all pages ($NA) Display first 5 pages, 5 pages before current page($pa), 5 pages after current page , 5 last pages 
            $GUIL1 = FALSE;// suspension points not writed
            $GUIL2 = FALSE;

		    if ($NA >1)
			{echo '<table width="100%" border="0"><tr></tr></table>';
			 echo "Pages and Posts : ";
			 
             for ($j = 1; $j <= $NA; $j++) 
             if ($j <= $NA)
              { // $i is not the last Articles page
               if($j == $pa)  // $i is current page
	              echo " [{$j}] ";
	           else { // Not the current page Hyperlink them
	                 if (($j <= 5) or (($j >= $pa-4) and ($j <= $pa+4)) or ($j >= $NA-5)or is_int($i/25)) 
					 echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?page=dailystat/action='.$action.'&pa='. $j . '">' . $j . '</a> ';
	                 else 
	                   { if ($GUIL1 == FALSE) 
					      {echo "... "; $GUIL1 = TRUE;}
	                   if (($j == $pa+6) and ($GUIL2 == FALSE)) 
					      { echo " ... "; $GUIL2 = TRUE;}
	                    // suspension points writed
	                   }
	                 }
               }
			}
		}
		
	function luc_ValueTable2($fld,$fldtitle,$limit = 0,$param = "", $queryfld = "", $exclude= "") {
	global $wpdb;
	$table_name = $wpdb->prefix . "dailystat";
	
	if ($queryfld == '') { $queryfld = $fld; }
	print "<div class='wrap'><table class='widefat'><thead><tr><th scope='col' style='width:400px;'><h2>$fldtitle</h2></th><th scope='col' style='width:100px;'>".__('Visits','dailystat')."</th><th></th></tr></thead>";
	$rks = $wpdb->get_var("SELECT count($param $queryfld) as rks FROM $table_name WHERE 1=1 $exclude;"); 
	if($rks > 0) {
		$sql="SELECT count($param $queryfld) as pageview, $fld FROM $table_name WHERE 1=1 $exclude GROUP BY $fld ORDER BY pageview DESC";
		if($limit > 0) { $sql=$sql." LIMIT $limit"; }
		$qry = $wpdb->get_results($sql);
	    $tdwidth=450;
		
		// Collects data
		$data=array();
		foreach ($qry as $rk) {
			$pc=round(($rk->pageview*100/$rks),1);
			if($fld == 'nation') { $rk->$fld = strtoupper($rk->$fld); }
			if($fld == 'date') { $rk->$fld = luc_hdate($rk->$fld); }
			if($fld == 'urlrequested') { $rk->$fld = luc_StatPress_Decode($rk->$fld); }
        	$data[substr($rk->$fld,0,50)]=$rk->pageview;
		}
	}

	// Draw table body
	print "<tbody id='the-list'>";
	if($rks > 0) {  // Chart!
		if($fld == 'nation') {
			$chart=luc_GoogleGeo("","",$data);
		} else {
			$chart=luc_GoogleChart("","500x200",$data);
		}
		print "<tr><td></td><td></td><td rowspan='".($limit+2)."'>$chart</td></tr>";
		foreach ($data as $key => $value) {
    	   	print "<tr><td style='width:500px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis;'>".$key;
        	print "</td><td style='width:100px;text-align:center;'>".$value."</td>";
			print "</tr>";
		}
	}
	print "</tbody></table></div><br>\n";
	
}

function luc_GoogleChart($title,$size,$data_array) {
	if(empty($data_array)) { return ''; }
	// get hash
	foreach($data_array as $key => $value ) {
		$values[] = $value;
		$labels[] = $key;
	}
	$maxValue=max($values);
	$simpleEncoding='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$chartData="s:";
	for($i=0;$i<count($values);$i++) {
		$currentValue=$values[$i];
		if($currentValue>-1) {
			$chartData.=substr($simpleEncoding,61*($currentValue/$maxValue),1);
		} else {
			$chartData.='_';
		}
	}
	$data=$chartData."&chxt=y&chxl=0:|0|".$maxValue;
	return "<img src=http://chart.apis.google.com/chart?chtt=".urlencode($title)."&cht=p3&chs=$size&chd=".$data."&chl=".urlencode(implode("|",$labels)).">";
}

function luc_GoogleGeo($title,$size,$data_array) {
	if(empty($data_array)) { return ''; }
	// get hash
	foreach($data_array as $key => $value ) {
		$values[] = $value;
		$labels[] = $key;
	}
	return "<img src=http://chart.apis.google.com/chart?chtt=".urlencode($title)."&cht=t&chtm=world&chs=440x220&chco=eeeeee,FFffcc,cc3300&chd=t:0,".(implode(",",$values))."&chld=XX".(implode("",$labels)).">";
}

     
	function luc_dropdown_caps($default = false)
      {
          global $wp_roles;
          $role = get_role('administrator');
          foreach ($role->capabilities as $cap => $grant)
          {
              echo "<option ";
              if ($default == $cap)
                  echo "selected ";
              
              echo ">$cap</option>";
          }
      }	
	  
	  	
      function luc_dailystat_Abbrevia($s, $c)
      {
          $res = "";
          if (strlen($s) > $c)
              $res = "...";
          return my_substr($s, 0, $c) . $res;
      }
	  
	   function luc_tablesize($table)
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
      
	   function luc_rgbhex($red, $green, $blue)
      {
          $red = 0x10000 * max(0, min(255, $red + 0));
          $green = 0x100 * max(0, min(255, $green + 0));
          $blue = max(0, min(255, $blue + 0));
          // convert the combined value to hex and zero-fill to 6 digits
          return "#" . str_pad(strtoupper(dechex($red + $green + $blue)), 6, "0", STR_PAD_LEFT);
      }
	 
    function luc_dailystat_extractfeedreq($url)
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


class GoogChart
{
	// Constants
	const BASE = 'http://chart.apis.google.com/chart?';

	// Variables
	protected $types = array(
							'pie' => 'p',
							'line' => 'lc',
							'sparkline' => 'ls',
							'bar-horizontal' => 'bhg',
							'bar-vertical' => 'bvg',
						);

	protected $type;
	protected $title;
	protected $data = array();
	protected $size = array();
	protected $color = array();
	protected $fill = array();
	protected $labelsXY = false;
	protected $legend;
	protected $useLegend = true;
	protected $background = 'a,s,ffffff';

	protected $query = array();

	// debug
	public $debug = array();

	// Return string
	public function __toString()
	{
		return $this->display();
	}


	/** Create chart
	*/
	protected function display()
	{
		// Create query
		$this->query = array(
							'cht'	 => $this->types[strtolower($this->type)],					// Type
							'chtt'	 => $this->title,											// Title
							'chd' => 't:'.$this->data['values'],								// Data
							'chl'   => $this->data['names'],									// Data labels
							'chdl' => ( ($this->useLegend) && (is_array($this->legend)) ) ? implode('|',$this->legend) : null, // Data legend
							'chs'   => $this->size[0].'x'.$this->size[1],						// Size
							'chco'   => preg_replace( '/[#]+/', '', implode(',',$this->color)), // Color ( Remove # from string )
							'chm'   => preg_replace( '/[#]+/', '', implode('|',$this->fill)),   // Fill ( Remove # from string )
							'chxt' => ( $this->labelsXY == true) ? 'x,y' : null,				// X & Y axis labels
							'chf' => preg_replace( '/[#]+/', '', $this->background),			// Background color ( Remove # from string )
						);

		// Return chart
		return $this->img(
					GoogChart::BASE.http_build_query($this->query),
					$this->title
				);
	}

	/** Set attributes
	*/
	public function setChartAttrs( $attrs )
	{
		// debug
		$this->debug[] = $attrs;

		foreach( $attrs as $key => $value )
		{
			$this->{"set$key"}($value);
		}
	}

	/** Set type
	*/
	protected function setType( $type )
	{
		$this->type = $type;
	}


	/** Set title
	*/
	protected function setTitle( $title )
	{
		$this->title = $title;
	}


	/** Set data
	*/
	protected function setData( $data )
	{
		// Clear any previous data
		unset( $this->data );

		// Check if multiple data
		if( is_array(reset($data)) )
		{
			/** Multiple sets of data
			*/
			foreach( $data as $key => $value )
			{
				// Add data values
				$this->data['values'][] = implode( ',', $value );

				// Add data names
				$this->data['names'] = implode( '|', array_keys( $value ) );
			}
			/** Implode data correctly
			*/
			$this->data['values'] = implode('|', $this->data['values']);
			/** Create legend
			*/
			$this->legend = array_keys( $data );
		}
		else
		{
			/** Single set of data
			*/
			// Add data values
			$this->data['values'] = implode( ',', $data );

			// Add data names
			$this->data['names'] = implode( '|', array_keys( $data ) );
		}

	}

	/** Set legend
	*/
	protected function setLegend( $legend )
	{
		$this->useLegend = $legend;
	}

	/** Set size
	*/
	protected function setSize( $width, $height = null )
	{
		// check if width contains multiple params
		if(is_array( $width ) )
		{
			$this->size = $width;
		}
		else
		{
			// set each individually
			$this->size[] = $width;
			$this->size[] = $height;
		}
	}

	/** Set color
	*/
	protected function setColor( $color )
	{
		$this->color = $color;
	}

	/** Set labels
	*/
	protected function setLabelsXY( $labels )
	{
		$this->labelsXY = $labels;
	}

	/** Set fill
	*/
	protected function setFill( $fill )
	{
		// Fill must have atleast 4 parameters
		if( count( $fill ) < 4 )
		{
			// Add remaining params
			$count = count( $fill );
			for( $i = 0; $i < $count; ++$i )
				$fill[$i] = 'b,'.$fill[$i].','.$i.','.($i+1).',0';
		}
		
		$this->fill = $fill;
	}


	/** Set background
	*/
	protected function setBackground( $background )
	{
		$this->background = 'bg,s,'.$background;
	}

	/** Create img html tag
	*/
	protected function img( $url, $alt = null )
	{
		return sprintf('<img src="%s" alt="%s" style="width:%spx;height:%spx;" />', $url, $alt, $this->size[0], $this->size[1]);
	}


}
?>