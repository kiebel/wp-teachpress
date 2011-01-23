<?php
/* Course overview
 * from editlvs.php (GET), showlvs.php (GET):
 * @param $sem (String) 
 * @param $search (String)
*/
function teachpress_show_courses_page() {

	$course_ID = tp_sec_var($_GET[lvs_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$sem = tp_sec_var($_GET[sem]);
	
	global $wpdb;
	global $teachpress_settings; 
	global $teachpress_courses;
	
	// test if teachpress database is up to date
	$test = tp_get_option('db-version');
	$version = get_tp_version();
	// if is the actual one
	if ($test != $version) {
		$message = __('An database update is necessary.','teachpress') . ' <a href="options-general.php?page=teachpress/settings.php&amp;up=1">' . __('Update','teachpress') . '</a>';
		tp_get_message($message, '');
	} 

	// Event Handler
	if ($_GET[action] == 'edit') {
		tp_add_course_page();
	}
	elseif ($_GET[action] == 'show') {
		tp_show_single_course_page();
	}
	elseif ($_GET[action] == 'list') {
		tp_lists_page();
	}
	else {
	
	// Formular-Einträge aus dem Post Array holen
	$checkbox = $_GET[checkbox];
	$bulk = $_GET[bulk];
	$copysem = tp_sec_var($_GET[copysem]);
	$search = tp_sec_var($_GET[search]);
	// Wenn Semester vorher von User ausgewaehlt wurde
	if (isset($_GET[sem])) {
		$sem = tp_sec_var($_GET[sem]);
	}
	else {
		$sem = tp_get_option('sem');
	}
	?> 
	
	<div class="wrap">
	  <h2><?php _e('Courses','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
		<div id="hilfe_anzeigen">
			<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
			<p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
            <p class="hilfe_text"><?php _e('You can use courses in a page or article with the following shortcodes:','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('For course informations','teachpress'); ?>: <strong><?php _e('[tpdate id="x"]','teachpress'); ?></strong> <?php _e('x = Course-ID','teachpress'); ?></p>
            <p class="hilfe_text"><?php _e('For the course list','teachpress'); ?>: <strong><?php _e('[tp_courselist image="x" image_size="y"]','teachpress'); ?></strong> <?php _e('x = image position (left, right, bottom or none), y = size of the images (for example 50)','teachpress'); ?></p>
            <p class="hilfe_text"><?php _e('For the enrollment system','teachpress'); ?>: <strong><?php _e('[tpenrollments]','teachpress'); ?></strong></p>
			<p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
		</div>
	  <form id="showlvs" name="showlvs" method="get" action="<?php echo $PHP_SELF ?>">
	  <input name="page" type="hidden" value="teachpress/teachpress.php" />
		<?php 	
		// Prüfen welche Checkbox genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
		// Veranstaltungen loeschen
		if ( $bulk == "delete" ) {
			echo '<div class="teachpress_message">
			<p class="hilfe_headline">' . __('Are you sure to delete the selected courses?','teachpress') . '</p>
			<p><input name="delete_ok" type="submit" class="teachpress_button" value="' . __('delete','teachpress') . '"/>
			<a href="admin.php?page=teachpress/teachpress.php&sem=' . $sem . '&search=' . $search . '"> ' . __('cancel','teachpress') . '</a></p>
			</div>';
		}
		// Veranstaltung loeschen Teil 2
		if ( isset($_GET[delete_ok]) ) {
			tp_delete_course($checkbox);
			$message = __('Course(s) deleted','teachpress');
			tp_get_message($message);
		}
		// Veranstaltungen kopieren
		if ( $bulk == "copy" ) { ?>
			<div class="teachpress_message">
			<p class="hilfe_headline"><?php _e('Copy courses','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('Select the term, in which you will copy the selected courses.','teachpress'); ?></p>
			<p class="hilfe_text">
			<select name="copysem" id="copysem">
				<?php    
				$term = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id DESC";
				$term = $wpdb->get_results($term);
				foreach ($term as $term) { 
					if ($term->value == $sem) {
						$current = 'selected="selected"' ;
					}
					else {
						$current = '' ;
					} 
					echo '<option value="' . $term->value . '" ' . $current . '>' . stripslashes($term->value) . '</option>';
				} ?> 
			</select>
			<input name="copy_ok" type="submit" class="teachpress_button" value="<?php _e('copy','teachpress'); ?>"/>
			<a href="<?php echo 'admin.php?page=teachpress/teachpress.php&sem=' . $sem . '&search=' . $search . ''; ?>"> <?php _e('cancel','teachpress'); ?></a>
			</p>
			</div>
		<?php
		}
		// Kopiervorgang Teil 2
		if ( isset($_GET[copy_ok]) ) {
			tp_copy_course($checkbox, $copysem);
			$message = __('Copying successful','teachpress');
			tp_get_message($message);
		}
		?>
		<div id="searchbox" style="float:right; padding-bottom:10px;"> 
			<?php if ($search != "") { ?>
			<a href="admin.php?page=teachpress/teachpress.php" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a>
			<?php } ?>
			<input type="text" name="search" id="pub_search_field" value="<?php echo stripslashes($search); ?>"/></td>
			<input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/>
		</div>
		<div id="filterbox" style="padding-bottom:10px;">
			<select name="sem" id="sem">
				<option value="alle"><?php _e('All terms','teachpress'); ?></option>
				<?php    
				$row = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id DESC";
				$row = $wpdb->get_results($row);
				foreach ($row as $row) { 
					if ($row->value == $sem) {
						$current = 'selected="selected"' ;
					}
					else {
						$current = '' ;
					} 
					echo '<option value="' . $row->value . '" ' . $current . '>' . stripslashes($row->value) . '</option>';
				} ?> 
			</select>
			<input type="submit" name="start" value="<?php _e('show','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/>
			<select name="bulk" id="bulk">
				<option>- <?php _e('Bulk actions','teachpress'); ?> -</option>
				<option value="copy"><?php _e('copy','teachpress'); ?></option>
				<option value="delete"><?php _e('delete','teachpress'); ?></option>
		  </select>
		  <input type="submit" name="teachpress_submit" value="<?php _e('ok','teachpress'); ?>" id="teachpress_submit2" class="teachpress_button"/>
	   </div>
	<table cellpadding="5" cellspacing="0" border="1" class="widefat">
		<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php _e('Name','teachpress'); ?></th>
			<th><?php _e('ID','teachpress'); ?></th>
			<th><?php _e('Type','teachpress'); ?></th> 
			<th><?php _e('Room','teachpress'); ?></th>
			<th><?php _e('Lecturer','teachpress'); ?></th>
			<th><?php _e('Date','teachpress'); ?></th>
			<th colspan="2" align="center" style="text-align:center;"><?php _e('Places','teachpress'); ?></th>
			<th colspan="2" align="center" style="text-align:center;"><?php _e('Enrollments','teachpress'); ?></th>
			<th><?php _e('Term','teachpress'); ?></th>
			<th><?php _e('Visibility','teachpress'); ?></th>
		</tr>
		</thead>
		<tbody>
	<?php
		// Abfragen je nachdem was im Filter gewaehlt wurde
		if ($search == "") {
			if ($sem == 'alle') {
				$abfrage = "SELECT * FROM " . $teachpress_courses . " ORDER BY name";
			}
			else {
				$abfrage = "SELECT * FROM " . $teachpress_courses . " WHERE semester = '$sem' ORDER BY name, course_id";
			}	
		}
		// Falls Eingabe in Suchfeld
		else {
			$abfrage = "SELECT course_id, name, type, lecturer, date, room, places, fplaces, start, end, semester, parent, visible, parent_name 
			FROM (SELECT t.course_id AS course_id, t.name AS name, t.type AS type, t.lecturer AS lecturer, t.date AS date, t.room As room, t.places AS places, t.fplaces AS fplaces, t.start AS start, t.end As end, t.semester AS semester, t.parent As parent, t.visible AS visible, p.name AS parent_name FROM " . $teachpress_courses . " t LEFT JOIN " . $teachpress_courses . " p ON t.parent = p.course_id ) AS temp 
			WHERE name like '%$search%' OR parent_name like '%$search%' OR lecturer like '%$search%' OR date like '%$search%' OR room like '%$search%' OR course_id = '$search' 
			ORDER BY semester DESC, name";
		}
		$test = $wpdb->query($abfrage);	
		// Falls es keine Treffer gibt
		if ($test == 0) { 
			echo '<tr><td colspan="13"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
		}
		// Zusammenstellung Ergebnisse
		else {
			$z = 0;
			$ergebnis = $wpdb->get_results($abfrage);
			foreach ($ergebnis as $row){
				$date1 = tp_datumsplit($row->start);
				$date2 = tp_datumsplit($row->end);
				$courses[$z]['course_id'] = $row->course_id;
				$courses[$z]['name'] = stripslashes($row->name);
				$courses[$z]['type'] = stripslashes($row->type);
				$courses[$z]['room'] = stripslashes($row->room);
				$courses[$z]['lecturer'] = stripslashes($row->lecturer);
				$courses[$z]['date'] = stripslashes($row->date);
				$courses[$z]['places'] = $row->places;
				$courses[$z]['fplaces'] = $row->fplaces;
				$courses[$z]['start'] = '' . $date1[0][0] . '-' . $date1[0][1] . '-' . $date1[0][2] . '';
				$courses[$z]['end'] = '' . $date2[0][0] . '-' . $date2[0][1] . '-' . $date2[0][2] . '';
				$courses[$z]['semester'] = stripslashes($row->semester);
				$courses[$z]['parent'] = $row->parent;
				$courses[$z]['visible'] = $row->visible;
				$z++;
			}
			// display courses
			for ($i=0; $i<$z; $i++) {
				if ($search == "") {
					if ($courses[$i]['parent'] == 0) {
						echo '<tr>';
						echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $courses[$i]['course_id'] . '"';
						if ( $bulk == "copy" || $bulk == "delete") { 
							for( $k = 0; $k < count( $checkbox ); $k++ ) { 
								if ( $courses[$i]['course_id'] == $checkbox[$k] ) { echo 'checked="checked"';} 
							} 
						}
						echo '/></th>';
						echo '<td><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $courses[$i]['course_id'] . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=show" class="teachpress_link" title="' . __('Click to show','teachpress') . '">' . $courses[$i]['name'] . '</a></td>';
						echo '<td>' . $courses[$i]['course_id'] . '</td>';
						echo '<td>' . $courses[$i]['type'] . '</td>';
						echo '<td>' . $courses[$i]['room'] . '</td>';
						echo '<td>' . $courses[$i]['lecturer'] . '</td>';
						echo '<td>' . $courses[$i]['date'] . '</td>';
						echo '<td>' . $courses[$i]['places'] . '</td>';
						echo '<td';
						if ($courses[$i]['places'] > 0 && $courses[$i]['fplaces'] == 0) {
							echo ' style="color:#ff6600; font-weight:bold;"'; 
						}
						echo '>' . $courses[$i]['fplaces'] . '</td>';
						if ($courses[$i]['start'] != '0000-00-00' && $courses[$i]['end'] != '0000-00-00') {
							echo '<td>' . $courses[$i]['start'] . '</td>';
							echo '<td>' . $courses[$i]['end'] . '</td>';
						} 
						else {
							echo '<td colspan="2" style="text-align:center;">' . __('none','teachpress') . '</td>';
						}
						echo '<td>' . $courses[$i]['semester'] . '</td>';
						if ($courses[$i]['visible'] == 1) {
							echo '<td>' . __('yes','teachpress') . '</td>';
						} 
						else {
							echo '<td>' . __('no','teachpress') . '</td>';
						}
						echo '</tr>';
						// Search Childs
						for ($j=0; $j<$z; $j++) {
							if ($courses[$i]['course_id'] == $courses[$j]['parent']) {
								echo '<tr id="teachpress_table">';
								echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $courses[$j]['course_id'] . '"';
								if ( $bulk == "copy" || $bulk == "delete") { 
									for( $k = 0; $k < count( $checkbox ); $k++ ) { 
										if ( $courses[$j]['course_id'] == $checkbox[$k] ) { echo 'checked="checked"';} 
									} 
								}
								echo '/></th>';
								echo '<td><strong>&mdash;</strong> <a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $courses[$j]['course_id'] . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=show" class="teachpress_link" title="' . __('Click to show','teachpress') . '">';
								if ($courses[$i]['name'] == $courses[$j]['name']) {
									echo $courses[$i]['name']; 
								} else {
									echo $courses[$i]['name'] . ' ' . $courses[$j]['name'];
								}
								echo '</a></td>';
								echo '<td>' . $courses[$j]['course_id'] . '</td>';
								echo '<td>' . $courses[$j]['type'] . '</td>';
								echo '<td>' . $courses[$j]['room'] . '</td>';
								echo '<td>' . $courses[$j]['lecturer'] . '</td>';
								echo '<td>' . $courses[$j]['date'] . '</td>';
								echo '<td>' . $courses[$j]['places'] . '</td>';
								echo '<td';
								if ($courses[$j]['places'] > 0 && $courses[$j]['fplaces'] == 0) {
									echo ' style="color:#ff6600; font-weight:bold;"'; 
								}
								echo '>' . $courses[$j]['fplaces'] . '</td>';
								if ($courses[$j]['start'] != '0000-00-00' && $courses[$j]['end'] != '0000-00-00') {
									echo '<td>' . $courses[$j]['start'] . '</td>';
									echo '<td>' . $courses[$j]['end'] . '</td>';
								} 
								else {
									echo '<td colspan="2" style="text-align:center;">' . __('none','teachpress') . '</td>';
								}
								echo '<td>' . $courses[$j]['semester'] . '</td>';
								if ($courses[$j]['visible'] == 1) {
									echo '<td>' . __('yes','teachpress') . '</tr>';
								} 
								else {
									echo '<td>' . __('no','teachpress') . '</tr>';
								}
								echo '</tr>';
							}
						}
						// End search childs
					}	
				}
				// if the user is using the search
				else {
					if ($courses[$i]['parent'] != 0) {
						$parent_name = $wpdb->get_var("SELECT name FROM " . $teachpress_courses . " WHERE course_id = '" . $courses[$i]['parent'] . "'");
						$parent_name = $parent_name . " ";
					}
					else {
						$parent_name = "";
					} 
					echo '<tr id="teachpress_table">';
					echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $courses[$i]['course_id'] . '"';
					if ( $bulk == "copy" || $bulk == "delete") { 
						for( $k = 0; $k < count( $checkbox ); $k++ ) { 
							if ( $courses[$i]['course_id'] == $checkbox[$k] ) { echo 'checked="checked"';} 
						} 
					}
					echo '/></th>';
					echo '<td><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $courses[$i]['course_id'] . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=show" class="teachpress_link" title="' . __('Click to edit','teachpress') . '">';
					if ($courses[$i]['name'] == $parent_name) {
						echo $parent_name; 
					} 
					else {
						echo $parent_name . ' ' . $courses[$i]['name'] . '';
					} 
					echo '</a></td>';
					echo '<td>' . $courses[$i]['course_id'] . '</td>';
					echo '<td>' . $courses[$i]['type'] . '</td>';
					echo '<td>' . $courses[$i]['room'] . '</td>';
					echo '<td>' . $courses[$i]['lecturer'] . '</td>';
					echo '<td>' . $courses[$i]['date'] . '</td>';
					echo '<td>' . $courses[$i]['places'] . '</td>';
					echo '<td';
					if ($courses[$i]['places'] > 0 && $courses[$i]['fplaces'] == 0) {
						echo ' style="color:#ff6600; font-weight:bold;"'; 
					}
					echo '>' . $courses[$i]['fplaces'] . '</td>';
					if ($courses[$i]['start'] != '0000-00-00' && $courses[$i]['end'] != '0000-00-00') {
						echo '<td>' . $courses[$i]['start'] . '</td>';
						echo '<td>' . $courses[$i]['end'] . '</td>';
					} 
					else {
						echo '<td colspan="2" style="text-align:center;">' . __('none','teachpress') . '</td>';
					}
					echo '<td>' . $courses[$i]['semester'] . '</td>';
					if ($courses[$i]['visible'] == 1) {
						echo '<td>' . __('yes','teachpress') . '</tr>';
					} 
					else {
						echo '<td>' . __('no','teachpress') . '</tr>';
					}
					echo '</tr>';
				}
			}	
		}   
	?>
	</tbody>
	</table>
	</form>
	</div>
	<?php 
	}
} ?>