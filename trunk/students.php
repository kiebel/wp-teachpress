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
$suche = htmlentities(utf8_decode($_GET[suche])); 
$studenten = htmlentities(utf8_decode($_GET[studenten]));

if (isset($kontrolle)) {
	delete_student_admin ($checkbox, $user_ID);
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
	    <th>&nbsp;</th>
        <th><?php _e('Registr.-Number','teachpress'); ?></th>
        <th><?php _e('Last name','teachpress'); ?></th>
        <th><?php _e('First name','teachpress'); ?></th>
        <th><?php _e('Course of studies','teachpress'); ?></th>
        <th><?php _e('Number of terms','teachpress'); ?></th>
        <th><?php _e('Date of birth','teachpress'); ?></th>
        <th><?php _e('User account','teachpress'); ?></th>
        <th><?php _e('E-Mail','teachpress'); ?></th>
	 </tr>
    </thead>
    <tbody> 
<?php
	if ($suche != "") {
		$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE matrikel like '%$suche%' OR wp_id like '%$suche%' OR vorname LIKE '%$suche%' OR nachname LIKE '%$suche%' ORDER BY matrikel";
	}
	else {
		if ($studenten == '1') {
			$abfrage = "SELECT * FROM " . $teachpress_stud . " ORDER BY matrikel";
		}
		else {
			$abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE studiengang = '$studenten' ORDER BY matrikel";
		}
	}
	$test = tp_query($abfrage);
	// Test ob Eintraege vorhanden
	if ($test == 0) { ?>
			<tr>
			  <td colspan="9"><strong><?php _e('Sorry, no entries matched your criteria.','teachpress'); ?></strong></td>
			</tr>
	<?php }
	else {
		$row3 = tp_results($abfrage);
		foreach($row3 as $row3) { ?>
			  <tr>
				<th class="check-column"><input type="checkbox" name="checkbox[]" id="checkbox" value="<?php echo "$row3->wp_id" ?>"/></th>
				<td><a href="admin.php?page=teachpress/editstudent.php&student_ID=<?php echo "$row3->wp_id" ?>&suche=<?php echo "$suche" ?>&studenten=<?php echo "$studenten" ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo "$row3->matrikel" ?></a></td>
				<td><?php echo "$row3->nachname" ?></td>
				<td><?php echo "$row3->vorname" ?></td>
				<td><?php echo "$row3->studiengang" ?></td>
				<td><?php echo "$row3->fachsemester" ?></td>
				<td><?php echo "$row3->gebdat" ?></td>
				<td><?php echo "$row3->urzkurz" ?></td>
				<td><a href="mailto:<?php echo "$row3->email" ?>" title="E-Mail senden"><?php echo "$row3->email" ?></a></td>
			  </tr>
		<?php } 
	}
	?> 
    </tbody>
    </table>
<?php
	if ($studenten !="0") { ?>
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