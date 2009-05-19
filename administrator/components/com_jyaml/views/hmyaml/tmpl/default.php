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

$bar =& new JToolBar( 'My ToolBar' );
$popup =& $bar->loadButtonType( 'Popup' );
$button =& $bar->loadButtonType( 'Custom' );

$create_design_link = 'index3.php?option='.$option.'&controller=fileControl&task=createDesign&template='.$this->selected_template;
$create_design_button = $popup->fetchButton( 'Popup', 'create_html', 'YAML CREATE DESIGN', $create_design_link , 640, 350, 150, 150 );  
$install_pgl_link = 'index3.php?option='.$option.'&controller=fileControl&task=uploadTemplatePlugin&template='.$this->selected_template;
$install_pgl_button = $popup->fetchButton( 'Popup', 'create_html', 'YAML INSTALL PLUGIN BUTTON', $install_pgl_link , 640, 350, 150, 150 );  

?>
<div id="editcell">
 
  <?php if ($this->require_ftp_login) : ?>
    <?php echo $this->loadTemplate('ftp_login'); ?>
  <?php endif; ?>
  
<form action="index.php" method="post" name="adminForm">

  <div class="designbox floatbox">
  
    <div class="legend"><?php echo JText::_( 'YAML TITLE PLUGINS' ); ?> [ <span class="on" id="installTemplatePlugin"><?php echo $install_pgl_button; ?></span> ]</div>
    <div class="content">   
      <?php if ($this->template_plugins) : ?>
        <div id="yamlPlugins">
        <?php 
        foreach ($this->template_plugins as $plugin) {
          $img = $plugin->published ? 'publish_g.png' : 'publish_x.png';
          $edit_url = JURI::base().'index3.php?option='.$option.'&amp;controller=plugins&amp;task=edit&amp;template_name='.$this->selected_template.'&amp;plugin='.$plugin->plugin;  
          $link   = "<a class=\"modal plugin-row\" title=\"".strip_tags($plugin->description)."\" href=\"$edit_url\" rel=\"{handler: 'iframe', size: {x: 450, y: 480}}\">";
          $link  .= '<img alt="" align="left" src="images/'.$img.'" />&nbsp;&nbsp;'.$plugin->name;
          $link  .= "</a>\n";      
          echo $button->fetchButton( 'Custom', $link, 'editplugin' );
        }
        ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <h2><?php echo JText::_( 'YAML TITLE DESIGNS' ); ?> <small>[ <span class="on createDesign"><?php echo $create_design_button; ?></span> ]</small></h2>
  
  <div id="designlist">
    <?php
    if (!$this->designlist) {
      $msg = JText::_('YAML NO DESIGNS FOUND'); 
      JYAML::outputMsg($msg);
    }
      foreach($this->designlist as $i=>$design) 
      {
        // Ceck Design Directories
        $validation = JYAML:: checkValideTemplate($design);
    
        $copy_design_link = 'index3.php?option='.$option.'&controller=fileControl&task=createDesign&template='.$this->selected_template.'&source='.$design->value;
        $copy_design_button = $popup->fetchButton( 'Popup', 'create_html', 'YAML COPY DESIGN', $copy_design_link , 640, 350, 150, 150 );      
  
        if ($validation['noValid']) {
          $edit_xml_button = '<span class="hasTip off" title="'.JText::_( 'YAML EDIT DESIGN XML NO VALIDE' ).'">'.JText::_( 'YAML EDIT DESIGN XML' ).'</span>';        
        } else {
          $edit_xml_url = 'index3.php?option='.$option.'&controller=designConfig&task=edit&template_name='.$this->selected_template.'&design='.$design->value;
          $edit_xml_button = '<span class="on editButton">'.$popup->fetchButton( 'Popup', 'xml_edit', 'YAML EDIT DESIGN XML', $edit_xml_url , 640, 480, 150, 150 ).'</span>';
        }
        ?>
        <a name="<?php echo $design->value; ?>" id="<?php echo $design->value; ?>"></a>
        <div class="yslider-design designbox">
          <div class="slide-title-design">
            <div style="float:right;" class="titlelinks">
              [
              <?php
              if ($design->value!='default') {
                echo ' <a class="MAINdeleteDesign" tmpl="'.$this->selected_template.'" design="'.$design->value.'">'.JText::_( 'YAML DELETE DESIGN' ).'</a> | '; 
                
                $rename_design_url = 'index3.php?option='.$option.'&controller=fileControl&task=rename_design&template='.$this->selected_template.'&design='.$design->value;
                echo $popup->fetchButton( 'Popup', 'export', 'YAML RENAME DESIGN', $rename_design_url , 640, 480, 150, 150 ). ' | ';     
              }
              echo $copy_design_button.' |';
              $export_url = 'index3.php?option='.$option.'&controller=fileControl&task=export&template='.$this->selected_template.'&design='.$design->value;
              echo $popup->fetchButton( 'Popup', 'export', 'YAML EXPORT DESIGN TITLE', $export_url , 640, 480, 150, 150 );
              ?>          
              ]
            </div>
            
            <?php          
            echo '<span style="font-weight:normal;">'.JText::_('NAME').': </span>'.$design->value.' [ '.$edit_xml_button.' ] '.($i == 0 ? '<span style="font-weight:normal">(global)</span>': '');            
            ?>          
          </div>
          
          <div class="ycontent-design floatbox">            
          <div class="floatbox">            
            <div style="float:right; width:43%; overflow:hidden;"> 
              
              <div class="designbox-sub">
                <div class="legend-sub"><?php echo JText::_( 'YAML CONF SETTINGS' ); ?>:</div>    
                <div class="content-sub">
                  <?php echo  JYAML::getSimpleInfo($design->value); ?>
                </div>
              </div>
              
              <div class="designbox-sub">
                <div class="legend-sub"><?php echo JText::_( 'YAML CONF CUSTOM XML' ); ?>:</div>    
                <div class="content-sub">
                  <?php echo  JYAML::getCustomXMLInfo($design->value); ?>
                </div>
              </div>     
              
              <?php              
              $create_xml_link = 'index3.php?option='.$option.'&controller=fileControl&task=create&ext=xml&folder='.$this->selected_template.DS.'config'.DS.$design->value;
              $create_xml_button = $popup->fetchButton( 'Popup', 'create_xml', 'YAML CREATE FILE', $create_xml_link , 640, 350, 150, 150 );  
              ?>
              
              <div class="designbox-sub">
                <div class="legend-sub"><span class="hasTip" title="<?php echo JText::_( 'YAML CLICK TO EDIT' ); ?>"><?php echo JText::_( 'YAML TITLE CUSTOM XMLFILES' ); ?></span></div>
                <div class="content-sub">
                  [<span class="on"> <?php echo $create_xml_button; ?> </span>]
                  <?php echo  JYAML::viewCustomXmlFiles($design->value); ?>
                </div>
              </div>
            </div>
            
            <div style="overflow:hidden;width:55%;float:left;">                          
              <div class="designbox-sub">
                <div class="legend-sub"><?php echo JText::_( 'YAML CONF DESIGN STATUS' ); ?></div>
                <div class="content-sub">
                  <?php      
                  // Check Design Folder exists
                  echo $validation['list'];
                  
                  if ($validation['noValid']) : ?>
                      <button type="button" onclick="submitbutton('makeDesignValid')"><?php echo JText::_( 'YAML BUTTON MAKE DESIGN VALIDE' ); ?></button>
                      <input type="hidden" name="mkV_template_name" value="<?php echo $this->selected_template; ?>" />
                      <input type="hidden" name="mkV_design" value="<?php echo $design->value; ?>" />
                      <input type="hidden" name="mkV_data" value="<?php echo implode('-', $validation['noValid']); ?>" />
                  <?php endif; ?>  
                </div>
              </div>
              
              <div class="designbox-sub">         
                <div class="legend-sub"><span class="hasTip" title="<?php echo JText::_( 'YAML CLICK TO EDIT AND BROWSE' ); ?>"><?php echo JText::_( 'YAML TITLE CSSFILES' ); ?></span></div>
                <div class="content-sub"><?php echo  JYAML::viewCSSFiles($design->value); ?></div>
              </div>
              
              <div class="designbox-sub">
                <div class="legend-sub"><span class="hasTip" title="<?php echo JText::_( 'YAML CLICK TO EDIT AND BROWSE' ); ?>"><?php echo JText::_( 'YAML TITLE HTMLFILES' ); ?></span></div>
                <div class="content-sub"><?php echo  JYAML::viewHTMLFiles($design->value); ?></div>
              </div>
              
              <div class="designbox-sub">
                <div class="legend-sub"><span class="hasTip" title="<?php echo JText::_( 'YAML CLICK TO EDIT AND BROWSE' ); ?>"><?php echo JText::_( 'YAML TITLE SCRIPTFILES' ); ?></span></div>
                <div class="content-sub"><?php echo  JYAML::viewScriptFiles($design->value); ?></div>
              </div>
                            
            </div>
          </div>
          </div>
                  
        </div>
        <?php
      }
  ?>
  </div>

