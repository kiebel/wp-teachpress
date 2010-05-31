<?php 
/* Suche nach Studenten, Anzeige von Uebersichtslisten
 *
 * from editstudent.php (GET), students.php (GET):
 * @param $suche - String
 * @param $studenten - String
*/
function teachpress_students_page() { 

global $teachpress_stud;
global $user_ID;
get_currentuserinfo();
$checkbox = $_GET[checkbox];
$kontrolle = $_GET[kontrolle];
$suche = tp_sec_var($_GET[suche]); 
$studenten = tp_sec_var($_GET[studenten]);

if (isset($kontrolle)) {
	tp_delete_student($checkbox, $user_ID);
	$message = __('Students deleted','teachpress');
	$site = 'admin.php?page=teachpress/students.php&suche=' . $suche . '&studenten=' . $studenten . '';
	tp_get_message($message, $site);
}
?>
<div class="wrap" style="padding-top:10px;">
  <form name="suche" method="get" action="<?php echo $PHP_SELF ?>">
  <input name="page" type="hidden" value="teachpress/students.php" />
  <table border="0" cellpadding="0" cellspacing="0" style="padding-top:10px; padding-bottom:10px; float:right;">
  <tr>
  	<td><?php if ($suche != "") { ?><a href="admin.php?page=teachpress/students.php" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Cancel the search','teachpress'); ?>">&crarr;</a><?php } ?></td>
    <td><input name="suche" type="text" value="<?php echo "$suche" ?>"/></td>
    <td><input name="go" type="submit" value="<?php _e('search','teachpress'); ?>" id="teachpress_suche_senden" class="teachpress_button"/></td>
  </tr>
</table>
  <table cellpadding="0" cellspacing="5" id="auswahllisten" style="padding-top:5px; padding-bottom:5px;">
    <tr>
      <td><label>
        <select name="studenten" id="studenten">
        <?php
			if ($studenten != "" && $suche == "") {
				if ($studenten == 1) {
					echo '<option value="1">' . __('All students','teachpress') . '</option>';
					echo '<option>------</option>';
				}
				else {
					echo '<option value="' . $studenten . '">' . $studenten . '</option>';
					echo '<option>------</option>';
				}
            }    
			else {
				echo '<option value="0">- ' . __('please select','teachpress') . ' -</option>';
			}			
		?>
          <option value="1"><?php _e('All students','teachpress'); ?></option>
           <?php
			$row1 = "SELECT DISTINCT studiengang FROM " . $teachpress_stud . " ORDER BY studiengang";
			$row1 = tp_results($row1);
			foreach($row1 as $row1){ ?>
                <option value="<?php echo"$row1->studiengang" ?>"><?php echo"$row1->studiengang" ?></option>
			<?php } ?>
        </select>
      </label></td>
      <td><label>
        <input name="anzeigen" type="submit" id="teachpress_suche_senden" value="<?php _e('show','teachpress'); ?>" class="teachpress_button"/>
      </label></td>
    </tr>
  </table>
<table border="1" cellpadding="5" cellspacing="0" class="widefat">
	<thead>
	 <tr>
     	<?php
	    echo '<th>&nbsp;</th>'; 
		$field1 = tp_get_option('regnum');
        if ($field1 == '1') {
            echo '<th>' .  __('Registr.-Number','teachpress') . '</th>';
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
	if ($field1 == '1') {
		$order = 'matrikel';
	}
	else {
		$order = 'wp_id';
	}
	if ($suche != "") {
		$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE matrikel like '%$suche%' OR wp_id like '%$suche%' OR vorname LIKE '%$suche%' OR nachname LIKE '%$suche%' ORDER BY " . $order . "";
	}
	else {
		if ($studenten == '1') {
			$abfrage = "SELECT * FROM " . $teachpress_stud . " ORDER BY " . $order . "";
		}
		else {
			$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE studiengang = '$studenten' ORDER BY " . $order . "";
		}
	}
	$test = tp_query($abfrage);
	// Test ob Eintraege vorhanden
	if ($test == 0) { 
		echo '<tr><td colspan="9"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
	}
	else {
		$row3 = tp_results($abfrage);
		foreach($row3 as $row3) { 
			echo '<tr>';
			echo '<th class="check-column"><input type="checkbox" name="checkbox[]" id="checkbox" value="' . $row3->wp_id . '"/></th>';
			echo '<td><a href="admin.php?page=teachpress/editstudent.php&student_ID=' . $row3->wp_id . '&suche=' . $suche . '&studenten=' . $studenten . '" class="teachpress_link" title="' . __('Click to edit','teachpress') . '">';
			if ($field1 == '1') {
				echo '' . $row3->matrikel . '</a></td>';
			}
			else {
				echo '' . $row3->wp_id . '</a></td>';
			}
			echo '<td>' . $row3->nachname . '</td>';
			echo '<td>' . $row3->vorname . '</td>';
            if ($field2 == '1') {
				echo '<td>' . $row3->studiengang . '</td>';
			} 
			if ($field3 == '1') {
				echo '<td>' . $row3->fachsemester . '</td>';
			} 
			if ($field4 == '1') {
				echo '<td>' . $row3->gebdat . '</td>';
			}
			echo '<td>' . $row3->urzkurz . '</td>';
			echo '<td><a href="mailto:' . $row3->email . '" title="E-Mail senden">' . $row3->email . '</a></td>';
			echo '</tr>';
		} 
	}
	?> 
    </tbody>
    </table>
<?php
	if ($studenten !="0" || $suche != "") { ?>
        <table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
          <tr>
            <td><?php _e('Delete students','teachpress'); ?> </td>
            <td><input name="kontrolle" type="checkbox" id="kontrolle" value="delete" title="<?php _e('Markup this field and click on the submit button to delete the student.','teachpress'); ?>"/> 
              </td>
            <td> <input type="submit" name="delete" id="delete_student" value="<?php _e('submit', 'teachpress'); ?>" class="teachpress_button"/></td>  
          </tr>
        </table>
	<?php } ?>
    </form>
    </div>
    <?php
} ?>