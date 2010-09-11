<?php
/********************/
/* BibTeX functions */
/********************/ 

/* teachPress bibtex import
 * @param $input (String) - BibTeX format
 * Return $return (String)
*/ 
function tp_bibtex_import($bibtex_data) {
	global $PARSEENTRIES;
	if ($bibtex_data == "") {
	}
	else {
		// Parse a bibtex PHP string
		$parse = NEW PARSEENTRIES();
		$parse->expandMacro = TRUE;
		$array = array("RMP" =>"Rev., Mod. Phys.");
		$parse->loadStringMacro($array);
		$parse->loadBibtexString($bibtex_data);
		$parse->extractEntries();
		list($preamble, $strings, $entries, $undefinedStrings) = $parse->returnArrays();
		echo '<p><strong>' . __('Imported Publications:','teachpress') . '</strong></p>';
		for ($i = 0; $i < count($entries); $i++) {
			$number = $i + 1;
			// for the date of publishing
			if ($entries[$i]['month'] != '') {
				$entries[$i]['date'] = $entries[$i]['year'] . '-' . $entries[$i]['month'] . '-01';
			}
			elseif ($entries[$i]['month'] != '' && $entries[$i]['day'] != '') {
				$entries[$i]['date'] = $entries[$i]['year'] . '-' . $entries[$i]['month'] . '-' . $entries[$i]['day'];
			}
			else {
				$entries[$i]['date'] = $entries[$i]['year'] . '-01-01';
			}
			// for tags
			if ($entries[$i]['keywords'] != '') {
				$tags = str_replace(" ",",",$entries[$i]['keywords']);
			}
			elseif ($entries[$i]['tags'] != '') {
				$tags = str_replace(" ",",",$entries[$i]['tags']);
			}
			else {
				$tags = '';
			}
			// add in database
			$entries[$i]['name'] = $entries[$i]['title'];
			$entries[$i]['type'] = $entries[$i]['bibtexEntryType'];
			$entries[$i]['bibtex'] = $entries[$i]['bibtexCitation'];
			$new_entry = tp_add_publication($entries[$i], $tags, '');
			// return for user
			echo '<p>(' . $number . ') <a href="admin.php?page=teachpress/addpublications.php&amp;pub_ID=' . $new_entry . '" target="_blank">' . $entries[$i]['bibtexEntryType'] . ': ' . $entries[$i]['author'] . ' (' . $entries[$i]['year'] . '): ' . $entries[$i]['title'] . '</a></p>';
		}
	}
}

