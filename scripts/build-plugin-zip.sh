#!/usr/bin/env bash

set -eu

ROOT_DIR="$(CDPATH='' cd -- "$(dirname "$0")/.." && pwd)"
PLUGIN_SLUG="tangnest-bebras"
DIST_DIR="$ROOT_DIR/dist"
STAGE_DIR="$DIST_DIR/.stage"
PLUGIN_FILE="$ROOT_DIR/tangnest-bebras.php"

cleanup() {
	rm -rf "$STAGE_DIR"
}

trap cleanup EXIT

extract_version() {
	sed -n "s/^[[:space:]]*\\*[[:space:]]*Version:[[:space:]]*//p" "$PLUGIN_FILE" | head -n 1 | tr -d '\r'
}

VERSION="${1:-$(extract_version)}"

if [ -z "$VERSION" ]; then
	echo "Unable to determine plugin version from $PLUGIN_FILE" >&2
	exit 1
fi

mkdir -p "$STAGE_DIR/$PLUGIN_SLUG" "$DIST_DIR"

for entry in assets includes templates vendor tangnest-bebras.php readme.txt uninstall.php; do
	cp -R "$ROOT_DIR/$entry" "$STAGE_DIR/$PLUGIN_SLUG/"
done

(
	cd "$STAGE_DIR"
	zip -qr "$DIST_DIR/$PLUGIN_SLUG-$VERSION.zip" "$PLUGIN_SLUG"
)

echo "$DIST_DIR/$PLUGIN_SLUG-$VERSION.zip"
