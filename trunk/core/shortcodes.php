<?php
/**************/
/* Shortcodes */
/**************/

/* Show the enrollment system
 * @param $atts
 * Return: $asg (String)
 * used in: WordPress-Core
*/
function tpenrollments_shortcode($atts) {		
	// Advanced Login
	$tp_login = tp_get_option('login');
	if ( $tp_login == 'int' ) {
		tp_advanced_registration();
	}
	// WordPress
	global $wpdb;
	global $user_ID;
	global $user_email;
	global $user_login;
	get_currentuserinfo();

	// teachPress
	global $teachpress_courses; 
	global $teachpress_stud; 
	global $teachpress_settings; 
	global $teachpress_signup;
	$sem = tp_get_option('sem');
	$is_sign_out = tp_get_option('sign_out');
	$permalink = tp_get_option('permalink');

	// Form
	global $pagenow;
	$wp_id = $user_ID;
	$aendern = $_POST[aendern];
	$eintragen = $_POST[eintragen];
	$austragen = $_POST[austragen];
	$checkbox = $_POST[checkbox];
	$checkbox2 = $_POST[checkbox2];
	$einschreiben = $_POST[einschreiben];
	$tab = tp_sec_var($_GET[tab]);

	// Registration
	$data['firstname'] = tp_sec_var($_POST[firstname]);
	$data['lastname'] = tp_sec_var($_POST[lastname]);
	$data['course_of_studies'] = tp_sec_var($_POST[course_of_studies]);
	$data['semesternumber'] = tp_sec_var($_POST[semesternumber], 'integer');
	$data['userlogin'] = $user_login;
	$data['birth_day'] = tp_sec_var($_POST[birth_day]);
	$data['birth_month'] = tp_sec_var($_POST[birth_month]);
	$data['birth_year'] = tp_sec_var($_POST[birth_year], 'integer');
	$data['email'] = $user_email;
	$data['matriculation_number'] = tp_sec_var($_POST[matriculation_number], 'integer');

	// Edit form
	$data2['matriculation_number'] = tp_sec_var($_POST[matriculation_number2], 'integer');
	$data2['firstname'] = tp_sec_var($_POST[firstname2]);
	$data2['lastname'] = tp_sec_var($_POST[lastname2]);
	$data2['course_of_studies'] = tp_sec_var($_POST[course_of_studies2]);
	$data2['semesternumber'] = tp_sec_var($_POST[semesternumber2], 'integer');
	$data2['birthday'] = tp_sec_var($_POST[birthday2]);
	$data2['email'] = tp_sec_var($_POST[email2]);
	
	$str = "'";
	
	$a1 = '<div id="enrollments">
    		<h2 class="tp_enrollments">' . __('Enrollments for the','teachpress') . ' ' . $sem . '</h2>
    		<form name="anzeige" method="post" id="anzeige" action="' . $PHP_SELF . '">';
    /*
     * Messages
    */ 
    if ( isset($aendern) || isset($austragen) || isset($einschreiben) || isset($eintragen) ) { 
        if ( isset($aendern)) {
            $a2 = tp_change_student($wp_id, $data2, 0);
        }
        if ( isset($austragen)) {
            $a2 = tp_delete_registration_student($checkbox2);
        }
        if ( isset($einschreiben)) {
			for ($n = 0; $n < count( $checkbox ); $n++) {
            	$a2 = $a2 . tp_add_registration($checkbox[$n], $wp_id);
			}
        }	
        if ( isset($eintragen)) {
            $ret = tp_add_student($wp_id, $data);
            if ($ret != false) {
                $a2 = '<div class="teachpress_message"><strong>' . __('Registration successful','teachpress') . '</strong></div>';
            }
            else {
                $a2 = '<div class="teachpress_message"><strong>' . __('Error: User already exist','teachpress') . '</strong></div>';
            }
        } 
    }

	/*
	 * User status
	*/ 
	if (is_user_logged_in()) {
		$auswahl = "Select wp_id FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
		$auswahl = $wpdb->get_var($auswahl);
		// if user is not registered
		if($auswahl == '' ) {
			/*
			 * Registration
			*/
			$a3 = '<div id="eintragen">
					<p style="text-align:left; color:#FF0000;">' . __('Please fill in the following registration form and sign up in the system. You can edit your data later.','teachpress') . '</p>
					<fieldset style="border:1px solid silver; padding:5px;">
						<legend>' . __('Your data','teachpress') . '</legend>
						<table border="0" cellpadding="0" cellspacing="5" style="text-align:left; padding:5px;">';
			$field1 = tp_get_option('regnum');
			if ($field1 == '1') { 
				$a3 = $a3 . '<tr>
							 <td><label for="matriculation_number">' . __('Matr. number','teachpress') . '</label></td>
							 <td><input type="text" name="matriculation_number" id="matriculation_number" /></td>
			  				 </tr>';
            } 
			$a3 = $a3 . '<tr>
						 <td><label for="firstname">' . __('First name','teachpress') . '</label></td>
						 <td><input name="firstname" type="text" id="firstname" /></td>
			  			 </tr>
			  			 <tr>
						 <td><label for="lastname">' . __('Last name','teachpress') . '</label></td>
						 <td><input name="lastname" type="text" id="lastname" /></td>
			  			 </tr>';
            $field2 = tp_get_option('studies');
        	if ($field2 == '1') {
				$a3 = $a3 . '<tr>
							 <td><label for="course_of_studies">' . __('Course of studies','teachpress') . '</label></td>
							 <td>
							 <select name="course_of_studies" id="course_of_studies">';
				global $teachpress_settings;
				$rowstud = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'course_of_studies'";
				$rowstud = $wpdb->get_results($rowstud);
				foreach ($rowstud as $rowstud) {
					$a3 = $a3 . '<option value="' . $rowstud->value . '">' . $rowstud->value . '</option>';
				} 
				$a3 = $a3 . '</select>
							 </td>
			  				 </tr>';
			}
			$field2 = tp_get_option('termnumber');
			if ($field2 == '1') {
				$a3 = $a3 . '<tr>
							 <td><label for="semesternumber">' . __('Number of terms','teachpress') . '</label></td>
							 <td style="text-align:left;">
							 <select name="semesternumber" id="semesternumber">';
				for ($i=1; $i<20; $i++) {
					$a3 = $a3 . '<option value="' . $i . '">' . $i . '</option>';
				}
				$a3 = $a3 . '</select>
							 </td>
			  				 </tr>';
			}
			$a3 = $a3 . '<tr>
						 <td>' . __('User account','teachpress') . '</td>
						 <td style="text-align:left;"><?php echo"$user_login" ?></td>
			  			 </tr>';
			$field2 = tp_get_option('birthday');
        	if ($field2 == '1') {
				$a3 = $a3 . '<tr>
							 <td><label for="birth_day">' . __('Date of birth','teachpress') . '</label></td>
							 <td><input name="birth_day" id="birth_day" type="text" title="Day" size="2" value="01"/>
							 	<select name="birth_month" title="Month">
									<option value="01">' . __('Jan','teachpress') . '</option>
									<option value="02">' . __('Feb','teachpress') . '</option>
									<option value="03">' . __('Mar','teachpress') . '</option>
									<option value="04">' . __('Apr','teachpress') . '</option>
									<option value="05">' . __('May','teachpress') . '</option>
									<option value="06">' . __('Jun','teachpress') . '</option>
									<option value="07">' . __('Jul','teachpress') . '</option>
									<option value="08">' . __('Aug','teachpress') . '</option>
									<option value="09">' . __('Sep','teachpress') . '</option>
									<option value="10">' . __('Oct','teachpress') . '</option>
									<option value="11">' . __('Nov','teachpress') . '</option>
									<option value="12">' . __('Dec','teachpress') . '</option>
								</select>
								<input name="birth_year" type="text" title="Year" size="4" value="19xx"/>
							 </td>
			  				 </tr>';
            }
			$a3 = $a3 . '<tr>
						 <td>' . __('E-Mail','teachpress') . '</td>
						 <td>' . $user_email . '</td>
			  			 </tr>
						</table>
						</fieldset>
        				<input name="eintragen" type="submit" id="eintragen" onclick="teachpress_validateForm(' . $str . 'firstname' . $str .',' . $str . $str . ',' . $str . 'R' . $str . ',' . $str . 'lastname' . $str . ',' . $str . $str . ',' . $str . 'R' . $str . ');return document.teachpress_returnValue" value="' . __('Send','teachpress') . '" />
						</div>
						</form>';
		}
		else {
			// Select all user information
			$auswahl = "Select * FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
			$auswahl = $wpdb->get_results($auswahl);
			foreach ($auswahl as $row) {
				/*
				 * Menu
				*/
				$a5 = '<div class="tp_user_menu" style="padding:5px;">
					   <h4>' . __('Hello','teachpress') . ', ' . stripslashes($row->firstname) . ' ' . stripslashes($row->lastname) . '</h4>';
				// handle permalink usage
				// No Permalinks: Page or Post?
				if (is_page()) {
					$page = "page_id";
				}
				else {
					$page = "p";
				}
				// Define permalinks
				if ($permalink == '1') {
					$link = $pagenow;
					$link = str_replace("index.php", "", $link);
					$link = $link . '?tab=';
				}
				else {
					$postid = get_the_ID();
					$link = $pagenow;
					$link = str_replace("index.php", "", $link);
					$link = $link . '?' . $page . '=' . $postid . '&amp;tab=';
				}
				// Create Tabs
				if ($tab == '' || $tab == 'current') {
					$tab1 = '<strong>' . __('Current enrollments','teachpress') . '</strong>';
				}
				else {
					$tab1 = '<a href="' . $link . 'current">' . __('Current enrollments','teachpress') . '</a>';
				}
				if ($tab == 'old') {
					$tab2 = '<strong>' . __('Your enrollments','teachpress') . '</strong>';
				}
				else {
					$tab2 = '<a href="' . $link . 'old">' . __('Your enrollments','teachpress') . '</a>';
				}
				if ($tab == 'data') {
					$tab3 = '<strong>' . __('Your data','teachpress') . '</strong>';
				}
				else {
					$tab3 = '<a href="' . $link . 'data">' . __('Your data','teachpress') . '</a>';
				}
				$a5 = $a5 . '<p>' . $tab1 . ' | ' . $tab2 . ' | ' . $tab3 . '</p>
							</div>';
			
				/*
				 * Old Enrollments / Sign out
				*/
				if ($tab == 'old') {
					$a5 = $a5 . '<p><strong>' . __('Signed up for','teachpress') . '</strong></p>   
								<table class="teachpress_enr_old" border="1" cellpadding="5" cellspacing="0">
								<tr>';
					if ($is_sign_out == '0') {
						$a5 = $a5 . '<th>&nbsp;</th>';
					}
					$a5 = $a5 . '<th>' . __('Name','teachpress') . '</th>
								 <th>' . __('Type','teachpress') . '</th>
								 <th>' . __('Date','teachpress') . '</th>
								 <th>' . __('Room','teachpress') . '</th>
								 <th>' . __('Term','teachpress') . '</th>
								</tr>';
				// Select all courses where user is registered
				$row1 = "SELECT wp_id, v_id, b_id, waitinglist, name, type, room, date, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.course_id as v_id, k.con_id as b_id, k.waitinglist as waitinglist, v.name as name, v.type as type, v.room as room, v.date as date, v.semester as semester, p.name as parent_name FROM " . $teachpress_signup . " k INNER JOIN " . $teachpress_courses . " v ON k.course_id = v.course_id LEFT JOIN " . $teachpress_courses . " p ON v.parent = p.course_id ) AS temp 
				WHERE wp_id = '$row->wp_id' AND waitinglist = '0' 
				ORDER BY b_id DESC";
				$row1 = $wpdb->get_results($row1);
				foreach($row1 as $row1) {
					$row1->parent_name = stripslashes($row1->parent_name);
					$row1->name = stripslashes($row1->name);
					if ($row1->parent_name != "") {
						$row1->parent_name = '' . $row1->parent_name . ' -';
					}
					$a5 = $a5 . '<tr>';
					if ($is_sign_out == '0') {
						$a5 = $a5 . '<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>';
					}		
					$a5 = $a5 . '<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
								 <td>' . stripslashes($row1->type) . '</td>
								 <td>' . stripslashes($row1->date) . '</td>
								 <td>' . stripslashes($row1->room) . '</td> 
								 <td>' . stripslashes($row1->semester) . '</td>
								</tr>';
				}
				$a5 = $a5 . '</table>';
				// all courses where user is registered in a waiting list
				$row1 = "SELECT wp_id, v_id, b_id, waitinglist, name, type, room, date, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.course_id as v_id, k.con_id as b_id, k.waitinglist as waitinglist, v.name as name, v.type as type, v.room as room, v.date as date, v.semester as semester, p.name as parent_name FROM " . $teachpress_signup . " k INNER JOIN " . $teachpress_courses . " v ON k.course_id = v.course_id LEFT JOIN " . $teachpress_courses . " p ON v.parent = p.course_id ) AS temp 
				WHERE wp_id = '$row->wp_id' AND waitinglist = '1' 
				ORDER BY b_id DESC";
				$test = $wpdb->query($row1);
				if ($test != 0) {
					$a5 = $a5 . '<p><strong>' . __('Waiting list','teachpress') . '</strong></p>
								<table class="teachpress_enr_old" border="1" cellpadding="5" cellspacing="0">
								<tr>';
					if ($is_sign_out == '0') {
						$a5 = $a5 . '<th>&nbsp;</th>';
					}
					$a5 = $a5 . '<th>' . __('Name','teachpress') . '</th>
								 <th>' . __('Type','teachpress') . '</th>
								 <th>' . __('Date','teachpress') . '</th>
								 <th>' . __('Room','teachpress') . '</th>
								 <th>' . __('Term','teachpress') . '</th>
								</tr>';
					$row1 = $wpdb->get_results($row1);
					foreach($row1 as $row1) {
						if ($row1->parent_name != "") {
							$row1->parent_name = '' . $row1->parent_name . ' -';
						}
						$row1->parent_name = stripslashes($row1->parent_name);
						$row1->name = stripslashes($row1->name);
						$a5 = $a5 . '<tr>';
						if ($is_sign_out == '0') {
							$a5 = $a5 . '<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>';
						}		
						$a5 = $a5 . '<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
									 <td>' . stripslashes($row1->type) . '</td>
									 <td>' . stripslashes($row1->date) . '</td>
									 <td>' . stripslashes($row1->room) . '</td> 
									 <td>' . stripslashes($row1->semester) . '</td>
									</tr>';
						}
					$a5 = $a5 . '</table>';
					}
					if ($is_sign_out == '0') {
						$a5 = $a5 . '<p><input name="austragen" type="submit" value="' . __('unsubscribe','teachpress') . '" id="austragen" /></p>';
					}
				}	
				/*
				 * Edit userdata
				*/
				if ($tab == 'data') {
					$a5 = $a5 . '<table class="teachpress_enr_edit">';
					$field1 = tp_get_option('regnum');
					if ($field1 == '1') {
						$a5 = $a5 . '<tr>
									 <td><label for="matriculation_number2">' . __('Matr. number','teachpress') . '</label></td>
									 <td><input type="text" name="matriculation_number2" id="matriculation_number2" value="' . $row->matriculation_number . '"/></td>
									 </tr>';
					}  
                	$a5 = $a5 . '<tr>
                    			 <td><label for="firstname2">' . __('First name','teachpress') . '</label></td>
                    			 <td><input name="firstname2" type="text" id="firstname2" value="' . stripslashes($row->firstname) . '" size="30"/></td>
                  	 			 </tr>';
                	$a5 = $a5 . '<tr>
                    			 <td><label for="lastname2">' . __('Last name','teachpress') . '</label></td>
                    			 <td><input name="lastname2" type="text" id="lastname2" value="' . stripslashes($row->lastname) . '" size="30"/></td>
                  	  			 </tr>';
					$field2 = tp_get_option('studies');
					if ($field2 == '1') { 
						$a5 = $a5 . '<tr>
									 <td><label for="course_of_studies2">' . __('Course of studies','teachpress') . '</label></td>
									 <td><select name="course_of_studies2" id="course_of_studies2">';
						$stud = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'course_of_studies'";
						$stud = $wpdb->get_results($stud);
						foreach($stud as $stud) { 
							if ($stud->value == $row->course_of_studies) {
								$current = 'selected="selected"' ;
							}
							else {
								$current = '' ;
							}
							$a5 = $a5 . '<option value="' . stripslashes($stud->value) . '" ' . $current . '>' . stripslashes($stud->value) . '</option>';
						} 
						$a5 = $a5 . '</select>
									 </td>
									 </tr>';
					}
					$field3 = tp_get_option('termnumber');
					if ($field3 == '1') {
						$a5 = $a5 . '<tr>
									 <td><label for="semesternumber2">' . __('Number of terms','teachpress') . '</label></td>
									 <td><select name="semesternumber2" id="semesternumber2">';
						for ($i=1; $i<20; $i++) {
							if ($i == $row->semesternumber) {
								$current = 'selected="selected"' ;
							}
							  else {
									$current = '' ;
							  }
							  $a5 = $a5 . '<option value="' . $i . '" ' . $current . '>' . $i . '</option>';
						}  
						$a5 = $a5 . '</select>
									 </td>
									 </tr>';
					}
					$field4 = tp_get_option('birthday');
					if ($field4 == '1') {
						$a5 = $a5 . '<tr>
									 <td><label for="birthday2">' . __('Date of birth','teachpress') . '</label></td>
									 <td><input name="birthday2" type="text" value="' . $row->birthday . '" size="30"/>
							  		 <em>' . __('Format: JJJJ-MM-TT','teachpress') . '</em></td>
							 		 </tr>';
					}
					$a5 = $a5 . '<tr>
								 <td><label for="email2">' . __('E-Mail','teachpress') . '</label></td>
								 <td><input name="email2" type="text" id="email2" value="' . $row->email . '" size="50" readonly="true"/></td>
						 		 </tr>
								 </table>';
					if ($field1 != '1') {
						$a5 = $a5 . '<input type="hidden" name="matriculation_number2" value="' . $row->matriculation_number . '" />';
					}
					if ($field2 != '1') {
						$a5 = $a5 . '<input type="hidden" name="course_of_studies2" value="' . $row->course_of_studies . '" />';
					}
					if ($field3 != '1') {
						$a5 = $a5 . '<input type="hidden" name=semesternumber2"" value="' . $row->semesternumber . '" />';
					}
					if ($field4 != '1') {
						$a5 = $a5 . '<input type="hidden" name="birthday2" value="' . $row->birthday . '" />';
					}
					$a5 = $a5 . '<input name="aendern" type="submit" id="aendern" onclick="teachpress_validateForm(' . $str . 'matriculation_number2' . $str . ',' . $str . $str . ',' . $str . 'RisNum' . $str . ',' . $str . 'firstname2' . $str . ',' . $str . $str . ',' . $str . 'R' . $str . ',' . $str . 'lastname2' . $str . ',' . $str . $str . ',' . $str . 'R' . $str . ',' . $str . 'email2' . $str . ',' . $str . $str . ',' . $str . 'RisEmail' . $str . ');return document.teachpress_returnValue" value="senden" />';
           	 }
			}
		}
	}
	/*
	 * Enrollments
	*/
	if ($tab == '' || $tab == 'current') {
		// Select all courses where enrollments in the current term are available
		$row = "SELECT * FROM " . $teachpress_courses . " WHERE semester = '$sem' AND parent = '0' AND visible = '1' ORDER BY type DESC, name";
		$row = $wpdb->get_results($row);
		foreach($row as $row) {
			$date1 = $row->start;
			$date2 = $row->end;
			$a6 = $a6 . '<div class="teachpress_course_group">
				   		 <div class="teachpress_course_name"><a href="' . get_permalink($row->rel_page) . '">' . stripslashes($row->name) . '</a></div>
				   		 <table class="teachpress_enr" width="100%" border="0" cellpadding="1" cellspacing="0">
				   		 <tr>
						 <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">';
			if (is_user_logged_in() && $auswahl != '') {
				if ($date1 != '0000-00-00 00:00:00' && current_time('mysql') >= $date1 && current_time('mysql') <= $date2) {
					$a6 = $a6 . '<input type="checkbox" name="checkbox[]" value="' . $row->course_id . '" title="' . stripslashes($row->name) . ' ' . __('select','teachpress') . '" id="checkbox_' . $row->course_id . '"/>';
				} 
			}
			else {
				$a6 = $a6 . '&nbsp;';
			}	
			$a6 = $a6 . '</td>
						 <td colspan="2">&nbsp;</td>
						 <td align="center"><strong>' . __('Date(s)','teachpress') . '</strong></td>
						 <td align="center">';
			if ($date1 != '0000-00-00 00:00:00') {
				$a6 = $a6 . '<strong>' . __('free places','teachpress') . '</strong>';
			}
			$a6 = $a6 . '</td>
						</tr>
						<tr>
						 <td width="20%" style="font-weight:bold;">';
			if ($date1 != '0000-00-00 00:00:00' && current_time('mysql') >= $date1 && current_time('mysql') <= $date2) {
				$a6 = $a6 . '<label for="checkbox_' . $row->course_id . '" style="line-height:normal;">';
			}
			$a6 = $a6 . stripslashes($row->type);
			if ($date1 != '0000-00-00 00:00:00' && current_time('mysql') >= $date1 && current_time('mysql') <= $date2) {
				$a6 = $a6 . '</label>';
			}
			$a6 = $a6 . '</td>
						 <td width="20%">' . stripslashes($row->lecturer) . '</td>
						 <td align="center">' . stripslashes($row->date) . ' ' . stripslashes($row->room) . '</td>
						 <td align="center">';
			if ($date1 != '0000-00-00 00:00:00') { 
				$a6 = $a6 . $row->fplaces . ' ' . __('of','teachpress') . ' ' .  $row->places;
			}
			$a6 = $a6 . '</td>
						</tr>
						<tr>
						 <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="waitinglist">';
			if ($row->waitinglist == 1 && $row->fplaces == 0) {
				$a6 = $a6 . __('Possible to subscribe in the waiting list','teachpress'); 
			}
			else {
				$a6 = $a6 . '&nbsp;';
			}
			$a6 = $a6 . '</td>
						 <td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist">';
			if ($date1 != '0000-00-00 00:00:00') {
				$a6 = $a6 . __('Registration period','teachpress') . ': ' . substr($row->start,0,strlen($row->start)-3) . ' ' . __('to','teachpress') . ' ' . substr($row->end,0,strlen($row->end)-3);
			}
			$a6 = $a6 . '</td>
						</tr>';
			// Select all childs
			$row2 = "Select * FROM " . $teachpress_courses . " WHERE parent = '$row->course_id' AND visible = '1' ORDER BY course_id";
			$row2 = $wpdb->get_results($row2);
			foreach ($row2 as $row2) {
				$date3 = $row2->start;
				$date4 = $row2->end;
				if ($row->name == $row2->name) {
					$row2->name = $row->type;
				}
				$a6 = $a6 . '<tr>
							 <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">';
				if (is_user_logged_in() && $auswahl != '') {
					if ($date3 != '0000-00-00 00:00:00' && current_time('mysql') >= $date3 && current_time('mysql') <= $date4) {
						$a6 = $a6 . '<input type="checkbox" name="checkbox[]" value="' . $row2->course_id . '" title="' . stripslashes($row2->name) . ' ausw&auml;hlen" id="checkbox_' . $row2->course_id . '"/>';
					}
				}
				$a6 = $a6 . '</td>
						 	 <td colspan="2">&nbsp;</td>
						 	 <td align="center"><strong>' . __('Date(s)','teachpress') . '</strong></td>
							 <td align="center"><strong>' . __('free places','teachpress') . '</strong></td>
							</tr>
							<tr>
							 <td width="20%" style="font-weight:bold;">';
				if ($date1 != '0000-00-00 00:00:00' && current_time('mysql') >= $date1 && current_time('mysql') <= $date2) {
					$a6 = $a6 . '<label for="checkbox_' . $row2->course_id . '" style="line-height:normal;">';
				}
				$a6 = $a6 . $row2->name;
				if ($date1 != '0000-00-00 00:00:00' && current_time('mysql') >= $date1 && current_time('mysql') <= $date2) {
					$a6 = $a6 . '</label>';
				}
				$a6 = $a6 . '</td>
							 <td width="20%">' . stripslashes($row2->lecturer) . '</td>
							 <td align="center">' . stripslashes($row2->date) . ' ' . stripslashes($row2->room) . '</td>
							 <td align="center">' . $row2->fplaces . ' ' . __('of','teachpress') . ' ' . $row2->places . '</td>
							</tr>
							<tr>
							 <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="waitinglist">';
				$a6 = $a6 . stripslashes($row2->comment) . ' ';
				if ($row2->waitinglist == 1 && $row2->fplaces == 0) {
					$a6 = $a6 . __('Possible to subscribe in the waiting list','teachpress');
				} 
				else {
					$a6 = $a6 . '&nbsp;';
				}
				$a6 = $a6 . '</td>
							 <td align="center" class="einschreibefrist" style="border-bottom:1px solid silver; border-collapse: collapse;">';
				if ($date3 != '0000-00-00 00:00:00') {
					$a6 = $a6 . __('Registration period','teachpress') . ': ' . substr($row2->start,0,strlen($row2->start)-3) . ' ' . __('to','teachpress') . ' ' . substr($row2->end,0,strlen($row2->end)-3);
				}
				$a6 = $a6 . '</td>
							</tr>'; 
			} 
			// End (search for childs)
			$a6 = $a6 . '</table>
						</div>';
		}	
		if (is_user_logged_in() && $auswahl != '') {
			$a6 = $a6 . '<input name="einschreiben" type="submit" value="' . __('Sign up','teachpress') . '" />';
		}
	}
	$a6 = $a6 . '</form>
				</div>';
	$asg = $a1 . $a2 . $a3 . $a4 . $a5 . $a6;
	return $asg;
}

