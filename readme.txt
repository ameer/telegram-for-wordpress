=== Telegram for WP ===
Contributors: amir-mousavi
Tags: telegram,notifications,email,admin,plugin,channel
Requires at least: 3.5
Tested up to: 4.5.2
Stable tag: 1.6.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Allows WordPress site to send notifications to Telegram. You can also send files/posts/products to Telegram Channel.

== Description ==
Hi,
Whether you want to receive notifications about your website (e.g. comment submit, user register, new core update and etc.) or you just want to share your posts/products in Telegram Channel; you are welcome to use this plugin. This will help you to integrate Telegram with WordPress.

This plugin based on <a href="http://notifcaster.com" target="_blank">Notifcaster</a> service by Ameer Mousavi and allows WordPress site to send notifications to telegram.

Note: You have to turn on WordPress email notifications in order to receive Telegram notifications. Because in this version you will receive the body of any email that WordPrss is sending.

Please feedback and rate to make this plugin better.

Here is a  <a href="http://ameer.ir/telegram-for-wp" target="_blank">Persian Tutorial</a>

== Installation ==
1. Download Telegram for WordPress plugin from Wordpress Plugin directory. 

2. Upload zip file to your WordPress Plugins directory. /wp-content/plugins

3. Activate the plugin through the \'Plugins\' menu in WordPress

4. Go to TWP Settings Page in Wordpress dashboard.

5. Follow the guides in the TWP Settings page.

== Screenshots ==

1. Notification Settings.
2. Telegram Channel Settings.
3. Metabox in post editor allows you to override global settings.

== Changelog ==

= 1.6 =
* Fixed request timed-out when publishing a new post.
* Added support for pages/products and any other post type.
* Added feature for sending photo and text; all in one message.
* Added feature for sending almost any file (limited to 50MB).
* Fix the Internal Server Error in WooCommerce and Gravity Forms.
* Optimized codes for faster and safer operation.
* Better support for scheduled posts/products.
* Added custom field support. Use following format for any custom field: %custom field name% or %#custom field name% for converting them to hashtags.
* Added feature for controlling excerpt length.
* Support supergroups in notifications.

= 1.5 =
* Using patterns for sending messages.
* Using markdown and HTML format in messages.
* Added support for sending long messages (Messages will be splitted).
* Added support for emojis.
* Added support for scheduled posts.
*‌ Allow sending custom image.
* Initial Support for WooCommerce.
* Better report for sent messages.
* Allow disabling link previews for links in the sent message.
* Improved UI and UX.
* Optimized codes for faster and safer operation.

= 1.4 =
* Sending posts to Telegram channel.
* Sending notifications to groups.
* Adding basic hashtag option.
* Adding signature at the end of the channel messages.
* Add backward compatibility for php < 5.5 .

= 1.3 =
* Introducing Notifcaster, a new service for sending notifications to Telegram using bot api.

= 1.2 =
* Fixing the conflict with Contact Form 7
* Change "Notifygram" class name to "Notifygram_Class"

= 1.1 =
* Add Persian Tutorial Link
* Add test button

= 1.0 =
* Initial Release