<?php

/**
 * usermanual plugin for Craft CMS 6.x
 *
 * @link      https://twitter.com/erskinerob
 * @copyright Copyright (c) 2018 Rob Erskine
 */

namespace roberskine\usermanual\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\StringHelper;
use roberskine\usermanual\UserManual;
use Symfony\Component\Yaml\Yaml;
use Throwable;

/**
 * Manual service — imports a folder of markdown files into the configured
 * "User Manual" section as a one-way (git → DB) sync.
 *
 * Each `.md` file may begin with a YAML frontmatter block:
 *
 *     ---
 *     title: Getting Started
 *     slug: getting-started      # optional; defaults to the filename (minus an "NN-" order prefix)
 *     parent: craft-cms          # optional; slug of the parent page (structure sections only)
 *     order: 10                  # optional; sort hint used only when creating a new page
 *     enabled: true              # optional; default true
 *     delete: false              # optional; when true, soft-deletes the page with this slug
 *     ---
 *     # Markdown body…
 *
 * The remainder of the file (after the frontmatter) is written verbatim into
 * the section entry's body field (Settings::$bodyField, default `body`).
 *
 * The sync is idempotent and change-detecting: a page is only saved when its
 * title, body, or enabled state actually differ from the file. Entries in the
 * section that have NO backing file are never touched (so a CP-maintained page
 * can coexist). Removals are declarative: a file with `delete: true`.
 *
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     5.1.0
 */
class Manual extends Component
{
    /**
     * Resolve the configured managed folder to an absolute path, or null when
     * unset / missing.
     */
    public function getManagedFolderPath(): ?string
    {
        $folder = UserManual::$plugin->getSettings()->managedFolder;
        if (!$folder) {
            return null;
        }
        $path = Craft::getAlias($folder);
        return is_string($path) && is_dir($path) ? rtrim($path, '/') : null;
    }

    /**
     * Return every parsed `.md` file in the managed folder, keyed by slug.
     *
     * @return array<string, array> slug => parsed file
     */
    public function getManagedFiles(): array
    {
        $path = $this->getManagedFolderPath();
        if ($path === null) {
            return [];
        }

        $files = [];
        foreach (glob($path . '/*.md') ?: [] as $file) {
            $parsed = $this->parseFile($file);
            if ($parsed !== null) {
                $files[$parsed['slug']] = $parsed;
            }
        }
        return $files;
    }

    /**
     * The set of slugs that are backed by a managed file (excludes `delete`
     * tombstones). Used by the read-only guard. Cached per request.
     *
     * @return array<string, true>
     */
    public function managedSlugs(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $cache = [];
        foreach ($this->getManagedFiles() as $slug => $file) {
            if (empty($file['delete'])) {
                $cache[$slug] = true;
            }
        }
        return $cache;
    }

    /**
     * Parse a single markdown file into its frontmatter + body. Returns null
     * if the file can't be read.
     *
     * @return array{slug:string,title:?string,parent:?string,order:int,enabled:bool,delete:bool,body:string}|null
     */
    public function parseFile(string $file): ?array
    {
        $raw = @file_get_contents($file);
        if ($raw === false) {
            return null;
        }
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // strip UTF-8 BOM

        $front = [];
        $body = $raw;
        // Frontmatter: a leading "---" line, YAML, then a closing "---" line.
        if (preg_match('/^---\s*\R(.*?)\R---\s*\R?(.*)$/s', $raw, $m)) {
            try {
                $front = Yaml::parse($m[1]) ?: [];
            } catch (Throwable) {
                $front = [];
            }
            $body = $m[2];
        }

        $base = preg_replace('/\.md$/i', '', basename($file));
        $slug = (string)($front['slug'] ?? preg_replace('/^\d+[-_]/', '', $base));
        $slug = StringHelper::slugify($slug);

        return [
            'slug' => $slug,
            'title' => isset($front['title']) ? (string)$front['title'] : null,
            'parent' => isset($front['parent']) ? StringHelper::slugify((string)$front['parent']) : null,
            'order' => (int)($front['order'] ?? 0),
            'enabled' => (bool)($front['enabled'] ?? true),
            'delete' => (bool)($front['delete'] ?? false),
            'body' => $this->normalizeBody($body),
        ];
    }

    /**
     * Normalize a markdown body so re-syncs are idempotent: LF line endings, no
     * leading blank lines (left by the frontmatter block), and no trailing
     * whitespace — matching how Craft stores a PlainText value.
     */
    private function normalizeBody(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        return rtrim(ltrim($body, "\n"));
    }

    /**
     * Run the sync. Returns a summary array of counts + any errors.
     *
     * @param bool $dryRun when true, report what would change without saving
     * @param callable|null $log optional line logger (string $msg) => void
     * @return array{created:int,updated:int,unchanged:int,deleted:int,skipped:int,errors:string[]}
     */
    public function sync(bool $dryRun = false, ?callable $log = null): array
    {
        $log ??= static fn(string $m) => null;
        $summary = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'deleted' => 0, 'skipped' => 0, 'errors' => []];

