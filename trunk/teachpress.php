<?php
/*
Plugin Name: teachPress
Plugin URI: http://mtrv.wordpress.com/teachpress/
Description: With teachPress you can easy manage courses, enrollments and publications.
Version: 2.1.0
Author: Michael Winkler
Author URI: http://mtrv.wordpress.com/
Min WP Version: 2.8
Max WP Version: 3.1
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

// Admin-Menu
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

// Includes
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

include_once("core/bibtex.php");
include_once("core/shortcodes.php");

include_once("includes/bibtexParse/PARSEENTRIES.php");
include_once("includes/bibtexParse/PARSECREATORS.php");

// Include teh export file
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

/* Get WordPress pages
 * adapted from Flexi Pages Widget Plugin
 * @param $sort_column
 * @param sort_order
 * @param $selected
 * @param $parent
 * @param $level
*/ 
function teachpress_wp_pages($sort_column = "menu_order", $sort_order = "ASC", $selected, $parent = 0, $level = 0 ) {
	global $wpdb;
	if ($level == 0) {
		if ($selected == '0') {
			$current = ' selected="selected"';
		}	
		else {
			$current = '';
		}
		echo "\n\t<option value='0'$current>$pad " . __('none','teachpress') . "</option>";
	}
	$items = $wpdb->get_results( "SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = $parent AND post_type = 'page' AND post_status = 'publish' ORDER BY {$sort_column} {$sort_order}" );
	if ( $items ) {
		foreach ( $items as $item ) {
			$pad = str_repeat( '&nbsp;', $level * 3 );
			if ( $item->ID == $selected) {
				$current = ' selected="selected"';
			}	
			else {
				$current = '';
			}	
			echo "\n\t<option value='$item->ID'$current>$pad " . get_the_title($item->ID) . "</option>";
			teachpress_wp_pages( $sort_column, $sort_order, $selected, $item->ID,  $level +1 );
		}
	} else {
		return false;
	}
}

