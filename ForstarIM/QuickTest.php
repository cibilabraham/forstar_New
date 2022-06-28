
<html>
<head><TITLE></TITLE>
<script language=JavaScript src="libjs/milonic_src.js" type=text/javascript></script>
<script language=JavaScript>
if(ns4)_d.write("<scr"+"ipt language=JavaScript src=libjs/mmenuns4.js></scr"+"ipt>");
else _d.write("<scr"+"ipt language=JavaScript src=libjs/mmenudom.js></scr"+"ipt>");
</script>
<script type="text/javascript" src="libjs/listmenus.js"></script>
<script>
fixMozillaZIndex=true; //Fixes Z-Index problem  with Mozilla browsers but causes odd scrolling problem, toggle to see if it helps
_menuCloseDelay=500;
_menuOpenDelay=150;
_subOffsetTop=2;
_subOffsetLeft=-2;

with(menuStyle1=new mm_style()){
bordercolor="#FACD7A";
borderstyle="solid";
borderwidth=1;
fontfamily="Verdana, Arial, Helvetica, sans-serif";
fontsize="11px";
fontstyle="normal";
headerbgcolor="#ffffff";
headercolor="#000000";
offbgcolor="#FFFEDF";
offcolor="#000000";
onbgcolor="#FFFFFF";

oncolor="black";
padding=5;
pagebgcolor="#FDEEB3";
pagecolor="black";

separatorcolor="#FACD7A";
separatorsize=1;
subimage="images/arrow.gif";
subimagepadding=2;
}


with(mainMenuStyle1=new mm_style()){
borderstyle="solid";
fontfamily="Arial, Helvetica, sans-serif";
fontsize="12px";
fontstyle="normal";
fontweight="normal";
headerbgcolor="#ffffff";
headercolor="#000000";
offbgcolor="transparent";
offcolor="#05315C";
onbgcolor="#FFFEDF";
oncolor="black";
padding=5;
pagebgcolor="#FDEEB3";
pagecolor="black";

separatorcolor="#AAAAAA";
separatorsize=1;
subimage="images/arrow.gif";
subimagepadding=2;
}
</script>
</head>
<body>

<table align="left" width="100%" cellspacing="0" cellpadding="0"  border="0">
		<tr>
		<td align="right" width="100%">
		<script>
			with(milonic=new menuname("Quick Link")){
				overflow="scroll";
				style=menuStyle1;
				aI("text=Shobu;url=shobu.html");
			}
		</script>

		<script>
		with(milonic=new menuname("Main Menu")) {
			style=mainMenuStyle1;
			top=10;
			itemwidth="100";
			alwaysvisible=1;
			orientation="horizontal";
			margin=2;
			position="relative";
			aI("text=Quick Link;showmenu=Quick Link;");			
		}		
		drawMenus();
		</script>
		</td></tr>
		<tr><TD>sdfdsf</TD></tr>
	</table>
</body>
</html>


