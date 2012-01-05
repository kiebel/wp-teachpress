<?php 
/*
 * Import BibTeX
*/ 
function teachpress_import_page() {
     $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';
     // variable 
     if ( isset($_POST['tp_submit']) && isset ($_POST['bibtex_area']) ) {
        $bibtex = $_POST['bibtex_area']; // The input is checked by the function tp_bibtex::import_bibtex
        $settings['keyword_separator'] = tp_sec_var($_POST['keyword_option']);
	tp_bibtex::import_bibtex($bibtex, $settings);
	echo '<p><a href="admin.php?page=teachpress/import.php" class="button-secondary">&larr; ' . __('Back','teachpress') . '</a></p>';
     }
     else {
          if ($tab == '' || $tab == 'import') { 
           $set_menu_1 = __('Import'); 
          }
          else { 
           $set_menu_1 =  '<a href="admin.php?page=teachpress/import.php&amp;tab=import" title="' . __('Import') . '" >' . __('Import') . '</a>'; 
          }

          if ($tab == 'export') {
           $set_menu_2 = __('Export'); 
          }
          else {
           $set_menu_2 = '<a href="admin.php?page=teachpress/import.php&amp;tab=export" title="' . __('Export') . '">' . __('Export') . '</a>'; 
          }
	?>
	<div class="wrap">
	<h2><?php _e('Publications','teachpress'); ?></h2>
        <h3><?php echo $set_menu_1 . ' | ' . $set_menu_2; ?></h3>
        <?php if ($tab == '' || $tab == 'import') { ?>
        <p><?php _e("Copy your BibTeX entries in the textarea. Restrictions: teachPress can't converting LaTeX special chars as well as not numeric month and day attributes.","teachpress"); ?></p>
	<form id="tp_import" name="tp_import" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<input type="hidden" name="page" value="teachpress/import.php"/>
        <table class="form-table">
             <tr>
                  <th style="width: 100px;"><label for="bibtex_area"><?php _e('Import Area','teachpress'); ?></label></th>
                  <td><textarea name="bibtex_area" id="bibtex_area" rows="20" style="width:90%;" title="<?php _e('Insert your BibTeX entries here','teachpress'); ?>"></textarea></td>
             </tr>
        </table>
        <p><a onclick="teachpress_showhide('import_options')" style="cursor: pointer;"><strong>+ <?php _e('Options','teachpress'); ?></strong></a></p>
        <div id="import_options" style="display:none;">
        <table class="form-table">
             <tr>
                  <th style="width:150px;"><label for="keyword_option"><?php _e('Keyword Separator','teachpress'); ?></label></th>
                  <td><input type="input" name="keyword_option" id="keyword_option" title="<?php _e('Keyword Separator','teachpress'); ?>" value="," size="3"/></td>
             </tr>
        </table>
        </div>     
	<p><input name="tp_submit" type="submit" class="button-primary" value="<?php _e('Import'); ?>"/></p>
	</form>
	
	<?php
        }
        
        if ($tab == 'export') {
        ?>
        <form id="tp_export" name="tp_export" action="<?php echo WP_PLUGIN_URL; ?>/teachpress/export.php?type=pub" method="post">
        <table class="form-table">
             <tr>
                  <th style="width: 150px;"><label for="tp_user"><?php _e('Publications by user','teachpress'); ?></label></th>
                  <td>
                       <select name="tp_user" id="tp_user">
                            <option value="all"><?php _e('All'); ?></option>
                            <?php
                            global $wpdb;
                            global $teachpress_user;
                            $abfrage = "SELECT DISTINCT user FROM " . $teachpress_user . "";
                            $row = $wpdb->get_results($abfrage, ARRAY_A);
                            foreach($row as $row) {
                                 $user_info = get_userdata($row['user']);
                                 if ( $user_info != false ) { 
                                      echo '<option value="' . $user_info->ID . '">' . $user_info->display_name . '</option>';
                                 }
                            }
                            ?>
                       </select>
                  </td>
             </tr>
             <tr>
                  <th style="width: 150px;"><label for="tp_format"><?php _e('Format'); ?></label></th>
                  <td>
                       <select name="tp_format" id="tp_format">
                            <option value="bibtex">BibTeX</option>
                            <option value="rtf">RTF</option>
                       </select>
                  </td>
             </tr>
        </table>
        <p><input name="tp_submit_2" type="submit" class="button-primary" value="<?php _e('Export'); ?>"/></p>
        </form>
        <?php
        }
        ?>
        </div>
        <?php
        
     }
} ?>