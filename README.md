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

New releases are automatically published to WordPress.org via Travis CI (using [this script](bin/deploy.sh)) whenever the version in [hypothesis.php](hypothesis.php) is incremented and a new version is tagged. Translation files will be updated as well. This method is largely based on the process described by [Iain Poulson](https://github.com/polevaultweb) in [this blog post](https://deliciousbrains.com/deploying-wordpress-plugins-travis/), and also makes use of the [wp-cli](https://wp-cli.org) `i18n` command.

## License

[BSD](http://opensource.org/licenses/BSD-2-Clause)
