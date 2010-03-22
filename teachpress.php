<?php
/*
Plugin Name: teachPress
Plugin URI: http://www.mtrv.kilu.de/teachpress/
Description: With teachPress you can easy manage courses, enrollments and publications.
Version: 0.80.1
Author: Michael Winkler
Author URI: http://www.mtrv.kilu.de/
Min WP Version: 2.8
Max WP Version: 2.9.2
*/

/*
   LICENCE
 
    Copyright 2008-2010 Michael Winkler

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
$teachpress_ver = $wpdb->prefix . 'teachpress_ver'; //Events
$teachpress_stud = $wpdb->prefix . 'teachpress_stud'; //Students
$teachpress_einstellungen = $wpdb->prefix . 'teachpress_einstellungen'; //Settings
$teachpress_kursbelegung = $wpdb->prefix . 'teachpress_kursbelegung'; //Enrollments
$teachpress_log = $wpdb->prefix . 'teachpress_log'; // Security-Log
$teachpress_pub = $wpdb->prefix . 'teachpress_pub'; //Publications
$teachpress_tags = $wpdb->prefix . 'teachpress_tags'; //Tags
$teachpress_beziehung = $wpdb->prefix . 'teachpress_beziehung'; //Relationship Tags - Publications
$teachpress_user = $wpdb->prefix . 'teachpress_user'; // Relationship Publications - User
include_once('version.php');

// Admin-Menu
// Courses and students
function teachpress_add_menu() {
	add_menu_page(__('Course','teachpress'), __('Course','teachpress'),'use_teachpress', __FILE__, 'teachpress_showlvs_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
	add_submenu_page('teachpress/teachpress.php',__('New course','teachpress'), __('New course','teachpress'),'use_teachpress','teachpress/addlvs.php','teachpress_addlvs_page');
	add_submenu_page('teachpress/teachpress.php',__('Students','teachpress'), __('Students','teachpress'),'use_teachpress', 'teachpress/students.php', 'teachpress_students_page');
	add_submenu_page('teachpress/teachpress.php', __('Add manually','teachpress'), __('Add manually','teachpress'),'use_teachpress','teachpress/students_new.php', 'teachpress_students_new_page');
	// For page access since WordPress 2.8.1
	add_submenu_page('teachpress/addlvs.php', 'Edit LVS','Edit LVS','use_teachpress','teachpress/editlvs.php','teachpress_editlvs_page');
	add_submenu_page('teachpress/addlvs.php', 'Listen', 'Listen','use_teachpress','teachpress/lists.php','teachpress_lists_page');
	add_submenu_page('teachpress/addlvs.php', 'Edit Stud', 'Edit Stud','use_teachpress','teachpress/editstudent.php','teachpress_editstudent_page');
	add_submenu_page('teachpress/addlvs.php', 'Export', 'Export','use_teachpress','teachpress/export.php','teachpress_export');
	// End
	}
// Publications
function teachpress_add_menu2() {
	add_menu_page (__('Publications','teachpress'), __('Publications','teachpress'), 'use_teachpress', 'publications.php', 'teachpress_publications_page', WP_PLUGIN_URL . '/teachpress/images/logo_small.png');
	add_submenu_page('publications.php',__('Your publications','teachpress'), __('Your publications','teachpress'),'use_teachpress','teachpress/publications.php','teachpress_publications_page');
	add_submenu_page('publications.php',__('New','teachpress'), __('New','teachpress'),'use_teachpress','teachpress/addpublications.php','teachpress_addpublications_page');
	add_submenu_page('publications.php',__('Tags','teachpress'),__('Tags','teachpress'),'use_teachpress','teachpress/tags.php','teachpress_tags_page');
}
// Settings
function teachpress_add_menu_settings() {
	add_options_page(__('teachPress Settings','teachpress'),'teachpress','administrator','teachpress/settings.php', 'teachpress_admin_settings');
}

// Course overview backend
include_once("showlvs.php");
// Course overview frontend
include_once('display_courses.php');
// Enrollment system frontent
include_once('display.php');		
// New courses
include_once("addlvs.php");
// Edit courses
include_once("editlvs.php");
// Attendance lists
include_once("lists.php");
// Students
include_once("students.php");
// Add students manually
include_once("students_new.php");
// Edit students manually
include_once("editstudent.php");
// teachPress settings
include_once("settings.php");
// Publications
include_once("publications.php");
// new publications /edit publications
include_once("addpublications.php");
// Tags
include_once("tags.php");
// CSV/XLS-export
function teachpress_export () {
	include_once("export.php");
}

/***************/
/* SQL-Klassen */
/***************/ 

/* Abfragen von Datenbank-Queries
 * @param $tp_query (String)
 * Return $tp_return (Obeject)
*/
function tp_results($tp_query) {
	global $wpdb;
	$wpdb->escape($tp_query);
	$tp_return = $wpdb->get_results($tp_query);
	return $tp_return;
}

/* Ausfuehren allgemeiner Queries
 * @param $tp_query (String)
 * Return $tp_return (Int) - Anzahl der beinflussten Zeilen
*/
function tp_query($tp_query) {
	global $wpdb;
	$wpdb->escape($tp_query);
	$tp_return = $wpdb->query($tp_query);
	return $tp_return;
}
/* Abfrage einer einzelnen Variable
 * @param $tp_query (String)
 * Return $tp_var (String)
*/
function tp_var($tp_query) {
	global $wpdb;
	$wpdb->escape($tp_query);
	$tp_var = $wpdb->get_var($tp_query);
	return $tp_var;
} 

