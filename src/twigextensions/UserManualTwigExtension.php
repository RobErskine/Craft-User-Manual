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

namespace roberskine\usermanual\twigextensions;

use roberskine\usermanual\UserManual;

use Craft;
use craft\elements\Entry;
use craft\helpers\UrlHelper;
use craft\web\View;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use yii\base\Exception;


/**
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 */
class UserManualTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'User Manual Twig Extension';
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getHelpDocument', [$this, 'getHelpDocument']),
            new TwigFunction('craftMajorVersion', [$this, 'craftMajorVersion']),
        ];
    }

    /**
     * Render an entry in the given section using the nominated template
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function getHelpDocument(): string
    {
        $settings = UserManual::$plugin->getSettings();
        $query = Entry::find();

        $segments = Craft::$app->request->segments;
        $segment = end($segments);
        $sectionId = $settings->section;

        if (count($segments) === 1 && $segment === 'usermanual') {
            $slug = null;
        } else {
            $slug = $segment;
        }

        $criteria = [
            'sectionId' => $sectionId,
            'slug' => $slug,
        ];

        Craft::configure($query, $criteria);
        $entry = $query->one();

        // If the app does not have a section selected, return an error message to let the admin know
        if (!$sectionId) {
            return 'There is no section selected for the User Manual plugin. Please check the settings page.';
        }

        // If there are no entries in the selected section, return an error message to let the admin know
        if (!$entry) {
            return 'There are no entries in the selected section for the User Manual Plugin. Entries must be enabled and have a slug to be displayed.';
        } else {
            if ($settings->templateOverride) {
                // Setting the mode also sets the templatepath to the default for that mode
                Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);
                $template = $settings->templateOverride;
            } else {
                $template = 'usermanual/_body.twig';
            }

            $output = Craft::$app->view->renderTemplate($template, [
                'entry' => $entry,
            ]);

            // Ensure template mode is set back to control panel
            Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

            return $output;
        }
    }

    public function craftMajorVersion()
    {
        $version = Craft::$app->getVersion();
        return $version[0];
    }
}
