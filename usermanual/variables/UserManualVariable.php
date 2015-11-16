<?php

namespace Craft;

class UserManualVariable
{
    public function getSettings()
    {
        return craft()->plugins->getPlugin('usermanual')->getSettings();
    }

    public function getName()
    {
        $plugin = craft()->plugins->getPlugin('usermanual');

        return $plugin->getName();
    }
}
