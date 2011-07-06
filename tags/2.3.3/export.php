<?php
/*
 * teachPress XLS and CSV export for courses
*/
if ( isset($_GET['lvs_ID']) && isset($_GET['type']) ) {
	include_once('version.php');
	
	// include wp-load.php
	require_once( '../../../wp-load.php' );
	
	// secure parameters
	$lvs = $_GET['lvs_ID'];
	settype ($lvs, 'integer');
	if (is_user_logged_in()) {
	
		$type = htmlspecialchars($_GET[type]);
		$filename = 'teachpress_' . date('dmY');
		
		// edit haeader
		if ($type == "xls") {
			header("Content-type: application/vnd-ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=" . $filename . ".xls");
		}
		if ($type == 'csv') {
			header('Content-Type: text/x-csv');
			header("Content-Disposition: attachment; filename=" . $filename . ".csv");
		}
		
		// Define databases
		global $wpdb;
		$teachpress_courses = $wpdb->prefix . 'teachpress_courses';
		$teachpress_stud = $wpdb->prefix . 'teachpress_stud';
		$teachpress_settings = $wpdb->prefix . 'teachpress_settings';
		$teachpress_signup = $wpdb->prefix . 'teachpress_signup';
		
		// For decoding chars
		$array_1 = array('Ã¼','Ã¶', 'Ã¤', 'Ã¤', 'Ã?','Â§','Ãœ','Ã','Ã–','&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
		$array_2 = array('ü','ö', 'ä', 'ä', 'ß', '§','Ü','Ä','Ö','Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
		
		// load course data
		$sql = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$lvs'";
		$daten = $wpdb->get_row($sql, ARRAY_A);
		if ($daten['parent'] != '0') {
			$id = $daten['parent'];
			$parent = $wpdb->get_var("SELECT name FROM " . $teachpress_courses . " WHERE course_id = '$id'");
		}
		
		// load settings
		$field1 = tp_get_option('regnum');
		$field2 = tp_get_option('studies');
		
		$sql = "SELECT s.matriculation_number, s.firstname, s.lastname, s.course_of_studies, s.userlogin, s.email, u.date, u.con_id, u.waitinglist
				FROM " . $teachpress_signup . " u
				INNER JOIN " . $teachpress_courses . " c ON c.course_id=u.course_id
				INNER JOIN " . $teachpress_stud . " s ON s.wp_id=u.wp_id";
		if ($type == "xls") {
			$order = "ORDER BY s.lastname ASC";	
			$where = "WHERE c.course_id = '$lvs' AND u.waitinglist = '0'";
			$row = $sql . " " . $where . " " . $order;
			$row = $wpdb->get_results($row, ARRAY_A);
			if ($parent != '') {
				$course_name = $parent . ' ' . $daten['name'];
			}
			else {
				$course_name = $daten['name'];
			}
			?>
			<h2><?php echo stripslashes(utf8_decode($course_name)); ?> <?php echo stripslashes(utf8_decode($daten['semester'])); ?> </h2>
			<table border="1" cellspacing="0" cellpadding="5">
			<thead>
			  <tr>
				<th><?php _e('Lecturer','teachpress'); ?></th>
				<td><?php echo stripslashes(utf8_decode($daten['lecturer'])); ?></td>
				<th><?php _e('Date','teachpress'); ?></th>
				<td><?php echo $daten['date']; ?></td>
				<th><?php _e('Room','teachpress'); ?></th>
				<td><?php echo stripslashes(utf8_decode($daten['room'])); ?></td>
			  </tr>
			  <tr>
				<th><?php _e('Places','teachpress'); ?></th>
				<td><?php echo $daten['places']; ?></td>
				<th><?php _e('free places','teachpress'); ?></th>
				<td><?php echo $daten['fplaces']; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<th><?php _e('Comment','teachpress'); ?></th>
				<td colspan="5"><?php echo stripslashes(utf8_decode($daten['comment'])); ?></td>
			  </tr>
			  <tr>
				<th><?php _e('URL','teachpress'); ?></th>
				<td colspan="5"><?php echo $daten['url']; ?></td>
			  </tr>
			  </thead>
			</table>
			<h3><?php _e('Registered participants','teachpress'); ?></h3>
			 <table border="1" cellpadding="5" cellspacing="0">
                 <thead>
                  <tr>
                      <th><?php _e('Last name','teachpress'); ?></th>
                      <th><?php _e('First name','teachpress'); ?></th>
                      <?php if ($field1 == '1') {?>
                      <th><?php _e('Matr. number','teachpress'); ?></th>
                      <?php } ?>
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
                    // Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
                    foreach($row as $row) {
					  	$row['firstname'] = str_replace($array_1, $array_2, $row['firstname']);
						$row['lastname'] = str_replace($array_1, $array_2, $row['lastname']);
						$row['course_of_studies'] = str_replace($array_1, $array_2, $row['course_of_studies']);
						?>
					  <tr>
						<td><?php echo stripslashes(utf8_decode($row['lastname'])); ?></td>
						<td><?php echo stripslashes(utf8_decode($row['firstname'])); ?></td>
						<?php if ($field1 == '1') {?>
						<td><?php echo $row['matriculation_number']; ?></td>
						<?php } ?>
						<?php if ($field2 == '1') {?>
						<td><?php echo stripslashes(utf8_decode( $row['course_of_studies'])); ?></td>
						<?php } ?>
						<td><?php echo $row['userlogin']; ?></td>
						<td><?php echo $row['email']; ?></td>
						<td><?php echo $row['date']; ?></td>
					  </tr>
					  <?php
                   }
                    ?>
                </tbody>
                </table>
                <?php
                // waitinglist
				$order = "ORDER BY u.date ASC";	
				$where = "WHERE c.course_id = '$lvs' AND u.waitinglist = '1'";
				$row = $sql . " " . $where . " " . $order;
				$test = $wpdb->query($row);
				$row = $wpdb->get_results($row, ARRAY_A);
                if ($test != 0) { ?>
                    <h3><?php _e('Waiting list','teachpress'); ?></h3>
                    <table border="1" cellpadding="5" cellspacing="0">
                     <thead>
                      <tr>
                          <th><?php _e('Last name','teachpress'); ?></th>
                          <th><?php _e('First name','teachpress'); ?></th>
                          <?php if ($field1 == '1') {?>
						  <td><?php echo $row['matriculation_number']; ?></td>
						  <?php } ?>
						  <?php if ($field2 == '1') {?>
						  <td><?php echo stripslashes(utf8_decode( $row['course_of_studies'])); ?></td>
						  <?php } ?>
                          <th><?php _e('User account','teachpress'); ?></th>
                          <th><?php _e('E-Mail','teachpress'); ?></th>
                          <th><?php _e('Registered at','teachpress'); ?></th>
                      </tr>
                     </thead>  
                     <tbody> 
                     <?php
                     foreach($row as $row) {
					 	$row['firstname'] = str_replace($array_1, $array_2, $row['firstname']);
						$row['lastname'] = str_replace($array_1, $array_2, $row['lastname']);
						$row['course_of_studies'] = str_replace($array_1, $array_2, $row['course_of_studies']);
					 ?>
                        <tr>
                            <td><?php echo stripslashes(utf8_decode($row['lastname'])); ?></td>
                            <td><?php echo stripslashes(utf8_decode($row['firstname'])); ?></td>
                            <?php if ($field1 == '1') {?>
							<td><?php echo $row['matriculation_number']; ?></td>
							<?php } ?>
							<?php if ($field2 == '1') {?>
							<td><?php echo stripslashes(utf8_decode( $row['course_of_studies'])); ?></td>
							<?php } ?>
                            <td><?php echo $row['userlogin']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                        </tr> 
                    <?php }?>
                    </tbody>
				</table>
			<?php  } 
			global $tp_version;
			?>       
			<p style="font-size:11px; font-style:italic;"><?php _e('Created on','teachpress'); ?>: <?php echo date("d.m.Y")?> | teachPress <?php echo $tp_version ?></p>
    <?php }  
	if ($type == 'csv') {
		$order = "ORDER BY s.lastname ASC";	
		$where = "WHERE c.course_id = '$lvs'";
		$row = $sql . " " . $where . " " . $order;
		$row = $wpdb->get_results($row, ARRAY_A);
		
		if ($field1 == '1') { $matr = "" . __('Matr. number','teachpress') . ";"; } else { $matr = ""; }
		if ($field2 == '1') { $cos = "" . __('Course of studies','teachpress') . ";"; } else { $cos = ""; }
		
		$headline = "" . __('Last name','teachpress') . ";" . __('First name','teachpress') . ";" . $matr . "" . $cos . "" . __('User account','teachpress') . ";" . __('E-Mail','teachpress') . ";" . __('Registered at','teachpress') . ";" . __('Record-ID','teachpress') . ";" . __('Waiting list','teachpress') . "\r\n";
		$headline = str_replace($array_1, $array_2, $headline);
		echo $headline;
		foreach($row as $row) {
			$row['firstname'] = str_replace($array_1, $array_2, $row['firstname']);
			$row['lastname'] = str_replace($array_1, $array_2, $row['lastname']);
			$row['course_of_studies'] = str_replace($array_1, $array_2, $row['course_of_studies']);
			
			if ($field1 == '1') { $matr = "" . $row['matriculation_number'] . ";"; } else { $matr = ""; }
			if ($field2 == '1') { $cos = "" . stripslashes(utf8_decode($row['course_of_studies'])) . ";"; } else { $cos = ""; }
			
			echo "" . stripslashes(utf8_decode($row['lastname'])) . ";" . stripslashes(utf8_decode($row['firstname'])) . ";" . $matr . "" . $cos . "" . $row['userlogin'] . ";" . $row['email'] . ";" . $row['date'] . ";" . $row['con_id'] . ";" . $row['waitinglist']. "\r\n";
		}
	} 
}
} ?>   