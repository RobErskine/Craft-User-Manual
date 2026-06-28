# Craft User Manual — project notes for Claude

Open-source Craft CMS plugin. Lets editors build CP documentation ("User Manual" /
"Help") from Craft's own sections (singles, channels, structures).

- **Packagist:** `hillholliday/craft-user-manual` (keep this vendor name — do not rename).
- **Repo:** `RobErskine/Craft-User-Manual`, default branch `main`.
- **PHP namespace:** `roberskine\usermanual\` → `src/`.
- **Supports:** Craft `^4.0 | ^5.0`, PHP `^8.0.2`.
- **No database layer** — no records/migrations. Settings model + Twig extension + variable.

## Craft version branching

`src/UserManual.php` branches Craft 4 vs 5 via `getMajorVersion()`:
Craft 4 uses `Craft::$app->sections`, Craft 5+ uses `Craft::$app->entries`.
Craft 6 (Laravel rewrite, GA expected Q4 2026) will likely change these services —
track readiness before widening the constraint to `^6.0`.

## Releasing

Tag-driven; Packagist auto-syncs. See `docs/RELEASING.md`. **Never re-add a
`version` field to `composer.json`** — the git tag is the source of truth.

## Local testing

DDEV + a sibling Craft project, plugin symlinked via a Composer `path` repo.
See `docs/LOCAL-DEV.md`.

## craft-skill (agent-craft) CLI

`happycog/craft-skill` is a PHAR CLI agent (not a markdown skill) that bootstraps
against a live Craft install and returns JSON. Once installed globally, point it at
the local test install:

```bash
agent-craft --path=../craft-user-manual-test sections/list
```

Install: download `agent-craft.phar` from
<https://github.com/happycog/craft-skill/releases/latest> → `chmod +x` →
`mv` to `/usr/local/bin/agent-craft`.

## Quality tooling

- `composer phpstan` — static analysis (`phpstan.neon`, craftcms/phpstan).
- `composer rector` — Craft upgrade-rule preview (`rector.php`, dry-run).
- CI runs both plus `composer validate --strict` and `composer audit` across a
  PHP × Craft matrix (`.github/workflows/ci.yml`).
