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

class hmyamlViewhmyamldesignconf extends JView
{
  /**
   * display method
   * @return void
   **/
  function display($tpl = null)
  {
    // make mainframe variable available
    global $mainframe, $option;
    
        
    // get design and template name
    $template_name =  JRequest::getVar( 'template_name',  'hm_yaml', 'REQUEST' );
    $this->assignRef('template_name', $template_name);
    
    $design =  JRequest::getVar( 'design',  'default', 'REQUEST' );
    $this->assignRef('design', $design);
    
    // Design XML
    $xmlfile = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'config'.DS.$design.'.xml'; ////////////
    $config = JYAML::readConfig($template_name, $xmlfile); ////////////////
    $this->assignRef('config', $config);
    
    // Global XML for referenz view
    $xmlfile       = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'config'.DS.'_global.xml'; //////////////
    $config_global = JYAML::readConfig($template_name, $xmlfile); /////////////
    $this->assignRef('config_global', $config_global);
    
    $html_list       = JYAML::getHTMLList();
    $this->assignRef('html_list', $html_list);
        
    $positions = JYAML::getPositions(); ///////////////    
    $this->assignRef('positions', $positions);
    
    /*** Load Configuration ***/
    $conf        = JYAML::parseConfigDesign($config);
    $conf_global = JYAML::parseConfigDesign($config_global, true);  
    $this->assignRef('conf', $conf);
    $this->assignRef('conf_global', $conf_global);  
    
    /*** Buttons ***/
    // Explore Buttons for Stylesheets
    $explore_buttons[0][0]['label'] = 'YAML EXPLORE CSS';
    $explore_buttons[0][0]['link'] = 'index3.php?option='.$option.'&controller=templateExplorer&task=view&ext=css&template_name='.$this->template_name.'&design='.$design;
    // Explore Buttons for Scripts
    $explore_buttons[1][0]['label'] = 'YAML EXPLORE SCRIPTS';
    $explore_buttons[1][0]['link'] = 'index3.php?option='.$option.'&controller=templateExplorer&task=view&ext=js&template_name='.$this->template_name.'&design='.$design;
    $this->assignRef('explore_buttons', $explore_buttons);

    parent::display($tpl);
  }
}
