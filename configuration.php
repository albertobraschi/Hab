<?php
class JConfig {
	var $offline = '0';
	var $editor = 'tinymce';
	var $list_limit = '20';
	var $helpurl = 'http://help.joomla.org';
	var $debug = '0';
	var $debug_lang = '0';
	var $sef = '0';
	var $sef_rewrite = '0';
	var $sef_suffix = '0';
	var $feed_limit = '10';
	var $feed_email = 'author';
	var $secret = 'yIw5cm5H1uk7AuKO';
	var $gzip = '1';
	var $error_reporting = '-1';
	var $xmlrpc_server = '0';
	var $log_path = '/var/www/hab/logs';
	var $tmp_path = '/var/www/hab/tmp';
	var $live_site = '';
	var $force_ssl = '0';
	var $offset = '0';
	var $caching = '0';
	var $cachetime = '15';
	var $cache_handler = 'file';
	var $memcache_settings = array(
		);
	var $ftp_enable = '0';
	var $ftp_host = '127.0.0.1';
	var $ftp_port = '21';
	var $ftp_user = 'admin';
	var $ftp_pass = 'la05na09';
	var $ftp_root = '';
	var $dbtype = 'mysql';
	var $host = 'localhost';
	var $user = 'root';
	var $db = 'hab';
	var $dbprefix = 'hab_';
	var $mailer = 'mail';
	var $mailfrom = 'alberto.braschi@gmail.com';
	var $fromname = 'Hab';
	var $sendmail = '/usr/sbin/sendmail';
	var $smtpauth = '0';
	var $smtpuser = '';
	var $smtppass = '';
	var $smtphost = 'localhost';
	var $MetaAuthor = '0';
	var $MetaTitle = '0';
	var $lifetime = '15';
	var $session_handler = 'database';
	var $password = '1234';
	var $sitename = 'Hab';
	var $MetaDesc = '';
	var $MetaKeys = '';
	var $offline_message = '';
}
