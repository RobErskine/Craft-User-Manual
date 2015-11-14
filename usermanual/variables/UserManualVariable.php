<?php

namespace Craft;

class UserManualVariable
{
    public function settings()
    {
        return craft()->plugins->getPlugin('usermanual')->getSettings();
    }
}
