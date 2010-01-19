<?php  
/*
 * Neue Publikationen hinzufuegen
*/
if ( is_user_logged_in() ) { 
?>
<script type="text/javascript">
	// for adding new tags
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
<h2><?php _e('Add publications','teachpress'); ?><span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
 <div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Bookmarks','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Add the publication to different publication lists.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Image &amp; Related page','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Both fields are for the teachPress Books widget. With the related page you can link a publication with a normal post/page.','teachpress'); ?></p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
    </div>
<?php
global $teachpress_pub;
global $teachpress_tags;
global $teachpress_beziehung;
global $teachpress_user;
global $current_user;

get_currentuserinfo();
$user = $current_user->ID;

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
$image_url = htmlentities(utf8_decode($_POST[image_url]));
$rel_page = htmlentities(utf8_decode($_POST[rel_page]));
$is_isbn = htmlentities(utf8_decode($_POST[isisbn]));
$bookmark = $_POST[bookmark];
// if publications was created
if (isset($erstellen)) {
	add_pub($name, $typ, $autor, $erschienen, $jahr, $isbn, $links, $sort, $tags, $bookmark, $user, $comment, $image_url, $rel_page, $is_isbn);
	$message = __('Publication added','teachpress');
	$site = 'admin.php?page=teachpress/addpublications.php';
	tp_get_message($message, $site);
}
?>
<form name="form1" method="post" action="<?php echo $PHP_SELF ?>" id="form1">
  <div style="min-width:780px; width:100%; max-width:1100px;">
  <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">
  <table class="widefat">
  	<thead>
  	<tr>
   		<th><strong><?php _e('Bookmarks','teachpress'); ?></strong></th>
    </tr>
    <tr>
      <td>
      <input type="checkbox" name="bookmark[]" id="bookmark" value="<?php echo $user; ?>" title="<?php _e('click to add the publication in your own list','teachpress'); ?>"/> <label for="bookmark" title="<?php _e('click to add the publication in your own list','teachpress'); ?>"><?php _e('add to your own list','teachpress'); ?></label>
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
				<input type="checkbox" name="bookmark[]" id="bookmark_<?php echo $user_info->ID; ?>" value="<?php echo $user_info->ID; ?>" title="<?php _e('Bookmark for','teachpress'); ?> <?php echo $user_info->display_name; ?>"/> <label for="bookmark_<?php echo $user_info->ID; ?>" title="<?php _e('Bookmark for','teachpress'); ?> <?php echo $user_info->display_name; ?>"><?php echo $user_info->display_name; ?></label> | 
				<?php 
				} 
		   } 
	   }
	   ?>
      </p>
      </td>
    </tr>
    </thead>
    </table>
   <p style="font-size:2px; margin:0px;">&nbsp;</p> 
  <table class="widefat">
  	<thead>
    <tr>
    	<th><?php _e('Image &amp; Related page','teachpress'); ?></th>
    </tr>
  	<tr>
        <td><p><strong><?php _e('Image URL','teachpress'); ?></strong></p>
        <input name="image_url" id="image_url" type="text" style="width:90%;"/>
         <a id="add_image" class="thickbox" href="media-upload.php?post_id=0&type=image&TB_iframe=true&width=640&height=440" title="<?php _e('Add Image','teachpress'); ?>" onclick="return false;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
        <p><strong><?php _e('Related page','teachpress'); ?></strong></p>
        <div style="overflow:hidden;">
        <select name="rel_page" id="rel_page" style="width:90%;">
        <?php teachpress_wp_pages("menu_order","ASC",$rel_page,0,0); ?>
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
      <td>
      <p><strong><?php _e('Type','teachpress'); ?></strong></p>
      <select name="typ" id="typ">
          <option value="Buch"><?php _e('Book','teachpress'); ?></option>
          <option value="Vortrag"><?php _e('Presentation','teachpress'); ?></option>
          <option value="Chapter in book"><?php _e('Chapter in book','teachpress'); ?></option>
          <option value="Conference paper"><?php _e('Conference paper','teachpress'); ?></option>
          <option value="Journal article"><?php _e('Journal article','teachpress'); ?></option>
          <option value="Bericht"><?php _e('Report','teachpress'); ?></option>
          <option value="Sonstiges"><?php _e('Others','teachpress'); ?></option>
      </select>
      <p><strong><?php _e('Name','teachpress'); ?></strong></p>
      <textarea name="name" wrap="virtual" id="name" style="width:95%"></textarea>
      <p><strong><?php _e('Author(s)','teachpress'); ?></strong></p>
      <textarea name="autor" wrap="virtual" id="autor" style="width:95%"></textarea>
      <p><strong><?php _e('Published by','teachpress'); ?></strong></p>
      <textarea name="erschienen" rows="3" wrap="virtual" id="erschienen" style="width:95%"></textarea>
      <p><strong><?php _e('Year','teachpress'); ?></strong></p>
      <input type="text" name="jahr" id="jahr">
      <p><strong><?php _e('ISBN/ISSN','teachpress'); ?></strong></p>
      <input type="text" name="isbn" id="isbn">
        <span style="padding-left:7px;">
          <label>
            <input name="isisbn" type="radio" id="isisbn_0" value="1" checked="checked"/>
            <?php _e('ISBN','teachpress'); ?></label>
          <label>
            <input name="isisbn" type="radio" value="0" id="isisbn_1" />
            <?php _e('ISSN','teachpress'); ?></label>
        </span>
      <p><strong><?php _e('Link','teachpress'); ?></strong></p>
      <input name="links" type="text" id="links" style="width:95%">
      <p><strong><?php _e('Sorting date','teachpress'); ?></strong></p>
      <input type="text" name="sortierung" id="sortierung" value="<?php _e('JJJJ-MM-TT','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';"/>
      <input type="submit" name="calendar" id="calendar" value="..." class="teachpress_button"/>
      <p><strong><?php _e('Comment','teachpress'); ?></strong></p>
      <textarea name="comment" wrap="virtual" id="comment" style="width:95%"></textarea></td>
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
      <p><strong><?php _e('New (seperate by comma)','teachpress'); ?></strong></p>
      <input name="tags" type="text" id="tags" value="" style="width:95%">
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
    <input name="erstellen" type="submit" class="teachpress_button" id="publikation_erstellen" onclick="teachpress_validateForm('tags','','R','name','','R','autor','','R');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>">
    <input type="reset" name="Reset" value="<?php _e('reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button">
  </p>
  </div>
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