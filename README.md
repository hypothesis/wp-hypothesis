# [hypothes.is](http://hypothes.is/) for WordPress

Stable versions are available on the [Hypothesis plugin page on WordPress.org](https://wordpress.org/plugins/hypothesis/).

## Install this plugin

### Via composer

This plugin can be installed with composer, from the standard package registry (packagist.org)

    composer require hypothesis/wp-hypothesis

### Via WordPress plugins directory

1. Visit your WordPress plugins page (/wp-admin/plugins.php)
2. Click the Add New button
3. Search the WordPress plugins directory for Hypothes.is
4. Click Install Now.
5. Click Activate
6. Visit your WordPress Settings > Hypothesis page to configure how it works on your site

## Development

1. Install `php` and the `dom` and `mbstring` extensions.
2. [Download Composer](https://getcomposer.org/download/), the PHP package manager.
3. Run `make dev`. This will start a local WordPress instance with this plugin mounted on it.
4. Access http://localhost:8080 (the first time you'll have to finish setting up WordPress by following presented instructions)

## Publishing

Follow these steps to publish a new plugin version.

1. **Update the package version** in `hypothesis.php`, `readme.txt` and `package.json`
2. **Update readme.txt**, adding the new version with its list of changes, under the `Changelog` section.
3. **Merge** the changes into the `main` branch[^1]. We use [Semantic Versioning](https://semver.org/#semantic-versioning-200).
4. **Create a tag** pointing at the version-change commit and generate a **new GitHub release** (details follow). Publishing a GitHub release will kick off a GitHub Action that will publish the plugin to wordpress.org

> [!NOTE]
> The package will be automatically published in packagist.org just by pushing the new git tag.

### Creating a GitHub release

Create a [new GitHub release](https://github.com/hypothesis/wp-hypothesis/releases/new/) with these values:

1.  _Tag_: Create a new tag for the release, targeting the `main` branch (your just-merged version bump should be at the tip)[^2]. The tag should match the version number, e.g. `v5.2.1`.
2.  _Title_: Use the tag name.
3.  Click the `Auto-generate release notes` button to generate release notes and edit as needed. We use [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) formatting.[^3]
4.  Leave other fields alone/as defaults.

[^1]: Unlike other PRs, a version-bump PR does not require review. But do wait for CI to complete first.
[^2]: You can create a tag manually as a separate step if you want to tag a non-tip commit.
[^3]: You can look at release notes for [other recent releases](https://github.com/hypothesis/wp-hypothesis/releases) as exemplars. You don't need to include every change (especially, e.g., dependency updates).

## License

[BSD-3-Clause](http://opensource.org/licenses/BSD-3-Clause)
