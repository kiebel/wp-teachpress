<?php
/*
 * Anzeige der Lehrveranstaltungen im Backend
 *
 * Eingangsparameter von editlvs.php
 * $semester2 - String 
 * $search - String
 * (wurden  beide beim Aufruf von editlvs.php bereits übergeben
 * und werden nun wieder zurueckgegeben, um die Anzeige wiederherzustellen)
*/
?>

<?php 
/* Sicherheitsabfrage ob User eingeloggt ist, um unbefugte Zugriffe von außen zu vermeiden
 * Nur wenn der User eingeloggt ist, wird das Script ausgeführt
*/ 
if ( is_user_logged_in() ) { 
?> 

<div class="wrap" style="padding-top:10px;">
  <form id="showlvs" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
  <input name="page" type="hidden" value="teachpress/teachpress.php" />
	<?php 
	global $teachpress_einstellungen; 
	global $teachpress_ver;
    // Formular-Einträge aus dem Post Array holen
    $checkbox = $_GET[checkbox];
    $delete = $_GET[delete];
	$search = htmlentities(utf8_decode($_GET[search]));
	// Wenn Semester vorher von User ausgewaehlt wurde
	if (isset($_GET[semester2])) {
		$semester2 = htmlentities(utf8_decode($_GET[semester2]));
	}
	else {
		$abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
		$semester2 = tp_var($abfrage);
	}	
    // Prüfen welche Checkbox genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
    if ( isset($delete)) {
        delete_lehrveranstaltung($checkbox);
		$message = __('ausgew&auml;hlte Lehrveranstaltung gel&ouml;scht','teachpress');
		$site = 'admin.php?page=teachpress/teachpress.php&semester2=' . $semester2 . '';
		tp_get_message($message, $site);
    }
    ?> 
    <table border="0" cellspacing="0" cellpadding="5" style="float:right;">
        <tr>
            <td><?php if ($search != "") { ?><a href="admin.php?page=teachpress/teachpress.php" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Suche abbrechen','teachpress'); ?>">&crarr;</a><?php } ?></td>
            <td><input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/></td>
            <td><input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('suche','teachpress'); ?>" class="teachpress_button"/></td>
        </tr>
    </table>  
  <table cellpadding="5" id="filter">
    <tr>
      <td><label>
        <select name="semester2" id="semester2">
            <option value="<?php echo"$semester2" ?>"><?php echo"$semester2" ?></option>
            <option>------</option>
        	<option value="alle"><?php _e('Alle Semester','teachpress'); ?></option>
            <?php    
		    $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
			$sem = tp_results($sem);
			foreach ($sem as $sem) { ?> 
				<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
			<?php } ?> 
        </select>
      </label></td>
      <td style="padding-left:10px;"><input type="submit" name="start" value="<?php _e('anzeigen','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/></td>
    </tr>
  </table>
<p style="padding:0px; margin:0px; height:8px;">&nbsp;</p> 
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
	  	<th>&nbsp;</th>
      	<th><?php _e('Name','teachpress'); ?></th>
    	<th><?php _e('ID','teachpress'); ?></th>
        <th><?php _e('Typ','teachpress'); ?></th> 
        <th><?php _e('Raum','teachpress'); ?></th>
        <th><?php _e('Dozent','teachpress'); ?></th>
        <th><?php _e('Termin','teachpress'); ?></th>
        <th colspan="2" align="center" style="text-align:center;"><?php _e('Pl&auml;tze','teachpress'); ?></th>
        <th colspan="2" align="center" style="text-align:center;"><?php _e('Einschreibungen','teachpress'); ?></th>
        <th><?php _e('Semester','teachpress'); ?></th>
        <th><?php _e('Parent','teachpress'); ?></th>
        <th><?php _e('Sichtbar','teachpress'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php
	// Abfragen je nachdem was im Filter gewählt wurde
	if ($search == "") {
		if ($semester2 == 'alle') {
			$abfrage = "SELECT * FROM " . $teachpress_ver . " ORDER BY semester DESC, name";
			}
		else {
			$abfrage = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$semester2' ORDER BY semester DESC, name";
		}	
	}
	else {
		$abfrage = "SELECT * FROM " . $teachpress_ver . " 
					WHERE name like '%$search%' OR dozent like '%$search%' OR termin like '%$search%' OR raum like '%$search%' OR veranstaltungs_id = '$search'
					ORDER BY semester DESC, name";		
	}
	$test = tp_query($abfrage);	
	if ($test == 0) { ?>
        	<tr>
           	  <td colspan="14"><strong><?php _e('Keine Eintr&auml;ge vorhanden','teachpress'); ?></strong></td>
            </tr>
    <?php }
	else {
		$ergebnis = tp_results($abfrage);
		foreach ($ergebnis as $row){
		   ?>
		  <tr id="teachpress_table">
				<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo"$row->veranstaltungs_id" ?>" /></th>
				<td><a href="admin.php?page=teachpress/editlvs.php&lvs_ID=<?php echo"$row->veranstaltungs_id" ?>&sem=<?php echo"$semester2" ?>&search=<?php echo"$search" ?>" class="teachpress_link" title="<?php _e('Zum Bearbeiten klicken','teachpress'); ?>"><?php echo"$row->name" ?></a></td>
				<td><?php echo"$row->veranstaltungs_id" ?></td>
				<td><?php echo"$row->vtyp" ?></td>
				<td><?php echo"$row->raum" ?></td>
				<td><?php echo"$row->dozent" ?></td>
				<td><?php echo"$row->termin" ?></td>
				<td><?php echo"$row->plaetze" ?></td>
				<td<?php if ($row->plaetze > 0 && $row->fplaetze == 0) {?> style="color:#ff6600; font-weight:bold;"<?php } ?>><?php echo"$row->fplaetze" ?></td>
				<td><?php echo"$row->startein" ?></td>
				<td><?php echo"$row->endein" ?></td>
				<td><?php echo"$row->semester" ?></td>
				<td><?php echo"$row->parent" ?></td>
				<td><?php if ($row->sichtbar == 1) {echo"ja";} else {echo"nein";} ?></td>
		  </tr>
	   <?php       
	   }
	}   
?>
</tbody>
</table>
<table border="0" cellpadding="5" cellspacing="5" id="show_lvs_optionen">
  <tr>
    <td><?php _e('Ausgew&auml;hlte Veranstaltungen l&ouml;schen','teachpress'); ?></td>
    <td><input type="checkbox" name="delete" id="teachpress_delete" value="delete" title="<?php _e('Markieren sie dieses Feld und bet&auml;tigen Sie anschlie&szlig;end den Submit-Button, um die ausgew&auml;hltes Lehrveranstaltungen zu l&ouml;schen','teachpress'); ?>"/>
     </td>
     <td>
     	<input type="submit" name="teachpress_submit" value="<?php _e('submit','teachpress'); ?>" id="teachpress_submit2" class="teachpress_button"/>
     </td>
    </tr>
</table>
</form>
</div>
<?php } ?>