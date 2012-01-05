<?php
/**
 * teachPress export class
 *
 * @since 3.0.0
 */
class tp_export {
     
     /**
      * Get course registrations
      * @global CLASS $wpdb
      * @global STRING $teachpress_courses
      * @global STRING $teachpress_stud
      * @global STRING $teachpress_signup
      * @param INT $course_ID
      * @param INT $waitinglist
      * @return ARRAY_A 
      */
     function get_course_registrations($course_ID, $waitinglist = '') {
          global $wpdb;
          global $teachpress_courses;
          global $teachpress_stud;
          global $teachpress_signup;
          $sql = "SELECT s.matriculation_number, s.firstname, s.lastname, s.course_of_studies, s.userlogin, s.email, u.date, u.con_id, u.waitinglist
		  FROM " . $teachpress_signup . " u
		  INNER JOIN " . $teachpress_courses . " c ON c.course_id=u.course_id
		  INNER JOIN " . $teachpress_stud . " s ON s.wp_id=u.wp_id
                  WHERE c.course_id = '$course_ID'";
          if ( $waitinglist == '0' ) {
               $sql = $sql . " AND u.waitinglist = '0'";
          }
          if ( $waitinglist == '1' ) {
               $sql = $sql . " AND u.waitinglist = '1'";
          }
          $sql = $sql . " ORDER BY s.lastname ASC";
          return $wpdb->get_results($sql, ARRAY_A);
     }
     
     /**
      * Print html table with registrations
      * @param INT $course_ID
      * @param ARRAY $option
      * @param INT $waitinglist 
      */
     function get_course_registration_table($course_ID, $option, $waitinglist = '') {
          $row = tp_export::get_course_registrations($course_ID, $waitinglist);
          echo '<table border="1" cellpadding="5" cellspacing="0">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>' . __('Last name','teachpress') . '</th>';
          echo '<th>' . __('First name','teachpress') . '</th>';
          if ($option['regnum'] == '1') {
               echo '<th>' . __('Matr. number','teachpress') . '</th>';
          }
          if ($option['studies'] == '1') {
               echo '<th>' . __('Course of studies','teachpress') . '</th>';
          }
          echo '<th>' . __('User account','teachpress') . '</th>';
          echo '<th>' . __('E-Mail') . '</th>';
          echo '<th>' . __('Registered at','teachpress') . '</th>';
          echo '</tr>';
          echo '</thead>';  
          echo '<tbody>';
          foreach($row as $row) {
               $row['firstname'] = tp_export::decode($row['firstname']);
               $row['lastname'] = tp_export::decode($row['lastname']);
               $row['course_of_studies'] = tp_export::decode($row['course_of_studies']);
               echo '<tr>';
               echo '<td>' . stripslashes(utf8_decode($row['lastname'])) . '</td>';
               echo '<td>' . stripslashes(utf8_decode($row['firstname'])) . '</td>';
               if ($option['regnum'] == '1') {
                    echo '<td>' . $row['matriculation_number'] . '</td>';
               }
               if ($option['studies'] == '1') {
                    echo '<td>' . stripslashes(utf8_decode($row['course_of_studies'])) . '</td>';
               }
               echo '<td>' . $row['userlogin'] . '</td>';
               echo '<td>' . $row['email'] . '</td>';
               echo '<td>' . $row['date'] . '</td>';
               echo '</tr>';
           
         }
         echo '</tbody>';
         echo '</table>';
     }
     
     /**
      * Export course data in xls format
      * @global CLASS $wpdb
      * @global STRING $teachpress_courses
      * @global INT $tp_version
      * @param INT $course_ID 
      */
     function get_course_xls($course_ID) {
          global $wpdb;
          global $teachpress_courses;
          
          // load course data
          $daten = $wpdb->get_row("SELECT * FROM " . $teachpress_courses . " WHERE `course_id` = '$course_ID'", ARRAY_A);
          if ($daten['parent'] != '0') {
              $id = $daten['parent'];
              $parent = $wpdb->get_var("SELECT name FROM " . $teachpress_courses . " WHERE `course_id` = '$id'");
          }
          if ($parent != '') {
                 $course_name = $parent . ' ' . $daten['name'];
          }
          else {
                 $course_name = $daten['name'];
          }
          
          // load settings
          $option['regnum'] = get_tp_option('regnum');
          $option['studies'] = get_tp_option('studies');
          
          echo '<h2>' . stripslashes(utf8_decode($course_name)) . ' ' . stripslashes(utf8_decode($daten['semester'])) . '</h2>';
          echo '<table border="1" cellspacing="0" cellpadding="5">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>' . __('Lecturer','teachpress') . '</th>';
          echo '<td>' . stripslashes(utf8_decode($daten['lecturer'])) . '</td>';
          echo '<th>' . __('Date','teachpress') . '</th>';
          echo '<td>' . $daten['date'] . '</td>';
          echo '<th>' . __('Room','teachpress') . '</th>';
          echo '<td>' . stripslashes(utf8_decode($daten['room'])) . '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<th>' . __('Places','teachpress') . '</th>';
          echo '<td>' . $daten['places'] . '</td>';
          echo '<th>' . __('free places','teachpress') . '</th>';
          echo '<td>' . $daten['fplaces'] . '</td>';
          echo '<td>&nbsp;</td>';
          echo '<td>&nbsp;</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<th>' . __('Comment','teachpress') . '</th>';
          echo '<td colspan="5">' . stripslashes(utf8_decode($daten['comment'])) . '</td>';
          echo '</tr>';
          echo '</thead>';
          echo '</table>';
          
          echo '<h3>' . __('Registered participants','teachpress') . '</h3>'; 
          tp_export::get_course_registration_table($course_ID, $option, 0);
          echo '<h3>' . __('Waiting list','teachpress') . '</h3>'; 
          tp_export::get_course_registration_table($course_ID, $option, 1);
         
          global $tp_version;
          echo '<p style="font-size:11px; font-style:italic;">' . __('Created on','teachpress') . ': ' . date("d.m.Y") . ' | teachPress ' . $tp_version . '</p>';
     }
     
