<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />

</head>
<body>
<div class="header-full">
	<!-- begin: #header -->
	<div id="header">

		<?php if($this->countModules( 'search' )) : ?>
		<div class="header_right">
            <div id="changeFont">
                <span class="label">Aumentar Texto</span>
                <a href="#" class="decreaseFont"><img src="<?php echo $jyaml->imagePath; ?>/icon-minus.gif" /></a>
                <a href="#" class="increaseFont"><img src="<?php echo $jyaml->imagePath; ?>/icon-maximus.gif" /></a>
                <a href="#" class="lock"><img src="<?php echo $jyaml->imagePath; ?>/icon-lock.gif" /></a>
            </div>
            <div style="clear:both; float:right; width: 20%;"></div>
			<div class="search">
					<?php $jyaml->getPosition('modules', 'search', 'raw', ''); ?>
			</div>
			</div>

		<?php endif; ?>
		<?php if($this->countModules( 'logo' )) : ?>
		<div class="header_left">
			<h1 class="home">
				<a href="<?php echo JURI::base(); ?>">
					<img src="<?php echo $jyaml->imagePath; ?>/logo.gif" width="105" height="80" alt="<?php echo JApplication::getCfg('sitename') ?>" />
				</a>
			</h1>
			<div style="clear:both"></div>
		<?php else : ?>
			<h1 class="login">
				<a href="<?php echo JURI::base(); ?>">
					<img src="<?php echo $jyaml->imagePath; ?>/logo-login.gif" width="193" height="146" alt="<?php echo JApplication::getCfg('sitename') ?>" />
				</a>
			</h1>
		<?php endif; ?>
		<?php if($this->countModules( 'greeting' )) : ?>
			<div class="greeting">
					<?php $jyaml->getPosition('modules', 'greeting', 'raw', ''); ?>
			</div>
			</div>
		<?php endif; ?>
	</div>
		<?php if($this->countModules( 'banner-flash' )) : ?>
<div style="clear:both"></div>		
			<div class="banner-flash">
				<div class="banner">
					<?php $jyaml->getPosition('modules', 'banner-flash', 'raw', ''); ?>
				</div>
			</div>
		<?php endif; ?>
	<!-- end: #header -->
</div>
<div style="clear:both"></div>
<div id="page_margins">
  <div id="page">
    <?php if($this->countModules( 'breadcrumbs' )) : ?>
    <!-- begin: #breadcrumbs -->
    <div id="breadcrumbs">
      <?php echo JText::_('YAML YOU ARE HERE'); ?>:
      <?php $jyaml->getPosition('module', 'breadcrumbs', 'raw', ''); ?>
    </div>
    <!-- end: #breadcrumbs -->
	<?php endif; ?>

    <?php if($this->countModules( 'nav_main' )) : ?>
    <!-- begin: main navigation #nav -->
    <div id="nav">
      <a id="navigation" name="navigation"></a>
      <!-- skip anchor: navigation -->
      <div id="nav_main">
        <?php $jyaml->getPosition('modules', 'nav_main', 'raw', ''); ?>
      </div>
    </div>
    <!-- end: main navigation -->
    <?php endif; ?>

    <!-- begin: main content area #main -->
    <div id="main">
      <?php if ( $jyaml->col1_enabled ) : ?>
      <!-- begin: #col1 - first float column -->
      <div id="col1">
        <div id="col1_content" class="clearfix">
          <?php $jyaml->getContent('col1_content'); ?>
        </div>
      </div>
      <!-- end: #col1 -->
      <?php endif; ?>

      <?php if ( $jyaml->col2_enabled ) : ?>
      <!-- begin: #col2 second float column -->
      <div id="col2">
        <div id="col2_content" class="clearfix">
          <?php $jyaml->getContent('col2_content'); ?>
        </div>
      </div>
      <!-- end: #col2 -->
       <?php endif; ?>

      <!-- begin: #col3 static column -->
      <div id="col3">
        <div id="col3_content" class="clearfix">
        <div id="col3_content_wrapper" class="floatbox">
          <a id="content" name="content"></a>
		  	<?php if($this->countModules( 'login' )) : ?>
				<?php $jyaml->getPosition('modules', 'login', 'raw', ''); ?>
			<?php endif; ?>

			<?php $jyaml->getContent('col3_content'); ?>
          </div>
        </div>

        <div id="ie_clearing"> </div>
        <!-- Ende: IE Column Clearing -->
      </div>
      <!-- end: #col3 -->

    </div>
    <!-- end: #main -->



  </div> <!-- end: #page -->
</div> <!-- end: #page_margins -->
<!-- begin: #footer -->
<div class="footer-full">
	<div id="footer" class="floatbox">
		<a href="http://www.artdesign.com.br/" class="float_left" rev="made">by ART&DESIGN </a>
		<img src="<?php echo $jyaml->imagePath; ?>/logo-footer.gif" width="154" height="31" alt="<?php echo JApplication::getCfg('sitename') ?>" class="float_right" />
	</div>
</div>
<!-- end: #footer -->
<jdoc:include type="modules" name="debug" />
</body>
</html>

