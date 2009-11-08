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
        ?>
        <div class="tp_auswahl">
            <?php _e('Bitte w&auml;hlen Sie das Semester','teachpress'); ?>
            <select name="semester">
                <option value="<?php echo $sem; ?>"><?php echo $sem;?></option>
                <option>-----</option>
                <?php
                $rowsem = "SELECT DISTINCT semester FROM " . $teachpress_ver . "";
                $rowsem = tp_results($rowsem);
                foreach($rowsem as $rowsem) { ?>
                    <option value="<?php echo $rowsem->semester; ?>"><?php echo $rowsem->semester; ?></option>
                <?php } 
                $semester = htmlspecialchars($_GET[semester]);	
                if ($semester != "") {
                    $sem = $semester;
                }
                ?>
            </select>  
            <input type="submit" name="start" value="<?php _e('anzeigen','teachpress'); ?>" id="teachpress_submit" class="teachpress_button"/>
        </div>   
        <h3 style="color:#005A46;"><?php _e('Lehrveranstaltungen für das','teachpress'); ?> <?php echo"$sem" ;?></h3>
        <?php
            $row = "Select name, bemerkungen, url FROM " . $teachpress_ver . " WHERE semester = '$sem' AND parent = '0' ORDER BY name";
            $row = tp_results($row);
            foreach($row as $row) { ?>
               <div style="margin:10px; border:1px solid silver; padding:5px;">
               <div style="background-image:none; padding-left:5px; font-size:14px; color:#005A46;"><strong><?php echo "$row->name"; ?></strong></div>
               <div style="border-bottom:1px dotted silver; font-size:2px; margin-left:10px;">&nbsp;</div>
               <table border="0" cellspacing="0" cellpadding="0" width="100%" style="padding-top:5px;">
                  <tr>
                    <td style="font-size:12px; padding-left:15px;"><?php echo "$row->bemerkungen"; ?></td>
                    <td style="text-align:right; font-size:14px;"><a href="<?php echo "$row->url"; ?>"><?php _e('Details','teachpress'); ?></a></td>
                  </tr>
               </table>
               </div>
           <?php } ?>
    </form>
</div>