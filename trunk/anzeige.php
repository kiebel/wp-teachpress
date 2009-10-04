<?php 
/* Anzeige des Einschreibesystems im Frontent
 * Eingangsparameter: keine
*/
?>
<div>
<?php 
// Wordpress Variablen holen
global $user_ID;
global $user_email;
global $user_login;
get_currentuserinfo();
// teachpress Variablen
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_einstellungen; 
global $teachpress_kursbelegung;
// Formular-Einträge aus dem Post Array holen
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

// Prüfen welcher Button genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
if ( isset($aendern)) {
	change_student($wp_id, $vorname2, $nachname2, $studiengang2, $gebdat2, $email2, $fachsemester2, $matrikel2);
}
if ( isset($austragen)) {
	delete_einschreibung_student($checkbox2);
}
if ( isset($einschreiben)) {
	add_einschreibung($checkbox, $wp_id);
}			
if ( isset($aendern) || isset($austragen) || isset($einschreiben) ) {
	?>
		<div>
	  <form method="POST" action="<?php echo $PHP_SELF ?>" id="teachpress_einstellungen_weiter">
          <p style="background-color:#FFFFCC; border:1px solid silver; padding:5px; width:80%;">
          <input type="submit" name="Submit" value="<?php _e('weiter','teachpress'); ?>" id="teachpress_einstellungen_weiter">
          </p>
      </form>
	</div>
    <?php
}
// Aktuelles Semester abfragen
	$sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
	$sem = tp_var($sem);
?>   
<h3 style="color:#005A46;"><?php _e('Einschreibungen f&uuml;r das','teachpress'); ?> <?php echo"$sem" ;?></h3>
<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
<?php

