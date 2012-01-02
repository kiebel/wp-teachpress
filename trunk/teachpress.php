<?php
/*
Plugin Name: teachPress
Plugin URI: http://mtrv.wordpress.com/teachpress/
Description: With teachPress you can easy manage courses, enrollments and publications.
Version: 2.3.3
Author: Michael Winkler
Author URI: http://mtrv.wordpress.com/
Min WP Version: 3.0
Max WP Version: 3.2
*/

/*
   LICENCE
 
    Copyright 2008-2011 Michael Winkler

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
function teachpress_add_menu() {
	add_menu_page(__('Course','teachpress'), __('Course','teachpress'),'use_teachpress', __FILE__, 'teachpress_show_courses_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
	add_submenu_page('teachpress/teachpress.php',__('Add new','teachpress'), __('Add new','teachpress'),'use_teachpress','teachpress/add_course.php','tp_add_course_page');
	add_submenu_page('teachpress/teachpress.php',__('Students','teachpress'), __('Students','teachpress'),'use_teachpress', 'teachpress/students.php', 'teachpress_students_page');
	add_submenu_page('teachpress/teachpress.php', __('Add manually','teachpress'), __('Add manually','teachpress'),'use_teachpress','teachpress/students_new.php', 'teachpress_students_new_page');
}
// Publications
function teachpress_add_menu2() {
	add_menu_page (__('Publications','teachpress'), __('Publications','teachpress'), 'use_teachpress', 'publications.php', 'teachpress_publications_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
	add_submenu_page('publications.php',__('Your publications','teachpress'), __('Your publications','teachpress'),'use_teachpress','teachpress/publications.php','teachpress_publications_page');
	add_submenu_page('publications.php',__('Add new','teachpress'), __('Add new','teachpress'),'use_teachpress','teachpress/addpublications.php','teachpress_addpublications_page');
	add_submenu_page('publications.php',__('Import','teachpress'), __('Import','teachpress'), 'use_teachpress', 'teachpress/import.php','teachpress_import_page');
	add_submenu_page('publications.php',__('Tags','teachpress'),__('Tags','teachpress'),'use_teachpress','teachpress/tags.php','teachpress_tags_page');
}
// Settings
function teachpress_add_menu_settings() {
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
include_once("admin/show_students.php");
include_once("admin/add_students.php");
include_once("admin/edit_student.php");
include_once("admin/settings.php");
include_once("admin/show_publications.php");
include_once("admin/add_publication.php");
include_once("admin/edit_tags.php");
include_once("admin/import_publications.php");
// Core functions
include_once("core/bibtex.php");
include_once("core/shortcodes.php");
include_once("core/admin.php");
// BibTeX Parse by Mark Grimshaw
if ( !class_exists( 'PARSEENTRIES' ) ) {
	include_once("includes/bibtexParse/PARSEENTRIES.php");
	include_once("includes/bibtexParse/PARSECREATORS.php");
}
// Include the export file
function teachpress_export () {
	include_once("export.php");
}

/*****************/
/* Mainfunctions */
/*****************/

/* Print message
 * @param $message (String) - Content
 * @param $site (String) - Page
*/ 
function tp_get_message($message, $site = '') {
	echo '<p class="teachpress_message">';
	echo '<strong>' . $message . '</strong>';
	if ($site != '') {
		echo '<a href="' . $site . '" class="teachpress_back">' . __('resume', 'teachpress') . '</a>';
	}
	echo '</p>';
}

/* Split the timestamp
 * @param $datum - timestamp
 * return $split
 *
 * $split[0][0] => Year
 * $split[0][1] => Month 
 * $split[0][2] => Day
 * $split[0][3] => Hour 
 * $split[0][4] => Minute 
 * $split[0][5] => Second
*/ 
function tp_datumsplit($datum) {
    $preg = '/[\d]{2,4}/'; 
    $split = array(); 
    preg_match_all($preg, $datum, $split); 
	return $split; 
}

