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

namespace hillholliday\usermanual\variables;

use hillholliday\usermanual\UserManual;

use Craft;

/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class UserManualVariable
{
    // Public Methods
    // =========================================================================

    public function getName()
    {
        $name = UserManual::$plugin->getName();

        return $name;
    }

    public function getSettings()
    {
        $settings = UserManual::$plugin->getSettings();

        return $settings;
    }
}