/* Show the course overview
 * @param $atts (Array)
 * @param $semester(String, GET)
 * Return: $asg (String)
 * used in: WordPress-Core
*/
function tp_courselist_shortcode($atts) {	
	global $wpdb;
	global $teachpress_courses; 
	global $teachpress_settings; 
	// Shortcode options
	extract(shortcode_atts(array(
		'image' => 'none',
		'image_size' => 0,
	), $atts));
	$image = tp_sec_var($image);
	settype($image_size, 'integer');
	
	$permalink = tp_get_option('permalink');
	$sem = tp_get_option('sem');
	$postid = get_the_ID();
	
	if (is_page()) {
		$page = "page_id";
	}
	else {
		$page = "p";
	}
	
	$semester = tp_sec_var($_GET[semester]);	
	if ($semester != "") {
		$sem = $semester;
	}

	$a1 = '<div id="tpcourselist">
			<h2>' . __('Courses for the','teachpress') . ' ' . stripslashes($sem) . '</h2>
			<form name="lvs" method="get">
			<input type="hidden" name="' . $page . '" id="' . $page . '" value="' . $postid . '"/>
			<div class="tp_auswahl">' . __('Select the term','teachpress') . ' <select name="semester">';
	$rowsem = "SELECT value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id DESC";
	$rowsem = $wpdb->get_results($rowsem);
	foreach($rowsem as $rowsem) { 
		if ($rowsem->value == $sem) {
			$current = 'selected="selected"' ;
		}
		else {
			$current = '';
		}
		$a2 = $a2 . '<option value="' . $rowsem->value . '" ' . $current . '>' . $rowsem->value . '</option>';
	}
	$a3 = '</select>
	       <input type="submit" name="start" value="' . __('show','teachpress') . '" id="teachpress_submit" class="teachpress_button"/>
        </div>';
	
	$row = "Select name, comment, rel_page, image_url FROM " . $teachpress_courses . " WHERE semester = '$sem' AND parent = '0' ORDER BY name";
	$test = $wpdb->query($row);
	if ($test != 0){
		$row = $wpdb->get_results($row);
		foreach($row as $row) {
			$row->name = stripslashes($row->name);
			$row->comment = stripslashes($row->comment);
			// handle images	
			$colspan = '';
			if ($image == 'left' || $image == 'right') {
				$pad_size = $image_size + 5;
				$rowspan = ' colspan="2"';
			}
			$image_marginally = '';
			$image_bottom = '';
			if ($image == 'left' || $image == 'right') {
				if ($row->image_url != '') {
					$image_marginally = '<img name="' . $row->name . '" src="' . $row->image_url . '" width="' . $image_size .'" alt="' . $row->name . '" />';
				}
			}
			if ($image == 'left') {
				$td_left = '<td width="' . $pad_size . '">' . $image_marginally . '</td>';
			}
			if ($image == 'right') {
				$td_right = '<td width="' . $pad_size . '">' . $image_marginally . '</td>';
			}
			if ($image == 'bottom') {
				if ($row->image_url != '') {
					$image_bottom = '<div class="tp_pub_image_bottom"><img name="' . $row->name . '" src="' . $row->image_url . '" style="max-width:' . $image_size .'px;" alt="' . $row->name . '" /></div>';
				}
			}
			// End handle images
			$a4 = $a4 . '<tr>
						  ' . $td_left . '
						  <td class="tp_lvs_container">
				   			<div class="tp_lvs_name"><a href="' . get_permalink($row->rel_page) . '" title ="' . $row->name . '"><strong>' . $row->name . '</strong></a></div>
            	   			<div class="tp_lvs_comments">' . $row->comment . '</div>
							' . $image_bottom . '
				   		  </td>
						  ' . $td_right . '  
						</tr>';
		} 
	}
	else {
		$a4 = '<tr><td class="teachpress_message">' . __('Sorry, no entries matched your criteria.','teachpress') . '</td></tr>';
	}
	$a4 = '<table class="teachpress_course_list">' . $a4 . '</table>';
	$a5 = '</form></div>';
	$asg = $a1 . $a2 . $a3 . $a4. $a5;
	return $asg;
}

