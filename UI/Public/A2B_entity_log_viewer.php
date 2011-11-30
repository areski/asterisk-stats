<?php
include ("../lib/defines.php");
include ("../lib/module.access.php");
include ("../lib/Form/Class.FormHandler.inc.php");
include ("./form_data/FG_var_log_viewer.inc");
include ("../lib/smarty.php");

if (! has_rights (ACX_ACCESS)){
	Header ("HTTP/1.0 401 Unauthorized");
	Header ("Location: PP_error.php?c=accessdenied");
	die();
}

/***********************************************************************************/

$HD_Form -> setDBHandler (DbConnect());

$HD_Form -> init();
if ($id!="" || !is_null($id)){
	$HD_Form -> FG_EDITION_CLAUSE = str_replace("%id", "$id", $HD_Form -> FG_EDITION_CLAUSE);
}

if (!isset($form_action))  $form_action="list"; //ask-add
if (!isset($action)) $action = $form_action;

$list = $HD_Form -> perform_action($form_action);

// #### HEADER SECTION
$smarty->display('main.tpl');

// #### HELP SECTION
echo $CC_help_list_log;

// #### TOP SECTION PAGE
$HD_Form -> create_toppage ($form_action);


// #### CREATE FORM OR LIST
//$HD_Form -> CV_TOPVIEWER = "menu";
if (strlen($_GET["menu"])>0) $_SESSION["menu"] = $_GET["menu"];

?>

