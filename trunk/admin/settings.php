<?php
/*
 * Setting page
*/ 
function teachpress_admin_settings() {

	global $wpdb;
	global $teachpress_settings; 
	global $teachpress_stud; 
	global $teachpress_courses;
	
	$semester = tp_sec_var($_GET[semester]);
	$permalink = tp_sec_var($_GET[permalink]);
	$sign_out = tp_sec_var($_GET[sign_out]);
	$matriculation_number_field = tp_sec_var($_GET[matriculation_number_field]);
	$course_of_studies_field = tp_sec_var($_GET[course_of_studies_field]);
	$semesternumber_field = tp_sec_var($_GET[semesternumber_field]);
	$birthday_field = tp_sec_var($_GET[birthday_field]);
	$drop_tp = tp_sec_var($_GET[drop_tp], 'integer');
	$login = tp_sec_var($_GET[login]);
	$userrole = $_GET[userrole];
	$einstellungen = $_GET[einstellungen];
	$delete = $_GET[delete];
	$name = tp_sec_var($_GET[name]);
	$typ = tp_sec_var($_GET[typ]);
	$newsem = tp_sec_var($_GET[newsem]);
	$addstud = $_GET[addstud];
	$addtyp = $_GET[addtyp];
	$addsem = $_GET[addsem];
	$site = 'options-general.php?page=teachpress/settings.php';
	$tab = $_GET[tab];
	?> 
	<div class="wrap">
	 <?php
	// event handler
	if ($_GET[up] == 1) {
		tp_db_update();
	}
	if ($_GET[ins] == 1) {
		teachpress_install();
	}
	if (isset($einstellungen)) {
		if ($matriculation_number_field != '1') {
			$matriculation_number_field == 0;
		}
		if ($course_of_studies_field != '1') {
			$course_of_studies_field == 0;
		}
		if ($semesternumber_field != '1') {
			$semesternumber_field == 0;
		}
		if ($birthday_field != '1') {
			$birthday_field == 0;
		}
		if ($drop_tp == '1') {
			tp_uninstall();
		}
		else {
			tp_change_settings($semester, $permalink, $sign_out, $userrole, $matriculation_number_field, $course_of_studies_field, $semesternumber_field, $birthday_field, $login);
		}
		$message = __('Settings updated','teachpress');
		tp_get_message($message);
	}
	if (isset($addstud) && $name != __('Add course of studies','teachpress')) {
		tp_add_setting($name, 'course_of_studies');
	}
	if (isset($addtyp) && $typ != __('Add type','teachpress')) {
		tp_add_setting($typ, 'course_type');
	}
	if (isset($addsem) && $newsem != __('Add term','teachpress')) {
		tp_add_setting($newsem, 'semester');
	}
	if (isset($delete)) {
		tp_delete_setting($delete);
	}?>
    <h2 style="padding-bottom:0px;"><?php _e('teachPress settings','teachpress'); ?></h2>
    <?php
	// Site menu
	if ($tab == '' || $tab == 'general') { 
		$set_menu_1 = __('General','teachpress'); 
	}
	else { 
		$set_menu_1 =  '<a href="' . $site . '&amp;tab=general" title="' . __('General','teachpress') . '" >' . __('General','teachpress') . '</a>'; 
	}
	
	if ($tab == 'courses') {
		$set_menu_2 = __('Courses','teachpress'); 
	}
	else { 
		$set_menu_2 = '<a href="' . $site . '&amp;tab=courses" title="' . __('Courses','teachpress') . '">' . __('Courses','teachpress') . '</a>'; 
	}
	
	if ($tab == 'publications') { 
		$set_menu_3 = __('Publications','teachpress'); 
	}
	else { 
		$set_menu_3 = '<a href="' . $site . '&amp;tab=publications" title="' . __('Publications','teachpress') . '">' . __('Publications','teachpress') . '</a>'; 
	}
	// End Site Menu
	?>
    <h3><?php echo $set_menu_1 . ' | ' .  $set_menu_2 . ' | ' . $set_menu_3; ?></h3>
    <div id="einstellungen" style="float:left; width:97%;">
  	<form id="form1" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
	<input name="page" type="hidden" value="teachpress/settings.php" />
    <input name="tab" type="hidden" value="<?php echo $tab; ?>" />
    <?php
	
	/***********/
	/* General */
	/***********/
	if ($tab == '' || $tab == 'general') {?>
    	<table class="widefat">
        	<thead>
			  <tr>
				<th width="160"><label title="<?php _e('teachPress version','teachpress'); ?>"><?php _e('teachPress version','teachpress'); ?></label></th>
				<td width="160"><?php 
					// Test ob Datenbank installiert ist
					$test = tp_get_option('db-version');
					if ($test != '') {
						 // Test ob Datenbank noch aktuell
						$version =  get_tp_version();
						if ($test == $version) { 
							echo '' . $version . ' <span style="color:#00FF00; font-weight:bold;">&radic;</span>';
						} 
						else {
							echo '' . $test . ' <span style="color:#FF0000; font-weight:bold;">X</span> <a href="options-general.php?page=teachpress/settings.php&up=1"><strong>' . __('Update to','teachpress') . ' ' . $version . '</strong></a>';
						}
					}
					else {
						$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'wp_id'";
						$test2 = $wpdb->query($sql);
						if ($test2 != 0) {
							echo '<a href="options-general.php?page=teachpress/settings.php&up=1"><strong>' . __('Update','teachpress') . '</strong></a>';
						}
						else {
							echo '<a href="options-general.php?page=teachpress/settings.php&ins=1"><strong>' . __('install','teachpress') . '</strong></a>';
						}
					} ?>
               </td>
               <td><em><?php _e('Shows the teachPress database version and available database updates','teachpress'); ?></em></td>
			  </tr>
			  <tr>
				<th><label for="semester" title="<?php _e('Current term','teachpress'); ?>"><?php _e('Current term','teachpress'); ?></label></th>
				<td style="vertical-align:middle;"><select name="semester" id="semester">
					<?php
                    $value = tp_get_option('sem');   
                    $sem = "SELECT setting_id, value FROM " . $teachpress_settings . " WHERE category = 'semester' ORDER BY setting_id DESC";
                    $sem = $wpdb->get_results($sem);
                    foreach ($sem as $sem) { 
                        if ($sem->value == $value) {
                            $current = 'selected="selected"' ;
                        }
                        else {
                            $current = '' ;
                        } 
                        echo '<option value="' . $sem->value . '" ' . $current . '>' . $sem->value . '</option>';
                    } ?>    
					</select></td>
                    <td><em><?php _e('Here you can change the current term. This value is used for the default settings for all menus.','teachpress'); ?></em></td>
			  </tr>
			  <tr>
				<th><label for="permalink" title="<?php _e('Permalinks','teachpress'); ?>"><?php _e('Permalinks','teachpress'); ?></label></th>
				<td style="vertical-align:middle;"><select name="permalink" id="permalink">
					  <?php
					  $value = tp_get_option('permalink');
					  if ($value == '1') {
					  	echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
					  	echo '<option value="0">' . __('no','teachpress') . '</option>';
					  }
					  else {
                      	echo '<option value="1">' . __('yes','teachpress') . '</option>';
					  	echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
					  } 
					  ?>
					</select></td>
                 <td><em><?php _e('Here you can specify, if your WordPress installation using permalinks or not.','teachpress'); ?></em></td>   
			  </tr>
              <tr>
              	<th><label for="userrole" title="<?php _e('Backend access for','teachpress'); ?>"><?php _e('Backend access for','teachpress'); ?></label></th>
                <td style="vertical-align:middle;">
                	<select name="userrole[]" id="userrole" multiple="multiple" style="height:80px;">
                    <?php
					global $wp_roles;
    				$wp_roles->WP_Roles();
					foreach ($wp_roles->role_names as $roledex => $rolename) 
					{
						$role = $wp_roles->get_role($roledex);
						$select = $role->has_cap('use_teachpress') ? 'selected="selected"' : '';
						echo '<option value="'.$roledex.'" '.$select.'>'.$rolename.'</option>';
					}
					?>
                    </select>
					
                </td>
                <td><em><?php _e('Select which userrole your users must have to use the teachPress backend.','teachpress'); ?><br /><?php _e('use &lt;Ctrl&gt; key to select multiple roles','teachpress'); ?></em></td>
              </tr>
             </thead>
             </table>
             <h4><?php _e('Enrollment system','teachpress'); ?></h4>
             <table class="widefat">
             <thead>
              <tr>
              	<th width="160"><label for="login"><?php _e('Mode','teachpress'); ?></label></th>
                <td width="160" style="vertical-align:middle;">
                <select name="login" id="login">
                  <?php
                  $value = tp_get_option('login');
                  if ($value == 'int') {
				  	echo '<option value="std">' . __('Standard','teachpress') . '</option>';
                    echo '<option value="int" selected="selected">' . __('Integrated','teachpress') . '</option>';
                  }
                  else {
				  	echo '<option value="std" selected="selected">' . __('Standard','teachpress') . '</option>';
                    echo '<option value="int">' . __('Integrated','teachpress') . '</option>';
                  } 
                  ?>
                </select>
                </td>
                <td><em><?php _e('Standard - teachPress has a seperate registration. This is usefull if you have an auto login for WordPress or most of your users are registered in your blog, for example in a network.','teachpress'); ?><br /><?php _e('Integrated - teachPress deactivates the own registration and uses all available data from WordPress. This is usefull, if most of your users has not an acount in your blog.','teachpress'); ?></em></td>
              </tr>
              <tr>
              <th><label for="sign_out" title="<?php _e('Prevent sign out','teachpress'); ?>"><?php _e('Prevent sign out','teachpress'); ?></label></th>
              <td style="vertical-align:middle;"><select name="sign_out" id="sign_out">
				  <?php
                  $value = tp_get_option('sign_out');
                  if ($value == '1') {
                    echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0">' . __('no','teachpress') . '</option>';
                  }
                  else {
                    echo '<option value="1">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
                  } 
                  ?>
              </select></td>
              <td><em><?php _e('Prevent sign out for your users','teachpress'); ?></em></td>
              </tr>
              <tr>
              	<th><?php _e('User data fields','teachpress'); ?></th>
                <td>
                 <?php
				$val = tp_get_option('regnum');
				if ($val == '1') {
					$check = ' checked="checked"';
				}
				else {
					$check = '';
				}
				?>
                <input name="matriculation_number_field" id="matriculation_number_field" type="checkbox"<?php echo $check; ?> value="1" /> <label for="matriculation_number_field"><?php _e('Matr. number','teachpress'); ?></label><br />
                <input name="firstname_field" type="checkbox" checked="checked" disabled="disabled" /> <?php _e('First name','teachpress'); ?><br />
                <input name="lastname_field" type="checkbox"checked="checked" disabled="disabled" /> <?php _e('Last name','teachpress'); ?><br />
                <?php
				$val = tp_get_option('studies');
				if ($val == '1') {
					$check = ' checked="checked"';
				}
				else {
					$check = '';
				}
				?>
                <input name="course_of_studies_field" id="course_of_studies_field" type="checkbox"<?php echo $check; ?> value="1" /> <label for="course_of_studies_field"><?php _e('Course of studies','teachpress'); ?></label><br />
                <?php
				$val = tp_get_option('termnumber');
				if ($val == '1') {
					$check = ' checked="checked"';
				}
				else {
					$check = '';
				}
				?>
                <input name="semesternumber_field" id="semesternumber_field" type="checkbox"<?php echo $check; ?> value="1" /> <label for="semesternumber_field"><?php _e('Number of terms','teachpress'); ?></label><br />
                <input name="nutzerkuerzel_field" type="checkbox" checked="checked" disabled="disabled" /> <?php _e('User account','teachpress'); ?><br />
                <?php
				$val = tp_get_option('birthday');
				if ($val == '1') {
					$check = ' checked="checked"';
				}
				else {
					$check = '';
				}
				?>
                <input name="birthday_field" id="birthday_field" type="checkbox"<?php echo $check; ?> value="1" /> <label for="birthday_field"><?php _e('Date of birth','teachpress'); ?></label><br />
                <input name="email_field" type="checkbox" checked="checked" disabled="disabled" /> <?php _e('E-Mail','teachpress'); ?><br />
                </td>
                <td><em><?php _e('Define which fields for the registration form you will use. Some are required.','teachpress'); ?></em></td>
              </tr>
             </thead> 
			</table>
            <h4><?php _e('Uninstalling','teachpress'); ?></h4> 
            <table class="widefat">
            	<thead>
                    <tr>
                      <th width="160"><?php _e('Database','teachpress'); ?></th>
                      <td>
						<?php _e('Remove teachPress from the database:','teachpress'); ?>
                            <input type="radio" name="drop_tp" value="1" id="drop_tp_0" />
                             <label for="drop_tp_0"><?php _e('yes','teachpress'); ?></label>
                            <input type="radio" name="drop_tp" value="0" id="drop_tp_1" checked="checked" />
                            <label for="drop_tp_1"><?php _e('no','teachpress'); ?></label>
                     </td>
                    </tr>
                </thead>
            </table>
			  <p><input name="einstellungen" type="submit" id="teachpress_settings" value="<?php _e('save','teachpress'); ?>" class="button-primary" /></p>
              <?php
	}
	
	/***********/
	/* Courses */
	/***********/
	if ($tab == 'courses') { ?>
    <div style="min-width:780px; width:100%;">
		<div style="width:48%; float:left; padding-right:2%;">
		<h4><strong><?php _e('Courses of studies','teachpress'); ?></strong></h4> 
		  <table class="widefat">
			  <thead>
				  <tr>
					<th>&nbsp;</th>
					<th><?php _e('Name','teachpress'); ?></th>
					<th><?php _e('Number of students','teachpress'); ?></th>
				  </tr>
			  </thead>
		  <?php
		  	$row = "SELECT number, value, setting_id FROM ( SELECT COUNT(s.course_of_studies) as number, e.value AS value,  e.setting_id as setting_id, e.category as category FROM " . $teachpress_settings . " e LEFT JOIN " . $teachpress_stud . " s ON e.value = s.course_of_studies GROUP BY e.value ORDER BY number DESC ) AS temp WHERE category = 'course_of_studies' ORDER BY value";
			$row = $wpdb->get_results($row);
			foreach ($row as $row) { ?>
			  <tr>
				<td><a title="course_of_studies &#8220;<?php echo $row->value; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&amp;delete=<?php echo $row->setting_id; ?>&amp;tab=courses" class="teachpress_delete">X</a></td>
				<td><?php echo $row->value; ?></td>
				<td><?php echo $row->number; ?></td>
			  </tr>
			  <?php } ?>
		  </table>
		  <table class="widefat" style="margin-top:10px;">
			  <thead>
				  <tr>
					<td><input name="name" type="text" id="name" size="30" value="<?php _e('Add course of studies','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Add course of studies','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Add course of studies','teachpress'); ?>') this.value='';"/></td>
					<td><input name="addstud" type="submit" class="teachpress_button" value="<?php _e('create','teachpress'); ?>"/></td>
				  </tr>
			  </thead>
		</table>
        </div>
        <div style="width:48%; float:left; padding-left:2%;">
        <h4><strong><?php _e('Term','teachpress'); ?></strong></h4>
			<table border="0" cellspacing="0" cellpadding="0" class="widefat">
			 <thead>
			  <tr>
				<th>&nbsp;</th>
				<th><?php _e('Term','teachpress'); ?></th>
				<th><?php _e('Number of courses','teachpress'); ?></th>
			  </tr>
			 <?php
			    $row = "SELECT number, value, setting_id FROM ( SELECT COUNT(v.semester) as number, e.variable AS value,  e.setting_id as setting_id, e.category as category FROM " . $teachpress_settings . " e LEFT JOIN " . $teachpress_courses . " v ON e.variable = v.semester GROUP BY e.variable ORDER BY number DESC ) AS temp WHERE category = 'semester' ORDER BY setting_id";
				$row = $wpdb->get_results($row);
				foreach ($row as $row) { ?> 
			  <tr>
				<td><a title="course_of_studies &#8220;<?php echo $row->value; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&amp;delete=<?php echo $row->setting_id; ?>&amp;tab=courses" class="teachpress_delete">X</a></td>
				<td><?php echo $row->value; ?></td>
				<td><?php echo $row->number; ?></td>
			  </tr>
			 <?php } ?> 
			 </thead> 
			</table>
			<table class="widefat" style="margin-top:10px;">
				  <thead>
					  <tr>
						<td><input name="newsem" type="text" id="newsem" size="30" value="<?php _e('Add term','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Add term','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Add term','teachpress'); ?>') this.value='';"/></td>
						<td><input name="addsem" type="submit" class="teachpress_button" value="<?php _e('create','teachpress'); ?>"/></td>
					  </tr>
				  </thead>
			</table>
            <h4><strong><?php _e('Types of courses','teachpress'); ?></strong></h4> 
			 <table border="0" cellspacing="0" cellpadding="0" class="widefat">
				<thead>
				  <tr>
					<th>&nbsp;</th>
					<th><?php _e('Name','teachpress'); ?></th>
					<th><?php _e('Number of courses','teachpress'); ?></th>
				  </tr>
				</thead>
			<?php    
				$row = "SELECT number, value, setting_id FROM ( SELECT COUNT(v.type) as number, e.value AS value,  e.setting_id as setting_id, e.category as category FROM " . $teachpress_settings . " e LEFT JOIN " . $teachpress_courses . " v ON e.value = v.type GROUP BY e.value ORDER BY number DESC ) AS temp WHERE category = 'course_type' ORDER BY value";
				$row = $wpdb->get_results($row);
				foreach ($row as $row) { ?>  
			  <tr>
				<td><a title="Kategorie &#8220;<?php echo $row->value; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&amp;delete=<?php echo $row->setting_id; ?>&amp;tab=courses" class="teachpress_delete">X</a></td>
				<td><?php echo $row->value; ?></td>
				<td><?php echo $row->number; ?></td>
			  </tr>
			  <?php } ?>  
		   </table>  
		   <table class="widefat" style="margin-top:10px;">
			  <thead>
				  <tr>
					<td><input name="typ" type="text" id="typ" size="30" value="<?php _e('Add type','teachpress'); ?>" onblur="if(this.value=='') this.value='<?php _e('Add type','teachpress'); ?>';" onfocus="if(this.value=='<?php _e('Add type','teachpress'); ?>') this.value='';"/></td>
					<td><input name="addtyp" type="submit" class="teachpress_button" value="<?php _e('create','teachpress'); ?>"/></td>
				  </tr>
			  </thead>
		   </table>
       </div>    
    </div>       
    <?php
	}
	
	/****************/
	/* Publications */
	/****************/
	if ($tab == 'publications') {?>
    <table class="widefat">
    	<thead>
    	<tr>
        	<th width="160"><?php _e('RSS feed addresses','teachpress'); ?></th>
            <td><p><em><?php _e('For all publications:','teachpress'); ?></em><br />
            	<strong><?php echo WP_PLUGIN_URL . '/teachpress/feed.php'; ?></strong> &raquo; <a href="<?php echo WP_PLUGIN_URL . '../../../../teachpress/feed.php'; ?>" target="_blank"><?php _e('show','teachpress'); ?></a></p>
            	<p><em><?php _e('Example for publications of a single user (id = WordPress user-ID):','teachpress'); ?></em><br />
            	<strong><?php echo WP_PLUGIN_URL . '/teachpress/feed.php?id=1'; ?></strong> &raquo; <a href="<?php echo WP_PLUGIN_URL . '../../../../teachpress/feed.php'; ?>" target="_blank"><?php _e('show','teachpress'); ?></a></p>
                <p><em><?php _e('Example for publications of a single tag (tag = tag-id):','teachpress'); ?></em><br />
            	<strong><?php echo WP_PLUGIN_URL . '/teachpress/feed.php?tag=1'; ?></strong> &raquo; <a href="<?php echo WP_PLUGIN_URL . '../../../../teachpress/feed.php'; ?>" target="_blank"><?php _e('show','teachpress'); ?></a></p>
            </td>
        </tr>
        </thead>
    </table>
    <p><?php _e('Keep in mind that this feeds only work, if you have defined the path for the WordPress directory in parameters.php correctly (You find the file in the teachPress plugin directory).','teachpress'); ?></p>
    <?php
	}
	?>   
    </form>
    </div> 
</div>
<?php } ?>