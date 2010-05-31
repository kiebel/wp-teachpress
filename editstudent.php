<?php
/* Editiieren der Datensätze von Studenten
 * from studenten.php
 * @param $student_ID (Int)
 * @param $suche (String)
 * @param $studenten (String)
*/ 
function teachpress_editstudent_page () { 
?> 
<div class="wrap">
<?php
// Eingangsparameter
$student = $_GET[student_ID];
$studenten = $_GET[studenten];
$suche = $_GET[suche];
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_kursbelegung;
global $teachpress_einstellungen;
 // Formular-Einträge
$checkbox = $_GET[checkbox];
$delete = $_GET[loeschen];
$speichern = $_GET[speichern];
	
$wp_id = tp_sec_var($_GET[wp_id], 'integer');
$matrikel = tp_sec_var($_GET[matrikel], 'integer');
$vorname = tp_sec_var($_GET[vorname]);
$nachname = tp_sec_var($_GET[nachname]);
$studiengang = tp_sec_var($_GET[studiengang]);
$fachsemester = tp_sec_var($_GET[fachsemester]);
$urzkurz = tp_sec_var($_GET[urzkurz]);
$gebdat = tp_sec_var($_GET[gebdat]);
$email = tp_sec_var($_GET[email]);
// WP User ID
global $user_ID;
get_currentuserinfo();
// Prüfen welche Checkbox genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
if ( isset($delete)) {
	tp_delete_registration($checkbox, $user_ID);
	$message = __('Enrollment deleted','teachpress');
	$site = 'admin.php?page=teachpress/editstudent.php&student_ID=' . $student . '&suche=' . $suche . '&studenten=' . $studenten . '';
	tp_get_message($message, $site);
}
if ( isset($speichern)) {
	tp_change_student($wp_id, $vorname, $nachname, $studiengang, $gebdat, $email, $fachsemester, $matrikel, $user_ID);
	$message = __('Changes successful','teachpress');
	$site = 'admin.php?page=teachpress/editstudent.php&student_ID=' . $wp_id . '&suche=' . $suche . '&studenten=' . $studenten . '';
	tp_get_message($message, $site);
}
if (!isset($bearbeiten) && !isset($delete) && !isset($speichern)) {
	echo '<p><a href="admin.php?page=teachpress/students.php&suche=' . $suche . '&studenten=' . $studenten . '" class="teachpress_back" title="' . __('back to the overview','teachpress') . '">&larr; ' . __('back','teachpress') . ' </a></p>';
}
?>
<form name="personendetails" method="get" action="<?php echo $PHP_SELF ?>">
<input name="page" type="hidden" value="teachpress/editstudent.php">
<input name="student_ID" type="hidden" value="<?php echo"$student" ?>">
<input name="studenten" type="hidden" value="<?php echo"$studenten" ?>">
<input name="suche" type="hidden" value="<?php echo"$suche" ?>">
<?php
	$row3 = "SELECT * FROM " . $teachpress_stud . " WHERE wp_id = '$student'";
	$row3 = tp_results($row3);
	foreach($row3 as $row3){ ?>
    	<h2 style="padding-top:0px;"><?php echo "$row3->vorname" ?> <?php echo "$row3->nachname" ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('daten_aendern')" id="daten_aendern_2" style="cursor:pointer;"><?php _e('edit','teachpress'); ?> </a></small></h2>
          <div id="daten_aendern" style="display:none; padding-top:5px; padding-bottom:5px; margin:5px;">
            <fieldset style="border:1px solid silver; padding:10px; width:650px;">
              <legend><?php _e('Edit Data','teachpress'); ?></legend>
                <table class="widefat">
                 <thead>
                 <tr>
                    <th><label for="wp_id"><?php _e('WordPress User-ID','teachpress'); ?></label></th>
                    <td style="text-align:left;"><input name="wp_id" type="text" id="wp_id" value="<?php echo "$row3->wp_id" ?>" readonly="true"/></td>
                  </tr>
                 <?php
                $field1 = tp_get_option('regnum');
                if ($field1 == '1') { ?>
                  <tr>
                    <th><label for="matrikel"><?php _e('Registr.-Number','teachpress'); ?></label></th>
                    <td style="text-align:left;"><input name="matrikel" type="text" id="matrikel" value="<?php echo "$row3->matrikel" ?>" readonly="true"/></td>
                  </tr>
				<?php }?>
                  <tr>
                    <th><label for="vorname"><?php _e('First name','teachpress'); ?></label></th>
                    <td><input name="vorname" type="text" id="vorname" value="<?php echo "$row3->vorname" ?>" size="40"/></td>
                  </tr>
                  <tr>
                    <th><label for="nachname"><?php _e('Last name','teachpress'); ?></label></th>
                    <td><input name="nachname" type="text" id="nachname" value="<?php echo "$row3->nachname" ?>" size="40"/></td>
                  </tr>
                  <tr>
				<?php
                $field2 = tp_get_option('studies');
                if ($field2 == '1') { ?>
                    <th><label for="studiengang"><?php _e('Course of studies','teachpress'); ?></label></th>
                    <td>
                    <select name="studiengang" id="studiengang">
                      <?php
                      $stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
                      $stud = tp_results($stud);
                      foreach ($stud as $stud) {
					  	if ($stud->wert == $row3->studiengang) {
							$current = 'selected="selected"' ;
						}
						else {
							$current = '' ;
						}
						echo '<option value="' . $stud->wert . '" ' . $current . '>' . $stud->wert . '</option>';
                      } ?>
                    </select></td>
                  </tr>
                <?php } ?>
                <?php
                $field3 = tp_get_option('termnumber');
                if ($field3 == '1') { ?>
                  <tr>
                    <th><label for="fachsemester"><?php _e('Number of terms','teachpress'); ?></label></th>
                    <td style="text-align:left;">
                    <select name="fachsemester" id="fachsemester">
                      <?php
						for ($i=1; $i<20; $i++) {
						if ($i == $row3->fachsemester) {
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
                    <th><label for="urzkurz"><?php _e('User account','teachpress'); ?></label></th>
                    <td style="text-align:left;"><input name="urzkurz" type="text" id="urzkurz" value="<?php echo "$row3->urzkurz" ?>" readonly="true"/></td>
                  </tr>
                <?php
                $field4 = tp_get_option('birthday');
                if ($field4 == '1') { ?>  
                  <tr>
                    <th><label for="gebdat"><?php _e('Date of birth','teachpress'); ?></label></th>
                    <td><input name="gebdat" type="text" id="gebdat" value="<?php echo "$row3->gebdat" ?>" size="15"/>
                      <em><?php _e('Format: JJJJ-MM-TT','teachpress'); ?></em></td>
                  </tr>
                <?php } ?>  
                  <tr>
                    <th><label for="email"><?php _e('E-Mail','teachpress'); ?></label></th>
                    <td><input name="email" type="text" id="email" value="<?php echo "$row3->email" ?>" size="50" readonly="true"/></td>
                  </tr>
                 </thead> 
                </table>
            <?php 
			if ($field1 != '1') {
            	echo '<input name="matrikel" type="hidden" id="matrikel" value="' . $row3->matrikel . '" />';
			}
            if ($field2 != '1') {
            	echo '<input name="studiengang" type="hidden" id="studiengang" value="' . $row3->studiengang . '" />';
			}
            if ($field3 != '1') {
            	echo '<input name="fachsemester" type="hidden" id="fachsemester" value="' . $row3->fachsemester . '" />';
			}
            if ($field4 != '1') {
            	echo '<input name="gebdat" type="hidden" id="gebdat" value="' . $row3->gebdat . '" />';
			} ?>     
            <table border="0" cellspacing="7" cellpadding="0">
                  <tr>
                    <td><input name="speichern" type="submit" id="teachpress_einzel_change" onclick="teachpress_validateForm('wp_id','','RisNum','matrikel','','RisNum','vorname','','R','nachname','','R','urzkurz','','R','gebdat','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('save','teachpress'); ?>" class="teachpress_button"/></td>
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
				echo '<th>' . __('Registr.-Number','teachpress') . '</th>';
				echo '<td>' . $row3->matrikel . '</td>';
				echo '</tr>';
            }
            if ($field2 == '1') {
            	echo '<tr>';
              	echo '<th>' . __('Course of studies','teachpress') . '</th>';
             	echo '<td>' . $row3->studiengang . '</td>';
            	echo '</tr>';
            }
            if ($field3 == '1') { 
            	echo '<tr>';
              	echo '<th>' . __('Number of terms','teachpress') . '</th>';
            	echo '<td>' . $row3->fachsemester . '</td>';
            	echo '</tr>';
            }
            if ($field4 == '1') {
            	echo '<tr>';
              	echo '<th>' . __('Date of birth','teachpress') . '</th>';
            	echo '<td>' . $row3->gebdat . '</td>';
            	echo '</tr>';
            }
            echo '<tr>';
            echo '<th>' . __('User account','teachpress') . '</th>';
            echo '<td>' . $row3->urzkurz . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo'<th>' . __('E-Mail','teachpress') . '</th>';
            echo '<td><a href="mailto:' . $row3->email . '" title="' . __('Send E-Mail to','teachpress') . ' ' . $row3->vorname . ' ' . $row3->nachname . '">' . $row3->email . '</a></td>';
            echo '</tr>';
			?>
           </thead>   
          </table>
          </div>
<?php } ?> 
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
		$row2= "SELECT " . $teachpress_stud . ".wp_id, " . $teachpress_stud . ".vorname, " . $teachpress_stud . ".nachname, " . $teachpress_ver . ".name, " . $teachpress_ver . ".vtyp, " . $teachpress_ver . ".termin, " . $teachpress_ver . ".parent," . $teachpress_kursbelegung . ".belegungs_id, " . $teachpress_kursbelegung . ".datum
					FROM " . $teachpress_kursbelegung . "
					INNER JOIN " . $teachpress_ver . " ON " . $teachpress_ver . ".veranstaltungs_id=" . $teachpress_kursbelegung . ".veranstaltungs_id
					INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id= " . $teachpress_kursbelegung . ".wp_id
					WHERE " . $teachpress_kursbelegung . ".wp_id = '$student'";
		$row2 = tp_results($row2);
		foreach($row2 as $row2) {
			if ($row2->parent != 0) {
				$parent_name = tp_var("SELECT name FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row2->parent'");
				$parent_name = $parent_name . " ";
			}
			else {
				$parent_name = "";
			}
			// Ausgabe der Infos zur gewählten LVS mit integriertem Aenderungsformular
			echo '<tr>';
			echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $row2->belegungs_id . '"/></th>';
			echo '<td>' . $row2->belegungs_id . '</td>';
			echo '<td>' . $row2->datum . '</td>';
			echo '<td>' . $parent_name . $row2->name . '</td>';
			echo '<td>' . $row2->vtyp . '</td>';
            echo '<td>' . $row2->termin . '</td>';
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