</div>
<div id="yaml_status_info">
    <div class="designbox">
      <div class="legend"><?php echo JText::_( 'YAML LABEL SWITCH TEMPLATE' ); ?></div>
      <div class="content">
        <div>
          <?php if ($this->no_tpl_activ) : ?>         
            <p class="yaml_info"><?php echo JText::_( 'YAML NO TEMPLATE ACTIVE' ); ?></p>
          <?php endif; ?>
          <?php echo $this->lists['switch_template']; ?><br /><br />
          <button type="button" onclick="submitbutton('')"><?php echo JText::_( 'YAML BUTTON SWITCH TEMPLATE' ); ?></button> 
          <button type="button" onclick="submitbutton('activateTemplate')"><?php echo JText::_( 'YAML ACTIVE TEMPLATE' ); ?></button>
        </div>
        <hr class="seperator" />
        <?php
        echo $this->selected_template. ' - ';
        $rename_tpl_url = 'index3.php?option='.$option.'&controller=fileControl&task=rename_template&template_name='.$this->selected_template;
        echo $rename_tpl_button = ' <span>['.$popup->fetchButton( 'Popup', 'tpl_edit', 'YAML BUTTON RENAME TEMPLATE', $rename_tpl_url , 640, 300, 150, 150 ).']</span> ';
        
        $copy_tpl_url = 'index3.php?option='.$option.'&controller=fileControl&task=copy_template&template_name='.$this->selected_template;
        echo $copy_tpl_button = ' <span>['.$popup->fetchButton( 'Popup', 'tpl_edit', 'YAML BUTTON COPY TEMPLATE', $copy_tpl_url , 640, 300, 150, 150 ).']</span> ';
        ?>
      </div>
    </div>
    
    <div class="designbox">         
      <div class="legend">
        <span class="hasTip" title="<?php echo JText::_( 'YAML CLICK TO EDIT AND BROWSE' ); ?>">
        <?php echo JText::_( 'YAML TITLE EXTENSION TEMPLATES' ); ?></span>
      </div>
      <div class="content"><?php echo JYAML::viewExtHTMLFiles(); ?></div>
    </div>
    
    <div class="designbox">         
      <div class="legend"><?php echo JText::_( 'YAML VERSION TITLE' ); ?></div>
      <div class="content"><?php echo JYAML::getVersionInfo(); ?></div>
    </div>
    
    <div class="designbox">         
      <div class="legend"><?php echo JText::_( 'YAML TITLE SUPPORT' ); ?></div>
      <div class="content">  
        <ul>
          <li><a href="http://www.jyaml.de" target="_blank">YAML Joomla! Template</a></li>
          <li><a href="http://forum.yaml.de/index.php?board=11.0" target="_blank">Support Forum</a></li>
        </ul>
        <ul>
          <li><a href="http://www.famfamfam.com/" target="_blank">famfamfam Silk Icons</a></li>
        </ul>
      </div>
    </div>
   
    <p style="font-size:13px;">&copy; Reinhard Hiebl [<img style="line-height: 0pt; vertical-align: text-top;" src="components/<?php echo $option; ?>/images/hieblmedia_logo_icon.gif" alt="" width="16" height="16" /><a href="http://www.hieblmedia.de" target="_blank">Hieblmedia</a>]</p>
   
  
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="template_name" value="<?php echo $this->selected_template; ?>" />
<input type="hidden" name="controller" value="hmyaml" />
</form>

</div>