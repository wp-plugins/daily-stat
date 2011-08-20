=== Daily Stat ===

Contributors: luciole135
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F9J2LJPDE5UQ8
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, spider, bot, page, post
Requires at least: 2.5
Tested up to: 3.2
Stable Tag: 1.3.1

A fork of Statpress Visitors for very limited space like there is in Free hosting.


== Description ==

* This plugin (a lite fork of StatPress Visitors) shows the real-time statistics on your blog. It collects information about visitors, spiders, search keywords, feeds, browsers, OS, etc., as Statpress Visitors. 
* Daily stat allows you to make stat on the free web hosting that **prohibit** them by **autodelete** records **older** than **2** **days**.
* The **"spy robots"** lets you know which pages were indexed by search robots.
* The **"referrer"** page shows what referrer bring the most visitors to your website.

**NEW** **in** **1.3** :

* ALL **logos** and **icons** with tooltip : "**search** **engines**", "**spider**", "**RSS** **feeds**, **browsers** and **OS** are represented by their **logo**. **Internet** **domains**" and **country**, are represented by a **flag**. All icons, flags and logo display the correct name by a **tooltip** at mouse-over. 
* Two new informations : the **language** and **country** in addition to the internet domain. Indeed, the "original StatPress" stores languages spoken as the country which is not true, Americans speak English and so far are American. To remedy this error, "StatPress Reloaded" stored in the data table (column "nation") the Internet domain. So we added two columns in the table of data: the language and the country given by the visitor's browser. 
* On the "spy visitors" page, the flag displayed in the first place is the country given by the visitor's browser (preceded by "http country"), if it is not known then, secondly, it's the flag of the internet domain that is displayed (preceded by "http domain"). If neither is given, then querying the free internet service "hostip.info" (preceded by "hostip country").
* In the main page, the country's flag is displayed only if different from the Internet domain. If the same flag is displayed, then the tooltips do not give the same indication. Indeed, some Internet domains correspond to several countries and some countries have regions with theirs own internet domain.
* The functions of the administration part of the plugin are no longer stored in RAM when a visitor visits the site, this frees up RAM unnecessarily consumed otherwise. The functions and administration pages are stored in memory RAM only if the Dashboard is visible. Thanks to xknown.
* The tables "last terms search", "Last referrers", "Last Feeds" and "Last spiders" on the main page are more informatives. 

**Still** **in** **1.3** :

* **SQL** queries **optimization** in the pages "Visitors Spy" and "Spy Bot" by the use of the natural index of the datatable. Now these pages are made in **only** **one** **SQL** **query**. The previous versions need as many SQL queries that there is IP or Bot displayed on the page. The speed is 3 times faster. 
* **New** **way** to **count** the **RSS** feed by IP. Thus, there are **two** separate counts of RSS: as far as total subscription on every page (pageviews feeds), as far as visitors subscribers(visitors feeds).
* The **"yesterday"** page show you the number of "unique visitors", "page views", subscriptions to "RSS visitors feeds", subscriptions to "RSS pageviews feeds"  and "search engines" (spider) for each page and posts (with or without visits) of your website at the date of yesterday. 
* In the options page you can choose the number of IP displayed on **"Visitor** **Spy"** page (20, 50 or 100) and the number of visits for each IP (20, 50 or 100).

= DB Table maintenance =

Daily stat automatically delete older records than 2 days (to have a very short database) and **automaticaly** **optimise** the daily-stat datatable after the removal of olds datas.

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

You could add these values everywhere! Daily Stat offers a new PHP function *Daily_stat_Print() i.e. StatPress_Print("%totalvisits% total visits.");
Put it whereever you want the details to be displayed in your template. Remember, as this is PHP, it needs to be surrounded by PHP-Tags!

= Ban IP =

You could ban IP list from stats editing def/banips.dat file.

== Installation ==
Unzip file and Upload "Daily-stat" directory in wp-content/plugins/ . Then just activate it on your plugin management page.
That's it, you're done!

== Frequently Asked Questions ==
= What is the difference between "visitors Feeds" and "Pageviews feeds"? =

Quite simply, if a single visitor subscribed to RSS feeds on pages 5, then "Visitors Feeds" is 1 and "Pageviews Feeds" is 5.
= Why "Visitors Feeds" and "pageviews feeds" are not the same account in the "yesterday" and  "Main Overview" pages ? =

This is because the calculations are not the same!
On the "Main Overview", all pages are counted, even those that are automatically generated by WordPress (category, etc.).
On the "Yesterday", only the pages you have actually written and that are stored in your database are taken into account, those generated by WordPress are not counted.
= Isn’t it possible to make it work with network of sites ? =

You can use http://wordpress.org/extend/plugins/proper-network-activation/
= Where can I get help? =

Please visit the http://additifstabac.free.fr/index.php/daily-stat-new-statistics-wordpress-plugin/

== Screenshots ==
http://www.flickr.com/photos/59604063@N05/sets/72157626523354350/

== Changelog ==
= 1.3.1=
* Replacement of all the WordPress functions deprecated by the new WordPress functions.
* Add a new table in the main page : "Undefined agent", the agent without definition in StatPress Visitors, then you can update it by yourself.
* Display the entire name of page in the main page, do no made abbrevia

= 1.3 =
* PHP optimization : Daily stat 1.3 make more with less memory RAM use than the previous versions.
* FULL PHP 5.3 and higher compatibility
* On "Bot spy", "more info" show the agent and ip of the bot.
* **Spam** **Bots** are detected with new definitions.
* Added in the options pages "Do no collect logged user", "Do no collect spider"

= 1.2.1 =
* **SQL** queries **optimization** in the pages "Visitors Spy" and "Spy Bot" by the use of the natural index of the datatable. Now these pages are made in **only** **one** **SQL** **query**. The previous versions need as many SQL queries that there is IP or Bot displayed on the page. The speed is 3 times faster. 
* Added smalls **Icons** of browsers and OS in the Main "Overview" page.
* Display last 40, pages, referrer, searchs terms and spider in the "Overview" page.
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

