<?php
/*
 * Zum Erstellen neuer Lehrveranstaltungen
 *
*/
?>

<?php 
/* Sicherheitsabfrage ob User eingeloggt ist, um unbefugte Zugriffe von außen zu vermeiden
 * Nur wenn der User eingeloggt ist, wird das Script ausgeführt
*/ 
if ( is_user_logged_in() ) { 
global $teachpress_einstellungen;
global $teachpress_ver;
 // Formular-Einträge aus dem Post Array holen
$erstellen = $_POST[erstellen]; 
$veranstaltungstyp = htmlentities(utf8_decode($_POST[veranstaltungstyp])) ;
$lvname = htmlentities(utf8_decode($_POST[lvname])) ;
$raum = htmlentities(utf8_decode($_POST[raum])) ;
$dozent = htmlentities(utf8_decode($_POST[dozent]));
$termin = htmlentities(utf8_decode($_POST[termin]));
$plaetze = htmlentities(utf8_decode($_POST[platz])); 
$fplaetze = $plaetze;
$startein = htmlentities(utf8_decode($_POST[startein])); 
$endein = htmlentities(utf8_decode($_POST[endein])); 
$semester = htmlentities(utf8_decode($_POST[semester]));
$bemerkungen = htmlentities(utf8_decode($_POST[bemerkungen]));
$url = htmlentities(utf8_decode($_POST[url]));
$parent = htmlentities(utf8_decode($_POST[parent2]));
$sichtbar = htmlentities(utf8_decode($_POST[sichtbar]));
$warteliste = htmlentities(utf8_decode($_POST[warteliste]));
//Übergabe der Variablen
if (isset($erstellen)) {
	add_lvs_in_database($lvname, $veranstaltungstyp, $raum, $dozent, $termin, $plaetze, $fplaetze, $startein, $endein, $semester,  $bemerkungen, $url, $parent, $sichtbar, $warteliste);
	$message = __('Veranstaltung erstellt','teachpress');
	$site = 'admin.php?page=teachpress/addlvs.php';
    tp_get_message($message, $site);
}
?>
<div class="wrap" style="width:850px;">
    <h2><?php _e('Neue Lehrveranstaltung erstellen','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Hilfe','teachpress'); ?></a></small></h2>
    <div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Hilfe','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Parent','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Hier kann die LVS einer übergeordneten Veranstaltung (muss vorher erstellt werden) zugeordnet werden.  Wenn Sie jetzt das Formular für die Erstellung einer untergeordneten LVS nutzen, achten Sie darauf, dass sie unter Parent die Übergeordnete LVS auswählen. Benennen Sie die Übung möglichst nach der Vorlesung, also beispielsweise: &quot;Einführung Recht Übung 1&quot;. Das dient der Übersicht in den Administrationsmenüs. Für das öffentliche Einschreibeformular wird nur der Veranstaltungstyp verwendet, die Nummer wird hochgezählt, daher sollten untergeordnete LVS des gleichen Typs immer in Reihenfolge erstellt werden.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Mehr als ein Termin','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Wenn es mehr als einen Termin für eine LVS geben soll, dann kann man alle Termine mit Raum in das Feld Termin schreiben (max. 100 Zeichen) oder die Werte auf die Felder Termin und Raum aufteilen','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Sichtbar','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Gibt an, ob für die LVS eine Einschreibung stattfinden soll und ob die LVS bei den Einschreibungen angezeigt werden soll. Falls es untergeordnete LVS mit Einschreibungen geben soll dann unbedingt auf &quot;Ja&quot; stellen','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('URL','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('&Uuml;ber die URL wird die Lehrveranstaltung mit einer statischen WordPress-Seite  oder einer externen Seite verlinkt, auf der man weitere Angaben zur Veranstaltung geben kann.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('F&uuml;r Kursinfos:','teachpress'); ?> <strong><?php _e('[tpdate id="x"] (x = ID des Kurses)','teachpress'); ?></strong></p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('schlie&szlig;en','teachpress'); ?></a></strong></p>
    </div>
  <form id="addlvs" name="form1" method="post" action="<?php echo $PHP_SELF ?>">
        <table class="widefat">
         <thead>
          <tr>
            <th><?php _e('Veranstaltungstyp','teachpress'); ?></th>
            <td>
              <select name="veranstaltungstyp" id="veranstaltungstyp">
              <?php 
			    $row = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'veranstaltungstyp' ORDER BY wert";
				$row = tp_results($row);
				foreach ($row as $row) { ?>  
                	<option value="<?php echo $row->wert; ?>"><?php echo $row->wert; ?></option>
                <?php } ?>
            </select>            </td>
            <th><?php _e('Semester','teachpress'); ?></th>
            <td>
              <select name="semester" id="semester">
                    <?php
                    $abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
                    $wert = tp_var($abfrage);
                       ?>
                <option value="<?php echo"$wert" ?>"><?php echo"$wert" ?></option>
                <option>------</option>
                <?php    
				$sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
				$sem = tp_results($sem);
				$x = 0;
				foreach ($sem as $sem) { 
					$period[$x] = $sem->wert;
					$x++;?> 
                   	<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
                <?php } ?> 
            </select></td>
          </tr>
              <tr>
                <th><?php _e('Parent','teachpress'); ?></th>
<td>
                    <select name="parent2" id="parent2">
                      <option value="0"><?php _e('keine','teachpress'); ?></option>
                      <?php
                        $abfrage = "SELECT veranstaltungs_id, name, semester FROM " . $teachpress_ver . " WHERE parent='0' ORDER BY semester DESC, name";
                        $row = tp_results($abfrage);
						$z = 0;
                        foreach($row as $row){
							$par[$z][0] = $row->veranstaltungs_id;
							$par[$z][1] = $row->name;
							$par[$z][2] = $row->semester;
							$z++;
						}
						for ($i = 0; $i < $x; $i++) {
							for ($j = 0; $j < $z; $j++) {
								if ($period[($x - 1)-$i] == $par[$j][2] ) {
									echo '<option value="' . $par[$j][0] . '">' . $par[$j][1] . ' ' . $period[$x-$i] . '</option>';
						        } 
						    } 
						    echo '<option>----</option>';  
					    }?>
                    </select>
                </td>
                <th><?php _e('Sichtbar','teachpress'); ?></th>
                <td><select name="sichtbar" id="sichtbar">
                  <option value="1"><?php _e('ja','teachpress'); ?></option>
                  <option value="0"><?php _e('nein','teachpress'); ?></option>
                </select>            </td>
              </tr>
        </thead>      
    </table>     
       <p style="font-size:2px;">&nbsp;</p>
        <table class="widefat">
         <thead>
              <tr>
                <th><?php _e('Veranstaltungsname','teachpress'); ?></th>
                <td><input name="lvname" type="text" id="lvname" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Dozent','teachpress'); ?></th>
                <td><input name="dozent" type="text" id="dozent" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Termin','teachpress'); ?></th>
                <td><input name="termin" type="text" id="termin" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Raum','teachpress'); ?></th>
                <td><input name="raum" type="text" id="raum" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Anzahl Pl&auml;tze','teachpress'); ?></th>
                <td><input name="platz" type="text" id="platz" size="6" /></td>
              </tr>
              <tr>
                <th><?php _e('Bemerkungen','teachpress'); ?></th>
                <td><input name="bemerkungen" type="text" id="bemerkungen" size="75" /></td>
              </tr>
              <tr>
                <th><?php _e('URL','teachpress'); ?></th>
                <td><input name="url" type="text" id="url" size="75" /></td>
              </tr>
       </thead>       
      </table>
    <h4 style="margin-bottom:7px; margin-top:7px;"><?php _e('Einschreibungen','teachpress'); ?></h4>
        <table class="widefat">
         <thead>
          <tr>
            <td colspan="6" style="font-size:11px; color:#FF0000;"><strong><?php _e('Format für das Datum','teachpress'); ?> <?php _e('JJJJ-MM-TT','teachpress'); ?></strong></td>
          </tr>
          <tr>
            <th><?php _e('Beginn','teachpress'); ?></th>
            <td><input name="startein" type="text" id="startein" value="JJJJ-MM-TT" size="15"/><input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/></td>
            <th><?php _e('Ende','teachpress'); ?></th>
            <td><input name="endein" type="text" id="endein" value="JJJJ-MM-TT" size="15"/><input type="submit" name="calendar2" id="calendar2" value="..." class="teachpress_button"/></td>
            <th><?php _e('Warteliste','teachpress'); ?></th>
            <td><select name="warteliste" id="warteliste">
              <option value="0"><?php _e('nein','teachpress'); ?></option>
              <option value="1"><?php _e('ja','teachpress'); ?></option>
            </select>
            </td>
          </tr>
      </thead>    
    </table>
      <p>
        <input name="erstellen" type="submit" id="teachpress_erstellen" onclick="teachpress_validateForm('lvname','','R','dozent','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('Erstellen','teachpress'); ?>" class="teachpress_button">
        <input type="reset" name="Reset" value="<?php _e('Reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button">
        <input type="hidden" name="gesendet" value="1">
    </p>
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