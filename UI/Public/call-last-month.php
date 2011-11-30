<?php
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");



getpost_ifset(array('months_compare', 'current_page', 'fromstatsday_sday', 'fromstatsmonth_sday', 'days_compare', 'min_call', 'posted',  'AsteriskDsttype', 'sourcetype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'stitle', 'atmenu', 'current_page', 'order', 'sens', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx'));


if (!isset($months_compare)){		
	$months_compare=2;
}


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
<br><br>

<?php  if ($posted==1){ ?>
	<center>
	
	<?php if (FLASHCHART) { ?>
		
		<?php
		include_once(dirname(__FILE__) . "/../lib/charts/charts.php");
		// InsertChart( $flash_file, $library_path, $php_source, $width=400, $height=250, $bg_color="666666", $transparent=false, $license=null )
		// echo InsertChart ( "../lib/charts/charts.swf", "../lib/charts/charts_library", "../lib/charts/test_chart_xml.php?chart_type=bar&user_id=658", 600, 400 );
		
		echo InsertChart ( "../lib/charts/charts.swf", "../lib/charts/charts_library", "graph_pie.php?min_call=$min_call&fromstatsday_sday=$fromstatsday_sday&months_compare=$months_compare&fromstatsmonth_sday=$fromstatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx", 750, 550 );
		
		// echo "graph_pie.php?min_call=$min_call&fromstatsday_sday=$fromstatsday_sday&months_compare=$months_compare&fromstatsmonth_sday=$fromstatsmonth_sday&AsteriskDsttype=$AsteriskDsttype&sourcetype=$sourcetype&AsteriskClidtype=$AsteriskClidtype&NASIPAddress=$NASIPAddress&resulttype=$resulttype&AsteriskDst=$AsteriskDst&AsteriskSrc=$AsteriskSrc&AsteriskClid=$AsteriskClid&AsteriskUserFieldtype=$AsteriskUserFieldtype&AsteriskUserField=$AsteriskUserField&AsteriskDstCtxType=$AsteriskDstCtxType&AsteriskDstCtx=$AsteriskDstCtx";
		?>
		
	<?php } else { ?>
		
		<IMG SRC="graph_pie.php?min_call=<?php echo $min_call?>&fromstatsday_sday=<?php echo $fromstatsday_sday?>&months_compare=<?php echo $months_compare?>&fromstatsmonth_sday=<?php echo $fromstatsmonth_sday?>&AsteriskDsttype=<?php echo $AsteriskDsttype?>&sourcetype=<?php echo $sourcetype?>&AsteriskClidtype=<?php echo $AsteriskClidtype?>&NASIPAddress=<?php echo $NASIPAddress?>&resulttype=<?php echo $resulttype?>&AsteriskDst=<?php echo $AsteriskDst?>&AsteriskSrc=<?php echo $AsteriskSrc?>&AsteriskClid=<?php echo $AsteriskClid?>&AsteriskUserFieldtype=<?php echo $AsteriskUserFieldtype?>&AsteriskUserField=<?php echo $AsteriskUserField?>&AsteriskDstCtxType=<?php echo $AsteriskDstCtxType?>&AsteriskDstCtx=<?php echo $AsteriskDstCtx?>" ALT="Stat Graph">
	<?php } ?>
	
	</center>
<?php  } ?>
</center>


<?php
	include("../asterisk-fak/PP_footer.php");
?>
