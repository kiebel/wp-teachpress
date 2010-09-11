<?php
/*
 * teachPress XLS and CSV export for courses
*/
if (isset($_REQUEST[lvs_ID]) && isset($_REQUEST[type]) ) {
	include_once('parameters.php');
	include_once('version.php');
	
	// include wp-load.php
	global $root;
	require( '' . $_SERVER['DOCUMENT_ROOT'] . '/' . $root . '/wp-load.php' );
	
	if (is_user_logged_in()) {
	
		$type = htmlspecialchars($_REQUEST[type]);
		$filename = 'teachpress_' . date('dmY');
		
		// edit haeader
		if ($type == "xls") {
			header("Content-type: application/vnd-ms-excel"); 
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
		
		$lvs = htmlspecialchars($_REQUEST[lvs_ID]);
		settype($lvs, 'integer');
		
		// load course data
		$sql = "SELECT * FROM " . $teachpress_courses . " WHERE course_id = '$lvs'";
		$daten = $wpdb->get_row($sql, ARRAY_A);
		if ($daten['parent'] != '0') {
			$id = $daten['parent'];
			$parent = $wpdb->get_var("SELECT name FROM " . $teachpress_courses . " WHERE course_id = '$id'");
		}
		
		// load enrollments
		$row = "SELECT s.matriculation_number, s.firstname, s.lastname, s.course_of_studies, s.userlogin, s.email, u.date, u.con_id, u.waitinglist
				FROM " . $teachpress_signup . " u
				INNER JOIN " . $teachpress_courses . " c ON c.course_id=u.course_id
				INNER JOIN " . $teachpress_stud . " s ON s.wp_id=u.wp_id
				WHERE c.course_id = '$lvs'
				ORDER BY s.matriculation_number";
		$row = $wpdb->get_results($row);
		$counter2 = 0;
		foreach($row as $row){
			$enrolls[$counter2]['matrikulation_number'] = $row->matriculation_number;
			$enrolls[$counter2]['firstname'] = $row->firstname;
			$enrolls[$counter2]['lastname'] = $row->lastname;
			$enrolls[$counter2]['course_of_studies'] = $row->course_of_studies;
			$enrolls[$counter2]['userlogin'] = $row->userlogin;
			$enrolls[$counter2]['email'] = $row->email;
			$enrolls[$counter2]['date'] = $row->date;
			$enrolls[$counter2]['con_id'] = $row->con_id;
			$enrolls[$counter2]['waitinglist'] = $row->waitinglist;
			$counter2++;
		}
		if ($type == "xls") {
			if ($parent != '') {
				$course_name = $parent . ' ' . $daten['name'];
			}
			else {
				$course_name = $daten['name'];
			}
			?>
			<h2><?php echo $course_name; ?> <?php echo $daten['semester'] ?> </h2>
			<table border="1" cellspacing="0" cellpadding="5">
			<thead>
			  <tr>
				<th><?php _e('Lecturer','teachpress'); ?></th>
				<td><?php echo $daten['lecturer']; ?></td>
				<th><?php _e('Date','teachpress'); ?></th>
				<td><?php echo $daten['date']; ?></td>
				<th><?php _e('Room','teachpress'); ?></th>
				<td><?php echo $daten['room']; ?></td>
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
				<td colspan="5"><?php echo $daten['comment']; ?></td>
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
                    // Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
                    for($i=0; $i<$counter2; $i++) {
                        if ($enrolls[$i]['waitinglist']== 0 ) {
                          ?>
                          <tr>
                            <td><?php echo $enrolls[$i]['matrikulation_number']; ?></td>
                            <td><?php echo $enrolls[$i]['lastname']; ?></td>
                            <td><?php echo $enrolls[$i]['lfirstname']; ?></td>
                            <td><?php echo $enrolls[$i]['course_of_studies']; ?></td>
                            <td><?php echo $enrolls[$i]['userlogin']; ?></td>
                            <td><?php echo $enrolls[$i]['email']; ?></td>
                            <td><?php echo $enrolls[$i]['date']; ?></td>
                          </tr>
                          <?php
                      }
                   }
                    ?>
                </tbody>
                </table>
                <?php
                // Ausgabe der waitinglist
                $test = 0;
                for($i=0; $i<$counter2; $i++) {
                    if ($enrolls[$i]['waitinglist']== 1 ) {
                        $test++;
                    }
                }	
                if ($test != 0) { ?>
                    <h3><?php _e('Waiting list','teachpress'); ?></h3>
                    <table border="1" cellpadding="5" cellspacing="0">
                     <thead>
                      <tr>
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
                        if ($enrolls[$i]['waitinglist']== 1 ) { ?>
                            <tr>
                                <td><?php echo $enrolls[$i]['matrikulation_number']; ?></td>
                                <td><?php echo $enrolls[$i]['lastname']; ?></td>
                                <td><?php echo $enrolls[$i]['lfirstname']; ?></td>
                                <td><?php echo $enrolls[$i]['course_of_studies']; ?></td>
                                <td><?php echo $enrolls[$i]['userlogin']; ?></td>
                                <td><?php echo $enrolls[$i]['email']; ?></td>
                                <td><?php echo $enrolls[$i]['date']; ?></td>
                            </tr> 
                        <?php } }?>
                    </tbody>
				</table>
			<?php  } 
			global $tp_version;
			?>       
			<p style="font-size:11px; font-style:italic;"><?php _e('Created on','teachpress'); ?>: <?php echo date("d.m.Y")?> | teachPress <?php echo $tp_version ?></p>  
    <?php }  
	if ($type == 'csv') {
		$headline = "" . __('Matr. number','teachpress') . ";" . __('First name','teachpress') . ";" . __('Last name','teachpress') . ";" . __('Course of studies','teachpress') . ";" . __('User account','teachpress') . ";" . __('E-Mail','teachpress') . ";" . __('Registered at','teachpress') . ";" . __('Record-ID','teachpress') . ";" . __('Waiting list','teachpress') . "\r\n";
		$array_1 = array('Ã¼','Ã¶', 'Ã¤', 'Ã¤', 'Ã?','Â§','Ãœ','Ã','Ã–','&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
		$array_2 = array('ü','ö', 'ä', 'ä', 'ß', '§','Ü','Ä','Ö','Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
		$headline = str_replace($array_1, $array_2, $headline);
		echo $headline;
		for($i=0; $i<$counter2; $i++) {
				$enrolls[$i]['matrikulation_number'] = str_replace($array_1, $array_2, $enrolls[$i]['matrikulation_number']);
				$enrolls[$i]['lfirstname'] = str_replace($array_1, $array_2, $enrolls[$i]['lfirstname']);
				$enrolls[$i]['lastname'] = str_replace($array_1, $array_2, $enrolls[$i]['lastname']);
				$enrolls[$i]['course_of_studies'] = str_replace($array_1, $array_2, $enrolls[$i]['course_of_studies']);
				$enrolls[$i]['userlogin'] = str_replace($array_1, $array_2, $enrolls[$i]['userlogin']);
				$enrolls[$i]['email'] = str_replace($array_1, $array_2, $enrolls[$i]['email']);
				$enrolls[$i]['date'] = str_replace($array_1, $array_2, $enrolls[$i]['date']);
				$enrolls[$i]['con_id'] = str_replace($array_1, $array_2, $enrolls[$i]['con_id']);
				$enrolls[$i]['waitinglist'] = str_replace($array_1, $array_2, $enrolls[$i]['waitinglist']);
				echo "" . $enrolls[$i]['matrikulation_number'] . ";" . $enrolls[$i]['lfirstname'] . ";" . $enrolls[$i]['lastname'] . ";" . $enrolls[$i]['course_of_studies'] . ";" . $enrolls[$i]['userlogin'] . ";" . $enrolls[$i]['email'] . ";" . $enrolls[$i]['date'] . ";" . $enrolls[$i]['con_id'] . ";" . $enrolls[$i]['waitinglist']. "\r\n";
		}
	} 
}
} ?>   