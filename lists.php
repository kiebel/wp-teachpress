<?php 
/* Create attendance lists
 * from editlvs.php (GET):
 * @param $lvs_ID
 * @param $search
 * @param $sem
*/
function teachpress_lists_page () {
// tables
global $teachpress_ver; 
global $teachpress_stud; 
global $teachpress_einstellungen; 
global $teachpress_kursbelegung;
// from editlvs.php
$weiter = tp_sec_var($_GET[lvs_ID], 'integer');
$search = tp_sec_var($_GET[search]);
$sem = tp_sec_var($_GET[sem]);
$sort = tp_sec_var($_GET[sort]);
$matrikel_field = tp_sec_var($_GET[matrikel_field]);
$nutzerkuerzel_field = tp_sec_var($_GET[nutzerkuerzel_field]);
$studiengang_field = tp_sec_var($_GET[studiengang_field]);
$fachsemester_field = tp_sec_var($_GET[fachsemester_field]);
$gebdat_field = tp_sec_var($_GET[gebdat_field]);
$email_field = tp_sec_var($_GET[email_field]);
// lists.php
$anzahl = tp_sec_var($_GET[anzahl], 'integer');
$create = $_GET[create];
?>
<div class="wrap" style="padding-top:10px;">
<?php if ($create == '') {
	echo '<a href="admin.php?page=teachpress/editlvs.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '" class="teachpress_back" title="' . __('back to the course','teachpress') . '">&larr; ' . __('back','teachpress') . '</a>';
}
else {
	echo '<a href="admin.php?page=teachpress/lists.php&lvs_ID=' . $weiter . '&sem=' . $sem . '&search=' . $search . '" class="teachpress_back" title="' . __('back to the course','teachpress') . '">&larr; ' . __('back','teachpress') . '</a>';
}?>
<form id="einzel" name="einzel" action="<?php echo $PHP_SELF ?>" method="get">
<input name="page" type="hidden" value="teachpress/lists.php"/>
<input name="lvs_ID" type="hidden" value="<?php echo $weiter; ?>"/>
<input name="sem" type="hidden" value="<?php echo $sem; ?>" />
<input name="search" type="hidden" value="<?php echo $search; ?>" />
<?php if ($create == '') {?>
<div style="padding:10px 0 10px 30px;">
<h4><?php _e('Setup attendance list','teachpress'); ?></h4>
<table class="widefat" style="width:400px;">
	<thead>
     <tr>
    	<th><label for="anzahl"><?php _e('Sort after','teachpress'); ?></label></th>
        <td><select name="sort" id="sort">
        		<option value="1"><?php _e('Last name','teachpress'); ?></option>
                <?php 
				$val = tp_get_option('regnum');
				if ($val == '1') {?>
                <option value="2"><?php _e('Registr.-Number','teachpress'); ?></option>
                <?php } ?>
			</select>
        </td>
    </tr>
    <tr>
    	<th style="width:160px;"><label for="anzahl"><?php _e('Number of free columns','teachpress'); ?></label></th>
        <td><select name="anzahl" id="anzahl">
				<?php
                for ($i=1; $i<=15; $i++) {
                    if ($i == 7) {
                        echo '<option value="' . $i . '" selected="selected">' . $i . '</option>';
                    }
                    else {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }	
                } ?>
            </select>
        </td>
    </tr>
    <tr>
    	<th><?php _e('Additional columns','teachpress'); ?></th>
        <td>
         <?php
			if ($val == '1') {
				echo '<input name="matrikel_field" id="matrikel_field" type="checkbox" value="1" /> <label for="matrikel_field">' . __('Registr.-Number','teachpress') . '</label><br />';
			}
			echo '<input name="nutzerkuerzel_field" id="nutzerkuerzel_field" type="checkbox" checked="checked" value="1" /> <label for="nutzerkuerzel_field">' . __('User account','teachpress') . '</label><br />';
			$val = tp_get_option('studies');
			if ($val == '1') {
				echo '<input name="studiengang_field" id="studiengang_field" type="checkbox" value="1" /> <label for="studiengang_field">' . __('Course of studies','teachpress') . '</label><br />';
			}
			$val = tp_get_option('termnumber');
			if ($val == '1') {
				echo '<input name="fachsemester_field" id="fachsemester_field" type="checkbox" value="1" /> <label for="fachsemester_field">' . __('Number of terms','teachpress') . '</label><br />';
			}
			$val = tp_get_option('birthday');
			if ($val == '1') {
				echo '<input name="gebdat_field" id="gebdat_field" type="checkbox" value="1" /> <label for="gebdat_field">' .  __('Date of birth','teachpress') . '</label><br />';
			}
			echo '<input name="email_field" id="email_field" type="checkbox" /> <label for="email_field_field">' . __('E-Mail','teachpress') . '</label><br />';
			?>
        </td>
    </tr>
    </thead>
</table>
<p><input name="create" type="submit" class="teachpress_button" value="<?php _e('Create','teachpress'); ?>"/></p>
</div>
<?php
}
if ( $create == __('Create','teachpress') ) {
	$row = "SELECT * FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$weiter'";
	$row = tp_results($row);
	foreach($row as $row) {
		// define course name
	   	if ($row->parent != 0) {
			$sql = "SELECT name FROM " . $teachpress_ver . " WHERE veranstaltungs_id = '$row->parent'";
			$parent_name = tp_var($sql);
			// if parent_name == child name
			if ($parent_name == $row->name) {
				$parent_name = "";
			}
		}
		else {
			$parent_name = "";
		}
	   ?>
        <h2><?php echo $parent_name; ?> <?php echo $row->name; ?> <?php echo $row->semester; ?></h2>
        <div id="einschreibungen" style="padding:5px;">
        <div style="width:700px; padding-bottom:10px;">
      		<table border="1" cellspacing="0" cellpadding="0" class="tp_print">
                  <tr>
                    <th><?php _e('Lecturer','teachpress'); ?></th>
                    <td><?php echo $row->dozent; ?></td>
                    <th><?php _e('Date','teachpress'); ?></th>
                    <td><?php echo $row->termin; ?></td>
                    <th><?php _e('Room','teachpress'); ?></th>
                    <td><?php echo $row->raum; ?></td>
                  </tr>
            </table>
        </div>
        <table border="1" cellpadding="0" cellspacing="0" class="tp_print" width="100%">
          <tr style="border-collapse: collapse; border: 1px solid black;">
          	<th width="20" height="100">&nbsp;</th>
            <th width="250"><?php _e('Name','teachpress'); ?></th>
            <?php
			if ($matrikel_field == '1') {
				echo '<th>' . __('Registr.-Number','teachpress') . '</th>';
			}
			if ($nutzerkuerzel_field == '1') {
				echo '<th width="81">' . __('User account','teachpress') . '</th>';
			}
			if ($studiengang_field == '1') {
				echo '<th>' . __('Course of studies','teachpress') . '</th>';
			}
			if ($fachsemester_field == '1') {
				echo '<th>' . __('Number of terms','teachpress') . '</th>';
			}
			if ($gebdat_field == '1') {
				echo '<th>' . __('Date of birth','teachpress') . '</th>';
			}
			if ($email_field == '1') {
				echo '<th>' . __('E-Mail','teachpress') . '</th>';
			}
			for ($i=1; $i<=$anzahl; $i++ ) {
				echo '<th>&nbsp;</th>';
			}
			?>
          </tr>
         <tbody> 
    <?php         
	   }
	$nummer = 1;
	// Ausgabe der Tabelle zu den in die LVS eingeschriebenen Studenten
	$select = "SELECT s.vorname, s.nachname";
	if ($matrikel_field == '1') {
		$select = $select . ", s.matrikel";
	}
	if ($nutzerkuerzel_field == '1') {
		$select = $select . ", s.urzkurz";
	}
	if ($studiengang_field == '1') {
		$select = $select . ", s.studiengang";
	}
	if ($fachsemester_field == '1') {
		$select = $select . ", s.fachsemester";
	}
	if ($gebdat_field == '1') {
		$select = $select . ", s.gebdat";
	}
	if ($email_field == '1') {
		$select = $select . ", s.email";
	}
	if ($sort == '2') {
		$order_by = "s.matrikel";
	}
	else {
		$order_by = "s.nachname";
	}
	$row = "" . $select . "
			FROM " . $teachpress_kursbelegung . " k
			INNER JOIN " . $teachpress_ver . " v ON v.veranstaltungs_id=k.veranstaltungs_id
			INNER JOIN " . $teachpress_stud . " s ON s.wp_id=k.wp_id
			WHERE v.veranstaltungs_id = '$weiter'
			ORDER BY " . $order_by . "";
	$row = tp_results($row);
	foreach($row as $row3)
 	  {
	  ?>
  	  <tr>
      	<td><?php echo $nummer; ?></td>
        <td><?php echo $row3->nachname; ?>, <?php echo $row3->vorname; ?></td>
        <?php
        if ($matrikel_field == '1') {
            echo '<td>' . $row3->matrikel . '</td>';
        }
        if ($nutzerkuerzel_field == '1') {
            echo '<td>' . $row3->urzkurz . '</td>';
        }
        if ($studiengang_field == '1') {
            echo '<td>' . $row3->studiengang . '</td>';
        }
        if ($fachsemester_field == '1') {
            echo '<td>' . $row3->fachsemester . '</td>';
        }
        if ($gebdat_field == '1') {
            echo '<td>' . $row3->gebdat . '</td>';
        }
        if ($email_field == '1') {
            echo '<td>' . $row3->email . '</td>';
        }
		for ($i=1; $i<=$anzahl; $i++ ) {
			echo '<td>&nbsp;</td>';
		}
		?>
  	  </tr>
      <?php
	  $nummer++;
   }
?>
</tbody>
</table>
<?php } ?>
</form>
</div>
<?php } ?>