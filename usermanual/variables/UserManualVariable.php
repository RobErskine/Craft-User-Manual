<?php 
namespace Craft;

class UserManualVariable
{
	public function getSettings(){
		return craft()->userManual_settings->getChannelSetting();
	}
	public function getCpUrl(){
		return craft()->userManual_settings->getCp();
	}
}