#!/usr/bin/env bash

set -eu

ROOT_DIR="$(CDPATH='' cd -- "$(dirname "$0")/.." && pwd)"
PLUGIN_FILE="$ROOT_DIR/tangnest-bebras.php"
README_FILE="$ROOT_DIR/readme.txt"

plugin_version="$(sed -n "s/^[[:space:]]*\\*[[:space:]]*Version:[[:space:]]*//p" "$PLUGIN_FILE" | head -n 1 | tr -d '\r')"
stable_tag="$(sed -n "s/^Stable tag:[[:space:]]*//p" "$README_FILE" | head -n 1 | tr -d '\r')"
tag_name="${1:-}"
normalized_tag="${tag_name#refs/tags/}"
normalized_tag="${normalized_tag#v}"

if [ -z "$plugin_version" ]; then
	echo "Plugin version could not be read from $PLUGIN_FILE" >&2
	exit 1
fi

if [ -z "$stable_tag" ]; then
	echo "Stable tag could not be read from $README_FILE" >&2
	exit 1
fi

if [ "$plugin_version" != "$stable_tag" ]; then
	echo "Version mismatch: plugin header is $plugin_version but readme stable tag is $stable_tag" >&2
	exit 1
fi

if [ -n "$normalized_tag" ] && [ "$normalized_tag" != "$plugin_version" ]; then
	echo "Tag mismatch: pushed tag is $normalized_tag but plugin version is $plugin_version" >&2
	exit 1
fi

echo "$plugin_version"
