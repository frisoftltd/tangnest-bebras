# Tangnest Bebras Release Flow

This repository is set up so GitHub can build a WordPress-ready plugin zip and publish it as a GitHub Release asset.

## Safe Commit And Push Flow

1. Make your code changes locally.
2. If the release includes a new version, update both:
   - `tangnest-bebras.php` plugin header `Version`
   - `readme.txt` `Stable tag`
3. Run the local checks:

```bash
bash scripts/check-version-consistency.sh
bash scripts/build-plugin-zip.sh
```

4. Review the generated artifact in `dist/`.
5. Commit your changes to `main`.
6. Push `main` to GitHub.
7. When you are ready to publish a release, create and push a version tag that matches the plugin version:

```bash
git tag v0.1.0
git push origin main
git push origin v0.1.0
```

## What GitHub Actions Does

- On every push to `main`, GitHub Actions validates versions, lints PHP files, builds `dist/tangnest-bebras-<version>.zip`, and uploads it as a workflow artifact.
- On every pushed version tag such as `v0.1.0`, GitHub Actions validates the tag, builds the same zip, and creates or updates the matching GitHub Release.

## Release Asset Naming

The WordPress updater scaffold in this plugin looks for a release asset named either:

- `tangnest-bebras.zip`
- `tangnest-bebras-<version>.zip`

The release workflow publishes the versioned asset format so WordPress can detect and download it.
