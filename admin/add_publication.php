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
   $data = get_tp_var_types('publication_array');
   $data['name'] = isset( $_POST['post_title'] ) ? tp_sec_var($_POST['post_title']) : '';
   $data['type'] = isset( $_POST['type'] ) ? tp_sec_var($_POST['type']) : '';
   $data['bibtex'] = isset( $_POST['bibtex'] ) ? tp_sec_var($_POST['bibtex']) : '';
   $data['author'] = isset( $_POST['author'] ) ? tp_sec_var($_POST['author']) : '';
   $data['editor'] = isset( $_POST['editor'] ) ? tp_sec_var($_POST['editor']) : '';
   $data['isbn'] = isset( $_POST['isbn'] ) ? tp_sec_var($_POST['isbn']) : '';
   $data['url'] = isset( $_POST['url'] ) ? tp_sec_var($_POST['url']) : '';
   $data['date'] = isset( $_POST['date'] ) ? tp_sec_var($_POST['date']) : '';
   $data['booktitle'] = isset( $_POST['booktitle'] ) ? tp_sec_var($_POST['booktitle']) : '';
   $data['journal'] = isset( $_POST['journal'] ) ? tp_sec_var($_POST['journal']) : '';
   $data['volume'] = isset( $_POST['volume'] ) ? tp_sec_var($_POST['volume']) : '';
   $data['number'] = isset( $_POST['number'] ) ? tp_sec_var($_POST['number']) : '';
   $data['pages'] = isset( $_POST['pages'] ) ? tp_sec_var($_POST['pages']) : '';
   $data['publisher'] = isset( $_POST['publisher'] ) ? tp_sec_var($_POST['publisher']) : '';
   $data['address'] = isset( $_POST['address'] ) ? tp_sec_var($_POST['address']) : '';
   $data['edition'] = isset( $_POST['edition'] ) ? tp_sec_var($_POST['edition']) : '';
   $data['chapter'] = isset( $_POST['chapter'] ) ? tp_sec_var($_POST['chapter']) : '';
   $data['institution'] = isset( $_POST['institution'] ) ? tp_sec_var($_POST['institution']) : '';
   $data['organization'] = isset( $_POST['organization'] ) ? tp_sec_var($_POST['organization']) : '';
   $data['school'] = isset( $_POST['school'] ) ? tp_sec_var($_POST['school']) : '';
   $data['series'] = isset( $_POST['series'] ) ? tp_sec_var($_POST['series']) : '';
   $data['crossref'] = isset( $_POST['crossref'] ) ? tp_sec_var($_POST['crossref']) : '';
   $data['abstract'] = isset( $_POST['abstract'] ) ? tp_sec_var($_POST['abstract']) : '';
   $data['howpublished'] = isset( $_POST['howpublished'] ) ? tp_sec_var($_POST['howpublished']) : '';
   $data['key'] = isset( $_POST['key'] ) ? tp_sec_var($_POST['key']) : '';
   $data['techtype'] = isset( $_POST['techtype'] ) ? tp_sec_var($_POST['techtype']) : '';
   $data['comment'] = isset( $_POST['comment'] ) ? tp_sec_var($_POST['comment']) : '';
   $data['note'] = isset( $_POST['note'] ) ? tp_sec_var($_POST['note']) : '';
   $data['image_url'] = isset( $_POST['image_url'] ) ? tp_sec_var($_POST['image_url']) : '';
   $data['rel_page'] = isset( $_POST['rel_page'] ) ? tp_sec_var($_POST['rel_page'],'integer') : '';
   $data['is_isbn'] = isset( $_POST['is_isbn'] ) ? tp_sec_var($_POST['is_isbn'],'integer') : '';

   $tags = isset( $_POST['tags'] ) ? tp_sec_var($_POST['tags']) : '';
   $delbox = isset( $_POST['delbox'] ) ? $_POST['delbox'] : '';
   $bookmark = isset( $_POST['bookmark'] ) ? $_POST['bookmark'] : '';

   // from show_publications.php
   $pub_ID = isset( $_REQUEST['pub_ID'] ) ? tp_sec_var($_REQUEST['pub_ID'], 'integer') : '';
   $search = isset( $_GET['search'] ) ? tp_sec_var($_GET['search']) : '';
   $filter = isset( $_GET['filter'] ) ? tp_sec_var($_GET['filter']) : '';
   $site = isset( $_GET['site'] ) ? tp_sec_var($_GET['site']) : '';
   $tag_id = isset( $_GET['tag'] ) ? tp_sec_var($_GET['tag']) : '';
   $entry_limit = isset( $_GET['limit'] ) ? tp_sec_var($_GET['limit']) : '';

   $daten = get_tp_var_types('publication_array');
   ?>
   <div class="wrap">
   <form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="form1">
   <?php
   // if publications was created
   if ( isset($_POST['erstellen']) ) {
      $pub_ID = tp_add_publication($data, $tags, $bookmark);
      $message = __('Publication added','teachpress') . ' <a href="admin.php?page=teachpress/addpublications.php">' . __('Add New','teachpress') . '</a>';
      get_tp_message($message);
   }
   // if publication was saved
   if ( isset($_POST['speichern']) ) {
      tp_change_publication($pub_ID, $data, $bookmark, $delbox, $tags);
      $message = __('Publication changed','teachpress');
      get_tp_message($message);
   }

   if ( $pub_ID != '' && !isset($_POST['erstellen']) ) {
           echo '<p style="margin-bottom:0px;"><a href="admin.php?page=publications.php&amp;search=' . $search . '&amp;filter=' .  $filter . '&amp;limit=' . $entry_limit . '&amp;tag=' . $tag_id . '" class="button-secondary" title="' . __('Back','teachpress') . '">&larr; ' . __("Back",'teachpress') . '</a></p>';
   } ?>
   <h2><?php if ($pub_ID == '') { _e('Add a new publication','teachpress'); } else { _e('Edit publication','teachpress'); } ?></h2>
     <input name="page" type="hidden" value="teachpress/addpublications.php">
     <?php if ($pub_ID != '') { 
          $row = "SELECT * FROM " . $teachpress_pub . " WHERE `pub_id` = '$pub_ID'";
          $daten = $wpdb->get_row($row, ARRAY_A)
          ?>
          <input type="hidden" name="pub_ID" value="<?php echo $pub_ID; ?>" />
          <input type="hidden" name="search" value="<?php echo stripslashes($search); ?>" />
          <input type="hidden" name="limit" id="limit" value="<?php echo $entry_limit; ?>" />
          <input type="hidden" name="site" id="site" value="<?php echo $site; ?>" />
          <input type="hidden" name="filter" id="filter" value="<?php echo $filter; ?>" />
          <input type="hidden" name="tag" id="tag" value="<?php echo $tag_id; ?>" />
     <?php } ?>
     <div style="min-width:780px; width:100%; max-width:1100px;">
     <div style="width:30%; float:right; padding-right:2%; padding-left:1%;">
     <table class="widefat" style="margin-bottom:15px;">
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
                   if ($test != '0') {
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
                   $user_info = get_userdata($row->user);
                   if ($user != $row->user && $user_info != false) { 
                         if ($pub_ID != '') {
                              $sql = "SELECT pub_id FROM " . $teachpress_user . " WHERE `pub_id`='$pub_ID' AND `user` = '$user_info->ID'";
                              $test = $wpdb->query($sql);
                              if ($test != '0') {
                                   echo '<p><input type="checkbox" name="bookmark[]" id="bookmark_' . $user_info->ID . '" disabled="disabled"/> <label for="bookmark_' . $user_info->ID . '">' . $user_info->display_name . '</label></p>';
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
           <tr style="text-align:center;">
                   <td>   
                   <div style="width:50%; float:left; height:25px;">
                   <input type="reset" name="Reset" value="<?php _e('Reset','teachpress'); ?>" id="teachpress_reset" class="button-secondary">
                   </div>
                   <div style="width:50%; float:right; height:25px;">
                   <input name="erstellen" type="submit" class="button-primary" id="publikation_erstellen" onclick="teachpress_validateForm('tags','','R','title','','R','author','','R','bibtex','','R');return document.teachpress_returnValue" value="<?php _e('Create','teachpress'); ?>">
                   </div>
                   </td>
           </tr>    
           <?php } else { ?>
           <tr style="text-align:center;">
                   <td>
                   <input type="submit" name="speichern" id="publikation_erstellen" value="<?php _e('Save'); ?>" class="button-primary" title="<?php _e('Save'); ?>">
                   </td>
           </tr>
   <?php } ?>  
           </thead>
     </table>
      <table class="widefat" style="margin-bottom:15px;">
           <thead>
           <tr>
               <th><?php _e('Tags'); ?></th>
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
                           echo'<input name="delbox[]" type="checkbox" value="' . $row3->con_id . '" title="Tag &laquo;' . $row3->name . '&raquo; ' . __('Delete','teachpress') . '" id="checkbox_' . $row3->con_id . '" onclick="teachpress_change_label_color(' . $s . $row3->con_id . $s . ')"/> <span style="font-size:12px;" ><label for="checkbox_' . $row3->con_id . '" title="Tag &laquo;' . $row3->name . '&raquo; ' . __('Delete','teachpress') . '" id="tag_label_' . $row3->con_id . '">' . $row3->name . '</label></span> | ';
                   } 
             }	
             }?>  
             <p><label for="tags"><strong><?php _e('New (separate by comma)','teachpress'); ?></strong></label></p>
             <input name="tags" type="text" id="tags" title="<?php _e('New (separate by comma)','teachpress'); ?>" style="width:95%">
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
                      // Schriftgr��e berechnen
                      // Minimum ausgleichen
                      if ($min == 1) {
                              $min = 0;
                      }
                      // Formel: max. Schriftgroesse*(aktuelle anzahl - kleinste Anzahl)/ (groe�te Anzahl - kleinste Anzahl)
                      $size = floor(($maxsize*($tagcloud['tagPeak']-$min)/($max-$min)));
                      // Ausgleich der Schriftgr��e
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
                   <input name="image_url" id="image_url" class="upload" type="text" title="<?php _e('With the image field you can add an image to a publication. You can display images in all publication lists','teachpress'); ?>" style="width:90%;" value="<?php echo $daten["image_url"]; ?>"/>
                    <a class="upload_button_image" title="<?php _e('Add Image','teachpress'); ?>" style="cursor:pointer; border:none;"><img src="images/media-button-image.gif" alt="<?php _e('Add Image','teachpress'); ?>" /></a>
                   <p><label for="rel_page" title="<?php _e('With the related page you can link a publication with a normal post/page. It is only used for the teachPress books widget.','teachpress'); ?>"><strong><?php _e('Related page','teachpress'); ?></strong></label></p>
                   <div style="overflow:hidden;">
                   <select name="rel_page" id="rel_page" title="<?php _e('With the related page you can link a publication with a normal post/page. It is only used for the teachPress books widget.','teachpress'); ?>" style="width:90%;">
                   <?php
                   $post_type = get_tp_option('rel_page_publications');
                   get_tp_wp_pages("menu_order","ASC",$daten["rel_page"],$post_type,0,0); 
                   ?>
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
           <input type="text" name="post_title" size="30" title="<?php _e('Publication name','teachpress'); ?>" tabindex="1" value="<?php echo stripslashes($daten["name"]); ?>" id="title" autocomplete="off" />
           </div>
           </div>
           </div>
           </div>
           <table class="widefat" style="margin-bottom:15px;">
           <thead>
           <tr>
                   <th><?php _e('General information','teachpress'); ?></th>
           </tr>
           <tr>
             <td>
                   <table>
                   <tr>
                    <td style="border:none; padding:0 0 0 0; margin: 0 0 0 0;">
                     <p><label for="type" title="<?php _e('The type of publication','teachpress'); ?>"><strong><?php _e('Type'); ?></strong></label></p>
                     <select name="type" id="type" title="<?php _e('The type of publication','teachpress'); ?>" onchange="teachpress_publicationFields('std')" tabindex="2">
                            <?php echo get_tp_publication_type_options ($daten["type"], $mode = 'list'); ?>
                     </select>
                    </td>
                    <td style="border:none; padding:0 0 0 0; margin: 0 0 0 0;">
                     <p><label for="bibtex" title="<?php _e('A simple unique key without spaces','teachpress'); ?>"><strong><?php _e('BibTex-Key','teachpress'); ?></strong></label></p>
                     <input name="bibtex" id="bibtex" type="text" title="<?php _e('A simple unique key without spaces','teachpress'); ?>" value="<?php echo stripslashes($daten["bibtex"]); ?>" tabindex="3" />
                    </td>
                   </tr>
              </table>
             <p><label for="author" title="<?php _e('The names of the authors, separate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>"><strong><?php _e('Author(s)','teachpress'); ?></strong></label></p>
             <textarea name="author" wrap="virtual" id="author" title="<?php _e('The names of the authors, separate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>" style="width:95%" tabindex="4"><?php echo stripslashes($daten["author"]); ?></textarea>
             <p><label for="editor" title="<?php _e('The names of the editors, separate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>"><strong><?php _e('Editor(s)','teachpress'); ?></strong></label></p>
             <textarea name="editor" id="editor" type="text" title="<?php _e('The names of the editors, separate by `and`. Example: Mark Twain and Albert Einstein','teachpress'); ?>" style="width:95%" tabindex="5"><?php echo stripslashes($daten["editor"]); ?></textarea>
             <p><label for="date" title="<?php _e('The date of publishing','teachpress'); ?>"><strong><?php _e('Date','teachpress'); ?></strong></label></p>
             <input type="text" name="date" id="date" title="<?php _e('The date of publishing','teachpress'); ?>" value="<?php if ($pub_ID != '') { echo $daten["date"]; } else {_e('JJJJ-MM-TT','teachpress'); } ?>" onblur="if(this.value=='') this.value='<?php _e('JJJJ-MM-TT','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('JJJJ-MM-TT','teachpress'); ?>') this.value='';" tabindex="6"/>
           </td>
           </tr>
           </thead>
           </table>
           <table class="widefat" style="margin-bottom:15px;">
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
             <textarea name="booktitle" id="booktitle" wrap="virtual" style="width:95%" tabindex="7" title="<?php _e('The title of a book','teachpress'); ?>"><?php echo stripslashes($daten["booktitle"]); ?></textarea>
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "article" || $daten["type"] == "") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_journal" <?php echo $display; ?>>
             <p><label for="journal" title="<?php _e('The title of a journal','teachpress'); ?>"><strong><?php _e('journal','teachpress'); ?></strong></label></p>
             <input name="journal" id="journal" type="text" title="<?php _e('The title of a journal','teachpress'); ?>" style="width:95%" value="<?php echo stripslashes($daten["journal"]); ?>" tabindex="8" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "article" || $daten["type"] == "book" || $daten["type"] == "booklet" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings" || $daten["type"] == "") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_volume" <?php echo $display; ?>>
             <p><label for="volume" title="<?php _e('The volume of a journal or book','teachpress'); ?>"><strong><?php _e('volume','teachpress'); ?></strong></label></p>
             <input name="volume" id="volume" type="text" title="<?php _e('The volume of a journal or book','teachpress'); ?>" value="<?php echo stripslashes($daten["volume"]); ?>" tabindex="9" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "article" || $daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings" || $daten["type"] == "techreport" || $daten["type"] == "") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_number" <?php echo $display; ?>>
             <p><label for="number" title="<?php _e('The number of a book, journal or work in a series','teachpress'); ?>"><strong><?php _e('Number','teachpress'); ?></strong></label></p>
             <input name="number" id="number" type="text" title="<?php _e('The number of a book, journal or work in a series','teachpress'); ?>" value="<?php echo stripslashes($daten["number"]); ?>" tabindex="10" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "article" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_pages" <?php echo $display; ?>>
             <p><label for="pages" title="<?php _e('The page you are referring to.','teachpress'); ?>"><strong><?php _e('pages','teachpress'); ?></strong></label></p>
             <input name="pages" id="pages" type="text" title="<?php _e('The page you are referring to.','teachpress'); ?>" value="<?php echo stripslashes($daten["pages"]); ?>" tabindex="11" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_publisher" <?php echo $display; ?>>
             <p><label for="publisher" title="<?php _e('The names of publisher','teachpress'); ?>"><strong><?php _e('publisher','teachpress'); ?></strong></label></p>
             <input name="publisher" id="publisher" type="text" title="<?php _e('The names of publisher','teachpress'); ?>" style="width:95%" value="<?php echo stripslashes($daten["publisher"]); ?>" tabindex="12" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "book" || $daten["type"] == "booklet" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "manual" || $daten["type"] == "masterthesis" || $daten["type"] == "phdthesis" || $daten["type"] == "proceedings" || $daten["type"] == "techreport") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_address" <?php echo $display; ?>>
             <p><label for="address" title="<?php _e('The address of the publisher or the place of confernece','teachpress'); ?>"><strong><?php _e('address','teachpress'); ?></strong></label></p>
             <input name="address" type="text" id="address" style="width:95%" tabindex="13" title="<?php _e('The address of the publisher or the place of confernece','teachpress'); ?>" value="<?php echo stripslashes($daten["address"]); ?>" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "book" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "manual") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_edition" <?php echo $display; ?>>
             <p><label for="edition" title="<?php _e('The edition of a book','teachpress'); ?>"><strong><?php _e('edition','teachpress'); ?></strong></label></p>
             <input name="edition" id="edition" type="text" title="<?php _e('The edition of a book','teachpress'); ?>" value="<?php echo stripslashes($daten["edition"]); ?>" tabindex="14" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "inbook" || $daten["type"] == "incollection") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_chapter" <?php echo $display; ?>>
             <p><label for="chapter" title="<?php _e('The chapter or the section number','teachpress'); ?>"><strong><?php _e('chapter','teachpress'); ?></strong></label></p>
             <input name="chapter" id="chapter" type="text" title="<?php _e('The chapter or the section number','teachpress'); ?>" value="<?php echo stripslashes($daten["chapter"]); ?>" tabindex="15" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "techreport") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_institution" <?php echo $display; ?>>
             <p><label for="institution" title="<?php _e('The name of a sponsoring institution','teachpress'); ?>"><strong><?php _e('institution','teachpress'); ?></strong></label></p>
             <input name="institution" id="institution" type="text" title="<?php _e('The name of a sponsoring institution','teachpress'); ?>" style="width:95%" value="<?php echo stripslashes($daten["institution"]); ?>" tabindex="15"/>
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "conference" || $daten["type"] == "inproceedings" || $daten["type"] == "manual" || $daten["type"] == "proceedings") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_organization" <?php echo $display; ?>>
             <p><label for="organization" title="<?php _e('The names of a sponsoring organization','teachpress'); ?>"><strong><?php _e('organization','teachpress'); ?></strong></label></p>
             <input name="organization" id="organization" type="text" title="<?php _e('The names of a sponsoring organization','teachpress'); ?>" style="width:95%" value="<?php echo stripslashes($daten["organization"]); ?>" tabindex="16" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "masterthesis" || $daten["type"] == "phdthesis") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_school" <?php echo $display; ?>>
             <p><label for="school" title="<?php _e('The names of the academic instituion where a thesis was written','teachpress'); ?>"><strong><?php _e('school','teachpress'); ?></strong></label></p>
             <input name="school" id="school" type="text" title="<?php _e('The names of the academic instituion where a thesis was written','teachpress'); ?>" style="width:95%" value="<?php echo stripslashes($daten["school"]); ?>" tabindex="17" />
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "book" || $daten["type"] == "conference" || $daten["type"] == "inbook" || $daten["type"] =="incollection" || $daten["type"] == "inproceedings" || $daten["type"] == "proceedings") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_series" <?php echo $display; ?>>
             <p><label for="series" title="<?php _e('The name of a series','teachpress'); ?>"><strong><?php _e('series','teachpress'); ?></strong></label></p>
             <input name="series" id="series" type="text" title="<?php _e('The name of a series','teachpress'); ?>" value="<?php echo stripslashes($daten["series"]); ?>" tabindex="18"/>
             </div>
             <div id="div_crossref" style="display:none;">
             <p><label for="crossref" title="<?php _e('The bibTeX key this work is referring to','teachpress'); ?>"><strong><?php _e('crossref','teachpress'); ?></strong></label></p>
             <input name="crossref" id="crossref" type="text" title="<?php _e('The bibTeX key this work is referring to','teachpress'); ?>" value="<?php echo stripslashes($daten["crossref"]); ?>" tabindex="19" />
             </div>
             <div id="div_abstract">
             <p><label for="abstract" title="<?php _e('A short summary of the publication','teachpress'); ?>"><strong><?php _e('abstract','teachpress'); ?></strong></label></p>
             <textarea name="abstract" id="abstract" rows="5" title="<?php _e('A short summary of the publication','teachpress'); ?>" style="width:95%" tabindex="20" ><?php echo stripslashes($daten["abstract"]); ?></textarea>
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "booklet" || $daten["type"] == "misc") 
                   {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_howpublished" <?php echo $display; ?>>
             <p><label for="howpublished" title="<?php _e('An unusual method for publishing','teachpress'); ?>"><strong><?php _e('howpublished','teachpress'); ?></strong></label></p>
             <input name="howpublished" id="howpublished" type="text" title="<?php _e('An unusual method for publishing','teachpress'); ?>" value="<?php echo stripslashes($daten["howpublished"]); ?>" tabindex="21" />
             </div>
             <div id="div_key" style="display:none;">
             <p><label for="key" title="<?php _e('If there is no author or editor given, so this field is used for the sorting.','teachpress'); ?>"><strong><?php _e('Key','teachpress'); ?></strong></label></p>
             <input name="key" id="key" type="text" title="<?php _e('If there is no author or editor given, so this field is used for the sorting.','teachpress'); ?>" value="<?php echo stripslashes($daten["key"]); ?>" tabindex="22"/>
             </div>
             <?php
             $display = "";
             if ($daten["type"] == "inbook" || $daten["type"] == "incollection" || $daten["type"] == "masterthesis" || $daten["type"] == "phdthesis" || $daten["type"] == "techreport") {$display = 'style="display:block;"';}
             else { $display = 'style="display:none;"';}
             ?>
             <div id="div_techtype" <?php echo $display; ?>>
             <p><label for="techtype" title="<?php _e('The type of a technical report.','teachpress'); ?>"><strong><?php _e('Type'); ?></strong></label></p>
             <input name="techtype" id="techtype" type="text" title="<?php _e('The type of a technical report.','teachpress'); ?>" value="<?php echo stripslashes($daten["techtype"]); ?>" tabindex="23" />
             </div>
             <div id="div_isbn">
             <p><label for="isbn" title="<?php _e('The ISBN or ISSN of the publication','teachpress'); ?>"><strong><?php _e('ISBN/ISSN','teachpress'); ?></strong></label></p>
             <input type="text" name="isbn" id="isbn" title="<?php _e('The ISBN or ISSN of the publication','teachpress'); ?>" value="<?php echo $daten["isbn"]; ?>" tabindex="24">
                   <span style="padding-left:7px;">
                     <label><input name="is_isbn" type="radio" id="is_isbn_0" value="1" <?php if ($daten["is_isbn"] == '1' || $pub_ID == '') { echo 'checked="checked"'; }?> tabindex="25"/><?php _e('ISBN','teachpress'); ?></label>
                     <label><input name="is_isbn" type="radio" value="0" id="is_isbn_1" <?php if ($daten["is_isbn"] == '0') { echo 'checked="checked"'; }?> tabindex="26"/><?php _e('ISSN','teachpress'); ?></label>
                   </span>
             </div>
             <p><label for="url" title="<?php _e('URL'); ?>"><strong><?php _e('URL'); ?></strong></label></p>
     <input name="url" type="text" id="url" class="upload" title="<?php _e('URL'); ?>" style="width:95%" value="<?php echo $daten["url"]; ?>" tabindex="27">
     <a class="upload_button" style="cursor:pointer; border:none;" title="<?php _e('Insert a file from the WordPress Media Library','teachpress'); ?>"><img src="images/media-button-other.gif"/></a>
             </td>
           </tr>
           </thead>
           </table>
           <table class="widefat">
           <thead>
           <tr>
             <th><?php _e('comments','teachpress'); ?></th>
           </tr>
           <tr>
             <td>
             <p><label for="comment" title="<?php _e('A not vissible private comment','teachpress'); ?>"><strong><?php _e('private comment','teachpress'); ?></strong></label></p>
             <textarea name="comment" wrap="virtual" id="comment" title="<?php _e('A not vissible private comment','teachpress'); ?>" style="width:95%" rows="3" tabindex="28"><?php echo stripslashes($daten["comment"]); ?></textarea>
             <p><label for="comment" title="<?php _e('Additional information','teachpress'); ?>"><strong><?php _e('note','teachpress'); ?></strong></label></p>
             <textarea name="note" wrap="virtual" id="note" title="<?php _e('Additional information','teachpress'); ?>" style="width:95%" rows="3" tabindex="29"><?php echo stripslashes($daten["note"]); ?></textarea>
             </td>
           </tr>
           </thead>    
           </table>
     </p>
     </div>
   </form>
     <script type="text/javascript" charset="utf-8">
     jQuery(document).ready(function($) {
         $('#date').datepicker({showWeek: true, changeMonth: true, changeYear: true, showOtherMonths: true, firstDay: 1, renderer: $.extend({}, $.datepicker.weekOfYearRenderer), onShow: $.datepicker.showStatus, dateFormat: 'yy-mm-dd', yearRange: '1950:c+5'});
     });
     </script>
   </div>
<?php } ?>