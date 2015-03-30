# [hypothes.is](http://hypothes.is/) for WordPress

Stable versions are available on the
[Hypothesis plugin page on WordPress.org](https://wordpress.org/plugins/hypothesis/).

## Install Directions

1. Upload `hypothesis.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You're done! The frontend of your site should now be enabled to use Hypothesis.

## Publishing

We use [wp-release.sh](https://github.com/sun/wordpress-git-svn-release) to
handle the somewhat insane world of git/svn repo juggling.

To cut a release:

0. install `wp-release.sh`
1. customize `.wp-release.conf` (if needed)
2. run `wp-release` from within the root of this repo's local working copy
3. follow the instructions
4. check the WordPress.org page to be sure it worked

## License

[BSD](http://opensource.org/licenses/BSD-2-Clause)
