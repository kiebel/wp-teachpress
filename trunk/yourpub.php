<?php 
/* Uebersicht eigene Publikationen
 * from editlvs.php
 * @param $search (String)
*/
?>

<?php 
if ( is_user_logged_in() ) { 
?> 

<div class="wrap" style="padding-top:10px;">
  <form id="showlvs" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
  <input type="hidden" name="page" id="page" value="teachpress/yourpub.php"/>
<?php
global $teachpress_pub; 
global $teachpress_user;
global $teachpress_beziehung;
global $teachpress_tags;
// User Infos von WordPress
global $current_user;
get_currentuserinfo();
// Get-Variablen holen und auswerten
$user = htmlentities(utf8_decode($_GET[user]));
$del_id = htmlentities(utf8_decode($_GET[del_id]));
settype($user, 'integer');
settype($del_id, 'integer');
// Daten von editpub.php oder yourpub.php
$search = htmlentities(utf8_decode($_GET[search]));

// Wenn Bookmark geloescht werden soll
if ($del_id !="") {
	del_bookmark($del_id);
}
?>
<table border="0" cellspacing="0" cellpadding="5" style="float:right;">
  <tr>
    <td><?php if ($search != "") { ?><a href="admin.php?page=teachpress/yourpub.php" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Cancel the search','teachpress'); ?>">&crarr;</a><?php } ?></td>
    <td><input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/></td>
    <td><input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/></td>
  </tr>
</table>  
<h2><?php _e('Your publications','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
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
<table border="1" cellspacing="0" cellpadding="6" class="widefat">
  <thead>
  <tr>
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
	global $pagenow;
	// Auswahl dea SQL-Statemants basierend auf dem was der User will
	// Wenn Suche genutzt wird
	if ($search != "") {
		$abfrage = "SELECT DISTINCT " . $teachpress_pub . ".pub_id," . $teachpress_pub . ".name, " . $teachpress_pub . ".typ, " . $teachpress_pub . ".autor, " . $teachpress_pub . ".verlag,  " . $teachpress_pub . ".jahr, " . $teachpress_pub . ".isbn, " . $teachpress_pub . ".url, " . $teachpress_user . ".bookmark_id 
					FROM " . $teachpress_beziehung . "
					INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
					INNER JOIN " . $teachpress_user . " ON " . $teachpress_user . ".pub_id=" . $teachpress_pub . ".pub_id
					WHERE " . $teachpress_user . ".user = '$current_user->ID' AND ( " . $teachpress_pub . ".name like '%$search%' OR " . $teachpress_pub . ".autor like '%$search%' OR " . $teachpress_pub . ".verlag like '%$search%' )
					ORDER BY " . $teachpress_pub . ".sort DESC";	
	}
	// Standart
	else {
  	$abfrage = "SELECT DISTINCT " . $teachpress_pub . ".pub_id," . $teachpress_pub . ".name, " . $teachpress_pub . ".typ, " . $teachpress_pub . ".autor, " . $teachpress_pub . ".verlag,  " . $teachpress_pub . ".jahr, " . $teachpress_pub . ".isbn, " . $teachpress_pub . ".url, " . $teachpress_user . ".bookmark_id 
			FROM " . $teachpress_beziehung . "
			INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
			INNER JOIN " . $teachpress_user . " ON " . $teachpress_user . ".pub_id=" . $teachpress_pub . ".pub_id
			WHERE " . $teachpress_user . ".user = '$current_user->ID'
			ORDER BY " . $teachpress_pub . ".sort DESC";
	}				
	$test = tp_query($abfrage);
	// Test ob Eintraege vorhanden
	if ($test == 0) {
		?>
        	<tr>
           	  <td colspan="8"><strong><?php _e('Sorry, no entries matched your criteria.','teachpress'); ?></strong></td>
            </tr>
        <?php
	}
	else {
		$row2 = tp_results($abfrage);
		foreach ($row2 as $row2) { ?>
          <tr>
            <td style="font-size:20px; padding-top:0px; padding-bottom:0px; padding-right:0px;"><a href="<?php echo '' . $pagenow . '?page=teachpress/yourpub.php&del_id='. $row2->bookmark_id . '&search=' . $search . '' ?>" title="<?php _e('Delete from you own publication list','teachpress'); ?>">&laquo;</a></td>
            <td><a href="admin.php?page=teachpress/editpub.php&pub_ID=<?php echo"$row2->pub_id" ?>&search=<?php echo "$search"?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo"$row2->name" ?></a></td>
            <td><?php echo"$row2->pub_id" ?></td>
            <td><?php echo"$row2->typ" ?></td>
            <td><?php echo"$row2->autor" ?></td>
            <td><?php echo"$row2->verlag" ?></td>
            <td><?php echo"$row2->jahr" ?></td>
            <td><?php echo"$row2->isbn" ?></td>
          </tr>
  		<?php }
 	} ?>
  </tbody>
</table>
</form>
</div>
<?php  } ?>