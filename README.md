# [hypothes.is](http://hypothes.is/) for WordPress

Stable versions are available on the
[Hypothesis plugin page on WordPress.org](https://wordpress.org/plugins/hypothesis/).

## Install Directions

1. Visit your WordPress plugins page (/wp-admin/plugins.php)
2. Click the Add New button
3. Search the WordPress plugins directory for Hypothes.is
4. Click Install Now.
5. Click Activate
6. Visit your WordPress Settings > Hypothesis page to configure how it works on your site

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
