<?php
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");



getpost_ifset(array('months_compare', 'current_page', 'fromstatsday_sday', 'fromstatsmonth_sday', 'days_compare', 'min_call', 'posted',  'AsteriskDsttype', 'sourcetype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx'));


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




// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#FFFFFF";
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#F2F8FF";



//$link = DbConnect();
$DBHandle  = DbConnect();

// The variable Var_col would define the col that we want show in your table
// First Name of the column in the html page, second name of the field
$FG_TABLE_COL = array();


/*******
Calldate Clid Src Dst Dcontext Channel DstNASIPAddress Lastapp Lastdata Duration Billsec Disposition Amaflags Accountcode Uniqueid Serverid
*******/

$FG_TABLE_COL[]=array ("Calldate", "AsteriskStartTime", "18%", "center", "SORT", "19");
$FG_TABLE_COL[]=array ("Server", "NASIPAddress", "13%", "center", "", "30");
$FG_TABLE_COL[]=array ("Source", "AsteriskSrc", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("Clid", "AsteriskClid", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Lastapp", "AsteriskLastApp", "8%", "center", "", "30");

$FG_TABLE_COL[]=array ("Lastdata", "AsteriskLastData", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Dst", "AsteriskDst", "9%", "center", "SORT", "30");
//$FG_TABLE_COL[]=array ("Serverid", "serverid", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("Disposition", "AsteriskDisposition", "9%", "center", "", "30");
$FG_TABLE_COL[]=array ("Duration", "AsteriskDuration", "6%", "center", "SORT", "30");


$FG_TABLE_DEFAULT_ORDER = "AsteriskStartTime";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
$FG_COL_QUERY='AsteriskStartTime, NASIPAddress, AsteriskSrc, AsteriskClid, AsteriskLastApp, AsteriskLastData, AsteriskDst, AsteriskDisposition, AsteriskDuration';
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
$FG_HTML_TABLE_WIDTH="90%";




if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}


if ($_POST['posted']==1){
	
	$SQLcmd = '';
	
	if ($_POST['before']) {
	if (strpos($SQLcmd, 'WHERE') > 0) { 	$SQLcmd = "$SQLcmd AND ";
	}else{     								$SQLcmd = "$SQLcmd WHERE "; }
	$SQLcmd = "$SQLcmd AsteriskStartTime<'".$_POST['before']."'";
	}
	if ($_POST['after']) {    if (strpos($SQLcmd, 'WHERE') > 0) {      $SQLcmd = "$SQLcmd AND ";
	} else {      $SQLcmd = "$SQLcmd WHERE ";    }
	$SQLcmd = "$SQLcmd AsteriskStartTime>'".$_POST['after']."'";
	}
	$SQLcmd = do_field($SQLcmd, 'AsteriskClid');
	$SQLcmd = do_field($SQLcmd, 'AsteriskSrc');
	$SQLcmd = do_field($SQLcmd, 'AsteriskDst');
	$SQLcmd = do_field($SQLcmd, 'NASIPAddress');  
	
	$SQLcmd = do_field($SQLcmd, 'AsteriskUserField');
	$SQLcmd = do_field($SQLcmd, 'AsteriskAccCode');
  
  
}


$date_clause='';
// Period (Month-Day)

if (!isset($months_compare)) $months_compare=2;

if (DB_TYPE == "postgres"){	
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND AsteriskStartTime < date'$fromstatsmonth_sday-$fromstatsday_sday'+ INTERVAL '1 DAY' AND AsteriskStartTime >= date'$fromstatsmonth_sday-$fromstatsday_sday' - INTERVAL '$days_compare DAY'";
}else{
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND AsteriskStartTime < ADDDATE('$fromstatsmonth_sday-$fromstatsday_sday',INTERVAL 1 DAY) AND AsteriskStartTime >= SUBDATE('$fromstatsmonth_sday-$fromstatsday_sday',INTERVAL $days_compare DAY)";  
}

if ($FG_DEBUG == 3) echo "<br>$date_clause<br>";
/*
Month
fromday today
frommonth tomonth (true)
fromstatsmonth tostatsmonth

fromstatsday_sday
fromstatsmonth_sday
tostatsday_sday
tostatsmonth_sday
*/
  
if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}



if ($FG_DEBUG == 3) echo "<br>Clause : $FG_TABLE_CLAUSE";
//$nb_record = $instance_table -> Table_count ($FG_TABLE_CLAUSE);
$nb_record = count($list_total);

if ($FG_DEBUG >= 1) var_dump ($list);

if ($nb_record<=$FG_LIMITE_DISPLAY){ 
	$nb_record_max=1;
}else{ 
	$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY)+1);
}

