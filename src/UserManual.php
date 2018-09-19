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

namespace hillholliday\usermanual;

use hillholliday\usermanual\variables\UserManualVariable;
use hillholliday\usermanual\twigextensions\UserManualTwigExtension;
use hillholliday\usermanual\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;

use yii\base\Event;

/**
 * Class Usermanual
 *
 * @author    Rob Erskine
 * @package   Usermanual
 * @since     2.0.0
 *
 */
class UserManual extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var UserManual
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '2.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->name = $this->getName();

        // Register twig extensions
        $this->_addTwigExtensions();

        // Register CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [$this, 'registerCpUrlRules']
        );

        // Register variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('userManual', UserManualVariable::class);
            }
        );

        // Plugin Install event
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            [$this, 'afterInstallPlugin']
        );

        Craft::info(
            Craft::t(
                'usermanual',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Returns the user-facing name of the plugin, which can override the name
     * in composer.json
     *
     * @return string
     */
    public function getName()
    {
        $pluginName = Craft::t('usermanual', 'User Manual');
        $pluginNameOverride = $this->getSettings()->pluginNameOverride;

        return ($pluginNameOverride)
            ? $pluginNameOverride
            : $pluginName;
    }

    public function registerCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $rules = [
            'usermanual/<userManualPath:([a-zéñåA-Z0-9\-\_\/]+)?>' => ['template' => 'usermanual/index'],
        ];

        $event->rules = array_merge($event->rules, $rules);
    }

    public function afterInstallPlugin(PluginEvent $event)
    {
        $isCpRequest = Craft::$app->getRequest()->isCpRequest;

        if ($event->plugin === $this && $isCpRequest) {
            Craft::$app->controller->redirect(UrlHelper::cpUrl('settings/plugins/usermanual/'))->send();
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        $options = [[
            'label' => '',
            'value' => '',
        ]];
        foreach (Craft::$app->sections->getAllSections() as $section) {
            $siteSettings = Craft::$app->sections->getSectionSiteSettings($section['id']);
            $hasUrls = false;
            foreach ($siteSettings as $siteSetting) {
                if ($siteSetting->hasUrls) {
                    $hasUrls = true;
                }
            }

            if (!$hasUrls) {
                continue;
            }
            $options[] = [
                'label' => $section['name'],
                'value' => $section['id'],
            ];
        }

        // Get override settings from config file.
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'usermanual/settings',
            [
                'settings' => $this->getSettings(),
                'overrides' => array_keys($overrides),
                'options' => $options,
                'siteTemplatesPath' => Craft::$app->getPath()->getSiteTemplatesPath(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettings()
    {
        $settings = parent::getSettings();
        $config = Craft::$app->config->getConfigFromFile('usermanual');

        foreach ($settings as $settingName => $settingValue) {
            $settingValueOverride = null;
            foreach ($config as $configName => $configValue) {
                if ($configName === $settingName) {
                    $settingValueOverride = $configValue;
                }
            }
            $settings->$settingName = $settingValueOverride ?? $settingValue;
        }

        // Allow handles from config
        if (!is_numeric($settings->section)) {
            $section = Craft::$app->getSections()->getSectionByHandle('homepage');
            if ($section) {
                $settings->section = $section->id;
            }
        }

        return $settings;
    }

    // Private Methods
    // =========================================================================

    private function _addTwigExtensions()
    {
        Craft::$app->view->twig->addExtension(new UserManualTwigExtension);
    }
}