/**********************************/
/* Mainfunctions (old kernel.php) */
/**********************************/
/* Print message
 * @param $message (String) - Content
 * @param $site (String) - Page
*/ 
function tp_get_message($message, $site) {
	echo '<p class="teachpress_message">';
	echo '<strong>' . $message . '</strong>';
	echo '<a href="' . $site . '" class="teachpress_back">' . __('resume', 'teachpress') . '</a>';
	echo '</p>';
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
function get_tp_publication_type_options ($selected, $mode = 'list', $url = '', $tgid = '', $yr = '', $autor = '') {
	$selected = htmlentities(utf8_decode($selected));
	$url = htmlentities(utf8_decode($url));
	$tgid = htmlentities(utf8_decode($tgid));
	$yr = htmlentities(utf8_decode($yr));
	$autor = htmlentities(utf8_decode($autor));
	// Define publication types
	$pub_types[0][0] = '0';
	$pub_types[0][1] = __('All types','teachpress');
	$pub_types[1][0] = 'Buch';
	$pub_types[1][1] = __('Buch','teachpress');;
	$pub_types[2][0] = 'Chapter in book';
	$pub_types[2][1] = __('Chapter in book','teachpress');
	$pub_types[3][0] = 'Conference paper';
	$pub_types[3][1] = __('Conference paper','teachpress');
	$pub_types[4][0] = 'Journal article';
	$pub_types[4][1] = __('Journal article','teachpress');
	$pub_types[5][0] = 'Vortrag';
	$pub_types[5][1] = __('Vortrag','teachpress');
	$pub_types[6][0] = 'Bericht';
	$pub_types[6][1] = __('Bericht','teachpress');
	$pub_types[7][0] = 'Sonstiges';
	$pub_types[7][1] = __('Sonstiges','teachpress');
	if ($mode == 'jump') {
		for ($i = 0; $i <= 7; $i++) {
			if ($pub_types[$i][0] == $selected && $selected != '') {
				$current = 'selected="selected"';
			}
			else {
				$current = '';
			}
			$types = $types . '<option value="' . $url . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $pub_types[$i][0] . '&amp;autor=' . $autor . '#tppubs" ' . $current . '>' . __('' . $pub_types[$i][1] . '','teachpress') . '</option>';
		}
	}
	else {
		for ($i = 1; $i <= 7; $i++) {
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
/* Add a new course
 * @param $lvname (String) - Course name
 * @param $veranstaltungstyp (String) - type of course
 * @param $raum (String) - Room
 * @param $dozent (String) - Lecturer
 * @param $termin (DATE) - Date
 * @param $plaetze (INT) - Number of places
 * @param $startein (DATE) - Begin enrollment period
 * @param $endein (DATE) - End enrollment period
 * @param $semester (String) - Term
 * @param $bemerkungen (String) - comment
 * @param $rel_page (INT) - ID of related page
 * @param $parent (INT) - ID of parent course
 * @param $sichtbar (INT) - 0 (no) or 1 (yes)
 * @param $warteliste (INT) - 0(off) or 1 (on)
 * used in addlvs.php
*/
function add_lvs_in_database($lvname, $veranstaltungstyp, $raum, $dozent, $termin, $plaetze, $startein, $endein, $semester,  $bemerkungen, $rel_page, $parent, $sichtbar, $warteliste) {
	global $teachpress_ver;
	$eintragen = sprintf("INSERT INTO " . $teachpress_ver . " (`name`, `vtyp`, `raum`, `dozent`, `termin`, `plaetze`, `fplaetze`, `startein`, `endein`, `semester`, `bemerkungen`, `rel_page`, `parent`, `sichtbar`, `warteliste`) VALUES('$lvname', '$veranstaltungstyp', '$raum', '$dozent', '$termin' , '$plaetze', '$plaetze', '$startein', '$endein', '$semester', '$bemerkungen', '$rel_page', '$parent', '$sichtbar', '$warteliste')", 
	mysql_real_escape_string( "$" . $teachpress_ver . "_name"), 
	mysql_real_escape_string( "$" . $teachpress_ver . "_vtyp"), 
	mysql_real_escape_string( "$" . $teachpress_ver . "_raum") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_dozent") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_termin") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_semester") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_bemerkungen") );
	tp_query($eintragen);
	}
	
/* Delete courses
 * @param $checkbox (Array)
 * used in: showlvs.php
*/
function delete_lehrveranstaltung($checkbox){
	global $teachpress_ver; 
	global $teachpress_kursbelegung;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {  
   		tp_query( "DELETE FROM " . $teachpress_ver . " WHERE veranstaltungs_id = $checkbox[$i]" );
		tp_query( "DELETE FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = $checkbox[$i]" );
    }
}
	
/* Change a course
 * @param $name (String) - Course name
 * @param $vtyp (String) - type of course
 * @param $raum (String) - Room
 * @param $dozent (String) - Lecturer
 * @param $termin (DATE) - Date
 * @param $plaetze (INT) - Number of places
 * @param $fplaetze (INT) - Number of free places
 * @param $startein (DATE) - Begin enrollment period
 * @param $endein (DATE) - End enrollment period
 * @param $semester (String) - Term
 * @param $bemerkungen (String) - comment
 * @param $rel_page (INT) - ID of related page
 * @param $parent (INT) - ID of parent course
 * @param $sichtbar (INT) - 0 (no) or 1 (yes)
 * @param $warteliste (INT) - 0(off) or 1 (on)
 * @param $veranstaltung (INT) - course ID
 * used in: editlvs.php
*/ 
function change_lehrveranstaltung($name, $vtyp, $raum, $dozent, $termin, $plaetze, $fplaetze, $startein, $endein, $semester, $bemerkungen, $rel_page, $parent, $sichtbar, $warteliste, $veranstaltung){
	global $teachpress_ver;	
	$aendern = sprintf("UPDATE " . $teachpress_ver . " SET name = '$name', vtyp = '$vtyp', raum = '$raum', dozent = '$dozent', termin = '$termin', plaetze = '$plaetze', fplaetze = '$fplaetze', startein = '$startein', endein = '$endein', semester = '$semester', bemerkungen = '$bemerkungen', rel_page = '$rel_page', parent = '$parent', sichtbar = '$sichtbar', warteliste = '$warteliste' WHERE veranstaltungs_id = '$veranstaltung'",
	mysql_real_escape_string( "$" . $teachpress_ver . "_name"), 
	mysql_real_escape_string( "$" . $teachpress_ver . "_vtyp"), 
	mysql_real_escape_string( "$" . $teachpress_ver . "_raum") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_dozent") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_termin") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_semester") , 
	mysql_real_escape_string( "$" . $teachpress_ver . "_bemerkungen") );
	tp_query($aendern);
}
/* Copy courses
 * @param $checkbox (Array) - Veranstaltungen die kopiert werden sollen
 * @param $copysem (String) - Semester in das kopiert werden soll
 * used in showlvs.php
*/
function copy_veranstaltung($checkbox, $copysem) {
	global $teachpress_ver; 
	$counter = 0;
	$counter2 = 0;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		$row = "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = $checkbox[$i]";
		$row = tp_results($row);
		foreach ($row as $row) {
				$daten[$counter][0] = $row->veranstaltungs_id;
				$daten[$counter][1] = $row->name;
				$daten[$counter][2] = $row->vtyp;
				$daten[$counter][3] = $row->raum;
				$daten[$counter][4] = $row->dozent;
				$daten[$counter][5] = $row->termin;
				$daten[$counter][6] = $row->plaetze;
				$daten[$counter][8] = $row->startein;
				$daten[$counter][9] = $row->endein;
				$daten[$counter][10] = $row->semester;
				$daten[$counter][11] = $row->bemerkungen;
				$daten[$counter][12] = $row->rel_page;
				$daten[$counter][13] = $row->parent;
				$daten[$counter][14] = $row->sichtbar;
				$daten[$counter][15] = $row->warteliste;
				$counter++;
		}
		// Parents kopieren
		if ( $daten[$i][13] == 0) {
			$merke[$counter2] = $daten[$i][0];
			add_lvs_in_database($daten[$i][1], $daten[$i][2], $daten[$i][3], $daten[$i][4], $daten[$i][5], $daten[$i][6], $daten[$i][6], $daten[$i][8], $daten[$i][9], $copysem, $daten[$i][11], $daten[$i][12], $daten[$i][13], $daten[$i][14], $daten[$i][15]);
			$counter2++;
		}
	}	
	// Childs kopieren
	for( $i = 0; $i < $counter ; $i++ ) {
		if ( $daten[$i][13] != 0) {
			// Test ob in Checkbox die ID der Parent-Veranstaltung steht
			$test = 0;
			for( $j = 0; $j < $counter2 ; $j++ ) {
				if ( $daten[$i][13] == $merke[$j]) {
					$test = $merke[$j];
				}
			}
			// Wenn ja
			if ($test != 0) {
				// Dann suche die Daten aus dem Array und bilde eine Abfrage, nach der Kopie des Parents
				for( $k = 0; $k < $counter ; $k++ ) {
					if ( $daten[$k][0] == $test) {
					$suche = "SELECT veranstaltungs_id FROM " . $teachpress_ver . " WHERE name = '" . $daten[$k][1] . "' AND vtyp = '" . $daten[$k][2] . "' AND raum = '" . $daten[$k][3] . "' AND dozent = '" . $daten[$k][4] . "' AND termin = '" . $daten[$k][5] . "' AND semester = '$copysem' AND parent = 0";
					$suche = tp_var($suche);
					add_lvs_in_database($daten[$i][1], $daten[$i][2], $daten[$i][3], $daten[$i][4], $daten[$i][5], $daten[$i][6], $daten[$i][6], $daten[$i][8], $daten[$i][9], $copysem, $daten[$i][11], $daten[$i][12], $suche, $daten[$i][14], $daten[$i][15]);					
					}
				}
			}
			// Sonst erstelle direkt Kopie
			else {
				add_lvs_in_database($daten[$i][1], $daten[$i][2], $daten[$i][3], $daten[$i][4], $daten[$i][5], $daten[$i][6], $daten[$i][6], $daten[$i][8], $daten[$i][9], $copysem, $daten[$i][11], $daten[$i][12], $daten[$i][13], $daten[$i][14], $daten[$i][15]);
			}
		}
	}
}


/* Einschreibung hinzufuegen (= Student in Veranstaltung einschreiben) (Funktion für Frontend)
 * @param $checkbox (Array) - ID der Veranstaltungen
 * @param $wp_id (INT) - User_ID
 * used in display.php
*/
function add_einschreibung($checkbox, $wp_id){
	// Zur Datenbank verbinden
	global $teachpress_ver; 
	global $teachpress_stud;  
	global $teachpress_kursbelegung;
	for( $i = 0; $i < count( $checkbox ); $i++ ) { 	
		if ( $checkbox[$i] != 0 ) {	
			// Pruefen ob noch freie Plätze vorhanden sind
			$row1 = "SELECT fplaetze, name, startein, endein, warteliste FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$checkbox[$i]'";
			$row1 = tp_results($row1);
			foreach ($row1 as $row1) {
				// Wenn noch freie Plätze vorhanden sind
				if ($row1->fplaetze > 0 ) {
					// dann pruefen ob der Nutzer schon eingeschrieben ist
					$check = "SELECT belegungs_id FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = '$checkbox[$i]' and wp_id = '$wp_id'";
					$check = tp_query($check);
					// Wenn diese gleich 0 ist, dann kann eine Eisnchreibung vorgenommen werden
					if ($check == 0 ) {		
						$eintragen = "INSERT INTO " . $teachpress_kursbelegung . " (veranstaltungs_id, wp_id, warteliste, datum) VALUES ('$checkbox[$i]', '$wp_id', '0', NOW() )";
						tp_query( $eintragen );
						// In Tabelle teachpress_ver die Anzahl der freien Plätze um eins veringern
						$fplaetze = "SELECT fplaetze FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$checkbox[$i]'";
						$fplaetze = tp_var($fplaetze);
						$neu = $fplaetze - 1;
						$aendern = "UPDATE " . $teachpress_ver . " SET fplaetze = '$neu' WHERE veranstaltungs_id = '$checkbox[$i]'";
						tp_query( $aendern );
						echo "<span>" . __('Registration for','teachpress') . " &quot;$row1->name&quot; " . __('successful','teachpress') . ".</span> ";
					}
					else {
						echo "<span>" . __('You are already for','teachpress') . " &quot;$row1->name&quot; " . __('registered','teachpress') . "</span> ";
					}
				}
				// Falls die Veranstaltung schon voll ist, dann automatischer Eintrag als Warteliste wenn diese für LVS aktiviert
				else {
					// Falls Wartelisten aktiviert sind
					if ($row1->warteliste == '1') {
						// Pruefe ob Nutzer schon in Warteliste steht
						$check = "SELECT belegungs_id FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = '$checkbox[$i]' AND wp_id = '$wp_id'";
						$check = tp_query($check);
						// Wenn diese gleich 0 ist, dann kann eine Eisnchreibung vorgenommen werden
						if ($check == 0 ) {
							$eintragen = "INSERT INTO " . $teachpress_kursbelegung . " (veranstaltungs_id, wp_id, warteliste, datum) VALUES ('$checkbox[$i]', '$wp_id', '1', NOW() )";
							tp_query( $eintragen );
							echo "<span>" . __('For','teachpress') . " &quot;$row1->name&quot; " . __('there are no more free places. You are automatically signed in a waiting list.','teachpress') . "</span> ";
						}
						// Sonst Fehlermeldung ausgeben
						else {
							echo "<span>" . __('You are already for','teachpress') . " &quot;$row1->name&quot; " . __('registered','teachpress') . "</span> ";
						}
					}
					// Sonst Fehlermeldung ausgeben
					else {
						echo "<span>" . __('For','teachpress') . " &quot;$row1->name&quot; " . __('there are no more free places.','teachpress') . "</span> ";
					}
				}
			}
		}
	}
}

