<?php
/*
 * Zum Erstellen neuer Publikationen
 *
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
<h2><?php _e('Publikationen hinzuf&uuml;gen','teachpress'); ?></h2>
<?php
global $teachpress_pub;
global $teachpress_tags;
global $teachpress_beziehung;
global $teachpress_user;
global $current_user;
// User Infos von WordPress
get_currentuserinfo();
$user = $current_user->ID;
// Formulardaten
$erstellen = $_POST[erstellen];
$typ = htmlentities(utf8_decode($_POST[typ]));
$name = htmlentities(utf8_decode($_POST[name]));
$autor = htmlentities(utf8_decode($_POST[autor]));
$erschienen = htmlentities(utf8_decode($_POST[erschienen]));
$jahr = htmlentities(utf8_decode($_POST[jahr]));
$isbn = htmlentities(utf8_decode($_POST[isbn]));
$links = htmlentities(utf8_decode($_POST[links]));
$sort = htmlentities(utf8_decode($_POST[sortierung]));
$comment = htmlentities(utf8_decode($_POST[comment]));
$tags = htmlentities(utf8_decode($_POST[tags]));
$bookmark = $_POST[bookmark];
// Falls Publikation erstellt wurde, wird Dialog ausgegeben
if (isset($erstellen)) {
	add_pub($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $tags, $bookmark, $user, $comment);
	$message = __('Publikation hinzugef&uuml;gt','teachpress');
	$site = 'admin.php?page=teachpress/addpublications.php';
	tp_get_message($message, $site);
}
?>
<form name="form1" method="post" action="<?php echo $PHP_SELF ?>" id="form1">
  <table class="widefat">
    <tr>
      <td><strong><?php _e('Bookmarks','teachpress'); ?></strong></td>
      <td><input type="checkbox" name="bookmark[]" id="bookmark" value="<?php echo $user; ?>" title="<?php _e('Anklicken, damit die Publikation in Ihre Publikationsliste aufgenommen wird','teachpress'); ?>"/> <label for="bookmark" title="<?php _e('Anklicken, damit die Publikation in Ihre Publikationsliste aufgenommen wird','teachpress'); ?>"><?php _e('zur eigenen Liste hinzuf&uuml;gen','teachpress'); ?></label>
      <p>
      <?php
	   // Abfrage der User mit Bookmark auf mindestens 1 Publikation
	   // If-Anweisung soll Ausgabe einer Fehlermeldung nach Absenden des Formulars verhindern
	   if ($send != 'Senden') {
		   $abfrage = "SELECT DISTINCT user FROM " . $teachpress_user . "";
		   $row = tp_results($abfrage);
		   foreach($row as $row) {
			  if ($user != $row->user) { 
				  $user_info = get_userdata($row->user);
			   ?>
				<input type="checkbox" name="bookmark[]" id="bookmark_<?php echo $user_info->ID; ?>" value="<?php echo $user_info->ID; ?>" title="<?php _e('Bookmark f&uuml;r','teachpress'); ?> <?php echo $user_info->display_name; ?>"/> <label for="bookmark_<?php echo $user_info->ID; ?>" title="<?php _e('Bookmark f&uuml;r','teachpress'); ?> <?php echo $user_info->display_name; ?>"><?php echo $user_info->display_name; ?></label> | 
				<?php 
				} 
		   } 
	   }
	   ?>
      </p>
      </td>
    </tr>
    <tr>
      <td><strong><?php _e('Typ','teachpress'); ?></strong></td>
      <td>
        <select name="typ" id="typ">
          <option value="Buch"><?php _e('Buch','teachpress'); ?></option>
          <option value="Vortrag"><?php _e('Vortrag','teachpress'); ?></option>
          <option value="Chapter in book"><?php _e('Chapter in book','teachpress'); ?></option>
          <option value="Conference paper"><?php _e('Conference paper','teachpress'); ?></option>
          <option value="Journal article"><?php _e('Journal article','teachpress'); ?></option>
          <option value="Bericht"><?php _e('Bericht','teachpress'); ?></option>
          <option value="Sonstiges"><?php _e('Sonstiges','teachpress'); ?></option>
        </select>      </td>
    </tr>
    <tr>
      <td><strong><?php _e('Name','teachpress'); ?></strong></td>
      <td><textarea name="name" cols="80" wrap="virtual" id="name"></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Autor(en)','teachpress'); ?></strong></td>
      <td><textarea name="autor" cols="80" wrap="virtual" id="autor"></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Erschienen/Verlag','teachpress'); ?></strong></td>
      <td><textarea name="erschienen" cols="80" wrap="virtual" id="erschienen"></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Jahr','teachpress'); ?></strong></td>
      <td><input type="text" name="jahr" id="jahr"></td>
    </tr>
    <tr>
      <td><strong><?php _e('ISBN (optinal)','teachpress'); ?></strong></td>
      <td><input type="text" name="isbn" id="isbn"></td>
    </tr>
    <tr>
      <td><strong><?php _e('Link (optinal)','teachpress'); ?></strong></td>
      <td><input name="links" type="text" id="links" size="80"></td>
    </tr>
    <tr>
      <td><strong><?php _e('Sortierdatum','teachpress'); ?></strong></td>
      <td><input type="text" name="sortierung" id="sortierung" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>"/>
        <input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/></td>
    </tr>
    <tr>
      <td><strong><?php _e('Kommentar (f&uuml;r interne Zwecke)','teachpress'); ?></strong></td>
      <td><textarea name="comment" cols="80" id="comment" wrap="virtual"></textarea></td>
    </tr>
    <tr>
      <td><strong><?php _e('Tags (durch Komma trennen)','teachpress'); ?></strong></td>
      <td><input name="tags" type="text" id="tags" size="80" value=""></td>
    </tr>
    
    <tr>
      <td></td>
      <td>
      <div class="teachpress_cloud">
       <?php
	   	// Abfrage aller Tags, Tag-Cloud erstellen
		    $row = "SELECT name, tag_id FROM " . $teachpress_tags . " ORDER by name";
		    $row = tp_results($row);
			$z=0;
			// Tags zu einem array zusammenfassen
			foreach($row as $row) {
				$a[$z][0] = $row->tag_id;
				$a[$z][1] = $row->name;
				$z++;
			}
			$abfrage = "SELECT tag_id FROM " . $teachpress_beziehung . "";
			$row = tp_results($abfrage);
			$x=0;
			foreach($row as $row) {
				$b[$x] = $row->tag_id;
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
           </div>       </td>
    </tr>
  </table>
  <p>
    <input name="erstellen" type="submit" class="teachpress_button" id="publikation_erstellen" onclick="teachpress_validateForm('tags','','R','name','','R','autor','','R');return document.teachpress_returnValue" value="<?php _e('Erstellen','teachpress'); ?>">
    <input type="reset" name="Reset" value="<?php _e('Reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button">
  </p>
</form>
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