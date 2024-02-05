=== Hypothesis ===
Contributors: timmmmyboy, BigBlueHat, JakeHartnell, greatislander, acelaya
Tags: hypothesis, annotation, comments
Requires at least: 6.2
Tested up to: 6.4.2
Stable tag: 0.7.3
License: BSD-3-Clause
License URI: http://opensource.org/licenses/BSD-3-Clause

An open platform for the collaborative evaluation of knowledge.

== Description ==

Hypothesis is a web annotation tool that allows users to provide commentary, references, and insight on top of news, blogs, scientific articles, books, terms of service, ballot initiatives, legislation and regulations, software code and more. You can find out more at [http://hypothes.is/](http://hypothes.is/)

This plugin will allow you to automatically embed Hypothesis in your site.

Without this plugin, you would have to follow [these steps](https://web.hypothes.is/help/embedding-hypothesis-in-websites-and-platforms/), but with this plugin you only need to check some checkboxes, and you will be good to go.

* [Terms of Service](https://web.hypothes.is/terms-of-service/)
* [Privacy Policy](https://web.hypothes.is/privacy/)

== Installation ==

1. Upload `hypothesis.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You're done! The frontend of your site should now be enabled to use Hypothesis.

== Changelog ==

= 0.7.3 =
* Add new banner and logo

= 0.7.2 =
* Migrate to BSD-3-Clause license
* Document third party integration
* Use `Hypothesis` namespace in PHP code

= 0.7.1 =
* Do not include wp-cli.phar on releases

= 0.7.0 =
* Add support for WordPress v6
* Update to WordPress coding standard v3

= 0.6.0 =
* Fix PDF links in multisite and other customized installations (props @boonbgorges).
* Tested up to WordPress 5.2.2.
* Updated to current WordPress Coding Standards.
* Automated deploys to WordPress plugin directory.

= 0.5.0 =
- Fix an incompatibility with PHP < 5.4 introduced in the last release.
- Load plugin textdomain.

= 0.4.9 =
- Add localization support.
- Fix an error in options sanitization routine.

= 0.4.8 =
- Refactor per-post type settings for Hypothesis display.
- Add `hypothesis_supported_posttypes` to allow developers to support their custom post types.

= 0.4.0 =
 - Add customized embedding options
     + Show highlights by default
     + Sidebar open by default.
     + Disable click to close.

= 0.3.0 =
 - Add option to allow on select pages or posts
 - Remove category IDs override

= 0.2.0 =
Introduce settings panel and enabling/disabling functionality.
 - Adds Hypothesis settings panel
 - Admins can now configure which pages, posts, or categories
   to load Hypothes.is on.

= 0.1.2 =
Bumped version to work out release process. No code changes.

= 0.1.1 =
Relicensed under the BSD to match other Hypothesis projects.

= 0.1 =
First!
