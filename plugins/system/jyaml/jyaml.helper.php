<?php
/**
 * (JYAML) - "YAML Joomla! Template" - http://www.jyaml.de
 *
 * @version         $Id: jyaml.helper.php 462 2008-07-22 19:28:29Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 462 $
 * @lastmodified    $Date: 2008-07-22 21:28:29 +0200 (Di, 22. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JYAML
{
  /** @var string */
  var $template = 'hm_yaml';
  
  /** @var boolean */
  var $col1_enabled = false;
  var $col2_enabled = false;
  
  /** @var int */
  var $col1Count = 0;
  var $col2Count = 0;
  var $col3Count = 0;
  
  /** @var boolean */
  var $user_browser = false;
  var $user_agent = false;
  var $user_platform = false;

  /** @var array */
  var $active_extensions = array();
  
  /** @var string */
  var $imagePath = '';
  var $scriptPath = '';
  var $cssPath = '';

  /** @var object */
  var $config = NULL;
  var $juri = NULL;
  
  /** @var array */
  var $_errors = array();
  
  /** @var array */
  var $_logs = array();

  /** @var object */
  var $document = NULL;

  /** @var array */
  var $_plugin_list = array();
  var $pgl_logs_onBefore = array();
  var $pgl_logs_onAfter = array();
  
  /**
   * Set configuration for YAML Template
   * @param template_name current template
  **/
  function JYAML($template_name, $fastmode=false)
  {
    if (!$template_name) return;
    
    // get document to modify and work with html head
    if (!$fastmode)	$this->document=& JFactory::getDocument();
   
    if (!$fastmode)	$this->juri = clone( JURI::getInstance() );
      
    $this->template = $template_name;
    
    $this->getCfg();
    
		if (!$fastmode)	$this->setRTLStylesheets();
    
    if (!$fastmode)	$this->setConfigReqeust();
    
    $this->getBrowser();  
    
    if (!$fastmode)	$this->initColumns();
    
    /* Set Design Path's */
    $this->imagePath  = JURI::base(true).'/'.JYAML_PATH_REL.'/images/' .$this->config->design;
    $this->scriptPath = JURI::base(true).'/'.JYAML_PATH_REL.'/scripts/'.$this->config->design; 
    $this->cssPath    = JURI::base(true).'/'.JYAML_PATH_REL.'/css/'    .$this->config->design; 
    
    if (!$fastmode)	$this->initLayout();
    
    if (!$fastmode)	$this->getPlugins();
    
    if (!$fastmode)	$this->debug();
    
    //$this->setHead();    
    //$this->setExtensionStylesheets();
  }
  
  function setEditorStyles() {    
    jimport( 'joomla.html.editor' );
    $conf =& JFactory::getConfig();
    $editor = $conf->getValue('config.editor');
    $instance =& JEditor::getInstance($editor);
    if ($instance->_editor) {
      $css = '/css/'.$this->config->design.'/extensions/editor_view.css';
      $this->addStylesheet(JYAML_PATH_REL.$css);
    }
  }
  
  
  /**
   * Search and insert Stylesheets for RTL direction
   * @return void
  **/
  function setRTLStylesheets()
  {
    $lang =& JFactory::getLanguage();
    
    if ($lang->isRTL())
    {
      $this->config->addStylesheets['/yaml/core/slim_base-rtl.css'] = array('type' => 'text/css', 'media' => 'all', 'browser' => '');
      
      // Serch for basemod.css and content.css in CSS-Design screen Folder if exists
      if ( JFile::exists(JYAML_PATH_ABS.'/css/'.$this->config->design.'/screen/basemod-rtl.css') )
      {
        $this->config->addStylesheets['/{DesignCssFolder}/screen/basemod-rtl.css'] = array('type' => 'text/css', 'media' => 'all', 'browser' => '');
      }
      if ( JFile::exists(JYAML_PATH_ABS.'/css/'.$this->config->design.'/screen/content-rtl.css') )
      {
        $this->config->addStylesheets['/{DesignCssFolder}/screen/content-rtl.css'] = array('type' => 'text/css', 'media' => 'all', 'browser' => '');
      }
    
      // Search RTL Stylesheets for set Stylesheets in Configuration
      if ($this->config->addStylesheets)
      {
        foreach ($this->config->addStylesheets as $stylesheet => $attribs)
        {
          $stylesheet = str_replace('{DesignCssFolder}', 'css/'.$this->config->css_folder, $stylesheet);
          /* Set name to [Name]-rtl[.css] */
          $stylesheet = str_replace('.css', '-rtl.css', $stylesheet);
          
          if ( JFile::exists(JYAML_PATH_ABS.$stylesheet) )
          {
            /* Add RTL Stylesheet */
            $this->config->addStylesheets[$stylesheet] = $attribs;
          }
        }
      }
    }
  }
  
  /**
   * Load required thing for debugging
   * @return viod
  **/
  function debug()
  {
    if ($this->config->debug) 
    {
      JHTML::_( 'behavior.mootools' );
    
      $css = '/yaml/debug/debug.css';
      $this->addStylesheet(JYAML_PATH_REL.$css);
      
      $css = '/yaml/debug/joomla_debug.css';
      $this->addStylesheet(JYAML_PATH_REL.$css);
      
      $script = '/scripts/ftod.js';
      $this->addScript(JYAML_PATH_REL.$script);
      $this->addScriptDeclaration('    /* JYAML Debug Script */ window.onload=function(){ AddFillerLink("col1_content","col2_content","col3_content"); }');
      
      $script = '/scripts/jquery.js';
      $this->addScript(JYAML_PATH_REL.$script);

      $script = '/scripts/jquery.ui/jquery.ui.js';
      $this->addScript(JYAML_PATH_REL.$script);
      
      $script = '/scripts/jquery.noConflict.js';
      $this->addScript(JYAML_PATH_REL.$script);
      
      $script = ('
        jQuery(document).ready(function(){
          jQuery("#system-debug").css( {opacity:.9} );
          jQuery("#view_grid").css( {opacity:.6} );
          
          jQuery(".trans25").css({opacity:.25});
          jQuery(".trans50").css({opacity:.5});
          jQuery(".trans75").css({opacity:.75});
          
          jQuery("#toogle_grid").click(function () {
            jQuery("#view_grid").appendTo("#page");
            jQuery("#view_grid").toggle();
          });
                    
          jQuery("#view_grid").draggable({
            zIndex: 90000
          });
          
          jQuery("#page_margins a").addClass("hasTip");
          jQuery("#page_margins a").each(function(){
            var tipTitle = jQuery(this).text()+" :: href: "+jQuery(this).attr("href")+" <br />title: "+jQuery(this).attr("title");
            var tipimgTitle = "";
            if (jQuery(">img", this).length) {
              tipimgTitle = " <br /><br />img-alt: "+jQuery(">img", this).attr("alt")+" <br />img-src: "+jQuery(">img", this).attr("src");
            }            
            jQuery(this).attr("title", tipTitle+tipimgTitle);
          });
          
          jQuery("#page_margins img").addClass("hasTip");
          jQuery("#page_margins img").each(function(){
            var tipTitle = "alt: "+jQuery(this).attr("alt")+" :: src: "+jQuery(this).attr("src");
            jQuery(this).attr("title", tipTitle);
          });
          
        });
        window.addEvent("domready", function(){ var JTooltips = new Tips($$(".hasTip"), { maxTitleChars:50, fixed: false}); });
      ');      
      $this->addScriptDeclaration( '    /* JYAML Debug Script */ '.$this->trimmer($script) );
    }
  }
  
  /**
   * Trim string
   * @return string
  **/
  function trimmer($content, $strip_tags=false) 
  {
    $content = str_replace(array("\n", "\r", "\t", "  ",), '', $content);
    
    //$c1 = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
    //$c2 = '//[^\n]*';
    //$c3 = '\#[^\n]*';

    //$s = "'[^'\\]*(?:\.[^'\\]*)*'";
    //$d = '"[^"\\]*(?:\.[^"\\]*)*"';
    //$o = '[^"\'/\#<]';

    //$eot = '<<<\s?(\S+)\b.*^\2';
    
    if ($strip_tags) $content = strip_tags($content);
                
    return $content;
  }
  
  /**
   * Get Request for modifications on configuration
   * @return overwrite $this->config->[xyz]
  **/
  function setConfigReqeust() 
  {
    $r = JRequest::getVar('jyamlC', false);    
    if ($r) foreach ($r as $k=>$v) { $this->config->$k = $this->trimmer($v, true); }
  }
  
  /**
   * Get Template Plugins
  **/
  function getPlugins($_afterRender=false, $adminPlugin=false) 
  {
    if (!$this->_plugin_list) 
    {
      $path = JYAML_PATH_ABS.DS.'plugins';
      $this->_plugin_list = JFolder::folders($path); 
    }
    
    $this->loadPlugins($_afterRender, $adminPlugin);    
  }
  
  /**
   * Load Template Plugins
  **/  
  function loadPlugins($_afterRender=false, $adminPlugin=false, $frontend_suffix=false) 
  {
  
    if (!$this->_plugin_list) return false;
    
    $path = JYAML_PATH_ABS.DS.'plugins';
    
    foreach ($this->_plugin_list as $plugin) 
    {
    
      if ( JFile::exists($path.DS.$plugin.DS.'config.xml') ) 
      {
        $paramString = JFile::read($path.DS.$plugin.DS.'config.xml');
        $params = '';
        $paramArray  = explode("\n", $paramString);
        $paramString = '';

        foreach ($paramArray as $param) 
        {
          $val = explode("=", $param);
          $name = isset($val[0]) ? $val[0] : '';
          $data = isset($val[1]) ? $val[1] : '';
          
          if ($name) 
          {
            if ( isset($this->config->plugins[$plugin][$name]) ) 
            {
              $params[$name] = $this->config->plugins[$plugin][$name];
            } 
            else 
            {
              $params[$name] = $data;              
              $this->config->plugins[$plugin][$name] = $data;
            }        
            
            $paramString .= $name."=".$params[$name]."\n";
          }
        }
        
        if ( $params['published'] > 0 ) 
        {        
          $yparams = new JParameter($paramString);
          
          $tmpClass = $plugin;
          $tmpClass_frontend = false;
          if ($adminPlugin) {
            $tmpClass = 'admin_'.str_replace('admin.', '', $plugin);
          } else {
            $tmpClass_frontend = 'frontend_'.str_replace('admin.', '', $plugin);
          }
          
          
          if ( !$_afterRender ) {
            include_once($path.DS.$plugin.DS.$plugin.'.php');
            
            if ( class_exists($tmpClass) ) $yaml_plugin = new $tmpClass($yparams, $this); 
            if($tmpClass_frontend && class_exists($tmpClass_frontend)) $yaml_plugin = new $tmpClass_frontend($yparams, $this);                       
          } else {
            $tmpClass = $tmpClass.'_afterRender';
            
            if ( class_exists($tmpClass) ) $yaml_plugin_afterRender = new $tmpClass($yparams, $this); 
            if($tmpClass_frontend && class_exists($tmpClass_frontend)) $yaml_plugin_afterRender = new $tmpClass_frontend($yparams, $this);       
          }
          
          if (isset($yaml_plugin->JYAMLc)) 
          {
            foreach ($yaml_plugin->JYAMLc as $key=>$value) 
            {
              $this->config->$key = $value;
            }
          }

          if (isset($yaml_plugin->JYAML)) 
          {          
            foreach ($yaml_plugin->JYAML as $key=>$value) 
            {
              $this->$key = $value;
            }
          }      
        }
      }
    }  
  }
  
  /**
   * Get template configuration
  **/   
  function getCfg()
  {     
    $this->config = new JYAMLconfig($this->template);
    
    if ($this->config->_errors) 
    {
      $this->_errors += $this->config->_errors;
      unset($this->config->_errors);
    }
  }
  

  /**
   * Set errors
   * @param type error type
   * @param error message
   * @return into array of error type
  **/    
  function setError( $type, $error ) 
  {
    $this->_errors[$type][] = $error;  
  } 
   
  /**
   * Get Positions for Columns
   * @param col column name
   * @return <jdoc:include ... /> string
  **/   
  function getContent( $col )
  { 
    $content = array();
    $preview = JRequest::getVar('jyaml-preview-positions', false);
    
    if ( JRequest::getVar('jyaml-view-cols', false) || $this->config->debug )
    {
      $content[] = '<div class="'.$col.'_preview">#'.$col.'</div>';
    }
    
    foreach (JYAMLconfig::getValue($col) as $value) {
      $style    = $value['style'] ? ' style="'.$value['style'].'"' : '';    
      $advanced = $value['advanced'] ? ' '.$value['advanced'] : ''; 
      
      if ($value['type'] == 'module-position' || $value['type'] == 'module') 
      {
        if ($value['type'] == 'module-position') $value['type'] = 'modules';
        $pos = '<jdoc:include type="'.$value['type'].'" name="'.$value['pos'].'"'.$style.$advanced.' />';
        
        if ($preview) {
          $pos_preview = '<div class="preview_positions">'.htmlentities($pos).'</div>';
          $content[] = $pos_preview;
        }
        
        $content[] = $pos;
      } 
      else 
      {
        $pos = '<jdoc:include type="'.$value['type'].'"'.$style.' />';
        
        if ($preview) {
          $pos_preview = '<div class="preview_positions">'.htmlentities($pos).'</div>';
          $content[] = $pos_preview;
        }        
        $content[] = $pos;
      }
    }
      
    if ($content) echo implode ("\r\n", $content);
  }
  
  /**
   * Get Positions
   * @param  type
   * @param  name
   * @param  style
   * @param  attribs
   * @doc    http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,templates:jdoc_statements/
   * @return <jdoc:include ... /> string
  **/
  function getPosition($type='modules', $name, $style='xhtml', $attribs='')
  {
    $preview = JRequest::getVar('jyaml-preview-positions', false);
    
    if ($type) $type = ' type="'.$type.'"';
    if ($name) $name = ' name="'.$name.'"';
    if ($style) $style = ' style="'.$style.'"';
    if ($attribs) $attribs = ' '.$attribs;
    
    $pos = "<jdoc:include$type$name$style$attribs />";
    if ($preview) $pos = '<span class="preview_positions">'.htmlentities($pos).'</span>';
    echo $pos;  
  }
    
  /**
   * Get current user Browser
  **/  
  function getBrowser()
  {
    jimport('joomla.environment.browser');
    $browser = new JBrowser;
    
    $this->user_browser   = $browser->getBrowser().' '.$browser->getVersion();
    $this->user_agent     = $browser->getAgentString();
    $this->user_platform   = $browser->getPlatform();
  }
  
  /**
   * Initialisize Columns for viewing
  **/   
  function initColumns() 
  {
    $this->col1Count = $this->countColumns('col1_content');
    $this->col2Count = $this->countColumns('col2_content');
    $this->col3Count = $this->countColumns('col3_content');  
  }
  
  /**
   * Initialisize Layout(-names) for viewing
  **/  
  function initLayout()
  {
        
    if ( $this->col1Count && $this->col2Count ) 
    {
      $this->config->layout = JYAMLconfig::getValue('layout_3col');            
    } 
    elseif ( $this->col1Count && !$this->col2Count ) 
    {    
      $this->config->layout = JYAMLconfig::getValue('layout_2col_1');             
    } 
    elseif ( !$this->col1Count && $this->col2Count ) 
    {    
      $this->config->layout = JYAMLconfig::getValue('layout_2col_2');     
    } 
    else 
    {
      $this->config->layout = JYAMLconfig::getValue('layout_1col');
    }
    
    $layout = explode('_', $this->config->layout);
  
    // Correction if not text in stylesheet name (without standard, left, right)
    if (!isset($layout[2])) $layout[2] = $layout[1];

    $this->col1_enabled = strpos($layout[2], '1')===false ? false : true;
    $this->col2_enabled = strpos($layout[2], '2')===false ? false : true;
  } 
  
  /**
   * Set Header for HTML Document
  **/ 
  function setHead($jyaml=false)
  {    
    // Add Sylesheets
    $css    = '/css/'.$this->config->css_folder.'/layout_'.$this->config->layout.'.css';
    $css_patch   = '/css/'.$this->config->css_folder.'/patches/patch_'.$this->config->layout.'.css';  
        
    $this->addStylesheet(JYAML_PATH_REL.$css);
    $this->addStylesheet(JYAML_PATH_REL.$css_patch, 'text/css', '', '', 'msie');
    
    /* Add Scripts */
    if ( isset($this->config->addScripts) ) 
    {
      foreach ( $this->config->addScripts as $filename => $attribs ) 
      {
        $filename = str_replace('{DesignScriptFolder}', $this->config->design, $filename);        
        $type = isset($attribs['type']) ? $attribs['type'] : 'text/javascript';
        $browser = isset($attribs['browser']) ? $attribs['browser'] : null;      
      
        if ( !$browser ) 
        {
          $this->addScript(JYAML_PATH_REL.$filename, $type);      
        } 
        else 
        {
          $this->addScript(JYAML_PATH_REL.$filename, $type, $browser);        
        }
      }
    }
    
    /* addtitional Config (XML) Stylesheets */
    if ( isset($this->config->addStylesheets) ) 
    {
      foreach ( $this->config->addStylesheets as $filename => $attribs ) 
      {
        $filename = str_replace('{DesignCssFolder}', 'css/'.$this->config->css_folder, $filename);        
        $type = isset($attribs['type']) ? $attribs['type'] : 'text/css';
        $media = isset($attribs['media']) ? $attribs['media'] : null;
        $browser = isset($attribs['browser']) ? $attribs['browser'] : null;
        
        if ( !$browser ) 
        {
          $this->addStylesheet(JYAML_PATH_REL.$filename, $type, $media);      
        } 
        else 
        {
          $this->addStylesheet(JYAML_PATH_REL.$filename, $type, $media, '', $browser);        
        }
      }
    }
    
    /* addtitional Config (XML) head string */    
    if ( isset($this->config->addhead) ) $this->document->addCustomTag( $this->config->addhead );  
  }
  
  /**
   * Add Extension Stylesheets if exists
  **/   
  function setExtensionStylesheets()
  {  
    $this->getAcitveExtensions();
       
    foreach ($this->active_extensions as $extension) 
    {
      $dPath = JYAML_PATH_ABS.DS.'css'.DS.$this->config->css_folder.DS.'extensions';
      
      if ( !JFile::exists($dPath.DS.$extension.'.css') ) 
      {      
        $css     = '/yaml/extensions/'.$extension.'.css';
      } 
      else 
      {
        $css     = '/css/'.$this->config->css_folder.'/extensions/'.$extension.'.css';    
      }
      
      if ( !JFile::exists($dPath.DS.$extension.'_patch.css') ) 
      {      
        $css_patch   = '/yaml/extensions/'.$extension.'_patch.css';
      } 
      else 
      {
        $css_patch   = '/css/'.$this->config->css_folder.'/extensions/'.$extension.'_patch.css';      
      }
      
      $this->addStylesheet(JYAML_PATH_REL.$css);
      $this->addStylesheet(JYAML_PATH_REL.$css_patch, 'text/css', '', '', 'msie');
    }
  }
  
  /**
   * Get active extensions for current site
  **/   
  function getAcitveExtensions()
  {
    array_unshift($this->active_extensions, JRequest::getVar('option', NULL));  
    
    $this->active_extensions = array_unique($this->active_extensions);
  }
  
  /**
   * Get all used/configured Positions
  **/  
  function getAllPositions() {
    $pos = array();
    
    if ($positions = $this->config->col1_content) 
    {
      foreach ($positions as $position) 
      {
        if ($position['pos']) $pos[] = $position['pos'];  
      }        
    }
    
    if ($positions = $this->config->col2_content) 
    {
      foreach ($positions as $position) 
      {
        if ($position['pos']) $pos[] = $position['pos'];    
      }        
    }
    
    if ($positions = $this->config->col3_content) 
    {
      foreach ($positions as $position) 
      {
        if ($position['pos']) $pos[] = $position['pos'];  
      }        
    }
    
    return $pos;  
  }
  
  /**
   * Count Positions for Columns
  **/   
  function countColumns ( $col )
  {  
    $count = 0;
    
    foreach (JYAMLconfig::getValue($col) as $value) 
    {
      if ($value['type'] == 'module-position') 
      {
        $modules = $this->getModule($value['pos']);  
        $count += count($modules);    
      }
      elseif ($value['type'] == 'module')
      {
        $modules = $this->getModule($value['pos'], 1);  
        $count += count($modules);  
      }  
      elseif ($value['type'] == 'component')
      {
        $count ++;  
      }
    }
    
    return $count;    
  }
  
  function getModule($pos='', $module=false) {
    $user  =& JFactory::getUser();
    $db    =& JFactory::getDBO();
    
    $aid  = $user->get('aid', 0);
    $modules  = array();
    $where = '';
    
    if ($pos) 
    {
      if ($module) {
        $where = " AND m.module = 'mod_$pos'";
      } else {
        $where = " AND m.position = '$pos'";      
      }
    }
    
    $query = 'SELECT position, module'
      . ' FROM #__modules AS m'
      . ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
      . ' WHERE m.published = 1'
      . ' AND m.access <= '. (int)$aid
      . ' AND m.client_id = 0'
      . $where
      . ' AND ( mm.menuid = '. (int) JRequest::getVar('Itemid', '0') .' OR mm.menuid = 0 )'
      . ' ORDER BY position, ordering';

    $db->setQuery( $query );
    $modules = $db->loadObjectList();
    
    // write to active extensions
    foreach ($modules as $module) {
      $this->active_extensions[] = $module->module;
    }
    
    return $modules;  
  }
  
  /**
   * Add Script for head in HTML document
   * @param url relative url to site
   * @param type content type
   * @param browser
   * @param contitional_comments
  **/   
  function addScript($url, $type='text/javascript', $browser = null, $contitional_comments = array() ) 
  {
    $type = $type ? $type : 'text/javascript';
    $browser = $browser ? $browser : null;
    $contitional_comments = $contitional_comments ? $contitional_comments : array();
    
    if ( !JFile::exists(JPATH_BASE.DS.str_replace('/', DS, $url)) ) 
    {
      $this->setError('Script', 'Not Found: '.$url);
      return; // break if file not exists
    }
    
    // set url path to relative docroot
    $url = JURI::base(true).'/'.$url;
    
    if ( !$browser ) 
    {
      $this->document->addScript($url, $type);
    } 
    elseif( strpos($this->user_browser, 'msie') && $contitional_comments ) 
    {
      // Not Implementet yet
      //echo 'import Stylesheet as Conditonal Comment'; 
    } 
    elseif ( strpos($this->user_browser, $browser) !==false ) 
    {
      $this->document->addScript( $url, $type );
    } 
    elseif (strpos($this->user_browser, $browser) ===false && $this->config->debug) 
    {
      // Insert as placeholder to sourcecode for information in debug mode
      $this->document->addCustomTag('<!-- JYAML Debug Output ('.$browser.'): <script type="'.$type.'" src="'.$url.'"></script> -->');  
    }  
    
  }
  
  /**
   * Add Script Declaration for head in HTML document
   * @param script content
   * @param browser
   * @param contitional_comments
  **/  
  function addScriptDeclaration( $script, $browser = null, $contitional_comments = array() ) 
  {
    $browser = $browser ? $browser : null;
    $contitional_comments = $contitional_comments ? $contitional_comments : array();
    
    if ( !$browser ) 
    {
      $this->document->addScriptDeclaration( $script );
    } 
    elseif( strpos($this->user_browser, 'msie') && $contitional_comments ) 
    {
      // Not Implementet yet
      //echo 'import Stylesheet as Conditonal Comment';  
    } 
    elseif ( strpos($this->user_browser, $browser) !==false ) 
    {
      $this->document->addScriptDeclaration( $script );
    } 
    elseif (strpos($this->user_browser, $browser) ===false && $this->config->debug) 
    {
      // Insert as placeholder to sourcecode for information in debug mode
      $this->document->addCustomTag('<!-- JYAML Debug Output ('.$browser.'): <script> '.$style.' </script> -->');    
    }    
  }

  /**
   * Add Stylesheets for head in HTML document
   * @param url relative url to site
   * @param type content type
   * @param media
   * @param attribs
   * @param browser
   * @param contitional_comments
  **/  
  function addStyleSheet( $url, $type = 'text/css', $media = null, $attribs = array(), $browser = null, $contitional_comments = array() )
  {  
    // init attribs
    $type = $type ? $type : 'text/css';
    $media = $media ? $media : null;
    $attribs = $attribs ? $attribs : array();
    $browser = $browser ? $browser : null;
    $contitional_comments = $contitional_comments ? $contitional_comments : array();
    
    // not implemented yet
    // $agent = null, $platform = null,
    
    if ( !JFile::exists(JPATH_BASE.DS.str_replace('/', DS, $url)) ) 
    {
      if (strpos($url, '/extensions/')===false) 
      { // Ignore Extension Stylesheets
        $this->setError('Stylesheet', 'Not Found: '.$url);
      }
      return; // break if file not exists
    }
    
    // set url path to relative docroot
    $url = JURI::base(true).'/'.$url;
    
    if ( !$browser ) 
    {
      $this->document->addStyleSheet( $url, $type, $media, $attribs );
    } 
    elseif( strpos($this->user_browser, 'msie') && $contitional_comments ) 
    {
      // Not Implementet yet
      //echo 'import Stylesheet as Conditonal Comment';  
    } 
    elseif ( strpos($this->user_browser, $browser) !==false ) 
    {
      $this->document->addStyleSheet( $url, $type, $media, $attribs );
    } 
    elseif (strpos($this->user_browser, $browser) ===false && $this->config->debug) 
    {
      // Insert as placeholder to sourcecode for information in debug mode
      $this->document->addCustomTag('<!-- JYAML Debug Output ('.$browser.'): <link rel="stylesheet" href="'.$url.'" type="text/css" /> -->');    
    }
  }
  
  /**
   * Add Stylesheet Declaration for head in HTML document
   * @param script content
   * @param browser
   * @param contitional_comments
  **/  
  function addStyleDeclaration( $style, $browser = null, $contitional_comments = array() ) 
  {
    $browser = $browser ? $browser : null;
    $contitional_comments = $contitional_comments ? $contitional_comments : array();
    
    if ( !$browser ) 
    {
      $this->document->addStyleDeclaration( $style );
    } 
    elseif( strpos($this->user_browser, 'msie') && $contitional_comments ) 
    {
      // Not Implementet yet
      //echo 'import Stylesheet as Conditonal Comment';  
    } 
    elseif ( strpos($this->user_browser, $browser) !==false ) 
    {
      $this->document->addStyleDeclaration( $style );
    } 
    elseif (strpos($this->user_browser, $browser) ===false && $this->config->debug) 
    {
      // Insert as placeholder to sourcecode for information in debug mode
      $this->document->addCustomTag('<!-- JYAML Debug Output ('.$browser.'): <style> '.$style.' </style> -->');    
    }    
  }
  
  /**
   * View debug Mode
   * @param display before or after site
  **/ 
  function viewDebug( $display='after' ) 
  {
    if ($display=='befor') 
    {    
      ob_start();
      
      $this->juri->setVar('jyaml-preview-positions', '1');
      $this->juri->setVar('tp', '1');
      $enable_preview = $this->ampReplace( $this->juri->toString() );
      
      $this->juri->delVar('jyaml-preview-positions');
      $this->juri->delVar('tp');
      $disable_preview = $this->ampReplace( $this->juri->toString() );  
      
      ?>
      <a id="yaml_debug_top" name="yaml_debug_top"></a>
      <div id="view_grid" class="bg_grid"></div>
      <div class="trans75 yaml-debug" style="text-align:left; background:#000; border:1px solid #ccc; margin:1em; padding:1em; color:#fff;">
         <h1><?php echo JText::_( 'YAML DEBUG TITLE' ); ?></h1>
         <p><?php echo JText::_( 'YAML DEBUG INFO' ); ?></p>
         <a href="#yaml_debug_object"><?php echo JText::_( 'YAML DEBUG SKIP OBJECT' ); ?></a><br /><br />
         <a href="#" id="toogle_grid">Toggle Grid</a> | 
         <a href="<?php echo $enable_preview; ?>"><?php echo JText::_( 'YAML DEBUG PREVIEW ON' ); ?></a> |
         <a href="<?php echo $disable_preview; ?>"><?php echo JText::_( 'YAML DEBUG PREVIEW OFF' ); ?></a>              
      </div>
      <?php
          
      return ob_get_clean();    
    } 
    else 
    {    
      $lang_to_top = JText::_( 'YAML DEBUG SKIP TOP' );  
      
      ob_start();
      ?>
      <a id="yaml_debug_object" name="yaml_debug_object"></a>
      <pre class="trans75 yaml-debug" style="text-align:left; background:#000; border:1px solid #ccc; margin:1em; padding:1em; color:#fff; white-space:pre;"><a href="#yaml_debug_top"><?php echo $lang_to_top; ?></a>
      
    <?php
      unset($this->config->_menus);
      unset($this->juri);
      unset($this->document);
      //unset($this->pgl_logs_onBefore);
      //unset($this->pgl_logs_onAfter);
            
      $debug_output = $this;
      
      print_r( $debug_output ); 
      ?>
      </pre>
      <?php
          
      return ob_get_clean();
    }
  }
  
  /**
   * Entity replacement function
   * @param text
   * @return replaced text
  **/ 
  function ampReplace( $text )
  {
    $text = str_replace( '&&', '*--*', $text );
    $text = str_replace( '&#', '*-*', $text );
    $text = str_replace( '&amp;', '&', $text );
    $text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
    $text = str_replace( '*-*', '&#', $text );
    $text = str_replace( '*--*', '&&', $text );

    return $text;
  }
  
  /**
   * Replace Joomla! M_images standard Icons from main images folder to design image folder
   * @param body HTML body content
   * @return replaced image path for M_images
  **/  
  function replaceM_images( $body )
  {
    // find M_images (joomla default icons) in input and img elements
    preg_match_all('/(<img|<input).*?src="(.+?)"(.*?)>/is', $body, $matches);
    /* with input preg_match_all('/<img|<input.*?src="(.+?)"(.*?)>/is', $body, $matches); */
  
    // reduce dublicates
    $images = array_unique($matches[2]);
    
    $old_img_path = 'images/M_images/';
    $new_img_path = JYAML_PATH_REL.'/images/'.$this->config->design.'/M_images/';
  
    foreach($images as $image)
    {
      if ( strpos($image, $old_img_path)===false ) continue;
    
      // Get Path withourt URL if in path
      $uri = new JURI( $image );
      $image = $uri->getPath();

      // absolute path to check if file exists
      if ( strpos($image, JURI::base(true).'/'.$old_img_path)!==false )
      {
        $check_file = $new_img_path.str_replace(JURI::base(true).'/'.$old_img_path, '', $image);
      } 
      else
      {
        $check_file = $new_img_path.str_replace($old_img_path, '', $image);      
      }
      $check_file = JPATH_SITE.DS.str_replace('/', DS, $check_file);
       
      // check image replacement exists in design/image/M_images/ folder
      if ( JFile::exists($check_file) ) {
        $this->_logs[] = 'M_Image Replaced: '.$old_img_path.basename($image)." \n\t\t\t\t\ - To: ".$new_img_path.basename($image);
        $body = str_replace($old_img_path.basename($image), $new_img_path.basename($image), $body); 
      }
    }
    return $body;
  }

}


