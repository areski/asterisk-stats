<?php 
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");

session_start();

getpost_ifset(array('posted', 'type', 'day1', 'day2', 'day3', 'day4', 'day5', 'day6', 'day7', 'hour', 'gtm', 'email', 'title'));


// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME= SCHEDULER_TABLENAME;
$FG_COL_QUERY="id, type, days, hour, gtm, time_server, email, subject, report";

include("../asterisk-fak/PP_header.php");


if ($posted==1){

	if (strlen($_SESSION["pr_sql_export"])>10){
		$DBHandle  = DbConnect();
		
		$report=str_replace("'","\'",$_SESSION["pr_sql_export"]);
		
		$gtm = 0;
		//$gtm_num = explode(" ",$gtm);
		//$gtm = $gtm_num[1];
		
		$days='';
		if ($type == 1)	{
			$days_ = $day1.'|'.$day2.'|'.$day3.'|'.$day4.'|'.$day5.'|'.$day6.'|'.$day7;
			$days_temp = explode("|",$days_);
			$num_days = count($days_temp);
			for ($i=0;$i<$num_days;$i++){
				if ($days_temp[$i] != ""){
					$days.= $days_temp[$i]."|";
				}
			}
		}		
	
		$time_server = 0;
	
		$value = "'','$type', '$days', '$hour', '$gtm', '$time_server','$email', '$title', '$report'";
		$scheduler_new = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
		$result = $scheduler_new -> Add_Table($DBHandle ,$value, null, null, null);
		if (!$result){
			$msg = "<p>&nbsp;</p><p align='center'>".$scheduler_new->$errstr."</p>";
			$msg .= "<p>&nbsp;</p><p align='center'><b> Error </b></p>";
		}else{
			$msg = "<p>&nbsp;</p><p align='center'><b>Scheduler ".$title." Saved</b></p>";
		}
		
		
	}else{
		$msg = "<p>&nbsp;</p><p align='center'><b>Error no scheduler to save</b></p>";
	}

	echo $msg;
	

	include("../asterisk-fak/PP_footer.php");
	exit();
}	
?>

<br><br><br>
<!-- ** ** ** ** ** Part for the scheduler ** ** ** ** ** -->
	<center>
	<FORM METHOD=POST ACTION="<?php echo $PHP_SELF?>">
	<INPUT TYPE="hidden" NAME="posted" value=1>
		<table class="bar-status" width="75%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody>
			<tr>
        		<td class="bar-search" align="left" bgcolor="#555577">
				<input type="radio" name="type" value="0" checked="checked">
				<font face="verdana" size="1" color="#ffffff"><b>
					Daily
				</b></font>
			</td>
			<td class="bar-search" align="left" bgcolor="#cddeff">
				<font face="verdana" size="1" color="#000000">
					Every day
				</font>
			</td>	
			</tr>
			<tr>
			<td class="bar-search" align="left" bgcolor="#555577">
				<input type="radio" name="type" value="1">
				<font face="verdana" size="1" color="#ffffff"><b>
                                        Weekly
                                </b></font>
			</td>
			<td class="bar-search" align="left" bgcolor="#cddeff">
			<?php
				$daysofweek = array( "Monday", "Thuesday","Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
				foreach ($daysofweek as $key => $day){
				?>
					<input type="radio" name="day<?php echo $key+1?>" value="<?php echo $key+1?>">
					<font face="verdana" size="1" color="#000000">
						<?php echo $day; ?>
					</font>
				<?php	
				}
			?>
			</td>
			</tr>
			<td class="bar-search" align="left" bgcolor="#555577">
				<input type="radio" name="type" value="2"> 
				<font face="verdana" size="1" color="#ffffff"><b>
					Monthly
				</b></font>
			</td>
			<td class="bar-search" align="left" bgcolor="#cddeff">
				<font face="verdana" size="1" color="#000000">Will be sent the last day of month</font>
			</td>
			</tr>

		</tbody></table>
		<table>
		<tbody>
			<tr>
			<td>
				<b>Delivere Time</b>
			</td>
			<td>
				<select name="hour">
				<?php
					for ($hour=0;$hour<24;$hour++){
					?>
						<option value="<? echo $hour; ?>">
						<?php
							if ($hour < 10) echo '0'.$hour.':00'; 
							else echo $hour.':00';	
						?>
						</option>
					<?
					}
				?>
				</select>
				<!-- <select name="gtm"> -->
				<?php
				//	$gtm = array("GTM -1100","GTM -1000","GTM -0900","GTM -0800","GTM -0700","GTM -0600","GTM -0500","GTM -0400","GTM -0300","GTM -0200","GTM -0100","GTM 0000","GTM +0100","GTM +0200","GTM +0300","GTM +0400","GTM +0500","GTM +0600","GTM +0700","GTM +0800","GTM +0900","GTM +1000");
					//foreach ($gtm as $value){
					?>
					<!-- <option value="<?php echo $value?>"><?php echo $value?></option>	-->
					<?
					//}
				?>	
				
				<!-- </select> -->
			</td>
			</tr>
			
			<tr>
			<td>
				<b>Email address</b>
			</td>
			<td>
				<input type="text" name="email" value="" maxlength="50" size="40">	
			</td>
			</tr>
			
			<tr>
                        <td>
				<b>Scheduler name</b>
                        </td>
			<td>
				<input type="text" name="title" value="" maxlength="50" size="40"><br>
				( will be used also for subjet in mail )
			</td>
	                </tr>
		</tbody>
		</table>
		<p><input type="submit" name="send" value="Save Scheduler"> &nbsp;<input type="button" value="List" onclick="javascript:location.href='list_scheduler.php'">
		</p>
	</FORM>
</center>


<br><br>

</center>


<?php
	include("../asterisk-fak/PP_footer.php");
?>
