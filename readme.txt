=== Crumbly ===
Contributors: pb_86
Tags: tag1, tag2
Requires at least: 6.0
Tested up to: 6.8.2
Stable tag: 2.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple and lightweight plugin for theme developers that provide easy to use function for displaying breadcrumbs.

== Changelog ==
= 2.1 =
* Refactor: renamed functions, css classes and added backward-compatible rddgbc() wrapper.

= 2.0.1 =
* Refactor: reorganize code structure for better readability
* Refactor: changed flag to be self-explanatory in function rddgbc_print()
* Refactor: changed incrementation method to comply with phpcs
* Refactor: reorganize code structure in function rddgbc_the_singular() for better readability
* Docs: added readme.txt file with changelog
* Docs: corrected English in header, comments and PHPDoc
* Fixed: Translation loading and refreshed translation files
* Removed: code for displaying breadcrumbs on the home page