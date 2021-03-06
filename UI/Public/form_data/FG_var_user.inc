<?php

getpost_ifset(array('id', 'groupID', 'userid', 'login', 'password', 'perms', 'name', 'direction', 'zipcode', 'state', 'phone', 'fax', 'popup_select', 'popup_formname', 'popup_fieldname'));


if ($groupID=="0"){
	$FG_INSTANCE_NAME="Administrator";
}elseif ($groupID=="1"){
	$FG_INSTANCE_NAME="Sub-administrator";
}elseif ($groupID=="2"){
	$FG_INSTANCE_NAME="Resellers-User";
}else{
	$FG_INSTANCE_NAME="Reseller";
}

$HD_Form = new FormHandler("cc_ui_authen",$FG_INSTANCE_NAME);


$HD_Form -> FG_DEBUG = 0;
$HD_Form -> FG_TABLE_ID = " userid";
$HD_Form -> FG_TABLE_DEFAULT_ORDER = " datecreation";
$HD_Form -> FG_TABLE_DEFAULT_SENS = "ASC";

if (is_numeric($groupID)){
	$FG_TABLE_CLAUSE = "groupid='$groupID'";
}else{
	$FG_TABLE_CLAUSE = "groupid='1'";
}
		
$HD_Form -> FG_TABLE_CLAUSE = $FG_TABLE_CLAUSE;

$right_list = array();
// 1 2 4 8 16 32 64 128 256 512
$right_list["1"] = array( gettext("CUSTOMERS"), "1");
$right_list["2"] = array( gettext("BILLING"), "2");
$right_list["3"] = array( gettext("RATECARD"), "4");
$right_list["4"] = array( gettext("TRUNK"), "8");
$right_list["5"] = array( gettext("CALL REPORT"), "16");
$right_list["6"] = array( gettext("CRONT SERVICE"), "32");
$right_list["7"] = array( gettext("ADMINISTRATOR"), "64");
$right_list["8"] = array( gettext("FILE MANAGER"), "128");
$right_list["9"] = array( gettext("MISC"), "256");
$right_list["10"] = array( gettext("DID"), "512");
$right_list["11"] = array( gettext("CALL BACK"), "1024");
$right_list["12"] = array( gettext("OUTBOUND CID"), "2048");
$right_list["13"] = array( gettext("PACKAGE OFFER"), "4096");
//$right_list["14"] = array( gettext("PREDICTIVE DIALER"), "8192");
$right_list["14"] = array( gettext("INVOICES"), "16384");
$len_right_list = count($right_list);


$HD_Form -> AddViewElement(gettext("USERID"), "UserID", "14%", "center", "sort");
$HD_Form -> AddViewElement(gettext("DATE CREATION"), "datecreation", "24%", "center", "sort", "19", "", "", "", "", "", "display_dateformat");
$HD_Form -> AddViewElement(gettext("LOGIN"), "login", "24%", "center", "sort");
$HD_Form -> AddViewElement(gettext("PASSWORD"), "password", "24%", "center", "sort");

$HD_Form -> FieldViewElement ('userid, datecreation, login, password');

$HD_Form -> CV_NO_FIELDS  = gettext("NO")." ".strtoupper($HD_Form->FG_INSTANCE_NAME)." ".gettext("HAVE BEEN CREATED!"); 
$HD_Form -> CV_DISPLAY_LINE_TITLE_ABOVE_TABLE = false;
$HD_Form -> CV_TEXT_TITLE_ABOVE_TABLE = '';
$HD_Form -> CV_DISPLAY_FILTER_ABOVE_TABLE = false;

if($popup_select == "")
{
	$HD_Form -> FG_EDITION = true;
	$HD_Form -> FG_DELETION = true;
}


if ($form_action!="ask-add" && $form_action!="add"){
	$HD_Form -> AddEditElement (gettext("USERID"),
		"userid",
		'$value',
		"INPUT",
		"size=8 READONLY maxlength=6",
		"4",
		gettext("Insert the userid"),
		"" , "", "", "", "", "", "", "" );
}

$HD_Form -> AddEditElement (gettext("LOGIN"),
	"login",
	'$value',
	"INPUT",
	"size=30 maxlength=12",
	"3",
	gettext("Insert the username"),
	"" , "", "", "", "" , "", "", "");

$HD_Form -> AddEditElement (gettext("PASSWORD"),
	"password",
	'$value',
	"INPUT",
	"size=30 maxlength=15",
	"3",
	gettext("Insert the Reseller password"),
	"" , "", "", "", "", "", "", "");

if ($groupID!="0"){
	$HD_Form -> AddEditElement (gettext("PERMISSION"),
		"perms",
		'$value',
		"SELECT",
		"size=\"$len_right_list\" multiple=\"multiple\"",
		"", "",
		"list", "", "description, id", "", $right_list, "%1" , "", 
		gettext("To select several rights in the above ListBox, hold the CTRL key down while clicking the items") );
}
	

$HD_Form -> AddEditElement (gettext("NAME"),
	"name",
	'$value',
	"INPUT",
	"size=60 maxlength=50",
	"",
	gettext("Insert the name"),
	"" , "", "", "", "", "", "", "");
		
$HD_Form -> AddEditElement (gettext("ADDRESS"),
	"direction",
	'$value',
	"INPUT",
	"size=60 maxlength=250",
	"",
	gettext("Insert the direction"),
	"" , "", "", "", "", "", "", "");
	
