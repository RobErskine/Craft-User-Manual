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

/**
 * usermanual config.php
 *
 * This file exists only as a template for the usermanual settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'usermanual.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    'pluginNameOverride' => null,
    'templateOverride' => null,
    'section' => null, // section ID (int)
    'enabledSideBar' => true,
];
