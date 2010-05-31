<?php 
/* 
 * Enrollment system frontend
*/
function teachpress_enrollment_frontend() {
// Advanced Login
$tp_login = tp_get_option('login');
if ( $tp_login == 'int' ) {
	tp_advanced_registration();
}
// WordPress
global $user_ID;
global $user_email;
global $user_login;
get_currentuserinfo();

// teachPress
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_einstellungen; 
global $teachpress_kursbelegung;
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
$vorname = tp_sec_var($_POST[vorname]);
$nachname = tp_sec_var($_POST[nachname]);
$studiengang = tp_sec_var($_POST[studiengang]);
$fachsemester = tp_sec_var($_POST[fachsemester], 'integer');
$urzkurz = $user_login;
$gebdat = tp_sec_var($_POST[gebdat]);
$email = $user_email;
$matrikel = tp_sec_var($_POST[matrikel], 'integer');

// Edit form
$matrikel2 = tp_sec_var($_POST[matrikel2], 'integer');
$vorname2 = tp_sec_var($_POST[vorname2]);
$nachname2 = tp_sec_var($_POST[nachname2]);
$studiengang2 = tp_sec_var($_POST[studiengang2]);
$fachsemester2 = tp_sec_var($_POST[fachsemester2], 'integer');
$gebdat2 = tp_sec_var($_POST[gebdat2]);
$email2 = tp_sec_var($_POST[email2]);
?>
<div class="enrollments">
<h2 class="tp_enrollments"><?php _e('Enrollments for the','teachpress'); ?> <?php echo"$sem" ;?></h2>
<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
<?php
/*
 * Messages
*/ 
if ( isset($aendern) || isset($austragen) || isset($einschreiben) || isset($eintragen) ) { 
    if ( isset($aendern)) {
        tp_change_student($wp_id, $vorname2, $nachname2, $studiengang2, $gebdat2, $email2, $fachsemester2, $matrikel2);
    }
    if ( isset($austragen)) {
        tp_delete_registration_student($checkbox2);
    }
    if ( isset($einschreiben)) {
        tp_add_registration($checkbox, $wp_id);
    }	
    if ( isset($eintragen)) {
        $ret = tp_add_student($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel);
        if ($ret == true) {
            echo '<div class="teachpress_message"><strong>' . __('Registration successful','teachpress') . '</strong></div>';
        }
        else {
            echo '<div class="teachpress_message"><strong>' . __('Error: User already exist','teachpress') . '</strong></div>';
        }
    } 
}

/*
 * User status
*/ 
if (is_user_logged_in()) {
	$auswahl = "Select wp_id FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
	$auswahl = tp_var($auswahl);
	// if user is not registered
	if($auswahl == '' ) {
		/*
		 * Registration
		*/
		?>
		<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
		<div id="eintragen">
		<p style="text-align:left; color:#FF0000;"><?php _e('Please fill in the following registration form and sign up in the system. You can edit your data later.','teachpress'); ?></p>
		<fieldset style="border:1px solid silver; padding:5px;">
			<legend><?php _e('Your data','teachpress'); ?></legend>
			<table border="0" cellpadding="0" cellspacing="5" style="text-align:left; padding:5px;">
			  <tr>
				<td><label for="text"><?php _e('Registr.-Number','teachpress'); ?></label></td>
				<td><input type="text" name="matrikel" id="matrikel" /></td>
			  </tr>
			  <tr>
				<td><label for="vorname"><?php _e('First name','teachpress'); ?></label></td>
				<td><input name="vorname" type="text" id="vorname" /></td>
			  </tr>
			  <tr>
				<td><label for="nachname"><?php _e('Last name','teachpress'); ?></label></td>
				<td><input name="nachname" type="text" id="nachname" /></td>
			  </tr>
			  <tr>
				<td><label for="studiengang"><?php _e('Course of studies','teachpress'); ?></label></td>
				<td>
				<select name="studiengang" id="studiengang">
					<?php
					  global $teachpress_einstellungen;
					  $rowstud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
					  $rowstud = tp_results($rowstud);
					  foreach ($rowstud as $rowstud) { ?>
						  <option value="<?php echo $rowstud->wert; ?>"><?php echo $rowstud->wert; ?></option>
					  <?php } 
					  ?>
				</select>
				</td>
			  </tr>
			  <tr>
				<td><label for="fachsemester"><?php _e('Number of terms','teachpress'); ?></label></td>
				<td style="text-align:left;">
				<select name="fachsemester" id="fachsemester">
                <?php
				for ($i=1; $i<20; $i++) {
					echo '<option value="' . $i . '">' . $i . '</option>';
				} ?>
				  </select>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('User account','teachpress'); ?></td>
				<td style="text-align:left;"><?php echo"$user_login" ?></td>
			  </tr>
			  <tr>
				<td><label for="gebdat"><?php _e('Date of birth','teachpress'); ?></label></td>
				<td><input name="gebdat" type="text" size="15"/>
				  <em><?php _e('Format: JJJJ-MM-TT','teachpress'); ?></em></td>
			  </tr>
			  <tr>
				<td><?php _e('E-Mail','teachpress'); ?></td>
				<td><?php echo"$user_email" ?></td>
			  </tr>
			</table>
		</fieldset>
        <input name="eintragen" type="submit" id="eintragen" onclick="teachpress_validateForm('matrikel','','RisNum','vorname','','R','nachname','','R');return document.teachpress_returnValue" value="<?php _e('Send','teachpress'); ?>" />
		</div>
		</form>
		<?php
	}
	else {
		// Select all user information
		$auswahl = "Select * FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
		$auswahl = tp_results($auswahl);
		foreach ($auswahl as $row) {
			/*
			 * Menu
			*/
			echo '<div class="tp_user_menu" style="padding:5px;">';
			echo '<h4>' . __('Hello','teachpress') . ', ' . $row->vorname . ' ' . $row->nachname . '</h4>';
			// ID Namen bei abgeschalteten Permalinks ermitteln
			if (is_page()) {
				$page = "page_id";
			}
			else {
				$page = "p";
			}
			// Define links
			if ($permalink == '1') {
				$link = $pagenow;
				$link = str_replace("index.php", "", $link);
				$link = $link . '?tab=';
			}
			// wenn keine Permalinks genutzt werden
			else {
				$postid = get_the_ID();
				$link = $pagenow;
				$link = str_replace("index.php", "", $link);
				$link = $link . '?' . $page . '=' . $postid . '&amp;tab=';
			}
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
			echo '<p>' . $tab1 . ' | ' . $tab2 . ' | ' . $tab3 . '</p>';
			echo '</div>';
			
			/*
			 * Old Enrollments / Sign out
			*/
			if ($tab == 'old') {
				echo '<p><strong>' . __('Signed up for','teachpress') . '</strong></p>';    
				echo '<table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">';
				echo '<tr>';
            if ($is_sign_out == '0') {
            	echo '<th>&nbsp;</th>';
            }
            echo '<th>' . __('Name','teachpress') . '</th>';
            echo '<th>' . __('Type','teachpress') . '</th>';
            echo '<th>' . __('Date','teachpress') . '</th>';
            echo '<th>' . __('Room','teachpress') . '</th>';
            echo '<th>' . __('Term','teachpress') . '</th>';
            echo '</tr>';
			// Select all courses where user is registered
			$row1 = "SELECT wp_id, v_id, b_id, warteliste, name, vtyp, raum, termin, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.veranstaltungs_id as v_id, k.belegungs_id as b_id, k.warteliste as warteliste, v.name as name, v.vtyp as vtyp, v.raum as raum, v.termin as termin, v.semester as semester, p.name as parent_name FROM " . $teachpress_kursbelegung . " k INNER JOIN " . $teachpress_ver . " v ON k.veranstaltungs_id = v.veranstaltungs_id LEFT JOIN " . $teachpress_ver . " p ON v.parent = p.veranstaltungs_id ) AS temp 
			WHERE wp_id = '$row->wp_id' AND warteliste = '0' 
			ORDER BY b_id DESC";
			$row1 = tp_results($row1);
			foreach($row1 as $row1) {
				if ($row1->parent_name != "") {
					$row1->parent_name = '' . $row1->parent_name . ' -';
				}
				if ($is_sign_out == '0') {
				echo '<tr>
						<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>';
				}		
				echo '<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
						<td>' . $row1->vtyp . '</td>
						<td>' . $row1->termin . '</td>
						<td>' . $row1->raum . '</td> 
						<td>' . $row1->semester . '</td>
					</tr>';
			}
			echo '</table>';
			// all courses where user is registered in a waiting list
			$row1 = "SELECT wp_id, v_id, b_id, warteliste, name, vtyp, raum, termin, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.veranstaltungs_id as v_id, k.belegungs_id as b_id, k.warteliste as warteliste, v.name as name, v.vtyp as vtyp, v.raum as raum, v.termin as termin, v.semester as semester, p.name as parent_name FROM " . $teachpress_kursbelegung . " k INNER JOIN " . $teachpress_ver . " v ON k.veranstaltungs_id = v.veranstaltungs_id LEFT JOIN " . $teachpress_ver . " p ON v.parent = p.veranstaltungs_id ) AS temp 
			WHERE wp_id = '$row->wp_id' AND warteliste = '1' 
			ORDER BY b_id DESC";
			$test = tp_query($row1);
			if ($test != 0) {
				echo '<p><strong>' . __('Waiting list','teachpress') . '</strong></p>';
				echo '<table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">';
				echo '<tr>';
				if ($is_sign_out == '0') {
					echo '<th>&nbsp;</th>';
				}
				echo '<th>' . __('Name','teachpress') . '</th>';
				echo '<th>' . __('Type','teachpress') . '</th>';
				echo '<th>' . __('Date','teachpress') . '</th>';
				echo '<th>' . __('Room','teachpress') . '</th>';
				echo '<th>' . __('Term','teachpress') . '</th>';
				echo '</tr>';
				$row1 = tp_results($row1);
				foreach($row1 as $row1) {
					if ($row1->parent_name != "") {
						$row1->parent_name = '' . $row1->parent_name . ' -';
					} 
					if ($is_sign_out == '0') {
					echo '<tr>
							<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>';
					}		
					echo '<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
							<td>' . $row1->vtyp . '</td>
							<td>' . $row1->termin . '</td>
							<td>' . $row1->raum . '</td> 
							<td>' . $row1->semester . '</td>
						</tr>';
					}
				echo '</table>';
				}
				if ($is_sign_out == '0') {
					echo '<p><input name="austragen" type="submit" value="' . __('unsubscribe','teachpress') . '" id="austragen" /></p>';
				}
			}	
			/*
			 * Edit userdata
			*/
			if ($tab == 'data') {
				echo '<table border="0" cellpadding="0" cellspacing="5">';
                $field1 = tp_get_option('regnum');
                if ($field1 == '1') {
                	echo '<tr>
                    		<td><label for="matrikel2">' . __('Registr.-Number','teachpress') . '</label></td>
                    		<td><input type="text" name="matrikel2" id="matrikel2" value="' . $row->matrikel . '"/></td>
                  		</tr>';
                }  
                echo '<tr>
                    	<td><label for="vorname2">' . __('First name','teachpress') . '</label></td>
                    	<td><input name="vorname2" type="text" id="vorname2" value="' . $row->vorname . '" size="30"/></td>
                  	  </tr>';
                echo '<tr>
                    	<td><label for="nachname2">' . __('Last name','teachpress') . '</label></td>
                    	<td><input name="nachname2" type="text" id="nachname2" value="' . $row->nachname . '" size="30"/></td>
                  	  </tr>';
                $field2 = tp_get_option('studies');
                if ($field2 == '1') { 
                 	echo '<tr>
                    		<td><label for="studiengang2">' . __('Course of studies','teachpress') . '</label></td>
                    		<td><select name="studiengang2" id="studiengang2">';
					$stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
					$stud = tp_results($stud);
					foreach($stud as $stud) { 
						if ($stud->wert == $row->studiengang) {
							$current = 'selected="selected"' ;
						}
						else {
							$current = '' ;
						}
						echo '<option value="' . $stud->wert . '" ' . $current . '>' . $stud->wert . '</option>';
					} 
					echo '</select></td>
                  		</tr>';
                }
                $field3 = tp_get_option('termnumber');
                if ($field3 == '1') {
                	echo '<tr>
                    		<td><label for="fachsemester2">' . __('Number of terms','teachpress') . '</label></td>
                    		<td><select name="fachsemester2" id="fachsemester2">';
					for ($i=1; $i<20; $i++) {
						if ($i == $row->fachsemester) {
							$current = 'selected="selected"' ;
						}
						  else {
								$current = '' ;
						  }
						  echo '<option value="' . $i . '" ' . $current . '>' . $i . '</option>';
					}  
					echo '</select></td>
                	</tr>';
                }
                $field4 = tp_get_option('birthday');
                if ($field4 == '1') {
					echo '<tr>
							<td><label for="gebdat2">' . __('Date of birth','teachpress') . '</label></td>
							<td><input name="gebdat2" type="text" value="' . $row->gebdat . '" size="30"/>
						  <em>' . __('Format: JJJJ-MM-TT','teachpress') . '</em></td>
						 </tr>';
                }
                echo '<tr>
                    	<td><label for="email2">' . __('E-Mail','teachpress') . '</label></td>
                    	<td><input name="email2" type="text" id="email2" value="' . $row->email . '" size="50" readonly="true"/></td>
                  	 </tr>';
                echo '</table>';
				if ($field1 != '1') {
                    echo '<input type="hidden" name="matrikel2" value="' . $row->matrikel . '" />';
				}
				if ($field2 != '1') {
					echo '<input type="hidden" name="studiengang2" value="' . $row->studiengang . '" />';
				}
				if ($field3 != '1') {
					echo '<input type="hidden" name=fachsemester2"" value="' . $row->fachsemester . '" />';
				}
				if ($field4 != '1') {
					echo '<input type="hidden" name="gebdat2" value="' . $row->gebdat . '" />';
				}
                ?>
            	<input name="aendern" type="submit" id="aendern" onclick="teachpress_validateForm('matrikel2','','RisNum','vorname2','','R','nachname2','','R','email2','','RisEmail');return document.teachpress_returnValue" value="senden" /><?php
            }
		}
	}
}
/*
 * Enrollments
*/
if ($tab == '' || $tab == 'current') {
	// Select all courses where enrollments in the current term are available
	$row = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' AND sichtbar = '1' ORDER BY vtyp DESC, name";
	$row = tp_results($row);
	foreach($row as $row) {
		$datum1 = $row->startein;
		$datum2 = $row->endein;
		// for german localisation: new date format
		if ( __('Language','teachpress') == 'Sprache') {
			$row->startein = tp_date_mysql2german($row->startein);
			$row->endein = tp_date_mysql2german($row->endein);
		}
		echo '<div style="margin:10px; padding:5px;">';
		echo '<div class="the_course" style="font-size:15px;"><a href="' . get_permalink($row->rel_page) . '">' . $row->name . '</a></div>';
		echo '<table width="100%" border="0" cellpadding="1" cellspacing="0">';
       	echo '<tr>';
        echo '<td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">';
        if (is_user_logged_in() && $auswahl != '') {
		 	if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) {
         		echo '<input type="checkbox" name="checkbox[]" value="' . $row->veranstaltungs_id . '" title="' . $row->name . ' ' . __('select','teachpress') . '" id="checkbox_' . $row->veranstaltungs_id . '"/>';
			} 
		}
		else {
			echo '&nbsp;';
		}	
        echo '</td>';
        echo '<td colspan="2">&nbsp;</td>';
        echo '<td align="center" width="270"><strong>' . __('Date(s)','teachpress') . '</strong></td>';
        echo '<td align="center">';
		if ($datum1 != '0000-00-00') {
			echo '<strong>' . __('free places','teachpress') . '</strong>';
		}
		echo '</td>';
		echo '</tr>';
       	echo '<tr>';
        echo '<td width="20%" style="font-weight:bold;">';
		if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) {
			echo '<label for="checkbox_' . $row->veranstaltungs_id . '" style="line-height:normal;">';
		}
		echo $row->vtyp;
		if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) {
			echo '</label>';
		}
		echo '</td>';
        echo '<td width="20%">' . $row->dozent . '</td>';
        echo '<td align="center">' . $row->termin . ' ' . $row->raum . '</td>';
        echo '<td align="center">';
		if ($datum1 != '0000-00-00') { 
			echo $row->fplaetze . ' von ' .  $row->plaetze;
		}
		echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste">';
		if ($row->warteliste == 1 && $row->fplaetze == 0) {
			_e('Possible to subscribe in the waiting list','teachpress'); 
		}
		else {
			echo '&nbsp;';
		}
		echo '</td>';
		echo '<td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist">';
		if ($datum1 != '0000-00-00') {
			echo __('Registration period','teachpress') . ': ' . $row->startein . ' - ' . $row->endein;
		}
		echo '</td>';
        echo '</tr>';
		// Select all childs
		$row2 = "Select * FROM " . $teachpress_ver . " WHERE parent = '$row->veranstaltungs_id' AND sichtbar = '1' ORDER BY veranstaltungs_id";
		$row2 = tp_results($row2);
		foreach ($row2 as $row2) {
			$datum3 = $row2->startein;
			$datum4 = $row2->endein;
			// for german localisation: new date format
			if ( __('Language','teachpress') == 'Sprache') {
				$row2->startein = tp_date_mysql2german($row2->startein);
				$row2->endein = tp_date_mysql2german($row2->endein);
			}
			if ($row->name == $row2->name) {
				$row2->name = $row->vtyp;
			}
			echo '<tr>';
            echo '<td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">';
			if (is_user_logged_in() && $auswahl != '') {
				if ($datum3 != '0000-00-00' && date("Y-m-d") >= $datum3 && date("Y-m-d") <= $datum4) {
					echo '<input type="checkbox" name="checkbox[]" value="' . $row2->veranstaltungs_id . '" title="' . $row2->name . ' ausw&auml;hlen" id="checkbox_' . $row2->veranstaltungs_id . '"/>';
				}
			}
			echo '</td>';
            echo '<td colspan="2">&nbsp;</td>';
            echo '<td align="center" width="270"><strong>' . __('Date(s)','teachpress') . '</strong></td>';
            echo '<td align="center"><strong>' . __('free places','teachpress') . '</strong></td>';
            echo '</tr>';
            echo '<tr>';
			echo '<td width="20%" style="font-weight:bold;">';
			if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) {
				echo '<label for="checkbox_' . $row2->veranstaltungs_id . '" style="line-height:normal;">';
			}
			echo $row2->name;
			if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) {
				echo '</label>';
			}
			echo '</td>';
            echo '<td width="20%">' . $row2->dozent . '</td>';
            echo '<td align="center">' . $row2->termin . ' ' . $row2->raum . '</td>';
            echo '<td align="center">' . $row2->fplaetze . ' von ' . $row2->plaetze . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste">';
			echo $row2->bemerkungen . ' ';
			if ($row2->warteliste == 1 && $row2->fplaetze == 0) {
				_e('Possible to subscribe in the waiting list','teachpress');
			} 
			else {
				echo '&nbsp;';
			}
			echo '</td>';
            echo '<td align="center" class="einschreibefrist" style="border-bottom:1px solid silver; border-collapse: collapse;">';
			if ($datum3 != '0000-00-00') {
				_e('Registration period','teachpress') . ': ' . $row2->startein . ' - ' . $row2->endein;
			}
			echo '</td>';
            echo '</tr>'; 
		} 
		// End (search for childs)
		echo '</table>';
		echo '</div>';
	}	
	if (is_user_logged_in() && $auswahl != '') {
		echo '<input name="einschreiben" type="submit" value="' . __('Sign up','teachpress') . '" />';
	}
}
?>
</form>
</div>
<?php } ?>