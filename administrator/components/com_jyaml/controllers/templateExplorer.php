<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: templateExplorer.php 423 2008-07-01 11:44:05Z hieblmedia $
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

class hmyamlControllerTemplateExplorer extends hmyamlController
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
   * display the edit form
   * @return void
   */
  function view()
  {
    $template = JRequest::getVar('template_name', 'hm_yaml');
    $design = JRequest::getVar('design', 'default');
    $ext = JRequest::getVar('ext', 'none');    
    $returnid = JRequest::getVar('returnid', '');
    
    if (!$template && !$ext) return;
    
    $this->template_name = $template;
    
    ?>    
    <fieldset>
        <div class="configuration"><?php echo JText::_( 'YAML EXPLORER TITLE' ); ?></div>
    </fieldset>
    <p><?php echo JText::_( 'YAML EXPLORER PATH' ); ?>: <?php echo '/templates/'.$template; ?></p>  

    <?php if ($ext == 'css') : ?>
    <fieldset>
      <legend><?php echo JText::_( 'YAML EXPLORER DESIGN FOLDER TITLE' ); ?>
      <?php echo JYAML::viewCSSFiles($design, true); ?>
    </fieldset>
    
    <fieldset>
      <legend><?php echo JText::_( 'YAML EXPLORER DESIGN CORE TITLE' ); ?>
      <?php echo JYAML::viewCSSFiles($design, true, $source='yaml'); ?>
    </fieldset>
    <?php endif; ?>
    
    <?php if ($ext == 'js') : ?>
    <fieldset>
      <legend><?php echo JText::_( 'YAML EXPLORER SCRIPT FOLDER TITLE' ); ?>
      <?php echo JYAML::viewScriptFiles($design, true); ?>
    </fieldset>
    <?php endif; ?>

    <script type="text/javascript">
      jQuery.noConflict();                            
      (function($) { 
        $(function() {       
          $(document).ready(function(){            
            $(".chooseFile").click(function(){
              var file   = $(this).attr("file");
              var folder = $(this).attr("folder");
              
              var sf  = $("#<?php echo $returnid; ?>:eq(0)", parent.document);
              var sp  = $("#path_<?php echo $returnid; ?>:eq(0)", parent.document);              
              var spm = $("#source_<?php echo $returnid; ?>:eq(0)", parent.document);
              
              $(sf).removeClass("off");
              $(sf).val(file);
              if(folder){
                $(sp).text(folder);
                
                if (folder.indexOf('/yaml/') == '-1') {
                  $(spm).val("design");  
                } else {
                  $(spm).val("");  
                }  
              }
                        
              sf.focus();              
              window.parent.document.getElementById('sbox-window').close();              
            });
          
          });
        });
      })(jQuery);
    </script>
  <?php
  }
}
?>
