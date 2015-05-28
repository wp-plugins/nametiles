=== Nametiles ===
Contributors: larz3
Tags: nametiles, passcard, openname, avatar, nametile, hovercard, tagging, bitcoin, blockchain
Requires at least: 4.1
Tested up to: 4.2.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Add beautiful Passcard profiles & tagging to your site with Nametiles.

== Description ==

==Nametiles==
Nametiles lets you tag people on your website by their Passcard much like you
would on Facebook or Twitter. Your visitors see beautiful hovercards when they mouseover
the tagged names. Learn more and get your free API key[here ](https://nametiles.co)].


==Passcard Avatars==
Your users can keep their Wordpress avatar in sync with their Passcard
avatar. Each user can specify their Passcard in the "Your Profile" section of their account.

Changes to your Passcard avatar will take about an hour and or two to be
reflected in your Wordpress installation. This is because changes to a Passcard
take about 20 minutes to propagate and we also cache Passcard
avatar URLs locally in Wordpress for an hour to improve performance of your site.

<h3>Adding Passcard Data to Themes</h3>
<p>You can add Passcard data to your theme so that it is always up to date.</p>
`<?php $person = Passcard("larry"); // load the Passcard
echo $person->name_formatted();  // User's name
echo $person->cover_url(); // User's cover picture URL
echo $person->bitcoin_address(); // User's bitcoin address ?>`

==Endpoints (advanced)==

You can run your own Passcard endpoint and set it below or use the recommended endpoint.


For more information visit:

[Nametiles](https://nametiles.co)

[Passcard](https://passcard.info)


== Installation ==

1. Upload `nametiles` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit your user profile page, add your Passcard and enable your Passcard avatar.
4. Write a blog post about Nametiles and tag me by typing +larry in your post.
