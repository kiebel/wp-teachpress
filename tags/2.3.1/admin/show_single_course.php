<?php 
/* Single course overview
 * $_GET parameters:
 * @param $course_ID (INT) - course ID
 * @param $sem (String) - semester, from show_courses.php
 * @param $search (String) - search string, from show_courses.php
*/
function tp_show_single_course_page() {
	
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_stud; 
	global $teachpress_settings; 
	global $teachpress_signup;
	// WordPress
	global $user_ID;
	get_currentuserinfo();
	// form
	$checkbox = $_GET[checkbox];
	$delete = $_GET[loeschen];
	$speichern = $_GET[speichern];
	$aufnehmen = $_GET[aufnehmen];
	$course_ID = tp_sec_var($_GET[lvs_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$sem = tp_sec_var($_GET[sem]);
	// teachPress settings
	$field1 = tp_get_option('regnum');
	$field2 = tp_get_option('studies');
	?>
	<div class="wrap">
	<form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
    <input name="page" type="hidden" value="teachpress/teachpress.php">
    <input name="action" type="hidden" value="show" />
    <input name="lvs_ID" type="hidden" value="<?php echo $course_ID; ?>" />
    <input name="sem" type="hidden" value="<?php echo $sem; ?>" />
    <input name="search" type="hidden" value="<?php echo $search; ?>" />
	<?php
	// Event handler
	if ( isset($aufnehmen) ) {
		tp_add_from_waitinglist($checkbox);
		$message = __('Participant added','teachpress');
		tp_get_message($message);	
		}	 
	if ( isset($delete)) {
		tp_delete_registration($checkbox, $user_ID);
		$message = __('Enrollments deleted','teachpress');
		tp_get_message($message);	
	}
	
	// course data
	$row = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$course_ID'";
	$daten = $wpdb->get_row($row, ARRAY_A);
	
	// enrollments
	$sql = "SELECT st.matriculation_number, st.firstname, st.lastname, st.course_of_studies, st.userlogin, st.email , s.date, s.con_id, s.waitinglist
				FROM " . $teachpress_signup . " s 
				INNER JOIN " . $teachpress_courses . " c ON c.course_id=s.course_id
				INNER JOIN " . $teachpress_stud . " st ON st.wp_id=s.wp_id";	
	$enrollments = $sql . " WHERE c.course_id = '$course_ID' AND s.waitinglist = '0' ORDER BY st.lastname ASC";		
	$enrollments = $wpdb->get_results($enrollments, ARRAY_A);
	$count_enrollments = count($enrollments);
	
	// waitinglist
	$waitinglist = $sql . " WHERE c.course_id = '$course_ID' AND s.waitinglist = '1' ORDER BY s.date ASC";
	$waitinglist = $wpdb->get_results($waitinglist, ARRAY_A);
	$count_waitinglist = count($waitinglist);

	// course parent
	$row = "SELECT `course_id`, `name` FROM " . $teachpress_courses . " WHERE `parent` = '0' AND `course_id` = '" . $daten["parent"] . "'";
	$parent = $wpdb->get_row($row, ARRAY_A);
	
	if ($speichern != __('save','teachpress')) { ?>
		<p>
		<a href="admin.php?page=teachpress/teachpress.php&amp;sem=<?php echo $sem; ?>&amp;search=<?php echo $search; ?>" class="teachpress_back" title="<?php _e('back to the overview','teachpress'); ?>">&larr; <?php _e('back','teachpress'); ?></a><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=<?php echo $course_ID; ?>&amp;sem=<?php echo $sem; ?>&amp;search=<?php echo $search; ?>&amp;action=list" class="teachpress_back" title="<?php _e('create an attendance list','teachpress'); ?>"><?php _e('create attendance list','teachpress'); ?></a>
		  <select name="export" id="export" onchange="teachpress_jumpMenu('parent',this,0)" class="teachpress_select">
			<option><?php _e('Export as','teachpress'); ?> ... </option>
			<option value="<?php echo WP_PLUGIN_URL; ?>/teachpress/export.php?lvs_ID=<?php echo $course_ID; ?>&type=csv"><?php _e('csv-file','teachpress'); ?></option>
			<option value="<?php echo WP_PLUGIN_URL; ?>/teachpress/export.php?lvs_ID=<?php echo $course_ID; ?>&type=xls"><?php _e('xls-file','teachpress'); ?></option>
		  </select>
		  <select name="mail" id="mail" onchange="teachpress_jumpMenu('parent',this,0)" class="teachpress_select">
			<option><?php _e('E-Mail to','teachpress'); ?> ... </option>
			<option value="mailto:<?php foreach($enrollments as $e) { echo $e["email"] . ' ,'; } ?>"><?php _e('registered participants','teachpress'); ?></option>
			<option value="mailto:<?php foreach($waitinglist as $w) { echo $w["email"] . ' ,'; } ?>"><?php _e('participants in waiting list','teachpress'); ?></option>
			<option value="mailto:<?php foreach($enrollments as $e) { echo $e["email"] . ' ,'; } foreach($waitinglist as $w) { echo $w["email"] . ' ,'; } ?>"><?php _e('all participants','teachpress'); ?></option>
		  </select>
		</p>
	  <?php } 
	// define course name
	if ($daten["parent"] != 0) {
		if ($parent["course_id"] == $daten["parent"]) {
			$parent_name = $parent["name"];
			// if parent name == child name
			if ($parent_name == $daten["name"]) {
				$parent_name = "";
			}
		}
	}
	else {
		$parent_name = "";
	}
	?>
	<h2 style="padding-top:5px;"><?php echo stripslashes($parent_name); ?> <?php echo stripslashes($daten["name"]); ?> <?php echo $daten["semester"]; ?> <span class="tp_break">|</span> <small><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=<?php echo $course_ID; ?>&amp;sem=<?php echo $sem; ?>&amp;search=<?php echo $search; ?>&amp;action=edit" class="teachpress_link" style="cursor:pointer;"><?php _e('edit','teachpress'); ?></a></small></h2>
	<div style="min-width:780px; width:100%; max-width:1100px;">
	<div style="width:24%; float:right; padding-left:1%; padding-bottom:1%;">
        <table border="1" cellspacing="0" cellpadding="0" class="widefat" id="teachpress_edit">
            <thead>
              <tr>
                <th colspan="4"><?php _e('Meta Information','teachpress'); ?></th>
              </tr>
              <tr>  
                <td><strong><?php _e('ID','teachpress'); ?></strong></td>
                <td><?php echo $daten["course_id"]; ?></td>   
                <td><strong><?php _e('Parent-ID','teachpress'); ?></strong></td>
                <td><?php echo $daten["parent"]; ?></td>
              </tr>  
              <tr>  
                <td><strong><?php _e('Visibility','teachpress'); ?></strong></td>
                <td colspan="3">
				<?php 
					if ( $daten["visible"] == 1 ) {
						_e('normal','teachpress');
				  	}
					elseif ( $daten["visible"] == 2 ) {
						_e('extend','teachpress');
					}
				  	else {
				  		_e('invisible','teachpress');
				  	} 
				?></td>
              </tr>
              <tr>
                <th colspan="4"><?php _e('Enrollments','teachpress'); ?></th>
              </tr>
              <?php if ($daten["start"] != '0000-00-00 00:00:00' && $daten["end"] != '0000-00-00 00:00:00') {?>
              <tr>
                <td colspan="2"><strong><?php _e('Start','teachpress'); ?></strong></td>
                <td colspan="2"><?php echo substr($daten["start"],0,strlen($daten["start"])-3); ?></td>
              </tr>  
              <tr>  
                <td colspan="2"><strong><?php _e('End','teachpress'); ?></strong></td>
                <td colspan="2"><?php echo substr($daten["end"],0,strlen($daten["end"])-3); ?></td>
              </tr>
              <tr>
                <td><strong><?php _e('Places','teachpress'); ?></strong></th>
                <td><?php echo $daten["places"]; ?></td>  
                <td><strong><?php _e('free places','teachpress'); ?></strong></td>
                <td><?php echo $daten["fplaces"]; ?></td>
              </tr>  
              <?php } else {?>
              <tr>
                <td colspan="4"><?php _e('none','teachpress'); ?></td>
              </tr>  
              <?php } ?>  
              </thead>
        </table>
	</div>
	<div style="width:75%; float:left; padding-bottom:10px;">
        <table border="1" cellspacing="0" cellpadding="0" class="widefat">
            <thead>
            <tr>
                <th width="150px"><?php _e('Type','teachpress'); ?></th>
                <td><?php echo stripslashes($daten["type"]); ?></td>
            </tr>
            <tr>
                <th><?php _e('Lecturer','teachpress'); ?></th>
                <td colspan="3"><?php echo stripslashes($daten["lecturer"]); ?></td>
            </tr>
            <tr>
                <th><?php _e('Room','teachpress'); ?></th>
                <td colspan="3"><?php echo stripslashes($daten["room"]); ?></td>
            </tr>
            <tr>
                <th><?php _e('Date','teachpress'); ?></th>
                <td colspan="3"><?php echo stripslashes($daten["date"]); ?></td>
            </tr>
              <tr>
                <th><?php _e('Comment','teachpress'); ?></th>
                <td colspan="3"><?php echo stripslashes($daten["comment"]); ?></td>
              </tr>
              <tr>
                <th><?php _e('Related page','teachpress'); ?></th>
                <td colspan="3"><?php if ( $daten["rel_page"] != 0) {echo '<a href="' . get_permalink( $daten["rel_page"] ) . '" target="_blank" class="teachpress_link">' . get_permalink( $daten["rel_page"] ) . '</a>'; } else { _e('none','teachpress'); } ?></td>
              </tr>
              </thead>
        </table>
	</div>
	<div style="min-width:780px; width:100%; max-width:1100px;">
        <table class="widefat">
         <thead>
          <tr>
            <th>&nbsp;</th>
            <th><?php _e('Last name','teachpress'); ?></th>
            <th><?php _e('First name','teachpress'); ?></th>
            <?php
            if ($field2 == '1') {
                echo '<th>' .  __('Course of studies','teachpress') . '</th>';
            }	
            ?>
            <th><?php _e('User account','teachpress'); ?></th>
            <th><?php _e('E-Mail','teachpress'); ?></th>
            <th><?php _e('Registered at','teachpress'); ?></th>
          </tr>
         </thead>  
         <tbody>
        <?php
        if ($count_enrollments == 0) {
            echo '<tr><td colspan="8"><strong>' . __('No entries','teachpress') . '</strong></td></tr>';
        }
        else {
            // all registered students for the course
            foreach ($enrollments as $enrollments) {
				echo '<tr>';
				echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $enrollments["con_id"] . '"/></th>';
				echo '<td>' . stripslashes($enrollments["lastname"]) . '</td>';
				echo '<td>' . stripslashes($enrollments["firstname"]) . '</td>';
				if ($field2 == '1') {
					echo '<td>' . stripslashes($enrollments["course_of_studies"]) . '</td>';
				}
				echo '<td>' . stripslashes($enrollments["userlogin"]) . '</td>';
				echo '<td><a href="mailto:' . stripslashes($enrollments["email"]) . '" title="' . __('send E-Mail','teachpress') . '">' . stripslashes($enrollments["email"]) . '</a></td>';
				echo '<td>' . $enrollments["date"] . '</td>';
				echo '</tr>';
            } 
        }?>
        </tbody>
		</table>
		<?php
        // waitinglist
        if ($count_waitinglist != 0) { ?>
            <h3><?php _e('Waitinglist','teachpress'); ?></h3>
            <table class="widefat">
             <thead>
              <tr>
                <th>&nbsp;</th>
                <th><?php _e('Last name','teachpress'); ?></th>
                <th><?php _e('First name','teachpress'); ?></th>
                <?php if ($field2 == '1') {?>
                <th><?php _e('Course of studies','teachpress'); ?></th>
                <?php } ?>
                <th><?php _e('User account','teachpress'); ?></th>
                <th><?php _e('E-Mail','teachpress'); ?></th>
                <th><?php _e('Registered at','teachpress'); ?></th>
              </tr>
             </thead>  
             <tbody> 
             <?php
            foreach ( $waitinglist as $waitinglist ) { ?>
             <tr>
                <th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $waitinglist["con_id"]; ?>"/></th>
                <td><?php echo stripslashes($waitinglist["lastname"]); ?></td>
                <td><?php echo stripslashes($waitinglist["firstname"]); ?></td>
                <?php if ($field2 == '1') {?>
                <td><?php echo stripslashes($waitinglist["course_of_studies"]); ?></td>
                <?php } ?>
                <td><?php echo stripslashes($waitinglist["userlogin"]); ?></td>
                <td><a href="mailto:<?php echo stripslashes($waitinglist["email"]); ?>" title="<?php _e('send E-Mail','teachpress'); ?>"><?php echo stripslashes($waitinglist["email"]); ?></a></td>
                <td><?php echo stripslashes($waitinglist["date"]); ?></td>
             </tr> 
            <?php }?>
            </tbody>
            </table>
        <?php  } ?>      
	<table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
	  <tr>
		<td><?php if ($count_waitinglist != 0) { ?><input name="aufnehmen" type="submit" value="+ <?php _e('ingest','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/><?php } ?></td>
		<td><input name="loeschen" type="submit" value="<?php _e('delete enrollment','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/></td>
	  </tr>
	</table>
	</div>
	</form>
	<script type="text/javascript" charset="utf-8">
		$(function() {
			$('#start').datepick({showOtherMonths: true, firstDay: 1, 
			renderer: $.extend({}, $.datepick.weekOfYearRenderer), 
			onShow: $.datepick.showStatus, showTrigger: '#calImg',
			dateFormat: 'yyyy-mm-dd', yearRange: '2008:c+5'}); 
			
			$('#end').datepick({showOtherMonths: true, firstDay: 1, 
			renderer: $.extend({}, $.datepick.weekOfYearRenderer), 
			onShow: $.datepick.showStatus, showTrigger: '#calImg',
			dateFormat: 'yyyy-mm-dd', yearRange: '2008:c+5'}); 
			});
	</script>
	</div>
    </div>
<?php } ?>