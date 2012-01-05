<?php
/************************************/ 
/* Build RSS-feeds for publications */
/************************************/

// include wp-load.php
require_once( '../../../wp-load.php' );

// Define databases
global $wpdb;
$teachpress_pub = $wpdb->prefix . 'teachpress_pub';
$teachpress_user = $wpdb->prefix . 'teachpress_user';
$teachpress_relation = $wpdb->prefix . 'teachpress_relation';
$teachpress_tags = $wpdb->prefix . 'teachpress_tags';
$id = htmlspecialchars($_GET[id]);
$tag = htmlspecialchars($_GET[tag]);
$feedtype = htmlspecialchars($_GET[feedtype]);
settype ($id, 'integer');
settype ($tag, 'integer');

/*
 * Bibtex 
 */  
if ($feedtype == 'bibtex') {
	header('Content-Type: text/plain; charset=utf-8;');
	if ($id != '') {
	}
	else {
		$select = "SELECT DISTINCT p.pub_id, p.name, p.type, p.author, p.editor, p.date, DATE_FORMAT(p.date, '%Y') AS jahr, p.isbn , p.url, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.note, p.is_isbn, p.image_url";
		if ( $id != '' ) {
			if ( $tag != '' ) {
				$row = "" . $select . "
					FROM " . $teachpress_relation ." b
					INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
					INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
					INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
					WHERE u.user = '$id' AND t.tag_id = '$tag'
					ORDER BY p.date DESC";	
			}
			else {
				$row = "" . $select . " 
					FROM " . $teachpress_pub . " p 
					INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
					WHERE u.user = '$id' 
					ORDER BY p.date DESC";
			}	
		}
		else {
			if ( $tag != '' ) {
				$row = "" . $select . "
					FROM " . $teachpress_relation ." b
					INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
					INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
					WHERE t.tag_id = '$tag'
					ORDER BY p.date DESC";
			}
			else {
				$row = "" . $select . " 
					FROM " . $teachpress_pub . " p
					ORDER BY p.date DESC";
			}		
		}
	}
	$row = $wpdb->get_results($row, ARRAY_A);
	foreach ($row as $row) {
		echo tp_bibtex::get_single_publication_bibtex($row);
	}
}
/*
 * RSS 2.0
*/ 
else {
	$url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];  
	
	echo '<?xml version="1.0" encoding="utf-8"?>';
	echo '<rss version="2.0"
		xmlns:content="http://purl.org/rss/1.0/modules/content/"
		xmlns:wfw="http://wellformedweb.org/CommentAPI/"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:atom="http://www.w3.org/2005/Atom"
		xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
		xmlns:slash="http://purl.org/rss/1.0/modules/slash/">';
	echo '<channel>
		  <title>' . get_bloginfo('name') . '</title>
		  <atom:link href="' . $url . '" rel="self" type="application/rss+xml" />
		  <link>' . get_bloginfo('url') . '</link>
		  <description>' . get_bloginfo('description') . '</description>
		  <language>' . get_bloginfo('language') . '</language>
		  <sy:updatePeriod>daily</sy:updatePeriod>
		  <sy:updateFrequency>1</sy:updateFrequency>
		  <copyright>' . get_bloginfo('name') . '</copyright>
		  <pubDate>' . date('r') . '</pubDate>
		  <dc:creator>' . get_bloginfo('name') . '</dc:creator>';
	$select = "SELECT DISTINCT DATE_FORMAT(p.date, '%a, %d %b %Y %H:%i:%s GMT') AS date, p.pub_id, p.name, p.type, p.author, p.editor, p.date, DATE_FORMAT(p.date, '%Y') AS jahr, p.isbn , p.url, p.booktitle, p.journal, p.volume, p.number, p.pages, p.publisher, p.address, p.edition, p.chapter, p.institution, p.organization, p.school, p.series, p.crossref, p.abstract, p.howpublished, p.key, p.techtype, p.note, p.is_isbn, p.image_url ";
	if ( isset($_GET[id]) ) {
		if ( isset($_GET[tag]) ) {
			$row = "" . $select . "
				FROM " . $teachpress_relation ." b
				INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
				INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
				WHERE u.user = '$id' AND t.tag_id = '$tag'
				ORDER BY p.date DESC";	
		}
		else {
			$row = "" . $select . " 
				FROM " . $teachpress_pub . " p 
				INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
				WHERE u.user = '$id' 
				ORDER BY p.date DESC";
		}	
	}
	else {
		if ( isset($_GET[tag]) ) {
			$row = "" . $select . "
				FROM " . $teachpress_relation ." b
				INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
				INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
				WHERE t.tag_id = '$tag'
				ORDER BY p.date DESC";
		}
		else {
			$row = "" . $select . " 
				FROM " . $teachpress_pub . " p
				ORDER BY p.date DESC";
		}		
	}
	$row = $wpdb->get_results($row, ARRAY_A);
	foreach ($row as $row) {
		if ($row['url'] != '') {
			$item_link = $row['url'];
		}
		elseif ($row['rel_page'] != '') {
			$item_link = get_bloginfo('url') . '/?page=' . $row['rel_page'];
		}
		else {
			$item_link = get_bloginfo('url');
		}
		$array_1 = array('&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
		$array_2 = array('Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
		$row['author'] = tp_bibtex::replace_html_chars($row['author']);
		$row['author'] = str_replace(' and ', ', ', $row['author']);
		$row['name'] = tp_bibtex::replace_html_chars($row['name']); 
		$item_link = tp_bibtex::replace_html_chars($item_link);
		$settings['editor_name'] = 'simple';
		echo '<item>
                           <title>' . stripslashes($row['name']) . '</title>
                           <description>' . tp_bibtex::single_publication_meta_row($row, $settings['editor_name']) . '</description>
                           <link><![CDATA[' . $item_link . ']]></link>
                           <dc:creator>' . stripslashes($row['author']) . '</dc:creator>
                           <guid isPermaLink="false">' . get_bloginfo('url') . '?publication=' . $row['pub_id'] . '</guid>
                           <pubDate>' . $row['date'] . '</pubDate>
                           </item>';
	}
	echo '</channel>';	
	echo '</rss>';
}
?>
