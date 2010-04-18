<?php 
/* 
 * Formular für alle manuellen Eingriffe ins Einschreibesystem
 * from students_new.php (POST):
 * 1. Adding new students manually:
 * @param wp_id - WordPress user-ID
 * @param matrikel - Registration number
 * @param vorname
 * @param nachmane - Last name
 * @param studiengang - Course of studies
 * @param fachsemester - Number of termns
 * @param uzrkurz - 
 * @param gebdat - Date of birth
 * @param email
 * 2. Subscribe students manually
 * @param student - WordPress user-ID
 * @param veranstaltung - Course-ID
 * 2. Actions
 * @param insert
 * @param einschreiben
*/ 
function teachpress_students_new_page() { 

$wp_id = htmlentities(utf8_decode($_POST[wp_id]));
$matrikel = htmlentities(utf8_decode($_POST[matrikel]));
$vorname = htmlentities(utf8_decode($_POST[vorname]));
$nachname = htmlentities(utf8_decode($_POST[nachname]));
$studiengang = htmlentities(utf8_decode($_POST[studiengang]));
$fachsemester = htmlentities(utf8_decode($_POST[fachsemester]));
$urzkurz = htmlentities(utf8_decode($_POST[urzkurz]));
$gebdat = htmlentities(utf8_decode($_POST[gebdat]));
$email = htmlentities(utf8_decode($_POST[email]));
$student = htmlentities(utf8_decode($_POST[student]));
$veranstaltung = htmlentities(utf8_decode($_POST[veranstaltung]));
$insert = $_POST[insert];
$einschreiben = $_POST[einschreiben];

if (isset($insert)) {
	tp_add_student($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel);
	$message = __('Student added','teachpress');
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
<div class="wrap" style="padding-top:10px;">
<h2><?php _e('Add students manually','teachpress'); ?></h2>
<form name="einschreibung" method="post" action="<?php echo $PHP_SELF ?>">
	<fieldset style="padding:10px; border:1px solid silver;">
	<legend><?php _e('Enrollment','teachpress'); ?></legend>
    	<p style="color:#FF0000;"><?php _e('If the student is registered for this course, the number of free places drops up to 0. The system ignores any criteria for the registration','teachpress'); ?></p>
<table border="0" cellspacing="7" cellpadding="0">
          <tr>
            <td><select name="student" id="student">
              <option value="0">- <?php _e('Select student','teachpress'); ?>- </option>
           <?php
			global $teachpress_ver; 
			global $teachpress_stud; 
			global $teachpress_einstellungen;
			$row1 = "SELECT wp_id, nachname, vorname, matrikel FROM " . $teachpress_stud . " ORDER BY nachname, vorname";
			$row1 = tp_results($row1);
			$zahl = 0;
			foreach($row1 as $row1) {
				if ($zahl != 0 && $merke[0] != $row1->nachname[0]) {
					echo '<option value="0">----------</option>';
				}
            	echo '<option value="' . $row1->wp_id . '">' . $row1->nachname . ' ' . $row1->vorname . ' ' . $row1->matrikel . '</option>';
				$merke = $row1->nachname;
				$zahl++;
			} ?>
        	</select>
            </select></td>
            <td><select name="veranstaltung" id="veranstaltung">
              <option value="0">- <?php _e('Select course','teachpress'); ?> -</option>
              <?php
			  	// Semester
                $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
				$sem = tp_results($sem);
				$x = 0;
				foreach ($sem as $sem) { 
					$period[$x] = $sem->wert;
					$x++;
                }
				// Veranstaltungen
				$row1 = "SELECT veranstaltungs_id, name, semester, parent FROM " . $teachpress_ver . " ORDER BY semester DESC, name";
				$row1 = tp_results($row1);
				$v = 0;
				foreach($row1 as $row1) { 
					$veranstaltungen[$v][0] = $row1->veranstaltungs_id;
					$veranstaltungen[$v][1] = $row1->name;
					$veranstaltungen[$v][2] = $row1->semester;
					$veranstaltungen[$v][3] = $row1->parent;
					$v++;
				}
				// Semester
				for ($i = 0; $i < $x; $i++) {
					$zahl = 0;
					//Veranstaltungen zum Semester
					for ($j = 0; $j < $v; $j++) {
						// Wenn's zum Semester passt
						if ($period[($x - 1)-$i] == $veranstaltungen[$j][2] ) {
							$parent_name = "";
							// Wenn Child
							if ($veranstaltungen[$j][3] != '0') {
								// Parent_Name suchen
								for ($k=0;$k<$v;$k++) {
									if ($veranstaltungen[$j][3] == $veranstaltungen[$k][0]) {
										$parent_name = $veranstaltungen[$k][1] . ' ';
									}
								}
							}
							echo '<option value="' . $veranstaltungen[$j][0] . '">' . $parent_name . ' ' . $veranstaltungen[$j][1] . ' ' . $veranstaltungen[$j][2] . '</option>';
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
<p style="color:#FF0000;"><?php _e('All fields required','teachpress'); ?>.</p>
<table class="widefat">
	<thead>
          <tr>
            <th><label for="wp_id"><?php _e('WordPress User-ID','teachpress'); ?></label></th>
            <td style="text-align:left;"><input type="text" name="wp_id" id="wp_id" /> 
              <span style="font-size:10px; color:#FF0000;"><?php _e('If the student has not an account for your blog, so you must create this account manually.','teachpress'); ?></span></td>
      	  </tr>
          <tr>
            <th><label for="matrikel"><?php _e('Registr.-Number','teachpress'); ?></label></th>
            <td style="text-align:left;"><input type="text" name="matrikel" id="matrikel" /></td>
          </tr>
          <tr>
            <th><label for="vorname"><?php _e('First name','teachpress'); ?></label></th>
            <td><input name="vorname" type="text" id="vorname" size="40" /></td>
          </tr>
          <tr>
            <th><label for="nachname"><?php _e('Last name','teachpress'); ?></label></th>
            <td><input name="nachname" type="text" id="nachname" size="40" /></td>
          </tr>
          <tr>
            <th><label for="studiengang"><?php _e('Course of studies','teachpress'); ?></label></th>
            <td>
            <select name="studiengang" id="studiengang">
             <?php
              $stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
			  $stud = tp_results($stud);
			  foreach ($stud as $stud) {
			  	echo '<option value="' . $stud->wert . '">' . $stud->wert . '</option>';
              } ?>
            </select>
            </td>
          </tr>
          <tr>
            <th><label for="fachsemester"><?php _e('Number of terms','teachpress'); ?></label></th>
            <td style="text-align:left;">
            <select name="fachsemester" id="fachsemester">
            <?php
			for ($i=1; $i<20; $i++) {
				echo '<option value="' . $i . '">' . $i . '</option>';
			} ?>
            </select>
            </td>
          </tr>
          <tr>
            <th><label for="urzkurz"><?php _e('User account','teachpress'); ?></label></th>
            <td style="text-align:left;"><input type="text" name="urzkurz" id="urzkurz" /></td>
          </tr>
          <tr>
            <th><label for="gebdat"><?php _e('Date of birth','teachpress'); ?></label></th>
            <td><input name="gebdat" type="text" id="gebdat" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" size="15"/>
              <em><?php _e('Format','teachpress'); ?>: <?php _e('JJJJ-MM-TT','teachpress'); ?></em></td>
          </tr>
          <tr>
            <th><label for="email"><?php _e('E-Mail','teachpress'); ?></label></th>
            <td><input name="email" type="text" id="email" size="50" /></td>
          </tr>
         </thead> 
        </table>
    <p>
      <input name="insert" type="submit" id="std_einschreiben" onclick="teachpress_validateForm('wp_id','','RisNum','matrikel','','RisNum','vorname','','R','nachname','','R','urzkurz','','R','gebdat','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>" class="teachpress_button"/>
      <input name="reset" type="reset" id="reset" value="Reset" class="teachpress_button"/>
    </p>
</fieldset>
</form>   
</div>
<?php } ?>