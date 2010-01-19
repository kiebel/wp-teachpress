=== teachPress ===
Contributors: Michael Winkler
Tags: management, publications, enrollments, teachpress
Requires at least: 2.8
Tested up to: 2.9.1
Stable tag: 0.40.0

With this plugin you can easy manage courses, enrollments and publications.

== Description ==
teachPress is a powerful course and publication managment plugin, which is published under the terms of GPL-License. You can use it with WordPress 2.8.0 or higher.

New in Version 0.40.0: The plugin supports ISSN and now you can add images to publications. But, it's currently not possible to insert images directly from the WordPress Media Library, so you must insert the url manually. This bug would be fixed with the next release.

For more informations have a look in the FAQ, or see [here](http://www.mtrv.kilu.de/teachpress/).

= Features: =
* Publication management
* Course management
* Student management
* Enrollments system with optional waiting lists
* xls/csv-export
* Print templates for attendance lists
* Integrated search functions
* Some shortcodes for an easy using in posts an pages
* **New**: ISSN support
* **New**: An own widget for displaying books in the sidebar


= Supported Languages =
* English 
* Deutsch

= Disclaimer =  
Use at your own risk. No warranty expressed or implied is provided.  

== Credits ==

Copyright 2008-2010 by Michael Winkler

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

**Special thanks for the great support:**  
[Chair of Business Information Systems, Chemnitz University of Technology](http://www.tu-chemnitz.de/wirtschaft/wi2/wp/en/)

== Installation ==

1. Download the plugin.
2. Extract all the files. 
3. For the CSV/XLS-export please define your root directory path in the parameters_sample.php and rename the file to parameters.php
3. Upload everything (keeping the directory structure) to your plugins directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.

**For updates:**

1. Download the plugin.
2. Delete all files in the 'plugins/teachpress/' directory except parameters.php.
3. Upload all files to the 'plugins/teachpress' directory.
4. Go in the backend to Courses->Settings and click on "Update to ....".

== Upgrade Notice == 

= 0.40.0 =
Don't forget to click on the update button (teachpress settings page) after upgrading.

= 0.30.0 =
For 0.22.0 Users: Please rename your teachPress database tables after the upgrade! ( `teachpress_ver` --> `[your wp_prefix]_teachpress_ver` )  

== Screenshots ==
[Screenshots](http://www.mtrv.kilu.de/teachpress/teachpress-images/)   

== Frequently Asked Questions ==

= How I can added the Frontend Pages? =

Create a new Page in WordPress and write the following strings in the textfield (html-mode):  
for enrollment page: **`<!--LVS-->`**  
for course overview: **`<!--LVS2-->`** 

= How I can use the shortcodes? = 

For the dates of courses:  
= [tpdate id="x"] =  
* x = Course ID

for publication lists with tag-cloud:
= [tpcloud (args)] =  
* user = WP-User-ID (0 for all)
* minsize = min. font size in the tag-cloud
* maxsize = max. font size in the tag-cloud
* limit = number of tags in the tag-cloud

for normal publication lists:  
= [tplist (args)] =  
* id - WP User-ID (0 for all)  
* tag - Tag-ID  
* year  
* headline - 0(off) or 1(on)  

= How I can change the style of teachPress? =
You can edit all frontend styles in the teachpress_front.css (Automatic updates overwrites your changes!) or you can use the following CSS-classes in your theme to change the style of publications:  
.tp_publication  
.tp_pub_autor  
.tp_pub_titel  
.tp_pub_typ  
.tp_pub_tags  
and following classes to change the style in the course overview:  
`#anzeigelvs h3`  
.tp_lvs_container  
.tp_lvs_name  

== Changelog ==
= 0.40.0 =
* New: teachPress books widget
* New: Add images to your publications
* New: Related pages for publications
* New: Related pages for courses
* New: ISSN field for publications
* Changed: Many little changes in the enrollment form (now display.php)
* Changed: Many file names
* Changed: Better script loading
* Bugfix: Fixed bug when you add a student manually
* Bugfix: Fixed bug in sort order of terms
* Bugfix: Fixed charset and collation for teachpress tables
* Bugfix: Fixed bug when parent and child course has the same name
* Killed: URL field for courses
= 0.32.0 = 
* Changed: Design for course overview
* Changed: Default language changed from german to english
* Bugfix: Fix bug when student unsubscribes from a course (Thanks to Jean T. )
* Bugfix: Fix bug in the course overview (frontend)
= 0.30.2 =
* Fix a little problem with the version name, sorry for this.
= 0.30.0 =
* New: Copy function for courses
* New: Simple CSV-export for enrollments
* New: Free selectable names for child courses
* New: More parameters for the `[tpcloud]` shortcode
* New: Using wpdb->prefix for database names
* Changed: Order of courses in the backend overview
* Changed: Structure of registration form
* Changed: Tag-Cloud creation
* Changed: Course search
* Bugfix: Fixed bug in 'add courses' form
* Bugfix: Fixed bug by using students search
* Bugfix: Fix bug in tp_get_message()
* Killed: XML-export for enrollments
= 0.22.0 =
* Changed: Logo and style
* Changed: Script and style load
* Bugfix: Fixed usability bugs
= 0.20.4 =
* Changed: Publication lists sorted by year
* Changed: New design for publication lists
* Bugfix: Bugfix for installer
* Bugfix: Fixed bug when adding new publications
* Bugfix: Fixed bug when adding new courses
* Bugfix: Fixed unauthorized access to the teachpress directory
= 0.20.3 =
* Bugfix: Fixed bug in "tp_date" shortcode
* Bugfix: Fixed bug in courses overview
* Bugfix: Fixed bug by adding new courses
* Bugfix: Fixed bug in parent listmenus
* Bugfix: Fixed security-Bug in excel export
* Added: Add XML-Export for Enrollments
* Added: Add new E-Mail functions
= 0.20.2 =
* Added: More filter for publications (Frontend)
= 0.20.1 =
* Changed: Redesigned settings page
= 0.20.0 =
* Changed: Better WordPress integration
* New: Search function for courses
* New: Tag management
* New: English language added