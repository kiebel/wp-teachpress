=== teachPress ===
Contributors: Michael Winkler
Tags: management, publications, enrollments, teachpress, education, course management, BibTeX, bibliography
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 2.3.0

With this plugin you can easy manage courses, enrollments and publications.

== Description ==
The plugin unites a course management system (with enrollments) and a BibTeX compatible publication management. teachPress is optimized for the needs of professorships and research groups. You can use it with WordPress 2.9.0 or higher.

For more information see [here](http://www.mtrv.wordpress.com/teachpress/).

= Features: =
* BibTeX compatible publication management
* Course management with enrollment system
* Student management
* Import and export function for publications (BibTeX format)
* xls/csv-export for course lists
* RSS-feed for publicaitons
* Widget for displaying books in the sidebar
* Many shortcodes for an easy using of publication lists, enrollments and course overviews

= Supported Languages =
* English 
* German
* Italian
* Spanish

= Disclaimer =  
Use at your own risk. No warranty expressed or implied is provided.  

== Credits ==

Copyright 2008-2011 by Michael Winkler

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

= Thanks =
I would like to thank the team of [CBIS, Chemnitz University of Technology](http://www.tu-chemnitz.de/wirtschaft/wi2/wp/en/) for the support and the collaboration during the last years.

= Translators who did a great job in translating the plugin into other languages. Thank you! =
* Elisabetta Mancini (Italian)
* Aurelio Pons (Spanish)

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
= 2.0.7 =
If you have installed teachpress with version 2.0.0 or higher, check if is the table teachpress_pub in your database!

== Screenshots ==
1. Add publication menu
2. Add course menu
3. teachPress books widget
 

== Frequently Asked Questions ==

= How can I add a course list in a page or post? = 
When you writing a post or page use the following tag: [tpcourselist]

= How can I add the enrollment system in my blog? =
Create a new page or post and use the following tag: [tpenrollments]

= How can I add longer course desciptions? =
You can write long course desciptions, as normal WordPress pages. The main function of teachPress is only to connect the static page with a course overview and an enrollment system. For this you can specify a related page for your course.

= How can I display images in publication lists? =
An example: [tplist id="0" image="left" image_size="70"]. Important: You must specify both image parameters.

= How can I deactivate parts of the plugin? =
If you want to use only one part of the plugin, so write the following in the wp-config.php of your WordPress installation (or in the parameters.php of the plugin):  
For deactivating the course system:  
define ('TP_COURSE_SYSTEM','disable');  
For deactivating the publication system:  
define ('TP_PUBLICATION_SYSTEM','disable');  

= I see only error messages if I use the RSS-Feed for publications or the xls/csv-Export for enrollments. What's wrong? =
Before you can use this features you must define the $root parameter in the parameters.php: You find in the teachPress directory the file: parameters_sample.php. Open this file and change the $root paramter (you find some examples there). After that rename the file to parameters.php and upload the file in the teachPress directory.

= How I can use the shortcodes? =
[See teachPress shortcode reference](http://mtrv.wordpress.com/teachpress/shortcode-reference/)

== Changelog ==
= 2.3.0 - (06.06.2011) =
* New: teachPress is now compatible with WordPress 3.2
* New: An option for selecting all checkboxes is now available in some admin menus
* New: The deactivation of the course/publication system is possible
* Changed: Visibility handling for courses
* Bugfix: Fixed a bug in the shortcode [tpcourselist]: With activated permalink structure it was in some cases for users not possible to select an other semester
* Bugfix: Fixed a bug in the page menu: Wrong page number calculation under determined conditions
* Bugfix: Fixed a bug in the enrollments system: If the course and the sub-course name were the same, the course type was displayed instead of the sub-course type
* Bugfix: Fixed a bug in the enrollments system: If there is no related page given, the course name is no longer a link
* Bugfix: Fixed the bibtex import for several special chars
= 2.2.0 - (17.04.2011) =
* New: "order" option for the shortcodes [tplist], [tpcloud]
* New: "type" option for the shortcodes [tplist], [tpcloud]
* New: Improved filter system for the backend publication menu
* New: teachPress can now manage the number of free places of a course automatically.
* New: Improved visibility options for courses
* Changed: Pagemenus have now the WordPress 3.0 Design
* Changed: Backend function have their own file: core/admin.php
* Bugfix: Fixed a redirect bug in the backend publication menu
* Bugfix: Fixed a small CSS bug in the frontend courselist, shortcode: [tpcourselist]
* Bugfix: The year 0000 is no longer visible in the year filter of [tpcloud] - 0000 stands for no date given - publications with no date are furthermore visible at the end of the publication list
* Killed: Detailed parameter description for shortcodes in the help sections --> moved to docs/shortcodes.html
* Killed: Language files for en_US (because it's already the basic plugin langauge)
= 2.1.2 - (15.03.2011) =
* New: If you want it, you can now use custom post types instead of pages for the related page links
* Info: WordPress 2.8 is not longer supported
= 2.1.1 - (10.03.2011) =
* Bugfix: Fixed a bug in the publication overview (backend): The tags were not displayed
* Bugfix: Fixed a bug in the updater which set the field type for birthday in the table teachpress_stud to varchar and not to date
* Bugfix: Fixed a bug when student data were edited via backend: Data were lost
* Bugfix: Fixed a bug which prevent an direct edit after adding a course/publication
= 2.1.0 - (08.03.2011) =
* New: Strict Subscribing
* New: Improved Admin menu
* Bugfix: Fixed a bug with a wrong redirect after the user cancelled the deleting of students
= 2.0.14 - (24.02.2011) =
* New: New style option for [tpcloud], [tplist]
* Bugfix: Fixed a bug in teachPress books widget - the name of a book is no longer a html-element name
* Bugfix: The Room is now vissible again in single course overviews (Bug was introcuced with teachPress 2.0.10)
= 2.0.13 - (20.02.2011) =
* New: Spanish translation added
= 2.0.12 - (01.02.2011) =
* New: Improved tag menu
* New: Improved students menu
* New: New style options for editor names - available for the shortcodes [tpcloud], [tplist], [tpsingle]
* New: New style options for author names - available for the shortcodes [tpsingle]
* Bugfix: Waiting lists are now sorted by registration date and not longer by user name
* Bugfix: Fixed a bug in single course menu: If there is no enrollment, enrollment details are not longer visible
* Bugfix: Some shortcode parameters are now more secure
* Bugfix: Fixed a bug which insert wrong links to the publication feeds in the settings menu
* Bugfix: Fixed some bugs with slashes with was not stripped for the final displaying. It's fixed for the xls/csv-export, the RSS parser and in some menus
* Bugfix: Fixed some bugs in xls/csv export
= 2.0.11 - (31.01.2011) =
* New: New style options for author names - available for the shortcodes [tpcloud], [tplist]
= 2.0.10 - (23.01.2011) = 
* New: Highlighting of child courses in the course overview 
* Bugfix: Fixed a html bug in show_single_course.php 
* Bugfix: Fixed the sort of participants if the registration number is disabled 
* Bugfix: Fixed a bug with the table of participants if the registration number is disabled
= 2.0.9 - (30.11.2010) =
* New: Better style permissions for all shortcodes. Some nasty hard coded CSS code is removed.
* Changed: Style for tpenrollments, tpdate and tpcloud shortcodes
* Changed: Italian translation updated
= 2.0.8 - (27.11.2010) =
* New: Support for WordPress 3.1
* New: Basic italian translation added
= 2.0.7 - (18.11.2010) =
* Bugfix: Fixed a bug with the charset in the xls-export - teachpress uses now utf8_encode()
* Bugfix: Fixed a bug in the bibtex output of articles
* Bugfix: Fixed some small GUI-bugs
* Bugfix: Fixed style of some buttons
= 2.0.6 - (24.10.2010) =
* Changed: Type of the registration timestamp has changed from date to datetime
* Bugfix: Fixed a security vulnerability (sql injection) which was opened with a fix in version 2.0.5
* Bugfix: teachPress uses now the right local server time and not longer greenwich time
= 2.0.5 - (11.10.2010) =
* New: Now you can set the time (hour, minute) for the start/end of a enrollment period
* Bugfix: Fixed a bug which displayed a wrong message after adding a student
* Bugfix: Fixed a bug which prevented the manual adding of students
* Bugfix: Some small user interface improvements
* Bugfix: Fixed some bugs in the english translation
= 2.0.4 - (02.10.2010) =
* Bugfix: Fixed a bug which prevented the installation of the teachpress_pub table
= 2.0.3 - (27.09.2010) = 
* Bugfix: Fixed a bug with one login mode (integrated)
= 2.0.2 - (27.09.2010) =
* Changed: New author and plugin website
* Bugfix: The year was not displaying for articles
* Bugfix: Images in the publication lists were not scaled
* Bugfix: Child courses are now visible, if their parent was deleted
* Bugfix: Fixed bugs in the admin course overview
* Bugfix: Course type was not selected when an user edit a course
* Bugfix: Fixed a variable declaration in get_tp_publication_type_options()
= 2.0.1 - (20.09.2010) =
* New: Introduce an option for deselecting the default teachPress frontend style
* Changed: Some small improvements for publication lists
* Bugfix: Fixed german translation for proceedings and inproceedings
* Bugfix: BibTeX-Key was not displaying in the frontend
= 2.0.0 - (18.09.2010) =
* Changed: Some small improvements for publication lists
* Bugfix: Fixed some bugs with the pagination in the students and the publication overview
* Bugfix: Delete the bugfix in tpdate shortcode from version 2.0.b3, because the bug was the bugfix
= 2.0.b3 - (16.09.2010) =
* Changed: Style of single publications generated with [tpsingle]
* Changed: Bibtex export now discerns isbn from issn
* Bugfix: Fixed a bug in the copy function for courses
* Bugfix: Fixed a bug in tpdate shortcode - missing number of columns
* Bugfix: Fixed a bug when adding students manually
= 2.0.b2 - (14.09.2010) =
* Bugfix: Fixed a bug in the registration system
* Bugfix: Fixed style of publication lists
* Bugfix: Fixed a bug which prevent to delete terms, course types and courses of studies
* Bugfix: Fixed a bug in the xls export
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