/* Einschreibung loeschen
 * @param $checkbox (Array) - ID der Veranstaltungen
 * @param $user_ID (INT) - User_ID
 * used in editstudent.php, editlvs.php
*/
function delete_einschreibung($checkbox, $user_ID) {	
	global $teachpress_ver;  
	global $teachpress_kursbelegung;
	global $teachpress_log;
    for( $i = 0; $i < count( $checkbox ); $i++ ) { 
		// ID der Veranstaltung holen
		$row1 = "SELECT veranstaltungs_id FROM " . $teachpress_kursbelegung . " WHERE belegungs_id = '$checkbox[$i]'";
		$row1 = tp_results($row1);
		foreach ($row1 as $row1) {
			// Test ob es Studenten in der Warteliste gibt
			$abfrage = "SELECT belegungs_id FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id' AND warteliste = '1' ORDER BY belegungs_id";
			$test= tp_query($abfrage);
			// Wenn ja
			if ($test != 0) {
				$zahl = 0;
				tp_results($abfrage);
				// dann setze den ersten Eintrag in die Liste
				foreach ($row as $row) {
					if ($zahl < 1) {
						$aendern = "UPDATE " . $teachpress_kursbelegung . " SET warteliste = '0' WHERE belegungs_id = '$row->belegungs_id'";
						tp_query( $aendern );
						$zahl++;
					}
				}
			}
			// Wenn nein, dann erhöhe die Anzahl der freien Plätze
			else {
				$fplaetze= "SELECT fplaetze FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
				$fplaetze = tp_var($fplaetze);
				// In Tabelle teachpress_var die Anzahl der freien Plätze um eins erhöhen
				$neu = $fplaetze + 1;
				$aendern = "UPDATE " . $teachpress_ver . " SET fplaetze = '$neu' WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
				tp_query( $aendern );
			}	
		}
   	tp_query( "DELETE FROM " . $teachpress_kursbelegung . " WHERE belegungs_id = '$checkbox[$i]'" );
	// Sicherheitslog seit Version 0.8
	tp_query( "INSERT INTO " . $teachpress_log . " (id, user, beschreibung, datum) VALUES ('$checkbox[$i]', '$user_ID', 'Delete Einschreibung', NOW())");
    }
}

/* Aufnahme aus Warteliste in Einschreibeliste
 * @param $checkbox (Array) - ID der Einschreibung
 * used in editlvs.php
*/
function aufnahme($checkbox) {
	global $wpdb;
	global $teachpress_kursbelegung;
	for( $i = 0; $i < count( $checkbox ); $i++ ) { 
		$wpdb->update( $teachpress_kursbelegung, array ( 'warteliste' => 0), array ( 'belegungs_id' => $checkbox[$i]), array ( '%d'), array ( '%d' ) );
	}
}

/* User hinzufuegen (Funktion im Frontend)
 * Eingang: siehe Funktionskopf
 * used in display.php
*/
function add_student($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel) {
	global $teachpress_stud; 		
	$eintragen = sprintf("INSERT INTO " . $teachpress_stud . " (`wp_id`, `vorname`, `nachname`, `studiengang`, `urzkurz`, `gebdat`, `email`, `fachsemester`, `matrikel`) VALUES ('$wp_id', '$vorname', '$nachname', '$studiengang', '$urzkurz' , '$gebdat', '$email', '$fachsemester', '$matrikel')", 
	mysql_real_escape_string( "$" . $teachpress_stud . "_wp_id"), 
	mysql_real_escape_string( "$" . $teachpress_stud . "_vorname") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_nachname") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_studiengang") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_urzkurz") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_email") );
    tp_query( $eintragen );
	echo '<strong>' .  __('Registration successful','teachpress') . '</strong> ';
}

/* Userdaten bearbeiten (Funktion im Fronted)
 * Eingang: siehe Funktionskopf
 * used in display.php
*/
function change_student($wp_id, $vorname2, $nachname2, $studiengang2, $gebdat2, $email2, $fachsemester2, $matrikel2) {
	global $teachpress_stud; 
	$aendern = sprintf("UPDATE " . $teachpress_stud . " SET vorname = '$vorname2', nachname = '$nachname2', studiengang = '$studiengang2', gebdat = '$gebdat2', email = '$email2', fachsemester = '$fachsemester2' , matrikel = '$matrikel2' WHERE wp_id = '$wp_id'" ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_vorname" ) ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_nachname" ) ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_email" ) );
    tp_query( $aendern );
	echo "<span>" . __('Changes in your profile successful.','teachpress') . "<span> ";
}

/* Userdaten bearbeiten (Funktion im Backend)
 * Eingang: siehe Funktionskopf
 * used in: editstudent.php
*/
function change_student_manuell($wp_id, $vorname, $nachname, $studiengang, $urzkurz, $gebdat, $email, $fachsemester, $matrikel, $user_ID) {
	global $teachpress_stud; 
	$aendern = sprintf("UPDATE " . $teachpress_stud . " SET vorname = '$vorname', nachname = '$nachname', studiengang = '$studiengang', gebdat = '$gebdat', email = '$email', fachsemester = '$fachsemester', matrikel = '$matrikel' WHERE wp_id = '$wp_id'" ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_vorname" ) ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_nachname" ) ,
	mysql_real_escape_string( "$" . $teachpress_stud . "_email" ) );
    tp_query( $aendern );
	// Sicherheitslog seit Version 0.8
	tp_query( "INSERT INTO " . $teachpress_log . " (id, user, beschreibung, datum) VALUES ('$wp_id', '$user_ID', 'Change Student', NOW())");
}

/* Student aus LVS austragen (Funktion im Frontend)
 * @param $checkbox2 (Array) - ID der Einschreibungen
 * used in: display.php
*/
function delete_einschreibung_student($checkbox2) {
	global $teachpress_ver; 
	global $teachpress_kursbelegung;
	for( $i = 0; $i < count( $checkbox2 ); $i++ ) { 
		// ID der Veranstaltung abfragen
		$row1 = "SELECT veranstaltungs_id FROM " . $teachpress_kursbelegung . " WHERE belegungs_id = '$checkbox2[$i]'";
		$row1 = tp_results($row1);
			foreach ($row1 as $row1) {
				// Test ob es Studenten in der Warteliste gibt
				$abfrage = "SELECT belegungs_id FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id' AND warteliste = '1' ORDER BY belegungs_id";
				$test = tp_query($abfrage);
				// Wenn ja
				if ($test!= 0) {
					$zahl = 0;
					$row = tp_results($abfrage);
					// dann setze den ersten Eintrag in die Liste
					foreach ($row as $row) {
						if ($zahl < 1) {
							$aendern = "UPDATE " . $teachpress_kursbelegung . " SET warteliste = '0' WHERE belegungs_id = '$row->belegungs_id'";
							tp_query( $aendern );
							$zahl++;
						}
					}
				}
				// Wenn nein, dann erhoehe die Anzahl der freien Plätze
				else {
					$fplaetze = "SELECT fplaetze FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
					$fplaetze = tp_var($fplaetze);
					// In Tabelle teachpress_ver die Anzahl der freien Plaetze um eins erhoehen
					$neu = $fplaetze + 1;
					$aendern = "UPDATE " . $teachpress_ver . " SET fplaetze = '$neu' WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
					tp_query( $aendern );
				}
			}
   		tp_query( "DELETE FROM " . $teachpress_kursbelegung . " WHERE belegungs_id = '$checkbox2[$i]'" );
    }	
	echo "<span>" . __('You are signed out successful','teachpress') . "</span> ";
}

/* Einstellungen aendern
 * @param $semester (String)
 * @param $permalink (INT)
 * used in: settings.php
*/
function change_einstellungen($semester, $permalink, $userrole, $sign_out) {
	global $teachpress_einstellungen; 		
	$eintragen = "UPDATE " . $teachpress_einstellungen . " SET  wert = '$semester' WHERE variable = 'sem'";
	tp_query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_einstellungen . " SET  wert = '$permalink' WHERE variable = 'permalink'";
    tp_query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_einstellungen . " SET  wert = '$sign_out' WHERE variable = 'sign_out'";
    tp_query( $eintragen );
	tp_update_userrole($userrole);
}

/* Einstellungen: Einstellung (Semester, Studiengang, Veranstaltungstyp) löschen
 * @param $delete (Array)
 * used in: settings.php
*/
function delete_einstellung($delete) {
	global $teachpress_einstellungen;		
	tp_query( "DELETE FROM " . $teachpress_einstellungen . " WHERE einstellungs_id = '$delete'" );
}

/* Einstellungen: Einstellung (Semester, Studiengang, Veranstaltungstyp) hinzufügen
 * @param $name (String)
 * @param $typ (String)
 * used in: settings.php
*/
function add_einstellung($name, $typ) { 
	global $teachpress_einstellungen; 	
	$add = sprintf("INSERT INTO " . $teachpress_einstellungen . " (`variable`, `wert`, `category`) VALUES ('$name', '$name', '$typ')", 
	mysql_real_escape_string( "$" . $teachpress_einstellungen . "_variable" ),
	mysql_real_escape_string( "$" . $teachpress_einstellungen . "_wert" ));
	tp_query($add);
}

