=== Statpress Visitors ===

Contributors: luciole135
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F9J2LJPDE5UQ8
Tags: stats, statistics, widget, admin, sidebar, visits, visitors, pageview, feed, referrer, spy, spider, bot, page, post
Requires at least: 2.5
Tested up to: 3.1.1
Stable Tag: 1.0.10

A fork of Statpress Reloaded with new "spy bot", "visitor", "view", "feeds" and "referrer" page.


== Description ==

This plugin (a highly improved fork of StatPress Reloaded) shows the real-time statistics on your blog. It corrects many programming errors of Statpress and statPress Reloaded. It collects information about visitors, spiders, search keywords, feeds, browsers, OS, etc., as Statpress Reloaded. It is compatible with all plugins Statpress derivative (except "ks_stat reloaded" and "newstatpress" who have their own data tables).

A new counting method significantly reduces the number of SQL query in the main page. Now, the graph is made in only 4 SQL query, then Statpress Visitors is faster than all others fork of statpress : 2 seconds than 10 sec with a large database of 45,000 datas. These new method of counting allows the counting of visitors, page views, search engines and subscription to RSS feeds for each page. This can not do StatPress and all the others Statpress derivative plugins with their method of counting. Then it gives an accurate view of traffic to your website: You can see the number of unique visitors, page views, subscriptions to RSS feeds and search engines for each page and posts of your website for every day saved in the database by charts of 7, 15, 21, 31 or 62 days depending on the option chosen.

Spy visitors has been redesigned. Now it displays the most recent visits to the oldest. This corrects an error of Statpress and Statpress reloaded. Indeed, if a visitor came yesterday and today you will see his first visit to yesterday's date and the other after it. So you can not see the loyalty of regular visitors to your site: each visitor is displayed on the date of his first visit. In the options page you can choose the number of IP displayed on each page (20, 50 or 100) and the number of visits for each IP (20, 50 or 100).

The spy robots is now available. This lets you know which pages were indexed by search robots.

The referrer page is now available. This shows what referrer bring the most visitors to your website.



= DB Table maintenance =

StatPress can automatically delete older records to allow the insertion of newer records when your space is limited.

= StatPress Widget / StatPress_Print function =

The widget is customizable. These are the available variables:

* %thistotalvisits% - this page, total visits
* %thistotalpageviews% - this page, total pageviews
* %since% - Date of the first hit
* %visits% - Today visits
* %totalvisits% - Total visits
* %os% - Operative system
* %browser% - Browser
* %ip% - IP address
* %visitorsonline% - Counts all online visitors
* %usersonline% - Counts logged online visitors
* %toppost% - The most viewed Post
* %topbrowser% - The most used Browser
* %topos% - The most used O.S.
* %thistotalpages% - Total pageviews so far
* %pagestoday% - Pageviews today
* %pagesyesterday% - Pageviews yesterday
* %latesthits%

You could add these values everywhere! StatPress offers a new PHP function *StatPress_Print()*.
* i.e. StatPress_Print("%totalvisits% total visits.");
Put it whereever you want the details to be displayed in your template. Remember, as this is PHP, it needs to be surrounded by PHP-Tags!

= Ban IP =

You could ban IP list from stats editing def/banips.dat file.


= Update Statpress database =
* The update of statpress database has been redesigned, now it's not update the entries when you update Search engine and the referrer is your website.
  This updates only day of the current period of time. This allows to update large database with low bandwidth website. 
  To update another periode simply click on the "Periode Days : 1 2 3..."
* To add an new search engine on /def/searchengines.dat make like on these example :
  If the referrer is : http://www.google.fr/url?sa=t&amp;source=web&amp;cd=22&amp;ved=0CCMQFjABOBQ&amp;url=http%3A%2F%2Fadditifstabac.free.fr%2Findex.php%2Ftabac-rouler-pourcentage-additifs-taux-nicotine-goudrons%2F&amp;rct=j&amp;q=rasta%20chill%20tobacco&amp;ei=F5VeTeOtAo2t8QOTyYBa&amp;usg=AFQjCNEw04UOF9nDHWpgmkNga6l6X6SexA
  add these line on statpress-visitors/def/searchengines.dat :
  google|www.google.|q 
  where "google" is the name of the search engine, "www.google." is the URL of the search engine, "q" is the key of the query search q=rasta%20chill%20tobacco
  then update the statpress database
* to add an new spider on /def/spider.dat make like on these example :
  if the spider is picsearch add these line :
  picsearch|www.picsearch.com| where "picsearch" is the name of the spider and "www.picsearch.com" the URL.
  Then update the statpress database
* In the Dashboard click "StatPress Visitors", then "Update Search Engine" for Update the Search engine or "Update Feed OS Browser Spider" and wait until it will add/update STATPRESS's database content.



== Installation ==
Unzip file and Upload "statpress-visitors" directory in wp-content/plugins/ . Then just activate it on your plugin management page.
That's it, you're done!
(Note: If you have been using an other derivative StatPress plugin before, deactivate it. Your data is taken over!)


== Frequently Asked Questions ==

= Where can I get help? =

Please visit the http://additifstabac.free.fr/index.php/statpress-visitors-new-statistics-wordpress-plugin/


== Screenshots ==
http://www.flickr.com/photos/59604063@N05/sets/72157626522412772/

== Changelog ==
= 1.0.10 =
correct "spy visitor" to work like in version 1.0.5 and lower : display "arrived from...searching..."
= 1.0.9 =
* add these variable
  %thistotalpageviews% - this page, total pageviews
= 1.0.8 =
* correct an URL error on 'Spy visitors' and 'Spy bot' page when there are multiple pages.
= 1.0.7 =
* Better URL for Statpress-Visitors pages.
* correct an URL error on 'Overview' page when there are multiple pages.
* New menu icon.
= 1.0.6 =
* Now when selecting one of the Statpress Visitors pages, such as visitor spy, the menu indicates that it is this page who is selected (shaded background & notch on left side).
* The main menu item is now "Statpress V" to keep it on a single line.
= 1.0.5 =
* this version correct some error when dadabase is empty 
= 1.0.4 =
 this version correct minimum capability to view stat
= 1.0.3 =
* This version 1.0.3 optimize some SQL query in "visitor and view" page, then it work a little faster.
= 1.0.2 =
* This version 1.0.2 optimize some SQL query in "feed page".
= 1.0.1 =
* statpress-visitors 1.0.1 correct a SQL query to work faster in "Overview" main page.
* This version 1.0.1 is much faster in displaying the main "Overview" page.
* add Cityreview spider in def/spider.dat

== Upgrade Notice ==
= 1.0.3 =
* This version 1.0.3 optimize some SQL query in "visitor and view" page, then it work a little faster.

= 1.0.2 =
* This version 1.0.2 optimize some SQL query in "feed" page, then it work a little faster.

= 1.0.1 =
* statpress-visitors 1.0.1 correct a SQL query to work faster in "Overview" main page.


