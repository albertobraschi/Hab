<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) {
	die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
}
/**
* @version $Id: README.php 1607 2009-01-17 14:32:12Z gregdev $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://virtuemart.net
*/
?>
<pre>
****************
VirtueMart 1.1
****************
Complete Package for Joomla! 1.0.x, Joomla! 1.5.x and Mambo >= 4.5.1

You can't use this software on an earlier Mambo version than 4.5.2 (e.g. Mambo 4.5 1.0.9) 
without running into serious problems.

Copyright (C) 2004-2009 soeren - All rights reserved.
License: http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
VirtueMart is free software. This version may have been modified pursuant
to the GNU General Public License, and as distributed it includes or
is derivative of works licensed under the GNU General Public License or
other free or open source software licenses.

Community Home: http://virtuemart.net

****************

##########################
Package Contents:
##########################

 * 1 component (com_virtuemart_1.1.x.zip for Joomla! 1.0.x and Mambo or com_virtuemart_1.1.x.j15.zip for Joomla! 1.5)
   INSTALLATION REQUIRED!
   
 * 1 main module (mod_virtuemart_1.1.x.zip for Joomla! 1.0.x and Mambo or mod_virtuemart_1.1.x.j15.zip for Joomla! 1.5)
   INSTALLATION REQUIRED!
   
 * 12 additional modules
 
 * Mambots / plugins
 
 * 3 mambots for Jooma! 1.0.x or mambo
   - 2 search mambots for integration into the site search (virtuemart.searchbot and vmxsearch.searchbot), 
   - 1 content mambot for displaying product details in content items (vmproductproductsnapshots)

-OR-

 * 2 plugins for Jooma! 1.5.x
   - 1 search plugin for integration into the site search (vmxsearch.plugin), 
   - 1 content mambot for displaying product details in content items (vmproductproductsnapshots)
   

##########################
   ABOUT
##########################
VirtueMart is an Open Source Online-Shop / Shopping Cart Web-Application.
It's a Component (means plugin / extension) for the Content Management Systems "Joomla!" and "Mambo"
and can't be used without one of both. It installs fairly easy using the automatic installers. 
It's intended for use in small / mid-sized Online businesses / Online-Shops. 
So every user who wants to build up a Online Store can use this component for selling something to customers.

This package is for New Installations and updates from mambo-phpShop. 

You just need a working Joomla/Mambo Installation. 
You can get your copy  of Joomla from http://joomla.org

This package contains some code from the original 0.8.0 Edikon Corp. phpShop distribution available at www.phpshop.org

This package was tested on 
- Mambo 4.5.2.3
- Mambo 4.5.3h
- Mambo 4.6.2
- Joomla 1.0.x
- Joomla 1.5.9

 -- IMPORTANT --
Please note that module and component SHOULD be used together! 
The thing is that you can only access all areas of the component via the VirtueMart Main Module links.

You can surely create a new Menu Item linking to VirtueMart, but you must also publish the VirtueMart module.

##########################
   INSTALLATION
##########################
The installation is really easy - 
thanks to the automatic installer!
You don't need to unpack any of the archives in this complete package!

1. If you have unpacked this archive (VirtueMart_x.x_COMPLETE_PACKAGE.zip), 
	you'll see a lot of other archives.
	- com_virtuemart_x.xxx.zip, 
	- some files beginning with mod_*.zip 
	- other packages (these are the mambots/plugins).
    
2. Login to the Administration Backend (/administrator on your site).
        
        Now go to "Installers" => "Components"
        or - if you are using an older Mambo version: "Components" --> "Install/Uninstall".
        
        You can see an upload form now.
        Select the file 
        - com_virtuemart_x.xx.zip 
        and click 'Upload Component'
        
        If everything is ok, you should see a "Welcome ..." screen.
        Choose you way of installation to finish the component installation.
        
3. Now we have to install the main module, which will help you to browse
        your categories and products.
        Go to "Installers" => "Modules"
        (or - if you're using an older Mambo version: "Modules" => "Install/Uninstall"), 
        and select the file 
        - mod_virtuemart_x.xx.zip
        and then click 'Upload module'.
    
4. The module is installed, but it still is not published!
	To publish that module on your site, you must go to the list of 
	your all modules. So now head on to 
	"Modules" => "Site Modules".
	You should somewhere see a module entry for "Virtuemart Module"
	with "mod_virtuemart" at the end of that row.
	If necessary, browse to the next page of the module list.
	If you've found the module, please select it's checkbox and click
	on "Edit" in the toolbar.
	Make your settings and don't forget to select "Published? - Yes".

       Note:
       Since unpublished modules and components appear by default on 
       the last page of  the modules listed, you may need to browse component 
       and module pages until you find the VirtueMart module.
       
	Now Save - and: Done.   
    
       Note: As long as the VirtueMart main module is NOT published, VirtueMart can't be used properly.

        IF successful, the installer will have created the following directory structure:
        
         /components/com_virtuemart
         - contains code for non-administrative surfing and ordering
         
         /components/com_virtuemart/js
         - contains JavaScript Files
		 
         /components/com_virtuemart/js/dtree
         - contains JS files and images for dTree usage.
		 
         /administrator/components/com_virtuemart/html/themes
         
         /components/com_virtuemart/shop_image
         /components/com_virtuemart/shop_image/vendor
         - contains the vendor images
		 
         /components/com_virtuemart/shop_image/product
         - contains the product images
         
         /components/com_virtuemart/shop_image/ps_image
         - contains general administration/shop images
         
         /components/com_virtuemart/shop_image/stars
         
         /administrator/components/com_virtuemart
         - contains config files and the main virtuemart-parser
         
         /administrator/components/com_virtuemart/classes
         - contains all the class files
         
         /administrator/components/com_virtuemart/html
         - contains all pages accessible for the shop
         
         /administrator/components/com_virtuemart/languages
         - contains the language files
         
   
##########################
   UNINSTALL
##########################

1. Go to "Installers" => "Components" (or "Modules")
	( or - if you're using Mambo - "Components" ( or "Modules" ) => "Install/Uninstall"
        
2. Now select 'VirtueMart' ( or for the module: 'mod_virtuemart' )
	and click on 'Delete'.
   
    Done.


############################
   WHAT'S NEW IN VM 1.1.3?
###########################

//TODO

#
############################
#

For updates / changes / hints please read the ChangeLog!
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


Developers, Documentation Writers, Helpers and Coders are welcome to help us. 
Please contact me: soeren|at|virtuemart.net

The VirtueMart Software needs Helpers to evolve.
</pre>