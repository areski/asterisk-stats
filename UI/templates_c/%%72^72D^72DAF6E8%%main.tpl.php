<?php /* Smarty version 2.6.18, created on 2007-11-12 01:22:23
         compiled from main.tpl */ ?>
<HTML>
<HEAD>
	<link rel="shortcut icon" href="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/favicon.ico">
	<link rel="icon" href="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/animated_favicon1.gif" type="image/gif">
	<title>..:: <?php echo $this->_tpl_vars['CCMAINTITLE']; ?>
 ::..</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/css/main.css" rel="stylesheet" type="text/css">
	<link href="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/css/menu.css" rel="stylesheet" type="text/css">
	<link href="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/css/style-def.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="./javascript/jquery/jquery.js"></script>
	<script type="text/javascript" src="./javascript/jquery/jquery.debug.js"></script>
	<script type="text/javascript" src="./javascript/jquery/ilogger.js"></script>
	<script type="text/javascript" src="./javascript/jquery/handler_jquery.js"></script>
	
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<p class="version" align="right"><?php echo $this->_tpl_vars['WEBUI_VERSION']; ?>
 - <?php echo $this->_tpl_vars['WEBUI_DATE']; ?>
<br><br><br>Logged-in as: <b><?php echo $this->_tpl_vars['adminname']; ?>
</b></p>
<br>

<DIV border="0" width="1000px">
<?php if (( $this->_tpl_vars['popupwindow'] == 0 )): ?>
<div class="divleft">


<div id="nav_before"></div>
<ul id="nav">

	<?php if (( $this->_tpl_vars['ACX_ACCESS'] > 0 )): ?>
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img7" 
	<?php if (( $this->_tpl_vars['section'] == '10' )): ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/minus.gif"
	<?php else: ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/plus.gif"
	<?php endif; ?> onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong><?php  echo gettext("ADMINISTRATOR"); ?></strong></a></li>
		<div class="tohide"
	<?php if (( $this->_tpl_vars['section'] == '10' )): ?>
		style="">
	<?php else: ?>
		style="display:none;">
	<?php endif; ?>
		<ul>
			<li><ul>
				<li><a href="call-log.php"><?php  echo gettext("CDR Report"); ?></a></li>
					<li><a href="call-comp.php"><?php  echo gettext("Calls Compare"); ?></a></li>
					<li><a href="call-last-month.php"><?php  echo gettext("onthly Traffic"); ?>M</a></li>
					<li><a href="call-daily-load.php"><?php  echo gettext("Daily Load"); ?></a></li>
					<li><a href="box-usage.php"><?php  echo gettext("Box Usage"); ?></a></li>
					<li><a href="userfield-monthly.php"><?php echo '<?php'; ?>
 echo USERFIELD <?php echo '?>'; ?>
 <?php  echo gettext("Report"); ?> </a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img7" 
	<?php if (( $this->_tpl_vars['section'] == '10' )): ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/minus.gif"
	<?php else: ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/plus.gif"
	<?php endif; ?> onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong><?php  echo gettext("MISC"); ?></strong></a></li>
		<div class="tohide"
	<?php if (( $this->_tpl_vars['section'] == '10' )): ?>
		style="">
	<?php else: ?>
		style="display:none;">
	<?php endif; ?>
		<ul>
			<li><ul>
				<li><a href="A2B_entity_user.php?atmenu=user&groupID=0&stitle=Administrator+management&section=10"><?php  echo gettext("Show Administrator"); ?></a></li>
				<li><a href="A2B_entity_user.php?form_action=ask-add&atmenu=user&groupID=0&stitle=Administrator+management&section=10"><?php  echo gettext("Add Administrator"); ?></a></li>
				
				<li><a href="A2B_logfile.php?section=10"><?php  echo gettext("Watch Log files"); ?></a></li>
				<li><a href="A2B_entity_log_viewer.php?section=10"><?php  echo gettext("System Log"); ?></a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	
	<div class="toggle_menu">
	<li><a href="javascript:;" class="toggle_menu" target="_self"><img id="img8" 
	<?php if (( $this->_tpl_vars['section'] == '11' )): ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/minus.gif"
	<?php else: ?>
	src="templates/<?php echo $this->_tpl_vars['SKIN_NAME']; ?>
/images/plus.gif"
	<?php endif; ?> onmouseover="this.style.cursor='hand';" WIDTH="9" HEIGHT="9">&nbsp; <strong><?php  echo gettext("FILE MANAGER"); ?></strong></a></li>
		<div class="tohide"
	<?php if (( $this->_tpl_vars['section'] == '11' )): ?>
		style="">
	<?php else: ?>
		style="display:none;">
	<?php endif; ?>
		<ul>
			<li><ul>
				<li><a href="CC_upload.php?section=11"><?php  echo gettext("Standard File"); ?></a></li>
			</ul></li>
		</ul>
	</div>
	</div>
	
	
	<?php endif; ?>

	<li><a href="#" target="_self"></a></a></li>
	<ul>
		<li><ul>
		<li><a href="logout.php?logout=true" target="_top"><font color="#DD0000"><b>&nbsp;&nbsp;<?php  echo gettext("LOGOUT"); ?></b></font></a></li>
		</ul></li>
	</ul>

</ul>
<div id="nav_after"></div>

</div>

<div class="divright">

<?php else: ?>
<div>
<?php endif; ?>