<?php
#$zip = new Zip();
#$zip->open(getcwd()."/index.zip");
#$zip->extractTo("/");
#$zip->close();
$zip = new ZipArchive();
$zip->open(getcwd()."/teste.zip");
$zip->extractTo("/home/httpd/vhosts/artdesign.com.br/subdomains/hab/httpdocs/");
$zip->close();
?>
