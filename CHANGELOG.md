# Release Notes for Craft User Manual

## 5.0.1 - 2024-05-15
 - Settings model updated with types and validation rules. (This fix is to help address possible issue in Craft 4 to Craft 5 migration.)
 - Added "enabledSideBar" config setting to enable/disable the sidebar on the manual page.
 - Error messages moved to translations file to allow for easier translation.

## 5.0.0 - 2024-04-04
- Craft 5 support.
- Adjusted version number to reflect Craft 5 compatibility.
- Styling updates for the side for User Manual content. Nested lists now have a different style to make them easier to read.
- Added conditional messaging to help provide instructions to the developer when the plugin is installed in a fresh installation of Craft CMS with no sections or entries yet created.
- Added craftMajorVersion() twig function to help style nested lists in Craft 4.

## 4.0.1 - 2022-06-20
- Updating name in Composer so it will update on Packagist

## 4.0.0 - 2022-06-20
- Craft 4 support. Thanks to Chris DuCharme for migrating. [PR #29](https://github.com/roberskine/Craft-User-Manual/pull/29)

## 2.1.2 - 2022-01-15
- Forcing an update for the plugin store

## 2.1.1 - 2021-11-24
- Removing fixed positioning from #content within the user manual [PR #24](https://github.com/roberskine/Craft-User-Manual/pull/24)

## 2.1.0 - 2021-11-10
- Merging PR from JorgeAnzalo for an optional sidebar [PR #23](https://github.com/roberskine/Craft-User-Manual/pull/23)
- Merging PR from for a hashed based navigation for long navbars [PR #19](https://github.com/roberskine/Craft-User-Manual/pull/19)

## v2.0.2 - 2019-02-27
### Updated
- Forcing new version for deprecation issues

## v2.0.1 - 2018-08-24
### Fixed
- Deprecation Issues

## v2.0.0 - 2018-08-24
### Added
- Initial Craft 3 release
