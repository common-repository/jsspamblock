=== JSSpamBlock ===
Contributors: PaulButler
Tags: comments, spam
Requires at least: 2.0.2
Tested up to: 2.1
Stable tag: 2.0

JSSpamBlock detects spam bots by sending a piece of JavaScript to the browser. This provides an effective and unobtrusive way to detect spam.

== Description ==

JSSpamBock is an effective way to reduce comment spam without your commenters even knowing. Users without JavaScript can prove their identity by simply entering a given number. In browsers without JavaScript, the user is simply asked to enter a number to prove they are human.

== Installation ==

The following instructions are for a basic installation. If you are using WP-Cache or you want to use a log file, see further below for how to achieve this.

1. Upload `jsspamblock.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Try leaving a comment on one of your posts. If everything went properly, you won't even notice a difference. Search the page's source code for `jsspamblock` to make sure the code is included in the page. **Please make sure you make a test comment to be sure it is working properly.**
4. If it didn't work (your comment was unexpectedly blocked when it should not have been), it is likely that your template does not have the comment form hook. If this is the case, you will have to manually call JS Spam Block in the template. To do this, find the "comments.php" template file and find the comment form - look for `</form>` and include the following just above it: `<?php jsspamblock_doform(); ?>`.
5. Try to post a comment again. If it still doesn't work, I would like to know. You can contact me at `<paulgb at gmail dot com>`.

Upgrading:

Replace the jsspamblock.php plugin file and update any settings you changed in the PHP file.

== Using with WP-Cache ==

[WP-Cache](http://dev.wp-plugins.org/wiki/WP-Cache) is a WordPress plugin that caches pages on the server to improve site performance and lower the use of server resources. It will work with JSSpamBlock, but you must make a few changes first:

1. Near the top of the plugin file, change the line that says `define('JSSPAMBLOCK_FORMLISTENER', true);` to `define('JSSPAMBLOCK_FORMLISTENER', false);`.
2. Find the directory of your current theme and find the comments page (usually comments.php). Find where it says `</form>` and insert the following **above** that line: `<!--mclude wp-content/plugins/jsspamblock.php--><!--/mclude--><!--mfunc jsspamblock_doform() --><?php jsspamblock_doform(); ?><!--/mfunc-->`
3. Log into the WordPress admin panel and go to the **Options** tab. From there, go to the **WP-Cache** tab. Scroll down to the bottom and click the button that says "Delete cache".
4. Go back to your blog and try adding a comment. If everything worked out, you shouldn't notice a difference.

== Logging comments ==

JSSpamBlock can be set up to log comments. In the plugin file, change the line `define('JSSPAMBLOCK_LOGFILE', '');` to `define('JSSPAMBLOCK_LOGFILE', 'yourlogfilename.txt');`, where yourlogfilename.txt is the name of your log file.  The path is relative to the base of your wordpress installation (where the index.php file is), so it is best to put the log in another directory. The log file must be editable by PHP, or else logging will not work and you may get an error.

== Saving spam ==

By default, spam posts are removed from the database. Alternatively, JSSpamBlock provides an option to keep the spam in the database and simply mark it as spam so that WordPress will ignore it. Comments marked as spam will not show up in the admin panel, so this option is only useful if you have direct access to the database or you have a plugin that looks at comments marked as spam.

To enable this, change the line `define('JSSPAMBLOCK_DELETECOMMENTS', true);` to `define('JSSPAMBLOCK_DELETECOMMENTS', false);` in the plugin file.

== Changelog ==

* March 22, 2007 - Initial release (1.0)
* April 1, 2007 - 1.1
	* Fixed installation instructions to work with themes without comment form hook.
	* Added message to detect improper installation.
	* Prevented form from being shown more than once if installed improperly.
* April 1, 2007 - 1.2
	* Fixed wp_die() for earlier versions of WordPress
* April 2, 2007 - 1.3
	* Fixed important comment deleting bug - Thanks to Stephen Darlington of [ZX81.org.uk](http://zx81.org.uk/) for finding the bug.
* April 17, 2007
	* Added SQL code to readme.txt after on request.
* April 18, 2007
	* Fixed bug when plugin was installed in a folder, found by david_kw of [exfer network](http://www.exfer.net/blog/). Thanks also to Ben for debugging info.
* October 20, 2007
	* Uses sessions instead of a database
	* Cleaned up the code - now uses a class instead of just functions

== Screenshots ==

1. What a user without JavaScript installed will see.
2. What a bot will see, or a no-JS user who did not type in the code.
	
== License ==

Copyright (c) 2007 Paul Butler

This software is provided 'as-is', without any express or implied warranty. In no event will the authors be held liable for any damages arising from the use of this software.

Permission is granted to anyone to use this software for any purpose, including commercial applications, and to alter it and redistribute it freely, subject to the following restrictions:

1. The origin of this software must not be misrepresented; you must not claim that you wrote the original software. If you use this software in a product, an acknowledgment in the product documentation would be appreciated but is not required.
2. Altered source versions must be plainly marked as such, and must not be misrepresented as being the original software.
3. This notice may not be removed or altered from any source distribution.
