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
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Untitled</title>

<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
var dn;
c1 = new Image(); c1.src = "pie_c1.gif";
c2 = new Image(); c2.src = "pie_c2.gif";
c3 = new Image(); c3.src = "pie_c3.gif";
c4 = new Image(); c4.src = "pie_c4.gif";
c5 = new Image(); c5.src = "pie_c5.gif";
c6 = new Image(); c6.src = "pie_c6.gif";
c7 = new Image(); c7.src = "pie_c7.gif";
c8 = new Image(); c8.src = "pie_c8.gif";
c9 = new Image(); c9.src = "pie_c9.gif";
c0 = new Image(); c0.src = "pie_c0.gif";
cb = new Image(); cb.src = "pie_cb.gif";
cam = new Image(); cam.src = "pie_cam.gif";
cpm = new Image(); cpm.src = "pie_cpm.gif";
function extract(h,m,s,type) {
if (!document.images) return;
if (h <= 9) {
document.images.a.src = cb.src;
document.images.b.src = eval("c"+h+".src");
}
else {
document.images.a.src = eval("c"+Math.floor(h/10)+".src");
document.images.b.src = eval("c"+(h%10)+".src");
}
if (m <= 9) {
document.images.d.src = c0.src;
document.images.e.src = eval("c"+m+".src");
}
else {
document.images.d.src = eval("c"+Math.floor(m/10)+".src");
document.images.e.src = eval("c"+(m%10)+".src");
}
if (s <= 9) {
document.g.src = c0.src;
document.images.h.src = eval("c"+s+".src");
}
else {
document.images.g.src = eval("c"+Math.floor(s/10)+".src");
document.images.h.src = eval("c"+(s%10)+".src");
}
if (dn == "AM") document.j.src = cam.src;
else document.images.j.src = cpm.src;
}
function show3() {
if (!document.images)
return;
var Digital = new Date();
var hours = Digital.getHours();
var minutes = Digital.getMinutes();
var seconds = Digital.getSeconds();
dn = "AM";
if ((hours >= 12) && (minutes >= 1) || (hours >= 13)) {
dn = "PM";
hours = hours-12;
}
if (hours == 0)
hours = 12;
extract(hours, minutes, seconds, dn);
setTimeout("show3()", 1000);
}
//  End -->
</script>
</head>

<body bgcolor="#00249C" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" onLoad="show3()">

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><img src="pie_top.gif" alt="" width="168" height="28" border="0"></td>
<td>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><img src="pie_cb.gif" name=a></td>
<td><img src="pie_cb.gif" name=b></td>
<td><img src="pie_colon.gif" name=c></td>
<td><img src="pie_cb.gif" name=d></td>
<td><img src="pie_cb.gif" name=e></td>
<td><img src="pie_colon.gif" name=f></td>
<td><img src="pie_cb.gif" name=g></td>
<td><img src="pie_cb.gif" name=h></td>
<td><img src="pie_cam.gif" name=j></td>
</tr>
</table>


</td>
</tr>
</table>

</body>
</html>




