###
# 08.02.2008 gregdev, Task #1425 - Changing User fields have no effect in frontend Shipping Addresses section
ALTER IGNORE TABLE `jos_vm_userfield` ADD `shipping` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `registration`;
INSERT INTO `jos_vm_userfield` VALUES (NULL, 'address_type_name', '_PHPSHOP_USER_FORM_ADDRESS_LABEL', '', 'text', 32, 30, 1, 6, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 0, 0, 1, 1, NULL);
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='company';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='first_name';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='last_name';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='middle_name';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='address_1';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='address_2';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='city';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='zip';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='country';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='state';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='phone_1';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='phone_2';
UPDATE `jos_vm_userfield` SET `shipping`=1 WHERE `name`='fax';
###