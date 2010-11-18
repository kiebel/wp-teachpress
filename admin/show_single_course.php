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
	$row = "SELECT " . $teachpress_stud . ".matriculation_number, " . $teachpress_stud . ".firstname, " . $teachpress_stud . ".lastname, " . $teachpress_stud . ".course_of_studies,  " . $teachpress_stud . ".userlogin, " . $teachpress_stud . ".email , " . $teachpress_signup . ".date, " . $teachpress_signup . ".con_id, " . $teachpress_signup . ".waitinglist
				FROM " . $teachpress_signup . " 
				INNER JOIN " . $teachpress_courses . " ON " . $teachpress_courses . ".course_id=" . $teachpress_signup . ".course_id
				INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id=" . $teachpress_signup . ".wp_id
				WHERE " . $teachpress_courses . ".course_id = '$course_ID'
				ORDER BY " . $teachpress_stud . ".matriculation_number";
	$row = $wpdb->get_results($row);
	$counter2 = 0;
	foreach($row as $row){
		$daten2[$counter2]["matriculation_number"] = $row->matriculation_number;
		$daten2[$counter2]["firstname"] = stripslashes($row->firstname);
		$daten2[$counter2]["lastname"] = stripslashes($row->lastname);
		$daten2[$counter2]["course_of_studies"] = $row->course_of_studies;
		$daten2[$counter2]["userlogin"] = $row->userlogin;
		$daten2[$counter2]["email"] = $row->email;
		$daten2[$counter2]["date"] = $row->date;
		$daten2[$counter2]["con_id"] = $row->con_id;
		$daten2[$counter2]["waitinglist"] = $row->waitinglist;
		$counter2++;
	}
	// available course parents
	$row = "SELECT course_id, name, semester FROM " . $teachpress_courses . " WHERE parent='0' AND course_id != '$veranstaltung' ORDER BY semester DESC, name";
	$row = $wpdb->get_results($row);
	$counter3 = 0;
	foreach($row as $row){
		$par[$counter3]["id"] = $row->course_id;
		$par[$counter3]["name"] = $row->name;
		$par[$counter3]["semester"] = $row->semester;
		$counter3++;
	}
	
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
			<option value="mailto:<?php for($i=0; $i<$counter2; $i++) { if ($daten2[$i]["waitinglist"]== 0 ) { ?><?php echo $daten2[$i]["email"]; ?> ,<?php } } ?>"><?php _e('registered participants','teachpress'); ?></option>
			<option value="mailto:<?php for($i=0; $i<$counter2; $i++) { if ($daten2[$i]["waitinglist"]== 1 ) { ?><?php echo $daten2[$i]["email"]; ?> ,<?php } } ?>"><?php _e('participants in waiting list','teachpress'); ?></option>
			<option value="mailto:<?php for($i=0; $i<$counter2; $i++) { echo '' . $daten2[$i]["email"] . ' ,'; } ?>"><?php _e('all participants','teachpress'); ?></option>
		  </select>
		</p>
	  <?php } 
		// define course name
		if ($daten["parent"] != 0) {
			for ($x=0; $x < $counter3; $x++) {
				if ($par[$x]["id"] == $daten["parent"]) {
					$parent_name = $par[$x]["name"];
					// if parent name == child name
					if ($parent_name == $daten["name"]) {
						$parent_name = "";
					}
				}
			}
		}
		else {
			$parent_name = "";
		}
		?>
		<h2 style="padding-top:5px;"><?php echo stripslashes($parent_name); ?> <?php echo stripslashes($daten["name"]); ?> <?php echo $daten["semester"]; ?> <span class="tp_break">|</span> <small><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=<?php echo $course_ID; ?>&amp;sem=<?php echo $sem; ?>&amp;search=<?php echo $search; ?>&amp;action=edit" class="teachpress_link" style="cursor:pointer;"><?php _e('edit','teachpress'); ?></a></small></h2>
		<div id="einschreibungen" style="padding:5px;">
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
				<td colspan="3"><?php if ($daten["visible"] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
			  </tr>
			  <tr>
				<th colspan="4"><?php _e('Enrollments','teachpress'); ?></th>
			  </tr>
              <?php if ($daten["start"] != '0000-00-00' && $daten["end"] != '0000-00-00') {?>
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
					<th><?php _e('Date','teachpress'); ?></th>
					<td colspan="3"><?php echo stripslashes($daten["date"]); ?></td>
				</tr>
				<tr>
					<th><?php _e('Room','teachpress'); ?></th>
					<td colspan="3"><?php echo stripslashes($daten["room"]); ?></td>
				</tr>
				  <tr>
					<th><?php _e('Comment','teachpress'); ?></th>
					<td colspan="3"><?php echo stripslashes($daten["comment"]); ?></td>
				  </tr>
				  <tr>
					<th><?php _e('Related Page','teachpress'); ?></th>
					<td colspan="3"><?php if ( $daten["rel_page"] != 0) {echo get_permalink( $daten["rel_page"] ); } else { _e('none','teachpress'); } ?></td>
				  </tr>
				  </thead>
				</table>
			</div>
			<div style="min-width:780px; width:100%; max-width:1100px;">
			<table class="widefat">
			 <thead>
			  <tr>
				<th>&nbsp;</th>
				<?php
				$field1 = tp_get_option('regnum');
				if ($field1 == '1') {
					echo '<th>' .  __('Matr. number','teachpress') . '</th>';
				}
				?>
				<th><?php _e('Last name','teachpress'); ?></th>
				<th><?php _e('First name','teachpress'); ?></th>
				<?php
				$field2 = tp_get_option('studies');
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
			if ($counter2 == 0) {
				echo '<tr><td colspan="8"><strong>' . __('No entries','teachpress') . '</strong></td></tr>';
			}
			else {
				// all registered students for the course
				for($i=0; $i<$counter2; $i++) {
					if ($daten2[$i]["waitinglist"]== 0 ) {
						echo '<tr>';
						echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $daten2[$i]["con_id"] . '"/></th>';
						if ($field1 == '1') {
							echo '<td>' . $daten2[$i]["matriculation_number"] . '</td>';
						}
						echo '<td>' . $daten2[$i]["lastname"] . '</td>';
						echo '<td>' . $daten2[$i]["firstname"] . '</td>';
						if ($field1 == '1') {
							echo '<td>' . $daten2[$i]["course_of_studies"] . '</td>';
						}
						echo '<td>' . $daten2[$i]["userlogin"] . '</td>';
						echo '<td><a href="mailto:' . $daten2[$i]["email"] . '" title="' . __('send E-Mail','teachpress') . '">' . $daten2[$i]["email"] . '</a></td>';
						echo '<td>' . $daten2[$i]["date"] . '</td>';
						echo '</tr>';
					}
				} 
			}?>
			</tbody>
			</table>
			<?php
			// waitinglist
			$test = 0;
			for($i=0; $i<$counter2; $i++) {
				if ($daten2[$i]["waitinglist"]== 1 ) {
					$test++;
				}
			}	
			if ($test != 0) { ?>
				<h3><?php _e('Waitinglist','teachpress'); ?></h3>
				<table class="widefat">
				 <thead>
				  <tr>
					<th>&nbsp;</th>
					<th><?php _e('Matr. number','teachpress'); ?></th>
					<th><?php _e('Last name','teachpress'); ?></th>
					<th><?php _e('First name','teachpress'); ?></th>
					<th><?php _e('Course of studies','teachpress'); ?></th>
					<th><?php _e('User account','teachpress'); ?></th>
					<th><?php _e('E-Mail','teachpress'); ?></th>
					<th><?php _e('Registered at','teachpress'); ?></th>
				  </tr>
				 </thead>  
				 <tbody> 
				 <?php
				for($i=0; $i<$counter2; $i++) {
					if ($daten2[$i]["waitinglist"]== 1 ) { ?>
					 <tr>
						<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $daten2[$i]["con_id"]; ?>"/></th>
						<td><?php echo $daten2[$i]["matriculation_number"]; ?></td>
						<td><?php echo $daten2[$i]["lastname"]; ?></td>
						<td><?php echo $daten2[$i]["firstname"]; ?></td>
						<td><?php echo $daten2[$i]["course_of_studies"]; ?></td>
						<td><?php echo $daten2[$i]["userlogin"]; ?></td>
						<td><a href="mailto:<?php echo $daten2[$i]["email"]; ?>" title="<?php _e('send E-Mail','teachpress'); ?>"><?php echo $daten2[$i]["email"]; ?></a></td>
						<td><?php echo $daten2[$i]["date"]; ?></td>
					 </tr> 
					<?php } }?>
				</tbody>
				</table>
			<?php  } ?>      
	<table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
	  <tr>
		<td><?php if ($test != 0) { ?><input name="aufnehmen" type="submit" value="+ <?php _e('ingest','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/><?php } ?></td>
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
<?php } ?>