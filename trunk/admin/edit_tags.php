<?php
/* Tag management
 * from tags.php:
 * @param $search (String) - Suchergebnis
 * @param $tag_id (INT) - ID eines zu bearbeitenden Tags
*/ 
function teachpress_tags_page(){ 
?> 
<div class="wrap" style="max-width:600px;">
  <form id="form1" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
  <input name="page" type="hidden" value="teachpress/tags.php" />
<?php
global $wpdb;
global $teachpress_pub; 
global $teachpress_user;
global $teachpress_relation;
global $teachpress_tags;
// Daten von tags.php
$search = tp_sec_var($_GET[search]);
$tag_id = tp_sec_var($_GET[tag_ID], 'integer');
settype($tag_id, 'integer');
$action = $_GET[action];
$checkbox = $_GET[checkbox];
$name = tp_sec_var($_GET[name]);
$speichern = $_GET[speichern];

if ( $action == "delete" ) {
	tp_delete_tags($checkbox);
	$message = __('Selected tags are deleted','teachpress');
	$site = 'admin.php?page=teachpress/tags.php&search=' . $search . '';
	tp_get_message($message, $site);
}
if ( isset($speichern)) {
	tp_edit_tag($tag_id, $name);
	$message = __('Tag saved','teachpress');
	$site = 'admin.php?page=teachpress/tags.php&search=' . $search . '';
	tp_get_message($message, $site);
}
?>
<h2><?php _e('Tags','teachpress'); ?></h2>
<div id="searchbox" style="float:right; padding-bottom:10px;">
	<?php if ($search != "") { ?><a href="admin.php?page=teachpress/tags.php" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a><?php } ?>
    <input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/>
    <input type="submit" name="button" id="button" value="<?php _e('search tag','teachpress'); ?>" class="teachpress_button"/>
</div>
<div id="filterbox" style="padding-bottom:10px;">  
	<select name="action">
        <option value="">- <?php _e('Bulk actions','teachpress'); ?> -</option>
        <option value="delete"><?php _e('delete','teachpress'); ?></option>
    </select>
    <input name="ok" value="ok" type="submit" class="teachpress_button"/></td>    
</div>
<p style="margin:0px;">&nbsp;</p>
<?php
// Bearbeiten von Tags
if ($tag_id != "") { 
	$name = "SELECT name FROM " . $teachpress_tags . " WHERE tag_id = '$tag_id'";
	$name = $wpdb->get_var($name);
	?>
    <fieldset style="width:590px; padding:5px; margin-bottom:15px; border:1px solid silver;">
    	<legend><?php _e('Edit tag','teachpress'); ?></legend>
        <input name="tag_ID" type="hidden" value="<?php echo $tag_id; ?>" />
        <input name="search" type="hidden" value="<?php echo $search; ?>" />
        <table border="0" cellspacing="0" cellpadding="0" class="widefat">
         <thead>
          <tr>
            <th><?php _e('Tag-ID','teachpress'); ?></th>
            <td><?php echo $tag_id; ?></td>
            <th><label for="name"><?php _e('Name','teachpress'); ?></label></th>
            <td><input name="name" type="text" id="name" value="<?php echo $name; ?>"/></td>
            <td><input name="speichern" type="submit" value="<?php _e('save','teachpress'); ?>" class="teachpress_button"/></td>
            <td><a title="<?php _e('cancel','teachpress'); ?>" href="admin.php?page=teachpress/tags.php&search=<?php echo $search; ?>" ><?php _e('cancel','teachpress'); ?></a></td>
          </tr>
         </thead> 
        </table>
    </fieldset>
<?php } ?>
<div style="width:600px;">
<table border="0" cellspacing="0" cellpadding="0" class="widefat">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th><?php _e('ID','teachpress'); ?></th>
        <th><?php _e('Name','teachpress'); ?></th>
        <th><?php _e('Number','teachpress'); ?></th>
      </tr>
    </thead> 
    <?php
	global $pagenow;
	// Auswahl dea SQL-Statemants basierend auf dem was der User will
	// Wenn Suche genutzt wird
	if ($search != "") {
		$abfrage = "SELECT * FROM " . $teachpress_tags . " WHERE name like '%$search%' OR tag_id = '$search'";	
	}
	// Standart
	else {
  		$abfrage = "SELECT * FROM " . $teachpress_tags . " ORDER BY name";
	}				
	$test = $wpdb->get_results($abfrage);
	// Test ob Eintraege vorhanden
	if ($test == 0) {
		echo '<tr><td colspan="4"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
	}
	else {
		// Abfrage wie oft die Tags verwendet werden
		$abfrage2 = "SELECT * FROM " . $teachpress_relation . "";
		$row = $wpdb->get_results($abfrage2);
		$z=0;
		foreach ($row as $row) {
			$daten[$z][0] = $row->pub_id;
			$daten[$z][1] = $row->tag_id;
			$z++;
		}
		// Ausgabe Tabelle Tags
		$row2 = $wpdb->get_results($abfrage);
		foreach ($row2 as $row2) { ?>
          <tr>
            <th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo"$row2->tag_id" ?>"></th>
            <td><?php echo"$row2->tag_id" ?></td>
            <td><a href="admin.php?page=teachpress/tags.php&tag_ID=<?php echo"$row2->tag_id" ?>&search=<?php echo "$search"?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo"$row2->name" ?></a></td>
            <td>
			<?php 
			$anzahl = 0;
			for ($i=0; $i < $z ; $i++) {
				if ($daten[$i][1] == $row2->tag_id) {
					$anzahl++;
				}
			}
			echo $anzahl;
			?>
            </td>
          </tr>
  		<?php }
 	} ?>
</table>
</div>
</form>
</div>
<?php } ?>