/* Date-Shortcode
 * @param $attr(Array) mit parameter 'id' (integer)
 * Return $asg (String)
 * used in WordPress-Shortcode API
*/
function tpdate_shortcode($attr) {
	$a1 = '<div class="untertitel">' . __('Date(s)','teachpress') . '</div>
			<table class="tpdate">';
	global $wpdb;	
	global $teachpress_courses; 
	$row = "SELECT name, type, room, lecturer, date, comment FROM " . $teachpress_courses . " WHERE course_id= ". $attr["id"] . "";
	$row = $wpdb->get_results($row);
	foreach($row as $row) {
		$v_test = $row->name;
		$a2 = $a2 . ' 
		  <tr>
			<td class="tp_date_type"><strong>' . stripslashes($row->type) . '</strong></td>
			<td class="tp_date_info">
				<p>' . stripslashes($row->date) . ' ' . stripslashes($row->room) . '</p>
				<p>' . stripslashes($row->comment) . '</p>
			</td>
			<td clas="tp_date_lecturer">' . stripslashes($row->lecturer) . '</td>
		  </tr>';
	} 
	// Search the child courses
	$row = "SELECT name, type, room, lecturer, date, comment FROM " . $teachpress_courses . " WHERE parent= ". $attr["id"] . " ORDER BY name";
    $row = $wpdb->get_results($row);
	foreach($row as $row) {
		// if parent name = child name
		if ($v_test == $row->name) {
			$row->name = $row->type;
		}
        $a3 = $a3 . '
		  <tr>
			<td class="tp_date_type"><strong>' . stripslashes($row->name) . '</strong></td>
			<td class="tp_date_info">
				<p>' . stripslashes($row->date) . ' ' . stripslashes($row->room) . '</p>
				<p>' . stripslashes($row->comment) . '</p>
			</td>
			<td class="tp_date_lecturer">' . stripslashes($row->lecturer) . '</td>
		  </tr>';
	} 
	$a4 = '</table>';
	$asg = '' . $a1 . '' . $a2 . '' . $a3 . '' . $a4 . '';
	return $asg;
}

