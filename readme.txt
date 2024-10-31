=== SBS Blogroll ===
Contributors: someblogsite
Donate link: http://www.someblogsite.com/donate.php
Tags: rss, atom, feed, blogroll, link
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: trunk

Dynamic blogroll based on RSS/Atom feeds.

== Description ==

I noticed that my WordPress blog had a Links widget for the sidebar, but it was static.  I couldn't tell that someone hadn't posted anything since my last visit, so I searched for a widget that would make the Links widget more dynamic.

I found RSS Blogroll. It did basically what I wanted, but it didn't look quite right to me.  I added a favicon option and also set the blog title to be a link.

This widget automatically grabs the latest posts from whichever blogs you want (enter the feed URLs into the widget setup) and displays those whenever someone visits your blog.  Links can be displayed chronologically, and you can click on the link to either the other blog or the specific post that is new.  

To aid in the aesthetics department, this widget displays the favicon of the target blog next to each link.

More information, including configuration and CSS tips, is at [Some Blog Site](http://www.someblogsite.com/web-stuff/sbs-blogroll "SBS Blogroll")

== Installation ==

1. Upload `sbs-blogroll.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the Appearance -> Widgets menu to select and configure SBS Blogroll

== Frequently Asked Questions ==

= Aren't you just mimicking Blogger? =

Maybe some people prefer the Blogger look...  If not, this widget is configurable so that you can turn off the favicon.

= How do I fix the error "Fatal error: Allowed memory size of 33554432 bytes exhausted (tried to allocate X bytes) in /path/wp-includes/wp-db.php on line 529"? =

That is described in [this WordPress page](http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP).  You need to increase WP_MEMORY_LIMIT, since your web server has run out of memory.

The problem is not anything with SBS Blogroll; rather, SBS Blogroll was the straw that broke the camel's back.  If you look at the error message, the amount of memory that this plugin is requesting is probably a couple hundred kilobytes.  And if you look at the allowed memory size, that will be dozens of megabytes.  So you probably have a bunch of other plugins or applications (or WordPress itself) that are using most of your memory.

== Screenshots ==

1. Example use of SBS Blogroll in action

== Changelog ==

= 0.4 =
* Added option to open links in a new window or the same window.  Thanks to [Pablo](http://www.lamiradaaleste.com).

= 0.3 =
* Fixed favicon configuration switch.  Thanks to [Francisco Puga](http://conocimientoabierto.es/).

= 0.2 =
* Fixed favicons to work with IE (and still work with Firefox)

= 0.1 =
* Initial version

== Original Code Credit ==

This code is a tweak of RSS Blogroll by pantsonhead.
Visit [pantsonhead](http://www.pantsonhead.com/wordpress/rss-blogroll/ "RSS Blogroll page") for his original code.

