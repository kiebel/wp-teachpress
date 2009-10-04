<?php
/* Bearbeiten von Publikationen
 * Eingangsvariablen von showpublications.php und yourpub.php:
 * $pub_ID (INT) - Bestimmt die Publikation die geladen wird
 * $search (String) - Dient zum Ruecksprung zum vorherigen Suchergebnis
 * Rückgabewerte: keine
*/
?>

<?php 
/* Sicherheitsabfrage ob User eingeloggt ist, um unbefugte Zugriffe von außen zu vermeiden
 * Nur wenn der User eingeloggt ist, wird das Script ausgeführt
*/ 
if ( is_user_logged_in() ) { 
?> 

<script type="text/javascript">
	function inserttag(tag) {
		if (document.getElementsByName("tags")[0].value == "") {
			document.getElementsByName("tags")[0].value = tag;
		}
		else {
			document.getElementsByName("tags")[0].value = document.getElementsByName("tags")[0].value+', '+tag;
			document.getElementsByName("tags")[0].value = document.getElementsByName("tags")[0].value;
		}	
	}
</script>
<div class="wrap" style="width:800px;">
<?php
global $teachpress_pub; 
global $teachpress_beziehung; 
global $teachpress_tags;
global $pagenow;
global $current_user;
// User Infos von WordPress
get_currentuserinfo();
// Formulardaten von editpub.php
$speichern = $_GET[speichern];
$typ = htmlentities(utf8_decode($_GET[typ]));
$name = htmlentities(utf8_decode($_GET[name]));
$autor = htmlentities(utf8_decode($_GET[autor]));
$erschienen = htmlentities(utf8_decode($_GET[erschienen]));
$jahr = htmlentities(utf8_decode($_GET[jahr]));
$isbn = htmlentities(utf8_decode($_GET[isbn]));
$links = htmlentities(utf8_decode($_GET[links]));
$sort = htmlentities(utf8_decode($_GET[sortierung]));
$comment = htmlentities(utf8_decode($_GET[comment]));
$tags = htmlentities(utf8_decode($_GET[tags]));
$delbox = $_GET[delbox];
// Daten von showpublications.php, yourpub.php oder editpub.php
$pub_ID = htmlentities(utf8_decode($_GET[pub_ID]));
$search = htmlentities(utf8_decode($_GET[search]));
// Abgleich was zu tun ist, anhand des Inhalts der Variablen
if (isset($speichern)) {
	change_pub($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $comment, $tags, $pub_ID, $delbox);
	$message = __('Publikation ge&auml;ndert','teachpress');
	$site = 'admin.php?page=teachpress/editpub.php&pub_ID=' . $pub_ID . '&search=' . $search . '';
	tp_get_message($message, $site);
}
else {
	?>
	<p><a href="admin.php?page=publikationen.php&search=<?php echo "$search" ?>" class="teachpress_back" title="<?php _e('Alle Publikationen anzeigen','teachpress'); ?>">&larr; <?php _e('Alle Publikationen','teachpress'); ?></a> <a href="admin.php?page=teachpress/yourpub.php&search=<?php echo "$search" ?>" class="teachpress_back" title="<?php _e('Eigene Publikationen anzeigen','teachpress'); ?>">&larr; <?php _e('Eigene Publikationen','teachpress'); ?></a></p>
	<?php
}
?>
<h2 style="padding-top:5px;"><?php _e('Publikation bearbeiten','teachpress'); ?></h2>
<?php
$row= "SELECT * FROM " . $teachpress_pub . " WHERE pub_id = '$pub_ID'";
$row = tp_results($row);
foreach ($row as $row) { 
?>
<form name="form1" method="GET" action="<?php echo $PHP_SELF ?>">
<input name="pub_ID" type="hidden" value="<?php echo "$pub_ID" ?>">
<input name="page" type="hidden" value="teachpress/editpub.php">
<input name="search" type="hidden" value="<?php echo "$search" ?>">
  <table class="widefat">
    <tr>
    	<td><strong><?php _e('Typ','teachpress'); ?></strong></td>
        <td><select name="typ" id="typ">
              <option value="<?php echo "$row->typ"; ?>"><?php echo "$row->typ"; ?></option>
              <option>------</option>	
              <option value="Buch"><?php _e('Buch','teachpress'); ?></option>
              <option value="Vortrag"><?php _e('Vortrag','teachpress'); ?></option>
              <option value="Chapter in book"><?php _e('Chapter in book','teachpress'); ?></option>
              <option value="Conference paper"><?php _e('Conference paper','teachpress'); ?></option>
         	  <option value="Journal article"><?php _e('Journal article','teachpress'); ?></option>
              <option value="Bericht"><?php _e('Bericht','teachpress'); ?></option>
              <option value="Sonstiges"><?php _e('Sonstiges','teachpress'); ?></option>
            </select>    	</td>
    </tr>
    <tr>
      <td><strong><?php _e('Name','teachpress'); ?></strong></td>
      <td><textarea name="name" cols="75" wrap="virtual" id="name"><?php echo "$row->name"; ?></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Autor(en)','teachpress'); ?></strong></td>
      <td><textarea name="autor" cols="75" wrap="virtual" id="autor"><?php echo "$row->autor"; ?></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Erschienen/Verlag','teachpress'); ?></strong></td>
      <td><textarea name="erschienen" cols="75" wrap="virtual" id="erschienen"><?php echo "$row->verlag"; ?></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Jahr','teachpress'); ?></strong></td>
      <td><input type="text" name="jahr" id="jahr" value="<?php echo "$row->jahr"; ?>"></td>
    </tr>
    <tr>
      <td><strong><?php _e('ISBN (optinal)','teachpress'); ?></strong></td>
      <td><input type="text" name="isbn" id="isbn" value="<?php echo "$row->isbn"; ?>"></td>
    </tr>
    <tr>
      <td><strong><?php _e('Link (optinal)','teachpress'); ?></strong></td>
      <td><input name="links" type="text" id="links" size="75" value="<?php echo "$row->url"; ?>"></td>
    </tr>
    <tr>
      <td><strong><?php _e('Sortierdatum','teachpress'); ?></strong></td>
      <td><input type="text" name="sortierung" id="sortierung" value="<?php echo "$row->sort"; ?>"/><input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/></td>
    </tr>
    <tr>
      <td><strong><?php _e('Kommentar','teachpress'); ?></strong></td>
      <td><textarea name="comment" cols="75" id="comment" wrap="virtual"><?php echo "$row->comment"; ?></textarea></td>
    </tr>
  </table>
  <p style="font-size:2px; margin:0px;">&nbsp;</p>
  <table class="widefat">
  <thead>
  <tr>
    <td><strong><?php _e('aktuelle Tags','teachpress'); ?></strong></td>
    <td> 
	<?php
	  $row = "SELECT " . $teachpress_tags . ".name, " . $teachpress_beziehung . ".belegungs_id 
			FROM " . $teachpress_beziehung . " 
			INNER JOIN " . $teachpress_tags . " ON " . $teachpress_tags . ".tag_id=" . $teachpress_beziehung . ".tag_id
			INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
			WHERE " . $teachpress_pub . ".pub_id = '$pub_ID'
			ORDER BY " . $teachpress_tags . ".name";	
	  $row = tp_results($row);
	  foreach ($row as $row3){
	  	?>
	  	<input name="delbox[]" type="checkbox" value="<?php echo $row3->belegungs_id; ?>" title="Tag &laquo;<?php echo $row3->name; ?>&raquo; <?php _e('l&ouml;schen','teachpress'); ?>" id="checkbox_<?php echo $row3->belegungs_id; ?>"/><span style="font-size:12px;" ><label for="checkbox_<?php echo $row3->belegungs_id; ?>" title="Tag &laquo;<?php echo $row3->name; ?>&raquo; <?php _e('l&ouml;schen','teachpress'); ?>"><?php echo $row3->name; ?></label></span>
		<?php } ?>  	
    </td>
  </tr>
  <tr>
    <td><strong><?php _e('neu','teachpress'); ?></strong></td>
    <td><input name="tags" type="text" id="tags" size="60">
    <span style="font-size:12px;"> (<?php _e('durch Komma trennen','teachpress'); ?>)</span></td>
  </tr>
  <tr>
    <td><strong><?php _e('neu aus Tag-Cloud','teachpress'); ?></strong></td>
    <td>
     <div class="teachpress_cloud">
       <?php
		  $row = "SELECT name, tag_id FROM " . $teachpress_tags . " ORDER by name";
		  $row = tp_results($row);
			$z=0;
			// Tags zu einem array zusammenfassen
			foreach($row as $row) {
				$a[$z][0] = $row->tag_id;
				$a[$z][1] = $row->name;
				$z++;
			}
			$row2 = "SELECT tag_id FROM " . $teachpress_beziehung . "";
			$row2 = tp_results($row2);
			$x=0;
			foreach($row2 as $row2) {
				$b[$x] = $row2->tag_id;
				$x++;
			}
			// nach Anzahl der häufigsten und seltensten Tags suchen
			$max = 0;
			$min = 0;
			$zahl = 0;
			for ($i=0; $i<$x; $i++ ) {
				$search = $b[$i];	
				for ($j=0; $j<$x; $j++) {
					if ($search == $b[$j] ) {
						$zahl++;
					}
				}	
				if ($zahl > $max) {
					$max = $zahl;
				}
				if ($zahl < $min) {
					$min = $zahl;
				}	
				$zahl = 0;
			}
			// Zusammensetzung des Return
			for($i=0; $i<$z; $i++) {
					$anzahl = $a[$i][0];
					$zahl=0;
					// Anzahl ermitteln
					for ($j=0; $j<$x; $j++) {
						if ($anzahl == $b[$j] ) {
							$zahl++;
						}	
					}
					$t = $zahl;
					// Schriftgröße berechnen
					$size = floor((35*($t-$min)/($max-$min)));
					// Ausgleich der Schriftgröße
					if ($size < 11) {
						$size = 11 ;
					}
					?>
					<span style="font-size:<?php echo $size; ?>px;"><a href="javascript:inserttag('<?php echo $a[$i][1]; ?>')" title="&laquo;<?php echo $a[$i][1]; ?>&raquo; <?php _e('als Tag hinzuf&uuml;gen','teachpress'); ?>"><?php echo $a[$i][1]; ?></a></span>
                    <?php } ?>
           </div>
     </td>
  </tr>
  </thead>
</table>
  <p>
    <input type="submit" name="speichern" id="publikation_erstellen" value="<?php _e('Speichern','teachpress'); ?>" class="teachpress_button">
  </p>
</form>
<?php } 
?>
<script type="text/javascript">
  Calendar.setup(
    {
      inputField  : "sortierung",         // ID of the input field
      ifFormat    : "%Y-%m-%d",    // the date format
      button      : "calendar"       // ID of the button
    }
  );
</script>
</div>
<?php } ?>