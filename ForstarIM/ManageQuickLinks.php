<?php
	require("include/include.php");
	//require("lib/ManageMenus_class.php");
	//$manageMenuObj = new ManageMenus($databaseConnect);

	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	$userId = $sessObj->getValue("userId");
	
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
	$reEdit = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
/*	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();	
	}	*/
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;		
//----------------------------------------------------------
	
	$selection = "?pageNo=".$p["pageNo"];


	if ($g["menuUp"]!="")	$menuChangeId   = $g["menuUp"];			
	else 			$menuChangeId	= $g["menuDown"];
	
	//$data = "101-80;10--80";
	//echo ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $data);
	
	if ($menuChangeId!="" && ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $menuChangeId)) {
		$updateMenuOrder = $manageQuickLinksObj->changeMenuOrder($menuChangeId);		
	} 
	//echo $updateMenuOrder;

	if ($g["selModule"]!="")	$selModule = $g["selModule"];
	else 				$selModule = $p["selModule"];
	
	if ($g["selSubModule"]!="")	$selSubModule = $g["selSubModule"];
	else 				$selSubModule = $p["selSubModule"];
	
	if ($g["selModule"]!="")	$hidSelModule = $g["selModule"];
	else 				$hidSelModule = $p["hidSelModule"];	
	if ($selModule!=$hidSelModule) {		
		$selSubModule = 0;
	}
	

	# list all Modules
	$roleId = $sessObj->getValue("userRole");
	$moduleRecords	= $manageQuickLinksObj->getModuleRecords($roleId);
	
//--------------------Add To QuickList --------------------

	if ($p["cmdAddToQuickList"]!="") {

		$rowCount	=	$p["hidRowCount"];

		for ($i=1; $i<=$rowCount; $i++) {
			$funcId	=	$p["chkfunctionId_".$i];
			if($funcId!="" && $funcId!=0)
			$quickListRecIns = $manageQuickLinksObj->addToQuickList($funcId,$userId);
		}

	}

//-----------------------------------------------------------

//--------------------Delete From QuickList -------------------
	if($p["cmdDeletFromQuickList"]!=""){
		
		$qlRowCount  = $p["hidQLRowCount"];
		for ($i=1; $i<=$qlRowCount; $i++) {
			$funcId	=	$p["chkQLFuncId_".$i];
			if($funcId!="" && $funcId!=0)
			$quickListRecDel = $manageQuickLinksObj->deletFromQuickList($funcId,$userId);

		}

	}
//-------------------------------------------------------------
//-------------------------List the quick list ---------------------

	$quickListRecord = $manageQuickLinksObj->getQuickListRecords();
	$sizeQuickList = sizeof($quickListRecord);

//--------------------------------------------------------------------

//echo "$selModule, $selSubModule, $hidSelModule";
	if ($selModule!="") {
		$subModuleRecords = $manageMenuObj->getSubMenus($selModule);
		# Filter Function Records
		$functionRecords = $manageQuickLinksObj->getFunctionRecords($selModule, $selSubModule,$userId);
	}

	# Include Javascript
	$ON_LOAD_PRINT_JS = "libjs/ManageQuickLinks.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmManageQuickLinks" action="ManageQuickLinks.php" method="POST">
