# Release Notes for Craft User Manual

## 5.1.0 - 2026-06-28
- Added one-way markdown → CP sync: a `usermanual/sync` console command (with `--dry-run`) that imports a `managedFolder` of version-controlled `.md` files into the manual section, plus optional read-only enforcement of synced pages (`readOnlyManaged`) and a configurable `bodyField`. Thanks to [Dan Gaidula](https://github.com/dgaidula). ([PR #55](https://github.com/RobErskine/Craft-User-Manual/pull/55))
- Fixed the default User Manual entry for Structure sections: the base URL now shows the first entry in structure order, rather than the oldest by creation date. Thanks to [John Morton](https://github.com/johnfmorton). ([PR #52](https://github.com/RobErskine/Craft-User-Manual/pull/52))
- Fixed the control panel nav link on sites with a custom `cpTrigger` (it no longer hardcodes `/admin/`). Based on [John Morton](https://github.com/johnfmorton)'s report in [PR #52](https://github.com/RobErskine/Craft-User-Manual/pull/52).
- Fixed the missing-`body`-field help message so it renders correctly under Twig `strict_variables` (dev mode) instead of throwing. Based on [John Morton](https://github.com/johnfmorton)'s change in [PR #52](https://github.com/RobErskine/Craft-User-Manual/pull/52).

## 5.0.4 - 2025-02-05
- Adding in ability to add a custom URL segment to the user manual documentation section.

## 5.0.3 - 2025-02-05
- Removing requirement for documentation to have URLs. [PR #48](https://github.com/RobErskine/Craft-User-Manual/pull/48)

## 5.0.2 - 2024-08-06
- Replacing `addExtension` with `Craft::$app->view->registerTwigExtension(new UserManualTwigExtension);`

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
