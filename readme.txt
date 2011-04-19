=== Daily Stat ===

Contributors: luciole135
donate link : https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F9J2LJPDE5UQ8
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, spider, bot, page, post
Requires at least: 2.5
Tested up to: 3.1.1
Stable Tag: 1.0

A fork of Statpress Visitors for very limited space like there is in Free hosting.


== Description ==

This plugin (a lite fork of StatPress Visitors) shows the real-time statistics on your blog. It collects information about visitors, spiders, search keywords, feeds, browsers, OS, etc., as Statpress Visitors. 
Daily stat allows you to make stat on the free web hosting that prohibit them by autodelete records older than 2 days.
The "yesterday" page show you the number of unique visitors, page views, subscriptions to RSS feeds and search engines for each page and posts of your website at the date of yesterday.

In the options page you can choose the number of IP displayed on « Visitor Spy » page (20, 50 or 100) and the number of visits for each IP (20, 50 or 100).

The spy robots is still available. This lets you know which pages were indexed by search robots.

The referrer page is still available. This shows what referrer bring the most visitors to your website.

= DB Table maintenance =

Daily stat automatically delete older records than 2 days to have a very short database.

= Daily Stat Widget / Daily_Stat_Print function =

The widget is customizable. These are the available variables:

* %visitsyesterday% - Visits yesterday
* %visitstoday% - Visits today
* %thistodaypageview% - this page, today total pageview
* %thisyesterdaypageviews% - this page, yesterday total pageview
* %thistodayvisits% - this page, today total visits
* %thisyesterdayvisits% - this page, yesterday total visits
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %pageviewstoday% - Pageviews today
* %pageviewsyesterday% - Pageviews yesterday
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

== Upgrade Notice ==

