<?php 
/* 
 * Formular für alle manuellen Eingriffe ins Einschreibesystem
*/
?>

<?php  
if ( is_user_logged_in() ) { 

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
	add_student_manuell($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel);
	$message = __('Student hinzugef&uuml;gt','teachpress');
	$site = 'admin.php?page=teachpress/studenten_neu.php';
	tp_get_message($message, $site);
}
if (isset($einschreiben) && $student != 0 && $veranstaltung != 0) {
	student_manuell_eintragen ($student, $veranstaltung);
	$message = __('Einschreibung f&uuml;r ausgew&auml;hlten Studenten vorgenommen','teachpress');
	$site = 'admin.php?page=teachpress/studenten_neu.php';
	tp_get_message($message, $site);
}
?>
<div class="wrap" style="padding-top:10px;">
<h2><?php _e('Studenten manuell hinzuf&uuml;gen','teachpress'); ?></h2>
<form name="einschreibung" method="post" action="<?php echo $PHP_SELF ?>">
	<fieldset style="padding:10px; border:1px solid silver;">
	<legend><?php _e('Einschreibung','teachpress'); ?></legend>
    	<p style="color:#FF0000;"><?php _e('Wenn der Student in die Lehrveranstaltung eingeschrieben wird, werden die freien Pl&auml;tze<u> bis 0 </u>heruntergez&auml;hlt! Eine Pr&uuml;fung, ob der Student bereits in die Lehrveranstaltung eingeschrieben ist, oder ob noch freie Pl&auml;tze vorhanden sind, erfolgt nicht!','teachpress'); ?></p>
<table border="0" cellspacing="7" cellpadding="0">
          <tr>
            <td><select name="student" id="student">
              <option value="0"><?php _e('Student ausw&auml;hlen','teachpress'); ?></option>
              <option value="0">----------</option>
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
              <option value="0"><?php _e('Veranstaltung ausw&auml;hlen','teachpress'); ?></option>
              <option value="0">----------</option>
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
            <td colspan="2"><input type="submit" name="einschreiben" id="std_einschreiben2" value="<?php _e('Erstellen','teachpress'); ?>" class="teachpress_button"/></td>
          </tr>
        </table>
	</fieldset>
</form> 
<p style="padding:0px; margin:0px;">&nbsp;</p>
<form id="neuer_student" name="neuer_student" method="post" action="<?php echo $PHP_SELF ?>">
<fieldset style="padding:10px; border:1px solid silver;">
<legend><?php _e('Student hinzuf&uuml;gen','teachpress'); ?></legend>
<p style="color:#FF0000;"><?php _e('Bitte alle Felder ausf&uuml;llen','teachpress'); ?>.</p>
<table class="widefat">
	<thead>
          <tr>
            <th><?php _e('WordPress User-ID','teachpress'); ?></th>
            <td style="text-align:left;"><input type="text" name="wp_id" id="wp_id" /> 
              <span style="font-size:10px; color:#FF0000;"><?php _e('Falls der Student noch keinen WP-User-Account besitzt, m&uuml;ssen Sie dieses per Hand anlegen','teachpress'); ?></span></td>
      	  </tr>
          <tr>
            <th><?php _e('Matrikel','teachpress'); ?></th>
            <td style="text-align:left;"><input type="text" name="matrikel" id="matrikel" /></td>
          </tr>
          <tr>
            <th><?php _e('Vorname','teachpress'); ?></th>
            <td><input name="vorname" type="text" id="vorname" size="40" /></td>
          </tr>
          <tr>
            <th><?php _e('Nachname','teachpress'); ?></th>
            <td><input name="nachname" type="text" id="nachname" size="40" /></td>
          </tr>
          <tr>
            <th><?php _e('Studiengang','teachpress'); ?></th>
            <td>
            <select name="studiengang" id="studiengang">
             <?php
              $stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
			  $stud = tp_results($stud);
			  foreach ($stud as $stud) { ?>
                  <option value="<?php echo $stud->wert; ?>"><?php echo $stud->wert; ?></option>
              <?php } ?>
            </select>
            </td>
          </tr>
          <tr>
            <th><?php _e('Fachsemester','teachpress'); ?></th>
            <td style="text-align:left;"><label>
            <select name="fachsemester" id="fachsemester">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              </select>
            </label></td>
          </tr>
          <tr>
            <th><?php _e('URZ-K&uuml;rzel','teachpress'); ?></th>
            <td style="text-align:left;"><input type="text" name="urzkurz" id="urzkurz" /></td>
          </tr>
          <tr>
            <th><?php _e('Geburtsdatum','teachpress'); ?></th>
            <td><input name="gebdat" type="text" id="gebdat" value="JJJJ-MM-TT" size="15"/>
              <em><?php _e('Format','teachpress'); ?>: <?php _e('JJJJ-MM-TT','teachpress'); ?></em></td>
          </tr>
          <tr>
            <th><?php _e('E-Mail','teachpress'); ?></th>
            <td><input name="email" type="text" id="email" size="50" /></td>
          </tr>
         </thead> 
        </table>
    <p>
      <input name="insert" type="submit" id="std_einschreiben" onclick="teachpress_validateForm('wp_id','','RisNum','matrikel','','RisNum','vorname','','R','nachname','','R','urzkurz','','R','gebdat','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('Erstellen','teachpress'); ?>" class="teachpress_button"/>
      <input name="reset" type="reset" id="reset" value="Reset" class="teachpress_button"/>
    </p>
</fieldset>
</form>   
</div>
<?php } ?>