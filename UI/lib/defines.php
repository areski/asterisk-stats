<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));	


define ("FSROOT", substr(dirname(__FILE__),0,-3));

define ("LIBDIR", FSROOT."lib/");


include (dirname(__FILE__)."/Class.Table.php");



define ("HOST", "localhost");
define ("PORT", "5432");
define ("USER", "root");
define ("PASS", "password");
define ("DBNAME", "asterisk-stat");
define ("DB_TYPE", "mysql"); // mysql or postgres


define ("SCHEDULER_TABLENAME","scheduler");
define ("SCHEDULER_LOGFILE","/var/log/asterisk/scheduler.log");
define ("SCHEDULER_PDF_PATH","/tmp/scheduler");
define ("SCHEDULER_MAIL_FROM","root@localhost");

// Regarding to the dst you can setup an application name
// Make more sense to have a text that just a number
// especially if you have a lot of extension in your dialplan
$appli_list['1711']=array("myappli_01");


// ENABLE TO LINK ON THE MONITOR FILES
define ("LINK_AUDIO_FILE", "NO"); // value : YES - NO


// PATH OF THE LOGS FILE
define ("LOGGILE_PATH", "/var/log/asterisk/"); 

// PATH TO LINK ON THE RECORDED MONITOR FILES
define ("MONITOR_PATH", "/var/spool/asterisk/monitor");  // value : /var/spool/asterisk/monitor/
// think to grant access to apache on read mode on this directory :>  chmod 755 /var/spool/asterisk/monitor/


// FORMAT OF THE RECORDED MONITOR FILE 
define ("MONITOR_FORMATFILE", "gsm"); 

//Monitor(wav,${UNIQUEID}) 

define ("MANAGER_HOST", "127.0.0.1");
define ("MANAGER_USERNAME", "myasterisk");
define ("MANAGER_SECRET", "mycode");


define ("USERFIELD", "Operator");

// Copyright & Title information 
define ("MAINTITLE", "..:: : ASTERISK-STATS : ::..");
define ("COPYRIGHT", gettext(" This software is under GPL licence. For further information, please visit : <a href=\"http://www.asterisk2billing.org\" target=\"_blank\">asterisk2billing.org</a>"));
define ("WEBUI_DATE", '');	 
define ("WEBUI_VERSION", 'Asterisk-Stats - Version 1.0');



// USE FLASH CHART : http://www.maani.us for flash Chart
define ("FLASHCHART", true);

/*
 *		GLOBAL USED VARIABLE
 */
$PHP_SELF = $HTTP_SERVER_VARS["PHP_SELF"];
$CURRENT_DATETIME = date("Y-m-d H:i:s");



