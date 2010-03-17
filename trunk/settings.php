<?php
/*
 * Setting page
*/ 
function teachpress_admin_settings() { 
?> 
<div class="wrap">
    <h2 style="padding-bottom:0px;"><?php _e('teachPress settings','teachpress'); ?> <span class="tp_break">|</span> <small><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('Help','teachpress'); ?></a></small></h2> 
    <div id="hilfe_anzeigen">
    	<h3 class="teachpress_help"><?php _e('Help','teachpress'); ?></h3>
        <p class="hilfe_headline"><?php _e('Current term','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Here you can change the current term. This value is used for the default settings for all menus.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Permalinks','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('Here you can specify, if your WordPress installation using permalinks or not.','teachpress'); ?></p>
        <p class="hilfe_headline"><?php _e('Terms, Courses of studies, Types of courses','teachpress'); ?></p>
        <p class="hilfe_text"><?php _e('If you delete types, terms or courses of studies the connected data will not be deleted.','teachpress'); ?></p>
        <p class="hilfe_close"><strong><a onclick="teachpress_showhide('hilfe_anzeigen')" style="cursor:pointer;"><?php _e('close','teachpress'); ?></a></strong></p>
        
    </div>
    <?php
	global $teachpress_einstellungen; 
	global $teachpress_stud; 
	global $teachpress_ver;
	// Formularvariablen von settings.php
	$semester = htmlentities(utf8_decode($_GET[semester]));
	$permalink = htmlentities(utf8_decode($_GET[permalink]));
	$sign_out = htmlentities(utf8_decode($_GET[sign_out]));
	$userrole = $_GET[userrole];
	$einstellungen = $_GET[einstellungen];
	$delete = $_GET[delete];
	$name = htmlentities(utf8_decode($_GET[name]));
	$typ = htmlentities(utf8_decode($_GET[typ]));
	$newsem = htmlentities(utf8_decode($_GET[newsem]));
	$addstud = $_GET[addstud];
	$addtyp = $_GET[addtyp];
	$addsem = $_GET[addsem];
	$site = 'options-general.php?page=teachpress/settings.php';
	// Aktionen ausfuehren und Nachrichten ausgeben
	if ($_GET[up] == 1) {
		tp_db_update();
	}
	if ($_GET[ins] == 1) {
		teachpress_install();
	}
	if (isset($einstellungen)) {
		change_einstellungen($semester, $permalink, $userrole, $sign_out);
		$message = __('Settings updated','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addstud) && $name != __('Add course of studies','teachpress')) {
		add_einstellung($name, 'studiengang');
		$message = __('Course of studies added','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addtyp) && $typ != __('Add type','teachpress')) {
		add_einstellung($typ, 'veranstaltungstyp');
		$message = __('Type added','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($addsem) && $newsem != __('Add term','teachpress')) {
		add_einstellung($newsem, 'semester');
		$message = __('Term added','teachpress');
		tp_get_message($message, $site);
	}
	if (isset($delete)) {
		delete_einstellung($delete);
	} 
	?>
   
  <div id="einstellungen" style="float:left; width:97%;">
  <form id="form1" name="form1" method="get" action="<?php echo $PHP_SELF ?>">
	<input name="page" type="hidden" value="teachpress/settings.php">
	<div style="min-width:780px; width:100%;">
	<div style="width:48%; float:left; padding-right:2%;">
		 <div>
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
		  	$row = "SELECT number, wert, einstellungs_id FROM ( SELECT COUNT(s.studiengang) as number, e.wert AS wert,  e.einstellungs_id as einstellungs_id, e.category as category FROM " . $teachpress_einstellungen . " e LEFT JOIN " . $teachpress_stud . " s ON e.wert = s.studiengang GROUP BY e.wert ORDER BY number DESC ) AS temp WHERE category = 'studiengang' ORDER BY wert";
			$row = tp_results($row);
			foreach ($row as $row) { ?>
			  <tr>
				<td><a title="Studiengang &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
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
		 <div style="padding-top:10px;">
			<h4><strong><?php _e('Term','teachpress'); ?></strong></h4>
			<table border="0" cellspacing="0" cellpadding="0" class="widefat">
			 <thead>
			  <tr>
				<th>&nbsp;</th>
				<th><?php _e('Term','teachpress'); ?></th>
				<th><?php _e('Number of courses','teachpress'); ?></th>
			  </tr>
			 <?php
			    $row = "SELECT number, wert, einstellungs_id FROM ( SELECT COUNT(v.semester) as number, e.variable AS wert,  e.einstellungs_id as einstellungs_id, e.category as category FROM " . $teachpress_einstellungen . " e LEFT JOIN " . $teachpress_ver . " v ON e.variable = v.semester GROUP BY e.variable ORDER BY number DESC ) AS temp WHERE category = 'semester' ORDER BY einstellungs_id";
				$row = tp_results($row);
				foreach ($row as $row) { ?> 
			  <tr>
				<td><a title="Studiengang &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
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
			</div>
	</div>
	<div style="width:48%; float:left; padding-left:2%;">
		<div>
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
				$row = "SELECT number, wert, einstellungs_id FROM ( SELECT COUNT(v.vtyp) as number, e.wert AS wert,  e.einstellungs_id as einstellungs_id, e.category as category FROM " . $teachpress_einstellungen . " e LEFT JOIN " . $teachpress_ver . " v ON e.wert = v.vtyp GROUP BY e.wert ORDER BY number DESC ) AS temp WHERE category = 'veranstaltungstyp' ORDER BY wert";
				$row = tp_results($row);
				foreach ($row as $row) { ?>  
			  <tr>
				<td><a title="Kategorie &#8220;<?php echo $row->wert; ?>&#8221; l&ouml;schen" href="options-general.php?page=teachpress/settings.php&delete=<?php echo $row->einstellungs_id; ?>" class="teachpress_delete">X</a></td>
				<td><?php echo $row->wert; ?></td>
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
		<div style="padding-top:10px;">
			<h4><strong><?php _e('General','teachpress'); ?></strong></h4>
			<table border="0" cellspacing="0" cellpadding="0" class="widefat">
			 <thead>
			  <tr>
				<th><label title="<?php _e('Shows the teachPress database version and available database updates','teachpress'); ?>"><?php _e('teachPress version','teachpress'); ?></label></th>
				<td><?php 
					// Test ob Datenbank installiert ist
					$test = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'db-version'";
					$test = tp_var($test);
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
						echo '<a href="options-general.php?page=teachpress/settings.php&ins=1"><strong>' . __('install','teachpress') . '</strong></a>';
					} ?>
               </td>
			  </tr>
			  <tr>
				<th><label for="semester" title="<?php _e('Here you can change the current term. This value is used for the default settings for all menus.','teachpress'); ?>"><?php _e('Current term','teachpress'); ?></label></th>
				<td><select name="semester" id="semester">
						<?php
						$abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
						$wert = tp_var($abfrage);   
					    $sem = "SELECT einstellungs_id, wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id DESC";
						$sem = tp_results($sem);
						foreach ($sem as $sem) { 
							if ($sem->wert == $wert) {
								$current = 'selected="selected"' ;
							}
							else {
								$current = '' ;
							} 
							echo '<option value="' . $sem->wert . '" ' . $current . '>' . $sem->wert . '</option>';
						} ?>    
					</select></td>
			  </tr>
			  <tr>
				<th><label for="permalink" title="<?php _e('Here you can specify, if your WordPress installation using permalinks or not.','teachpress'); ?>"><?php _e('Permalinks','teachpress'); ?></label></th>
				<td><select name="permalink" id="permalink">
					  <?php
					  $abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'permalink'";
					  $wert = tp_var($abfrage);
					  if ($wert == 1) {
					  	echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
					  	echo '<option value="0">' . __('no','teachpress') . '</option>';
					  }
					  else {
                      	echo '<option value="1">' . __('yes','teachpress') . '</option>';
					  	echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
					  } 
					  ?>
					</select></td>
			  </tr>
              <tr>
              <th><label for="sign_out" title="<?php _e('Prevent sign out for your users','teachpress'); ?>"><?php _e('Prevent sign out','teachpress'); ?></label></th>
              <td><select name="sign_out" id="sign_out">
				  <?php
                  $abfrage = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sign_out'";
                  $wert = tp_var($abfrage);
                  if ($wert == 1) {
                    echo '<option value="1" selected="selected">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0">' . __('no','teachpress') . '</option>';
                  }
                  else {
                    echo '<option value="1">' . __('yes','teachpress') . '</option>';
                    echo '<option value="0" selected="selected">' . __('no','teachpress') . '</option>';
                  } 
                  ?>
              </select></td>
              </tr>
              <tr>
              	<th><label for="userrole" title="<?php _e('Select which userrole your users must have to use the teachPress backend.','teachpress'); ?>"><?php _e('Backend access for','teachpress'); ?></label></th>
                <td>
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
					<small class="setting-description"><?php _e('use &lt;Ctrl&gt; key to select multiple roles','teachpress'); ?></small>
                </td>
              </tr>
			 </thead> 
			</table>
			  <p style="padding-left:20px;"><input name="einstellungen" type="submit" id="teachpress_einstellungen" value="<?php _e('save','teachpress'); ?>" class="teachpress_button"/></p>
		</div>
	</div>
     </div>   
    </form>
    </div> 
</div>
<?php } ?>