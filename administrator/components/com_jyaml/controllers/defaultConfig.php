<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: defaultConfig.php 423 2008-07-01 11:44:05Z hieblmedia $
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

class hmyamlControllerdefaultConfig extends hmyamlController
{
  /**
   * constructor (registers additional tasks to methods)
   * @return void
   */
  function __construct()
  {
    parent::__construct();
  }

  /**
   * display the edit form
   * @return void
   */
  function edit()
  {    
    JRequest::setVar( 'view', 'hmyamldefaultconf' );
    JRequest::setVar('hidemainmenu', 1);
    parent::display();
  }
  
  /**
   * apply a record (and redirect to self)
   */  
  function apply() 
  {
    $this->save(true);
  }

  /**
   * save a record (and redirect to main page)
   * @return void
   */
  function save($apply=false)
  {
    global $option, $mainframe;
        
    $template_name = JRequest::getVar( 'template_name',  'hm_yaml', 'POST' );
    
    $now = getdate();
    $now = $now['mday'].'.'.$now['month'].' '.$now['year'].' - '.$now['hours'].':'.$now['minutes'].':'.$now['seconds'];
    /* XML File */
    $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
          ."<yaml>\n"
          ."  <name>_global.xml</name>\n"
          ."  <description>Global Template Settings</description>\n\n"
          ."  <lastModification>".$now."</lastModification>\n\n"
          ."  <config>\n";
          
    // HTML File
    $html_file = JRequest::getVar( 'html_file',  false, 'POST' );
    if ($html_file) 
    {
      $xml .= "    <html_file>".$html_file."</html_file>\n\n";      
    }
    
    // Design
    $design = JRequest::getVar( 'design',  'default', 'POST' );
    $xml .= "    <design>".$design."</design>\n\n";  
            
    // Others        
    $debug = JRequest::getVar( 'debug',  '0', 'POST' );
    $xml .= "    <debug>".$debug."</debug>\n";
            
    // Close xml tags
    $xml .= "  </config>\n"
          ."</yaml>";    
          
    // Write file (_global.xml)
    jimport('joomla.filesystem.file');
    $file = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'config'.DS.'_global.xml';
    
    if ( JFile::write( $file, $xml ) ) 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML SAVED SUCCESS' ) );  
      if ($apply)
      {
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=defaultConfig&task=edit&template_name='.$template_name );
      }
      else 
      {
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');      
      }
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML SAVED FAILED' ), 'error' );  
      $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=defaultConfig&task=edit&template_name='.$template_name );
    }
  }  
}
?>