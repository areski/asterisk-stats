
			<tr>
        		<td class="bar-search" align="left" bgcolor="#555577">
					<input type="radio" name="Period" value="Month" <?php  if (($Period=="Month") || !isset($Period)){ ?>checked="checked" <?php  } ?>> 
					<font face="verdana" size="1" color="#ffffff"><b>Selection of the month</b></font>
				</td>
      			<td class="bar-search" align="left" bgcolor="#cddeff">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#cddeff"><tr><td  class="bar-search">
	  				<input type="checkbox" name="frommonth" value="true" <?php  if ($frommonth){ ?>checked<?php }?>> 
					From : <select name="fromstatsmonth">
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
							   			if ($fromstatsmonth=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>		
					</select>
					</td><td  class="bar-search">&nbsp;&nbsp;
					<input type="checkbox" name="tomonth" value="true" <?php  if ($tomonth){ ?>checked<?php }?>> 
					To : <select name="tostatsmonth">
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
							   			if ($tostatsmonth=="$i-$month_formated"){$selected="selected";}else{$selected="";}
										echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							   }
						}
					?>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
			
			<tr>
        		<td align="left" bgcolor="#000033">
					<input type="radio" name="Period" value="Day" <?php  if ($Period=="Day"){ ?>checked="checked" <?php  } ?>> 
					<font face="verdana" size="1" color="#ffffff"><b>Selection of the day</b></font>
				</td>
				
      			<td align="left" bgcolor="#acbdee">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#acbdee"><tr><td  class="bar-search">
	  				<input type="checkbox" name="fromday" value="true" <?php  if ($fromday){ ?>checked<?php }?>> From : 
					<select name="fromstatsday_sday">
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
					
					<select name="fromstatshour">
						<?php  
							for ($i=0;$i<=23;$i++){
								if ($fromstatshour==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
								echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
							}
						?>
					</select> : 
					<select name="fromstatsminute">
						<?php  
							for ($i=0;$i<=59;$i++){
								if ($fromstatsminute==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
								echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
							}
						?>
					</select>
					
					
					</td><td  class="bar-search">&nbsp;&nbsp;
					<input type="checkbox" name="today" value="true" <?php  if ($today){ ?>checked<?php }?>> To : 
					<select name="tostatsday_sday">
					<?php  
						for ($i=1;$i<=31;$i++){
							if ($tostatsday_sday==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
							echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
						}
					?>						
					</select>
				 	<select name="tostatsmonth_sday">
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
								if ($tostatsmonth_sday=="$i-$month_formated"){$selected="selected";}else{$selected="";}
								echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							}
						}
					?>
					</select>
					
					<select name="tostatshour">
						<?php  
							for ($i=0;$i<=23;$i++){
								if (!isset($tostatshour))	$tostatshour='23';
								if ($tostatshour==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
								echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
							}
						?>
					</select> :
					<select name="tostatsminute">
						<?php  
							for ($i=0;$i<=59;$i++){
								if (!isset($tostatsminute))	$tostatsminute='59';
								if ($tostatsminute==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
								echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
							}
						?>
					</select>
					
					</td></tr></table>
	  			</td>
    		</tr>
