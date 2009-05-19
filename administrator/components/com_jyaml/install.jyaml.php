<?php 
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: install.jyaml.php 423 2008-07-01 11:44:05Z hieblmedia $
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

$db  =& JFactory::getDBO();

/**
 * Delete database link like Core-Extension to hide Component in Menu-Manager
**/    
$query = "UPDATE #__components SET link='' WHERE link='option=com_jyaml' LIMIT 1";
$db->setQuery( $query );
$db->query();


/**
 * Check activate_parent Parameter id enabled. If not set to 1 or add param activate_parent=1
**/ 
$query = "SELECT id, params FROM #__modules WHERE module='mod_mainmenu'";
$db->setQuery( $query );
$menus = $db->loadObjectList();
foreach($menus as $key=>$menu) {
  if( strpos($menu->params, "activate_parent") ) 
  {
    if ( strpos($menu->params, "activate_parent=0") ) 
    {    
      $menus[$key]->params = str_replace("activate_parent=0", "activate_parent=1", $menu->params);
    } else {
      $menus[$key]->params .= "\nactivate_parent=1";    
    }
  }
}

/**
 * Check mod_mainmenu in nav_main exists 
**/
$query = "SELECT * FROM #__modules WHERE module='mod_mainmenu' AND position='nav_main'";
$db->setQuery( $query );
$result = $db->loadResult();

// If not insert
if (!$result) {
  // Insert mod_mainmenu with nav_main
  $params = "menutype=mainmenu\nmenu_style=list\nstartLevel=0\nendLevel=1\nshowAllChildren=0\nshow_whitespace=0\ncache=1\nmoduleclass_sfx=_menu\nmaxdepth=1\nactivate_parent=1";
  $query = "INSERT INTO #__modules " .
           "(id, title, content, ordering, position, checked_out, checked_out_time, published, module, numnews, access, showtitle, params, iscore, client_id, control) " .
           "VALUES (null, 'Main Menu (level0) - JYAML', '', '0', 'nav_main', '0', '', '1', 'mod_mainmenu', '0', '0', '0', '".$params."', '0', '0', '')"
           ;
  $db->setQuery( $query );
  $db->query();
  
  // Show on all pages
  $query = "INSERT INTO #__modules_menu " .
           "(moduleid, menuid) " .
           "VALUES ('".$db->insertid()."', '0')"
           ;
  $db->setQuery( $query );
  $db->query();
}

/**
 * Check mod_mainmenu in topnav exists 
**/
$query = "SELECT * FROM #__modules WHERE module='mod_mainmenu' AND position='topnav'";
$db->setQuery( $query );
$result = $db->loadResult();

// If not insert
if (!$result) {
  // Insert mod_mainmenu with nav_main
  $params = "menutype=topmenu\nmenu_style=list\nstartLevel=0\nendLevel=1\nshowAllChildren=0\nshow_whitespace=0\ncache=1\nmoduleclass_sfx=_menu\nmaxdepth=1\nactivate_parent=1";
  $query = "INSERT INTO #__modules " .
           "(id, title, content, ordering, position, checked_out, checked_out_time, published, module, numnews, access, showtitle, params, iscore, client_id, control) " .
           "VALUES (null, 'Top Menu (level0) - JYAML', '', '0', 'topnav', '0', '', '1', 'mod_mainmenu', '0', '0', '0', '".$params."', '0', '0', '')"
           ;
  $db->setQuery( $query );
  $db->query();
  
  // Show on all pages
  $query = "INSERT INTO #__modules_menu " .
           "(moduleid, menuid) " .
           "VALUES ('".$db->insertid()."', '0')"
           ;
  $db->setQuery( $query );
  $db->query();
}

?>