<?php
	require("include/include.php");
	$err			= "";
	$errDel			= "";	

	$editTaxMasterRecId	= "";
	$taxRecId		= "";
	$baseCst		= "";
	
	$editMode		= true;
	$addMode		= false;	

	#-------------------Admin Checking--------------------------------------
	$isAdmin = false;
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
		header("Location: ErrorPageIFrame.php");
		//header ("Location: ErrorPage.php");
		die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/IngredientSpex.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<style>
 #container-1 ul li
{
  list-style:none;
}
</style>

<form name="frmIngredientSpex" action="IngredientSpex.php" method="post">
<script type="text/javascript">
            $(function() {
                $('#container-1').tabs({remote: true});
            });
 </script>
 <!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%">
<table cellspacing="0"  align="center" cellpadding="0" width="100%">	
	<tr bgcolor="White">
		<TD  style="padding-left:10px;">			
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<TR>
					<TD align="left" width="10%" class="pageName" nowrap>Ingredient Spex</TD>
					<TD width="90%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="10"></TD></tr>		
<!-- IngredientMainCategory.php, IngredientCategory.php, IngredientsMaster.php, IngredientRateList.php, IngredientRateMaster.php, SupplierIngredient.php -->
	<tr>
	<TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="IngredientSpexLoad.php?tab=1"><span>Ingredient Category</span></a></li>
			<li><a href="IngredientSpexLoad.php?tab=2"><span>Ingredient Sub-Category</span></a></li>	
			<li><a href="IngredientSpexLoad.php?tab=3"><span>Ingredient Critical Parameters</span></a></li>
			<!--<li><a href="IngredientSpexLoad.php?tab=4"><span>Ing Rate List Master</span></a></li>-->
			<li><a href="IngredientSpexLoad.php?tab=5"><span>Ingredients Master</span></a></li>
			<li><a href="IngredientSpexLoad.php?tab=6"><span>Ingredient Rate Master</span></a></li>
			<li><a href="IngredientSpexLoad.php?tab=7"><span>Ingredient Suppliers</span></a></li>
			<li><a href="IngredientSpexLoad.php?tab=8"><span>Ingredient Physical stock </span></a></li>
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
						
						with(milonic=new menuname("Main Menu")) {
						alwaysvisible=1;
						menuwidth=485; // Default:400 up
						openstyle="tab";
						orientation="horizontal";
						screenposition="center";
						style=mStyle;							
						aI("align=center;keepalive=1;text=Ingredient Category;url=javascript:openIFrame('tempIFrame','IngredientMainCategory.php');showmenu=sample1;title=Ingredient Category;");
						aI("align=center;keepalive=1;text=Ingredient Sub-Category;url=javascript:openIFrame('tempIFrame','IngredientCategory.php');showmenu=sample2;title=Ingredient Sub-Category;");
						aI("align=center;keepalive=1;text=Ingredients Master;url=javascript:openIFrame('tempIFrame','IngredientsMaster.php');showmenu=sample3;title=Ingredients Master;");
						aI("align=center;keepalive=1;text=Ing Rate List Master;url=javascript:openIFrame('tempIFrame','IngredientRateList.php');showmenu=sample4;title=Ingredient Rate List Master;");
						aI("align=center;keepalive=1;text=Ingredient Rate Master;url=javascript:openIFrame('tempIFrame','IngredientRateMaster.php');showmenu=sample5;title=Ingredient Rate Master;");
						aI("align=center;keepalive=1;text=Ingredient Suppliers;url=javascript:openIFrame('tempIFrame','SupplierIngredient.php');showmenu=sample6;title=Ingredient Suppliers;");						
						}
						drawMenus();
					</script>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>-->
	<!--<tr><TD height="30"></TD></tr>	-->
	<!--<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td bgcolor="white">						
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Spex</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">		
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
		<td height="10" align="center" ></td>
	</tr>-->		
  </table>	
	</td>
	</tr>

	</table>
	<br><br>
<!-- end code --> 
 
</form>


<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>