/* User manuell über das Backend hinzufügen
 * Eingang: siehe Funktionskopf
 * used in: student_neu.php
*/
function add_student_manuell($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel) {
	global $teachpress_stud;			
	$eintragen = sprintf("INSERT INTO " . $teachpress_stud . " (`wp_id`, `vorname`, `nachname`, `studiengang`, `urzkurz`, `gebdat`, `email`, `fachsemester`, `matrikel`) VALUES ('$wp_id', '$vorname', '$nachname', '$studiengang', '$urzkurz', '$gebdat', '$email', '$fachsemester', '$matrikel')", 
	mysql_real_escape_string( "$" . $teachpress_stud . "_wp_id"), 
	mysql_real_escape_string( "$" . $teachpress_stud . "_vorname") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_nachname") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_studiengang") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_urzkurz") , 
	mysql_real_escape_string( "$" . $teachpress_stud . "_email") );
	echo $eintragen;
    // Schickt die Anfrage an die DB und schreibt die Daten in die Tabelle
    tp_query( $eintragen );
}

/* Student manuell über das Backend in eine LVS einschreiben
 * @param $student (Integer)
 * @param $veranstaltung (Integer)
 * used in: student_neu.php
*/	
function student_manuell_eintragen ($student, $veranstaltung) {
	global $teachpress_ver; 
	global $teachpress_kursbelegung;
	$eintragen = "INSERT INTO " . $teachpress_kursbelegung . " (veranstaltungs_id, wp_id, warteliste, datum) VALUES ('$veranstaltung', '$student', '0', NOW() )";
	tp_query( $eintragen );
	// Anzahl der freien Plätze verringern, wenn noch freie Plätze vorhanden sind
	$fplaetze = "SELECT fplaetze FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$veranstaltung'";
	$fplaetze = tp_var($fplaetze);
		if ($fplaetze > 0 ) {
			$neu = $fplaetze - 1;
			$aendern = "UPDATE " . $teachpress_ver . " SET fplaetze = '$neu' WHERE veranstaltungs_id = '$veranstaltung'";
			tp_query( $aendern );
		}
}	
/* Student über das Backend löschen
 * @param $checkbox (Array) - ID der Einschreibung
 * @param $user_ID (INT) - User_ID
 * used in: student.php
*/ 
function delete_student_admin ($checkbox, $user_ID){
	global $teachpress_ver; 
	global $teachpress_stud; 
	global $teachpress_kursbelegung;
	global $teachpress_log;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {
		// Suche nach allen Veranstaltungen in die der Student eingetragen war
		$row1 = "SELECT veranstaltungs_id FROM " . $teachpress_kursbelegung . " WHERE wp_id = '$checkbox[$i]'";
		$row1 = tp_results($row1);
			foreach ($row1 as $row1) {
				// Test ob es Studenten in der Warteliste gibt
				$abfrage = "SELECT belegungs_id FROM " . $teachpress_kursbelegung . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id' AND warteliste = '1' ORDER BY belegungs_id";
				$test = tp_query($abfrage);
				// Wenn ja
				if ($rows > 0) {
					$zahl = 0;
					// dann setze den ersten Eintrag in die Liste
					$row = tp_results($abfrage);
					foreach($row as $row) {
						if ($zahl < 1) {
							$aendern = "UPDATE " . $teachpress_kursbelegung . " SET warteliste = '0' WHERE belegungs_id = '$row->belegungs_id'";
							tp_query( $aendern );
							$zahl++;
						}
					}
				}
				// Wenn nein, dann erhöhe die Anzahl der freien Plätze
				else {
					$fplaetze = "SELECT fplaetze FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
					$fplaetze = tp_var($fplaetze);
					// In Tabelle teachpress_var die Anzahl der freien Plätze um eins erhöhen
					$neu = $fplaetze + 1;
					$aendern = "UPDATE " . $teachpress_ver . " SET fplaetze = '$neu' WHERE veranstaltungs_id = '$row1->veranstaltungs_id'";
					// Schickt die Anfrage an die DB und schreibt die Daten in die Tabelle
					tp_query( $aendern );
				}
			}
   		tp_query( "DELETE FROM " . $teachpress_stud . " WHERE wp_id = $checkbox[$i]" );
		// Sicherheitslog
		tp_query( "INSERT INTO " . $teachpress_log . " (id, user, beschreibung, datum) VALUES ('$checkbox[$i]', '$user_ID', 'Delete Student', NOW())");
		tp_query( "DELETE FROM " . $teachpress_kursbelegung . " WHERE wp_id = $checkbox[$i]" );
    }
}

/* Publikation hinzufügen
 * Eingang: siehe Funktionskopf
 * used in: addpublications.php
*/
function add_pub ($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $tags, $bookmark, $user, $comment, $image_url, $rel_page, $is_isbn) {
	global $teachpress_pub;
	global $teachpress_tags; 
	global $teachpress_beziehung;
	global $teachpress_user;
		
	$eintragen = sprintf("INSERT INTO " . $teachpress_pub . " (`name`, `typ`, `autor`, `verlag`, `jahr`, `isbn`, `url`, `sort`, `comment`, `image_url`, `rel_page`, `is_isbn`) VALUES('$name', '$typ', '$autor', '$erschienen', '$jahr' , '$isbn', '$links', '$sort', '$comment', '$image_url', '$rel_page', '$is_isbn')", 
	mysql_real_escape_string( "$" . $teachpress_pub . "_name"), 
	mysql_real_escape_string( "$" . $teachpress_pub . "_typ"), 
	mysql_real_escape_string( "$" . $teachpress_pub . "_autor") , 
	mysql_real_escape_string( "$" . $teachpress_pub . "_verlag") , 
	mysql_real_escape_string( "$" . $teachpress_pub . "_isbn") , 
	mysql_real_escape_string( "$" . $teachpress_pub . "_url"),
	mysql_real_escape_string( "$" . $teachpress_pub . "_comment"),
	mysql_real_escape_string( "$" . $teachpress_pub . "_image_url"),
	mysql_real_escape_string( "$" . $teachpress_pub . "_rel_page"),
	mysql_real_escape_string( "$" . $teachpress_pub . "_is_isbn") );
	
    tp_query( $eintragen );
	
	// Bookmarks
	for( $i = 0; $i < count( $bookmark ); $i++ ) { 
		$rw="SELECT pub_id FROM " . $teachpress_pub . " WHERE name ='$name' AND autor='$autor' AND verlag='$erschienen' AND sort='$sort'";
		$rw = tp_results($rw);
		foreach ($rw as $rw) {
			if ($bookmark[$i] != '' || $bookmark[$i] != 0) {
				tp_query( "INSERT INTO " . $teachpress_user . " (pub_id, user) VALUES ('$rw->pub_id', '$bookmark[$i]')");
			}
		}
	}
	$array = explode(",",$tags);
	foreach($array as $element) {
		// Check ob tag schon vorhanden
		$element = trim($element);
		if ($element != "") {
			$abfrage = "SELECT tag_id FROM " . $teachpress_tags . " WHERE name = '$element'";
			$check = tp_query($abfrage);
			// Falls tag nicht vorhanden, in Datenbank einfügen
			if ($check == 0){
				$eintrag = sprintf("INSERT INTO " . $teachpress_tags . " (`name`) VALUES('$element')", 
				mysql_real_escape_string( "$" . $teachpress_tags . "_name") );
				tp_query($eintrag);
				$row = tp_results($abfrage);
			}
			else {
				$row = tp_results($abfrage);
			}
			// Abfrage von pub_id und tag_id und Eintrag der Beziehung
			foreach($row as $row) {
				$row2 = "SELECT pub_id FROM " . $teachpress_pub . " WHERE name ='$name' AND autor='$autor' AND verlag='$erschienen' AND sort='$sort'";
				$row2 = tp_results($row2);
				foreach ($row2 as $row2) {
					// Prüfe ob Beziehung schon vorhanden
					$test = "SELECT pub_id FROM " .$teachpress_beziehung . " WHERE pub_id = '$row2->pub_id' AND tag_id = '$row->tag_id'";
					$test = tp_query($test);
					// Wenn nicht, dann eintragen
					if ($test == 0) {
						$eintrag2 = "INSERT INTO " .$teachpress_beziehung . " (pub_id, tag_id) VALUES ('$row2->pub_id', '$row->tag_id')";
						tp_query($eintrag2);
					}
				}
			}
		}
	}
}
/* Publikation löschen
 * @param $checkbox (Array) - IDs der Publikationen
 * used in: showpublications.php
*/
function delete_publication($checkbox){	
	global $teachpress_pub; 
	global $teachpress_beziehung;
	global $teachpress_user;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {  
   		tp_query( "DELETE FROM " . $teachpress_pub . " WHERE pub_id = $checkbox[$i]" );
		tp_query( "DELETE FROM " . $teachpress_beziehung . " WHERE pub_id = $checkbox[$i]" );
		tp_query( "DELETE FROM " . $teachpress_user . " WHERE pub_id = $checkbox[$i]" );
    }
}	

