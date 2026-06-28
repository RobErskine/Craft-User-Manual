# Local development & testing

This plugin has no app of its own — to test it you install it into a real Craft
install. The fastest way is [DDEV](https://ddev.com) (Docker-based, Craft's
official local-dev tooling). The test Craft project lives **outside** this repo;
the plugin is symlinked in via a Composer `path` repository so your edits here are
live in the running site.

## Prerequisites

- Docker (Docker Desktop or OrbStack)
- [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)
  (`brew install ddev/ddev/ddev`)

## One-time setup

Run from the directory that contains this repo (so the test project sits next to
`Craft-User-Manual/`):

```bash
# 1. Create a sibling test project
mkdir craft-user-manual-test && cd craft-user-manual-test

# 2. Configure DDEV for Craft
ddev config --project-type=craftcms --docroot=web --php-version=8.3
ddev start

# 3. Scaffold a fresh Craft 5 project
ddev composer create -y craftcms/craft

# 4. Point Composer at the local plugin checkout, then require it
ddev composer config repositories.usermanual path ../Craft-User-Manual
ddev composer require hillholliday/craft-user-manual:@dev

# 5. Install the plugin into Craft
ddev craft plugin/install usermanual

# 6. Open the control panel
ddev launch /admin
```

DDEV will print the admin credentials during `ddev craft install` (run that if the
`composer create` step didn't prompt for site setup).

## Day-to-day

- Editing files in `Craft-User-Manual/src/` updates the running site immediately
  (the `path` repo is symlinked).
- After changing `composer.json` requirements: `ddev composer update`.
- Clear caches if templates/settings act stale: `ddev craft clear-caches/all`.
- To test against **Craft 4**: in the test project run
  `ddev composer require craftcms/cms:^4.0 -W`.

## Smoke test checklist

1. "User Manual" appears in the CP nav.
2. Create a Structure section with a few entries, select it in the plugin settings.
3. Visit the configured URL segment (default `/admin/usermanual`) — entries render.
4. Custom URL segment and plugin-name overrides take effect.

## Tear down

```bash
ddev delete -O   # removes the project + its database
```
