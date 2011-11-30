<?php
include_once(dirname(__FILE__) . "/charts.php");


$chart[ 'axis_category' ] = array (  'size'=>16, 'color'=>"000000", 'alpha'=>75, 'skip'=>0 ,'orientation'=>"horizontal" ); 
$chart[ 'axis_ticks' ] = array ( 'value_ticks'=>false, 'category_ticks'=>true, 'major_thickness'=>2, 'minor_thickness'=>1, 'minor_count'=>1, 'major_color'=>"000000", 'minor_color'=>"222222" ,'position'=>"inside" );
//$chart[ 'axis_value' ] = array ( 'min'=>-40, 'size'=>10, 'color'=>"ffffff", 'alpha'=>50, 'steps'=>6, 'prefix'=>"", 'suffix'=>"", 'decimals'=>0, 'separator'=>"", 'show_min'=>false );
		
$chart[ 'chart_data' ] = array ( array ( "", "2004", "2005", "2006", "2007", "2008", "2009" ), array ( "region 1", 48, 55, 80, 100, 80, 100 ), array ( "region 2", -12, 10, 55, 65, 55, 65 ), array ( "region 3", 27, -20, 15, 80, 80, 150), array ( "region 4", 27, 250, 35, 80, 50, 150), array ( "region 5", 7, -2, 5, 0, 86, 100) );

$chart[ 'chart_grid_h' ] = array ( 'alpha'=>10, 'color'=>"000000", 'thickness'=>1 );
$chart[ 'chart_pref' ] = array ( 'line_thickness'=>2, 'point_shape'=>"circle", 'fill_shape'=>false );
//$chart[ 'chart_rect' ] = array ( 'x'=>50, 'y'=>100, 'width'=>320, 'height'=>150, 'positive_color'=>"ffffff", 'positive_alpha'=>50, 'negative_color'=>"000000", 'negative_alpha'=>10 );
$chart[ 'chart_rect' ] = array ( 'x'=>50, 'y'=>70, 'width'=>600, 'height'=>300, 'positive_color'=>"ffffff", 'positive_alpha'=>50, 'negative_color'=>"000000", 'negative_alpha'=>10 );

$chart[ 'chart_transition' ] = array ( 'type'=>"slide_left", 'delay'=>.5, 'duration'=>.5, 'order'=>"series" );
$chart[ 'chart_type' ] = "Line";
$chart[ 'chart_value' ] = array ( 'position'=>"cursor", 'size'=>12, 'color'=>"000000", 'background_color'=>"aaff00", 'alpha'=>80 );

//$chart[ 'draw' ] = array ( array ( 'transition'=>"dissolve", 'delay'=>0, 'duration'=>.5, 'type'=>"text", 'color'=>"000000", 'alpha'=>8, 'font'=>"Arial", 'rotation'=>0, 'bold'=>true, 'size'=>48, 'x'=>8, 'y'=>7, 'width'=>400, 'height'=>75, 'text'=>"annual report", 'h_align'=>"center", 'v_align'=>"bottom" ) );
$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"FFFFFF", 'alpha'=>15, 'size'=>20, 'x'=>50, 'y'=>-10, 'width'=>500, 'height'=>50, 'text'=>"This is my graph test", 'h_align'=>"center", 'v_align'=>"middle" )) ;

$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'bullet'=>"line", 'font'=>"arial", 'bold'=>true, 'size'=>13, 'color'=>"ffffff", 'alpha'=>65 ); 
$chart[ 'legend_rect' ] = array ( 'x'=>50, 'y'=>35, 'width'=>600, 'height'=>5, 'margin'=>5, 'fill_color'=>"000000", 'fill_alpha'=>7, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );  
$chart[ 'legend_transition' ] = array ( 'type'=>"dissolve", 'delay'=>0, 'duration'=>.5 );

$chart[ 'series_color' ] = array ( "ff4444", "ffff00", "8844ff" ); 
$chart [ 'series_explode' ] = array ( 400 );

SendChartData ( $chart );
?>
