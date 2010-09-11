<?php 
/* 
 * Formular für alle manuellen Eingriffe ins Einschreibesystem
 * from students_new.php (POST):
 * 1. Adding new students manually:
 * @param wp_id - WordPress user-ID
 * @param matriculation_number - Registration number
 * @param firstname
 * @param nachmane - Last name
 * @param course_of_studies - Course of studies
 * @param semesternumber - Number of termns
 * @param uzrkurz - 
 * @param birthday - Date of birth
 * @param email
 * 2. Subscribe students manually
 * @param student - WordPress user-ID
 * @param veranstaltung - Course-ID
 * 2. Actions
 * @param insert
 * @param einschreiben
*/ 
function teachpress_students_new_page() { 

global $wpdb;
global $teachpress_courses; 
global $teachpress_stud; 
global $teachpress_settings;

$wp_id = tp_sec_var($_POST[wp_id], 'integer');
$data['$matriculation_number'] = tp_sec_var($_POST[matriculation_number], 'integer');
$data['$firstname'] = tp_sec_var($_POST[firstname]);
$data['$lastname'] = tp_sec_var($_POST[lastname]);
$data['$course_of_studies'] = tp_sec_var($_POST[course_of_studies]);
$data['$semesternumber'] = tp_sec_var($_POST[semesternumber], 'integer');
$data['$userlogin'] = tp_sec_var($_POST[userlogin]);
$data['$birthday'] = tp_sec_var($_POST[birthday]);
$data['$email'] = tp_sec_var($_POST[email]);
$student = tp_sec_var($_POST[student]);
$veranstaltung = tp_sec_var($_POST[veranstaltung]);
$insert = $_POST[insert];
$einschreiben = $_POST[einschreiben];

if (isset($insert)) {
	$ret = tp_add_student($wp_id, $data);
	if ($ret != false) {
		$message = __('Registration successful','teachpress');
	}
	else {
		$message = __('Error: User already exist','teachpress');
	}
	$site = 'admin.php?page=teachpress/students_new.php';
	tp_get_message($message, $site);
}
if (isset($einschreiben) && $student != 0 && $veranstaltung != 0) {
	tp_subscribe_student_manually($student, $veranstaltung);
	$message = __('The enrollment for the selected student was successful.','teachpress');
	$site = 'admin.php?page=teachpress/students_new.php';
	tp_get_message($message, $site);
}
?>
<div class="wrap" >
<h2><?php _e('Add students manually','teachpress'); ?></h2>
<p><strong><?php _e("This menu is only for the case, if it's not possible for a student to sign up themselves.",'teachpress'); ?></strong></p>
<form name="einschreibung" method="post" action="<?php echo $PHP_SELF ?>">
	<fieldset style="padding:10px; border:1px solid silver;">
	<legend><?php _e('Enrollment','teachpress'); ?></legend>
    	<p style="color:#FF0000;"><?php _e('If the student is registered for this course, the number of free places drops up to 0. The system ignores any criteria for the registration','teachpress'); ?></p>
<table border="0" cellspacing="7" cellpadding="0">
          <tr>
            <td><select name="student" id="student">
              <option value="0">- <?php _e('Select student','teachpress'); ?>- </option>
           <?php
			$row1 = "SELECT wp_id, lastname, firstname, matriculation_number FROM " . $teachpress_stud . " ORDER BY lastname, firstname";
			$row1 = $wpdb->get_results($row1);
			$zahl = 0;
			foreach($row1 as $row1) {
				if ($zahl != 0 && $merke[0] != $row1->lastname[0]) {
					echo '<option value="0">----------</option>';
				}
            	echo '<option value="' . $row1->wp_id . '">' . $row1->lastname . ' ' . $row1->firstname . ' ' . $row1->matriculation_number . '</option>';
				$merke = $row1->lastname;
				$zahl++;
			} ?>
        	</select>
            </select></td>
            <td><select name="veranstaltung" id="veranstaltung">
              <option value="0">- <?php _e('Select course','teachpress'); ?> -</option>
              <?php
			  	// Semester
                $sem = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id";
				$sem = $wpdb->get_results($sem);
				$x = 0;
				foreach ($sem as $sem) { 
					$period[$x] = $sem->value;
					$x++;
                }
				// Veranstaltungen
				$row1 = "SELECT course_id, name, semester, parent FROM " . $teachpress_courses . " ORDER BY semester DESC, name";
				$row1 = $wpdb->get_results($row1);
				$v = 0;
				foreach($row1 as $row1) { 
					$veranstaltungen[$v]["id"] = $row1->course_id;
					$veranstaltungen[$v]["name"] = $row1->name;
					$veranstaltungen[$v]["semester"] = $row1->semester;
					$veranstaltungen[$v]["parent"] = $row1->parent;
					$v++;
				}
				// Semester
				for ($i = 0; $i < $x; $i++) {
					$zahl = 0;
					//Veranstaltungen zum Semester
					for ($j = 0; $j < $v; $j++) {
						// Wenn's zum Semester passt
						if ($period[($x - 1)-$i] == $veranstaltungen[$j]["semester"] ) {
							$parent_name = "";
							// Wenn Child
							if ($veranstaltungen[$j]["parent"] != '0') {
								// Parent_Name suchen
								for ($k=0;$k<$v;$k++) {
									if ($veranstaltungen[$j]["parent"] == $veranstaltungen[$k]["id"]) {
										$parent_name = $veranstaltungen[$k]["name"] . ' ';
									}
								}
							}
							echo '<option value="' . $veranstaltungen[$j]["id"] . '">' . $parent_name . ' ' . $veranstaltungen[$j]["name"] . ' ' . $veranstaltungen[$j]["semester"] . '</option>';
							$zahl++;
						} 
					}
					// Wenn Semester wechselt
					if ($zahl != 0) {
						echo '<option value="0">------</option>';
					}
				}	
				?>
            </select></td>
          </tr>
          <tr>
            <td colspan="2"><input type="submit" name="einschreiben" id="std_einschreiben2" value="<?php _e('create','teachpress'); ?>" class="teachpress_button"/></td>
          </tr>
        </table>
	</fieldset>
</form> 
<p style="padding:0px; margin:0px;">&nbsp;</p>
<form id="neuer_student" name="neuer_student" method="post" action="<?php echo $PHP_SELF ?>">
<fieldset style="padding:10px; border:1px solid silver;">
<legend><?php _e('Add student','teachpress'); ?></legend>
<table class="widefat">
	<thead>
          <tr>
            <th><label for="wp_id"><?php _e('WordPress User-ID','teachpress'); ?></label></th>
            <td style="text-align:left;">
            <?php 
			echo '<select name="wp_id" id="wp_id">';
			echo '<option value="n">' . __('Select user','teachpress') . '</option>';
			$sql = "SELECT u.ID, s.wp_id, u.user_login FROM " . $wpdb->users . " u
					LEFT JOIN " . $teachpress_stud . " s ON u.ID = s.wp_id";	
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				if ($row->ID != $row->wp_id) {
					echo '<option value="' . $row->ID . '">' . $row->user_login . '</option>';
				}
			}
				echo '</select>';
			?>
            </td>
            <td><?php _e('The Menu shows all your blog users who has no teachPress account','teachpress'); ?></td>  
      	  </tr>
          <?php $field1 = tp_get_option('regnum');
			if ($field1 == '1') { ?>
          <tr>
            <th><label for="matriculation_number"><?php _e('Matr. number','teachpress'); ?></label></th>
            <td style="text-align:left;"><input type="text" name="matriculation_number" id="matriculation_number" /></td>
            <td></td>
          </tr>
          <?php } ?>
          <tr>
            <th><label for="firstname"><?php _e('First name','teachpress'); ?></label></th>
            <td><input name="firstname" type="text" id="firstname" size="40" /></td>
            <td></td>
          </tr>
          <tr>
            <th><label for="lastname"><?php _e('Last name','teachpress'); ?></label></th>
            <td><input name="lastname" type="text" id="lastname" size="40" /></td>
            <td></td>
          </tr>
          <?php $field2 = tp_get_option('studies');
        	if ($field2 == '1') { ?>
          <tr>
            <th><label for="course_of_studies"><?php _e('Course of studies','teachpress'); ?></label></th>
            <td>
            <select name="course_of_studies" id="course_of_studies">
             <?php
              $stud = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'course_of_studies'";
			  $stud = $wpdb->get_results($stud);
			  foreach ($stud as $stud) {
			  	echo '<option value="' . $stud->value . '">' . $stud->value . '</option>';
              } ?>
            </select>
            </td>
            <td></td>
          </tr>
          <?php } ?>
          <?php $field2 = tp_get_option('termnumber');
        	if ($field2 == '1') { ?>
          <tr>
            <th><label for="semesternumber"><?php _e('Number of terms','teachpress'); ?></label></th>
            <td style="text-align:left;">
            <select name="semesternumber" id="semesternumber">
            <?php
			for ($i=1; $i<20; $i++) {
				echo '<option value="' . $i . '">' . $i . '</option>';
			} ?>
            </select>
            </td>
            <td></td>
          </tr>
          <?php } ?> 
          <tr>
            <th><label for="userlogin"><?php _e('User account','teachpress'); ?></label></th>
            <td style="text-align:left;"><input type="text" name="userlogin" id="userlogin" /></td>
            <td></td>
          </tr>
          <?php $field2 = tp_get_option('birthday');
        	if ($field2 == '1') { ?>
          <tr>
            <th><label for="birthday"><?php _e('Date of birth','teachpress'); ?></label></th>
            <td><input name="birthday" type="text" id="birthday" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" size="15"/>
              </td>
            <td><?php _e('Format','teachpress'); ?>: <?php _e('JJJJ-MM-TT','teachpress'); ?></td>  
          </tr>
          <?php } ?>
          <tr>
            <th><label for="email"><?php _e('E-Mail','teachpress'); ?></label></th>
            <td><input name="email" type="text" id="email" size="50" /></td>
            <td></td>
          </tr>
         </thead> 
        </table>
    <p>
      <input name="insert" type="submit" id="std_einschreiben" onclick="teachpress_validateForm('wp_id','','RisNum','firstname','','R','lastname','','R','birthday','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>" class="teachpress_button"/>
      <input name="reset" type="reset" id="reset" value="Reset" class="teachpress_button"/>
    </p>
</fieldset>
</form>   
</div>
<?php } ?>