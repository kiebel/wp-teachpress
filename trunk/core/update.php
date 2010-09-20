<?php
function tp_db_update_function() {	

	global $wpdb;
    global $teachpress_settings;
	global $teachpress_pub;
	global $teachpress_courses;
	global $teachpress_signup;
	global $teachpress_relation;
	global $teachpress_stud;
	global $teachpress_tags;
	global $teachpress_user;
	// teachpress 0.x/1.x table names
	$teachpress_ver = $wpdb->prefix . 'teachpress_ver';
	$teachpress_beziehung = $wpdb->prefix . 'teachpress_beziehung';
	$teachpress_kursbelegung = $wpdb->prefix . 'teachpress_kursbelegung';
	$teachpress_einstellungen = $wpdb->prefix . 'teachpress_einstellungen';
	
	// test if teachpress database is up to date
	$test = tp_get_option('db-version');
	$version = get_tp_version();
	// if is the actual one
	if ($test == $version) {
		$message = __('An update is not necessary.','teachpress');
		tp_get_message($message);
	} 
	else {
		// charset & collate like WordPress
		$charset_collate = '';
		if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
			if ( ! empty($wpdb->charset) ) {
				$charset_collate = "CHARACTER SET $wpdb->charset";
			}
			else {
				$charset_collate = "CHARACTER SET utf8";
			}
			if ( ! empty($wpdb->collate) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
			else {
				$charset_collate .= " COLLATE utf8_general_ci";
			}
		}
		
		/*
		 * Capabilities
		*/
		global $wp_roles;
		$wp_roles->WP_Roles();
		$role = $wp_roles->get_role('administrator');
		if ( !$role->has_cap('use_teachpress') ) {
			$wp_roles->add_cap('administrator', 'use_teachpress');
		}
		
		/****************************************************************/
		/* Upgrade from teachpress 0.x and 1.x series to teachpress 2.0 */
		/****************************************************************/
		
		/*
		 * teachpress courses
		*/ 
		$sql = "SHOW COLUMNS FROM " . $teachpress_ver . " LIKE 'veranstaltungs_id'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$table_name = $teachpress_courses;
			// create new table teachpress_courses
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $teachpress_courses. " (
								 course_id INT UNSIGNED AUTO_INCREMENT ,
								 name VARCHAR(100) ,
								 type VARCHAR (100) ,
								 room VARCHAR(100) ,
								 lecturer VARCHAR (100) ,
								 date VARCHAR(60) ,
								 places INT(4) ,
								 fplaces INT(4) ,
								 start DATE ,
								 end DATE ,
								 semester VARCHAR(100) ,
								 comment VARCHAR(500) ,
								 rel_page INT ,
								 parent INT(4) ,
								 visible INT(1) ,
								 waitinglist INT(1),
								 image_url VARCHAR(400) ,
								 PRIMARY KEY (course_id)
							   ) $charset_collate;";			
				$wpdb->query($sql);
			}
			// copy all data
			$sql = "SELECT * FROM " . $teachpress_ver . "";
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				$eintragen = "INSERT INTO " . $teachpress_courses . " (`course_id`, `name`, `type`, `room`, `lecturer`, `date`, `places`, `fplaces`, `start`, `end`, `semester`, `comment`, `rel_page`, `parent`, `visible`, `waitinglist`) VALUES('$row->veranstaltungs_id', '$row->name', '$row->vtyp', '$row->raum', '$row->dozent', '$row->termin', '$row->plaetze', '$row->fplaetze', '$row->startein', '$row->endein', '$row->semester', '$row->bemerkungen', '$row->rel_page', '$row->parent', '$row->sichtbar', '$row->warteliste')";
				$wpdb->query($eintragen);
			}
			// delete old table
			$wpdb->query("DROP TABLE " . $teachpress_ver . "");
			// get message
			echo '<p>' . __('Table for courses updated.','teachpress') . '</p>';
		}
		
		/*
		 * teachpress_relation
		*/ 
		$sql = "SHOW COLUMNS FROM " . $teachpress_beziehung . " LIKE 'belegungs_id'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			// create new table teachpress_relation
			$table_name = $teachpress_relation;
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $teachpress_relation . " (
								 con_id INT UNSIGNED AUTO_INCREMENT ,
								 pub_id INT ,
								 tag_id INT ,
								 FOREIGN KEY (pub_id) REFERENCES " . $teachpress_pub. "(pub_id) ,
								 FOREIGN KEY (tag_id) REFERENCES " . $teachpress_tags . "(tag_id) ,
								 PRIMARY KEY (con_id)
							   ) $charset_collate;";		   		   
				$wpdb->query($sql);
			}
			// copy all data
			$sql = "SELECT * FROM " . $teachpress_beziehung . "";
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				$eintragen = "INSERT INTO " . $teachpress_relation . " (`con_id`, `pub_id`, `tag_id`) VALUES('$row->belegungs_id', '$row->pub_id', '$row->tag_id')";
				$wpdb->query($eintragen);
			}
			// delete old table
			$wpdb->query("DROP TABLE " . $teachpress_beziehung . "");
			// get message
			echo '<p>' . __('Table for relations updated.','teachpress') . '</p>';
		}
		
		/*
		 * teachpress_signup
		*/
		$sql = "SHOW COLUMNS FROM " . $teachpress_kursbelegung . " LIKE 'belegungs_id'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			// create new table teachpress_signup
			$table_name = $teachpress_signup;
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $teachpress_signup . " (
								 con_id INT UNSIGNED AUTO_INCREMENT ,
								 course_id INT ,
								 wp_id INT ,
								 waitinglist INT(1) ,
								 date DATE ,
								 FOREIGN KEY (course_id) REFERENCES " . $teachpress_courses. "(course_id) ,
								 FOREIGN KEY (wp_id) REFERENCES " . $teachpress_stud . "(wp_id) ,
								 PRIMARY KEY (con_id)
							   ) $charset_collate;";
				$wpdb->query($sql);
			 }
			// copy all data
			$sql = "SELECT * FROM " . $teachpress_kursbelegung . "";
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				$eintragen = "INSERT INTO " . $teachpress_signup . " (`con_id`, `course_id`, `wp_id`, `waitinglist`, `date`) VALUES('$row->belegungs_id', '$row->veranstaltungs_id', '$row->wp_id', '$row->warteliste', '$row->datum')";
				$wpdb->query($eintragen);
			}
			// delete old table
			$wpdb->query("DROP TABLE " . $teachpress_kursbelegung . "");
			// get message
			echo '<p>' . __('Table for enrollments updated.','teachpress') . '</p>';
		}
		
		/*
		 * teachpress_settings
		*/
		$sql = "SHOW COLUMNS FROM " . $teachpress_einstellungen . " LIKE 'einstellungs_id'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			// create new table teachpress_settings
			$table_name = $teachpress_settings;
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $teachpress_settings . " (
								setting_id INT UNSIGNED AUTO_INCREMENT ,
								variable VARCHAR (100) ,
								value VARCHAR (100) ,
								category VARCHAR (100) ,
								PRIMARY KEY (setting_id)
								) $charset_collate;";				
				$wpdb->query($sql);
			}
			// copy all data
			$sql = "SELECT * FROM " . $teachpress_einstellungen . "";
			$row = $wpdb->get_results($sql);
			foreach ($row as $row) {
				if ($row->category == 'studiengang') {
					$row->category = 'course_of_studies';
				}
				if ($row->category == 'veranstaltungstyp') {
					$row->category = 'course_type';
				}
				$eintragen = "INSERT INTO " . $teachpress_settings . " (`setting_id`, `variable`, `value`, `category`) VALUES('$row->einstellungs_id', '$row->variable', '$row->wert', '$row->category')";
				$wpdb->query($eintragen);
			}
			// delete old table
			$wpdb->query("DROP TABLE " . $teachpress_einstellungen . "");
			// get message
			echo '<p>' . __('Table for settings updated.','teachpress') . '</p>';
		}
		/*
		 * teachpress_students
		*/
		// rename column vorname to firstname
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'vorname'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `vorname`  `firstname` VARCHAR( 100 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// rename column nachname to lastname
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'nachname'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `nachname`  `lastname` VARCHAR( 100 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// rename column studiengang to course_of_studies
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'studiengang'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `studiengang`  `course_of_studies` VARCHAR( 100 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// rename column urzkurz to userlogin
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'urzkurz'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `urzkurz`  `userlogin` VARCHAR( 100 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// rename column gebdat to birthday
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'gebdat'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `gebdat`  `birthday` VARCHAR( 100 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// rename column fachsemester to semesternumber
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'fachsemester'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `fachsemester`  `semesternumber` INT(2) NULL DEFAULT NULL");
		}
		// rename column matrikel to matriculation_number
		$sql = "SHOW COLUMNS FROM " . $teachpress_stud . " LIKE 'matrikel'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_stud . " CHANGE  `matrikel`  `matriculation_number` INT NULL DEFAULT NULL");
			// get message
			echo '<p>' . __('Table for students updated.','teachpress') . '</p>';
		}
		
		/*
		 * teachpress_pub
		*/ 
		// add column image_url
		// since version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'image_url'";
		$test = $wpdb->query($sql);
		if ($test == '0') { 
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `image_url` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `comment`");
		}
		// add colum rel_page
		// since version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'rel_page'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `rel_page` INT NULL AFTER `image_url`");
		}
		// add column is_isbn
		// since version 0.40
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'is_isbn'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `is_isbn` INT(1) NULL DEFAULT NULL AFTER `rel_page`");
		}
		// Rename sort to date
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'sort'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " CHANGE  `sort`  `date` DATE NULL DEFAULT NULL");
		}
		// Rename typ to type
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'typ'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " CHANGE `typ`  `type` VARCHAR( 50 ) " . $charset_collate . " NULL DEFAULT NULL");
			// remane publication types
			$row = $wpdb->get_results("SELECT pub_id, type  FROM " . $teachpress_pub . "");
			foreach ($row as $row) {
				if ($row->type == 'Buch') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'book' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Chapter in book') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'inbook' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Conference paper') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'proceedings' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Journal article') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'article' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Vortrag') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'presentation' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Bericht') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'techreport' WHERE pub_id = '$row->pub_id'");
				}
				if ($row->type == 'Sonstiges') {
					$wpdb->query("UPDATE " . $teachpress_pub . " SET type = 'misc' WHERE pub_id = '$row->pub_id'");
				}
			}
		}
		// Rename autor to author
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'autor'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " CHANGE `autor` `author` VARCHAR( 500 ) " . $charset_collate . " NULL DEFAULT NULL");
		}
		// Drop column jahr
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'jahr'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " DROP `jahr`");
		}
		// insert column bibtex
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'bibtex'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `bibtex` VARCHAR(50) " . $charset_collate . " NULL DEFAULT NULL AFTER `type`");
		}
		// insert column editor
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'editor'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `editor` VARCHAR(500) " . $charset_collate . " NULL DEFAULT NULL AFTER `author`");
		}
		// insert column booktitle
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'booktitle'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `booktitle` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `date`");
		}
		// insert column journal
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'journal'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `journal` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `booktitle`");
		}
		// insert column volume
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'volume'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `volume` VARCHAR(20) " . $charset_collate . " NULL DEFAULT NULL AFTER `journal`");
		}
		// insert column number
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'number'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `number` VARCHAR(20) " . $charset_collate . " NULL DEFAULT NULL AFTER `volume`");
		}
		// insert column pages
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'pages'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `pages` VARCHAR(20) " . $charset_collate . " NULL DEFAULT NULL AFTER `number`");
		}
		// insert column publisher
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'publisher'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `publisher` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `pages`");
		}
		// insert column address
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'address'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `address` VARCHAR(300) " . $charset_collate . " NULL DEFAULT NULL AFTER `publisher`");
		}
		// insert column edition
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'edition'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `edition` VARCHAR(100) " . $charset_collate . " NULL DEFAULT NULL AFTER `address`");
		}
		// insert column chapter
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'chapter'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `chapter` VARCHAR(20) " . $charset_collate . " NULL DEFAULT NULL AFTER `edition`");
		}
		// insert column institution
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'institution'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `institution` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `chapter`");
		}
		// insert column organization
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'organization'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `organization` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `institution`");
		}
		// insert column school
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'school'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `school` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `organization`");
		}
		// insert column series
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'series'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `series` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `school`");
		}
		// insert column crossref
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'crossref'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `crossref` VARCHAR(100) " . $charset_collate . " NULL DEFAULT NULL AFTER `series`");
		}
		// insert column abstract
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'abstract'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `abstract` TEXT " . $charset_collate . " NULL DEFAULT NULL AFTER `crossref`");
		}
		// insert column howpublished
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'howpublished'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `howpublished` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `abstract`");
		}
		// insert column key
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'key'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `key` VARCHAR(100) " . $charset_collate . " NULL DEFAULT NULL AFTER `howpublished`");
		}
		// insert column techtype
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'techtype'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `techtype` VARCHAR(200) " . $charset_collate . " NULL DEFAULT NULL AFTER `key`");
		}
		// insert column note
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'note'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " ADD `note` TEXT " . $charset_collate . " NULL DEFAULT NULL AFTER `comment`");
		}
		// drop column verlag
		// since version 2.0
		$sql = "SHOW COLUMNS FROM " . $teachpress_pub . " LIKE 'verlag'";
		$test = $wpdb->query($sql);
		if ($test == '1') {
			$row = $wpdb->get_results("SELECT pub_id, verlag  FROM " . $teachpress_pub . "");
			foreach ($row as $row) {
				$wpdb->query("UPDATE " . $teachpress_pub . " SET editor = '$row->verlag' WHERE pub_id = '$row->pub_id'");
			}
			$wpdb->query("ALTER TABLE " . $teachpress_pub . " DROP `verlag`");
			echo '<p>' . __('Table for publications updated.','teachpress') . '</p>';
		}
		
		/******************************/
		/* End teachPress 2.0 Upgrade */
		/******************************/
		
		/*
		 * teachpress_settings
		*/
		// Stylesheet
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'stylesheet'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('stylesheet', '1', 'system')"); 
		}
		// Sign out
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'sign_out'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('sign_out', '0', 'system')"); 
		}
		// Login
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'login'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('login', 'std', 'system')"); 
		}
		// Registration number
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'regnum'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('regnum', '1', 'system')"); 
		}
		// Studies
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'studies'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('studies', '1', 'system')"); 
		}
		// Termnumber
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'termnumber'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('termnumber', '1', 'system')");
		}
		// Birthday
		$sql = "SELECT value FROM " . $teachpress_settings . " WHERE variable = 'birthday'";
		$test = $wpdb->query($sql);
		if ($test == '0') {
			$wpdb->query("INSERT INTO " . $teachpress_settings . " (variable, value, category) VALUES ('birthday', '1', 'system')"); 
		}
		
		// Update version information in the database
		$wpdb->query("UPDATE " . $teachpress_settings . " SET  value = '$version' WHERE variable = 'db-version'");
		// Finalize
		$message = __('Update successful','teachpress');
		tp_get_message($message);
	}
}
?>    