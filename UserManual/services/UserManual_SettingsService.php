<?php
namespace Craft;

class UserManual_SettingsService extends BaseApplicationComponent{
	public function getChannelSetting(){
		$plugin = craft()->plugins->getPlugin('UserManual');
		$settings = $plugin->settings;

		return $settings->channels;
	}

	public function getCp(){
		return craft()->config->get('cpTrigger');
	}
}