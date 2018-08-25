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

        Craft::$app->view->registerTwigExtension(new UserManualTwigExtension());

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['usermanual/(?P<userManualPath>[a-zéñåA-Z0-9\-\_\/]+)'] = ['template' => 'usermanual/index'];
        });

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('usermanual', UserManualVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    $url = Craft::$app->getCpUrl('settings/plugins/usermanual/');
                    Craft::$app->getResponse()->redirect($url);
                }
            }
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
        return Craft::$app->view->renderTemplate(
            'usermanual/settings',
            [
                'settings' => $this->getSettings()
            ]
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
}
