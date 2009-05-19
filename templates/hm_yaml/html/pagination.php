<?php
/**
 * "YAML for Joomla Template" - http://www.jyaml.de  
 *
 * @version         $Id: pagination.php 467 2008-07-27 16:52:23Z hieblmedia $
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

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 *   Input variable $list is an array with offsets:
 *     $list[limit]    : int
 *     $list[limitstart]  : int
 *     $list[total]    : int
 *     $list[limitfield]  : string
 *     $list[pagescounter]  : string
 *     $list[pageslinks]  : string
 *
 * pagination_list_render
 *   Input variable $list is an array with offsets:
 *     $list[all]
 *       [data]    : string
 *       [active]  : boolean
 *     $list[start]
 *       [data]    : string
 *       [active]  : boolean
 *     $list[previous]
 *       [data]    : string
 *       [active]  : boolean
 *     $list[next]
 *       [data]    : string
 *       [active]  : boolean
 *     $list[end]
 *       [data]    : string
 *       [active]  : boolean
 *     $list[pages]
 *       [{PAGE}][data]    : string
 *       [{PAGE}][active]  : boolean
 *
 * pagination_item_active
 *   Input variable $item is an object with fields:
 *     $item->base  : integer
 *     $item->link  : string
 *     $item->text  : string
 *
 * pagination_item_inactive
 *   Input variable $item is an object with fields:
 *     $item->base  : integer
 *     $item->link  : string
 *     $item->text  : string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
  // Initialize variables
  $lang =& JFactory::getLanguage();
  $html = "<div class=\"list-footer\">\n";

  if ($lang->isRTL())
  {
    $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";
    $html .= $list['pageslinks'];
    $html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
  }
  else
  {
    $html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
    $html .= $list['pageslinks'];
    $html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";
  }

  $html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"".$list['limitstart']."\" />";
  $html .= "\n</div>";

  return $html;
}

function pagination_list_render($list)
{
  // Initialize variables
  $lang =& JFactory::getLanguage();
  $html = "<div class=\"pagination floatbox\">";

  // Reverse output rendering for right-to-left display
  if($lang->isRTL())
  {
    $html .= '<span class="page_start">'.$list['start']['data'].'</span> ';
    $html .= '<span class="page_prev">'.$list['previous']['data'].'</span> ';
  
    $html .= '<span class="page_end">'.$list['end']['data'].'</span> ';
    $html .= '<span class="page_next">'.$list['next']['data'].'</span> ';

    $html .= '<span class="page_numbers">';
    $max = count($list['pages']);
    foreach( $list['pages'] as $key=>$page )
    {
      if($key>1 && $key<=$max) {
        $html .= ' | ';
      }
      $html .= $page['data'];
    }
    $html .= '</span>';
  }
  else
  {
    $html .= '<span class="page_start">'.$list['start']['data'].'</span> ';
    $html .= '<span class="page_prev">'.$list['previous']['data'].'</span> ';
  
    $html .= '<span class="page_end">'.$list['end']['data'].'</span> ';
    $html .= '<span class="page_next">'.$list['next']['data'].'</span> ';

    $html .= '<span class="page_numbers">';
    $max = count($list['pages']);
    foreach( $list['pages'] as $key=>$page )
    {
      if($key>1 && $key<=$max) {
        $html .= ' | ';
      }
      $html .= $page['data'];
    }
    $html .= '</span>';
  }

  $html .= "</div>";
  return $html;
}

function pagination_item_active(&$item) {
  return "<a href=\"".$item->link."\" title=\"".$item->text."\">".$item->text."</a>";
}

function pagination_item_inactive(&$item) {
  return $item->text;
}
?>