/* Gives a single table row for show_courses.php
 * @param $couse (ARRAY_A) --> course data
 * @param $checkbox (ARRAY)
 * @param $static (ARRAY_A):
 		@param $static['bulk'] --> copy or delete
		@param $static['sem'] --> semester
		@param $static['search'] --> input from search field
 * @param $parent_course_name
 * @param $type (STRING) --> parent or child
*/ 
function tp_get_single_table_row_course ($course, $checkbox, $static, $parent_course_name = '', $type = 'parent') {
	$check = '';
	$style = '';
	// Check if checkbox must be activated or not
	if ( $static['bulk'] == "copy" || $static['bulk'] == "delete") { 
		for( $k = 0; $k < count( $checkbox ); $k++ ) { 
			if ( $course['course_id'] == $checkbox[$k] ) { $check = 'checked="checked"';} 
		} 
	}
	// Change the style for an important information
	if ( $course['places'] > 0 && $course['fplaces'] == 0 ) {
		$style = ' style="color:#ff6600; font-weight:bold;"'; 
	}
	// Type specifics
	if ( $type == 'parent' || $type == 'search' ) {
		$class = ' class="tp_course_parent"';
	}
	else {
		$class = ' class="tp_course_child"';
	}
	
	if ( $type == 'child' || $type == 'search' ) {
		if ( $course['name'] != $parent_course_name ) {
			$course['name'] = $parent_course_name . ' ' . $course['name'];
		}
	}
	// complete the row
	$a1 = '<tr>
				<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $course['course_id'] . '"' . $check . '/></th>
				<td' . $class . '>
					<a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course['course_id'] . '&amp;sem=' . $static['sem'] . '&amp;search=' . $static['search'] . '&amp;action=show" class="teachpress_link" title="' . __('Click to show','teachpress') . '"><strong>' . $course['name'] . '</strong></a>
					<div class="tp_row_actions">
						<a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course['course_id'] . '&amp;sem=' . $static['sem'] . '&amp;search=' . $static['search'] . '&amp;action=show" title="' . __('Show this element','teachpress') . '">' . __('Show','teachpress') . '</a> | <a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course['course_id'] . '&amp;sem=' . $static['sem'] . '&amp;search=' . $static['search'] . '&amp;action=edit&amp;ref=overview" title="' . __('Edit this element','teachpress') . '">' . __('Edit','teachpress') . '</a> | <a href="admin.php?page=teachpress/teachpress.php&amp;sem=' . $static['sem'] . '&amp;search=' . $static['search'] . '&amp;checkbox%5B%5D=' . $course['course_id'] . '&amp;bulk=delete" style="color:red;" title="' . __('Delete this element','teachpress') . '">' . __('Delete','teachpress') . '</a>
					</div>
				</td>
				<td>' . $course['course_id'] . '</td>
				<td>' . $course['type'] . '</td>
				<td>' . $course['lecturer'] . '</td>
				<td>' . $course['date'] . '</td>
				<td>' . $course['places'] . '</td>
				<td' . $style . '>' . $course['fplaces'] . '</td>';
	if ( $course['start'] != '0000-00-00' && $course['end'] != '0000-00-00' ) {
		$a2 ='<td>' . $course['start'] . '</td>
			  <td>' . $course['end'] . '</td>';
	} 
	else {
		$a2 = '<td colspan="2" style="text-align:center;">' . __('none','teachpress') . '</td>';
	}
	$a3 = '<td>' . $course['semester'] . '</td>';
	if ( $course['visible'] == 1 ) {
		$a4 = '<td>' . __('yes','teachpress') . '</td>';
	} 
	else {
		$a4 = '<td>' . __('no','teachpress') . '</td>';
	}
	$a5 = '</tr>';
	// Return
	$return = $a1 . $a2 . $a3 . $a4 . $a5;
	return $return;
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
    $wp_roles->WP_Roles();

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

/***********/
/* Courses */
/***********/

/* Add a new course
 * @param $data (ARRAY_A)
 * used in add_course.php
 * Return $insert_ID (INT) - id of the new course
*/
function tp_add_course($data) {
	global $wpdb;
	global $teachpress_courses;
	$data['start'] = $data['start'] . ' ' . $data['start_hour'] . ':' . $data['start_minute'] . ':00';
	$data['end'] = $data['end'] . ' ' . $data['end_hour'] . ':' . $data['end_minute'] . ':00';
	$wpdb->insert( $teachpress_courses, array( 'name' => $data['name'], 'type' => $data['type'], 'room' => $data['room'], 'lecturer' => $data['lecturer'], 'date' => $data['date'], 'places' => $data['places'], 'fplaces' => $data['places'], 'start' => $data['start'], 'end' => $data['end'], 'semester' => $data['semester'], 'comment' => $data['comment'], 'rel_page' => $data['rel_page'], 'parent' => $data['parent'], 'visible' => $data['visible'], 'waitinglist' => $data['waitinglist'], 'image_url' => $data['image_url'], 'strict_signup' => $data['strict_signup'] ), array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d' ) );
	return $wpdb->insert_id;
}
	
/* Delete courses
 * @param $checkbox (Array)
 * used in: showlvs.php
*/
function tp_delete_course($checkbox){
	global $wpdb;
	global $teachpress_courses;
	global $teachpress_signup;
    for( $i = 0; $i < count( $checkbox ); $i++ ) { 
		settype($checkbox[$i], 'integer'); 
   		$wpdb->query( "DELETE FROM " . $teachpress_courses . " WHERE course_id = $checkbox[$i]" );
		$wpdb->query( "DELETE FROM " . $teachpress_signup . " WHERE course_id = $checkbox[$i]" );
		// Check if there are parent courses, which are not selected for erasing, and set there parent to default
		$sql = "SELECT course_id FROM " . $teachpress_courses . " WHERE parent = $checkbox[$i]";
		$test = $wpdb->query($sql);
		if ($test != '0') {
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				if ( !in_array($row->course_id, $checkbox) ) {
					$wpdb->update( $teachpress_courses, array( 'parent' => 0 ), array( 'course_id' => $row->course_id ), array('%d' ), array( '%d' ) );
				}
			}
		}
    }
}
	
/* Change a course
 * @param $course_ID (INT) - course ID
 * @param $data (ARRAY_A)
*/ 
function tp_change_course($course_ID, $data){
	global $wpdb;
	global $teachpress_courses;
	$course_ID = tp_sec_var($course_ID, 'integer');
	$data['start'] = $data['start'] . ' ' . $data['start_hour'] . ':' . $data['start_minute'] . ':00';
	$data['end'] = $data['end'] . ' ' . $data['end_hour'] . ':' . $data['end_minute'] . ':00';
	$wpdb->update( $teachpress_courses, array( 'name' => $data['name'], 'type' => $data['type'], 'room' => $data['room'], 'lecturer' => $data['lecturer'], 'date' => $data['date'], 'places' => $data['places'], 'fplaces' => $data['fplaces'], 'start' => $data['start'], 'end' => $data['end'], 'semester' => $data['semester'], 'comment' => $data['comment'], 'rel_page' => $data['rel_page'], 'parent' => $data['parent'], 'visible' => $data['visible'], 'waitinglist' => $data['waitinglist'], 'image_url' => $data['image_url'], 'strict_signup' => $data['strict_signup'] ), array( 'course_id' => $course_ID ), array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d' ), array( '%d' ) );
}

/* Copy courses
 * @param $checkbox (Array) - Veranstaltungen die kopiert werden sollen
 * @param $copysem (String) - Semester in das kopiert werden soll
 * used in showlvs.php
*/
function tp_copy_course($checkbox, $copysem) {
	global $wpdb;
	global $teachpress_courses; 
	$counter = 0;
	$counter2 = 0;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
		$row = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = $checkbox[$i]";
		$row = $wpdb->get_results($row);
		foreach ($row as $row) {
				$daten[$counter]['id'] = $row->course_id;
				$daten[$counter]['name'] = $row->name;
				$daten[$counter]['type'] = $row->type;
				$daten[$counter]['room'] = $row->room;
				$daten[$counter]['lecturer'] = $row->lecturer;
				$daten[$counter]['date'] = $row->date;
				$daten[$counter]['places'] = $row->places;
				$daten[$counter]['start'] = $row->start;
				$daten[$counter]['end'] = $row->end;
				$daten[$counter]['semester'] = $row->semester;
				$daten[$counter]['comment'] = $row->comment;
				$daten[$counter]['rel_page'] = $row->rel_page;
				$daten[$counter]['parent'] = $row->parent;
				$daten[$counter]['visible'] = $row->visible;
				$daten[$counter]['waitinglist'] = $row->waitinglist;
				$daten[$counter]['image_url'] = $row->image_url;
				$counter++;
		}
		// copy parents
		if ( $daten[$i]['parent'] == 0) {
			$merke[$counter2] = $daten[$i]['id'];
			$daten[$i]['semester'] = $copysem;
			tp_add_course($daten[$i]);
			$counter2++;
		}
	}	
	// copy childs
	for( $i = 0; $i < $counter ; $i++ ) {
		if ( $daten[$i]['parent'] != 0) {
			// check if where is a parent for the current course
			$test = 0;
			for( $j = 0; $j < $counter2 ; $j++ ) {
				if ( $daten[$i]['parent'] == $merke[$j]) {
					$test = $merke[$j];
				}
			}
			// if is true
			if ($test != 0) {
				// search the parent
				for( $k = 0; $k < $counter ; $k++ ) {
					if ( $daten[$k]['id'] == $test) {
						$suche = "SELECT course_id FROM " . $teachpress_courses . " WHERE name = '" . $daten[$k]['name'] . "' AND type = '" . $daten[$k]['type'] . "' AND room = '" . $daten[$k]['room'] . "' AND lecturer = '" . $daten[$k]['lecturer'] . "' AND date = '" . $daten[$k]['date'] . "' AND semester = '$copysem' AND parent = 0";
						$suche = $wpdb->get_var($suche);
						$daten[$i]['parent'] = $suche;
						$daten[$i]['semester'] = $copysem;
						tp_add_course($daten[$i]);					
					}
				}
			}
			// if is false: create copy directly
			else {
				$daten[$i]['semester'] = $copysem;
				tp_add_course($daten[$i]);
			}
		}
	}
}

/*****************/
/* Registrations */
/*****************/

/* Get parent course data
 * @param $id (INT) - Parent-ID (Course_ID)
 * @param $col (STRING) - Column name
 * @mode $mode (STRING) - single (default), all
 * Return $value, or $value[] (if mode = all)
*/  
function tp_get_parent_data ($id, $col, $mode = 'single') {
	global $wpdb;
	global $teachpress_courses;
	$id = tp_sec_var($id, 'integer');
	if ( $mode == 'single' ) {
		$value = "SELECT `" . $col . "` FROM `" . $teachpress_courses . "` WHERE `course_id` = '$id'";
		$value = $wpdb->get_var($value);
		return $value;
	}
}

/* Add registration (= subscribe student in a course)
 * @param $checkbox (INT) - Course_ID
 * @param $wp_id (INT) - User_ID
 * Return (Message)
*/
function tp_add_registration($checkbox, $wp_id){
	global $wpdb;
	global $teachpress_courses;
	global $teachpress_stud;
	global $teachpress_signup;
	settype($checkbox, 'integer');
	// if there is no course selected
	if ( $checkbox == 0 ) {
		return '';
	}
	// load data
	$row1 = "SELECT fplaces, name, start, end, waitinglist, parent FROM " . $teachpress_courses . " WHERE course_id = '$checkbox'";
	$row1 = $wpdb->get_row($row1);
	// handle parent and child name
	if ($row1->parent != '0') {
		$parent = tp_get_parent_data ($row1->parent, 'name');
		if ($row1->name != $parent) {
			$row1->name = $parent . ' ' . $row1->name; 
		}
	}
	//Check if there are free places available
	if ($row1->fplaces > 0 ) {
		// Check if the user is already registered
		$check = "SELECT `con_id` FROM " . $teachpress_signup . " WHERE course_id = '$checkbox' and wp_id = '$wp_id'";
		$check = $wpdb->query($check);
		if ( $check == 0 ) {
			// Check if there is a parent course with strict signup
			$check = tp_get_parent_data ($row1->parent, 'strict_signup');
			if ( $check != 0 ) {
				$check = "SELECT c.course_id FROM " . $teachpress_courses . " c INNER JOIN " . $teachpress_signup . " s ON s.course_id = c.course_id WHERE c.parent = '$row1->parent' AND s.wp_id = '$wp_id' AND s.waitinglist = '0'";
				$check = $wpdb->query($check);
				// if the user is signed in a child course of the parent
				if ( $check != 0 ) {
					return '<div class="teachpress_message_error">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('Registration is not possible, because you are already registered for an other course of this course group.','teachpress') . '</div>';
				}
			}
			$wpdb->query( "INSERT INTO " . $teachpress_signup . " (course_id, wp_id, waitinglist, date) VALUES ('$checkbox', '$wp_id', '0', NOW() )" );
			// reduce the number of free places in the course
			$neu = $row1->fplaces - 1;
			$wpdb->query( "UPDATE " . $teachpress_courses . " SET `fplaces` = '$neu' WHERE `course_id` = '$checkbox'" );
			return '<div class="teachpress_message_success">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('Registration was successful.','teachpress') . '</div>';
		}
		else {
			return '<div class="teachpress_message_error">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('You are already registered for this course.','teachpress') . '</div>';
		}		
	}
	else {
		// if there is a waiting lis available
		if ($row1->waitinglist == '1') {
			// Check if the user is already registered in the waitinglist
			$check = $wpdb->query("SELECT con_id FROM " . $teachpress_signup . " WHERE course_id = '$checkbox' AND wp_id = '$wp_id'");
			// if not: subscribe the user
			if ($check == 0 ) {
				$wpdb->query( "INSERT INTO " . $teachpress_signup . " (course_id, wp_id, waitinglist, date) VALUES ('$checkbox', '$wp_id', '1', NOW() )" );
				return'<div class="teachpress_message_info">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('For this course there are no more free places. You are automatically signed up in a waiting list.','teachpress') . '</div>';
			}
			// if the user is already registered
			else {
				return '<div class="teachpress_message_error">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('You are already registered for this course.','teachpress') . '</div>';
			}
		}
		// if there is no waiting list
		else {
			return '<div class="teachpress_message_error">&quot;' . stripslashes($row1->name) . '&quot;: ' . __('Registration is not possible, because there are no more free places available','teachpress') . '</div>';
		}
	}
}

/* Delete registration
 * @param $checkbox (Array) - ID der Veranstaltungen
 * @param $user_ID (INT) - User_ID
*/
function tp_delete_registration($checkbox, $user_ID) {
	global $wpdb;
	global $teachpress_courses;  
	global $teachpress_signup;
	global $teachpress_log;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
		// select the course_ID
		$row1 = "SELECT course_id FROM " . $teachpress_signup . " WHERE con_id = '$checkbox[$i]'";
		$row1 = $wpdb->get_results($row1);
		foreach ($row1 as $row1) {
			// check if there are users in teh waiting list
			$abfrage = "SELECT con_id FROM " . $teachpress_signup . " WHERE course_id = '$row1->course_id' AND waitinglist = '1' ORDER BY con_id";
			$test= $wpdb->query($abfrage);
			// if is true
			if ($test != 0) {
				$zahl = 0;
				$wpdb->get_results($abfrage);
				foreach ($row as $row) {
					if ($zahl < 1) {
						$aendern = "UPDATE " . $teachpress_signup . " SET waitinglist = '0' WHERE con_id = '$row->con_id'";
						$wpdb->query( $aendern );
						$zahl++;
					}
				}
			}
			// if not enhance the number of free places
			else {
				$fplaces= "SELECT fplaces FROM " . $teachpress_courses . " WHERE course_id = '$row1->course_id'";
				$fplaces = $wpdb->get_var($fplaces);
				$neu = $fplaces + 1;
				$aendern = "UPDATE " . $teachpress_courses . " SET fplaces = '$neu' WHERE course_id = '$row1->course_id'";
				$wpdb->query( $aendern );
			}	
		}
   	$wpdb->query( "DELETE FROM " . $teachpress_signup . " WHERE con_id = '$checkbox[$i]'" );
	// Security log
	// since version 0.8
	$mess = __('Delete registration','teachpress');
	$wpdb->query( "INSERT INTO " . $teachpress_log . " (id, user, description, date) VALUES ('$checkbox[$i]', '$user_ID', '$mess', NOW())");
    }
}

