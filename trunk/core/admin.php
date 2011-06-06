<?php
/****************************************/
/* teachPress Admin Interface functions */
/****************************************/ 

/*********************/
/* General functions */
/*********************/

/* teachPress Admin Page Menu
 * @access	public
 * @param 	$number_entries (Integer) 	-> Number of all available entries
 * @param 	$entries_per_page (Integer) -> Number of entries per page
 * @param 	$current_page (Integer)		-> current displayed page
 * @param	$entry_limit (Integer) 		-> SQL entry limit
 * @param 	$page_link (String)
 * @param	$link_atrributes (String)
 * @param 	$type - top or bottom, default: top
*/
function tp_admin_page_menu ($number_entries, $entries_per_page, $current_page, $entry_limit, $page_link = '', $link_attributes = '', $type = 'top') {
	// if number of entries > number of entries per page
	if ($number_entries > $entries_per_page) {
		$num_pages = floor (($number_entries / $entries_per_page));
		$mod = $number_entries % $entries_per_page;
		if ($mod != 0) {
			$num_pages = $num_pages + 1;
		}
		
		// first page / previous page
		if ($entry_limit != 0) {
			$back_links = '<a href="' . $page_link . '&amp;limit=1&amp;' . $link_attributes . '" title="' . __('first page','teachpress') . '" class="page-numbers">&laquo;</a> <a href="' . $page_link . '&amp;limit=' . ($current_page - 1) . '&amp;' . $link_attributes . '" title="' . __('previous page','teachpress') . '" class="page-numbers">&lsaquo;</a> ';
		}
		else {
			$back_links = '<a class="first-page disabled">&laquo;</a> <a class="prev-page disabled">&lsaquo;</a> ';
		}
		$page_input = ' <input name="limit" type="text" size="2" value="' .  $current_page . '" style="text-align:center;" /> ' . __('of','teachpress') . ' ' . $num_pages . ' ';
		
		// next page/ last page
		if ( ( $entry_limit + $entries_per_page ) <= ($number_entries)) { 
			$next_links = '<a href="' . $page_link . '&amp;limit=' . ($current_page + 1) . '&amp;' . $link_attributes . '" title="' . __('next page','teachpress') . '" class="page-numbers">&rsaquo;</a> <a href="' . $page_link . '&amp;limit=' . $num_pages . '&amp;' . $link_attributes . '" title="' . __('last page','teachpress') . '" class="page-numbers">&raquo;</a> ';
		}
		else {
			$next_links = '<a class="next-page disabled">&rsaquo;</a> <a class="last-page disabled">&raquo;</a> ';
		}
		
		// for displaying number of entries
		if ($entry_limit + $entries_per_page > $number_entries) {
			$anz2 = $number_entries;
		}
		else {
			$anz2 = $entry_limit + $entries_per_page;
		}
		
		// return
		if ($type == 'top') {
			return '<div class="tablenav-pages"><span class="displaying-num">' . ($entry_limit + 1) . ' - ' . $anz2 . ' ' . __('of','teachpress') . ' ' . $number_entries . ' ' . __('entries','teachpress') . '</span> ' . $back_links . '' . $page_input . '' . $next_links . '</div>';
		}
		else {
			return '<div class="tablenav"><div class="tablenav-pages"><span class="displaying-num">' . ($entry_limit + 1) . ' - ' . $anz2 . ' ' . __('of','teachpress') . ' ' . $number_entries . ' ' . __('entries','teachpress') . '</span> ' . $back_links . ' ' . $current_page . ' ' . __('of','teachpress') . ' ' . $num_pages . ' ' . $next_links . '</div></div>';
		}	
	}
}	

/* Get WordPress pages
 * adapted from Flexi Pages Widget Plugin
 * @param $sort_column
 * @param sort_order
 * @param $selected
 * @param $post_type
 * @param $parent
 * @param $level
*/ 
function teachpress_wp_pages($sort_column = "menu_order", $sort_order = "ASC", $selected, $post_type, $parent = 0, $level = 0 ) {
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
	$items = $wpdb->get_results( "SELECT `ID`, `post_parent`, `post_title` FROM $wpdb->posts WHERE `post_parent` = $parent AND `post_type` = '$post_type' AND `post_status` = 'publish' ORDER BY {$sort_column} {$sort_order}" );
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
			teachpress_wp_pages( $sort_column, $sort_order, $selected, $post_type, $item->ID,  $level +1 );
		}
	} else {
		return false;
	}
}

/* Gives a single table row for show_courses.php
 * @param 	$couse (ARRAY_A) --> course data
 * @param 	$checkbox (ARRAY)
 * @param 	$static (ARRAY_A):
 				$static['bulk'] --> copy or delete
				$static['sem'] --> semester
				$static['search'] --> input from search field
 * @param 	$parent_course_name
 * @param 	$type (STRING) --> parent or child
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
		$a4 = '<td>' . __('normal','teachpress') . '</td>';
	}
	elseif ( $course['visible'] == 2 ) {
		$a4 = '<td>' . __('extend','teachpress') . '</td>';
	}
	else {
		$a4 = '<td>' . __('invisible','teachpress') . '</td>';
	}
	$a5 = '</tr>';
	// Return
	$return = $a1 . $a2 . $a3 . $a4 . $a5;
	return $return;
}

/***********/
/* Courses */
/***********/

