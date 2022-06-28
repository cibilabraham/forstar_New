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
	$ON_LOAD_PRINT_JS	= "libjs/CostMasters.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php"); 
?>
<style>
 #container-1 ul li
{
  list-style:none;
}
</style>
<form name="frmCostMasters" action="CostMasters.php" method="post">
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
					<TD align="left" width="10%" class="pageName" nowrap>Cost Masters</TD>
					<TD width="90%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="10"></TD></tr>
<!-- 
ProductionManPower.php		Man Power Cost
ProductionFishCutting.php	Fish Cutting Cost
ProductionMarketing.php		Marketing Cost
ProductionTravel.php		Travel Cost
PackingLabourCost.php		Packing Labour Cost
PackingSealingCost.php		Packing Sealing Cost
PackingMaterialCost.php		Packing Material Cost

ProductionManPower.php, ProductionFishCutting.php, ProductionMarketing.php, ProductionTravel.php, PackingLabourCost.php, PackingSealingCost.php, PackingMaterialCost.php
 -->
	<tr>
	<TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="CostMastersLoad.php?tab=1"><span>Man Power Cost</span></a></li>
			<li><a href="CostMastersLoad.php?tab=2"><span>Fish Cutting Cost</span></a></li>				
			<li><a href="CostMastersLoad.php?tab=3"><span>Marketing Cost</span></a></li>
			<li><a href="CostMastersLoad.php?tab=4"><span>Travel Cost</span></a></li>
			<li><a href="CostMastersLoad.php?tab=5"><span>Packing Labour Cost</span></a></li>
			<li><a href="CostMastersLoad.php?tab=6"><span>Packing Sealing Cost</span></a></li>
			<li><a href="CostMastersLoad.php?tab=7"><span>Packing Material Cost</span></a></li>
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
						
						with(milonic=new menuname("Main Menu")) {
						alwaysvisible=1;
						menuwidth=560; // Default:400 up
						openstyle="tab";
						orientation="horizontal";
						screenposition="center";
						style=mStyle;							
						aI("align=center;keepalive=1;text=Man Power Cost;url=javascript:openIFrame('tempIFrame','ProductionManPower.php');showmenu=sample1;title=Man Power Cost;");
						aI("align=center;keepalive=1;text=Fish Cutting Cost;url=javascript:openIFrame('tempIFrame','ProductionFishCutting.php');showmenu=sample2;title=Fish Cutting Cost;");
						aI("align=center;keepalive=1;text=Marketing Cost;url=javascript:openIFrame('tempIFrame','ProductionMarketing.php');showmenu=sample3;title=Marketing Cost;");
						aI("align=center;keepalive=1;text=Travel Cost;url=javascript:openIFrame('tempIFrame','ProductionTravel.php');showmenu=sample4;title=Travel Cost;");
						aI("align=center;keepalive=1;text=Packing Labour Cost;url=javascript:openIFrame('tempIFrame','PackingLabourCost.php');showmenu=sample5;title=Packing Labour Cost;");
						aI("align=center;keepalive=1;text=Packing Sealing Cost;url=javascript:openIFrame('tempIFrame','PackingSealingCost.php');showmenu=sample6;title=Packing Sealing Cost;");
						aI("align=center;keepalive=1;text=Packing Material Cost;url=javascript:openIFrame('tempIFrame','PackingMaterialCost.php');showmenu=sample7;title=Packing Material Cost;");
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
                  <td width="1" background="images/heading_bg.gif" class="page_hint">
                  </td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >
                     &nbsp;Cost Masters 
                  </td>
                </tr>
                <tr>
                  <td width="1" >
                  </td>
                  <td colspan="2" >
                    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr>
                        <td colspan="2" height="10" >
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2" nowrap class="fieldName" height="5">
                        </td>
                      </tr>
                      <tr>
                        <TD>
                          <iframe width="100%" height="600" id="tempIFrame" src="" style="border:none;" frameborder="0"></iframe>
                        </TD>
                      </tr>
                      <tr>
                        <td colspan="2"  height="10" >
                        </td>
                      </tr>
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
  <script type="text/javascript" language="JavaScript">		
		function openDefaultFrame()
		{
			javascript:openIFrame('tempIFrame','ProductionManPower.php');
			
		}
		//window.load = openDefaultFrame();		
	</script>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php"); 
?>
