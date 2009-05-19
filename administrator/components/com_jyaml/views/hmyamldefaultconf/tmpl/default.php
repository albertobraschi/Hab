<?php 
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: default.php 423 2008-07-01 11:44:05Z hieblmedia $
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

?>
<div id="yaml_loading"><p><?php echo JText::_( 'YAML PLEASE WAIT' ); ?></p></div>
<script type="text/javascript">
  <!-- /* Reset Popup */
  jQuery(document).ready(function () {
    var css = {'width':'auto', 'height':'auto', 'left':'50%', 'top':'50%', 'right':'auto', 'bottom':'auto' }
    jQuery(parent.document).find("#sbox-window").css(css);    
  });
  -->
</script>

<!-- HTML dummys: start -->
<?php echo JYAML::getHTMLDummys(); ?>
<!-- HTML dummys: end -->

<div id="yaml_def_conf">
  <fieldset>
      <div style="float: right;">
        <button onclick="submitbutton('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="submitbutton('apply');" type="button"><?php echo JText::_( 'YAML APPLY' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
      <div class="configuration"><?php echo JText::_( 'YAML DEFAULT CONF TITLE' ); ?> (<?php echo $this->template_name; ?>)</div>
  </fieldset>
  
  <form action="index.php" method="post" name="adminForm">
  <!-- Layout: start -->
  <div class="yslider-off">
    <div class="slide-title slide-title-open">
      <?php echo JYAML::docLink('config:layout'); ?>
      <?php echo JText::_( 'YAML CONF LAYOUT TITLE' ); ?>
    </div>    
    
    <div class="ycontent-off">
      <label class="yaml_label_def" for="design"><?php echo JText::_( 'YAML CONF DESIGN LABEL' ); ?></label>
      <?php echo JHTML::_( 'select.genericlist', $this->designlist, 'design' , '', 'value', 'text', $this->conf['design'], 'design' ); ?>
      <br style="clear:both;" />
    </div>
  </div>
  <!-- Layout: end -->
  
  <!-- Misc config: start -->  
  <div class="yslider-off">   
    <div class="slide-title slide-title-open">
      <?php echo JYAML::docLink('config:misc'); ?>
      <?php echo JText::_( 'YAML CONF MISC TITLE' ); ?>
    </div>
    
    <div class="ycontent-off">
      <?php 
      $radiolist[] = new JYAMLmakeSelect('1', JText::_( 'YAML ON TXT' ));  
      $radiolist[] = new JYAMLmakeSelect('0', JText::_( 'YAML OFF TXT' ));
      ?>
      <label class="yaml_label_def" for="debug"><?php echo JText::_( 'YAML CONF DEBUG LABEL' ); ?></label>
      <?php echo JHTML::_( 'select.radiolist', $radiolist, 'debug', '', 'value', 'text', $this->conf['debug']); ?><br style="clear:both;" />
    </div>
  </div>
  <!-- Misc config: end --> 
    
    <input type="hidden" name="option" value="<?php echo $option; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="defaultConfig" />
    <input type="hidden" name="template_name" value="<?php echo $this->template_name; ?>" />
  </form>
  
  <fieldset>
      <div style="float: right;">
        <button onclick="submitbutton('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="submitbutton('apply');" type="button"><?php echo JText::_( 'YAML APPLY' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
  </fieldset>
  
</div>
