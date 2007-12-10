<?
######################################################################
# Simple Pocket IE emulator for PDAview4phpRS
######################################################################
// phpRS
// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/

// PDAview4phpRS and Simple Pocket IE emulator
// Copyright (c) 2003 by Jaroslav Mallat (jaroslav@mallat.cz)
// http://hps.mallat.cz/

// This program is free software. - Toto je bezplatny a svobodny software.

include("../../config.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Simple Pocket IE emulator</title>
</head>
<form name="emuPIE">
<input type="hidden" name="pie" value="a">
</form>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
    <td width="20" rowspan="3" bgcolor="#000000">&nbsp;</td>
    <td height="40" valign="middle" bgcolor="#000000"><font face="System,Chicago,sans-serif" size="1" color="#FFFFFF">Simple Pocket IE emulator</font></td>
    <td width="20" rowspan="3" bgcolor="#000000">&nbsp;</td>
</tr><tr bgcolor="#000000"><td>
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY><TR>
	<TD><iframe src="pietop.php" name="top" width="241" height="28" marginwidth="0" marginheight="0" scrolling="no" frameborder="0"></iframe></TD>
	</TR><TR>
  	<TD><iframe src="<?echo $baseadr?>" name="text" width="241" height="268" frameborder="0">Sorry, Vas prohlizec nepodporuje IFRAME</iframe></TD>
	</TR><TR>
	<TD><iframe src="piebot.php" name="bot" width="241" height="26" marginwidth="0" marginheight="0" scrolling="no" frameborder="0"></iframe></TD>
	</TR></TBODY></TABLE></td></tr><tr>
    <td height="40" align="right" valign="middle" bgcolor="#000000"><font face="System,Chicago,sans-serif" size="1" color="#FFFFFF">&copy;HPS team</font></td>
</tr></table>


</body>
</html>
