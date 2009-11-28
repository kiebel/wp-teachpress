<?php
/* Uebersicht Kurse
 * from editlvs.php, showlvs.php
 * @param $semester2 (String) 
 * @param $search (String)
*/
?>

<?php 
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
    $bulk = $_GET[bulk];
	$copysem = htmlentities(utf8_decode($_GET[copysem]));
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
	// Veranstaltungen loeschen
    if ( $bulk == "delete" ) {?>
		<div class="teachpress_message">
        <p class="hilfe_headline"><?php _e('Are you sure to delete the selected courses?','teachpress'); ?></p>
        <p><input name="delete_ok" type="submit" class="teachpress_button" value="<?php _e('delete','teachpress'); ?>"/>
        <a href="<?php echo 'admin.php?page=teachpress/teachpress.php&semester2=' . $semester2 . '&search=' . $search . ''; ?>"> <?php _e('cancel','teachpress'); ?></a></p>
		</div>
        <?php
    }
	// Veranstaltung loeschen Teil 2
	if ( isset($_GET[delete_ok]) ) {
		delete_lehrveranstaltung($checkbox);
		$message = __('Course(s) deleted','teachpress');
		$site = 'admin.php?page=teachpress/teachpress.php&semester2=' . $semester2 . '&search=' . $search . '';
		tp_get_message($message, $site);
	}
	// Veranstaltungen kopieren
	if ( $bulk == "copy" ) { ?>
    	<div class="teachpress_message">
        <p class="hilfe_headline"><?php _e('Copy courses','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Select the term, in which you will copy the selected courses.','teachpress'); ?></p>
        <p class="hilfe_text">
    	<select name="copysem" id="copysem">
            <?php    
		    $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
			$sem = tp_results($sem);
			foreach ($sem as $sem) { ?> 
				<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
			<?php } ?> 
        </select>
        <input name="copy_ok" type="submit" class="teachpress_button" value="<?php _e('copy','teachpress'); ?>"/>
        <a href="<?php echo 'admin.php?page=teachpress/teachpress.php&semester2=' . $semester2 . '&search=' . $search . ''; ?>"> <?php _e('cancel','teachpress'); ?></a>
        </p>
        </div>
    <?php
	}
	// Kopiervorgang Teil 2
	if ( isset($_GET[copy_ok]) ) {
		copy_veranstaltung($checkbox, $copysem);
		$message = __('Copying successful','teachpress');
		$site = 'admin.php?page=teachpress/teachpress.php&semester2=' . $semester2 . '&search=' . $search . '';
		tp_get_message($message, $site);
	}
    ?>
    <table border="0" cellspacing="0" cellpadding="5" style="float:right;">
        <tr>
            <td><?php if ($search != "") { ?><a href="admin.php?page=teachpress/teachpress.php" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Cancel the search','teachpress'); ?>">&crarr;</a><?php } ?></td>
            <td><input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/></td>
            <td><input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/></td>
        </tr>
    </table>  
  <table cellpadding="5" id="filter">
    <tr>
      <td>
        <select name="semester2" id="semester2">
            <option value="<?php echo"$semester2" ?>"><?php echo"$semester2" ?></option>
            <option>------</option>
        	<option value="alle"><?php _e('All terms','teachpress'); ?></option>
            <?php    
		    $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
			$sem = tp_results($sem);
			foreach ($sem as $sem) { ?> 
				<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
			<?php } ?> 
        </select>
      </td>
      <td style="padding-left:10px;"><input type="submit" name="start" value="<?php _e('show','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/></td>
      <td style="padding-left:10px;">
      	<select name="bulk" id="bulk">
        	<option><?php _e('Bulk actions','teachpress'); ?></option>
            <option value="copy"><?php _e('copy','teachpress'); ?></option>
            <option value="delete"><?php _e('delete','teachpress'); ?></option>
      </select>
      </td>
      <td style="padding-left:10px;"><input type="submit" name="teachpress_submit" value="<?php _e('ok','teachpress'); ?>" id="teachpress_submit2" class="teachpress_button"/></td>
    </tr>
  </table>
<p style="padding:0px; margin:0px; height:8px;">&nbsp;</p> 
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
	<tr>
	  	<th>&nbsp;</th>
      	<th><?php _e('Name','teachpress'); ?></th>
    	<th><?php _e('ID','teachpress'); ?></th>
        <th><?php _e('Type','teachpress'); ?></th> 
        <th><?php _e('Room','teachpress'); ?></th>
        <th><?php _e('Lecturer','teachpress'); ?></th>
        <th><?php _e('Date','teachpress'); ?></th>
        <th colspan="2" align="center" style="text-align:center;"><?php _e('Places','teachpress'); ?></th>
        <th colspan="2" align="center" style="text-align:center;"><?php _e('Enrollments','teachpress'); ?></th>
        <th><?php _e('Term','teachpress'); ?></th>
        <th><?php _e('Parent','teachpress'); ?></th>
        <th><?php _e('Visibility','teachpress'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php
	// Abfragen je nachdem was im Filter gewaehlt wurde
	if ($search == "") {
		if ($semester2 == 'alle') {
			$abfrage = "SELECT * FROM " . $teachpress_ver . " ORDER BY semester DESC, name";
			}
		else {
			$abfrage = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$semester2' ORDER BY name, veranstaltungs_id";
		}	
	}
	// Falls Eingabe in Suchfeld
	else {
		$abfrage = "SELECT veranstaltungs_id, name, vtyp, dozent, termin, raum, plaetze, fplaetze, startein, endein, semester, parent, sichtbar, parent_name 
		FROM (SELECT t.veranstaltungs_id AS veranstaltungs_id, t.name AS name, t.vtyp AS vtyp, t.dozent AS dozent, t.termin AS termin, t.raum As raum, t.plaetze AS plaetze, t.fplaetze AS fplaetze, t.startein AS startein, t.endein As endein, t.semester AS semester, t.parent As parent, t.sichtbar AS sichtbar, p.name AS parent_name FROM " . $teachpress_ver . " t LEFT JOIN " . $teachpress_ver . " p ON t.parent = p.veranstaltungs_id ) AS temp 
		WHERE name like '%$search%' OR parent_name like '%$search%' OR dozent like '%$search%' OR termin like '%$search%' OR raum like '%$search%' OR veranstaltungs_id = '$search' 
		ORDER BY semester DESC, name";
	}
	$test = tp_query($abfrage);	
	// Falls es keine Treffer gibt
	if ($test == 0) { ?>
        	<tr>
           	  <td colspan="14"><strong><?php _e('Sorry, no entries matched your criteria.','teachpress'); ?></strong></td>
            </tr>
    <?php }
	// Zusammenstellung Ergebnisse
	else {
		$z = 0;
		$ergebnis = tp_results($abfrage);
		foreach ($ergebnis as $row){
			$courses[$z][0] = $row->veranstaltungs_id;
			$courses[$z][1] = $row->name;
			$courses[$z][2] = $row->vtyp;
			$courses[$z][3] = $row->raum;
			$courses[$z][4] = $row->dozent;
			$courses[$z][5] = $row->termin;
			$courses[$z][6] = $row->plaetze;
			$courses[$z][7] = $row->fplaetze;
			$courses[$z][8] = $row->startein;
			$courses[$z][9] = $row->endein;
			$courses[$z][10] = $row->semester;
			$courses[$z][11] = $row->parent;
			$courses[$z][12] = $row->sichtbar;
			$z++;
		}
		// Ausgabe Kurse
		for ($i=0; $i<$z; $i++) {
			if ($search == "") {
				if ($courses[$i][11] == 0) {?>
					<tr id="teachpress_table">
						<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $courses[$i][0]; ?>" <?php if ( $bulk == "copy" || $bulk == "delete") { for( $k = 0; $k < count( $checkbox ); $k++ ) { if ( $courses[$i][0] == $checkbox[$k] ) {?>checked="checked"<?php } } }?>/></th>
						<td><a href="admin.php?page=teachpress/editlvs.php&lvs_ID=<?php echo $courses[$i][0]; ?>&sem=<?php echo"$semester2" ?>&search=<?php echo"$search" ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo $courses[$i][1]; ?></a></td>
						<td><?php echo $courses[$i][0]; ?></td>
						<td><?php echo $courses[$i][2]; ?></td>
						<td><?php echo $courses[$i][3]; ?></td>
						<td><?php echo $courses[$i][4]; ?></td>
						<td><?php echo $courses[$i][5]; ?></td>
						<td><?php echo $courses[$i][6]; ?></td>
						<td<?php if ($courses[$i][6] > 0 && $courses[$i][7] == 0) {?> style="color:#ff6600; font-weight:bold;"<?php } ?>><?php echo $courses[$i][7]; ?></td>
						<td><?php echo $courses[$i][8]; ?></td>
						<td><?php echo $courses[$i][9]; ?></td>
						<td><?php echo $courses[$i][10]; ?></td>
						<td><?php echo $courses[$i][11]; ?></td>
						<td><?php if ($courses[$i][12] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
					</tr> <?php
					// Childs suchen
					for ($j=0; $j<$z; $j++) {
						if ($courses[$i][0] == $courses[$j][11]) { ?>
						   <tr id="teachpress_table">
								<th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $courses[$j][0]; ?>" <?php if ( $bulk == "copy" || $bulk == "delete") { for( $k = 0; $k < count( $checkbox ); $k++ ) { if ( $courses[$j][0] == $checkbox[$k] ) {?>checked="checked"<?php } } }?>/></th>
								<td><a href="admin.php?page=teachpress/editlvs.php&lvs_ID=<?php echo $courses[$j][0]; ?>&sem=<?php echo"$semester2" ?>&search=<?php echo"$search" ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo $courses[$i][1]; ?> <?php echo $courses[$j][1]; ?></a></td>
								<td><?php echo $courses[$j][0]; ?></td>
								<td><?php echo $courses[$j][2]; ?></td>
								<td><?php echo $courses[$j][3]; ?></td>
								<td><?php echo $courses[$j][4]; ?></td>
								<td><?php echo $courses[$j][5]; ?></td>
								<td><?php echo $courses[$j][6]; ?></td>
								<td<?php if ($courses[$j][6] > 0 && $courses[$j][7] == 0) {?> style="color:#ff6600; font-weight:bold;"<?php } ?>><?php echo $courses[$j][7]; ?></td>
								<td><?php echo $courses[$j][8]; ?></td>
								<td><?php echo $courses[$j][9]; ?></td>
								<td><?php echo $courses[$j][10]; ?></td>
								<td><?php echo $courses[$j][11]; ?></td>
								<td><?php if ($courses[$j][12] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
						   </tr><?php
						}
					}
				}
			}
			else {
				if ($courses[$i][11] != 0) {
					$parent_name = tp_var("SELECT name FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '" . $courses[$i][11] . "'");
					$parent_name = $parent_name . " ";
				}
				else {
					$parent_name = "";
				} ?>
                <tr id="teachpress_table">
                    <th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $courses[$i][0]; ?>" <?php if ( $bulk == "copy" || $bulk == "delete") { for( $k = 0; $k < count( $checkbox ); $k++ ) { if ( $courses[$i][0] == $checkbox[$k] ) {?>checked="checked"<?php } } }?>/></th>
                    <td><a href="admin.php?page=teachpress/editlvs.php&lvs_ID=<?php echo $courses[$i][0]; ?>&sem=<?php echo"$semester2" ?>&search=<?php echo"$search" ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo $parent_name . $courses[$i][1]; ?></a></td>
                    <td><?php echo $courses[$i][0]; ?></td>
                    <td><?php echo $courses[$i][2]; ?></td>
                    <td><?php echo $courses[$i][3]; ?></td>
                    <td><?php echo $courses[$i][4]; ?></td>
                    <td><?php echo $courses[$i][5]; ?></td>
                    <td><?php echo $courses[$i][6]; ?></td>
                    <td<?php if ($courses[$i][6] > 0 && $courses[$i][7] == 0) {?> style="color:#ff6600; font-weight:bold;"<?php } ?>><?php echo $courses[$i][7]; ?></td>
                    <td><?php echo $courses[$i][8]; ?></td>
                    <td><?php echo $courses[$i][9]; ?></td>
                    <td><?php echo $courses[$i][10]; ?></td>
                    <td><?php echo $courses[$i][11]; ?></td>
                    <td><?php if ($courses[$i][12] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
                </tr> <?php
			}
		}	
	}   
?>
</tbody>
</table>
</form>
</div>
<?php } ?>