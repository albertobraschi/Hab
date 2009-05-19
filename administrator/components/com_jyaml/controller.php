<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: controller.php 423 2008-07-01 11:44:05Z hieblmedia $
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

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

// try to set ini - need for Updates, Version info etc.
@ini_set('allow_url_fopen', '1');

class hmyamlController extends JController
{
  /**
   * Main display
   * @access  public
  **/ 
  function display()
  {
    parent::display();
  }
  
  /**
   * Message output for loading popups
   * @run redirect
  **/ 
  function ymsg() 
  {
    global $option, $mainframe;

    $message = JRequest::getVar('message', false);
    $state = JRequest::getVar('state', false);

    if ($message && !$state) {
      $mainframe->enqueueMessage( $message );
    } else {
      $mainframe->enqueueMessage( $message, 'error' );    
    }  

    $mainframe->redirect( JURI::base() . 'index.php?option='.$option);    
  }

  /**
   * Enable Plugin by fail
  **/ 
  function enablePlugin() 
  {
    JYAML::enablePlugin();
    parent::display();
  }

  /**
   * Make design valid by fail
  **/ 
  function makeDesignValid() 
  {
    JYAML::makeDesignValid();  
  }

  /**
   * Delete File function (for ajax)
  **/ 
  function mainDeleteFile() 
  {
    global $mainframe;
    $file = JRequest::getVar('file', false, 'POST');
    $filefull = JPATH_SITE.DS.'templates'.DS.$file;
    
    if ( JFile::delete($filefull) ) {
      echo JText::_( 'YAML DELETE SUCCESS' );
    } else {
      echo 'error';
      echo JText::_( 'YAML DELETE ERROR' );    
    } 
    
    /* Check empty folders in /html/ extension templates */
    if ( strpos($file, DS.'html'.DS.'com_') || strpos($file, DS.'html'.DS.'com_') )
    {
      $folders = explode(DS, $file);
      foreach ($folders as $folder) {
        if ( !JFolder::files(JPATH_SITE.DS.'templates'.DS.$folders[0].DS.'html'.DS.$folders[2]) )
        {
          JFolder::delete(JPATH_SITE.DS.'templates'.DS.$folders[0].DS.'html'.DS.$folders[2]);
        }
        elseif ( !JFolder::files(JPATH_SITE.DS.'templates'.DS.$folders[0].DS.'html'.DS.$folders[2].DS.$folders[3]) )
        {
          JFolder::delete(JPATH_SITE.DS.'templates'.DS.$folders[0].DS.'html'.DS.$folders[2].DS.$folders[3]);
        }        
      }
    }           
    $mainframe->close();
  }
  
  /**
   * Delete Design function (for ajax)
  **/  
  function MAINdeleteDesign() 
  {
    global $mainframe;
    $template = JRequest::getVar('template', false, 'POST');
    $design = JRequest::getVar('design', false, 'POST');

    if ($template && $design) {
      $d = new JYAMLdesignStructure($template, $design);
      $state = $d->deleteDesign();

      if ( $state==='isdefault' )
      {
        echo 'error';
        echo JText::_( 'YAML DELETE FAILED IS DEFAULT' );           
      }
      else 
      {      
        echo JText::_( 'YAML DELETE SUCCESS' );  
      }
    }    
    $mainframe->close();
  }
  
  /**
   * Delete CSS Folder function (for ajax)
  **/   
  function MAINdeleteCssFolder() 
  {
    global $mainframe;
    
    $folder = JRequest::getVar('folder', false, 'POST'); 
    if ($folder) {
      if ( JFolder::delete(JPATH_SITE.DS.'templates'.DS.$folder) ) {
        echo JText::_( 'YAML DELETE CSS FOLDER SUCCESS' );      
      } else {
        echo 'error';
        echo JText::_( 'YAML DELETE CSS FOLDER ERROR' );
      }
    }
    
    $mainframe->close();
  }
  
  /**
   * Delete ExtHTML Folder function (for ajax)
  **/ 
  function MAINdeleteExtHtmlFolder() {
    global $mainframe;
    
    $folder = JRequest::getVar('folder', false, 'POST');  
    if ($folder) {
      if ( JFolder::delete(JPATH_SITE.DS.'templates'.DS.$folder) ) {
        echo JText::_( 'YAML DELETE EXT HTML FOLDER SUCCESS' );      
      } else {
        echo 'error';
        echo JText::_( 'YAML DELETE EXT HTML FOLDER ERROR' );
      }      
    }
    
    $mainframe->close();
  }
  
  /**
   * Installing plugin by press install button
   * @run redirect
  **/   
  function installPlugin() {  
    global $option, $mainframe;
    $db =& JFactory::getDBO();
    
    /* Delete Plugin */
    $query = "DELETE FROM #__plugins WHERE element = 'jyaml' and folder = 'system' LIMIT 1";
    $db->setQuery( $query );
    if (!$result = $db->query()) echo $db->stderr();
    
    $files = array();
    $files2 = array();
    $files = JFolder::files(JPATH_SITE.DS.'plugins'.DS.'system', '^jyaml', true, true );
    $files2 = JFolder::files(JPATH_ADMINISTRATOR.DS.'language', 'plg_system_jyaml.ini$', true, true );
    $files = array_merge($files, $files2);

    foreach ($files as $file)
    {
      JFile::delete($file);
    }
    
    $lang =& JFactory::getLanguage();
    $lang->load( 'com_installer', JPATH_BASE);
    
    /* Install Plugin */
    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php');
    $m = new InstallerModelInstall();
    $r = $m->install();
    
    /* and enable the plugin */
    if ($r) {
      JYAML::enablePlugin();
    } else {
      $mainframe->redirect( JURI::base() . 'index.php?option='.$option );
    }
  }
  
  /**
   * Installing default by press install button
   * @run redirect
  **/ 
  function installDefaultTemplate() {  
    global $option, $mainframe;
    $db =& JFactory::getDBO();
    
    $lang =& JFactory::getLanguage();
    $lang->load( 'com_installer', JPATH_BASE);
    
    /* Install Plugin */
    require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'models'.DS.'install.php');
    $m = new InstallerModelInstall();
    $r = $m->install();  
    
    if ($r) {
      // Activate Template as default        
      JYAML::activateTemplate('hm_yaml', false);
    }

    $mainframe->redirect( JURI::base() . 'index.php?option='.$option );
  }
  
  function activateTemplate() {
    JYAML::activateTemplate(JRequest::getVar('switch_template', false, 'POST'), true);
  }
  
}
?>