/* Gives an array with all publication types
 * Return $pub_types
*/ 
function tp_get_publication_types() {
	// Define publication types
	$pub_types[0][0] = '0';
	$pub_types[0][1] = __('All types','teachpress');
	$pub_types[1][0] = 'article';
	$pub_types[1][1] = __('article','teachpress');;
	$pub_types[2][0] = 'book';
	$pub_types[2][1] = __('book','teachpress');
	$pub_types[3][0] = 'booklet';
	$pub_types[3][1] = __('booklet','teachpress');
	$pub_types[4][0] = 'conference';
	$pub_types[4][1] = __('conference','teachpress');
	$pub_types[5][0] = 'inbook';
	$pub_types[5][1] = __('inbook','teachpress');
	$pub_types[6][0] = 'incollection';
	$pub_types[6][1] = __('incollection','teachpress');
	$pub_types[7][0] = 'inproceedings';
	$pub_types[7][1] = __('inproceedings','teachpress');
	$pub_types[8][0] = 'manual';
	$pub_types[8][1] = __('manual','teachpress');
	$pub_types[9][0] = 'mastersthesis';
	$pub_types[9][1] = __('mastersthesis','teachpress');
	$pub_types[10][0] = 'misc';
	$pub_types[10][1] = __('misc','teachpress');
	$pub_types[11][0] = 'phdthesis';
	$pub_types[11][1] = __('phdthesis','teachpress');
	$pub_types[12][0] = 'presentation';
	$pub_types[12][1] = __('presentation','teachpress');
	$pub_types[13][0] = 'proceedings';
	$pub_types[13][1] = __('proceedings','teachpress');
	$pub_types[14][0] = 'techreport';
	$pub_types[14][1] = __('techreport','teachpress');
	$pub_types[15][0] = 'unpublished';
	$pub_types[15][1] = __('unpublished','teachpress');
	return $pub_types;
}

/* Get publication types
 * @param $selected
 * @param $mode - list (list menu) or jump (jump menu)
 * @param $url - jump mode only
 * @param $tgid - jump mode only
 * @param $yr - jump mode only
 * @param $autor - jump mode only
 *
 * $pub_types[i][0] --> Database string
 * $pub_types[i][1] --> Translation string
 *
 * Return $types (String)
*/
function get_tp_publication_type_options ($selected, $mode = 'list', $url = '', $tgid = '', $yr = '', $autor = '', $anchor = 1) {
	$selected = tp_sec_var($selected);
	$url = tp_sec_var($url);
	$tgid = tp_sec_var($tgid);
	$yr = tp_sec_var($yr);
	$autor = tp_sec_var($autor);
	$anchor = tp_sec_var($anchor, 'integer');
	if ($anchor = 1) {
		$html_anchor = '#tppubs';
	}
	else {
		$html_anchor = '';
	}
	$pub_types = tp_get_publication_types();
	if ($mode == 'jump') {
		for ($i = 0; $i <= 15; $i++) {
			if ($pub_types[$i][0] == $selected && $selected != '') {
				$current = 'selected="selected"';
			}
			else {
				$current = '';
			}
			$types = $types . '<option value="' . $url . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $pub_types[$i][0] . '&amp;autor=' . $autor . $html_anchor . '" ' . $current . '>' . __('' . $pub_types[$i][1] . '','teachpress') . '</option>';
		}
	}
	else {
		for ($i = 1; $i <= 15; $i++) {
			if ($pub_types[$i][0] == $selected && $selected != '') {
				$current = 'selected="selected"';
			}
			else {
				$current = '';
			}
			$types = $types . '<option value="' . $pub_types[$i][0] . '" ' . $current . '>' . __('' . $pub_types[$i][1] . '','teachpress') . '</option>';
		}
	}
	return $types;
}

/* Get the current teachPress version
 * Return: $version (String)
*/
function get_tp_version(){
	global $tp_version;
	return $tp_version;
}

/* define who can use teachPress
 * @param $roles (ARRAY)
 * used in: settings.php 
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

/* Get a teachPress option
 * @param $var (String) --> permalink, sem, db-version, sign_out, login
 * Return $term
*/
function tp_get_option($var) {
	global $wpdb;
	global $teachpress_settings;
	$var = tp_sec_var($var);
	$term = "SELECT value FROM " . $teachpress_settings . " WHERE variable = '$var'";
	$term = $wpdb->get_var($term);
	return $term;
}

/* Secure variables
 * @param $var
 * @param $type - integer, string (default)
*/
function tp_sec_var($var, $type = 'string') {
	$var = htmlspecialchars($var);
	if ($type == 'integer') {
		settype($var, 'integer');
	}
	return $var;
}

