<?php
/* Aktuelle Kursuebersicht fuer Frontend
 * from display_courses.php (GET):
 * @param $semester(String) - term
*/
function teachpress_course_overview() {
global $teachpress_ver; 
global $teachpress_einstellungen; 
$sem = tp_get_option('sem');
// Falls Semester vom User gewaehlt
$semester = tp_sec_var($_GET[semester]);	
if ($semester != "") {
	$sem = $semester;
}
?>
<div id="anzeigelvs">
	<h2><?php _e('Courses for the','teachpress'); ?> <?php echo"$sem" ;?></h2>
    <form name="lvs" method="get">
        <div class="tp_auswahl">
            <?php _e('Select the term','teachpress'); ?>
            <select name="semester">
                <?php
                $rowsem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id DESC";
                $rowsem = tp_results($rowsem);
                foreach($rowsem as $rowsem) { 
					if ($rowsem->wert == $sem) {
						$current = 'selected="selected"' ;
					}
					else {
						$current = '';
					}
					echo '<option value="' . $rowsem->wert . '" ' . $current . '>' . $rowsem->wert . '</option>';
                } ?>
            </select>  
            <input type="submit" name="start" value="<?php _e('show','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/>
        </div>
        <?php
            $row = "Select name, bemerkungen, rel_page FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' ORDER BY name";
			$test = tp_query($row);
			if ($test != 0){
				$row = tp_results($row);
				foreach($row as $row) { 
					echo '<div class="tp_lvs_container">';
					echo '<div class="tp_lvs_name"><a href="' . get_permalink($row->rel_page) . '" title ="' . $row->name . '"><strong>' . $row->name . '</strong></a></div>';
                    echo '<div class="tp_lvs_comments">' . $row->bemerkungen . '</div>';
					echo '</div>';
				} 
		   }
		   else {
		   		echo '<p class="teachpress_message"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</p>';
		   }?>
    </form>
</div>
<?php } ?>