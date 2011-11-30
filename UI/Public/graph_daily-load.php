<?php
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph.php");
include_once(dirname(__FILE__) . "/jpgraph_lib/jpgraph_bar.php");

// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;


getpost_ifset(array('min_call', 'fromstatsday_sday', 'days_compare', 'fromstatsmonth_sday', 'AsteriskDsttype', 'sourcetype', 'AsteriskClidtype', 'NASIPAddress', 'resulttype', 'AsteriskDst', 'AsteriskSrc', 'AsteriskClid', 'AsteriskUserFieldtype', 'AsteriskUserField', 'AsteriskDstCtxType', 'AsteriskDstCtx'));

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME=DB_TABLENAME;

//$link = DbConnect();
$DBHandle  = DbConnect();



$FG_TABLE_DEFAULT_ORDER = "AsteriskStartTime";
$FG_TABLE_DEFAULT_SENS = "DESC";

// This Variable store the argument for the SQL query
$FG_COL_QUERY='AsteriskStartTime, AsteriskDuration';
$FG_COL_QUERY_GRAPH='AsteriskStartTime, AsteriskDuration';






if ($FG_DEBUG == 3) echo "<br>Table : $FG_TABLE_NAME  	- 	Col_query : $FG_COL_QUERY";
$instance_table = new Table($FG_TABLE_NAME, $FG_COL_QUERY);
$instance_table_graph = new Table($FG_TABLE_NAME, $FG_COL_QUERY_GRAPH);


if ( is_null ($order) || is_null($sens) ){
	$order = $FG_TABLE_DEFAULT_ORDER;
	$sens  = $FG_TABLE_DEFAULT_SENS;
}

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


$date_clause='';

$min_call= intval($min_call);
if (($min_call!=0) && ($min_call!=1)) $min_call=0;

if (!isset($fromstatsday_sday)){	
	$fromstatsday_sday = date("d");
	$fromstatsmonth_sday = date("Y-m");	
}


 

if (DB_TYPE == "postgres"){	
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) 
	$date_clause.=" AND AsteriskStartTime < date'$fromstatsmonth_sday-$fromstatsday_sday'+ INTERVAL '1 DAY' AND AsteriskStartTime >= '$fromstatsmonth_sday-$fromstatsday_sday'";
}else{
	if (isset($fromstatsday_sday) && isset($fromstatsmonth_sday)) $date_clause.=" AND AsteriskStartTime < ADDDATE('$fromstatsmonth_sday-$fromstatsday_sday',INTERVAL 1 DAY) AND AsteriskStartTime >= '$fromstatsmonth_sday-$fromstatsday_sday'";  
}


if (strpos($SQLcmd, 'WHERE') > 0) { 
	$FG_TABLE_CLAUSE = substr($SQLcmd,6).$date_clause; 
}elseif (strpos($date_clause, 'AND') > 0){
	$FG_TABLE_CLAUSE = substr($date_clause,5); 
}

if ($FG_DEBUG == 3) echo $FG_TABLE_CLAUSE;

$list_total = $instance_table_graph -> Get_list ($DBHandle, $FG_TABLE_CLAUSE, 'AsteriskStartTime', 'ASC', null, null, null, null);


/**************************************/


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

//print_r($table_graph_hours);
//exit();

$mmax=0;
$totalcall==0;
$totalminutes=0;
foreach ($table_graph as $tkey => $data){	
	if ($mmax < $data[1]) $mmax=$data[1];
	$totalcall+=$data[0];
	$totalminutes+=$data[1];
}




/************************************************/


$datax1 = array_keys($table_graph_hours);
$datay1 = array_values ($table_graph_hours);

//$days_compare // 3
$nbday=0;  // in tableau_value and array_hours to select the day in which you store the data
//$min_call=0; // min_call variable : 0 > get the number of call 1 > number minutes


$table_subtitle[]="Statistic : Load by hours";
$table_subtitle[]="Statistic : Minutes by Hours";



$table_colors[]="yellow@0.3";
$table_colors[]="purple@0.3";
$table_colors[]="green@0.3";
$table_colors[]="blue@0.3";
$table_colors[]="red@0.3";



$jour = substr($datax1[0],8,2); //le jour courant 
$legend[0] = substr($datax1[0],0,10); //l

//print_r ($table_graph_hours);
// Create the graph to compare the day
// extract all minutes/nb call for each hours 
foreach ($table_graph_hours as $key => $value) {
	
	$jour_suivant = substr($key,8,2);
	
	if($jour_suivant != $jour) {
		  $nbday++; 
		  $legend[$nbday] = substr($key,0,10);
		  $jour = $jour_suivant;
	}
  
	
	$heure = intval(substr($key,11,2));

	if ($min_call == 0) $div = 1; else $div = 60;

	$tableau_value[$nbday][$heure] = $value[$min_call]/$div;
}



// fill the empty cell by 0 0
for ($i=0; $i<=$nbday; $i++){
	for ($j=0; $j<24; $j++){
		if (!isset($tableau_value[$i][$j])) $tableau_value[$i][$j]=0;
	}
}


