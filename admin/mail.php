<?php
/**
 * Mail form
 * 
 * @global class $wpdb
 * @global var $teachpress_stud
 * @global var $teachpress_signup
 * @global $current_user;
 * 
 * @since 3.0.0
 */
function tp_show_mail_page() {
     global $wpdb;
     global $teachpress_stud; 
     global $teachpress_signup;
     global $current_user;
     get_currentuserinfo();
   
     $course_ID = isset( $_GET['course_ID'] ) ? tp_sec_var($_GET['course_ID'], 'integer') : '';
     $student_ID = isset( $_GET['student_ID'] ) ? tp_sec_var($_GET['student_ID'], 'integer') : '';
     $search = isset( $_GET['search'] ) ? tp_sec_var($_GET['search']) : '';
     $sem = isset( $_GET['sem'] ) ? tp_sec_var($_GET['sem']) : '';
     $single = isset( $_GET['single'] ) ? tp_sec_var($_GET['single']) : '';
     $students_group = isset( $_GET['students_group'] ) ? tp_sec_var($_GET['students_group']) : '';
     $limit = isset( $_GET['limit'] ) ? tp_sec_var($_GET['limit']) : '';
     
     if( !isset( $_GET['single'] ) ) {
          $sql = "SELECT DISTINCT st.email 
                     FROM " . $teachpress_signup . " s 
                     INNER JOIN " . $teachpress_stud . " st ON st.wp_id=s.wp_id
                     WHERE s.course_id = '$course_ID'";	
          // E-Mails of registered participants
          if ( $_GET['type'] == 'reg' ) {
               $sql = $sql . " AND s.waitinglist = '0'";	
          }
          // E-Mails of participants in waitinglist
          if ( $_GET['type'] == 'wtl' ) {
               $sql = $sql . " AND s.waitinglist = '1'";		
          }
          $sql = $sql . " ORDER BY st.lastname ASC";	
          $mails = $wpdb->get_results($sql, ARRAY_A);
     }
     ?>
     <div class="wrap">
          <?php
          if ( isset( $_GET['course_ID'] ) ) {
               $return_url = 'admin.php?page=teachpress/teachpress.php&amp;course_ID=' . $course_ID . '&amp;sem=' . $sem . '&amp;search=' . $search . '&amp;action=show';
          }
          if ( isset( $_GET['student_ID'] ) ) {
               $return_url = 'admin.php?page=teachpress/students.php&amp;student_ID=' . $student_ID . '&amp;search=' . $search . '&amp;students_group=' . $students_group . '&amp;limit=' . $limit;
          }
          ?>
          <p><a href="<?php echo $return_url; ?>" class="button-secondary">&larr; <?php _e('Back','teachpress'); ?></a></p>
          <h2><?php _e('Writing an E-Mail','teachpress'); ?></h2>
          <form name="form_mail" method="post" action="<?php echo $return_url; ?>">
          <table class="form-table">
               <tr>
                    <th scope="row" style="width: 65px;"><label for="mail_from"><?php _e('From','teachpress'); ?></label</th>
                    <td>
                         <select name="from" id="mail_from">
                              <option value="currentuser"><?php echo $current_user->display_name . ' (' . $current_user->user_email . ')'; ?></option>
                              <option value="wordpress"><?php echo get_bloginfo('name') . ' (' . get_bloginfo('admin_email') . ')'; ?></option>
                         </select>
                    </td>
               </tr>
               <tr>
                    <th scope="row" style="width: 65px;">
                         <select name="recipients_option" id="mail_recipients_option">
                              <option value="To"><?php _e('To','teachpress'); ?></option>
                              <option value="BCC"><?php _e('BCC','teachpress'); ?></option>
                         </select>
                    </th>
                    <td>
                        <?php
                        if( !isset( $_GET['single'] ) ) {
                             $to = '';
                             foreach($mails as $mail) { 
                                $to = $to . $mail["email"] . ', '; 
                             } 
                             $to = substr($to, 0, -2);
                        }
                        else {
                             $to = $single;
                        }
                        ?> 
                        <textarea name="recipients" id="mail_recipients" rows="3" style="width: 590px;"><?php echo $to; ?></textarea>
                    </td>
               </tr>
               <tr>
                    <th scope="row" style="width: 65px;"><label for="mail_subject"><?php _e('Subject','teachpress'); ?></label></th>
                    <td><input name="subject" id="mail_subject" type="text" style="width: 590px;"/></td>
               </tr>
          </table>
          <br />
          <textarea name="text" style="width: 685px;" rows="15"></textarea>
          
          <br />
          <input type="submit" class="button-primary" name="send_mail" value="<?php _e('Send','teachpress'); ?>"/>
          </form>
     </div>
     <?php
}
?>