if ($FG_DEBUG == 3) echo "<br>Nb_record : $nb_record";
if ($FG_DEBUG == 3) echo "<br>Nb_record_max : $nb_record_max";

?>
<?php
	include("../asterisk-fak/PP_header.php");
?>
<br><br><br>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>


<!-- ** ** ** ** ** Part for the research ** ** ** ** ** -->
	<center>
	<FORM METHOD=POST ACTION="<?php echo $PHP_SELF?>?s=<?php echo $s?>&t=<?php echo $t?>&order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php echo $current_page?>">
	<INPUT TYPE="hidden" NAME="posted" value=1>
		<table class="bar-status" width="95%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody>
			
			<?php
				include("date_criteria_select_last_month.php");
			?>
			
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
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="1" <?php if((!isset($sourcetype))||($sourcetype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="2" <?php if($sourcetype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="3" <?php if($sourcetype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="4" <?php if($sourcetype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CONTEXT</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td>&nbsp;&nbsp;<?php get_context($DBHandle, $AsteriskDstCtx);?></td>
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
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td>&nbsp;&nbsp;<?php get_servers($DBHandle, $NASIPAddress);?></td>				
				</tr></table></td>
			</tr>

			<tr>
        		<td class="bar-search" align="left" bgcolor="#555577"> </td>

				<td class="bar-search" align="center" bgcolor="#cddeff">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#cddeff">
						<tr>
						<td align="right">							
							<input type="image"  name="image16" align="top" border="0" src="../images/button-search.gif" />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td></tr></table>
	  			</td>
    		</tr>
		</tbody></table>
	</FORM>
</center>


<!-- ** ** ** ** ** Part to display the GRAPHIC ** ** ** ** ** -->


<?php 
if (is_array($list) && count($list)>0){

$table_graph=array();
$table_graph_hours=array();
$numm=0;
foreach ($list_total as $recordset){
		$numm++;
		$mydate= substr($recordset[0],0,10);
		$mydate_hours= substr($recordset[0],0,13);
		//echo "$mydate<br>";
		if (is_array($table_graph_hours[$mydate_hours])){
			$table_graph_hours[$mydate_hours][0]++;
			$table_graph_hours[$mydate_hours][1]=$table_graph_hours[$mydate_hours][1]+$recordset[1];
		}else{
			$table_graph_hours[$mydate_hours][0]=1;
			$table_graph_hours[$mydate_hours][1]=$recordset[1];
		}
		
		
		if (is_array($table_graph[$mydate])){
			$table_graph[$mydate][0]++;
			$table_graph[$mydate][1]=$table_graph[$mydate][1]+$recordset[1];
		}else{
			$table_graph[$mydate][0]=1;
			$table_graph[$mydate][1]=$recordset[1];
		}
}

$mmax=0;
$totalcall==0;
$totalminutes=0;
foreach ($table_graph as $tkey => $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[0];
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
		// #ffffff #cccccc
		foreach ($table_graph as $tkey => $data){	
		$i=($i+1)%2;		
		$tmc = $data[1]/$data[0];
		
		$tmc_60 = sprintf("%02d",intval($tmc/60)).":".sprintf("%02d",intval($tmc%60));		
		
		$minutes_60 = sprintf("%02d",intval($data[1]/60)).":".sprintf("%02d",intval($data[1]%60));
		$widthbar= intval(($data[1]/$mmax)*200);
		
	?>
		</tr><tr>
		<td align="right" class="sidenav" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><?php echo $tkey?></font></td>
		<td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $minutes_60?> </font></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="left" nowrap="nowrap" width="<?php echo $widthbar+60?>">
        <table cellspacing="0" cellpadding="0"><tbody><tr>
        <td bgcolor="#e22424"><img src="../images/spacer.gif" width="<?php echo $widthbar?>" height="6"></td>
        </tr></tbody></table></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $data[0]?></font></td>
        <td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR[$i]?>" align="right" nowrap="nowrap"><font face="verdana" color="#000000" size="1"><?php echo $tmc_60?> </font></td>
     <?php 	 }	 
	 	$total_tmc_60 = sprintf("%02d",intval(($totalminutes/$totalcall)/60)).":".sprintf("%02d",intval(($totalminutes/$totalcall)%60));				
		$total_minutes_60 = sprintf("%02d",intval($totalminutes/60)).":".sprintf("%02d",intval($totalminutes%60));
	 
	 ?>                   	
	</tr>
	<!-- FIN DETAIL -->		

	<!-- FIN BOUCLE -->

	<!-- TOTAL -->
	<tr bgcolor="#600101">
		<td align="right" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b>TOTAL</b></font></td>
		<td align="center" nowrap="nowrap" colspan="2"><font face="verdana" size="1" color="#ffffff"><b><?php echo $total_minutes_60?> </b></font></td>
		<td align="center" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php echo $totalcall?></b></font></td>
		<td align="center" nowrap="nowrap"><font face="verdana" size="1" color="#ffffff"><b><?php echo $total_tmc_60?></b></font></td>                        
	</tr>
	<!-- FIN TOTAL -->

	  </tbody></table>
	  <!-- Fin Tableau Global //-->

</td></tr></tbody></table>
	<br>

<?php  }else{
	
  } 
  if ($posted==1){ ?>
	<center>
	
	<?php if (FLASHCHART) { 
		
		include_once(dirname(__FILE__) . "/../lib/charts/charts.php");
		
		echo InsertChart ( "../lib/charts/charts.swf", "../lib/charts/charts_library", "graph_pie_userfield.php?min_call=$min_call&fromstatsday_sday=$fromstatsday_sday&months_compare=$months_compare&fromstatsmonth_sday=$fromstatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx", 750, 600 );
		
	} else { ?>
		
		<IMG SRC="graph_pie_userfield.php?min_call=<?php echo $min_call?>&fromstatsday_sday=<?php echo $fromstatsday_sday?>&months_compare=<?php echo $months_compare?>&fromstatsmonth_sday=<?php echo $fromstatsmonth_sday?>&AsteriskDsttype=<?php echo $AsteriskDsttype?>&sourcetype=<?php echo $sourcetype?>&AsteriskClidtype=<?php echo $AsteriskClidtype?>&NASIPAddress=<?php echo $NASIPAddress?>&resulttype=<?php echo $resulttype?>&AsteriskDst=<?php echo $AsteriskDst?>&AsteriskSrc=<?php echo $AsteriskSrc?>&AsteriskClid=<?php echo $AsteriskClid?>&AsteriskUserFieldtype=<?php echo $AsteriskUserFieldtype?>&AsteriskUserField=<?php echo $AsteriskUserField?>&AsteriskDstCtxType=<?php echo $AsteriskDstCtxType?>&AsteriskDstCtx=<?php echo $AsteriskDstCtx?>" ALT="Stat Graph">
	<?php } ?>
	
	
	</center>
<?php  } ?>
</center>


<?php
	include("../asterisk-fak/PP_footer.php");
?>
