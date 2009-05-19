<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: admin.jyaml.php 423 2008-07-01 11:44:05Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 423 $
 * @lastmodified    $Date: 2008-07-01 13:44:05 +0200 (Di, 01. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();  

/*
** Force deleting jyaml package installer folders - needed to use with FTP-Mode
*/
if (JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jyaml_package')) {
  JFolder::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jyaml_package');
} 
if (JFolder::exists(JPATH_SITE.DS.'components'.DS.'com_jyaml_package')) {
  JFolder::delete(JPATH_SITE.DS.'components'.DS.'com_jyaml_package');
}
////////////////////////////////////////////////////////////////////////////////


require_once (JPATH_COMPONENT.DS.'controller.php');
require_once( JPATH_COMPONENT.DS.'helper.php' );

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) 
{
  require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Create the controller
$classname  = 'hmyamlController'.$controller;
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

$document =& JFactory::getDocument();
        
// Add required head files 
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal', 'a.modal');
     
$document =& JFactory::getDocument();

$document->addStyleSheet('templates/system/css/system.css');
$document->addStyleSheet('components/'.$option.'/css/stylesheet.css');

$document->addScript('components/'.$option.'/js/jquery.js');
$document->addScript('components/'.$option.'/js/jquery.cookie.js');
$document->addScript('components/'.$option.'/js/jquery.form.js');
$document->addScript('components/'.$option.'/js/jquery.treeview.js');
$document->addScript('components/'.$option.'/js/jquery.yslider.js');
$document->addScript('components/'.$option.'/js/component.js');
$document->addScript('components/'.$option.'/tools/codepress/codepress.js');

// Scripts with language tags
$script = "" .
"jQuery(document).ready(function(){      
  jQuery(\"a.MAINdeleteFile\").click(function() {
    var rel   = jQuery(this).attr(\"rel\");
    var press = confirm('".JText::_( 'YAML CONFIRM DELETE' )."\\n'+rel);        
    if (press) {
      if (rel) {
        jQuery.ajax({
         type: \"POST\",
         url: \"index.php\",
         data: {'option':'".$option."', 'task':'MAINdeleteFile', 'file':rel},
         success: function(msg){
           var state = msg.search(/^error/);
           var msg = msg.replace(/error/, '');
           if (state==-1) {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=0');
           } else {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=1');                 
           }
         }
        });
      }
    }
    return false;
  }); 
  
  jQuery(\"a.MAINdeleteDesign\").click(function() {
    var tmpl   = jQuery(this).attr(\"tmpl\");
    var design = jQuery(this).attr(\"design\");
    var press  = confirm('".JText::_( 'YAML CONFIRM DELETE DESIGN' )."\\n'+design);        
    if (press) {
      if (tmpl && design) {
        jQuery.ajax({
         type: \"POST\",
         url: \"index.php\",
         data: {'option':'".$option."', 'task':'MAINdeleteDesign', 'design':design, 'template':tmpl},
         success: function(msg){
           var state = msg.search(/^error/);
           var msg = msg.replace(/error/, '');
           if (state==-1) {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=0');
           } else {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=1');                 
           }              
         }
        });
      }
    }
    return false;
  });
  
  jQuery(\"a.MAINdeleteCssFolder\").click(function() {
    var folder = jQuery(this).attr(\"folder\");
    var press  = confirm('".JText::_( 'YAML CONFIRM DELETE CSS FOLDER' )."\\n'+folder);        
    if (press) {
      if (folder) {
        jQuery.ajax({
          type: \"POST\",
          url: \"index.php\",
          data: {'option':'".$option."', 'task':'MAINdeleteCssFolder', 'folder':folder},
          success: function(msg){
           var state = msg.search(/^error/);
           var msg = msg.replace(/error/, '');
           if (state==-1) {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=0');
           } else {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=1');                 
           }
         }
        });
      }
    }
    return false;
  }); 
  
  jQuery(\"a.MAINdeleteExtHtmlFolder\").click(function() {
    var folder = jQuery(this).attr(\"folder\");
    var press  = confirm('".JText::_( 'YAML CONFIRM DELETE EXT HTML FOLDER' )."\\n'+folder);        
    if (press) {
      if (folder) {
        jQuery.ajax({
          type: \"POST\",
          url: \"index.php\",
          data: {'option':'".$option."', 'task':'MAINdeleteExtHtmlFolder', 'folder':folder},
          success: function(msg){
           var state = msg.search(/^error/);
           var msg = msg.replace(/error/, '');
           if (state==-1) {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=0');
           } else {
             window.location.replace('index.php?option=".$option."&task=ymsg&message='+msg+'&state=1');                 
           }
         }
        });
      }
    }
    return false;
  });
  
    
});";
$document->addScriptDeclaration( str_replace( array("\r\n", "\t", "  "), " ", $script) );
?>