<?php
/*
 * Excel-Export von Lehrveranstaltungen und zugehörigen Einschreibungen
 *
*/
if (isset($_REQUEST[lvs_ID]) && isset($_REQUEST[type]) ) {
	include_once('parameters.php');
	include_once('version.php');
	
	// wp-load.php einbinden
	global $root;
	require( '' . $_SERVER['DOCUMENT_ROOT'] . '/' . $root . '/wp-load.php' );
	
	if (is_user_logged_in()) {
	
		// Typ auslesen
		$type = htmlentities($_REQUEST[type]);
		$filename = 'teachpress_' . date('dmY');
		
		// Header veraendern
		if ($type == "xls") {
			header("Content-type: application/vnd-ms-excel"); 
			header("Content-Disposition: attachment; filename=" . $filename . ".xls");
		}
		if ($type == "xml") {
			header("Content-type: text/xml; charset=utf-8");
			header("Content-Disposition: attachment; filename=" . $filename . ".xml"); 
		}
		
		global $teachpress_ver; 
		global $teachpress_stud; 
		global $teachpress_einstellungen; 
		global $teachpress_kursbelegung;
		// ID der Lehrveranstaltung auslesen
		$lvs = htmlentities(utf8_decode($_GET[lvs_ID]));
		settype($lvs, 'integer');
		// Daten der Lehrveranstaltung laden
		$row= "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$lvs'";
		$row = $wpdb->get_results($row);
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
		// Daten der Einschreibungen laden
		$row = "SELECT " . $teachpress_stud . ".matrikel, " . $teachpress_stud . ".vorname, " . $teachpress_stud . ".nachname, " . $teachpress_stud . ".studiengang,  " . $teachpress_stud . ".urzkurz, " . $teachpress_stud . ".email , " . $teachpress_kursbelegung . ".datum, " . $teachpress_kursbelegung . ".belegungs_id, " . $teachpress_kursbelegung . ".warteliste
					FROM " . $teachpress_kursbelegung . " 
					INNER JOIN " . $teachpress_ver . " ON " . $teachpress_ver . ".veranstaltungs_id=" . $teachpress_kursbelegung . ".veranstaltungs_id
					INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id=" . $teachpress_kursbelegung . ".wp_id
					WHERE " . $teachpress_ver . ".veranstaltungs_id = '$lvs'
					ORDER BY " . $teachpress_stud . ".matrikel";
		$row = $wpdb->get_results($row);
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
		if ($type == "xls") {
			?>
			<h2><?php echo $daten[0][1] ?> <?php echo $daten[0][10] ?> </h2>
			<table border="1" cellspacing="0" cellpadding="5">
			<thead>
			  <tr>
				<th><?php _e('Dozent','teachpress'); ?></th>
				<td><?php echo $daten[0][4]; ?></td>
				<th><?php _e('Termin','teachpress'); ?></th>
				<td><?php echo $daten[0][5]; ?></td>
				<th><?php _e('Raum','teachpress'); ?></th>
				<td><?php echo $daten[0][3]; ?></td>
			  </tr>
			  <tr>
				<th><?php _e('Pl&auml;tze','teachpress'); ?></th>
				<td><?php echo $daten[0][6]; ?></td>
				<th><?php _e('freie Pl&auml;tze','teachpress'); ?></th>
				<td><?php echo $daten[0][7]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<th><?php _e('Bemerkungen','teachpress'); ?></th>
				<td colspan="5"><?php echo $daten[0][11]; ?></td>
			  </tr>
			  <tr>
				<th><?php _e('URL','teachpress'); ?></th>
				<td colspan="5"><?php echo $daten[0][12]; ?></td>
			  </tr>
			  </thead>
			</table>
			<h3><?php _e('Eingeschriebene Teilnehmer','teachpress'); ?></h3>
			 <table border="1" cellpadding="5" cellspacing="0">
					 <thead>
					  <tr>
						  <th><?php _e('Matrikel','teachpress'); ?></th>
						  <th><?php _e('Nachname','teachpress'); ?></th>
						  <th><?php _e('Vorname','teachpress'); ?></th>
						  <th><?php _e('Studiengang','teachpress'); ?></th>
						  <th><?php _e('URZ-K&uuml;rzel','teachpress'); ?></th>
						  <th><?php _e('E-Mail','teachpress'); ?></th>
						  <th><?php _e('Datum','teachpress'); ?></th>
					  </tr>
					 </thead>  
					 <tbody> 
					<?php
						// Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
						for($i=0; $i<$counter2; $i++) {
							if ($daten2[$i][8]== 0 ) {
							  ?>
							  <tr>
								<td><?php echo $daten2[$i][0]; ?></td>
								<td><?php echo $daten2[$i][2]; ?></td>
								<td><?php echo $daten2[$i][1]; ?></td>
								<td><?php echo $daten2[$i][3]; ?></td>
								<td><?php echo $daten2[$i][4]; ?></td>
								<td><?php echo $daten2[$i][5]; ?></td>
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
						<h3><?php _e('Warteliste','teachpress'); ?></h3>
						<table border="1" cellpadding="5" cellspacing="0">
						 <thead>
						  <tr>
							<th><?php _e('Matrikel','teachpress'); ?></th>
							<th><?php _e('Nachname','teachpress'); ?></th>
							<th><?php _e('Vorname','teachpress'); ?></th>
							<th><?php _e('Studiengang','teachpress'); ?></th>
							<th><?php _e('URZ-K&uuml;rzel','teachpress'); ?></th>
							<th><?php _e('E-Mail','teachpress'); ?></th>
							<th><?php _e('Datum','teachpress'); ?></th>
						  </tr>
						 </thead>  
						 <tbody> 
						 <?php
						for($i=0; $i<$counter2; $i++) {
							if ($daten2[$i][8]== 1 ) { ?>
								<tr>
									<td><?php echo $daten2[$i][0]; ?></td>
									<td><?php echo $daten2[$i][2]; ?></td>
									<td><?php echo $daten2[$i][1]; ?></td>
									<td><?php echo $daten2[$i][3]; ?></td>
									<td><?php echo $daten2[$i][4]; ?></td>
									<td><?php echo $daten2[$i][5]; ?></td>
									<td><?php echo $daten2[$i][6]; ?></td>
								</tr> 
							<?php } }?>
						</tbody>
				</table>
			<?php  } 
			global $tp_version;
			?>       
			<p style="font-size:11px; font-style:italic;"><?php _e('Erstellt am','teachpress'); ?>: <?php echo date("d.m.Y")?> | teachPress <?php echo $tp_version ?></p>  
        <?php }
if ($type == "xml") {?>
<?xml version="1.0" encoding="utf-8"?>
<export>
 <title><?php echo $daten[0][1] ?> <?php echo $daten[0][10] ?></title>
 <type><?php echo $daten[0][2]; ?></type>
 <dozent><?php echo $daten[0][4]; ?></dozent>
 <date><?php echo $daten[0][5]; ?></date>
 <room><?php echo $daten[0][3]; ?></room>
 <places><?php echo $daten[0][6]; ?></places>
 <freeplaces><?php echo $daten[0][7]; ?></freeplaces>
 <url><?php echo $daten[0][12]; ?></url>
 <bemerkungen><?php echo $daten[0][11]; ?></bemerkungen>
 <?php	
 for($i=0; $i<$counter2; $i++) {
	if ($daten2[$i][8]== 0 ) { ?>
	 <enrollment> 
     	<id><?php echo $daten2[$i][7]; ?></id>          
		<matrikel><?php echo $daten2[$i][0]; ?></matrikel>
		<nachname><?php echo $daten2[$i][2]; ?></nachname>
		<vorname><?php echo $daten2[$i][1]; ?></vorname>
		<studiengang><?php echo $daten2[$i][3]; ?></studiengang>
		<kennzeichen><?php echo $daten2[$i][4]; ?></kennzeichen>
		<email><?php echo $daten2[$i][5]; ?></email>
		<edate><?php echo $daten2[$i][6]; ?></edate>
	 </enrollment>
 <?php } }
 for($i=0; $i<$counter2; $i++) {
	if ($daten2[$i][8]== 1 ) { ?>
	 <waiting>
     	<id><?php echo $daten2[$i][7]; ?></id>
		<matrikel><?php echo $daten2[$i][0]; ?></matrikel>
		<nachname><?php echo $daten2[$i][2]; ?></nachname>
		<vorname><?php echo $daten2[$i][1]; ?></vorname>
		<studiengang><?php echo $daten2[$i][3]; ?></studiengang>
		<kennzeichen><?php echo $daten2[$i][4]; ?></kennzeichen>
		<email><?php echo $daten2[$i][5]; ?></email>
		<edate><?php echo $daten2[$i][6]; ?></edate>
	 </waiting>
 <?php } } ?>
</export>
<?php    
}
}
} ?>   