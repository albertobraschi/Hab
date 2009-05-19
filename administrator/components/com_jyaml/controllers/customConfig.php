<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: customConfig.php 429 2008-07-04 16:12:06Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 429 $
 * @lastmodified    $Date: 2008-07-04 18:12:06 +0200 (Fr, 04. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();   

class hmyamlControllercustomConfig extends hmyamlController
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
    $document =& JFactory::getDocument();
    $document->addStyleSheet('templates/system/css/system.css');
    
    JRequest::setVar( 'view', 'hmyamlcustomconf' );
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
    $design = JRequest::getVar( 'design',  false, 'POST' );
    $filename = JRequest::getVar( 'filename',  false, 'POST' );
    
    $now = JHTML::_( 'date', 'now', JText::_('DATE_FORMAT_LC2') );  
    $now = str_replace('&bull;', '', $now);  

    /* XML File */
    $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
          ."<yaml>\n"
          ."  <name>".$design.".xml</name>\n"
          ."  <description>Design Template Settings</description>\n\n"
          ."  <lastModification>".$now."</lastModification>\n\n"
          ."  <config>\n";
          
    // Switch Design
    $switch_design = JRequest::getVar( 'switch_design',  false, 'POST' );
    if ($switch_design) 
    {
      $xml .= "    <design>".$switch_design."</design>\n\n";      
    }

    // HTML File
    $html_file = JRequest::getVar( 'html_file',  false, 'POST' );
    if ($html_file) 
    {
      $xml .= "    <html_file>".$html_file."</html_file>\n\n";      
    }
    
    // Layout filenames
    if ( $layout_value = JRequest::getVar('layout_1col') ) 
    {
        $xml .= "    <layout_1col>".$layout_value."</layout_1col>\n";  
    }
    if ( $layout_value = JRequest::getVar('layout_2col_1') ) 
    {
        $xml .= "    <layout_2col_1>".$layout_value."</layout_2col_1>\n";  
    }
    if ( $layout_value = JRequest::getVar('layout_2col_2') ) 
    {
        $xml .= "    <layout_2col_2>".$layout_value."</layout_2col_2>\n";  
    }
    if ( $layout_value = JRequest::getVar('layout_3col') ) 
    {
        $xml .= "    <layout_3col>".$layout_value."</layout_3col>\n";  
    }
    if ($layout_value) { $xml .= "\n"; }
    
    // Column Clearings  
    $col1_clear_all = JRequest::getVar( 'col1_clear_all', 0, 'POST' );
    $col2_clear_all = JRequest::getVar( 'col2_clear_all', 0, 'POST' );
    $col3_clear_all = JRequest::getVar( 'col3_clear_all', 0, 'POST' );
    $col1_clear = JRequest::getVar( 'col1_clear', array(), 'POST' );
    $col2_clear = JRequest::getVar( 'col2_clear', array(), 'POST' );
    $col3_clear = JRequest::getVar( 'col3_clear', array(), 'POST' );
    
    if ( $col1_clear_all ) 
    {
      $xml .= "    <col1_content clear=\"__all__\" />\n";    
    } 
    elseif ( $col1_clear ) 
    {
      foreach ( $col1_clear as $pos ) 
      {
        $xml .= "    <col1_content clear=\"".$pos."\" />\n";        
      }    
    }
    if ( $col2_clear_all ) 
    {
      $xml .= "    <col2_content clear=\"__all__\" />\n";    
    } 
    elseif ( $col2_clear ) 
    {
      foreach ( $col2_clear as $pos ) 
      {
        $xml .= "    <col2_content clear=\"".$pos."\" />\n";        
      }    
    }
    
    if ( $col3_clear_all ) 
    {
      $xml .= "    <col3_content clear=\"__all__\" />\n";    
    } 
    elseif ( $col3_clear ) 
    {
      foreach ( $col3_clear as $pos ) 
      {
        $xml .= "    <col3_content clear=\"".$pos."\" />\n";        
      }    
    }
    if ($col1_clear_all || $col2_clear_all || $col3_clear_all) $xml .= "\n";
          
    // Content Column Configuration
    $col1 = JYAML::validateCol('col1');      
    $col2 = JYAML::validateCol('col2');        
    $col3 = JYAML::validateCol('col3');
    
    // col1_content
    for($i=0; $i<count($col1['content_type']) ;$i++) 
    {
      $style = $col1['content_style'][$i] == '(empty_value)' ? '' : ' style="'.$col1['content_style'][$i].'"';
      $pos = $col1['content_pos'][$i] == '(empty_value)' ? '' : $col1['content_pos'][$i];
      $advanced = $col1['content_advanced'][$i] ? " advanced='".$col1['content_advanced'][$i]."'" : '';
      if ($pos) 
      {    
        $xml .= "    <col1_content type=\"".$col1['content_type'][$i]."\"".$style.$advanced.">".$col1['content_pos'][$i]."</col1_content>\n";
      } 
      else 
      {
        $xml .= "    <col1_content type=\"".$col1['content_type'][$i]."\"".$style." />\n";      
      }
    }
    if (count($col1['content_type'])) $xml .= "\n";
    
    // col2_content    
    for($i=0; $i<count($col2['content_type']) ;$i++) 
    {
      $style = $col2['content_style'][$i] == '(empty_value)' ? '' : ' style="'.$col2['content_style'][$i].'"';
      $pos = $col2['content_pos'][$i] == '(empty_value)' ? '' : $col2['content_pos'][$i];
      $advanced = $col2['content_advanced'][$i] ? " advanced='".$col2['content_advanced'][$i]."'" : '';
      if ($pos) 
      {
        $xml .= "    <col2_content type=\"".$col2['content_type'][$i]."\"".$style.$advanced.">".$col2['content_pos'][$i]."</col2_content>\n";
      } 
      else 
      {
        $xml .= "    <col2_content type=\"".$col2['content_type'][$i]."\"".$style." />\n";      
      }
    }  
    if (count($col2['content_type'])) $xml .= "\n";

    // col3_content    
    for($i=0; $i<count($col3['content_type']) ;$i++) 
    {
      $style = $col3['content_style'][$i] == '(empty_value)' ? '' : ' style="'.$col3['content_style'][$i].'"';
      $pos = $col3['content_pos'][$i] == '(empty_value)' ? '' : $col3['content_pos'][$i];
      $advanced = $col3['content_advanced'][$i] ? " advanced='".$col3['content_advanced'][$i]."'" : '';
      if ($pos) 
      {
        $xml .= "    <col3_content type=\"".$col3['content_type'][$i]."\"".$style.$advanced.">".$col3['content_pos'][$i]."</col3_content>\n";
      } 
      else 
      {
        $xml .= "    <col3_content type=\"".$col3['content_type'][$i]."\"".$style." />\n";      
      }
    }  
    if (count($col3['content_type'])) $xml .= "\n";
    
    // Stylesheets
    $stylesheets = JYAML::validateStylesheets();    
    foreach ($stylesheets as $stylesheet) 
    {
      $source  = false;
      $browser = false;
      $type    = false;
      $media   = false;
      
      if ( isset($stylesheet['file']) && $stylesheet['file'] ) 
      {
        if ( $stylesheet['source']  ) { $source  = ' source="'.$stylesheet['source'].'"'; }
        if ( $stylesheet['browser'] ) { $browser = ' browser="'.$stylesheet['browser'].'"'; }
        if ( $stylesheet['type']    ) { $type    = ' type="'.$stylesheet['type'].'"'; }
        if ( $stylesheet['media']   ) { $media   = ' media="'.$stylesheet['media'].'"'; }
        
        $xml .= "    <addStylesheet".$source."".$browser."".$type."".$media.">".$stylesheet['file']."</addStylesheet>\n";
      }  
    }
    if ($stylesheets) $xml .= "\n";
    
    // Scripts
    $scripts = JYAML::validateScripts();    
    foreach ($scripts as $script) 
    {
      $source  = false;
      $browser = false;
      $type    = false;
      
      if ( isset($script['file']) && $script['file'] ) 
      {
        if ( $script['source']  ) { $source  = ' source="'.$script['source'].'"'; }
        if ( $script['browser'] ) { $browser = ' browser="'.$script['browser'].'"'; }
        if ( $script['type']    ) { $type    = ' type="'.$script['type'].'"'; }
        
        $xml .= "    <addScript".$source."".$browser."".$type.">".$script['file']."</addScript>\n";
      }  
    }
    if ($scripts) $xml .= "\n";
    
    // Add Head
    $addhead = JRequest::getVar( 'addhead',  false, 'POST', '', JREQUEST_ALLOWRAW );
    if ( $addhead ) $xml .= "    <addHead><![CDATA[".$addhead."]]></addHead>\n\n";    

    // Custom site configuation
    $customs = JYAML::validateCustoms();
    if ($customs) 
    {
      $xml .= "    <custom>\n";    
      foreach ( $customs as $custom ) 
      {
        $xml .= "      <xmlconfig parts=\"".$custom['parts']."\" desc=\"".$custom['desc']."\" subitems=\"".$custom['subitems']."\" force=\"".$custom['force']."\">".$custom['file']."</xmlconfig>\n";
      }
      $xml .= "    </custom>\n\n";    
    }
    
    // Own Vars
    $ownVars = JRequest::getVar( 'ownVars',  array(), 'POST' );
    $i = 0;
    $vars = array();
    foreach ($ownVars as $var) 
    {
      if (isset($var['name'])) $vars[$i]['name'] = $var['name'];
      if (isset($var['value'])) $vars[$i-1]['value'] = $var['value'];      
      $i++;  
    }  
    foreach ($vars as $var) 
    {
      if ($var['name'] && isset($var['value'])) $xml .= "    <".$var['name'].">".$var['value']."</".$var['name'].">\n";  
    }
    if ($vars) $xml .= "\n";
            
    // Others  
    $debug = JRequest::getVar( 'debug',  false, 'POST' );
    if ( is_numeric($debug) ) { $xml .= "    <debug>".$debug."</debug>\n"; }
    
    // Plugins
    $plugins = JRequest::getVar('plugins', array(), 'POST');
    if ($plugins) 
    {
      $xml .= "    <plugins>\n";
      foreach ($plugins as $plugin=>$params) 
      {
        $xml .= "      <".$plugin.">\n";
        foreach ($params as $name=>$data) 
        {
          $xml .= "        <".$name.">".$data."</".$name.">\n";
        }
        $xml .= "      </".$plugin.">\n";
      }
      $xml .= "    </plugins>\n";
    }
            
    // Close xml Tags
    $xml .= "  </config>\n"
          ."</yaml>";  
          
    // Write file ([designname].xml)
    $file = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'config'.DS.$design.DS.$filename;
    if ( JFile::write( $file, $xml ) ) 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML SAVED SUCCESS' ) );  
      if ($apply)
      {
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=customConfig&task=edit&template_name='.$template_name.'&design='.$design.'&file='.$filename );
      }
      else 
      {
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');      
      }
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML SAVED FAILED' ), 'error' );  
      $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=customConfig&task=edit&template_name='.$template_name.'&design='.$design.'&file='.$filename );
    }
  }
  
}
?>