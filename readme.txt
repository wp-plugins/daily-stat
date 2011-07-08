=== Daily Stat ===

Contributors: luciole135
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F9J2LJPDE5UQ8
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, spider, bot, page, post
Requires at least: 2.5
Tested up to: 3.2
Stable Tag: 1.2

A fork of Statpress Visitors for very limited space like there is in Free hosting.


== Description ==

* This plugin (a lite fork of StatPress Visitors) shows the real-time statistics on your blog. It collects information about visitors, spiders, search keywords, feeds, browsers, OS, etc., as Statpress Visitors. 

* Daily stat allows you to make stat on the free web hosting that prohibit them by autodelete records older than 2 days.

* NEW in 1.2 :
* SQL queries optimization in the pages "Visitors Spy" and "Spy Bot" by the use of Set Theory. Now these pages are made in only one SQL query. The previous versions need as many SQL queries that there is IP or Bot displayed on the page. The speed is 3 times faster. 
* Detection of the referring page when the referrer is Facebook. In this case, in previous versions, all page views were called "fb_xd_fragment", now, their real name is displayed.
* Icons of browsers and OS in the Main "Overview" page.
* Display last 40, pages, referrer, searchs terms and spider in the "Overview" page.

* Still in 1.2 :  
* Added a new way to count the RSS feed by IP. Thus, there are two separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
               
* Better design of the Yesterday page. New row in the table: total for all URL of "visitors", "visitors feeds", "pageviews", "pageviews feeds" and "spider". 
               
* Now, the yesterday page display all the pages and posts of your website, with or without visits. 
  
* The "yesterday" page show you the number of "unique visitors", "page views", subscriptions to "RSS visitors feeds", subscriptions to "RSS pageviews feeds"  and "search engines" (spider) for each page and posts of your website at the date of yesterday.
  
* In the options page you can choose the number of IP displayed on "Visitor Spy" page (20, 50 or 100) and the number of visits for each IP (20, 50 or 100).

* The "spy robots" lets you know which pages were indexed by search robots.

* The "referrer" page shows what referrer bring the most visitors to your website.

= DB Table maintenance =

Daily stat automatically delete older records than 2 days to have a very short database.

= Daily Stat Widget / Daily_Stat_Print function =

The widget is customizable. These are the available variables:
* %today% - date of today
* %visitorsyesterday% - Visitors yesterday
* %visitorstoday% - Visitors today
* %pageviewstoday% - Pageviews today
* %pageviewsyesterday% - Pageviews yesterday
* %thistodaypageview% - this page, today total pageview
* %thisyesterdaypageviews% - this page, yesterday total pageview
* %thistodayvisitors% - this page, today total visitors
* %thisyesterdayvisitors% - this page, yesterday total visitors
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %latesthits% - last 10 search term



You could add these values everywhere! Daily Stat offers a new PHP function *Daily_stat_Print()*.
* i.e. StatPress_Print("%totalvisits% total visits.");
Put it whereever you want the details to be displayed in your template. Remember, as this is PHP, it needs to be surrounded by PHP-Tags!

= Ban IP =

You could ban IP list from stats editing def/banips.dat file.

== Installation ==
Unzip file and Upload "Daily-stat" directory in wp-content/plugins/ . Then just activate it on your plugin management page.
That's it, you're done!

== Frequently Asked Questions ==

= Where can I get help? =

Please visit the http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/

== Screenshots ==
http://www.flickr.com/photos/59604063@N05/sets/72157626523354350/

== Changelog ==
= 1.2 =
* detection of the page visited when the referrer is Facebook.
* SQL queries optimisation in "Spy bot" and "spy visitors" page.
* Add icon of browsers and OS in the Main "Overview" page.
* Display last 40 "hits", "searchs terms", "referrer" and "spider" in the "Overview" page.
* Add definitions in os.dat and browsers.dat files.

= 1.1 =
* Added a new way to count the RSS feed by IP. Thus, there are two separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
* Better design of the Yesterday page. New row in the table: total for all URL of "visitors", "visitors feeds", "pageviews", "pageviews feeds" and "spider". Now, the yesterday page display all the pages and posts of your website, with or without visits. 
* Added new variable %today%.
* Better prevention of SQL injections by the use of the $wpdb->insert WordPress function.
= 1.0.1 =
* Every day, automatic optimization of the data table 'dailystat' after the removal of olds data. Then, now, the data table 'dailystat' is always optimized.

== Upgrade Notice ==

