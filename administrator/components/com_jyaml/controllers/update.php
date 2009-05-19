<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: update.php 471 2008-07-27 17:29:39Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 471 $
 * @lastmodified    $Date: 2008-07-27 19:29:39 +0200 (So, 27. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class hmyamlControllerUpdate extends hmyamlController
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
   * Update function 
   * @return void
  **/
  function make_update()
  {
    global $option, $mainframe;
    
    jimport('joomla.client.helper');
    $ftp_requiered = !JClientHelper::hasCredentials('ftp');
    
    ?>    
    <fieldset>
        <div class="configuration"><?php echo JText::_( 'YAML UPDATE TITLE' ); ?></div>
    </fieldset>

    <?php if($ftp_requiered) : ?>
      <div class="designbox ftpwarn floatbox">
        <div class="legend"><?php echo JText::_('YAML FTP TITLE'); ?></div>
        <div class="content">
          <strong><?php echo JText::_('YAML FTP DESC'); ?></strong>
          
          <form action="index.php" name="ftpForm" id="ftpForm" method="post">
            <p>
              <label for="username">FTP-<?php echo JText::_('Username'); ?>:</label><br />
              <input type="text" id="username" name="username" class="input_box" size="70" value="<?php echo $mainframe->getCfg( 'ftp_user' ); ?>" />
            </p>
            
            <p>
              <label for="password">FTP-<?php echo JText::_('Password'); ?>:</label><br />
              <input type="password" id="password" name="password" class="input_box" size="70" value="" />
            </p>
            
            <p>
              <input type="submit" value="<?php echo JText::_('YAML FTP SET BUTTON'); ?>" />
            </p>
            
            <input type="hidden" name="return" value="index3.php?option=<?php echo $option; ?>&controller=update&task=make_update" />
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="ftpLogin" />
            <input type="hidden" name="controller" value="hmyaml" />
          </form>
        </div>
      </div>
    <?php else : ?>
    
      <p><?php echo JText::_('YAML DOWNLOAD UPDATE DESC'); ?></p>
      
      <div align="center">
        <button id="startUpdate" onclick="startUpdate()" type="button"><?php echo JText::_( 'YAML START UPDATE BUTTON' ); ?></button>
      </div>


      <div id="updateResult"></div>
      
      <script type="text/javascript">
        function startUpdate() {       
          jQuery("#startUpdate").hide();
          pd = jQuery(parent.document);
          pd.find("#sbox-btn-close").hide();
          jQuery("#updateResult").html('<div id="updateResult"><h3><?php echo JText::_('YAML DOWNLOAD UPDATE FILES'); ?></h3><img src="<?php echo JURI::base(); ?>/components/<?php echo $option; ?>/images/ajax-loader.gif" width="31" height="31" alt="Loading..." /></div>');
          jQuery("#updateResult").load("index3.php?option=<?php echo $option; ?>&controller=update&task=processUpdate");       
        }
      </script>    
    <?php 
    
    endif;  
  }
  
  function getUpdateFiles($code)
  {
    return eval($code);
  }
  
  function processUpdate()
  {
    global $option, $mainframe;
    
    $allow_url_fopen=ini_get('allow_url_fopen');
    
    if ( !$allow_url_fopen ) 
    {      
      echo '<p class="yaml_msg">allow_url_fopen on PHP is disabled on your host. Please make a manual Update.</p>';
      $mainframe->close();  
    }
    else
    {  
      $data = JYAML::getVersionInfo(true);
      $version = $data['i-j-version'].'-'.$data['i-j-build'];
      
      /* Update URL */
      $url = JYAML::getDownloadURL('updater').'?version='.rawurlencode($version).'&full=1';
      
      $updateFiles = file_get_contents($url);      
      $files = $this->getUpdateFiles($updateFiles);
    }
    
    $config =& JFactory::getConfig();
    $tmp_path   = $config->getValue('config.tmp_path');
    
    jimport('joomla.filesystem.archive');
    
    $dataold = JYAML::getVersionInfo(true);
    
    /* Full Update */
    if (isset($files['full']))
    {
      $extractPath = $tmp_path.DS.'jyaml_update';
      $filename = basename($files['full']);
		
      /* Delete temp folder and create empty */
      if (JFolder::exists($extractPath)) 
      {
        JFolder::delete($extractPath);
        JFolder::create($extractPath);
      }
      if (JFile::exists($tmp_path.DS.$filename)) 
      {
        JFile::delete($tmp_path.DS.$filename);
      }    
    
      $content = file_get_contents($files['full']);
      
      if (JFile::exists($tmp_path.DS.$filename)) 
      {
        JFile::delete($tmp_path.DS.$filename);
      }
      if (!JFile::write($tmp_path.DS.$filename, $content) )
      {
        echo '<p class="off">'.JText::_('YAML CANT EXTRACT SAVE UPDATE FILE IN TMP').'</p>';
      }
      
      if(!JArchive::extract($tmp_path.DS.$filename, $extractPath)) {
        echo '<p class="off">'.JText::_('YAML CANT EXTRACT UPDATE FILE').'</p>';
      } 
      
      // Call update script if available
      $update_script = $extractPath.DS.'jyaml_update_script.php';
      if (JFile::exists($update_script)) 
      {
        include_once($update_script);
        $update_result = new jyamlUpdateScript();
        
        // Delete update script
        JFile::delete($update_script);
        
        if (!$update_result) 
        {
          echo '<p class="off">'.JText::_('YAML RUN UPDATE SCRIPT FAIL').'</p>';
        }
      }   
               
      $files = array();
      $files = JFolder::files($extractPath, '.', true, true);
      
      $error_folders = array();
      $error_files = array();
      
      foreach ($files as $file)
      {
      
        $file_relative = str_replace($extractPath.DS, '', $file);
        
        $dest = JPATH_ROOT.DS.$file_relative;
        
        if ( !JFolder::exists( dirname($dest) ) ) 
        {
          if( !JFolder::create(dirname($dest)) )
          {
            $error_folders[] = dirname($dest);
          }
        }
        
        if(!JFile::copy($file, $dest)) 
        {
          $error_files[] = DS.$file_relative;
        }
      }
      
      if ($error_folders)   {
        echo '<p class="off">'.JText::_('YAML CANT CREATE DIRECTORY').'</p>';
        echo '<ul>';
        foreach ($error_folders as $ef) {
          echo '<li>'.$ef.'</li>';
        }
        echo '</ul>';
      }
      
      if ($error_files)   {
        echo '<p class="off">'.JText::_('YAML CANT UPDATE-CREATE FILE').'</p>';
        echo '<ul>';
        foreach ($error_files as $ef) {
          echo '<li>'.$ef.'</li>';
        }
        echo '</ul>';
      }
      
      if (!$error_folders && !$error_files) {
        echo '<h2 class="on" style="text-align:center;">'.JText::_('YAML UPDATE SUCCESSFULLY').'</h2>';
      }

      /* Delete temp file and folder */
      if (JFolder::exists($extractPath)) 
      {
        JFolder::delete($extractPath);
      }
      if (JFile::exists($tmp_path.DS.$filename)) 
      {
        JFile::delete($tmp_path.DS.$filename);
      }

      $data = JYAML::getVersionInfo(true);
      echo '<hr />';
      echo '<p class="off" style="font-weight:normal;">'.JText::_('YAML UPDATE OLD VERSION').': '.$dataold['i-j-version'].' Build: '.$dataold['i-j-build'].'<p>';  
      echo '<p class="on" style="font-weight:bold;">'.JText::_('YAML UPDATE NEW VERSION').': '.$data['i-j-version'].' Build: '.$data['i-j-build'].'<p>';    
      echo '<hr />';  
          
      ?>
      <fieldset>
          <div align="center">
            <button onclick="window.parent.location.replace('index.php?option=<?php echo $option; ?>');" type="button"><?php echo JText::_( 'YAML CLOSE UPDATE WINDOW' ); ?></button>
          </div>
      </fieldset>
      <?php    
    }    
    
    $mainframe->close();  
  }
  
}
?>