        $settings = UserManual::$plugin->getSettings();
        $sectionId = (int)$settings->section;
        $bodyField = $settings->bodyField ?: 'body';

        $path = $this->getManagedFolderPath();
        if ($path === null) {
            $summary['errors'][] = "managedFolder is not set or does not exist: {$settings->managedFolder}";
            return $summary;
        }
        if (!$sectionId) {
            $summary['errors'][] = 'No section configured for the User Manual plugin.';
            return $summary;
        }

        $section = Craft::$app->entries->getSectionById($sectionId);
        if ($section === null) {
            $summary['errors'][] = "Configured section {$sectionId} not found.";
            return $summary;
        }
        $entryType = $section->getEntryTypes()[0] ?? null;
        if ($entryType === null) {
            $summary['errors'][] = "Section {$section->handle} has no entry types.";
            return $summary;
        }
        $structureId = $section->structureId ?? null;
        $siteId = Craft::$app->getSites()->getPrimarySite()->id;
        $authorId = User::find()->admin(true)->status(null)->one()?->id;

        $files = $this->getManagedFiles();
        if (!$files) {
            $log("No .md files found in {$path}");
            return $summary;
        }

        // Process creates/updates/deletes (sorted: deletes last, then by order).
        uasort($files, static fn($a, $b) => [$a['delete'], $a['order']] <=> [$b['delete'], $b['order']]);

        UserManual::$isSyncing = true;
        try {
            foreach ($files as $slug => $file) {
                $existing = Entry::find()->sectionId($sectionId)->slug($slug)->status(null)->one();

                // --- Tombstone: delete if present -------------------------------
                if ($file['delete']) {
                    if ($existing === null) {
                        $summary['skipped']++;
                        continue;
                    }
                    if ($dryRun) {
                        $log("DELETE  {$slug}");
                        $summary['deleted']++;
                        continue;
                    }
                    if (Craft::$app->getElements()->deleteElement($existing)) {
                        $log("deleted {$slug}");
                        $summary['deleted']++;
                    } else {
                        $summary['errors'][] = "delete failed for {$slug}: " . implode('; ', $existing->getFirstErrors());
                    }
                    continue;
                }

                $title = $file['title'] ?? StringHelper::titleize(str_replace('-', ' ', $slug));
                $body = $file['body'];

                // --- Update in place --------------------------------------------
                if ($existing !== null) {
                    $curBody = $this->normalizeBody((string)$existing->getFieldValue($bodyField));
                    $changed = $existing->title !== $title
                        || $curBody !== $body
                        || $existing->enabled !== $file['enabled'];
                    if (!$changed) {
                        $summary['unchanged']++;
                        continue;
                    }
                    if ($dryRun) {
                        $log("UPDATE  {$slug}");
                        $summary['updated']++;
                        continue;
                    }
                    $existing->title = $title;
                    $existing->enabled = $file['enabled'];
                    $existing->setFieldValue($bodyField, $body);
                    if (Craft::$app->getElements()->saveElement($existing)) {
                        $log("updated {$slug}");
                        $summary['updated']++;
                    } else {
                        $summary['errors'][] = "save failed for {$slug}: " . implode('; ', $existing->getFirstErrors());
                    }
                    continue;
                }

                // --- Create -----------------------------------------------------
                if ($dryRun) {
                    $log("CREATE  {$slug}");
                    $summary['created']++;
                    continue;
                }
                $entry = new Entry();
                $entry->sectionId = $sectionId;
                $entry->typeId = $entryType->id;
                $entry->siteId = $siteId;
                $entry->slug = $slug;
                $entry->title = $title;
                $entry->enabled = $file['enabled'];
                if ($authorId) {
                    $entry->setAuthorId($authorId);
                }
                $entry->setFieldValue($bodyField, $body);
                if (Craft::$app->getElements()->saveElement($entry)) {
                    $log("created {$slug}");
                    $summary['created']++;
                } else {
                    $summary['errors'][] = "create failed for {$slug}: " . implode('; ', $entry->getFirstErrors());
                }
            }

            // Second pass: parent placement (structure sections only), once all
            // pages exist so parents are resolvable.
            if ($structureId && !$dryRun) {
                foreach ($files as $slug => $file) {
                    if ($file['delete'] || !$file['parent']) {
                        continue;
                    }
                    $entry = Entry::find()->sectionId($sectionId)->slug($slug)->status(null)->one();
                    $parent = Entry::find()->sectionId($sectionId)->slug($file['parent'])->status(null)->one();
                    if (!$entry || !$parent) {
                        continue;
                    }
                    if ($entry->getParent()?->id === $parent->id) {
                        continue; // already correctly nested
                    }
                    Craft::$app->getStructures()->append($structureId, $entry, $parent);
                    $log("nested  {$slug} under {$file['parent']}");
                }
            }
        } finally {
            UserManual::$isSyncing = false;
        }

        return $summary;
    }
}
