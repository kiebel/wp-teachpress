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
	// Handle limits
	if (isset($_GET[limit])) {
		$curr_page = (int)$_GET[limit] ;
		if ( $curr_page <= 0 ) {
			$curr_page = 1;
		}
		$entry_limit = ( $curr_page - 1 ) * $number_messages;
	}
	else {
		$entry_limit = 0;
		$curr_page = 1;
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
	<input type="hidden" name="page" id="page" value="<?php echo $page; ?>" />
    <input type="hidden" name="tag" id="tag" value="<?php echo $tag_id; ?>" />
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
		echo '<p><a href="admin.php?page=' . $page . '&amp;search=' . $search . '&amp;limit=' . $curr_page . '" class="teachpress_back">&larr; ' . __('back','teachpress') . '</a></p>';
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
		// Build SQL-Statements
		$order = "ORDER BY date DESC, p.name ASC";
		$select = "p.pub_id, p.name, p.type, p.author, p.editor, DATE_FORMAT(p.date, '%Y') AS year";
		
		if ($filter != "" && $filter != '0') {
			$where = "( p.type = '$filter' ) AND ";
		}
		else {
			$where = "";
		}
		if ($search != "") {
			$where = $where . "( p.name like '%$search%' OR p.author like '%$search%' OR p.editor like '%$search%' ) AND ";
		}
		else {
			$where = $where . "";
		}
		
		if ($page == 'publications.php') {
			if ($where != "" && $tag_id == 0) {
				$abfrage = "SELECT " . $select . " FROM " . $teachpress_pub . " p
						WHERE " . substr($where, 0, -4) . "
						" . $order . "";
			}
			elseif ($tag_id != 0) {
				$abfrage = "SELECT DISTINCT " . $select . " 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_tags . " t ON t.tag_id=b.tag_id
						WHERE " . $where . " b.tag_id = $tag_id
						" . $order . "";
			}
			else {
				$abfrage = "SELECT " . $select . " FROM " . $teachpress_pub . " p " . $order . "";
			}
		}
		else {
			if ($where != "" && $tag_id == 0) {
				$abfrage = "SELECT DISTINCT " . $select . ", u.bookmark_id 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
						WHERE u.user = '$current_user->ID' AND " . substr($where, 0, -4) . "
						" . $order . "";
			}
			elseif ($tag_id != 0) {
				$abfrage = "SELECT DISTINCT " . $select . ", u.bookmark_id 
						FROM " . $teachpress_relation . " b
						INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
						INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
						INNER JOIN " . $teachpress_tags . " t ON t.tag_id=b.tag_id
						WHERE u.user = '$current_user->ID' AND " . $where . " b.tag_id = $tag_id
						" . $order . "";
			}
			else {
				$abfrage = "SELECT DISTINCT " . $select . ", u.bookmark_id 
				FROM " . $teachpress_relation . " b
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
				INNER JOIN " . $teachpress_user . " u ON u.pub_id=p.pub_id
				WHERE u.user = '$current_user->ID'
				" . $order . "";
			}
		}
		$test = $wpdb->query($abfrage);	
		$abfrage = $abfrage . " LIMIT $entry_limit, $number_messages";
		// Load tags
		$sql = "SELECT DISTINCT t.name, b.tag_id, b.pub_id FROM " . $teachpress_relation . " b
				INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id";
		$tags = $wpdb->get_results($sql, ARRAY_A);
		?>
		<h2><?php echo $title; ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
        <div id="hilfe_anzeigen">
			<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
				<p class="hilfe_headline"><?php _e('Shortcodes','teachpress'); ?></p>
				<p class="hilfe_text"><?php _e('You can use publications in a page or article with the following shortcodes:','teachpress'); ?></p>
				<p class="hilfe_text"><?php _e('For a single publication:','teachpress'); ?> <strong>[tpsingle]</strong></p>
				<p class="hilfe_text"><?php _e('For a publication list with tag cloud:','teachpress'); ?> <strong>[tpcloud]</strong></p>
				<p class="hilfe_text"><?php _e('For normal publication lists:','teachpress'); ?> <strong>[tplist]</strong>
				</p>
                <p class="hilfe_headline"><?php _e('More information','teachpress'); ?></p>
                <p class="hilfe_text"><a href="http://mtrv.wordpress.com/teachpress/shortcode-reference/" target="_blank" title="<?php _e('teachPress Shortcode Reference (engl.)', 'teachpress') ?>"><?php _e('teachPress Shortcode Reference (engl.)', 'teachpress') ?></a></p>
				<p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
		</div>
		<div id="searchbox" style="float:right; padding-bottom:5px;">
        	<?php if ($search != "") { 
				echo '<a href="admin.php?page=' . $page . '&amp;filter=' . $filter . '&amp;tag=' . $tag_id . '" style="font-size:14px; font-weight:bold; text-decoration:none; padding-right:3px;" title="' . __('Cancel the search','teachpress') . '">X</a>';
			} ?>
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
		echo tp_admin_page_menu ($test, $number_messages, $curr_page, $entry_limit, 'admin.php?page=' . $page . '', 'search=' . $search . '&amp;filter=' . $filter . '&amp;tag=' . $tag_id . ''); ?>
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
								echo '<a href="' . $pagenow . '?page=' . $page . '&amp;add_id='. $row->pub_id . '&amp;user=' . $current_user->ID . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $curr_page . '&amp;tag=' . $tag_id . '" title="' . __('Add to your own list','teachpress') . '">+</a>';
							} 
						}
						else {
							$bookmark = $wpdb->get_var($sql);
							// Delete from your own list icon
							echo '<a href="' . $pagenow . '?page=' . $page .'&amp;del_id='. $bookmark . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $curr_page . '&amp;tag=' . $tag_id . '" title="' . __('Delete from you own publication list','teachpress') . '">&laquo;</a>';
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
						<td><?php echo '<a href="admin.php?page=teachpress/addpublications.php&amp;pub_ID=' . $row->pub_id . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $curr_page . '&amp;site=' . $page . '&amp;tag=' . $tag_id . '" class="teachpress_link" title="' . __('Click to edit','teachpress') . '"><strong>' . stripslashes($row->name) . '</strong></a>'; ?></td>
						<td><?php echo $row->pub_id; ?></td>
						<td><?php _e('' . $row->type . '','teachpress'); ?></td>
						<td><?php echo stripslashes( str_replace(' and ', ', ', $row->author) ); ?></td>
						<td>
						<?php
						// Tags
						$tag_string = '';
						foreach ($tags as $temp) {
							if ($temp["pub_id"] == $row->pub_id) {
								if ($temp["tag_id"] == $tag_id) {
									$tag_string = $tag_string . '<a href="admin.php?page=' . $page . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;limit=' . $curr_page . '" title="' . __('Delete tag as filter','teachpress') . '"><strong>' . stripslashes($temp["name"]) . '</strong></a>, ';
								}
								else {
									$tag_string = $tag_string . '<a href="admin.php?page=' . $page . '&amp;search=' . $search . '&amp;filter=' . $filter . '&amp;tag=' . $temp["tag_id"] . '" title="' . __('Show all publications which have a relationship to this tag','teachpress') . '">' . stripslashes($temp["name"]) . '</a>, ';
								}
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
			echo tp_admin_page_menu ($test, $number_messages, $curr_page, $entry_limit, 'admin.php?page=' . $page . '', 'search=' . $search . '&amp;filter=' . $filter . '&amp;tag=' . $tag_id . '', 'bottom');
		} 
		else {
			if ($test == 1) {
				echo '' . $test . ' ' . __('entry','teachpress') . '';
			}
			else {
				echo '' . $test . ' ' . __('entries','teachpress') . '';
			}
		}?>
		</div></div>
	<?php } ?>
	</form>
	</div>
<?php } ?>