/* Shorcode for a single publication
 * @param $atts (Array) with id (INT)
 * Return $asg (String)
 * used in WordPress shorcode API
*/ 
function tpsingle_shortcode ($atts) {
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_relation;
	global $teachpress_settings;
	global $wpdb;
	// Shortcode options
	extract(shortcode_atts(array(
		'id' => 0
	), $atts));
	// Select from database
	$id = tp_sec_var($id, 'integer');
	$row = "SELECT * FROM " . $teachpress_pub . " WHERE pub_id = '$id'";
  	$daten = $wpdb->get_row($row, OBJECT);
	// Return
	$asg = '<div class="tp_single_publication"><span class="tp_single_author">' . $daten->author . '</span>, "<span class="tp_single_title">' . $daten->name . '</span>", <span class="tp_single_additional">' . tp_publication_advanced_information($daten) . '</span></div>';
	return $asg;
}

/* Publication list with tag cloud
 * @param $atts (Array) with userid (INT), maxsize (INT), minsize (INT), limit (INT), image (STRING), size (INT), anchor (INT)
 * $_GET: $yr (Year, INT), $type (Type, STRING), $autor (Author, INT)
 * Return $asg (String)
 * used in WordPress shortcode API
*/
function tpcloud_shortcode($atts) {
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_relation;
	global $teachpress_settings;
	global $teachpress_user;
	global $pagenow;
	global $wpdb;
	// Shortcode options
	extract(shortcode_atts(array(
		'id' => 0,
		'maxsize' => 35,
		'minsize' => 11,
		'limit' => 30,
		'image' => 'none',
		'image_size' => 0,
		'anchor' => 1,
	), $atts));
	// tgid - shows the current tag
	$tgid = tp_sec_var($_GET[tgid], 'integer');
	if ($tgid == '') {
		$tgid = 0;
	}
	// year
	$yr = tp_sec_var($_GET[yr], 'integer');
	if ($yr == '') {
		$yr = 0;
	}
	// publication type
	$type = tp_sec_var($_GET[type]);
	if ($type == '') {
		$type = 0;
	}
	// author
	$autor = tp_sec_var($_GET[autor], 'integer');
	if ($autor == '') {
		$autor = 0;
	}
	// Falls Autor via Shortcode gesetzt
	if ($id != 0) {
		$autor = $id;
	}
	settype($id, 'integer');
	settype($image_size, 'integer');
	settype($anchor, 'integer');
	// ID Namen bei abgeschalteten Permalinks ermitteln
	if (is_page()) {
		$page = "page_id";
	}
	else {
		$page = "p";
	}
	// With anchor or not
	if ($anchor == '1') {
		$html_anchor = '#tppubs';
	}
	else {
		$html_anchor = '';
	}
	$permalink = tp_get_option('permalink');
	
	/*
	 * Tag cloud
	*/
	
	// Ermittle Anzahl der Tags absteigend sortiert
	if ($id == '0') {
		$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_relation . " GROUP BY " . $teachpress_relation . ".`tag_id` ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
	}
	else {
		$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_relation . " b  LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id  WHERE u.user = '$id' GROUP BY b.tag_id ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
	}
	
	// Ermittle einzelnes Vorkommen der Tags, sowie Min und Max
	$sql = "SELECT MAX(anzahlTags) AS max, min(anzahlTags) AS min, COUNT(anzahlTags) as gesamt FROM (".$sql.") AS temp";
	$tagcloud_temp = $wpdb->get_row($sql, ARRAY_A);
	$max = $tagcloud_temp['max'];
	$min = $tagcloud_temp['min'];

	$insgesamt = $tagcloud_temp['gesamt'];
	
	// Tags und Anzahl zusammenstellen
	// Unterscheidung welche ID angegeben wurde, danach holen der Tags aus Datenbank
	// 0 enspricht alle Publikationen
	if ($id == '0') {
		$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name,  t.tag_id as tag_id FROM " . $teachpress_relation . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
	}
	else {
		$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name, t.tag_id as tag_id FROM " . $teachpress_relation . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id  WHERE u.user = '$id' GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
	}
	$temp = $wpdb->get_results($sql, ARRAY_A);
	// Endausgabe der Cloud zusammenstellen
	foreach ($temp as $tagcloud) {
		// Schriftgröße berechnen
		// Minimum ausgleichen
		if ($min == 1) {
			$min = 0;
		}
		// Formel: max. Schriftgroesse*(aktuelle anzahl - kleinste Anzahl)/ (groeßte Anzahl - kleinste Anzahl)
		$size = floor(($maxsize*($tagcloud['tagPeak']-$min)/($max-$min)));
		// Ausgleich der Schriftgröße
		if ($size < $minsize) {
			$size = $minsize ;
		}
		if ($tagcloud['tagPeak'] == 1) {
			$pub = __('publication', 'teachpress');
		}
		else {
			$pub = __('publications', 'teachpress');
		}
		// Falls Permalinks genutzt werden
		if ($permalink == 1) {
			$link = $pagenow;
			$link = str_replace("index.php", "", $link);
			// String zusammensetzen
			// fuer aktuellen Tag
			if ( $tgid == $tagcloud['tag_id'] ) {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?tgid=0&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" class = "teachpress_cloud_active" title="' . __('Delete tag as filter','teachpress') . '">' . $tagcloud['name'] . ' </a></span> ';
			}
			// Normaler Tag
			else {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?tgid=' . $tagcloud['tag_id'] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" title="' . $tagcloud['tagPeak'] . ' ' . $pub . '">' . $tagcloud['name'] . ' </a></span> ';
			}
		}
		// wenn keine Permalinks genutzt werden
		else {
			$postid = get_the_ID();
			$link = $pagenow;
			$link = str_replace("index.php", "", $link);
			// String zusammensetzen
			// fuer aktuellen Tag
			if ( $tgid == $tagcloud['tag_id'] ) {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=0&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" class = "teachpress_cloud_active" title="' . __('Delete tag as filter','teachpress') . '">' . $tagcloud['name'] . ' </a></span> ';
			}
			else {
				$asg = $asg . '<span style="font-size:' . $size . 'px;"><a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=' . $tagcloud['tag_id'] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" title="' . $tagcloud['tagPeak'] . ' ' . $pub . '"> ' . $tagcloud['name'] . '</a></span> ';
			}
		}
	}
	
	/* 
	 * Filter
	*/ 
	
	// for javascripts
	$str ="'";
	// Link structure
	if ($permalink == 1) {
		$tpurl = '' . $link . '?';
	}
	else {
		$tpurl = '' . $link . '?' . $page . '=' . $postid . '&amp;';
	}
	
	// Filter year
	if ($id == 0) {
		$row = $wpdb->get_results("SELECT DISTINCT DATE_FORMAT(p.date, '%Y') AS jahr FROM " . $teachpress_pub . " p ORDER BY jahr DESC");
	}
	else {
		$row = $wpdb->get_results("SELECT DISTINCT DATE_FORMAT(p.date, '%Y') AS jahr FROM " . $teachpress_pub . "  p
									INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
									WHERE u.user = '$id'
									ORDER BY jahr DESC");
	}
	$options = '';
	foreach ($row as $row) {
		if ($row->jahr == $yr) {
			$current = 'selected="selected"';
		}
		else {
			$current = '';
		}
		$options = $options . '<option value = "' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $row->jahr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" ' . $current . '>' . $row->jahr . '</option>';
	}
	$filter1 ='<select name="yr" id="yr" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">
               <option value="' . $tpurl . 'tgid=' . $tgid . '&amp;yr=0&amp;type=' . $type . '&amp;autor=' . $autor . '' . $html_anchor . '">' . __('All years','teachpress') . '</option>
			   ' . $options . '
               </select>';
			   
	// Filter type
	if ($id == 0) {
		$row = $wpdb->get_results("SELECT DISTINCT p.type FROM " . $teachpress_pub . " p ORDER BY p.type ASC");
	}
	else {
		$row = $wpdb->get_results("SELECT DISTINCT p.type from " . $teachpress_pub . "  p
									INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
									WHERE u.user = '$id'
									ORDER BY p.type ASC");
	}
	$current = '';	
	$options = '';
	foreach ($row as $row) {
		if ($row->type == $type && $type != '0') {
			$current = 'selected="selected"';
		}
		else {
			$current = '';
		}
		$options = $options . '<option value = "' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $row->type . '&amp;autor=' . $autor . $html_anchor . '" ' . $current . '>' . __('' . $row->type . '','teachpress') . '</option>';
	}
	$filter2 ='<span style="padding-left:10px; padding-right:10px;"><select name="type" id="type" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">
               <option value="' . $tpurl . 'tgid=' . $tgid . '&amp;yr=0&amp;type=' . $type . '&amp;autor=' . $autor . '' . $html_anchor . '">' . __('All types','teachpress') . '</option>
			   ' . $options . '
               </select></span>';
			   
	// Filter author
	$current = '';	
	$options = '';  
	// for all publications	   
	if ($id == '0') {	
		$row = $wpdb->get_results("SELECT DISTINCT user FROM " . $teachpress_user . "");	 
		foreach ($row as $row) {
			if ($row->user == $autor) {
				$current = 'selected="selected"';
			}
			else {
				$current = '';
			}
			$user_info = get_userdata($row->user);
			$options = $options . '<option value = "' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $row->user . $html_anchor . '" ' . $current . '>' . $user_info->display_name . '</option>';
		}  
		$filter3 ='<select name="pub-author" id="pub-author" onchange="teachpress_jumpMenu(' . $str . 'parent' . $str . ',this,0)">
					   <option value="' . $tpurl . 'tgid=' . $tgid . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=0' . $html_anchor . '">' . __('All authors','teachpress') . '</option>
				   ' . $options . '
				   </select>';	
	}
	// for publications of one author, where is no third filter	   	
	else {
		$filter3 = "";
	}
	
	// Endformat
	if ($yr == '' && $type == '' && ($autor == '' || $autor == $id ) && $tgid == '') {
		$showall = "";
	}
	else {
		$link = $pagenow;
		$link = str_replace("index.php", "", $link);
		if ($permalink == 1) {
			$showall ='<a href="' . $link . '?tgid=0' . $html_anchor . '" title="' . __('Show all','teachpress') . '">' . __('Show all','teachpress') . '</a>';
		}
		else {
			$showall ='<a href="' . $link . '?' . $page . '=' . $postid . '&amp;tgid=0' . $html_anchor . '" title="' . __('Show all','teachpress') . '">' . __('Show all','teachpress') . '</a>';
		}
	}
	// fertige Tag-Cloud fuer return als String zusammensetzen
	$asg1 = '<a name="tppubs" id="tppubs"></a><div class="teachpress_cloud">' . $asg . '</div><div class="teachpress_filter">' . $filter1 . '' . $filter2 . '' . $filter3 . '</div><p align="center">' . $showall . '</p>';
	
	/* 
	 * Liste der Publikatioen
	*/
	// Filter beruecksichtigen
	// nach Jahr
	if ($yr == '' || $yr == 0) {
		$select_year = '';
	}
	else {
		$select_year = "(p.date BETWEEN '" . $yr . "-01-01' AND '" . $yr . "-12-31')";
	}
	// nach Typ
	if ($type == '0') {
		$select_type = '';
	}
	else {
		$select_type = 'p.type = ' . $str . '' . $type . '' . $str . '';
	}
	if ($select_year != '') {
		if ($select_type != '') {
			$zusatz1 = "WHERE " . $select_year . " AND " . $select_type . " ";
			$zusatz2 = "AND " . $select_year . " AND " . $select_type . "";
		}
		else {
			$zusatz1 = "WHERE " . $select_year . " ";
			$zusatz2 = "AND " . $select_year . "";
		}
	}
	else {
		if ($select_type != '') {
			$zusatz1 = "WHERE " . $select_type . " ";
			$zusatz2 = "AND " . $select_type . "";
		}
		else {
			$zusatz1 = "";
			$zusatz2 = "";
		}
	}
	// id umstellen
	if ($autor != 0) {
		$id = $autor;
	}
	 $select = "SELECT DISTINCT p.pub_id, p.name, p.type, p.bibtex, p.author, p.editor, p.date, DATE_FORMAT(p.date, '%Y') AS jahr, p.isbn , p.url, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.note, p.is_isbn, p.image_url 
			FROM " . $teachpress_relation . " b ";
	 $join1 = "INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			   INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id ";		
	// Wenn kein Tag ausgewaehlt wurde
	if ($tgid == "" || $tgid == 0) {
		// fuer alle Publikationen
		if ($id == 0) {
			$row =  "" . $select . "" . $join1 . "" . $zusatz1 . "ORDER BY p.date DESC";
		}
		// fuer Publikationen eines Autors
		else {
		$row = "" . $select . "" . $join1 . "
			INNER JOIN " . $teachpress_user . " u ON u.pub_id= b.pub_id
			WHERE u.user = '$id' " . $zusatz2 . "
			ORDER BY p.date DESC";
		}	
	}
	// Falls Tag ausgewaehlt wurde
	else {
		if ($id == 0) {
		// fuer alle Publikationen
		$row = "" . $select . "" . $join1 . "
			WHERE t.tag_id = '$tgid' " . $zusatz2 . "
			ORDER BY p.date DESC";
		}
		// fuer Publikationen eines Autors
		else {
		$row = "" . $select . "" . $join1 . "
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' AND t.tag_id = '$tgid' " . $zusatz2 . "
			ORDER BY p.date DESC";
		}	
	}
	$row = $wpdb->get_results($row);
	$sql = "SELECT name, tag_id, pub_id FROM (SELECT t.name AS name, t.tag_id AS tag_id, b.pub_id AS pub_id FROM " . $teachpress_tags . " t LEFT JOIN " . $teachpress_relation . " b ON t.tag_id = b.tag_id ) as temp";
	$temp = $wpdb->get_results($sql);
	$atag = 0;
	foreach ($temp as $temp) {
		$all_tags[$atag][0] = $temp->name;
		$all_tags[$atag][1] = $temp->tag_id;
		$all_tags[$atag][2] = $temp->pub_id;
		$atag++;
	}
	$tpz = 0;
	$jahr = 0;
	$colspan = '';
	if ($image == 'left' || $image == 'right') {
		$pad_size = $image_size + 5;
		$rowspan = ' colspan="2"';
	}
	foreach ($row as $row) {
		$tparray[$tpz][0] = '' . $row->jahr . '' ;
		$tparray[$tpz][1] = tp_get_publication_html($row,$pad_size,$image, $all_tags, $atag, 1 ,$html_anchor);
		$tpz++;
	}
	if ($tpz != 0) {
		if ($yr == 0) {
			$jahre = "SELECT DISTINCT DATE_FORMAT(p.date, '%Y') AS jahr FROM " . $teachpress_pub . " p ORDER BY jahr DESC";
			$row = $wpdb->get_results($jahre);
			foreach($row as $row) {
				for ($i=0; $i<= $tpz; $i++) {
					if ($tparray[$i][0] == $row->jahr) {
						$zwischen = $zwischen . $tparray[$i][1];
					}
					else {
						if ($zwischen != '') {
							$pubs = $pubs . '<tr><td' . $colspan . '><h3 class="tp_h3">' . $row->jahr . '</h3></td></tr>' . $zwischen;
							$zwischen = '';
						}
					}
				}
			}
		}
		else {
			for ($i=0; $i<$tpz; $i++) {
					if ($tparray[$i][0] == $yr) {
						$pubs = $pubs . $tparray[$i][1];
					}
			}
			if ($pubs != '') {
				$pubs = '<tr><td' . $colspan . '><h3 class="tp_h3">' . $row->jahr . '</h3></td></tr>' . $pubs;
			}
		}
		// Alles zusammensetzen
		$asg2 = '<table class="teachpress_publication_list">' . $pubs . '</table>';
		$asg = $asg1 . $asg2;
	}
	else {
		$asg2 = '<div class="teachpress_list"><p class="teachpress_mistake">' . __('Sorry, no publications matched your criteria.','teachpress') . '</p></div>';
		$asg = $asg1 . $asg2;
	}
	// Rueckgabe des gesamten Strings
	return "$asg";
}

/* Publication list without tag cloud
 * @param $attr (Array) with user (INT), tag (INT), year (string), headline (INT), image (string), image_size (INT)
 * Return: $asg (String)
 * used in WordPress Shortcode-API
*/
function tplist_shortcode($atts){
	global $wpdb;
	global $teachpress_pub;
	global $teachpress_tags;
	global $teachpress_relation;
	global $teachpress_user;
	// Attribute aus array extrahieren
	extract(shortcode_atts(array(
		'user' => 0,
		'tag' => 0,
		'year' => 0,
		'headline' => 1,
		'image' => 'none',
		'image_size' => 0,
	), $atts));
	$userid = $user;
	$tag_id = $tag;
	$yr = $year;
	// Define some parameters as integer
	settype($userid, 'integer');
	settype($tag_id, 'integer');
	settype($yr, 'integer');
	settype($headline, 'integer');
	settype($image_size, 'integer');
	$select = "SELECT DISTINCT p.pub_id, p.name, p.type, p.bibtex, p.author, p.editor, p.date, DATE_FORMAT(p.date, '%Y') AS jahr, p.isbn , p.url, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.note, p.is_isbn, p.image_url 
			FROM " . $teachpress_relation ." b "; 
	// Publikationen aller Autoren
	if ($userid == 0) {
		// Publikationen aller Autoren zu einem bestimmten Tag
		if ($tag_id != 0) {
			$row = "" . $select . "
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			WHERE t.tag_id = '$tag_id'
			ORDER BY p.date DESC";
		}	
		// Alle Publikationen aller Autoren
		else {
			$row = "SELECT p.pub_id, p.name, p.type, p.bibtex, p.author, p.editor, p.date, DATE_FORMAT(p.date, '%Y') AS jahr, p.isbn , p.url, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.note, p.is_isbn, p.image_url FROM " . $teachpress_pub. " p ORDER BY p.date DESC, p.type";
		}
	}
	else {
		if ($tag_id != 0) {
			$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' AND t.tag_id = '$tag_id'
			ORDER BY p.date DESC";
		}
		else {
			$row = "" . $select . " 
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = b.pub_id
			WHERE u.user = '$userid'
			ORDER BY p.date DESC";
		}	
	}	
	$tpz = 0;
	$colspan = '';
	if ($image == 'left' || $image == 'right') {
		$pad_size = $image_size + 5;
		$rowspan = ' colspan="2"';
	}
	$row = $wpdb->get_results($row);
	foreach ($row as $row) {
		$tparray[$tpz][0] = '' . $row->jahr . '' ;
		$tparray[$tpz][1] = tp_get_publication_html($row,$pad_size,$image, $all_tags, $atag,0);
		$tpz++;			
	}
	// Strings nach Publikationstyp zusammensetzen
	if ($headline == 1) {
		if ($yr == 0) {
			$jahre = "SELECT DISTINCT DATE_FORMAT(p.date, '%Y') AS jahr FROM " . $teachpress_pub . " p ORDER BY jahr DESC";
			$row = $wpdb->get_results($jahre);
			foreach($row as $row) {
				for ($i=0; $i<= $tpz; $i++) {
					if ($tparray[$i][0] == $row->jahr) {
						$zwischen = $zwischen . $tparray[$i][1];
					}
					else {
						if ($zwischen != '') {
							$pubs = $pubs . '<tr><td' . $colspan . '><h3 class="tp_h3">' . $row->jahr . '</h3></td></tr>' . $zwischen;
							$zwischen = '';
						}
					}
				}
			}
		}
		else {
			for ($i=0; $i<$tpz; $i++) {
					if ($tparray[$i][0] == $yr) {
						$pubs = $pubs . $tparray[$i][1];
					}
			}
			if ($pubs != '') {
				$pubs = '<tr><td' . $colspan . '><h3 class="tp_h3">' . $yr . '</h3></td></tr>' . $pubs;
			}
		}	
	}	
	else {
		for($i=0; $i<$tpz; $i++) {
			$pubs = $pubs . $tparray[$i][1];
		}
	}
	// Alles zusammensetzen	
	$asg = '<table class="teachpress_publication_list">' . $pubs . '</table>';
	
	return $asg;
}

/* Private Post shortcode
 * @param $atts
 * @param $content
 * return $content
*/
function tppost_shortcode ($atts, $content) {
	global $wpdb;
	global $teachpress_signup;
	global $user_ID;
    get_currentuserinfo();
	extract(shortcode_atts(array(
		'id' => 0,
	), $atts));
	settype($id, 'integer');
	$sql = "SELECT con_id FROM " . $teachpress_signup . " WHERE course_id = '$id' AND wp_id = '$user_ID'";
	$test = $wpdb->query($sql);
	if ($test == 1) {
		return $content;
	}
}
?>
