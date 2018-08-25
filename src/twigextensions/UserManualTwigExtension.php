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

namespace hillholliday\usermanual\twigextensions;

use hillholliday\usermanual\UserManual;

use Craft;
use craft\elements\Entry;
use craft\web\View;

/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class UserManualTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'User Manual Twig Extension';
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getHelpDocument', [$this, 'getHelpDocument']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function getHelpDocument($text = null)
    {
        $view = Craft::$app->getView();
        $settings = UserManual::$plugin->getSettings();
        $query = Entry::find();

        $newPath = UserManual::$plugin->getPath() . 'usermanual/templates/';
        $template = '_body.twig';
        $segments = Craft::$app->request->segments;
        $segment = end($segments);
        $sectionId = $settings->section;


        $criteria = [
            'sectionId' => $sectionId,
            'slug' => $segment,
        ];

        // $criteria->slug = $segment;
        // if (!$criteria->total()) {
        //     $criteria->slug = null;
        // }

        Craft::configure($query, $criteria);
        $entry = $query->one();

        // If the app has not been set up at all or there are no entires, redirect to the settings page
        if (!$sectionId || !$entry) {
            Craft::$app->getResponse()->redirect(UrlHelper::getCpUrl('settings/plugins/usermanual/'));
        } else {
            if ($settings->templateOverride) {
                // Setting the mode also sets the templatepath to the default for that mode
                $view->setTemplateMode(View::TEMPLATE_MODE_SITE);
                $template = $settings->templateOverride;
            } else {
                $view->setTemplatesPath($newPath);
            }

            $output = $view->renderTemplate($template, [
                'entry' => $entry,
            ]);

            // Ensure template mode is set back to control panel
            $view->setTemplateMode(View::TEMPLATE_MODE_CP);
            return $output;
        }
    }
}
