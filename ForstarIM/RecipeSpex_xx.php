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
		//header ("Location: ErrorPage.php");
		//die();	
	}	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------
	
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/RecipeSpex.js"; 
	
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
		<form name="frmRecipeSpex" action="RecipeSpex.php" method="post">
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
					<TD align="left" width="10%" class="pageName" nowrap>Recipe Spex</TD>
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
			<li><a href="RecipeSpexLoad.php?tab=1"><span>Recipe Category</span></a></li>
			<!--<li><a href="RecipeSpexLoad.php?tab=2"><span>Recipe Sub-Category</span></a></li>-->	
			<li><a href="RecipeSpexLoad.php?tab=2"><span>Recipe Master</span></a></li>
			<!--
			<li><a href="RecipeSpexLoad.php?tab=3"><span>Recipe List Master</span></a></li>
			<li><a href="RecipeSpexLoad.php?tab=4"><span>Recipe Rate Master</span></a></li>
			<li><a href="RecipeSpexLoad.php?tab=5"><span>Recipe Suppliers</span></a></li>
			-->
		</ul>
		</div>
	</TD></tr>
	</table>
</form>
	</td>
	</tr>
	</table>
<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>