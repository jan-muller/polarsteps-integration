=== Plugin Name ===
Contributors: janmuller
Donate link: https://paypal.me/janmuller?country.x=NL&locale.x=en_US
Tags: polarsteps, importer, janmuller, travel, travelmap
Requires at least: 6.0
Tested up to: 6.2.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import steps from Polarsteps.com and convert them into a blogpost.

== Description ==

The website [Polarsteps.com](http://polarsteps.com/ "Polarsteps.com") provides an excellent platform for documenting travel experiences. 
It features a mobile app that records GPS locations, referred to as "Steps," allowing users to add images and text to accompany their journeys. 
With this plugin, you can effortlessly import your Polarsteps "Steps" and automatically generate blog posts based on your trip.

See the Github-Repo here: [https://github.com/jan-muller/polarsteps-integration](https://github.com/jan-muller/polarsteps-integration "https://github.com/jan-muller/polarsteps-integration")

== Key features ==
* Import Polarsteps Steps: Connect your Polarsteps account to WordPress and easily import your Steps directly into your website.
* Automatic Blogpost Generation: The plugin automatically generates a blog posts containing one or more step(s). It combines images and text from your Polarsteps account to create a blog post.

== How to get started ==
* Go to polarsteps.com and select the trip you want to import.
* The trip visiblilty must be set to Public.
* Get the trip id from the URL. Only the numbers are needed.
* Eg. https://www.polarsteps.com/username/1234567-trip-name
* Every hour all new posts will be imported.
* Once a week a blogpost will be generated including the new steps.
* Both actions can be triggerd manually.

== Disclaimer ==

As I'm not associated with the company behind Polarsteps and just developed the plugin for personal purposes. The APIs on their side could change from one day to another resulting in breaking this plugin.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `polarsteps-integration.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register an account for Polarsteps (if not already done)
1. Create Trip (with at least one Step) on Polarsteps.com and make sure it is public
1. Obtain trip id from Polarsteps.com and add it to the plugin's settings


== Frequently Asked Questions ==

= How to get a Trip Id from Polarsteps? =

Go to polarsteps.com and select the trip you want to use. Make sure it is public. Get the trip id from the URL. Only the numbers are needed. In the following URL "1234567" is the trip id: https://www.polarsteps.com/username/1234567-trip-name

= How to exclude or skip a step? =

On the "Polarsteps steps" page you will find an overview of all the steps and have the option to skip one or more steps.

= What image will be used as featured image for the post? =

Once a week (or manually) a blogpost will be generated, a blogpost can contain multiple steps. The first image of the last step is used as featured image. If a blogpost only contains one step, the first image will be used as featured image.

= Can I change the post after it has been generated? =

Yes, after the post is generated, it will not be updated or anything by the plugin. You can make as many changes as you like.

= I've changed the step on Polarsteps, does this update the content on my website? =

If a step is not yet in a blogpost, the content will be updated. After a blogpost is generated, the content of the blogpost does not update.


== Screenshots ==

1. Settings screen
2. Overview of all imported steps
3. Blogpost containing a step

== Changelog ==

= 1.0.0 =
Initial commit.