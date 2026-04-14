#!/usr/bin/env bash
# Usage:
#   bash scripts/check-version-consistency.sh                   # verify only (build.yml)
#   bash scripts/check-version-consistency.sh "$GITHUB_REF"     # verify + output version (release.yml)
set -euo pipefail

PLUGIN_FILE="tangnest-bebras.php"

if [ ! -f "$PLUGIN_FILE" ]; then
  echo "ERROR: $PLUGIN_FILE not found" >&2
  exit 1
fi

PLUGIN_VERSION=$(grep -m1 'Version:' "$PLUGIN_FILE" | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')

if [ -z "$PLUGIN_VERSION" ]; then
  echo "ERROR: Could not read Version from $PLUGIN_FILE" >&2
  exit 1
fi

GITHUB_REF="${1:-}"

if [ -n "$GITHUB_REF" ] && [[ "$GITHUB_REF" == refs/tags/* ]]; then
  TAG="${GITHUB_REF#refs/tags/}"
  TAG_VERSION="${TAG#v}"   # strip optional leading 'v'

  if [ "$TAG_VERSION" != "$PLUGIN_VERSION" ]; then
    echo "ERROR: Tag version ($TAG_VERSION) does not match plugin version ($PLUGIN_VERSION)" >&2
    exit 1
  fi

  echo "Version verified: $PLUGIN_VERSION matches tag $TAG" >&2
else
  echo "Plugin version: $PLUGIN_VERSION" >&2
fi

# Output ONLY the bare version to stdout — captured as $VERSION in release.yml.
echo "$PLUGIN_VERSION"
