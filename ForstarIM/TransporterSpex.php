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
		//header("Location: ErrorPageIFrame.php");
		header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	$redirectUrl  = $g["url"];

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterSpex.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");	
?>
<style>
 #container-1 ul li
{
  list-style:none;
}
</style>
<form name="frmTransporterSpex" action="TransporterSpex.php" method="post">
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
					<TD align="left" width="10%" class="pageName" nowrap>Transporter Spex</TD>
					<TD width="90%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="10"></TD></tr>		
<!-- 
TransporterMaster.php		Transporter Data
ZoneMaster.php			Manage Zone
WeightSlabMaster.php		Manage Weight Slab
TransporterRateList.php		Transporter Rate List
TransporterOtherCharges.php	Transporter Other Charges
TransporterWeightSlab.php	Transporter Weight Slab
TransporterRateMaster.php	Transporter Rate Master
TransporterStatus.php		Transporter Management 
-->

<!-- TransporterMaster.php, ZoneMaster.php, WeightSlabMaster.php, TransporterRateList.php, TransporterOtherCharges.php, TransporterWeightSlab.php, TransporterRateMaster.php, TransporterStatus.php -->
<!-- TransporterMaster.php, ZoneMaster.php, WeightSlabMaster.php, TransporterRateList.php, TransporterOtherCharges.php, TransporterWeightSlab.php, TransporterRateMaster.php, TransporterStatus.php -->
	<tr>
	<TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="TransporterSpexLoad.php?tab=1"><span>Transporter Data</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=2"><span>Manage Zone</span></a></li>	
			<li><a href="TransporterSpexLoad.php?tab=3"><span>Manage Weight Slab</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=4"><span>Transporter Rate List</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=5"><span>Transporter Other Charges</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=6"><span>Transporter Weight Slab</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=7"><span>Transporter Rate Master</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=8"><span>Transporter Cost</span></a></li>
			<li><a href="TransporterSpexLoad.php?tab=9"><span>Transporter Management</span></a></li>
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
						with(milonic=new menuname("sample3")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}						
						with(milonic=new menuname("sample4")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}						
						with(milonic=new menuname("sample5")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}						
						with(milonic=new menuname("sample6")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}						
						with(milonic=new menuname("sample7")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}
						with(milonic=new menuname("sample8")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}					 
												
						with(milonic=new menuname("Main Menu")) {
						alwaysvisible=1;
						menuwidth=640; // Default:400/690 up/ /640 Default 
						openstyle="tab";
						orientation="horizontal";
						screenposition="center";
						style=mStyle;							
						aI("align=center;keepalive=1;text=Transporter Data;url=javascript:openIFrame('tempIFrame','TransporterMaster.php');showmenu=sample1;title=Transporter Data;");
						aI("align=center;keepalive=1;text=Manage Zone;url=javascript:openIFrame('tempIFrame','ZoneMaster.php');showmenu=sample2;title=Manage Zone;");
						aI("align=center;keepalive=1;text=Manage Weight Slab;url=javascript:openIFrame('tempIFrame','WeightSlabMaster.php');showmenu=sample3;title=Manage Weight Slab;");
						//aI("align=center;keepalive=1;text=Manage Area Demarcation;url=javascript:openIFrame('tempIFrame','AreaDemarcationMaster.php');showmenu=sample4;title=Manage Area Demarcation;");
						aI("align=center;keepalive=1;text=Transporter Rate List;url=javascript:openIFrame('tempIFrame','TransporterRateList.php');showmenu=sample5;title=Transporter Rate List;");
						aI("align=center;keepalive=1;text=Transporter Other Charges;url=javascript:openIFrame('tempIFrame','TransporterOtherCharges.php');showmenu=sample6;title=Transporter Other Charges;");
						aI("align=center;keepalive=1;text=Transporter Weight Slab;url=javascript:openIFrame('tempIFrame','TransporterWeightSlab.php');showmenu=sample7;title=Transporter Wise Weight Slab;");
						aI("align=center;keepalive=1;text=Transporter Rate Master;url=javascript:openIFrame('tempIFrame','TransporterRateMaster.php');showmenu=sample8;title=Transporter Rate Master;");	
						aI("align=center;keepalive=1;text=Transporter Management;url=javascript:openIFrame('tempIFrame','TransporterStatus.php');showmenu=sample9;title=Transporter Management;");
						
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
					<td bgcolor="white">
						
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Spex</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">		
										<tr>
										  <td colspan="2" nowrap height="5"></td>
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
		
		<!--<tr>
			<td height="10"></td>
		</tr>-->	
  </table>
<script language="JavaScript" type="text/javascript">		
		function openDefaultFrame(url)
		{	
			if (url!="") javascript:openIFrame('tempIFrame',url);
			else javascript:openIFrame('tempIFrame','TransporterMaster.php');
			
		}
		//window.load = openDefaultFrame('<?=$redirectUrl?>');
		
	</script>
</form>
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>