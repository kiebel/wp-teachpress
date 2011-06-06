<?php 
/*
 * Import BibTeX
*/ 
function teachpress_import_page() {
$bibtex = $_POST[bibtex_area];
// replace bibtex special chars
$bibtex = str_replace(chr(92),'',$bibtex);
$bibtex = str_replace(array ('{"A}','{"a}','{"O}','{"o}','{ss}','{"U}','{"u}'), array('Ä','ä','Ö','ö','ß','Ü','ü'), $bibtex);
// END
$bibtex = tp_sec_var( $bibtex );
echo $bibtex;
if (isset($_POST[tp_submit])) {
	tp_bibtex_import ($bibtex);
	echo '<p><a href="admin.php?page=teachpress/import.php" class="teachpress_back">&larr; ' . __('back','teachpress') . '</a></p>';
}
else {
	?>
	<div class="wrap">
	<h2><?php _e('BibTeX - Import','teachpress'); ?></h2>
    <p><?php _e("Copy your BibTeX entries in the textarea. Restrictions: teachPress can't converting LaTeX special chars as well as not numeric month and day attributes.","teachpress"); ?></p>
	<form id="tp_import" name="tp_import" action="<?php echo $PHP_SELF ?>" method="post">
	<input type="hidden" name="page" value="teachpress/import.php"/>
	<textarea name="bibtex_area" rows="20" style="width:90%;"></textarea>
	<p><input name="tp_submit" type="submit" class="button-primary" value="<?php _e('Import','teachpress'); ?>"/></p>
	</form>
	</div>
	<?php
	}
} ?>