# Releasing Craft User Manual

Releases are published to Packagist as
[`hillholliday/craft-user-manual`](https://packagist.org/packages/hillholliday/craft-user-manual).
Packagist is **auto-updated from GitHub via webhook**, so cutting a release is just
tag + push — there is no manual Packagist step.

> The `version` field has been removed from `composer.json` on purpose. Composer
> derives the package version from the **git tag**, so the tag is the single source
> of truth. Do not add `"version"` back — it only creates drift.

## Steps to cut a release

1. Make sure `main` is green in CI and has everything you want to ship.
2. Update `CHANGELOG.md` — add a new section at the top following the existing
   format:

   ```markdown
   ## X.Y.Z - YYYY-MM-DD
   ### Added / Changed / Fixed
   - Short description of the change (#PR)
   ```

3. Commit the changelog:

   ```bash
   git add CHANGELOG.md
   git commit -m "Release X.Y.Z"
   git push origin main
   ```

4. Tag and push the tag (the tag **is** the version — no leading `v`, matching the
   existing tags like `5.0.4`):

   ```bash
   git tag X.Y.Z
   git push origin X.Y.Z
   ```

5. Within a minute or two Packagist syncs the new version. Confirm at
   <https://packagist.org/packages/hillholliday/craft-user-manual>.
6. (Optional) Create a GitHub Release for the tag and paste the changelog entry so
   users browsing GitHub see release notes.

## Versioning (semver)

- **Patch** (`5.0.x`) — bug fixes, no behavior change for existing setups.
- **Minor** (`5.x.0`) — new backwards-compatible features (e.g. a new setting).
- **Major** (`x.0.0`) — breaking changes or a new minimum Craft/PHP requirement.

Bumping the minimum Craft major (adding `^6.0` and dropping `^4.0`, etc.) is a
**major** release.

## Pre-releases

To test a release end-to-end without publishing a stable version, tag a
pre-release, e.g. `git tag 5.1.0-beta.1 && git push origin 5.1.0-beta.1`. Consumers
only receive it if their `minimum-stability` allows it.
