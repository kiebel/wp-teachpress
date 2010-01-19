<?php
/* Bearbeiten von Publikationen
 * from showpublications.php, yourpub.php:
 * @param $pub_ID (INT) - Bestimmt die Publikation die geladen wird
 * @param $search (String) - Dient zum Ruecksprung zum vorherigen Suchergebnis
*/
?>

<?php
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
<div class="wrap">
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
$image_url = htmlentities(utf8_decode($_GET[image_url]));
$rel_page = htmlentities(utf8_decode($_GET[rel_page]));
$is_isbn = htmlentities(utf8_decode($_GET[isisbn]));
$delbox = $_GET[delbox];
// Daten von showpublications.php, yourpub.php oder editpub.php
$pub_ID = htmlentities(utf8_decode($_GET[pub_ID]));
$search = htmlentities(utf8_decode($_GET[search]));
// Abgleich was zu tun ist, anhand des Inhalts der Variablen
if (isset($speichern)) {
	change_pub($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $tags, $comment, $image_url, $rel_page, $is_isbn, $pub_ID, $delbox);
	$message = __('Publication changed','teachpress');
	$site = 'admin.php?page=teachpress/editpub.php&pub_ID=' . $pub_ID . '&search=' . $search . '';
	tp_get_message($message, $site);
}
else {
	?>
	<p><a href="admin.php?page=publikationen.php&search=<?php echo "$search" ?>" class="teachpress_back" title="<?php _e('Show all publications','teachpress'); ?>">&larr; <?php _e('All publications','teachpress'); ?></a> <a href="admin.php?page=teachpress/yourpub.php&search=<?php echo "$search" ?>" class="teachpress_back" title="<?php _e('Show own publications','teachpress'); ?>">&larr; <?php _e('Your publications','teachpress'); ?></a></p>
	<?php
}
?>
<h2 style="padding-top:5px;"><?php _e('Edit publications','teachpress'); ?></h2>
<form name="form1" method="GET" action="<?php echo $PHP_SELF ?>">
<?php
$row= "SELECT * FROM " . $teachpress_pub . " WHERE pub_id = '$pub_ID'";
$row = tp_results($row);
foreach ($row as $row) { 
?>
<input name="pub_ID" type="hidden" value="<?php echo "$pub_ID" ?>">
<input name="page" type="hidden" value="teachpress/editpub.php">
<input name="search" type="hidden" value="<?php echo "$search" ?>">
  <div style="min-width:780px; width:100%; max-width:1100px;">
  <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">
  <table class="widefat">
  	<thead>
  	<tr>
    	<th><strong><?php _e('Image &amp; Related page','teachpress'); ?></strong></th>
    </tr>
  	<tr>
        <td>
        <?php if ($row->image_url) {?>
      	<p><img name="tp_pub_image" src="<?php echo "$row->image_url"; ?>" alt="<?php echo "$row->name"; ?>" title="<?php echo "$row->name"; ?>"/></p>
        <?php } ?>
        <p><strong><?php _e('Image URL','teachpress'); ?></strong></p>
        <input name="image_url" id="image_url" type="text" value="<?php echo "$row->image_url"; ?>" style="width:90%;"/>
        <a id="add_image" class="thickbox" href="media-upload.php?post_id=0&type=image&TB_iframe=true&width=640&height=440" title="<?php _e('Add Image','teachpress'); ?>" onclick="return false;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
        <p><strong><?php _e('Related page','teachpress'); ?></strong></p>
        <div style="overflow:hidden;">
        <select name="rel_page" id="rel_page" style="width:90%;">
        <?php teachpress_wp_pages("menu_order","ASC",$row->rel_page,0,0); ?>
        </select>
        </div>
        </td>
    </tr>
    </thead>
 </table>
 </div>
 <div style="width:67%; float:left;">
 <table class="widefat">  
 	<thead>
    <tr>
    	<th><?php _e('Publication','teachpress'); ?></th>
    </tr>
    <tr>  
        <td><p><strong><?php _e('Type','teachpress'); ?></strong></p>
        	<select name="typ" id="typ">
              <option value="<?php echo "$row->typ"; ?>"><?php echo "$row->typ"; ?></option>
              <option>------</option>	
              <option value="Buch"><?php _e('Book','teachpress'); ?></option>
              <option value="Vortrag"><?php _e('Presentation','teachpress'); ?></option>
              <option value="Chapter in book"><?php _e('Chapter in book','teachpress'); ?></option>
              <option value="Conference paper"><?php _e('Conference paper','teachpress'); ?></option>
         	  <option value="Journal article"><?php _e('Journal article','teachpress'); ?></option>
              <option value="Bericht"><?php _e('Report','teachpress'); ?></option>
              <option value="Sonstiges"><?php _e('Others','teachpress'); ?></option>
            </select>    	
            <p><strong><?php _e('Name','teachpress'); ?></strong></p>
      		<textarea name="name" wrap="virtual" id="name" style="width:95%;"><?php echo "$row->name"; ?></textarea>
            <p><strong><?php _e('Author(s)','teachpress'); ?></strong></p>
            <textarea name="autor" wrap="virtual" id="autor" style="width:95%;"><?php echo "$row->autor"; ?></textarea>
    		<p><strong><?php _e('Published by','teachpress'); ?></strong></p>
      		<textarea name="erschienen" rows="3" wrap="virtual" id="erschienen" style="width:95%;"><?php echo "$row->verlag"; ?></textarea>
            <p><strong><?php _e('Year','teachpress'); ?></strong></p>
            <input type="text" name="jahr" id="jahr" value="<?php echo "$row->jahr"; ?>">
            <p><strong><?php _e('ISBN/ISSN','teachpress'); ?></strong></p>
      		<input type="text" name="isbn" id="isbn" value="<?php echo "$row->isbn"; ?>">
               <span style="padding-left:7px;">
                  <label>
                    <input name="isisbn" type="radio" id="isisbn_0" value="1" <?php if ($row->is_isbn == '1') { echo 'checked="checked"'; }?>/>
                    <?php _e('ISBN','teachpress'); ?></label>
                  <label>
                    <input name="isisbn" type="radio" value="0" id="isisbn_1" <?php if ($row->is_isbn == '0') { echo 'checked="checked"'; }?>/>
                    <?php _e('ISSN','teachpress'); ?></label>
                </span>
     <p><strong><?php _e('Link','teachpress'); ?></strong></p>
     <input name="links" type="text" id="links" value="<?php echo "$row->url"; ?>" style="width:95%;">
     <p><strong><?php _e('Sorting date','teachpress'); ?></strong></p>
     <input type="text" name="sortierung" id="sortierung" value="<?php echo "$row->sort"; ?>"/><input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/><p>
     <strong><?php _e('Comment','teachpress'); ?></strong></p>
     <textarea name="comment" wrap="virtual" id="comment" style="width:95%;"><?php echo "$row->comment"; ?></textarea></td>
    </tr>
    </thead>
  </table>
  <p style="font-size:2px; margin:0px;">&nbsp;</p>
  <table class="widefat">
  <thead>
  <tr>
  	<th><?php _e('Tags','teachpress'); ?></th>
  </tr>
  <tr>
    <td>
    <p><strong><?php _e('Current','teachpress'); ?></strong></p>
	<?php
	  $sql = "SELECT " . $teachpress_tags . ".name, " . $teachpress_beziehung . ".belegungs_id 
			FROM " . $teachpress_beziehung . " 
			INNER JOIN " . $teachpress_tags . " ON " . $teachpress_tags . ".tag_id=" . $teachpress_beziehung . ".tag_id
			INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
			WHERE " . $teachpress_pub . ".pub_id = '$pub_ID'
			ORDER BY " . $teachpress_tags . ".name";	
	  $sql = tp_results($sql);
	  foreach ($sql as $row3){
	  	?>
	  	<input name="delbox[]" type="checkbox" value="<?php echo $row3->belegungs_id; ?>" title="Tag &laquo;<?php echo $row3->name; ?>&raquo; <?php _e('delete','teachpress'); ?>" id="checkbox_<?php echo $row3->belegungs_id; ?>"/> <span style="font-size:12px;" ><label for="checkbox_<?php echo $row3->belegungs_id; ?>" title="Tag &laquo;<?php echo $row3->name; ?>&raquo; <?php _e('delete','teachpress'); ?>"><?php echo $row3->name; ?></label></span> |  
		<?php } ?>  	
   <p><strong><?php _e('New (seperate by comma)','teachpress'); ?></strong></p>
   <input name="tags" type="text" id="tags" style="width:85%;">
     <div class="teachpress_cloud" style="padding-top:15px;">
       <?php
	   // Anzahl darzustellender Tags
	   	$limit = 50;
		// Schriftgroessen
		$maxsize = 35;
		$minsize = 11;
	   	// Ermittle Anzahl der Tags absteigend sortiert
		$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_beziehung . " GROUP BY " . $teachpress_beziehung . ".`tag_id` ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
		// Ermittle einzelnes Vorkommen der Tags, sowie Min und Max
		$sql = "SELECT MAX(anzahlTags) AS max, min(anzahlTags) AS min, COUNT(anzahlTags) as gesamt FROM (".$sql.") AS temp";
		$tagcloud_temp = mysql_fetch_array(mysql_query($sql));
		$max = $tagcloud_temp['max'];
		$min = $tagcloud_temp['min'];
		$insgesamt = $tagcloud_temp['gesamt'];
		// Tags und Anzahl zusammenstellen
		$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name,  t.tag_id as tag_id FROM " . $teachpress_beziehung . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
		$temp = mysql_query($sql);
		// Endausgabe der Cloud zusammenstellen
		while ($tagcloud = mysql_fetch_array($temp)) {
			// Schriftgröße berechnen
			// Minimum ausgleichen
			if ($min == 1) {
				$min = 0;
			}
			// Formel: max. Schriftgroesse*(aktuelle anzahl - kleinste Anzahl)/ (groeßte Anzahl - kleinste Anzahl)
			$size = floor(($maxsize*($tagcloud['tagPeak']-$min)/($max-$min)));
			// Ausgleich der Schriftgröße
			if ($size < $minsize) {
				$size = $minsize ;
			}
			?>
			<span style="font-size:<?php echo $size; ?>px;"><a href="javascript:inserttag('<?php echo $tagcloud['name']; ?>')" title="&laquo;<?php echo $tagcloud['name']; ?>&raquo; <?php _e('add as tag','teachpress'); ?>"><?php echo $tagcloud['name']; ?> </a></span> 
            <?php 
		  }
		  ?>
           </div>
     </td>
  </tr>
  </thead>
</table>
  <p>
    <input type="submit" name="speichern" id="publikation_erstellen" value="<?php _e('save','teachpress'); ?>" class="teachpress_button">
  </p>
</div>
</div>
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