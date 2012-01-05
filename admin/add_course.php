<?php
/**
 * Add help tab for add new courses page
 */
function tp_add_course_page_help () {
    $screen = get_current_screen();  
    $screen->add_help_tab( array(
        'id'        => 'tp_add_course_help',
        'title'     => __('Create a new course','teachpress'),
        'content'   => '<p><strong>' . __('Course name','teachpress') . '</strong></p>
                        <p>' . __('For child courses: The name of the parent course will be add automatically.','teachpress') . '</p>
                        <p><strong>' . __('Enrollments','teachpress') . '</strong></p>
                        <p>' . __('If you have a course without enrollments, so add no dates in the fields start and end. teachPress will be deactivate the enrollments automatically.','teachpress') . ' ' . __('Please note, that your local time is not the same as the server time. The current server time is:','teachpress') . ' <strong>' . current_time('mysql') . '</strong></p>
                        <p><strong>' . __('Strict sign up','teachpress') . '</strong></p>
                        <p>' . __('This is an option only for parent courses. If you activate it, subscribing is only possible for one of the child courses and not in all. This option has no influence on waiting lists.','teachpress') . '</p>
                        <p><strong>' . __('Terms and course types','teachpress') . '</strong></p>
                        <p>' . __('You can add new course types and terms','teachpress') . ' <a href="options-general.php?page=teachpress/settings.php&amp;tab=courses">' . __('here','teachpress') . '</a>.</p>
                        <p><strong>' . __('Visibility','teachpress') . '</strong></p>
                        <p>' . __('You can choice between the following visibiltiy options','teachpress') . ':</p>
                        <ul style="list-style:disc; padding-left:40px;">
                               <li><strong>' . __('normal','teachpress') . ':</strong> ' . __('The course is visible at the enrollment pages, if enrollments are justified. If it is a parent course, the course is visible at the frontend semester overview.','teachpress') . '</li>
                               <li><strong>' . __('extend','teachpress') . ' (' . __('only for parent courses','teachpress') . '):</strong> ' . __('The same as normal, but in the frontend semester overview all sub-courses will also be displayed.','teachpress') . '</li>
                               <li><strong>' . __('invisible','teachpress') . ':</strong> ' . __('The course is invisible.','teachpress') . '</li></ul>',
    ) );
} 

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

   $data['type'] = isset( $_POST['course_type'] ) ? tp_sec_var($_POST['course_type']) : '';
   $data['name'] = isset( $_POST['post_title'] ) ? tp_sec_var($_POST['post_title']) : '';
   $data['room'] = isset( $_POST['room'] ) ? tp_sec_var($_POST['room']) : '';
   $data['lecturer'] = isset( $_POST['lecturer'] ) ? tp_sec_var($_POST['lecturer']) : '';
   $data['date'] = isset( $_POST['date'] ) ? tp_sec_var($_POST['date']) : '';
   $data['places'] = isset( $_POST['places'] ) ? (int) $_POST['places'] : 0;
   $data['fplaces'] = isset( $_POST['fplaces'] ) ? (int) $_POST['fplaces'] : 0;
   $data['start'] = isset( $_POST['start'] ) ? tp_sec_var($_POST['start']) : ''; 
   $data['start_hour'] = isset( $_POST['start_hour'] ) ? tp_sec_var($_POST['start_hour']) : '';
   $data['start_minute'] = isset( $_POST['start_minute'] ) ? tp_sec_var($_POST['start_minute']) : '';
   $data['end'] = isset( $_POST['end'] ) ? tp_sec_var($_POST['end']) : '';
   $data['end_hour'] = isset( $_POST['end_hour'] ) ? tp_sec_var($_POST['end_hour']) : '';
   $data['end_minute'] = isset( $_POST['end_minute'] ) ? tp_sec_var($_POST['end_minute']) : '';
   $data['semester'] = isset( $_POST['semester'] ) ? tp_sec_var($_POST['semester']) : '';
   $data['comment'] = isset( $_POST['comment'] ) ? tp_sec_var($_POST['comment']) : '';
   $data['rel_page'] = isset( $_POST['rel_page'] ) ? (int) $_POST['rel_page'] : 0;
   $data['parent'] = isset( $_POST['parent2'] ) ? (int) $_POST['parent2'] : 0;
   $data['visible'] = isset( $_POST['visible'] ) ? (int) $_POST['visible'] : 1;
   $data['waitinglist'] = isset( $_POST['waitinglist'] ) ? (int) $_POST['waitinglist'] : 0;
   $data['image_url'] = isset( $_POST['image_url'] ) ? tp_sec_var($_POST['image_url']) : '';
   $data['strict_signup'] = isset( $_POST['strict_signup'] ) ? (int) $_POST['strict_signup'] : 0;

   // Handle that the activation of strict sign up is not possible for a child course
   if ( $data['parent'] != 0) { $data['strict_signup'] = 0; }

   $course_ID = isset( $_REQUEST['course_ID'] ) ? (int) $_REQUEST['course_ID'] : 0;
   $search = isset( $_GET['search'] ) ? tp_sec_var($_GET['search']) : '';
   $sem = isset( $_GET['sem'] ) ? tp_sec_var($_GET['sem']) : '';
   $ref = isset( $_GET['ref'] ) ? tp_sec_var($_GET['ref']) : '';

   // possible course parents
   $row = "SELECT `course_id`, `name`, `semester` FROM " . $teachpress_courses . " WHERE `parent` = '0' AND `course_id` != '$course_ID' ORDER BY semester DESC, name";
   $row = $wpdb->get_results($row);
   $counter3 = 0;
   foreach($row as $row){
           $par[$counter3]["id"] = $row->course_id;
           $par[$counter3]["name"] = $row->name;
           $par[$counter3]["semester"] = $row->semester;
           $counter3++;
   }
   // Event handler
   if ( isset($_POST['create']) ) {
           $course_ID = tp_add_course($data);
           $message = __('Course created successful.','teachpress') . ' <a href="admin.php?page=teachpress/add_course.php">' . __('Add New','teachpress') . '</a>';
           get_tp_message($message, '');
   }
   if ( isset($_POST['save']) ) {
           tp_change_course($course_ID, $data);
           $message = __('Changes successful','teachpress');
           get_tp_message($message, '');
   }
   if ( $course_ID != 0 ) {
           $sql = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$course_ID'";
           $daten = $wpdb->get_row($sql, ARRAY_A);
   }
   else {
           $daten = get_tp_var_types('course_array');
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
            $back = 'admin.php?page=teachpress/teachpress.php&amp;course_ID=' . $course_ID . '&amp;sem=' . stripslashes($sem) . '&amp;search=' . stripslashes($search) . '&amp;action=show';
         }
         ?>
          <p style="margin-bottom:0;"><a href="<?php echo $back; ?>" class="button-secondary">&larr; <?php _e('Back','teachpress'); ?></a></p>	
   <?php }?>
    <h2><?php if ($course_ID == 0) { _e('Create a new course','teachpress'); } else { _e('Edit course','teachpress'); } ?></h2>
     <form id="add_course" name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
     <input name="page" type="hidden" value="<?php if ($course_ID != 0) {?>teachpress/teachpress.php<?php } else {?>teachpress/add_course.php<?php } ?>" />
     <input name="action" type="hidden" value="edit" />
     <input name="course_ID" type="hidden" value="<?php echo $course_ID; ?>" />
     <input name="sem" type="hidden" value="<?php echo $sem; ?>" />
     <input name="search" type="hidden" value="<?php echo $search; ?>" />
     <input name="ref" type="hidden" value="<?php echo $ref; ?>" />
     <div style="min-width:780px; width:100%; max-width:1100px;">
     <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">   
     <table class="widefat" style="margin-bottom:15px;">
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
           <input name="image_url" id="image_url" class="upload" type="text" title="<?php _e('Image URL','teachpress'); ?>" style="width:90%;" tabindex="12" value="<?php echo $daten["image_url"]; ?>"/>
           <a class="upload_button_image" title="<?php _e('Add Image','teachpress'); ?>" style="cursor:pointer;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
                   <p><label for="visible" title="<?php _e('Here you can edit the visibility of a course in the enrollments.','teachpress'); ?>"><strong><?php _e('Visibility','teachpress'); ?></strong></label></p>
                   <select name="visible" id="visible" title="<?php _e('Here you can edit the visibility of a course in the enrollments.','teachpress'); ?>" tabindex="13">
           <option value="1"<?php if ( $daten["visible"] == 1 && $course_ID != 0 ) {echo ' selected="selected"'; } ?>><?php _e('normal','teachpress'); ?></option>
           <option value="2"<?php if ( $daten["visible"] == 2 && $course_ID != 0 ) {echo ' selected="selected"'; } ?>><?php _e('extend','teachpress'); ?></option>
           <option value="0"<?php if ( $daten["visible"] == 0 && $course_ID != 0 ) {echo ' selected="selected"'; } ?>><?php _e('invisible','teachpress'); ?></option>
                   </select>            
                   </td>
                   </tr>
                   <tr>
                   <td style="text-align:center;">
       <?php if ($course_ID != 0) {?>
           <input name="save" type="submit" id="teachpress_create" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('Save'); ?>" class="button-primary"/>
       <?php } else { ?>
           <div style="width: 50%; float: left; height: 25px;"><input type="reset" name="Reset" value="<?php _e('Reset','teachpress'); ?>" id="teachpress_reset" class="button-secondary"/></div>
           <div style="width: 50%; float: right; height: 25px;"><input name="create" type="submit" id="teachpress_create" onclick="teachpress_validateForm('title','','R','lecturer','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('Create','teachpress'); ?>" class="button-primary"/></div>
       <?php } ?>
           </td>
           </tr>
          </thead>      
     </table>
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
              $date1 = tp_datesplit($daten["start"]);
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
              $date1 = tp_datesplit($daten["end"]);
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
              $parent_data_strict = get_tp_course_data($daten["parent"], 'strict_signup'); 
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
                 <p><label for="course_type" title="<?php _e('The course type','teachpress'); ?>"><strong><?php _e('Type'); ?></strong></label></p>
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
                    $value = get_tp_option('sem');
                 }
                 else {
                    $value = 0;
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
           | <?php echo __('free places','teachpress') . ': ' . $daten["fplaces"]; ?>
                           <input name="fplaces" id="fplaces" type="hidden" value="<?php echo $daten["fplaces"]; ?>"/>
                   <?php } ?>
                   <p><label for="parent2" title="<?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?>"><strong><?php _e('Parent course','teachpress'); ?></strong></label></p>
                   <select name="parent2" id="parent2" title="<?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?>" tabindex="8">
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
                   <textarea name="comment" cols="75" rows="3" id="comment" title="<?php _e('For parent courses the comment is showing in the overview and for child courses in the enrollments system.','teachpress'); ?>" tabindex="9" style="width:95%;"><?php echo stripslashes($daten["comment"]); ?></textarea>
                   <p><label for="rel_page" title="<?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?>"><strong><?php _e('Related page','teachpress'); ?></strong></label></p>
                   <select name="rel_page" id="rel_page" title="<?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?>" tabindex="10">
                           <?php 
                           $post_type = get_tp_option('rel_page_courses');
                           get_tp_wp_pages("menu_order","ASC",$daten["rel_page"],$post_type,0,0); 
                           ?>
                   </select>
             </tr>
     </thead>       
     </table>
     </div>
     </div>
      <script type="text/javascript" charset="utf-8">
      jQuery(document).ready(function($) {
              $('#start').datepicker({showWeek: true, changeMonth: true, changeYear: true, showOtherMonths: true, firstDay: 1, renderer: $.extend({}, $.datepicker.weekOfYearRenderer), onShow: $.datepicker.showStatus, dateFormat: 'yy-mm-dd', yearRange: '2008:c+5'
              }); 

              $('#end').datepicker({showWeek: true, changeMonth: true, changeYear: true, showOtherMonths: true, firstDay: 1, renderer: $.extend({}, $.datepicker.weekOfYearRenderer), onShow: $.datepicker.showStatus, dateFormat: 'yy-mm-dd', yearRange: '2008:c+5'}); 
              });
      </script>
      </form>
   </div>
<?php } ?>