/* Function for the integrated registration mode
 *
*/
function tp_advanced_registration() {
	$user = wp_get_current_user();
	global $wpdb;
	global $teachpress_stud;
	$sql = "SELECT wp_id FROM " . $teachpress_stud . "WHERE wp_id = '$current_user->ID'";
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
class teachpress_books_widget extends WP_Widget {
    /** constructor */
    function teachpress_books_widget() {
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
        $title = esc_attr($instance['title']);
		$url = esc_attr($instance['url']);
		$books = $instance['books'];
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title', 'teachpress') . ': <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
		
		echo '<p><label for="' . $this->get_field_id('books') . '">' . __('Books', 'teachpress') . ': <select class="widefat" id="' . $this->get_field_id('books') . '" name="' . $this->get_field_name('books') . '[]" style="height:auto; max-height:25em" multiple="multiple" size="10">';
		$sql= "SELECT pub_id, name FROM " . $teachpress_pub . " WHERE type = 'Book' ORDER BY date DESC";
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
		
		$post_type = tp_get_option('rel_page_publications');
		teachpress_wp_pages("menu_order","ASC",$url,$post_type,0,0);
		//$items = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY menu_order ASC" );
			echo '</select></label></p>';
		}
}

/*************************/
/* Installer and Updater */
/*************************/

/* Database update manager */
function tp_db_update() {
	include_once('core/update.php');
	tp_db_update_function();
}

/* 
 * Installation
*/
function teachpress_install() {
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
						 `course_id` INT ,
						 `wp_id` INT ,
						 `waitinglist` INT(1) ,
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
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('db-version', '2.3.3', 'system')");
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
						 `pub_id` INT ,
						 `tag_id` INT ,
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
						 `pub_id` INT ,
						 `user` INT ,
						 PRIMARY KEY (bookmark_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
}

/* 
 * Uninstalling
*/ 
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

/* Admin interface script loader
 * used in: Wordpress-Admin-Header
*/ 
function teachpress_admin_head() {
	// load scripts only, when it's teachpress page
	if ( eregi('teachpress', $_GET[page]) || eregi('publications', $_GET[page]) ) {
		$lang = __('de','teachpress');
		wp_enqueue_script('teachpress-standard', WP_PLUGIN_URL . '/teachpress/js/backend.js');
		wp_enqueue_style('teachpress.css', WP_PLUGIN_URL . '/teachpress/styles/teachpress.css');
		wp_enqueue_script('media-upload');
		add_thickbox();
		wp_enqueue_script('teachpress-jquery', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery-1.4.2.js');
		wp_enqueue_script('teachpress-datepicker', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.datepick.min.js');
		wp_enqueue_script('teachpress-datepicker2', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.datepick.ext.js');
		wp_enqueue_style('teachpress-datepicker.css', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.datepick.css');
		if ($lang == 'de') {
			wp_enqueue_script('teachpress-datepicker-de', WP_PLUGIN_URL . '/teachpress/js/datepicker/jquery.datepick-de.js');
		}	
	}
}
/* Adds a javascript to the WordPress Backend Admin Header
 * used in: Wordpress-Admin-Header
*/
function teachpress_js_admin_head() {
    echo '<link media="print" type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/styles/print.css" rel="stylesheet" />';
}

/* Adds files to the WordPress Frontend Admin Header
 * used in: Wordpress-Header
*/ 
function teachpress_js_wp_header() {
	echo chr(13) . chr(10) . '<!-- teachPress ' . get_tp_version() . ' -->' . chr(13) . chr(10);
	echo '<script type="text/javascript" src="' . WP_PLUGIN_URL . '/teachpress/js/frontend.js"></script>' . chr(13) . chr(10);
	$value = tp_get_option('stylesheet');
	if ($value == '1') {
		echo '<link type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/styles/teachpress_front.css" rel="stylesheet" />' . chr(13) . chr(10);
	}
	echo '<!-- END teachPress -->' . chr(13) . chr(10);
}

// load language files
function teachpress_language_support() {
	load_plugin_textdomain('teachpress', false, 'teachpress/languages');
}

// Register WordPress-Hooks
register_activation_hook( __FILE__, 'teachpress_install');
add_action('init', 'teachpress_language_support');
add_action('admin_menu', 'teachpress_add_menu_settings');
add_action('admin_head', 'teachpress_js_admin_head');
add_action('wp_head', 'teachpress_js_wp_header');
add_action('admin_init','teachpress_admin_head');

if ( !defined('TP_COURSE_SYSTEM') ) {
	add_action('admin_menu', 'teachpress_add_menu');
	add_action('widgets_init', create_function('', 'return register_widget("teachpress_books_widget");'));
	add_shortcode('tpdate', 'tpdate_shortcode');
	add_shortcode('tpcourselist', 'tp_courselist_shortcode');
	add_shortcode('tpenrollments', 'tpenrollments_shortcode');
	add_shortcode('tppost','tppost_shortcode');
}

if ( !defined('TP_PUBLICATION_SYSTEM') ) {
	add_action('admin_menu', 'teachpress_add_menu2');
	add_shortcode('tpcloud', 'tpcloud_shortcode');
	add_shortcode('tplist', 'tplist_shortcode');
	add_shortcode('tpsingle','tpsingle_shortcode');
}

?>