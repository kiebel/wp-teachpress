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
// form data
$search = tp_sec_var($_GET[search]);
$tag_id = tp_sec_var($_GET[tp_edit_tag_ID], 'integer');
settype($tag_id, 'integer');
$action = $_GET[action];
$checkbox = $_GET[checkbox];
$name = tp_sec_var($_GET[tp_edit_tag_name]);
$save = $_GET[tp_edit_tag_submit];

if ( $action == "delete" ) {
	tp_delete_tags($checkbox);
	$message = __('Selected tags are deleted','teachpress');
	tp_get_message($message, $site);
}
if ( isset($save)) {
	tp_edit_tag($tag_id, $name);
	$message = __('Tag saved','teachpress');
	tp_get_message($message, $site);
}
?>
<h2><?php _e('Tags','teachpress'); ?></h2>
<div id="searchbox" style="float:right; padding-bottom:10px;">
	<?php if ($search != "") { ?><a href="admin.php?page=teachpress/tags.php" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a><?php } ?>
    <input type="text" name="search" id="pub_search_field" value="<?php echo stripslashes($search); ?>"/>
    <input type="submit" name="button" id="button" value="<?php _e('search tag','teachpress'); ?>" class="teachpress_button"/>
</div>
<div id="filterbox" style="padding-bottom:10px;">  
	<select name="action">
        <option value="">- <?php _e('Bulk actions','teachpress'); ?> -</option>
        <option value="delete"><?php _e('delete','teachpress'); ?></option>
    </select>
    <input name="ok" value="ok" type="submit" class="teachpress_button"/></td>    
</div>
<div style="width:600px;">
<table border="0" cellspacing="0" cellpadding="0" class="widefat">
    <thead>
      <tr>
        <th>&nbsp;</th>
        <th><?php _e('Name','teachpress'); ?></th>
        <th><?php _e('ID','teachpress'); ?></th>
        <th><?php _e('Number','teachpress'); ?></th>
      </tr>
    </thead> 
    <?php
	global $pagenow;
	// if the user use the search
	if ($search != "") {
		$abfrage = "SELECT * FROM " . $teachpress_tags . " WHERE `name` like '%$search%' OR `tag_id` = '$search'";	
	}
	// normal sql statement
	else {
  		$abfrage = "SELECT * FROM " . $teachpress_tags . " ORDER BY `name`";
	}				
	$test = $wpdb->get_results($abfrage);
	if ($test == 0) {
		echo '<tr><td colspan="4"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
	}
	else {
		$abfrage2 = "SELECT * FROM " . $teachpress_relation . "";
		$row = $wpdb->get_results($abfrage2);
		$z=0;
		foreach ($row as $row) {
			$daten[$z][0] = $row->pub_id;
			$daten[$z][1] = $row->tag_id;
			$z++;
		}
		$row2 = $wpdb->get_results($abfrage);
		foreach ($row2 as $row2) { ?>
          <tr>
            <th class="check-column"><input name="checkbox[]" type="checkbox" value="<?php echo $row2->tag_id; ?>"></th>
            <td id="tp_tag_row_<?php echo $row2->tag_id; ?>"><a onclick="teachpress_editTags('<?php echo $row2->tag_id; ?>')" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>" style="cursor:pointer;"><strong><?php echo stripslashes($row2->name); ?></strong></a><input type="hidden" id="tp_tag_row_name_<?php echo $row2->tag_id; ?>" value="<?php echo stripslashes($row2->name); ?>"/></td>
            <td><?php echo $row2->tag_id; ?></td>
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