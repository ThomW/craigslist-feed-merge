<?php
class MergedRSS {
    /*
    Code from http://www.widgetsandburritos.com/merge-rss-feeds-php-cache/
    Posted on 9/8/10 by David Stinemetze
    */
    private $myFeeds = null;
    private $myTitle = null;
    private $myLink = null;
    private $myDescription = null;
    private $myPubDate = null;
    private $myCacheTime = null;

    // create our Merged RSS Feed
    public function __construct($feeds = null, $channel_title = null, $channel_link = null, $channel_description = null, $channel_pubdate = null, $cache_time_in_seconds = 3600) {
        // set variables
        $this->myTitle = $channel_title;
        $this->myLink = $channel_link;
        $this->myDescription = $channel_description;
        $this->myPubDate = $channel_pubdate;
        $this->myCacheTime = $cache_time_in_seconds;

        // initialize feed variable
        $this->myFeeds = array();

        if (isset($feeds)) {
            // check if it's an array.  if so, merge it into our existing array.  if it's a single feed, just push it into the array
            if (is_array($feeds)) {
                    $this->myFeeds = array_merge($feeds);
                } else {
                    $this->myFeeds[] = $feeds;
                }
        }
    }

    // exports the data as a returned value and/or outputted to the screen
    public function export($return_as_string = true, $output = false, $limit = null) {

        // initialize a combined item array for later
        $items = array();

        // loop through each feed
        foreach ($this->myFeeds as $feed_url) {

            // determine my cache file name.  for now i assume they're all kept in a file called "cache"
            $cache_file = "cache/" . $this->__create_feed_key($feed_url);

            // determine whether or not I should use the cached version of the xml
            $use_cache = false;
            if (file_exists($cache_file)) {
                if (time() - filemtime($cache_file) < $this->myCacheTime) {
                    $use_cache = true;
                }
            }

            if ($use_cache) {
                // retrieve cached version
                $sxe = $this->__fetch_rss_from_cache($cache_file);
                $results = $sxe->item;

            } else {
                // retrieve updated rss feed
                $sxe = $this->__fetch_rss_from_url($feed_url);
                $results = $sxe->item;

                if (!isset($results)) {
                    // couldn't fetch from the url. grab a cached version if we can
                    if (file_exists($cache_file)) {
                        $sxe = $this->__fetch_rss_from_cache($cache_file);
                        $results = $sxe->item;
                    }
                } else {
                    // we need to update the cache file
                    $sxe->asXML($cache_file);
                }
            }

            if (isset($results)) {
                // add each item to the master item list
                foreach ($results as $item) {
                    $items[] = $item;
                }
            }
        }

        // set all the initial, necessary xml data
        $xml =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

        $xml .= "<rdf:RDF\n";
        $xml .= " xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
        $xml .= " xmlns=\"http://purl.org/rss/1.0/\"\n";
        $xml .= " xmlns:ev=\"http://purl.org/rss/1.0/modules/event/\"\n";
        $xml .= " xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\n";
        $xml .= " xmlns:taxo=\"http://purl.org/rss/1.0/modules/taxonomy/\"\n";
        $xml .= " xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
        $xml .= " xmlns:syn=\"http://purl.org/rss/1.0/modules/syndication/\"\n";
        $xml .= " xmlns:dcterms=\"http://purl.org/dc/terms/\"\n";
        $xml .= " xmlns:admin=\"http://webns.net/mvcb/\"\n";
        $xml .= ">\n";

        $xml .= "<channel>\n";
        if (isset($this->myTitle)) { $xml .= "\t<title>".$this->myTitle."</title>\n"; }
        $xml .= "\t<atom:link href=\"http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."\" rel=\"self\" type=\"application/rss+xml\" />\n";
        if (isset($this->myLink)) { $xml .= "\t<link>".$this->myLink."</link>\n"; }
        if (isset($this->myDescription)) { $xml .= "\t<description>".$this->myDescription."</description>\n"; }
        if (isset($this->myPubDate)) { $xml .= "\t<pubDate>".$this->myPubDate."</pubDate>\n"; }

        // if there are any items to add to the feed, let's do it
        if (sizeof($items) > 0) {

            // sort items
            usort($items, array($this, '__compare_items'));

            // if desired, splice items into an array of the specified size
            if (isset($limit)) { array_splice($items, intval($limit)); }

            // now let's convert all of our items to XML
            for ($i=0; $i<sizeof($items); $i++) {
                $xml .= $items[$i]->asXML() ."\n";
            }
        }
        $xml .= "</channel>\n</rss>";

        // if output is desired print to screen
        if ($output) { echo $xml; }

        // if user wants results returned as a string, do so
        if ($return_as_string) { return $xml; }
    }


    private function __get_date($item) {

        // Item uses pubDate
        if ($item->pubDate) {
            return strtotime($item->pubDate);
        }

        // Item uses dc:date
        $namespaces = $item->getNameSpaces(true);
        $dc = $item->children($namespaces['dc']);
        if (isset($dc->date)) {
            return strtotime($dc->date);
        }

        throw new Exception('Date not found');
    }

    // compares two items based on "dc:date"
    private function __compare_items($a,$b) {
        return $this->__get_date($b) - $this->__get_date($a);
    }

    // retrieves contents from a cache file ; returns null on error
    private function __fetch_rss_from_cache($cache_file) {
        if (file_exists($cache_file)) {
            return simplexml_load_file($cache_file, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        return null;
    }

    // retrieves contents of an external RSS feed ; implicitly returns null on error
    private function __fetch_rss_from_url($url) {
        // Create new SimpleXMLElement instance
        $sxe = new SimpleXMLElement($url, LIBXML_NOCDATA, true);
        return $sxe;
    }

    // creates a key for a specific feed url (used for creating friendly file names)
    private function __create_feed_key($url) {
        return preg_replace('/[^a-zA-Z0-9\.]/', '_', $url) . 'cache';
    }
}
