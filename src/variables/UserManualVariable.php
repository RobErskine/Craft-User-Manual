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

namespace roberskine\usermanual\variables;

use roberskine\usermanual\UserManual;

use Craft;
use craft\base\Model;

/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class UserManualVariable
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return UserManual::$plugin->getName();
    }

    public function getSettings(): ?Model
    {
        return UserManual::$plugin->getSettings();
    }
}
