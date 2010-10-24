<?php 
/* overview for students
 *
 * from editstudent.php (GET), students.php (GET):
 * @param $suche - String
 * @param $studenten - String
*/
function teachpress_students_page() { 

	global $teachpress_stud;
	global $wpdb;
	global $user_ID;
	get_currentuserinfo();
	$checkbox = $_GET[checkbox];
	$bulk = $_GET[bulk];
	$suche = tp_sec_var($_GET[suche]); 
	$studenten = tp_sec_var($_GET[studenten]);
	
	// Page menu
	$page = 'teachpress/students.php';
	$number_messages = 50;
	// Handles limits 
	if (isset($_GET[limit])) {
		$entry_limit = (int)$_GET[limit];
		if ($entry_limit < 0) {
			$entry_limit = 0;
		}
	}
	else {
		$entry_limit = 0;
	}
	
	// Event handler
	if ($_GET[action] == 'show') {
		teachpress_editstudent_page();
	}
	else {
		$field1 = tp_get_option('regnum');
		if ($field1 == '1') {
			$order = 'matriculation_number';
		}
		else {
			$order = 'wp_id';
		}
		if ($suche != "") {
			$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE matriculation_number like '%$suche%' OR wp_id like '%$suche%' OR firstname LIKE '%$suche%' OR lastname LIKE '%$suche%' ORDER BY " . $order . "";
		}
		else {
			if ($studenten == 'alle' || $studenten == '') {
				$abfrage = "SELECT * FROM " . $teachpress_stud . " ORDER BY " . $order . "";
			}
			else {
				$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE course_of_studies = '$studenten' ORDER BY " . $order . "";
			}
		}
		$test = $wpdb->query($abfrage);
		$abfrage = $abfrage . " LIMIT $entry_limit, $number_messages";
		// Test ob Eintraege vorhanden
		?>
		<div class="wrap">
		<h2><?php _e('Students','teachpress'); ?></h2>
		<form name="suche" method="get" action="<?php echo $PHP_SELF ?>">
		<input name="page" type="hidden" value="teachpress/students.php" />
        <input type="hidden" name="limit" id="limit" value="<?php echo $entry_limit; ?>"/>
		<?php
		// Delete students part 1
		if ( $bulk == "delete" ) {
			echo '<div class="teachpress_message">
			<p class="hilfe_headline">' . __('Are you sure to delete the selected students?','teachpress') . '</p>
			<p><input name="delete_ok" type="submit" class="teachpress_button" value="' . __('delete','teachpress') . '"/>
			<a href="admin.php?page=teachpress/teachpress.php&amp;semester2=' . $semester2 . '&amp;search=' . $search . '&amp;limit=' . $entry_limit . '"> ' . __('cancel','teachpress') . '</a></p>
			</div>';
		}
		// Delete students part 2
		if ( isset($_GET[delete_ok]) ) {
			tp_delete_student($checkbox, $user_ID);
			$message = __('Students deleted','teachpress');
			tp_get_message($message);
		}
		?>
		<div id="searchbox" style="float:right; padding-bottom:5px;">  
		<?php if ($suche != "") { ?>
		<a href="admin.php?page=teachpress/students.php" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a>
		<?php } ?>
		<input name="suche" type="text" value="<?php echo stripslashes($suche); ?>"/></td>
		<input name="go" type="submit" value="<?php _e('search','teachpress'); ?>" id="teachpress_suche_senden" class="teachpress_button"/>
	  </div>
	  <div class="tablenav" style="padding-bottom:5px;">
		<select name="studenten" id="studenten">
			<option value="alle">- <?php _e('All students','teachpress'); ?> -</option>
			<?php
			$row = "SELECT DISTINCT course_of_studies FROM " . $teachpress_stud . " ORDER BY course_of_studies";
			$row = $wpdb->get_results($row);
			foreach($row as $row){
				if ($row->course_of_studies == $studenten) {
					$current = ' selected="selected"' ;
				}
				else {
					$current = '' ;
				}
				echo'<option value="' . $row->course_of_studies . '"' . $current . '>' . $row->course_of_studies . '</option>';
			} ?>
			</select>
		<input name="anzeigen" type="submit" id="teachpress_suche_senden" value="<?php _e('show','teachpress'); ?>" class="teachpress_button"/>
		<select name="bulk" id="bulk">
			<option>- <?php _e('Bulk actions','teachpress'); ?> -</option>
			<option value="delete"><?php _e('delete','teachpress'); ?></option>
		</select>
		<input type="submit" name="teachpress_submit" value="<?php _e('ok','teachpress'); ?>" id="teachpress_submit2" class="teachpress_button"/>
         <?php
		// Page Menu
		if ($test > $number_messages) {
			$num_pages = floor (($test / $number_messages) + 1);
			// previous page link
			if ($entry_limit != 0) {
				$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ($entry_limit - $number_messages) . '&amp;search=' . $search . '" title="' . __('previous page','teachpress') . '" class="page-numbers">&larr;</a> ';
			}	
			// page numbers
			$akt_seite = $entry_limit + $number_messages;
			for($i=1; $i <= $num_pages; $i++) { 
				$s = $i * $number_messages;
				// First and last page
				if ( ($i == 1 && $s != $akt_seite ) || ($i == $num_pages && $s != $akt_seite ) ) {
					$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ( $s - $number_messages) . '&amp;search=' . $search . '" title="' . __('Page','teachpress') . ' ' . $i . '" class="page-numbers">' . $i . '</a> ';
				}
				// current page
				elseif ( $s == $akt_seite ) {
					$all_pages = $all_pages . '<span class="page-numbers current">' . $i . '</span> ';
				}
				else {
					// Placeholder before
					if ( $s == $akt_seite - (2 * $number_messages) && $num_pages > 4 ) {
						$all_pages = $all_pages . '... ';
					}
					// Normal page
					if ( $s >= $akt_seite - (2 * $number_messages) && $s <= $akt_seite + (2 * $number_messages) ) {
						$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ( ( $i * $number_messages ) - $number_messages) . '&amp;search=' . $search . '" title="' . __('Page','teachpress') . ' ' . $i . '" class="page-numbers">' . $i . '</a> ';
					}
					// Placeholder after
					if ( $s == $akt_seite + (2 * $number_messages) && $num_pages > 4 ) {
						$all_pages = $all_pages . '... ';
					}
				}
			}
			// next page link
			if ( ( $entry_limit + $number_messages ) <= ($test)) { 
				$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ($entry_limit + $number_messages) . '&amp;search=' . $search . '" title="' . __('next page','teachpress') . '" class="page-numbers">&rarr;</a> ';
			}
			// handle displaying entry number
			if ($akt_seite - 1 > $test) {
				$anz2 = $test;
			}
			else {
				$anz2 = $akt_seite - 1;
			}
			// print menu
			echo '<div class="tablenav-pages" style="float:right;">' . __('Displaying','teachpress') . ' ' . ($entry_limit + 1) . '-' . $anz2 . ' of ' . $test . ' ' . $all_pages . '</div>';
		}?>
	  </div>
	  <table border="1" cellpadding="5" cellspacing="0" class="widefat">
		<thead>
		 <tr>
			<?php
			echo '<th>&nbsp;</th>'; 
			if ($field1 == '1') {
				echo '<th>' .  __('Matr. number','teachpress') . '</th>';
			}
			else {
				echo '<th>' . __('WordPress User-ID','teachpress') . '</th>';
			}
			echo '<th>' . __('Last name','teachpress') . '</th>';
			echo '<th>' . __('First name','teachpress') . '</th>'; 
			$field2 = tp_get_option('studies');
			if ($field2 == '1') {
				echo '<th>' .  __('Course of studies','teachpress') . '</th>';
			}
			$field3 = tp_get_option('termnumber');
			if ($field3 == '1') {
				echo '<th>' .  __('Number of terms','teachpress') . '</th>';
			}
			$field4 = tp_get_option('birthday');
			if ($field4 == '1') {
				echo '<th>' .  __('Date of birth','teachpress') . '</th>';
			}
			echo '<th>' . __('User account','teachpress') . '</th>';
			echo '<th>' . __('E-Mail','teachpress') . '</th>';
			?>
		 </tr>
		</thead>
		<tbody> 
	<?php
		// Show students
		if ($test == 0) { 
			echo '<tr><td colspan="9"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
		}
		else {
			$row3 = $wpdb->get_results($abfrage);
			foreach($row3 as $row3) { 
				echo '<tr>';
				echo '<th class="check-column"><input type="checkbox" name="checkbox[]" id="checkbox" value="' . $row3->wp_id . '"';
				if ( $bulk == "delete") { 
					for( $i = 0; $i < count( $checkbox ); $i++ ) { 
						if ( $row3->wp_id == $checkbox[$i] ) { echo 'checked="checked"';} 
					} 
				}
				echo '/></th>';
				echo '<td><a href="admin.php?page=teachpress/students.php&amp;student_ID=' . $row3->wp_id . '&amp;suche=' . $suche . '&amp;studenten=' . $studenten . '&amp;limit=' . $entry_limit . '&amp;action=show" class="teachpress_link" title="' . __('Click to edit','teachpress') . '">';
				if ($field1 == '1') {
					echo '' . $row3->matriculation_number . '</a></td>';
				}
				else {
					echo '' . $row3->wp_id . '</a></td>';
				}
				echo '<td>' . stripslashes($row3->lastname) . '</td>';
				echo '<td>' . stripslashes($row3->firstname) . '</td>';
				if ($field2 == '1') {
					echo '<td>' . stripslashes($row3->course_of_studies) . '</td>';
				} 
				if ($field3 == '1') {
					echo '<td>' . $row3->semesternumber . '</td>';
				} 
				if ($field4 == '1') {
					echo '<td>' . $row3->birthday . '</td>';
				}
				echo '<td>' . $row3->userlogin . '</td>';
				echo '<td><a href="mailto:' . $row3->email . '" title="E-Mail senden">' . $row3->email . '</a></td>';
				echo '</tr>';
			} 
		}
		?> 
		</tbody>
		</table>
        <div class="tablenav"><div class="tablenav-pages" style="float:right;">
		<?php 
		if ($test > $number_messages) {
			echo __('Displaying','teachpress') . ' ' . ($entry_limit + 1) . '-' . $anz2 . ' ' . __('of','teachpress') . ' ' . $test . ' ' . $all_pages . '';
		} 
		else {
			echo __('Displaying','teachpress') . ' ' . $test . ' ' . __('entries','teachpress') . ' '. $all_pages . '';
		}?>
		</div></div>
		</form>
		</div>
		<?php
	}
} ?>