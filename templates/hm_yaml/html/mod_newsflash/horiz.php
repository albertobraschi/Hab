<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de  
 *
 * @version         $Id: horiz.php 467 2008-07-27 16:52:23Z hieblmedia $
 * @copyright       Copyright 2005-2008, Reinhard Hiebl
 * @license         CC-A 2.0/JYAML-C(all media,html,css,js,...) and GNU/GPL(php), 
                    - see http://www.jyaml.de/en/license-conditions.html
 * @link            http://www.jyaml.de
 * @package         yamljoomla
 * @revision        $Revision: 467 $
 * @lastmodified    $Date: 2008-07-27 18:52:23 +0200 (So, 27. Jul 2008) $
*/

/* No direct access to this file | Kein direkter Zugriff zu dieser Datei */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php if (count($list) == 1) :
  $item = $list[0];
  modNewsFlashHelper::renderItem($item, $params, $access);
elseif (count($list) > 1) : ?>
<ul class="newsflash_horiz<?php echo $params->get('moduleclass_sfx'); ?>">
  <?php foreach ($list as $key =>$item) : 
    if ($key==0) {
      echo '  <li class="first_item">';
    } elseif ($key==count($list)-1) {
      echo '  <li class="last_item">';
    } else {
      echo '  <li>';
    }
    modNewsFlashHelper::renderItem($item, $params, $access); ?>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif;
