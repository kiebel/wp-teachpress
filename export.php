<?php
/*
 * teachPress XLS and CSV export for courses
*/

// include wp-load.php
require_once( '../../../wp-load.php' );
if ( is_user_logged_in() && current_user_can('use_teachpress') ) {
     $course_ID = isset ( $_GET['course_ID'] ) ? (int) $_GET['course_ID'] : 0;
     $user_ID = isset ( $_POST['tp_user'] ) ? (int) $_POST['tp_user'] : 0;
     $format = isset ( $_POST['tp_format'] ) ?  htmlspecialchars($_POST['tp_format']) : '';
     $type = isset ( $_GET['type'] ) ? htmlspecialchars($_GET['type']) : '';
     $filename = 'teachpress_' . date('dmY');

     if ($type == "xls" && $course_ID != 0) {
          header("Content-type: application/vnd-ms-excel; charset=utf-8");
          header("Content-Disposition: attachment; filename=" . $filename . ".xls");
          tp_export::get_course_xls($course_ID);
     }
     
     if ($type == 'csv' && $course_ID != 0) {
          header('Content-Type: text/x-csv');
          header("Content-Disposition: attachment; filename=" . $filename . ".csv");
          tp_export::get_course_csv($course_ID);
     }
     
     if ($type == 'pub') {
          if ($format == 'bibtex') {
               header('Content-Type: text/plain; charset=utf-8' );
               header("Content-Disposition: attachment; filename=" . $filename . ".txt");
               tp_export::get_publication($user_ID,'bibtex');
          }
          if ($format == 'rtf') {
               header('Content-Type: text/plain; charset=utf-8' );
               header("Content-Disposition: attachment; filename=" . $filename . ".rtf");
               tp_export::get_publication($user_ID,'rtf');
          }
     }
} ?>   