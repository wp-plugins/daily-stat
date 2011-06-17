=== Daily Stat ===

Contributors: luciole135
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F9J2LJPDE5UQ8
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, spider, bot, page, post
Requires at least: 2.5
Tested up to: 3.1.3
Stable Tag: 1.1

A fork of Statpress Visitors for very limited space like there is in Free hosting.


== Description ==

* This plugin (a lite fork of StatPress Visitors) shows the real-time statistics on your blog. It collects information about visitors, spiders, search keywords, feeds, browsers, OS, etc., as Statpress Visitors. 

* Daily stat allows you to make stat on the free web hosting that prohibit them by autodelete records older than 2 days.

* NEW in 1.1 : Added a new way to count the RSS feed by IP. Thus, there are two separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
             Better design of the Yesterday page. New row in the table: total for all URL of "visitors", "visitors feeds", "pageviews", "pageviews feeds" and "spider". 

* The "yesterday" page show you the number of "unique visitors", "page views", subscriptions to "RSS visitors feeds", subscriptions to "RSS pageviews feeds"  and "search engines" (spider) for each page and posts of your website at the date of yesterday.

* In the options page you can choose the number of IP displayed on "Visitor Spy" page (20, 50 or 100) and the number of visits for each IP (20, 50 or 100).

* The spy robots is still available. This lets you know which pages were indexed by search robots.

* The referrer page is still available. This shows what referrer bring the most visitors to your website.

= DB Table maintenance =

Daily stat automatically delete older records than 2 days to have a very short database.

= Daily Stat Widget / Daily_Stat_Print function =

The widget is customizable. These are the available variables:
* %today% - date of today
* %visitorsyesterday% - Visits yesterday
* %visitorstoday% - Visits today
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
= 1.1 =
* Added a new way to count the RSS feed by IP. Thus, there are two separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
* Better design of the Yesterday page. New row in the table: total for all URL of "visitors", "visitors feeds", "pageviews", "pageviews feeds" and "spider". 
* Added new variable %today%.

= 1.0.1 =
* Every day, automatic optimization of the data table 'dailystat' after the removal of olds data. Then, now, the data table 'dailystat' is always optimized.

== Upgrade Notice ==

