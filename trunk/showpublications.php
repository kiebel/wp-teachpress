<?php 
/* Anzeige Publikationen
 * from editpub.php
 * @param $search (String)
*/
?>

<?php 
if ( is_user_logged_in() ) { 
?> 

<div class="wrap" style="padding-top:10px;">
<form id="showlvs" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
<input type="hidden" name="page" id="page" value="publikationen.php"/>
<?php
global $teachpress_pub; 
global $teachpress_user;
// User Infos von WordPress
global $current_user;
get_currentuserinfo();
// Get-Variablen von showpublications.php
$checkbox = $_GET[checkbox];
$action = $_GET[action];
$user = htmlentities(utf8_decode($_GET[user]));
$add_id = htmlentities(utf8_decode($_GET[add_id]));
$search = htmlentities(utf8_decode($_GET[search]));
settype($user, 'integer');
settype($add_id, 'integer');
// Wenn ein Plus angeklickt wurde, dann Bookmark auf Publikation setzen
if ($add_id != "") {
	add_bookmark($add_id, $user);
}	

// Prüfen welche Checkbox genutzt wurde und Aufteilung an die Funktionen mit Variablenübergabe
if ( $action == "delete" ) {
	delete_publication($checkbox);
	$message = __('Publications deleted','teachpress');
	$site = 'admin.php?page=publikationen.php';
	tp_get_message($message, $site);
}
?> 
<table border="0" cellspacing="0" cellpadding="5" style="float:right;">
  <tr>
  	<td><?php if ($search != "") { ?><a href="admin.php?page=publikationen.php" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Cancel the search','teachpress'); ?>">&crarr;</a><?php } ?></td>
    <td><input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/></td>
    <td><input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/></td>
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" id="show_pub_optionen">
  <tr>
    <td><h2><?php _e('All publications','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2></td>
  </tr>
  <tr>  
    <td><select name="action">
    		<option><?php _e('Bulk actions','teachpress'); ?></option>
            <option value="delete"><?php _e('delete','teachpress'); ?></option>
    	</select>
        <input name="ok" value="ok" type="submit" class="teachpress_button"/></td>    
  </tr>
</table>
<div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('For a publication list with tag cloud:','teachpress'); ?> <strong><?php _e('[tpcloud id="w" maxsize="x" minsize="y" limit="z"]','teachpress'); ?></strong></p>
         <ul style="list-style:disc; padding-left:40px;">
        	<li><?php _e('id - WP User-ID (0 for all)','teachpress'); ?></li>
            <li><?php _e('maxsize - max. font size in the tag cloud (default: 35)','teachpress'); ?> </li>
            <li><?php _e('minsize - min. font size in the tag cloud (default: 11)','teachpress'); ?></li>
            <li><?php _e('limit - maximum of visible tags (default: 30)','teachpress'); ?></li>
        </ul>
        <p class="hilfe_text"><?php _e('For normal publication lists:','teachpress'); ?> <strong><?php _e('[tplist user="w" tag="x" year="y" headline="z"]','teachpress'); ?></strong>
        <ul style="list-style:disc; padding-left:40px;">
        	<li><?php _e('user - WP User-ID (0 for all)','teachpress'); ?></li>
            <li><?php _e('tag - Tag-ID','teachpress'); ?> </li>
            <li><?php _e('year','teachpress'); ?></li>
            <li><?php _e('headline - 0(off) or 1(on)','teachpress'); ?></li>
        </ul>
        </p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
</div>

<p style="margin:0px;">&nbsp;</p>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
        <tr>
        	<th>&nbsp;</th>
            <th>&nbsp;</th>
            <th><?php _e('Name','teachpress'); ?></th>
            <th><?php _e('ID','teachpress'); ?></th>
            <th><?php _e('Type','teachpress'); ?></th> 
            <th><?php _e('Author(s)','teachpress'); ?></th>
            <th><?php _e('Published by','teachpress'); ?></th>
            <th><?php _e('Year','teachpress'); ?></th>
            <th><?php _e('ISBN','teachpress'); ?></th>
        </tr>
    </thead>
    <tbody>
<?php
	// Zur Datenbank verbinden ( Variablen in der verbindung.php definiert)
	global $pagenow;
	// Auswahl dea SQL-Statemants basierend auf dem was der User will
	// Wenn Suche genutzt wird
	if ($search != "") {
		$abfrage = "SELECT * FROM " . $teachpress_pub . "
					WHERE name like '%$search%' OR autor like '%$search%' OR verlag like '%$search%'
					ORDER BY sort DESC";	
	}
	// Standart
	else {
		$abfrage = "SELECT * FROM " . $teachpress_pub . " ORDER BY sort DESC";	
	}
	$test = tp_query($abfrage);
	// Test ob Eintraege vorhanden
	if ($test == 0) { ?>
        	<tr>
           	  <td colspan="9"><strong><?php _e('Sorry, no entries matched your criteria.','teachpress'); ?></strong></td>
            </tr>
    <?php }
	else {
		$row = tp_results($abfrage);
		foreach ($row as $row) { ?>
	 		<tr>
                <td style="font-size:20px; padding-top:0px; padding-bottom:0px; padding-right:0px;">
                <?php
                // Abfrage ob Publikation bereits in der eigenen Publikationsliste steht
                $abfrage = "SELECT pub_id FROM " . $teachpress_user . " WHERE pub_id='$row->pub_id' AND user = '$current_user->ID'";
                $test = tp_query($abfrage);
                if ($test == 0) {
                ?>
                 <a href="<?php echo '' . $pagenow . '?page=publikationen.php&add_id='. $row->pub_id . '&user=' . $current_user->ID . '&search=' . $search . '' ?>" title="<?php _e('Add to your own list','teachpress'); ?>">+</a>
                <?php } ?>
                </td>
                <td><input name="checkbox[]" type="checkbox" value="<?php echo"$row->pub_id" ?>" /></td>
            <td><a href="admin.php?page=teachpress/editpub.php&pub_ID=<?php echo"$row->pub_id" ?>&search=<?php echo "$search"?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo"$row->name" ?></a></td>
                <td><?php echo"$row->pub_id" ?></td>
                <td><?php echo"$row->typ" ?></td>
                <td><?php echo"$row->autor" ?></td>
                <td><?php echo"$row->verlag" ?></td>
                <td><?php echo"$row->jahr" ?></td>
                <td><?php echo"$row->isbn" ?></td>
	  		</tr>
	   <?php       
       }
   }
?>
</tbody>
</table>
</form>
</div>
<?php } ?>