/**
 * usermanual plugin for Craft CMS
 *
 * usermanual JS
 *
 * @author    Rob Erskine
 * @copyright Copyright (c) 2018 Rob Erskine
 * @link      https://twitter.com/erskinerob
 * @package   Usermanual
 * @since     2.0.0
 */

 $('document').ready(function () {
   //  check if hash tag is availbe and
   //  get the menu id
   if(window.location.hash.substr(1)){
     $('body').addClass('peUserManual');
   }
   else{
     $('#global-header').hide();
   }

 });
