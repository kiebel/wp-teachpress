<?php
/*
 * Aktuelle Kursuebersicht fuer Frontend
*/
?>
<div id="anzeigelvs">
    <form name="lvs" method="get">
		<?php
        global $teachpress_ver; 
        global $teachpress_einstellungen; 
        $sem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE variable = 'sem'";
        $sem = tp_var($sem);
		// Falls Semester vom User gewaehlt
		$semester = htmlspecialchars($_GET[semester]);	
		if ($semester != "") {
			$sem = $semester;
		}
        ?>
        <div class="tp_auswahl">
            <?php _e('Select the term','teachpress'); ?>
            <select name="semester">
                <option value="<?php echo $sem; ?>"><?php echo $sem;?></option>
                <option>-----</option>
                <?php
                $rowsem = "SELECT wert FROM " . $teachpress_einstellungen . " WHERE category = 'semester' ORDER BY einstellungs_id";
                $rowsem = tp_results($rowsem);
                foreach($rowsem as $rowsem) { ?>
                    <option value="<?php echo $rowsem->wert; ?>"><?php echo $rowsem->wert; ?></option>
                <?php } ?>
            </select>  
            <input type="submit" name="start" value="<?php _e('show','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/>
        </div>   
        <h3><?php _e('Courses for the','teachpress'); ?> <?php echo"$sem" ;?></h3>
        <?php
            $row = "Select name, bemerkungen, url FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' ORDER BY name";
			$test = tp_query($row);
			if ($test != 0){
				$row = tp_results($row);
				foreach($row as $row) { ?>
				   <div class="tp_lvs_container">
				   <div class="tp_lvs_name"><strong><?php echo "$row->name"; ?></strong></div>
				   <table border="0" cellspacing="0" cellpadding="0" width="100%" style="padding-top:5px;">
					  <tr>
						<td style="font-size:12px;"><?php echo "$row->bemerkungen"; ?></td>
						<td style="text-align:right; font-size:14px;"><a href="<?php echo "$row->url"; ?>"><?php _e('Details','teachpress'); ?></a></td>
					  </tr>
				   </table>
				   </div>
			   <?php } 
		   }
		   else {
		   		echo '<p class="teachpress_message"><strong>' . __('Sorry, no entries matched your criteria.','teachpress') . '</p>';
		   }?>
    </form>
</div>