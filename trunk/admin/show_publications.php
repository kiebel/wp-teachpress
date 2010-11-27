<?php
/* Show all publications / Show user's publications
 * from addpublications.php (GET):
 * @param $search (String)
*/  
function teachpress_publications_page() {

	// parameters for database
	global $wpdb;
	global $teachpress_pub; 
	global $teachpress_user;
	global $teachpress_relation;
	global $teachpress_tags;
	// WordPress User informations
	global $current_user;
	get_currentuserinfo();
	// parameters from show_publications.php
	$checkbox = $_GET[checkbox];
	$action = $_GET[action];
	$page = tp_sec_var($_GET[page]);
	$filter = tp_sec_var($_GET[filter]);
	$user = tp_sec_var($_GET[user], 'integer');
	$add_id = tp_sec_var($_GET[add_id], 'integer');
	$del_id = tp_sec_var($_GET[del_id], 'integer');
	$search = tp_sec_var($_GET[search]);
	$tag_id = tp_sec_var($_GET[tag], 'integer');
	// Page menu
	$number_messages = 50;
	// Handles limits 
	if (isset($_GET[limit])) {
		$entry_limit = (int)$_GET[limit];
		if ($entry_limit < 0) {
			$entry_limit = 0;
		}
	}
	else {
		$entry_limit = 0;
	}
	// test if teachpress database is up to date
	$test = tp_get_option('db-version');
	$version = get_tp_version();
	// if is the actual one
	if ($test != $version) {
		$message = __('An database update is necessary.','teachpress') . ' <a href="options-general.php?page=teachpress/settings.php&amp;up=1">' . __('Update','teachpress') . '</a>';
		tp_get_message($message, '');
	}
	// Add a bookmark for the publication
	if ($add_id != "") {
		tp_add_bookmark($add_id, $user);
	}
	// Delete bookmark for the publication
	if ($del_id != "") {
		tp_delete_bookmark($del_id);
	}
	?>
	<div class="wrap">
	<form id="showlvs" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
	<input type="hidden" name="page" id="page" value="<?php echo $page; ?>"/>
    <input type="hidden" name="limit" id="limit" value="<?php echo $entry_limit; ?>"/>
    <input type="hidden" name="filter" id="filter" value="<?php echo $filter; ?>" />
    <input type="hidden" name="search" id="search" value="<?php echo $search; ?>" />
	<?php
	
	// Delete publications - part 1
	if ( $action == "delete" ) {
		echo '<div class="teachpress_message">
			<p class="hilfe_headline">' . __('Are you sure to delete the selected courses?','teachpress') . '</p>
			<p><input name="delete_ok" type="submit" class="teachpress_button" value="' . __('delete','teachpress') . '"/>
			<a href="admin.php?page=publications.php&search=' . $search . '"> ' . __('cancel','teachpress') . '</a></p>
			</div>';
	}
	// delete publications - part 2
	if ( isset($_GET[delete_ok]) ) {
		tp_delete_publications($checkbox);
		$message = __('Publications deleted','teachpress');
		tp_get_message($message);
	}
	
	if ($page == 'publications.php' && $search == '') {
		$title = __('All publications','teachpress');
	}
	else {
		$title = __('Your publications','teachpress');
	}
	// For displaying as bibtex entries
	if ($action == 'bibtex') {
		echo '<p><a href="admin.php?page=' . $page . '&amp;search=' . $search . '&amp;limit=' . $entry_limit . '" class="teachpress_back">&larr; ' . __('back','teachpress') . '</a></p>';
		echo '<h2>' . __('BibTeX','teachpress') . '</h2>';
		echo '<textarea name="bibtex_area" rows="20" style="width:90%;">';
		for ($i=0; $i < count ($checkbox); $i++) {
			settype($checkbox[$i], 'integer');
			$row = $wpdb->get_results("SELECT * FROM " . $teachpress_pub . " WHERE pub_id = '$checkbox[$i]'");
			foreach ($row as $row) {
				echo tp_get_bibtex($row);
			}	
		}
		echo '</textarea>';
	}
	else {
		if ($page == 'publications.php') {
			if ($search != "") {
				$abfrage = "SELECT p.pub_id, p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year FROM " . $teachpress_pub . " p
						WHERE p.name like '%$search%' OR p.author like '%$search%' OR p.editor like '%$search%'
						ORDER BY date DESC";
			}
			elseif ($filter != ""&& $filter != '0') {
				$abfrage = "SELECT p.pub_id, p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year FROM " . $teachpress_pub . " p
						WHERE p.type = '$filter'
						ORDER BY date DESC";
			}
			elseif ($tag_id != "") {
				$abfrage = "SELECT DISTINCT p.pub_id,p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_tags . " t ON t.tag_id=b.tag_id
						WHERE b.tag_id = $tag_id
						ORDER BY p.date DESC";
			}
			else {
				$abfrage = "SELECT p.pub_id, p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year FROM " . $teachpress_pub . " p ORDER BY date DESC";
			}
		}
		else {
			if ($search != "") {
				$abfrage = "SELECT DISTINCT p.pub_id,p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year, u.bookmark_id 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
						WHERE u.user = '$current_user->ID' AND ( p.name like '%$search%' OR p.author like '%$search%' OR p.editor like '%$search%' )
						ORDER BY p.date DESC";
			}
			elseif ($filter != "" && $filter != '0') {
				$abfrage = "SELECT DISTINCT p.pub_id,p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year, u.bookmark_id 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
						WHERE u.user = '$current_user->ID' AND p.type = '$filter'
						ORDER BY p.date DESC";
			}
			elseif ($tag_id != "") {
				$abfrage = "SELECT DISTINCT p.pub_id,p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year, u.bookmark_id 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
						INNER JOIN " . $teachpress_tags . " t ON t.tag_id=b.tag_id
						WHERE u.user = '$current_user->ID' AND b.tag_id = $tag_id
						ORDER BY p.date DESC";
			}
			else {
				$abfrage = "SELECT DISTINCT p.pub_id, p.name, p.type, p.author, p.editor,  DATE_FORMAT(p.date, '%Y') AS year, u.bookmark_id 
				FROM " . $teachpress_relation . " b
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
				INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
				WHERE u.user = '$current_user->ID'
				ORDER BY p.date DESC";
			}
		}
		$test = $wpdb->query($abfrage);	
		$abfrage = $abfrage . " LIMIT $entry_limit, $number_messages";
		?>
		<h2><?php echo $title; ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
        <div id="hilfe_anzeigen">
			<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
				<p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
				<p class="hilfe_text"><?php _e('You can use publications in a page or article with the following shortcodes:','teachpress'); ?></p>
				<p class="hilfe_text"><?php _e('For a single publication:','teachpress'); ?> <strong><?php _e('[tpsingle id="u"]','teachpress'); ?></strong></p>
				 <ul style="list-style:disc; padding-left:40px;">
					<li><?php _e('id - ID of the publication','teachpress'); ?></li>
				</ul>
				<p class="hilfe_text"><?php _e('For a publication list with tag cloud:','teachpress'); ?> <strong><?php _e('[tpcloud id="u" maxsize="v" minsize="w" limit="x" image="y" image_size="z"]','teachpress'); ?></strong></p>
				 <ul style="list-style:disc; padding-left:40px;">
					<li><?php _e('id - WP User-ID (0 for all)','teachpress'); ?></li>
					<li><?php _e('maxsize - max. font size in the tag cloud (default: 35)','teachpress'); ?> </li>
					<li><?php _e('minsize - min. font size in the tag cloud (default: 11)','teachpress'); ?></li>
					<li><?php _e('limit - maximum of visible tags (default: 30)','teachpress'); ?></li>
					<li><?php _e('image - image position: left, right, bottom (default: none)','teachpress'); ?></li>
					<li><?php _e('image_size - maximum size in pixel (px) of an image (default: 0).','teachpress'); ?></li>
				</ul>
				<p class="hilfe_text"><?php _e('For normal publication lists:','teachpress'); ?> <strong><?php _e('[tplist user="u" tag="v" year="w" headline="x" image="y" image_size="z"]','teachpress'); ?></strong>
				<ul style="list-style:disc; padding-left:40px;">
					<li><?php _e('user - WP User-ID (0 for all)','teachpress'); ?></li>
					<li><?php _e('tag - Tag-ID (You can only choice one tag!)','teachpress'); ?> </li>
					<li><?php _e('year','teachpress'); ?></li>
					<li><?php _e('headline - 0(off) or 1(on)','teachpress'); ?></li>
					<li><?php _e('image - image position: left, right, bottom (default: none)','teachpress'); ?></li>
					<li><?php _e('image_size - maximum size in pixel (px) of an image (default: 0).','teachpress'); ?></li>
				</ul>
				</p>
				<p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
		</div>
		<div id="searchbox" style="float:right; padding-bottom:5px;">
        	<?php if ($search != "") { ?>
			<a href="admin.php?page=<?php echo $page; ?>" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="<?php _e('Cancel the search','teachpress'); ?>">X</a>
			<?php } ?>
			<input type="text" name="search" id="pub_search_field" value="<?php echo $search; ?>"/>
			<input type="submit" name="pub_search_button" id="pub_search_button" value="<?php _e('search','teachpress'); ?>" class="teachpress_button"/>
		</div>
		<div class="tablenav" style="padding-bottom:5px;">
		<select name="action">
			<option value="0">- <?php _e('Bulk actions','teachpress'); ?> -</option>
			<option value="bibtex"><?php _e('Show as BibTeX entry','teachpress'); ?></option>
			<?php if ($page == 'publications.php') {?>
			<option value="delete"><?php _e('delete','teachpress'); ?></option>
			<?php } ?>
		</select>
		<input name="ok" value="<?php _e('ok','teachpress'); ?>" type="submit" class="teachpress_button"/>
		<select name="filter">
			<option value="0">- <?php _e('All types','teachpress'); ?> -</option>
			<?php echo get_tp_publication_type_options ($filter, $mode = 'list'); ?>
		</select>
		<input name="filter-ok" value="<?php _e('Limit selection','teachpress'); ?>" type="submit" class="teachpress_button"/>
         <?php
		// Page Menu
		if ($test > $number_messages) {
			$num_pages = floor (($test / $number_messages) + 1);
			// previous page link
			if ($entry_limit != 0) {
				$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ($entry_limit - $number_messages) . '&amp;search=' . $search . '" title="' . __('previous page','teachpress') . '" class="page-numbers">&larr;</a> ';
			}	
			// page numbers
			$akt_seite = $entry_limit + $number_messages;
			for($i=1; $i <= $num_pages; $i++) { 
				$s = $i * $number_messages;
				// First and last page
				if ( ($i == 1 && $s != $akt_seite ) || ($i == $num_pages && $s != $akt_seite ) ) {
					$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ( $s - $number_messages) . '&amp;search=' . $search . '" title="' . __('Page','teachpress') . ' ' . $i . '" class="page-numbers">' . $i . '</a> ';
				}
				// current page
				elseif ( $s == $akt_seite ) {
					$all_pages = $all_pages . '<span class="page-numbers current">' . $i . '</span> ';
				}
				else {
					// Placeholder before
					if ( $s == $akt_seite - (2 * $number_messages) && $num_pages > 4 ) {
						$all_pages = $all_pages . '... ';
					}
					// Normal page
					if ( $s >= $akt_seite - (2 * $number_messages) && $s <= $akt_seite + (2 * $number_messages) ) {
						$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ( ( $i * $number_messages ) - $number_messages) . '&amp;search=' . $search . '" title="' . __('Page','teachpress') . ' ' . $i . '" class="page-numbers">' . $i . '</a> ';
					}
					// Placeholder after
					if ( $s == $akt_seite + (2 * $number_messages) && $num_pages > 4 ) {
						$all_pages = $all_pages . '... ';
					}
				}
			}
			// next page link
			if ( ( $entry_limit + $number_messages ) <= ($test)) { 
				$all_pages = $all_pages . '<a href="admin.php?page=' . $page . '&amp;limit=' . ($entry_limit + $number_messages) . '&amp;author=' . $author . '&amp;search=' . $search . '&amp;tag=' . $tag . '" title="' . __('next page','teachpress') . '" class="page-numbers">&rarr;</a> ';
			}
			// handle displaying entry number
			if ($akt_seite - 1 > $test) {
				$anz2 = $test;
			}
			else {
				$anz2 = $akt_seite - 1;
			}
			// print menu
			echo '<div class="tablenav-pages" style="float:right;">' . __('Displaying','teachpress') . ' ' . ($entry_limit + 1) . '-' . $anz2 . ' of ' . $test . ' ' . $all_pages . '</div>';
		}?>
		</div>
		<table class="widefat">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th><?php _e('Name','teachpress'); ?></th>
					<th><?php _e('ID','teachpress'); ?></th>
					<th><?php _e('Type','teachpress'); ?></th> 
					<th><?php _e('Author(s)','teachpress'); ?></th>
					<th><?php _e('Tags','teachpress'); ?></th>
					<th><?php _e('Year','teachpress'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($test == 0) {
				echo '<tr><td colspan="7"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</strong></td></tr>';
			}
			else {
				$row = $wpdb->get_results($abfrage);
				foreach ($row as $row) { ?>
					<tr>
						<td style="font-size:20px; padding-top:0px; padding-bottom:0px; padding-right:0px;">
						<?php
						// check if the publication is already in users publication list
						$sql = "SELECT bookmark_id FROM " . $teachpress_user . " WHERE pub_id='$row->pub_id' AND user = '$current_user->ID'";
						$test2 = $wpdb->query($sql);
						if ($page == 'publications.php') {
							// Add to your own list icon
							if ($test2 == 0) {
								echo '<a href="' . $pagenow . '?page=' . $page . '&amp;add_id='. $row->pub_id . '&amp;user=' . $current_user->ID . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $entry_limit . '" title="' . __('Add to your own list','teachpress') . '">+</a>';
							} 
						}
						else {
							$bookmark = $wpdb->get_var($sql);
							// Delete from your own list icon
							echo '<a href="' . $pagenow . '?page=' . $page .'&amp;del_id='. $bookmark . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $entry_limit . '" title="' . __('Delete from you own publication list','teachpress') . '">&laquo;</a>';
						} ?>
						</td>
						<?php 
						if ( $action == "delete") { 
							$checked = '';
							for( $k = 0; $k < count( $checkbox ); $k++ ) { 
								if ( $row->pub_id == $checkbox[$k] ) { $checked = 'checked="checked" '; } 
							} 
						}
						echo '<td><input style="margin-left:8px; padding-left:7px; text-align:left;" name="checkbox[]" type="checkbox" ' . $checked . 'value="' . $row->pub_id . '" /></td>';
						?>
						<td><a href="admin.php?page=teachpress/addpublications.php&pub_ID=<?php echo $row->pub_id; ?>&amp;search=<?php echo $search; ?>&amp;filter=<?php echo $filter; ?>&amp;limit=<?php echo $entry_limit; ?>&amp;site=<?php echo $page; ?>" class="teachpress_link" title="<?php _e('Click to edit','teachpress'); ?>"><?php echo $row->name; ?></a></td>
						<td><?php echo $row->pub_id; ?></td>
						<td><?php _e('' . $row->type . '','teachpress'); ?></td>
						<td><?php echo str_replace(' and ', ', ', $row->author); ?></td>
						<td>
						<?php
						// Tags
						$sql = "SELECT name, tag_id, pub_id FROM (SELECT t.name AS name, t.tag_id AS tag_id, b.pub_id AS pub_id FROM " . $teachpress_tags . " t LEFT JOIN " . $teachpress_relation . " b ON t.tag_id = b.tag_id ) as temp";
						$temp = $wpdb->get_results($sql);
						$atag = 0;
						foreach ($temp as $temp) {
							$all_tags[$atag][0] = $temp->name;
							$all_tags[$atag][1] = $temp->tag_id;
							$all_tags[$atag][2] = $temp->pub_id;
							$atag++;
						}
						$tag_string = '';
						for ($i = 0; $i < $atag; $i++) {
							if ($all_tags[$i][2] == $row->pub_id) {
								$tag_string = $tag_string . '<a href="admin.php?page=' . $page . '&amp;tag=' . $all_tags[$i][1] . '" title="' . __('Show all publications which have a relationship to this tag','teachpress') . '">' . $all_tags[$i][0] . '</a>, ';
							}
						}
						$tag_string = substr($tag_string, 0, -2);
						echo $tag_string;
						?></td>
						<td><?php echo $row->year; ?></td>
					</tr>
			   <?php       
			   }
			}
			?>
			</tbody>
		</table>
        <div class="tablenav"><div class="tablenav-pages" style="float:right;">
		<?php 
		if ($test > $number_messages) {
			echo __('Displaying','teachpress') . ' ' . ($entry_limit + 1) . '-' . $anz2 . ' ' . __('of','teachpress') . ' ' . $test . ' ' . $all_pages . '';
		} 
		else {
			echo __('Displaying','teachpress') . ' ' . $test . ' ' . __('entries','teachpress') . ' '. $all_pages . '';
		}?>
		</div></div>
	<?php } ?>
	</form>
	</div>
<?php } ?>