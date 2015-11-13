<?php

namespace Craft;

use Twig_Extension;
use Twig_Function_Method;
use Twig_Filter_Method;

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

  public function getHelpDocument($path = '') {
    $segments = craft()->request->segments;
    $segment = end($segments);

    $sectionId = craft()->userManual_settings->getChannelSetting();

    $criteria = craft()->elements->getCriteria(ElementType::Entry);
    $criteria->sectionId = $sectionId;
    $criteria->slug = $segment;
    $entry = $criteria->find();

  // if the app has not been set up at all, redirect to the settings page
    if($sectionId == ''){
      craft()->request->redirect(UrlHelper::getCpUrl('settings/plugins/usermanual/'));
    }
  // if there is no url segment, load the homepage
    else if(! $entry){
      $firstCriteria = craft()->elements->getCriteria(ElementType::Entry);
      $firstCriteria->sectionId = $sectionId;
      $firstCriteria->limit = 1;
      $firstEntry = $firstCriteria->find();
      echo($firstEntry[0]['body']);
    }
  // if there is a defined page, load that page's body
    else{
      echo($entry[0]['body']);
    }
  }
}

?>
