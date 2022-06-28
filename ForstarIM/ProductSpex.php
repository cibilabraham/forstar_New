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
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/ProductSpex.js"; 
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<style>
 #container-1 ul li
{
  list-style:none;
}
</style>
<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
		
<form name="frmProuctionMasters" id="frmProuctionMasters" action="ProuctionMasters.php" method="post">
 <script type="text/javascript">
            $(function() {
                $('#container-1').tabs({remote: true});
            });
 </script>
<table cellspacing="0"  align="center" cellpadding="0" width="100%">
	<? if($err!="" ){?>
	<tr>
		<td height="20" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
	<tr bgcolor="White">
		<TD  style="padding-left:10px;">
			<!--Product Spex&nbsp;&nbsp;&nbsp;&nbsp;<input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab">-->
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<TR>
					<TD align="left" width="20%" class="pageName">Product Spex</TD>
					<TD width="80%" align="center" style="padding-right:100px;"><input name="cmdRefresh" type="button" class="button" id="cmdRefresh" value="Refresh Tab" onclick="refreshTab();" title="Refresh selected tab"></TD>
				</TR>
			</table>
		</TD>
	</tr>
	<!--<tr bgcolor="White"><TD class="pageName" style="padding-left:10px;">Product Spex</TD></tr>	-->
	<tr><TD height="5"></TD></tr>
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
						with(milonic=new menuname("sample9")){				
							style=submenuStyle;
							aI("separatorsize=4;");
						}				
						
						with(milonic=new menuname("Main Menu")) {
						alwaysvisible=1;
						menuwidth=722; // Default:400/690/720 up
						openstyle="tab";
						orientation="horizontal";
						screenposition="center";
						style=mStyle;							
						aI("align=center;keepalive=1;text=Product Category;url=javascript:openIFrame('tempIFrame','ProductCategory.php');showmenu=sample1;title=Product Category;");
						aI("align=center;keepalive=1;text=Product State;url=javascript:openIFrame('tempIFrame','ProductState.php');showmenu=sample2;title=Product State;");
						aI("align=center;keepalive=1;text=Product Group;url=javascript:openIFrame('tempIFrame','ProductGroup.php');showmenu=sample3;title=Product Group;");
						aI("align=center;keepalive=1;text=Product Master;url=javascript:openIFrame('tempIFrame','ProductMaster.php');showmenu=sample4;title=Product Master;");
						aI("align=center;keepalive=1;text=Semi-Finish Product;url=javascript:openIFrame('tempIFrame','SemiFinishProductMaster.php');showmenu=sample5;title=Semi-Finished Product Master;");
						aI("align=center;keepalive=1;text=Product Price Rate List;url=javascript:openIFrame('tempIFrame','ProductPriceRateList.php');showmenu=sample6;title=Product Price Rate List;");
						aI("align=center;keepalive=1;text=Product Pricing;url=javascript:openIFrame('tempIFrame','ProductPricing.php');showmenu=sample7;title=Product Pricing;");
						aI("align=center;keepalive=1;text=Manage Product;url=javascript:openIFrame('tempIFrame','ManageProduct.php');showmenu=sample8;title=Manage Product;");
						aI("align=center;keepalive=1;text=Product MRP Master;url=javascript:openIFrame('tempIFrame','ProductMRPMaster.php');showmenu=sample9;title=Product MRP Master;");
						}
						drawMenus();
					</script>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>-->
	<tr><TD align="center">
		<div id="container-1" align="center">
		<ul>
			<li><a href="SpexLoad.php?tab=1"><span>Product Category</span></a></li>
			<li><a href="SpexLoad.php?tab=2"><span>Product State</span></a></li>	
			<li><a href="SpexLoad.php?tab=3"><span>Product Group</span></a></li>
			<li><a href="SpexLoad.php?tab=4"><span>Product Master</span></a></li>
			<li><a href="SpexLoad.php?tab=5"><span>Semi-Finish Product</span></a></li>
			<li><a href="SpexLoad.php?tab=6"><span>Product Price Rate List</span></a></li>
			<li><a href="SpexLoad.php?tab=7"><span>Product Pricing</span></a></li>
			<li><a href="SpexLoad.php?tab=8"><span>Manage Product</span></a></li>
			<li><a href="SpexLoad.php?tab=9"><span>Product MRP Master</span></a></li>	
			<li><a href="SpexLoad.php?tab=10"><span>Product Matrix</span></a></li>
		</ul>
		
		</div>

	</TD></tr>
	<tr><TD height="30"></TD></tr>
	<?
		if ($editMode || $addMode) {
	?>
	<!--<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">						
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Spex</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
				<input type="hidden" name="hidTaxMasterRecId" value="<?=$editTaxMasterRecId;?>">
										<tr>
										  <td colspan="2" nowrap class="fieldName" height="5"></td>
								  </tr>
	<tr><TD>
		<iframe width="100%" height="600" id="tempIFrame" src="" style="border:none;" frameborder="0"></iframe>
	</TD></tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
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
	<?php
		}
		
		# Listing LandingCenter Starts
	?>
	<tr>
		<td height="10" align="center" ></td>
	</tr>
			<tr>
			<td>
				
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>	
  </table>
	<script type="text/javascript" language="JavaScript">		
		function openDefaultFrame()
		{
			//document.getElementById('frmProuctionMasters:lnk198').click();	
			javascript:openIFrame('tempIFrame','ProductCategory.php');
			
		}
		//window.load = openDefaultFrame();
		
	</script>
</form>
	</td>
	</tr>
	</table>
<!-- end code -->
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>
