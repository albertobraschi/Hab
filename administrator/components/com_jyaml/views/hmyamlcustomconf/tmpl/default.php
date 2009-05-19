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
<!-- HTML dummys: start -->
<?php echo JYAML::getHTMLDummys(); ?>
<!-- HTML dummys: end -->

<div id="yaml_def_conf">
  <fieldset>
      <div style="float: right;">
        <button onclick="save_conf('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="save_conf('apply');" type="button"><?php echo JText::_( 'YAML APPLY' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
      <div class="configuration"><?php echo JText::_( 'YAML CUSTOM CONF TITLE' ); ?> (<?php echo $this->design; ?> / <?php echo $this->filename; ?>)</div>
  </fieldset>
  
  <form action="index.php" method="post" name="adminForm" id="customConfigForm">
    <!-- Layout: start -->
    <div class="yslider">
      <div class="slide-title">
        <?php echo JYAML::docLink('config:layout'); ?>
        <?php echo JText::_( 'YAML CONF LAYOUT TITLE' ); ?>
      </div>
      
      <div class="ycontent">
        <label class="yaml_label_def3" for="switch_design"><?php echo JText::_( 'YAML CONF DESIGN LABEL' ); ?></label>
        <?php echo JHTML::_( 'select.genericlist', $this->designlist, 'switch_design' , '', 'value', 'text', $this->conf['design'], 'design' ); ?>
        <br style="clear:both;" />
        
        <label class="yaml_label_def3" for="html_file"><?php echo JText::_( 'YAML CONF HTMLFILE LABEL' ); ?>/html/index/<?php echo $this->design; ?>/</label>
        <?php $htmlfiles = JYAML::getHTMLFiles($this->html_list, true); ?>
        <?php echo JHTML::_( 'select.genericlist', $htmlfiles, 'html_file', '', 'value', 'text', $this->conf['html_file']); ?>
        
        <h4>
          <span class="hasTip" title="<?php echo JText::_( 'YAML CONF LAYOUT STYLESHEETS DESC' ); ?>"><?php echo JText::_( 'YAML CONF LAYOUT STYLESHEETS' ); ?></span>
        </h4>    
        
        <div class="layout_highlight">
          <?php $cssl = JYAML::getCSSLayouts('1col', '3', true, $this->design); ?>
          <label class="yaml_label_def4" for="layout_1col"><?php echo JText::_( 'YAML LAYOUT FILE COL1' );?>:</label>
          <span>patch_ / layout_<?php echo $cssl['html']; ?>.css</span> | 
          
          <input<?php echo $cssl['checked']; ?> type="checkbox" name="layout_1col_global" id="layout_1col_global" value="1" />
          <label for="layout_1col_global"><?php echo JText::_( 'YAML PARENT CONFIG' ); ?></label>
        </div>
        
        <div class="layout_highlight">
          <?php $cssl = JYAML::getCSSLayouts('2col_1', '13', true, $this->design); ?>
          <label class="yaml_label_def4" for="layout_1col"><?php echo JText::_( 'YAML LAYOUT FILE COL1' );?>:</label>
          <span>patch_ / layout_<?php echo $cssl['html']; ?>.css</span> | 
          
          <input<?php echo $cssl['checked']; ?> type="checkbox" name="layout_2col_1_global" id="layout_2col_1_global" value="1" />
          <label for="layout_2col_1_global"><?php echo JText::_( 'YAML PARENT CONFIG' ); ?></label>
        </div>
        
        <div class="layout_highlight">
          <?php $cssl = JYAML::getCSSLayouts('2col_2', '32', true, $this->design); ?>
          <label class="yaml_label_def4" for="layout_1col"><?php echo JText::_( 'YAML LAYOUT FILE COL1' );?>:</label>
          <span>patch_ / layout_<?php echo $cssl['html']; ?>.css</span> | 
          
          <input<?php echo $cssl['checked']; ?> type="checkbox" name="layout_2col_2_global" id="layout_2col_2_global" value="1" />
          <label for="layout_2col_2_global"><?php echo JText::_( 'YAML PARENT CONFIG' ); ?></label>
        </div>
        
        <div class="layout_highlight">
          <?php $cssl = JYAML::getCSSLayouts('3col', '132', true, $this->design); ?>
          <label class="yaml_label_def4" for="layout_1col"><?php echo JText::_( 'YAML LAYOUT FILE COL1' );?>:</label>
          <span>patch_ / layout_<?php echo $cssl['html']; ?>.css</span> | 
          
          <input<?php echo $cssl['checked']; ?> type="checkbox" name="layout_3col_global" id="layout_3col_global" value="1" />
          <label for="layout_3col_global"><?php echo JText::_( 'YAML PARENT CONFIG' ); ?></label>
        </div>

      </div>
    </div>
    <!-- Layout: end -->
   
    <!-- Content column settings: start -->
    <div class="yslider"> 
      <div class="slide-title">
        <?php echo JYAML::docLink('config:column-settings'); ?>
        <?php echo JText::_( 'YAML CONF CONTENT TITLE' ); ?>
      </div>
        
      <div class="ycontent">        
        <!-- #col1: -->
        <fieldset class="conf-columns">
          <div class="conf-columns-title">#col1_content</div>
          <div class="conf-columns-content">
            <div id="yaml_col1" class="ySortable">
              <?php echo $this->conf['col1_clearing']; ?>
              <?php echo $this->conf['col1_config']; ?>
            </div>
            &nbsp;<a class="addPosition_col1">[<?php echo JText::_( 'YAML ADD POS' ); ?>]</a>
          </div>
        </fieldset>
        
        <!-- #col2: -->
        <fieldset class="conf-columns">
          <div class="conf-columns-title">#col2_content</div>
          <div class="conf-columns-content">
            <div id="yaml_col2" class="ySortable">
              <?php echo $this->conf['col2_clearing']; ?>
              <?php echo $this->conf['col2_config']; ?>
            </div>
            &nbsp;<a class="addPosition_col2">[<?php echo JText::_( 'YAML ADD POS' ); ?>]</a>
          </div>
        </fieldset>
        
        <!-- #col3: -->
        <fieldset class="conf-columns">
          <div class="conf-columns-title">#col3_content</div>
          <div class="conf-columns-content">
            <div id="yaml_col3" class="ySortable">
              <?php echo $this->conf['col3_clearing']; ?>
              <?php echo $this->conf['col3_config']; ?>
            </div>
            &nbsp;<a class="addPosition_col3">[<?php echo JText::_( 'YAML ADD POS' ); ?>]</a>
          </div>
        </fieldset>
        
      </div>
    </div>
    <!-- Content column settings: end --> 
   
    <!-- Stylesheets: start -->   
    <div class="yslider">
      <div class="slide-title">
        <?php echo JYAML::docLink('config:stylesheets'); ?>
        <?php echo JText::_( 'YAML CONF STYLESHEETS' ); ?>
      </div>
  
      <div class="ycontent">
        <?php
        if ($this->conf_design['addstylesheet']) {
          echo '<em>'.JText::_( 'YAML LOADED STYLESHEETS' ).'</em><br />';
          foreach ($this->conf_design['addstylesheet'] as $file=>$attr) {
            if ($attr['source']=='design') {
              $folder = '/css/'. $this->design.' / ';
            } else {
              $folder = '/yaml / ';      
            }
            echo $folder.'<strong>'.$file.'</strong> (Browser: '.($attr['browser'] ? $attr['browser'] : 'all').')<br />';
          }
          echo '<br />';
        }
        ?>
        <div id="yaml_stylesheets" class="ySortable">
        <?php echo JYAML::getHeadFiles('addstylesheets', $this->conf['addstylesheet'], $this->explore_buttons[0]); ?>
        </div>
        <a class="addHeadFileStylesheet">[<?php echo JText::_( 'YAML ADD HEAD SYLESHEET' ); ?>]</a>
      </div>
    </div>
    <!-- Stylesheets: end --> 
    
    <!-- Scripts: start -->    
    <div class="yslider"> 
      <div class="slide-title">
        <?php echo JYAML::docLink('config:scripts'); ?>
        <?php echo JText::_( 'YAML CONF SCRIPTS' ); ?>
      </div>
      
      <div class="ycontent">
        <?php
        if ($this->conf_design['addscript']) {
          echo '<em>'.JText::_( 'YAML LOADED SCRIPTS' ).'</em><br />';
          foreach ($this->conf_design['addscript'] as $file=>$attr) {
          $folder = '/scripts / ';
            echo $folder.'<strong>'.$file.'</strong> (Browser: '.($attr['browser'] ? $attr['browser'] : 'all').')<br />';
          }
          echo '<br />';
        }
        ?>
        <div id="yaml_scripts" class="ySortable">
        <?php echo JYAML::getHeadFiles('addscripts', $this->conf['addscript'], $this->explore_buttons[1]); ?>
        </div>
        <a class="addHeadFileScript">[<?php echo JText::_( 'YAML ADD HEAD SCRIPT' ); ?>]</a>
      </div>
    </div>
    <!-- Scripts: end --> 
  
    <!-- Custom head: start -->    
    <div class="yslider">  
      <div class="slide-title">
        <?php echo JYAML::docLink('config:custom-head'); ?>
        <?php echo JText::_( 'YAML CONF CUSTOM HEAD' ); ?>
      </div>
      
      <div class="ycontent">
        <a class="startSyntaxEditor"><?php echo JText::_( 'YAML EDITOR TOGGLE' ); ?></a>        
        <textarea style="width:100%;height:100px;" name="addhead" id="yaml_custom_head_value"><?php echo $this->conf['addhead']; ?></textarea> 
        <?php
        $lang    =& JFactory::getLanguage();
        $langTag = explode( '-', $lang->getTag() );
        ?> 
      </div>   
      <script type="text/javascript">
        <!--
        jQuery(".startSyntaxEditor").click(function () {
          jQuery("#yaml_custom_head_value").addClass('codepress');
          jQuery("#yaml_custom_head_value").addClass('html');
          jQuery("#yaml_custom_head_value").height(200);
          
          jQuery(this).removeClass('startSyntaxEditor');
          jQuery(this).addClass('toggleSyntaxEditor');
          
          s = document.getElementsByTagName('script');
          for(var i=0,n=s.length;i<n;i++) {
            if(s[i].src.match('codepress.js')) {
              CodePress.path = s[i].src.replace('codepress.js','');
            }
          }
          t = document.getElementsByTagName('textarea');
          for(var i=0,n=t.length;i<n;i++) {
            if(t[i].className.match('codepress')) {
              id = t[i].id;
              t[i].id = id+'_cp';
              eval(id+' = new CodePress(t[i])');
              t[i].parentNode.insertBefore(eval(id), t[i]);
            } 
          }
          
          jQuery(this).unbind();
          initToggleEditor();
        });
        
        function initToggleEditor() {
          jQuery(".toggleSyntaxEditor").click(function () {
             yaml_custom_head_value.toggleEditor();
          });
        }
        -->
      </script>
    </div>
    <!-- Custom head: end --> 
  
    <!-- Own vars: start --> 
    <div class="yslider">     
      <div class="slide-title">
        <?php echo JYAML::docLink('config:own-vars'); ?>
        <?php echo JText::_( 'YAML CONF OWN VARS' ); ?>
      </div>
      
      <div class="ycontent">
        <?php echo JYAML::getOwnVars($this->conf['ownVars'], $this->conf_design['ownVars']); ?>
      </div>
    </div>
    <!-- Own vars: end -->  
    
    <!-- Plugins: start -->
    <div class="yslider">
      <div class="slide-title">
        <?php echo JYAML::docLink('config:plugins'); ?>
        <?php echo JText::_( 'YAML CONF PLUGINS' ); ?>
      </div>
      
      <div class="ycontent">
        <?php
        $plugins = JYAML::getPlugins($this->template_name, false, $this->conf['plugins'], $this->conf_design['plugins']);      
        
        foreach ($plugins as $plugin) {
          echo '<div class="yslider-sub">';
          $pgl_checked = '';
          if ( !isset($this->conf['plugins'][$plugin->plugin]) )   {
            $pgl_checked = ' checked="checked"';
          }      
          $pgl_global  = ' | <label for="pgl_'.$plugin->name.'">'.JText::_( 'YAML PARENT CONFIG' ).'</label>: ';
          $pgl_global .= '<input'.$pgl_checked.' type="checkbox" name="pgl_'.$plugin->name.'" class="pgl_global" value="1" />';
          echo '<div class="slide-title-sub">'.$plugin->name.$pgl_global.'</div>';
          echo '<div class="ycontent-sub">'.$plugin->paramOutput.'</div>';
          echo '</div>';
        }    
        ?>
      </div>  
    </div>
    <!-- Plugins: end -->
  
    <!-- Misc config: start --> 
    <div class="yslider">    
      <div class="slide-title">
        <?php echo JYAML::docLink('config:misc'); ?>
        <?php echo JText::_( 'YAML CONF MISC TITLE' ); ?>
      </div>
      
      <div class="ycontent">
        <?php 
        $radiolist[] = new JYAMLmakeSelect('1', JText::_( 'YAML ON TXT' ));  
        $radiolist[] = new JYAMLmakeSelect('0', JText::_( 'YAML OFF TXT' ));
        $radiolist[] = new JYAMLmakeSelect('', JText::_( 'YAML PARENT CONFIG' ));
        ?>
        <label class="yaml_label_def" for="debug"><?php echo JText::_( 'YAML CONF DEBUG LABEL' ); ?></label>
        <?php echo JHTML::_( 'select.radiolist', $radiolist, 'debug', '', 'value', 'text', $this->conf['debug']); ?><br style="clear:both;" />
      </div>
    </div>
    <!-- Misc config: end --> 
    
    <input type="hidden" name="option" value="<?php echo $option; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="customConfig" />
    <input type="hidden" name="filename" value="<?php echo $this->filename; ?>" />
    <input type="hidden" name="design" value="<?php echo $this->design; ?>" />
    <input type="hidden" name="template_name" value="<?php echo $this->template_name; ?>" />
  </form>
  
  <fieldset>
      <div style="float: right;">
        <button onclick="save_conf('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
        <button onclick="save_conf('apply');" type="button"><?php echo JText::_( 'YAML APPLY' ); ?></button>
        <button onclick="window.parent.document.getElementById('sbox-window').close();" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
      </div>
  </fieldset>
  
</div>

<script type="text/javascript">  
  function save_conf(pressbutton){        
    var check = true;
    if ( (jQuery("#yaml_def_conf #clear_col3_all:checked").val() || 
          (jQuery("#yaml_def_conf input[name^=col3_clear]").length-1) == jQuery("#yaml_def_conf input[name^=col3_clear]:checked").length) &&
          !jQuery("#yaml_def_conf select[name^=col3_content_type]:not(:hidden) option:selected").val()
       )
    { check = false; }
    
    if (!check) {
      alert('<?php echo JText::_( 'YAML POS IN COL3 NEEDED', 1 ); ?>');
      return false;
    } else {    
      if (window.yaml_custom_head_value) {
        var code = yaml_custom_head_value.getCode();
        var textarea = jQuery('#customConfigForm').prepend('<textarea name="addhead" style="width:0;height:0;border:0;padding:0;margin:0;overflow:hidden;">'+ code +'</textarea>');
      }
      jQuery(textarea).ready(function () {
        submitbutton(pressbutton);
      });
    }
  }
  -->
</script>