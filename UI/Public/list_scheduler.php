<?php 
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");

session_start();

getpost_ifset(array('current_page', 'order', 'sens','nb_record'));



// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME= SCHEDULER_TABLENAME;
$FG_TABLE_CLAUSE="";
$FG_COL_QUERY="id, type, days, hour, email, subject";

$FG_TABLE_COL[]=array ("ID", "id", "5%", "center", "SORT", "5");
$FG_TABLE_COL[]=array ("Type", "type", "13%", "center", "", "30");
$FG_TABLE_COL[]=array ("Days", "days", "10%", "center", "", "30");
$FG_TABLE_COL[]=array ("Hour", "hour", "12%", "center", "", "30");
$FG_TABLE_COL[]=array ("Email", "email", "8%", "center", "", "30");
$FG_TABLE_COL[]=array ("Subject", "subject", "8%", "center", "", "30");


$FG_TABLE_DEFAULT_ORDER = "id";
$FG_TABLE_DEFAULT_SENS = "DESC";
$FG_LIMITE_DISPLAY=5;


// Number of column in the html table
$FG_NB_TABLE_COL=count($FG_TABLE_COL);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}
		
$daysofweek = array( "Monday", "Thuesday","Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

include("../asterisk-fak/PP_header.php");

$DBHandle  = DbConnect();

if (!isset ($current_page) || ($current_page == "")){
        $current_page=0;
	$QUERY = "SELECT id FROM ".$FG_TABLE_NAME." WHERE 1";
	$res = $DBHandle -> query($QUERY);
        $num = $res -> numRows();
	$nb_record = $num;
}


$scheduler = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$list = $scheduler -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, $order, $sens, null, null, $FG_LIMITE_DISPLAY, $current_page*$FG_LIMITE_DISPLAY);



if ($nb_record<=$FG_LIMITE_DISPLAY){
	$nb_record_max=1;
}else{
	if ($nb_record % $FG_LIMITE_DISPLAY == 0){
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY));
	}else{
		$nb_record_max=(intval($nb_record/$FG_LIMITE_DISPLAY)+1);
	}
}

?>
<br>
<!-- ** ** ** ** ** Part for the scheduler ** ** ** ** ** -->
<center>
	<table class="bar-status" width="90%" border="0" cellspacing="1" cellpadding="2" align="center">
	<tbody>
	<tr>
<?php		
	for($i=0;$i<$FG_NB_TABLE_COL;$i++){
?>
	<TD class="bar-search" align="left" bgcolor="#555577"
	<center><font face="verdana" size="1" color="#ffffff"><b><?php echo $FG_TABLE_COL[$i][0]?></b></font></center></td>
<?php
	}

	echo '<td>&nbsp;</td><tr>';
	foreach ($list as $row){
		$days='';
?>
	<tr>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                   <font face="verdana" size="1" color="#000000"><?php echo $row[0]?></font>
		</td>
<?php
		switch ($row[1]){
			case 0: $type="Daily";
			break;
			case 1: $type="Weekly";
			break;
			case 2: $type="Monthly";
			break;
		}
?>
		
		<td class="bar-search" align="left" bgcolor="#cddeff">
                   <font face="verdana" size="1" color="#000000"><?php echo $type?></font>
		</td>
<?php
		$days_ = explode("|",$row[2]);
		foreach ($days_ as $value){
			if ($value != ""){
				$days .= " ".$daysofweek[$value-1];					
			}
		}
?>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                    <font face="verdana" size="1" color="#000000"><?php echo $days?></font>
		</td>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                    <font face="verdana" size="1" color="#000000"><?php echo $row[3]?></font>
		</td>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                    <font face="verdana" size="1" color="#000000"><?php echo $row[4]?></font>
		</td>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                   <font face="verdana" size="1" color="#000000"><?php echo $row[5]?></font>
		</td>
		<td class="bar-search" align="left" bgcolor="#cddeff">
                   <font face="verdana" size="1" color="#000000"><a href="del_scheduler.php?id=<?php echo $row[0]?>">Delete</a></font>
		</td>
	</tr>
<?php
	}
?>
 </tbody></table>
 
 <table>
	<TR bgcolor="#ffffff">
          <TD bgColor=#ADBEDE height=16 style="PADDING-LEFT: 5px; PADDING-RIGHT: 3px">
          <TABLE border=0 cellPadding=0 cellSpacing=0 width="100%">
              <TBODY>
               <TR>
                <TD align="right"><SPAN style="COLOR: #ffffff; FONT-SIZE: 11px"><B>
                <?php if ($current_page>0){?>
                 <img src="../images/fleche-g.gif" width="5" height="10"> <a href="<?php echo $PHP_SELF?>?order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php  echo ($current_page-1)?>&nb_record=<?php echo ($nb_record)?>">
             Previous </a> -
            <?php }?>
            <?php echo ($current_page+1);?> / <?php  echo $nb_record_max;?>
            <?php if ($current_page<$nb_record_max-1){?>
            - <a href="<?php echo $PHP_SELF?>?order=<?php echo $order?>&sens=<?php echo $sens?>&current_page=<?php  echo ($current_page+1)?>&nb_record=<?php echo ($nb_record)?>">
             Next </a> <img src="../images/fleche-d.gif" width="5" height="10">
            </B></SPAN>
          <?php }?>
       </TD>
      </TBODY>
      </TABLE></TD>
      </TR>
 </table>
 
</center>

<?php
	include("../asterisk-fak/PP_footer.php");
?>
