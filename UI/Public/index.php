<?php
include ("../lib/defines.php");
include ("../lib/smarty.php");

$smarty->assign("error", $_GET["error"]);

$smarty->display('index.tpl');


$_SESSION["menu_section"] = 1;
?>
