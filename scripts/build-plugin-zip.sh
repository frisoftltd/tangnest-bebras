#!/usr/bin/env bash
# Produces dist/tangnest-bebras-{VERSION}.zip
# The zip top-level folder is tangnest-bebras/ (required by WordPress for clean installs).
# vendor/plugin-update-checker/ is included; dev files are excluded.
#
# Usage:
#   bash scripts/build-plugin-zip.sh [VERSION]
#   (VERSION defaults to the Version header in tangnest-bebras.php)
set -euo pipefail

PLUGIN_SLUG="tangnest-bebras"
PLUGIN_FILE="tangnest-bebras.php"

VERSION="${1:-$(grep -m1 'Version:' "$PLUGIN_FILE" | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')}"

if [ -z "$VERSION" ]; then
  echo "ERROR: Could not determine plugin version." >&2
  exit 1
fi

ZIP_NAME="${PLUGIN_SLUG}-${VERSION}.zip"
DIST_DIR="dist"
BUILD_ROOT="/tmp/${PLUGIN_SLUG}-build-$$"
BUILD_DIR="${BUILD_ROOT}/${PLUGIN_SLUG}"

echo "Building ${ZIP_NAME}..."

rm -rf "$BUILD_ROOT"
mkdir -p "$BUILD_DIR"

rsync -a \
  --exclude='.git/' \
  --exclude='.github/' \
  --exclude='.gitignore' \
  --exclude='scripts/' \
  --exclude='dist/' \
  --exclude='node_modules/' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='README.md' \
  --exclude='*.save' \
  --exclude='*.zip' \
  . "$BUILD_DIR/"

mkdir -p "$DIST_DIR"

(
  cd "$BUILD_ROOT"
  zip -r "${OLDPWD}/${DIST_DIR}/${ZIP_NAME}" "${PLUGIN_SLUG}/"
)

rm -rf "$BUILD_ROOT"

echo "Built: ${DIST_DIR}/${ZIP_NAME}"
ls -lh "${DIST_DIR}/${ZIP_NAME}"
