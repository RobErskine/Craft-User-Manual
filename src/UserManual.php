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

namespace roberskine\usermanual;

use roberskine\usermanual\variables\UserManualVariable;
use roberskine\usermanual\twigextensions\UserManualTwigExtension;
use roberskine\usermanual\models\Settings;

use Craft;
use craft\base\Model;
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
    public static UserManual $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '4.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
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
    public function getName(): string
    {
        $pluginName = Craft::t('usermanual', 'User Manual');
        $pluginNameOverride = $this->getSettings()->pluginNameOverride;

        return ($pluginNameOverride)
            ?: $pluginName;
    }

    public function registerCpUrlRules(RegisterUrlRulesEvent $event): void
    {
        $rules = [
            'usermanual/<userManualPath:([a-zéñåA-Z0-9\-\_\/]+)?>' => ['template' => 'usermanual/index'],
        ];

        $event->rules = array_merge($event->rules, $rules);
    }

    public function afterInstallPlugin(PluginEvent $event): void
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
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {

        // Get override settings from config file.
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'usermanual/settings',
            [
                'settings' => $this->getSettings(),
                'overrides' => array_keys($overrides),
                'options' => $this->getSectionOptions(),
                'siteTemplatesPath' => Craft::$app->getPath()->getSiteTemplatesPath(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettings(): ?Model
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

        // if section is a string, convert to int by looking up the section ID
        if (($settings !== null) && is_string($settings->section)) {
            $sections = $this->getSections();
            foreach ($sections as $section) {
                if ($section['handle'] === $settings->section) {
                    $settings->section = $section['id'];
                }
            }
        }

        return $settings;
    }

    // Private Methods
    // =========================================================================

    private function _addTwigExtensions(): void
    {
        Craft::$app->view->twig->addExtension(new UserManualTwigExtension);
    }

    private function getSectionOptions(): array
    {

        $sections = $this->getSections();
        $options = [];

        foreach ($sections as $section) {
            $siteSettings = $this->getSectionSiteSettings($section['id']);
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

        if (!empty($options)) {
            $optionToSelectNoSection = [
                'label' => '',
                'value' => '',
            ];

            array_unshift($options, $optionToSelectNoSection);
        }

        return $options;
    }

    // Abstracted Method to get major version
    private function getMajorVersion(): string
    {
        $version = Craft::$app->getVersion();
        return $version[0]; // Assuming version format is X.Y.Z
    }

    // Abstracted Method to get sections
    private function getSections(): array
    {
        $majorVersion = $this->getMajorVersion();

        if ($majorVersion === '4') {
            $sections = Craft::$app->sections->getAllSections();
        } else {
            $sections = Craft::$app->entries->getAllSections();
        }

        return $sections;
    }

    // Abstracted method to get site settings based on version
    private function getSectionSiteSettings($sectionId): array
    {
        $majorVersion = $this->getMajorVersion();

        if ($majorVersion === '4') {
            return Craft::$app->sections->getSectionSiteSettings($sectionId);
        }

        return Craft::$app->entries->getSectionSiteSettings($sectionId);
    }

}
