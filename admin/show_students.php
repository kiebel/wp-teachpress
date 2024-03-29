<?php 
/* overview for students
 *
 * from editstudent.php (GET):
 * @param $search - String
 * @param $students_group - String
*/
function teachpress_students_page() { 

     global $teachpress_stud;
     global $wpdb;
     global $user_ID;
     get_currentuserinfo();
     $checkbox = isset ( $_GET['checkbox'] ) ? $_GET['checkbox'] : '';
     $bulk = isset ( $_GET['bulk'] ) ? $_GET['bulk'] : '';
     $search = isset ( $_GET['search'] ) ? tp_sec_var($_GET['search']) : ''; 
     $students_group = isset ( $_GET['students_group'] ) ? tp_sec_var($_GET['students_group']) : '';
     $action = isset ($_GET['action']) ? $_GET['action'] : '';

     // Page menu
     $page = 'teachpress/students.php';
     $number_messages = 50;
     // Handle limits
     if (isset($_GET['limit'])) {
           $curr_page = (int)$_GET['limit'] ;
           if ( $curr_page <= 0 ) {
                   $curr_page = 1;
           }
           $entry_limit = ( $curr_page - 1 ) * $number_messages;
     }
     else {
           $entry_limit = 0;
           $curr_page = 1;
     }

     // Send mail (received from mail.php)
     if( isset( $_POST['send_mail'] ) ) {
          $from = isset ( $_POST['from'] ) ? tp_sec_var($_POST['from']) : '';
          $to = isset ( $_POST['recipients'] ) ? tp_sec_var($_POST['recipients']) : '';
          $recipients_option = isset ( $_POST['recipients_option'] ) ? tp_sec_var($_POST['recipients_option']) : '';
          $subject = isset ( $_POST['subject'] ) ? tp_sec_var($_POST['subject']) : '';
          $text = isset ( $_POST['text'] ) ? tp_sec_var($_POST['text']) : '';
          $attachments = isset ( $_POST['attachments'] ) ? $_POST['attachments'] : '';
          tp_mail::sendMail($from, $to, $recipients_option, $subject, $text, $attachments);
          $message = __('E-Mail sent','teachpress');
          get_tp_message($message);
     }

     // Event handler
     if ($action == 'show') {
           teachpress_editstudent_page();
     }
     else {
           $field1 = get_tp_option('regnum');
           $field2 = get_tp_option('studies');
           $order = '`lastname` ASC, `firstname` ASC';
           if ($search != "") {
                   $abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE `matriculation_number` like '%$search%' OR `wp_id` like '%$search%' OR `firstname` LIKE '%$search%' OR `lastname` LIKE '%$search%' ORDER BY " . $order . "";
           }
           else {
                   if ($students_group == 'alle' || $students_group == '') {
                           $abfrage = "SELECT * FROM " . $teachpress_stud . " ORDER BY " . $order . "";
                   }
                   else {
                           $abfrage = "SELECT * FROM " . $teachpress_stud . " WHERE `course_of_studies` = '$students_group' ORDER BY " . $order . "";
                   }
           }
           $test = $wpdb->query($abfrage);
           $abfrage = $abfrage . " LIMIT $entry_limit, $number_messages";
           ?>
           <div class="wrap">
           <form name="search" method="get" action="admin.php">
           <input name="page" type="hidden" value="<?php echo $page; ?>" />
           <?php
           // Delete students part 1
           if ( $bulk == "delete" ) {
                   echo '<div class="teachpress_message">
                   <p class="hilfe_headline">' . __('Are you sure to delete the selected students?','teachpress') . '</p>
                   <p><input name="delete_ok" type="submit" class="button-secondary" value="' . __('Delete','teachpress') . '"/>
                   <a href="admin.php?page=teachpress/students.php&amp;search=' . $search . '&amp;students_group=' . $students_group . '&amp;limit=' . $curr_page . '"> ' . __('Cancel','teachpress') . '</a></p>
                   </div>';
           }
           // Delete students part 2
           if ( isset($_GET['delete_ok']) ) {
                   tp_delete_student($checkbox, $user_ID);
                   $message = __('Students deleted','teachpress');
                   get_tp_message($message);
           }
           ?>
     <h2><?php _e('Students','teachpress'); ?></h2>
           <div id="searchbox" style="float:right; padding-bottom:5px;">  
           <?php if ($search != "") { ?>
           <a href="admin.php?page=teachpress/students.php" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a>
           <?php } ?>
           <input name="search" type="text" value="<?php echo stripslashes($search); ?>"/></td>
           <input name="go" type="submit" value="<?php _e('Search'); ?>" id="teachpress_search_senden" class="button-secondary"/>
     </div>
     <div class="tablenav" style="padding-bottom:5px;">
     <select name="bulk" id="bulk">
                   <option>- <?php _e('Bulk actions','teachpress'); ?> -</option>
                   <option value="delete"><?php _e('Delete','teachpress'); ?></option>
           </select>
           <input type="submit" name="teachpress_submit" value="<?php _e('OK','teachpress'); ?>" id="teachpress_submit2" class="button-secondary"/>
     <?php if ($field2 == '1') { ?>
           <select name="students_group" id="students_group">
                   <option value="alle">- <?php _e('All students','teachpress'); ?> -</option>
                   <?php
                   $row = "SELECT DISTINCT `course_of_studies` FROM " . $teachpress_stud . " ORDER BY `course_of_studies`";
                   $row = $wpdb->get_results($row);
                   foreach($row as $row){
                           if ($row->course_of_studies == $students_group) {
                                   $current = ' selected="selected"' ;
                           }
                           else {
                                   $current = '' ;
                           }
                           echo'<option value="' . $row->course_of_studies . '"' . $current . '>' . $row->course_of_studies . '</option>';
                   } ?>
                   </select>
           <input name="anzeigen" type="submit" id="teachpress_search_senden" value="<?php _e('Show','teachpress'); ?>" class="button-secondary"/>
     <?php }
           // Page Menu
           echo tp_admin_page_menu ($test, $number_messages, $curr_page, $entry_limit, 'admin.php?page=' . $page . '', 'search=' . $search . '&amp;students_group=' . $students_group . ''); ?>
     </div>
     <table border="1" cellpadding="5" cellspacing="0" class="widefat">
           <thead>
            <tr>
           <th class="check-column"><input name="tp_check_all" id="tp_check_all" type="checkbox" value="" onclick="teachpress_checkboxes();" /></th>
                   <?php
                   echo '<th>' . __('Last name','teachpress') . '</th>';
                   echo '<th>' . __('First name','teachpress') . '</th>'; 
                   if ($field1 == '1') {
                           echo '<th>' .  __('Matr. number','teachpress') . '</th>';
                   }
                   if ($field2 == '1') {
                           echo '<th>' .  __('Course of studies','teachpress') . '</th>';
                   }
                   $field3 = get_tp_option('termnumber');
                   if ($field3 == '1') {
                           echo '<th>' .  __('Number of terms','teachpress') . '</th>';
                   }
                   $field4 = get_tp_option('birthday');
                   if ($field4 == '1') {
                           echo '<th>' .  __('Date of birth','teachpress') . '</th>';
                   }
                   echo '<th>' . __('User account','teachpress') . '</th>';
                   echo '<th>' . __('E-Mail') . '</th>';
                   ?>
            </tr>
           </thead>
           <tbody> 
     <?php
           // Show students
           if ($test == 0) { 
                   echo '<tr><td colspan="9"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
           }
           else {
                   $row3 = $wpdb->get_results($abfrage);
                   foreach($row3 as $row3) { 
                           echo '<tr>';
                           echo '<th class="check-column"><input type="checkbox" name="checkbox[]" id="checkbox" value="' . $row3->wp_id . '"';
                           if ( $bulk == "delete") { 
                                   for( $i = 0; $i < count( $checkbox ); $i++ ) { 
                                           if ( $row3->wp_id == $checkbox[$i] ) { echo 'checked="checked"';} 
                                   } 
                           }
                           echo '/></th>';
                           echo '<td><a href="admin.php?page=teachpress/students.php&amp;student_ID=' . $row3->wp_id . '&amp;search=' . $search . '&amp;students_group=' . $students_group . '&amp;limit=' . $curr_page . '&amp;action=show" class="teachpress_link" title="' . __('Click to edit','teachpress') . '"><strong>' . stripslashes($row3->lastname) . '</strong></a></td>';
                           echo '<td>' . stripslashes($row3->firstname) . '</td>';
                           if ($field1 == '1') {
                                   echo '<td>' . $row3->matriculation_number . '</td>';
                           }
                           if ($field2 == '1') {
                                   echo '<td>' . stripslashes($row3->course_of_studies) . '</td>';
                           } 
                           if ($field3 == '1') {
                                   echo '<td>' . $row3->semesternumber . '</td>';
                           } 
                           if ($field4 == '1') {
                                   echo '<td>' . $row3->birthday . '</td>';
                           }
                           echo '<td>' . $row3->userlogin . '</td>';
                           echo '<td><a href="admin.php?page=teachpress/teachpress.php&amp;student_ID=' . $row3->wp_id . '&amp;search=' . $search . '&amp;students_group=' . $students_group . '&amp;limit=' . $curr_page . '&amp;action=mail&amp;single=' . $row3->email . '" title="' . __('send E-Mail','teachpress') . '">' . $row3->email . '</a></td>';
                           echo '</tr>';
                   } 
           }
           ?> 
           </tbody>
           </table>
     <div class="tablenav"><div class="tablenav-pages" style="float:right;">
           <?php 
           if ($test > $number_messages) {
                   echo tp_admin_page_menu ($test, $number_messages, $curr_page, $entry_limit, 'admin.php?page=' . $page . '', 'search=' . $search . '&amp;students_group=' . $students_group . '', 'bottom');
           } 
           else {
                   if ($test == 1) {
                           echo '' . $test . ' ' . __('entry','teachpress') . '';
                   }
                   else {
                           echo '' . $test . ' ' . __('entries','teachpress') . '';
                   }
           }?>
           </div></div>
           </form>
           </div>
           <?php
     }
} ?>