<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: hmyaml.php 423 2008-07-01 11:44:05Z hieblmedia $
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

class hmyamlControllerhmyaml extends hmyamlController
{
  /**
   * Method to display the view
   * @access  public
   */
  function display()
  {  
    parent::display();
  }
  
  function wait() 
  {     
    echo '<p>'.JText::_('YAML WAIT CLOSE POPUP').'</p>';
  }
  
  function ftpLogin()
  {
    global $mainframe, $option;
    
    // Set FTP credentials, if given
    jimport('joomla.client.helper');
    $error = JClientHelper::setCredentialsFromRequest('ftp');
    
    if ($error) {
      $mainframe->enqueueMessage( JText::_( 'YAML FTP LOGIN ERROR' ), 'error' );
    } 
    else 
    {
      $mainframe->enqueueMessage( JText::_( 'YAML FTP LOGIN SUCCESS' ) );
    }    
    
    if($return = JRequest::getVar('return', false, 'POST')) 
    {
      $mainframe->redirect($return);
    }
   
    parent::display();
  }
}
?>