     /**
      * Export course data in csv format
      * @global CLASS $wpdb
      * @param INT $course_ID
      * @param ARRAY $options 
      */
     function get_course_csv($course_ID) {
          // load settings
          $option['regnum'] = get_tp_option('regnum');
          $option['studies'] = get_tp_option('studies');
          $row = $row = tp_export::get_course_registrations($course_ID, 0);

          if ($option['regnum'] == '1') { $matr = "" . __('Matr. number','teachpress') . ";"; } else { $matr = ""; }
          if ($option['studies'] == '1') { $cos = "" . __('Course of studies','teachpress') . ";"; } else { $cos = ""; }

          $headline = "" . __('Last name','teachpress') . ";" . __('First name','teachpress') . ";" . $matr . "" . $cos . "" . __('User account','teachpress') . ";" . __('E-Mail') . ";" . __('Registered at','teachpress') . ";" . __('Record-ID','teachpress') . ";" . __('Waiting list','teachpress') . "\r\n";
          $headline = tp_export::decode($headline);
          echo $headline;
          foreach($row as $row) {
              $row['firstname'] = tp_export::decode($row['firstname']);
              $row['lastname'] = tp_export::decode($row['lastname']);
              $row['course_of_studies'] = tp_export::decode($row['course_of_studies']);

              if ($option['regnum'] == '1') { $matr = "" . $row['matriculation_number'] . ";"; } else { $matr = ""; }
              if ($option['studies'] == '1') { $cos = "" . stripslashes(utf8_decode($row['course_of_studies'])) . ";"; } else { $cos = ""; }

              echo "" . stripslashes(utf8_decode($row['lastname'])) . ";" . stripslashes(utf8_decode($row['firstname'])) . ";" . $matr . "" . $cos . "" . $row['userlogin'] . ";" . $row['email'] . ";" . $row['date'] . ";" . $row['con_id'] . ";" . $row['waitinglist']. "\r\n";
          }
     }
     
     /**
      * Export publications
      * @global CLASS $wpdb
      * @global STRING $teachpress_pub
      * @global STRING $teachpress_tags
      * @global STRING $teachpress_relation
      * @global STRING $teachpress_user
      * @param INT $user_ID 
      * @param STRING $format - bibtex or rtf
      */
     function get_publication($user_ID, $format = 'bibtex') {
          global $wpdb;
          global $teachpress_pub;
          global $teachpress_tags;
          global $teachpress_relation;
          global $teachpress_user;
          $select = "p.pub_id, p.name, p.type, p.bibtex, p.author, p.editor, p.isbn, p.url, p.date, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.comment, p.note, p.is_isbn, p.rel_page, DATE_FORMAT(p.date, '%Y') AS jahr";
          if ( $user_ID != 0 ) {
               $sql = "SELECT DISTINCT " . $select . " 
                       FROM " . $teachpress_relation . " b
                       INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
                       INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
                       WHERE u.user = '$user_ID'
                       ORDER BY p.date DESC";
               $row = $wpdb->get_results($sql, ARRAY_A);
          }
          else {
               $row = $wpdb->get_results("SELECT " . $select . " FROM " . $teachpress_pub . " p ORDER BY `date` DESC", ARRAY_A);
          }
          if ( $format == 'bibtex' ) {
               foreach ($row as $row) {
                    $tags = $wpdb->get_results("SELECT DISTINCT t.name FROM " . $teachpress_tags . " t INNER JOIN  " . $teachpress_relation . " r ON r.`tag_id` = t.`tag_id` WHERE r.pub_id = '" . $row['pub_id'] . "' ", ARRAY_A);
                    echo tp_bibtex::get_single_publication_bibtex($row, $tags);
               }
          }     
          if ( $format == 'rtf' ) {
               echo tp_export::rtf($row);
          }
     }
     
     /**
      * Generate rtf document format
      * @param ARAY $row
      * @return STRING
      */
     function rtf ($row) {
          $head = '{\rtf1';
          $line = '';
          foreach ($row as $row) {
               $line = $line . tp_export::rtf_row($row) . '\par'. '\par';
          }
          $foot = '}';
          return $head . $line . $foot;
     }
     
     /**
      * Get single line for frt file
      * @param ARRAY $row
      * @return STRING 
      */
     function rtf_row ($row) {
          $settings['editor_name'] = 'initials';
          $all_authors = tp_bibtex::parse_author($row['author'], $settings['editor_name'] );
          $meta = tp_bibtex::single_publication_meta_row($row, $settings);
          $in = $row['editor'] != '' ? '' . __('In','teachpress') . ':' : '';
          $line = $all_authors . ' (' . $row['jahr'] . ')' . ': ' . stripslashes($row['name']) . ', ' . $in . $meta;
          $line = str_replace('  ', ' ', $line);
          $line = utf8_decode($line);
          return $line;
     }
     
     /**
      * Decode chars
      * @param STRING $char
      * @return STRING 
      */
     function decode ($char) {
          $array_1 = array('Ã¼','Ã¶', 'Ã¤', 'Ã¤', 'Ã?','Â§','Ãœ','Ã','Ã–','&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
	  $array_2 = array('ü','ö', 'ä', 'ä', 'ß', '§','Ü','Ä','Ö','Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
          $char = str_replace($array_1, $array_2, $char);
          return $char;
     }
}
?>
