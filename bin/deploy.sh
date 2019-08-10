#!/usr/bin/env bash

if [[ -z "$TRAVIS" ]]; then
    echo "Script is only to be run by Travis CI" 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_USERNAME" ]]; then
    echo "WordPress.org username not set" 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_PASSWORD" ]]; then
    echo "WordPress.org password not set" 1>&2
    exit 1
fi

if [[ -z "$TRAVIS_BRANCH" || "$TRAVIS_BRANCH" != "master" ]]; then
    echo "Build branch is required and must be 'master'" 1>&2
    exit 0
fi

PLUGIN="hypothesis"
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
PLUGIN_BUILDS_PATH="$PROJECT_ROOT/builds"
VERSION=$(php -f "$PROJECT_ROOT/bin/get_plugin_version.php" "$PROJECT_ROOT" "$PLUGIN")
BUILD_DIRECTORY="$PLUGIN_BUILDS_PATH/$PLUGIN-$VERSION"

# Ensure the current version has been built
if [ ! -d "$BUILD_DIRECTORY" ]; then
    echo "Built plugin directory $BUILD_DIRECTORY does not exist" 1>&2
    exit 1
fi

# Check if the tag exists for the version we are building
TAG=$(svn ls "https://plugins.svn.wordpress.org/$PLUGIN/tags/$VERSION")
error=$?
if [ $error == 0 ]; then
    # Tag exists, don't deploy
    echo "Tag already exists for version $VERSION, aborting deployment"
    exit 1
fi
    
cd "$PLUGIN_BUILDS_PATH"

# Clean up any previous svn dir
rm -fR svn

# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$PLUGIN" svn

# Move out the trunk directory to a temp location
mv svn/trunk ./svn-trunk
# Create trunk directory
mkdir svn/trunk
# Copy our new version of the plugin into trunk
rsync -r -p $BUILD_DIRECTORY/* svn/trunk

# Copy all the .svn folders from the checked out copy of trunk to the new trunk.
# This is necessary as the Travis container runs Subversion 1.6 which has .svn dirs in every sub dir
cd svn/trunk/
TARGET=$(pwd)
cd ../../svn-trunk/

# Find all .svn dirs in sub dirs
SVN_DIRS=`find . -type d -iname .svn`

for SVN_DIR in $SVN_DIRS; do
    SOURCE_DIR=${SVN_DIR/.}
    TARGET_DIR=$TARGET${SOURCE_DIR/.svn}
    TARGET_SVN_DIR=$TARGET${SVN_DIR/.}
    if [ -d "$TARGET_DIR" ]; then
        # Copy the .svn directory to trunk dir
        cp -r $SVN_DIR $TARGET_SVN_DIR
    fi
done

# Back to builds dir
cd ../

# Remove checked out dir
rm -fR svn-trunk

# Add new version tag
mkdir svn/tags/$VERSION
rsync -r -p $BUILD_DIRECTORY/* svn/tags/$VERSION

# Add new files to SVN
svn stat svn | grep '^?' | awk '{print $2}' | xargs -I x svn add x@
# Remove deleted files from SVN
svn stat svn | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@
svn stat svn

# Commit to SVN
svn commit --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD svn -m "Deploy version $VERSION"

# Remove SVN temp dir
rm -fR svn