/* unsubscribe a student (frontend)
 * @param $checkbox2 (Array) - ID der Einschreibungen
 * Return (Message)
 * used in: shortcodes.php
*/
function tp_delete_registration_student($checkbox2) {
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_signup;
	for( $i = 0; $i < count( $checkbox2 ); $i++ ) {
		settype($checkbox2[$i], 'integer');
		// Select course ID
		$row1 = "SELECT course_id FROM " . $teachpress_signup . " WHERE con_id = '$checkbox2[$i]'";
		$row1 = $wpdb->get_results($row1);
			foreach ($row1 as $row1) {
				// check if there are users in teh waiting list
				$abfrage = "SELECT con_id FROM " . $teachpress_signup . " WHERE course_id = '$row1->course_id' AND waitinglist = '1' ORDER BY con_id";
				$test = $wpdb->query($abfrage);
				// if is true
				if ($test!= 0) {
					$zahl = 0;
					$row = $wpdb->get_results($abfrage);
					foreach ($row as $row) {
						if ($zahl < 1) {
							$aendern = "UPDATE " . $teachpress_signup . " SET waitinglist = '0' WHERE con_id = '$row->con_id'";
							$wpdb->query( $aendern );
							$zahl++;
						}
					}
				}
				// if not enhance the number of free places
				else {
					$fplaces = "SELECT fplaces FROM " . $teachpress_courses . " WHERE course_id = '$row1->course_id'";
					$fplaces = $wpdb->get_var($fplaces);
					$neu = $fplaces + 1;
					$aendern = "UPDATE " . $teachpress_courses . " SET fplaces = '$neu' WHERE course_id = '$row1->course_id'";
					$wpdb->query( $aendern );
				}
			}
   		$wpdb->query( "DELETE FROM " . $teachpress_signup . " WHERE con_id = '$checkbox2[$i]'" );
    }	
	return '<div class="teachpress_message">' . __('You are signed out successful','teachpress') . '</div>';
}