class JYAMLconfig 
{
  var $design = 'default';
  var $html_file = null;
  var $css_folder = null;
  var $img_folder = null;
  
  var $col1_content = array ();          
  var $col2_content = array ();          
  var $col3_content = array ();
            
  var $layout_1col = '1col_3';
  var $layout_2col_1 = '2col_13';
  var $layout_2col_2 = '2col_23';
  var $layout_3col = '3col_132';
  
  var $global_xml = '_global.xml'; 
  var $design_xml = null; 
  var $custom_xml = null; 
  var $addStylesheets = array();
  var $addScripts = array();
  var $addHead = null;
  
  var $plugins = array();
  
  var $currentItemID = NULL;
  var $parentItemIDs = array();
  
  var $url_parts = array();
  
  var $debug = false; 
  
  /** @var array */
  var $_logs = array();
   /** @var array */ 
  var $_errors = array();
  
  var $_menus = NULL;

  /**
   * Configuration Object for YAML Template
   * @param template current template
   * @return config object
  **/  
  function JYAMLconfig($template)
  {  
    $this->currentItemID = JRequest::getVar('Itemid', '1');  
    $this->_menus = JSite::getMenu();
    $this->getParentItemIDs($this->_menus, $this->currentItemID);
    
    // Load global settings
    $xmlfile = JPATH_BASE.DS.'templates'.DS.$template.DS.'config'.DS.$this->global_xml;
    $this->readConfig($template, $xmlfile);
    
    // Overwrite with design settings and check request changeing design
    if ( $design_xml = JRequest::getVar( 'design_xml' ) ) 
    {
      $this->design_xml = $design_xml.'.xml';
    } 
    else 
    {
      $this->design_xml = $this->design.'.xml';    
    }
    
    $xmlfile = JPATH_BASE.DS.'templates'.DS.$template.DS.'config'.DS.$this->design_xml;    
    if ( JFile::exists($xmlfile) ) 
    {
      $this->readConfig($template, $xmlfile);
    }
    
    $this->html_file = $this->html_file ? $this->html_file : 'index';
    
    $this->css_folder = $this->design;
    $this->img_folder = $this->design;
  }
  
