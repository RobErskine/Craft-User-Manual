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

namespace roberskine\usermanual\twigextensions;

use Craft;

use craft\web\View;
use Twig\TwigFunction;
use yii\base\Exception;
use craft\elements\Entry;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use craft\helpers\UrlHelper;
use Twig\Error\RuntimeError;
use roberskine\usermanual\UserManual;
use Twig\Extension\AbstractExtension;


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
        $urlSegment = $settings->urlSegment;

        if (count($segments) === 1 && $segment === $urlSegment) {
            // Get the first entry in the section when viewing the base URL
            $criteria = [
                'sectionId' => $sectionId,
                'limit' => 1,
                'orderBy' => 'dateCreated ASC'
            ];
        } else {
            $criteria = [
                'sectionId' => $sectionId,
                'id' => $segment,
            ];
        }

        Craft::configure($query, $criteria);
        $entry = $query->one();

        // If the app does not have a section selected, return an error message to let the admin know
        if (!$sectionId) {
            // check if the user is using a config file
            if (Craft::$app->config->getConfigFromFile('usermanual')) {
                return Craft::t('usermanual', 'no section error for config file');
            }
            return Craft::t('usermanual', 'no section error');
        }

        // If there are no entries in the selected section, return an error message to let the admin know
        if (!$entry) {
            return Craft::t('usermanual', 'no entry error');
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
