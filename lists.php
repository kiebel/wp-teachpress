<?php 
/* Erzeugung von Anwesenheitslisten zum ausdrucken
 * from editlvs.php (GET):
 * @param $lvs_ID
 * @param $search
 * @param $sememster2
*/
function teachpress_lists_page () {
// fuer Zurueckleitung an editlvs.php
$search = htmlentities(utf8_decode($_GET[search]));
$sem = htmlentities(utf8_decode($_GET[sem]));
// von editlvs.php
$weiter = htmlentities(utf8_decode($_GET[lvs_ID]));
settype($weiter, 'integer');
?>
<div class="wrap" style="padding-top:10px;">
    <a href="admin.php?page=teachpress/editlvs.php&lvs_ID=<?php echo"$weiter"; ?>&sem=<?php echo"$sem" ?>&search=<?php echo"$search" ?>" class="teachpress_back" title="<?php _e('back to the course','teachpress'); ?>">&larr; <?php _e('back','teachpress'); ?></a>
    <?php
    global $teachpress_ver; 
    global $teachpress_stud; 
    global $teachpress_einstellungen; 
    global $teachpress_kursbelegung;
	$veranstaltung = $_GET[lvs_ID];
	$row2 = "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$weiter'";
	$row2 = tp_results($row2);
	foreach($row2 as $row2) {
	   // Ausgabe der Infos zur gewählten LVS mit integriertem Änderungsformular
	   ?>
       <form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
       <input name="page" type="hidden" value="teachpress/editlvs.php">
       <input name="lvs_ID" type="hidden" value="<?php echo"$row2->veranstaltungs_id" ?>">
        <h2><?php echo"$row2->name" ?> <?php echo"$row2->semester" ?></h2>
        <div id="einschreibungen" style="padding:5px;">
        <div style="width:700px; padding-bottom:10px;">
      		<table border="1" cellspacing="0" cellpadding="0" class="tp_print">
                  <tr>
                    <th><?php _e('Lecturer','teachpress'); ?></th>
                    <td><?php echo"$row2->dozent" ?></td>
                    <th><?php _e('Date','teachpress'); ?></th>
                    <td><?php echo"$row2->termin" ?></td>
                    <th><?php _e('Room','teachpress'); ?></th>
                    <td><?php echo"$row2->raum" ?></td>
                  </tr>
            </table>
        </div>
        <table border="1" cellpadding="0" cellspacing="0" class="tp_print" width="100%">
          <tr style="border-collapse: collapse; border: 1px solid black;">
            <th height="100" width="250"><?php _e('Name','teachpress'); ?></th>
            <th width="81" ><?php _e('User account','teachpress'); ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
         <tbody> 
    <?php         
	   }
	   // Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
	$row = "SELECT " . $teachpress_stud . ".vorname, " . $teachpress_stud . ".nachname, " . $teachpress_stud . ".urzkurz
			FROM " . $teachpress_kursbelegung . " 
			INNER JOIN " . $teachpress_ver . " ON " . $teachpress_ver . ".veranstaltungs_id=" . $teachpress_kursbelegung . ".veranstaltungs_id
			INNER JOIN " . $teachpress_stud . " ON " . $teachpress_stud . ".wp_id=" . $teachpress_kursbelegung . ".wp_id
			WHERE " . $teachpress_ver . ".veranstaltungs_id = '$veranstaltung'
			ORDER BY " . $teachpress_stud . ".nachname";
	$row = tp_results($row);
	foreach($row as $row3)
 	  {
	  ?>
  	  <tr>
        <td><?php echo"$row3->nachname" ?>, <?php echo"$row3->vorname" ?></td>
        <td><?php echo"$row3->urzkurz" ?></td>
        <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	    <td>&nbsp;</td>
  	  </tr>
      <?php
   }
?>
</tbody>
</table>
</form>
</div>
<?php } ?>