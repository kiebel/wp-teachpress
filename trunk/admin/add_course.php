<?php 
/* Add new courses
 *
 * GET parameters:
 * @param $course_ID (INT)
 * @param $search (String)
 * @param $sem (String)
 * @param $ref (String)
*/
function tp_add_course_page() { 

	global $wpdb;
	global $teachpress_settings;
	global $teachpress_courses;

	$data['type'] = tp_sec_var($_POST[course_type]);
	$data['name'] = tp_sec_var($_POST[post_title]);
	$data['room'] = tp_sec_var($_POST[room]);
	$data['lecturer'] = tp_sec_var($_POST[lecturer]);
	$data['date'] = tp_sec_var($_POST[date]);
	$data['places'] = tp_sec_var($_POST[places], 'integer');
	$data['fplaces'] = tp_sec_var($_POST[fplaces], 'integer');
	$data['start'] = tp_sec_var($_POST[start]); 
	$data['start_hour'] = tp_sec_var($_POST[start_hour]);
	$data['start_minute'] = tp_sec_var($_POST[start_minute]);
	$data['end'] = tp_sec_var($_POST[end]); 
	$data['end_hour'] = tp_sec_var($_POST[end_hour]);
	$data['end_minute'] = tp_sec_var($_POST[end_minute]);
	$data['semester'] = tp_sec_var($_POST[semester]);
	$data['comment'] = tp_sec_var($_POST[comment]);
	$data['rel_page'] = tp_sec_var($_POST[rel_page], 'integer');
	$data['parent'] = tp_sec_var($_POST[parent2], 'integer');
	$data['visible'] = tp_sec_var($_POST[visible], 'integer');
	$data['waitinglist'] = tp_sec_var($_POST[waitinglist], 'integer');
	$data['image_url'] = tp_sec_var($_POST[image_url]);
	$data['strict_signup'] = tp_sec_var($_POST[strict_signup], 'integer');
	
	// Handle that the activation of strict sign up is not possible for a child course
	if ( $data['parent'] != 0) { $data['strict_signup'] = 0; }
	
	$course_ID = tp_sec_var($_GET[lvs_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$sem = tp_sec_var($_GET[sem]);
	$ref = tp_sec_var($_GET[ref]);
	
	$create = $_POST[create]; 
	$save = $_POST[save];
	// possible course parents
	$row = "SELECT course_id, name, semester FROM " . $teachpress_courses . " WHERE parent='0' AND course_id != '$veranstaltung' ORDER BY semester DESC, name";
	$row = $wpdb->get_results($row);
	$counter3 = 0;
	foreach($row as $row){
		$par[$counter3]["id"] = $row->course_id;
		$par[$counter3]["name"] = $row->name;
		$par[$counter3]["semester"] = $row->semester;
		$counter3++;
	}
	// Event handler
	if (isset($create)) {
		$course_ID = tp_add_course($data);
		$message = __('Course created successful.','teachpress') . ' <a href="admin.php?page=teachpress/add_course.php">' . __('Add New','teachpress') . '</a>';
		tp_get_message($message, '');
	}
	if (isset($save)) {
		tp_change_course($course_ID, $data);
		$message = __('Changes successful','teachpress');
		tp_get_message($message, '');
	}
	if ($course_ID != 0) {
		$sql = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$course_ID'";
		$daten = $wpdb->get_row($sql, ARRAY_A);
	}
	?>
	<div class="wrap">
    	<?php 
		if ($sem != "") {
			// Define URL for "back"-button
			if ($ref == 'overview' ) {
				$back = 'admin.php?page=teachpress/teachpress.php&amp;sem=' . stripslashes($sem) . '&amp;search=' . stripslashes($search) . '';
			}
			else {
				$back = 'admin.php?page=teachpress/teachpress.php&amp;lvs_ID=' . $course_ID . '&amp;sem=' . stripslashes($sem) . '&amp;search=' . stripslashes($search) . '&amp;action=show';
			}
		?>
        <p style="margin-bottom:0;"><a href="<?php echo $back; ?>" class="teachpress_back">&larr; <?php _e('back','teachpress'); ?></a></p>	
		<?php }?>
		<h2><?php if ($course_ID == 0) { _e('Create a new course','teachpress'); } else { _e('Edit course','teachpress'); } ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
		<div id="hilfe_anzeigen">
			<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
			<p class="hilfe_headline"><?php _e('Course name','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('For child courses: The name of the parent course will be add automatically.','teachpress'); ?></p>
			<p class="hilfe_headline"><?php _e('Enrollments','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('If you have a course without enrollments, so add no dates in the fields start and end. teachPress will be deactivate the enrollments automatically.','teachpress'); ?></p>
            <p class="hilfe_text"><?php _e('Please note, that your local time is not the same as the server time. The current server time is:','teachpress'); ?> <strong><?php echo current_time('mysql'); ?></strong></p>
			<p class="hilfe_headline"><?php _e('Terms and course types','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('You can add new course types and terms','teachpress'); ?> <a href="options-general.php?page=teachpress/settings.php&amp;tab=courses"><?php _e('here','teachpress'); ?></a>.</p>
            <p class="hilfe_headline"><?php _e('Strict sign up','teachpress'); ?></p>
            <p class="hilfe_text"><?php _e('This is an option only for parent courses. If you activate it, subscribing is only possible for one of the child courses and not in all. This option has no influence on waiting lists.','teachpress'); ?></p>
            <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
		</div>
	  <form id="add_course" name="form1" method="post" action="<?php echo $PHP_SELF ?>">
      <input name="page" type="hidden" value="<?php if ($course_ID != 0) {?>teachpress/teachpress.php<?php } else {?>teachpress/add_course.php<?php } ?>" />
      <input name="action" type="hidden" value="edit" />
      <input name="lvs_ID" type="hidden" value="<?php echo $course_ID; ?>" />
      <input name="sem" type="hidden" value="<?php echo $sem; ?>" />
      <input name="search" type="hidden" value="<?php echo $search; ?>" />
      <input name="ref" type="hidden" value="<?php echo $ref; ?>" />
	  <div style="min-width:780px; width:100%; max-width:1100px;">
	  <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">   
		<table class="widefat">
		<thead>
			<tr>
			<th><?php _e('Meta','teachpress'); ?></th>
			</tr>
			<tr>
			<td>
            <?php if ($daten["image_url"] != '') {
				echo '<p><img name="tp_pub_image" src="' . $daten["image_url"] . '" alt="' . $daten["name"] . '" title="' . $daten["name"] . '" style="max-width:100%;"/></p>';
			} ?>
            <p><label for="image_url" title="<?php _e('With the image field you can add an image to a course.','teachpress'); ?>"><strong><?php _e('Image URL','teachpress'); ?></strong></label></p>
        	<input name="image_url" id="image_url" type="text" title="<?php _e('Image URL','teachpress'); ?>" style="width:90%;" tabindex="12" value="<?php echo $daten["image_url"]; ?>"/>
         	<a id="upload_image_button" class="thickbox" title="<?php _e('Add Image','teachpress'); ?>" style="cursor:pointer;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
			<p><label for="visible" title="<?php _e('Here you can edit the visibility of a course in the enrollments. If this is a course with inferier events so must select "Yes".','teachpress'); ?>"><strong><?php _e('Visibility','teachpress'); ?></strong></label></p>
			<select name="visible" id="visible" title="<?php _e('Here you can edit the visibility of a course in the enrollments. If this is a course with inferier events so must select "Yes".','teachpress'); ?>" tabindex="13">
              <?php
                if ($daten["visible"] == 1) {
                    echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0">' . __('no','teachpress') . '</option>';
                }
				else {
					echo '<option value="1">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0">' . __('no','teachpress') . '</option>';
				}?>
			</select>            
			</td>
			</tr>
			<tr>
			<td style="background-color:#EAF2FA; text-align:center;">
            <?php if ($course_ID != 0) {?>
            	<p><input name="save" type="submit" id="teachpress_create" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('save','teachpress'); ?>" class="button-primary"></p>
            <?php } else { ?>
                <p><input type="reset" name="Reset" value="<?php _e('reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button"><input name="create" type="submit" id="teachpress_create" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>" class="button-primary"></p>
            <?php } ?>
		</td>
		</tr>
	  </thead>      
	  </table>
	  <p style="font-size:2px; margin:0px;">&nbsp;</p>
	  <table class="widefat">
	  <thead>
		<tr>
		<th><?php _e('Enrollments','teachpress'); ?></th>
		</tr>
		<tr>
		<td>
		<p><label for="start" title="<?php _e('The start date for the enrollment','teachpress'); ?>"><strong><?php _e('Start','teachpress'); ?></strong></label></p>
        <?php 
		if ($course_ID == 0) {
			$str = "'";
			$meta = 'value="' . __('JJJJ-MM-TT','teachpress') . '" onblur="if(this.value==' . $str . $str . ') this.value=' . $str . __('JJJJ-MM-TT','teachpress') . $str . ';" onfocus="if(this.value==' . $str . __('JJJJ-MM-TT','teachpress') . $str . ') this.value=' . $str . $str . ';"';
			$hour = '00';
			$minute = '00';
		}	
		else {
			$date1 = tp_datumsplit($daten["start"]);
			$meta = 'value="' . $date1[0][0] . '-' . $date1[0][1] . '-' . $date1[0][2] . '"';
			$hour = $date1[0][3];
			$minute = $date1[0][4]; 
		}	
		?>
		<input name="start" type="text" id="start" title="<?php _e('Date','teachpress'); ?>" tabindex="14" size="15" <?php echo $meta; ?>/> <input name="start_hour" type="text" title="<?php _e('Hours','teachpress'); ?>" value="<?php echo $hour; ?>" size="2" tabindex="15" /> : <input name="start_minute" type="text" title="<?php _e('Minutes','teachpress'); ?>" value="<?php echo $minute; ?>" size="2" tabindex="16" />
		<p><label for="end" title="<?php _e('The end date for the enrollment','teachpress'); ?>"><strong><?php _e('End','teachpress'); ?></strong></label></p>
        <?php 
		if ($course_ID == 0) {
			// same as for start
		}
		else {
			$date1 = tp_datumsplit($daten["end"]);
			$meta = 'value="' . $date1[0][0] . '-' . $date1[0][1] . '-' . $date1[0][2] . '"';
			$hour = $date1[0][3];
			$minute = $date1[0][4];
		}
		?>
		<input name="end" type="text" id="end" title="<?php _e('Date','teachpress'); ?>" tabindex="17" size="15" <?php echo $meta; ?>/> <input name="end_hour" type="text" title="<?php _e('Hours','teachpress'); ?>" value="<?php echo $hour; ?>" size="2" tabindex="18" /> : <input name="end_minute" type="text" title="<?php _e('Minutes','teachpress'); ?>" value="<?php echo $minute; ?>" size="2" tabindex="19" />
        <p><strong><?php _e('Options','teachpress'); ?></strong></p>
        <?php
		if ( $daten["waitinglist"] == 1 ) {
			$check = 'checked="checked"';
		}
		else {
			$check = "";
		}
		?>
		 <p><input name="waitinglist" id="waitinglist" type="checkbox" value="1" tabindex="26" <?php echo $check; ?>/> <label for="waitinglist" title="<?php _e('Waiting list','teachpress'); ?>"><?php _e('Waiting list','teachpress'); ?></label></p>
        <p>
        <?php 
		if ($daten["parent"] != 0) {
			$parent_data_strict = tp_get_parent_data($daten["parent"], 'strict_signup'); 
			if ( $parent_data_strict == 1 ) {
				$check = 'checked="checked"';
			}
			else {
				$check = "";
			}?>
			<input name="strict_signup_2" id="strict_signup_2" type="checkbox" value="1" tabindex="27" <?php echo $check; ?> disabled="disabled" /> <label for="strict_signup_2" title="<?php _e('This is a child course. You can only change this option in the parent course','teachpress'); ?>"><?php _e('Strict sign up','teachpress'); ?></label></p>
		<?php } else { 
			if ( $daten["strict_signup"] == 1 ) {
				$check = 'checked="checked"';
			}
			else {
				$check = "";
			}?>
            <input name="strict_signup" id="strict_signup" type="checkbox" value="1" tabindex="27" <?php echo $check; ?> /> <label for="strict_signup" title="<?php _e('This is an option only for parent courses. If you activate it, subscribing is only possible for one of the child courses and not in all. This option has no influence on waiting lists.','teachpress'); ?>"><?php _e('Strict sign up','teachpress'); ?></label></p>
        <?php } ?>
		</td>
		</tr>
	  </thead>    
	  </table>   
	  </div>
	  <div style="width:67%; float:left;">
	  <div id="post-body">
	  <div id="post-body-content">
	  <div id="titlediv">
	  <div id="titlewrap">
		<label class="hide-if-no-js" style="display:none;" id="title-prompt-text" for="title"><?php _e('Course name','teachpress'); ?></label>
		<input type="text" name="post_title" title="<?php _e('Course name','teachpress'); ?>" size="30" tabindex="1" value="<?php echo stripslashes($daten["name"]); ?>" id="title" autocomplete="off" />
	  </div>
	  </div>
	  </div>
	  </div>
	  <table class="widefat">
	  <thead>
		<tr>
			<th><?php _e('General','teachpress'); ?></th>
		</tr>
		<tr>
			<td>
            <p><label for="course_type" title="<?php _e('The course type','teachpress'); ?>"><strong><?php _e('Course type','teachpress'); ?></strong></label></p>
			<select name="course_type" id="course_type" title="<?php _e('The course type','teachpress'); ?>" tabindex="2">
			<?php 
				$row = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'course_type' ORDER BY value";
				$row = $wpdb->get_results($row);
				foreach ($row as $row) {
					if ($daten["type"] == $row->value) {
						$check = ' selected="selected"';
					}
					else {
						$check = '';
					}	
					echo '<option value="' . stripslashes($row->value) . '"' . $check . '>' . stripslashes($row->value) . '</option>';
				} ?>
			</select>
            <p><label for="semester" title="<?php _e('The term where the course will be happening','teachpress'); ?>"><strong><?php _e('Term','teachpress'); ?></strong></label></p>
			  <select name="semester" id="semester" title="<?php _e('The term where the course will be happening','teachpress'); ?>" tabindex="3">
				<?php
				if ($course_ID == 0) {
					$value = tp_get_option('sem');
				} 
				$sql = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id";
				$sem = $wpdb->get_results($sql);
				$x = 0;
				// Semester in array speichern - wird spaeter fuer Parent-Menu genutzt
				foreach ($sem as $sem) { 
					$period[$x] = $sem->value;
					$x++;
				}
				$zahl = $x-1;
				// gibt alle Semester aus (umgekehrte Reihenfolge)
				while ($zahl >= 0) {
					if ($period[$zahl] == $value && $course_ID == 0) {
						$current = 'selected="selected"' ;
					}
					elseif ($period[$zahl] == $daten["semester"] && $course_ID != 0) {
						$current = 'selected="selected"' ;
					}
					else {
						$current = '' ;
					}
					echo '<option value="' . stripslashes($period[$zahl]) . '" ' . $current . '>' . stripslashes($period[$zahl]) . '</option>';
					$zahl--;
				}?> 
			</select>
			<p><label for="lecturer" title="<?php _e('The lecturer(s) of the course','teachpress'); ?>"><strong><?php _e('Lecturer','teachpress'); ?></strong></label></p>
			<input name="lecturer" type="text" id="lecturer" title="<?php _e('The lecturer(s) of the course','teachpress'); ?>" style="width:95%;" tabindex="4" value="<?php echo stripslashes($daten["lecturer"]); ?>" />
			<p><label for="date" title="<?php _e('The date(s) for the course','teachpress'); ?>"><strong><?php _e('Date','teachpress'); ?></strong></label></p>
             <input name="date" type="text" id="date" title="<?php _e('The date(s) for the course','teachpress'); ?>" tabindex="5" style="width:95%;" value="<?php echo stripslashes($daten["date"]); ?>" />
			<p><label for="room" title="<?php _e('The room or place for the course','teachpress'); ?>"><strong><?php _e('Room','teachpress'); ?></strong></label></p>
			<input name="room" type="text" id="room" title="<?php _e('The room or place for the course','teachpress'); ?>" style="width:95%;" tabindex="6" value="<?php echo stripslashes($daten["room"]); ?>" />
			<p><label for="platz" title="<?php _e('The number of available places.','teachpress'); ?>"><strong><?php _e('Number of places','teachpress'); ?></strong></label></p>
			<input name="places" type="text" id="places" title="<?php _e('The number of available places.','teachpress'); ?>" style="width:70px;" tabindex="7" value="<?php echo $daten["places"]; ?>" />
            <?php 
			if ($course_ID != 0) {?>
            	<p><label for="fplaces" title="<?php _e('The number of free places','teachpress'); ?>"><strong><?php _e('free places','teachpress'); ?></strong></label></p>
				<input name="fplaces" id="fplaces" type="text" title="<?php _e('The number of free places','teachpress'); ?>" style="width:70px;" tabindex="8" value="<?php echo $daten["fplaces"]; ?>"/>
			<?php } ?>
			<p><label for="parent2" title="<?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?>"><strong><?php _e('Parent','teachpress'); ?></strong></label></p>
			<select name="parent2" id="parent2" title="<?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?>" tabindex="9">
			  <option value="0"><?php _e('none','teachpress'); ?></option>
			  <option>------</option>
			  <?php 	
                for ($i = 0; $i < $x; $i++) {
                    $zahl = 0;
                    for ($j = 0; $j < $counter3; $j++) {
                        if ($period[($x - 1)-$i] == $par[$j]["semester"] ) {
                            if ($par[$j]["id"] == $daten["parent"]) {
                                $current = 'selected="selected"' ;
                            }
                            else {
                                $current = '' ;
                            }
                            echo '<option value="' . $par[$j]["id"] . '" ' . $current . '>' . $par[$j]["id"] . ' - ' . stripslashes($par[$j]["name"]) . ' ' . $par[$j]["semester"] . '</option>';
                            $zahl++;
                        } 
                    } 
                    if ($zahl != 0) {
                        echo '<option>------</option>';
                    } 
                }?>
			</select>
			<p><label for="comment" title="<?php _e('For parent courses the comment is showing in the overview and for child courses in the enrollments system.','teachpress'); ?>"><strong><?php _e('Comment or Description','teachpress'); ?></strong></label></p>
			<textarea name="comment" cols="75" rows="2" id="comment" title="<?php _e('For parent courses the comment is showing in the overview and for child courses in the enrollments system.','teachpress'); ?>" tabindex="10" style="width:100%;"><?php echo stripslashes($daten["comment"]); ?></textarea>
			<p><label for="rel_page" title="<?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?>"><strong><?php _e('Related page','teachpress'); ?></strong></label></p>
			<select name="rel_page" id="rel_page" title="<?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?>" tabindex="11">
				<?php teachpress_wp_pages("menu_order","ASC",$daten["rel_page"],0,0); ?>
			</select>
		  </tr>
	  </thead>       
	  </table>
	  </div>
	  </div>
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
		</form>
	</div>
<?php } ?>