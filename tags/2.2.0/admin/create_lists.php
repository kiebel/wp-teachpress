<?php 
/* Create attendance lists
 * from editlvs.php (GET):
 * @param $course_ID
 * @param $search
 * @param $sem
*/
function tp_lists_page() {
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_stud; 
	global $teachpress_settings; 
	global $teachpress_signup;
	// from editlvs.php
	$course_ID = tp_sec_var($_GET[lvs_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$sem = tp_sec_var($_GET[sem]);
	$sort = tp_sec_var($_GET[sort]);
	$matriculation_number_field = tp_sec_var($_GET[matriculation_number_field]);
	$nutzerkuerzel_field = tp_sec_var($_GET[nutzerkuerzel_field]);
	$course_of_studies_field = tp_sec_var($_GET[course_of_studies_field]);
	$semesternumber_field = tp_sec_var($_GET[semesternumber_field]);
	$birthday_field = tp_sec_var($_GET[birthday_field]);
	$email_field = tp_sec_var($_GET[email_field]);
	// lists.php
	$anzahl = tp_sec_var($_GET[anzahl], 'integer');
	$create = $_GET[create];
	?>
	<div class="wrap" style="padding-top:10px;">
	<?php if ($create == '') {
		echo '<a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course_ID . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=show" class="teachpress_back" title="' . __('back to the course','teachpress') . '">&larr; ' . __('back','teachpress') . '</a>';
	}
	else {
		echo '<a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course_ID . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=list" class="teachpress_back" title="' . __('back to the course','teachpress') . '">&larr; ' . __('back','teachpress') . '</a>';
	}?>
	<form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
	<input name="page" type="hidden" value="teachpress/teachpress.php"/>
	<input name="action" type="hidden" value="list"/>
	<input name="lvs_ID" type="hidden" value="<?php echo $course_ID; ?>"/>
	<input name="sem" type="hidden" value="<?php echo $sem; ?>" />
	<input name="search" type="hidden" value="<?php echo $search; ?>" />
	<?php if ($create == '') {?>
	<div style="padding:10px 0 10px 30px;">
	<h4><?php _e('Setup attendance list','teachpress'); ?></h4>
	<table class="widefat" style="width:400px;">
		<thead>
		 <tr>
			<th><label for="anzahl"><?php _e('Sort after','teachpress'); ?></label></th>
			<td><select name="sort" id="sort">
					<option value="1"><?php _e('Last name','teachpress'); ?></option>
					<?php 
					$val = tp_get_option('regnum');
					if ($val == '1') {?>
					<option value="2"><?php _e('Matr. number','teachpress'); ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th style="width:160px;"><label for="anzahl"><?php _e('Number of free columns','teachpress'); ?></label></th>
			<td><select name="anzahl" id="anzahl">
					<?php
					for ($i=1; $i<=15; $i++) {
						if ($i == 7) {
							echo '<option value="' . $i . '" selected="selected">' . $i . '</option>';
						}
						else {
							echo '<option value="' . $i . '">' . $i . '</option>';
						}	
					} ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><?php _e('Additional columns','teachpress'); ?></th>
			<td>
			 <?php
				if ($val == '1') {
					echo '<input name="matriculation_number_field" id="matriculation_number_field" type="checkbox" value="1" /> <label for="matriculation_number_field">' . __('Matr. number','teachpress') . '</label><br />';
				}
				echo '<input name="nutzerkuerzel_field" id="nutzerkuerzel_field" type="checkbox" checked="checked" value="1" /> <label for="nutzerkuerzel_field">' . __('User account','teachpress') . '</label><br />';
				$val = tp_get_option('studies');
				if ($val == '1') {
					echo '<input name="course_of_studies_field" id="course_of_studies_field" type="checkbox" value="1" /> <label for="course_of_studies_field">' . __('Course of studies','teachpress') . '</label><br />';
				}
				$val = tp_get_option('termnumber');
				if ($val == '1') {
					echo '<input name="semesternumber_field" id="semesternumber_field" type="checkbox" value="1" /> <label for="semesternumber_field">' . __('Number of terms','teachpress') . '</label><br />';
				}
				$val = tp_get_option('birthday');
				if ($val == '1') {
					echo '<input name="birthday_field" id="birthday_field" type="checkbox" value="1" /> <label for="birthday_field">' .  __('Date of birth','teachpress') . '</label><br />';
				}
				echo '<input name="email_field" id="email_field" type="checkbox" value="1" /> <label for="email_field_field">' . __('E-Mail','teachpress') . '</label><br />';
				?>
			</td>
		</tr>
		</thead>
	</table>
	<p><input name="create" type="submit" class="teachpress_button" value="<?php _e('Create','teachpress'); ?>"/></p>
	</div>
	<?php
	}
	if ( $create == __('Create','teachpress') ) {
		$row = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$course_ID'";
		$row = $wpdb->get_results($row);
		foreach($row as $row) {
			// define course name
			if ($row->parent != 0) {
				$sql = "SELECT name FROM " . $teachpress_courses . " WHERE course_id = '$row->parent'";
				$parent_name = $wpdb->get_var($sql);
				// if parent_name == child name
				if ($parent_name == $row->name) {
					$parent_name = "";
				}
			}
			else {
				$parent_name = "";
			}
		   ?>
			<h2><?php echo $parent_name; ?> <?php echo $row->name; ?> <?php echo $row->semester; ?></h2>
			<div id="einschreibungen" style="padding:5px;">
			<div style="width:700px; padding-bottom:10px;">
				<table border="1" cellspacing="0" cellpadding="0" class="tp_print">
					  <tr>
						<th><?php _e('Lecturer','teachpress'); ?></th>
						<td><?php echo $row->lecturer; ?></td>
						<th><?php _e('Date','teachpress'); ?></th>
						<td><?php echo $row->date; ?></td>
						<th><?php _e('Room','teachpress'); ?></th>
						<td><?php echo $row->room; ?></td>
					  </tr>
				</table>
			</div>
			<table border="1" cellpadding="0" cellspacing="0" class="tp_print" width="100%">
			  <tr style="border-collapse: collapse; border: 1px solid black;">
				<th width="20" height="100">&nbsp;</th>
				<th width="250"><?php _e('Name','teachpress'); ?></th>
				<?php
				if ($matriculation_number_field == '1') {
					echo '<th>' . __('Matr. number','teachpress') . '</th>';
				}
				if ($nutzerkuerzel_field == '1') {
					echo '<th width="81">' . __('User account','teachpress') . '</th>';
				}
				if ($course_of_studies_field == '1') {
					echo '<th>' . __('Course of studies','teachpress') . '</th>';
				}
				if ($semesternumber_field == '1') {
					echo '<th>' . __('Number of terms','teachpress') . '</th>';
				}
				if ($birthday_field == '1') {
					echo '<th>' . __('Date of birth','teachpress') . '</th>';
				}
				if ($email_field == '1') {
					echo '<th>' . __('E-Mail','teachpress') . '</th>';
				}
				for ($i=1; $i<=$anzahl; $i++ ) {
					echo '<th>&nbsp;</th>';
				}
				?>
			  </tr>
			 <tbody> 
		<?php         
		   }
		$nummer = 1;
		// Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
		$select = "SELECT s.firstname, s.lastname";
		if ($matriculation_number_field == '1') {
			$select = $select . ", s.matriculation_number";
		}
		if ($nutzerkuerzel_field == '1') {
			$select = $select . ", s.userlogin";
		}
		if ($course_of_studies_field == '1') {
			$select = $select . ", s.course_of_studies";
		}
		if ($semesternumber_field == '1') {
			$select = $select . ", s.semesternumber";
		}
		if ($birthday_field == '1') {
			$select = $select . ", s.birthday";
		}
		if ($email_field == '1') {
			$select = $select . ", s.email";
		}
		if ($sort == '2') {
			$order_by = "s.matriculation_number";
		}
		else {
			$order_by = "s.lastname";
		}
		$row = "" . $select . "
				FROM " . $teachpress_signup . " k
				INNER JOIN " . $teachpress_courses . " v ON v.course_id=k.course_id
				INNER JOIN " . $teachpress_stud . " s ON s.wp_id=k.wp_id
				WHERE v.course_id = '$course_ID'
				ORDER BY " . $order_by . "";	
		$row = $wpdb->get_results($row);
		foreach($row as $row3)
		  {
		  ?>
		  <tr>
			<td><?php echo $nummer; ?></td>
			<td><?php echo $row3->lastname; ?>, <?php echo $row3->firstname; ?></td>
			<?php
			if ($matriculation_number_field == '1') {
				echo '<td>' . $row3->matriculation_number . '</td>';
			}
			if ($nutzerkuerzel_field == '1') {
				echo '<td>' . $row3->userlogin . '</td>';
			}
			if ($course_of_studies_field == '1') {
				echo '<td>' . $row3->course_of_studies . '</td>';
			}
			if ($semesternumber_field == '1') {
				echo '<td>' . $row3->semesternumber . '</td>';
			}
			if ($birthday_field == '1') {
				echo '<td>' . $row3->birthday . '</td>';
			}
			if ($email_field == '1') {
				echo '<td>' . $row3->email . '</td>';
			}
			for ($i=1; $i<=$anzahl; $i++ ) {
				echo '<td>&nbsp;</td>';
			}
			?>
		  </tr>
		  <?php
		  $nummer++;
	   }
	?>
	</tbody>
	</table>
	<?php } ?>
	</form>
	</div>
<?php } ?>