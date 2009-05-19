<?php
/**
 * (JYAML) - "YAML Joomla! Template" - http://www.jyaml.de
 *
 * @version         $Id: jyaml.php 463 2008-07-23 20:11:29Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 463 $
 * @lastmodified    $Date: 2008-07-23 22:11:29 +0200 (Mi, 23. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

global $jyaml;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


// Initialisize jyaml - need here for better error handling
if ( !$mainframe->isAdmin() && is_dir(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'yaml') )
{
	require_once(JPATH_PLUGINS.DS.'system'.DS.'jyaml'.DS.'jyaml.helper.php');
	
	define('JYAML_PATH_REL', 'templates/'.$mainframe->getTemplate());
	define('JYAML_PATH_ABS', JPATH_THEMES.DS.$mainframe->getTemplate());
	
	// first call in fastmode for error handler is active
	$jyaml = new JYAML( $mainframe->getTemplate(), true);
}

class plgSystemJYAML extends JPlugin
{
  function plgSystemJYaml(&$subject, $config) 
  {
    global $mainframe;
    
    parent::__construct($subject, $config);
    
    // Load language files
    $this->loadLanguage('plg_system_'.$this->_name, JPATH_ADMINISTRATOR);
    
    // Temporary Template Switcher
    $r = JRequest::getVar('jyamlC', array());    
    if (isset($r['switch_template']) && $r['switch_template']!="") 
		{
      $mainframe->setTemplate($r['switch_template']);
    }

  }
  
  /**
   * Call all needed things to use YAML Joomla! Templates
  **/
	function onAfterRoute() // to can use global $jyaml in template html overwrites
  {
    global $mainframe, $jyaml; 
    
    $document =& JFactory::getDocument();
		$doctype = $document->getType();        
    // Return document is not type of html
		if ($doctype != 'html' ) { return; }

    // backand
    if( $mainframe->isAdmin() || !is_dir(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'yaml') ) 
		{      
      $option = JRequest::getVar('option', false);
			
			if ($option != 'com_jyaml') 
      {			       
        $db =& JFactory::getDBO();
        // Get the current frontend default template
        $query = ' SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0';
        $db->setQuery($query);
				
        define('JYAML_FRONTEND_TEMPLATE', $db->loadResult());
        
        if (!is_dir(JPATH_SITE.DS.'templates'.DS.JYAML_FRONTEND_TEMPLATE.DS.'yaml')) { return; }		
        
        require_once(JPATH_PLUGINS.DS.'system'.DS.$this->_name.DS.$this->_name.'.helper.php');
        
        // define template path
        define('JYAML_PATH_ABS', JPATH_SITE.DS.'templates'.DS.JYAML_FRONTEND_TEMPLATE);
              
        // Reset object without set the configuration
				$jyaml = new JYAML( false );
        // Load Template Plugins prefix admin.
				$jyaml->getPlugins(false, true); 
      }
  
      return;
    }
        
    // call jyaml object
    $jyaml = new JYAML( $mainframe->getTemplate() );
  }
  
  /**
   * Load Editor configuration and set HTML Head
  **/
  function onAfterDispatch()
  {
    global $mainframe, $jyaml;
		
    $document =& JFactory::getDocument();
		$doctype = $document->getType();        
    // Return document is not type of html
		if ($doctype != 'html' ) { return; }
    
    // do not load in backand
    if( !$mainframe->isAdmin() ) {      
      if (!$jyaml) return false;
      
      $jyaml->setEditorStyles();
      
      $jyaml->setHead();    
      $jyaml->setExtensionStylesheets();
    }
  }

  /**
   * M_Images Replacement to design folders, after render plugins, debuging
  **/
  function onAfterRender()
  {
    global $mainframe, $jyaml;
    
    $document =& JFactory::getDocument();
		$doctype = $document->getType();        
    // Return document is not type of html
		if ($doctype != 'html' ) { return; }
      
    // Do not load in backend and not by other templates
    if( $mainframe->isAdmin() || !is_dir(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'yaml') ) {  
      if (JRequest::getVar('option', false)!='com_jyaml') 
      {
        if (defined('JYAML_FRONTEND_TEMPLATE')) {
          if (!is_dir(JPATH_SITE.DS.'templates'.DS.JYAML_FRONTEND_TEMPLATE.DS.'yaml')) { return; }
          $jyaml->getPlugins(true, true); /* Load Template Plugins with class *_afterRender and prefix admin.* */
        }
      }
      return;
    }
    
    $jyaml->getPlugins(true); /* Load Template Plugins with class *_afterRender */
    
    // get the body text
    $body = JResponse::getBody();
    
    // M_Images replacement for each design    
    $body = $jyaml->replaceM_images($body);
        
    // Debug output
		$debug = $jyaml->config->debug;
		
    if ( $debug ) 
    {     
      $body = str_replace('<body>', '<body>'.$jyaml->viewDebug('befor'), $body);
      $body = str_replace('</body>', $jyaml->viewDebug('after').'</body>', $body);            
    }

    // Add Plugin logs into debug
    if ( $debug ) 
    {      
      if ( isset($jyaml->pgl_logs_onBefore) && $jyaml->pgl_logs_onBefore ) 
      {  
          $body = str_replace('<body>', '<body>'.implode('', $jyaml->pgl_logs_onBefore), $body);
      }      
      if ( isset($jyaml->pgl_logs_onAfter) &&  $jyaml->pgl_logs_onAfter) 
      {  
         $body = str_replace('</body>', implode('', $jyaml->pgl_logs_onAfter).'</body>', $body);
      }
    }
    
    // set new body text    
    JResponse::setBody($body);   
  }    
}
?>