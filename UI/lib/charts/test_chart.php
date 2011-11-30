<HTML>
<BODY bgcolor="#FFFFFF">

<?php
include "charts.php";
// InsertChart( $flash_file, $library_path, $php_source, $width=400, $height=250, $bg_color="666666", $transparent=false, $license=null )
echo InsertChart ( "charts.swf", "charts_library", "test_chart_xml2.php?chart_type=bar&user_id=658",700, 400 );
?>

</BODY>
</HTML>
