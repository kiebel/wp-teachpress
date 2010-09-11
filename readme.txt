=== teachPress ===
Contributors: Michael Winkler
Tags: management, publications, enrollments, teachpress, education, course management, BibTeX, bibliography
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 2.0.b1

With this plugin you can easy manage courses, enrollments and publications.

== Description ==
The plugin unites a course management system with integrated enrollments and a BibTeX compatible publication management. teachPress is optimized for the needs of professorships and research groups. You can use it with WordPress 2.8.0 or higher.

For more information see [here](http://www.mtrv.kilu.de/teachpress/).

= Features: =
* BibTeX compatible publication management
* Course management with enrollment system
* Student management
* Import and export function for publications (BibTeX format)
* xls/csv-export for course lists
* RSS-feed for publicaitons
* Widget for displaying books in the sidebar

= New features of teachPress 2.0: =
* New publication system with BibTeX support, 31 data fields, 15 publication types
* New shortcode "tpsingle" for displaying single publications
* New shortcode "tpcourselist" for displaying a course overview (replaced `<!--LVS2-->`)
* New shortcode "tpenrollments" for displaying the enrollement system (replaced `<!--LVS-->`)
* New Shortcode "tppost" for displaying parts of a post only for registered students
* Add images for courses
* Redesigned user interface

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

**Special thanks for supporting:**  
[Chair of Business Information Systems, Chemnitz University of Technology](http://www.tu-chemnitz.de/wirtschaft/wi2/wp/en/)

== Installation ==

1. Download the plugin.
2. Extract all the files. 
3. Define your wp-root directory path in the parameters_sample.php and rename the file to parameters.php (it is only for RSS-Feeds and CSV/XLS-Export)
4. Upload everything (keeping the directory structure) to your plugins directory.
5. Activate the plugin through the 'plugins' menu in WordPress.

**For updates:**

1. Download the plugin.
2. Delete all files in the 'plugins/teachpress/' directory except parameters.php.
3. Upload all files to the 'plugins/teachpress' directory.
4. Go in the backend to Courses->Settings and click on "Update to ....".

== Upgrade Notice ==
= 2.0.b1 =
teachpress 2.0 uses a brand new database structure, so save your database before upgrading!

== Screenshots ==
1. Screenshot Add publication menu
2. Screenshot Add course menu
3. Screenshot teachPress books widget

[More](http://www.mtrv.kilu.de/teachpress/teachpress-images/)   

== Frequently Asked Questions ==

= How can I add a course list in a page or post? = 
When you writing a post or page use the following tag: [tpcourselist]

= How can I add the enrollment system in my blog? =
Create a new page or post and use the following tag: [tpenrollments]

= How can I add longer course desciptions? =
You can write long course desciptions, as normal WordPress pages. The main function of teachPress is only to connect the static page with a course overview and an enrollment system. For this you can specify a related pages for your course.

= How can I display images in publication lists? =
An example: [tplist id="0" image="left" image_size="70"]. Important: You must specify both image parameters.

= I see only error messages if I use the RSS-Feed for publications or the xls/csv-Export for enrollments. What's wrong? =
Before you can use this features you must define the $root parameter in the parameters.php: You find in the teachPress directory the file: parameters_sample.php. Open this file and change the $root paramter (you find some examples there). After that rename the file to parameters.php and upload the file in the teachPress directory.

= Reference of shortcode parameters =
= For course information: [tpdate id="x"] =  
* id = Course-ID

= For the enrollments system [tpenrollments] =
* no parameters

= For course lists [tpcourselist image="x" image_size="y"] =
* image = image position: left, right or bottom (default: none)
* image_size = maximum size in of an image in px(default: 0)

= For protected posts: [tpost id="x"] =
* id = Course-ID

= For single publications [tpsingle id="x"] =
* id = Publication-ID

= For publication lists with tag-cloud: [tpcloud (args)] =  
* user = WP User-ID (0 for all)
* minsize = min. font size in the tag-cloud
* maxsize = max. font size in the tag-cloud
* limit = number of tags in the tag-cloud
* image = image position: left, right or bottom (default: none)
* image_size = maximum size in of an image in px (default: 0)
* anchor = html link anchor on(1) or off (0), default: 1

= For normal publication lists: [tplist (args)] =  
* id - WP User-ID (0 for all)  
* tag - Tag-ID  
* year  
* headline - 0(off) or 1(on), default: 1   
* image = image position: left, right or bottom (default: none)
* image_size = maximum size of an image (default: 0)

== Changelog ==
= 2.0.b1 - (11.09.2010) =
* New: BibTeX support (bibtex export, more data fields, more publication types)
* New: Shortcode "tpsingle" for displaying single publications
* New: Shortcode "tpcourselist" for displaying a course list
* New: Shortcode "tpenrollments" for displaying the enrollement system
* New: Shortcode "tppost" for displaying parts of a post only for registered students
* New: Images for courses
* Changed: Shortcode "tpcloud": It's now possible to deactivate the html anchor
* Changed: Redesigned user interface
* Changed: Number of chars for a semester name (from 10 to 100)
* Changed: Database and directory structure
* Bugfix: Fixed bugs in the overview of students
* Bugfix: Fixed problems with the user data field selection for registration forms
* Bugfix: It's now possible to add images directly from the WordPress Media Library
* Bugfix: Fixed a bug with the email column in the course lists.
* Bugfix: Fixed a bug in xls export: The parent course name is now displaying
* Killed: own database functions tp_var, tp_query, tp_results
= 1.0.0 - (31.05.2010) =
* New: It is possible to deactivate some fields for user data
* New: New registration mode available
* New: Function for uninstalling teachPress
* Changed: Some small improvement for attendance lists
* Changed: Settings
* Changed: Design for enrollment system
* Changed: Calendar: from jscalendar to datepicker (jquery-plugin)
* Changed: Directory structure
* Bugfix: Fixed bugs with utf8 chars
= 0.85.1 =
* New: RSS-Feed script for publications
* Bugfix: Fixed bug in the "copy course" function
* Bugfix: Fixed bug in the "add students manually" function
= 0.85.0 =
* New: Displaying images in publication lists is possible
* New: Larger edit field for course comments
* Bugfix: Size of visible images in the publication edit menu is limited
* Bufgix: Some function names now more unique
* Bugfix: Fix some security vulnerabilities
= 0.80.2 =
* Bugfix: Fixed different bugs, which originated with the file merging in the publication management in 0.80.0
= 0.80.1 =
* Bugfix: Fixed bug when adding a publication
= 0.80.0 =
* New: Capabilities for backend access control
* New: Possible to prevent sign outs for registrations 
* Changed: Style of frontend course overview
* Changed: Central definition of publication types in the source code 
* Changed: Select fields reworked
* Changed: Translation for publication types
* Changed: Put all javascript functions to standard.js
* Changed: Put the teachPress settings page from the courses menu to the Wordpress settings menu
* Bugfix: Fixed displaying child courses in display.php 
* Bugfix: Cleaned backend CSS and more CSS3 compatibility
* Bugfix: Fixed access bug for students.php
* Bugfix: Fixed updater
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
* Fix a little problem with the version name
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