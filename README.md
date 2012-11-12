craigslist-feed-merge
=====================

Allows someone to take a Craigslist RSS search feed and perform that search in multiple cities, returning a single feed containing all of the cities' searches merged into one.

Some Background
---------------

I wrote this thing because I'm looking for a pinball machine.  I love pinball, and live 15 minutes from Harrisburg, PA.  Harrisburg's Craigslist isn't exactly exploding with pinball machines for sale, so I wanted to cast a wider net. 

Philly's just a couple hours away, and Baltimore's a short 90 minutes to the south.  I *could* just run separate searches in their respective Craigslist sites, but I'd then have to manage those feeds separately in Google Reader, which is a pain considering how spammy some of those listings can be.

Getting Started
---------------
You need a web server your RSS reader service can see running PHP.  

Download the files, make a few changes to the settings in the index.php file to specify the terms you want to search for along with the cities you want to search in, and you should be on your way.  