<?php 
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");

session_start();

getpost_ifset(array('posted', 'Period', 'frommonth', 'fromstatsmonth', 'tomonth', 'tostatsmonth', 'fromday', 'fromstatsday_sday', 'fromstatsmonth_sday', 'today', 'tostatsday_sday', 'tostatsmonth_sday', 'AsteriskDsttype', 'AsteriskSrctype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx', 'duration1', 'duration1type', 'duration2', 'duration2type'));


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

$FG_TABLE_ALTERNATE_ROW_COLOR_DARKER[] = "#EEEEEE";
$FG_TABLE_ALTERNATE_ROW_COLOR_DARKER[] = "#E2E8EE";


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
$FG_COL_QUERY=' AsteriskStartTime, NASIPAddress, AsteriskSrc, AsteriskClid, AsteriskLastApp, AsteriskLastData, AsteriskDst, AsteriskDisposition, AsteriskDuration, AsteriskDstCtx, AsteriskUserField';
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
	$to_hour_min = "$tostatshour:$tostatshour:59";

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
	$QUERY = "SELECT NASIPAddress, sum(AsteriskDuration) AS calltime, count(*) as nbcall FROM ".$FG_TABLE_NAME." WHERE ".$FG_TABLE_CLAUSE." GROUP BY NASIPAddress";
	
	if ($FG_DEBUG == 3) echo "<br>QUERY :  $QUERY";
	
	$res = $DBHandle -> Execute($QUERY);
	$num = $res->RecordCount();
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

?>

<?php
	include("../asterisk-fak/PP_header.php");
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
				<td class="bar-search" align="left" bgcolor="#000033">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CONTEXT</b></font>
				</td>				
				<td class="bar-search" align="left" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr><td>&nbsp;&nbsp;<?php get_context($DBHandle, $AsteriskDstCtx);?></td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskDstCtxType" value="1" <?php if((!isset($AsteriskDstCtxType))||($AsteriskDstCtxType==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskDstCtxType" value="2" <?php if($AsteriskDstCtxType==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskDstCtxType" value="3" <?php if($AsteriskDstCtxType==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="AsteriskDstCtxType" value="4" <?php if($AsteriskDstCtxType==4){?>checked<?php }?>>Ends with</td>
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

<!-- FIN TITLE GLOBAL MINUTES //-->

<table border="0" cellspacing="0" cellpadding="0" width="80%">
<tbody><tr><td>			
	<table border="0" cellspacing="1" cellpadding="2" width="100%"><tbody>
	<tr>	
		<td align="center" bgcolor="#600101"></td>
    	<td bgcolor="#b72222" align="center" colspan="4"><font face="verdana" size="1" color="#ffffff"><b>SERVER USAGE</b></font></td>
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
		$widthbar= intval(($data[1]/$mmax)*400); 
		
	?>
		</tr><tr>
		<td bgcolor="<?php echo $FG_TABLE_ALTERNATE_ROW_COLOR_DARKER[$i]?>" align="center" class="sidenav" nowrap="nowrap"> <img src="../images/server_icon.gif">
			<font face="verdana" size="3" color="#000000"><?php echo $data[0]?></font></td>
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



<?php  }else{ ?>
	<center><h3>No calls in your selection.</h3></center>
<?php  } ?>
</center>


<?php
	include("../asterisk-fak/PP_footer.php");
?>