/* Gives a bibtex code of a publication
 * @param $row (object)
 * return $string
*/ 
function tp_get_bibtex($row) {
	$string = "";
	// replace html special chars
	$array_1 = array('&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
	$array_2 = array('Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
	$row->author = str_replace($array_1, $array_2, $row->author);
	$row->name = str_replace($array_1, $array_2, $row->name); 
	$item_link = str_replace($array_1, $array_2, $item_link);
	$array_1 = array('&');
	$array_2 = array('und');
	$row->verlag = str_replace($array_1, $array_2, $row->verlag); 
	$row->name = str_replace($array_1, $array_2, $row->name);
	
	// Change publication type to bibtex type
	if ($row->typ == 'presentation') {$row->typ = 'misc';}
	
	/* 
	 * compound the entry
	*/ 
	$string = '@' . $row->type . '{' . $row->bibtex . ',' . chr(13) . chr(10);
	// Author
	if ($row->author != "") {
		$string = $string . 'author  = {' . $row->author . '},' . chr(13) . chr(10);
	}
	// Editor
	if ($row->editor != "") {
		$string = $string . 'editor  = {' . $row->editor . '},' . chr(13) . chr(10);
	}
	// Title
		$string = $string . 'title   = {' . $row->name . '},' . chr(13) . chr(10);
	// Note
	if ($row->note != "") {
		$string = $string . 'note  = {' . $row->note . '},' . chr(13) . chr(10);
	}
	// URL
	if ($row->url != '') {
		$string = $string . 'url = {' . $row->url . '},' . chr(13) . chr(10);
	}
	// ISBN
	if ($row->isbn != '') {
		$string = $string . 'isbn = {' . $row->isbn . '},' . chr(13) . chr(10);
	}
	// Year
	$preg = '/[\d]{2,4}/'; 
    $time = array(); 
    preg_match_all($preg, $row->date, $time);
	$string = $string . 'year  = {' . $time[0][0] . '},' . chr(13) . chr(10);
	
	// booktitle
	if ($row->booktitle != '') {
		$string = $string . 'booktitle = {' . $row->booktitle . '},' . chr(13) . chr(10);
	}
	// journal
	if ($row->journal != '') {
		$string = $string . 'journal = {' . $row->journal . '},' . chr(13) . chr(10);
	}
	// volume
	if ($row->volume != '') {
		$string = $string . 'volume = {' . $row->volume . '},' . chr(13) . chr(10);
	}
	// number
	if ($row->number != '') {
		$string = $string . 'number = {' . $row->number . '},' . chr(13) . chr(10);
	}
	// pages
	if ($row->pages != '') {
		$string = $string . 'pages = {' . $row->pages . '},' . chr(13) . chr(10);
	}
	// publisher
	if ($row->publisher != '') {
		$string = $string . 'publisher = {' . $row->publisher . '},' . chr(13) . chr(10);
	}
	// address
	if ($row->address != '') {
		$string = $string . 'address = {' . $row->address . '},' . chr(13) . chr(10);
	}
	// edition
	if ($row->edition != '') {
		$string = $string . 'edition = {' . $row->edition . '},' . chr(13) . chr(10);
	}
	// chapter
	if ($row->chapter != '') {
		$string = $string . 'chapter = {' . $row->chapter . '},' . chr(13) . chr(10);
	}
	// institution
	if ($row->institution != '') {
		$string = $string . 'institution = {' . $row->institution . '},' . chr(13) . chr(10);
	}
	// organization
	if ($row->organization != '') {
		$string = $string . 'organization = {' . $row->organization . '},' . chr(13) . chr(10);
	}
	// school
	if ($row->school != '') {
		$string = $string . 'school = {' . $row->school . '},' . chr(13) . chr(10);
	}
	// series
	if ($row->series != '') {
		$string = $string . 'series = {' . $row->series . '},' . chr(13) . chr(10);
	}
	// crossref
	if ($row->crossref != '') {
		$string = $string . 'crossref = {' . $row->crossref . '},' . chr(13) . chr(10);
	}
	// abstract
	if ($row->abstract != '') {
		$string = $string . 'abstract = {' . $row->abstract . '},' . chr(13) . chr(10);
	}
	// howpublished
	if ($row->howpublished != '') {
		$string = $string . 'howpublished = {' . $row->howpublished . '},' . chr(13) . chr(10);
	}
	// key
	if ($row->key != '') {
		$string = $string . 'key = {' . $row->key . '},' . chr(13) . chr(10);
	}
	// techtype
	if ($row->techtype != '') {
		$string = $string . 'type = {' . $row->techtype . '},' . chr(13) . chr(10);
	}
	// note
	if ($row->note != '') {
		$string = $string . 'note = {' . $row->note . '},' . chr(13) . chr(10);
	}
	$string = $string . '}' . chr(13) . chr(10);
	return $string;
}

/* Handles all advanced information like address, howpublished, publisher, ...
 * @param $row (object)
 * Return $end (string)
*/ 
function tp_publication_advanced_information($row) {
	// Falls isbn eingegeben wurde, wird formatiert
	if ($row->isbn != '') {
		// Test ob ISBN oder ISSN
		if ($row->is_isbn == '0') { 
			$isbn = ', ISSN: ' . $row->isbn . '';
		}
		else {
			$isbn = ', ISBN: ' . $row->isbn . '';
		}
	}
	// Year
	if ($row->jahr != '') {
		$year = '' . $row->jahr . '';
	}
	// Editor
	if ($row->editor != '') {
		// sort editor names
		$editor = "";
		$array = explode(" and ",$row->editor);
		$lenth = count ($array);
		for ($i=0; $i < $lenth; $i++) {
			$array[$i] = trim($array[$i]);
			$names = explode(" ",$array[$i]);
			$lenth2 = count ($names);
			for ($j=0; $j < $lenth2-1; $j++) {
				$one_editor = $one_editor . ' ' . trim( $names[$j] );
			}
			$one_editor = trim( $names[$lenth2 - 1] ). ', ' . $one_editor;
			$editor = $editor . $one_editor;
			if ($i < $lenth - 1) {
				$editor = $editor . '; ';
			}
			$one_editor = "";
		}
		$editor = '' . $editor . ' (' . __('Publ.','teachpress') . ') ';
	}
	// booktitle
	if ($row->booktitle != '') {
		$booktitle = '' . $row->booktitle . ', ';
	}
	// journal
	if ($row->journal != '') {
		$journal = '' . $row->journal . ' ';
	}
	// volume
	if ($row->volume != '') {
		$volume = '' . $row->volume . ' ';
	}
	// number
	if ($row->number != '') {
		$number = '' . $row->number . ' ';
	}
	// pages
	if ($row->pages != '') {
		$pages = '' . __('Page(s)','teachpress') . ': ' . $row->pages . ' ';
	}
	// publisher
	if ($row->publisher != '') {
		$publisher = ' ' . $row->publisher . ', ';
	}
	// address
	if ($row->address != '') {
		$address = '' . $row->address . ' ';
	}
	// edition
	if ($row->edition != '') {
		$edition = '' . $row->edition . ', ';
	}
	// chapter
	if ($row->chapter != '') {
		$chapter = '' . $row->chapter . ' ';
	}
	// institution
	if ($row->institution != '') {
		$institution = '' . $row->institution . ' ';
	}
	// organization
	if ($row->organization != '') {
		$organization = '' . $row->organization . ' ';
	}
	// school
	if ($row->school != '') {
		$school = '' . $row->school . ' ';
	}
	// series
	if ($row->series != '') {
		$series = '' . $row->series . ' ';
	}
	// howpublished
	if ($row->howpublished != '') {
		$howpublished = '' . $row->howpublished . ' ';
	}
	// techtype
	if ($row->techtype != '') {
		$techtype = '' . $row->techtype . ' ';
	}
	// end format after type
	if ($row->type == 'article') {
		$end = $journal . $volume . $number . $pages . $isbn . '.';
	}
	elseif ($row->type == 'book') {
		$end = $edition . $publisher . $address . $year . $isbn . '.';
	}
	elseif ($row->type == 'booklet') {
		$end = $howpublished . $address . $edition . $isbn . '.';
	}
	elseif ($row->type == 'conference') {
		$end = $booktitle . $year . $volume . $number . $series . $publisher . $address . $isbn . '.';
	}
	elseif ($row->type == 'inbook') {
		$end = $editor . $booktitle . $volume . $pages . $publisher . $address . $edition . $year . $isbn . '.';
	}
	elseif ($row->type == 'incollection') {
		$end = $editor . $booktitle . $publisher . $isbn . '.';
	}
	elseif ($row->type == 'inproceedings') {
		$end = $booktitle . $pages . $address . $publisher . $year . $isbn . '.';
	}
	elseif ($row->type == 'manual') {
		$end = $editor . $address. $edition . $year . $isbn . '.';
	}
	elseif ($row->type == 'mastersthesis') {
		$end = $school . $year . $isbn . '.';
	}
	elseif ($row->type == 'misc') {
		$end = $howpublished . $year . $isbn . '.';
	}
	elseif ($row->type == 'phdthesis') {
		$end = $school . $year . $isbn . '.';
	}
	elseif ($row->type == 'presentation') {
		$end = $howpublished . $address . $year . $isbn . '.';
	}
	elseif ($row->type == 'proceedings') {
		$end = $howpublished . $address . $edition . $year . $isbn . '.';
	}
	elseif ($row->type == 'techreport') {
		$end = $institution . $address . $year . $isbn . '.';
	}
	elseif ($row->type == 'unpublished') {
		$end = $year . $isbn . '.';
	}
	else {
		$end = $row->jahr . '.';
	}
	
	return $end;
}

/* Gives a single publication in html
 * @param $row (object)
 * @param $pad_size (int)
 * @param $image (string) - left, right or bottom
 * @param $all_tags (array) - array with tags
 * @param $all_tags (int) - number of lines
 * @param $with_tags (int) - for a publication with tags 1, else 0
 * Return $string (string)
*/ 
function tp_get_publication_html($row, $pad_size, $image, $all_tags, $atag, $with_tags = 1, $html_anchor = '#tppubs') {
	$tag_string = '';
	$str = "'";
	// Tags suchen
	if ($with_tags == '1') {
		for ($i = 0; $i < $atag; $i++) {
			if ($all_tags[$i][2] == $row->pub_id) {
				$tag_string = $tag_string . '<a href="' . $link . '?tgid=' . $all_tags[$i][1] . '&amp;yr=' . $yr . '&amp;type=' . $type . '&amp;autor=' . $autor . $html_anchor . '" title="' . __('Show all publications which have a relationship to this tag','teachpress') . '">' . $all_tags[$i][0] . '</a>, ';
			}
		}
		$tag_string = substr($tag_string, 0, -2);
	}	
	// handle images
	$image_marginally = '';
	$image_bottom = '';
	if ($image == 'left' || $image == 'right') {
		if ($row->image_url != '') {
			$image_marginally = '<img name="' . $row->name . '" src="' . $row->image_url . '" width="' . $image_size .'" alt="' . $row->name . '" />';
		}
	}
	if ($image == 'left') {
		$td_left = '<td width="' . $pad_size . '">' . $image_marginally . '</td>';
	}
	if ($image == 'right') {
		$td_right = '<td width="' . $pad_size . '">' . $image_marginally . '</td>';
	}
	if ($image == 'bottom') {
		if ($row->image_url != '') {
			$image_bottom = '<div class="tp_pub_image_bottom"><img name="' . $row->name . '" src="' . $row->image_url . '" style="max-width:' . $image_size .'px;" alt="' . $row->name . '" /></div>';
		}
	}
	// Falls url eingegeben wurde, wird ein Link draus
	if ($row->url !='') {
		$name = '<a href="' . $row->url . '">' . $row->name . '</a>';
	}
	else {
		$name = $row->name;
	}
	
	// sort author names
	$all_authors = "";
	$array = explode(" and ",$row->author);
	$lenth = count ($array);
	for ($i=0; $i < $lenth; $i++) {
		$array[$i] = trim($array[$i]);
		$names = explode(" ",$array[$i]);
		$lenth2 = count ($names);
		for ($j=0; $j < $lenth2-1; $j++) {
			$one_author = $one_author . ' ' . trim( $names[$j] );
		}
		$one_author = trim( $names[$lenth2 - 1] ). ', ' . $one_author;
		$all_authors = $all_authors . $one_author;
		if ($i < $lenth - 1) {
			$all_authors = $all_authors . '; ';
		}
		$one_author = "";
	}
	
	// language sensitive publication type
	$type = __('' . $row->type . '','teachpress');
	
	$a1 = '<tr class="tp_publication">
				' . $td_left . '
				<td class ="tp_pub_info">
				<p class="tp_pub_autor">' . $all_authors . '</p>
				<p class="tp_pub_titel">' . $name . ' <span class="tp_pub_typ">(' . $type . ')</span></p>
				<p class="tp_pub_zusatz">' . tp_publication_advanced_information($row) . '</p>';
	if ($with_tags == '1') {			
		$a2 = '<p class="tp_pub_tags">(<a onclick="teachpress_showhide(' . $str . 'tp_bibtex_' . $row->pub_id . $str . ')" style="cursor:pointer;" title="' . __('Show BibTeX entry','teachpress') . '">' . __('BibTeX','teachpress') . '</a> | ' . __('Tags','teachpress') . ': ' . $tag_string . ')</p>';
	}
	$a3 = '<div class="tp_bibtex" id="tp_bibtex_' . $row->pub_id . '" style="display:none;">
			<textarea name="tp_bibtex_area" rows="8" style="width:90%; margin:10px;">' . tp_get_bibtex($row) . '</textarea>
			<p class="tp_bibtex_menu"><a class="tp_bibtex_close" onclick="teachpress_showhide(' . $str . 'tp_bibtex_' . $row->pub_id . $str . ')">' . __('close','teachpress') . '</a></p>
		   </div>';
	$a4 = '
				' . $image_bottom . '
				</td>
				' . $td_right . '
				</tr>';
	$a = $a1 . $a2 . $a3 . $a4;			
	return $a;
}
?>