define ("ENABLE_LOG", 1);
include (FSROOT."lib/Class.Logger.php");
$log = new Logger();
include (FSROOT."lib/help.php");
// 
// The system will not log for Public/index.php and 
// signup/index.php
$URI = $_SERVER['REQUEST_URI'];
$restircted_url = substr($URI,-16);
if(!($restircted_url == "Public/index.php") && !($restircted_url == "signup/index.php") && isset($_SESSION["admin_id"])) {
	$log -> insertLog($_SESSION["admin_id"], 1, "Page Visit", "User Visited the Page", '', $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'],'');
}
$log = null;




/*
 *		GLOBAL USED VARIABLE
 */
$PHP_SELF = $_SERVER["PHP_SELF"];


$CURRENT_DATETIME = date("Y-m-d H:i:s");		
	
/*
 *		GLOBAL POST/GET VARIABLE
 */		 
getpost_ifset(array('form_action', 'atmenu', 'action', 'stitle', 'sub_action', 'IDmanager', 'current_page', 'order', 'sens', 'mydisplaylimit', 'filterprefix', 'cssname', 'popup_select'));

/*
 *		CONNECT / DISCONNECT DATABASE
 */


session_start();


if (!isset($_SESSION["language"]))
{
  $_SESSION["language"] = 'english';
}
else if (isset($language))
{
  $_SESSION["language"] = $language;
}
define ("LANGUAGE",$_SESSION["language"]);
require_once("languageSettings.php");
SetLocalLanguage();

function DbConnect($db= NULL)
{
	// $ADODB_CACHE_DIR = dirname(__FILE__)."/ADODB_cache";
	$ADODB_CACHE_DIR = "/tmp/ADODB_cache";
	/*	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;	*/
	require_once(LIBDIR.'/adodb-495a/adodb.inc.php'); // AdoDB
	
	if (DB_TYPE == "postgres"){
		$datasource = 'pgsql://'.USER.':'.PASS.'@'.HOST.'/'.DBNAME;
	}elseif (DB_TYPE == "mysql"){
		$datasource = 'mysql://'.USER.':'.PASS.'@'.HOST.'/'.DBNAME;
	}else{			
		$datasource = 'mssql://'.USER.':'.PASS.'@'.HOST.'/'.DBNAME;			
	}
	
	$DBHandle = NewADOConnection($datasource);
	if (!$DBHandle) die("Connection failed");
	
	return $DBHandle;
}


function DbDisconnect($DBHandle)
{
	$DBHandle ->disconnect();
}


getpost_ifset(array('cssname'));
	
if(isset($cssname) && $cssname != "")
{
	$_SESSION["stylefile"] = $cssname;
}

if(isset($cssname) && $cssname != "")
{
	if ($_SESSION["stylefile"]!=$cssname){
		foreach (glob("./templates_c/*.*") as $filename)
		{
			unlink($filename);
		}			
	}
	$_SESSION["stylefile"] = $cssname;		
}

if(!isset($_SESSION["stylefile"]) || $_SESSION["stylefile"]==''){
	$_SESSION["stylefile"]='default';
}

//Images Path
define ("Images_Path","../Public/templates/".$_SESSION["stylefile"]."/images");
define ("Images_Path_Main","../Public/templates/".$_SESSION["stylefile"]."/images");
define ("KICON_PATH","../Public/templates/".$_SESSION["stylefile"]."/images/kicons");









/*
 *	  -%-%-%-%-%-%-		MISC FUNCTIONS 		-%-%-%-%-%-%-
 */
 
 
function getpost_ifset($test_vars)
{
	if (!is_array($test_vars)) {
		$test_vars = array($test_vars);
	}
	foreach($test_vars as $test_var) { 
		if (isset($_POST[$test_var])) { 
			global $$test_var;
			$$test_var = $_POST[$test_var]; 
		} elseif (isset($_GET[$test_var])) {
			global $$test_var; 
			$$test_var = $_GET[$test_var];
		}
	}
}


function display_minute($sessiontime){
	global $resulttype;
	if ((!isset($resulttype)) || ($resulttype=="min")){  
		$minutes = sprintf("%02d",intval($sessiontime/60)).":".sprintf("%02d",intval($sessiontime%60));
	}else{
		$minutes = $sessiontime;
	}
	echo $minutes;
}


function display_2dec($var){		
	echo number_format($var,2);
}


function display_2bill($var){	
	$var=$var/100;
	echo '$ '.number_format($var,2);
}


function remove_prefix($phonenumber){
	
	if (substr($phonenumber,0,3) == "011"){
		echo substr($phonenumber,3);
		return 1;
	}
	echo $phonenumber;
}


function display_acronym($field){		
	echo '<acronym title="'.$field.'">'.substr($field,0,7).'...</acronym>';		
}


function linkonmonitorfile($value){
	
	$myfile = $value.".".MONITOR_FORMATFILE;
	$myfile = base64_encode($myfile);
	echo "<a target=_blank href=\"call-log.php?download=file&file=".$myfile."\">";
	echo '<img src="../images/stock-mic.png" height="18" /></a>';
	
}


function get_context($DBHandle=null, $AsteriskDstCtx){

	if ($DBHandle == null) $DBHandle = DbConnect();

	echo '<SELECT name="AsteriskDstCtx"><OPTION value="" selected>ALL</OPTION>';

	$res = $DBHandle -> CacheExecute(86400,"SELECT distinct(AsteriskDstCtx) as context from ".DB_TABLENAME);
	if (($res) || ($res->RecordCount() > 0)) {
		while(!$res->EOF){
			$selected='';
			$context = $res->fields['context'];
			if ((isset($AsteriskDstCtx)) && ($AsteriskDstCtx == $context)) $selected='selected';
			?><OPTION value="<?php echo $context; ?>" <?php echo $selected;?>><?php echo $context; ?></OPTION><?php
			$res->MoveNext();
		}
	}
        $res->Close();
	echo '</SELECT>';

}




function do_field_duration($sql,$fld, $fldsql){
	$fldtype = $fld.'type';
	global $$fld;
	global $$fldtype;				
	if (isset($$fld) && ($$fld!='')){
		if (strpos($sql,'WHERE') > 0){
			$sql = "$sql AND ";
		}else{
			$sql = "$sql WHERE ";
		}
		$sql = "$sql $fldsql";
		if (isset ($$fldtype)){                
			switch ($$fldtype) {
				case 1:	$sql = "$sql ='".$$fld."'";  break;
				case 2: $sql = "$sql <= '".$$fld."'";  break;
				case 3: $sql = "$sql < '".$$fld."'";  break;							
				case 4: $sql = "$sql > '".$$fld."'";  break;
				case 5: $sql = "$sql >= '".$$fld."'";  break;
			}
		}else{ $sql = "$sql = '".$$fld."'"; }
	}
	return $sql;
}

function do_field($sql,$fld){
	$fldtype = $fld.'type';
	global $$fld;
	global $$fldtype;
	if (isset($$fld) && ($$fld!='')){
		if (strpos($sql,'WHERE') > 0){
				$sql = "$sql AND ";
		}else{
				$sql = "$sql WHERE ";
		}
		$sql = "$sql $fld";
		if (isset ($$fldtype)){                
			switch ($$fldtype) {
				case 1:	$sql = "$sql='".$$fld."'";  break;
				case 2: $sql = "$sql LIKE '".$$fld."%'";  break;
				case 3: $sql = "$sql LIKE '%".$$fld."%'";  break;
				case 4: $sql = "$sql LIKE '%".$$fld."'";
			}
		}else{ $sql = "$sql LIKE '%".$$fld."%'"; }
	}
	return $sql;
}

function create_arr_color () {
	$arr = array ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
	
	for ($i=0 ; $i < 16 ; $i=$i+3){
		$t1 = rand(5,15);
		$t2 = rand(5,15);
		$t3 = rand(5,15);
		
		for ($j=0 ; $j < 16 ; $j=$j+3){
			
			$t4 = rand(5,15);
			$t5 = rand(5,15);
			$t6 = rand(5,15);
			
			$color[] = $arr[$i].$arr[$t2].$arr[$t3].$arr[$j].$arr[$t5].$arr[$t5];
		}
	}
	
	$color = array_unique($color);
	shuffle ($color);
	return ($color);
}


?>