/* Subscribe a student from a wating list manually
 * @param $checkbox (Array) - ID der Einschreibung
*/
function tp_add_from_waitinglist($checkbox) {
	global $wpdb;
	global $teachpress_signup;
	for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
		$wpdb->update( $teachpress_signup, array ( 'waitinglist' => 0), array ( 'con_id' => $checkbox[$i]), array ( '%d'), array ( '%d' ) );
	}
}

/* Subscribe a student manually
 * @param $student (INT)
 * @param $veranstaltung (INT)
*/	
function tp_subscribe_student_manually($student, $veranstaltung) {
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_signup;
	$eintragen = "INSERT INTO " . $teachpress_signup . " (course_id, wp_id, waitinglist, date) VALUES ('$veranstaltung', '$student', '0', NOW() )";
	$wpdb->query( $eintragen );
	// if there are free places -->reduce this number
	$fplaces = "SELECT fplaces FROM " . $teachpress_courses . " WHERE course_id = '$veranstaltung'";
	$fplaces = $wpdb->get_var($fplaces);
	if ($fplaces > 0 ) {
		$neu = $fplaces - 1;
		$aendern = "UPDATE " . $teachpress_courses . " SET fplaces = '$neu' WHERE course_id = '$veranstaltung'";
		$wpdb->query( $aendern );
	}
}	

