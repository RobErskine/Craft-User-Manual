<?php

/**
 * usermanual plugin for Craft CMS 4.x
 *
 * Craft User Manual allows developers (or even content editors) to provide CMS
 * documentation using Craft's built-in sections (singles, channels, or structures)
 * to create a `User Manual` or `Help` section directly in the control panel.
 *
 * @link      https://twitter.com/erskinerob
 * @copyright Copyright (c) 2018 Rob Erskine
 */

namespace roberskine\usermanual\assetbundles\usermanual;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class UserManualAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@roberskine/usermanual/assetbundles/usermanual/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/UserManual.js',
        ];

        $this->css = [
            'css/UserManual.css',
        ];

        parent::init();
    }
}
