<?php 
/* 
 * Einschreibungsformular
*/
?>
<div class="enrollments">
<?php 

global $user_ID;
global $user_email;
global $user_login;
get_currentuserinfo();

global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_einstellungen; 
global $teachpress_kursbelegung;

$wp_id = $user_ID;
$aendern = $_POST[aendern];
$austragen = $_POST[austragen];
$checkbox = $_POST[checkbox];
$checkbox2 = $_POST[checkbox2];
$einschreiben = $_POST[einschreiben];
$matrikel2 = htmlentities(utf8_decode($_POST[matrikel2]));
$vorname2 = htmlentities(utf8_decode($_POST[vorname2]));
$nachname2 = htmlentities(utf8_decode($_POST[nachname2]));
$studiengang2 = htmlentities(utf8_decode($_POST[studiengang2]));
$fachsemester2 = htmlentities(utf8_decode($_POST[fachsemester2]));
$gebdat2 = htmlspecialchars($_POST[gebdat2]);
$email2 = htmlentities(utf8_decode($_POST[email2]));
			
if ( isset($aendern) || isset($austragen) || isset($einschreiben) ) { ?>
	<div class="teachpress_message">
        <form method="POST" action="<?php echo $PHP_SELF ?>" id="teachpress_einstellungen_weiter">
        <?php
        if ( isset($aendern)) {
            change_student($wp_id, $vorname2, $nachname2, $studiengang2, $gebdat2, $email2, $fachsemester2, $matrikel2);
        }
        if ( isset($austragen)) {
            delete_einschreibung_student($checkbox2);
        }
        if ( isset($einschreiben)) {
            add_einschreibung($checkbox, $wp_id);
        } ?>
        <input type="submit" name="Submit" value="<?php _e('resume','teachpress'); ?>" id="teachpress_einstellungen_weiter">
        </form>
	</div>
    <?php
}
// request current term
$sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
$sem = tp_var($sem);
?>   
<h3 class="tp_enrollments"><?php _e('Enrollments for the','teachpress'); ?> <?php echo"$sem" ;?></h3>
<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
<?php