$HD_Form -> AddEditElement (gettext("ZIPCODE"),
	"zipcode",
	'$value',
	"INPUT",
	"size=10 maxlength=7",
	"",
	gettext("Insert the zip code"),
	"" , "", "", "", "", "", "", "");

$HD_Form -> AddEditElement (gettext("STATE"),
	"state",
	'$value',
	"INPUT",
	"size=30 maxlength=30",
	"",
	gettext("Insert the state"),
	"" , "", "", "", "" , "", "", "");

$HD_Form -> AddEditElement (gettext("PHONE"),
	"phone",
	'$value',
	"INPUT",
	"size=30 maxlength=20",
	"",
	gettext("Insert the telephone number"),
	"" , "", "", "", "" , "", "", "");
	
$HD_Form -> AddEditElement (gettext("FAX"),
	"fax",
	'$value',
	"INPUT",
	"size=30 maxlength=20",
	"",
	gettext("Insert the fax number"),
	"" , "", "", "", "" , "", "", "");
	

if (($groupID=="2") || ($groupID=="3")){
	if ($groupID=="2"){
		$HD_Form -> AddEditElement ("UserID",
			"a",
			'$value',
			"SELECT",
			"","", "Insert the iva",
			"sql" ,
			"Customer",
			"AnaCust, IDCust",
			"", "", "%2 (%1)", "", "");
	}else{
		$HD_Form -> AddEditElement ("ResellerID",
			"a",
			'$value',
			"SELECT",
			"","", "Insert the iva",
			"sql" ,
			"secauth",
			"login, userID",
			"groupID='1'", "", "%2 (%1)", "", "" );
	}
}	
																											
if ($form_action!="ask-add" && $form_action!="add"){
        $FG_QUERY_EDITION='userid, ';
}
	
$FG_QUERY_EDITION .= 'login, password, ';

if ($groupID!="0"){
	$FG_QUERY_EDITION .= 'perms, ';
}

$FG_QUERY_EDITION .= 'name, direction, zipcode, state, phone, fax';

$HD_Form -> FieldEditElement ($FG_QUERY_EDITION);

$HD_Form -> FG_EDITION_CLAUSE = " userid='%id'";

if (DB_TYPE == "postgres"){
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS = "";
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE  = "";	
}else{
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS = "datecreation, ";
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE  = "now(), ";	
}

if (is_numeric($groupID)){
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS .= "groupID";
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE  .= "$groupID";

	$HD_Form -> FG_QUERY_EDITION_HIDDEN_FIELDS  = "groupID";
	$HD_Form -> FG_QUERY_EDITION_HIDDEN_VALUE   = "$groupID";
	
	if ($groupID=="0"){
		$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS .= ", perms";
		$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE  .= ", 1023";

		$HD_Form -> FG_QUERY_EDITION_HIDDEN_FIELDS .= ", perms";
		$HD_Form -> FG_QUERY_EDITION_HIDDEN_VALUE  .= ", 1023";
	}
}else{
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_FIELDS .= "groupID";
	$HD_Form -> FG_QUERY_ADITION_HIDDEN_VALUE  .= "1";
}

if (($popup_select>=1)){
	$HD_Form -> FG_OTHER_BUTTON1 = true;
	$HD_Form -> FG_OTHER_BUTTON1_ALT = '<font color="red">&lt;select&gt;</font>';
	$HD_Form -> FG_OTHER_BUTTON1_IMG = '';
	
//echo $popup_select;
if ($popup_select==1){
	$HD_Form -> FG_OTHER_BUTTON1_LINK = "javascript:sendValue('|param|');";	
}
}
						 
$HD_Form -> FG_INTRO_TEXT_EDITION= gettext("Modify the properties of the")." ".$HD_Form->FG_INSTANCE_NAME;
$HD_Form -> FG_INTRO_TEXT_ASK_DELETION = gettext("If you really want remove this")." ".$HD_Form->FG_INSTANCE_NAME.", ".gettext("click on the delete button.");
$HD_Form -> FG_INTRO_TEXT_ADD = gettext("you can add easily a new")." ".$HD_Form->FG_INSTANCE_NAME.".<br>".gettext("Fill the following fields and confirm by clicking on the button add.");





$HD_Form -> FG_INTRO_TEXT_ADITION = '';
$HD_Form -> FG_TEXT_ADITION_CONFIRMATION = gettext("Your new")." ".$HD_Form->FG_INSTANCE_NAME." ".gettext("has been inserted.")."<br>";


$HD_Form -> FG_BUTTON_EDITION_SRC = $HD_Form -> FG_BUTTON_ADITION_SRC  = Images_Path . "/cormfirmboton.gif";
$HD_Form -> FG_BUTTON_EDITION_BOTTOM_TEXT = $HD_Form -> FG_BUTTON_ADITION_BOTTOM_TEXT = gettext("Click 'Confirm Data' to continue");


$HD_Form -> FG_GO_LINK_AFTER_ACTION = $_SERVER['PHP_SELF']."?atmenu=user&groupID=$groupID&stitle=Administrator+management&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_ADD = $_SERVER['PHP_SELF']."?atmenu=user&groupID=$groupID&stitle=Administrator+management&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_EDIT = $_SERVER['PHP_SELF']."?atmenu=user&groupID=$groupID&stitle=Administrator+management&id=";
$HD_Form -> FG_GO_LINK_AFTER_ACTION_DELETE = $_SERVER['PHP_SELF']."?atmenu=user&groupID=$groupID&stitle=Administrator+management&id=";
?>
