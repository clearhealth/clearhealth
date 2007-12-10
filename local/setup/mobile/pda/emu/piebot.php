<?
######################################################################
# Simple Pocket IE emulator for PDAview4phpRS
######################################################################
// phpRS
// Copyright (c) 2001-2003 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/

// PDAview4phpRS and Simple Pocket IE emulator
// Copyright (c) 2003 by Jaroslav Mallat (mallat@seznam.cz)
// http://hps.euweb.cz/

// This program is free software. - Toto je bezplatny a svobodny software.

include("../../config.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Untitled</title>
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><img src="pie1.gif" width="69" height="26" alt="" border="0"></td>
<td><a href="javascript:parent.frames[2].history.back()"><img src="pie2.gif" width="19" height="26" alt="Back" border="0" title="Back"></a></td>
<td><a href="javascript:parent.frames[2].history.forward()"><img src="pie2a.gif" width="19" height="26" alt="Forward" border="0" title="Forward"></a></td>
<td><a href="javascript:parent.frames[2].history.go(0)"><img src="pie3.gif" width="24" height="26" alt="Reload" border="0" title="Reload"></td>
<td><a href="<?echo $baseadr?>pda/" target="text"><img src="pie4.gif" alt="Home" width="21" height="26" border="0" title="Home"></a></td>
<td><img src="pie5.gif" width="94" height="26" alt="" border="0"></td>
</tr>
</table>



</body>
</html>
