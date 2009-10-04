<?php
/*
 * Anzeige der Einstellungen im Backend
 *
*/
?>

<?php 
/* Sicherheitsabfrage ob User eingeloggt ist, um unbefugte Zugriffe von außen zu vermeiden
 * Nur wenn der User eingeloggt ist, wird das Script ausgeführt
*/ 
if ( is_user_logged_in() ) { 
?> 
<div class="wrap">
    <h2 style="padding-bottom:0px;"><?php _e('teachPress Einstellungen','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Hilfe','teachpress'); ?></a></small></h2> 
    <div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Hilfe','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Aktuelles Semester','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Geben Sie hier das aktuelle Semester an. Durch diesen Wert wird bestimmt welches Semester in den Anzeigen verwendet wird. Unabh&auml;nig von dieser Einstellung k&ouml;nnen Lehrveranstaltungen f&uuml;r andere Semester angelegt werden.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Permalinks','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Geben Sie hier an, ob Sie WordPress mit Permalinks verwenden oder nicht. Wird ben&ouml;tigt, damit die Tag-Clouds im Frontend funktionieren. Wenn sie WordPress ohne Permalinks verwenden funktionieren die Tag-Clouds derzeit nur auf Seiten.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Verwaltung Studieng&auml;nge','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Bestimmt die Auswahlm&ouml;glichkeiten f&uuml;r Studenten bei der Anmeldung im Einschreibesystem','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Verwaltung Veranstaltungstypen','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Bestimmt die Auswahlm&ouml;glichkeiten f&uuml;r Veranstaltungstypen','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Verwaltung Semester','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Erm&ouml;glicht das Hinzuf&uuml;gen neuer Semester.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Beim L&ouml;schen von Veranstaltungstypen, Semestern und Studieng&auml;ngen, werden keine mit diesen Werten in Verbindung stehenden Daten gel&ouml;scht.','teachpress'); ?></p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('schlie&szlig;en','teachpress'); ?></a></strong></p>
        
    </div>
    <?php
	global $teachpress_einstellungen; 
	global $teachpress_stud; 
	global $teachpress_ver;
	// Formularvariablen von einstellungen.php
	$semester = htmlentities(utf8_decode($_GET[semester]));
	$permalink = htmlentities(utf8_decode($_GET[permalink]));
	$einstellungen = $_GET[einstellungen];
	$delete = $_GET[delete];
	$name = htmlentities(utf8_decode($_GET[name]));
	$typ = htmlentities(utf8_decode($_GET[typ]));
	$newsem = htmlentities(utf8_decode($_GET[newsem]));
	$addstud = $_GET[addstud];
	$addtyp = $_GET[addtyp];
	$addsem = $_GET[addsem];
	$site = 'admin.php?page=teachpress/einstellungen.php';
	// Aktionen ausfuehren und Nachrichten ausgeben
	if ($_GET[up] == 1) {
		tp_db_update();
	}
	if ($_GET[ins] == 1) {
		teachpress_install();
	}
	if (isset($einstellungen)) {
		change_einstellungen($semester, $permalink);
		$message = __('Einstellungen aktualisiert','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addstud) && $name != __('Studiengang hinzuf&uuml;gen','teachpress')) {
		add_einstellung($name, 'studiengang');
		$message = __('Studiengang hinzugef&uuml;gt','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addtyp) && $typ != __('Typ hinzuf&uuml;gen','teachpress')) {
		add_einstellung($typ, 'veranstaltungstyp');
		$message = __('Typ hinzugef&uuml;gt','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addsem) && $newsem != __('Semester hinzuf&uuml;gen','teachpress')) {
		add_einstellung($newsem, 'semester');
		$message = __('Semester hinzugef&uuml;gt','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($delete)) {
		delete_einstellung($delete);
	} 
	?>
   
  <div id="einstellungen" style="float:left; width:97%;">
  <form id="form1" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
	<input name="page" type="hidden" value="teachpress/einstellungen.php">
	<div style="min-width:780px; width:100%;">
	<div style="width:48%; float:left; padding-right:2%;">
		 <div>
		  <h4><strong><?php _e('Studieng&auml;nge','teachpress'); ?></strong></h4> 
		  <table class="widefat">
			  <thead>
				  <tr>
					<th>&nbsp;</th>
					<th><?php _e('Name','teachpress'); ?></th>
					<th><?php _e('Anzahl Studenten','teachpress'); ?></th>
				  </tr>
			  </thead>
		  <?php
			$row = "SELECT einstellungs_id, wert FROM " . $teachpress_einstellungen . " WHERE category = 'studiengang'";
			$row = tp_results($row);
			$z = 0;
			$anzahl = "SELECT studiengang FROM " . $teachpress_stud . "";	
			$anzahl = tp_results($anzahl);
			foreach ($anzahl as $anzahl) {
				$a[$z] = $anzahl->studiengang;
				$z++;
			}
			foreach ($row as $row) { ?>
			  <tr>
				<td><a title="Studiengang &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="admin.php?page=teachpress/einstellungen.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
				<td>
				<?php 
				$zahl=0;
				for ($i=0;$i<$z;$i++) {
					if ($a[$i] == $row->wert) {
						$zahl++;
					}
				}
				echo $zahl ;
				?>
				</td>
			  </tr>
			  <?php } ?>
		  </table>
		  <table class="widefat" style="margin-top:10px;">
			  <thead>
				  <tr>
					<td><input name="name" type="text" id="name" size="30" value="<?php _e('Studiengang hinzuf&uuml;gen','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Studiengang hinzuf&uuml;gen','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Studiengang hinzuf&uuml;gen','teachpress'); ?>') this.value='';"/></td>
					<td><input name="addstud" type="submit" class="teachpress_button" value="<?php _e('erstellen','teachpress'); ?>"/></td>
				  </tr>
			  </thead>
		</table>
		</div>
		 <div style="padding-top:10px;">
			<h4><strong><?php _e('Semester','teachpress'); ?></strong></h4>
			<table border="0" cellspacing="0" cellpadding="0" class="widefat">
			 <thead>
			  <tr>
				<th>&nbsp;</th>
				<th><?php _e('Semester','teachpress'); ?></th>
				<th><?php _e('Anzahl Veranstaltungen','teachpress'); ?></th>
			  </tr>
			 <?php    
			   $row = "SELECT einstellungs_id, wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
				$row = tp_results($row);
				$z = 0;
				$anzahl = "SELECT semester FROM " . $teachpress_ver . "";	
				$anzahl = tp_results($anzahl);
				foreach ($anzahl as $anzahl) {
					$a[$z] = $anzahl->semester;
					$z++;
				}
				foreach ($row as $row) { ?> 
			  <tr>
				<td><a title="Studiengang &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="admin.php?page=teachpress/einstellungen.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
				<td> 
				<?php 
					$zahl=0;
					for ($i=0;$i<$z;$i++) {
						if ($a[$i] == $row->wert) {
							$zahl++;
						}
					}
					echo $zahl ;
					?>
			   </td>
			  </tr>
			 <?php } ?> 
			 </thead> 
			</table>
			<table class="widefat" style="margin-top:10px;">
				  <thead>
					  <tr>
						<td><input name="newsem" type="text" id="newsem" size="30" value="<?php _e('Semester hinzuf&uuml;gen','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Semester hinzuf&uuml;gen','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Semester hinzuf&uuml;gen','teachpress'); ?>') this.value='';"/></td>
						<td><input name="addsem" type="submit" class="teachpress_button" value="<?php _e('erstellen','teachpress'); ?>"/></td>
					  </tr>
				  </thead>
			</table>
			</div>
	</div>
	<div style="width:48%; float:left; padding-left:2%;">
		<div>
			<h4><strong><?php _e('Veranstaltungstypen','teachpress'); ?></strong></h4> 
			 <table border="0" cellspacing="0" cellpadding="0" class="widefat">
				<thead>
				  <tr>
					<th>&nbsp;</th>
					<th><?php _e('Name','teachpress'); ?></th>
					<th><?php _e('Anzahl','teachpress'); ?></th>
				  </tr>
				</thead>
			<?php    
				$row = "SELECT einstellungs_id, wert FROM " . $teachpress_einstellungen . " WHERE category = 'veranstaltungstyp' ORDER BY wert";
				$row = tp_results($row);
				$z = 0;
				$anzahl = "SELECT vtyp FROM " . $teachpress_ver . "";	
				$anzahl = tp_results($anzahl);
				foreach ($anzahl as $anzahl) {
					$a[$z] = $anzahl->vtyp;
					$z++;
				}
				foreach ($row as $row) { ?>  
			  <tr>
				<td><a title="Kategorie &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="admin.php?page=teachpress/einstellungen.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
				<td>
				<?php 
				$zahl=0;
				for ($i=0;$i<$z;$i++) {
					if ($a[$i] == $row->wert) {
						$zahl++;
					}
				}
				echo $zahl ;
				?></td>
			  </tr>
			  <?php } ?>  
		   </table>  
		   <table class="widefat" style="margin-top:10px;">
			  <thead>
				  <tr>
					<td><input name="typ" type="text" id="typ" size="30" value="<?php _e('Typ hinzuf&uuml;gen','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Typ hinzuf&uuml;gen','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Typ hinzuf&uuml;gen','teachpress'); ?>') this.value='';"/></td>
					<td><input name="addtyp" type="submit" class="teachpress_button" value="<?php _e('erstellen','teachpress'); ?>"/></td>
				  </tr>
			  </thead>
		   </table>   
		</div>
		<div style="padding-top:10px;">
			<h4><strong><?php _e('Allgemein','teachpress'); ?></strong></h4>
			<table border="0" cellspacing="0" cellpadding="0" class="widefat">
			 <thead>
			  <tr>
				<th><?php _e('teachPress Version','teachpress'); ?>:</th>
				<td><?php 
					// Test ob Datenbank installiert ist
					$test = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
					$test = tp_var($test);
					if ($test != '') {
						 // Test ob Datenbank noch aktuell
						$test = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'db-version'";
						$test = tp_var($test);
						$version =  get_tp_version();
						if ($test == $version) { ?>
							<?php echo $version; ?> <span style="color:#00FF00; font-weight:bold;">&radic;</span>
						<?php } 
						else { ?>
							<?php echo $test; ?> <span style="color:#FF0000; font-weight:bold;">X</span> <a href="admin.php?page=teachpress/einstellungen.php&up=1"><strong><?php _e('Update auf','teachpress'); ?> <?php echo $version; ?></strong></a>
						<?php }
					} 
					else { ?>
						<a href="admin.php?page=teachpress/einstellungen.php&ins=1"><strong><?php _e('installieren','teachpress'); ?></strong></a>
					<?php } ?>   </td>
			  </tr>
			  <tr>
				<th><?php _e('Aktuelles Semester','teachpress'); ?></th>
				<td><select name="semester" id="semester">
						<?php
						$abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
						$wert = tp_var($abfrage);
						?>
						<option value="<?php echo"$wert" ?>"><?php echo"$wert"?></option>
						<option>------------</option>
						<?php    
					   $sem = "SELECT einstellungs_id, wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
						$sem = tp_results($sem);
						foreach ($sem as $sem) { ?> 
							<option value="<?php echo $sem->wert; ?>"><?php echo $sem->wert; ?></option>
						<?php } ?>    
					</select></td>
			  </tr>
			  <tr>
				<th><?php _e('Permalinks','teachpress'); ?></th>
				<td><select name="permalink" id="permalink">
					  <?php
					  $abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'permalink'";
					  $wert = tp_var($abfrage);
					  ?>
					  <option value="<?php echo"$>wert" ?>"><?php if ($wert == 0) { _e('nein','teachpress'); } else { _e('ja','teachpress'); }?></option>
					  <option>---</option>  
					  <option value="1"><?php _e('ja','teachpress'); ?></option>
					  <option value="0"><?php _e('nein','teachpress'); ?></option>
					</select></td>
			  </tr>
			 </thead> 
			</table>
			  <p style="padding-left:20px;"><input name="einstellungen" type="submit" id="teachpress_einstellungen" value="<?php _e('speichern','teachpress'); ?>" class="teachpress_button"/></p>
		</div>
	</div>
     </div>   
    </form>
    </div> 
</div>
<?php } ?>