// Prüfen ob Nuter in Wordpress angemeldet ist, wenn ja dann Ausgabe, wenn nein, dann Meldung an den User
if( $user_ID != '') {
	$auswahl = "Select wp_id FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
	$auswahl = tp_var($auswahl);
	// Prüfen ob die Auswahl leer ist (Wenn ja, dann Formular ausgeben, da User noch nicht in teachpress eingetragen ist)
	if($auswahl == '' ) {
		include_once('anmeldung.php');
	}	
	// Wenn der User eingetragen ist, werden seine Daten ausgelesen
	// zudem erhält er mit Buttons ausklappbaren Listen seiner bisherigen Einschreibungen und zum ändern seiner Nutzerdaten
	else {
		$auswahl = "Select * FROM " . $teachpress_stud . " WHERE wp_id = '$user_ID'";
		$auswahl = tp_results($auswahl);
		foreach ($auswahl as $row) {
		   ?>
            <div id="nutzer" style=" text-align:left; padding:5px;">
            <p><strong><?php _e('Hallo','teachpress'); ?>, <?php echo"$row->vorname" ?> <?php echo"$row->nachname" ?></strong></p>
            <table cellpadding="5">
              <tr>
                <td><a onclick="teachpress_showhide('einschreibungen_anzeigen')" name="daten" id="einschreibungen_link" style="cursor:pointer;"> <?php _e('Deine Einschreibungen anzeigen','teachpress'); ?></a> </td>
                <td><a onclick="teachpress_showhide('daten_aendern')" name="daten2" id="daten_link" style="cursor:pointer;"><?php _e('Deine Nutzerdaten &auml;ndern','teachpress'); ?></a></td>
              </tr>
            </table>
			<div id="daten_aendern" style="padding-left:20px; padding-top:5px; padding-bottom:5px; padding-right:20px; margin:5px; display:none;">
            <fieldset style="padding:5px; border:1px solid silver;">
            <legend><?php _e('Deine Daten','teachpress'); ?></legend>
                <table border="0" cellpadding="0" cellspacing="5">
                  <tr>
                    <td><?php _e('Matrikel','teachpress'); ?></td>
                    <td><input type="text" name="matrikel2" id="matrikel2" value="<?php echo"$row->matrikel" ?>"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('Vorname','teachpress'); ?></td>
                    <td><input name="vorname2" type="text" id="vorname2" value="<?php echo"$row->vorname" ?>" size="30"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('Nachname','teachpress'); ?></td>
                    <td><input name="nachname2" type="text" id="nachname2" value="<?php echo"$row->nachname" ?>" size="30"/></td>
                  </tr>
                  <tr>
                    <td><?php _e('Studiengang','teachpress'); ?></td>
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
                    <td><?php _e('Fachsemester','teachpress'); ?></td>
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
                    <td><?php _e('Geburtsdatum','teachpress'); ?></td>
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
            <legend><?php _e('Bisherige Einschreibungen','teachpress'); ?></legend>
            <p><strong><?php _e('Eingeschrieben in','teachpress'); ?></strong></p>    
            <table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">
            <tr>
            	<th>&nbsp;</th>
                <th><?php _e('Name','teachpress'); ?></th>
                <th><?php _e('Typ','teachpress'); ?></th>
                <th><?php _e('Termin','teachpress'); ?></th>
                <th><?php _e('Raum','teachpress'); ?></th>
                <th><?php _e('Semester','teachpress'); ?></th>
            </tr>
            <?php
				// Alle Veranstaltungen wo Student eingetragen ist
				$row1 = "SELECT veranstaltungs_id, belegungs_id FROM " . $teachpress_kursbelegung . " WHERE wp_id = '$row->wp_id' AND warteliste = '0' ORDER BY belegungs_id DESC";
				$row1 = tp_results($row1);
				foreach($row1 as $row1) {
					$row2 = "SELECT veranstaltungs_id, name, vtyp, raum, termin, semester FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id' ORDER BY name, semester DESC";
					$row2 = tp_results($row2);
					foreach($row2 as $row2) {
					?>
                        <tr>
                            <td><input name="checkbox2[]" type="checkbox" value="<?php echo"$row1->belegungs_id" ?>" title="<?php echo "$row2->name" ?>" id="ver_<?php echo"$row1->belegungs_id" ?>"/></td>
                            <td><label for="ver_<?php echo"$row1->belegungs_id" ?>" style="line-height:normal;"><?php echo "$row2->name" ?></label></td>
                            <td><?php echo "$row2->vtyp" ?></td>
                            <td><?php echo "$row2->termin" ?></td>
                            <td><?php echo "$row2->raum" ?></td> 
                            <td><?php echo "$row2->semester" ?></td>
                        </tr>
                       
                    <?php        
					}
			   }
			?>
        </table>
        <?php
		// Alle Veranstaltungen wo Student auf Warteliste steht
		$row = "SELECT veranstaltungs_id, belegungs_id FROM " . $teachpress_kursbelegung . " WHERE wp_id = '$row->wp_id' AND warteliste = '1'";
		$test = tp_query($row);
		if ($test != 0) {
		?>
        <p><strong>Warteliste<?php _e('','teachpress'); ?></strong></p>
        <table border="1" cellpadding="5" cellspacing="0" class="teachpress_table">
            <tr>
            	<th>&nbsp;</th>
                <th><?php _e('Name','teachpress'); ?></th>
                <th><?php _e('Typ','teachpress'); ?></th>
                <th><?php _e('Termin','teachpress'); ?></th>
                <th><?php _e('Raum','teachpress'); ?></th>
                <th><?php _e('Semester','teachpress'); ?></th>
            </tr>
            <?php
				$row = tp_results($row);
				foreach($row as $row) {
					// Daten zu diesen Veranstaltungen, wo Stundent auf Warteliste steht
					$row2 = "SELECT veranstaltungs_id, name, vtyp, raum, termin, semester FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row1->veranstaltungs_id' ORDER BY name, semester DESC";
					$row2 = tp_results($row2);
					foreach($row2 as $row2) { ?>
                        <tr>
                            <td><input name="checkbox2[]" type="checkbox" value="<?php echo"$row->belegungs_id" ?>" title="<?php echo "$row2->name" ?>" id="war_<?php echo"$row->belegungs_id" ?>"/></td>
                            <td><label for="war_<?php echo"$row->belegungs_id" ?>" style="line-height:normal;"><?php echo "$row2->name" ?></label></td>
                            <td><?php echo "$row2->vtyp" ?></td>
                            <td><?php echo "$row2->termin" ?></td>
                            <td><?php echo "$row2->raum" ?></td> 
                            <td><?php echo "$row2->semester" ?></td>
                        </tr>  
                    <?php        
					}
				 }
				?>
        </table>
        <?php } ?>
        <p><input name="austragen" type="submit" value="<?php _e('austragen','teachpress'); ?>" id="austragen" /></p>
            </fieldset>
            </div>
            </div>
			<?php
		}
	// Abfrage der zur Einschreibung zur Verfügung stehenden Lehrveranstaltungen des aktuellen Semesters
	$row = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' AND sichtbar = '1' ORDER BY vtyp DESC, name";
	$row = tp_results($row);
	foreach($row as $row) {
		// Datum wird auf den deutschen Standart umgestellt, altes MySQL-Datum wird auf andere Variablen umgelagert
		$datum1 = $row->startein;
		$datum2 = $row->endein;
		$row->startein = date_mysql2german($row->startein);
		$row->endein = date_mysql2german($row->endein);
	   ?>  
       <div style="margin:10px; padding:5px;">
       <div class="untertitel" style="font-size:15px;"><a href="<?php echo"$row->url" ?>"><?php echo"$row->name" ?></a></div>
     <table width="100%" border="0" cellpadding="1" cellspacing="0">
       <tr>
         <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;"><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?><input type="checkbox" name="checkbox[]" value="<?php echo"$row->veranstaltungs_id" ?>" title="<?php echo"$row->name" ?> <?php _e('ausw&auml;hlen','teachpress'); ?>" id="checkbox_<?php echo"$row->veranstaltungs_id" ?>"/> <?php } ?></td>
         <td colspan="2">&nbsp;</td>
         <td align="center" width="270"><strong><?php _e('Termin(e)','teachpress'); ?></strong></td>
         <td align="center"><?php if ($datum1 != '0000-00-00') { ?><strong><?php _e('freie Pl&auml;tze','teachpress'); ?></strong><?php } ?></td>
       </tr>
       <tr>
         <td width="20%" style="font-weight:bold;"><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?><label for="checkbox_<?php echo"$row->veranstaltungs_id" ?>" style="line-height:normal;"><?php } ?><?php echo"$row->vtyp" ?><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?></label><?php } ?></td>
         <td width="20%"><?php echo"$row->dozent" ?></td>
         <td align="center"><?php echo"$row->termin" ?> <?php echo"$row->raum" ?></td>
         <td align="center"><?php if ($datum1 != '0000-00-00') { ?><?php echo"$row->fplaetze" ?> von <?php echo"$row->plaetze" ?><?php } ?></td>
       </tr>
       <tr>
         <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row->warteliste == 1 && $row->fplaetze == 0) {?><?php _e('Wartelisteneintrag m&ouml;glich','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
         <td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist"><?php if ($datum1 != '0000-00-00') { ?><?php _e('Einschreibefrist','teachpress'); ?>: <?php echo"$row->startein" ?> - <?php echo"$row->endein" ?><?php }?></td>
       </tr>
    
     <?php
	 $zahl = 1;
	 // Abfrage um Child's einer LVS zu finden
	 $row2 = "Select * FROM " . $teachpress_ver . " WHERE parent = $row->veranstaltungs_id AND sichtbar = '1' ORDER BY veranstaltungs_id";
	 $row2 = tp_results($row2);
	 foreach ($row2 as $row2) {
	 	// Datum wird auf den deutschen Standart umgestellt, altes MySQL-Datum wird auf andere Variablen umgelagert
		$datum3 = $row2->startein;
		$datum4 = $row2->endein;
	 	$row2->startein = date_mysql2german($row2->startein);
		$row2->endein = date_mysql2german($row2->endein);
		if ($row2->vtyp == $row->vtyp) {
			$merke = $zahl;
			$zahl = "";
		}
	 	?>
               <tr>
                 <td rowspan="3" width="25" style="border-bottom:1px solid silver; border-collapse: collapse;"><?php if ($datum3 != '0000-00-00' && date("Y-m-d") >= $datum3 && date("Y-m-d") <= $datum4) { ?><input type="checkbox" name="checkbox[]" value="<?php echo"$row2->veranstaltungs_id" ?>" title="<?php echo"$row-2>name" ?> ausw&auml;hlen" id="checkbox_<?php echo"$row->veranstaltungs_id" ?>"/><?php }?></td>
                 <td colspan="2">&nbsp;</td>
                 <td align="center" width="270"><strong><?php _e('Termin(e)','teachpress'); ?></strong></td>
                 <td align="center"><strong><?php _e('freie Pl&auml;tze','teachpress'); ?></strong></td>
               </tr>
               <tr>
                 <td width="20%" style="font-weight:bold;"><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?><label for="checkbox_<?php echo"$row->veranstaltungs_id" ?>" style="line-height:normal;"><?php } ?><?php echo"$row2->vtyp" ?> <?php echo "$zahl" ?><?php if ($datum1 != '0000-00-00' && date("Y-m-d") >= $datum1 && date("Y-m-d") <= $datum2) { ?></label><?php } ?></td>
                 <td width="20%"><?php echo"$row2->dozent" ?></td>
                 <td align="center"><?php echo"$row2->termin" ?> <?php echo"$row2->raum" ?></td>
                 <td align="center"><?php echo"$row2->fplaetze" ?> von <?php echo"$row2->plaetze" ?></td>
               </tr>
               <tr>
                 <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row->warteliste == 1 && $row->fplaetze == 0) {?><?php _e('Wartelisteneintrag m&ouml;glich','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
                 <td align="center" class="einschreibefrist" style="border-bottom:1px solid silver; border-collapse: collapse;"><?php _e('Einschreibefrist','teachpress'); ?>: <?php echo"$row2->startein" ?> - <?php echo"$row2->endein" ?></td>
               </tr> 
        <?php 
			if ($vtyp_test == $row->vtyp) {
				$zahl = $merke;
			}
			else {
				$zahl++;
			}
			// Ende der Schleife um Child's zur LVS zu finden
		} 
		?>
         </table>
    </div> 
  <?php      
   }
	?>
    <input name="einschreiben" type="submit" value="<?php _e('Einschreiben','teachpress'); ?>" />
    <?php
		}
	}
	// Ende der 1. If-Bedingung, also wenn der user angeloggt ist
	// Folgend anweisungen, wenn der User nicht eingeloggt ist
	else {
	?>
	<div id="del" style="background-color:#FF9977; padding:7px; border:1px solid red;"><?php _e('Du musst dich einloggen, damit du dich einschreiben kannst.','teachpress'); ?></div>
    <?php    
	$row4 = "SELECT * FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' AND sichtbar = '1' ORDER BY vtyp DESC, name";
	$row4 = tp_results($row4);
	foreach($row4 as $row4) {
		// Datum wird auf den deutschen Standart umgestellt, altes MySQL-Datum wird auf andere Variablen umgelagert
		$datum5 = $row4->startein;
		$datum6 = $row4->endein;
		$row4->startein = date_mysql2german($row4->startein);
		$row4->endein = date_mysql2german($row4->endein);
	   ?>  
    <div style="margin:10px; padding:5px;">
    <div class="untertitel" style="font-size:15px;"><a href="<?php echo"$row4->url" ?>"><?php echo"$row4->name" ?></a></div>
     <table width="100%" border="0" cellpadding="1" cellspacing="0">
       <tr>
         <td colspan="2">&nbsp;</td>
         <td align="center" width="270"><strong><?php _e('Termin(e)','teachpress'); ?></strong></td>
         <td align="center"><?php if ($datum5 != '0000-00-00') { ?><strong><?php _e('freie Pl&auml;tze','teachpress'); ?></strong><?php } ?></td>
       </tr>
       <tr>
         <td width="20%" style="font-weight:bold;"><?php echo"$row4->vtyp" ?></td>
         <td width="20%"><?php echo"$row4->dozent" ?></td>
         <td align="center"><?php echo"$row4->termin" ?> <?php echo"$row4->raum" ?></td>
         <td align="center"><?php if ($datum5 != '0000-00-00') { ?><?php echo"$row4->fplaetze" ?> von<?php _e('','teachpress'); ?> <?php echo"$row4->plaetze" ?><?php } ?></td>
       </tr>
       <tr>
         <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row4->warteliste == 1 && $row4->fplaetze == 0) {?><?php _e('Wartelisteneintrag m&ouml;glich','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
         <td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist"><?php if ($datum5 != '0000-00-00') { ?><?php _e('Einschreibefrist','teachpress'); ?>: <?php echo"$row4->startein" ?> - <?php echo"$row4->endein" ?><?php }?></td>
       </tr>
    
     <?php
	 // Abfrage (mit While-Schelife) um Child's einer LVS zu finden
	 $zahl2 = 1;
	 $row5 = "Select * FROM " . $teachpress_ver . " WHERE parent = $row4->veranstaltungs_id AND sichtbar = '1' ORDER BY veranstaltungs_id ";
	 $row5 = tp_results($row5);
	 foreach ($row5 as $row5) {
	 	// Datum wird auf den deutschen Standart umgestellt, altes MySQL-Datum wird auf andere Variablen umgelagert
		$datum7 = $row5->startein;
		$datum8 = $row5->endein;
	 	$row5->startein = date_mysql2german($row5->startein);
		$row5->endein = date_mysql2german($row5->endein);
		if ($row5->vtyp == $row4->vtyp) {
			$merke = $zahl;
			$zahl2 = "";
		}
	 	?>
               <tr>
                 <td colspan="2">&nbsp;</td>
                 <td align="center" width="270"><strong><?php _e('Termin(e)','teachpress'); ?></strong></td>
                 <td align="center"><strong><?php _e('freie Pl&auml;tze','teachpress'); ?></strong></td>
               </tr>
               <tr>
                 <td style="font-weight:bold;"><?php echo"$row5->vtyp" ?> <?php echo "$zahl2" ?></td>
                 <td style="color:#FF0000;"><?php echo"$row5->dozent" ?></td>
                 <td width="270"><?php echo"$row5->termin" ?> <?php echo"$row5->raum" ?></td>
                 <td align="center"><?php echo"$row5->fplaetze" ?> <?php _e('von','teachpress'); ?> <?php echo"$row5->plaetze" ?></td>
               </tr>
               <tr>
                 <td colspan="3" style="border-bottom:1px solid silver; border-collapse: collapse;" class="warteliste"><?php if ($row->warteliste == 1 && $row->fplaetze == 0) {?><?php _e('Wartelisteneintrag m&ouml;glich','teachpress'); ?><?php } else { ?>&nbsp;<?php }?></td>
                 <td style="border-bottom:1px solid silver; border-collapse: collapse;" align="center" class="einschreibefrist"><?php _e('Einschreibefrist','teachpress'); ?>: <?php echo"$row5->startein" ?> - <?php echo"$row5->endein" ?></td>
               </tr> 
        <?php 
			if ($vtyp_test == $row4->vtyp) {
				$zahl2 = $merke;
			}
			else {
				$zahl2++;
			}
			// Ende der Schleife um Child's zur LVS zu finden
		} 
		?>
         </table>
    </div> 
  <?php      
   }
	}
?>
</form>
<?php $version = get_tp_version(); ?>
<p style="font-size:11px; color:#AAAAAA"><em><strong>teachPress <?php echo $version; ?></strong></em> - <?php _e('Veranstaltungs- und Publikationsmanagement für WordPress','teachpress'); ?></p>
</div>