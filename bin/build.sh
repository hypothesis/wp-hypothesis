#!/usr/bin/env bash

PLUGIN="hypothesis"
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
PLUGIN_BUILDS_PATH="$PROJECT_ROOT/builds"
VERSION=$(php -f "$PROJECT_ROOT/bin/get_plugin_version.php" "$PROJECT_ROOT" "$PLUGIN")

cd "$PROJECT_ROOT"
[ -d "$PLUGIN_BUILDS_PATH" ] || mkdir "$PLUGIN_BUILDS_PATH"

wp i18n make-pot . "languages/hypothesis.pot"

mkdir "$PLUGIN_BUILDS_PATH/$PLUGIN-$VERSION"

rsync -r --exclude-from .distignore $PROJECT_ROOT/ "$PLUGIN_BUILDS_PATH/$PLUGIN-$VERSION"
