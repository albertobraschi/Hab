<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: view.html.php 423 2008-07-01 11:44:05Z hieblmedia $
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

jimport( 'joomla.application.component.view' );

class hmyamlViewhmyamldefaultconf extends JView
{
  /**
   * display method
   * @return void
   **/
  function display($tpl = null)
  {
    // make mainframe variable available
    global $mainframe, $option;
        
    // get template name
    $template_name =  JRequest::getVar( 'template_name',  'hm_yaml', 'REQUEST' );
    $this->assignRef('template_name', $template_name);
    
    $design =  JRequest::getVar( 'design',  'default', 'REQUEST' );
    $this->assignRef('design', $design);
    
    $xmlfile = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'config'.DS.'_global.xml';    
    $config  = JYAML::readConfig($template_name, $xmlfile);
    $this->assignRef('config', $config);
    
    $designlist      = JYAML::getDesignList(true);
    $this->assignRef('designlist', $designlist);
    
    $positions = JYAML::getPositions();
    $this->assignRef('positions', $positions);    

    /*** Load Configuration ***/
    $conf        = JYAML::parseConfigDesign($this->config); 
    $this->assignRef('conf', $conf);

    parent::display($tpl);
  }
}