/* Add a new course
 * used in add_course.php
 * @access	public
 * @param 	$data (ARRAY_A)
 * @return 	$insert_ID (INT) - id of the new course
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
 * used in: showlvs.php
 * @param	$checkbox (Array)
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
	global $teachpress_signup;
	$course_ID = tp_sec_var($course_ID, 'integer');
	$old_places = tp_get_course_data ($course_ID, 'places');
	
	// handle the number of free places
	if ( $data['places'] == $old_places ) {
		$fplaces = $data['fplaces'];
	}
	elseif ( $data['places'] > $old_places ) {
		$new_free_places = $data['places'] - $old_places;
		// subscribe students from the waiting list automatically
		$sql = "SELECT s.con_id, s.waitinglist, s.date
				FROM " . $teachpress_signup . " s 
				INNER JOIN " . $teachpress_courses . " c ON c.course_id=s.course_id
				WHERE c.course_id = '$course_ID' AND s.waitinglist = '1' ORDER BY s.date ASC";
		$waitinglist = $wpdb->get_results($sql, ARRAY_A);
		$count_waitinglist = count($waitinglist);
		if ( $count_waitinglist > 0 ) {
			foreach ( $waitinglist as $waitinglist ) {
				if ( $new_free_places > 0 ) {
					$wpdb->update( $teachpress_signup, array ( 'waitinglist' => 0 ), array ( 'con_id' => $waitinglist["con_id"] ), array ( '%d' ), array ( '%d' ) );
				}
				else {
					break;
				}
				$new_free_places = $new_free_places -1;
			}
		}
		// END subscribe students from the waiting list automatically
		$fplaces = $new_free_places + $data['fplaces'];
	}
	else {
		$fplaces = $data['fplaces'] - ( $old_places - $data['places'] );
		// no negative free places
		if ( $fplaces < 0 ) { 
			$fplaces = 0; 
		}
	}
	// END handle the number of free places
	
	$data['start'] = $data['start'] . ' ' . $data['start_hour'] . ':' . $data['start_minute'] . ':00';
	$data['end'] = $data['end'] . ' ' . $data['end_hour'] . ':' . $data['end_minute'] . ':00';
	$wpdb->update( $teachpress_courses, array( 'name' => $data['name'], 'type' => $data['type'], 'room' => $data['room'], 'lecturer' => $data['lecturer'], 'date' => $data['date'], 'places' => $data['places'], 'fplaces' => $fplaces, 'start' => $data['start'], 'end' => $data['end'], 'semester' => $data['semester'], 'comment' => $data['comment'], 'rel_page' => $data['rel_page'], 'parent' => $data['parent'], 'visible' => $data['visible'], 'waitinglist' => $data['waitinglist'], 'image_url' => $data['image_url'], 'strict_signup' => $data['strict_signup'] ), array( 'course_id' => $course_ID ), array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%d' ), array( '%d' ) );
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
function tp_get_course_data ($id, $col, $mode = 'single') {
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
		$parent = tp_get_course_data ($row1->parent, 'name');
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
			$check = tp_get_course_data ($row1->parent, 'strict_signup');
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
		$row1 = "SELECT `course_id` FROM " . $teachpress_signup . " WHERE `con_id` = '$checkbox[$i]'";
		$row1 = $wpdb->get_results($row1);
		foreach ($row1 as $row1) {
			// check if there are users in teh waiting list
			$abfrage = "SELECT `con_id` FROM " . $teachpress_signup . " WHERE `course_id` = '$row1->course_id' AND `waitinglist` = '1' ORDER BY `con_id`";
			$test= $wpdb->query($abfrage);
			// if is true
			if ($test != 0) {
				$zahl = 0;
				$wpdb->get_results($abfrage);
				foreach ($row as $row) {
					if ($zahl < 1) {
						$aendern = "UPDATE " . $teachpress_signup . " SET `waitinglist` = '0' WHERE `con_id` = '$row->con_id'";
						$wpdb->query( $aendern );
						$zahl++;
					}
				}
			}
			// if not enhance the number of free places
			else {
				$fplaces= "SELECT `fplaces` FROM " . $teachpress_courses . " WHERE `course_id` = '$row1->course_id'";
				$fplaces = $wpdb->get_var($fplaces);
				$neu = $fplaces + 1;
				$aendern = "UPDATE " . $teachpress_courses . " SET `fplaces` = '$neu' WHERE `course_id` = '$row1->course_id'";
				$wpdb->query( $aendern );
			}	
		}
   	$wpdb->query( "DELETE FROM " . $teachpress_signup . " WHERE `con_id` = '$checkbox[$i]'" );
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
		$wpdb->update( $teachpress_signup, array ( 'waitinglist' => 0 ), array ( 'con_id' => $checkbox[$i] ), array ( '%d'), array ( '%d' ) );
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
 * @param $options (ARRAY)
 * used in: settings.php
*/
function tp_change_settings($options) {
	global $wpdb;
	global $teachpress_settings;
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['semester'] . "' WHERE `variable` = 'sem'";
	$wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['permalink'] . "' WHERE `variable` = 'permalink'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['rel_page_courses'] . "' WHERE `variable` = 'rel_page_courses'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['rel_page_publications'] . "' WHERE `variable` = 'rel_page_publications'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['stylesheet'] . "' WHERE `variable` = 'stylesheet'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['sign_out'] . "' WHERE `variable` = 'sign_out'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['matriculation_number'] . "' WHERE `variable` = 'regnum'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['course_of_studies'] . "' WHERE `variable` = 'studies'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['semesternumber'] . "' WHERE `variable` = 'termnumber'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['birthday'] . "' WHERE `variable` = 'birthday'";
    $wpdb->query( $eintragen );
	$eintragen = "UPDATE " . $teachpress_settings . " SET `value` = '" . $options['login'] . "' WHERE `variable` = 'login'";
    $wpdb->query( $eintragen );
	tp_update_userrole($options['userrole']);
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
?>