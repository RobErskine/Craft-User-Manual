<?php
namespace Craft;

class UserManualPlugin extends BasePlugin
{

	function getName()
	{
		return Craft::t('User Manual');
	}

	function getVersion()
	{
		return '1.0.0';
	}

	function getDeveloper()
	{
		return 'Hill Holliday';
	}

	function getDeveloperUrl()
	{
		return 'http://hhcc.com';
	}

	function hasCpSection()
    {
        return true;
    }

	public function addTwigExtension()
	{
		Craft::import('plugins.userManual.twigextensions.userManualTwigExtension');
		return new userManualTwigExtension();
	}

	public function registerCpRoutes() {
	    return array(
	      'usermanual/(?P<userManualPath>[a-zA-Z0-9\-\_\/]+)' => 'usermanual/index'
	    );
	  }


    protected function defineSettings()
    {
		return array(
            'channels' => array(AttributeType::Mixed, 'default' => ""),
        );
    }

	public function getSettingsHtml(){

		$options= [[
			'label' => 'Please select',
			'value' => ''
		]];

		foreach(craft()->sections->getAllSections() as $section){
			$options[] = [
				'label' => $section['name'],
				'value' => $section['handle']
			];
		}

		return craft()->templates->render('UserManual/settings',array(
			'settings' => $this->getSettings(),
			'options' => $options
		));
	}

    public function onAfterInstall()
    {
        craft()->request->redirect(UrlHelper::getCpUrl('settings/plugins/usermanual/'));
    }

}