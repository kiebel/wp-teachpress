<?php
/* Show all publications / Show user's publications
 * from addpublications.php (GET):
 * @param $search (String)
*/  
function teachpress_publications_page() { 

// parameters for database
global $teachpress_pub; 
global $teachpress_user;
global $teachpress_beziehung;
global $teachpress_tags;
// WordPress User informations
global $current_user;
get_currentuserinfo();
// parameters from showpublications.php
$checkbox = $_GET[checkbox];
$action = $_GET[action];
$page = tp_sec_var($_GET[page]);
$user = tp_sec_var($_GET[user], 'integer');
$add_id = tp_sec_var($_GET[add_id], 'integer');
$del_id = tp_sec_var($_GET[del_id], 'integer');
$search = tp_sec_var($_GET[search]);
// Add a bookmark for the publication
if ($add_id != "") {
	tp_add_bookmark($add_id, $user);
}
// Delete bookmark for the publication
if ($del_id !="") {
	tp_delete_bookmark($del_id);
}
// Delete publications
if ( $action == "delete" ) {
	tp_delete_publications($checkbox);
	$message = __('Publications deleted','teachpress');
	$site = 'admin.php?page=publications.php';
	tp_get_message($message, $site);
}
?>
<div class="wrap" style="padding-top:10px;">
<form id="showlvs" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>"/>
<table border="0" cellspacing="0" cellpadding="5" style="float:right;">
  <tr>
    <td><?php if ($search != "") { ?><a href="admin.php?page=<?php echo $page; ?>" style="font-size:20px; font-weight:bold; text-decoration:none;" title="<?php _e('Cancel the search','teachpress'); ?>">&crarr;</a><?php } ?></td>
    <td><input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/></td>
    <td><input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/></td>
  </tr>
</table>
<?php
if ($page == 'publications.php') {
	$title = __('All publications','teachpress');
}
else {
	$title = __('Your publications','teachpress');
}	
?>
<h2><?php echo $title; ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
<?php
if ($page == 'publications.php') {?>
<div style="padding-bottom:5px;">
<select name="action">
    <option value="0">- <?php _e('Bulk actions','teachpress'); ?> -</option>
    <option value="delete"><?php _e('delete','teachpress'); ?></option>
</select>
<input name="ok" value="ok" type="submit" class="teachpress_button"/>
</div>
<?php } ?>
<div id="hilfe_anzeigen">
    <h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('For a publication list with tag cloud:','teachpress'); ?> <strong><?php _e('[tpcloud id="u" maxsize="v" minsize="w" limit="x" image="y" image_size="z"]','teachpress'); ?></strong></p>
         <ul style="list-style:disc; padding-left:40px;">
        	<li><?php _e('id - WP User-ID (0 for all)','teachpress'); ?></li>
            <li><?php _e('maxsize - max. font size in the tag cloud (default: 35)','teachpress'); ?> </li>
            <li><?php _e('minsize - min. font size in the tag cloud (default: 11)','teachpress'); ?></li>
            <li><?php _e('limit - maximum of visible tags (default: 30)','teachpress'); ?></li>
            <li><?php _e('image - image position: left, right, bottom (default: none)','teachpress'); ?></li>
            <li><?php _e('image_size - maximum size in pixel (px) of an image (default: 0). ','teachpress'); ?></li>
        </ul>
        <p class="hilfe_text"><?php _e('For normal publication lists:','teachpress'); ?> <strong><?php _e('[tplist user="u" tag="v" year="w" headline="x" image="y" image_size="z"]','teachpress'); ?></strong>
        <ul style="list-style:disc; padding-left:40px;">
        	<li><?php _e('user - WP User-ID (0 for all)','teachpress'); ?></li>
            <li><?php _e('tag - Tag-ID (You can only choice one tag!)','teachpress'); ?> </li>
            <li><?php _e('year','teachpress'); ?></li>
            <li><?php _e('headline - 0(off) or 1(on)','teachpress'); ?></li>
            <li><?php _e('image - image position: left, right, bottom (default: none)','teachpress'); ?></li>
            <li><?php _e('image_size - maximum size in pixel (px) of an image (default: 0). ','teachpress'); ?></li>
        </ul>
        </p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
</div>
<table cellpadding="5" cellspacing="0" border="1" class="widefat">
	<thead>
        <tr>
        	<th>&nbsp;</th>
            <?php if ($page == 'publications.php') {?>
            <th>&nbsp;</th>
            <?php } ?>
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
	if ($page == 'publications.php') {
		if ($search != "") {
			$abfrage = "SELECT * FROM " . $teachpress_pub . "
					WHERE name like '%$search%' OR autor like '%$search%' OR verlag like '%$search%'
					ORDER BY sort DESC";
		}
		else {
			$abfrage = "SELECT * FROM " . $teachpress_pub . " ORDER BY sort DESC";
		}
	}
	else {
		if ($search != "") {
			$abfrage = "SELECT DISTINCT " . $teachpress_pub . ".pub_id," . $teachpress_pub . ".name, " . $teachpress_pub . ".typ, " . $teachpress_pub . ".autor, " . $teachpress_pub . ".verlag,  " . $teachpress_pub . ".jahr, " . $teachpress_pub . ".isbn, " . $teachpress_pub . ".url, " . $teachpress_user . ".bookmark_id 
					FROM " . $teachpress_beziehung . "
					INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
					INNER JOIN " . $teachpress_user . " ON " . $teachpress_user . ".pub_id=" . $teachpress_pub . ".pub_id
					WHERE " . $teachpress_user . ".user = '$current_user->ID' AND ( " . $teachpress_pub . ".name like '%$search%' OR " . $teachpress_pub . ".autor like '%$search%' OR " . $teachpress_pub . ".verlag like '%$search%' )
					ORDER BY " . $teachpress_pub . ".sort DESC";
		}
		else {
			$abfrage = "SELECT DISTINCT " . $teachpress_pub . ".pub_id," . $teachpress_pub . ".name, " . $teachpress_pub . ".typ, " . $teachpress_pub . ".autor, " . $teachpress_pub . ".verlag,  " . $teachpress_pub . ".jahr, " . $teachpress_pub . ".isbn, " . $teachpress_pub . ".url, " . $teachpress_user . ".bookmark_id 
			FROM " . $teachpress_beziehung . "
			INNER JOIN " . $teachpress_pub . " ON " . $teachpress_pub . ".pub_id=" . $teachpress_beziehung . ".pub_id
			INNER JOIN " . $teachpress_user . " ON " . $teachpress_user . ".pub_id=" . $teachpress_pub . ".pub_id
			WHERE " . $teachpress_user . ".user = '$current_user->ID'
			ORDER BY " . $teachpress_pub . ".sort DESC";
		}
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
	$row = tp_results($abfrage);
		foreach ($row as $row) { ?>
	 		<tr>
                <td style="font-size:20px; padding-top:0px; padding-bottom:0px; padding-right:0px;">
                <?php
				if ($page == 'publications.php') {
                // Abfrage ob Publikation bereits in der eigenen Publikationsliste steht
                $abfrage = "SELECT pub_id FROM " . $teachpress_user . " WHERE pub_id='$row->pub_id' AND user = '$current_user->ID'";
                $test = tp_query($abfrage);
                if ($test == 0) {?>
                <a href="<?php echo '' . $pagenow . '?page=' . $page . '&amp;add_id='. $row->pub_id . '&amp;user=' . $current_user->ID . '&amp;search=' . $search . '' ?>" title="<?php _e('Add to your own list','teachpress'); ?>">+</a>
                <?php } 
				}
				else {?>
				<a href="<?php echo '' . $pagenow . '?page=' . $page .'&amp;del_id='. $row2->bookmark_id . '&amp;search=' . $search . '' ?>" title="<?php _e('Delete from you own publication list','teachpress'); ?>">&laquo;</a>
                <?php } ?>
                </td>
                <?php if ($page == 'publications.php') {?>
                <td><input name="checkbox[]" type="checkbox" value="<?php echo"$row->pub_id" ?>" /></td>
				<?php }?>
                <td><a href="admin.php?page=teachpress/addpublications.php&pub_ID=<?php echo $row->pub_id; ?>&amp;search=<?php echo $search; ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo"$row->name" ?></a></td>
                <td><?php echo"$row->pub_id" ?></td>
                <td><?php _e('' . $row->typ . '','teachpress'); ?></td>
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