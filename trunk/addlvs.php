<?php 
/*
 * Neue Kurse hinzufuegen
*/ 
if ( is_user_logged_in() ) { 

global $teachpress_einstellungen;
global $teachpress_ver;

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
$rel_page = htmlentities(utf8_decode($_POST[rel_page]));
$parent = htmlentities(utf8_decode($_POST[parent2]));
$sichtbar = htmlentities(utf8_decode($_POST[sichtbar]));
$warteliste = htmlentities(utf8_decode($_POST[warteliste]));

if (isset($erstellen)) {
	add_lvs_in_database($lvname, $veranstaltungstyp, $raum, $dozent, $termin, $plaetze, $fplaetze, $startein, $endein, $semester,  $bemerkungen, $rel_page, $parent, $sichtbar, $warteliste);
	$message = __('Course created','teachpress');
	$site = 'admin.php?page=teachpress/addlvs.php';
    tp_get_message($message, $site);
}
?>
<div class="wrap" style="width:850px;">
    <h2><?php _e('Create a new course','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
    <div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Course name','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('For child courses: The name of the parent course will be add automatically.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Enrollments','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('If you have a course without enrollments, so add no dates in the fields start and end. teachPress will be deactivate the enrollments automatically.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Parent','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Here you can connect a course with a parent one. With this function you can create courses with an hierarchical order.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Related Page','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('If you will connect a course with a page (it is used as link in the courses overview) so you can do this here','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('For course informations','teachpress'); ?>: <strong><?php _e('[tpdate id="x"] (x = Course-ID)','teachpress'); ?></strong></p>
        <p class="hilfe_headline"><?php _e('Visibility','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Here you can edit the visibility of a course in the enrollments. If this is a course with inferier events so must select "Yes".','teachpress'); ?></p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
    </div>
  <form id="addlvs" name="form1" method="post" action="<?php echo $PHP_SELF ?>">
        <table class="widefat">
         <thead>
          <tr>
            <th><?php _e('Course type','teachpress'); ?></th>
            <td>
              <select name="veranstaltungstyp" id="veranstaltungstyp">
              <?php 
			    $row = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'veranstaltungstyp' ORDER BY wert";
				$row = tp_results($row);
				foreach ($row as $row) { ?>  
                	<option value="<?php echo $row->wert; ?>"><?php echo $row->wert; ?></option>
                <?php } ?>
            </select>            </td>
            <th><?php _e('Term','teachpress'); ?></th>
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
				// Semester in array speichern - wird spaeter fuer Parent-Menu genutzt
				foreach ($sem as $sem) { 
					$period[$x] = $sem->wert;
					$x++;
				}
				$zahl = $x-1;
				// gibt alle Semester aus (umgekehrte Reihenfolge)
				while ($zahl != 0) {
					echo '<option value="' . $period[$zahl] . '">' . $period[$zahl] . '</option>';
					$zahl--;
				}?> 
            </select></td>
          </tr>
              <tr>
                <th><?php _e('Parent','teachpress'); ?></th>
<td>
                    <select name="parent2" id="parent2">
                      <option value="0"><?php _e('none','teachpress'); ?></option>
                      <option>------</option>
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
							$zahl = 0;
							for ($j = 0; $j < $z; $j++) {
								if ($period[($x - 1)-$i] == $par[$j][2] ) {
									echo '<option value="' . $par[$j][0] . '">' . $par[$j][1] . ' ' . $par[$j][2] . '</option>';
									$zahl++;
						        } 
						    }
							if ($zahl != 0) {
						    	echo '<option>------</option>';
							}
					    }?>
                    </select>
                </td>
                <th><?php _e('Visibility','teachpress'); ?></th>
                <td><select name="sichtbar" id="sichtbar">
                  <option value="1"><?php _e('yes','teachpress'); ?></option>
                  <option value="0"><?php _e('no','teachpress'); ?></option>
                </select>            </td>
              </tr>
        </thead>      
    </table>     
       <p style="font-size:2px;">&nbsp;</p>
        <table class="widefat">
         <thead>
              <tr>
                <th><?php _e('Course name','teachpress'); ?></th>
                <td><input name="lvname" type="text" id="lvname" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Lecturer','teachpress'); ?></th>
                <td><input name="dozent" type="text" id="dozent" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Date','teachpress'); ?></th>
                <td><input name="termin" type="text" id="termin" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Room','teachpress'); ?></th>
                <td><input name="raum" type="text" id="raum" size="50" /></td>
              </tr>
              <tr>
                <th><?php _e('Number of places','teachpress'); ?></th>
                <td><input name="platz" type="text" id="platz" size="6" /></td>
              </tr>
              <tr>
                <th><?php _e('Comment','teachpress'); ?></th>
                <td><input name="bemerkungen" type="text" id="bemerkungen" size="75" /></td>
              </tr>
              <tr>
                <th><?php _e('Related Page','teachpress'); ?></th>
                <td><select name="rel_page" id="rel_page">
        			<?php teachpress_wp_pages("menu_order","ASC",$rel_page,0,0); ?>
        			</select>
                </td>
              </tr>
       </thead>       
      </table>
    <h4 style="margin-bottom:7px; margin-top:7px;"><?php _e('Enrollments','teachpress'); ?></h4>
        <table class="widefat">
         <thead>
          <tr>
            <th><?php _e('Start','teachpress'); ?></th>
            <td><input name="startein" type="text" id="startein" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" size="15"/><input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/></td>
            <th><?php _e('End','teachpress'); ?></th>
            <td><input name="endein" type="text" id="endein" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" size="15"/><input type="submit" name="calendar2" id="calendar2" value="..." class="teachpress_button"/></td>
            <th><?php _e('Waiting list','teachpress'); ?></th>
            <td><select name="warteliste" id="warteliste">
              <option value="0"><?php _e('no','teachpress'); ?></option>
              <option value="1"><?php _e('yes','teachpress'); ?></option>
            </select>
            </td>
          </tr>
      </thead>    
    </table>
      <p>
        <input name="erstellen" type="submit" id="teachpress_erstellen" onclick="teachpress_validateForm('lvname','','R','dozent','','R','platz','','NisNum');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>" class="teachpress_button">
        <input type="reset" name="Reset" value="<?php _e('reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button">
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