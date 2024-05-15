<?php

/**
 * usermanual plugin for Craft CMS 4.x / 5.x
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



    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['pluginNameOverride', 'templateOverride'], 'string'],
            ['section', function ($attribute): void
                {
                    if (!is_int($this->$attribute) && !is_string($this->$attribute)) {
                        $this->addError($attribute, Craft::t('usermanual', 'config file section error'));
                    }
                }],
            ['enabledSideBar', 'boolean']
        ];
    }
}
