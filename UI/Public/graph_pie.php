<?php
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph_pie.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph_pie3d.php");


/*
NOTE : 
## FAST SOLUTION 
cdrasterisk=> SELECT sum(AsteriskDuration) FROM cdr WHERE AsteriskStartTime < '2005-02-01' AND AsteriskStartTime >= '2005-01-01';
## SLOW SOLUTION 
cdrasterisk=> SELECT sum(AsteriskDuration) FROM cdr WHERE AsteriskStartTime < date '2005-02-01'  - interval '0 months' AND AsteriskStartTime >=  date '2005-02-01'  - interval '1 months';
*/

getpost_ifset(array('months_compare', 'min_call', 'fromstatsday_sday', 'days_compare', 'fromstatsmonth_sday', 'AsteriskDsttype', 'sourcetype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx'));

$FG_DEBUG = 0;
$months = Array ( 0 => 'Jan', 1 => 'Feb', 2 => 'Mar', 3 => 'Apr', 4 => 'May', 5 => 'Jun', 6 => 'Jul', 7 => 'Aug', 8 => 'Sep', 9 => 'Oct', 10 => 'Nov', 11 => 'Dec' );

if (!isset($months_compare)) $months_compare = 3;
if (!isset($fromstatsmonth_sday)) $fromstatsmonth_sday = date("Y-m");	


// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;

//$link = DbConnect();
$DBHandle  = DbConnect();


$FG_COL_QUERY = ' sum(AsteriskDuration) ';
if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}


$SQLcmd = '';

if ($_GET['before']) {
	if (strpos($SQLcmd, 'WHERE') > 0) {
		$SQLcmd = "$SQLcmd AND ";
	} else {
		$SQLcmd = "$SQLcmd WHERE ";
	}
	$SQLcmd = "$SQLcmd AsteriskStartTime<'".$_POST['before']."'";
}

if ($_GET['after']) {    
	if (strpos($SQLcmd, 'WHERE') > 0) {
		$SQLcmd = "$SQLcmd AND ";
	} else {
		$SQLcmd = "$SQLcmd WHERE ";
	}
	$SQLcmd = "$SQLcmd AsteriskStartTime>'".$_GET['after']."'";
}

$SQLcmd = do_field($SQLcmd, 'AsteriskClid');
$SQLcmd = do_field($SQLcmd, 'AsteriskSrc');
$SQLcmd = do_field($SQLcmd, 'AsteriskDst');
$SQLcmd = do_field($SQLcmd, 'NASIPAddress');

$SQLcmd = do_field($SQLcmd, 'AsteriskUserField');
$SQLcmd = do_field($SQLcmd, 'AsteriskDstCtx');

$date_clause='';

$min_call= intval($min_call);
if (($min_call!=0) && ($min_call!=1)) $min_call=0;

if (!isset($fromstatsday_sday)){	
	$fromstatsday_sday = date("d");
	$fromstatsmonth_sday = date("Y-m");	
}

if (!isset($days_compare) ){		
	$days_compare=2;
}

 

list($myyear, $mymonth)= split ("-", $fromstatsmonth_sday);

$mymonth = $mymonth +1;
if ($current_mymonth==13) {
	$mymonth=1;		
	$myyear = $myyear + 1;
}


for ($i=0; $i<$months_compare+1; $i++){
	// creer un table legende	
	$current_mymonth = $mymonth -$i;
	if ($current_mymonth<=0) {
		$current_mymonth=$current_mymonth+12;		
		$minus_oneyar = 1;
	}
	$current_myyear = $myyear - $minus_oneyar;
	
	$current_mymonth2 = $mymonth -$i -1;
	if ($current_mymonth2<=0) {
		$current_mymonth2=$current_mymonth2+12;		
		$minus_oneyar = 1;
	}
	$current_myyear2 = $myyear - $minus_oneyar;
	
	
	if (DB_TYPE == "postgres"){	
		$date_clause= " AND AsteriskStartTime >= '$current_myyear2-".sprintf("%02d",intval($current_mymonth2))."-01' AND AsteriskStartTime < '$current_myyear-".sprintf("%02d",intval($current_mymonth))."-01'";				
	}else{
		$date_clause= " AND AsteriskStartTime >= '$current_myyear2-".sprintf("%02d",intval($current_mymonth2))."-01' AND AsteriskStartTime < '$current_myyear-".sprintf("%02d",intval($current_mymonth))."-01'";		
	}
	
	
	if (strpos($SQLcmd, 'WHERE') > 0) { 
		$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
	}elseif (strpos($date_clause, 'AND') > 0){
		$FG_TABLE_CLAUSE = substr($date_clause,5); 
	}
	
	if ($FG_DEBUG == 3) echo $FG_TABLE_CLAUSE;
	
	
	
	$list_total = $instance_table_graph -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, null, null, null, null, null, null);
	$data[] = $list_total[0][0];	
	$mylegend[] = $months[$current_mymonth2-1]." $current_myyear : ".intval($list_total[0][0]/60)." min";

}

