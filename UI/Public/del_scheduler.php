<?php 
include_once(dirname(__FILE__) . "/../lib/defines.php");
include_once(dirname(__FILE__) . "/../lib/Class.Table.php");

session_start();

getpost_ifset(array('id'));


// this variable specifie the debug type (0 => nothing, 1 => sql result, 2 => boucle checking, 3 other value checking)
$FG_DEBUG = 0;

// The variable FG_TABLE_NAME define the table name to use
$FG_TABLE_NAME= SCHEDULER_TABLENAME;
$FG_TABLE_CLAUSE="id = '$id'";

$DBHandle  = DbConnect();
		
$scheduler = new Table($DBHandle, $FG_TABLE_NAME, null);
if ($scheduler -> Delete_table($DBHandle, $FG_TABLE_CLAUSE, $FG_TABLE_NAME)){
	header("Location: list_scheduler.php");
	exit();
}

?>
