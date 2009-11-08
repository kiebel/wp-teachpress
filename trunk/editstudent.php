<?php
/* Editiieren der Datensätze von Studenten
 * from studenten.php
 * @param $student_ID (Int)
 * @param $suche (String)
 * @param $studenten (String)
*/
?>
<?php  
if ( is_user_logged_in() ) { 
?> 
<div class="wrap">
<?php
// Eingangsparameter
$student = $_GET[student_ID];
$studenten = $_GET[studenten];
$suche = $_GET[suche];
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_kursbelegung;
global $teachpress_einstellungen;
 // Formular-Einträge
$checkbox = $_GET[checkbox];
$delete = $_GET[loeschen];
$speichern = $_GET[speichern];
	
$wp_id = htmlentities(utf8_decode($_GET[wp_id]));
$matrikel = htmlentities(utf8_decode($_GET[matrikel]));
$vorname = htmlentities(utf8_decode($_GET[vorname]));
$nachname = htmlentities(utf8_decode($_GET[nachname]));
$studiengang = htmlentities(utf8_decode($_GET[studiengang]));
$fachsemester = htmlentities(utf8_decode($_GET[fachsemester]));
$urzkurz = htmlentities(utf8_decode($_GET[urzkurz]));
$gebdat = htmlentities($_GET[gebdat]);
$email = htmlentities(utf8_decode($_GET[email]));
// WP User ID
global $user_ID;
get_currentuserinfo();
// Prüfen welche Checkbox genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
if ( isset($delete)) {
	delete_einschreibung($checkbox, $user_ID);
	$message = __('Einschreibung gel&ouml;scht','teachpress');
	$site = 'admin.php?page=teachpress/editstudent.php&student_ID=' . $student . '&suche=' . $suche . '&studenten=' . $studenten . '';
	tp_get_message($message, $site);
}
if ( isset($speichern)) {
	change_student_manuell($wp_id, $vorname, $nachname, $studiengang, $urzkurz, $gebdat, $email, $fachsemester, $matrikel, $user_ID);
	$message = __('&Auml;nderungen erfolgreich.','teachpress');
	$site = 'admin.php?page=teachpress/editstudent.php&student_ID=' . $wp_id . '&suche=' . $suche . '&studenten=' . $studenten . '';
	tp_get_message($message, $site);
}
if (isset($bearbeiten) || isset($delete) || isset($speichern)) {
}	
else {
?>
<p><a href="admin.php?page=teachpress/studenten.php&suche=<?php echo"$suche" ?>&studenten=<?php echo "$studenten" ?>" class="teachpress_back" title="<?php _e('zur&uuml;ck zur &Uuml;bersicht','teachpress'); ?>">&larr; <?php _e('zur&uuml;ck','teachpress'); ?> </a>
</p>
<?php
}
?>
<form name="personendetails" method="get" action="<?php echo $PHP_SELF ?>">
<input name="page" type="hidden" value="teachpress/editstudent.php">
<input name="student_ID" type="hidden" value="<?php echo"$student" ?>">
<input name="studenten" type="hidden" value="<?php echo"$studenten" ?>">
<input name="suche" type="hidden" value="<?php echo"$suche" ?>">
<?php
	$row3 = "SELECT * FROM " . $teachpress_stud . " WHERE wp_id = '$student'";
	$row3 = tp_results($row3);
	foreach($row3 as $row3){ ?>
    	<h2 style="padding-top:0px;"><?php echo "$row3->vorname" ?> <?php echo "$row3->nachname" ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('daten_aendern')" id="daten_aendern_2" style="cursor:pointer;"><?php _e('bearbeiten','teachpress'); ?> </a></small></h2>
          <div id="daten_aendern" style="display:none; padding-top:5px; padding-bottom:5px; margin:5px;">
            <fieldset style="border:1px solid silver; padding:10px; width:650px;">
              <legend><?php _e('Daten bearbeiten','teachpress'); ?></legend>
                <table class="widefat">
                  <tr>
                    <td><strong><?php _e('WordPress User-ID','teachpress'); ?></strong></td>
                    <td style="text-align:left;"><input name="wp_id" type="text" id="wp_id" value="<?php echo "$row3->wp_id" ?>" readonly="true"/></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Matrikel','teachpress'); ?></strong></td>
                    <td style="text-align:left;"><input name="matrikel" type="text" id="matrikel" value="<?php echo "$row3->matrikel" ?>" readonly="true"/></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Vorname','teachpress'); ?></strong></td>
                    <td><input name="vorname" type="text" id="vorname" value="<?php echo "$row3->vorname" ?>" size="40"/></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Nachname','teachpress'); ?></strong></td>
                    <td><input name="nachname" type="text" id="nachname" value="<?php echo "$row3->nachname" ?>" size="40"/></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Studiengang','teachpress'); ?></strong></td>
                    <td>
                    <select name="studiengang" id="studiengang">
                      <option value="<?php echo "$row3->studiengang" ?>"><?php echo "$row3->studiengang" ?></option>
                      <option>------------------</option>
                      <?php
                      $stud = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
                      $stud = tp_results($stud);
                      foreach ($stud as $stud) { ?>
                          <option value="<?php echo $stud->wert; ?>"><?php echo $stud->wert; ?></option>
                      <?php } ?>
                    </select></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Fachsemester','teachpress'); ?></strong></td>
                    <td style="text-align:left;"><label>
                    <select name="fachsemester" id="fachsemester">
                      <option value="<?php echo "$row3->fachsemester" ?>"><?php echo "$row3->fachsemester" ?></option>
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
                    </select>
                    </label></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('URZ-K&uuml;rzel','teachpress'); ?>l</strong></td>
                    <td style="text-align:left;"><input name="urzkurz" type="text" id="urzkurz" value="<?php echo "$row3->urzkurz" ?>" readonly="true"/></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('Geburtsdatum','teachpress'); ?></strong></td>
                    <td><input name="gebdat" type="text" id="gebdat" value="<?php echo "$row3->gebdat" ?>" size="15"/>
                      <em>              Format: JJJJ-MM-TT</em></td>
                  </tr>
                  <tr>
                    <td><strong><?php _e('E-Mail','teachpress'); ?></strong></td>
                    <td><input name="email" type="text" id="email" value="<?php echo "$row3->email" ?>" size="50" readonly="true"/></td>
                  </tr>
                </table>
            <table border="0" cellspacing="7" cellpadding="0">
                  <tr>
                    <td><input name="speichern" type="submit" id="teachpress_einzel_change" onclick="teachpress_validateForm('wp_id','','RisNum','matrikel','','RisNum','vorname','','R','nachname','','R','urzkurz','','R','gebdat','','R','email','','RisEmail');return document.teachpress_returnValue" value="<?php _e('speichern','teachpress'); ?>" class="teachpress_button"/></td>
                    <td><a onclick="teachpress_showhide('daten_aendern')" style="cursor:pointer;"><?php _e('abbrechen','teachpress'); ?></a></td>
                  </tr>
                </table>
            </fieldset>
          </div>
          <div style="width:55%; padding-bottom:10px;">
          <table border="0" cellpadding="0" cellspacing="5" class="widefat">
            <thead>
            <tr>
              <th width="130"><?php _e('WordPress User-ID','teachpress'); ?></th>
              <td><?php echo "$row3->wp_id" ?></td>
            </tr>
            <tr>
              <th><?php _e('Matrikel','teachpress'); ?></th>
              <td><?php echo "$row3->matrikel" ?></td>
              </tr>
            <tr>
              <th><?php _e('Studiengang','teachpress'); ?></th>
              <td><?php echo "$row3->studiengang" ?></td>
              </tr>
            <tr>
              <th><?php _e('Fachsemester','teachpress'); ?></th>
              <td><?php echo "$row3->fachsemester" ?></td>
            </tr>
            <tr>
              <th><?php _e('Geburtsdatum','teachpress'); ?></th>
              <td><?php echo "$row3->gebdat" ?></td>
              </tr>
            <tr>
              <th><?php _e('URZ-K&uuml;rzel','teachpress'); ?></th>
              <td><?php echo "$row3->urzkurz" ?></td>
              </tr>
            <tr>
              <th><?php _e('E-Mail','teachpress'); ?></th>
              <td><a href="mailto:<?php echo "$row3->email" ?>" title="<?php _e('E-Mail an','teachpress'); ?> <?php echo "$row3->vorname" ?> <?php echo "$row3->nachname" ?> senden<?php _e('','teachpress'); ?>"><?php echo "$row3->email" ?></a></td>
              </tr>
           </thead>   
          </table>
          </div>
<?php } ?> 
</form>
<form method="get" action="<?php echo $PHP_SELF ?>">
<input name="page" type="hidden" value="teachpress/editstudent.php">
<input name="student_ID" type="hidden" value="<?php echo"$student" ?>">
<input name="suche" type="hidden" value="<?php echo"$suche" ?>">
<input name="typ" type="hidden" value="<?php echo"$typ" ?>">
<table border="1" cellspacing="0" cellpadding="5" class="widefat">
	<thead>
        <tr>
          <th>&nbsp;</th>
          <th><?php _e('Einschr.-Nr.','teachpress'); ?></th>
          <th><?php _e('Datum','teachpress'); ?></th>
          <th><?php _e('Lehrveranstaltung','teachpress'); ?></th>
          <th><?php _e('Typ','teachpress'); ?></th>
          <th><?php _e('Termin','teachpress'); ?></th>
          <th><?php _e('Nachname','teachpress'); ?></th>
          <th><?php _e('Vorname','teachpress'); ?></th>
          <th><?php _e('User-ID','teachpress'); ?></th>
        </tr>
    </thead>    
    <tbody>
<?php
		// Nach Daten zur Person: Ausgabe aller Einschreibungen
		$row2= "SELECT " . $teachpress_stud . ".wp_id, " . $teachpress_stud . ".vorname, " . $teachpress_stud . ".nachname, " . $teachpress_ver . ".name, " . $teachpress_ver . ".vtyp, " . $teachpress_ver . ".termin, " . $teachpress_ver . ".parent," . $teachpress_kursbelegung . ".belegungs_id, " . $teachpress_kursbelegung . ".datum
					FROM " . $teachpress_kursbelegung . "
					INNER JOIN " . $teachpress_ver . " ON " . $teachpress_ver . ".veranstaltungs_id=" . $teachpress_kursbelegung . ".veranstaltungs_id
					INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id= " . $teachpress_kursbelegung . ".wp_id
					WHERE " . $teachpress_kursbelegung . ".wp_id = '$student'";
		$row2 = tp_results($row2);
		foreach($row2 as $row2) {
			if ($row2->parent != 0) {
				$parent_name = tp_var("SELECT name FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row2->parent'");
				$parent_name = $parent_name . " ";
			}
			else {
				$parent_name = "";
			}
			   // Ausgabe der Infos zur gewählten LVS mit integriertem Aenderungsformular
			   ?>
		    <tr>
				<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo "$row2->belegungs_id" ?>"/></th>
				<td><?php echo "$row2->belegungs_id" ?></td>
				<td><?php echo "$row2->datum" ?></td>
				<td><?php echo $parent_name . $row2->name; ?></td>
				<td><?php echo "$row2->vtyp" ?></td> 
                <td><?php echo "$row2->termin" ?></td>
				<td><?php echo "$row2->nachname" ?></td>
				<td><?php echo "$row2->vorname" ?></td>
				<td><?php echo "$row2->wp_id" ?></td>
			</tr>
        <?php } ?>
    </tbody>
	</table>
            <table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
              <tr>
                <td><?php _e('Einschreibung l&ouml;schen','teachpress'); ?></td>
                <td> <input name="loeschen" type="submit" value="<?php _e('l&ouml;schen','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/></td>
              </tr>
            </table>
</form>
</div>
<?php } ?>