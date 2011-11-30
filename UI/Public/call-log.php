<?php 
include ("../lib/defines.php");
include ("../lib/module.access.php");
include ("../lib/smarty.php");

if (! has_rights (ACX_ACCESS)){ 
	Header ("HTTP/1.0 401 Unauthorized");
	Header ("Location: PP_error.php?c=accessdenied");	   
	die();	   
}

if ( (isset($_GET["download"])) && ($_GET["download"]=="file") && $_GET["file"] ) 
{
	
	$value_de=base64_decode($_GET[file]);
	$dl_full = MONITOR_PATH."/".$value_de;
	$dl_name=$value_de;

	if (!file_exists($dl_full))
	{ 
		echo"ERROR: Cannot download file $dl_full, it does not exist.<br>";  
		exit();
	} 
	
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$dl_name");
	header("Content-Length: ".filesize($dl_full));
	header("Accept-Ranges: bytes");
	header("Pragma: no-cache");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-transfer-encoding: binary");
	@readfile($dl_full);
	exit();
}


session_start();

getpost_ifset(array('posted', 'Period', 'frommonth', 'fromstatsmonth', 'tomonth', 'tostatsmonth', 'fromday', 'fromstatsday_sday', 'fromstatsmonth_sday', 'today', 'tostatsday_sday', 'tostatsmonth_sday', 'AsteriskDsttype', 'AsteriskSrctype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx', 'AsteriskUniqueID', 'AsteriskUniqueIDType', 'duration1', 'duration1type', 'duration2', 'duration2type', 'fromstatshour', 'fromstatsminute', 'tostatshour', 'tostatsminute'));



if (!isset ($current_page) || ($current_page == "")){	
	$current_page=0; 
}


// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;



// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_HEAD_COLOR = "#D1D9E7";


$FG_TABLE_EXTERN_COLOR = "#7F99CC"; //#CC0033 (Rouge)
$FG_TABLE_INTERN_COLOR = "#EDF3FF"; //#FFEAFF (Rose)
$FG_ACTION_SIZE_COLUMN = '';



// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#FFFFFF";
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#F2F8FF";



//$link = DbConnect();
$DBHandle  = DbConnect();

// The variable Var_col would define the col that we want show in your table
// First Name of the column in the html page, second name of the field
$FG_TABLE_COL = array();


/*******
AsteriskStartTime AsteriskClid AsteriskSrc AsteriskDst AsteriskDstCtx NASIPAddress  AsteriskDstChan AsteriskLastApp AsteriskLastData 
AsteriskDuration AsteriskBillSec AsteriskDisposition AsteriskAMAFlags AsteriskDstCtx AsteriskUniqueID NASIPAddress
*******/


