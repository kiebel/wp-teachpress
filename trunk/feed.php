<?php
// build feeds for publications

// include wp-load.php
include_once('parameters.php');
global $root;
require( '' . $_SERVER['DOCUMENT_ROOT'] . '/' . $root . '/wp-load.php' );

// Define databases
global $wpdb;
$teachpress_pub = $wpdb->prefix . 'teachpress_pub';
$teachpress_user = $wpdb->prefix . 'teachpress_user';
$teachpress_beziehung = $wpdb->prefix . 'teachpress_beziehung';
$teachpress_tags = $wpdb->prefix . 'teachpress_tags';
$id = htmlentities(utf8_decode($_GET[id]));
$tag = htmlentities(utf8_decode($_GET[tag]));
settype ($id, 'integer');
settype ($tag, 'integer');

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
$select = "SELECT DISTINCT DATE_FORMAT(p.sort, '%a, %d %b %Y %H:%i:%s GMT') AS datum, p.name, p.verlag, p.autor, p.pub_id, p.url, p.rel_page";
if ( isset($_GET[id]) ) {
	if ( isset($_GET[tag]) ) {
		$row = "" . $select . "
			FROM " . $teachpress_beziehung ." b
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' AND t.tag_id = '$tag'
			ORDER BY p.sort DESC";	
	}
	else {
		$row = "" . $select . " 
			FROM " . $teachpress_pub . " p 
			INNER JOIN " . $teachpress_user . " u ON u.pub_id = p.pub_id
			WHERE u.user = '$id' 
			ORDER BY p.sort DESC";
	}	
}
else {
	if ( isset($_GET[tag]) ) {
		$row = "" . $select . "
			FROM " . $teachpress_beziehung ." b
			INNER JOIN " . $teachpress_tags . " t ON t.tag_id = b.tag_id
			INNER JOIN " . $teachpress_pub . " p ON p.pub_id = b.pub_id
			WHERE t.tag_id = '$tag'
			ORDER BY p.sort DESC";
	}
	else {
		$row = "" . $select . " 
			FROM " . $teachpress_pub . " p
			ORDER BY p.sort DESC";
	}		
}
$row = $wpdb->get_results($row);
foreach ($row as $row) {
	if ($row->url != '') {
		$item_link = $row->url;
	}
	elseif ($row->rel_page != '') {
		$item_link = get_bloginfo('url') . '/?page=' . $row->rel_page;
	}
	else {
		$item_link = get_bloginfo('url');
	}
	$array_1 = array('&Uuml;','&uuml;', '&Ouml;', '&ouml;', '&Auml;','&auml;', '&nbsp;', '&szlig;', '&sect;', '&ndash;', '&rdquo;', '&ldquo;', '&eacute;', '&egrave;', '&aacute;', '&agrave;', '&ograve;','&oacute;', '&copy;', '&reg;', '&micro;', '&pound;', '&raquo;', '&laquo;', '&yen;', '&Agrave;', '&Aacute;', '&Egrave;', '&Eacute;', '&Ograve;', '&Oacute;', '&shy;', '&amp;');
    $array_2 = array('Ü','ü', 'Ö', 'ö', 'Ä', 'ä', ' ', 'ß', '§', '-', '”', '“', 'é', 'è', 'á', 'à', 'ò', 'ó', '©', '®', 'µ', '£', '»', '«', '¥', 'À', 'Á', 'È', 'É', 'Ò', 'Ó', '­', '&');
  	$row->autor = str_replace($array_1, $array_2, $row->autor);
	$row->verlag = str_replace($array_1, $array_2, $row->verlag); 
	$row->name = str_replace($array_1, $array_2, $row->name); 
	$item_link = str_replace($array_1, $array_2, $item_link);
	$array_1 = array('&');
    $array_2 = array('und');
  	$row->autor = str_replace($array_1, $array_2, $row->autor);
	$row->verlag = str_replace($array_1, $array_2, $row->verlag); 
	$row->name = str_replace($array_1, $array_2, $row->name);
	echo '<item>
	 		<title>' . $row->name . '</title>
			<description>' . $row->verlag . '</description>
			<link><![CDATA[' . $item_link . ']]></link>
			<dc:creator>' . $row->autor . '</dc:creator>
			<guid isPermaLink="false">' . get_bloginfo('url') . '?publication=' . $row->pub_id . '</guid>
			<pubDate>' . $row->datum . '</pubDate>
			</item>';
}
echo '</channel>';	
echo '</rss>';
?>