/* Publikation bearbeiten
 * Eingang: siehe Funktionskopf
 * used in: editpub.php
*/
function change_pub ($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $tags, $comment, $image_url, $rel_page, $is_isbn, $pub_ID, $delbox) {
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_beziehung;
	$aendern = "UPDATE " . $teachpress_pub . " SET name = '$name', typ = '$typ', autor = '$autor', verlag = '$erschienen', jahr = '$jahr', isbn = '$isbn', url = '$links', sort = '$sort', comment = '$comment', image_url = '$image_url', rel_page = '$rel_page', is_isbn = '$is_isbn' WHERE pub_id = '$pub_ID'";
    // Schickt die Anfrage an die DB und schreibt die Daten in die Tabelle
    tp_query( $aendern );
	// Löschen von Tag-Beziehungen
	for( $i = 0; $i < count( $delbox ); $i++ ) {
   		tp_query( "DELETE FROM " . $teachpress_beziehung . " WHERE belegungs_id = $delbox[$i]" );
    }
	$array = explode(",",$tags);
	foreach($array as $element) {
		// Check ob tag schon vorhanden
		$element = trim($element);
		if ($element != '') {
			$row = "SELECT tag_id FROM " . $teachpress_tags . " WHERE name = '$element'";
			$check = tp_query($row);
			// Falls tag nicht vorhanden, in Datenbank einfügen
			if ($check == 0){
				$eintrag = "INSERT INTO " . $teachpress_tags . " (name) VALUES ('$element')";
				tp_query($eintrag);
				$row = tp_results($row);
			}
			else {
				$row = tp_results($row);
			}
			// Abfrage von pub_id und tag_id und Eintrag der Beziehung
			foreach($row as $row) {
				// Prüfe ob Beziehung schon vorhanden
				$test ="SELECT pub_id FROM " .$teachpress_beziehung . " WHERE pub_id = '$pub_ID' AND tag_id = '$row->tag_id'";
				$test = tp_query($test);
				// Wenn nicht, dann eintragen
				if ($test == 0) {
					$eintrag = "INSERT INTO " .$teachpress_beziehung . " (pub_id, tag_id) VALUES ('$pub_ID', '$row->tag_id')";
					tp_query($eintrag);
				}
			}
		}	
	}
}
/* Hinzufuegen von Bookmarks 
 * @param $add_id (INT) - IDs der Publikationen
 * @param $user (INT) - User_ID
 * used in showpublications.php
*/
function add_bookmark ($add_id, $user) {
	global $teachpress_user;
	tp_query( "INSERT INTO " . $teachpress_user . " (pub_id, user) VALUES ('$add_id', '$user')");
}
/* Löschen von Bookmarks 
 * @param $del_id (INT) - IDs der Publikationen
 * @param $user (INT) - User_ID
 * used in publications.php
*/
function del_bookmark($del_id) {
	global $teachpress_user;
	tp_query( "DELETE FROM " . $teachpress_user . " WHERE bookmark_id = '$del_id'" );
}
/* Loeschen von Tags
 * @param $checkbox (Array) - Tag IDs
 * used in tags.php
*/
function delete_tags($checkbox) {
	global $teachpress_beziehung;
	global $teachpress_tags;
    for( $i = 0; $i < count( $checkbox ); $i++ ) {  
		tp_query( "DELETE FROM " . $teachpress_beziehung . " WHERE tag_id = $checkbox[$i]" );
		tp_query( "DELETE FROM " . $teachpress_tags . " WHERE tag_id = $checkbox[$i]" );
    }
} 
/* Bearbeiten von Tags
 * @param $tag_id (INT)
 * @param $name (String)
 * used in tags_php
*/
function edit_tag($tag_id, $name) {
	global $teachpress_tags;
	tp_query( "UPDATE " . $teachpress_tags . " SET name = '$name' WHERE tag_id = '$tag_id'" );
} 
/* Anzeige des Einschreibesystems im Fronted
 * @param $content
 * Return: $content
 * used in: WordPress-Core
*/
function teachpress_anzeigen($content) {
	if(!preg_match('<!--LVS-->', $content))	{
		return $content;
	}		
	teachpress_enrollment_frontend();
	return __('<!--LVS-->', $content);
}

/* Anzeige einer Übersicht der LVS des aktuellen Semesters im Fronted
 * @param $content
 * Return: $content
 * used in: WordPress-Core
*/
function teachpress_lvs_fronted($content) {
	if(!preg_match('<!--LVS2-->', $content)) {	
		return $content;
	}		
	teachpress_course_overview();
	return __('<!--LVS2-->', $content);
}

/* wandelt ein MySQL-DATE (ISO-Date) in ein traditionelles deutsches Datum um. 
 * @param $datum
 * Return: datum formatiert
 * used in: display.php
*/
function date_mysql2german($datum)  
{ 
  list($jahr, $monat, $tag) = explode("-", $datum); 
  return sprintf("%02d.%02d.%04d", $tag, $monat, $jahr); 
}

/* Gibt die aktuelle Version von teachPress zurueck
 * Return: $version (String)
 * used in: display.php, docs.php, excel.php
*/
function get_tp_version(){
	global $tp_version;
	return $tp_version;
}
/* Update who can use teachPress
 * @param $roles
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
/* Database update manager
 * used in settings.php
*/
function tp_db_update() {
	global $teachpress_einstellungen;
	global $teachpress_pub;
	global $teachpress_ver;
	// Test ob Datenbank noch aktuell
	$test = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'db-version'";
	$test = tp_var($test) ;
	$version = get_tp_version();
	$site = 'admin.php?page=teachpress/settings.php';
	// bei versionen auf aktuellen Stand
	if ($test == $version) {
		$message = __('An update is not necessary.','teachpress');
		tp_get_message($message, $site);
	} 
	else {
		/*
		 * Capabilities
		*/
		global $wp_roles;
		$wp_roles->WP_Roles();
		$role = $wp_roles->get_role('administrator');
		if ( !$role->has_cap('use_teachpress') ) {
			$wp_roles->add_cap('administrator', 'use_teachpress');
		}
		/* 
		 * teachpress_ver
		*/
		// Spalte rel_page anlegen in teachpress_ver
		$sql = "SHOW COLUMNS FROM " . $teachpress_ver . " LIKE 'rel_page'";
		$test = tp_query($sql);
		if ($test == '0') {
			tp_query("ALTER TABLE " . $teachpress_ver . " ADD `rel_page` INT NULL AFTER `bemerkungen`");
		}
		// Spalte url loeschen in teachpress_ver
		$sql = "SHOW COLUMNS FROM " . $teachpress_ver . " LIKE 'url'";
		$test = tp_query($sql);
		if ($test == '1') {
			tp_query("ALTER TABLE " . $teachpress_ver . " DROP `url`");
		}
		/*
		 * teachpress_pub
		*/ 
		// Spalte Image_URL anlegen
		// ab Version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'image_url'";
		$test = tp_query($sql);
		if ($test == '0') {
			tp_query("ALTER TABLE " . $teachpress_pub . " ADD `image_url` VARCHAR(200) NULL DEFAULT NULL AFTER `comment`");
		}
		// Spalte rel_page anlegen
		// ab Version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'rel_page'";
		$test = tp_query($sql);
		if ($test == '0') {
			tp_query("ALTER TABLE " . $teachpress_pub . " ADD `rel_page` INT NULL AFTER `image_url`");
		}
		// Spalte is_ISBN anlegen
		// ab Version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'is_isbn'";
		$test = tp_query($sql);
		if ($test == '0') {
			tp_query("ALTER TABLE " . $teachpress_pub . " ADD `is_isbn` INT(1) NULL DEFAULT NULL AFTER `rel_page`");
		}
		/*
		 * teachpress_einstellungen
		*/
		$sql = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sign_out'";
		$test = tp_query($sql);
		if ($test == '0') {
			tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('sign_out', '0', 'system')"); 
		}
		// Versionsinfo in Datenbank aktualisieren
		tp_query("UPDATE " . $teachpress_einstellungen . " SET  wert = '$version' WHERE variable = 'db-version'");
		// Abschluss
		$message = __('Update successful','teachpress');
		tp_get_message($message, $site);
	}
}

/**************/
/* Shortcodes */
/**************/