<table cellspacing="0"  align="center" cellpadding="0" width="100%">
	<tr>
		<td height="30" align="center" class="err1" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Quick Links</td>
							</tr>
							<tr>
								<td width="1" ></td>
								<td colspan="2"  align="center">
									<table cellpadding="0"  width="94%" cellspacing="0" border="0" align="center">
									  <tr> 
										<td colspan="2" height="5"></td>
									  </tr>
									  <tr>
										<td colspan="3" nowrap height="5"></td>
									  </tr>
									  <tr>
										<td class="fieldName" nowrap >&nbsp;</td>
										<td colspan="2" align="center">
											<table width="250">
												<tr> 
													<td class="fieldName" nowrap="true">Modules:</td>
													<td nowrap="true">
													<select name="selModule" onchange="this.form.submit();">
													<option value="">-- Select --</option>
													<?
													foreach ($moduleRecords as $mr) {
														$moduleId   = $mr[0]; 
														$moduleName = $mr[1]; 
														$selected = "";
														if ($selModule==$moduleId) $selected = "selected";
													?>
													<option value="<?=$moduleId?>" <?=$selected?>><?=$moduleName?></option>
													<?
													}
													?>
													</select>
													</td>
													<td class="fieldName" nowrap="true">Sub Menu:</td>
													<td nowrap="true">
													<select name="selSubModule" onchange="this.form.submit();">
													<option value="">-- Select --</option>
													<?
													foreach ($subModuleRecords as $smr) {
														$subModuleId   = $smr[0]; 
														$subModuleName = $smr[1]; 
														$selected = "";
														if ($selSubModule==$subModuleId) $selected = "selected";
													?>
													<option value="<?=$subModuleId?>" <?=$selected?>><?=$subModuleName?></option>
													<?
													}
													?>
													</select>
													</td>					
												</tr>
											</table>
										</td>
									</tr>
								    <tr> 
										<td class="fieldName" nowrap >&nbsp;</td>
										<td colspan="2" align="center">
											<table width="250" cellpadding="0" cellspacing="0">
											  <tr>
												<td class="fieldName"></td>
												<td></td>
											  </tr>
											</table>
										</td>
								   </tr>			
									<? 
									if (sizeof($functionRecords)>0) {
										$i = 0;
									?>
								   <tr><TD height="10"></TD></tr>
									<? if($err!="" ){?>
								   <tr>
										<td height="30" align="center" class="err1" colspan="3"><? if($err!="" ){?><?=$err;?><?}?></td>
								  </tr>
									<?}?>
								<!-- BOX START -->
								<tr>
								<td colspan="3" align="center">
									<table width="60%" border="0" cellspacing="0" cellpadding="0">
									
										<tr>
											<!-- Function BOX START -->
											<td  align="center" valign="top">
												<table width="100" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
													<tr bgcolor="#f2f2f2" align="center">
														<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" >No.</th>
														<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" >Function</th>
														<th class="listing-head" style="padding-left:5px; padding-right:5px;" ></th>
													</tr>
													
													<?
														for ($j=1;$j<=sizeof($functionRecords);$j++) {
														$rec = $functionRecords[$j-1]; // Get Current Record
														$i++;	
														$functionId   = $rec[0];
														$functionName = $rec[1];					
														$menuOrderId  = $rec[2];
													?>
												 <tr bgcolor="#FFFFFF">	
													<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$j?></td>	
													<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$functionName?></td>
													<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
														<input type="checkbox" name="chkfunctionId_<?=$j;?>" id="chkfunctionId_<?=$j;?>" value="<?=$functionId;?>" >
													</td>
												</tr>
												<?
													}
												?>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >		
											 </table>
										 </td>
								<!-- FUNCTION BOX END -->
										<td width="6">
										</td>
								<!-- ADD BUTTON BOX START -->
										<td align="center" valign="center">
											<table width="70" cellspacing="0" cellpadding="0"   align="center">
			
												<tr>
													<td align="center">
														<input type="submit" value=" > "  class="button" name="cmdAddToQuickList"  onclick="return validateAddToQuickList(document.frmManageQuickLinks);">
													</td>
												</tr>
											</table>
										 </td>
										 <td width="4"></td>
							 <!-- ADD BUTTON BOX END -->
							 <!-- QUICKLIST BOX SECTION START -->
										<td  align="center" valign="top">
											<table width="100" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td>
													<!-- Remove From Quick List Start-->
														<tr>
															<td align="right" >
																<input type="submit" value=" Remove From Quick List " class="button" name="cmdDeletFromQuickList" onclick="return confDelete(document.frmManageQuickLinks);">
															</td>
														</tr>
														<tr>
															<td height="10" ></td>
														</tr>
												<!-- Remove From Quick List End -->
										

												<!--QUICK LIST BOX START -->
														<tr>
															<td>
																<table width="100" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
																	<tr bgcolor="#f2f2f2" align="center">
																		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">No.</th>
																		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Quick List</th>
																		<th class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2"></th>
																		<th class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2">Menu Order </th>
																	</tr>
																	<tr>
																		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Move Up</th>
																		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Move Down</th>
																	</tr>
				
																	  <?
																		 $cnt = 0;
																	for ($j=1;$j<=sizeof($quickListRecord);$j++) {
																	$rec = $quickListRecord[$j-1]; // Get Current Record
																	$fRec   = $quickListRecord[$j]; //Forward Record
																	$pRec   = $quickListRecord[$j-2]; // Prev Rec				
																	$cnt++;
																	$quickId  = $rec[0];
																	$funcId   = $rec[1];					
																	$uId = $rec[2];					
																	$menuOrderId  = $rec[3];
																	$menuUp		= "$pRec[0]-$rec[3];$rec[0]-$pRec[3]";	// Pass URL value		
																	$menuDown	= "$fRec[0]-$rec[3];$rec[0]-$fRec[3]"; 
																	$functionName=$manageQuickLinksObj->getFunctionName($funcId);
																	
																	?>
																	<tr bgcolor="#FFFFFF">	
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$j?></td>	
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$functionName[0];?></td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left">
																			<input type="checkbox" name="chkQLFuncId_<?=$j;?>" id="chkQLFuncId_<?=$j;?>" value="<?=$funcId;?>" >
																		</td>

																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center">
																			<? if ($cnt>1 && $cnt!=sizeof($quickListRecord)) {?>
																			<a href="ManageQuickLinks.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuUp=<?=$menuUp?>" class="displayArrow"><img src="images/arrow_u.gif" border="0" title="Move Up"></a>
																			<? }?>
																			<? if ($cnt==sizeof($quickListRecord)) {?>
																			<a href="ManageQuickLinks.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuUp=<?=$menuUp?>" class="displayArrow"><img src="images/arrow_u.gif" border="0" title="Move Up"></a>
																			<? }?>
																		</td>
																		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center">
																			<? if ($cnt==1) {?>
																			<a href="ManageQuickLinks.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuDown=<?=$menuDown?>" class="displayArrow"><img src="images/arrow_d.gif" border="0" title="Move Down"></a>
																			<? } ?>
																			<? if ($cnt>1 && $cnt!=sizeof($quickListRecord)) {?>
																			<a href="ManageQuickLinks.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuDown=<?=$menuDown?>" class="displayArrow"><img src="images/arrow_d.gif" border="0" title="Move Down"></a>
																			<? }?>
																		</td>
																	</tr>
																	<?
																			
																		}
																		
																	?>
																	<input type="hidden" name="hidQLRowCount"	id="hidQLRowCount" value="<?=$cnt?>" >		
															</table>
														</td>
													</tr>	
									
										<!-- QUICK LIST BOX END -->
										<!-- Remove From Quick List Start-->
													<tr>
														<td height="10" colspan="5"></td>
													</tr>
													<tr>
														<td align="right" colspan="5">
															<input type="submit" value=" Remove From Quick List " class="button" name="cmdDeletFromQuickList" onclick="return confDelete(document.frmManageQuickLinks);">
														</td>
													</tr>
								
									<!-- Remove From Quick List End -->
												</td>
											</tr>
										</table>		
									</td>
					<!-- QUICKLIST BOX SECTION END -->
								</tr>
							</td>
					</table>
				</tr>
	<!-- BOX END -->
				<tr><TD height="10"></TD></tr>		
			
				<? } else if($selModule!="" && $selSubModule!="") {
				?>
				<tr bgcolor="white">
					<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
				</tr>
				<?
					}
				?>     
				<tr> 
                    <td colspan="4" align="center" class="err1"><? if(sizeof($functionRecords)<=0 && $selModule!=""){ echo "No Menus Found";}?></td>
                 </tr>
				 <tr> 
                     <? if($editMode){?>
                     <?} else{?>
                     <td colspan="4" align="center"></td>
                        <input type="hidden" name="cmdAddNew" value="1">
                        <?}?>
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
	<!-- Form fields end   -->
	</td>
	  </tr>
	 <input type="hidden" name="hidSelModule" value="<?=$selModule?>">	
	 <!--<input type="hidden" name="hidSelModule" value="<?=$selModule?>">-->
		<tr>
			<td height="10" ></td>
		</tr>		
	</table>	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
