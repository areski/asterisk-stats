<?php
include_once ("../lib/defines.php");
include ("../lib/module.access.php");
include ("../lib/smarty.php");

// #### HEADER SECTION
$smarty->display('main.tpl');
?>
<br/><br/><br/><br/>


<table align="center" width="90%" bgcolor="white" cellpadding="5" cellspacing="5" style="border-bottom: medium dotted #AA0000">
	<tr>
		<td width="10%"><img src="../images/owl.jpg"  border="1"></td>
		<td align="right"> <?php  echo COPYRIGHT; ?></a>
		</td>
	</tr>
</table>

<br/><br/><br/>


<?php
// #### FOOTER SECTION
$smarty->display('footer.tpl');

?>
