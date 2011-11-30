#!/usr/bin/php -q
<?php
include_once(dirname(__FILE__) . "/../../lib/defines.php");
include_once(dirname(__FILE__) . "/../../lib/Class.Table.php");
require_once 'Mail.php';
require_once 'Mail/mime.php';
include (dirname(__FILE__) . "/functions.php");

$FG_TABLE_NAME= SCHEDULER_TABLENAME;
$FG_COL_QUERY="id, days ,email, subject";


$DBHandle  = DbConnect();
$scheduler_new = new Table($FG_TABLE_NAME, $FG_COL_QUERY);


$file_poiner = open_log();
$msg='';

// Get date
// Num of day of week:num of day of month:num of month:days of this month:
$get_date = date("w:j:n:t:G");

// $arr_date[0] = Numeric day of Week ( Sun = 0 , Saturday = 6)
// $arr_date[1] = Numeric day of Month ( 1 .. 31 )
// $arr_date[2] = Numeric Month  ( 1 .. 12 )
// $arr_date[3] = Days of current month ( 28 ,29 , 30 , 31 )
// $arr_date[4] = Current hour ( 0 ..23 )

$arr_date = explode(":",$get_date);

// Fix week of day
if  ($arr_date[0] == 0) $day_of_week = '7';
else  $day_of_week = '$arr_date[0]';

$day_of_month = $arr_date[1];
$days_of_current_month = $arr_date[3];
$current_hour = $arr_date[4];

// Get Monthly schedulers
// Here check if today is the last day of month 
// type could be Daily = 0 ; Weekly = 1 ; Monthly = 2

if ( $day_of_month == $days_of_current_month ) {
	$FG_TABLE_CLAUSE = " type = 2 and hour = '$current_hour' ";
	
	$list = $scheduler_new -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, null, null, null, null, null, null);
	$msg .= "\n\n ****** Monthly".date("Y/m/d G:i:s")." ******\n\n";
	
	$sent = 0;
	foreach ($list as $sched){
		$msg .= " Scheduler (".$sched[0].") ".$sched[3]." for ".$sched[2]."\n";
		$file = get_pdf($sched[0]);
		$sent = send_mail($sched[2],$file,$sched[3]);
		if ($sent) $msg .= "Sent to ".$sched[2]." [OK]\n";
		else $msg .= "Sent to ".$sched[2]." [Failed]\n";
	}
	
	$msg .=  "\n*************************************************";
	
	unset($list);
}

// Get the Weekly schedulers

$FG_TABLE_CLAUSE = " type = 1 AND hour = '$current_hour'";

$list = $scheduler_new -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, null, null, null, null, null, null);

// check the day for Weekly
$msg .=  "\n ****** Weekly ".date("Y/m/d G:i:s")." ******\n\n";

$sent = 0;
foreach ($list as $sched){
	$days = explode('|',$sched[1]);
	if (in_array($day_of_week,$days)){
		$msg .= " Scheduler (".$sched[0].") ".$sched[3]." for ".$sched[2]."\n";
		$file = get_pdf($sched[0]);
		$sent = send_mail($sched[2],$file,$sched[3]);
		if ($sent) $msg .= "Sent to ".$sched[2]." [OK]\n";
                else $msg .= "Sent to ".$sched[2]." [Failed]\n";
	}	
}

$msg .=  "\n";

unset($list);

// Get the Daily schedulers

$FG_TABLE_CLAUSE = " type = 0 AND hour = '$current_hour'";

$list = $scheduler_new -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, null, null, null, null, null, null);
$msg .=  "\n*************************************************";
$msg .=  "\n\n ****** Daily".date("Y/m/d G:i:s")." ****** \n\n";

foreach ($list as $sched){
	$msg .= " Scheduler (".$sched[0].") ".$sched[3]." for ".$sched[2]."\n";
	$file = get_pdf($sched[0]);
	$sent = send_mail($sched[2],$file,$sched[3]);
	if ($sent) $msg .= "Sent to ".$sched[2]." [OK]\n";
	else $msg .= "Sent to ".$sched[2]." [Failed]\n";
}

$msg .= "****************************************************\n";


write_log($file_poiner,$msg);

close_log($file_poiner);

?>
