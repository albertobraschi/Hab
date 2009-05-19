<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: helper.php 464 2008-07-23 20:21:12Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 464 $
 * @lastmodified    $Date: 2008-07-23 22:21:12 +0200 (Mi, 23. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Component Helper Classes 
**/
class JYAML
{
  /**
   * Get a template list
   * @return $templates
  **/
  function getTemplates()
  {
    $templates = array();
    if ($handle = opendir(JPATH_SITE.DS.'templates')) {
       $i=0;
       while (false !== ($file = readdir($handle))) {
         if ($file != "." && $file != ".." && $file != "css" && $file != "404.php" && $file != "index.html") {
           if ( is_dir(JPATH_SITE.DS.'templates'.DS.$file.DS.'yaml') ) {
             $templates[$i]->name = $file;
             $templates[$i]->text = $file;
             $i++;
           }
         }
       }
       closedir($handle);
    }    
    return $templates;
  }
  
  function activateTemplate($template, $redirect=true)
  {
    global $mainframe, $option;
    
    if ($template)
    {
      // Activate Template as default  
      $db  =& JFactory::getDBO();    
      $query = "UPDATE #__templates_menu SET template='".$template."' WHERE client_id=0 AND menuid=0";
      $db->setQuery( $query );
      if (!$result = $db->query()) echo $db->stderr();
    }
    
    if ($redirect) $mainframe->redirect( 'index.php?option='.$option );
  }
  
  /**
   * Get Configuration
   * @return $config
  **/
  function readConfig($template, $xmlfile, $custom=false) 
  {    
    $config = array();    
    $xmldoc =& JFactory::getXMLParser( 'Simple' );    
    $xmldoc->loadfile( $xmlfile );
    
		$xmlconfig = $xmldoc->document->config[0];    
    if ( $xmlconfig ) 
    {
      foreach( $xmlconfig->children() as $child ) 
      {
        $config[] = JYAML::readXML($template, $child, $custom);
      }
    } else {
      unset($xmldoc);
    } 
      
    return $config;  
  }
  
  /**
   * Read Configuration from XML
   * @return $config
  **/  
  function readXML($template, $child, $custom) {
    $config = array();
  
    $attribs = $child->attributes();
    $name = $child->name();
    $data = $child->data();
    
    if ( $child->name() == 'col1_content' ||
       $child->name() == 'col2_content' || 
       $child->name() == 'col3_content' ) 
    {  
      if ( isset($attribs['clear']) ) 
      {  
        $config[$name]['__clear'][] = $attribs['clear'];
      } 
      else 
      {    
        $style     = isset($attribs['style'])     ? $attribs['style']     : '';
        $type      = isset($attribs['type'])      ? $attribs['type']      : '';
        $advanced  = isset($attribs['advanced'])  ? $attribs['advanced']  : '';
        
        $config[$name][$data] = array(
                      'style' => $style,
                      'type' => $type,
                      'advanced' => $advanced
                      );
      }
      
    } 
    elseif ( $child->name() == 'addstylesheet' ) 
    {
      $stylesheetname = $child->data();
      $config['addstylesheet'][$stylesheetname] = array();        
      $config['addstylesheet'][$stylesheetname]['type']     = isset($attribs['type']) ? $attribs['type'] : '';
      $config['addstylesheet'][$stylesheetname]['media']   = isset($attribs['media']) ? $attribs['media'] : '';
      $config['addstylesheet'][$stylesheetname]['browser'] = isset($attribs['browser']) ? $attribs['browser'] : '';
      $config['addstylesheet'][$stylesheetname]['source'] = isset($attribs['source']) ? $attribs['source'] : '';
      
    } 
    elseif ( $child->name() == 'addscript' ) 
    {
      $scriptname = $child->data();
      $config['addscript'][$scriptname] = array();        
      $config['addscript'][$scriptname]['type']     = isset($attribs['type']) ? $attribs['type'] : '';
      $config['addscript'][$scriptname]['browser'] = isset($attribs['browser']) ? $attribs['browser'] : '';
      $config['addscript'][$scriptname]['source'] = isset($attribs['source']) ? $attribs['source'] : '';
          
    } 
    elseif( $child->name() == 'custom') 
    {
      foreach ( $child->children() as $value ) 
      {
        if ( $value->name() == 'xmlconfig' && $attribs = $value->attributes()) 
        {        
            $xmlfile = $value->data();
            $desc = isset($attribs['desc']) ? $attribs['desc'] : '';
            $subitems = isset($attribs['subitems']) ? $attribs['subitems'] : '0';
            $parts = isset($attribs['parts']) ? $attribs['parts'] : '';
            $force = isset($attribs['force']) ? $attribs['force'] : '0';
  
            $config['custom']['xmlconfig'][$parts] = array();
            $config['custom']['xmlconfig'][$parts]['file'] = $xmlfile;
            $config['custom']['xmlconfig'][$parts]['desc'] = $desc;
            $config['custom']['xmlconfig'][$parts]['subitems'] = $subitems;
            $config['custom']['xmlconfig'][$parts]['force'] = $force;
        }
      }
    } 
    elseif( $child->name() == 'plugins') 
    {
      foreach ( $child->children() as $plugin ) 
      {
        $pgl_name = $plugin->name();
        
        foreach ($plugin->children() as $params) 
        {
          $name = $params->name();
          $data = $params->data();
          $config['plugins'][$pgl_name][$name] = $data;
        }
        
      }
    } 
    else 
    {
      $config[$name] = $data;
    }

    return $config;
  }
  
  /**
   * Dummys for column positions
   * @return $this->getContentConfig as hidden
  **/
  function getPositionDummy($col, $positions) 
  {
    return JYAML::getContentConfig($col, '', $positions, '', $col);
  }
  
  /**
   * Get configuration for content columns
   * @return $config as html
  **/  
  function getContentConfig($name, $pos, $positions, $col, $dummy=false) 
  {
    global $option;
    
    $disabled = '';
    
    if ($dummy) $dummy = ' id="posDummy_'.$name.'"';
    
    $config = '<div class="yslider-sub posIDs"'.$dummy.'>';    
    $config .= '<div class="slide-title-sub">';
    
    $config .= '<a class="deletePosition">['.JText::_( 'YAML REMOVE POSITION' ).']</a>';    
    $config .= '<span class="yOrder">[ '; 
    $config .= '<a class="imagelink yOrderUp" href="javascript:return false;"><img src="components/'.$option.'/images/icons/order_up.png" alt="'.JText::_( 'YAML ORDER UP' ).'" /></a> '; 
    $config .= '<a class="imagelink yOrderDown" href="javascript:return false;"><img src="components/'.$option.'/images/icons/order_down.png" alt="'.JText::_( 'YAML ORDER DOWN' ).'" /></a> ] ';
    $config .= '</span>';
    
    $config .= '<span class="titleView">';
    $config .= '<span class="titleViewType">'.($dummy ? '<span class="off">'.JText::_( 'YAML SELECT POSITION' ).'</span>' : '').'</span>';
    $config .= '<span class="titleViewPos"></span>';
    $config .= '</span>';
        
    $config .= '</div>';    
    $config .= '<div class="ycontent-sub">'; 
    
    //// Type Start ////
    $config .= '<span class="posType">'.JText::_( 'YAML CONF POS TYPE' ).':</span>';
    $config .= '<select class="changeType" name="'.$name.'_content_type[]">';
    
    $type_vals = array(
                    'module-position' => 'Module Position',
                    'module' => 'Module',
                    'component' => 'Component',
                    'message' => 'Message'
                  );
                  
    $sel_type = '';
      
    if ($dummy) { $config .= '<option selected="selected" value="">-- Select --</option>'; }
    foreach ($type_vals as $key => $value) 
    {
      if (isset($col['type']) && $col['type']==$key) 
      {
        $selected = ' selected="selected"';
        $sel_type = $key;
        if (strpos($key, 'module') === false) 
        { 
          $disabled = ' disabled="disabled"'; 
        } 
        else 
        {
          $disabled = '';         
        }
      } 
      else 
      {
        $selected = '';      
      }
    
      $config .= '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
    }
    $config .= '</select>';
    //// Type End ////
    
    //// Pos Start ////
    $posName = JText::_( 'YAML CONF MODULPOSITION' );
    if ($sel_type=='module') $posName = 'Modul-Name';
    
    $config .= '&nbsp;&nbsp;&nbsp;<span class="posName">'.$posName.'</span>:';
    $config .= '<select'.$disabled.' class="changePositon typeValue" name="'.$name.'_content_pos[]">';
                  
    $modules = JFolder::folders(JPATH_SITE.DS.'modules', '^mod_');      
    // If Position not available add this.
    if (!in_array($pos, $positions) && !in_array('mod_'.$pos, $modules) && !$dummy) 
    {
      array_unshift($positions, $pos);
    }
    
    $posStyle = '';
    if ($sel_type=='module') $posStyle = ' style="display:none;"';
        
    if ($dummy) $config .= '<option selected="selected" value="">-- Select --</option>';
        
    foreach ($positions as $value) 
    {
      $selected = $pos==$value ? ' selected="selected"' : '';
      $config .= '<option'.$selected.$posStyle.' class="posPositions" value="'.$value.'">'.$value.'</option>';  
    }
    
    $posStyle = '';
    if ($sel_type=='module-position' || $dummy) $posStyle = ' style="display:none;"';
            
    // Add Module Names
    foreach ($modules as $module) 
    {
      $module_name = substr($module, 4, strlen($module));
      $selected = $pos==$module_name ? ' selected="selected"' : '';
      $config .= '<option'.$selected.$posStyle.' class="posModules" value="'.$module_name.'">'.$module_name.'</option>';      
    }    
    $config .= '<option value="(_TYPE_VALUE)">'.JText::_( 'YAML TYPE VALUE' ).'</option>';            
    $config .= '</select>';
    //// Pos End ////

    //// Style Start ////
    $config .= '&nbsp;&nbsp;&nbsp;<span class=" posStyle">'.JText::_( 'YAML CONF STYLE' ).'</span>:';
    $config .= '<select'.$disabled.' class="changeStyle typeValue" name="'.$name.'_content_style[]">';

    $stlye_vals = array(
                    'xhtml' => 'xhtml',
                    'none' => 'none',
                    'rounded' => 'rounded',
                    'table' => 'table'
                  );
                  
    // If Style not available add this.
    if (isset($col['style'])) 
    {
      if (!array_key_exists($col['style'], $stlye_vals) && !$dummy) 
      {
        $new_val = $col['style'];
        $stlye_vals = array($new_val=>$new_val) + $stlye_vals;
      }
    }
    
    // Serach and add modChrome's from current modules.php if exists
    $modChrome_file = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'html'.DS.'modules.php';
    if ( JFile::exists($modChrome_file) ) 
    {
      $modChrome = JFile::read($modChrome_file);
      
      $result = preg_match_all("/function modChrome_(.*)\(/Uis", $modChrome, $matches);
      
      if ($matches) 
      {
        $Chromes = array();
        foreach ($matches[1] as $Chrome) 
        {
          $Chromes[$Chrome] = '(modChrome) '.$Chrome;            
        }
        $stlye_vals = array_merge($stlye_vals, $Chromes);
      }
    }
    $stlye_vals = array_merge($stlye_vals, array('(_TYPE_VALUE)' => JText::_( 'YAML TYPE VALUE' )));
    
    if ($dummy) $config .= '<option selected="selected" value="">-- Select --</option>';
    foreach ($stlye_vals as $key => $value) {
      if (isset($col['style']) && $col['style']==$key) 
      {
        $selected = ' selected="selected"';
      } 
      else 
      {
        $selected = '';      
      }    
      $config .= '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
    }
    $config .= '</select>';
    //// Style End ////  
      
    //// Advanced Start ////
    $hide = '';
    if ($sel_type=='message' || $sel_type=='component') $hide = ' style="display:none"';
    
    $advanced = isset($col['advanced']) ? htmlentities($col['advanced']) : '';      
    $config .= '<div class="modChromeAdv"'.$hide.'>';
    $config .= '<br /><span class="hasTip" title="'.JText::_( 'YAML CONF ADVANCED DESC' ).'">'.JText::_( 'YAML CONF ADVANCED' ).'</span>';
    $config .= ': <input style="width:200px;" type="text" name="'.$name.'_content_advanced[]" value="'.$advanced.'" />';  
    $config .= '</div>';    
    //// Advanced End ////
    
    $config .= '</div>';    
    $config .= '</div>';
    
    return $config;
  }
  
  /**
   * Validate Column Config Request
   * @param colname
   * @return $col
  **/  
  function validateCol($colname) 
  {
    // Get values
    $col['content_type']     = JRequest::getVar( $colname.'_content_type',  array(), 'POST' );
    $col['content_pos']      = JRequest::getVar( $colname.'_content_pos',   array(), 'POST' );
    $col['content_style']    = JRequest::getVar( $colname.'_content_style', array(), 'POST' );
    $col['content_advanced'] = JRequest::getVar( $colname.'_content_advanced', array(), 'POST' );
     
    /* Validate cols */
    foreach($col['content_type'] as $key=>$type) 
    {
      if (strpos($type, 'module') === false) 
      {  
        if ( is_array($col['content_pos']) && is_array($col['content_style']) ) 
        {
          array_splice($col['content_pos'], $key, 0, '(empty_value)');
          array_splice($col['content_style'], $key, 0, '(empty_value)');
        }
      }      
    }
       
    return $col;
  }
  
  /**
   * Validate Stylesheet Request
   * @return $stylesheets
  **/   
  function validateStylesheets() 
  {
    $addstylesheets  = JRequest::getVar( 'addstylesheets',  array(), 'POST' );
    $stylesheets = array();
    
    if ($addstylesheets) 
    {
      for($i=0; $i<count($addstylesheets);) 
      {
        $stylesheets[$i]['file'] = $addstylesheets[$i]['file'];
        $stylesheets[$i]['source'] = $addstylesheets[$i+1]['source'];
        $stylesheets[$i]['browser'] = $addstylesheets[$i+2]['browser'];
        $stylesheets[$i]['type'] = $addstylesheets[$i+3]['type'];
        $stylesheets[$i]['media'] = $addstylesheets[$i+4]['media'];
        $i=$i+5;
      }
    }      
    return $stylesheets; 
  }
  
  /**
   * Validate Script Request
   * @return $scripts
  **/   
  function validateScripts() 
  {
    $addscripts  = JRequest::getVar( 'addscripts',  array(), 'POST' );  
    $scripts = array();

    if ($addscripts) 
    {
      for($i=0; $i<count($addscripts);) 
      {
        $scripts[$i]['file'] = $addscripts[$i]['file'];
        $scripts[$i]['source'] = $addscripts[$i+1]['source'];
        $scripts[$i]['browser'] = $addscripts[$i+2]['browser'];
        $scripts[$i]['type'] = $addscripts[$i+3]['type'];
        $i=$i+4;
      }
    }   

    return $scripts; 
  }
  
  /**
   * Validate Custom XML Request
   * @return $custom
  **/   
  function validateCustoms() 
  {
    $customs  = JRequest::getVar( 'custom',  array(), 'POST' );  
    $custom = array();
    
    if ($customs) 
    {
      for($i=0; $i<count($customs);) {
        $custom[$i]['parts'] = $customs[$i]['parts'];
        $custom[$i]['file'] = $customs[$i+1]['file'];
        $custom[$i]['desc'] = $customs[$i+2]['desc'];
        $custom[$i]['subitems'] = $customs[$i+3]['subitems'] ? 1 : 0;
        $custom[$i]['force'] = $customs[$i+4]['force'] ? 1 : 0;
        $i=$i+5;
      }
    }      
    return $custom; 
  }
  
  /**
   * Get a list of designs
   * @param  all
   * @return $designs
  **/  
  function getDesignList($all=false) 
  {
    $designs = array();
    $path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config';
    $files = JFolder::files($path, 'xml$', false, false, array('_global.xml'));
    
    if ($files) 
    {
      // Get design set in global configuration
      foreach($files as $file) 
      {
        $value = str_replace('.xml', '', $file);
        $design = isset($this->conf_global) ? $this->conf_global : false;
			  if ($design && $value == $design) 
				{
				  $designs[0] = new JYAMLmakeSelect($value, $value);
				}
      }
      
      // Get all other designs
      foreach($files as $file) 
      {
        $value = str_replace('.xml', '', $file);
        $design = isset($this->conf_global) ? $this->conf_global : false;
				if ($design) {
					if ( $value != $design) 
					{
					  $designs[] = new JYAMLmakeSelect($value, $value);
					}
        } else {
          $designs[] = new JYAMLmakeSelect($value, $value);
        }
      }
      
      // Check design index File exists
      foreach($designs as $key=>$design) 
      {
        $file = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'html'.DS.'index'.DS.$design->value.DS.'index.php';
        $thisDesign = isset($this->design) ? $this->design : '';
        
        if ( (!JFile::exists($file) || $design->value == $thisDesign) && !$all ) unset($designs[$key]);

        if ($design->value == $thisDesign  && !$all ) 
        {
          $no_switch[] = new JYAMLmakeSelect('', JText::_( 'YAML SWITCH DESIGN NOT' ) );
          $designs = array_merge( $no_switch, $designs );
        }
      }
    }    
    return $designs;  
  }
  
  /**
   * View of Custom XML Files
   * @param  designs
   * @return $html
  **/  
  function viewCustomXmlFiles($design) 
  {
    global $option;

    $xml_config_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design;
    $xml_config = JFolder::files($xml_config_path, 'xml$', false, false);
    
    $html = '';    
    $bar =& new JToolBar( 'My ToolBar' );
    $button =& $bar->loadButtonType( 'Custom' );
    
    $html .= '<ul>';        
    if ($xml_config) 
    {      
      foreach($xml_config as $file) 
      {      
        $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=customConfig&amp;task=edit&amp;template_name='.$this->template_name.'&amp;design='.$design.'&amp;file='.$file;  
        $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">\n";
        $link  .= "$design / <strong>$file</strong>\n";
        $link  .= "</a>\n";
        $deleteLink = $this->template_name.DS.'config'.DS.$design.DS.$file;      
        $html .= '<li>[ <a rel="'.$deleteLink.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] '.$button->fetchButton( 'Custom', $link, 'editfile' ).'</li>';      
      }
    } 
    else 
    {
      $html .= '<li>'.JText::_( 'YAML NO OTHERS AVALIABLE' ).'</li>';
    }    
    $html .= '</ul>';

    return $html;
  }
  
  /**
   * get Tree of CSS files
   * @param  designs
   * @return $filetree
  **/  
  function getCSSTree($design, $source=false) 
  {
    if ($source)
    {
      $rel_path = DS.'templates'.DS.$this->template_name.DS.'yaml';
    } 
    else 
    {
      $rel_path = DS.'templates'.DS.$this->template_name.DS.'css'.DS.($design ? $design : $this->design);    
    }
    $path = JPATH_SITE.$rel_path;

    $folders = JFolder::listFolderTree( $path.'/', '.', 1 );
    
    // Basic CSS Layout Files
    $filetree['basicfiles'] = JFolder::files($path, 'css$');    

    // Child CSS Files
    foreach ($folders as $folder) 
    {
      $folder_name = $folder['name'];      
      $filetree['folderfiles'][$folder_name.'/'] = JFolder::files($folder['fullname'], 'css$');    
    }
        
    return $filetree;
  }
  
  /**
   * View Tree of CSS files
   * @param  designs
   * @return $html
  **/  
  function viewCSSFiles($design, $safe=false, $source=false) 
  {  
    global $option;
    
    $filetree = JYAML::getCSSTree($design, $source);
    $html = ''; 
    
    $bar =& new JToolBar( 'My ToolBar' );
    $button =& $bar->loadButtonType( 'Custom' );    
    $popup =& $bar->loadButtonType( 'Popup' );
    
    $lockedFolders = array(
        '/',
        'screen/',
        'patches/',
        'print/',
        'extensions/',
        'navigation/' 
      ) ;    

    if (!$safe) $html .= '<ul class="css-file-tree filetree tree-disabled">';
    if ($safe)  $html .= '<ul class="tree-safe tree-safe-css filetree tree-disabled">';
    if ($source) {
      $html .= '<li><span class="folder">&nbsp;/'.$source.'/</span>';
    }
    else
    {
      $html .= '<li><span class="folder">&nbsp;/css/'.$design.'/</span>';    
    }
    $html .= '<ul>'; 
    
    
    $create_css_folder_link = 'index3.php?option='.$option.'&controller=fileControl&task=createCssFolder&template='.$this->template_name.'&design='.$design;
    $create_css_folder_button = $popup->fetchButton( 'Popup', 'create_css', 'YAML CREATE CSS FOLDER', $create_css_folder_link , 640, 350, 150, 150 );      
    if (!$safe) $html .= '<li><span class="folder folder_add">&nbsp;<span class="on">'.$create_css_folder_button.'</span></span></li>';    
    
    foreach ($filetree['folderfiles'] as $folder=>$files) 
    {  
      $deleteFolder = '';
      if ( !$safe && !$files && !in_array($folder, $lockedFolders) ) 
      {
        $deleteLink = $this->template_name.DS.'css'.DS.$design.DS.str_replace('/', DS, $folder);  
        $deleteFolder = ' [ <a folder="'.$deleteLink.'" class="MAINdeleteCssFolder">'.JText::_( 'YAML DELETE CSS FOLDER' ).'</a> ] - ';
      }
      $html .= '<li class="css-folder"><span class="folder">&nbsp;'.$deleteFolder.$folder.'</span>';  
      
      $create_css_link = 'index3.php?option='.$option.'&controller=fileControl&task=create&ext=css&folder='.$this->template_name.DS.'css'.DS.$design.DS.str_replace('/', DS, $folder).'&core_folder='.$this->template_name;
      $create_css_button = $popup->fetchButton( 'Popup', 'create_css', 'YAML CREATE FILE', $create_css_link , 640, 350, 150, 150 );  
      
      if ($files) 
      {
        $html .= '<ul>';
        foreach ($files as $file) 
        {
          $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=css&amp;file='.$this->template_name.DS.'css'.DS.$design.DS.str_replace('/', DS, $folder).$file;  

          $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">";
          $link  .= "$file";
          $link  .= "</a>\n";  
          $deleteLink = $this->template_name.DS.'css'.DS.$design.DS.str_replace('/', DS, $folder).$file;      
          if ($safe) 
          {
            if ($source) 
            {
              $sfolder = '/'.$source.'/';
              $sfile = $folder.$file;
            }
            else
            {
              $sfolder = '/css/'.$design.'/';
              $sfile = str_replace('\\', '/', $folder).$file;
            }
            $html .= '<li><span class="file"><a href="#" onclick="javascript:return false;" folder="'.$sfolder.'" file="'.$sfile.'" class="chooseFile">'.$file.'</a></span></li>';            
          }
          else
          {
            $html .= '<li><span class="file">[ <a rel="'.$deleteLink.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - '.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>';                      
          }
        }
        if (!$safe) $html .= '<li><span class="file">[<span class="on"> '.$create_css_button.' </span>]</span></li>';
        $html .= '</ul>';        
      } 
      else 
      {
        $html .= '<ul>';
        if (!$safe) $html .= '<li><span class="file">[<span class="on"> '.$create_css_button.' </span>]</span></li>';
        $html .= '<li><span class="file">'.JText::_( 'YAML NO FILES IN FOLDER' ).'</span></li>';          
        $html .= '</ul>';
      }
      $html .= '</li>';      
    }
    
    foreach ($filetree['basicfiles'] as $file) 
    { 
      $lockedFiles = array(
          'layout_1col_3.css',
          'layout_2col_13.css',
          'layout_2col_23.css',
          'layout_2col_31.css',
          'layout_2col_32.css',
          'layout_3col_123.css',
          'layout_3col_132.css',
          'layout_3col_213.css',
          'layout_3col_231.css',
          'layout_3col_312.css',
          'layout_3col_321.css'
        );
         
      $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=css&amp;file='.$this->template_name.DS.'css'.DS.$design.DS.$file;  
      $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">";
      $link  .= "$file";
      $link  .= "</a>\n"; 
      $delButton = '';
      if ( !$safe && !in_array($file, $lockedFiles) ) 
      {
        $delButton = '[ <a rel="'.$this->template_name.DS.'css'.DS.$design.DS.$file.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - ';
      } 
      if ($safe) 
      {
        if ($source) 
        {
          $sfolder = '/'.$source.'/';
        }
        else
        {
          $sfolder = '/css/'.$design.'/';
        }
        $html .= '<li><span class="file"><a href="#" onclick="javascript:return false;" folder="'.$sfolder.'" file="'.$file.'" class="chooseFile">'.$file.'</a></span></li>';            
      }
      else
      {
        $html .= '<li><span class="file">'.$delButton.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>';                  
      }
    }
    
    $create_css_link = 'index3.php?option='.$option.'&controller=fileControl&task=create&ext=css&folder='.$this->template_name.DS.'css'.DS.$design.'&core_folder='.$this->template_name;
    $create_css_button = $popup->fetchButton( 'Popup', 'create_css', 'YAML CREATE FILE', $create_css_link , 640, 350, 150, 150 );  
    if (!$safe) $html .= '<li><span class="file file_add">[<span class="on"> '.$create_css_button.' </span>]</span></li>';
      
    $html .= '</ul>';    
    $html .= '</li>'; 
    
    $html .= '</ul>';      
    
    return $html;
  }
  
  /**
   * Get a list of usend and unused extension templates
   * @param  rel_path
   * @return $extensions
  **/  
  function getExtHTMLFiles($rel_path) 
  {
    $modules = JFolder::folders(JPATH_SITE.DS.'modules', '^mod_');
    $components = JFolder::folders(JPATH_SITE.DS.'components', '^com_');    
    $av_exts = array_merge($modules, $components);  
    
    $tpl_html_path = JPATH_SITE.$rel_path;  
    
    $extensions = array();
    
    $mod_files['unused'] = array();
    $com_files['unused'] = array();
    $mod_files['used'] = array();
    $com_files['used'] = array();

    if ($av_exts)
    {
      foreach ($av_exts as $ext)
      {
        if (strpos($ext, 'com_')!==false) 
        {
          if (JFolder::exists(JPATH_SITE.DS.'components'.DS.$ext.DS.'views') )
          {          
            $views = JFolder::folders(JPATH_SITE.DS.'components'.DS.$ext.DS.'views');
            if ($views)
            {
              foreach ($views as $view)
              {                
                $files = JFolder::files(JPATH_SITE.DS.'components'.DS.$ext.DS.'views'.DS.$view.DS.'tmpl', 'php$', false, true);  
                if ($files) $com_files['unused'] = array_merge($com_files['unused'], $files);
              }  
            }                                
          }
        }
        if (strpos($ext, 'mod_')!==false) 
        {
          if (JFolder::exists(JPATH_SITE.DS.'modules'.DS.$ext.DS.'tmpl') )
          {
            $files = JFolder::files(JPATH_SITE.DS.'modules'.DS.$ext.DS.'tmpl', 'php$', false, true);  
            if ($files) $mod_files['unused'] = array_merge($mod_files['unused'], $files);        
          }
        }
      }
    }
    
    /* Extract used Templates */    
    $i=0;
    foreach ($com_files['unused'] as $file)
    {
      $file = str_replace(JPATH_SITE.DS.'components', $tpl_html_path, $file);
      $file = str_replace(DS.'views'.DS, DS, $file);
      $file = str_replace(DS.'tmpl'.DS, DS, $file);
      
      if ( JFile::exists($file) ) {
        $file = str_replace($tpl_html_path.DS, '', $file);
        $com_files['used'] = array_merge($com_files['used'], array($file));    
        unset($com_files['unused'][$i]);
      } 
      else 
      {
        $com_files['unused'][$i] = str_replace(JPATH_SITE.DS.'components'.DS, '', $com_files['unused'][$i]);
      }
       
      $i++;
    }
    
    $i=0;
    foreach ($mod_files['unused'] as $file)
    {
      $file = str_replace(JPATH_SITE.DS.'modules', $tpl_html_path, $file);
      $file = str_replace(DS.'tmpl'.DS, DS, $file);
      
      if ( JFile::exists($file) ) {
        $file = str_replace($tpl_html_path.DS, '', $file);
        $mod_files['used'] = array_merge($mod_files['used'], array($file));    
        unset($mod_files['unused'][$i]);
      } 
      else 
      {
        $mod_files['unused'][$i] = str_replace(JPATH_SITE.DS.'modules'.DS, '', $mod_files['unused'][$i]);
      }
       
      $i++;
    }    
    
    sort($com_files['unused']);
    sort($com_files['used']);
    sort($mod_files['unused']);
    sort($mod_files['used']);
    
    $extensions['com'] = $com_files;
    $extensions['mod'] = $mod_files;
            
    return $extensions;
  }
  
  /**
   * Generate tree of usend and unused extension templates
   * @param  files
   * @param  unused
   * @return $html
  **/  
  function extFileTree($files, $unused=0)
  {  
    global $option;
    
    $pathway = array();
    $html = '';
    $filesSrc = array();
    
    /* generate pathway */
    foreach ($files as $i=>$file) 
    {
      $fileSrc = '';
      if ($unused) 
      {
        $fileSrc = $file;
        if ( strpos($fileSrc, 'com_')===false )
        {
          $fileSrc = 'modules'.DS.$fileSrc;                    
        } 
        else
        {
          $fileSrc = 'components'.DS.$fileSrc;                    
        }
        $file = str_replace(DS.'views'.DS, DS, $file);
        $file = str_replace(DS.'tmpl'.DS, DS, $file);
      }
      
      $paths = explode(DS, $file);
      
      if ( count($paths)==3 )
      {
        $path_0 = $paths[0];
				$path_1 = $paths[1];
				$path_2 = $paths[2];
				
				$pathway[$path_0][$path_1][$i]['dest'] = $path_2;
        $pathway[$path_0][$path_1][$i]['src']  = $fileSrc;
      } 
      elseif ( count($paths)==2 ) 
      {
        $path_0 = $paths[0];
				$path_1 = $paths[1];
			
        $pathway[$path_0][$i]['dest']             = $path_1;
        if ($unused) 
				{
				  $pathway[$path_0][$i]['src'] = $fileSrc;
				}
      } 
    }
    
    if ($unused==1)
    {
      $html .= '<li id="unused_extensions"><span class="folder">&nbsp;'.JText::_('YAML EXTENSION TEMPLATES UNUSED').'</span>'."\n"; 
      $html .= '<ul>'."\n"; 
    }
    
    /* make list */
    foreach ($pathway as $f1=>$p1)
    {
      $html .= '<li><span class="folder">&nbsp;'.$f1.'</span>';
      
      if ( is_array($p1) ) {
        $html .= '<ul>';  
        foreach ($p1 as $f2=>$p2)
        {
          if ( is_numeric($f2) )
          {
  
            $dest = isset($p2['dest']) ? $p2['dest'] : '';
            $srcpath = isset($p2['src']) ? '&amp;srcfile='.$p2['src'] : '';
            $filepath = $this->template_name.DS.'html'.DS.$f1.DS.$dest;  
            $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=php&amp;file='.$filepath.$srcpath;  
            $editButton   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">$dest</a>";  
            if (!$unused) {
              $delButton = '[ <a rel="'.$filepath.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - ';
            $html .= '<li><span class="file">'.$delButton.$editButton.'</span>';  

            }              
            else 
            {
              $html .= '<li><span class="file hasTip" title="'.JText::_( 'YAML HTML EXTENSIONS UNUSED TIP' ).'">'.$editButton.'</span>';                
            }        
          }
          else
          {      
            $html .= '<li><span class="folder">&nbsp;'.$f2.'</span>';
          }
          
          if ( is_array($pathway[$f1][$f2]) && !isset($pathway[$f1][$f2]['dest']) ) {
            $html .= '<ul>';  
            foreach ($pathway[$f1][$f2] as $f3=>$p3)
            {
              if ( is_numeric($f3) )
              {
                $dest = $p3['dest'];
                $srcpath = '&amp;srcfile='.$p3['src'];
                $filepath = $this->template_name.DS.'html'.DS.$f1.DS.$f2.DS.$dest;  
                $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=php&amp;file='.$filepath.$srcpath;  
                $editButton   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">$dest</a>";  
                if (!$unused) {
                  $delButton = '[ <a rel="'.$filepath.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - ';
                  $html .= '<li><span class="file">'.$delButton.$editButton.'</span>';
                }  
                else 
                {
                  $html .= '<li><span class="file hasTip" title="'.JText::_( 'YAML HTML EXTENSIONS UNUSED TIP' ).'">'.$editButton.'</span>';                
                }                      
              }
              else
              {
                $html .= '<li><span class="folder">&nbsp;'.$f3.'</span>';
              }            
            
              $html .= '</li>';
            }
            $html .= '</ul>';    
          }
        
          $html .= '</li>';
        }
        $html .= '</ul>';    
      }
      $html .= '</li>';
    }
    
    if ($unused==2)
    {
      $html .= '</ul>'."\n"; 
      $html .= '</li>'."\n"; 
    }    
    
    return $html;
  }
  
  /**
   * View usend and unused extension templates
   * @return $html
  **/  
  function viewExtHTMLFiles() {  
    global $option;
    
    $rel_path = DS.'templates'.DS.$this->template_name.DS.'html';
    $ext_files = JYAML::getExtHTMLFiles($rel_path);
    
    $html = '<p>'.JText::_('YAML EXTENSION TEMPLATES DESC').'</p>';
    
    $bar    =& new JToolBar( 'My ToolBar' );
    $button =& $bar->loadButtonType( 'Custom' );    
    $popup  =& $bar->loadButtonType( 'Popup' );
    
    $html .= '<ul class="exthtml-file-tree filetree tree-disabled">'."\n";
    $html .= '<li><span class="folder">&nbsp;/html/</span>'."\n";

    
    $html .= '<ul>'."\n"; 
    $html .= JYAML::extFileTree($ext_files['com']['used'])."\n";
    $html .= JYAML::extFileTree($ext_files['mod']['used'])."\n";
        
    $files = JFolder::files(JPATH_SITE.$rel_path, 'php$');   
    if ($files)
    {
      foreach ($files as $file)
      {
        $filepath = $this->template_name.DS.'html'.DS.$file;  
        $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=html&amp;file='.$filepath;  
        $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">";
        $link  .= "$file";
        $link  .= "</a>";  
        $deleteLink = $filepath;   
        $html .= '<li><span class="file">'.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>'."\n";
      }  
    }
    $html .= '</ul>'."\n";     
    $html .= '</li>'."\n";
    
    $html .= JYAML::extFileTree($ext_files['com']['unused'], 1)."\n";
    $html .= JYAML::extFileTree($ext_files['mod']['unused'], 2)."\n";
    
    $html .= '</ul>'."\n";
    
    return $html;
  }
  
  /**
   * Get a list of template html as php files
   * @param  design
   * @return $files
  **/  
  function getHTMLList($design='') 
  {
    $path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'html'.DS.'index'.DS.($design ? $design :$this->design);      
    $excludes = array(
      'index.php', 'component.php', 'offline.php', 'error.php', 'contentPreview.php',  
      '400.php', '401.php', '402.php', '403.php', '404.php', '405.php', '406.php', '407.php', '408.php', '409.php', '410.php', '411.php', '412.php', '413.php', '414.php', '415.php', 
      '500.php', '501.php', '502.php', '503.php', '504.php', '505.php'
    );
    
    if ($design) 
    {
      $files = JFolder::files($path, 'php$', false, false , $excludes);
    } 
    else 
    {
      $files = JFolder::files($path, 'php$', false, false , $excludes);
    }
            
    return $files;
  }
  
  /**
   * View html as php files
   * @param  design
   * @return $html
  **/ 
  function viewHTMLFiles($design) 
  {
    global $option;
    
    $htmlfiles = JYAML::getHTMLList($design);
    $html = '';
    
    $noDelFiles = array('index.php', 'component.php', 'offline.php', '403.php', '404.php', '500.php', 'error.php', 'contentPreview.php');
    /*
    ToDo: incude following, more capable
    $noDelFiles = array(
      'index.php', 'component.php', 'offline.php', 'error.php', 'contentPreview.php',  
      '400.php', '401.php', '402.php', '403.php', '404.php', '405.php', '406.php', '407.php', '408.php', '409.php', '410.php', '411.php', '412.php', '413.php', '414.php', '415.php', 
      '500.php', '501.php', '502.php', '503.php', '504.php', '505.php'
    );
    */
    
    $bar =& new JToolBar( 'My ToolBar' );
    $button =& $bar->loadButtonType( 'Custom' );          
    
    $html .= '<ul class="htmlindex-file-tree filetree tree-disabled">'; 
    $html .= '<li><span class="folder">&nbsp;/html/index/'.$design.'</span>';
    $html .= '<ul>';    
    // Main design file
    $di=0;
    foreach ($noDelFiles as $file) 
    {
      $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=html&amp;file='.$this->template_name.DS.'html'.DS.'index'.DS.$design.DS.$file;  
      $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">\n";
      $link  .= "$file\n";
      $link  .= "</a>\n";        
      
      $style = '';
      if ( (count($noDelFiles)-1) == $di ) 
      { 
        $style = ' style="margin-bottom:2px;padding-bottom:3px;border-bottom:1px solid #eee;"';
      }
      
      $html .= '<li'.$style.'><span class="file">'.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>';
      
      $di++;
    }
    
    if ($htmlfiles) 
    {      
      foreach($htmlfiles as $file) 
      {      
        $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=html&amp;file='.$this->template_name.DS.'html'.DS.'index'.DS.$design.DS.$file;  
        $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">";
        $link  .= "<strong>$file</strong>";
        $link  .= "</a>\n";    
        $deleteLink = $this->template_name.DS.'html'.DS.'index'.DS.$design.DS.$file;    
        $html .= '<li><span class="file">[ <a rel="'.$deleteLink.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - '.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>';      
      }
    } 
    else 
    {
      $html .= '<li>'.JText::_( 'YAML NO OTHERS AVALIABLE' ).'</li>';
    }    
    $html .= '</li>';
    
    $bar    =& new JToolBar( 'My ToolBar' );
    $popup  =& $bar->loadButtonType( 'Popup' );    
    $create_html_link = 'index3.php?option='.$option.'&controller=fileControl&task=create&ext=php&folder='.$this->template_name.DS.'html'.DS.'index'.DS.$design;
    $create_html_button = $popup->fetchButton( 'Popup', 'create_html', 'YAML CREATE FILE', $create_html_link , 640, 350, 150, 150 );  
    $html .= '<li><span class="file file_add">[<span class="on"> '.$create_html_button.' </span>]</span></li>';
    
    $html .= '</ul>';  
    
    $html .= '</ul>';
    
    return $html;
  }
  
  /**
   * View script files
   * @param  design
   * @return $html
  **/  
  function viewScriptFiles($design, $safe=false) 
  {
    global $option;
    
    $path_design = '';
    $path = '';
    $html = ''; 
    $scriptfiles_design = false;
    
    if (!$safe) $path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'scripts'.DS.($design ? $design : $this->design);    
    if ($safe){
      $path_design = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'scripts'.DS.($design ? $design : $this->design);  
      $path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'scripts';  
    }  
    
    $scriptfiles = JFolder::files($path, 'js$', false, false);
    
    if ($path_design)
    {
      $scriptfiles_design = JFolder::files($path_design, 'js$', false, false);
    }
    
    $bar =& new JToolBar( 'My ToolBar' );
    $button =& $bar->loadButtonType( 'Custom' );          
    
    if (!$safe) $html .= '<ul class="scriptindex-file-tree filetree tree-disabled">'; 
    if ($safe) $html  .= '<ul class="tree-safe tree-safe-scripts filetree tree-disabled">';
    
    if (!$safe) $html .= '<li><span class="folder">&nbsp;/scripts/'.$design.'</span>';
    if ($safe) $html .= '<li><span class="folder">&nbsp;/scripts</span>';
    
    $html .= '<ul>';    
    
    if ($scriptfiles) 
    {  
      foreach($scriptfiles as $file) 
      {
        if ($scriptfiles_design) 
        {
          $html .= '<li><span class="folder">&nbsp;'.$design.'</span>';
          $html .= '<ul>'; 
          foreach($scriptfiles_design as $file2) 
          {
            $html .= '<li><span class="file"><a href="#" onclick="javascript:return false;" folder="" file="'.$design.'/'.$file2.'" class="chooseFile">'.$file2.'</a></span></li>';
          }
          $html .= '</ul>';
          $html .= '</li>';
        }
        $scriptfiles_design = false;
             
        $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=syntaxEditor&amp;task=edit&amp;eSyntax=js&amp;file='.$this->template_name.DS.'scripts'.DS.$design.DS.$file;  
        $link   = "<a class=\"modal\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 640, y: 480}}\">";
        $link  .= "<strong>$file</strong>";
        $link  .= "</a>\n";    
        $deleteLink = $this->template_name.DS.'scripts'.DS.$design.DS.$file;    
        if (!$safe) {
          $html .= '<li><span class="file">[ <a rel="'.$deleteLink.'" class="MAINdeleteFile">'.JText::_( 'YAML DELETE' ).'</a> ] - '.$button->fetchButton( 'Custom', $link, 'editfile' ).'</span></li>';      
        }
        else
        {
          $html .= '<li><span class="file"><a href="#" onclick="javascript:return false;" folder="" file="'.$file.'" class="chooseFile">'.$file.'</a></span></li>';
        }
      }
    } 
    else 
    {
      $html .= '<li><span class="nofiles">'.JText::_( 'YAML NO FILES IN FOLDER' ).'</span></li>';
    }
    
    $html .= '</li>';
    
    $bar    =& new JToolBar( 'My ToolBar' );
    $popup  =& $bar->loadButtonType( 'Popup' );    
    $create_script_link = 'index3.php?option='.$option.'&controller=fileControl&task=create&ext=js&folder='.$this->template_name.DS.'scripts'.DS.$design;
    $create_script_button = $popup->fetchButton( 'Popup', 'create_html', 'YAML CREATE FILE', $create_script_link , 640, 350, 150, 150 );  
    if (!$safe) $html .= '<li><span class="file file_add">[<span class="on"> '.$create_script_button.' </span>]</span></li>';

    $html .= '</ul>';     
    $html .= '</ul>';
    
    return $html;
  }
  
  /**
   * View own vars
   * @param  vars
   * @param  vars_design = parent configuration
   * @return $html
  **/  
  function getOwnVars($vars=array(), $vars_design=array()) 
  {
    $html = '';
    $vars_design_switch = false;
        
    if ($vars_design) 
    {
      $html .= '<h4>'.JText::_( 'YAML OWNVARS FROM DESIGN' ).'</h4>';
      $label_parent = JText::_( 'YAML PARENT CONFIG' );
      
      $html .= '<span class="ownvars">'.JText::_( 'YAML LABEL NAME' ).'</span>&nbsp;= <span>'.JText::_( 'YAML LABEL VALUE' ).'</span>';
      $html .= '<br style="clear:both;" />'; 
      $vars_design_switch = true;
    }
    
    foreach ($vars as $var=>$value) 
    {    
      // Global vars
      if ( array_key_exists($var, $vars_design) ) 
      {      
        $vars_design[$var] = isset($vars_design[$var]) ? $vars_design[$var] : false;
        
        if ( strlen($value) > 0 ) 
        {
          $var_value = $value;
        } 
        elseif ($vars_design[$var] ) 
        {
          $var_value = $vars_design[$var];        
        }
        
        if ( $vars_design[$var] === $vars[$var] ) 
        {
        } 
        else 
        {
          // Var changed from global/design  
          $html .= '<div class="ownVars">';
          $html .= '<input class="ownvars readonly" readonly="readonly" class="ownvars" type="text" name="ownVars[][name]" value="'.$var.'" /> = ';
          $html .= '<input disabled="disabled" type="hidden" value="'.$var_value .'" />';
          $html .= '<input class="var_value" type="text" name="ownVars[][value]" value="'.$var_value .'" />';
          $html .= '<input class="varFromParent" type="checkbox" value="1" id="varFromParent_'.$var.'" name="varFromParent_'.$var.'"/><label for="varFromParent_'.$var.'">'.$label_parent.'</label>';
          $html .= '</div>';
        }        
        // Delete Var Temporary
        unset($vars[$var]);
        unset($vars_design[$var]); 
      }
    }  
    
    // Var default from design
    foreach ($vars_design as $var_d=>$value_d) 
    {
      $html .= '<div class="ownVars">';
      $html .= '<input class="ownvars readonly" readonly="readonly" class="ownvars" type="text" name="ownVars[][name]" value="'.$var_d.'" /> = ';
      $html .= '<input disabled="disabled" type="hidden" value="'.$value_d .'" />';
      $html .= '<input disabled="disabled" class="var_value" type="text" name="ownVars[][value]" value="'.$value_d.'" />';
      $html .= '<input checked="checked" class="varFromParent" type="checkbox" value="1" id="varFromParent_'.$var_d.'" name="varFromParent_'.$var_d.'"/><label for="varFromParent_'.$var_d.'">'.$label_parent.'</label>';
      $html .= '</div>';
    }
    
    if ($vars_design_switch)
    {
      $html .= '<hr />';
      $html .= '<h4>'.JText::_( 'YAML OWNVARS ADDITIONAL' ).'</h4>';
    }
    
    $html .= '<span class="ownvars">'.JText::_( 'YAML LABEL NAME' ).'</span>&nbsp;= <span>'.JText::_( 'YAML LABEL VALUE' ).'</span>';
    $html .= '<br style="clear:both;" />';
    
    foreach ($vars as $var=>$value) 
    {
      // Var from current config
      $html .= '<div class="ownVars">';
      $html .= '<input class="ownvars" class="ownvars" type="text" name="ownVars[][name]" value="'.$var.'" /> = ' ;
      $html .= '<input class="var_value" type="text" name="ownVars[][value]" value="'.$value.'" /> ';
      $html .= '<a class="deleteVar">['.JText::_( 'YAML REMOVE' ).']</a>';
      $html .= '</div>';
    }
    
    $html .= '<a class="addVar">['.JText::_( 'YAML ADD VAR' ).']</a>';
    
    return $html;
  }
  
  /**
   * Get available positions from template
   * @param  cur_template
   * @return $positions
  **/  
  function getPositions()
  {
    jimport('joomla.filesystem.folder');
    jimport('joomla.application.helper');

    //Get the database object
    $db  =& JFactory::getDBO();

    // template assignment filter
    $query = 'SELECT DISTINCT(template) AS text, template AS value'.
        ' FROM #__templates_menu' .
        ' WHERE client_id = 0';
    $db->setQuery( $query );
    $templates = $db->loadObjectList();

    // Get a list of all template xml files for a given application
    $positions = array();

    // Get the xml parser first
    $path = JPATH_SITE.DS.'templates'.DS.$this->template_name;

    $xml =& JFactory::getXMLParser('Simple');
    if ($xml->loadFile($path.DS.'templateDetails.xml'))
    {
      $p =& $xml->document->getElementByPath('positions');
      if (is_a($p, 'JSimpleXMLElement') && count($p->children()))
      {
        foreach ($p->children() as $child)
        {
          if (!in_array($child->data(), $positions)) {
            $positions[] = $child->data();
          }
        }
      }
    }

    $positions = array_unique($positions);
    sort($positions);

    return $positions;
  }
  
  /**
   * Get head files in configuration
   * @param  type
   * @param  array
   * @param  buttons
   * @param  dummy
   * @return $html
  **/  
  function getHeadFiles($type, $array, $buttons=array(), $dummy=false) 
  {
    global $option;
    
    $html = '';
    $dummyid = '';
    
    // Leeren key erzeugen das Dummy erstellt werden kann
    if ( !count($array) && $dummy ) $array[1] = true;
    
    if ($dummy) $dummyid = ' id="headDummy_'.$dummy.'"';
    
    $bar =& new JToolBar( 'Popups' );
    $popup =& $bar->loadButtonType( 'Popup' );
    
    $browser_vals = array(
                    ''              => JText::_( 'YAML ALL' ),
                    'msie'          => JText::_( 'YAML BROWSER IE' ),
                    'msie 5'        => JText::_( 'YAML BROWSER IE5' ),
                    'msie 6'        => JText::_( 'YAML BROWSER IE6' ),
                    'msie 7'        => JText::_( 'YAML BROWSER IE7' ),
                    'opera'         => JText::_( 'YAML BROWSER OPERA' ),
                    'mozilla'       => JText::_( 'YAML BROWSER MOZILLA' ),
                    '(_TYPE_VALUE)' => JText::_( 'YAML TYPE VALUE' )
                  );
                  
    if ($type=='addstylesheets') 
    {
      $type_vals = array(
                      'text/css'       => JText::_( 'YAML TYPE TEXT CSS' ),
                      '(_TYPE_VALUE)'  => JText::_( 'YAML TYPE VALUE' )
                    );
      $media_vals = array(
                      'all'           => JText::_( 'YAML ALL' ),
                      'print'         => JText::_( 'YAML MEDIA PRINT' ),
                      'screen'        => JText::_( 'YAML MEDIA SCREEN' ),
                      'aural'         => JText::_( 'YAML MEDIA AURAL' ),
                      'braille'       => JText::_( 'YAML MEDIA BRAILLE' ),
                      'handheld'      => JText::_( 'YAML MEDIA HANDHELD' ),
                      'projection'    => JText::_( 'YAML MEDIA PROJECTION' ),
                      'tty'           => JText::_( 'YAML MEDIA TTY' ),
                      'tv'            => JText::_( 'YAML MEDIA TV' ),
                      '(_TYPE_VALUE)' => JText::_( 'YAML TYPE VALUE' )
                    );
    } 
    elseif ($type=='addscripts') 
    {
      $type_vals = array(
                      'text/javascript'           => JText::_( 'YAML TYPE TEXT JAVASCRIPT' ),
                      'text/ecmascript'          => JText::_( 'YAML TYPE TEXT ECMASCRIPT' ),
                      'text/jscript'             => JText::_( 'YAML TYPE TEXT JSCRIPT' ),
                      'text/livescript'          => JText::_( 'YAML TYPE TEXT LIVESCRIPT' ),
                      'text/tcl'                 => JText::_( 'YAML TYPE TEXT TCL' ),
                      'text/x-ecmascript'        => JText::_( 'YAML TYPE TEXT X-ECMASCRIPT' ),
                      'text/x-javascript'        => JText::_( 'YAML TYPE TEXT X-JAVASCRIPT' ),
                      'application/ecmascript'   => JText::_( 'YAML TYPE APP ECMASCRIPT' ),
                      'application/javascript'   => JText::_( 'YAML TYPE APP JAVASCRIPT' ),
                      'application/x-ecmascript' => JText::_( 'YAML TYPE APP X-ECMASCRIPT' ),
                      'application/x-javascript' => JText::_( 'YAML TYPE APP X-JAVASCRIPT' ),
                      '(_TYPE_VALUE)'            => JText::_( 'YAML TYPE VALUE' )
                    );    
    }
    
    $source_value = '';
    foreach ($array as $file=>$attribs) 
    {      
      if ($type=='addstylesheets') 
      {
      if ($attribs['source']=='design') 
      {
        $source_value = 'design';
        $def_folder = '/css/'.$this->design.'/';
      } 
      else 
      {
        $source_value = '';
        $def_folder = '/yaml/';       
      }
    } 
    elseif($type=='addscripts') 
    {
      if ($attribs['source']=='design') 
      {
        $source_value = 'design';
        $def_folder = '/scripts/'.$this->design.'/';       
      } 
      else 
      {
        $source_value = '';
        $def_folder = '/scripts/';         
      }  
    }

    // Für Dummy muss per JS eine neue ID generiert werden
    srand(microtime()*1000000);
    $Id = 'input_id_'.rand(1,400);
    
    $html .= '<div class="head_files yslider-sub"'.$dummyid.'>'; 
    if (!$dummy) $html .= '<a name="'.$Id.'"></a>';  
    if ($dummy) $file = '';
    
    $html .= '<div class="slide-title-sub">';
    $html .= '<a class="deleteHeadFile">[ '.JText::_( 'YAML REMOVE' ).' ]</a> '; 
    $html .= '<span class="yOrder">[ '; 
    $html .= '<a class="imagelink yOrderUp" href="#"><img src="components/'.$option.'/images/icons/order_up.png" alt="'.JText::_( 'YAML ORDER UP' ).'" /></a> '; 
    $html .= '<a class="imagelink yOrderDown" href="#"><img src="components/'.$option.'/images/icons/order_down.png" alt="'.JText::_( 'YAML ORDER DOWN' ).'" /></a> ] ';
    $html .= '</span>';
  
    if ($type=='addstylesheets') 
    { 
      if ($dummy) 
      {
        $html .= '<span class="source_path" id="path_'.$Id.'">'.$def_folder.'</span><input readonly="readonly" id="'.$Id.'" name="'.$type.'[][file]" class="inputbox_stylesheet off" type="text" value="'.JText::_( 'YAML ALERT NO FILE SELECTED' ).'" />';
      } 
      else 
      {
        $html .= '<span class="source_path" id="path_'.$Id.'">'.$def_folder.'</span><input readonly="readonly" id="'.$Id.'" name="'.$type.'[][file]" class="inputbox_stylesheet" type="text" value="'.$file.'" />';
      }
    } 
    else 
    {
      if ($dummy) 
      {
        $html .= '<span class="source_path" id="path_'.$Id.'">'.$def_folder.'</span><input readonly="readonly" id="'.$Id.'" name="'.$type.'[][file]" class="inputbox_script off" type="text" value="'.JText::_( 'YAML ALERT NO FILE SELECTED' ).'" />';
      } 
      else 
      {
        $html .= '<span class="source_path" id="path_'.$Id.'">'.$def_folder.'</span><input readonly="readonly" id="'.$Id.'" name="'.$type.'[][file]" class="inputbox_script" type="text" value="'.$file.'" />';
      }
    }
    $html .= '<input class="input_source" id="source_'.$Id.'" name="'.$type.'[][source]" type="hidden" value="'.$source_value.'" />';
    
    $html .= '</div>';
    $html .= '<div class="ycontent-sub">';  
              
    if ($buttons) 
    {
      
      $html .= '<div class="explore_buttons">';
      for($b=0; $b<count($buttons); $b++) 
      {        
        $bar =& new JToolBar( 'My ToolBar' );
        $button =& $bar->loadButtonType( 'Custom' );
        
        $link   = "<a onclick=\"javascript:return false;\" class=\"modal yamlExplorer\" href=\"".$buttons[$b]['link'].'&returnid='.$Id."\" rel=\"{handler: 'iframe', size: {x: 420, y: 420}}\">";
        $link  .= JText::_( $buttons[$b]['label'] );
        $link  .= "</a>\n";  
        
        $html .= $button->fetchButton( 'Custom', $link, 'editplugin' );
        
        if ( $b<(count($buttons)-1) ) $html .= '| ';
      }
      $html .= '</div><br />';
    }
      
    // Browser
    // If Browser not available add this.
    if ( !in_array($attribs['browser'], $browser_vals) && !$dummy ) 
    {
      $browser_vals = array_merge(array($attribs['browser']=>$attribs['browser']), $browser_vals);
    }

    $html .= '<label class="yaml_label_def2" for="'.$type.'[][browser]">'.JText::_( 'YAML BROWSER' ).':</label>';
    $html .= '<select class="typeValue" name="'.$type.'[][browser]">';  
    foreach ($browser_vals as $key => $value) 
    {
      if ($attribs['browser']==$key && !$dummy) 
      {
        $selected = ' selected="selected"';
      } 
      else 
      {
        $selected = '';      
      }      
      $html .= '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
    }
    $html .= '</select><br style="clear:both;" />';
      
    // Type
    // If Type not available add this.
    if ( !in_array($attribs['type'], $type_vals) && $attribs['type'] != 'text/css' && $attribs['type'] != 'text/javascript' && !$dummy ) 
    {
      $type_vals = array_merge(array($attribs['type']=>$attribs['type']), $type_vals);
    }
    
    $html .= '<label class="yaml_label_def2" for="'.$type.'[][type]">'.JText::_( 'YAML TYPE' ).':</label>';
    $html .= '<select class="typeValue" name="'.$type.'[][type]">';  
    foreach ($type_vals as $key => $value) 
    {
      if ($attribs['type']==$key && !$dummy) 
      {
        $selected = ' selected="selected"';
      } 
      else 
      {
        $selected = '';      
      }      
      $html .= '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
    }
    $html .= '</select><br style="clear:both;" />';
      
    // Media
    if ($type=='addstylesheets') 
    {
      // If Media not available add this.
      if ( !in_array($attribs['media'], $media_vals) && $attribs['media'] != 'all' && !$dummy ) 
      {
        $media_vals = array_merge(array($attribs['media']=>$attribs['media']), $media_vals);
      }
    
      $html .= '<label class="yaml_label_def2" for="'.$type.'[][media]">'.JText::_( 'YAML MEDIA' ).':</label>';
      $html .= '<select class="typeValue" name="'.$type.'[][media]">';  
      foreach ($media_vals as $key => $value) 
      {
        if ($attribs['media']==$key && !$dummy) 
        {
          $selected = ' selected="selected"';
        } 
        else 
        {
          $selected = '';      
        }      
        $html .= '<option'.$selected.' value="'.$key.'">'.$value.'</option>';
      }
      $html .= '</select>';
    }
      
      $html .= '</div>';      
      $html .= '</div>';  
      
      if ($dummy) return $html;
    
    }
  
    return $html;
  }
  
  /**
   * Parse configuration
   * @param  config
   * @param  design
   * @return $pConf
  **/  
  function parseConfigDesign($config, $design=false) 
  {
    $col1_config = '';
    $col2_config = '';
    $col3_config = '';
    
    $col1_clearing = '';
    $col2_clearing = '';
    $col3_clearing = '';
    
    $pConf['addhead']       = '';
    $pConf['addscript']     = array();
    $pConf['addstylesheet'] = array();
    
    $pConf['layout_1col']    = '';
    $pConf['layout_2col_1']  = '';
    $pConf['layout_2col_2']  = '';
    $pConf['layout_3col']    = '';
    
    $pConf['custom_xml'] = '';     
    $pConf['debug']      = '';
    $pConf['design']     = '';
    $pConf['html_file']  = '';
    
    $plugins = array();
    $ownVars = array();

    foreach ($config as $conf) 
    {
      if ( isset($conf['layout_1col']) )   { $pConf['layout_1col']   = $conf['layout_1col']   ? $conf['layout_1col']   : '1col_3'; continue; }
      if ( isset($conf['layout_2col_1']) ) { $pConf['layout_2col_1'] = $conf['layout_2col_1'] ? $conf['layout_2col_1'] : '2col_13'; continue; }
      if ( isset($conf['layout_2col_2']) ) { $pConf['layout_2col_2'] = $conf['layout_2col_2'] ? $conf['layout_2col_2'] : '2col_23'; continue; }
      if ( isset($conf['layout_3col']) )   { $pConf['layout_3col']   = $conf['layout_3col']   ? $conf['layout_3col']   : '3col_123'; continue; }
      
      if ( isset($conf['debug']) )     { $pConf['debug']     = $conf['debug'];     continue; }
      if ( isset($conf['design']) )    { $pConf['design']    = $conf['design'];    continue; }
      if ( isset($conf['html_file']) ) { $pConf['html_file'] = $conf['html_file']; continue; }
      if ( isset($conf['addhead']) )   { $pConf['addhead']   = $conf['addhead'];   continue; }
      
      if ( isset($conf['custom']['xmlconfig']) ) { $pConf['custom_xml'] = $conf['custom']['xmlconfig']; continue; }

      if ( isset($conf['addstylesheet']) ) 
      { 
        foreach ( $conf['addstylesheet'] as $file=>$attribs ) 
        {        
          $pConf['addstylesheet'][$file]['type']     = isset($attribs['type']) ? $attribs['type'] : '';
          $pConf['addstylesheet'][$file]['media']     = isset($attribs['media']) ? $attribs['media'] : '';
          $pConf['addstylesheet'][$file]['browser']  = isset($attribs['browser']) ? $attribs['browser'] : '';
          $pConf['addstylesheet'][$file]['source']   = isset($attribs['source']) ? $attribs['source'] : '';  
        }
        continue;      
      }
      if ( isset($conf['addscript']) ) 
      { 
        foreach ( $conf['addscript'] as $file=>$attribs ) 
        {        
          $pConf['addscript'][$file]['type']     = isset($attribs['type']) ? $attribs['type'] : '';
          $pConf['addscript'][$file]['browser']  = isset($attribs['browser']) ? $attribs['browser'] : '';
          $pConf['addscript'][$file]['source']   = isset($attribs['source']) ? $attribs['source'] : '';  
        }
        continue;      
      }
      
      if ( isset($conf['col1_content']) ) 
      {    
        foreach ($conf['col1_content'] as $pos => $col1) {        
          if ($pos=='__clear')
           {
            $col1_clearing[] = $col1[0];
          } 
          else 
          {  
            $col1_config .= JYAML::getContentConfig('col1', $pos, $this->positions, $col1);
          }
        }
        continue;
      }
      
      if ( isset($conf['col2_content']) ) 
      {
        foreach ($conf['col2_content'] as $pos => $col2) 
        {
          if ($pos=='__clear') {
            $col2_clearing[] = $col2[0];
          } 
          else 
          {
            $col2_config .= JYAML::getContentConfig('col2', $pos, $this->positions, $col2);
          }
        }
        continue;
      }
      
      if ( isset($conf['col3_content']) ) 
      {
        foreach ($conf['col3_content'] as $pos => $col3) 
        {
          if ($pos=='__clear') 
          {
            $col3_clearing[] = $col3[0];
          } 
          else 
          {
            $col3_config .= JYAML::getContentConfig('col3', $pos, $this->positions, $col3);
          }
        }
        continue;
      }
      
      if ( isset($conf['plugins']) ) 
      {
        foreach ($conf['plugins'] as $plugin=>$params) 
        {
          $plugins[$plugin]['paramString'] = '';
          foreach ($params as $name=>$data) 
          {
            $plugins[$plugin]['params'][$name] = $data;
            $plugins[$plugin]['paramString'] .= $name."=".$data."\n";
          }
        }
        continue;
      }
      
      /* save own vars */
      $ownKey = key($conf);
      $ownVars[$ownKey] = $conf[$ownKey];
    }
    
    // Entities for viewing
    $pConf['addhead'] = htmlentities( $pConf['addhead'] );
    
    $pConf['col1_config'] = $col1_config ? $col1_config : '';
    $pConf['col2_config'] = $col2_config ? $col2_config : '';
    $pConf['col3_config'] = $col3_config ? $col3_config : '';
    $pConf['col1_clearing'] = JYAML::getPosClear('col1', $col1_clearing);
    $pConf['col2_clearing'] = JYAML::getPosClear('col2', $col2_clearing);
    $pConf['col3_clearing'] = JYAML::getPosClear('col3', $col3_clearing);
    $pConf['plugins']     = $plugins;
    $pConf['ownVars']     = $ownVars;
    
    return $pConf;    
  }
  
  /**
   * View clarings of positions
   * @param  col
   * @param  positions
   * @return $html
  **/  
  function getPosClear($col=false, $positions=array()) 
  {
    $checked_all = '';
    $disabled = '';
    $html = '';    

    if (!$col || !isset($this->config_design)) return '';
    
    $html .= '<fieldset class="clearPositions">';
    $html .= '<legend>';
    
    if ($positions)
    {
      foreach ($positions as $pos)
      {
        if($pos=='__all__')
        {
          $disabled = ' disabled="disabled"';
          $checked_all = ' checked="checked"';
        }          
      }  
    }    

    $html .= JText::_( 'YAML DESIGN CLEAR POSITIONS' ).' | <label for="'.$col.'_clear[]">'.JText::_( 'YAML CLEAR ALL' ).':</label>';
    $html .= '<input'.$checked_all.' type="checkbox" name="'.$col.'_clear[]" id="clear_'.$col.'_all'.'" value="__all__"  />';    
    $html .= '</legend>';    
    
    foreach ($this->config_design as $conf) 
    { 
      if ( !isset($conf[$col.'_content']) ) continue;
      
      foreach ($conf[$col.'_content'] as $pos => $attribs)
      {      
        if (!$pos)
        {
          $posl = '<strong>'.$attribs['type'].'</strong>';
          $posv = 'main::'.$attribs['type'];
          $pos = $attribs['type'];
        }
        else
        {
          $posl = $attribs['type'].':<strong>'.$pos.'</strong>';
          $posv = $attribs['type'].'::'.$pos;
          $pos = $attribs['type'].'_'.$pos;
        }
        
        $checked = '';
        if ( $positions && in_array($posv, $positions) ) $checked = ' checked="checked"';
                
        $html .= '<label for="'.$col.'_clear[]">'.$posl.'</label>';
        $html .= '<input'.$disabled.$checked.' class="clear_checkbox" type="checkbox" name="'.$col.'_clear[]" id="'.$col.'_clear_'.$pos.'" value="'.$posv.'"  />';
      }
    }        
    $html .= '</fieldset>';
    
    return $html;
  }
  
  /**
   * Create list of html/index files
   * @param  files
   * @param  custom
   * @return $radiolist
  **/  
  function getHTMLFiles($files, $custom=false) 
  {
    // Set default/global value
    if ($custom) 
    {
      $radiolist[] = new JYAMLmakeSelect('', JText::_( 'YAML PARENT CONFIG' ));
    } 
    else 
    {
      $radiolist[] = new JYAMLmakeSelect('', JText::_( 'YAML DEFAULT HTMLFILE' ));    
    }
    
    foreach($files as $value) 
    {
      $text  = $value;
      $value = str_replace('.php', '', $value);
      $radiolist[] = new JYAMLmakeSelect($value, $text);
    }
        
    return $radiolist;
  }
  
  /**
   * Dummy for Custom XML
   * @return $html
  **/  
  function getCustomXMLDummy() {
    $html  = '<fieldset class="custom_xml" id="custom_xml_dummy">';
    $html .= '<a class="deleteCustomXML">['.JText::_( 'YAML REMOVE' ).']</a>';
    $html .= '</fieldset>';
    
    return $html;
  }
  
  /**
   * View Custom XML selections
   * @param customs
   * @return $html
  **/ 
  function getCustomXML($customs) 
  {
    $html = '';
    $parts_list = array();    
        
    // create array of parts
    if ($customs) 
    {
      foreach($customs as $parts=>$custom) 
      {
        $parts_list[] = $parts;    
      }
    }
    
    // Get Menus
    $menulinks[] = JHTML::_( 'select.option', '0', JText::_( 'YAML SELECT MENULINK' ) );
    $menulinks = array_merge($menulinks, JHTML::_('menu.linkoptions') );
    
    $db =& JFactory::getDBO();
    
    // Set Menulinks id to link
    foreach ($menulinks as $key=>$menulink) 
    {
      if ($menulink->value > 0) 
      {
        $query = 'SELECT link FROM #__menu'
        . ' WHERE id = '.$menulink->value
        ;
        $db->setQuery( $query );
        $link = $db->loadResult();
        
        // set link as value
        if ( strpos($link, 'index.php') !== false ) 
        {
          // append id to text
          $menulinks[$key]->text = $menulinks[$key]->text.' ('.$menulinks[$key]->value.')';
          
          $link = explode('?', $link);
          $link[1] = str_replace('&amp;', '&', $link[1]);
          $link[1] = str_replace('&', ',', $link[1]);
          $menulinks[$key]->value = $link[1].',Itemid='.$menulinks[$key]->value;
          $linklist[] = $menulinks[$key]->value;
          
          // unset assigned parts
          if ( in_array($link[1], $parts_list) ) unset($menulinks[$key]);          
          
        } 
        elseif ($link) 
        {
          // unset external links
          unset($menulinks[$key]);
        }
      }
    }
    
    // Get articles
    $query = 'SELECT c.id as id, c.title as title, c.state as status, g.name AS groupname, cc.title AS cat_name, s.title AS sec_name' .
        ' FROM #__content AS c' .
        ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' .
        ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' .
        ' LEFT JOIN #__groups AS g ON g.id = c.access' .
        ' WHERE c.state >= 0'
        ;
    $db->setQuery( $query );
    $articles = $db->loadObjectList();  
    
    // Sort to sections and categories for optgroup
    $link_key = 'view=article,option=com_content,id=';
    $contentlist = array();
    foreach ($articles as $key => $article) 
    {
      $contentlist[] = $link_key.$article->id;
      if ( !in_array($link_key.$article->id, $parts_list) ) 
      {
        if ($article->sec_name) 
        {
          $article_group[$article->sec_name.' / '.$article->cat_name][$key]['title'] = $article->title.' ('.$article->id.')';
          $article_group[$article->sec_name.' / '.$article->cat_name][$key]['id'] = $link_key.$article->id;
          $article_group[$article->sec_name.' / '.$article->cat_name][$key]['groupname'] = $article->groupname;
        } 
        else 
        {
          $article_group[JText::_( 'YAML UNCATEGORESIZED' )][$key]['title'] = $article->title.' ('.$article->id.')';
          $article_group[JText::_( 'YAML UNCATEGORESIZED' )][$key]['id'] = $article->id;    
          $article_group[JText::_( 'YAML UNCATEGORESIZED' )][$key]['groupname'] = $article->groupname;
        }
      }
    }  
    
    // Get Cagegories
    $query = 'SELECT c.id as id, c.title as cat_name, s.title as sec_name' .
        ' FROM #__categories AS c' .
        ' LEFT JOIN #__sections AS s ON s.id = c.section' .
        ' WHERE c.section > 0'
        ;
    $db->setQuery( $query );
    $categories = $db->loadObjectList();
    
    // Sort to sections for optgroup
    $link_key = 'view=category,option=com_content,id=';
    $categorylist = array();
    foreach( $categories as $key=>$categorie ) 
    {
      $categorylist[] = $link_key.$categorie->id;
      if ( !in_array($link_key.$categorie->id, $parts_list) ) 
      {
        $cats[$categorie->sec_name][$key]['title'] = $categorie->cat_name.' ('.$categorie->id.')';
        $cats[$categorie->sec_name][$key]['id'] = $link_key.$categorie->id;
      }
    }
        
    // Get Sections
    $query = 'SELECT id, title' .
        ' FROM #__sections'
        ;
    $db->setQuery( $query );
    $sections = $db->loadObjectList();
    
    // Set values and test
    $link_key = 'view=section,option=com_content,id=';
    $sectionlist = array();
    foreach( $sections as $key=>$section ) 
    {
      $sectionlist[] = $link_key.$section->id;
      if ( !in_array($link_key.$section->id, $parts_list) ) 
      {
        $sections[$key]->title = $section->title.' ('.$section->id.')';
        $sections[$key]->id = $link_key.$section->id;
      } 
      else 
      {
        unset($sections[$key]);
      }
    }
    
    // Get Components
    $components_dir = JPATH_SITE.DS.'components';
    $components = JFolder::folders( $components_dir, '.*', false, false, array('.svn') );
        
    $html .= '<div id="xml_customs">';
    
    // Menulinks
    $html .= '<label class="yaml_label_def" for="menulinks">'.JText::_( 'YAML LABEL MENULINKS' ).':</label>';
    $html .= JHTML::_('select.genericlist', $menulinks, 'menulinks', '', 'value', 'text', '0', 'menulinks' );    
    $html .= '<br style="clear:both" />';
    
    // Contents
    $html .= '<label class="yaml_label_def" for="contents">'.JText::_( 'YAML LABEL OR' ).' '.JText::_( 'YAML LABEL CONTENTS' ).':</label>';
    $html .= '<select name="contents" id="contents">';
    $html .= '<option value="0">'.JText::_( 'YAML SELECT CONTENTLINK' ).'</option>';
    foreach ($article_group as $key=>$articles ) 
    {  
      $html .= '<optgroup label="'.$key.'">';
      foreach( $articles as $article ) 
      {
        $html .= '<option value="'.$article['id'].'">'.$article['title'].'</option>';
      }
      $html .= '</optgroup>';   
    }      
    $html .= '</select>';
    $html .= '<br style="clear:both" />';
  
    // Categories
    $html .= '<label class="yaml_label_def" for="categories">'.JText::_( 'YAML LABEL OR' ).' '.JText::_( 'YAML LABEL CATEGORIES' ).':</label>';
    $html .= '<select name="categories" id="categories">';
    $html .= '<option value="0">'.JText::_( 'YAML SELECT CATEGORIELINK' ).'</option>';
    foreach ( $cats as $sec=>$cat ) 
    {
      $html .= '<optgroup label="'.$sec.'">';
      foreach( $cat as $c ) {
        $html .= '<option value="'.$c['id'].'">'.$c['title'].'</option>';
      }
      $html .= '</optgroup>';
    }
    $html .= '</select>';
    $html .= '<br style="clear:both" />';
    
    // Sections
    $html .= '<label class="yaml_label_def" for="sections">'.JText::_( 'YAML LABEL OR' ).' '.JText::_( 'YAML LABEL SECTIONS' ).':</label>';
    $html .= '<select name="sections" id="sections">';
    $html .= '<option value="0">'.JText::_( 'YAML SELECT SECTIONLINK' ).'</option>';
    foreach ( $sections as $section ) 
    {
        $html .= '<option value="'.$section->id.'">'.$section->title.'</option>';    
    }
    $html .= '</select>';
    $html .= '<br style="clear:both" />';
    
    // Components
    $link_key = 'option=';
    $html .= '<label class="yaml_label_def" for="components">'.JText::_( 'YAML LABEL OR' ).' '.JText::_( 'YAML LABEL COMPONENTS' ).':</label>';
    $html .= '<select name="components" id="components">';
    $html .= '<option value="0">'.JText::_( 'YAML SELECT COMPONENTLINK' ).'</option>';
    $componentlist = array();
    foreach ( $components as $component) 
    {
      $componentlist[] = $link_key.$component;
      $value = $link_key.$component;
      if ( !in_array($value, $parts_list) ) 
      {
        $html .= '<option value="'.$value.'">'.$component.'</option>';  
      }      
    }
    $html .= '</select>';
    $html .= '<br style="clear:both" />';
    
    $html .= '</div>'; // end: #xml_customs
    
    // URL-Parts
    $html .= '<label class="yaml_label_def" for="custom_xml_parts"><span class="hasTip" title="'.JText::_( 'YAML OR TYPE URL-PART' ).'">'.JText::_( 'YAML LABEL OR' ).' '.JText::_( 'YAML LABEL CUSTOM URL PARTS' ).'</span>:</label>';
    $html .= '<input id="custom_xml_parts" name="custom_xml_parts" value="" />';
    $html .= '<br style="clear:both" /><br />';
    
    // Description
    $html .= '<label class="yaml_label_def" for="custom_xml_desc">'.JText::_( 'YAML LABEL CUSTOM DESC' ).':</label>';
    $html .= '<input id="custom_xml_desc" name="custom_xml_desc" value="" />';
    $html .= '<br style="clear:both" />';
    
    // Include Subitems
    $html .= '<label class="yaml_label_def" for="custom_xml_subitems"><span class="hasTip" title="'.JText::_( 'YAML LABEL CUSTOM SUBITEMS DESC' ).'">'.JText::_( 'YAML LABEL CUSTOM SUBITEMS' ).'</span>:</label>';
    $html .= '<input type="checkbox" id="custom_xml_subitems" name="custom_xml_subitems" value="1" />';
    $html .= '<br style="clear:both" />';
    
    // Force Config
    $html .= '<label class="yaml_label_def" for="custom_xml_force"><span class="hasTip" title="'.JText::_( 'YAML LABEL CUSTOM FORCE DESC' ).'">'.JText::_( 'YAML LABEL CUSTOM FORCE' ).'</span>:</label>';
    $html .= '<input type="checkbox" id="custom_xml_force" name="custom_xml_force" value="1" />';
    $html .= '<br style="clear:both" /><br />';
    
    // XML Filelist
    $xml_files_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$this->design;
    $xml_files = JFolder::files($xml_files_path, 'xml$', false, false);
    $xml_filelist[] = new JYAMLmakeSelect('0', JText::_( 'YAML SELECT XMLFILE' ));
    
    $html .= '<label class="yaml_label_def" for="custom_xml_file">'.JText::_( 'YAML LABEL CUSTOM XMLFILE' ).' /config/'.$this->design.'/</label>';
    foreach ($xml_files as $xml_file) {
      $xml_filelist[] = new JYAMLmakeSelect( str_replace('.xml', '', $xml_file), $xml_file);      
    }
    $html .= JHTML::_('select.genericlist', $xml_filelist, 'custom_xmlfile', '', 'value', 'text', '0', 'custom_xmlfile' );
    $html .= '<br style="clear:both" />';

    // Add Button
    $html .= '<br /><div align="center"><a name="addCustomXML" href="#addCustomXML" class="button addCustomXML">'.JText::_( 'YAML ADD CUSTOM CONFIG' ).'</a></div><br /><hr />';
    $html .= ('
      <script type="text/javascript">  
        jQuery("a.addCustomXML").click(function () {
          // validate required fields
          var message = "";
          
          if ( !jQuery("#custom_xml_desc").val() ) { message = "'.JText::_( 'YAML ALERT NO DESC', 1 ).'\n"; }
          if ( !jQuery("#custom_xml_parts").val() ) { message = message + "'.JText::_( 'YAML ALERT NO PARTS', 1 ).'\n"; }
          if ( jQuery("#custom_xmlfile option[@selected]").val() == \'0\' ) { message = message + "'.JText::_( 'YAML ALERT NO FILE SELECTED', 1 ).'"; }
          if (message) {
            alert(message);
            return false;
          } else {
            // add new custom xml
            var clone = jQuery("#custom_xml_dummy").clone(true);
            
            var desc  = jQuery("#custom_xml_desc").val();
            var parts = jQuery("#custom_xml_parts").val();
            if ( jQuery("#custom_xml_subitems[@checked]").val() ) {
              var subitems = jQuery("#custom_xml_subitems[@checked]").val();
            } else {
              var subitems = \'0\';
            }
            if ( jQuery("#custom_xml_force[@checked]").val() ) {
              var force = jQuery("#custom_xml_force[@checked]").val();
            } else {
              var force = \'0\';
            }
            var file  = jQuery("#custom_xmlfile option[@selected]").val();
            
            var html;
            html  = \'<legend>\' + desc + \'</legend>\';
            html += \'<label class="yaml_label_def">'.JText::_( 'YAML LABEL CUSTOM XMLFILE' ).' /config/'.$this->design.'/</label>: <span class="xml_filename">\' + file + \'</span>.xml\';
            html += \'<br style="clear:both" />\';
            html += \'<label class="yaml_label_def">'.JText::_( 'YAML LABEL CUSTOM URL PARTS' ).'</label>: <span class="xml_parts">\' + parts + \'</span>\';
            html += \'<input type="hidden" id="input_custom_parts" name="custom[][parts]" value="\' + parts + \'" />\';  
            html += \'<input type="hidden" id="input_custom_file" name="custom[][file]" value="\' + file + \'" />\';    
            html += \'<input type="hidden" id="input_custom_desc" name="custom[][desc]" value="\' + desc + \'" />\';
            html += \'<input type="hidden" id="input_custom_subitems" name="custom[][subitems]" value="\' + subitems + \'" />\';
            html += \'<input type="hidden" id="input_custom_force" name="custom[][force]" value="\' + force + \'" />\';
            
            clone.append(html);                
            clone.appendTo("#custom_xml_list");
            
            clone.slideDown("fast");          
            clone.removeAttr("id");
            
            // Reset elements
            jQuery("#custom_xml_parts").val(\'\').removeAttr("readonly");
            jQuery("#custom_xml_desc").val(\'\').removeAttr("readonly");
            jQuery("#custom_xml_subitems[@checked]").removeAttr("checked");
            jQuery("#custom_xml_force[@checked]").removeAttr("checked");
            jQuery("#menulinks").removeAttr("disabled");
            jQuery("#contents").removeAttr("disabled");
            jQuery("#categories").removeAttr("disabled");
            jQuery("#sections").removeAttr("disabled");
            jQuery("#components").removeAttr("disabled");
            jQuery("#menulinks").find("option[@selected]").removeAttr("selected");
            jQuery("#contents").find("option[@selected]").removeAttr("selected");
            jQuery("#categories").find("option[@selected]").removeAttr("selected");
            jQuery("#sections").find("option[@selected]").removeAttr("selected");
            jQuery("#components").find("option[@selected]").removeAttr("selected");
            jQuery("#custom_xmlfile").find("option[@selected]").removeAttr("selected");
          }                    
        });
    </script>
    ');
    
    // return and add empty outline if custom is empty
    if ( !$customs ) 
    {
      $html .= '<div id="custom_xml_list"></div>';
      return $html;
    }
    
    $custom_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$this->design.DS;
    
    // Get Custom List
    $html .= '<div id="custom_xml_list">';
    foreach ($customs as $key=>$custom) 
    {
      // get assign type        
      if ( in_array($key, $linklist) ) {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX MENULINK' );
      } elseif ( in_array($key, $contentlist) ) {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX CONTENTLINK' );    
      } elseif ( in_array($key, $categorylist) ) {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX CATEGORYLINK' );    
      } elseif ( in_array($key, $sectionlist) ) {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX SECTIONLINK' );    
      } elseif ( in_array($key, $componentlist) ) {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX COMPONENTLINK' );    
      } else {
        $label_suffix = JText::_( 'YAML LABEL SUFFIX CUSTOMLINK' );      
      }    
      
      $file_not_exists = '';
      if ( !JFile::exists($custom_path.$custom['file'].'.xml') ) 
      {
        $file_not_exists = '<span class=\'warn\'>'.JText::_( 'YAML XML FILE NOT EXISTS' ).'</span><br /><br />';
      }  
    
      $html .= '<fieldset class="custom_xml">';
      $html .= '<a class="deleteCustomXML">['.JText::_( 'YAML REMOVE' ).']</a>';
      $html .= $file_not_exists;
      $html .= '<legend><span class="label_suffix">'.$label_suffix.'</span>: '.$custom['desc'].'</legend>';
      if ( isset($custom['subitems']) && $custom['subitems'] )
      {
        $html .= '<div class="desc_subitems">'.JText::_( 'YAML LABEL CUSTOM SUBITEMS INCLUDED' ).'</div>';    
      } 
      if ( isset($custom['force']) && $custom['force'] )
      {
        $html .= '<div class="desc_force off">'.JText::_( 'YAML LABEL CUSTOM FORCE CONFIG' ).'</div>';    
      }
      $html .= '<label class="yaml_label_def">'.JText::_( 'YAML LABEL CUSTOM XMLFILE' ).' /config/'.$this->design.'/</label>: <span class="xml_filename">'.$custom['file'].'</span>.xml';
      $html .= '<br style="clear:both" />';
      $html .= '<label class="yaml_label_def">'.JText::_( 'YAML LABEL CUSTOM URL PARTS' ).'</label>: <span class="xml_parts">'.$key.'</span>';   
      $html .= '<input type="hidden" name="custom[][parts]" value="'.$key.'" />';  
      $html .= '<input type="hidden" name="custom[][file]" value="'.$custom['file'].'" />';    
      $html .= '<input type="hidden" name="custom[][desc]" value="'.$custom['desc'].'" />'; 
      $html .= '<input type="hidden" name="custom[][subitems]" value="'.($custom['subitems'] ? '1' : '0').'" />'; 
      $html .= '<input type="hidden" name="custom[][force]" value="'.($custom['force'] ? '1' : '0').'" />';
      $html .= '</fieldset>';
    }    
    $html .= '</div>';
    
    return $html;
  }
  
  /**
   * View Custom XML information
   * @param design
   * @return $html
  **/  
  function getCustomXMLInfo($design) 
  {
    $html = '';
    
    // get xml
    $file = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design.'.xml';
    $custom_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design.DS;

    $xml = & JFactory::getXMLParser('Simple');
    $config = JYAML::readConfig($this->template_name, $file, true);
    
    foreach ( $config as $conf ) 
    {
      // custom xml
      if (isset($conf['custom']['xmlconfig'])) 
      {
        $html .= '<ul>';
        foreach ( $conf['custom']['xmlconfig'] as $parts=>$attribs) 
        {
          $class = '';
          $file_not_exists = '';
          if ( !JFile::exists($custom_path.$attribs['file'].'.xml') ) 
          {
            $file_not_exists = '<span class=\'warn\'>'.JText::_( 'YAML XML FILE NOT EXISTS' ).'</span><br /><br />';
            $class = ' warn';
          }
          $tolltip = $file_not_exists 
                   . JText::_( 'YAML LABEL CUSTOM XMLFILE' ).': /config/'.$design.'/'.$attribs['file'].'.xml<br />'
                   . JText::_( 'YAML LABEL CUSTOM URL PARTS' ).': '.$parts;
          $html .= '<li><span class="hasTip'.$class.'" title="'.$tolltip.'">'.$attribs['desc'].'</span></li>';
        }
        $html .= '</ul>';
      }
    }
    
    return $html;
  }
  
  /**
   * Get shot overview information
   * @param design
   * @param plugins
   * @return $html
  **/    
  function getSimpleInfo($design) 
  {
    $html = '';
    
    // get xml
    $file = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design.'.xml';
    $custom_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design.DS;

    $xml = & JFactory::getXMLParser('Simple');
    $config = JYAML::readConfig($this->template_name, $file, true);
    
    foreach ( $config as $conf ) 
    {            
      $c['debug'] = isset($conf['debug']) ? $conf['debug'] : '';  
      $c['html_file'] = isset($conf['html_file']) ? $conf['html_file'] : ''; 
      
      if ( isset($conf['plugins']) ) 
      {
        foreach($conf['plugins'] as $plugin=>$pgl_array)
        {
          $c['plugins'][$plugin] = $pgl_array;
        }
      }
    }
      
    // Short info of settings
    $html_file_path = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'html'.DS.'index'.DS.$design.DS;
    $class = '';
    $tooltip_title ='';
    $file_not_exists = '';
    if ( $c['html_file'] && !JFile::exists($html_file_path.$c['html_file'].'.php') ) 
    {
      $tooltip_title = ' title="<span class=\'warn\'>'.JText::_( 'YAML HTML FILE NOT EXISTS' ).'</span>"';
      $class = ' class="hasTip warn"';
    }  

    // Main Settings    
    $html .= '<ul>';
    $html .= '<li>'.JText::_( 'YAML CONF HTMLFILE LABEL' ).': <span'.$class.$tooltip_title.'>'.($c['html_file'] ? $design.'/'.$c['html_file'].'.php' : $design.' / index.php').'</span></li>';
    $debug = JText::_( 'YAML DEFAULT CONFIG' );
    
    if ( isset($c['debug']) && $c['debug']!=1) 
    {
      $debug = JText::_( 'YAML OFF TXT' );
    } 
    elseif($c['debug']==1) 
    {
      $debug = JText::_( 'YAML ON TXT' );
    }
    $html .= '<li>'.JText::_( 'YAML CONF DEBUG LABEL' ).': '.$debug.'</li>';
    
    // Plugin Setting
    if ( $this->template_plugins ) 
    {  
      $html .= '<li style="margin-top:4px;"><strong>'.JText::_( 'YAML CONF PLUGINS' ).':</strong> ';
      $html .= '<ul>';
      foreach ($this->template_plugins as $plugin) 
      {    
        $pgl_status = isset($c['plugins'][$plugin->plugin]['published']) ? $c['plugins'][$plugin->plugin]['published'] : '0';
        if ( $pgl_status ) {
          $status = $pgl_status;    
        } else {
          $status = $plugin->published;    
        }
        $status = $status ? '<span class="on">'.JText::_( 'YAML ENABLED TXT' ).'</span>' : '<span class="off">'.JText::_( 'YAML DISABLED TXT' ).'</span>';
        
        $html .= '<li>'.$plugin->name.' ['.$status.']</li>';
      }
      $html .= '</ul>';
      $html .= '</li>';
    }
    $html .= '</ul>';
    
    return $html;
  }
  
  /**
   * Output YAML Message
   * @param msg
  **/  
  function outputMsg($msg) 
  {
    echo '<p class="yaml_msg">'.$msg.'</p>';
  }
  
  /**
   * Download links for packages
   * @param  ext
   * @return $url
  **/  
  function getDownloadURL($ext) 
  {
    $url = 'http://dev.jyaml.de/JYAML.GET/';
    
    if ($ext=='updater') $url .= 'update.php';
    
    if ($ext=='plugin') $url .= 'downloads/plugins/plg_system_jyaml.zip';
    if ($ext=='template') $url .= 'downloads/templates/tpl_hm_yaml.zip';
    if ($ext=='component') $url .= 'downloads/components/com_jyaml.zip';
    if ($ext=='package') $url .= 'downloads/components/com_jyaml_package.zip';
    
    return $url;
  }
  
  /**
   * Satus of YAML Plugin
   * @param  ext
   * @return message in queue
  **/  
  function getPluginStatus() 
  {
    global $option, $mainframe;
    $html = '';
    
    //Get the database object
    $db  =& JFactory::getDBO();

    $query = 'SELECT published FROM #__plugins' .
            ' WHERE element = \'jyaml\' and folder = \'system\'';
    $db->setQuery( $query );
    $result = $db->loadResult();   
    
    $plugin_file = JPATH_SITE.DS.'plugins'.DS.'system'.DS.'jyaml.php';
    if (!JFile::exists($plugin_file)) $result = 2;
    
    $html .= '<p style="text-align:center;text-decoration:underline;">YAML Joomla! System Plugin:</p>';

    if ($result=='0') 
    {
      $html .= '<p style="text-align:center;">'.JText::_( 'YAML PLUGIN DISABLED' ).'</p>';
      $html .= '<p style="text-align:center"><a href="index.php?option='.$option.'&amp;task=enablePlugin">[ '.JText::_( 'YAML PLUGIN ENABLE BUTTON' ).' ]</a></p>';  
    } 
    elseif ($result=='1') 
    {
      //$html .= '<p class="on">'.JText::_( 'YAML PLUGIN ENABLED' ).'</p>';
    } 
    else 
    {
      $html .= '<p style="text-align:center;">'.JText::_( 'YAML PLUGIN NOT INSTALLED' ).'</p>';
      
      $allow_url_fopen = ini_get('allow_url_fopen');
      
      if ( !$allow_url_fopen ) 
      {      
        $html .= '<p style="text-align:center;"><a href="'.JYAML::getDownloadURL('plugin').'" target="_blank">[ '.JText::_( 'YAML PLUGIN DOWNLOAD HERE' ).' ]</a></p>';  
      }
      else
      {        
        $html .= '<form id="install_tpl_pgl" enctype="multipart/form-data" action="index.php" method="post" name="adminForm_pgl_install">';    
        $html .= '<input type="hidden" id="install_url" name="install_url" class="input_box" size="70" value="'.JYAML::getDownloadURL('plugin').'" />';
        $html .= '<p style="text-align:center;"><input type="submit" class="button" value="'.JText::_( 'YAML PLUGIN INSTALL' ).'" /></p>';
        $html .= '<input type="hidden" name="type" value="" />';
        $html .= '<input type="hidden" name="installtype" value="url" />';
        $html .= '<input type="hidden" name="task" value="installPlugin" />';
        $html .= '<input type="hidden" name="option" value="'.$option.'" />';
        $html .= JHTML::_( 'form.token' );
        $html .= '</form>';
        $html .= '<div id="installresult"></div>';
      }
    }
    if ($result!='1') $mainframe->enqueueMessage( $html, 'error' );
  }
  
  /**
   * Enable Plugin
  **/  
  function enablePlugin() 
  {
    global $option, $mainframe;
    $db =& JFactory::getDBO();
    
    $query = 'UPDATE #__plugins SET published=1' .
            ' WHERE element = \'jyaml\' and folder = \'system\'';
    $db->setQuery( $query );
    if (!$result = $db->query()) 
    {
      echo $db->stderr();
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML PLUGIN ENABLED MSG' ) );
      $mainframe->redirect( JURI::base() . 'index.php?option='.$option );
    }
  }
  
  /**
   * Get version information
   * @return $html
  **/  
  function getVersionInfo($getData=false) 
  {
    global $option;
    
    $url = 'http://dev.jyaml.de/JYAML.GET/version.ini';
    $read_online = false;
    
    $info['c-j-version'] = '?';
    $info['c-j-build']   = '?';
    
    $jc_version = '';
    $j_version = '';
    
    $html = '';
    $data = false;
    
    $allow_url_fopen = ini_get('allow_url_fopen');
    
    if ( !@fsockopen('dev.jyaml.de', 80) || !$allow_url_fopen ) 
    { 
      $read_online = false;
      
      $html .= '<div class="off">'.JText::_('YAML VERSION NOT AVAILABLE').'</div> ';        
      if (!$allow_url_fopen) 
      {
        $html .= '<div class="off">'.JText::_('YAML OPEN EXTERNAL URL NOT ALLOWED').'</div> ';
      }
      $html .= '<br />';
    } 
    else 
    {
      $data = JFile::read( $url );  
      $read_online = true;  
    }
    
    if ($data) 
    {
      $registry = new JRegistry( $option );
      $registry->loadINI( $data );
      
      // get current version info
      $info['c-j-version'] = $registry->getValue( $option.'.YAML JOOMLA VERSION' );
      $info['c-j-build']   = $registry->getValue( $option.'.YAML JOOMLA BUILD' );
    }   
      
    // Installed component version
    $xml = & JFactory::getXMLParser('Simple');
    if (!$xml->loadFile(JPATH_BASE.DS.'components'.DS.$option.DS.substr($option, 4).'.xml')) 
    {
      unset($xml);
    } else {    
      $element =& $xml->document->version[0];
      $info['i-j-version'] = $element ? $element->data() : '';
      $element =& $xml->document->build[0];
      $info['i-j-build'] = $element ? $element->data() : '';
    }  
        
    $update = false;
    
    if ( $info['c-j-version'].$info['c-j-build'] > $info['i-j-version'].$info['i-j-build'] ) 
    {
      $j_version = '<span class="off" style="font-weight:normal;">'.$info['i-j-version'].'(Build: '.$info['i-j-build'].')</span>';
      $jc_version = '<span class="on" style="font-weight:bold;">'.$info['c-j-version'].'(Build: '.$info['c-j-build'].')</span>';
      $update = true;
    } 
    else 
    {
      $j_version  = '<span>'.$info['i-j-version'].'(Build: '.$info['i-j-build'].')</span>';
      $jc_version = '<span>'.$info['c-j-version'].'(Build: '.$info['c-j-build'].')</span>';
    } 
    if ($info['i-j-version']) {
      $html .= JText::_( 'YAML JOOMLA VERSION INSTALLED' ).': '.$j_version.'<br />';
    }
    else
    {
      $html .= JText::_( 'YAML JOOMLA VERSION INSTALLED' ).': (SVN Version)<br />';
    }
    $html .= JText::_( 'YAML JOOMLA VERSION CURRENT' ).': '.$jc_version.'<br /><br />';  
    
    if ($update && $info['i-j-version']) 
    {   
      $bar =& new JToolBar( 'My ToolBar' );
      $button =& $bar->loadButtonType( 'Custom' );
  
      $url   = 'index3.php?option='.$option.'&amp;controller=update&amp;task=make_update';  
      $link  = "<a class=\"modal\" href=\"$url\" rel=\"{closeWithOverlay:false, handler: 'iframe', size: {x: 640, y: 480}}\">\n";
      $link .= JText::_( 'YAML UPDATE BUTTON' );
      $link .= "</a>\n";    
      $html .= '<p id="make_update">'.$button->fetchButton( 'Custom', $link, 'editfile' ).'</p>';      
    }
        
    if ($getData) 
    {
      return $info;
    }
    else
    {
      return $html;
    }
  }
  
  /**
   * Make a valid design  
  **/  
  function makeDesignValid( ) 
  {
    global $option, $mainframe;
    
    $template_name = JRequest::getVar( 'mkV_template_name', false, 'POST' );
    $design = JRequest::getVar( 'mkV_design', false, 'POST' );
    $data   = explode( '-', JRequest::getVar('mkV_data', false, 'POST') );
    
    
    if ($template_name && $design && $data) 
    {
      foreach ($data as $dir) 
      {
        $folder = JPATH_SITE.DS.'templates'.DS.$template_name.DS.$dir.DS.$design;
        if (JFolder::create( $folder )) 
        {
            $mainframe->enqueueMessage( JText::_( 'YAML FOLDER SUCCESS CREAT' ) . ' - ('.DS.'templates'.DS.$template_name.DS.$dir.DS.$design.DS.')' );
        } 
        else 
        {
            $mainframe->enqueueMessage( JText::_( 'YAML FOLDER FAILED CREAT' ) . ' - ('.DS.'templates'.DS.$template_name.DS.$dir.DS.$design.DS.')', 'error' );
        }
      }
    }
    
    $mainframe->redirect( JURI::base() . 'index.php?option='.$option );
  }
  
  /**
   * Get Template Plugins
   * @param  template
   * @param  plugin for once
   * @param  config
   * @param  design_config
   * @return $plugins
  **/  
  function getPlugins($template, $plugin=false, $config=false, $design_config=false) 
  {
    if ($plugin) 
    {
      $plugins = new JYAMLplugin($plugin, $template, $config);      
    } 
    else 
    {  
      $path = JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins';
      $pluginlist = JFolder::folders($path, '', false, false, array('.svn', 'example') );
      
      if ($pluginlist) 
      {
        foreach ($pluginlist as $plugin) 
        {
          if ( isset($design_config[$plugin]) && !isset($config[$plugin]) ) $config = $design_config;
          $plugins[] = new JYAMLplugin($plugin, $template, $config);
        }
        
        /* Check if Plugin is valide */
        $i=0;
        foreach ($plugins as $plg) 
        {
          if (!$plg->name) unset($plugins[$i]);
          $i++;
        }
      }
    }    
    return $plugins;
  }
  
  /**
   * Get CSS basic layout files
   * @param  col
   * @param  set
   * @param  global
   * @param  design
   * @return $html
  **/  
  function getCSSLayouts($col, $set, $global=true, $design) 
  {
    $l = array();
    
    $l['name'] = $this->conf['layout_'.$col];  
    $l['checked'] = '';

    if ($global) 
    {    
      if ( !$this->conf['layout_'.$col] ) 
      { 
        $l['checked'] = ' checked="checked"'; 
        $l['name'] = $this->conf_design['layout_'.$col];
      } 
      else 
      {
        $l['checked'] = '';  
      }
    }
    
    $files = JFolder::files(JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'css'.DS.$design, '^layout_');
    
    $html = '<select'.($l['checked'] ? ' disabled="disabled"' : '').' class="layout_name" name="layout_'.$col.'" id="layout_'.$col.'">';
        
    for($i=0; $i<count($files); $i++)
    {
      $n = explode('_', $files[$i]);
      $n = explode('.', $n[2]);
      $n = str_replace('0', '', $n[0]);
        
      $search = array();
      
      switch ($col) 
      {
        case '1col':
          $search = array('3');
          break;
        case '2col_1':
          $search = array('13', '31');
          break;
        case '2col_2':
          $search = array('23', '32');
          break;
        case '3col':
          $search = array('123', '132', '213', '231', '312', '321');
          break;
      }
      
      if (in_array($n, $search))
      {
        $fn = str_replace(array('layout_', '.css'), '', $files[$i]);
        $html .= '<option'.($fn==$l['name'] ? ' selected="selected"' : '').' value="'.$fn.'">'.$fn.'</option>';
      }
    }    
    $html .= '</select>';    
    $l['html'] = $html;
    
    return $l; 
  }
  
  /**
   * String to Array helper
   * @param  string
   * @param  ignore
   * @return $array
  **/  
  function stringToArray($string, $ignore=false) {
    $array = array();
    for($i=0; $i<strlen($string); $i++)
    {
      $s = $string{$i};
      if ($ignore===false || $ignore!=$s ) $array[$i] = $s;
    }
    return $array;
  }
  
  /**
   * Check design is valide
   * @param  design
   * @return $validationList
  **/  
  function checkValideTemplate($design) 
  {
    $noValid = array();
    $validationList = '';
    $validationList .= '<ul>';
    
    // Ceck design CSS directory
    $check = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'css'.DS.$design->value;
    if ( JFolder::exists($check) ) 
    {
      $tipTitle = DS.$this->template_name.DS.'css'.DS.$design->value.DS;
      $validationList .= '<li><span class="ok hasTip" title="'.$tipTitle.'">'.JText::_( 'YAML CONF CSS DIR FOUND' ).'</span></li>';
    } 
    else 
    {
      $noValid[] = 'css';
      $validationList .= '<li><span class="warn">'.JText::_( 'YAML CONF CSS DIR NOT FOUND' ).'</span></li>';      
    }      
    
    // Check desgin Image dir
    $check = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'images'.DS.$design->value;
    if ( JFolder::exists($check) ) 
    {
      $tipTitle = DS.$this->template_name.DS.'images'.DS.$design->value.DS;
      $validationList .= '<li><span class="ok hasTip" title="'.$tipTitle.'">'.JText::_( 'YAML CONF IMAGE DIR FOUND' ).'</span></li>';
    } 
    else 
    {
      $noValid[] = 'images';
      $validationList .= '<li><span class="warn">'.JText::_( 'YAML CONF IMAGE DIR NOT FOUND' ).'</span></li>';      
    }
    
    // Check desgin Image dir
    $check = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'scripts'.DS.$design->value;
    if ( JFolder::exists($check) ) 
    {
      $tipTitle = DS.$this->template_name.DS.'scripts'.DS.$design->value.DS;
      $validationList .= '<li><span class="ok hasTip" title="'.$tipTitle.'">'.JText::_( 'YAML CONF SCRIPT DIR FOUND' ).'</span></li>';
    } 
    else 
    {
      $noValid[] = 'scripts';
      $validationList .= '<li><span class="warn">'.JText::_( 'YAML CONF SCRIPT DIR NOT FOUND' ).'</span></li>';      
    }
    
    // Check desgin Layout dir
    $check = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.$design->value;
    if ( JFolder::exists($check) ) 
    {
      $tipTitle = DS.$this->template_name.DS.'config'.DS.$design->value.DS;
      $validationList .= '<li><span class="ok hasTip" title="'.$tipTitle.'">'.JText::_( 'YAML CONF CONFIG DIR FOUND' ).'</span></li>';    
    } 
    else 
    {
      $noValid[] = 'config';
      $validationList .= '<li><span class="warn">'.JText::_( 'YAML CONF CONFIG DIR NOT FOUND' ).'</span></li>';    
    }
    
    // Check design Index dir
    $check = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'html'.DS.'index'.DS.$design->value;
    if ( JFolder::exists($check) ) 
    {
      $tipTitle = DS.$this->template_name.DS.'html'.DS.'index'.DS.$design->value.DS;
      $validationList .= '<li><span class="ok hasTip" title="'.$tipTitle.'">'.JText::_( 'YAML CONF INDEX DIR FOUND' ).'</span></li>';  
    } 
    else 
    {
      $noValid[] = 'html'.DS.'index';
      $validationList .= '<li><span class="warn">'.JText::_( 'YAML CONF INDEX DIR NOT FOUND' ).'</span></li>';
    }  
    $validationList .= '</ul>';
    
    $res['list'] = $validationList;
    $res['noValid'] = $noValid;
    
    return $res;
  }
  
  /**
   * View HTML Dummys
   * @return $d as html
  **/  
  function getHTMLDummys() {
    $d  = JYAML::getPositionDummy('col1', $this->positions);
    $d .= JYAML::getPositionDummy('col2', $this->positions);
    $d .= JYAML::getPositionDummy('col3', $this->positions);
    
    if (isset($this->explore_buttons)) 
    {
      $d .= JYAML::getHeadFiles('addstylesheets', $this->conf['addstylesheet'], $this->explore_buttons[0], 'addstylesheets');
      $d .= JYAML::getHeadFiles('addscripts', $this->conf['addscript'], $this->explore_buttons[1], 'addscripts');
    }
    
    $d .= JYAML::getCustomXMLDummy();
    
    $d .= '<div class="ownVars" id="varsDummy">';
    $d .= '<input class="ownvars" type="text" name="ownVars[][name]" value="" /> = ';
    $d .= '<input type="text" name="ownVars[][value]" value="" /> ';
    $d .= '<a class="deleteVar">['.JText::_( 'YAML REMOVE' ).']</a>';
    $d .= '</div>';
    
    return $d;
  } 
  
  /**
   * Generate Link for documentation
   * @param  link
   * @return link as html
  **/  
  function docLink($link=false)
  {
    if (!$link) return '';
    
    $d = 'http://dev.jyaml.de/jyamldoc.php?link=';    
    return '<a class="doclink" target="_blank" href="'.$d.$link.'" title="'.JText::_( 'YAML TXT LINK DOKUMENTATION' ).'">[?]</a>';      
  } 
  
}

/**
 * Template Plugin Class
**/
class JYAMLplugin {
  /** @var string */
  var $plugin;
  var $name;
  var $xmlfile;
  var $configfile;
  var $version;
  var $description;
  var $paramString = '';
    
  /** @var boolean */
  var $published;
  var $isCore = 1;
  
  /** @var array */
  var $paramOutput;

  /**
   * Start Plugin class
   * @param  plugin
   * @param  template
   * @param  config
  **/  
  function JYAMLplugin($plugin, $template, $config=false) {    
    $this->plugin = $plugin;    
    
    // core array
    $core_plugins = array ('ie_png_fix', 'css_optimizer', 'ie_min_max_expressions');
    
    $this->configfile = JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins'.DS.$plugin.DS.'config.xml';
    
    if ( JFile::exists($this->configfile) ) {
      // Load language File
      $lang    =& JFactory::getLanguage();
      $lang->load('pgl_template', JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins'.DS.$plugin);  
  
      $this->paramString = isset($config[$plugin]['paramString']) ? $config[$plugin]['paramString'] : false;
      if ( !$this->paramString ) $this->paramString = JFile::read( $this->configfile );
      
      $this->xmlfile = JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins'.DS.$plugin.DS.$plugin.'.xml';
      $xmldoc = & JFactory::getXMLParser('Simple');
          
      if ( JFile::exists($this->xmlfile) ) 
      {
        $xmldoc->loadfile( $this->xmlfile );
        
        $config_name = $xmldoc->document->name[0]->data();
				$this->name  = $config_name;
				
				$config_version = $xmldoc->document->version[0]->data();
        $this->version  = $config_version;
				
				$config_description = $xmldoc->document->description[0]->data();
        $this->description  = JText::_($config_description);
        
        $params = new JParameter( $this->paramString, $this->xmlfile );        
        //$params->addElementPath( JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins'.DS.$plugin.DS.'element' );
        
        $this->paramOutput = $params->render("plugins[$plugin]");
        
        $this->published = $params->get('published', 0);
        
        // Check is Core
        if ( !in_array($this->plugin, $core_plugins) ) 
				{
				  $this->isCore = 0;  
				}    
      }
      
      unset($xmldoc);
    }  
  }
}

/**
 * Make Select oject for joomla selector includes
**/
class JYAMLmakeSelect {
  /* @var variable */
  var $value;
  var $text;

  function JYAMLmakeSelect($value, $text) 
  {
    $this->value = $value;
    $this->text  = $text;
  }
}

/**
 * Design structure class for template
**/
class JYAMLdesignStructure {
  /* @var string */
  var $design_name;
  var $template_name;
  /* @var string */  
  var $main_config_file;
  /* @var string */  
  var $css_folder;
  var $image_folder;
  var $script_folder;
  var $config_folder;
  var $index_folder;
  
  /**
   * Start class
   * @param  template
   * @param  design
  **/  
  function JYAMLdesignStructure($template, $design) 
  {  
    $this->design_name   = $design;
    $this->template_name = $template;
    
    $path = JPATH_SITE.DS.'templates'.DS.$this->template_name;
    
    $this->main_config_file = $path.DS.'config'.DS.$design.'.xml';
    
    $this->css_folder       = $path.DS.'css'.DS.$design;
    $this->image_folder     = $path.DS.'images'.DS.$design;
    $this->script_folder    = $path.DS.'scripts'.DS.$design;
    $this->config_folder    = $path.DS.'config'.DS.$design;
    $this->index_folder     = $path.DS.'html'.DS.'index'.DS.$design;
  }
  
  /**
   * Delete design
  **/    
  function deleteDesign($ignore=false) 
  {
    // Check Design is default
    $xmlfile = JPATH_SITE.DS.'templates'.DS.$this->template_name.DS.'config'.DS.'_global.xml';
    $config  = JYAML::readConfig($this->template_name, $xmlfile);
    
    if ( $config[0]['design']==$this->design_name && !$ignore )
    {
      return 'isdefault';
    } 
    else 
    { 
      JFile::delete($this->main_config_file);
      
      JFolder::delete($this->css_folder);
      JFolder::delete($this->image_folder);
      JFolder::delete($this->script_folder);
      JFolder::delete($this->config_folder);
      JFolder::delete($this->index_folder);
        
      return true;
    }
    
    return false;
  }
}
  
?>