// Replace the 0 by null for the hours
$i = 23;
while ($tableau_value[$nbday][$i] == 0) {
	$tableau_value[$nbday][$i] = null;
	$i--;
}

foreach ($datay1 as $tkey => $data){
	$dataz1[]=$data[1];
	$dataz2[]=$data[0];
}



$array_hours[0] = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");


if (FLASHCHART) {
	
	$title = $table_subtitle[$min_call];
	
	include_once(dirname(__FILE__) . "/../lib/charts/charts.php");
	
	
	$chart[ 'chart_data' ] = array ( $array_hours[0] );
	for ($indgraph=0;$indgraph<=$nbday;$indgraph++){
		array_unshift($tableau_value[$indgraph], $legend[$indgraph]);
		array_push($chart[ 'chart_data' ], $tableau_value[$indgraph]);
	}
	
	$graph_3d = true;
	
	if (!$graph_3d){
	
		$chart[ 'axis_category' ] = array (  'size'=>16, 'color'=>"000000", 'alpha'=>75, 'skip'=>0 ,'orientation'=>"horizontal" ); 
		$chart[ 'axis_ticks' ] = array ( 'value_ticks'=>false, 'category_ticks'=>true, 'major_thickness'=>2, 'minor_thickness'=>1, 'minor_count'=>1, 'major_color'=>"000000", 'minor_color'=>"222222" ,'position'=>"inside" );
		
		
		//$chart[ 'chart_data' ] = array ( array ( "", "2004", "2005", "2006", "2007", "2008", "2009" ), array ( "region 1", 48, 55, 80, 100, 80, 100 ), array ( "region 2", -12, 10, 55, 65, 55, 65 ), array ( "region 3", 27, -20, 15, 80, 80, 150), array ( "region 4", 27, 250, 35, 80, 50, 150), array ( "region 5", 7, -2, 5, 0, 86, 100) );
		
		$chart[ 'chart_grid_h' ] = array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
		$chart[ 'chart_pref' ] = array ( 'line_thickness'=>2, 'point_shape'=>"circle", 'fill_shape'=>false );
		//$chart[ 'chart_rect' ] = array ( 'x'=>50, 'y'=>100, 'width'=>320, 'height'=>150, 'positive_color'=>"ffffff", 'positive_alpha'=>50, 'negative_color'=>"000000", 'negative_alpha'=>10 );
		$chart[ 'chart_rect' ] = array ( 'x'=>50, 'y'=>70, 'width'=>600, 'height'=>300, 'positive_color'=>"ffffff", 'positive_alpha'=>50, 'negative_color'=>"000000", 'negative_alpha'=>10 );
		
		//$chart[ 'chart_transition' ] = array ( 'type'=>"slide_left", 'delay'=>.5, 'duration'=>.5, 'order'=>"series" );
		$chart[ 'chart_transition' ] = array ( 'type'=>"scale", 'delay'=>.5, 'duration'=>.8, 'order'=>"series" );
		
		$chart[ 'chart_type' ] = "Line";
		$chart[ 'chart_value' ] = array ( 'position'=>"cursor", 'size'=>12, 'color'=>"000000", 'background_color'=>"aaff00", 'alpha'=>80 );
		
		//$chart[ 'draw' ] = array ( array ( 'transition'=>"dissolve", 'delay'=>0, 'duration'=>.5, 'type'=>"text", 'color'=>"000000", 'alpha'=>8, 'font'=>"Arial", 'rotation'=>0, 'bold'=>true, 'size'=>48, 'x'=>8, 'y'=>7, 'width'=>400, 'height'=>75, 'text'=>"annual report", 'h_align'=>"center", 'v_align'=>"bottom" ) );
		$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"FFFFFF", 'alpha'=>15, 'size'=>20, 'x'=>50, 'y'=>-10, 'width'=>500, 'height'=>50, 'text'=>"$title", 'h_align'=>"center", 'v_align'=>"middle" )) ;
		
		$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'bullet'=>"line", 'font'=>"arial", 'bold'=>true, 'size'=>13, 'color'=>"ffffff", 'alpha'=>65 ); 
		$chart[ 'legend_rect' ] = array ( 'x'=>50, 'y'=>35, 'width'=>600, 'height'=>5, 'margin'=>5, 'fill_color'=>"000000", 'fill_alpha'=>7, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );  
		$chart[ 'legend_transition' ] = array ( 'type'=>"dissolve", 'delay'=>0, 'duration'=>.5 );
		
		$arr_color = create_arr_color ();
		$chart[ 'series_color' ] = $arr_color;
		
		$chart[ 'series_explode' ] = array ( 400 );
		
		
	} else {
		
		$chart[ 'axis_category' ] = array ( 'size'=>10, 'color'=>"FFFFFF", 'alpha'=>75 ); 
		$chart[ 'axis_ticks' ] = array ( 'value_ticks'=>true, 'category_ticks'=>true, 'minor_count'=>1 );
		$chart[ 'axis_value' ] = array ( 'size'=>10, 'color'=>"FFFFFF", 'alpha'=>75 );
		
		$chart[ 'chart_border' ] = array ( 'top_thickness'=>0, 'bottom_thickness'=>2, 'left_thickness'=>2, 'right_thickness'=>0 );
		
		$chart[ 'chart_grid_h' ] = array ( 'thickness'=>1, 'type'=>"solid" );
		$chart[ 'chart_grid_v' ] = array ( 'thickness'=>1, 'type'=>"solid" );
		$chart[ 'chart_rect' ] = array ( 'x'=>50, 'y'=>70, 'width'=>600, 'height'=>300, 'positive_color'=>"ffffff", 'positive_alpha'=>50, 'negative_color'=>"000000", 'negative_alpha'=>10 );
		$chart[ 'chart_pref' ] = array ( 'rotation_x'=>25, 'rotation_y'=>2 ); 
		$chart[ 'chart_type' ] = "stacked 3d column" ;
		$chart[ 'chart_value' ] = array ( 'color'=>"ffffcc", 'background_color'=>"444488", 'alpha'=>100, 'size'=>12, 'position'=>"cursor" );
		$chart[ 'chart_transition' ] = array ( 'type'=>"scale", 'delay'=>.5, 'duration'=>.8, 'order'=>"series");
		
		$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"FFFFFF", 'alpha'=>15, 'size'=>20, 'x'=>50, 'y'=>-10, 'width'=>500, 'height'=>50, 'text'=>"$title", 'h_align'=>"center", 'v_align'=>"middle" )) ;
		
		$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'font'=>"arial", 'bold'=>true, 'size'=>12, 'color'=>"000000", 'alpha'=>50 ); 
		$chart[ 'legend_rect' ] = array ( 'x'=>50, 'y'=>35, 'width'=>600, 'height'=>5, 'margin'=>5, 'fill_color'=>"000000", 'fill_alpha'=>7, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );  
		
		$chart[ 'series_color' ] = array ("ff6600", "88ff00", "8866ff" ); 
		$chart[ 'series_gap' ] = array ( 'bar_gap'=>0, 'set_gap'=>20) ; 
		
		
	}
	
	SendChartData ( $chart );
	
} else {
	
	// Setup the graph
	$graph = new Graph(750,450);
	$graph->SetMargin(40,40,45,90); //droit,gauche,haut,bas
	$graph->SetMarginColor('white');
	$graph->SetScale("textlin");
	$graph->yaxis->scale->SetGrace(3);
	
	// Hide the frame around the graph
	$graph->SetFrame(false);
	
	// Setup title
	$graph->title->Set("Graphic");
	//$graph->title->SetFont(FF_VERDANA,FS_BOLD,14);
	
	// Note: requires jpgraph 1.12p or higher
	$graph->SetBackgroundGradient('#FFFFFF','#CDDEFF:0.8',GRAD_HOR,BGRAD_PLOT);
	$graph->tabtitle->Set($table_subtitle[$min_call]);
	$graph->tabtitle->SetWidth(TABTITLE_WIDTHFULL);
	
	// Enable X and Y Grid
	$graph->xgrid->Show();
	$graph->xgrid->SetColor('gray@0.5');
	$graph->ygrid->SetColor('gray@0.5');
	
	$graph->yaxis->HideZeroLabel();
	$graph->xaxis->HideZeroLabel();
	$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#CDDEFF@0.5');
	
	
	//$graph->xaxis->SetTickLabels($array_hours[0]);
	
	// initialisaton fixe de AXE X
	$graph->xaxis->SetTickLabels($array_hours[0]);  
	
	
	// Setup X-scale
	//$graph->xaxis->SetTickLabels($array_hours[0]);
	$graph->xaxis->SetLabelAngle(90);
	
	// Format the legend box
	$graph->legend->SetColor('navy');
	$graph->legend->SetFillColor('gray@0.8');
	$graph->legend->SetLineWeight(1);
	//$graph->legend->SetFont(FF_ARIAL,FS_BOLD,8);
	$graph->legend->SetShadow('gray@0.4',3);
	$graph->legend->SetAbsPos(15,130,'right','bottom');
	
	
	
	for ($indgraph=0;$indgraph<=$nbday;$indgraph++){
		
		$bplot[$indgraph] = new BarPlot($tableau_value[$indgraph]);
		
		$bplot[$indgraph]->SetColor($table_colors[$indgraph]);
		$bplot[$indgraph]->SetWeight(2);
		$bplot[$indgraph]->SetFillColor('orange');
		$bplot[$indgraph]->SetShadow();
		$bplot[$indgraph]->value->Show();
		
		$bplot[$indgraph]->SetLegend($legend[$indgraph]);
	
		$graph->Add($bplot[$indgraph]);
		
	}
	
	// Output the graph
	$graph->Stroke();

}

?>
