<?php 
/* Bearbeitung von Lehrveranstaltungen
 * from showlvs.php:
 * @param $lvs_ID (INT) - ID der Veranstaltung die geladen werden soll
 * @param $sem (String) - angezeigtes Semester auf showlvs.php
 * @param $search (String) - verwendeter Suchstring auf showlvs.php
*/
?>

<?php 
if ( is_user_logged_in() ) { 
?>
<div class="wrap">
<form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
<?php
// Variablen holen
// Datenbankvariablen
global $dtb_name;
global $dtb_server;
global $dtb_nutzername;
global $dtb_passwort;
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_einstellungen; 
global $teachpress_kursbelegung;
// WordPressvariablen
global $user_ID;
get_currentuserinfo();
// Formularvariablen
$checkbox = $_GET[checkbox];
$delete = $_GET[loeschen];
$speichern = $_GET[speichern];
$aufnehmen = $_GET[aufnehmen];
$name = htmlentities(utf8_decode($_GET[name]));
$vtyp = htmlentities(utf8_decode($_GET[vtyp]));
$raum = htmlentities(utf8_decode($_GET[raum])) ;
$dozent = htmlentities(utf8_decode($_GET[dozent]));
$termin = htmlentities(utf8_decode($_GET[termin]));
$plaetze = htmlentities(utf8_decode($_GET[plaetze])); 
$fplaetze = htmlentities(utf8_decode($_GET[fplaetze]));
$startein = htmlentities(utf8_decode($_GET[startein])); 
$endein = htmlentities(utf8_decode($_GET[endein])); 
$semester = htmlentities(utf8_decode($_GET[semester]));
$bemerkungen = htmlentities(utf8_decode($_GET[bemerkungen]));
$url = htmlentities(utf8_decode($_GET[url]));
$sichtbar = htmlentities(utf8_decode($_GET[sichtbar]));
$parent = htmlentities(utf8_decode($_GET[par]));
$warteliste = htmlentities(utf8_decode($_GET[warteliste]));
$weiter = htmlentities(utf8_decode($_GET[lvs_ID]));
$veranstaltung = htmlentities(utf8_decode($_GET[lvs_ID]));
// fuer Zurueckleitung an showlvs.php
$search = htmlentities(utf8_decode($_GET[search]));
$sem = htmlentities(utf8_decode($_GET[sem]));
// Befehle ausfürhen
if ( isset($speichern) ) {
	change_lehrveranstaltung($name, $vtyp, $raum, $dozent, $termin, $plaetze, $fplaetze, $startein, $endein, $semester, $bemerkungen, $url, $sichtbar, $veranstaltung, $parent, $warteliste);
	$message = __('Changes successful','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);
}
if ( isset($aufnehmen) ) {
    aufnahme($checkbox);
	$message = __('Participant added','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);	
    }	 
if ( isset($delete)) {
    delete_einschreibung($checkbox, $user_ID);
	$message = __('Enrollments deleted','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);	
}
// Abfrage-Arrays füllen
// LVS-Daten
$row = "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$veranstaltung'";
$row = tp_results($row);
$counter = 0;
foreach($row as $row){
	$daten[$counter][0] = $row->veranstaltungs_id;
	$daten[$counter][1] = $row->name;
	$daten[$counter][2] = $row->vtyp;
	$daten[$counter][3] = $row->raum;
	$daten[$counter][4] = $row->dozent;
	$daten[$counter][5] = $row->termin;
	$daten[$counter][6] = $row->plaetze;
	$daten[$counter][7] = $row->fplaetze;
	$daten[$counter][8] = $row->startein;
	$daten[$counter][9] = $row->endein;
	$daten[$counter][10] = $row->semester;
	$daten[$counter][11] = $row->bemerkungen;
	$daten[$counter][12] = $row->url;
	$daten[$counter][13] = $row->parent;
	$daten[$counter][14] = $row->sichtbar;
	$daten[$counter][15] = $row->warteliste;
	$counter++;
}
// Einschreibungen
$row = "SELECT " . $teachpress_stud . ".matrikel, " . $teachpress_stud . ".vorname, " . $teachpress_stud . ".nachname, " . $teachpress_stud . ".studiengang,  " . $teachpress_stud . ".urzkurz, " . $teachpress_stud . ".email , " . $teachpress_kursbelegung . ".datum, " . $teachpress_kursbelegung . ".belegungs_id, " . $teachpress_kursbelegung . ".warteliste
			FROM " . $teachpress_kursbelegung . " 
			INNER JOIN " . $teachpress_ver . " ON " . $teachpress_ver . ".veranstaltungs_id=" . $teachpress_kursbelegung . ".veranstaltungs_id
			INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id=" . $teachpress_kursbelegung . ".wp_id
			WHERE " . $teachpress_ver . ".veranstaltungs_id = '$veranstaltung'
			ORDER BY " . $teachpress_stud . ".matrikel";
$row = tp_results($row);
$counter2 = 0;
foreach($row as $row){
	$daten2[$counter2][0] = $row->matrikel;
	$daten2[$counter2][1] = $row->vorname;
	$daten2[$counter2][2] = $row->nachname;
	$daten2[$counter2][3] = $row->studiengang;
	$daten2[$counter2][4] = $row->urzkurz;
	$daten2[$counter2][5] = $row->email;
	$daten2[$counter2][6] = $row->datum;
	$daten2[$counter2][7] = $row->belegungs_id;
	$daten2[$counter2][8] = $row->warteliste;
	$counter2++;
}
// verfügbare Parents
$row = "SELECT veranstaltungs_id, name, semester FROM " . $teachpress_ver . " WHERE parent='0' AND veranstaltungs_id != '$veranstaltung' ORDER BY semester DESC, name";
$row = tp_results($row);
$counter3 = 0;
foreach($row as $row){
	$par[$counter3][0] = $row->veranstaltungs_id;
	$par[$counter3][1] = $row->name;
	$par[$counter3][2] = $row->semester;
	$counter3++;
}

if ($speichern != __('save','teachpress')) { ?>
    <p>
    <a href="admin.php?page=teachpress/teachpress.php&semester2=<?php echo"$sem" ?>&search=<?php echo"$search" ?>" class="teachpress_back" title="<?php _e('back to the overview','teachpress'); ?>">&larr; <?php _e('back','teachpress'); ?></a><a href="admin.php?page=teachpress/listen.php&lvs_ID=<?php echo"$weiter" ?>&sem=<?php echo"$sem" ?>&search=<?php echo"$search" ?>" class="teachpress_back" title="<?php _e('print template for an attendance list','teachpress'); ?>"><?php _e('create attendance list','teachpress'); ?></a>
      <select name="export" id="export" onchange="teachpress_jumpMenu('parent',this,0)" class="teachpress_select">
        <option><?php _e('Export as','teachpress'); ?> ... </option>
        <option value="<?php echo WP_PLUGIN_URL; ?>/teachpress/export.php?lvs_ID=<?php echo"$weiter" ?>&type=csv"><?php _e('csv-file','teachpress'); ?></option>
        <option value="<?php echo WP_PLUGIN_URL; ?>/teachpress/export.php?lvs_ID=<?php echo"$weiter" ?>&type=xls"><?php _e('xls-file','teachpress'); ?></option>
      </select>
      <select name="mail" id="mail" onchange="teachpress_jumpMenu('parent',this,0)" class="teachpress_select">
      	<option><?php _e('E-Mail to','teachpress'); ?> ... </option>
      	<option value="mailto:<?php for($i=0; $i<$counter2; $i++) { if ($daten2[$i][8]== 0 ) { ?><?php echo $daten2[$i][5]; ?> ,<?php } } ?>"><?php _e('registered participants','teachpress'); ?></option>
        <option value="mailto:<?php for($i=0; $i<$counter2; $i++) { if ($daten2[$i][8]== 1 ) { ?><?php echo $daten2[$i][5]; ?> ,<?php } } ?>"><?php _e('participants in waiting list','teachpress'); ?></option>
        <option value="mailto:<?php for($i=0; $i<$counter2; $i++) { echo '' . $daten2[$i][5] . ' ,'; } ?>"><?php _e('all participants','teachpress'); ?></option>
      </select>
    </p>
  <?php } ?>
	<input name="page" type="hidden" value="teachpress/editlvs.php">
    <input name="sem" type="hidden" value="<?php echo"$sem" ?>" />
    <input name="search" type="hidden" value="<?php echo"$search" ?>" />
    <?php
	if ($daten[0][13] != 0) {
		for ($x=0; $x < $counter3; $x++) {
			if ($par[$x][0] == $daten[0][13]) {
				$parent_name = $par[$x][1];
			}
		}
	}
	else {
		$parent_name = "";
	}
	?>
    <h2 style="padding-top:5px;"><?php echo $parent_name; ?> <?php echo $daten[0][1]; ?> <?php echo $daten[0][10]; ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('name_anzeigen')" style="cursor:pointer;"><?php _e('edit','teachpress'); ?></a></small></h2>
    <div id="name_anzeigen" style="display:none; width:850px;">
    	<fieldset style="padding:10px; border:1px solid silver;">
        <legend><?php _e('Edit course','teachpress'); ?></legend>
            <table class="widefat">
				<thead>
            	<tr>
                    <th><?php _e('Course type','teachpress'); ?></th>
					<td>
                      <select name="vtyp" id="vtyp">
                          <option value="<?php echo $daten[0][2]; ?>"><?php echo $daten[0][2]; ?></option>
                          <option>--------------</option>
                          <?php 
							global $teachpress_einstellungen;
							$row = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'veranstaltungstyp' ORDER BY wert";
							$row = tp_results($row);
							foreach ($row as $row) { ?>  
								<option value="<?php echo $row->wert; ?>"><?php echo $row->wert; ?></option>
							<?php } ?>
                      </select>
                    </td>
                    <th><?php _e('Term','teachpress'); ?></th>
					<td>
                        <select name="semester" id="semester">
                            <option value="<?php echo $daten[0][10]; ?>"><?php echo $daten[0][10]; ?></option>
                            <option>---------</option>
                           <?php    
							$sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
							$sem = tp_results($sem);
							$x = 0;
							foreach ($sem as $sem) { 
								$period[$x] = $sem->wert;
								$x++;?>  
								<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
							<?php } ?> 
                        </select>            </td>
                    <th><?php _e('Visibility','teachpress'); ?></th>
<td><select name="sichtbar" id="sichtbar">
                      <option value="<?php echo $daten[0][14]; ?>"><?php if ($daten[0][14] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></option>
                      <option>----</option>
                      <option value="1"><?php _e('yes','teachpress'); ?></option>
                      <option value="0"><?php _e('no','teachpress'); ?></option>
                    </select></td>
                  </tr>
                  <tr>
                    <th><?php _e('ID','teachpress'); ?></th>
                    <td><input name="lvs_ID" type="text" id="parent" size="10" readonly="true" value="<?php echo $daten[0][0]; ?>"/></td>
                    <th><?php _e('Parent','teachpress'); ?></th>
                    <td colspan="3">
                      <select name="par" id="par">
                        <option value="<?php echo $daten[0][13]; ?>"><?php echo $daten[0][13]; ?></option>
                        <option>------</option>
                        <option value="0"><?php _e('none','teachpress'); ?></option>
                         <?php 	for ($i = 0; $i < $x; $i++) {
									for ($j = 0; $j < $counter3; $j++) {
										if ($period[($x - 1)-$i] == $par[$j][2] ) {
											echo '<option value="' . $par[$j][0] . '">' . $par[$j][0] . ' - ' . $par[$j][1] . ' ' . $par[$j][2] . '</option>';
										} 
									} 
									echo '<option>----</option>';  
								}?>
                      </select>    
                     </td>
                    </tr>
              </thead>
        </table>
                <p style="font-size:2px;">&nbsp;</p>
                <table class="widefat">
                 <thead>
                  <tr>
                    <th><?php _e('Course name','teachpress'); ?></th>
                    <td><input name="name" type="text" id="name" value="<?php echo $daten[0][1]; ?>" size="40"/></td>
                   </tr>
                  <tr>
                    <th><?php _e('Lecturer','teachpress'); ?></th>
                    <td><input name="dozent" type="text" value="<?php echo $daten[0][4]; ?>" size="40"/></td>
                  </tr>
                  <tr>
                    <th><?php _e('Date','teachpress'); ?></th>
                    <td><input name="termin" type="text" value="<?php echo $daten[0][5]; ?>" size="40"/></td>
                  </tr>
                  <tr>
                    <th><?php _e('Room','teachpress'); ?></th>
                    <td><input name="raum" type="text" size="40" value="<?php echo $daten[0][3]; ?>"/></td>
                  </tr>
                  <tr>
                    <th><?php _e('Comment','teachpress'); ?></th>
                    <td><input name="bemerkungen" type="text" value="<?php echo $daten[0][11]; ?>" size="70"/></td>
                  </tr>
                  <tr>
                    <th><?php _e('URL','teachpress'); ?></th>
                    <td><input name="url" type="text" value="<?php echo $daten[0][12]; ?>" size="70"/></td>
                  </tr>
                 </thead> 
        </table>
        <h4 style="margin-bottom:7px; margin-top:7px;"><?php _e('Enrollments','teachpress'); ?></h4>
            <table class="widefat">
             <thead>
              <tr>
                <td colspan="6" style="font-size:11px; color:#FF0000;"><strong><?php _e('Format for the date','teachpress'); ?> <?php _e('JJJJ-MM-TT','teachpress'); ?></strong></td>
              </tr>
              <tr>
                <th><?php _e('Number of places','teachpress'); ?></th>
                <td><input name="plaetze" type="text" size="10" value="<?php echo $daten[0][6]; ?>"/></td>
                <th><?php _e('free places','teachpress'); ?></th>
                <td><input name="fplaetze" type="text" size="10" value="<?php echo $daten[0][7]; ?>"/></td>
                <td colspan="2">&nbsp;</td>
               </tr>
              <tr>
                <th><?php _e('Start','teachpress'); ?></th>
                <td><input name="startein" id="startein"type="text" size="10" value="<?php echo $daten[0][8]; ?>"/><input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/></td>
                <th><?php _e('End','teachpress'); ?></th>
                <td><input name="endein" id="endein" type="text" size="10"  value="<?php echo $daten[0][9]; ?>"/><input type="submit" name="calendar2" id="calendar2" value="..." class="teachpress_button"/></td>
                <th><?php _e('Waiting list','teachpress'); ?></th>
                <td><select name="warteliste" id="warteliste">
                   <option value="<?php echo $daten[0][15]; ?>"><?php if ($daten[0][15] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></option>
                  <option>----</option> 
                  <option value="0"><?php _e('no','teachpress'); ?></option>
                  <option value="1"><?php _e('yes','teachpress'); ?></option>
                </select>                </td>
              </tr>
             </thead> 
            </table>
   		<p><input name="speichern" type="submit" value="<?php _e('save','teachpress'); ?>" id="teachpress_einzel_change" class="teachpress_button"/> <a onclick="teachpress_showhide('name_anzeigen')" style="cursor:pointer;"><?php _e('cancel','teachpress'); ?></a></p>
            </fieldset>
        </div>
        <div id="einschreibungen" style="padding:5px;">
        <div style="width:520px;"> 
        <table border="1" cellspacing="0" cellpadding="0" class="widefat" id="teachpress_edit">
            <thead>
              <tr>
                <th><?php _e('Type','teachpress'); ?></th>
                <td><?php echo $daten[0][2]; ?></td>
                <th><?php _e('ID','teachpress'); ?></th>
                <td><?php echo $daten[0][0]; ?></td>
                <th><?php _e('Parent','teachpress'); ?></th>
                <td><?php echo $daten[0][13]; ?></td>
                <th><?php _e('Visibility','teachpress'); ?></th>
                <td><?php if ($daten[0][14] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
              </tr>
           </thead>               
         </table>
         </div>
      <p style="height:5px; margin:0px; padding:0px;">&nbsp;</p>
      <table border="1" cellspacing="0" cellpadding="0" class="widefat">
      		<thead>
              <tr>
                <th><?php _e('Lecturer','teachpress'); ?></th>
                <td><?php echo $daten[0][4]; ?></td>
                <th><?php _e('Date','teachpress'); ?></th>
                <td><?php echo $daten[0][5]; ?></td>
                <th><?php _e('Room','teachpress'); ?></th>
                <td><?php echo $daten[0][3]; ?></td>
              </tr>
              <tr>
                <th><?php _e('Places','teachpress'); ?></th>
                <td><?php echo $daten[0][6]; ?></td>
                <th><?php _e('free places','teachpress'); ?></th>
                <td><?php echo $daten[0][7]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <th><?php _e('Comment','teachpress'); ?></th>
                <td colspan="5"><?php echo $daten[0][11]; ?></td>
              </tr>
              <tr>
                <th><?php _e('URL','teachpress'); ?></th>
                <td colspan="5"><?php echo $daten[0][12]; ?></td>
              </tr>
              </thead>
            </table>
        <div style="width:620px; padding-top:5px; padding-bottom:10px;">
 		<table border="1" cellspacing="0" cellpadding="0" class="widefat">
         <thead>
          <tr>
            <th><?php _e('Start of enrollments','teachpress'); ?></th>
            <td><?php echo $daten[0][8]; ?></td>
            <th colspan="3"><?php _e('End of enrollments','teachpress'); ?></th>
            <td><?php echo $daten[0][9]; ?></td>
           </tr>
          </thead>
        </table>
        </div>
        <table class="widefat">
         <thead>
          <tr>
            <th>&nbsp;</th>
            <th><?php _e('Registr.-Number','teachpress'); ?></th>
            <th><?php _e('Last name','teachpress'); ?></th>
            <th><?php _e('First name','teachpress'); ?></th>
            <th><?php _e('Course of studies','teachpress'); ?></th>
            <th><?php _e('User account','teachpress'); ?></th>
            <th><?php _e('E-Mail','teachpress'); ?></th>
            <th><?php _e('Date','teachpress'); ?></th>
          </tr>
         </thead>  
         <tbody> 
		<?php
			// Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
			for($i=0; $i<$counter2; $i++) {
				if ($daten2[$i][8]== 0 ) {
				  ?>
				  <tr>
					<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $daten2[$i][7]; ?>"/></th>
					<td><?php echo $daten2[$i][0]; ?></td>
					<td><?php echo $daten2[$i][2]; ?></td>
					<td><?php echo $daten2[$i][1]; ?></td>
					<td><?php echo $daten2[$i][3]; ?></td>
					<td><?php echo $daten2[$i][4]; ?></td>
					<td><a href="mailto:<?php echo $daten2[$i][5]; ?>" title="<?php _e('send E-Mail','teachpress'); ?>"><?php echo $daten2[$i][5]; ?></a></td>
					<td><?php echo $daten2[$i][6]; ?></td>
				  </tr>
				  <?php
			  }
		   }
			?>
        </tbody>
        </table>
		<?php
		// Ausgabe der Warteliste
		$test = 0;
		for($i=0; $i<$counter2; $i++) {
			if ($daten2[$i][8]== 1 ) {
				$test++;
			}
		}	
		if ($test != 0) { ?>
			<h3>Warteliste</h3>
			<table class="widefat">
             <thead>
              <tr>
                <th>&nbsp;</th>
                <th><?php _e('Registr.-Number','teachpress'); ?></th>
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
				if ($daten2[$i][8]== 1 ) { ?>
                    <tr>
                        <th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $daten2[$i][7]; ?>"/></th>
                        <td><?php echo $daten2[$i][0]; ?></td>
                        <td><?php echo $daten2[$i][2]; ?></td>
                        <td><?php echo $daten2[$i][1]; ?></td>
                        <td><?php echo $daten2[$i][3]; ?></td>
                        <td><?php echo $daten2[$i][4]; ?></td>
                        <td><a href="mailto:<?php echo $daten2[$i][5]; ?>" title="<?php _e('send E-Mail','teachpress'); ?>"><?php echo $daten2[$i][5]; ?></a></td>
                        <td><?php echo $daten2[$i][6]; ?></td>
                    </tr> 
				<?php } }?>
            </tbody>
            </table>
        <?php  } ?>
<table border="0" cellspacing="7" cellpadding="0" id="einzel_optionen">
          <tr>
            <td colspan="4"></td>
          </tr>
          <tr>
            <td><?php if ($test != 0) { ?><?php _e('ingest in course','teachpress'); ?> <?php } ?></td>
            <td><?php if ($test != 0) { ?><input name="aufnehmen" type="submit" value="<?php _e('ingest','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/><?php } ?></td>
            <td><?php _e('delete enrollment','teachpress'); ?></td>
            <td><input name="loeschen" type="submit" value="<?php _e('delete','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/></td>
          </tr>
    </table>
</form>
<script type="text/javascript">
  Calendar.setup(
    {
      inputField  : "startein",         // ID of the input field
      ifFormat    : "%Y-%m-%d",    // the date format
      button      : "calendar"       // ID of the button
    }
  );
  Calendar.setup(
    {
      inputField  : "endein",         // ID of the input field
      ifFormat    : "%Y-%m-%d",    // the date format
      button      : "calendar2"       // ID of the button
    }
  );
</script>
</div>
<?php } ?>