  /**
   * Read Template Configuration
   * @param template current template
   * @param xmlfile configuration file
   * @return parsed configuration
  **/    
  function readConfig($template, $xmlfile) 
  {    
    $xmldoc =& JFactory::getXMLParser( 'Simple' );
    
    $xmldoc->loadfile( $xmlfile );
    
    $xmlconfig = $xmldoc->document->config[0];    
    if ( $xmlconfig ) 
    {
      foreach( $xmlconfig->children() as $child ) 
      {
        $this->readXML($template, $child);
      }
    }      
    return $this;  
  }
  
  /**
   * Read Template XML Configuration files
   * @param template current template
   * @param child
   * @param custom for custom configuration files
   * @return xml to array
  **/   
  function readXML($template, $child, $custom=true) 
  {
    $attribs = $child->attributes();
    $name = $child->name();
    
    if ( $child->name() == 'col1_content' ||
       $child->name() == 'col2_content' || 
       $child->name() == 'col3_content' ) 
    {
    
      if ( isset($attribs['clear']) ) 
      {      
        if ($attribs['clear'] == '__all__') 
        {
          $this->$name = array();  
        } 
        else 
        {
          $col_temp = $this->$name;
          
          foreach ( $this->$name as $key=>$value ) 
          {          
            if ( strpos($attribs['clear'], 'main::')!==false )
            {
              if ( substr( strrchr($attribs['clear'], "::"), 1 ) == $value['type'] ) unset($col_temp[$key]);
            }
            else
            {
              if ( substr( strrchr($attribs['clear'], "::"), 1 ) == $value['pos'] ) unset($col_temp[$key]);
            }
          }
          
          $this->$name = $col_temp;      
        }    
      } 
      else 
      { 
        $advanced = isset($attribs['advanced']) ? $attribs['advanced'] : '';
        $style = isset($attribs['style']) ? $attribs['style'] : '';
        $type = isset($attribs['type']) ? $attribs['type'] : '';
        array_push( $this->$name, array('pos'=>$child->data(), 'style'=>$style, 'type'=>$type, 'advanced'=>$advanced) );      
      }    
    } 
    elseif ( $child->name() == 'addstylesheet' ) 
    {
      if (isset($attribs['source']) && $attribs['source']=='design') 
      {
        $stylesheetname = '/{DesignCssFolder}/'.$child->data();
      } 
      else 
      {
        $stylesheetname = '/yaml/'.$child->data();
      }
      
      $this->addStylesheets[$stylesheetname] = array();
        
      $this->addStylesheets[$stylesheetname]['type']     = isset($attribs['type']) ? $attribs['type'] : '';
      $this->addStylesheets[$stylesheetname]['media']   = isset($attribs['media']) ? $attribs['media'] : '';
      $this->addStylesheets[$stylesheetname]['browser'] = isset($attribs['browser']) ? $attribs['browser'] : '';
    } 
    elseif ( $child->name() == 'addscript' ) 
    {
      if (isset($attribs['source']) && $attribs['source']=='design') 
      {
        $scriptname = '/scripts/{DesignScriptFolder}/'.$child->data();
      } 
      else 
      {
        $scriptname = '/scripts/'.$child->data();
      }
      $this->addScripts[$scriptname]['type']    = isset($attribs['type']) ? $attribs['type'] : '';
      $this->addScripts[$scriptname]['browser'] = isset($attribs['browser']) ? $attribs['browser'] : '';
      
    } 
    elseif ($child->name() == 'plugins') 
    {
      foreach ( $child->children() as $value ) 
      {
        $plugin = $value->name();
        foreach ($value->children() as $config) 
        {
          $name = $config->name();
          $data = $config->data();
          $this->plugins[$plugin][$name] = $data;
        }
      }
    } 
    elseif ( !$child->children() ) 
    {
      $this->$name = $child->data();  
    } 
    elseif ($child->name() == 'custom' && $custom) 
    {
      $isCustom = $this->getCustomXML($child, $attribs);
      if ($isCustom) $this->setCustomConfig($template);
    }  
  }
  
