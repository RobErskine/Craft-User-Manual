<?php

/**
 * usermanual plugin for Craft CMS 6.x
 *
 * Craft User Manual allows developers (or even content editors) to provide CMS
 * documentation using Craft's built-in sections (singles, channels, or structures)
 * to create a `User Manual` or `Help` section directly in the control panel.
 *
 * @link      https://twitter.com/erskinerob
 * @copyright Copyright (c) 2018 Rob Erskine
 */

namespace roberskine\usermanual\models;

use roberskine\usermanual\UserManual;

use Craft;
use craft\base\Model;

/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string | null
     */
    public ?string $pluginNameOverride = "";

    /**
     * @var string | null
     */
    public ?string $templateOverride = "";

    /**
     * @var int|string|null
     */
    public int|string|null $section = null;

    /**
     * @var boolean
     */
    public bool $enabledSideBar = true;

    /**
     * @var string
     */
    public string $urlSegment = 'usermanual';

    /**
     * Filesystem path (alias-aware, e.g. "@root/help-manual") to a folder of
     * `.md` files that the `usermanual/sync` console command imports into the
     * configured section. Empty disables the sync.
     *
     * @var string|null
     * @since 5.1.0
     */
    public ?string $managedFolder = null;

    /**
     * When true, entries in the configured section that have a backing `.md`
     * file in `managedFolder` become read-only in the control panel — the
     * markdown is the source of truth. Entries with no backing file (e.g. a
     * CP-maintained changelog) stay editable. The `usermanual/sync` command
     * bypasses this guard while it runs.
     *
     * @var bool
     * @since 5.1.0
     */
    public bool $readOnlyManaged = false;

    /**
     * Handle of the field on the section's entries that stores the markdown
     * body. Defaults to `body` (the plugin's default template field). Sites
     * using a `templateOverride` with a different field (e.g. `helpContent`)
     * set this accordingly.
     *
     * @var string
     * @since 5.1.0
     */
    public string $bodyField = 'body';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['pluginNameOverride', 'templateOverride', 'urlSegment', 'managedFolder', 'bodyField'], 'string'],
            ['section', 'number'],
            [['enabledSideBar', 'readOnlyManaged'], 'boolean'],
        ];
    }
}
