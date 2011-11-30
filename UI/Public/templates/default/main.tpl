<HTML>
<HEAD>
	<link rel="shortcut icon" href="templates/{$SKIN_NAME}/images/favicon.ico">
	<link rel="icon" href="templates/{$SKIN_NAME}/images/animated_favicon1.gif" type="image/gif">
	<title>..:: {$CCMAINTITLE} ::..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="templates/{$SKIN_NAME}/css/main.css" rel="stylesheet" type="text/css">
	<link href="templates/{$SKIN_NAME}/css/menu.css" rel="stylesheet" type="text/css">
	<link href="templates/{$SKIN_NAME}/css/style-def.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="./javascript/jquery/jquery.js"></script>
	<script type="text/javascript" src="./javascript/jquery/jquery.debug.js"></script>
	<script type="text/javascript" src="./javascript/jquery/ilogger.js"></script>
	<script type="text/javascript" src="./javascript/jquery/handler_jquery.js"></script>
	
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<p class="version" align="right">{$WEBUI_VERSION} - {$WEBUI_DATE}<br><br><br>Logged-in as: <b>{$adminname}</b></p>
<br>

<DIV border="0" width="1000px">
{if ($popupwindow == 0)}
<div class="divleft">


<div id="nav_before"></div>
<ul id="nav">

	{if ($ACX_ACCESS  > 0)}
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img7" 
	{if ($section =="10")}
	src="templates/{$SKIN_NAME}/images/minus.gif"
	{else}
	src="templates/{$SKIN_NAME}/images/plus.gif"
	{/if} onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong>{php} echo gettext("ADMINISTRATOR");{/php}</strong></a></li>
		<div class="tohide"
	{if ($section =="10")}
		style="">
	{else}
		style="display:none;">
	{/if}
		<ul>
			<li><ul>
				<li><a href="call-log.php">{php} echo gettext("CDR Report");{/php}</a></li>
					<li><a href="call-comp.php">{php} echo gettext("Calls Compare");{/php}</a></li>
					<li><a href="call-last-month.php">{php} echo gettext("onthly Traffic");{/php}M</a></li>
					<li><a href="call-daily-load.php">{php} echo gettext("Daily Load");{/php}</a></li>
					<li><a href="box-usage.php">{php} echo gettext("Box Usage");{/php}</a></li>
					<li><a href="userfield-monthly.php"><?php echo USERFIELD ?> {php} echo gettext("Report");{/php} </a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img7" 
	{if ($section =="10")}
	src="templates/{$SKIN_NAME}/images/minus.gif"
	{else}
	src="templates/{$SKIN_NAME}/images/plus.gif"
	{/if} onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong>{php} echo gettext("MISC");{/php}</strong></a></li>
		<div class="tohide"
	{if ($section =="10")}
		style="">
	{else}
		style="display:none;">
	{/if}
		<ul>
			<li><ul>
				<li><a href="A2B_entity_user.php?atmenu=user&groupID=0&stitle=Administrator+management&section=10">{php} echo gettext("Show Administrator");{/php}</a></li>
				<li><a href="A2B_entity_user.php?form_action=ask-add&atmenu=user&groupID=0&stitle=Administrator+management&section=10">{php} echo gettext("Add Administrator");{/php}</a></li>
				
				<li><a href="A2B_logfile.php?section=10">{php} echo gettext("Watch Log files");{/php}</a></li>
				<li><a href="A2B_entity_log_viewer.php?section=10">{php} echo gettext("System Log");{/php}</a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img8" 
	{if ($section == "11")}
	src="templates/{$SKIN_NAME}/images/minus.gif"
	{else}
	src="templates/{$SKIN_NAME}/images/plus.gif"
	{/if} onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong>{php} echo gettext("FILE MANAGER");{/php}</strong></a></li>
		<div class="tohide"
	{if ($section =="11")}
		style="">
	{else}
		style="display:none;">
	{/if}
		<ul>
			<li><ul>
				<li><a href="CC_upload.php?section=11">{php} echo gettext("Standard File");{/php}</a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	{/if}

	<li><a href="#" target="_self"></a></a></li>
	<ul>
		<li><ul>
		<li><a href="logout.php?logout=true" target="_top"><font color="#DD0000"><b>&nbsp;&nbsp;{php} echo gettext("LOGOUT");{/php}</b></font></a></li>
		</ul></li>
	</ul>

</ul>
<div id="nav_after"></div>

</div>

<div class="divright">

{else}
<div>
{/if}
