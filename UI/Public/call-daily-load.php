<?php
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");



getpost_ifset(array('current_page', 'fromstatsday_sday', 'fromstatsmonth_sday', 'days_compare', 'min_call', 'posted',  'AsteriskDsttype', 'sourcetype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx'));


if (!isset ($current_page) || ($current_page == "")){	
	$current_page=0; 
}

// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;


// THIS VARIABLE DEFINE THE COLOR OF THE HEAD TABLE
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#FFFFFF";
$FG_TABLE_ALTERNATE_ROW_COLOR[] = "#F2F8FF";



//$link = DbConnect();
$DBHandle  = DbConnect();

// The variable Var_col would define the col that we want show in your table
// First Name of the column in the html page, second name of the field
$FG_TABLE_COL = array();


$FG_TABLE_DEFAULT_ORDER = "AsteriskStartTime";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
$FG_COL_QUERY='AsteriskStartTime, NASIPAddress, AsteriskSrc, AsteriskClid, AsteriskLastApp, AsteriskLastData, AsteriskDst, AsteriskDisposition, AsteriskDuration';
//$FG_COL_QUERY='calldate, NASIPAddress, AsteriskSrc, AsteriskClid, lastapp, lastdata, AsteriskDst, serverid, disposition, duration';
$FG_COL_QUERY_GRAPH='AsteriskStartTime, AsteriskDuration';


// Number of column in the html table
$FG_NB_TABLE_COL=count($FG_TABLE_COL);



if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}


if ($posted==1){
	
	$SQLcmd = '';
	
	if ($_POST['before']) {
		
		if (strpos($SQLcmd, 'WHERE') > 0) { 	$SQLcmd = "$SQLcmd AND ";
		}else{     								$SQLcmd = "$SQLcmd WHERE "; 
		}
		$SQLcmd = "$SQLcmd AsteriskStartTime<'".$_POST['before']."'";
	}
		
	if ($_POST['after']) {    
		if (strpos($SQLcmd, 'WHERE') > 0) {      
			$SQLcmd = "$SQLcmd AND ";
		} else {      
			$SQLcmd = "$SQLcmd WHERE ";
		}
		$SQLcmd = "$SQLcmd AsteriskStartTime>'".$_POST['after']."'";
	}
	
	$SQLcmd = do_field($SQLcmd, 'AsteriskClid');
	$SQLcmd = do_field($SQLcmd, 'AsteriskSrc');
	$SQLcmd = do_field($SQLcmd, 'AsteriskDst');
	$SQLcmd = do_field($SQLcmd, 'NASIPAddress');  
	
	$SQLcmd = do_field($SQLcmd, 'AsteriskUserField');
	$SQLcmd = do_field($SQLcmd, 'AsteriskDstCtx');
	
	
}


$date_clause='';
// Period (Month-Day)


if (!isset($fromstatsday_sday)){	
	$fromstatsday_sday = date("d");
	$fromstatsmonth_sday = date("Y-m");	
}

if (DB_TYPE == "postgres"){	
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND AsteriskStartTime < date'$fromstatsmonth_sday-$fromstatsday_sday'+ INTERVAL '1 DAY' AND AsteriskStartTime >= date'$fromstatsmonth_sday-$fromstatsday_sday'";
}else{
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND AsteriskStartTime < ADDDATE('$fromstatsmonth_sday-$fromstatsday_sday',INTERVAL 1 DAY) AND AsteriskStartTime >= '$fromstatsmonth_sday-$fromstatsday_sday'";  
}

if ($FG_DEBUG == 3) echo "<br>$date_clause<br>";


  
if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}

if ($_POST['posted']==1){
	$list_total = $instance_table_graph -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, null, null, null, null, null, null);
}

