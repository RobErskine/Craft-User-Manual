<?php

namespace Craft;

class UserManualPlugin extends BasePlugin
{
    public function getName()
    {
        $pluginName = Craft::t('User Manual');
        $pluginNameOverride = $this->getSettings()->pluginNameOverride;

        return ($pluginNameOverride) ? $pluginNameOverride : $pluginName;
    }

    public function getVersion()
    {
        return '1.1.1';
    }

    public function getDeveloper()
    {
        return 'Hill Holliday';
    }

    public function getDeveloperUrl()
    {
        return 'http://hhcc.com';
    }

    public function hasCpSection()
    {
        return true;
    }

    public function addTwigExtension()
    {
        Craft::import('plugins.usermanual.twigextensions.UserManualTwigExtension');

        return new UserManualTwigExtension();
    }

    public function registerCpRoutes()
    {
        return [
    		'usermanual/(?P<userManualPath>[a-zéñåA-Z0-9\-\_\/]+)' => 'usermanual/index',
        ];
    }

    protected function defineSettings()
    {
        return [
	        'pluginNameOverride' => AttributeType::String,
	        'templateOverride' => AttributeType::Template,
	        'section' => AttributeType::Number,
        ];
    }

    public function getSettingsHtml()
    {
        $options = [[
            'label' => '',
            'value' => '',
        ]];

        foreach (craft()->sections->getAllSections() as $section) {
            if (!$section->hasUrls) {
                continue;
            }
            $options[] = [
                'label' => $section['name'],
                'value' => $section['id'],
            ];
        }

        return craft()->templates->render('usermanual/settings', [
            'settings' => $this->getSettings(),
            'options' => $options,
            'siteTemplatesPath' => craft()->path->getSiteTemplatesPath(),
        ]);
    }

    public function onAfterInstall()
    {
        craft()->request->redirect(UrlHelper::getCpUrl('settings/plugins/usermanual/'));
    }

    public function getSettings()
    {
        $settings = parent::getSettings();
        foreach ($settings as $name => $value) {
            $configValue = craft()->config->get($name, 'usermanual');
            $settings->$name = is_null($configValue) ? $value : $configValue;
        }

        // Allow handles from config
        if (!is_numeric($settings->section)) {
            $section = craft()->sections->getSectionByHandle('homepage');
            if ($section) {
                $settings->section = $section->id;
            }
        }
        return $settings;
    }
}
