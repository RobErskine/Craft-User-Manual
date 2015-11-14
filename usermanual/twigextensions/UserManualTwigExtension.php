<?php

namespace Craft;

use Twig_Extension;
use Twig_Function_Method;

class UserManualTwigExtension extends Twig_Extension
{
    public function getName()
    {
        return 'User Manual Twig Extension';
    }

    public function getFunctions()
    {
        return array(
            'getHelpDocument' => new Twig_Function_Method($this, 'getHelpDocument'),
        );
    }

    public function getHelpDocument()
    {
        $oldPath = craft()->path->getTemplatesPath();
        $newPath = craft()->path->getPluginsPath().'usermanual/templates/';
        $template = '_body.html';
        $settings = craft()->plugins->getPlugin('usermanual')->getSettings();
        $segments = craft()->request->segments;
        $segment = end($segments);
        $sectionId = $settings->section;

        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = $sectionId;
        $criteria->slug = $segment;

        if (!$criteria->total()) {
            $criteria->slug = null;
        }

        $entry = $criteria->first();

        // If the app has not been set up at all or there are no entires, redirect to the settings page
        if (!$sectionId || !$entry) {
            craft()->request->redirect(UrlHelper::getCpUrl('settings/plugins/usermanual/'));
        } else {
            if ($settings->templateOverride) {
                $newPath = craft()->path->getSiteTemplatesPath();
                $template = $settings->templateOverride;
            }

            craft()->path->setTemplatesPath($newPath);

            $output = craft()->templates->render($template, [
                'entry' => $entry,
            ]);
            craft()->path->setTemplatesPath($oldPath);

            return $output;
        }
    }
}
