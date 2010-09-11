<?php
/* Edit a student
 * from show_students.php:
 * @param $student_ID (Int)
 * @param $suche (String)
 * @param $studenten (String)
*/ 
function teachpress_editstudent_page() { 
	// Eingangsparameter
	$student = tp_sec_var($_GET[student_ID]);
	$studenten = tp_sec_var($_GET[studenten]);
	$suche = tp_sec_var($_GET[suche]);
	$entry_limit = tp_sec_var($_GET[limit]);
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_stud; 
	global $teachpress_signup;
	global $teachpress_settings;
	 // Formular-Einträge
	$checkbox = $_GET[checkbox];
	$delete = $_GET[loeschen];
	$speichern = $_GET[speichern];
		
	$wp_id = tp_sec_var($_GET[wp_id], 'integer');
	$data['$matriculation_number'] = tp_sec_var($_GET[matriculation_number], 'integer');
	$data['$firstname'] = tp_sec_var($_GET[firstname]);
	$data['$lastname'] = tp_sec_var($_GET[lastname]);
	$data['$course_of_studies'] = tp_sec_var($_GET[course_of_studies]);
	$data['$semesternumber'] = tp_sec_var($_GET[semesternumber]);
	$data['$userlogin'] = tp_sec_var($_GET[userlogin]);
	$data['$birthday'] = tp_sec_var($_GET[birthday]);
	$data['$email'] = tp_sec_var($_GET[email]);
	// WP User ID
	global $user_ID;
	get_currentuserinfo();
	?> 
	<div class="wrap">
	<?php
	// Event handler
	if ( isset($delete)) {
		tp_delete_registration($checkbox, $user_ID);
		$message = __('Enrollment deleted','teachpress');
		tp_get_message($message);
	}
	if ( isset($speichern)) {
		tp_change_student($wp_id, $data, $user_ID);
		$message = __('Changes successful','teachpress');
		tp_get_message($message);
	}
	echo '<p><a href="admin.php?page=teachpress/students.php&amp;suche=' . $suche . '&amp;studenten=' . $studenten . '&amp;limit=' . $entry_limit . '" class="teachpress_back" title="' . __('back to the overview','teachpress') . '">&larr; ' . __('back','teachpress') . ' </a></p>';
	?>
	<form name="personendetails" method="get" action="<?php echo $PHP_SELF ?>">
	<input name="page" type="hidden" value="teachpress/students.php" />
    <input name="action" type="hidden" value="show" />
	<input name="student_ID" type="hidden" value="<?php echo $student; ?>" />
	<input name="studenten" type="hidden" value="<?php echo $studenten; ?>" />
	<input name="suche" type="hidden" value="<?php echo $suche; ?>" />
    <input name="limit" type="hidden" value="<?php echo $entry_limit; ?>"
	<?php
		$sql = "SELECT * FROM " . $teachpress_stud . " WHERE wp_id = '$student'";
		$row3 = $wpdb->get_row($sql);
	?>
	<h2 style="padding-top:0px;"><?php echo $row3->firstname; ?> <?php echo $row3->lastname; ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('daten_aendern')" id="daten_aendern_2" style="cursor:pointer;"><?php _e('edit','teachpress'); ?> </a></small></h2>
	  <div id="daten_aendern" style="display:none; padding-top:5px; padding-bottom:5px; margin:5px;">
		<fieldset style="border:1px solid silver; padding:10px; width:650px;">
		  <legend><?php _e('Edit Data','teachpress'); ?></legend>
			<table class="widefat">
			 <thead>
			 <tr>
				<th><label for="wp_id"><?php _e('WordPress User-ID','teachpress'); ?></label></th>
				<td style="text-align:left;"><input name="wp_id" type="text" id="wp_id" value="<?php echo $row3->wp_id; ?>" readonly="true"/></td>
			  </tr>
			 <?php
			$field1 = tp_get_option('regnum');
			if ($field1 == '1') { ?>
			  <tr>
				<th><label for="matriculation_number"><?php _e('Matr. number','teachpress'); ?></label></th>
				<td style="text-align:left;"><input name="matriculation_number" type="text" id="matriculation_number" value="<?php echo $row3->matriculation_number; ?>" readonly="true"/></td>
			  </tr>
			<?php }?>
			  <tr>
				<th><label for="firstname"><?php _e('First name','teachpress'); ?></label></th>
				<td><input name="firstname" type="text" id="firstname" value="<?php echo $row3->firstname; ?>" size="40"/></td>
			  </tr>
			  <tr>
				<th><label for="lastname"><?php _e('Last name','teachpress'); ?></label></th>
				<td><input name="lastname" type="text" id="lastname" value="<?php echo $row3->lastname; ?>" size="40"/></td>
			  </tr>
			  <tr>
			<?php
			$field2 = tp_get_option('studies');
			if ($field2 == '1') { ?>
				<th><label for="course_of_studies"><?php _e('Course of studies','teachpress'); ?></label></th>
				<td>
				<select name="course_of_studies" id="course_of_studies">
				  <?php
				  $stud = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'course_of_studies'";
				  $stud = $wpdb->get_results($stud);
				  foreach ($stud as $stud) {
					if ($stud->value == $row3->course_of_studies) {
						$current = 'selected="selected"' ;
					}
					else {
						$current = '' ;
					}
					echo '<option value="' . $stud->value . '" ' . $current . '>' . $stud->value . '</option>';
				  } ?>
				</select></td>
			  </tr>
			<?php } ?>
			<?php
			$field3 = tp_get_option('termnumber');
			if ($field3 == '1') { ?>
			  <tr>
				<th><label for="semesternumber"><?php _e('Number of terms','teachpress'); ?></label></th>
				<td style="text-align:left;">
				<select name="semesternumber" id="semesternumber">
				  <?php
					for ($i=1; $i<20; $i++) {
					if ($i == $row3->semesternumber) {
						$current = 'selected="selected"' ;
					}
					  else {
							$current = '' ;
					  }
					  echo '<option value="' . $i . '" ' . $current . '>' . $i . '</option>';
					}  
				?>
				</select>
				</td>
			  </tr>
			<?php } ?> 
			  <tr>
				<th><label for="userlogin"><?php _e('User account','teachpress'); ?></label></th>
				<td style="text-align:left;"><input name="userlogin" type="text" id="userlogin" value="<?php echo $row3->userlogin; ?>" readonly="true"/></td>
			  </tr>
			<?php
			$field4 = tp_get_option('birthday');
			if ($field4 == '1') { ?>  
			  <tr>
				<th><label for="birthday"><?php _e('Date of birth','teachpress'); ?></label></th>
				<td><input name="birthday" type="text" id="birthday" value="<?php echo $row3->birthday; ?>" size="15"/>
				  <em><?php _e('Format: JJJJ-MM-TT','teachpress'); ?></em></td>
			  </tr>
			<?php } ?>  
			  <tr>
				<th><label for="email"><?php _e('E-Mail','teachpress'); ?></label></th>
				<td><input name="email" type="text" id="email" value="<?php echo $row3->email; ?>" size="50" readonly="true"/></td>
			  </tr>
			 </thead> 
			</table>
		<?php 
		if ($field1 != '1') {
			echo '<input name="matriculation_number" type="hidden" id="matriculation_number" value="' . $row3->matriculation_number . '" />';
		}
		if ($field2 != '1') {
			echo '<input name="course_of_studies" type="hidden" id="course_of_studies" value="' . $row3->course_of_studies . '" />';
		}
		if ($field3 != '1') {
			echo '<input name="semesternumber" type="hidden" id="semesternumber" value="' . $row3->semesternumber . '" />';
		}
		if ($field4 != '1') {
			echo '<input name="birthday" type="hidden" id="birthday" value="' . $row3->birthday . '" />';
		} ?>     
		<table border="0" cellspacing="7" cellpadding="0">
			  <tr>
				<td><input name="speichern" type="submit" id="teachpress_einzel_change" onclick="teachpress_validateForm('wp_id','','RisNum','matriculation_number','','RisNum','firstname','','R','lastname','','R','userlogin','','R','birthday','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('save','teachpress'); ?>" class="teachpress_button"/></td>
				<td><a onclick="teachpress_showhide('daten_aendern')" style="cursor:pointer;"><?php _e('cancel','teachpress'); ?></a></td>
			  </tr>
			</table>
		</fieldset>
	  </div>
	  <div style="width:55%; padding-bottom:10px;">
	  <table border="0" cellpadding="0" cellspacing="5" class="widefat">
		<thead>
		<?php
		echo '<tr>';
		echo '<th width="130">' . __('WordPress User-ID','teachpress') . '</th>';
		echo '<td>' . $row3->wp_id . '</td>';
		echo '</tr>';
		if ($field1 == '1') {
			echo '<tr>';
			echo '<th>' . __('Matr. number','teachpress') . '</th>';
			echo '<td>' . $row3->matriculation_number . '</td>';
			echo '</tr>';
		}
		if ($field2 == '1') {
			echo '<tr>';
			echo '<th>' . __('Course of studies','teachpress') . '</th>';
			echo '<td>' . $row3->course_of_studies . '</td>';
			echo '</tr>';
		}
		if ($field3 == '1') { 
			echo '<tr>';
			echo '<th>' . __('Number of terms','teachpress') . '</th>';
			echo '<td>' . $row3->semesternumber . '</td>';
			echo '</tr>';
		}
		if ($field4 == '1') {
			echo '<tr>';
			echo '<th>' . __('Date of birth','teachpress') . '</th>';
			echo '<td>' . $row3->birthday . '</td>';
			echo '</tr>';
		}
		echo '<tr>';
		echo '<th>' . __('User account','teachpress') . '</th>';
		echo '<td>' . $row3->userlogin . '</td>';
		echo '</tr>';
		echo '<tr>';
		echo'<th>' . __('E-Mail','teachpress') . '</th>';
		echo '<td><a href="mailto:' . $row3->email . '" title="' . __('Send E-Mail to','teachpress') . ' ' . $row3->firstname . ' ' . $row3->lastname . '">' . $row3->email . '</a></td>';
		echo '</tr>';
		?>
	   </thead>   
	  </table>
	  </div>
	</form>
	<form method="get" action="<?php echo $PHP_SELF ?>">
	<input name="page" type="hidden" value="teachpress/editstudent.php">
	<input name="student_ID" type="hidden" value="<?php echo"$student" ?>">
	<input name="suche" type="hidden" value="<?php echo"$suche" ?>">
	<input name="typ" type="hidden" value="<?php echo"$typ" ?>">
	<table border="1" cellspacing="0" cellpadding="5" class="widefat">
		<thead>
			<tr>
			  <th>&nbsp;</th>
			  <th><?php _e('Enrollment-Nr.','teachpress'); ?></th>
			  <th><?php _e('Registered at','teachpress'); ?></th>
			  <th><?php _e('Course','teachpress'); ?></th>
			  <th><?php _e('Type','teachpress'); ?></th>
			  <th><?php _e('Date','teachpress'); ?></th>
			</tr>
		</thead>    
		<tbody>
	<?php
			// Nach Daten zur Person: Ausgabe aller Einschreibungen
			$row2= "SELECT " . $teachpress_stud . ".wp_id, " . $teachpress_stud . ".firstname, " . $teachpress_stud . ".lastname, " . $teachpress_courses . ".name, " . $teachpress_courses . ".type, " . $teachpress_courses . ".date, " . $teachpress_courses . ".parent," . $teachpress_signup . ".con_id, " . $teachpress_signup . ".date
						FROM " . $teachpress_signup . "
						INNER JOIN " . $teachpress_courses . " ON " . $teachpress_courses . ".course_id=" . $teachpress_signup . ".course_id
						INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id= " . $teachpress_signup . ".wp_id
						WHERE " . $teachpress_signup . ".wp_id = '$student'";
			$row2 = $wpdb->get_results($row2);
			foreach($row2 as $row2) {
				if ($row2->parent != 0) {
					$parent_name = $wpdb->get_var("SELECT name FROM " . $teachpress_courses . " WHERE course_id = '$row2->parent'");
					$parent_name = $parent_name . " ";
				}
				else {
					$parent_name = "";
				}
				// Ausgabe der Infos zur gewählten LVS mit integriertem Aenderungsformular
				echo '<tr>';
				echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $row2->con_id . '"/></th>';
				echo '<td>' . $row2->con_id . '</td>';
				echo '<td>' . $row2->date . '</td>';
				echo '<td>' . $parent_name . $row2->name . '</td>';
				echo '<td>' . $row2->type . '</td>';
				echo '<td>' . $row2->date . '</td>';
				echo '</tr>';
			} ?>
		</tbody>
	</table>
	<table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
	  <tr>
		<td><?php _e('delete enrollment','teachpress'); ?></td>
		<td> <input name="loeschen" type="submit" value="<?php _e('delete','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/></td>
	  </tr>
	</table>
	</form>
	</div>
<?php } ?>