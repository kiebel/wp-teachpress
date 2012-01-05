<?php
/**
 * teachPress E-Mail class
 */
class tp_mail {
     
     /**
      * Send E-Mail
      * @param STRING $from
      * @param STRING $to
      * @param STRING $subject
      * @param STRING $message
      * @param STRING $attachments
      */
     function sendMail($from, $to, $recipients_option, $subject, $message, $attachments = '') {
          global $current_user;
          get_currentuserinfo();
          
          if ( $from == 'currentuser' ) {
               $headers = 'From: ' . $current_user->display_name . ' ' . utf8_decode(chr(60)) . $current_user->user_email . utf8_decode(chr(62)) . "\r\n";
          }
          else {
               $headers = 'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>' . "\r\n";
          }
          
          if ( $recipients_option == 'BCC' ) {
               $headers = $headers . tp_mail::prepareBCC($to);
               // Replace Recipients with the E-Mail Author
               $to = '';
          }
          
          $message = htmlspecialchars($message);
          if ($from != '') {
               wp_mail($to, $subject, $message, $headers, $attachments);
          }
     }
     
     /**
      * Prepare BCC field for E-Mail header
      * @param STRING $recipients
      * @return STRING
      */
     function prepareBCC($recipients) {
          $array = explode(",",$recipients);
          $bcc = '';
          foreach ($array as $recipient) {
               $recipient = trim($recipient);
               if ( !is_email($recipient) ) { continue; }
               if ( !empty($recipient) ) {
                    if ($bcc == '') {
                         $bcc = 'Bcc: ' . $recipient;
                    }
                    else {
                         $bcc = $bcc . ', ' . $recipient;
                    }
               }
          }
          return $bcc . "\r\n";
     }
}
?>