$FG_TABLE_COL[]=array ("Calldate", "AsteriskStartTime", "15%", "center", "SORT", "19");
$FG_TABLE_COL[]=array ("UniqueID", "AsteriskUniqueID", "8%", "center", "", "20");
$FG_TABLE_COL[]=array ("Server IP", "NASIPAddress", "9%", "center", "", "30", "", "", "", "", "", "");
$FG_TABLE_COL[]=array ("Source", "AsteriskSrc", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("CallerID", "AsteriskClid", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Lastapp", "AsteriskLastApp", "8%", "center", "", "30");

$FG_TABLE_COL[]=array ("Lastdata", "AsteriskLastData", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Dst", "AsteriskDst", "9%", "center", "SORT", "30");
// $FG_TABLE_COL[]=array ("APP", "AsteriskDst", "9%", "center", "", "30","list", $appli_list);
$FG_TABLE_COL[]=array ("Disposition", "AsteriskDisposition", "9%", "center", "", "30");
if ((!isset($resulttype)) || ($resulttype=="min")) $minute_function= "display_minute";
$FG_TABLE_COL[]=array ("Duration", "AsteriskDuration", "6%", "center", "SORT", "30", "", "", "", "", "", "$minute_function");

$FG_TABLE_COL[]=array ("Context", "AsteriskDstCtx", "8%", "center", "", "20");
$FG_TABLE_COL[]=array (USERFIELD, "AsteriskUserField", "11%", "center", "SORT", "30");

if (LINK_AUDIO_FILE == 'YES') 
	$FG_TABLE_COL[]=array ("", "AsteriskUniqueID", "1%", "center", "", "30", "", "", "", "", "", "linkonmonitorfile");


$FG_TABLE_DEFAULT_ORDER = "AsteriskStartTime";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
$FG_COL_QUERY=' AsteriskStartTime, AsteriskUniqueID, NASIPAddress, AsteriskSrc, AsteriskClid, AsteriskLastApp, AsteriskLastData, AsteriskDst, AsteriskDisposition, AsteriskDuration, AsteriskDstCtx, AsteriskUserField';
if (LINK_AUDIO_FILE == 'YES') 
	$FG_COL_QUERY .= ', AsteriskUniqueID';
	
	
$FG_COL_QUERY_GRAPH='AsteriskStartTime, AsteriskDuration';

// The variable LIMITE_DISPLAY define the limit of record to display by page
$FG_LIMITE_DISPLAY=25;

// Number of column in the html table
$FG_NB_TABLE_COL=count($FG_TABLE_COL);

// The variable $FG_EDITION define if you want process to the edition of the database record
$FG_EDITION=true;

//This variable will store the total number of column
$FG_TOTAL_TABLE_COL = $FG_NB_TABLE_COL;
if ($FG_DELETION || $FG_EDITION) $FG_TOTAL_TABLE_COL++;

//This variable define the Title of the HTML table
$FG_HTML_TABLE_TITLE=" - Call Logs - ";

//This variable define the width of the HTML table
$FG_HTML_TABLE_WIDTH="95%";




if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}

if ($posted==1){

	$SQLcmd = '';
	
	// FIELD TO ADD FROM THE SEARCH MODULE
	$SQLcmd = do_field($SQLcmd, 'AsteriskUniqueID');
	$SQLcmd = do_field($SQLcmd, 'AsteriskClid');
	$SQLcmd = do_field($SQLcmd, 'AsteriskSrc');
	$SQLcmd = do_field($SQLcmd, 'AsteriskDst');
	$SQLcmd = do_field($SQLcmd, 'AsteriskUserField');
	$SQLcmd = do_field($SQLcmd, 'AsteriskDstCtx');
	$SQLcmd = do_field($SQLcmd, 'NASIPAddress');
	$SQLcmd = do_field_duration($SQLcmd, 'duration1', 'AsteriskDuration');
	$SQLcmd = do_field_duration($SQLcmd, 'duration2', 'AsteriskDuration');
}


$date_clause='';
// Period (Month-Day)
if (DB_TYPE == "postgres"){		
	$UNIX_TIMESTAMP = "";
}else{		
	$UNIX_TIMESTAMP = "UNIX_TIMESTAMP";
}

if ($Period=="Month"){
	if ($frommonth && isset($fromstatsmonth)) $date_clause.=" AND $UNIX_TIMESTAMP(AsteriskStartTime) >= $UNIX_TIMESTAMP('$fromstatsmonth-01')";
	if ($tomonth && isset($tostatsmonth)) $date_clause.=" AND $UNIX_TIMESTAMP(AsteriskStartTime) <= $UNIX_TIMESTAMP('$tostatsmonth-31 23:59:59')";
}else{
	$from_hour_min = "$fromstatshour:$fromstatsminute:00";
	$to_hour_min = "$tostatshour:$tostatsminute:59";

	if ($fromday && isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND $UNIX_TIMESTAMP(AsteriskStartTime) >= $UNIX_TIMESTAMP('$fromstatsmonth_sday-$fromstatsday_sday $from_hour_min')";
	if ($today && isset($tostatsday_sday) && isset($tostatsmonth_sday)) $date_clause.=" AND $UNIX_TIMESTAMP(AsteriskStartTime) <= $UNIX_TIMESTAMP('$tostatsmonth_sday-".sprintf("%02d",intval($tostatsday_sday)/*+1*/)." $to_hour_min')";
}


  
if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}



if (!isset ($FG_TABLE_CLAUSE) || strlen($FG_TABLE_CLAUSE)==0){
	$cc_yearmonth = sprintf("%04d-%02d",date("Y"),date("n")); 	
	$FG_TABLE_CLAUSE=" $UNIX_TIMESTAMP(AsteriskStartTime) >= $UNIX_TIMESTAMP('$cc_yearmonth-01')";
}


if ($posted==1){
	$list = $instance_table -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, $order, $sens, null, null, $FG_LIMITE_DISPLAY, $current_page*$FG_LIMITE_DISPLAY);
	
	$_SESSION["pr_sql_export"]="SELECT $FG_COL_QUERY FROM $FG_TABLE_NAME WHERE $FG_TABLE_CLAUSE";
	
	
	/************************/
	$QUERY = "SELECT substring(AsteriskStartTime,1,10) AS day, sum(AsteriskDuration) AS calltime, count(*) as nbcall FROM ".$FG_TABLE_NAME." WHERE ".$FG_TABLE_CLAUSE." GROUP BY substring(AsteriskStartTime,1,10)"; //extract(DAY from calldate) 
	
	if ($FG_DEBUG == 3) echo "<br>QUERY :  $QUERY";
	
	$res = $DBHandle -> Execute($QUERY);
	$num = $res -> RecordCount();
	for($i=0;$i<$num;$i++)
	{				
		$list_total_day [] = $res -> fetchRow();				 
	}
	
	if ($FG_DEBUG == 3) echo "<br>Clause : $FG_TABLE_CLAUSE";
	$nb_record = $instance_table -> Table_count ($DBHandle, $FG_TABLE_CLAUSE);

}


if ($nb_record<=$FG_LIMITE_DISPLAY){ 
	$nb_record_max=1;
}else{ 
	if ($nb_record % $FG_LIMITE_DISPLAY == 0){
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY));
	}else{
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY)+1);
	}	
}


