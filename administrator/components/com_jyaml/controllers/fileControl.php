<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: fileControl.php 468 2008-07-27 16:54:14Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 468 $
 * @lastmodified    $Date: 2008-07-27 18:54:14 +0200 (So, 27. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class hmyamlControllerfileControl extends hmyamlController
{
  /**
   * constructor (registers additional tasks to methods)
   * @return void
   */
  function __construct()
  {
    parent::__construct();
  }

  function create()
  {
    global $option;
    
    $ext = JRequest::getVar('ext', '');
    $folder = JRequest::getVar('folder', '');
    $core_folder = JRequest::getVar('core_folder', '');
    
    $files = JFolder::files( JPATH_SITE.DS.'templates'.DS.$folder, $ext.'$', false, false);
    if ($core_folder) 
    {
      $core_files = JFolder::files( JPATH_SITE.DS.'templates'.DS.$core_folder.DS.'yaml', $ext.'$', true, true, array('core', 'debug') );
    }
		
    $tmp = explode(DS, $folder);		
		$plugins = JFolder::folders( JPATH_SITE.DS.'templates'.DS.$tmp[0].DS.'plugins');
		$plugin_example_files = array();
		foreach($plugins as $plugin) {
		  if($ext=='js') {
			  $pefolder = JPATH_SITE.DS.'templates'.DS.$tmp[0].DS.'plugins'.DS.$plugin.DS.'scripts'.DS.'examples';
				if ( JFolder::exists($pefolder) ) {
				  $plugin_example_files[$plugin][0] = JFolder::files($pefolder, $ext.'$', true, true);
				}
			} elseif ($ext=='css') {
			  $pefolder = JPATH_SITE.DS.'templates'.DS.$tmp[0].DS.'plugins'.DS.$plugin.DS.'css'.DS.'examples';
        if ( JFolder::exists($pefolder) ) {
			    $plugin_example_files[$plugin][0] = JFolder::files( $pefolder, $ext.'$', true, true );
				}
			} else {
			  $pefolder = JPATH_SITE.DS.'templates'.DS.$tmp[0].DS.'plugins'.DS.$plugin.DS.'examples';
        if ( JFolder::exists($pefolder) ) {
			    $plugin_example_files[$plugin][0] = JFolder::files( $pefolder, $ext.'$', true, true );	
				}		
			}
		}
		
	  ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML CREATE FILE TITLE' ); ?></div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_filename"><?php echo JText::_( 'YAML ENTER FILENAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_filename" value="" /><span class="bigform">.<?php echo $ext; ?></span>
      <br /><br />
      <?php
      echo '<label for="basefile">'.JText::_( 'YAML LABEL BASE FILE' ).':</label><br />';
      echo '<select name="basefile">';
      echo '<option value="">'.JText::_( 'YAML SELECT BASE FILE' ).'</option>';
      echo '<option value="">'.JText::_( 'YAML CREATE EMPTY FILE' ).'</option>';
      
      if ($files) 
      {
        echo '<optgroup label="'.JText::_( 'YAML CURRENT DIR FILES' ).'">';
        foreach ($files as $file) 
        {
          echo '<option value="'.$file.'">'.$file.'</option>';        
        }
        echo '</optgroup>';
      }
      
      if ($core_files) 
      {
        echo '<optgroup label="'.JText::_( 'YAML CORE FILES' ).'">';
        foreach ($core_files as $core_file) 
        {
          echo '<option value="'.$core_file.'">'.str_replace(JPATH_SITE.DS.'templates', '', $core_file).'</option>';        
        }  
        echo '</optgroup>';
      }
			
      if ($plugin_example_files) 
      {
        foreach ($plugin_example_files as $plugin=>$plugin_files) 
        {
					echo '<optgroup label="'.JText::_( 'YAML PLUGIN EXAMPLE FILES' ).': '.$plugin.'">';
						foreach($plugin_files[0] as $plugin_file) {
						  echo '<option value="'.$plugin_file.'">'.str_replace(JPATH_SITE.DS.'templates', '', $plugin_file).'</option>'; 
						}
					echo '</optgroup>';
        }
      }
      
      echo '</select>';
			
      ?>
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="create_file();" type="button"><?php echo JText::_( 'YAML CREATE FILE' ); ?></button>
      </div>

      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="folder" value="<?php echo $folder; ?>" />
      <input type="hidden" name="ext" value="<?php echo $ext; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function create_file() {
        var form = document.adminForm;
        
        if (form.new_filename.value) {
          if (form.new_filename.value.match(/[^a-z0-9_-]/i)) {
      alert('<?php echo JText::_( 'YAML VALIDATE AZ-_09', 1 ); ?>');
      form.new_filename.focus();
      } else {    
            submitbutton('create_file');
            window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
      }
        } else {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER FILENAME', 1 ); ?>');
          form.new_filename.focus();
        }
      }
    </script>
    <?php
  }
  
  function create_file() 
  {  
    global $option, $mainframe;  
    
    $ext = JRequest::getVar('ext', '');
    $folder = JRequest::getVar('folder', '');
    $new_filename = JRequest::getVar('new_filename', '');
    $basefile = JRequest::getVar('basefile', false);
    
    if ($new_filename && $folder && $ext) 
    {
      $file = JPATH_SITE.DS.'templates'.DS.$folder.DS.$new_filename.'.'.$ext;
      
      if ( JFile::exists($file) ) 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML CREATED FAILED FILE EXITS' ), 'error' );      
      } 
      else 
      {
        if ($basefile) 
        {
          if ( strpos($basefile, JPATH_SITE) ===false) 
          {
            $basefilefull = JPATH_SITE.DS.'templates'.DS.$folder.DS.$basefile;
          } 
          else 
          {
            $basefilefull = $basefile;
          }
          
          JFolder::create( dirname($file) );
          $status = JFile::copy($basefilefull, $file);      
        } 
        else 
        {
          $status = JFile::write( $file, ' ' );
        }
        if ( $status ) 
        {
          $mainframe->enqueueMessage( JText::_( 'YAML CREATED SUCCESS' ) );
        } 
        else 
        {
          $mainframe->enqueueMessage( JText::_( 'YAML CREATED FAILED' ), 'error' );  
        }
      }        
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML CREATED FAILED' ), 'error' );      
    }
    
    $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');    
  }
  
  function createDesign() 
  {
    global $option;
    
    $source   = JRequest::getVar('source', '');
    $template = JRequest::getVar('template', '');
    $source_files = array();
    
    if (!$source) 
    {
      $source_files = JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'config', 'xml$', false, false, array('_global.xml') );
    }
    $templates = JYAML::getTemplates();
    
    ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML CREATE DESIGN TITLE' ); ?></div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_design_name"><?php echo JText::_( 'YAML ENTER DESIGN NAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_design_name" value="" />
      <br /><br />
      <?php
      if ($source_files) {
        echo '<label for="basedesign">'.JText::_( 'YAML LABEL BASE DESIGN' ).':</label><br />';
        echo '<select name="basedesign">';
        echo '<option value="">'.JText::_( 'YAML SELECT BASE DESIGN' ).'</option>';
        //echo '<option value="empty">'.JText::_( 'YAML CREATE EMPTY DESIGN' ).'</option>';
        foreach ($source_files as $file) 
        {
          $file = JFile::stripExt($file);
          echo '<option value="'.$file.'">'.$file.'</option>';        
        }
        echo '</select>';
      }
      
      echo '<br /><br />';
      echo '<label for="template_dest">'.JText::_( 'YAML LABEL TEMPLATE DESTINATION' ).':</label><br />';
      echo '<select name="template_dest">';
      echo '<option selected="selected" class="highlight" value="">->'.$template.' <- '.JText::_( 'YAML CURRENT SELECTED' ).'</option>';
      foreach ($templates as $tmpl) 
      {
        if ($tmpl->name != $template) 
        {
          echo '<option value="'.$tmpl->name.'">'.$tmpl->name.'</option>';
        }        
      }
      echo '</select>';
      ?>
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="create_design();" type="button"><?php echo JText::_( 'YAML CREATE DESIGN' ); ?></button>
      </div>

      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="source" value="<?php echo $source; ?>" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function create_design() {
        var form = document.adminForm;
        <?php if (!$source) : ?>
        var selIndex = form.basedesign.selectedIndex;
        <?php else : ?>
        var selIndex = 1;
        <?php endif; ?>
                
        if (form.new_design_name.value=='') {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER DESIGN NAME', 1 ); ?>');
          form.new_design_name.focus();
        } else if (selIndex < 1) {
          alert('<?php echo JText::_( 'YAML PLEASE SELECT DESIGN SOURCE', 1 ); ?>');
          form.basedesign.focus();
        } else {
          submitbutton('save_design');
          window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
        }
      }
    </script>
    <?php
  }
  
  function rename_design() 
  {
    global $option;
    
    $template = JRequest::getVar('template', '');
    $design = JRequest::getVar('design', '');
       
    ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML RENAME DESIGN' ); ?> (<?php echo $design; ?>)</div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_design_name"><?php echo JText::_( 'YAML ENTER NEW DESIGN NAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_design_name" value="" />
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="do_rename_design();" type="button"><?php echo JText::_( 'YAML RENAME DESIGN' ); ?></button>
      </div>
      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="rename" value="1" />
      <input type="hidden" name="source" value="<?php echo $design; ?>" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function do_rename_design() {
        var form = document.adminForm;                
        if (form.new_design_name.value=='') {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER DESIGN NAME', 1 ); ?>');
          form.new_design_name.focus();
        } else {
          submitbutton('save_design');
          window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
        }
      }
    </script>
    <?php
  }
  
  function save_design() {
    global $option, $mainframe;
    
    $rename            = JRequest::getVar('rename', false, 'POST');
    $source            = JRequest::getVar('source', '', 'POST');
    $basedesign        = JRequest::getVar('basedesign', $source, 'POST');
    $new_design_name   = JRequest::getVar('new_design_name', '', 'POST');
    $template          = JRequest::getVar('template', '', 'POST');
    $template_dest     = JRequest::getVar('template_dest', $template, 'POST');
    
    if ($template_dest) 
    {
      $template = $template_dest;
    }
    
    $src_design = false;
    
    if( $new_design_name && $template ) 
    {      
      if ($basedesign && $basedesign != 'empty') 
      {
        $src_design = new JYAMLdesignStructure($template, $basedesign);
      }
      $new_design = new JYAMLdesignStructure($template, $new_design_name);
      
      if ($src_design && $new_design) 
      {
        // Copy Basic Files
        JFile::copy( $src_design->main_config_file, $new_design->main_config_file );
        //JFile::copy( $src_design->main_index_file,  $new_design->main_index_file );
        
        // Copy CSS Folder
        $cssfiles = JFolder::files($src_design->css_folder, '', true, true);
        JFolder::create($new_design->css_folder);
        foreach ($cssfiles as $file) 
        {
          $new_file = str_replace($src_design->css_folder, $new_design->css_folder, $file);
          $dir = dirname($new_file);
          JFolder::create($dir);
          JFile::copy( $file, $new_file );
          
          $this->replaceDesignPaths($new_file, $basedesign, $new_design_name);
        }
        
        // Copy Images Folder
        $imagefiles = JFolder::files($src_design->image_folder, '', true, true);
        JFolder::create($new_design->image_folder);
        foreach ($imagefiles as $file) 
        {
          $new_file = str_replace($src_design->image_folder, $new_design->image_folder, $file);
          $dir = dirname($new_file);
          JFolder::create($dir);
          JFile::copy( $file, $new_file );
        }
        
        // Copy Scripts Folder
        $scriptfiles = JFolder::files($src_design->script_folder, '', true, true);
        JFolder::create($new_design->script_folder);
        foreach ($scriptfiles as $file) 
        {
          $new_file = str_replace($src_design->script_folder, $new_design->script_folder, $file);
          $dir = dirname($new_file);
          JFolder::create($dir);
          JFile::copy( $file, $new_file );
          
          $this->replaceDesignPaths($new_file, $basedesign, $new_design_name);
        }
        
        // Copy Config Folder
        $configfiles = JFolder::files($src_design->config_folder, '', true, true);
        JFolder::create($new_design->config_folder);
        foreach ($configfiles as $file) 
        {
          $new_file = str_replace($src_design->config_folder, $new_design->config_folder, $file);
          $dir = dirname($new_file);
          JFolder::create($dir);
          JFile::copy( $file, $new_file );
        }
        
        // Copy Index/HTML Folder
        $indexfiles = JFolder::files($src_design->index_folder, '', true, true);
        JFolder::create($new_design->index_folder);
        foreach ($indexfiles as $file) 
        {
          $new_file = str_replace($src_design->index_folder, $new_design->index_folder, $file);
          $dir = dirname($new_file);          
          JFolder::create($dir);
          JFile::copy( $file, $new_file );
          
          $this->replaceDesignPaths($new_file, $basedesign, $new_design_name);
        }
      } 
      else 
      {
        echo '<h2>Sorry, not implementet yet. Plese do copy a design!</h2>';
      }
      
      if ($rename)
      {      
        $src_design->deleteDesign(true);
        
        // Rename Designname in configuration files
        $files = JFolder::files(JPATH_SITE.DS.'templates'.DS.$template.DS.'config', 'xml$', true, true);
        foreach ($files as $file)
        {
          $buffer = JFile::read($file);
          $buffer = str_replace('<design>'.$source.'</design>', '<design>'.$new_design_name.'</design>', $buffer);
          JFile::write($file, $buffer);
        }
      }
      
      $mainframe->enqueueMessage( JText::_( 'YAML CREATED DESIGN SUCCESS' ) );
      
      $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');
    }    
  }
  
  function replaceDesignPaths($file, $src, $dest) 
  {
    $content = JFile::read($file);

    if ( $content && strpos($content, '../../../images/'.$src) )  $content = str_replace('../../../images/'.$src, '../../../images/'.$dest, $content);
    if ( $content && strpos($content, '../../../css/'.$src) )     $content = str_replace('../../../css/'.$src, '../../../css/'.$dest, $content);
    if ( $content && strpos($content, '../../../scripts/'.$src) ) $content = str_replace('../../../scripts/'.$src, '../../../scripts/'.$dest, $content);
    
    JFile::write($file, $content);    
  }
  
  function createCssFolder() 
  {
    global $option;
    
    $template = JRequest::getVar('template', '');
    $design = JRequest::getVar('design', '');
    
    ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML CREATE CSS FOLDER TITLE' ); ?></div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_folder_name"><?php echo JText::_( 'YAML ENTER FOLDER NAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_folder_name" value="" />
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="create_folder();" type="button"><?php echo JText::_( 'YAML CREATE CSS FOLDER' ); ?></button>
      </div>  
      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="design" value="<?php echo $design; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function create_folder() {
        var form = document.adminForm;
               
        if (form.new_folder_name.value=='') {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER FOLDER NAME', 1 ); ?>');
          form.new_folder_name.focus();
        } else {
          submitbutton('save_css_folder');
          window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
        }
      }
    </script>
    <?php
  }
  
  function save_css_folder() 
  {
    global $option, $mainframe;
    
    $template        = JRequest::getVar('template', '', 'POST');
    $design          = JRequest::getVar('design', '', 'POST');
    $new_folder_name = JRequest::getVar('new_folder_name', '', 'POST');
    
    
    
    if ($new_folder_name && $template && $design) 
    {
      $dir = JPATH_SITE.DS.'templates'.DS.$template.DS.'css'.DS.$design.DS.$new_folder_name;
      
      if ( JFolder::exists($dir) ) 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML CREATED CSS FOLDER EXISTS' ), 'error' );      
      } 
      else 
      {      
        if ( JFolder::create($dir) ) 
        {
          $mainframe->enqueueMessage( JText::_( 'YAML CREATED FOLDER SUCCESS' ) );      
        } 
        else
        {
          $mainframe->enqueueMessage( JText::_( 'YAML CREATED FAILED CSS FOLDER' ), 'error' );      
        }
      }
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML CREATED FAILED CSS FOLDER' ), 'error' );      
    }
    
    $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');
  }
  
  function export() 
  {
    global $option;
    
    $design   = JRequest::getVar('design', false);
    $template = JRequest::getVar('template', false);
    
    $plugins = JYAML::getPlugins($template);
    
    if ($design == 'default') 
    {
      die ( '<p class="off">'.JText::_('YAML CANT EXPORT DEFAULT').'</p>' );
    }
    
    ?>
    <form action="index3.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML EXPORT DESIGN TITLE' ); ?> (<?php echo $design;?>)</div>
      </fieldset>
      
      <fieldset>
        <legend><?php echo JText::_( 'YAML EXPORT DESIGN CHROMEFILES TITLE' ); ?></legend>
      
        <p><?php echo JText::_( 'YAML EXPORT DESIGN CHROMEFILES DESC' ); ?></p>
        <?php echo JText::_( 'NO' ); ?><input checked="checked" type="radio" name="include_chromefiles" value="0" />
        <?php echo JText::_( 'YES' ); ?><input type="radio" name="include_chromefiles" value="1" />
      </fieldset>
      
      <fieldset>
        <legend><?php echo JText::_( 'YAML EXPORT DESIGN PLUGINS TITLE' ); ?></legend>
      
        <p><?php echo JText::_( 'YAML EXPORT DESIGN PLUGINS DESC' ); ?></p>
        <?php
        foreach ($plugins as $p) 
        {
          if ( !$p->isCore ) 
          {
            echo '<input name="include_plugins[]" type="checkbox" value="'.$p->plugin.'" />'.$p->name.'<br />';
          }
        }
        ?>
      </fieldset>

      <div align="center">
        <button class="bigform" onclick="process_export();" type="button"><?php echo JText::_( 'YAML EXPORT DESIGN TITLE' ); ?></button>
      </div>  
      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="design" value="<?php echo $design; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function process_export() {
        var form = document.adminForm;               
        submitbutton('process_export');
      }
    </script>
    <?php
  }
  
  function process_export() 
  {
    global $mainframe;
    
    echo '<h1>Export Design Package</h1>';
    
    $include_chromefiles = JRequest::getVar( 'include_chromefiles', true );    
    $include_plugins = JRequest::getVar( 'include_plugins', array() );
    
    $template = JRequest::getVar( 'template', false );
    $design = JRequest::getVar( 'design', false );
    
    $config =& JFactory::getConfig();
    $tmp_path = $config->getValue('config.tmp_path');
    
    $files = array();
    
    // Include Design configuration
    $files = array_merge( $files, array(JPATH_SITE.DS.'templates'.DS.$template.DS.'config'.DS.$design.'.xml') );
    
    // Include Custom configurations
    $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'config'.DS.$design, 'xml$', false, true ) );
    
    // Include Chromefiles / HTML folder
    if ($include_chromefiles) 
    {
      $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'html', 'php$', true, true, array('index') ) );
    }
    
    // Include Plugins
    foreach ($include_plugins as $plugin) 
    {
      $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins'.DS.$plugin, '', true, true ) );    
    }
    
    // Include CSS Files
    $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'css'.DS.$design, 'css$', true, true ) );
    
    // Include HTML Files
    //$files = array_merge( $files, array(JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'index'.DS.$design.'.php') );
    if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'index'.DS.$design)) 
    {
      $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'html'.DS.'index'.DS.$design, 'php$', false, true ) );
    }
    
    // Include Image Files
    if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$template.DS.'images'.DS.$design)) 
    {
      $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'images'.DS.$design, '', true, true ) );  
    }  

    // Include Script Files
    if (JFolder::exists(JPATH_SITE.DS.'templates'.DS.$template.DS.'scripts'.DS.$design)) 
    {
      $files = array_merge( $files, JFolder::files( JPATH_SITE.DS.'templates'.DS.$template.DS.'scripts'.DS.$design, '', true, true ) );
    }  
    
    
    // Create Achrive for Download
    jimport('joomla.filesystem.archive');
    $ext = 'tar';
    $archive = JArchive::create ($tmp_path.DS.'JYAML_DESIGN_'.$design, $files, $ext, '', JPATH_SITE.DS.'templates'.DS.$template, true);
    
    if ($archive) 
    {
      echo '<p class="on">'.JText::_('YAML EXPORT SUCESSFULLY').'</p>';
      echo '<p><strong><a href="../tmp/JYAML_DESIGN_'.$design.'.'.$ext.'">Download Design Package: JYAML_DESIGN_'.$design.'.'.$ext.'</a></strong></p>';
    } 
    else 
    {
      echo '<p class="off">'.JText::_('YAML EXPORT FAILD').'</p>';
    }    
  }  
  
  function importDesign() 
  {
    global $option;
    
    $template = JRequest::getVar('template', false);
    ?>
    <form action="index3.php" method="post" name="importForm" id="importForm" autocomplete="off" enctype="multipart/form-data">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML IMPORT DESIGN TITLE' ); ?> (<?php echo $template; ?>)</div>
      </fieldset>  
        
      <br />
      <label class="bigform" for="importfile"><?php echo JText::_( 'YAML SELECT DESIGN PACKAGE FILE' ); ?>:</label><br />
      <input class="bigform" size="30" type="file" name="file" id="file" value="" />
      <br /><br /><br /><br />

      <div align="center">
        <button class="bigform" type="submit"><?php echo JText::_( 'YAML IMPORT UPLOAD VALIDATE DESIGN' ); ?></button>
      </div>  
      
      <input type="hidden" name="task" value="process_import" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <div id="import_result"></div>
    
    <script type="text/javascript">
      jQuery(document).ready(function() {        
        // prepare the form when the DOM is ready 
        jQuery(document).ready(function() { 
            var options = { 
                target:        '#import_result',   // target element(s) to be updated with server response 
                resetForm: true        // reset the form after successful submit 
            }; 
         
            jQuery('#importForm').submit(function() { 
                jQuery("#import_result").text('');
                jQuery("#import_result").append('<br \/><p class="on"><?php echo JText::_('YAML UPLOAD PROCESS WAIT'); ?><\/p>');
                jQuery(this).ajaxSubmit(options); 
                return false; 
            }); 
        }); 
      }); 
    </script>
    
    <?php    
  }
  
  function process_import() 
  {
    global $option;
    
    $template = JRequest::getVar( 'template', 'hm_yaml' );
    $tpl_path = JPATH_SITE.DS.'templates'.DS.$template;  
    
    if ( !isset($_FILES['file']) ) 
    {
      die ( '<p class="off">'.JText::_('YAML PLEASE SELECT FILE').'</p>' );
    }
    
    $config =& JFactory::getConfig();
    $tmp_path = $config->getValue('config.tmp_path');
    
    $design_exists = false;
    $plugin_exists = array();
    $html_exists = false;
    $make_backup = true;
    
    $tmp_dest_folder = $tmp_path.DS.'import_yaml_design';
    $tmp_dest_file   = $tmp_path.DS.$_FILES['file']['name'];
    
    if ($tmp_dest_file) 
    {    
      if ( JFolder::exists($tmp_dest_folder) )
      {
        JFolder::delete($tmp_dest_folder);
      }
      if ( JFile::exists($tmp_dest_file) )
      {
        JFile::delete($tmp_dest_file);
      }
      
      move_uploaded_file($_FILES['file']['tmp_name'], $tmp_path.DS.$_FILES['file']['name']);
      jimport('joomla.filesystem.archive');
      JArchive::extract($tmp_path.DS.$_FILES['file']['name'], $tmp_path.DS.'import_yaml_design');

      
      $files = JFolder::files($tmp_dest_folder, '', true, true);      
      if (!$files) die( '<p class="off">'.JText::_('YAML NO FILES FOUND').'</p>' );
    
      // Validate
      foreach ($files as $file)
      {
        $file = str_replace($tmp_dest_folder.DS, '', $file);
        $filetree = explode(DS, $file);
				
				$mainfolder = isset($filetree[0]) ? $filetree[0] : '';
				$subfolder  = isset($filetree[1]) ? $filetree[1] : '';
				$tfile      = isset($filetree[2]) ? $filetree[2] : '';
        
        // Check Design exists
        if ($mainfolder == 'html' && $subfolder == 'index' && $tfile == 'index.php' )
        {
          if ( JFile::exists($tpl_path.DS.$file) ) 
          {
            $design_exists = true;
            echo '<p class="yaml_msg">'.JText::_('YAML CANT IMPORT DESIGN EXISTS').'</p>';        
          }
        }
        
        if (!$design_exists)
        {          
          // Check html overwrites exists
          if ($mainfolder == 'html' && $subfolder && $subfolder != 'index' )
          {
              $html_exists = true;
          }  
          
          // Check plugins exists
          if ($mainfolder == 'plugins' && $subfolder )
          {
              $plugin_exists[$subfolder] = true;
          }
        }  
      }
      
      if (!$design_exists)
      {
        echo '<br />';
        echo '<form action="index3.php" method="post" name="copyImportForm" id="copyImportForm" autocomplete="off">';
                
        if ($html_exists)
        {
          ?>
          <fieldset>
            <legend><?php echo JText::_( 'YAML IMPORT OVERWRITE AND BACKUP HTML' ); ?></legend>
            <p><?php echo JText::_( 'YAML IMPORT OVERWRITE AND BACKUP HTML DESC' ); ?></p>
            <?php echo JText::_( 'NO' ); ?><input checked="checked" type="radio" name="overwrite_html" value="0" />
            <?php echo JText::_( 'YES' ); ?><input type="radio" name="overwrite_html" value="1" />  
          </fieldset>        
          <?php 
        }
        
        if ($plugin_exists)
        {
          ?>
          <fieldset>
            <legend><?php echo JText::_( 'YAML IMPORT OVERWRITE AND BACKUP PLUGINS' ); ?></legend>
            <p><?php echo JText::_( 'YAML IMPORT OVERWRITE AND BACKUP PLUGINS DESC' ); ?></p>
            <?php 
            foreach ($plugin_exists as $plugin=>$value)
            {
              echo $plugin.': ';
              echo JText::_( 'NO' ); ?><input checked="checked" type="radio" name="overwrite_plugin[<?php echo $plugin; ?>]" value="0" /><?php 
              echo JText::_( 'YES' ); ?><input type="radio" name="overwrite_plugin[<?php echo $plugin; ?>]" value="1" /><br /><?php
            } ?>
          </fieldset>        
          <?php 
        }
        
        ?>
        <input type="hidden" name="task" value="process_import_copy" />
        <input type="hidden" name="template" value="<?php echo $template; ?>" />
        <input type="hidden" name="controller" value="fileControl" />    
        <input type="hidden" value="<?php echo $option; ?>" name="option"/>
        
        <?php
        foreach ($files as $file) 
        {
          echo '<input type="hidden" value="'.$file.'" name="files[]" />'."\n";
        }
        
        if ($plugin_exists || $html_exists) : ?>
          <div align="center" id="importbutton"></div>
        <?php endif; ?>        
        </form>
        
         <?php if ($plugin_exists || $html_exists) : ?>
          <script type="text/javascript">
            function viewButton() {
              jQuery(document).ready(function() {
                jQuery('#importbutton').html('<button class=\"bigform\" type=\"submit\"><?php echo JText::_( 'YAML IMPORT DESIGN BUTTON', 1 ); ?></button>');
              }); 
            }
            window.setTimeout( "viewButton()" ,600);
            
            jQuery(document).ready(function() {
              var form = document.adminForm;
              
              var options = { 
                  target:        'body',   // target element(s) to be updated with server response 
                  resetForm: true        // reset the form after successful submit 
              }; 
           
              jQuery('#copyImportForm').submit(function() { 
                  jQuery("#import_result").html('<br \/><p class="on"><?php echo JText::_('YAML IMPORT PROCESS WAIT'); ?><\/p>');
                  jQuery(this).ajaxSubmit(options); 
                  window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
                  return false; 
              }); 
            }); 
          </script>
        <?php else : ?>
          <script type="text/javascript">            
            jQuery(document).ready(function() {
              jQuery("#import_result").html('<br \/><p class="on"><?php echo JText::_('YAML IMPORT PROCESS WAIT'); ?><\/p>');                            
              var options = { 
                  target:        'body',   // target element(s) to be updated with server response 
                  resetForm: true,        // reset the form after successful submit 
                  success:       SuccessForm
              }; 
           
              jQuery("#copyImportForm").ajaxSubmit(options); 
            }); 
            
            function SuccessForm() {
              window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 500);            
            }
            
          </script>
        <?php endif;
      
      }    
    } 
    else 
    {
      echo JText::_('YAML FILE UPLOAD FAILED');
    }  
  }
  
  function process_import_copy( $files=array() ) 
  {
    global $option, $mainframe;
    
    $config =& JFactory::getConfig();
    $tmp_path = $config->getValue('config.tmp_path');
    
    $source = $tmp_path.DS.'import_yaml_design';
  
    $template = JRequest::getVar( 'template', false );
    $tpl_path = JPATH_SITE.DS.'templates'.DS.$template;  
    
    if (!$files) 
    {
      $files = JRequest::getVar( 'files', array() );
    }
    
    $overwrite_global = JRequest::getVar( 'overwrite_global', false );
    $global_file = $source.DS.'config'.DS.'_global.xml';
    
    $overwrite_html = JRequest::getVar( 'overwrite_html', false );
    $html_dir = $source.DS.'html';
    
    $overwrite_plugins = JRequest::getVar( 'overwrite_plugin', false );  
    $plugin_dir = $source.DS.'plugins';
    
    $i=0;
    foreach ($files as $file) 
    {
      if ( !$overwrite_global && $file == $global_file ) 
      {
       unset($files[$i]);
      }
      
      if ( !$overwrite_html ) 
      {
       if ( strpos($file, $html_dir.DS.'index')===false && strpos($file, $html_dir)!==false ) unset($files[$i]);
      }
      
      $backup_plugins = array();
      
      if ( is_array($overwrite_plugins) ) 
      {
       foreach ($overwrite_plugins as $plugin=>$overwrite) 
       {
         if ( !$overwrite && strpos($file, $plugin_dir.DS.$plugin)!==false ) 
         {
           unset($files[$i]);           
         } 
         elseif ($overwrite) 
         {
           $backup_plugins[] = $plugin;         
         }
       }
      }       
      
      $i++;      
    }    

    JError::setErrorHandling( E_ALL, 'ignore' );
    /* Backup files before copy/overwrite */
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
    $tmpl_path = JPATH_SITE;
    
    if ( $overwrite_global ) 
    {
      JFile::copy($tpl_path.DS.'config'.DS.'_global.xml', $tpl_path.DS.'config'.DS.'_global.xml.backup');
    }
    if ( $overwrite_html ) 
    {
      JFolder::copy($tpl_path.DS.'html', $tpl_path.DS.'html.backup');
      JFolder::delete($tpl_path.DS.'html.backup'.DS.'index');
    }
    if ( $overwrite_plugins ) 
    {
      foreach ($backup_plugins as $plugin) 
      {
        JFolder::copy($tpl_path.DS.'plugins'.DS.$plugin, $tpl_path.DS.'plugins'.DS.$plugin.'.backup');
      }
    }
    
    /* Copy Files */
    foreach ($files as $file) 
    {
      $file_dest = str_replace($source, JPATH_SITE.DS.'templates'.DS.$template, $file);
      
      if ( !JFolder::exists(dirname($file_dest)) ) 
      {
        JFolder::create( dirname($file_dest) );
      }
      
      JFile::copy($file, $file_dest);
    }    
    
    $mainframe->enqueueMessage( JText::_( 'YAML DESIGN INSTALLATION SUCCESS' ) );
    $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');      
  }
  
  function rename_template() 
  {
    global $option;
    
    $template = JRequest::getVar('template_name', '');           
    ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML RENAME TEMPLATE TITLE' ); ?> (<?php echo $template; ?>)</div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_template_name"><?php echo JText::_( 'YAML ENTER NEW TEMPLATE NAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_template_name" value="" />
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="do_rename_template();" type="button"><?php echo JText::_( 'YAML RENAME TEMPLATE NOW' ); ?></button>
      </div>
      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function do_rename_template() {
        var form = document.adminForm;                
        if (form.new_template_name.value=='') {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER TEMPLATE NAME', 1 ); ?>');
          form.new_template_name.focus();
        } else {
          submitbutton('do_rename_template');
          jQuery("button.bigform").parent().html('<p class="on"><?php echo JText::_( 'YAML PLEASE WAIT', 1 ); ?>.</p>');
          jQuery(window).unload( function () {
            window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
          });
        }
      }
    </script>
    <?php
  }
  
  function do_rename_template() 
  {
    global $option, $mainframe;
    
    $template_name = JRequest::getVar('template', '');
    $new_template_name = JRequest::getVar('new_template_name', '');
    
    if ($template_name && $new_template_name) 
    {
      $tpl_path_old = JPATH_SITE.DS.'templates'.DS.$template_name;
      $tpl_path_new = JPATH_SITE.DS.'templates'.DS.$new_template_name;
      $tpl_xml = JPATH_SITE.DS.'templates'.DS.$template_name.DS.'templateDetails.xml';
      
      if (JFolder::exists($tpl_path_new)) 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML TEMPLATE EXITS' ), 'error' );
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');        
      } 
      else 
      {      
        // Rename name in template xml
        $xmlcontent = JFile::read($tpl_xml);
        $xmlcontent = str_replace('<name>'.$template_name.'</name>', '<name>'.$new_template_name.'</name>', $xmlcontent);
        
        // Rename Language files
        $xmlcontent = str_replace('tpl_'.$template_name.'.ini', 'tpl_'.$new_template_name.'.ini', $xmlcontent);
        
        // Write new content in xml
        JFile::write($tpl_xml, $xmlcontent);
        
        // Rename template folder
        JFolder::move($tpl_path_old, $tpl_path_new);
        
        // Rename language files
        $langfiles = JFolder::files(JPATH_SITE.DS.'language', 'tpl_'.$template_name.'.ini$', true, true);
        foreach($langfiles as $langfile) 
        {
          $langfile_new = str_replace('tpl_'.$template_name.'.ini', 'tpl_'.$new_template_name.'.ini', $langfile);          
          JFile::move($langfile, $langfile_new);        
        }
                
        // Activate renamed template in database
        JYAML::activateTemplate($new_template_name, false);
            
        $mainframe->enqueueMessage( JText::_( 'YAML RENAME TEMPLATE SUCCESS' ) );
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');  
      }
    }    
  }  
  
  function copy_template() 
  {
    global $option;
    
    $template = JRequest::getVar('template_name', '');
       
    ?>
    <form action="index.php" method="post" name="adminForm" autocomplete="off">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML COPY TEMPLATE TITLE' ); ?> (<?php echo $template; ?>)</div>
      </fieldset>  
      <br /><br />
      <label class="bigform" for="new_template_name"><?php echo JText::_( 'YAML ENTER TEMPLATE NAME' ); ?>:</label><br />
      <input class="bigform" type="text" name="new_template_name" value="" />
      <br /><br /><br />
      <div align="center">
        <button class="bigform" onclick="do_copy_template();" type="button"><?php echo JText::_( 'YAML COPY TEMPLATE NOW' ); ?></button>
      </div>
      
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <script type="text/javascript">
      function do_copy_template() {
        var form = document.adminForm;                
        if (form.new_template_name.value=='') {
          alert('<?php echo JText::_( 'YAML PLEASE ENTER TEMPLATE NAME', 1 ); ?>');
          form.new_template_name.focus();
        } else {
          submitbutton('do_copy_template');
          jQuery("button.bigform").parent().html('<p class="on"><?php echo JText::_( 'YAML PLEASE WAIT', 1 ); ?>.</p>');
          jQuery(window).unload( function () {
            window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);
          });
        }
      }
    </script>
    <?php
  }
  
  function do_copy_template() 
  {
    global $option, $mainframe;
    
    $template_name = JRequest::getVar('template', '');
    $new_template_name = JRequest::getVar('new_template_name', '');
    
    if ($template_name && $new_template_name) 
    {
      $tpl_path_old = JPATH_SITE.DS.'templates'.DS.$template_name;
      $tpl_path_new = JPATH_SITE.DS.'templates'.DS.$new_template_name;
      $tpl_xml = JPATH_SITE.DS.'templates'.DS.$new_template_name.DS.'templateDetails.xml';
      
      if (JFolder::exists($tpl_path_new)) 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML TEMPLATE EXITS' ), 'error' );
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');        
      } 
      else 
      {      
        // Copy template folder
        JFolder::copy($tpl_path_old, $tpl_path_new);
        
        // Rename name in template xml
        $xmlcontent = JFile::read($tpl_xml);
        $xmlcontent = str_replace('<name>'.$template_name.'</name>', '<name>'.$new_template_name.'</name>', $xmlcontent);
        
        // Rename Language files
        $xmlcontent = str_replace('tpl_'.$template_name.'.ini', 'tpl_'.$new_template_name.'.ini', $xmlcontent);
        
        // Write new content in xml        
        JFile::write($tpl_xml, $xmlcontent);
        
        // Copy language files
        $langfiles = JFolder::files(JPATH_SITE.DS.'language', 'tpl_'.$template_name.'.ini$', true, true);
        foreach($langfiles as $langfile) 
        {
          $langfile_new = str_replace('tpl_'.$template_name.'.ini', 'tpl_'.$new_template_name.'.ini', $langfile);          
          JFile::copy($langfile, $langfile_new);        
        }
        
        $mainframe->enqueueMessage( JText::_( 'YAML COPY TEMPLATE SUCCESS' ) );
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');  
      }
    }
  }
  
  function uploadTemplatePlugin()
  {
    global $option;
    
    $template = JRequest::getVar('template', '');
    
    ?>
    <form action="index3.php" method="post" name="install_pglForm" id="install_pglForm" autocomplete="off" enctype="multipart/form-data">
      <fieldset>
          <div style="float: right;">
            <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML INSTALL TEMPLATE PLUGIN TITLE' ); ?> (<?php echo $template; ?>)</div>
      </fieldset>  
          
      <br />
      <label class="bigform" for="importfile"><?php echo JText::_( 'YAML SELECT TEMPLATE PLUGIN FILE' ); ?>:</label><br />
      <input class="bigform" size="30" type="file" name="file" id="file" value="" />
      <br /><br /><br /><br />

      <div align="center">
        <button class="bigform" type="submit"><?php echo JText::_( 'YAML UPLOAD TEMPLATE PLUGIN' ); ?></button>
      </div>  
      
      <input type="hidden" name="task" value="installTemplatePlugin" />
      <input type="hidden" name="template" value="<?php echo $template; ?>" />
      <input type="hidden" name="controller" value="fileControl" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
    </form>
    
    <div id="install_result"></div>
    
    <script type="text/javascript">
      jQuery.noConflict();
      (function($) { 
        $(function() {          
          // prepare the form when the DOM is ready 
          $(document).ready(function() { 
            var options = { 
                target:    '#install_result',
                success:   validateResponse,
                resetForm: true
            }; 
         
            $('#install_pglForm').submit(function() { 
                $("#install_result").text('');
                $("#install_result").append('<br \/><p class="on"><?php echo JText::_('YAML UPLOAD PROCESS WAIT'); ?><\/p>');
                $(this).ajaxSubmit(options); 
                return false; 
            });
            
            function validateResponse(responseText, statusText){
              if(responseText.indexOf('----SUCCESS----') != '-1' ){
                window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>&task=ymsg&message=<?php echo JText::_('YAML PLUGIN INSTALL SUCCESS'); ?>\')', 200);
              }
            } 
          });
        });
      })(jQuery);  
    </script>
    <?php    
  }
  
  function installTemplatePlugin()
  {
    global $mainframe;
    if ( !isset($_FILES['file']) ) {
      echo '<p class="off">'.JText::_('YAML PLEASE SELECT FILE').'</p>';
      $mainframe->close();
    }
    $check = true;
    $warns = array();
    
    $template = JRequest::getVar( 'template', 'hm_yaml' );
    $plg_path = JPATH_SITE.DS.'templates'.DS.$template.DS.'plugins';  
    
    $config =& JFactory::getConfig();
    $tmp_path = $config->getValue('config.tmp_path');
    
    $tmp_dest_folder = $tmp_path.DS.'install_yaml_plugin';
    $tmp_dest_file   = $tmp_path.DS.$_FILES['file']['name'];
    
		if ( JFolder::exists($tmp_dest_folder) ) { JFolder::delete($tmp_dest_folder); }
		if ( JFile::exists($tmp_dest_file) ) { JFile::delete($tmp_dest_file); }
		
		move_uploaded_file($_FILES['file']['tmp_name'], $tmp_dest_file);
		
		jimport('joomla.filesystem.archive');
		JArchive::extract($tmp_dest_file, $tmp_dest_folder);
		
		$files = JFolder::files($tmp_dest_folder, '', true, true);
		$filelist = JFolder::files($tmp_dest_folder, '', true, false); 
				
		if (!$files) 
		{
		  echo '<p class="off">'.JText::_('YAML NO FILES FOUND').'</p>';
			return false;
		}

    $xmls = JFolder::files($tmp_dest_folder, 'xml$', false, false, array('config.xml'));
		$folder = $xmls[0];
		$folder = JFile::stripExt($folder);

		// Check main Files exists
		if(!in_array($folder.'.xml', $filelist)) { $check = false; $warns[] = '<li>'.JText::_('YAML PGL INSTALL WARN XML').'</li>'; }
		if(!in_array($folder.'.php', $filelist)) { $check = false; $warns[] = '<li>'.JText::_('YAML PGL INSTALL WARN PHP').'</li>'; }
		if(!in_array('config.xml', $filelist))   { $check = false; $warns[] = '<li>'.JText::_('YAML PGL INSTALL WARN CONFIG').'</li>'; }		

		if (!$check) 
		{
			echo '<p class="warn off">'.JText::_('YAML PGL INSTALL WARN').'<ul>'.implode(' ', $warns).'</ul></p>';
			return false;
		} 
		else 
		{      
			if (JFolder::copy($tmp_dest_folder, $plg_path.DS.$folder)) 
			{
				echo '<span style="display:none">----SUCCESS----</span>';
			} 
			else 
			{
				echo '<span style="display:none">----FAILED----</span>';
			}
			
			if ( JFolder::exists($tmp_dest_folder) )
			{
			  JFolder::delete($tmp_dest_folder);  
			}
			
			return true;			
		}    
  
  }  
}
?>