$nb_record = count($list_total);


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
			
			<tr>
        		<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>Select the day</b></font>
				</td>
      			<td align="left" bgcolor="#acbdee">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td>
	  				<b>From : </b><select name="fromstatsday_sday">
					<?php  
						for ($i=1;$i<=31;$i++){
							if ($fromstatsday_sday==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
							echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
						}
					?>					
					</select>
				 	<select name="fromstatsmonth_sday">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							   $monthname = array( "January", "February","March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
							   if ($year_actual==$i){
									$monthnumber = date("n")-1; // Month number without lead 0.
							   }else{
									$monthnumber=11;
							   }		   
							   for ($j=$monthnumber;$j>=0;$j--){	
										$month_formated = sprintf("%02d",$j+1);
							   			if ($fromstatsmonth_sday=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}								
					?>										
					</select>
					</td></tr></table>
	  			</td>
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
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="1" <?php if((!isset($sourcetype))||($sourcetype==1)){?>checked<?php }?>>Exact</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="2" <?php if($sourcetype==2){?>checked<?php }?>>Begins with</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="3" <?php if($sourcetype==3){?>checked<?php }?>>Contains</td>
				<td class="bar-search" align="center" bgcolor="#acbdee"><input type="radio" NAME="sourcetype" value="4" <?php if($sourcetype==4){?>checked<?php }?>>Ends with</td>
				</tr></table></td>
			</tr>
			<tr>
				<td class="bar-search" align="left" bgcolor="#555577">				
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;CLI</b></font>
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
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;USERFIELD</b></font>
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
				<table width="100%" border="0" cellspacing="0" cellpadding="0"></td>
				<tr><td>&nbsp;&nbsp;<?php get_context($DBHandle, $AsteriskDstCtx);?>
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
<br><br>

<?php 
if ($nb_record > 0 && is_array($list_total)){

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
		
		//bgcolor="#336699" 
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
	
	
	<?php if (FLASHCHART) { ?>
		
		<?php
		include_once(dirname(__FILE__) . "/../lib/charts/charts.php");
		// InsertChart( $flash_file, $library_path, $php_source, $width=400, $height=250, $bg_color="666666", $transparent=false, $license=null )
		// echo InsertChart ( "../lib/charts/charts.swf", "../lib/charts/charts_library", "../lib/charts/test_chart_xml.php?chart_type=bar&user_id=658", 600, 400 );
		
		echo InsertChart ( "../lib/charts/charts.swf", "../lib/charts/charts_library", "graph_daily-load.php?days_compare=$days_compare&min_call=$min_call&fromstatsday_sday=$fromstatsday_sday&months_compare=$months_compare&fromstatsmonth_sday=$fromstatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx", 700, 400 );
		
		// echo "graph_pie.php?min_call=$min_call&fromstatsday_sday=$fromstatsday_sday&months_compare=$months_compare&fromstatsmonth_sday=$fromstatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx";
		?>
		
	<?php } else { ?>
		
		<IMG SRC="graph_daily-load.php?days_compare=<?php echo $days_compare?>&min_call=<?php echo $min_call?>&fromstatsday_sday=<?php echo $fromstatsday_sday?>&months_compare=<?php echo $months_compare?>&fromstatsmonth_sday=<?php echo $fromstatsmonth_sday?>&AsteriskDsttype=<?php echo $AsteriskDsttype?>&sourcetype=<?php echo $sourcetype?>&AsteriskClidtype=<?php echo $AsteriskClidtype?>&NASIPAddress=<?php echo $NASIPAddress?>&resulttype=<?php echo $resulttype?>&AsteriskDst=<?php echo $AsteriskDst?>&AsteriskSrc=<?php echo $AsteriskSrc?>&AsteriskClid=<?php echo $AsteriskClid?>&AsteriskUserFieldtype=<?php echo $AsteriskUserFieldtype?>&AsteriskUserField=<?php echo $AsteriskUserField?>&AsteriskDstCtxType=<?php echo $AsteriskDstCtxType?>&AsteriskDstCtx=<?php echo $AsteriskDstCtx?>" ALT="Stat Graph">
		
	<?php } ?>
	

<!-- ** ** ** ** ** HOURLY LOAD ** ** ** ** ** -->
&nbsp;
<br/>
	<center>Select the hour interval to see the details
	<FORM METHOD=POST ACTION="graph_hourdetail.php?posted=<?php echo $posted?>&min_call=<?php echo $min_call?>&fromstatsday_sday=<?php echo $fromstatsday_sday?>&days_compare=<?php echo $days_compare?>&fromstatsmonth_sday=<?php echo $fromstatsmonth_sday?>&AsteriskDsttype=<?php echo $AsteriskDsttype?>&sourcetype=<?php echo $sourcetype?>&AsteriskClidtype=<?php echo $AsteriskClidtype?>&NASIPAddress=<?php echo $NASIPAddress?>&resulttype=<?php echo $resulttype?>&AsteriskDst=<?php echo $AsteriskDst?>&AsteriskSrc=<?php echo $AsteriskSrc?>&AsteriskClid=<?php echo $AsteriskClid?>&AsteriskUserFieldtype=<?php echo $AsteriskUserFieldtype?>&AsteriskUserField=<?php echo $AsteriskUserField?>&AsteriskDstCtxType=<?php echo $AsteriskDstCtxType?>&AsteriskDstCtx=<?php echo $AsteriskDstCtx?>" target="superframe">		
	<!-- ** ** ** ** ** HOURLY LOAD ** ** ** ** ** -->
		<table class="bar-status" width="60%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody>		
			<tr>
			<td align="left" bgcolor="#000033">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;HOUR INTERVAL :</b></font>
				</td>				
				<td class="bar-search" align="center" bgcolor="#acbdee">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;
				<select name="hourinterval">
					<?php  
						for ($i=0;$i<=23;$i++){							
							echo '<option value="'.sprintf("%02d",$i)."\"> Interval [".sprintf("%02d",$i).'h to '.sprintf("%02d",$i+1).'h] </option>';
						}
					?>					
					</select>
				
				</td>				
				</tr></table></td>
			</tr>

			<tr>
				<td align="left" bgcolor="#555577">					
					<font face="verdana" size="1" color="#ffffff"><b>&nbsp;&nbsp;TYPE GRAPH :</b></font>
				</td>				
				<td class="bar-search" align="center" bgcolor="#cddeff">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>&nbsp;&nbsp;
				<select name="typegraph">
					<option value="fluctuation"> FLUCTUATION GRAPH</option> <option value="watch-call" selected> WATCH CALLS GRAPH</option>
				</select>
				
				</td>				
				</tr></table></td>
			</tr>

			<tr>
        		<td class="bar-search" align="left" bgcolor="#000033"> </td>

				<td class="bar-search" align="center" bgcolor="#acbdee">
					<input type="image"  name="image16" align="top" border="0" src="../images/button-search.gif" />				

	  			</td>
    		</tr>
		</tbody></table>
	</FORM>
</center>
<br>
<center>
    <iframe name="superframe" src="graph_hourdetail.php?posted=<?php echo $posted?>&min_call=<?php echo $min_call?>&fromstatsday_sday=<?php echo $fromstatsday_sday?>&days_compare=<?php echo $days_compare?>&fromstatsmonth_sday=<?php echo $fromstatsmonth_sday?>&AsteriskDsttype=<?php echo $AsteriskDsttype?>&sourcetype=<?php echo $sourcetype?>&AsteriskClidtype=<?php echo $AsteriskClidtype?>&NASIPAddress=<?php echo $NASIPAddress?>&resulttype=<?php echo $resulttype?>&AsteriskDst=<?php echo $AsteriskDst?>&AsteriskSrc=<?php echo $AsteriskSrc?>&AsteriskClid=<?php echo $AsteriskClid?>&AsteriskUserFieldtype=<?php echo $AsteriskUserFieldtype?>&AsteriskUserField=<?php echo $AsteriskUserField?>&AsteriskDstCtxType=<?php echo $AsteriskDstCtxType?>&AsteriskDstCtx=<?php echo $AsteriskDstCtx?>" BGCOLOR=white	width=770 height=800 marginWidth=0 marginHeight=0  frameBorder=0  scrolling=yes>

    </iframe>
</center>


</center>

<?php  }else{ ?>
	<center><h3>No calls in your selection.</h3></center>
<?php  } ?>

<?php
	include("../asterisk-fak/PP_footer.php");
?>
