# [hypothes.is](http://hypothes.is/) for WordPress

Stable versions are available on the [Hypothesis plugin page on WordPress.org](https://wordpress.org/plugins/hypothesis/).

## Install Directions

1. Visit your WordPress plugins page (/wp-admin/plugins.php)
2. Click the Add New button
3. Search the WordPress plugins directory for Hypothes.is
4. Click Install Now.
5. Click Activate
6. Visit your WordPress Settings > Hypothesis page to configure how it works on your site

## Publishing

Follow these steps to publish a new plugin version.

1. **Update the package version** in `hypothesis.php`, `readme.txt` and `package.json`, and merge that change into the `main` branch[^1]. We use [Semantic Versioning](https://semver.org/#semantic-versioning-200).
2. **Create a tag** pointing at the version-change commit and generate a **new GitHub release** (details follow). Publishing a GitHub release will kick off a GitHub Action that will publish the plugin to wordpress.org

### Creating a GitHub release

Create a [new GitHub release](https://github.com/hypothesis/wp-hypothesis/releases/new/) with these values:

1.  _Tag_: Create a new tag for the release, targeting the `main` branch (your just-merged version bump should be at the tip)[^2]. The tag should match the version number, e.g. `v5.2.1`.
2.  _Title_: Use the tag name.
3.  Click the `Auto-generate release notes` button to generate release notes and edit as needed. We use [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) formatting.[^3]
4.  Leave other fields alone/as defaults.

## License

[BSD](http://opensource.org/licenses/BSD-2-Clause)
