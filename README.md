candycal
===========


=== SquareCandy Google Calendar ===
Contributors: squarecandy
Donate link: http://squarecandy.net/
Tags: calendar, gcal, google calendar
Requires at least: 2.9.1
Tested up to: 4.0.0
Stable tag: trunk

Display a google calendar feed on your site in a variety of ways.  Easily theme the output. 


== Description ==

Display a public google calendar feed on your site using a variety of display methods:
* Upcoming Events
* Past Events (w/ headers by year)
* "Mini" - shows a limited amount of upcoming events - good for homepage, sidebar, etc.

Great for theme developers - easily theme the simple HTML5 output. Override the plugin stylesheet by copying it to your theme folder.


== Installation ==


1. Create a Google Calendar and make sure your calendar is set to public.
1. place the **candycal_v3** folder in your /wp-content/plugins directory.
1. enable the plugin
1. IMPORTANT: make sure you have specified a timezone on the wordpress Settings > General page. Using a city that is in your timezone will keep you from having to adjust for Daylight Savings Time...
1. Go to Settings > Candy-Cal and enter your Google Calendar ID.  You will also need a google API key for your domain name or server IP.  
1. Make any other setting adjustment you would like here as well.
1. Use shortcode to place the calendars on your site: 
    * [candycal type=upcoming]
    * [candycal type=past]
    * [candycal type=mini]

Theme developers may also hard code these calendars:

`if (function_exists('candycal_display')) { print candycal_display('upcoming'); }`

`if (function_exists('candycal_display')) { print candycal_display('past'); }`

`if (function_exists('candycal_display')) { print candycal_display('mini'); }`



== Changelog ==


= 1.0.2 =
* Updated for the Google Calendar API version 3.
* updated options page handling to use built in wordpress functions

= 1.0.1 =
* This is the first version of the plugin


