<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de
 *
 * @version         $Id: syntaxEditor.php 457 2008-07-21 17:29:14Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 457 $
 * @lastmodified    $Date: 2008-07-21 19:29:14 +0200 (Mo, 21. Jul 2008) $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class hmyamlControllerSyntaxEditor extends hmyamlController
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
  function edit($file=NULL)
  {
    global $option;
    // disable Joomla! renderer
    ob_end_clean();
    
    ?>
    <form action="index.php" method="post" name="adminForm">
      <fieldset>
          <div style="float: right;">
            <button onclick="save_file('save');window.top.setTimeout('window.parent.location.replace(\'index.php?option=<?php echo $option; ?>\')', 2000);" type="button"><?php echo JText::_( 'YAML SAVE' ); ?></button>
            <button onclick="save_file('apply')" type="button"><?php echo JText::_( 'YAML APPLY' ); ?></button>
            <button onclick="window.parent.location.replace('index.php?option=<?php echo $option; ?>');" type="button"><?php echo JText::_( 'YAML CANCEL' ); ?></button>
          </div>
          <div class="configuration"><?php echo JText::_( 'YAML EDITOR TITLE' ); ?></div>
      </fieldset>  
      <?php
      
      $lang    =& JFactory::getLanguage();
      $langTag = explode( '-', $lang->getTag() );
    
      if (!$file) 
      {
        $file = JRequest::getVar('file', NULL);
      }
      $srcfile = JRequest::getVar('srcfile', NULL);
      
      //$eSyntax = JRequest::getVar('eSyntax', 'text');
      $eSyntax = JFile::getExt($file);
      $eFullscreen = JRequest::getVar('eFullscreen', 'false');
      $eToolbar = JRequest::getVar('eToolbar', '0');
      $eMultiFiles = JRequest::getVar('eMultiFiles', 'false');
      $eAllowToogle = JRequest::getVar('eAllowToogle', 'true');
      
      $toolbar[0] = 'syntax_selection, fullscreen, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help';
      
      // Read File
      echo $srcfile ? '<p>Source File: '.$srcfile.'</p>' : '';
      echo '<p>'.($srcfile ? 'Save File: ' : '').$file.'</p>';			
      
      $content = '';
      if ($srcfile) 
      {
        $content = JFile::read( JPATH_SITE.DS.$srcfile );
      } 
      else 
      {
        $content = JFile::read( JPATH_SITE.DS.'templates'.DS.$file );
      }
			
      if( get_magic_quotes_runtime()) 
      {
        $content = stripslashes( $content );
      }
      ?>
      <button class="syntaxEditor" type="button" onclick="syntaxEditor.toggleEditor()"><?php echo JText::_( 'YAML EDITOR TOGGLE' ); ?></button>
      <button class="syntaxEditor" type="button" onclick="syntaxEditor.toggleLineNumbers()"><?php echo JText::_( 'YAML EDITOR TOGGLE LINENUMBERS' ); ?></button>
      
      <textarea id="syntaxEditor" class="<?php echo $eSyntax; ?>" name="content" style="width:100%; height:100%;"><?php echo $content; ?></textarea>
    
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="controller" value="syntaxEditor" />    
      <input type="hidden" value="<?php echo $option; ?>" name="option"/>
      <input type="hidden" value="" name="buffer" />
      <input type="hidden" value="<?php echo $eSyntax;?>" name="eSyntax" />
      <input type="hidden" value="<?php echo $file; ?>" name="file" />
    </form>

    <?php
    // Start buffer for Joomla! renderer
    ob_start();
    
    ?>    
    <script type="text/javascript">
      jQuery.noConflict();                            
      (function($) { 
        $(function() {       
          $(document).ready(function(){
            pd = $(parent.document);

            pd.find("#sbox-btn-close").hide();
            pd.find("#sbox-btn-close").click(function(){
              window.parent.location.replace('index.php?option=<?php echo $option; ?>');
            });
            
            $("html").css({ 'height':'100%', 'padding':'0', 'margin':'0' });
            $("body").css({ 'height':'100%', 'padding':'10px 10px 0 10px', 'margin':'0', 'overflow':'hidden' });
          
            var width = $("#sbox-overlay", pd).width();
            var height = $("#sbox-overlay", pd).height();  
            
            pd.find("#sbox-window").css({'width':width-20, 'height':height-20, 'left':'0', 'top':'0', 'right':'0', 'bottom':'0', 'margin':'0px' });    
            pd.find("#sbox-window>div>iframe").css( {'width':'100%'} );  
            pd.find("#sbox-window>div>iframe").css( {'height':'100%'} );    
            
            //alert(jQuery("body").height()); 
            h1 = $("body").height();
            h2 = $("body textarea").offset().top; 
            nh = h1-h2-20;
            $("body textarea").height(nh);
            
            $("#syntaxEditor").addClass("codepress");
            
            if($.browser.msie) CodePress.run();
          });
        });
      })(jQuery);
      
      function save_file(pressbutton){        
        var code = syntaxEditor.getCode();
        document.adminForm.buffer.value = code;
        submitbutton(pressbutton);
      }
    </script>
    <?php
  }
  
  function save() {
    global $option, $mainframe;
  
    $content = JRequest::getVar( 'buffer',  false, 'POST', '', JREQUEST_ALLOWRAW );
    $file = JRequest::getVar('file', false, 'POST');
    $task = JRequest::getVar('task', 'save', 'POST');
    $eSyntax = JRequest::getVar('eSyntax', 'html', 'POST');

    if ($file) 
    {
      if(JFile::write( JPATH_SITE.DS.'templates'.DS.$file, $content)) {    
        $mainframe->enqueueMessage( JText::_( 'YAML SAVED SUCCESS' ) );
			} else {
			  $mainframe->enqueueMessage( JText::_( 'YAML SAVED FAILED' ), 'error' );
			}
      
      if ($task=='save') 
      {
        $mainframe->redirect( JURI::base().'index3.php?option='.$option.'&controller=hmyaml&task=wait');      
      } 
      else 
      {
        $mainframe->redirect( JURI::base().'index3.php?option='.$option.'&controller=syntaxEditor&task=edit&eSyntax='.$eSyntax.'&file='.str_replace(DS.'templates', '', $file) );      
      }      
    }
  }
  
  function apply() 
  {
    $this->save();
  }  
}
?>