/**************************************/


if (FLASHCHART) {
	
	$title = "Traffic Last $months_compare Months";
	
	include_once(dirname(__FILE__) . "/../lib/charts/charts.php");
	
	
	array_unshift($mylegend, "");
	array_unshift($data, "");
	
	//print_r($mylegend);
	//print_r($data);

	//$chart[ 'chart_data' ] = array ( array ( "", "2004", "2005", "2006", "2007"), array ( "", 15, 27, 45, 60 ) );
	$chart[ 'chart_data' ] = array ( $mylegend, $data );
	$chart[ 'chart_grid_h' ] = array ( 'thickness'=>0 );
	$chart[ 'chart_pref' ] = array ( 'rotation_x'=>45 ); 
	$chart[ 'chart_rect' ] = array ( 'x'=>125, 'y'=>50, 'width'=>400, 'height'=>300, 'positive_alpha'=>0 );
	$chart[ 'chart_transition' ] = array ( 'type'=>"scale", 'delay'=>.3, 'duration'=>.25, 'order'=>"category" );
	$chart[ 'chart_type' ] = "3d pie";
	$chart[ 'chart_value' ] = array ( 'color'=>"000000", 'alpha'=>65, 'font'=>"arial", 'bold'=>true, 'size'=>10, 'position'=>"inside", 'prefix'=>"", 'suffix'=>"", 'decimals'=>0, 'separator'=>"", 'as_percentage'=>true );
	
	// $chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"000000", 'alpha'=>4, 'size'=>40, 'x'=>-50, 'y'=>260, 'width'=>500, 'height'=>50, 'text'=>"$title", 'h_align'=>"center", 'v_align'=>"middle" )) ;
	
	$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"ff6644", 'alpha'=>70, 'font'=>"arial", 'rotation'=>0, 'bold'=>true, 'size'=>53, 'x'=>55, 'y'=>0, 'width'=>600, 'height'=>200, 'text'=>"$title", 'h_align'=>"center" ) );


	
	$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'bullet'=>"circle", 'font'=>"arial", 'bold'=>true, 'size'=>12, 'color'=>"ffffff", 'alpha'=>85 ); 
	$chart[ 'legend_rect' ] = array ( 'x'=>0, 'y'=>45, 'width'=>150, 'height'=>210, 'margin'=>10, 'fill_color'=>"ffffff", 'fill_alpha'=>10, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );  
	$chart[ 'legend_transition' ] = array ( 'type'=>"dissolve", 'delay'=>0, 'duration'=>1 );
	
	$chart[ 'series_color' ] = array ( "00ff88", "ffaa00","44aaff", "aa00ff" ); 
	$chart[ 'series_explode' ] = array ( 25, 75, 0, 0 );
	
	SendChartData ( $chart );
	
	
} else {
	
	
	$data = array_reverse($data);
	
	$graph = new PieGraph(475,200,"auto");
	$graph->SetShadow();
	
	$graph->title->Set("Traffic Last $months_compare Months");
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
	$p1 = new PiePlot3D($data);
	$p1->ExplodeSlice(1);
	$p1->SetCenter(0.35);
	//print_r($gDateLocale->GetShortMonth());
	//Array ( [0] => Jan [1] => Feb [2] => Mar [3] => Apr [4] => May [5] => Jun [6] => Jul [7] => Aug [8] => Sep [9] => Oct [10] => Nov [11] => Dec )
	//$p1->SetLegends($gDateLocale->GetShortMonth());
	$p1->SetLegends($mylegend);
	
	
	// Format the legend box
	$graph->legend->SetColor('navy');
	$graph->legend->SetFillColor('gray@0.8');
	$graph->legend->SetLineWeight(1);
	//$graph->legend->SetFont(FF_ARIAL,FS_BOLD,8);
	$graph->legend->SetShadow('gray@0.4',3);
	//$graph->legend->SetAbsPos(10,80,'right','bottom');
	
	
	$graph->Add($p1);
	$graph->Stroke();

}

?>
