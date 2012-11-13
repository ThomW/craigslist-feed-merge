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

1. Throw the files in this project into the folder you want to use
2. Create a folder named 'cache' and chmod it to 777 so the script can write files to it
3. Update the settings at the top of index.php by following the examples laid out in that file

Good luck!

Credits
-------
I started with David Stinemetze's MergedRSS class he posted as part of the article posted at http://www.widgetsandburritos.com/merge-rss-feeds-php-cache/ and included in this project here with his permission.

