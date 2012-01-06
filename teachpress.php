<?php
/*
Plugin Name: teachPress
Plugin URI: http://mtrv.wordpress.com/teachpress/
Description: With teachPress you can easy manage courses, enrollments and publications.
Version: 3.0.1
Author: Michael Winkler
Author URI: http://mtrv.wordpress.com/
Min WP Version: 3.3
Max WP Version: 3.3.1
*/

/*
   LICENCE
 
    Copyright 2008-2012 Michael Winkler

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Define Databases
// Define teachpress database tables, change it, if you will install teachpress in other tables. Every name must be unique.
global $wpdb;
$teachpress_courses = $wpdb->prefix . 'teachpress_courses'; //Events
$teachpress_stud = $wpdb->prefix . 'teachpress_stud'; //Students
$teachpress_settings = $wpdb->prefix . 'teachpress_settings'; //Settings
$teachpress_signup = $wpdb->prefix . 'teachpress_signup'; //Enrollments
$teachpress_log = $wpdb->prefix . 'teachpress_log'; // Security-Log
$teachpress_pub = $wpdb->prefix . 'teachpress_pub'; //Publications
$teachpress_tags = $wpdb->prefix . 'teachpress_tags'; //Tags
$teachpress_relation = $wpdb->prefix . 'teachpress_relation'; //Relationship Tags - Publications
$teachpress_user = $wpdb->prefix . 'teachpress_user'; // Relationship Publications - User
require_once('version.php');

/*************/
/* Add menus */
/*************/
// Courses and students
function tp_add_menu() {
   global $tp_admin_page;
   global $tp_admin_page2;
   $tp_admin_page2 = add_menu_page(__('Course','teachpress'), __('Course','teachpress'),'use_teachpress', __FILE__, 'teachpress_show_courses_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
   $tp_admin_page = add_submenu_page('teachpress/teachpress.php',__('Add New','teachpress'), __('Add New', 'teachpress'),'use_teachpress','teachpress/add_course.php','tp_add_course_page');
   add_submenu_page('teachpress/teachpress.php',__('Students','teachpress'), __('Students','teachpress'),'use_teachpress', 'teachpress/students.php', 'teachpress_students_page');
   add_submenu_page('teachpress/teachpress.php', __('Add manually','teachpress'), __('Add manually','teachpress'),'use_teachpress','teachpress/students_new.php', 'teachpress_students_new_page');
   add_action("load-$tp_admin_page", 'tp_add_course_page_help');
   add_action("load-$tp_admin_page2", 'tp_show_course_page_help');
}
// Publications
function tp_add_menu2() {
   global $tp_admin_page3;
   global $tp_admin_page4;
   $tp_admin_page3 = add_menu_page (__('Publications','teachpress'), __('Publications','teachpress'), 'use_teachpress', 'publications.php', 'teachpress_publications_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
   $tp_admin_page4 = add_submenu_page('publications.php',__('Your publications','teachpress'), __('Your publications','teachpress'),'use_teachpress','teachpress/publications.php','teachpress_publications_page');
   add_submenu_page('publications.php',__('Add New', 'teachpress'), __('Add New','teachpress'),'use_teachpress','teachpress/addpublications.php','teachpress_addpublications_page');
   add_submenu_page('publications.php',__('Import/Export'), __('Import/Export'), 'use_teachpress', 'teachpress/import.php','teachpress_import_page');
   add_submenu_page('publications.php',__('Tags'),__('Tags'),'use_teachpress','teachpress/tags.php','teachpress_tags_page');
   add_action("load-$tp_admin_page3", 'tp_show_publications_page_help');
   add_action("load-$tp_admin_page4", 'tp_show_publications_page_help');
}
// Settings
function tp_add_menu_settings() {
   add_options_page(__('teachPress Settings','teachpress'),'teachPress','administrator','teachpress/settings.php', 'teachpress_admin_settings');
}

/************/
/* Includes */
/************/
// Admin menus
include_once("admin/show_courses.php");
include_once("admin/add_course.php");
include_once("admin/show_single_course.php");
include_once("admin/create_lists.php");
include_once("admin/mail.php");
include_once("admin/show_students.php");
include_once("admin/add_students.php");
include_once("admin/edit_student.php");
include_once("admin/settings.php");
include_once("admin/show_publications.php");
include_once("admin/add_publication.php");
include_once("admin/edit_tags.php");
include_once("admin/import_publications.php");
// Core functions
include_once("core/shortcodes.php");
include_once("core/admin.php");
include_once("core/class-bibtex.php");
include_once("core/class-mail.php");
include_once("core/class-export.php");
// BibTeX Parse
if ( !class_exists( 'PARSEENTRIES' ) ) {
   include_once("includes/bibtexParse/PARSEENTRIES.php");
   include_once("includes/bibtexParse/PARSECREATORS.php");
}

/*****************/
/* Mainfunctions */
/*****************/
/** 
 * Print message
 * @param STRING $message - Content
 * @param STRING $site - Page
*/ 
function get_tp_message($message, $site = '') {
   echo '<div class="teachpress_message">';
   echo '<strong>' . $message . '</strong>';
   if ($site != '') {
     echo ' <a href="' . $site . '" class="button-secondary">' . __('Resume', 'teachpress') . '</a>';
   }
   echo '</div>';
}

/** 
 * Split a timestamp
 * @param TIMESTAMP $datum
 * @return ARRAY
 *
 * $split[0][0] => Year
 * $split[0][1] => Month 
 * $split[0][2] => Day
 * $split[0][3] => Hour 
 * $split[0][4] => Minute 
 * $split[0][5] => Second
*/ 
function tp_datesplit($datum) {
    $preg = '/[\d]{2,4}/'; 
    $split = array(); 
    preg_match_all($preg, $datum, $split); 
    return $split; 
}

/** 
 * Gives an array with all publication types
 * @return ARRAY
*/ 
function get_tp_publication_types() {
   $pub_types[0] = array (0 => '0', 1 => __('All types','teachpress'), 2 => __('All types','teachpress'));
   $pub_types[1] = array (0 => 'article', 1 => __('Article','teachpress'), 2 => __('Articles','teachpress'));
   $pub_types[2] = array (0 => 'book', 1 => __('Book','teachpress'), 2 => __('Books','teachpress'));
   $pub_types[3] = array (0 => 'booklet', 1 => __('Booklet','teachpress'), 2 => __('Booklets','teachpress'));
   $pub_types[4] = array (0 => 'conference', 1 => __('Conference','teachpress'), 2 => __('Conferences','teachpress'));
   $pub_types[5] = array (0 => 'inbook', 1 => __('Inbook','teachpress'), 2 => __('Inbooks','teachpress'));
   $pub_types[6] = array (0 => 'incollection', 1 => __('Incollection','teachpress'), 2 => __('Incollections','teachpress'));
   $pub_types[7] = array (0 => 'inproceedings', 1 => __('Inproceeding','teachpress'), 2 => __('Inproceedings','teachpress'));
   $pub_types[8] = array (0 => 'manual', 1 => __('Manual','teachpress'), 2 => __('Manuals','teachpress'));
   $pub_types[9] = array (0 => 'masterthesis', 1 => __('Mastersthesis','teachpress'), 2 => __('Masterstheses','teachpress'));
   $pub_types[10] = array (0 => 'misc', 1 => __('Misc','teachpress'), 2 => __('Misc','teachpress'));
   $pub_types[11] = array (0 => 'phdthesis', 1 => __('PhD Thesis','teachpress'), 2 => __('PhD Theses','teachpress'));
   $pub_types[12] = array (0 => 'presentation', 1 => __('Presentation','teachpress'), 2 => __('Presentations','teachpress'));
   $pub_types[13] = array (0 => 'proceedings', 1 => __('Proceeding','teachpress'), 2 => __('Proceedings','teachpress'));
   $pub_types[14] = array (0 => 'techreport', 1 => __('Techreport','teachpress'), 2 => __('Techreports','teachpress'));
   $pub_types[15] = array (0 => 'unpublished', 1 => __('Unpublished','teachpress'), 2 => __('Unpublished','teachpress'));
   return $pub_types;
}

/**
 * Translate a publication type
 * @param STRING $string
 * @param STRING $num - sin (singular) or pl (plural)
 * @return STRING
 */
function tp_translate_pub_type($string, $num = 'sin') {
   $t = get_tp_publication_types();
   $num = $num == 'sin' ? 1 : 2;
   for ( $i=1; $i <= 15; $i++ ) {
        if ( $string == $t[$i][0] ) {
             $tr = $t[$i][$num];
        }
   }
   return $tr;
}

/** 
 * Get publication types
 * @param STRING $selected
 * @param STRING $mode - list (list menu) or jump (jump menu)
 * @param STRING $url - jump mode only
 * @param INT $tgid - jump mode only
 * @param INT $yr - jump mode only
 * @param STRING $autor - jump mode only
 *
 * $pub_types[i][0] --> Database string
 * $pub_types[i][1] --> Translation string
 *
 * Return STRING
*/
function get_tp_publication_type_options ($selected, $mode = 'list', $url = '', $tgid = '', $yr = '', $autor = '', $anchor = 1) {
     $selected = tp_sec_var($selected);
     $url = tp_sec_var($url);
     $tgid = tp_sec_var($tgid);
     $yr = tp_sec_var($yr);
     $autor = tp_sec_var($autor);
     $anchor = tp_sec_var($anchor, 'integer');
     $types = '';
     if ($anchor == 1) {
          $html_anchor = '#tppubs';
     }
     else {
          $html_anchor = '';
     }
     $pub_types = get_tp_publication_types();
     for ($i = 1; $i <= 15; $i++) {
         if ($pub_types[$i][0] == $selected && $selected != '') {
              $current = 'selected="selected"';
         }
         else {
              $current = '';
         }
         if ($mode == 'jump') {
              $types = $types . '<option value="' . $url . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $pub_types[$i][0] . '&amp;autor=' . $autor . $html_anchor . '" ' . $current . '>' . __('' . $pub_types[$i][1] . '','teachpress') . '</option>';
         }
         else {
              $types = $types . '<option value="' . $pub_types[$i][0] . '" ' . $current . '>' . __('' . $pub_types[$i][1] . '','teachpress') . '</option>';  
         }
     }
   return $types;
}

/**
 * Get the array structure for a parameter
 * @param STRING $type values: course_array, publication_array
 * @return ARRAY 
 */
function get_tp_var_types($type) {
     if ($type == 'course_array') {
          $ret = array( 'course_id' => '','name' => '','type' => '','room' => '','lecturer' => '','date' => '','places' => '','fplaces' => '','start' => '','end' => '','semester' => '','comment' => '','rel_page' => '','parent' => '','visible' => '','waitinglist' => '','image_url' => '','strict_signup' => '' );
     }
     if ($type == 'publication_array') {
          $ret = array( 'pub_id' => '', 'name' => '', 'type' => '', 'bibtex' => '', 'author' => '', 'editor' => '', 'isbn' => '', 'url' => '', 'date' => '', 'booktitle' => '', 'journal' => '', 'volume' => '', 'number' => '', 'pages' => '', 'publisher' => '', 'address' => '', 'edition' => '', 'chapter' => '', 'institution' => '', 'organization' => '', 'school' => '', 'series' => '', 'crossref' => '', 'abstract' => '', 'howpublished' => '', 'key' => '', 'techtype' => '', 'comment' => '', 'note' => '', 'image_url' => '', 'is_isbn' => '', 'rel_page' => '');
     }
     return $ret;
}

/** 
 * Get the current teachPress version
 * @return: STRING
*/
function get_tp_version(){
   global $tp_version;
   return $tp_version;
}

/** 
 * Define who can use teachPress
 * @param ARRAY $roles
 */
function tp_update_userrole($roles) {
	global $wp_roles;

    if ( empty($roles) || ! is_array($roles) ) { 
		$roles = array(); 
	}
    $who_can = $roles;
    $who_cannot = array_diff( array_keys($wp_roles->role_names), $roles);
    foreach ($who_can as $role) {
        $wp_roles->add_cap($role, 'use_teachpress');
    }
    foreach ($who_cannot as $role) {
        $wp_roles->remove_cap($role, 'use_teachpress');
    }
}

/** 
 * Get a teachPress option
 * @param STRING $var --> permalink, sem, db-version, sign_out or login
 * @Return STRING
*/
function get_tp_option($var) {
   global $wpdb;
   global $teachpress_settings;
   $var = tp_sec_var($var);
   $term = "SELECT `value` FROM " . $teachpress_settings . " WHERE `variable` = '$var'";
   $term = $wpdb->get_var($term);
   return $term;
}

/** Secure variables
 * @param STRING $var
 * @param STRING $type --> integer, string (default)
*/
function tp_sec_var($var, $type = 'string') {
   $var = htmlspecialchars($var);
   if ($type == 'integer') {
           settype($var, 'integer');
   }
   return $var;
}

/** Function for the integrated registration mode */
function tp_advanced_registration() {
     $user = wp_get_current_user();
     global $wpdb;
     global $teachpress_stud;
     $sql = "SELECT `wp_id` FROM " . $teachpress_stud . "WHERE `wp_id` = '$current_user->ID'";
     $test = $wpdb->query($sql);
     if ($test == '0' && $user->ID != '0') {
          if ($user->user_firstname == '') {
              $user->user_firstname = $user->display_name;
          }
          $data['firstname'] = $user->user_firstname;
          $data['lastname'] = $user->user_lastname;
          $data['userlogin'] = $user->user_login;
          $data['email'] = $user->user_email;
          tp_add_student($user->ID, $data );
     }
} 

/*********************************/
/* teachPress Books widget class */
/*********************************/
class tp_books_widget extends WP_Widget {
    /** constructor */
    function tp_books_widget() {
          $widget_ops = array('classname' => 'widget_teachpress_books', 'description' => __('Shows a random book in the sidebar', 'teachpress') );
          $control_ops = array('width' => 400, 'height' => 300);
          parent::WP_Widget(false, $name = __('teachPress books','teachpress'), $widget_ops, $control_ops);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
          global $wpdb;
          global $teachpress_pub;	
          extract( $args );
          $title = apply_filters('widget_title', $instance['title']);
          $all_url = get_permalink($instance['url']);
          $books = $instance['books'];
          $zahl = count($books);
          $zufall = rand(0, $zahl-1);
          $zufall = $books[$zufall];
          $row = $wpdb->get_results("SELECT name, image_url, rel_page FROM " . $teachpress_pub . " WHERE pub_id = '$zufall'" );
          foreach ($row as $row) {
              $pub_title = $row->name;
              $rel_page = get_permalink($row->rel_page);
              $image_url = $row->image_url;
          }
          echo $before_widget;
          if ( $title ) {
               echo $before_title . $title . $after_title;
          }
          echo '<p style="text-align:center"><a href="' . $rel_page . '" title="' . $pub_title . '"><img class="tp_image" src="' . $image_url . '" alt="' . $pub_title . '" title="' . $pub_title . '" /></a></p>';
          echo '<p style="text-align:center"><a href="' . $all_url . '" title="' . __('All books','teachpress') . '">' . __('All books','teachpress') . '</a></p>';
          echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
          return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
          global $wpdb;	
          global $teachpress_pub;			
          $title = isset ($instance['title']) ? esc_attr($instance['title']) : '';
          $url = isset ($instance['url']) ? esc_attr($instance['url']) : '';
          $books = isset ($instance['books']) ? $instance['books'] : '';
          echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'teachpress') . ': <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';

          echo '<p><label for="' . $this->get_field_id('books') . '">' . __('Books', 'teachpress') . ': <select class="widefat" id="' . $this->get_field_id('books') . '" name="' . $this->get_field_name('books') . '[]" style="height:auto; max-height:25em" multiple="multiple" size="10">';
          $sql= "SELECT `pub_id`, `name` FROM " . $teachpress_pub . " WHERE `type` = 'Book' ORDER BY `date` DESC";
          $row= $wpdb->get_results($sql);
          foreach ($row as $row) {
              if ( in_array($row->pub_id, $books) ) {
              echo '<option value="' . $row->pub_id . '" selected="selected">' . $row->pub_id . ': ' . $row->name . '</option>';
              }
              else {
                      echo '<option value="' . $row->pub_id . '">' . $row->pub_id . ': ' . $row->name . '</option>';
              }
          }
          echo '</select></label><small class="setting-description">' . __('use &lt;Ctrl&gt; key to select multiple books', 'teachpress') . '</small></p>';

          echo '<p><label for="' . $this->get_field_id('url') . '">' . __('Releated Page for &laquo;all books&raquo; link:', 'teachpress') . ' <select class="widefat" id="' . $this->get_field_id('url') . '" name="' . $this->get_field_name('url') . '>';
          echo '<option value="">' . __('none','teachpress') . '</option>';

          $post_type = get_tp_option('rel_page_publications');
          get_tp_wp_pages("menu_order","ASC",$url,$post_type,0,0);
              echo '</select></label></p>';
          }
}

/*************************/
/* Installer and Updater */
/*************************/

/** Database update manager */
function tp_db_update() {
   include_once('core/update.php');
   tp_db_update_function();
}

/** Installation */
function tp_install() {
   global $wpdb;
   $teachpress_courses = $wpdb->prefix . 'teachpress_courses'; // Courses
   $teachpress_stud = $wpdb->prefix . 'teachpress_stud'; // Students
   $teachpress_settings = $wpdb->prefix . 'teachpress_settings'; // Settings
   $teachpress_log = $wpdb->prefix . 'teachpress_log'; // Security-Log
   $teachpress_pub = $wpdb->prefix . 'teachpress_pub'; // Publications
   $teachpress_tags = $wpdb->prefix . 'teachpress_tags'; // Tags
   $teachpress_signup = $wpdb->prefix . 'teachpress_signup'; // Relationship Courses - Students
   $teachpress_relation = $wpdb->prefix . 'teachpress_relation'; // Relationsship Tags - Publications
   $teachpress_user = $wpdb->prefix . 'teachpress_user'; // Relationship Publications - User

   // Add capabilities
   global $wp_roles;
   $role = $wp_roles->get_role('administrator');
   if ( !$role->has_cap('use_teachpress') ) {
           $wp_roles->add_cap('administrator', 'use_teachpress');
   }

   // charset & collate like WordPress
   $charset_collate = '';
   if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
           if ( ! empty($wpdb->charset) ) {
                   $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
           }	
           if ( ! empty($wpdb->collate) ) {
                   $charset_collate .= " COLLATE $wpdb->collate";
           }	
   }

   // teachpress_courses
   $table_name = $teachpress_courses;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_courses. " (
                                            `course_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `name` VARCHAR(100) ,
                                            `type` VARCHAR (100) ,
                                            `room` VARCHAR(100) ,
                                            `lecturer` VARCHAR (100) ,
                                            `date` VARCHAR(60) ,
                                            `places` INT(4) ,
                                            `fplaces` INT(4) ,
                                            `start` DATETIME ,
                                            `end` DATETIME ,
                                            `semester` VARCHAR(100) ,
                                            `comment` VARCHAR(500) ,
                                            `rel_page` INT ,
                                            `parent` INT ,
                                            `visible` INT(1) ,
                                            `waitinglist` INT(1),
                                            `image_url` VARCHAR(400) ,
                                            `strict_signup` INT(1) ,
                                            PRIMARY KEY (course_id)
                                      ) $charset_collate;";
           require_once(ABSPATH . 'wp-admin/upgrade-functions.php');			
           dbDelta($sql);

    }
    // teachpress_stud
   $table_name = $teachpress_stud;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_stud . " (
                                            `wp_id` INT UNSIGNED ,
                                            `firstname` VARCHAR(100) ,
                                            `lastname` VARCHAR(100) ,
                                            `course_of_studies` VARCHAR(100) ,
                                            `userlogin` VARCHAR (100) ,
                                            `birthday` DATE ,
                                            `email` VARCHAR(50) ,
                                            `semesternumber` INT(2) ,
                                            `matriculation_number` INT,
                                            PRIMARY KEY (wp_id)
                                      ) $charset_collate;";
           dbDelta($sql);
    }
    // teachpress_signup
   $table_name = $teachpress_signup;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_signup . " (
                                            `con_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `course_id` INT UNSIGNED,
                                            `wp_id` INT UNSIGNED ,
                                            `waitinglist` INT(1) UNSIGNED ,
                                            `date` DATETIME ,
                                            FOREIGN KEY (course_id) REFERENCES " . $teachpress_courses. "(course_id) ,
                                            FOREIGN KEY (wp_id) REFERENCES " . $teachpress_stud . "(wp_id) ,
                                            PRIMARY KEY (con_id)
                                      ) $charset_collate;";
           dbDelta($sql);
    }
    // teachpress_settings
   $table_name = $teachpress_settings;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_settings . " (
                                           `setting_id` INT UNSIGNED AUTO_INCREMENT ,
                                           `variable` VARCHAR (100) ,
                                           `value` VARCHAR (100) ,
                                           `category` VARCHAR (100) ,
                                           PRIMARY KEY (setting_id)
                                           ) $charset_collate;";
           dbDelta($sql);
           // Default settings		
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('sem', 'Example term', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('db-version', '3.0.0', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('permalink', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('sign_out', '0', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('login', 'std', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('stylesheet', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('regnum', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('studies', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('termnumber', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('birthday', '1', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('rel_page_courses', 'page', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('rel_page_publications', 'page', 'system')");
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('Example term', 'Example term', 'semester')");									
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('Example', 'Example', 'course_of_studies')");	
           $wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('Lecture', 'Lecture', 'course_type')");
    }
    // teachpress_log
   $table_name = $teachpress_log;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_log . " (
                                           `log_id` INT UNSIGNED AUTO_INCREMENT ,
                                           `id` INT ,
                                           `user` INT ,
                                           `description` VARCHAR (200) ,
                                           `date` DATE ,
                                           PRIMARY KEY (log_id)
                                           ) $charset_collate;";
           dbDelta($sql);
    }
    //teachpress_pub
   $table_name = $teachpress_pub;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_pub. " (
                                            `pub_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `name` VARCHAR(500) ,
                                            `type` VARCHAR (50) ,
                                            `bibtex` VARCHAR (50) ,
                                            `author` VARCHAR (500) ,
                                            `editor` VARCHAR (500) ,
                                            `isbn` VARCHAR (50) ,
                                            `url` VARCHAR (400) ,
                                            `date` DATE ,
                                            `booktitle` VARCHAR (200) ,
                                            `journal` VARCHAR(200) ,
                                            `volume` VARCHAR(40) ,
                                            `number` VARCHAR(40) ,
                                            `pages` VARCHAR(40) ,
                                            `publisher` VARCHAR (500) ,
                                            `address` VARCHAR (300) ,
                                            `edition` VARCHAR (100) ,
                                            `chapter` VARCHAR (40) ,
                                            `institution` VARCHAR (200) ,
                                            `organization` VARCHAR (200) ,
                                            `school` VARCHAR (200) ,
                                            `series` VARCHAR (200) ,
                                            `crossref` VARCHAR (100) ,
                                            `abstract` TEXT ,
                                            `howpublished` VARCHAR (200) ,
                                            `key` VARCHAR (100) ,
                                            `techtype` VARCHAR (200) ,
                                            `comment` TEXT ,
                                            `note` TEXT ,
                                            `image_url` VARCHAR(400) ,
                                            `rel_page` INT ,
                                            `is_isbn` INT(1) ,
                                            PRIMARY KEY (pub_id)
                                      ) $charset_collate;";			   
           dbDelta($sql);
    }
    //teachpress_tags
   $table_name = $teachpress_tags;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_tags . " (
                                            `tag_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `name` VARCHAR(300) ,
                                            PRIMARY KEY (tag_id)
                                       ) $charset_collate;";
           dbDelta($sql);
    }
    //teachpress_relation
   $table_name = $teachpress_relation;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_relation . " (
                                            `con_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `pub_id` INT UNSIGNED,
                                            `tag_id` INT UNSIGNED,
                                            FOREIGN KEY (pub_id) REFERENCES " . $teachpress_pub. "(pub_id) ,
                                            FOREIGN KEY (tag_id) REFERENCES " . $teachpress_tags . "(tag_id) ,
                                            PRIMARY KEY (con_id)
                                      ) $charset_collate;";
           dbDelta($sql);
    }
    //teachpress_user
   $table_name = $teachpress_user;
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
           $sql = "CREATE TABLE " . $teachpress_user . " (
                                            `bookmark_id` INT UNSIGNED AUTO_INCREMENT ,
                                            `pub_id` INT UNSIGNED,
                                            `user` INT UNSIGNED,
                                            PRIMARY KEY (bookmark_id)
                                      ) $charset_collate;";
           dbDelta($sql);
    }
}

/** Uninstalling */ 
function tp_uninstall() {
   global $wpdb;
   global $teachpress_courses;
   global $teachpress_stud;
   global $teachpress_settings;
   global $teachpress_signup;
   global $teachpress_log;
   global $teachpress_pub;
   global $teachpress_tags;
   global $teachpress_relation;
   global $teachpress_user;
   $wpdb->query("DROP TABLE " . $teachpress_courses . ", " . $teachpress_stud . ", " . $teachpress_settings . ", " . $teachpress_signup . ", " . $teachpress_log . ", " . $teachpress_pub . ", " . $teachpress_tags . ", " . $teachpress_user . ", " . $teachpress_relation . "");
}

/*********************/
/* Loading functions */
/*********************/

/** Admin interface script loader */ 
function tp_backend_scripts() {
   // Define $page
   if ( isset($_GET['page']) ) {
           $page = $_GET['page'];
   }
   else {
           $page = '';
   }
   wp_enqueue_style('teachpress-print-css', WP_PLUGIN_URL . '/teachpress/styles/print.css', false, false, 'print');
   // load scripts only, when it's teachpress page
   if ( eregi('teachpress', $page) || eregi('publications', $page) ) {
     wp_enqueue_script('teachpress-standard', WP_PLUGIN_URL . '/teachpress/js/backend.js');
     wp_enqueue_style('teachpress.css', WP_PLUGIN_URL . '/teachpress/styles/teachpress.css');
     wp_enqueue_script('media-upload');
     add_thickbox();
     // Datepicker
     wp_enqueue_script(array('jquery-ui-core', 'jquery-ui-datepicker'));
     wp_enqueue_style('teachpress-datepicker.css', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.ui.datepicker.css');
     $lang = array('de_DE','it_IT','es_ES');
     if ( in_array( WPLANG , $lang) ) {
          wp_enqueue_script('teachpress-datepicker-de', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.ui.datepicker-' . WPLANG . '.js');
     }
   }
}

/** Adds files to the WordPress Frontend Admin Header */ 
function tp_frontend_scripts() {
     echo chr(13) . chr(10) . '<!-- teachPress ' . get_tp_version() . ' -->' . chr(13) . chr(10);
     echo '<script type="text/javascript" src="' . WP_PLUGIN_URL . '/teachpress/js/frontend.js"></script>' . chr(13) . chr(10);
     $value = get_tp_option('stylesheet');
     if ($value == '1') {
      echo '<link type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/styles/teachpress_front.css" rel="stylesheet" />' . chr(13) . chr(10);
     }
     echo '<!-- END teachPress -->' . chr(13) . chr(10);
}

// load language files
function tp_language_support() {
     load_plugin_textdomain('teachpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

// Add link to wp plugin overview
function tp_plugin_link($links, $file){
     if ($file == plugin_basename(__FILE__)) {
          return array_merge($links, array( sprintf('<a href="options-general.php?page=teachpress/settings.php">%s</a>', __('Settings') ) ));
     }
     return $links;
}

// Register WordPress-Hooks
register_activation_hook( __FILE__, 'tp_install');
add_action('init', 'tp_language_support');
add_action('admin_menu', 'tp_add_menu_settings');
add_action('wp_head', 'tp_frontend_scripts');
add_action('admin_init','tp_backend_scripts');
add_filter('plugin_action_links','tp_plugin_link', 10, 2);

if ( !defined('TP_COURSE_SYSTEM') ) {
     add_action('admin_menu', 'tp_add_menu');
     add_action('widgets_init', create_function('', 'return register_widget("tp_books_widget");'));
     add_shortcode('tpdate', 'tp_date_shortcode');
     add_shortcode('tpcourselist', 'tp_courselist_shortcode');
     add_shortcode('tpenrollments', 'tp_enrollments_shortcode');
     add_shortcode('tppost','tp_post_shortcode');
}

if ( !defined('TP_PUBLICATION_SYSTEM') ) {
     add_action('admin_menu', 'tp_add_menu2');
     add_shortcode('tpcloud', 'tp_cloud_shortcode');
     add_shortcode('tplist', 'tp_list_shortcode');
     add_shortcode('tpsingle','tp_single_shortcode');
}

?>