/* Termin-Shortcode
 * @param $attr(Array) mit parameter 'id' (integer)
 * Return $asg (String)
 * used in WordPress-Shortcode API
*/
function tpdate_shortcode($attr) {
	// Rueckgabestring wird ueber verschiedene Teile zusammengesetzt
	$a1 = '<div class="untertitel">' . __('Date(s)','teachpress') . '</div>
			<table border="0" cellspacing="0" cellpadding="5" width="100%" class= "tpdate">';
	// Abfrage nach den Daten der Lehrveranstaltung deren ID im Shortcode angegeben wurde		
	global $teachpress_ver; 
	$row = "SELECT name, vtyp, raum, dozent, termin, bemerkungen FROM " . $teachpress_ver . " WHERE veranstaltungs_id= ". $attr["id"] . "";
	$row = tp_results($row);
	foreach($row as $row) {
		$v_test = $row->name;
		$a2 = $a2 . ' 
			  <tr>
				<td rowspan="2"><strong>' . $row->vtyp . '</strong></td>
				<td>' . $row->termin . ' ' . $row->raum . '</td>
				<td rowspan="2">' . $row->dozent . '</td>
				<td rowspan="2">&nbsp;</td>
			  </tr>
			  <tr>
				<td>' . $row->bemerkungen . '</td>
			  </tr>
			  <tr>
				<td colspan="4" class="tpdatecol">&nbsp;</td>
			  </tr>';
	} 
	// Abfrage nach Tochter-Lehrveranstaltungen
	$row = "SELECT name, vtyp, raum, dozent, termin, bemerkungen FROM " . $teachpress_ver . " WHERE parent= ". $attr["id"] . " ORDER BY name";
    $row = tp_results($row);
	foreach($row as $row) {
		// wenn parent und child name gleich sind, dann zeige nur an
		if ($v_test == $row->name) {
			$row->name = $row->vtyp;
		}
        $a3 = $a3 . '
		  <tr>
			<td rowspan="2"><strong>' . $row->name . '</strong></td>
			<td>' . $row->termin . ' ' . $row->raum . '</td>
			<td rowspan="2">' . $row->dozent . '</td>
			<td rowspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td>' . $row->bemerkungen . '</td>
		  </tr>
          <tr>
          	<td colspan="4" class="tpdatecol">&nbsp;</td>
          </tr>
		';
	} 
	$a4 = '</table>';
	// Zusammensetzen des Rueckgabestring
	$rueck = '' . $a1 . '' . $a2 . '' . $a3 . '' . $a4 . '';
	return $rueck;
}
/* Publikation-Shortcode
 * @param $attr (Array) mit paramenter 'userid' (INT), maxsize (INT), minsize (INT), limit (INT)
 * $_GET: $yr (Jahr, Integer), $type (Typ, String), $autor (Autor, Integer)
 * Return $asg (String)
 * used in WordPress-Shortcode API
*/
function tpcloud_shortcode($atts) {
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_beziehung;
	global $teachpress_einstellungen;
	global $teachpress_user;
	global $pagenow;
	// Parameter aus dem Shortcode holen
	extract(shortcode_atts(array(
		'id' => 0,
		'maxsize' => 35,
		'minsize' => 11,
		'limit' => 30,
	), $atts));
	// tgid - gibt ausgewaehlten Tag an
	$tgid = htmlentities($_GET[tgid]);
	if ($tgid == '') {
		$tgid = 0;
	}
	// year - Jahr
	$yr = htmlentities($_GET[yr]);
	if ($yr == '') {
		$yr = 0;
	}
	// Typ
	$type = htmlentities(utf8_decode($_GET[type]));
	if ($type == '') {
		$type = 0;
	}
	// Autor
	$autor = htmlentities($_GET[autor]);
	if ($autor == '') {
		$autor = 0;
	}
	// Falls Autor via Shortcode gesetzt
	if ($id != 0) {
		$autor = $id;
	}
	// Zahlen werden auf Integer gesetzt
	settype($id, 'integer');
	settype($tgid, 'integer');
	settype($year, 'integer');
	settype($autor, 'integer');
	// ID Namen bei abgeschalteten Permalinks ermitteln
	if (is_page()) {
		$page = "page_id";
	}
	else {
		$page = "p";
	}
	/*
	 * Tag cloud
	*/
	
	// Abfrage ob Permalinks verwendet werden oder nicht
	$permalink = tp_var("SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'permalink'");
	
	// Ermittle Anzahl der Tags absteigend sortiert
	if ($id == '0') {
		$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_beziehung . " GROUP BY " . $teachpress_beziehung . ".`tag_id` ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
	}
	else {
		$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_beziehung . " b  LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id  WHERE u.user = '$id' GROUP BY b.tag_id ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
	}
	
	// Ermittle einzelnes Vorkommen der Tags, sowie Min und Max
	$sql = "SELECT MAX(anzahlTags) AS max, min(anzahlTags) AS min, COUNT(anzahlTags) as gesamt FROM (".$sql.") AS temp";
	$tagcloud_temp = mysql_fetch_array(mysql_query($sql));
	$max = $tagcloud_temp['max'];
	$min = $tagcloud_temp['min'];
	$insgesamt = $tagcloud_temp['gesamt'];
	
	// Tags und Anzahl zusammenstellen
	// Unterscheidung welche ID angegeben wurde, danach holen der Tags aus Datenbank
	// 0 enspricht alle Publikationen
	if ($id == '0') {
		$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name,  t.tag_id as tag_id FROM " . $teachpress_beziehung . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
	}
	else {
		$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name, t.tag_id as tag_id FROM " . $teachpress_beziehung . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id  WHERE u.user = '$id' GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
	}
	$temp = mysql_query($sql);
	// Endausgabe der Cloud zusammenstellen
	while ($tagcloud = mysql_fetch_array($temp)) {
		// Schriftgröße berechnen
		// Minimum ausgleichen
		if ($min == 1) {
			$min = 0;
		}
		// Formel: max. Schriftgroesse*(aktuelle anzahl - kleinste Anzahl)/ (groeßte Anzahl - kleinste Anzahl)
		$size = floor(($maxsize*($tagcloud['tagPeak']-$min)/($max-$min)));
		// Ausgleich der Schriftgröße
		if ($size < $minsize) {
			$size = $minsize ;
		}
		if ($tagcloud['tagPeak'] == 1) {
			$pub = __('publication', 'teachpress');
		}
		else {
			$pub = __('publications', 'teachpress');
		}
		// Falls Permalinks genutzt werden
		if ($permalink == 1) {
			// Link zum aktuellen Post herausfinden
			$link = $pagenow;
			// das index.php aus der URL schneiden
			$link = str_replace("index.php", "", $link);
			// String zusammensetzen
			// fuer aktuellen Tag
			if ( $tgid == $tagcloud['tag_id'] ) {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?tgid=0&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" class = "teachpress_cloud_active" title="' . __('Delete tag as filter','teachpress') . '">' . $tagcloud['name'] . ' </a></span> ';
			}
			// Normaler Tag
			else {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?tgid=' . $tagcloud['tag_id'] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" title="' . $tagcloud['tagPeak'] . ' ' . $pub . '">' . $tagcloud['name'] . ' </a></span> ';
			}
		}
		// wenn keine Permalinks genutzt werden
		else {
			$postid = get_the_ID();
			// Link zum aktuellen Post herausfinden
			$link = $pagenow;
			// das index.php aus der URL schneiden
			$link = str_replace("index.php", "", $link);
			// String zusammensetzen
			// fuer aktuellen Tag
			if ( $tgid == $tagcloud['tag_id'] ) {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=0&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" class = "teachpress_cloud_active" title="' . __('Delete tag as filter','teachpress') . '">' . $tagcloud['name'] . ' </a></span> ';
			}
			else {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=' . $tagcloud['tag_id'] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" title="' . $tagcloud['tagPeak'] . ' ' . $pub . '"> ' . $tagcloud['name'] . '</a></span> ';
			}
		}
	}
	
	/* 
	 * Auswahl-Filter
	*/ 
	
	// for javascripts
	$str ="'";
	// Link structure
	if ($permalink == 1) {
		$tpurl = '' . $link . '?';
	}
	else {
		$tpurl = '' . $link . '?' . $page . '=' . $postid . '&amp;';
	}
	// Filter 1
	if ($id == 0) {
		$row = tp_results("SELECT DISTINCT jahr from " . $teachpress_pub . " ORDER BY jahr DESC");
	}
	else {
		$row = tp_results("SELECT DISTINCT " . $teachpress_pub . ".jahr from " . $teachpress_pub . " 
							INNER JOIN " . $teachpress_user . " ON " . $teachpress_user . ".pub_id=" . $teachpress_pub . ".pub_id
							WHERE " . $teachpress_user . ".user = '$id'
							ORDER BY jahr DESC");
	}
	$options = '';
	foreach ($row as $row) {
		if ($row->jahr == $yr) {
			$current = 'selected="selected"';
		}
		else {
			$current = '';
		}
		$options = $options . '<option value = "' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $row->jahr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" ' . $current . '>' . $row->jahr . '</option>';
	}
	$filter1 ='<select name="yr" id="yr" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">
               <option value="' . $tpurl . 'tgid=' . $tgid . '&amp;yr=0&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs">' . __('All years','teachpress') . '</option>
			   ' . $options . '
               </select>';
	// Filter 2
	$options = '';
	$options = get_tp_publication_type_options($type, 'jump', $tpurl, $tgid, $yr, $autor);
	$filter2 ='<span style="padding-left:10px; padding-right:10px;"><select name="type" id="type" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">' . $options . '</select></span>';
	// Filter 3
	$current = '';	
	$options = '';  
	// Wenn alle Publikationen angefordert werden		   
	if ($id == '0') {	
		$row = tp_results("SELECT DISTINCT user FROM " . $teachpress_user . "");	 
		foreach ($row as $row) {
			if ($row->user == $autor) {
				$current = 'selected="selected"';
			}
			else {
				$current = '';
			}
			$user_info = get_userdata($row->user);
			$options = $options . '<option value = "' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $row->user . '#tppubs" ' . $current . '>' . $user_info->display_name . '</option>';
		}  
		$filter3 ='<select name="pub-author" id="pub-author" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">
					   <option value="' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=0#tppubs">' . __('All authors','teachpress') . '</option>
				   ' . $options . '
				   </select>';	
	}
	// Bei Publikationen eines bestimmten Autors entfaellt 3. Filter	   	
	else {
		$filter3 = "";
	}
	// Endformatierung
	if ($yr == '' && $type == '' && ($autor == '' || $autor == $id ) && $tgid == '') {
		$showall = "";
	}
	else {
		// Link zum aktuellen Post herausfinden
		$link = $pagenow;
		// das index.php aus der URL schneiden
		$link = str_replace("index.php", "", $link);
		if ($permalink == 1) {
			$showall ='<a href="' . $link . '?tgid=0#tppubs" title="' . __('Show all','teachpress') . '">' . __('Show all','teachpress') . '</a>';
		}
		else {
			$showall ='<a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=0#tppubs" title="' . __('Show all','teachpress') . '">' . __('Show all','teachpress') . '</a>';
		}
	}
	// fertige Tag-Cloud fuer return als String zusammensetzen
	$asg1 = '<a name="tppubs" id="tppubs"></a><div class="teachpress_cloud">' . $asg . '</div><div class="teachpress_filter">' . $filter1 . '' . $filter2 . '' . $filter3 . '</div><p align="center">' . $showall . '</p>';
	
	/* 
	 * Liste der Publikatioen
	*/
	// Filter beruecksichtigen
	// nach Jahr
	if ($yr == '' || $yr == 0) {
		$select_year = '';
	}
	else {
		$select_year = 'p.jahr = ' . $yr . '';
	}
	// nach Typ
	if ($type == '0') {
		$select_type = '';
	}
	else {
		$select_type = 'p.typ = ' . $str . '' . $type . '' . $str . '';
	}
	if ($select_year != '') {
		if ($select_type != '') {
			$zusatz1 = "WHERE " . $select_year . " AND " . $select_type . "";
			$zusatz2 = "AND " . $select_year . " AND " . $select_type . "";
		}
		else {
			$zusatz1 = "WHERE " . $select_year . "";
			$zusatz2 = "AND " . $select_year . "";
		}
	}
	else {
		if ($select_type != '') {
			$zusatz1 = "WHERE " . $select_type . "";
			$zusatz2 = "AND " . $select_type . "";
		}
		else {
			$zusatz1 = "";
			$zusatz2 = "";
		}
	}
	// id umstellen
	if ($autor != 0) {
		$id = $autor;
	}
	 $select = "SELECT DISTINCT p.pub_id, p.name, p.typ, p.autor, p.verlag, p.jahr, p.isbn , p.url, p.is_isbn 
			FROM " . $teachpress_beziehung . " b ";
	// Wenn kein Tag ausgewaehlt wurde
	if ($tgid == "" || $tgid == 0) {
		// fuer alle Publikationen
		if ($id == 0) {
		$row =  "" . $select . "
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			" . $zusatz1 . "
			ORDER BY p.sort DESC";
		}
		// fuer Publikationen eines Autors
		else {
		$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id= b.pub_id
			WHERE u.user = '$id' " . $zusatz2 . "
			ORDER BY p.sort DESC";
		}	
	}
	// Falls Tag ausgewaehlt wurde
	else {
		if ($id == 0) {
		// fuer alle Publikationen
		$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p  ON p.pub_id = b.pub_id
			WHERE t.tag_id = '$tgid' " . $zusatz2 . "
			ORDER BY p.sort DESC";
		}
		// fuer Publikationen eines Autors
		else {
		$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' AND t.tag_id = '$tgid' " . $zusatz2 . "
			ORDER BY p.sort DESC";
		}	
	}
		$row = tp_results($row);
		$sql = "SELECT name, tag_id, pub_id FROM (SELECT t.name AS name, t.tag_id AS tag_id, b.pub_id AS pub_id FROM " . $teachpress_tags . " t LEFT JOIN " . $teachpress_beziehung . " b ON t.tag_id = b.tag_id ) as temp";
		$temp = tp_results($sql);
		$atag = 0;
		foreach ($temp as $temp) {
			$all_tags[$atag][0] = $temp->name;
			$all_tags[$atag][1] = $temp->tag_id;
			$all_tags[$atag][2] = $temp->pub_id;
			$atag++;
		}
		$tpz = 0;
		$jahr = 0;
		foreach ($row as $row) {
			// Tags suchen
			for ($i = 0; $i < $atag; $i++) {
				if ($all_tags[$i][2] == $row->pub_id) {
					$tag_string = $tag_string . '<a href="' . $link . '?tgid=' . $all_tags[$i][1] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . '#tppubs" title="' . __('Show all publications which have a relationship to this tag','teachpress') . '">' . $all_tags[$i][0] . '</a>, ';
				}
			}
			$tag_string = substr($tag_string, 0, -2);
			// Falls url eingegeben wurde, wird ein Link draus
			if ($row->url !='') {
				$row->name = '<a href="' . $row->url . '">' . $row->name . '</a>';
			}
			// Falls isbn eingegeben wurde, wird formatiert
			if ($row->isbn != '') {
				// Test ob ISBN oder ISSN
				if ($row->is_isbn == '0') { 
					$row->isbn = '; ISSN: ' . $row->isbn . '';
				}
				else {
					$row->isbn = '; ISBN: ' . $row->isbn . '';
				}
			}
			// Ausgabe in Array umleiten
			$row->typ = __('' . $row->typ . '','teachpress');
			$tparray[$tpz][0] = '' . $row->jahr . '' ;
			$tparray[$tpz][1] = '<div class="tp_publication">
								<p class="tp_pub_autor">' . $row->autor . '</p>
								<p class="tp_pub_titel">' . $row->name . ' <span class="tp_pub_typ">(' . $row->typ . ')</span></p>
								<p class="tp_pub_zusatz">' . $row->verlag . '' . $row->isbn . ' <span class="tp_pub_tags">Tags: ' . $tag_string . '</span></p>
						</div>';
			$tpz++;
			$tag_string = '';
		}
		if ($tpz != 0) {
			if ($yr == 0) {
				$jahre = "SELECT DISTINCT jahr FROM " . $teachpress_pub . " ORDER BY jahr DESC";
				$row = tp_results($jahre);
				foreach($row as $row) {
					for ($i=0; $i<= $tpz; $i++) {
						if ($tparray[$i][0] == $row->jahr) {
							$zwischen = $zwischen . $tparray[$i][1];
						}
						else {
							if ($zwischen != '') {
								$pubs = $pubs . '<h3 class="tp_h3">' . $row->jahr . '</h3>' . $zwischen;
								$zwischen = '';
							}
						}
					}
				}
			}
			else {
				for ($i=0; $i<$tpz; $i++) {
						if ($tparray[$i][0] == $yr) {
							$pubs = $pubs . $tparray[$i][1];
						}
				}
				if ($pubs != '') {
					$pubs = '<h3 class="tp_h3">' . $row->jahr . '</h3>' . $pubs;
				}
			}
			// Alles zusammensetzen
			$asg2 = '<div class="teachpress_list">' . $pubs . '</div>';
			$asg = $asg1 . $asg2;
		}
		else {
			$asg2 = '<div class="teachpress_list"><p class="teachpress_mistake">' . __('Sorry, no publications matched your criteria.','teachpress') . '</p></div>';
			$asg = $asg1 . $asg2;
		}
	// Rueckgabe des gesamten Strings
	return "$asg";
}

/* Publikationen als Liste ohne Tag-Cloud
 * @param $attr (Array) mit 'id' und 'tag' (beides integer)
 * Return: $asg (String)
 * used in WordPress Shortcode-API
*/
function tplist_shortcode($atts){
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_beziehung;
	global $teachpress_user;
	// Attribute aus array extrahieren
	extract(shortcode_atts(array(
		'user' => 0,
		'tag' => 0,
		'year' => 0,
		'headline' => 1,
	), $atts));
	$userid = $user;
	$tag_id = $tag;
	$yr = $year;
	// Beide werden auf Integer gesetzt
	settype($userid, 'integer');
	settype($tag_id, 'integer');
	settype($yr, 'integer');
	settype($headline, 'integer');
	$select = "SELECT DISTINCT p.name, p.typ, p.autor, p.verlag, p.jahr, p.isbn , p.url, p.is_isbn 
			FROM " . $teachpress_beziehung ." b "; 
	// Publikationen aller Autoren
	if ($userid == 0) {
		// Publikationen aller Autoren zu einem bestimmten Tag
		if ($tag_id != 0) {
			$row = "" . $select . "
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			WHERE t.tag_id = '$tag_id'
			ORDER BY p.sort DESC";
		}	
		// Alle Publikationen aller Autoren
		else {
			$row = "SELECT * FROM " . $teachpress_pub. " ORDER BY " . $teachpress_pub . ".sort DESC, " . $teachpress_pub . ".typ";
		}
	}
	else {
		if ($tag_id != 0) {
			$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' AND t.tag_id = '$tag_id'
			ORDER BY p.sort DESC";
		}
		else {
			$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = b.pub_id
			WHERE u.user = '$userid'
			ORDER BY p.sort DESC";
		}	
	}	
	$tpz = 0;
	$row = tp_results($row);
	foreach ($row as $row) {
		// Falls url eingegeben wurde, wird ein Link draus
		if ($row->url !='') {
			$row->name = '<a href="' . $row->url . '">' . $row->name . '</a>';
		}
		// Falls isbn eingegeben wurde, wird formatiert
		if ($row->isbn != '') {
			// Test ob ISBN oder ISSN
			if ($row->is_isbn == '0') { 
				$row->isbn = '; ISSN: ' . $row->isbn . '';
			}
			else {
				$row->isbn = '; ISBN: ' . $row->isbn . '';
			}
		}
		// Ergebnis in $tparray laden
		$row->typ = __('' . $row->typ . '','teachpress');
		$tparray[$tpz][0] = '' . $row->jahr . '' ;
		$tparray[$tpz][1] = '<div class="tp_publication">
								<p class="tp_pub_autor">' . $row->autor . '</p>
								<p class="tp_pub_titel">' . $row->name . ' <span class="tp_pub_typ">(' . $row->typ . ')</span></p>
								<p class="tp_pub_zusatz">' . $row->verlag . '' . $row->isbn . '</p>
						</div>';
		$tpz++;			
	}
	// Strings nach Publikationstyp zusammensetzen
	if ($headline == 1) {
		if ($yr == 0) {
			$jahre = "SELECT DISTINCT jahr FROM " . $teachpress_pub . " ORDER BY jahr DESC";
			$row = tp_results($jahre);
			foreach($row as $row) {
				for ($i=0; $i<= $tpz; $i++) {
					if ($tparray[$i][0] == $row->jahr) {
						$zwischen = $zwischen . $tparray[$i][1];
					}
					else {
						if ($zwischen != '') {
							$pubs = $pubs . '<h3 class="tp_h3">' . $row->jahr . '</h3>' . $zwischen;
							$zwischen = '';
						}
					}
				}
			}
		}
		else {
			for ($i=0; $i<$tpz; $i++) {
					if ($tparray[$i][0] == $yr) {
						$pubs = $pubs . $tparray[$i][1];
					}
			}
			if ($pubs != '') {
				$pubs = '<h3 class="tp_h3">' . $yr . '</h3>' . $pubs;
			}
		}	
	}	
	else {
		for($i=0; $i<$tpz; $i++) {
			$pubs = $pubs . $tparray[$i][1];
		}
	}
	// Alles zusammensetzen	
	$asg = '<div class="teachpress_list">' . $pubs . '</div>';
	
	return $asg;
}

/**
 * teachPress Books widget class
 */
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
		echo '<p style="text-align:center"><a href="' . $rel_page . '" title="' . $pub_title . '"><img class="tp_image" name="' . $pub_title . '" src="' . $image_url . '" alt="' . $pub_title . '" title="' . $pub_title . '" /></a></p>';
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
		$sql= "SELECT pub_id, name FROM " . $teachpress_pub . " WHERE typ = 'Buch' ORDER BY sort DESC";
		$row= tp_results($sql);
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

/* Fuegt scripts und styles zum WP-Admin-Header
 * used in: Wordpress-Admin-Header
*/ 
function teachpress_admin_head() {
	// Lade scripts nur, wenn es eine teachpress Seite ist
	if ( eregi('teachpress', $_GET[page]) || eregi('publications', $_GET[page]) ) {
		wp_enqueue_script('teachpress-calendar-js', WP_PLUGIN_URL . '/teachpress/jscalendar/calendar.js');
		wp_enqueue_script('teachpress-calendarsetup-js', WP_PLUGIN_URL . '/teachpress/jscalendar/calendar-setup.js');
		wp_enqueue_script('teachpress-standard', WP_PLUGIN_URL . '/teachpress/js/standard.js');
		$lang = __('de','teachpress');
		if ($lang == 'de') {
			wp_enqueue_script('teachpress-calendarlang-js', WP_PLUGIN_URL . '/teachpress/jscalendar/lang/calendar-de.js');
		}
		else {
			wp_enqueue_script('teachpress-calendarlang-js', WP_PLUGIN_URL . '/teachpress/jscalendar/lang/calendar-en.js');
		}
		wp_enqueue_style('teachpress.css', WP_PLUGIN_URL . '/teachpress/teachpress.css');
		wp_enqueue_style('teachpress-calendar-css', WP_PLUGIN_URL . '/teachpress/jscalendar/skins/aqua/theme.css');
		add_thickbox();
	}
}
/* Fuegt einen String zum WP-Admin-Header
 * used in: Wordpress-Admin-Header
*/
function teachpress_js_admin_head() {
    echo '<link media="print" type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/print.css" rel="stylesheet" />'; 
}

/* Fügt eine CSS zum WP-Header hinzu
 * used in: Wordpress-Header
*/ 
function teachpress_js_wp_header() {
	echo '<script type="text/javascript" src="' . WP_PLUGIN_URL . '/teachpress/js/standard.js"></script>';
	echo '<link type="text/css" href="' . WP_PLUGIN_URL . '/teachpress/teachpress_front.css" rel="stylesheet" />';
}
/* 
 * Installer
*/
function teachpress_install() {
	global $wpdb;
	$teachpress_ver = $wpdb->prefix . 'teachpress_ver'; //Events
	$teachpress_stud = $wpdb->prefix . 'teachpress_stud'; //Students
	$teachpress_einstellungen = $wpdb->prefix . 'teachpress_einstellungen'; //Settings
	$teachpress_kursbelegung = $wpdb->prefix . 'teachpress_kursbelegung'; //Enrollments
	$teachpress_log = $wpdb->prefix . 'teachpress_log'; // Security-Log
	$teachpress_pub = $wpdb->prefix . 'teachpress_pub'; //Publications
	$teachpress_tags = $wpdb->prefix . 'teachpress_tags'; //Tags
	$teachpress_beziehung = $wpdb->prefix . 'teachpress_beziehung'; //Relationsship Tags - Publications
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
	
	// teachpress_ver
	$table_name = $teachpress_ver;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_ver. " (
						 veranstaltungs_id INT UNSIGNED AUTO_INCREMENT ,
						 name VARCHAR(100) ,
						 vtyp VARCHAR (100) ,
						 raum VARCHAR(100) ,
						 dozent VARCHAR (100) ,
						 termin VARCHAR(60) ,
						 plaetze INT(4) ,
						 fplaetze INT(4) ,
						 startein DATE ,
						 endein DATE ,
						 semester VARCHAR(10) ,
						 bemerkungen VARCHAR(200) ,
						 rel_page INT ,
						 parent INT(4) ,
						 sichtbar INT(1) ,
						 warteliste INT(1),
						 PRIMARY KEY (veranstaltungs_id)
					   ) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');			
		dbDelta($sql);
		
	 }
	 // teachpress_stud
	$table_name = $teachpress_stud;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_stud . " (
						 wp_id INT UNSIGNED ,
						 vorname VARCHAR(100) ,
						 nachname VARCHAR(100) ,
						 studiengang VARCHAR(100) ,
						 urzkurz VARCHAR (10) ,
						 gebdat DATE ,
						 email VARCHAR(50) ,
						 fachsemester INT(2) ,
						 matrikel INT (10),
						 PRIMARY KEY (wp_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
	 // teachpress_kursbelegung
	$table_name = $teachpress_kursbelegung;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_kursbelegung . " (
						 belegungs_id INT UNSIGNED AUTO_INCREMENT ,
						 veranstaltungs_id INT ,
						 wp_id INT ,
						 warteliste INT(1) ,
						 datum DATE ,
						 FOREIGN KEY (veranstaltungs_id) REFERENCES " . $teachpress_ver. "(veranstaltungs_id) ,
						 FOREIGN KEY (wp_id) REFERENCES " . $teachpress_stud . "(wp_id) ,
						 PRIMARY KEY (belegungs_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
	 // teachpress_einstellungen
	$table_name = $teachpress_einstellungen;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_einstellungen . " (
						einstellungs_id INT UNSIGNED AUTO_INCREMENT ,
						variable VARCHAR (30) ,
						wert VARCHAR (100) ,
						category VARCHAR (100) ,
						PRIMARY KEY (einstellungs_id)
						) $charset_collate;";
		dbDelta($sql);
		// Default-Einstellungen			
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('sem', 'WS09/10', 'system')");
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('db-version', '0.80.1', 'system')");
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('permalink', '1', 'system')");
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('sign_out', '0', 'system')");
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('userrole', 'administrator', 'system')");		
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('WS09/10', 'WS09/10', 'semester')");									
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('Bachelor Wirtschaftsinformatik', 'Bachelor Wirtschaftsinformatik', 'studiengang')");	
		tp_query("INSERT INTO " . $teachpress_einstellungen . " (variable, wert, category) VALUES ('Vorlesung', 'Vorlesung', 'veranstaltungstyp')");
	 }
	 // teachpress_log
	$table_name = $teachpress_log;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_log . " (
						log_id INT UNSIGNED AUTO_INCREMENT ,
						id INT ,
						user INT ,
						beschreibung VARCHAR (200) ,
						datum DATE ,
						PRIMARY KEY (log_id)
						) $charset_collate;";
		dbDelta($sql);
	 }
	 //teachpress_pub
	$table_name = $teachpress_pub;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_pub. " (
						 pub_id INT UNSIGNED AUTO_INCREMENT ,
						 name VARCHAR(500) ,
						 typ VARCHAR (50) ,
						 autor VARCHAR(300) ,
						 verlag VARCHAR (500) ,
						 jahr INT(4) ,
						 isbn VARCHAR(40) ,
						 url VARCHAR(200) ,
						 sort DATE ,
						 comment TEXT ,
						 image_url VARCHAR(200) ,
						 rel_page VARCHAR(200) ,
						 is_isbn INT(1) ,
						 PRIMARY KEY (pub_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
	 //teachpress_tags
	$table_name = $teachpress_tags;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_tags . " (
						 tag_id INT UNSIGNED AUTO_INCREMENT ,
						 name VARCHAR(300) ,
						 PRIMARY KEY (tag_id)
					    ) $charset_collate;";
		dbDelta($sql);
	 }
	 //teachpress_beziehung
	$table_name = $teachpress_beziehung;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_beziehung . " (
						 belegungs_id INT UNSIGNED AUTO_INCREMENT ,
						 pub_id INT ,
						 tag_id INT ,
						 FOREIGN KEY (pub_id) REFERENCES " . $teachpress_pub. "(pub_id) ,
						 FOREIGN KEY (tag_id) REFERENCES " . $teachpress_tags . "(tag_id) ,
						 PRIMARY KEY (belegungs_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
	 //teachpress_user
	$table_name = $teachpress_user;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $teachpress_user . " (
						 bookmark_id INT UNSIGNED AUTO_INCREMENT ,
						 pub_id INT ,
						 user INT ,
						 PRIMARY KEY (bookmark_id)
					   ) $charset_collate;";
		dbDelta($sql);
	 }
}

//lade sprachdateien
function teachpress_language_support() {
	load_plugin_textdomain('teachpress', false, 'teachpress');
}
// Registrieren der WordPress-Hooks
register_activation_hook( __FILE__, 'teachpress_install');
add_action('widgets_init', create_function('', 'return register_widget("teachpress_books_widget");'));
add_action('init', 'teachpress_language_support');
add_action('admin_menu', 'teachpress_add_menu');
add_action('admin_menu', 'teachpress_add_menu2');
add_action('admin_menu', 'teachpress_add_menu_settings');
add_action('the_content', 'teachpress_anzeigen');
add_action('the_content', 'teachpress_lvs_fronted');
add_action('admin_head', 'teachpress_js_admin_head');
add_action('wp_head', 'teachpress_js_wp_header');
add_action('admin_init','teachpress_admin_head');
add_shortcode('tpdate', 'tpdate_shortcode');
add_shortcode('tpcloud', 'tpcloud_shortcode');
add_shortcode('tplist', 'tplist_shortcode');
?>