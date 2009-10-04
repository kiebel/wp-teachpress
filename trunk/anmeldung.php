<?php
/*
 * Formular zum anmelden im Einschreibesystem
 * wird in anzeige.php integriert
 *
*/
?>
<?php 
// Formular-Einträge aus dem Post Array holen
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

// Prüfen welcher Button genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
if ( isset($eintragen) ) {
	add_student($wp_id, $vorname, $nachname, $studiengang, $urzkurz , $gebdat, $email, $fachsemester, $matrikel);
		?>
		<div style="padding-left:40px;">
	  <form method="POST" action="<?php echo $PHP_SELF ?>" id="teachpress_einstellungen_weiter">
          <p style="background-color:#FFFFCC; border:1px solid silver; padding:5px; width:80%;">
          <strong><?php _e('Anmeldung erfolgreich','teachpress'); ?></strong>
          <input type="submit" name="Submit" value="<?php _e('weiter','teachpress'); ?>" id="teachpress_einstellungen_weiter">
          </p>
      </form>
	</div>
    <?php
}
?>
<form name="anzeige" method="post" id="anzeige" action="<?php echo $PHP_SELF ?>">
<div id="eintragen">
<p style="text-align:left; color:#FF0000;"><?php _e('Du musst dich erst noch im System eintragen, damit du das Einschreibesystem nutzen kannst. Diese Eintragung ist einmalig. Du kannst deine Daten sp&auml;ter &auml;ndern.','teachpress'); ?></p>
<fieldset style="border:1px solid silver; padding:5px;">
    <legend><?php _e('Deine Daten','teachpress'); ?></legend>
    <table border="0" cellpadding="0" cellspacing="5" style="text-align:left; padding:5px;">
      <tr>
        <td><?php _e('Matrikel','teachpress'); ?></td>
        <td><input type="text" name="matrikel" id="matrikel" /></td>
      </tr>
      <tr>
        <td><?php _e('Vorname','teachpress'); ?></td>
        <td><input name="vorname" type="text" id="vorname" /></td>
      </tr>
      <tr>
        <td><?php _e('Nachname','teachpress'); ?></td>
        <td><input name="nachname" type="text" id="nachname" /></td>
      </tr>
      <tr>
        <td><?php _e('Studiengang','teachpress'); ?></td>
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
        <td><?php _e('Fachsemester','teachpress'); ?></td>
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
        <td><?php _e('URZ-K&uuml;rzel','teachpress'); ?></td>
        <td style="text-align:left;"><?php echo"$user_login" ?></td>
      </tr>
      <tr>
        <td><?php _e('Geburtsdatum','teachpress'); ?></td>
        <td><input name="gebdat" type="text" size="15" value="JJJJ-MM-TT"/>
          <em><?php _e('Format: JJJJ-MM-TT','teachpress'); ?></em></td>
      </tr>
      <tr>
        <td><?php _e('E-Mail','teachpress'); ?></td>
        <td><?php echo"$user_email" ?></td>
      </tr>
    </table>
</fieldset>

<input name="eintragen" type="submit" id="eintragen" onclick="teachpress_validateForm('matrikel','','RisNum','vorname','','R','nachname','','R');return document.teachpress_returnValue" value="<?php _e('Senden','teachpress'); ?>" />
</div>
</form>