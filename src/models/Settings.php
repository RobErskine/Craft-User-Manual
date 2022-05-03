<?php

/**
 * usermanual plugin for Craft CMS 3.x
 *
 * Craft User Manual allows developers (or even content editors) to provide CMS
 * documentation using Craft's built-in sections (singles, channels, or structures)
 * to create a `User Manual` or `Help` section directly in the control panel.
 *
 * @link      https://twitter.com/erskinerob
 * @copyright Copyright (c) 2018 Rob Erskine
 */

namespace hillholliday\usermanual\models;

use hillholliday\usermanual\UserManual;

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
     * @var string
     */
    public $pluginNameOverride;

    /**
     * @var string
     */
    public $templateOverride;

    /**
     * @var integer
     */
    public $section;

    /**
     * @var boolean
     */
    public $enabledSideBar = true;



    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['pluginNameOverride', 'templateOverride'], 'string'],
            ['section', 'number'],
            ['enabledSideBar', 'boolean']
        ];
    }
}
