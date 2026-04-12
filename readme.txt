=== Tangnest Bebras ===
Contributors: tangnest
Tags: bebras, education, tutor lms, interactive tasks
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tangnest Bebras is a modular WordPress plugin foundation for future Bebras-style interactive task types with Tutor LMS compatibility and GitHub-based update support.

== Description ==

Version 0.1.0 focuses on the plugin foundation only.

Features included:

* Modular plugin bootstrap and service classes
* Admin settings page under "Tangnest Bebras"
* GitHub repository settings for release-based update checks
* Tutor LMS detection with admin notice when it is not active
* Placeholder task registry for future interactive task types
* Activation, deactivation, and uninstall routines

Future task types planned:

* Multiple choice interactive
* Drag and drop
* Sequence / order
* Grid / logic puzzle

== Installation ==

1. Zip the `tangnest-bebras` plugin folder.
2. In WordPress admin, go to Plugins > Add New > Upload Plugin.
3. Upload the zip file and activate the plugin.
4. Open Tangnest Bebras in the WordPress admin menu.
5. If you want GitHub-based updates, enter the repository URL, branch, and enable update checks.
6. Publish GitHub releases. A release asset zip named `tangnest-bebras.zip` or `tangnest-bebras-<version>.zip` is recommended for the most predictable update package.

== Frequently Asked Questions ==

= Does this plugin require Tutor LMS? =

No. The plugin can be activated without Tutor LMS, but it will show an admin notice until Tutor LMS is active.

= Are task types included yet? =

No. This release provides the foundation and task registry placeholders only.

= How do GitHub updates work? =

Enable update checks in the plugin settings and publish GitHub releases. The updater can read the latest GitHub release, and a dedicated plugin zip asset is recommended for the cleanest update flow.

== Changelog ==

= 0.1.0 =

* Initial production-ready plugin foundation
* Added modular bootstrap, settings page, Tutor LMS checks, task registry placeholders, and GitHub updater scaffold
