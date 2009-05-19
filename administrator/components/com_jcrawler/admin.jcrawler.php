<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

@set_time_limit(99999999);
@ini_set("max_execution_time","false");
@ini_set("memory_limit",'256M');

//error_reporting(E_ALL);

$user = &JFactory::getUser();
if (!($user->usertype == 'Super Administrator' || $user->usertype == 'Administrator')) {
  $mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// require the html view class
jimport( 'joomla.application.helper' );
jimport('joomla.filesystem.file');

require_once( JApplicationHelper::getPath( 'admin_html', 'com_jcrawler' ) ); 

$task = JRequest::getVar( 'task', '' );

switch ($task) {
	case 'submit':
		submit($option);
		break;
	case 'notify':
		notify($option);
		break;
	case 'updatecheck':
		updatecheck($option);
		break;
	default:
		HTML_jcrawler::showForm($option);
		break;
}

$stack = array();
$disallow_file = array();


function submit($option) {
	global $stack,$mainframe;
	
	// get values from gui of script
	$website = JRequest::getVar( 'http_host', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	if(substr($website,-1)!="/") $website=$website."/";
	$page_root = JRequest::getVar( 'document_root', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$sitemap_file = $page_root . JRequest::getVar( 'sitemap_url', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$sitemap_url = $website . JRequest::getVar( 'sitemap_url', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$sitemap_form = JRequest::getVar( 'sitemap_url', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$priority = JRequest::getVar( 'priority', '1.0', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$forbidden_types = toArray(JRequest::getVar( 'forbidden_types', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML ));
	$exclude_names = toArray(JRequest::getVar( 'exclude_names', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML ));
	$freq = JRequest::getVar( 'freq', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$modifyrobots = JRequest::getVar( 'robots', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$method = JRequest::getVar( 'method', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$level = JRequest::getVar( 'levels', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$maxcon = JRequest::getVar( 'maxcon', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$timeout = JRequest::getVar( 'timeout', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$whitelist = JRequest::getVar( 'whitelist', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	
	($priority >= 1)?$priority="1.0":null;
	
	$xmlconfig=genConfig($priority,$forbidden_types,$exclude_names,$freq,$method,$level,$maxcon,$sitemap_form,$page_root,$timeout);
	
	
	if (substr($page_root,-1)!="/") $page_root=$page_root."/";
	$robots=@JFile::read( $page_root.'robots.txt' );

	preg_match_all("/Disallow:(.*?)\n/", $robots, $pos);
	
	if ($exclude_names[0]=="")
		unset($exclude_names[0]);
	
	foreach($pos[1] as $disallow){
		$disallow=trim($disallow);
		if (strpos($disallow,$website)===false)
			$disallow=$website.$disallow;
		$exclude_names[]=$disallow;
	}
	
	$forbidden_strings=array("print=1","format=pdf","option=com_mailto","component/mailto","/mailto/","mailto:","login","register","reset","remind");
	foreach ($exclude_names as $name) {
		($name!="") ? ($forbidden_strings[]=$name):null;
	}
	
	
	$stack = array();
	$s = microtime(true);
	
	if($whitelist=="yes") AntiFloodControl($website);
	
	$file = genSitemap($priority, getlinks($website,$forbidden_types,$level,$forbidden_strings,$method,$maxcon,$timeout),$freq,$website);
	writeXML($file,$sitemap_file,$option, $sitemap_url);
	writeXML($xmlconfig,$page_root."/administrator/components/com_jcrawler/config.xml",$option,$sitemap_url);
	
	$mainframe->enqueueMessage( "total time: ".round(microtime(true) - $s, 4)." seconds" );

	if ($modifyrobots==1) modifyrobots($sitemap_url,$page_root);
	HTML_jcrawler::showNotifyForm($option, $sitemap_url);
}

function notify($option) {
	global $mainframe;
	$url = JRequest::getVar( 'url', 'none', 'POST', 'ARRAY', JREQUEST_ALLOWHTML );
	
	 // erzeuge einen neuen cURL-Handle
	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if ($url[0]!="none"){
		foreach ($url as $key) {
			
			curl_setopt($ch, CURLOPT_URL, $key);
	 		curl_exec($ch);
	 		
				$curlError = curl_error($ch);
				if($curlError != "") {
	        		$mainframe->enqueueMessage("Curl error on url $key: $curlError",error);
	      		}
					
				$http_code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($http_code>=400) {
					 $mainframe->enqueueMessage(htmlentities("httpcode: ".$http_code." on url ".$key."<br><a href=\"".$key."\" target=\"_blank\">Click to submit by hand</a>") ,error);
				 } else {
					$mainframe->enqueueMessage( "Submission to ".parse_url($key, PHP_URL_HOST)." succeed " );
				 } 	 	
		}
		 
	}
	 curl_close($ch);
	$mainframe->redirect('index2.php?option='.$option);
	
}

function updatecheck(){

	define("jcrawler_version","1.7 Beta");
	
	 // erzeuge einen neuen cURL-Handle
	 $ch = curl_init();
	 
	 // setze die URL und andere Optionen
	 curl_setopt($ch, CURLOPT_URL, "http://www.pixelschieber.ch/version.php");
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 
	 // führe die Aktion aus und gebe die Daten an den Browser weiter
	 $content=curl_exec($ch);
	 
	 // schließe den cURL-Handle und gebe die Systemresourcen frei
	 curl_close($ch);
	 
	 if ($content==jcrawler_version){
	 	echo "<h2>Thank you for checking, you have the latest version (".jcrawler_version.")</h2>";
	 } else {
	 	echo "<h2>There is a new version (".$content.") available.</h2>Download version ".$content." now: <a href=\"http://joomlacode.org/gf/project/jcrawler/frs/\" target=\"_blank\">JCrawler ".$content."</a>";
	 }
	
	

}

	function modifyrobots ($sitemap_url,$page_root){
		global $mainframe;
		
		if (substr($page_root,-1)!="/") $page_root=$page_root."/";
		$robots=JFile::read( $page_root.'robots.txt' );
		//$pos=stripos("Sitemap:",$robots);
		$pos=preg_match("/Sitemap:/", $robots);
		
		if ($pos==0){	
			if (JFile::write( $page_root.'robots.txt',$robots."\n# BEGIN JCAWLER-XML-SITEMAP-COMPONENT\nSitemap: ".$sitemap_url."\n# END JCAWLER-XML-SITEMAP-COMPONENT" )!=false) {
				$mainframe->enqueueMessage( "robots.txt modified" );
			} else {
				$errors[] = JError::getErrors();
				foreach ($errors as $error) {
					$mainframe->enqueueMessage($error->message,error);
				}
				$mainframe->enqueueMessage("Your robots.txt is not writable!",error);
			}
		} else {
			$mainframe->enqueueMessage( "robots.txt contains already sitemap location" );
		}
	}
	
	function getConf($path){
   
      $parser = JFactory::getXMLParser('Simple');

      if ($parser->loadFile($path)) {
         if (isset( $parser->document )) {
            $document    = $parser->document;

			$chil=$document->children();
			$chil1=$chil[0]->children();
			
			foreach($chil1 as $child ) {
   				$config_data[$child->name()]=$child->data();
			}
			
         }
      }
	
		return $config_data;
	}
	
	function Checksh404sef(){
		$sef_config_file  = JPATH_ADMINISTRATOR.'/components/com_sh404sef/config/config.sef.php';
		if (is_readable($sef_config_file)){
			return true;
		}
		return false;
	}
	
	function AntiFloodControl($url){
		global $mainframe;
		$sef_config_class = JPATH_ADMINISTRATOR.'/components/com_sh404sef/sh404sef.class.php';
		$sef_config_file  = JPATH_ADMINISTRATOR.'/components/com_sh404sef/config/config.sef.php';

				// Make sure class was loaded.
		if (!class_exists('SEFConfig')) {   // V 1.2.4.T was wrong variable name $SEFConfig_class instead of $sef_config_class
		  if (is_readable($sef_config_class)) require_once($sef_config_class);
		  else return;
		}
		
		$sefConfig = new SEFConfig();
		
		$crawlurl=parse_url($url);
		$hosts = gethostbynamel($crawlurl['host']);
		$white_list=null;
		foreach ($hosts as $host) {
			if(array_search($host,$sefConfig->ipWhiteList)===FALSE){
				$white_list.="\n".$host;
			}
		}
		
		$handle = fopen(JPATH_ADMINISTRATOR.'/components/com_sh404sef/security/sh404SEF_IP_white_list.txt',"a");
		
		 if (!fwrite($handle, $white_list) and !empty($white_list)) {
		 	$mainframe->enqueueMessage(htmlentities("Couldn't add IP to sh404SEF whitelist") ,error);
		 }elseif(!empty($white_list)) {
		 	$mainframe->enqueueMessage("Added IP to sh404SEF whitelist");
		 }
		 fclose($handle);
		
	}


	function writeXML ($file, $location, $option, $sitemap_url) {
		global $mainframe;
			// Write $somecontent to our opened file.
		$buffer = pack("CCC",0xef,0xbb,0xbf);
		$buffer .= utf8_encode($file);
		//$buffer .=$file;
		if (JFile::write( $location, $buffer )){
		
			$mainframe->enqueueMessage( "Success, wrote $location" );
			
		} else {
			$errors[] = JError::getErrors();
			foreach ($errors as $error) {
			
				$mainframe->enqueueMessage($error->message,error);
			}
			$mainframe->enqueueMessage("$location is not writable",error);
			
		}

		return;
	}
	
	function genConfig($priority,$forbidden_types,$exclude_names,$freq,$method,$level,$maxcon,$sitemap_form,$docroot,$timeout){
		
		$xmlconfig="<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\" ?>
<document>
    <options>
		<forbiddentypes>".htmlentities(arrToString($forbidden_types))."</forbiddentypes>
		<excludelist>".htmlentities(htmlentities(arrToString($exclude_names),ENT_QUOTES,'UTF-8'))."</excludelist>
	<sitemapurl>".$sitemap_form."</sitemapurl>
	<priority>".$priority."</priority>
	<changefreq>".$freq."</changefreq>
	<method>".$method."</method>
	<level>".$level."</level>
	<maxconn>".$maxcon."</maxconn>
	<docroot>".$docroot."</docroot>
	<timeout>".$timeout."</timeout>
	</options>
</document>";
		
		return $xmlconfig;
	}
	

function getlinks($url,$forbidden_types,$level,$exclude_names,$method,$maxcon,$timeout) {
	global $stack,$linkarray,$linkarraykey;
	$tmp_ret_array = array();
	$linkarray= array(); 
	$linkarraykey=array();
	$tmparr_last=array();
	$ret_array=array();
	
	$level=$level+1;	

	/* changed $tmparr with $tmparr_last because of array_diff of first crawl) */
	is_array($url)?$tmparr=$url:$tmparr[]=$url;
	//(count($arrurl)<41)?$z=1:$z=count($arrurl);
	(count($tmparr)>$maxcon)?$z=$maxcon:$z=count($tmparr);
	

			
	for ($u=0;$u<$level;$u++){
		$tmparr_last=array_diff($tmparr,$tmparr_last);
		(count($tmparr_last)>$maxcon)?$z=$maxcon:$z=count($tmparr_last);
		$tmparr=array_unique(getUrl(connect($tmparr_last,$z,$method,$timeout),$forbidden_types,$exclude_names));		
		$linkarraykey=array_unique(getUrl($linkarraykey,$forbidden_types,$exclude_names));
		/*$linkarray=array_unique(getUrl($linkarray,$forbidden_types,$exclude_names));
		 tmparr_last_from = inlinks 
		$tmparr_last_from=$tmparr_last; */		
		
		foreach ($tmparr_last as $orr_key => $tmpurl) {


			/* todo: linkcount auf referenzenseite stimmt nocht nicht */
			
			
			$retsize=count($ret_array);

			for ($i=0;$i<$retsize;$i++){
				if($ret_array[$i]['url']==$tmpurl){
					$retsize=$i;
					break;
				}
			}
			
			$ret_array[$retsize]['url']=$tmpurl;
			
			$linkarraykeysize=count($linkarraykey);
			for ($i=0;$i<$linkarraykeysize;$i++){
				if($linkarraykey[$i]==$tmpurl){
					$linkarraysize=count($linkarray);
					for($z=0;$z<$linkarraysize;$z++){
						$ret_array[$retsize]['out_links'][]=$linkarray[$i][$z];
					}
				}
			}
			if(is_array($ret_array[$retsize]['out_links'])) $ret_array[$retsize]['out_links']=array_unique($ret_array[$retsize]['out_links']);
				if (!isset($ret_array[$retsize]['level'])) $ret_array[$retsize]['level']=$u;
				$ret_array[$retsize]['PR']=0;
		
		}
		
	}

	return $ret_array;
}

function in_array_like($referencia,$array){ 
      foreach($array as $ref){ 
        if (strstr($referencia,$ref)){          
          return true; 
        } 
      } 
      return false; 
    }


function array_search_recursive($needle, $haystack, $path=array())
{
    foreach($haystack as $id => $val)
    {
         $path2=$path;
         $path2[] = $id;
 
         if($val === $needle)
              return $path2;
         else if(is_array($val))
              if($ret = array_search_recursive($needle, $val, $path2))
                   return $ret;
      }
      return false;
}

 
function recursive_in_array($needle, $haystack) {
    foreach ($haystack as $stalk) {
        if ($needle === $stalk || (is_array($stalk) && recursive_in_array($needle, $stalk))) {
            return true;
        }
    }
    return false;
}

function printprogress($url, $count){
	print "<script language='JavaScript' type='text/javascript'>
				<!--
  					var d = document.getElementById('statusinfo');
  					if (d) d.innerHTML = '<b>&nbsp;Lese: </b>".$url." &nbsp; <b>".$count."</b> Links gefunden. Noch zu durchsuchende Seiten <b>".$count."</b>';
				//-->
			</script>";

}

function calcPriorities($urls){
	$website = JRequest::getVar( 'http_host', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$damp = 0.35;
	$iterate = 40; # loop 40 times
	$thesum=0;
	/*# Plain Heirarchical
	# forward links
	# a -> b     - 1 outgoing link  - home
	# b -> a,c   - 2 outgoing links - doc page 1
	# c -> b,a,d - 3 outgoing links - doc page 2
	# d -> a,c   - 2 outgoing links - doc page 3
	
	# i.e. "backward" links (what's pointing to me?)
	# a <= b/2, c/3, d/2
	# b <= a, c/3
	# c <= b/2, d/2
	# d <= c/3
	*/
	
	/* PR is always 0 */
	
	while ($iterate--) {
		$linkssize=count($urls);
		for($i=0;$i<$linkssize;$i++){
			//if(is_array($urls[$i]['out_links'])){
				//foreach ($urls[$i]['url'] as $out_links){
					$tempIndex_arr=getIndexfromURL($urls,$urls[$i]['url']);
					
					foreach($tempIndex_arr as $tempIndex_arr_url){
						$tempCount=count($urls[$tempIndex_arr_url]['out_links']);
						if ($tempCount>0) $thesum = $thesum + ($urls[$tempIndex_arr_url]['PR']/$tempCount);
					}
					
				if((1 - $damp) + ($damp * $thesum)>1 or $urls[$i]['url']==$website){
					$urls[$i]['PR']="1.0";
				} else {
					$urls[$i]['PR'] = round((1 - $damp) + ($damp * $thesum),2);
				}
				//echo " | ".$urls[$i]['PR'];
				$thesum=0;
			//}
		    /*$a = 1 - $damp + $damp * ($b/2 + $c/3 + $d/2);
		    $b = 1 - $damp + $damp * ($a + $c/3);
		    $c = 1 - $damp + $damp * ($b/2 + $d/2);
		    $d = 1 - $damp + $damp * ($c/3);*/
		} 
	}

	return $urls;
	
}

	function getIndexfromURL($links,$url){
		$linked_arr=array();
		$linkssize=count($links);
		for($i=0;$i<$linkssize;$i++){
			if(strpos($links[$i]['url'],$url)!==FALSE) $linked_arr[]=$i;
		}
		
		return $linked_arr;
	}


function genSitemap($priority, $urls, $freq, $document_root){
	global $mainframe;
	if($priority=="auto") $urls=calcPriorities($urls);
	
	$xml_string = '<?xml version=\'1.0\' encoding=\'UTF-8\'?><?xml-stylesheet type="text/xsl" href="'.$document_root.'/administrator/components/com_jcrawler/sitemap.xsl"?>
	<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	$i=0;
	
	foreach ($urls as $loc){
		$i++;
		/* urf-8 encoding */
		//$loc=htmlentities($loc,ENT_QUOTES,'UTF-8');
		//$loc=htmlspecialchars($loc,ENT_QUOTES,'UTF-8',false);
		$loc['url']=htmlspecialchars($loc['url']);
		
		$modified_at = date('Y-m-d\Th:i:s\Z');
		$xml_string .= "
		<url>
		   <loc>".$loc['url']."</loc>
		   <lastmod>$modified_at</lastmod>
		   <priority>";
		   ($priority=="auto")?($xml_string .= $loc['PR']):($xml_string .= $priority);
		   $xml_string .= "</priority>
		   <changefreq>".$freq."</changefreq>
		</url>";
	}
	
	$xml_string .= "
	</urlset>";
	
	$mainframe->enqueueMessage( "There are $i links in your sitemap." );
	
	return $xml_string;
}

// misc functions

function toArray($str, $delim = "\n") {
	$res = array();	
	$res = explode($delim, $str);
	
	for($i = 0; $i < count($res); $i++) {
		$res[$i] = trim($res[$i]);
	}
	
	return $res;
}
/* returns a string of all entries of array with delim */
function arrToString($array, $delim = "\n") {
  $res = "";
  if (is_array($array)) {
	for ($i = 0; $i < count($array); $i++) {
	  $res .= $array[$i];
	  if ($i < (count($array)-1)) $res .= $delim;
	}
   }
   return $res;
}

/* simple compare function: equals */
function ar_contains($key, $array) {
  if (is_array($array) && count($array) > 0) {
    foreach ($array as $val) {
	  	if ($key == $val) {
			return true;
		}
    }
  }
  return false;
}

/* better compare function: contains */
function fl_contains($key, $array) {
  if (is_array($array) && count($array) > 0) {
	foreach ($array as $val) {
	  $pos = strpos($key, $val);
	  //$pos2 = @strpos($key, myUrlcode($val));
	  //print $key." | ".$pos2." | ".myUrlcode($val)." | ".$val." | ".$pos."<br>";
	  // and $pos2 === false
	  if ($pos === false) continue;
		return true;
    }/* todo: vergleich optimieren/säubern */
  }
  return false;
}

/* better compare function: begins */
function fl_begins($key, $array) {
	//print $key;print_r($array);
  if (is_array($array) && count($array) > 0) {
	foreach ($array as $val) {
		//print $key." | ".$val." | ".substr($key,0,strlen($val))."\n";
		//substr($key,0,strlen(myUrlcode($val)))==$val or 
		if (substr($key,0,strlen($val))==$val){
		  return true;
		}
	}
  }
  return false;
}

/* this function changes a substring($old_offset) of each array element to $offset */
function changeOffset($array, $old_offset, $offset) {
  $res = array();
  if (is_array($array) && count($array) > 0) {
    foreach ($array as $val) {
      $res[] = str_replace($old_offset, $offset, $val);
    }
  }
  return $res;
}

	function parsebuffer($buffer,$key){
		global $linkarray,$linkarraykey;
		/*$buffer=strtolower($buffer);*/
		$ret_treffer=array();
		//$suchmuster='<a href=("|\')(.*?)("|\')>';
		$suchmuster='/<a[\s]+[^>]*href\s*=\s*[\"\']?([^\'\" >]+)[\'\" >]/i';
		preg_match_all($suchmuster,$buffer, $treffer);
		preg_match('/Location:(.*?)\n/', $buffer, $matches);
				
		foreach($matches as $match){
			if(strpos($match,"Location:")===false) $treffer[1][]=trim($match);
		}
		unset($matches);
		/*foreach($treffer[2] as $val){
			$ret_treffer[][]=$val;
		}*/
		//print_r($ret_treffer);
		//$keys[]=$key;
		//$thekey=array_search($key,$linkarraykey);
		
		if(!in_array($key,$linkarraykey)) $linkarraykey[]=$key;
		$thekey=array_search($key,$linkarraykey);
		
		/*if (isExtensionLoaded("dom")){
			$buffer=preg_replace('/<head[^>]*>/','<head>
			<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"> 
			',$buffer);
			$doc = new DOMDocument(); // Create a new DOMDocument object in $doc 
	    	@$doc->loadHTML($buffer); // Load the contents of our desired website into $doc
	    	$a = $doc->getElementsByTagName('a'); // Get all of the 'a' XHTML tags and there attributes and store in array $a
	    	if(is_a($a,"DOMNodeList")){
	    		foreach($a as $link) { // Iterate through $a
	      			$treffer[2][] = $link->getAttribute('href'); // Access each 'a' tag and get the contents of its 'href' attribute
	    		}
	    	}
	    	unset($a);
	    	unset($doc);
		}*/
		
		
		
		foreach($treffer[1] as $val){
			$linkarray[$thekey][]=$val;	
		}
		
		$ret_treffer = array_unique($treffer[1]);
		return $ret_treffer;
	
	}


	function isExtensionLoaded($name){
		 // Returns an Array of all Extensions loaded in PHP
		 $loaded = get_loaded_extensions();
		 if (array_search($name, $loaded)===false){
				return false;				
		 } else {
				return true; 
		 }
	}
	
	function checkfopen() {
		
		$val=ini_get('allow_url_fopen');
		if($val=="" or $val==0){
			return false;	
		}elseif($val=="On" or $val==1){
			return true;	
		}
	} 
	
	
	function parsePHPinfo() {
	 ob_start();
	phpinfo();
	$phpinfo = array('phpinfo' => array());
	if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?(?: colspan=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
		foreach($matches as $match)
			if(strlen($match[1]))
				$phpinfo[$match[1]] = array();
			elseif(isset($match[3]))
				$phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			else
				$phpinfo[end(array_keys($phpinfo))][] = $match[2];
				
		return $phpinfo;
	}    
	
	

function connect ($url,$z,$method,$timeout){
	global $mainframe,$_POST;

	$buffer= array();
	$str  = array(
		"Accept-Language: en-us,en;q=0.5",
		"Accept-Charset: utf-8;q=0.7,*;q=0.7",
		"Keep-Alive: 300",
		"Connection: keep-alive",
		"Pragma: ");
	
	if (isExtensionLoaded("curl") and (!function_exists('curl_multi_init') or $z==1) and $method=="curl") {
		
		$tmp_buffer=array();
		$ch = curl_init();$i=0;
		foreach ($url as $key) {
		
			 // erzeuge einen neuen cURL-Handle
	 			curl_setopt($ch, CURLOPT_URL, $key);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $str);
				curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, false);
				curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
				
	 
				 // führe die Aktion aus und gebe die Daten an den Browser weiter
				 $tmp_buffer[$i]=curl_exec($ch);
				 
				$curlError = curl_error($ch);
				if($curlError != "") {
	        		$mainframe->enqueueMessage("Curl error on url $urlentry: $curlError",error);
	      		}
					
				$http_code=curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($http_code>=400) {
					 $mainframe->enqueueMessage(htmlentities("httpcode: ".$http_code." on url ".$key) ,error);
				 } else {
					$buffer = array_unique(array_merge($buffer,parsebuffer($tmp_buffer[$i],$key)));
				 }
				 $i++;
		} //end foreach
		unset($tmp_buffer,$url);
	 	curl_close ($ch);
	
	} elseif (isExtensionLoaded("curl") and function_exists('curl_multi_init') and $z>1 and $method=="curl") {
		
		if(count($url)!=0){
			$k=ceil(count($url)/$z);
			$urls=array_chunk($url, ceil(count($url) / $k),true);
		}else {
			$k=0;
		}
		$mh = curl_multi_init();
		
		for ($i=0;$i<$k;$i++){	

			foreach ($urls[$i] as $key => $urlentry){
				$ch[$key] = curl_init($urlentry);
				curl_setopt($ch[$key], CURLOPT_HEADER, true);
				curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch[$key], CURLOPT_REFERER, "http://www.google.com");
				curl_setopt($ch[$key], CURLOPT_HTTPHEADER, $str);
				curl_setopt($ch[$key], CURLOPT_USERAGENT, "Googlebot/2.1 (+http://www.google.com/bot.html)");
				curl_setopt($ch[$key], CURLOPT_VERBOSE, true);
				curl_setopt($ch[$key], CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($ch[$key], CURLOPT_TIMEOUT, $timeout);
				curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch[$key], CURLOPT_FRESH_CONNECT, false);
				curl_setopt($ch[$key], CURLOPT_FORBID_REUSE, false);
				
				curl_multi_add_handle($mh,$ch[$key]);
			}
			
			

		$runningHandles=null;
			//execute the handles
			/*do {
				curl_multi_exec($mh,$runningHandles);
				// added a usleep for 0.25 seconds to reduce load
				usleep (250000);
			} while ($runningHandles > 0);
			*/
			
			 // Start performing the request
			 do {
				  $execReturnValue = curl_multi_exec($mh, $runningHandles);
			 } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);
			 // Loop and continue processing the request
			 while ($runningHandles && $execReturnValue == CURLM_OK) {
				// Wait forever for network
				$numberReady = curl_multi_select($mh);
				if ($numberReady != -1) {
				  // Pull in any new data, or at least handle timeouts
				  do {
					 $execReturnValue = curl_multi_exec($mh, $runningHandles);
				  } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);
				}
			 }
			
			// Check for any errors
		 if ($execReturnValue != CURLM_OK) {
			 $mainframe->enqueueMessage("Curl multi read error $execReturnValue\n",error);
		 }				
			
			
			foreach ($urls[$i] as $key => $urlentry){ 
				$curlError = curl_error($ch[$key]);
				
				if($curlError != "") {
        			$mainframe->enqueueMessage("Curl error on url $urlentry: $curlError",error);
      			}
				
				$http_code=curl_getinfo($ch[$key], CURLINFO_HTTP_CODE);
				if ($http_code>=400) {
					 $mainframe->enqueueMessage(htmlentities("httpcode: ".$http_code." on url ".$urlentry) ,error);
					 
				 } else {
					$buffer = array_unique(array_merge($buffer,parsebuffer(curl_multi_getcontent ($ch[$key]),$urlentry)));
				 }
				// Remove and close the handle
				curl_multi_remove_handle($mh,$ch[$key]);
				curl_close($ch[$key]);
			}
			unset($url,$ch);
		}
		
			curl_multi_close($mh);
			unset($urls,$mh);
	 	
	} elseif (function_exists('fopen')  and $method=="fopen"){
	
		foreach ($url as $key) {
			
			$handle = @fopen ($key, "r");
			
			$return_code = @explode(' ', $http_response_header[0]);
    		$return_code = (int)$return_code[1];
			
			if ($return_code>=400) {
				 $mainframe->enqueueMessage(htmlentities($http_response_header[0]." on url ".$key) ,error);
			} elseif(!$handle) {
				$mainframe->enqueueMessage(htmlentities("Could not connect to ".$key) ,error);
			} else {
				while (!feof($handle)) {
					$buffer = array_unique(array_merge($buffer,parsebuffer(fgets($handle),$key)));
				}
			}
			@fclose($handle);
		}
		unset($url);
		
	} elseif (function_exists('file_get_contents')) {
		foreach ($url as $key) {
			
			$data=file_get_contents($key);
			$return_code = @explode(' ', $http_response_header[0]);
    		$return_code = (int)$return_code[1];
			
			if ($return_code>=400) {
				 $mainframe->enqueueMessage(htmlentities($http_response_header[0]." on url ".$key) ,error);
			} else {
				while (!feof($handle)) {
					$buffer = array_unique(array_merge($buffer,parsebuffer($data,$key)));
				}
			}
		}
		unset($url);
	} else {
		$mainframe->enqueueMessage("You need curl or fopen, neither of them were available",error);
	}
	
	return $buffer;

}


/* this walks recursivly through all directories starting at page_root and
   adds all files that fits the filter criterias */
// taken from Lasse Dalegaard, http://php.net/opendir
function getUrl($buffer,$forbidden_types,$forbidden_strings) {
	global $_POST, $stack;
	$website = JRequest::getVar( 'http_host', 'none', 'POST', 'STRING', JREQUEST_ALLOWHTML );
	$web=parse_url($website);
	(strtolower(substr($web['host'],0,4))=="www.")?$web['host']=substr($web['host'],4):null;

	/*if(substr($website,-1)=="/") $website=substr($website,0,-1);
	if(substr($web['path'],-1)=="/") $web['path']=substr($web['path'],0,-1);*/
	
	$tmparray=array();
		foreach ($buffer as $key) {
			if ($web['scheme']."://www.".$web['host']."/"==$key or $web['scheme']."://".$web['host']."/"==$key){
				$key=$website; 
			}
			if(strtolower(substr($key,0,4))!="http"){
				// slash management
				if(substr($key,0,1)=="/" and substr($website,-1)=="/" ){
					$key=substr($key,1);
					//print $key."<br>";
				}
				// $website: support-masters.ch/
				($web['path']!="" and $web['path']!="/")?$key=substr($website,0,strpos($website,$web['path'])).$key:$key=$website.$key;
			}
			
			//print_r($web['scheme']."://(.*?)\.".$web['host']); die();
			$key=preg_replace(array('/([\?&]PHPSESSID=\w+)$/i','/(#[^\/]*)$/i', '/&amp;/','/^(javascript:.*)|(javascript:.*)$/i'),array('','','&','',''),$key);
			$suchmuster="/".$web['scheme'].":\/\/(.*?)".$web['host'].str_replace('/','\/',$web['path'])."/";
			preg_match($suchmuster,$key,$treffer);
			
			$key=myUrlcode(trim(relative2absolute($website,$key)));
			//print $key."\n";
			/* todo add url from Location: header tag without any check */
			
			//&& fl_begins($key,$treffer)==true
			
			if(!in_array($key,$tmparray) && !in_array($key,$stack) && count($treffer)>0 && fl_contains($key, $forbidden_strings)==false && in_array(substr($key,strrpos($key,".")),$forbidden_types)===false){
				//$key=trim(str_replace("&amp;","&",$key));
				//print trim(myUrlcode($key))."<br>";
				$tmparray[]=$key;
			}
			unset($key,$treffer);
		} //endforeach
		unset($buffer);
		
	return $tmparray;
}

/* rawurlencode for utf8 language strings*/
function myUrlcode($url,$encode=1) {
  // Make sure we have a string to work with
	//testurl http://www.playpo.co.il/??????/
  if(!empty($url)) {
    // Explode into URL keys
    $urllist=parse_url($url);

	
    // Make sure we have a valid result set and a query field
    if(is_array($urllist) && isset($urllist["query"])) {
      // Explode into key/value array
      $keyvalue_list=explode("&",($urllist["query"]));

      // Store resulting key/value pairs
      $keyvalue_result=array();

      foreach($keyvalue_list as $value) {
        // Explode each individual key/value into an array
        $keyvalue=explode("=",$value);

        // Make sure we have a "key=value" array
        if(count($keyvalue)==2) {
          // Encode the value portion
          ($encode==1)?($keyvalue[1]=rawurlencode($keyvalue[1])):($keyvalue[1]=rawurldecode($keyvalue[1]));

          // Add our key and encoded value into the result
          $keyvalue_result[]=implode("=",$keyvalue);
        }
      }

      // Repopulate our query key with encoded results
      $urllist["query"]=implode("&",$keyvalue_result);
      // Build the the final output URL
	} //end if isset query
	
	if(is_array($urllist) && isset($urllist["path"])) {
      // Explode into key/value array
      $keyvalue_list2=explode("/",($urllist["path"]));

      // Store resulting key/value pairs
      $keyvalue_result2=array();

      foreach($keyvalue_list2 as $value2) {
       
          // Encode the value portion
          ($encode==1)?($val2=rawurlencode($value2)):($val2=rawurldecode($value2));

          // Add our key and encoded value into the result
         $keyvalue_result2[]=$val2;
      }

      // Repopulate our query key with encoded results
      $urllist["path"]=implode("/",$keyvalue_result2);
      unset($keyvalue_list2,$keyvalue_result2,$keyvalue_list,$keyvalue_result);
      // Build the the final output URL
	} //end if isset query
	
	
      $url=(isset($urllist["scheme"])?$urllist["scheme"]."://":"").
           (isset($urllist["user"])?$urllist["user"].":":"").
           (isset($urllist["pass"])?$urllist["pass"]."@":"").
           (isset($urllist["host"])?$urllist["host"]:"").
           (isset($urllist["port"])?":".$urllist["port"]:"").
           (isset($urllist["path"])?cleanPath($urllist["path"]):"").
		   (isset($urllist["query"])?"?".$urllist["query"]:"").
           (isset($urllist["fragment"])?"#".$urllist["fragment"]:"");
    unset($urllist);
  }
	
  return $url;
}

function relative2absolute($base, $relative) {
if (stripos($base, '?')!==false) { $base=explode('?', $base);$base=$base[0];}
if (strtolower(substr($relative, 0, 4))=='http') {
return $relative;
} else {
$bparts=explode('/', $base, -1);
$rparts=explode('/', $relative);
foreach ($rparts as $i=>$part) {
if ($part=='' || $part=='.') {
unset($rparts[$i]);
if ($i==0) {$bparts=array_slice($bparts, 0, 3);}
} elseif ($part=='..') {
unset($rparts[$i]);
$done=false;
for ($j=$i-1;$j>=0;$jÐ) {if (isset($rparts[$j])) {unset($rparts[$j]); $done=true; break;}}
if (!($done) && count($bparts)>3) {array_pop($bparts);}
}
}
return implode('/', array_merge($bparts, $rparts));
}
}

function cleanPath($path)
    {
        $path = explode('/', preg_replace('#(/+)#', '/', $path));
 
        for ($i = 0; $i < count($path); $i ++) {
            if ($path[$i] == '.') {
                unset ($path[$i]);
                $path = array_values($path);
                $i --;
 
            }
            elseif ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != ''))) {
                unset ($path[$i]);
                unset ($path[$i -1]);
                $path = array_values($path);
                $i -= 2;
 
            }
            elseif ($path[$i] == '..' AND $i == 1 AND $path[0] == '') {
                unset ($path[$i]);
                $path = array_values($path);
                $i --;
 
            } else {
                continue;
            }
        }
 
        return implode('/', $path);
    }



?>