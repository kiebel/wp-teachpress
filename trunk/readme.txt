=== teachPress ===
Contributors: Michael Winkler
Tags: management, publications, enrollments, teachpress
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 0.22.0

With this plugin you can easy manage courses, enrollments and publications.

== Description ==
teachPress is a course and publication managment plugin for WordPress.  

= Features: =
* Publication management
* Course management
* Student management
* Enrollments system with optional waiting lists
* xls-export
* Print templates for attendance lists
* Ingegrated search functions
* Some shortcodes for an easy using in posts an pages

= Supported Languages =
* Deutsch
* English  

**Disclaimer:** Use at your own risk. No warranty expressed or implied is provided.  

== Credits ==

Copyright 2008-2009 by Michael Winkler

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

== Installation ==

1. Download the plugin.
2. Extract all the files. If you will change some parameters of teachpress, so you can do this in parameters.php
3. Upload everything (keeping the directory structure) tp your plugins directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.

**For updates:**

!!!IMPORTANT: Please don't use the auto update if you have made changes on parameters.php!!!!

1. Download the plugin.
2. Delete all files in the 'wp-content/plugins/teachpress/' directory except parameters.php
3. Upload all files (except parameters.php) to the '/wp-content/plugins/teachpress' directory.
4. Go in WordPress menu to Courses->Settings and click on "Update to ....".

== Screenshots ==
You could find some screenshots at: http://www.mtrv.kilu.de/teachpress/teachpress-images/

== Frequently Asked Questions ==

= 1. Adding Frontend Pages =

Create a new Page in WordPress and write the following strings in the textfield:  
for enrollment page: **< !--LVS-->** (without spaces)  
for course overview: **< !--LVS2-->** (without spaces)  

= 2. Shortcode: = 

For the dates of courses:  
= [tpdate id="x"] =  
* x = Course ID

for publication lists with tag-cloud:  
= [tpcloud id="x"] =  
* x = WP-User-ID (0 for all)

for normal publication lists:  
= [tplist (args)] =  
* user - WP User-ID (0 for all)  
* tag - Tag-ID  
* year  
* headline - 0(off) or 1(on)  

= 3. CSS-Classes for publication lists =
Use the following CSS-Classes to the style.css in your theme:  
.tp_publication – container  
.tp_pub_autor – autor  
.tp_pub_titel – title  
.tp_pub_typ – type  

== Changelog ==
= 0.22.0 =
* Changed: Logo and style
* Changed: Script and style load
* Bugfix: Fixed usability bugs
= 0.20.4 =
* Changed: Publication lists sorted by year
* Changed: New design for publication lists
* Bugfix: Bugfix for installer
* Bugfix: Fixed bug when adding new publications
* Bugfix: Fixed bug when adding new course
* Bugfix: Fixed unauthorized access to the teachpress directory
= 0.20.3 =
* Bugfix: Fixed bug in "tp_date" shortcode
* Bugfix: Fixed bug in courses overview
* Bugfix: Fixed bug by adding new course
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
