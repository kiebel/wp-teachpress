<?php
// Define your wp-root directory. It's the part between your wp-load.php and the value of $_SERVER['DOCUMENT_ROOT']
$root = 'wordpress';

// Define teachpress database tables, change it, if you will install teachpress in other tables. Every name must be unique.
$teachpress_ver = 'teachpress_ver'; //Events
$teachpress_stud = 'teachpress_stud'; //Students
$teachpress_einstellungen = 'teachpress_einstellungen'; //Settings
$teachpress_kursbelegung = 'teachpress_kursbelegung'; //Enrollments
$teachpress_log = 'teachpress_log'; // Security-Log
$teachpress_pub = 'teachpress_pub'; //Publications
$teachpress_tags = 'teachpress_tags'; //Tags
$teachpress_beziehung = 'teachpress_beziehung'; //Relationsship Tags - Publikationen
$teachpress_user = 'teachpress_user'; // Relationship Publikation - User
?>