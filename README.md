=== Plugin Name ===
Contributors: k0nsl, edwardw
Donate link: http://k0nsl.org/blog
Tags: geolocation, anti-spam, ipgeo, question, captcha, anti-bot question, anti-spam question, bots, cloudflare, comments, anti-bot, no-spam
Requires at least: 3.1
Tested up to: 3.4
Stable tag: trunk
License: GPLv3

Uses CloudFlare IP Geolocation and poses the question; what is your country code?

== Description ==

I wanted my own twist and a somewhat more 'unique' way to deal with spam so I crafted this simple plugin which is originally based on WP No-Bot Question by edwardw (http://wordpress.org/extend/plugins/wp-no-bot-question/) - with the exception that my plugin uses the IP Geolocation provided by CloudFlare. So my plugin merely asks "What is your country code" and you'll have to reply with the correct one. The correct country code is printed on the same page, but this may be removed in future versions - ir displayed in some other fashion.

Do keep in mind that you need CloudFlare and IP Geolocation for this plugin to work.

Plugin homepage: http://k0nsl.org/blog/plugins/no-spam-ip-geolocation/

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `k0nsl_nospam_ipgeo` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enable under "Settings -> No-Spam IP Geolocation"

== Frequently Asked Questions ==

= How do I change or disable this plugin's settings? =
Disable and edit the question and answer under "Settings -> No-Spam IP Geolocation"
== Screenshots ==

See it in action on the commentary form below.

== Changelog ==

= 0.1 =
* Initial version
