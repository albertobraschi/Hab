<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: plugins.php 455 2008-07-21 17:03:26Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 455 $
 * @lastmodified    $Date: 2008-07-21 19:03:26 +0200 (Mo, 21. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class hmyamlControllerplugins extends hmyamlController
{
  /**
   * Method to display the view
   * @access  public
   */
  function display()
  {  
    parent::display();
  }
  
  function edit() 
  { 
    global $option;
     
    $template_name = JRequest::getVar('template_name');
    $plugin_name = JRequest::getVar('plugin');
    
    $plugin = JYAML::getPlugins($template_name, $plugin_name); 
    
    ?>
    <fieldset>
      <div style="float: right;">
        <button onclick="submitbutton('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
      <div class="configuration"><?php echo JText::_( 'YAML PLUGIN CONF TITLE' ); ?></div>
    </fieldset>
    
    <p style="overflow:hidden; width:100%;">
      <span style="float:right"><?php echo JYAML::docLink('plugin:'.$plugin->plugin); ?></span>
      <?php echo JText::_( 'YAML PLUGIN VERSION' ).': '.$plugin->version; ?>
    </p>    
    
    <form action="index.php" method="post" name="adminForm">
      <div class="param-form">
        <?php echo $plugin->paramOutput; ?>
      </div>
      
      <input type="hidden" name="option" value="<?php echo $option; ?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="controller" value="plugins" />
      <input type="hidden" name="plugin_name" value="<?php echo $plugin->plugin; ?>" />
      <input type="hidden" name="template_name" value="<?php echo $template_name; ?>" />
      <input type="hidden" name="plugin_title" value="<?php echo $plugin->name; ?>" />
    </form>
    
    <p><?php echo $plugin->description; ?></p>
    
    <fieldset>
      <div style="float: right;">
        <button onclick="submitbutton('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
      <?php if(!$plugin->isCore) : ?><button class="off" type="button" onclick="showPromt('delete');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);"><?php echo JText::_( 'YAML PLUGIN DELETE BUTTON' ); ?></button><?php endif; ?>
    </fieldset>
    
    <?php if(!$plugin->isCore) : ?>
    <script type="text/javascript">
      function showPromt(pressbutton){
        var a = confirm('<?php echo JText::_( 'YAML CONFIRM DELETE PLUGIN' )."\\nPlugin: ".$plugin->name; ?>');
        if(a) submitbutton(pressbutton);
      }
    </script> 
    <?php endif;  
  }
  
  function delete() 
  {
    global $option, $mainframe;
    
    $plugin = JRequest::getVar('plugin_name');
    $plugin_title = JRequest::getVar('plugin_title');
    $template_name = JRequest::getVar('template_name');
    
    if ($plugin && $template) return false;  

    if ( JFolder::delete(JPATH_SITE.DS.'templates'.DS.$template_name.DS.'plugins'.DS.$plugin) ) {
      $mainframe->enqueueMessage( 'Plugin ('.$plugin_title.'): '.JText::_( 'YAML DELETE SUCCESS' ) );  
    }
    else
    {
      $mainframe->enqueueMessage( 'Plugin ('.$plugin_title.'): '.JText::_( 'YAML DELETE ERROR' ), 'error' );      
    }
    $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');
  }
  
  function save() 
  {
    global $option, $mainframe;
    
    $plugin_name   = JRequest::getVar('plugin_name', false);
    $template_name = JRequest::getVar('template_name', false);
    $plugins       = JRequest::getVar('plugins', false);    
    $paramString   = '';
    
    if ($plugin_name && $template_name) 
    {
      $plugin = JYAML::getPlugins($template_name, $plugin_name); 
      //$plugin->configfile
      
      foreach ($plugins[$plugin_name] as $name=>$data) 
      {
        $paramString .= $name."=".$data."\n";
      }
      
      if ( JFile::write($plugin->configfile, $paramString) ) 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML SAVED SUCCESS' ) );  
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=hmyaml&task=wait');
      } 
      else 
      {
        $mainframe->enqueueMessage( JText::_( 'YAML SAVED FAILED' ), 'error' );  
        $mainframe->redirect( JURI::base() . 'index3.php?option='.$option.'&controller=plugins&task=&template_name='.$template_name.'&plugin='.$plugin_name );
      }
    }  
  }
}
?>