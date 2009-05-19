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
jimport('joomla.client.helper');

class hmyamlViewhmyaml extends JView
{
  /**
   * display method
   * @return void
   **/
  function display($tpl = null)
  {
    global $option, $mainframe;
    
    $html = '';
    
    $selected_template = NULL;    
    $templates = JYAML::getTemplates();
    
    $db =& JFactory::getDBO();
    // Get the current default template
    $query = ' SELECT template '
        .' FROM #__templates_menu '
        .' WHERE client_id = 0'
        .' AND menuid = 0 ';
    $db->setQuery($query);
    $defaultemplate = $db->loadResult();
    
    $switch_template = JRequest::getVar( 'switch_template', false, 'POST' );
    
    if ( $switch_template ) 
    {
      setcookie('switch_template', $switch_template, time()+600);
      $selected_template = $switch_template;
      $mainframe->enqueueMessage( JText::_( 'YAML SWITCH TEMPLATE MSG' ).': '.$selected_template );
      $mainframe->redirect( JURI::base() . 'index.php?option='.$option);  
      
    } elseif ( isset($_COOKIE['switch_template']) ) {
      $selected_template = $_COOKIE['switch_template'];
    }
    
    if ( !$selected_template ) 
    {
      $first = true;
      foreach ($templates as $template) 
      {
        if ($first) $selected_template = $template->name;  
        if ( $defaultemplate==$template->name ) 
        {
          $selected_template = $defaultemplate;  
        }      
        $first = false;
      }
    }
    
    /* Mask default template in list */
    $i = 0;
    $no_tpl_activ = true;
    foreach ($templates as $template) 
    {   
      if ( $defaultemplate==$template->name ) 
      {
        $templates[$i]->text = $template->name.' (default)';
        $no_tpl_activ = false;
      }
      $i++;     
    }
    $this->assignRef('no_tpl_activ',  $no_tpl_activ);

    if (!$selected_template) 
    {
        echo '<p class="yaml_msg">'.JText::_( 'YAML NO TEMPLATES FOUND' ).'</p>';
        $html .= '<form id="install_tpl_pgl" enctype="multipart/form-data" action="index.php" method="post" name="adminForm_tpl_install">';    
        $html .= '<input type="hidden" id="install_url" name="install_url" class="input_box" size="70" value="'.JYAML::getDownloadURL('template').'" />';
        $html .= '<p style="text-align:center;"><input type="submit" class="button" value="'.JText::_( 'YAML TEMPLATE INSTALL ACTIVATE DEFAULT' ).'" /></p>';
        $html .= '<input type="hidden" name="type" value="" />';
        $html .= '<input type="hidden" name="installtype" value="url" />';
        $html .= '<input type="hidden" name="task" value="installDefaultTemplate" />';
        $html .= '<input type="hidden" name="option" value="'.$option.'" />';
        $html .= JHTML::_( 'form.token' );
        $html .= '</form>';
        $html .= '<div id="installresult"></div>';
        echo $html;
        return false;
    }
    
    JToolBarHelper::title( JText::_('YAML COMPONENT TITLE').': <small>['.$selected_template.']</small>', 'yamlconfig' );
    
    $this->assignRef('selected_template', $selected_template);
    $this->assignRef('template_name', $selected_template);
    
    $lists['switch_template'] =JHTML::_( 'select.genericlist', $templates, 'switch_template', '', 'name', 'text', $selected_template );
    
    // Global XML for referenz view
    $xmlfile       = JPATH_SITE.DS.'templates'.DS.$selected_template.DS.'config'.DS.'_global.xml';
    $config_global = JYAML::readConfig($selected_template, $xmlfile);
    $conf_global = JYAML::parseConfigDesign($config_global);
    $this->assignRef('conf_global', $conf_global);

    $designlist = JYAML::getDesignList();
    $this->assignRef('designlist', $designlist);  
    
    $this->assignRef('lists',  $lists);
    
    $bar = & JToolBar::getInstance('toolbar');
    $bar->appendButton( 'Popup', 'upload', 'YAML IMPORT DESIGN', 'index3.php?option='.$option.'&controller=fileControl&task=importDesign&template='.$selected_template );
    $bar->appendButton( 'Popup', 'config', 'YAML DEFAULT CONF TXT', 'index3.php?option='.$option.'&controller=defaultConfig&task=edit&template_name='.$selected_template );

    $template_plugins = JYAML::getPlugins($selected_template);
    $this->assignRef('template_plugins', $template_plugins);
    
    JYAML::getPluginStatus();
    
    // FTP-Mode    
    $ftp = !JClientHelper::hasCredentials('ftp');
    $this->assignRef('require_ftp_login', $ftp);
    
    parent::display($tpl);
  }  
}
?>