/************/
/* Students */
/************/

/* Add student
 * @param $data (ARRAY_A)
 * return: user_ID or false if the user alread exist
*/
function tp_add_student($wp_id, $data) {
	global $wpdb;
	global $teachpress_stud;
	$wp_id = tp_sec_var($wp_id, 'integer');
	$sql = "SELECT wp_id FROM " . $teachpress_stud . " WHERE wp_id = '$wp_id'";
	$test = $wpdb->query($sql);
	if ($test == '0') {
		$data['birthday'] = $data['birth_year'] . '-' . $data['birth_month'] . '-' . $data['birth_day'];
		$wpdb->insert( $teachpress_stud, array( 'wp_id' => $wp_id, 'firstname' => $data['firstname'], 'lastname' => $data['lastname'], 'course_of_studies' => $data['course_of_studies'], 'userlogin' => $data['userlogin'], 'birthday' => $data['birthday'], 'email' => $data['email'], 'semesternumber' => $data['semester_number'], 'matriculation_number' => $data['matriculation_number'] ), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d' ) );
		//return $wpdb->insert_id;
		return true;
	}
	else {
		return false;
	}
}

/* Edit userdata
 * @param $wp_id (INT) - user ID
 * @param $data (ARRAY_A) - user data
 * @param $user_ID (INT) - current user ID
*/
function tp_change_student($wp_id, $data, $user_ID = 0) {
	global $wpdb;
	global $teachpress_stud;
	$wp_id = tp_sec_var($wp_id, 'integer');
	$user_ID = tp_sec_var($user_ID, 'integer');
	$wpdb->update( $teachpress_stud, array( 'firstname' => $data['firstname'], 'lastname' => $data['lastname'], 'course_of_studies' => $data['course_of_studies'], 'userlogin' => $data['userlogin'], 'birthday' => $data['birthday'], 'email' => $data['email'], 'semesternumber' => $data['semester_number'], 'matriculation_number' => $data['matriculation_number'] ), array( 'wp_id' => $wp_id ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d' ), array( '%d' ) );
	if ($user_ID == 0) {
		$return = '<div class="teachpress_message">' . __('Changes in your profile successful.','teachpress') . '</div>';
		return $return;
	}
	else {
		$mess = __('Student data changed','teachpress');
		$wpdb->query( "INSERT INTO " . $teachpress_log . " (id, user, description, date) VALUES ('$wp_id', '$user_ID', '$mess', NOW())");
	}
}

/* Delete student
 * @param $checkbox (Array) - ID of the enrollment
 * @param $user_ID (INT) - User_ID
*/ 
function tp_delete_student($checkbox, $user_ID){
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_stud; 
	global $teachpress_signup;
	global $teachpress_log;
	$user_ID = tp_sec_var($user_ID, 'integer');
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
		// search courses where the user was registered
		$row1 = "SELECT course_id FROM " . $teachpress_signup . " WHERE wp_id = '$checkbox[$i]'";
		$row1 = $wpdb->get_results($row1);
			foreach ($row1 as $row1) {
				// check if there are users in the waiting list
				$abfrage = "SELECT con_id FROM " . $teachpress_signup . " WHERE course_id = '$row1->course_id' AND waitinglist = '1' ORDER BY con_id";
				$test = $wpdb->query($abfrage);
				// if is true
				if ($rows > 0) {
					$zahl = 0;
					$row = $wpdb->get_results($abfrage);
					foreach($row as $row) {
						if ($zahl < 1) {
							$aendern = "UPDATE " . $teachpress_signup . " SET waitinglist = '0' WHERE con_id = '$row->con_id'";
							$wpdb->query( $aendern );
							$zahl++;
						}
					}
				}
				// if not enhance the number of free places
				else {
					$fplaces = "SELECT fplaces FROM " . $teachpress_courses . " WHERE course_id = '$row1->course_id'";
					$fplaces = $wpdb->get_var($fplaces);
					$neu = $fplaces + 1;
					$aendern = "UPDATE " . $teachpress_courses . " SET fplaces = '$neu' WHERE course_id = '$row1->course_id'";
					$wpdb->query( $aendern );
				}
			}
   		$wpdb->query( "DELETE FROM " . $teachpress_stud . " WHERE wp_id = $checkbox[$i]" );
		// security log
		// Since Version 0.8
		$mess = __('Delete student data','teachpress');
		$wpdb->query( "INSERT INTO " . $teachpress_log . " (id, user, description, date) VALUES ('$checkbox[$i]', '$user_ID', '$mess', NOW())");
		$wpdb->query( "DELETE FROM " . $teachpress_signup . " WHERE wp_id = $checkbox[$i]" );
    }
}

/****************/
/* Publications */
/****************/

/* Add a publication
 * @param $data (ARRAY_A)
 * @param $tags (ARRAY)
 * @param $bookmarks (ARRAY)
 * return: $insert_id (INT) -> id of the new publication
*/
function tp_add_publication($data, $tags, $bookmark) {
	global $wpdb;
	global $teachpress_pub;
	global $teachpress_tags; 
	global $teachpress_relation;
	global $teachpress_user;
	$wpdb->insert( $teachpress_pub, array( 'name' => $data['name'], 'type' => $data['type'], 'bibtex' => $data['bibtex'], 'author' => $data['author'], 'editor' => $data['editor'], 'isbn' => $data['isbn'], 'url' => $data['url'], 'date' => $data['date'], 'booktitle' => $data['booktitle'], 'journal' => $data['journal'], 'volume' => $data['volume'], 'number' => $data['number'], 'pages' => $data['pages'] , 'publisher' => $data['publisher'], 'address' => $data['address'], 'edition' => $data['edition'], 'chapter' => $data['chapter'], 'institution' => $data['institution'], 'organization' => $data['organization'], 'school' => $data['school'], 'series' => $data['series'], 'crossref' => $data['crossref'], 'abstract' => $data['abstract'], 'howpublished' => $data['howpublished'], 'key' => $data['key'], 'techtype' => $data['techtype'], 'comment' => $data['comment'], 'note' => $data['note'], 'image_url' => $data['image_url'], 'is_isbn' => $data['is_isbn'], 'rel_page' => $data['rel_page'] ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '$d' ) );
	$pub_ID = $wpdb->insert_id;
	// Bookmarks
	for( $i = 0; $i < count( $bookmark ); $i++ ) {
		settype($bookmark[$i], 'integer');
		if ($bookmark[$i] != '' || $bookmark[$i] != 0) {
			$wpdb->query( "INSERT INTO " . $teachpress_user . " (pub_id, user) VALUES ('$pub_ID', '$bookmark[$i]')" );
		}
	}
	$array = explode(",",$tags);
	foreach($array as $element) {
		$element = trim($element);
		if ($element != '') {
			$element = tp_sec_var($element);
			$row = "SELECT tag_id FROM " . $teachpress_tags . " WHERE name = '$element'";
			$check = $wpdb->query($row);
			// if tag not exist
			if ($check == 0){
				$eintrag = "INSERT INTO " . $teachpress_tags . " (name) VALUES ('$element')";
				$wpdb->query($eintrag);
				$row = $wpdb->get_results($row);
			}
			else {
				$row = $wpdb->get_results($row);
			}
			// add releation between publication and tag
			foreach($row as $row) {
				$test ="SELECT pub_id FROM " .$teachpress_relation . " WHERE pub_id = '$pub_ID' AND tag_id = '$row->tag_id'";
				$test = $wpdb->query($test);
				if ($test == 0) {
					$eintrag = "INSERT INTO " .$teachpress_relation . " (pub_id, tag_id) VALUES ('$pub_ID', '$row->tag_id')";
					$wpdb->query($eintrag);
				}
			}
		}	
	}
	return $pub_ID;
}

/* Delete publications
 * @param $checkbox (Array) - IDs der Publikationen
*/
function tp_delete_publications($checkbox){	
	global $wpdb;
	global $teachpress_pub; 
	global $teachpress_relation;
	global $teachpress_user;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
   		$wpdb->query( "DELETE FROM " . $teachpress_pub . " WHERE pub_id = $checkbox[$i]" );
		$wpdb->query( "DELETE FROM " . $teachpress_relation . " WHERE pub_id = $checkbox[$i]" );
		$wpdb->query( "DELETE FROM " . $teachpress_user . " WHERE pub_id = $checkbox[$i]" );
    }
}	

/* Edit a publication
 * @param $data (ARRAY_A)
 * @param $pub_ID (INT)
 * $param $tags [ARRAY]
*/
function tp_change_publication($pub_ID, $data, $bookmark, $delbox, $tags) {
	global $wpdb;
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_relation;
	global $teachpress_user;
	$pub_ID = tp_sec_var($pub_ID, 'integer');
	// update row
	$wpdb->update( $teachpress_pub, array( 'name' => $data['name'], 'type' => $data['type'], 'bibtex' => $data['bibtex'], 'author' => $data['author'], 'editor' => $data['editor'], 'isbn' => $data['isbn'], 'url' => $data['url'], 'date' => $data['date'], 'booktitle' => $data['booktitle'], 'journal' => $data['journal'], 'volume' => $data['volume'], 'number' => $data['number'], 'pages' => $data['pages'] , 'publisher' => $data['publisher'], 'address' => $data['address'], 'edition' => $data['edition'], 'chapter' => $data['chapter'], 'institution' => $data['institution'], 'organization' => $data['organization'], 'school' => $data['school'], 'series' => $data['series'], 'crossref' => $data['crossref'], 'abstract' => $data['abstract'], 'howpublished' => $data['howpublished'], 'key' => $data['key'], 'techtype' => $data['techtype'], 'comment' => $data['comment'], 'note' => $data['note'], 'image_url' => $data['image_url'], 'is_isbn' => $data['is_isbn'], 'rel_page' => $data['rel_page'] ), array( 'pub_id' => $pub_ID ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d' ), array( '%d' ) );
	// Bookmarks
	for( $i = 0; $i < count( $bookmark ); $i++ ) {
		settype($bookmark[$i], 'integer');
		if ($bookmark[$i] != '' || $bookmark[$i] != 0) {
			$wpdb->query( "INSERT INTO " . $teachpress_user . " (pub_id, user) VALUES ('$pub_ID', '$bookmark[$i]')" );
		}
	}
	// Delete tag relations
	for( $i = 0; $i < count( $delbox ); $i++ ) {
		$delbox[$i] = tp_sec_var($delbox[$i], 'integer');
   		$wpdb->query( "DELETE FROM " . $teachpress_relation . " WHERE con_id = $delbox[$i]" );
    }
	$array = explode(",",$tags);
	foreach($array as $element) {
		$element = trim($element);
		if ($element != '') {
			$element = tp_sec_var($element);
			$row = "SELECT tag_id FROM " . $teachpress_tags . " WHERE name = '$element'";
			$check = $wpdb->query($row);
			// if tag not exist
			if ($check == 0){
				$eintrag = "INSERT INTO " . $teachpress_tags . " (name) VALUES ('$element')";
				$wpdb->query($eintrag);
				$row = $wpdb->get_results($row);
			}
			else {
				$row = $wpdb->get_results($row);
			}
			// add releation between publication and tag
			foreach($row as $row) {
				$test ="SELECT pub_id FROM " .$teachpress_relation . " WHERE pub_id = '$pub_ID' AND tag_id = '$row->tag_id'";
				$test = $wpdb->query($test);
				if ($test == 0) {
					$eintrag = "INSERT INTO " .$teachpress_relation . " (pub_id, tag_id) VALUES ('$pub_ID', '$row->tag_id')";
					$wpdb->query($eintrag);
				}
			}
		}	
	}
}

/************/
/* Settings */
/************/

/* Change settings
 * @param $semester (String)
 * @param $permalink (INT)
 * used in: settings.php
*/
function tp_change_settings($semester, $permalink, $stylesheet, $sign_out, $userrole, $regnum, $studies, $termnumber, $birthday, $login) {
	global $wpdb;
	global $teachpress_settings; 		
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$semester' WHERE `variable` = 'sem'";
	$wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$permalink' WHERE `variable` = 'permalink'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$stylesheet' WHERE `variable` = 'stylesheet'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$sign_out' WHERE `variable` = 'sign_out'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$regnum' WHERE `variable` = 'regnum'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$studies' WHERE `variable` = 'studies'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$termnumber' WHERE `variable` = 'termnumber'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$birthday' WHERE `variable` = 'birthday'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '$login' WHERE `variable` = 'login'";
    $wpdb->query( $eintragen );
	tp_update_userrole($userrole);
}

/* Delete a setting
 * @param $delete (Array)
 * used in: settings.php
*/
function tp_delete_setting($delete) {
	global $wpdb;
	global $teachpress_settings;
	$delete = tp_sec_var($delete, 'integer');		
	$wpdb->query( "DELETE FROM " . $teachpress_settings . " WHERE setting_id = '$delete'" );
}

/* Add a setting
 * @param $name (String)
 * @param $typ (String)
 * used in: settings.php
*/
function tp_add_setting($name, $typ) { 
	global $wpdb;
	global $teachpress_settings;
	$wpdb->insert( $teachpress_settings, array( 'variable' => $name, 'value' => $name, 'category' => $typ ), array( '%s', '%s', '%s' ) );
}

/*************/
/* Bookmarks */
/*************/

/* Add a bookmark
 * @param $add_id (INT) - publication id
 * @param $user (INT) - User_ID
*/
function tp_add_bookmark($add_id, $user) {
	global $wpdb;
	global $teachpress_user;
	$add_id = tp_sec_var($add_id, 'integer');
	$user = tp_sec_var($user, 'integer');
	$wpdb->query( "INSERT INTO " . $teachpress_user . " (pub_id, user) VALUES ('$add_id', '$user')");
}

/* Delete a bookmark 
 * @param $del_id (INT) - IDs der Publikationen
 * @param $user (INT) - User_ID
 * used in publications.php
*/
function tp_delete_bookmark($del_id) {
	global $wpdb;
	global $teachpress_user;
	$del_id = tp_sec_var($del_id, 'integer');
	$wpdb->query( "DELETE FROM " . $teachpress_user . " WHERE bookmark_id = '$del_id'" );
}

/********/
/* Tags */
/********/

/* Delete tags
 * @param $checkbox (Array) - Tag IDs
*/
function tp_delete_tags($checkbox) {
	global $wpdb;
	global $teachpress_relation;
	global $teachpress_tags;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		settype($checkbox[$i], 'integer');
		$wpdb->query( "DELETE FROM " . $teachpress_relation . " WHERE tag_id = $checkbox[$i]" );
		$wpdb->query( "DELETE FROM " . $teachpress_tags . " WHERE tag_id = $checkbox[$i]" );
    }
}

/* Edit a tag
 * @param $tag_id (INT)
 * @param $name (String)
*/
function tp_edit_tag($tag_id, $name) {
	global $wpdb;
	global $teachpress_tags;
	$wpdb->update( $teachpress_tags, array( 'name' => $name ), array( 'tag_id' => $tag_id ), array( '%s' ), array( '%d' ) );
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
		echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'teachpress') . ' <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
		
		echo '<p><label for="' . $this->get_field_id('books') . '">' . __('Books:', 'teachpress') . ' <select class="widefat" id="' . $this->get_field_id('books') . '" name="' . $this->get_field_name('books') . '[]" style="height:auto; max-height:25em" multiple="multiple" size="10">';
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
		
		teachpress_wp_pages("menu_order","ASC",$url,0,0);
		$items = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY menu_order ASC" );
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
	$wp_roles->WP_Roles();
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
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('db-version', '2.1.0', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('permalink', '1', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('sign_out', '0', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('login', 'std', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('stylesheet', '1', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('regnum', '1', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('studies', '1', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('termnumber', '1', 'system')");
		$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('birthday', '1', 'system')");	
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

/* Script loader
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
	echo '<script type="text/javascript" src="' . WP_PLUGIN_URL . '/teachpress/js/frontend.js"></script>';
	$value = tp_get_option('stylesheet');
	if ($value == '1') {
	echo '<link type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/styles/teachpress_front.css" rel="stylesheet" />';
	}
}

// load language files
function teachpress_language_support() {
	load_plugin_textdomain('teachpress', false, 'teachpress/languages');
}

// Register WordPress-Hooks
register_activation_hook( __FILE__, 'teachpress_install');
add_action('widgets_init', create_function('', 'return register_widget("teachpress_books_widget");'));
add_action('init', 'teachpress_language_support');
add_action('admin_menu', 'teachpress_add_menu');
add_action('admin_menu', 'teachpress_add_menu2');
add_action('admin_menu', 'teachpress_add_menu_settings');
add_action('admin_head', 'teachpress_js_admin_head');
add_action('wp_head', 'teachpress_js_wp_header');
add_action('admin_init','teachpress_admin_head');
add_shortcode('tpdate', 'tpdate_shortcode');
add_shortcode('tpcourselist', 'tp_courselist_shortcode');
add_shortcode('tpenrollments', 'tpenrollments_shortcode');
add_shortcode('tpcloud', 'tpcloud_shortcode');
add_shortcode('tplist', 'tplist_shortcode');
add_shortcode('tpsingle','tpsingle_shortcode');
add_shortcode('tppost','tppost_shortcode');
?>