<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

if(JPluginHelper::isEnabled('authentication', 'openid')) :
  $lang = &JFactory::getLanguage();
  $lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
  $langScript =   'var JLanguage = {};'.
          ' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
          ' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
          ' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
          ' var comlogin = 1;';
  $document = &JFactory::getDocument();
  $document->addScriptDeclaration( $langScript );
  JHTML::_('script', 'openid.js');
endif; 

if ( $this->params->get( 'show_login_title' ) ) : ?>
<?php endif; ?>