// if user is logged in
if (is_user_logged_in()) {
	$auswahl = "Select wp_id FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
	$auswahl = tp_var($auswahl);
	// if user is not registered
	if($auswahl == '' ) {
		/*
		 * Registration form
		*/
		$eintragen = $_POST[eintragen];
		$einschreiben = $_POST[einschreiben];
		$wp_id = $user_ID;
		$vorname = htmlentities(utf8_decode($_POST[vorname]));
		$nachname = htmlentities(utf8_decode($_POST[nachname]));
		$studiengang = htmlentities(utf8_decode($_POST[studiengang]));
		$fachsemester = htmlentities(utf8_decode($_POST[fachsemester]));
		$urzkurz = $user_login;
		$gebdat = htmlspecialchars($_POST[gebdat]);
		$email = $user_email;
		$matrikel = htmlentities(utf8_decode($_POST[matrikel]));
		
		if ( isset($eintragen) ) {
			add_student($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel);
				?>
				<div style="padding-left:40px;">
			  <form method="POST" action="<?php echo $PHP_SELF ?>" id="teachpress_einstellungen_weiter">
				  <p style="background-color:#FFFFCC; border:1px solid silver; padding:5px; width:80%;">
				  <strong><?php _e('Registration successful','teachpress'); ?></strong>
				  <input type="submit" name="Submit" value="<?php _e('resume','teachpress'); ?>" id="teachpress_einstellungen_weiter">
				  </p>
			  </form>
			</div>
			<?php
		}
		?>
		<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
		<div id="eintragen">
		<p style="text-align:left; color:#FF0000;"><?php _e('Please fill in the following registration form and sign up in the system. You can edit your data later.','teachpress'); ?></p>
		<fieldset style="border:1px solid silver; padding:5px;">
			<legend><?php _e('Your data','teachpress'); ?></legend>
			<table border="0" cellpadding="0" cellspacing="5" style="text-align:left; padding:5px;">
			  <tr>
				<td><?php _e('Registr.-Number','teachpress'); ?></td>
				<td><input type="text" name="matrikel" id="matrikel" /></td>
			  </tr>
			  <tr>
				<td><?php _e('First name','teachpress'); ?></td>
				<td><input name="vorname" type="text" id="vorname" /></td>
			  </tr>
			  <tr>
				<td><?php _e('Last name','teachpress'); ?></td>
				<td><input name="nachname" type="text" id="nachname" /></td>
			  </tr>
			  <tr>
				<td><?php _e('Course of studies','teachpress'); ?></td>
				<td><label>
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
				</label></td>
			  </tr>
			  <tr>
				<td><?php _e('Number of terms','teachpress'); ?></td>
				<td style="text-align:left;"><label>
				<select name="fachsemester" id="fachsemester">
				  <option value="1">1</option>
				  <option value="2">2</option>
				  <option value="3">3</option>
				  <option value="4">4</option>
				  <option value="5">5</option>
				  <option value="6">6</option>
				  <option value="7">7</option>
				  <option value="8">8</option>
				  <option value="9">9</option>
				  <option value="10">10</option>
				  <option value="11">11</option>
				  <option value="12">12</option>
				  <option value="13">13</option>
				  <option value="14">14</option>
				  <option value="15">15</option>
				  <option value="16">16</option>
				  <option value="17">17</option>
				  <option value="18">18</option>
				  </select>
				</label></td>
			  </tr>
			  <tr>
				<td><?php _e('User account','teachpress'); ?></td>
				<td style="text-align:left;"><?php echo"$user_login" ?></td>
			  </tr>
			  <tr>
				<td><?php _e('Date of birth','teachpress'); ?></td>
				<td><input name="gebdat" type="text" size="15" value="JJJJ-MM-TT"/>
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
		$auswahl = "Select * FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
		$auswahl = tp_results($auswahl);
		foreach ($auswahl as $row) {
		   ?>
            <div id="nutzer" style=" text-align:left; padding:5px;">
            <p><strong><?php _e('Hello','teachpress'); ?>, <?php echo"$row->vorname" ?> <?php echo"$row->nachname" ?></strong></p>
            <table cellpadding="5">
              <tr>
                <td><a onclick="teachpress_showhide('einschreibungen_anzeigen')" name="daten" id="einschreibungen_link" style="cursor:pointer;"> <?php _e('Your enrollments','teachpress'); ?></a></td>
                <td style="padding-left:5px;"><a onclick="teachpress_showhide('daten_aendern')" name="daten2" id="daten_link" style="cursor:pointer;"><?php _e('Your user data','teachpress'); ?></a></td>
              </tr>
            </table>
			<div id="daten_aendern" style="padding-left:20px; padding-top:5px; padding-bottom:5px; padding-right:20px; margin:5px; display:none;">
            <fieldset style="padding:5px; border:1px solid silver;">
            <legend><?php _e('Your data','teachpress'); ?></legend>
                <table border="0" cellpadding="0" cellspacing="5">
                  <tr>
                    <td><?php _e('Registr.-Number','teachpress'); ?></td>
                    <td><input type="text" name="matrikel2" id="matrikel2" value="<?php echo"$row->matrikel" ?>"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('First name','teachpress'); ?></td>
                    <td><input name="vorname2" type="text" id="vorname2" value="<?php echo"$row->vorname" ?>" size="30"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('Last name','teachpress'); ?></td>
                    <td><input name="nachname2" type="text" id="nachname2" value="<?php echo"$row->nachname" ?>" size="30"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('Course of studies','teachpress'); ?></td>
                    <td><select name="studiengang2" id="studiengang2">
                      <option value="<?php echo"$row->studiengang" ?>"><?php echo"$row->studiengang" ?></option>
                      <option>--------------------</option>
                      <?php
					  $stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
					  $stud = tp_results($stud);
					  foreach($stud as $stud) { ?>
						  <option value="<?php echo $stud->wert; ?>"><?php echo $stud->wert; ?></option>
					  <?php } 
					  ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td><?php _e('Number of terms','teachpress'); ?></td>
                    <td><select name="fachsemester2" id="fachsemester2">
                            <option value="<?php echo"$row->fachsemester" ?>"><?php echo"$row->fachsemester" ?></option>
                            <option>--</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                      </select>                    </td>
                  </tr>
                  <tr>
                    <td><?php _e('Date of birth','teachpress'); ?></td>
                    <td><input name="gebdat2" type="text" value="<?php echo"$row->gebdat" ?>" size="30"/>
                      <em>Format: JJJJ-MM-TT</em></td>
                  </tr>
                  <tr>
                    <td><?php _e('E-Mail','teachpress'); ?></td>
                    <td><input name="email2" type="text" id="email2" value="<?php echo"$row->email" ?>" size="50" readonly="true"/></td>
                  </tr>
                </table>
            <input name="aendern" type="submit" id="aendern" onclick="teachpress_validateForm('matrikel2','','RisNum','vorname2','','R','nachname2','','R','email2','','RisEmail');return document.teachpress_returnValue" value="senden" />
            </fieldset>
			</div>
            <div id="einschreibungen_anzeigen" style="display:none; padding-left:20px; padding-top:5px; padding-bottom:5px; padding-right:20px; margin:5px;">
			<fieldset style="padding:5px; border:1px solid silver;">
            <legend><?php _e('Previous enrollments','teachpress'); ?></legend>
            <p><strong><?php _e('Signed up for','teachpress'); ?></strong></p>    
            <table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">
            <tr>
            	<th>&nbsp;</th>
                <th><?php _e('Name','teachpress'); ?></th>
                <th><?php _e('Type','teachpress'); ?></th>
                <th><?php _e('Date','teachpress'); ?></th>
                <th><?php _e('Room','teachpress'); ?></th>
                <th><?php _e('Term','teachpress'); ?></th>
            </tr>
            <?php
			// Select all courses where user is registered
			$row1 = "SELECT wp_id, v_id, b_id, warteliste, name, vtyp, raum, termin, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.veranstaltungs_id as v_id, k.belegungs_id as b_id, k.warteliste as warteliste, v.name as name, v.vtyp as vtyp, v.raum as raum, v.termin as termin, v.semester as semester, p.name as parent_name FROM " . $teachpress_kursbelegung . " k INNER JOIN " . $teachpress_ver . " v ON k.veranstaltungs_id = v.veranstaltungs_id LEFT JOIN " . $teachpress_ver . " p ON v.parent = p.veranstaltungs_id ) AS temp 
			WHERE wp_id = '$row->wp_id' AND warteliste = '0' 
			ORDER BY b_id DESC";
			$row1 = tp_results($row1);
			foreach($row1 as $row1) {
				if ($row1->parent_name != "") {
					$row1->parent_name = '' . $row1->parent_name . ' -';
				} 
				echo '<tr>
						<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>
						<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
						<td>' . $row1->vtyp . '</td>
						<td>' . $row1->termin . '</td>
						<td>' . $row1->raum . '</td> 
						<td>' . $row1->semester . '</td>
					</tr>';
			} ?>
        </table>
        <?php
		// all courses where user is registered in a waiting list
		$row1 = "SELECT wp_id, v_id, b_id, warteliste, name, vtyp, raum, termin, semester, parent_name FROM (SELECT k.wp_id as wp_id, k.veranstaltungs_id as v_id, k.belegungs_id as b_id, k.warteliste as warteliste, v.name as name, v.vtyp as vtyp, v.raum as raum, v.termin as termin, v.semester as semester, p.name as parent_name FROM " . $teachpress_kursbelegung . " k INNER JOIN " . $teachpress_ver . " v ON k.veranstaltungs_id = v.veranstaltungs_id LEFT JOIN " . $teachpress_ver . " p ON v.parent = p.veranstaltungs_id ) AS temp 
		WHERE wp_id = '$row->wp_id' AND warteliste = '1' 
		ORDER BY b_id DESC";
		$test = tp_query($row1);
		if ($test != 0) {
		?>
        <p><strong><?php _e('Waiting list','teachpress'); ?></strong></p>
        <table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">
            <tr>
            	<th>&nbsp;</th>
                <th><?php _e('Name','teachpress'); ?></th>
                <th><?php _e('Type','teachpress'); ?></th>
                <th><?php _e('Date','teachpress'); ?></th>
                <th><?php _e('Room','teachpress'); ?></th>
                <th><?php _e('Term','teachpress'); ?></th>
            </tr>
            <?php
				$row1 = tp_results($row1);
				foreach($row1 as $row1) {
				if ($row1->parent_name != "") {
					$row1->parent_name = '' . $row1->parent_name . ' -';
				} 
				echo '<tr>
						<td><input name="checkbox2[]" type="checkbox" value="' . $row1->b_id . '" title="' . $row1->name . '" id="ver_' . $row1->b_id . '"/></td>
						<td><label for="ver_' . $row1->b_id . '" style="line-height:normal;" title="' . $row1->parent_name . ' ' .  $row1->name . '">' . $row1->parent_name . ' ' .  $row1->name . '</label></td>
						<td>' . $row1->vtyp . '</td>
						<td>' . $row1->termin . '</td>
						<td>' . $row1->raum . '</td> 
						<td>' . $row1->semester . '</td>
					</tr>';
			} ?>
        </table>
        <?php } ?>
        <p><input name="austragen" type="submit" value="<?php _e('unsubscribe','teachpress'); ?>" id="austragen" /></p>
        </fieldset>
        </div>
        </div>
			<?php
		}
	}
}	
else { ?>
	<div class="teachpress_message"><?php _e('You must be logged in, before you can use the registration.','teachpress'); ?></div>
<?php }	
// Select all courses where enrollments in the current term are available
$row = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' AND sichtbar = '1' ORDER BY vtyp DESC, name";
$row = tp_results($row);
foreach($row as $row) {
	$datum1 = $row->startein;
	$datum2 = $row->endein;
	// for german localisation: new date format
	if ( __('Language','teachpress') == 'Sprache') {
		$row->startein = date_mysql2german($row->startein);
		$row->endein = date_mysql2german($row->endein);
	}
   ?>  
   <div style="margin:10px; padding:5px;">
   <div class="the_course" style="font-size:15px;"><a href="<?php echo"$row->url" ?>"><?php echo"$row->name" ?></a></div>
     <table width="100%" border="0" cellpadding="1" cellspacing="0">
       <tr>
         <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">
         <?php if (is_user_logged_in() && $auswahl != '') {
		 			if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?>
         	<input type="checkbox" name="checkbox[]" value="<?php echo"$row->veranstaltungs_id" ?>" title="<?php echo"$row->name" ?> <?php _e('select','teachpress'); ?>" id="checkbox_<?php echo"$row->veranstaltungs_id" ?>"/> 
					<?php } 
				}	?>
         </td>
         <td colspan="2">&nbsp;</td>
         <td align="center" width="270"><strong><?php _e('Date(s)','teachpress'); ?></strong></td>
         <td align="center"><?php if ($datum1 != '0000-00-00') { ?><strong><?php _e('free places','teachpress'); ?></strong><?php } ?></td>
       </tr>
       <tr>
         <td width="20%" style="font-weight:bold;"><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?><label for="checkbox_<?php echo"$row->veranstaltungs_id" ?>" style="line-height:normal;"><?php } ?><?php echo"$row->vtyp" ?><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?></label><?php } ?></td>
         <td width="20%"><?php echo"$row->dozent" ?></td>
         <td align="center"><?php echo"$row->termin" ?> <?php echo"$row->raum" ?></td>
         <td align="center"><?php if ($datum1 != '0000-00-00') { ?><?php echo"$row->fplaetze" ?> von <?php echo"$row->plaetze" ?><?php } ?></td>
       </tr>
       <tr>
         <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row->warteliste == 1 && $row->fplaetze == 0) {?><?php _e('Possible to subscribe in the waiting list','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
         <td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist"><?php if ($datum1 != '0000-00-00') { ?><?php _e('Registration period','teachpress'); ?>: <?php echo"$row->startein" ?> - <?php echo"$row->endein" ?><?php }?></td>
       </tr>
     <?php
	 // Select all childs
	 $row2 = "Select * FROM " . $teachpress_ver . " WHERE parent = '$row->veranstaltungs_id' AND sichtbar = '1' ORDER BY veranstaltungs_id";
	 $row2 = tp_results($row2);
	 foreach ($row2 as $row2) {
		$datum3 = $row2->startein;
		$datum4 = $row2->endein;
		// for german localisation: new date format
		if ( __('Language','teachpress') == 'Sprache') {
	 		$row2->startein = date_mysql2german($row2->startein);
			$row2->endein = date_mysql2german($row2->endein);
		}
	 	?>
               <tr>
                 <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;">
				 	<?php if (is_user_logged_in() && $auswahl != '') {
						 if ($datum3 != '0000-00-00' && date("Y-m-d") >= $datum3 && date("Y-m-d") <= $datum4) { ?>
                         	<input type="checkbox" name="checkbox[]" value="<?php echo"$row2->veranstaltungs_id" ?>" title="<?php echo"$row2->name" ?> ausw&auml;hlen" id="checkbox_<?php echo"$row2->veranstaltungs_id" ?>"/>
						<?php }
					}?>	</td>
                 <td colspan="2">&nbsp;</td>
                 <td align="center" width="270"><strong><?php _e('Date(s)','teachpress'); ?></strong></td>
                 <td align="center"><strong><?php _e('free places','teachpress'); ?></strong></td>
               </tr>
               <tr>
                 <td width="20%" style="font-weight:bold;"><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?><label for="checkbox_<?php echo"$row2->veranstaltungs_id" ?>" style="line-height:normal;"><?php } ?><?php echo"$row2->name" ?><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?></label><?php } ?></td>
                 <td width="20%"><?php echo"$row2->dozent" ?></td>
                 <td align="center"><?php echo"$row2->termin" ?> <?php echo"$row2->raum" ?></td>
                 <td align="center"><?php echo"$row2->fplaetze" ?> von <?php echo"$row2->plaetze" ?></td>
               </tr>
               <tr>
                 <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row->warteliste == 1 && $row->fplaetze == 0) {?><?php _e('Possible to subscribe in the waiting list','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
                 <td align="center" class="einschreibefrist" style="border-bottom:1px solid silver; border-collapse: collapse;"><?php if ($datum3 != '0000-00-00') { ?><?php _e('Registration period','teachpress'); ?>: <?php echo"$row2->startein" ?> - <?php echo"$row2->endein" ?><?php } ?></td>
               </tr> 
        <?php
		} 
		// End (search for childs)
		?>
         </table>
    </div> 
  <?php  }
if (is_user_logged_in() && $auswahl != '') { ?> 
	<input name="einschreiben" type="submit" value="<?php _e('Sign up','teachpress'); ?>" />
<?php } ?>
</form>
<?php $version = get_tp_version(); ?>
<p style="font-size:11px; color:#AAAAAA"><em><strong>teachPress <?php echo $version; ?></strong></em> - <?php _e('course and publication management for WordPress','teachpress'); ?></p>
</div>