if ($FG_DEBUG == 3) echo "<br>Nb_record : $nb_record";
if ($FG_DEBUG == 3) echo "<br>Nb_record_max : $nb_record_max";

// #### HEADER SECTION
$smarty->display('main.tpl');

?>

<br><br><br>
<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->
	<center>
	<FORM METHOD=POST ACTION="<?php echo $PHP_SELF?>?s=<?php echo $s?>&t=<?php echo $t?>&order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php echo $current_page?>">
	<INPUT TYPE="hidden" NAME="posted" value=1>
	<INPUT TYPE="hidden" NAME="current_page" value=0>	
		<table class="bar-status" width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody>
			
			<?php
				include("date_criteria_select_month_day.php");
			?>
			
			<tr>
				<td class="bar-search" align="left" bgcolor="#000033">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;UNIQUE ID</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="AsteriskUniqueID" value="<?php echo $AsteriskUniqueID?>"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUniqueIDType" value="1" <?php if((!isset($AsteriskUniqueIDType))||($AsteriskUniqueIDType==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUniqueIDType" value="2" <?php if($AsteriskUniqueIDType==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUniqueIDType" value="3" <?php if($AsteriskUniqueIDType==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUniqueIDType" value="4" <?php if($AsteriskUniqueIDType==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">			
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;DESTINATION</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="AsteriskDst" value="<?php echo $AsteriskDst?>"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDsttype" value="1" <?php if((!isset($AsteriskDsttype))||($AsteriskDsttype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDsttype" value="2" <?php if($AsteriskDsttype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDsttype" value="3" <?php if($AsteriskDsttype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDsttype" value="4" <?php if($AsteriskDsttype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>			
			<tr>
				<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;SOURCE</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="AsteriskSrc" value="<?php echo "$AsteriskSrc";?>"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskSrctype" value="1" <?php if((!isset($AsteriskSrctype))||($AsteriskSrctype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskSrctype" value="2" <?php if($AsteriskSrctype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskSrctype" value="3" <?php if($AsteriskSrctype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskSrctype" value="4" <?php if($srctype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CALLER ID</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="AsteriskClid" value="<?php echo $AsteriskClid?>"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskClidtype" value="1" <?php if((!isset($AsteriskClidtype))||($AsteriskClidtype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskClidtype" value="2" <?php if($AsteriskClidtype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskClidtype" value="3" <?php if($AsteriskClidtype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskClidtype" value="4" <?php if($AsteriskClidtype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			<tr>
				<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;<?php echo strtoupper(USERFIELD);?></b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="AsteriskUserField" value="<?php echo "$AsteriskUserField";?>"></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUserFieldtype" value="1" <?php if((!isset($AsteriskUserFieldtype))||($AsteriskUserFieldtype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUserFieldtype" value="2" <?php if($AsteriskUserFieldtype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUserFieldtype" value="3" <?php if($AsteriskUserFieldtype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskUserFieldtype" value="4" <?php if($AsteriskUserFieldtype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CONTEXT</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr><td>&nbsp;&nbsp;<?php get_context($DBHandle, $AsteriskDstCtx);?></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDstCtxType" value="1" <?php if((!isset($AsteriskDstCtxType))||($AsteriskDstCtxType==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDstCtxType" value="2" <?php if($AsteriskDstCtxType==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDstCtxType" value="3" <?php if($AsteriskDstCtxType==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="AsteriskDstCtxType" value="4" <?php if($AsteriskDstCtxType==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>			
			<tr>
			<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;SERVER IP</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr><td>&nbsp;&nbsp;<?php get_servers($DBHandle, $NASIPAddress);?></td>				
				</tr></table></td>
			</tr>

			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;DURATION</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="duration1" size="4" value="<?php echo $duration1?>"></td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration1type" value="4" <?php if($duration1type==4){?>checked<?php }?>>&gt;</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration1type" value="5" <?php if($duration1type==5){?>checked<?php }?>>&gt; equal</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration1type" value="1" <?php if((!isset($duration1type))||($duration1type==1)){?>checked<?php }?>>Equal</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration1type" value="2" <?php if($duration1type==2){?>checked<?php }?>>&lt; equal</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration1type" value="3" <?php if($duration1type==3){?>checked<?php }?>>&lt;</td>	
				<td width="5%" class="bar-search" align="center" bgcolor="#cddeff"></td>
				
				<td>&nbsp;&nbsp;<INPUT TYPE="text" NAME="duration2" size="4" value="<?php echo $duration2?>"></td>			
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration2type" value="4" <?php if($duration2type==4){?>checked<?php }?>>&gt;</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration2type" value="5" <?php if($duration2type==5){?>checked<?php }?>>&gt; equal</td>								
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration2type" value="2" <?php if($duration2type==1){?>checked<?php }?>>&lt; equal</td>
				<td class="bar-search" align="center" bgcolor="#cddeff"><input type="radio" NAME="duration2type" value="3" <?php if($duration2type==3){?>checked<?php }?>>&lt;</td>	
				</tr></table>
				</td>
			</tr>	


			<tr>
        		<td class="bar-search" align="left" bgcolor="#000033"> </td>

				<td class="bar-search" align="center" bgcolor="#acbdee">
					<input type="image"  name="image16" align="top" border="0" src="../images/button-search.gif" />
					&nbsp;&nbsp;&nbsp;&nbsp;
					Result : Minutes<input type="radio" NAME="resulttype" value="min" <?php if((!isset($resulttype))||($resulttype=="min")){?>checked<?php }?>> - Seconds <input type="radio" NAME="resulttype" value="sec" <?php if($resulttype=="sec"){?>checked<?php }?>>
	  			</td>
    		</tr>
		</tbody></table>
	</FORM>
</center>


<br><br>

<!-- ** ** ** ** ** Part to display the CDR ** ** ** ** ** -->

			<center>Number of calls : <?php  if (is_array($list) && count($list)>0){ echo $nb_record; }else{echo "0";}?></center>
      <table width="<?php echo $FG_HTML_TABLE_WIDTH?>" border="0" align="center" cellpadding="0" cellspacing="0">
<TR bgcolor="#ffffff"> 
          <TD bgColor=#7f99cc height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px"> 
            <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
                <TR> 
                  <TD><SPAN style="COLOR: #ffffff; FONT-SIZE: 11px"><B><?php echo $FG_HTML_TABLE_TITLE?></B></SPAN></TD>
                  <TD align=right> <IMG alt="Back to Top" border=0 height=12 src="../images/btn_top_12x12.gif" width=12> 
                  </TD>
                </TR>
              </TBODY>
            </TABLE></TD>
        </TR>
        <TR> 
          <TD> <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
<TBODY>
                <TR bgColor=#F0F0F0> 
				  <TD width="<?php echo $FG_ACTION_SIZE_COLUMN?>" align=center class="tableBodyRight" style="PADDING-BOTTOM: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px; PADDING-TOP: 2px"></TD>					
				  
                  <?php 
				  	if (is_array($list) && count($list)>0){
					
				  	for($i=0;$i<$FG_NB_TABLE_COL;$i++){ 
					?>				
				  
					
                  <TD width="<?php echo $FG_TABLE_COL[$i][2]?>" align=middle class="tableBody" style="PADDING-BOTTOM: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px; PADDING-TOP: 2px"> 
                    <center><strong> 
                    <?php  if (strtoupper($FG_TABLE_COL[$i][4])=="SORT"){?>
                    <a href="<?php  echo $PHP_SELF."?s=1&t=$t&stitle=$stitle&atmenu=$atmenu&current_page=$current_page&order=".$FG_TABLE_COL[$i][1]."&sens="; if ($sens=="ASC"){echo"DESC";}else{echo"ASC";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskAsteriskClid";?>"> 
                    <span class="liens"><?php  } ?>
                    <?php echo $FG_TABLE_COL[$i][0]?> 
                    <?php if ($order==$FG_TABLE_COL[$i][1] && $sens=="ASC"){?>
                    &nbsp;<img src="../images/icon_up_12x12.GIF" width="12" height="12" border="0"> 
                    <?php }elseif ($order==$FG_TABLE_COL[$i][1] && $sens=="DESC"){?>
                    &nbsp;<img src="../images/icon_down_12x12.GIF" width="12" height="12" border="0"> 
                    <?php }?>
                    <?php  if (strtoupper($FG_TABLE_COL[$i][4])=="SORT"){?>
                    </span></a> 
                    <?php }?>
                    </strong></center></TD>
				   <?php } ?>		
				  	
                </TR>
                <TR> 
                  <TD bgColor=#e1e1e1 colSpan=<?php echo $FG_TOTAL_TABLE_COL?> height=1><IMG 
                              height=1 
                              src="../images/clear.gif" 
                              width=1></TD>
                </TR>
				<?php
				
				
				  
				  	 $ligne_number=0;					 
				  	 foreach ($list as $recordset){ 
						 $ligne_number++;
				?>
				
               		 <TR bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>"  onMouseOver="bgColor='#C4FFD7'" onMouseOut="bgColor='<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>'"> 
						<TD vAlign=top align="<?php echo $FG_TABLE_COL[$i][3]?>" class=tableBody><?php  echo $ligne_number+$current_page*$FG_LIMITE_DISPLAY.".&nbsp;"; ?></TD>
							 
				  		<?php for($i=0;$i<$FG_NB_TABLE_COL;$i++){ 
							if ($FG_TABLE_COL[$i][6]=="lie"){


									$instance_sub_table = new Table($FG_TABLE_COL[$i][7], $FG_TABLE_COL[$i][8]);
									$sub_clause = str_replace("%id", $recordset[$i], $FG_TABLE_COL[$i][9]);																																	
									$select_list = $instance_sub_table -> Get_list ($DBHandle, $sub_clause, null, null, null, null, null, null);
									
									
									$field_list_sun = split(',',$FG_TABLE_COL[$i][8]);
									$record_display = $FG_TABLE_COL[$i][10];
									
									for ($l=1;$l<=count($field_list_sun);$l++){										
										$record_display = str_replace("%$l", $select_list[0][$l-1], $record_display);	
									}
								
							}elseif ($FG_TABLE_COL[$i][6]=="list"){
									$select_list = $FG_TABLE_COL[$i][7];
									$record_display = $select_list[$recordset[$i]][0];
							
							}else{
									$record_display = $recordset[$i];
							}
							
							
							if ( is_numeric($FG_TABLE_COL[$i][5]) && (strlen($record_display) > $FG_TABLE_COL[$i][5])  ){
								$record_display = substr($record_display, 0, $FG_TABLE_COL[$i][5]-3)."";  
															
							}
							
							
				 		 ?>
                 		 <TD vAlign=top align="<?php echo $FG_TABLE_COL[$i][3]?>" class=tableBody><?php 
						 if (isset ($FG_TABLE_COL[$i][11]) && strlen($FG_TABLE_COL[$i][11])>1){
						 		call_user_func($FG_TABLE_COL[$i][11], $record_display);
						 }else{
						 		echo stripslashes($record_display);
						 }						 
						 ?></TD>
				 		 <?php  } ?>
                  
					</TR>
				<?php
					 }//foreach ($list as $recordset)
					 while ($ligne_number < $FG_LIMITE_DISPLAY){
					 	$ligne_number++;
				?>
					<TR bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$ligne_number%2]?>"> 
				  		<?php for($i=0;$i<$FG_NB_TABLE_COL;$i++){ 
				 		 ?>
                 		 <TD vAlign=top class=tableBody>&nbsp;</TD>
				 		 <?php  } ?>
                 		 <TD align="center" vAlign=top class=tableBodyRight>&nbsp;</TD>				
					</TR>
									
				<?php					 
					 } //END_WHILE
					 
				  }else{
				  		echo "No data found !!!";				  
				  }//end_if
				 ?>
                <TR> 
                  <TD class=tableDivider colSpan=<?php echo $FG_TOTAL_TABLE_COL?>><IMG height=1 
                              src="../images/clear.gif" 
                              width=1></TD>
                </TR>
                <TR> 
                  <TD class=tableDivider colSpan=<?php echo $FG_TOTAL_TABLE_COL?>><IMG height=1 
                              src="../images/clear.gif" 
                              width=1></TD>
                </TR>
              </TBODY>
            </TABLE></td>
        </tr>
        <TR bgcolor="#ffffff"> 
          <TD bgColor=#ADBEDE height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px"> 
			<TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
                <TR> 
                  <TD align="right"><SPAN style="COLOR: #ffffff; FONT-SIZE: 11px"><B> 
                    <?php if ($current_page>0){?>
                    <img src="../images/fleche-g.gif" width="5" height="10"> <a href="<?php echo $PHP_SELF?>?s=1&t=<?php echo $t?>&order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php  echo ($current_page-1)?><?php  if (!is_null($letter) && ($letter!="")){ echo "&letter=$letter";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskAsteriskClidtype=$AsteriskAsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx&duration1=$duration1&duration1type=$duration1type&duration2=$duration2&duration2type=$duration2type";?>"> 
                    Previous </a> - 
                    <?php }?>
                    <?php echo ($current_page+1);?> / <?php  echo $nb_record_max;?> 
                    <?php if ($current_page<$nb_record_max-1){?>
                    - <a href="<?php echo $PHP_SELF?>?s=1&t=<?php echo $t?>&order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php  echo ($current_page+1)?><?php  if (!is_null($letter) && ($letter!="")){ echo "&letter=$letter";} 
					echo "&posted=$posted&Period=$Period&frommonth=$frommonth&fromstatsmonth=$fromstatsmonth&tomonth=$tomonth&tostatsmonth=$tostatsmonth&fromday=$fromday&fromstatsday_sday=$fromstatsday_sday&fromstatsmonth_sday=$fromstatsmonth_sday&today=$today&tostatsday_sday=$tostatsday_sday&tostatsmonth_sday=$tostatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx&duration1=$duration1&duration1type=$duration1type&duration2=$duration2&duration2type=$duration2type";?>"> 
                    Next </a> <img src="../images/fleche-d.gif" width="5" height="10"> 
                    </B></SPAN> 
                    <?php }?>
                  </TD>
              </TBODY>
            </TABLE></TD>
        </TR>
      </table>

<!-- ** ** ** ** ** Part to display the GRAPHIC ** ** ** ** ** -->
<br><br>

<?php 

if (is_array($list_total_day) && count($list_total_day)>0){
$mmax=0;
$totalcall==0;
$totalminutes=0;
foreach ($list_total_day as $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[2];
	$totalminutes+=$data[1];
}

?>


<!-- TITLE GLOBAL -->
<center>
 <table border="0" cellspacing="0" cellpadding="0" width="80%"><tbody><tr><td align="left" height="30">
		<table cellspacing="0" cellpadding="1" bgcolor="#000000" width="50%"><tbody><tr><td>
			<table cellspacing="0" cellpadding="0" width="100%"><tbody>
				<tr><td bgcolor="#600101" align="left"><font face="verdana" size="1" color="white"><b>TOTAL</b></font></td></tr>
			</tbody></table>
		</td></tr></tbody></table>
 </td></tr></tbody></table>
		  
<!-- FIN TITLE GLOBAL MINUTES //-->
				
<table border="0" cellspacing="0" cellpadding="0" width="80%">
<tbody><tr><td bgcolor="#000000">			
	<table border="0" cellspacing="1" cellpadding="2" width="100%"><tbody>
	<tr>	
		<td align="center" bgcolor="#600101"></td>
    	<td bgcolor="#b72222" align="center" colspan="4"><font face="verdana" size="1" color="#ffffff"><b>ASTERISK MINUTES</b></font></td>
    </tr>
	<tr bgcolor="#600101">
		<td align="right" bgcolor="#b72222"><font face="verdana" size="1" color="#ffffff"><b>DATE</b></font></td>
        <td align="center"><font face="verdana" size="1" color="#ffffff"><b>DURATION</b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b>GRAPHIC</b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b>CALLS</b></font></td>
		<td align="center"><font face="verdana" size="1" color="#ffffff"><b> <acronym title="Average Connection Time">ACT</acronym> </b></font></td>
                			
		<!-- LOOP -->
	<?php  		
		$i=0;
		$data_order = array();
		foreach($list_total_day as $key => $day_data)
		{
			$data_order[$key]=$day_data[0];	
		}
		array_multisort($data_order,SORT_ASC,$list_total_day);
		foreach ($list_total_day as $data){	
		$i=($i+1)%2;		
		$tmc = $data[1]/$data[2];
		
		if ((!isset($resulttype)) || ($resulttype=="min")){  
			$tmc = sprintf("%02d",intval($tmc/60)).":".sprintf("%02d",intval($tmc%60));		
		}else{
		
			$tmc =intval($tmc);
		}
		
		if ((!isset($resulttype)) || ($resulttype=="min")){  
				$minutes = sprintf("%02d",intval($data[1]/60)).":".sprintf("%02d",intval($data[1]%60));
		}else{
				$minutes = $data[1];
		}
		$widthbar= intval(($data[1]/$mmax)*200); 
		
	?>
		</tr><tr>
		<td align="right" class="sidenav" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><?php echo $data[0]?></font></td>
		<td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $minutes?> </font></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="left" nowrap="nowrap" width="<?php echo $widthbar+60?>">
        <table cellspacing="0" cellpadding="0"><tbody><tr>
        <td bgcolor="#e22424"><img src="../images/spacer.gif" width="<?php echo $widthbar?>" height="6"></td>
        </tr></tbody></table></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $data[2]?></font></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $tmc?> </font></td>
     <?php 	 }	 	 	
	 	
		if ((!isset($resulttype)) || ($resulttype=="min")){  
			$total_tmc = sprintf("%02d",intval(($totalminutes/$totalcall)/60)).":".sprintf("%02d",intval(($totalminutes/$totalcall)%60));				
			$totalminutes = sprintf("%02d",intval($totalminutes/60)).":".sprintf("%02d",intval($totalminutes%60));
		}else{
			$total_tmc = intval($totalminutes/$totalcall);			
		}
	 
	 ?>                   	
	</tr>
	<!-- FIN DETAIL -->		
	
				
				<!-- FIN BOUCLE -->

	<!-- TOTAL -->
	<tr bgcolor="#600101">
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>TOTAL</b></font></td>
		<td align="center" nowrap="nowrap" colspan="2"><font face="verdana" size="1" color="#ffffff"><b><?php echo $totalminutes?> </b></font></td>
		<td align="center" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php echo $totalcall?></b></font></td>
		<td align="center" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php echo $total_tmc?></b></font></td>                        
	</tr>
	<!-- FIN TOTAL -->

	  </tbody></table>
	  <!-- Fin Tableau Global //-->

</td></tr></tbody></table>

<br/>
<table width="60%"><tr><td>
<a href="export_pdf.php" target="_blank"><img src="../images/pdf.gif" height="48" border="0"/></a> <a href="export_pdf.php" target="_blank">Export PDF file</a>
</td>
<td>
<a href="export_csv.php" target="_blank" ><img src="../images/excel.gif" height="48" border="0"/></a> <a href="export_csv.php" target="_blank">Export CSV file</a>
</td>
<td><a href="#" onclick="javascript:window.open('add_scheduler.php','scheduler','menubar=no,toolbar=no,width=650,height=300')"><img src="../images/config-date.png" border="0"/> Scheduler</a></td>
</tr>
</table>


<?php  }else{ ?>
	<center><h3>No calls in your selection.</h3></center>
<?php  } ?>
</center>


<?php
// #### FOOTER SECTION
$smarty->display('footer.tpl');

?>
