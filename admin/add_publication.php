<?php  
/* New publication / edit publication
 * from show_publications.php (GET):
 * @param $pub_ID (INT) - publication ID
 * @param $search (String) - for a return to the search
 * @param $filter (String) - for a return to the search
*/
function teachpress_addpublications_page() {

	global $teachpress_pub; 
	global $teachpress_relation; 
	global $teachpress_tags;
	global $teachpress_user;
	global $pagenow;
	global $current_user;
	global $wpdb;
	// WordPress current unser info
	get_currentuserinfo();
	$user = $current_user->ID;
	
	// form variables from add_publication.php
	$data['name'] = tp_sec_var($_GET[post_title]);
	$data['type'] = tp_sec_var($_GET[type]);
	$data['bibtex'] = tp_sec_var($_GET[bibtex]);
	$data['author'] = tp_sec_var($_GET[author]);
	$data['editor'] = tp_sec_var($_GET[editor]);
	$data['isbn'] = tp_sec_var($_GET[isbn]);
	$data['url'] = tp_sec_var($_GET[url]);
	$data['date'] = tp_sec_var($_GET[date]);
	$data['booktitle'] = tp_sec_var($_GET[booktitle]);
	$data['journal'] = tp_sec_var($_GET[journal]);
	$data['volume'] = tp_sec_var($_GET[volume]);
	$data['number'] = tp_sec_var($_GET[number]);
	$data['pages'] = tp_sec_var($_GET[pages]);
	$data['publisher'] = tp_sec_var($_GET[publisher]);
	$data['address'] = tp_sec_var($_GET[address]);
	$data['edition'] = tp_sec_var($_GET[edition]);
	$data['chapter'] = tp_sec_var($_GET[chapter]);
	$data['institution'] = tp_sec_var($_GET[institution]);
	$data['organization'] = tp_sec_var($_GET[organization]);
	$data['school'] = tp_sec_var($_GET[school]);
	$data['series'] = tp_sec_var($_GET[series]);
	$data['crossref'] = tp_sec_var($_GET[crossref]);
	$data['abstract'] = tp_sec_var($_GET[abstrac]);
	$data['howpublished'] = tp_sec_var($_GET[howpublished]);
	$data['key'] = tp_sec_var($_GET[key]);
	$data['techtype'] = tp_sec_var($_GET[techtype]);
	$data['comment'] = tp_sec_var($_GET[comment]);
	$data['note'] = tp_sec_var($_GET[note]);
	$data['image_url'] = tp_sec_var($_GET[image_url]);
	$data['rel_page'] = tp_sec_var($_GET[rel_page], 'integer');
	$data['is_isbn'] = tp_sec_var($_GET[is_isbn], 'integer');
		
	$tags = tp_sec_var($_GET[tags]);
	$delbox = $_GET[delbox];
	$erstellen = $_GET[erstellen];
	$bookmark = $_GET[bookmark];
	$speichern = $_GET[speichern];
	
	// from show_publications.php
	$pub_ID = tp_sec_var($_GET[pub_ID], 'integer');
	$search = tp_sec_var($_GET[search]);
	$filter = tp_sec_var($_GET[filter]);
	$site = tp_sec_var($_GET[site]);
	$entry_limit = tp_sec_var($_GET[limit]);
	
	?>
	<div class="wrap">
	<form name="form1" method="get" action="<?php echo $PHP_SELF ?>" id="form1">
	<?php
	// if publications was created
	if (isset($erstellen)) {
		$pub_ID = tp_add_publication($data, $tags, $bookmark);
		$message = __('Publication added','teachpress') . ' <a href="admin.php?page=teachpress/addpublications.php">' . __('Add new','teachpress') . '</a>';
		tp_get_message($message);
	}
	// if publication was saved
	if (isset($speichern)) {
		tp_change_publication($pub_ID, $data, $bookmark, $delbox, $tags);
		$message = __('Publication changed','teachpress');
		tp_get_message($message);
	}
	
	if ($pub_ID != '') {?>
    <p style="margin-bottom:0px;"><a href="admin.php?page=<?php echo $site; ?>&amp;search=<?php echo $search; ?>&amp;filter=<?php echo $filter; ?>&amp;limit=<?php echo $entry_limit; ?>" class="teachpress_back" title="<?php _e('back','teachpress'); ?>">&larr; <?php _e("back",'teachpress'); ?></a></p>
    <?php } ?>
	<h2><?php if ($pub_ID == '') { _e('Add a new publication','teachpress'); } else { _e('Edit publication','teachpress'); } ?><span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2>
	<div id="hilfe_anzeigen">
		<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
		<p><?php _e('No text available.','teachpress'); ?></p>
		
		<p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
	</div>
	  <input name="page" type="hidden" value="teachpress/addpublications.php">
	  <?php if ($pub_ID != '') { 
	  $row = "SELECT * FROM " . $teachpress_pub . " WHERE pub_id = '$pub_ID'";
	  $daten = $wpdb->get_row($row, ARRAY_A)
	  ?>
	  <input type="hidden" name="pub_ID" value="<?php echo $pub_ID; ?>">
	  <input type="hidden" name="search" value="<?php echo $search; ?>">
      <input type="hidden" name="limit" id="limit" value="<?php echo $entry_limit; ?>"/>
      <input type="hidden" name="site" id="site" value="<?php echo $site; ?>"/>
      <input type="hidden" name="filter" id="filter" value="<?php echo $filter; ?>"/>
	  <?php } ?>
	  <div style="min-width:780px; width:100%; max-width:1100px;">
	  <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">
	  <table class="widefat">
		<thead>
		<tr>
			<th><strong><?php _e('Publication','teachpress'); ?></strong></th>
		</tr>
		<tr>
		  <td>
		  <p><label for="bookmark" title="<?php _e('Add a publication to different publication lists','teachpress'); ?>"><strong><?php _e('Bookmarks','teachpress'); ?></strong></label></p>
		  <div class="bookmarks" style="background-attachment: scroll; border:1px #DFDFDF solid; display: block; height: 100px; max-height: 205px; overflow-x: auto; overflow-y: auto; padding: 6px 11px;">
          <?php 
		  if ($pub_ID != '') {
		  	$sql = "SELECT pub_id FROM " . $teachpress_user . " WHERE pub_id='$pub_ID' AND user = '$user'";
			$test = $wpdb->query($sql);
			if ($test == 1) {
				echo '<p><input type="checkbox" name="bookmark[]" id="bookmark" disabled="disabled"/> <label for="bookmark">' . __('add to your own list','teachpress') . '</label></p>';
			}
			else {
				echo '<p><input type="checkbox" name="bookmark[]" id="bookmark" value="' . $user . '" title="' . __('Click to add the publication in your own list','teachpress') . '"/> <label for="bookmark" title="' . __('Click to add the publication in your own list','teachpress') . '">' . __('add to your own list','teachpress') . '</label></p>';
			}
		  }	
		  else {
				echo '<p><input type="checkbox" name="bookmark[]" id="bookmark" value="' . $user . '" title="' . __('Click to add the publication in your own list','teachpress') . '"/> <label for="bookmark" title="' . __('Click to add the publication in your own list','teachpress') . '">' . __('add to your own list','teachpress') . '</label></p>';
			}
		   // search users with min. one bookmark
		   $abfrage = "SELECT DISTINCT user FROM " . $teachpress_user . "";
		   $row = $wpdb->get_results($abfrage);
		   foreach($row as $row) {
			  if ($user != $row->user) { 
				  $user_info = get_userdata($row->user);
				  if ($pub_ID != '') {
					  $sql = "SELECT pub_id FROM " . $teachpress_user . " WHERE pub_id='$pub_ID' AND user = '$user_info->ID'";
					  $test = $wpdb->query($sql);
					  if ($test == 1) {
						 echo '<p><input type="checkbox" name="bookmark[]" id="bookmark_' . $user_info->ID . '"/> <label for="bookmark_' . $user_info->ID . '">' . $user_info->display_name . '</label></p>';
					  }
					  else {
					  	echo '<p><input type="checkbox" name="bookmark[]" id="bookmark_' . $user_info->ID . '" value="' . $user_info->ID . '" title="' . __('Bookmark for','teachpress') . ' ' . $user_info->display_name . '"/> <label for="bookmark_' . $user_info->ID . '" title="' . __('Bookmark for','teachpress') . ' ' . $user_info->display_name . '">' . $user_info->display_name . '</label></p>';
					  }
				  }
				  else {
				  	echo '<p><input type="checkbox" name="bookmark[]" id="bookmark_' . $user_info->ID . '" value="' . $user_info->ID . '" title="' . __('Bookmark for','teachpress') . ' ' . $user_info->display_name . '"/> <label for="bookmark_' . $user_info->ID . '" title="' . __('Bookmark for','teachpress') . ' ' . $user_info->display_name . '">' . $user_info->display_name . '</label></p>';
				  }
			  } 
		   }
		   ?>
		  </div>
		  </td>
		</tr>
		<?php if ($pub_ID == '') {?>
		<tr style="background-color:#EAF2FA; text-align:center;">
			<td>   
			<div style="width:50%; float:left; height:25px;">
			<input type="reset" name="Reset" value="<?php _e('reset','teachpress'); ?>" id="teachpress_reset" class="teachpress_button">
			</div>
			<div style="width:50%; float:right; height:25px;">
			<input name="erstellen" type="submit" class="button-primary" id="publikation_erstellen" onclick="teachpress_validateForm('tags','','R','title','','R','author','','R','bibtex','','R');return document.teachpress_returnValue" value="<?php _e('create','teachpress'); ?>">
			</div>
			</td>
		</tr>    
		<?php } else { ?>
		<tr style="background-color:#EAF2FA; text-align:center;">
			<td>
			<input type="submit" name="speichern" id="publikation_erstellen" value="<?php _e('save','teachpress'); ?>" class="button-primary" title="<?php _e('Save publication','teachpress'); ?>">
			<?php } ?>
			</td>
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
		  <?php if ($pub_ID != '') {
		  $sql = "SELECT t.name, b.con_id 
				FROM " . $teachpress_relation . " b
				INNER JOIN " . $teachpress_tags . " t ON t.tag_id=b.tag_id
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id=b.pub_id
				WHERE p.pub_id = '$pub_ID'
				ORDER BY t.name";	
		  $test = $wpdb->query($sql);
		  if ($test != '0') {
			$sql = $wpdb->get_results($sql);
			echo '<p><strong>' . __('Current','teachpress') . '</strong></p>';
			foreach ($sql as $row3){
				$s = "'";
				echo'<input name="delbox[]" type="checkbox" value="' . $row3->con_id . '" title="Tag &laquo;' . $row3->name . '&raquo; ' . __('delete','teachpress') . '" id="checkbox_' . $row3->con_id . '" onclick="teachpress_change_label_color(' . $s . $row3->con_id . $s . ')"/> <span style="font-size:12px;" ><label for="checkbox_' . $row3->con_id . '" title="Tag &laquo;' . $row3->name . '&raquo; ' . __('delete','teachpress') . '" id="tag_label_' . $row3->con_id . '">' . $row3->name . '</label></span> | ';
			} 
		  }	
		  }?>  
		  <p><label for="tags"><strong><?php _e('New (seperate by comma)','teachpress'); ?></strong></label></p>
		  <input name="tags" type="text" id="tags" style="width:95%">
		  <div class="teachpress_cloud" style="padding-top:15px;">
			<?php
			// Anzahl darzustellender Tags
			$limit = 30;
			// Schriftgroessen
			$maxsize = 25;
			$minsize = 11;
			// Ermittle Anzahl der Tags absteigend sortiert
			$sql = "SELECT anzahlTags FROM ( SELECT COUNT(*) AS anzahlTags FROM " . $teachpress_relation . " GROUP BY " . $teachpress_relation . ".`tag_id` ORDER BY anzahlTags DESC ) as temp1 GROUP BY anzahlTags ORDER BY anzahlTags DESC";
			// Ermittle einzelnes Vorkommen der Tags, sowie Min und Max
			$sql = "SELECT MAX(anzahlTags) AS max, min(anzahlTags) AS min, COUNT(anzahlTags) as gesamt FROM (".$sql.") AS temp";
			$tagcloud_temp = $wpdb->get_row($sql, ARRAY_A);
			$max = $tagcloud_temp['max'];
			$min = $tagcloud_temp['min'];
			$insgesamt = $tagcloud_temp['gesamt'];
			// Tags und Anzahl zusammenstellen
			$sql = "SELECT tagPeak, name, tag_id FROM ( SELECT COUNT(b.tag_id) as tagPeak, t.name AS name,  t.tag_id as tag_id FROM " . $teachpress_relation . " b LEFT JOIN " . $teachpress_tags . " t ON b.tag_id = t.tag_id GROUP BY b.tag_id ORDER BY tagPeak DESC LIMIT " . $limit . " ) AS temp WHERE tagPeak>=".$min." ORDER BY name";
			$test = $wpdb->query($sql);
			if ($test != '0') {
			$temp = $wpdb->get_results($sql, ARRAY_A);
			// Endausgabe der Cloud zusammenstellen
			foreach ($temp as $tagcloud) {
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
				<span style="font-size:<?php echo $size; ?>px;"><a href="javascript:teachpress_inserttag('<?php echo $tagcloud['name']; ?>')" title="&laquo;<?php echo $tagcloud['name']; ?>&raquo; <?php _e('add as tag','teachpress'); ?>"><?php echo $tagcloud['name']; ?> </a></span> 
				<?php 
			}
			}  
			  ?>
			   </div>       
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
			<td>
			<?php if ($daten["image_url"] != '') {
				echo '<p><img name="tp_pub_image" src="' . $daten["image_url"] . '" alt="' . $daten["name"] . '" title="' . $daten["name"] . '" style="max-width:100%;"/></p>';
			} ?>
			<p><label for="image_url" title="<?php _e('With the image field you can add an image to a publication. You can display images in all publication lists','teachpress'); ?>"><strong><?php _e('Image URL','teachpress'); ?></strong></label></p>
			<input name="image_url" id="image_url" type="text" style="width:90%;" value="<?php echo $daten["image_url"]; ?>"/>
			 <a id="upload_image_button" class="thickbox" title="<?php _e('Add Image','teachpress'); ?>" style="cursor:pointer;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
			<p><label for="rel_page" title="<?php _e('With the related page you can link a publication with a normal post/page. It is only used for the teachPress books widget.','teachpress'); ?>"><strong><?php _e('Related page','teachpress'); ?></strong></label></p>
			<div style="overflow:hidden;">
			<select name="rel_page" id="rel_page" style="width:90%;">
			<?php teachpress_wp_pages("menu_order","ASC",$daten["rel_page"],0,0); ?>
			</select>
			</div>
			</td>
		</tr>
		</thead>
	  </table>
	  </div>
		<div style="width:67%; float:left;">
		<div id="post-body">
		<div id="post-body-content">
		<div id="titlediv">
		<div id="titlewrap">
		<label class="hide-if-no-js" style="display:none;" id="title-prompt-text" for="title"><?php _e('Name','teachpress'); ?></label>
		<input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $daten["name"]; ?>" id="title" autocomplete="off" />
		</div>
		</div>
		</div>
		</div>
		<table class="widefat">
		<thead>
		<tr>
			<th><?php _e('General information','teachpress'); ?></th>
		</tr>
		<tr>
		  <td>
			<table>
			<tr>
			 <td style="border:none; padding:0 0 0 0; margin: 0 0 0 0;">
			  <p><label for="type" title="<?php _e('The type of publication','teachpress'); ?>"><strong><?php _e('Type','teachpress'); ?></strong></label></p>
			  <select name="type" id="type" onchange="teachpress_publicationFields('std')" tabindex="2">
				 <?php echo get_tp_publication_type_options ($daten["type"], $mode = 'list'); ?>
			  </select>
			 </td>
			 <td style="border:none; padding:0 0 0 0; margin: 0 0 0 0;">
			  <p><label for="bibtex" title="<?php _e('A simple unique key without spaces','teachpress'); ?>"><strong><?php _e('BibTex-Key','teachpress'); ?></strong></label></p>
			  <input name="bibtex" id="bibtex" type="text" value="<?php echo $daten["bibtex"]; ?>" tabindex="3" />
			 </td>
			</tr>
		   </table>
		  <p><label for="author" title="<?php _e('The names of the authors, seperate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>"><strong><?php _e('Author(s)','teachpress'); ?></strong></label></p>
		  <textarea name="author" wrap="virtual" id="author" style="width:95%" tabindex="4"><?php echo $daten["author"]; ?></textarea>
		  <p><label for="editor" title="<?php _e('The names of the editors, seperate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>"><strong><?php _e('Editor(s)','teachpress'); ?></strong></label></p>
		  <textarea name="editor" id="editor" type="text" style="width:95%" tabindex="5"><?php echo $daten["editor"]; ?></textarea>
		  <p><label for="date" title="<?php _e('The date of publishing','teachpress'); ?>"><strong><?php _e('Date','teachpress'); ?></strong></label></p>
		  <input type="text" name="date" id="date" value="<?php if ($pub_ID != '') { echo $daten["date"]; } else {_e('JJJJ-MM-TT','teachpress'); } ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" tabindex="6"/>
		</td>
		</tr>
		</thead>
		</table>
		<p style="font-size:2px; margin:0px;">&nbsp;</p>
		<table class="widefat">
		<thead>
		<tr>
		  <th><?php _e('Detailed information','teachpress'); ?> <small><a id="show_all_fields" onclick="teachpress_publicationFields('all')" style="cursor:pointer; display:inline;"><?php _e('Show all fields','teachpress'); ?></a> <a id="show_recommend_fields" onclick="teachpress_publicationFields('std2')" style="cursor:pointer; display:none;"><?php _e('Show recommend fields','teachpress'); ?></a></small></th>
		</tr>
		<tr>
		  <td>
		  <?php
		  $display = "";
		  if ($daten["type"] == "conference" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_booktitle" <?php echo $display; ?>>
		  <p><label for="booktitle" title="<?php _e('The title of a book','teachpress'); ?>"><strong><?php _e('booktitle','teachpress'); ?></strong></label></p>
		  <input name="booktitle" id="booktitle" type="text" style="width:95%" value="<?php echo $daten["booktitle"]; ?>" tabindex="7" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "article" || $daten["type"] == "") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_journal" <?php echo $display; ?>>
		  <p><label for="journal" title="<?php _e('The title of a journal','teachpress'); ?>"><strong><?php _e('journal','teachpress'); ?></strong></label></p>
		  <input name="journal" id="journal" type="text" style="width:95%" value="<?php echo $daten["journal"]; ?>" tabindex="8" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "article" || $daten["type"] == "book" || $daten["type"] == "booklet" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings" || $daten["type"] == "") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_volume" <?php echo $display; ?>>
		  <p><label for="volume" title="<?php _e('The volume of a journal or book','teachpress'); ?>"><strong><?php _e('volume','teachpress'); ?></strong></label></p>
		  <input name="volume" id="volume" type="text" value="<?php echo $daten["volume"]; ?>" tabindex="9" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "article" || $daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings" || $daten["type"] == "techreport" || $daten["type"] == "") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_number" <?php echo $display; ?>>
		  <p><label for="number" title="<?php _e('The number of a book, journal or work in a series','teachpress'); ?>"><strong><?php _e('Number','teachpress'); ?></strong></label></p>
		  <input name="number" id="number" type="text" value="<?php echo $daten["number"]; ?>" tabindex="10" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "article" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_pages" <?php echo $display; ?>>
		  <p><label for="pages" title="<?php _e('The page you are referring to.','teachpress'); ?>"><strong><?php _e('pages','teachpress'); ?></strong></label></p>
		  <input name="pages" id="pages" type="text" value="<?php echo $daten["pages"]; ?>" tabindex="11" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_publisher" <?php echo $display; ?>>
		  <p><label for="publisher" title="<?php _e('The names of publisher','teachpress'); ?>"><strong><?php _e('publisher','teachpress'); ?></strong></label></p>
		  <input name="publisher" id="publisher" type="text" style="width:95%" value="<?php echo $daten["publisher"]; ?>" tabindex="12" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "book" || $daten["type"] == "booklet" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "manual" || $daten["type"] == "masterthesis" || $daten["type"] == "phdthesis" || $daten["type"] == "proceedings" || $daten["type"] == "techreport") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_address" <?php echo $display; ?>>
		  <p><label for="address" title="<?php _e('The address of the publisher or the place of confernece','teachpress'); ?>"><strong><?php _e('address','teachpress'); ?></strong></label></p>
		  <textarea name="address" id="address" style="width:95%" tabindex="13"><?php echo $daten["address"]; ?></textarea>
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "book" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "manual") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_edition" <?php echo $display; ?>>
		  <p><label for="edition" title="<?php _e('The edition of a book','teachpress'); ?>"><strong><?php _e('edition','teachpress'); ?></strong></label></p>
		  <input name="edition" id="edition" type="text" value="<?php echo $daten["edition"]; ?>" tabindex="14" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "inbook" || $daten["type"] == "incollection") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_chapter" <?php echo $display; ?>>
		  <p><label for="chapter" title="<?php _e('The chapter or the section number','teachpress'); ?>"><strong><?php _e('chapter','teachpress'); ?></strong></label></p>
		  <input name="chapter" id="chapter" type="text" value="<?php echo $daten["chapter"]; ?>" tabindex="15" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "techreport") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_institution" <?php echo $display; ?>>
		  <p><label for="institution" title="<?php _e('The name of a sponsoring institution','teachpress'); ?>"><strong><?php _e('institution','teachpress'); ?></strong></label></p>
		  <input name="institution" id="institution" type="text" style="width:95%" value="<?php echo $daten["institution"]; ?>" tabindex="15"/>
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "conference" || $daten["type"] == "inproceedings" || $daten["type"] == "manual" || $daten["type"] == "proceedings") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_organization" <?php echo $display; ?>>
		  <p><label for="organization" title="<?php _e('The names of a sponsoring organization','teachpress'); ?>"><strong><?php _e('organization','teachpress'); ?></strong></label></p>
		  <input name="organization" id="organization" type="text" style="width:95%" value="<?php echo $daten["organization"]; ?>" tabindex="16" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "masterthesis" || $daten["type"] == "phdthesis") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_school" <?php echo $display; ?>>
		  <p><label for="school" title="<?php _e('The names of the academic instituion where a thesis was written','teachpress'); ?>"><strong><?php _e('school','teachpress'); ?></strong></label></p>
		  <input name="school" id="school" type="text" style="width:95%" value="<?php echo $daten["school"]; ?>" tabindex="17" />
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_series" <?php echo $display; ?>>
		  <p><label for="series" title="<?php _e('The name of a series','teachpress'); ?>"><strong><?php _e('series','teachpress'); ?></strong></label></p>
		  <input name="series" id="series" type="text" value="<?php echo $daten["series"]; ?>" tabindex="18"/>
		  </div>
		  <div id="div_crossref" style="display:none;">
		  <p><label for="crossref" title="<?php _e('The bibTeX key this work is referring to','teachpress'); ?>"><strong><?php _e('crossref','teachpress'); ?></strong></label></p>
		  <input name="crossref" id="crossref" type="text" value="<?php echo $daten["crossref"]; ?>" tabindex="19" />
		  </div>
		  <div id="div_abstrac">
		  <p><label for="abstrac" title="<?php _e('A short summary of the publication','teachpress'); ?>"><strong><?php _e('abstract','teachpress'); ?></strong></label></p>
		  <textarea name="abstrac" id="abstrac" rows="3" style="width:95%" tabindex="20" ><?php echo $daten["abstract"]; ?></textarea>
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "booklet" || $daten["type"] == "misc") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_howpublished" <?php echo $display; ?>>
		  <p><label for="howpublished" title="<?php _e('An unusual method for publishing','teachpress'); ?>"><strong><?php _e('howpublished','teachpress'); ?></strong></label></p>
		  <input name="howpublished" id="howpublished" type="text" value="<?php echo $daten["howpublished"]; ?>" tabindex="21" />
		  </div>
		  <div id="div_key" style="display:none;">
		  <p><label for="key" title="<?php _e('If there is no author or editor given, so this field is used for the sorting.','teachpress'); ?>"><strong><?php _e('Key','teachpress'); ?></strong></label></p>
		  <input name="key" id="key" type="text" value="<?php echo $daten["key"]; ?>" tabindex="22"/>
		  </div>
		  <?php
		  $display = "";
		  if ($daten["type"] == "inbook" || $daten["type"] == "incollection" || $daten["type"] == "masterthesis" || $daten["type"] == "phdthesis" || $daten["type"] == "techreport") 
			{$display = 'style="display:block;"';}
		  else { $display = 'style="display:none;"';}
		  ?>
		  <div id="div_techtype" <?php echo $display; ?>>
		  <p><label for="techtype" title="<?php _e('The type of a technical report.','teachpress'); ?>"><strong><?php _e('Type','teachpress'); ?></strong></label></p>
		  <input name="techtype" id="techtype" type="text" value="<?php echo $daten["techtype"]; ?>" tabindex="23" />
		  </div>
		  <div id="div_isbn">
		  <p><label for="isbn" title="<?php _e('The ISBN or ISSN of the publication','teachpress'); ?>"><strong><?php _e('ISBN/ISSN','teachpress'); ?></strong></label></p>
		  <input type="text" name="isbn" id="isbn" value="<?php echo $daten["isbn"]; ?>" tabindex="24">
			<span style="padding-left:7px;">
			  <label><input name="is_isbn" type="radio" id="is_isbn_0" value="1" <?php if ($daten["is_isbn"] == '1' || $pub_ID == '') { echo 'checked="checked"'; }?> tabindex="25"/><?php _e('ISBN','teachpress'); ?></label>
			  <label><input name="is_isbn" type="radio" value="0" id="is_isbn_1" <?php if ($daten["is_isbn"] == '0') { echo 'checked="checked"'; }?> tabindex="26"/><?php _e('ISSN','teachpress'); ?></label>
			</span>
		  </div>  
		  <p><label for="url" title="<?php _e('A web link','teachpress'); ?>"><strong><?php _e('URL','teachpress'); ?></strong></label></p>
		  <input name="url" type="text" id="url" style="width:95%" value="<?php echo $daten["url"]; ?>" tabindex="27">
		  </td>
		</tr>
		</thead>
		</table>
		<p style="font-size:2px; margin:0px;">&nbsp;</p>
		<table class="widefat">
		<thead>
		<tr>
		  <th><?php _e('comments','teachpress'); ?></th>
		</tr>
		<tr>
		  <td>
		  <p><label for="comment" title="<?php _e('A not vissible private comment','teachpress'); ?>"><strong><?php _e('private comment','teachpress'); ?></strong></label></p>
		  <textarea name="comment" wrap="virtual" id="comment" style="width:95%" tabindex="28"><?php echo $daten["comment"]; ?></textarea>
		  <p><label for="comment" title="<?php _e('Additional information','teachpress'); ?>"><strong><?php _e('note','teachpress'); ?></strong></label></p>
		  <textarea name="note" wrap="virtual" id="note" style="width:95%" tabindex="29"><?php echo $daten["note"]; ?></textarea>
		  </td>
		</tr>
		</thead>    
		</table>
	  </p>
	  </div>
	</form>
	<script type="text/javascript" charset="utf-8">
		$(function() {
			$('#date').datepick({dateFormat: 'yyyy-mm-dd', yearRange: '1960:c+5', showTrigger: '#calImg'});
		});
		</script>
	</div>
<?php } ?>