<FORM METHOD=POST name="myForm" ACTION="<?php echo $PHP_SELF?>?s=1&t=0&order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php echo $current_page?>">
	<INPUT TYPE="hidden" NAME="posted" value=1>
	<INPUT TYPE="hidden" NAME="current_page" value=0>	
		<table class="bar-status" width="85%" border="0" cellspacing="1" cellpadding="2" align="center">
			<tbody>
			<?php  if ($_SESSION["pr_groupID"]==2 && is_numeric($_SESSION["pr_IDCust"])){ ?>
			<?php  }else{ ?>
			<tr>
				<td align="left" valign="top" class="bgcolor_004">					
					<font class="fontstyle_003">&nbsp;&nbsp;<?php echo gettext("CUSTOMERS");?></font>
				</td>				
				<td class="bgcolor_005" align="left">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
					<td class="fontstyle_searchoptions">
						<?php echo gettext("ADMIN USER");?>: <INPUT TYPE="text" NAME="enteradmin" value="<?php echo $enteradmin?>" class="form_input_text">
						<a href="#" onclick="window.open('A2B_entity_user.php?popup_select=1&popup_formname=myForm&popup_fieldname=enteradmin' , 'AdminSelection','scrollbars=1,width=550,height=330,top=20,left=100,scrollbars=1');"><img src="<?php echo Images_Path;?>/icon_arrow_orange.gif"></a>
					</td>					
				</tr></table></td>
			</tr>			
			<?php  }?>
			<tr>
        		<td class="bgcolor_002" align="left">

					<input type="radio" name="Period" value="Month" <?php  if (($Period=="Month") || !isset($Period)){ ?>checked="checked" <?php  } ?>> 
					<font class="fontstyle_003"><?php echo gettext("SELECT MONTH");?></font>
				</td>
      			<td class="bgcolor_003" align="left">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr><td class="fontstyle_searchoptions">
	  				<input type="checkbox" name="frommonth" value="true" <?php  if ($frommonth){ ?>checked<?php }?>>
					<?php echo gettext("From");?> : <select name="fromstatsmonth" class="form_input_select">
					<?php
						$monthname = array( gettext("January"), gettext("February"),gettext("March"), gettext("April"), gettext("May"), gettext("June"), gettext("July"), gettext("August"), gettext("September"), gettext("October"), gettext("November"), gettext("December"));
						$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
						   if ($year_actual==$i){
							$monthnumber = date("n")-1; // Month number without lead 0.
						   }else{
							$monthnumber=11;
						   }		   
						   for ($j=$monthnumber;$j>=0;$j--){	
							$month_formated = sprintf("%02d",$j+1);
				   			if ($fromstatsmonth=="$i-$month_formated")	$selected="selected";
							else $selected="";
							echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
						   }
						}
					?>		
					</select>
					</td><td  class="fontstyle_searchoptions">&nbsp;&nbsp;
					<input type="checkbox" name="tomonth" value="true" <?php  if ($tomonth){ ?>checked<?php }?>> 
					<?php echo gettext("To");?> : <select name="tostatsmonth" class="form_input_select">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
						   if ($year_actual==$i){
							$monthnumber = date("n")-1; // Month number without lead 0.
						   }else{
							$monthnumber=11;
						   }		   
						   for ($j=$monthnumber;$j>=0;$j--){	
							$month_formated = sprintf("%02d",$j+1);
				   			if ($tostatsmonth=="$i-$month_formated") $selected="selected";
							else $selected="";
							echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
						   }
						}
					?>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
			
			<tr>
        		<td align="left" class="bgcolor_004">
					<input type="radio" name="Period" value="Day" <?php  if ($Period=="Day"){ ?>checked="checked" <?php  } ?>> 
					<font class="fontstyle_003"><?php echo gettext("SELECT DAY");?></font>
				</td>
      			<td align="left" class="bgcolor_005">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr><td class="fontstyle_searchoptions">
	  				<input type="checkbox" name="fromday" value="true" <?php  if ($fromday){ ?>checked<?php }?>> <?php echo gettext("From");?> :
					<select name="fromstatsday_sday" class="form_input_select">
						<?php  
						for ($i=1;$i<=31;$i++){
							if ($fromstatsday_sday==sprintf("%02d",$i)) $selected="selected";
							else	$selected="";
							echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
						}
						?>	
					</select>
				 	<select name="fromstatsmonth_sday" class="form_input_select">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							if ($year_actual==$i){
								$monthnumber = date("n")-1; // Month number without lead 0.
							}else{
								$monthnumber=11;
							}		   
							for ($j=$monthnumber;$j>=0;$j--){	
								$month_formated = sprintf("%02d",$j+1);
								if ($fromstatsmonth_sday=="$i-$month_formated") $selected="selected";
								else $selected="";
								echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							}
						}
					?>
					</select>
					</td><td class="fontstyle_searchoptions">&nbsp;&nbsp;
					<input type="checkbox" name="today" value="true" <?php  if ($today){ ?>checked<?php }?>> 
					<?php echo gettext("To");?>  :
					<select name="tostatsday_sday" class="form_input_select">
					<?php  
						for ($i=1;$i<=31;$i++){
							if ($tostatsday_sday==sprintf("%02d",$i)){$selected="selected";}else{$selected="";}
							echo '<option value="'.sprintf("%02d",$i)."\"$selected>".sprintf("%02d",$i).'</option>';
						}
					?>						
					</select>
				 	<select name="tostatsmonth_sday" class="form_input_select">
					<?php 	$year_actual = date("Y");  	
						for ($i=$year_actual;$i >= $year_actual-1;$i--)
						{		   
							if ($year_actual==$i){
								$monthnumber = date("n")-1; // Month number without lead 0.
							}else{
								$monthnumber=11;
							}		   
							for ($j=$monthnumber;$j>=0;$j--){	
								$month_formated = sprintf("%02d",$j+1);
							   	if ($tostatsmonth_sday=="$i-$month_formated") $selected="selected";
								else	$selected="";
								echo "<OPTION value=\"$i-$month_formated\" $selected> $monthname[$j]-$i </option>";				
							}
						}
					?>
					</select>
					</td></tr></table>
	  			</td>
    		</tr>
			<tr>
				<td class="bgcolor_002" align="left">			
					<font class="fontstyle_003">&nbsp;&nbsp;<?php echo gettext("LOG LEVEL");?></font>
				</td>				
				<td class="bgcolor_003" align="left">
				<select name="loglevel" style="width:90px;">
				<option value="0" <?php if ($loglevel == 0) echo "selected"?>>ALL</option>
				<option value="1" <?php if ($loglevel == 1) echo "selected"?>>Level-1</option>
				<option value="2" <?php if ($loglevel == 2) echo "selected"?>>Level-2</option>
				<option value="3" <?php if ($loglevel == 3) echo "selected"?>>Level-3</option>				
				</select>
				</td>
			</tr>			
			<tr>
        		<td class="bgcolor_004" align="left" > </td>

				<td class="bgcolor_005" align="center" >
					<input type="image"  name="image16" align="top" border="0" src="<?php echo Images_Path;?>/button-search.gif" />
					
	  			</td>
    		</tr>
		</tbody></table>
</FORM>

<?php

$HD_Form -> create_form ($form_action, $list, $id=null) ;

// #### FOOTER SECTION
$smarty->display('footer.tpl');

?>
