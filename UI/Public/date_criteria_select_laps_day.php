
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
					</td><td>&nbsp;&nbsp;
					<b>Laps of days to compare :</b> 
				 	<select name="days_compare">
					<option value="4" <?php if ($days_compare=="4"){ echo "selected";}?>>- 4 days</option>
					<option value="3" <?php if ($days_compare=="3"){ echo "selected";}?>>- 3 days</option>
					<option value="2" <?php if (($days_compare=="2")|| !isset($days_compare)){ echo "selected";}?>>- 2 days</option>
					<option value="1" <?php if ($days_compare=="1"){ echo "selected";}?>>- 1 day</option>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
