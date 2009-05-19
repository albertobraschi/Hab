<?php 
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: default_ftp_login.php 423 2008-07-01 11:44:05Z hieblmedia $
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
      
      <input type="hidden" name="option" value="<?php echo $option; ?>" />
      <input type="hidden" name="task" value="ftpLogin" />
      <input type="hidden" name="controller" value="hmyaml" />
    </form>
  </div>
</div>