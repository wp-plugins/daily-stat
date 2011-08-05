<?php
  /*
   Plugin Name: Daily Stat
   Plugin URI: http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/
   Description: Improved real time stats for your blog
   Version: 1.2.1
   Author: luciole135
   Author URI: http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/
   */
  
$_DAILYSTAT['version'] = '1.2.1';
$_DAILYSTAT['feedtype'] = '';
  // call the custom function on the init hook
add_action('plugins_loaded', 'widget_dailystat_init');
add_action('send_headers', 'luc_StatAppend');
register_activation_hook(__FILE__, 'luc_dailystat_CreateTable');
	  
  if (is_admin())
      {include ABSPATH.'wp-content/plugins/'.dirname(plugin_basename(__FILE__)).'/admin/luc_admin.php';
       add_action('init', 'dailystat_load_textdomain');
       add_action('admin_menu', 'luc_add_pages');
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
     
      function luc_dailystat_CreateTable()
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
      
      function luc_StatAppend()
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
          if (luc_CheckBanIP($ipAddress) === true)
              return '';
          
          // URL (requested)
          $urlRequested = luc_dailystat_URL();
           if (preg_match("/.ico$/i", $urlRequested))
              return '';
		   if (preg_match("/favicon.ico/i", $urlRequested))
              return '';  
		   if (preg_match("/.css$/i", $urlRequested))
              return '';  
		   if (preg_match("/.js$/i", $urlRequested))
              return '';
           if (stristr($urlRequested, "/wp-content/plugins") != false)
              return '';
           if (stristr($urlRequested, "/wp-content/themes") != false)
              return '';
          
          $referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
          $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
          $spider = luc_GetSpider($userAgent);
          
          if (($spider != '') and (get_option('dailystat_not_collect_spider') == 'checked'))
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
              $feed = luc_dailystat_is_feed($prsurl['scheme'] . '://' . $prsurl['host'] . $_SERVER['REQUEST_URI']);
              // Get OS and browser
              $os = luc_GetOS($userAgent);
              $browser = luc_GetBrowser($userAgent);
              list($searchengine, $search_phrase) = explode("|", luc_GetSE($referrer));
            }
			
			$countrylang=iriGetLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
          // Auto-delete visits older than yesterday...*
		   $today = gmdate('Ymd', current_time('timestamp'));
		   if ($today <> get_option('dailystat_delete_today')) 
			{ 
			   update_option('dailystat_delete_today', $today);
			   $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
               $results = $wpdb->query("DELETE FROM " . $table_name . " WHERE date < '" . $yesterday . "'");
			   $results  = $wpdb->query('OPTIMIZE TABLE '. $table_name); 
		    }
              
          if ((!is_user_logged_in()) or (get_option('dailystat_collect_logged_user') == 'checked'))
          {
              if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
                  luc_dailystat_CreateTable();
              
             $result = $wpdb->insert( $table_name, array(date => $vdate, time => $vtime, ip => $ipAddress, urlrequested => mysql_real_escape_string($urlRequested), agent => mysql_real_escape_string(strip_tags($userAgent)) , referrer => mysql_real_escape_string($referrer), search => mysql_real_escape_string(strip_tags($search_phrase)), nation => luc_Domain($ipAddress) ,os => mysql_real_escape_string($os), browser => mysql_real_escape_string($browser), searchengine => $searchengine ,spider => $spider, feed => $feed, user => $userdata->user_login , timestamp => $timestamp),
			   array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s' ));
          }
      }
	  
	  function iriGetLanguage($accepted) 
		{
	            return substr($accepted,0,2);
        }
	        
    function luc_dailystat_is_feed($url) {
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
    function luc_GetOS($arg)
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
      
      function luc_GetBrowser($arg)
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
      
	  function luc_CheckBanIP($arg)
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
      
      function luc_GetSE($referrer = null)
      {
          $key = null;
          $lines = file(ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/def/searchengines.dat');
          foreach ($lines as $line_num => $se)
          {
              list($nome, $url, $key) = explode("|", $se);
              if (strpos($referrer, $url) === false)
                  continue;
              // trovato se
              $variables = luc_GetQueryPairs($referrer);
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
      
      function luc_GetSpider($agent = null)
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
      
	  
      function dailystat_Print($body = '')
      {
          echo luc_dailystat_Vars($body);
      }
      
      function luc_dailystat_Vars($body)
      {
          global $wpdb;
          $table_name = $wpdb->prefix. 'dailystat';
		  $yesterday = gmdate('Ymd', current_time('timestamp') - 86400);
          $today = gmdate('Ymd', current_time('timestamp'));
		  
          if (strpos(strtolower($body), "%today%") !== false)
          {
		      $today = gmdate('Ymd', current_time('timestamp'));
              $body = str_replace("%today%", luc_hdate($today), $body);
          }
          if (strpos(strtolower($body), "%vistorstoday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE date = $today and spider='' and feed='';");
              $body = str_replace("%vistorstoday%", $qry[0]->visitors, $body);
          }
		   if (strpos(strtolower($body), "%visitorsyesterday%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(DISTINCT(ip)) as visitors FROM $table_name WHERE date = $yesterday and spider='' and feed='';");
              $body = str_replace("%visitorsyesterday%", $qry[0]->visitors, $body);
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
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(luc_dailystat_URL()) . "';");
              $body = str_replace("%thistodaypageviews%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thisyesterdaypageviews%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $yesterday AND urlrequested='" . mysql_real_escape_string(luc_dailystat_URL()) . "';");
              $body = str_replace("%thisyesterdaypageviews%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thistodayvisitors%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $today AND urlrequested='" . mysql_real_escape_string(luc_dailystat_URL()) . "';");
              $body = str_replace("%thistodayvisitors%", $qry[0]->pageviews, $body);
          }
		  if (strpos(strtolower($body), "%thisyesterdayvisitors%") !== false)
          {
              $qry = $wpdb->get_results("SELECT count(distinct ip) as pageviews FROM $table_name WHERE spider='' and feed='' AND date = $yesterday AND urlrequested='" . mysql_real_escape_string(luc_dailystat_URL()) . "';");
              $body = str_replace("%thisyesterdayvisitors%", $qry[0]->pageviews, $body);
          }
		  
          if (strpos(strtolower($body), "%os%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $os = luc_GetOS($userAgent);
              $body = str_replace("%os%", $os, $body);
          }
          if (strpos(strtolower($body), "%browser%") !== false)
          {
              $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
              $browser = luc_GetBrowser($userAgent);
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
              $body = str_replace("%toppost%", luc_dailystat_Decode($qry[0]->urlrequested), $body);
          }
          if (strpos(strtolower($body), "%topbrowser%") !== false)
          {
              $qry = $wpdb->get_results("SELECT browser,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY browser ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topbrowser%", luc_dailystat_Decode($qry[0]->browser), $body);
          }
          if (strpos(strtolower($body), "%topos%") !== false)
          {
              $qry = $wpdb->get_results("SELECT os,count(id) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY os ORDER BY totale DESC LIMIT 1;");
              $body = str_replace("%topos%", luc_dailystat_Decode($qry[0]->os), $body);
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
      
      function luc_dailystat_TopPosts($limit = 5, $showcounts = 'checked')
      {
          global $wpdb;
          $res = "\n<ul>\n";
          $table_name = $wpdb->prefix. 'dailystat';
          $qry = $wpdb->get_results("SELECT urlrequested,count(*) as totale FROM $table_name WHERE spider='' AND feed='' GROUP BY urlrequested ORDER BY totale DESC LIMIT $limit;");
          foreach ($qry as $rk)
          {
              $res .= "<li><a href='" . luc_getblogurl() . ((strpos($rk->urlrequested, 'index.php') === FALSE) ? $rk->urlrequested : '') . "'>" . luc_dailystat_Decode($rk->urlrequested) . "</a></li>\n";
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
                  $options = array('title' => 'dailystat', 'body' => 'Visitors today: %vistorstoday%');
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
              echo '<input type="hidden" id="dailystat-submit" name="dailystat-submit" value="1" /><div style="font-size:7pt;">%today% %visitorsyesterday% %visitorstoday% %pageviewstoday% %pageviewsyesterday% %thistodaypageview% %thisyesterdaypageviews% %thistodayvisitors% %thisyesterdayvisitors% %os% %browser% %ip% %visitorsonline% %usersonline% %toppost% %latesthits%
                    %topbrowser% %topos% %pagestoday% %thistotalpages% %latesthits%</div>';
          }
		  
       function widget_dailystat($args)
          {
              extract($args);
              $options = get_option('widget_dailystat');
              $title = $options['title'];
              $body = $options['body'];
              echo $before_widget;
              print($before_title . $title . $after_title);
              echo luc_dailystat_Vars($body);
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
              echo luc_dailystat_TopPosts($howmany, $showcounts);
              echo $after_widget;
          }
          register_sidebar_widget('dailystat TopPosts', 'widget_dailystattopposts');
          register_widget_control(array('dailystat TopPosts', 'widgets'), 'widget_dailystattopposts_control', 300, 110);
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
      
    function luc_dailystat_Decode($out_url)
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
      
      function luc_dailystat_URL()
      {
          $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '');
          if (my_substr($urlRequested, 0, 2) == '/?')
              $urlRequested = my_substr($urlRequested, 2);
          if ($urlRequested == '/')
              $urlRequested = '';
          return $urlRequested;
      }
      
      function luc_getblogurl()
      {
      	$prsurl = parse_url(get_bloginfo('url'));
      	return $prsurl['scheme'] . '://' . $prsurl['host'] . ((!permalinksEnabled()) ? $prsurl['path'] . '/?' : '');
      }
      
      // Converte da data us to default format di Wordpress
      function luc_hdate($dt = "00000000")
      {
          return mysql2date(get_option('date_format'), my_substr($dt, 0, 4) . "-" . my_substr($dt, 4, 2) . "-" . my_substr($dt, 6, 2));
      }
      
     
     
      function luc_Domain($ip)
      {
          $host = gethostbyaddr($ip);
          if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $host))
              return "";
          else
              return my_substr(strrchr($host, "."), 1);
      }
      
      function luc_GetQueryPairs($url)
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
    
	
		
?>