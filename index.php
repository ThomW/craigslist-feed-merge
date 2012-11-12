<?php

// SETTINGS =============================

$feed_name = 'Craigslist Local Pinball Search';
$feed_link = 'http://lmnopc.com/';

// You can set $feed_description manually, but if it's not provided, one will be built using the cities and terms specified
// $feed_description = 'This space for rent';

// This is an array containing the list of cities to combine
// This doesn't have to be an associative array. If you're lazy and just want to do
// $cities = array('philadelphia','harrisburg','pittsburgh'...) that will work as well.
$cities = array('philadelphia'=>'Philadelphia, PA','harrisburg'=>'Harrisburg, PA','pittsburgh'=>'Pittsburgh, PA','baltimore'=>'Baltimore, MD','annapolis'=>'Annapolis, MD','lancaster'=>'Lancaster, PA','york'=>'York, PA','frederick'=>'Frederick, MD','newyork'=>'New York City');

// This is an array containing the list of search terms.
// Items prefixed with a minus sign from them are excluded from the search results.
$terms = array('pinball','arcade','-multicade','-psp','-ps2','-ps3','-xbox','-gamecube','-wii','-casino');

// Number of items to publish in the feed
$feed_num_items = 100;

// END SETTINGS =========================

if (!isset($feed_description)) {
	$feed_description = 'Craigslist Search for the term' . (sizeof($terms) != 1 ? 's: ' : ': ');
	for ($i = 0; $i < sizeof($terms); $i++) {
		if ($i > 0) { $feed_description .= ', '; }
		$feed_description .= '\'' . $terms[$i] . '\'';
	}
	
	// If I really wanted to make this sexy, I'd store $cities in an associative array
	// and make the keys the subdomains, and the values the proper names of the cities,
	// but I'm going to KISS and just show an ugly list of city names.  
	$feed_description .= ' for the following cit' . (sizeof($terms) !=1 ? 'ies: ' : 'y: ');
	$idx = 0;
	foreach ($cities as $key => $city) {
		if ($idx++ > 0) { $feed_description .= ', '; }
		$feed_description .= $city;
	}
}

// Build the query string using $terms above
$query = '';
for ($i = 0; $i < sizeof($terms); $i++) {
	$query .= strlen($query) ? '+' : '';
	$query .= urlencode($terms[$i]);
}

// Build array of cities to query
$feeds = array();
foreach ($cities as $key=>$value) {
	$city = !is_numeric($key) ? $key : $value;
	$feeds[] = 'http://' . $city . '.craigslist.org/search/sss?hasPic=1&query=' . $query . '&srchType=A&format=rss';	
}

// Set the content-type to text/plain to make it easier to debug, but
// Google Reader and (unfortunately) Chrome don't care
header("Content-type: text/plain");

// Create new MergedRSS object with desired parameters
include_once("class.mergedrss.php");
$MergedRSS = new MergedRSS($feeds, $feed_name, $feed_link, $feed_description, date('r'));

$MergedRSS->export(false, true, $feed_num_items);

?>