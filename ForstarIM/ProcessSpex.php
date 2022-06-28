<?php
	require("include/include.php");
	$err			= "";
	$errDel			= "";	
		
	$editMode		= true;
	$addMode		= false;	

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProcessSpex.js"; 
	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<style>
 #container-1 ul li
{
  list-style:none;
}
</style>
<form name="frmPackingSpex" action="PackingSpex.php" method="post">
<script type="text/javascript">
            $(function() {
                $('#container-1').tabs({remote: true});
            });
 </script>
<table cellspacing="0"  align="center" cellpadding="0" width="100%">
	<tr bgcolor="White">
		<TD  style="padding-left:10px;">			
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<TR>
					<TD align="left" width="10%" class="pageName" nowrap>Process Spex</TD>
					<TD width="90%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="10"></TD></tr>
	<tr>
	<TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="ProcessSpexLoad.php?tab=1"><span>Manage Net Weight Slab</span></a></li>
			<li><a href="ProcessSpexLoad.php?tab=2"><span>Process Master</span></a></li>
			<li><a href="ProcessSpexLoad.php?tab=3"><span>Process Net Weight Value</span></a></li>
		</ul>
		</div>
	</TD>
	</tr>
			
  </table>
</form>

<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>