  /**
   * Get a config value
   * @param value
   * @return config value
  **/  
  function getValue( $value )
  {
    return $this->config->$value;
  }
  
  function getParentItemIDs($menus, $start=0, $pIds=array() ) 
  {
    $items = $menus->getItems('id', $start);

    if ($items) 
    {
      foreach ($items as $item) 
      {
        if ($item->parent) 
        {
          $pIds[] = $item->parent;
          $this->getParentItemIDs($menus, $item->parent, $pIds);
        } 
        else 
        {
          $this->parentItemIDs = $pIds; 
        }
      }
    }
  }
  
  /**
   * Serach Custom configuration files
   * @param child
   * @param attribs
   * @return true or false
  **/   
  function getCustomXML($child, $attribs) 
  {    
    // Parse get request (sef, joomla1.5 sef, non-sef)
    foreach ( JRequest::get('GET') as $key=>$value ) 
    {
      // delete subvalue (:)
      // and ignore array values
      if (!is_array($value) && $pos = strpos($value, ":") !== false) 
      {
        $tmp_val = explode(':', $value);
        $value = $tmp_val[0]; 
      }
      $this->url_parts[] = $key.'='.$value;
    }  
    
    $compare_xml = '';
    $score = 0;  
    
    foreach ( $child->children() as $value ) 
    {
      if ( $value->name() == 'xmlconfig' && $attribs = $value->attributes()) 
      {        
        $parts = explode( ',', $attribs['parts'] );        
        $compare = array_intersect($this->url_parts, $parts);
        
        if ( count($compare)>= count($parts)) 
        {
          $score=count($compare);        
          
          $compare_xml[$score]['file']   = $value->data();
          $compare_xml[$score]['desc']   = isset($attribs['desc']) ? $attribs['desc'] : '';    
          $compare_xml[$score]['parts']  = isset($attribs['parts']) ? $attribs['parts'] : '';      
          $compare_xml[$score]['childs'] = isset($attribs['childs']) ? $attribs['childs'] : '';
          
          // If force configuration, once array/scoring with clearing compare_xml and stop foreach
          if (isset($attribs['force']) && $attribs['force']=='1') {
            $xml_tmp = $compare_xml;
            unset($compare_xml);
            $compare_xml[$score] = $xml_tmp[$score];
            break;
          } 
        }
        
        if (isset($attribs['subitems']) && $attribs['subitems'] && $this->parentItemIDs)
        {
          foreach($parts as $part)
          {
            $key = explode('=', $part);
            if ($key[0]=='Itemid' && in_array($key[1], $this->parentItemIDs))
            {
               $this->custom_xml = $value->data();
               return true;
            }
          }
        }
        
      }
    }
    
    if ($compare_xml && $score) 
    {
      ksort($compare_xml);
      $custom_xml = array_pop($compare_xml);      
      $this->custom_xml = $custom_xml['file'];
      
      return true;
    } 
    else 
    {
      return false;
    }
  }
  
  /**
   * Set Custom configuration
   * @param template current template
   * @return overwritten configuration
  **/   
  function setCustomConfig($template) 
  {  
    $xmlfile = JPATH_BASE.DS.'templates'.DS.$template.DS.'config'.DS.$this->design.DS.$this->custom_xml.'.xml';
    
    if ( !JFile::exists($xmlfile) ) 
    {
      JYAML::setError('XML File', 'Not Found: '.DS.'config'.DS.$this->design.DS.$this->custom_xml.'.xml');
    }
    
    $xmldoc =& JFactory::getXMLParser( 'Simple' );    
    $xmldoc->loadfile( $xmlfile );
    
    $xmlconfig = $xmldoc->document->config[0];    
    if ( $xmlconfig ) 
    {  
      foreach( $xmlconfig->children() as $child ) 
      {
        $this->readXML($template, $child, false);
      }
    }
  }
}

?>