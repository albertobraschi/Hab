<?php
/**
 * @package JoomlaPack
 * @copyright Copyright (c)2006-2008 JoomlaPack Developers
 * @license GNU General Public License version 2, or later
 * @version $id$
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

/**
 * MVC View for DEF
 *
 */
class JoomlapackViewDef extends JView
{
	function display()
	{
		// Add toolbar buttons
		JToolBarHelper::title(JText::_('JOOMLAPACK').': <small><small>'.JText::_('DEF').'</small></small>');
		JToolBarHelper::back('Back', 'index.php?option='.JRequest::getCmd('option'));
		JToolBarHelper::spacer();
		JoomlapackHelperUtils::addLiveHelp('def');
		
		parent::display();
	}
}