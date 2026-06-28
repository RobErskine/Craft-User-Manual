<?php

/**
 * usermanual plugin for Craft CMS 4.x / 5.x
 *
 * @link      https://twitter.com/erskinerob
 * @copyright Copyright (c) 2018 Rob Erskine
 */

namespace roberskine\usermanual\console\controllers;

use craft\console\Controller;
use craft\helpers\Console;
use roberskine\usermanual\UserManual;
use yii\console\ExitCode;

/**
 * Syncs a folder of markdown files into the User Manual section.
 *
 * Usage:
 *   craft usermanual/sync            # import managedFolder/*.md into the section
 *   craft usermanual/sync --dry-run  # report what would change, write nothing
 *
 * Configure `managedFolder` (and optionally `bodyField`) in config/usermanual.php.
 * The sync is one-way (git → DB), idempotent, and change-detecting.
 *
 * @since 5.1.0
 */
class SyncController extends Controller
{
    /**
     * @var bool Report what would change without writing anything.
     */
    public bool $dryRun = false;

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['dryRun']);
    }

    public function optionAliases(): array
    {
        return array_merge(parent::optionAliases(), ['d' => 'dryRun']);
    }

    /**
     * Import the managed markdown folder into the User Manual section.
     */
    public function actionIndex(): int
    {
        $settings = UserManual::$plugin->getSettings();
        $this->stdout("User Manual sync" . ($this->dryRun ? " (dry run)" : "") . "\n", Console::FG_CYAN);
        $this->stdout("  folder : {$settings->managedFolder}\n");
        $this->stdout("  section: {$settings->section}   bodyField: {$settings->bodyField}\n\n");

        $summary = UserManual::$plugin->getManual()->sync(
            $this->dryRun,
            fn(string $msg) => $this->stdout("  {$msg}\n", Console::FG_GREY)
        );

        $this->stdout("\n");
        $this->stdout("  created   : {$summary['created']}\n");
        $this->stdout("  updated   : {$summary['updated']}\n");
        $this->stdout("  unchanged : {$summary['unchanged']}\n");
        $this->stdout("  deleted   : {$summary['deleted']}\n");
        $this->stdout("  skipped   : {$summary['skipped']}\n");

        if (!empty($summary['errors'])) {
            $this->stderr("\n  ERRORS:\n", Console::FG_RED);
            foreach ($summary['errors'] as $err) {
                $this->stderr("   - {$err}\n", Console::FG_RED);
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("\nDone.\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
}
