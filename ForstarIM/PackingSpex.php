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
	$ON_LOAD_PRINT_JS	= "libjs/PackingSpex.js"; 
	

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
					<TD align="left" width="10%" class="pageName" nowrap>Packing Spex</TD>
					<TD width="90%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="10"></TD></tr>
<!-- 
PackingCostMaster.php	Packing Cost Master
PackingMatrix.php	Packing Matrix

PackingCostMaster.php, PackingMatrix.php
 -->
	<tr>
	<TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="PackingSpexLoad.php?tab=1"><span>Packing Cost Master</span></a></li>
			<li><a href="PackingSpexLoad.php?tab=2"><span>Packing Material</span></a></li>
			<li><a href="PackingSpexLoad.php?tab=3"><span>Packing Matrix</span></a></li>
		</ul>
		</div>
	</TD>
	</tr>
	
	<!--<tr>
		<TD>
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD>
					<script language="JavaScript">
						with(milonic=new menuname("sample1")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}
						with(milonic=new menuname("sample2")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}
												
						with(milonic=new menuname("Main Menu")) {
						alwaysvisible=1;
						menuwidth=160; // Default:400 up
						openstyle="tab";
						orientation="horizontal";
						screenposition="center";
						style=mStyle;							
						aI("align=center;keepalive=1;text=Packing Cost Master;url=javascript:openIFrame('tempIFrame','PackingCostMaster.php');showmenu=sample1;title=Packing Cost Master;");
						aI("align=center;keepalive=1;text=Packing Matrix;url=javascript:openIFrame('tempIFrame','PackingMatrix.php');showmenu=sample2;title=Packing Matrix;");
						}
						drawMenus();
					</script>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>-->	
	<!--<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Spex</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
	<tr><TD>
		<iframe width="100%" height="600" id="tempIFrame" src="" style="border:none;" frameborder="0"></iframe>
	</TD></tr>	
	</table>
  	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>	
		</td>
	</tr>-->		
  </table>
</form>

<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>