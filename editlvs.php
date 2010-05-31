<?php 
/* Bearbeitung von Lehrveranstaltungen
 * from showlvs.php (GET):
 * @param $lvs_ID (INT) - ID der Veranstaltung die geladen werden soll
 * @param $sem (String) - angezeigtes Semester auf showlvs.php
 * @param $search (String) - verwendeter Suchstring auf showlvs.php
*/
function teachpress_editlvs_page () { 
?>
<div class="wrap">
<form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
<?php
// Datenbankvariablen
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
$name = tp_sec_var($_GET[name]);
$vtyp = tp_sec_var($_GET[vtyp]);
$raum = tp_sec_var($_GET[raum]);
$dozent = tp_sec_var($_GET[dozent]);
$termin = tp_sec_var($_GET[termin]);
$plaetze = tp_sec_var($_GET[plaetze], 'integer'); 
$fplaetze = tp_sec_var($_GET[fplaetze], 'integer');
$startein = tp_sec_var($_GET[startein]); 
$endein = tp_sec_var($_GET[endein]); 
$semester = tp_sec_var($_GET[semester]);
$bemerkungen = tp_sec_var($_GET[bemerkungen]);
$rel_page = tp_sec_var($_GET[rel_page]);
$sichtbar = tp_sec_var($_GET[sichtbar]);
$parent = tp_sec_var($_GET[par]);
$warteliste = tp_sec_var($_GET[warteliste]);
$weiter = tp_sec_var($_GET[lvs_ID], 'integer');
$veranstaltung = tp_sec_var($_GET[lvs_ID], 'integer');
// fuer Zurueckleitung an showlvs.php
$search = tp_sec_var($_GET[search]);
$sem = tp_sec_var($_GET[sem]);
// Befehle ausfürhen
if ( isset($speichern) ) {
	tp_change_lvs($name, $vtyp, $raum, $dozent, $termin, $plaetze, $fplaetze, $startein, $endein, $semester, $bemerkungen, $rel_page, $parent, $sichtbar, $warteliste, $veranstaltung);
	$message = __('Changes successful','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);
}
if ( isset($aufnehmen) ) {
    tp_add_from_waitinglist($checkbox);
	$message = __('Participant added','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);	
    }	 
if ( isset($delete)) {
    tp_delete_registration($checkbox, $user_ID);
	$message = __('Enrollments deleted','teachpress');
	$site = 'admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '';
	tp_get_message($message, $site);	
}
// Abfrage-Arrays füllen
// LVS-Daten
$row = "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$veranstaltung'";
$row = tp_results($row);
foreach($row as $row){
	$daten[0][0] = $row->veranstaltungs_id;
	$daten[0][1] = $row->name;
	$daten[0][2] = $row->vtyp;
	$daten[0][3] = $row->raum;
	$daten[0][4] = $row->dozent;
	$daten[0][5] = $row->termin;
	$daten[0][6] = $row->plaetze;
	$daten[0][7] = $row->fplaetze;
	$daten[0][8] = $row->startein;
	$daten[0][9] = $row->endein;
	$daten[0][10] = $row->semester;
	$daten[0][11] = $row->bemerkungen;
	$daten[0][12] = $row->rel_page;
	$daten[0][13] = $row->parent;
	$daten[0][14] = $row->sichtbar;
	$daten[0][15] = $row->warteliste;
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
    <a href="admin.php?page=teachpress/teachpress.php&semester2=<?php echo"$sem" ?>&search=<?php echo"$search" ?>" class="teachpress_back" title="<?php _e('back to the overview','teachpress'); ?>">&larr; <?php _e('back','teachpress'); ?></a><a href="admin.php?page=teachpress/lists.php&lvs_ID=<?php echo"$weiter" ?>&sem=<?php echo"$sem" ?>&search=<?php echo"$search" ?>" class="teachpress_back" title="<?php _e('create an attendance list','teachpress'); ?>"><?php _e('create attendance list','teachpress'); ?></a>
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
	// define course name
	if ($daten[0][13] != 0) {
		for ($x=0; $x < $counter3; $x++) {
			if ($par[$x][0] == $daten[0][13]) {
				$parent_name = $par[$x][1];
				// Wenn parent_name == child name
				if ($parent_name == $daten[0][1]) {
					$parent_name = "";
				}
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
                <th><label for="vtyp"><?php _e('Course type','teachpress'); ?></label></th>
                <td>
                  <select name="vtyp" id="vtyp">
                      <?php
                        global $teachpress_einstellungen;
                        $row = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'veranstaltungstyp' ORDER BY wert";
                        $row = tp_results($row);
                        foreach ($row as $row) { 
							if ($daten[0][2] == $row->wert) {
								$current = 'selected="selected"' ;
							}
							else {
								$current = '' ;
							}  
                            echo '<option value="' . $row->wert . '" ' . $current . '>' . $row->wert . '</option>';
                        } ?>
                  </select>
                </td>
                <th><label for="semester"><?php _e('Term','teachpress'); ?></label></th>
                <td>
                    <select name="semester" id="semester">
                       <?php    
                        $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
                        $sem = tp_results($sem);
                        $x = 0;
                        foreach ($sem as $sem) { 
                            $period[$x] = $sem->wert;
                            $x++;
                        }
                        $zahl = $x-1;
                        while ($zahl >= 0) {
							if ($period[$zahl] == $daten[0][10]) {
								$current = 'selected="selected"' ;
							}
							else {
								$current = '' ;
							}
                            echo '<option value="' . $period[$zahl] . '" ' . $current . '>' . $period[$zahl] . '</option>';
                            $zahl--;
                        }
                        ?> 
                    </select>            </td>
                <th><label for="sichtbar"><?php _e('Visibility','teachpress'); ?></label></th>
                <td><select name="sichtbar" id="sichtbar">
                  <?php
				  	if ($daten[0][14] == 1) {
						echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
                  		echo '<option value="0">' . __('no','teachpress') . '</option>';
					}
					else {
                    	echo '<option value="1">' . __('yes','teachpress') . '</option>';
                  		echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
					}?>
                </select></td>
              </tr>
              <tr>
                <th><label for="lvs_ID"><?php _e('ID','teachpress'); ?></label></th>
                <td><input name="lvs_ID" type="text" id="parent" size="10" readonly="true" value="<?php echo $daten[0][0]; ?>"/></td>
                <th><label for="par"><?php _e('Parent','teachpress'); ?></label></th>
                <td colspan="3">
                  <select name="par" id="par">
                    <option value="0">- <?php _e('none','teachpress'); ?> -</option>
                    <option value="0">------</option>
                     <?php 	
                    for ($i = 0; $i < $x; $i++) {
                        $zahl = 0;
                        for ($j = 0; $j < $counter3; $j++) {
                            if ($period[($x - 1)-$i] == $par[$j][2] ) {
								if ($par[$j][0] == $daten[0][13]) {
									$current = 'selected="selected"' ;
								}
								else {
									$current = '' ;
								}
                                echo '<option value="' . $par[$j][0] . '" ' . $current . '>' . $par[$j][0] . ' - ' . $par[$j][1] . ' ' . $par[$j][2] . '</option>';
                                $zahl++;
                            } 
                        } 
                        if ($zahl != 0) {
                            echo '<option>------</option>';
                        } 
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
            <th><label for="name"><?php _e('Course name','teachpress'); ?></label></th>
            <td><input name="name" type="text" id="name" value="<?php echo $daten[0][1]; ?>" size="40"/></td>
           </tr>
          <tr>
            <th><label for="dozent"><?php _e('Lecturer','teachpress'); ?></label></th>
            <td><input name="dozent" id="dozent" type="text" value="<?php echo $daten[0][4]; ?>" size="40"/></td>
          </tr>
          <tr>
            <th><label for="termin"><?php _e('Date','teachpress'); ?></label></th>
            <td><input name="termin" id="termin" type="text" value="<?php echo $daten[0][5]; ?>" size="40"/></td>
          </tr>
          <tr>
            <th><label for="raum"><?php _e('Room','teachpress'); ?></label></th>
            <td><input name="raum" id="raum" type="text" size="40" value="<?php echo $daten[0][3]; ?>"/></td>
          </tr>
          <tr>
            <th><label for="bemerkungen"><?php _e('Comment or Description','teachpress'); ?></label></th>
            <td><textarea name="bemerkungen" id="bemerkungen" cols="70" rows="2"/><?php echo $daten[0][11]; ?></textarea></td>
          </tr>
          <tr>
            <th><label for="rel_page"><?php _e('Related Page','teachpress'); ?></label></th>
            <td><select name="rel_page" id="rel_page">
                <?php teachpress_wp_pages("menu_order","ASC",$daten[0][12],0,0); ?>
                </select>
            </td>
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
            <th><label for="plaetze"><?php _e('Number of places','teachpress'); ?></label></th>
            <td><input name="plaetze" id="plaetze" type="text" size="10" value="<?php echo $daten[0][6]; ?>"/></td>
            <th><label for="fplaetze"><?php _e('free places','teachpress'); ?></label></th>
            <td><input name="fplaetze" id="fplaetze" type="text" size="10" value="<?php echo $daten[0][7]; ?>"/></td>
            <td colspan="2">&nbsp;</td>
           </tr>
          <tr>
            <th><label for="startein"><?php _e('Start','teachpress'); ?></label></th>
            <td><input name="startein" id="startein" type="text" size="10" value="<?php echo $daten[0][8]; ?>"/></td>
            <th><label for="endein"><?php _e('End','teachpress'); ?></label></th>
            <td><input name="endein" id="endein" type="text" size="10"  value="<?php echo $daten[0][9]; ?>"/></td>
            <th><label for="warteliste"><?php _e('Waiting list','teachpress'); ?></label></th>
            <td><select name="warteliste" id="warteliste">
            <?php
			if ($daten[0][15] == 1) {
				echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
				echo '<option value="0">' . __('no','teachpress') . '</option>';
			}
			else {
				echo '<option value="1">' . __('yes','teachpress') . '</option>';
				echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
			}?>
            </td>
          </tr>
         </thead> 
        </table>
   		<p><input name="speichern" type="submit" value="<?php _e('save','teachpress'); ?>" id="teachpress_einzel_change" class="teachpress_button"/> <a onclick="teachpress_showhide('name_anzeigen')" style="cursor:pointer;"><?php _e('cancel','teachpress'); ?></a></p>
	</fieldset>
	</div>
    <div id="einschreibungen" style="padding:5px;">
    <div style="min-width:780px; width:100%; max-width:1100px;">
    <div style="width:24%; float:right; padding-left:1%; padding-bottom:1%;">
    <table border="1" cellspacing="0" cellpadding="0" class="widefat" id="teachpress_edit">
        <thead>
          <tr>
          	<th colspan="4"><?php _e('Meta Information','teachpress'); ?></th>
          </tr>
          <tr>  
            <td><strong><?php _e('ID','teachpress'); ?></strong></td>
            <td><?php echo $daten[0][0]; ?></td>   
            <td><strong><?php _e('Parent-ID','teachpress'); ?></strong></td>
            <td><?php echo $daten[0][13]; ?></td>
          </tr>  
          <tr>  
            <td><strong><?php _e('Visibility','teachpress'); ?></strong></td>
            <td colspan="3"><?php if ($daten[0][14] == 1) {_e('yes','teachpress');} else {_e('no','teachpress');} ?></td>
          </tr>
          <tr>
          	<th colspan="4"><?php _e('Enrollments','teachpress'); ?></th>
          </tr>
          <tr>
            <td colspan="2"><strong><?php _e('Start','teachpress'); ?></strong></td>
            <td colspan="2"><?php echo $daten[0][8]; ?></td>
          </tr>  
          <tr>  
            <td colspan="2"><strong><?php _e('End','teachpress'); ?></strong></td>
            <td colspan="2"><?php echo $daten[0][9]; ?></td>
          </tr>
          <tr>
            <td><strong><?php _e('Places','teachpress'); ?></strong></th>
            <td><?php echo $daten[0][6]; ?></td>  
            <td><strong><?php _e('free places','teachpress'); ?></strong></td>
            <td><?php echo $daten[0][7]; ?></td>
          </thead>
        </table>
     </div>
     <div style="width:75%; float:left;">
      <table border="1" cellspacing="0" cellpadding="0" class="widefat">
      	<thead>
            <tr>
                <th width="150px"><?php _e('Type','teachpress'); ?></th>
                <td><?php echo $daten[0][2]; ?></td>
            </tr>
        	<tr>
            	<th><?php _e('Lecturer','teachpress'); ?></th>
                <td colspan="3"><?php echo $daten[0][4]; ?></td>
            </tr>  
            <tr>
                <th><?php _e('Date','teachpress'); ?></th>
                <td colspan="3"><?php echo $daten[0][5]; ?></td>
            </tr>
            <tr>
            	<th><?php _e('Room','teachpress'); ?></th>
                <td colspan="3"><?php echo $daten[0][3]; ?></td>
            </tr>
              <tr>
                <th><?php _e('Comment','teachpress'); ?></th>
                <td colspan="3"><?php echo $daten[0][11]; ?></td>
              </tr>
              <tr>
                <th><?php _e('Related Page','teachpress'); ?></th>
                <td colspan="3"><?php if ( $daten[0][12] != 0) {echo get_permalink( $daten[0][12] ); } else { _e('none','teachpress'); } ?></td>
              </tr>
              </thead>
            </table>
        </div>
        <div style="min-width:780px; width:100%; max-width:1100px;">
        <table class="widefat">
         <thead>
          <tr>
            <th>&nbsp;</th>
            <?php
			$field1 = tp_get_option('regnum');
			if ($field1 == '1') {
            	echo '<th>' .  __('Registr.-Number','teachpress') . '</th>';
            }
            ?>
            <th><?php _e('Last name','teachpress'); ?></th>
            <th><?php _e('First name','teachpress'); ?></th>
            <?php
			$field2 = tp_get_option('studies');
			if ($field2 == '1') {
            	echo '<th>' .  __('Course of studies','teachpress') . '</th>';
			}	
            ?>
            <th><?php _e('User account','teachpress'); ?></th>
            <th><?php _e('E-Mail','teachpress'); ?></th>
            <th><?php _e('Registered at','teachpress'); ?></th>
          </tr>
         </thead>  
         <tbody> 
		<?php
		// Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
		for($i=0; $i<$counter2; $i++) {
			if ($daten2[$i][8]== 0 ) {
				echo '<tr>';
				echo '<th class="check-column"><input name="checkbox[]" type="checkbox" value="' . $daten2[$i][7] . '"/></th>';
                if ($field1 == '1') {
            		echo '<td>' . $daten2[$i][0] . '</td>';
            	}
				echo '<td>' . $daten2[$i][2] . '</td>';
				echo '<td>' . $daten2[$i][1] . '</td>';
				if ($field1 == '1') {
            		echo '<td>' . $daten2[$i][3] . '</td>';
            	}
				echo '<td>' . $daten2[$i][4] . '</td>';
				echo '<td><a href="mailto:' . $daten2[$i][5] . '" title="' . __('send E-Mail','teachpress') . '">' . $daten2[$i][5] . '</a></td>';
				echo '<td>' . $daten2[$i][6] . '</td>';
			  	echo '</tr>';
			}
	    } ?>
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
    <td><?php if ($test != 0) { ?><input name="aufnehmen" type="submit" value="+ <?php _e('ingest','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/><?php } ?></td>
    <td><input name="loeschen" type="submit" value="<?php _e('delete enrollment','teachpress'); ?>" id="teachpress_suche_delete" class="teachpress_button"/></td>
  </tr>
</table>
</div>
</form>
<script type="text/javascript" charset="utf-8">
	$(function() {
		$('#startein').datepick({showOtherMonths: true, firstDay: 1, 
		renderer: $.extend({}, $.datepick.weekOfYearRenderer), 
		onShow: $.datepick.showStatus, showTrigger: '#calImg',
		dateFormat: 'yyyy-mm-dd', yearRange: '2008:c+5'}); 
		
		$('#endein').datepick({showOtherMonths: true, firstDay: 1, 
		renderer: $.extend({}, $.datepick.weekOfYearRenderer), 
		onShow: $.datepick.showStatus, showTrigger: '#calImg',
		dateFormat: 'yyyy-mm-dd', yearRange: '2008:c+5'}); 
		});
</script>
</div>
<?php } ?>