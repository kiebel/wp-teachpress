<?php 
/* Add new courses
 *
 * for edit a course:
 * @param $course_ID (INT)
 * @param $search (String)
 * @param $sem (String)
*/ 
function tp_add_course_page() { 

	global $wpdb;
	global $teachpress_settings;
	global $teachpress_courses;

	$data['type'] = tp_sec_var($_GET[course_type]);
	$data['name'] = tp_sec_var($_GET[post_title]);
	$data['room'] = tp_sec_var($_GET[room]);
	$data['lecturer'] = tp_sec_var($_GET[lecturer]);
	$data['date'] = tp_sec_var($_GET[date]);
	$data['places'] = tp_sec_var($_GET[places], 'integer');
	$data['fplaces'] = tp_sec_var($_GET[fplaces], 'integer');
	$data['start'] = tp_sec_var($_GET[start]); 
	$data['end'] = tp_sec_var($_GET[end]); 
	$data['semester'] = tp_sec_var($_GET[semester]);
	$data['comment'] = tp_sec_var($_GET[comment]);
	$data['rel_page'] = tp_sec_var($_GET[rel_page], 'integer');
	$data['parent'] = tp_sec_var($_GET[parent2], 'integer');
	$data['visible'] = tp_sec_var($_GET[visible], 'integer');
	$data['waitinglist'] = tp_sec_var($_GET[waitinglist], 'integer');
	$data['image_url'] = tp_sec_var($_GET[image_url]);
	
	$course_ID = tp_sec_var($_GET[lvs_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$sem = tp_sec_var($_GET[sem]);
	
	$erstellen = $_GET[erstellen]; 
	$save = $_GET[save];
	
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
	if (isset($erstellen)) {
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
    	<?php if ($sem != "") {?>
        <p><a href="admin.php?page=teachpress/teachpress.php&amp;lvs_ID=<?php echo $course_ID; ?>&amp;sem=<?php echo $sem; ?>&amp;search=<?php echo $search; ?>&amp;action=show" class="teachpress_back">&larr; <?php _e('back','teachpress'); ?></a></p>	
		<?php }?>
		<h2><?php if ($course_ID == 0) { _e('Create a new course','teachpress'); } else { _e('Edit course','teachpress'); } ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
		<div id="hilfe_anzeigen">
			<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
			<p class="hilfe_headline"><?php _e('Course name','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('For child courses: The name of the parent course will be add automatically.','teachpress'); ?></p>
			<p class="hilfe_headline"><?php _e('Enrollments','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('If you have a course without enrollments, so add no dates in the fields start and end. teachPress will be deactivate the enrollments automatically.','teachpress'); ?></p>
			<p class="hilfe_headline"><?php _e('Terms and course types','teachpress'); ?></p>
			<p class="hilfe_text"><?php _e('You can add new course types and terms','teachpress'); ?> <a href="options-general.php?page=teachpress/settings.php&amp;tab=courses"><?php _e('here','teachpress'); ?></a>.</p>
			
		</div>
	  <form id="add_course" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
      <input name="page" type="hidden" value="<?php if ($course_ID != 0) {?>teachpress/teachpress.php<?php } else {?>teachpress/add_course.php<?php } ?>" />
      <input name="action" type="hidden" value="edit" />
      <input name="lvs_ID" type="hidden" value="<?php echo $course_ID; ?>" />
      <input name="sem" type="hidden" value="<?php echo $sem; ?>" />
      <input name="search" type="hidden" value="<?php echo $search; ?>" />
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
        	<input name="image_url" id="image_url" type="text" style="width:90%;" tabindex="12" value="<?php echo $daten["image_url"]; ?>"/>
         	<a id="upload_image_button" class="thickbox" title="<?php _e('Add Image','teachpress'); ?>" style="cursor:pointer;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
			<p><label for="visible" title="<?php _e('Here you can edit the visibility of a course in the enrollments. If this is a course with inferier events so must select "Yes".','teachpress'); ?>"><strong><?php _e('Visibility','teachpress'); ?></strong></label></p>
			<select name="visible" id="visible" tabindex="13">
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
            	<p><input name="save" type="submit" id="teachpress_erstellen" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('save','teachpress'); ?>" class="button-primary"></p>
            <?php } else { ?>
                <p><input type="reset" name="Reset" value="<?php _e('reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button"><input name="erstellen" type="submit" id="teachpress_erstellen" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>" class="button-primary"></p>
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
		}	
		else {
			$meta = 'value="' . $daten["start"] . '"';
		}	
		?>
		<input name="start" type="text" id="start" tabindex="14" size="15" <?php echo $meta; ?>/>
		<p><label for="end" title="<?php _e('The end date for the enrollment','teachpress'); ?>"><strong><?php _e('End','teachpress'); ?></strong></label></p>
        <?php 
		if ($course_ID == 0) {
			// same as for start
		}
		else {
			$meta = 'value="' . $daten["end"] . '"';
		}
		?>
		<input name="end" type="text" id="end" tabindex="15" size="15" <?php echo $meta; ?>/>
		<p><label for="waitinglist" title="<?php _e('Waiting list: yes or no','teachpress'); ?>"><strong><?php _e('Waiting list','teachpress'); ?></strong></label></p>
		<select name="waitinglist" id="waitinglist" tabindex="16">
        	<?php
			if ($daten["waitinglist"] == 1) {
				echo '<option value="0">' . __('no','teachpress') . '</option>';
				echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
			}
			else {
				echo '<option value="0">' . __('no','teachpress') . '</option>';
				echo '<option value="1">' . __('yes','teachpress') . '</option>';
			}?>
		</select>
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
		<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $daten["name"]; ?>" id="title" autocomplete="off" />
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
			<select name="course_type" id="course_type" tabindex="2">
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
					echo '<option value="' . $row->value . '"' . $check . '>' . $row->value . '</option>';
				} ?>
			</select>
            <p><label for="semester" title="<?php _e('The term where the course will be happening','teachpress'); ?>"><strong><?php _e('Term','teachpress'); ?></strong></label></p>
			  <select name="semester" id="semester" tabindex="3">
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
					echo '<option value="' . $period[$zahl] . '" ' . $current . '>' . $period[$zahl] . '</option>';
					$zahl--;
				}?> 
			</select>
			<p><label for="lecturer" title="<?php _e('The Lecturer of the course','teachpress'); ?>"><strong><?php _e('Lecturer','teachpress'); ?></strong></label></p>
			<input name="lecturer" type="text" id="lecturer" style="width:95%;" tabindex="4" value="<?php echo $daten["lecturer"]; ?>" />
			<p><label for="date" title="<?php _e('The dates for the course','teachpress'); ?>"><strong><?php _e('Date','teachpress'); ?></strong></label></p>
			<input name="date" type="text" id="date" style="width:95%;" tabindex="5" value="<?php echo $daten["date"]; ?>" />
			<p><label for="room" title="<?php _e('The room or place for the course','teachpress'); ?>"><strong><?php _e('Room','teachpress'); ?></strong></label></p>
			<input name="room" type="text" id="room" style="width:95%;" tabindex="6" value="<?php echo $daten["room"]; ?>" />
			<p><label for="platz" title="<?php _e('The number of available places. Important for enrollements','teachpress'); ?>"><strong><?php _e('Number of places','teachpress'); ?></strong></label></p>
			<input name="places" type="text" id="places" style="width:30%;" tabindex="7" value="<?php echo $daten["places"]; ?>" />
            <?php 
			if ($course_ID != 0) {?>
            	<p><label for="fplaces" title="<?php _e('The number of free places','teachpress'); ?>"><strong><?php _e('free places','teachpress'); ?></strong></label></p>
				<input name="fplaces" id="fplaces" type="text" style="width:30%;" tabindex="8" value="<?php echo $daten["fplaces"]; ?>"/>
			<?php } ?>
			<p><label for="parent2" title="<?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?>"><strong><?php _e('Parent','teachpress'); ?></strong></label></p>
			<select name="parent2" id="parent2" tabindex="9">
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
                            echo '<option value="' . $par[$j]["id"] . '" ' . $current . '>' . $par[$j]["id"] . ' - ' . $par[$j]["name"] . ' ' . $par[$j]["semester"] . '</option>';
                            $zahl++;
                        } 
                    } 
                    if ($zahl != 0) {
                        echo '<option>------</option>';
                    } 
                }?>
			</select>
			<p><label for="comment" title="<?php _e('For parent courses the comment is showing in the overview and for child courses in the enrollments system.','teachpress'); ?>"><strong><?php _e('Comment or Description','teachpress'); ?></strong></label></p>
			<textarea name="comment" cols="75" rows="2" id="comment" tabindex="10"><?php echo $daten["comment"]; ?></textarea>
			<p><label for="rel_page" title="<?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?>"><strong><?php _e('Related Page','teachpress'); ?></strong></label></p>
			<select name="rel_page" id="rel_page" tabindex="11">
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