<?php
	require("include/include.php");
	require_once("lib/ManageMenus_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	
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
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;		
//----------------------------------------------------------
	
	//$selection = "?pageNo=".$p["pageNo"];


	
	
	if ($g["menuUp"]!="")	$menuChangeId   = $g["menuUp"];			
	else 			$menuChangeId	= $g["menuDown"];
	
	//$data = "101-80;10--80";
	//echo ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $data);
	
	if ($menuChangeId!="" && ereg("^[0-9]*\-[0-9]*\;[0-9]*\-[0-9]", $menuChangeId)) {
		$updateMenuOrder = $manageMenuObj->changeMenuOrder($menuChangeId);		
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

	$selection = "?pageNo=".$p["pageNo"]."&selModule=".$p["selModule"]."&selSubModule=".$p["selSubModule"];
	//echo "$selModule, $selSubModule, $hidSelModule";
	if ($selModule!="") {
		$subModuleRecords = $manageMenuObj->getSubMenus($selModule);
		# Filter Function Records
		$functionRecords = $manageMenuObj->getFunctionRecords($selModule, $selSubModule);
	}

	# list all Modules
	$moduleRecords	= $manageMenuObj->getModuleRecords();

	if ($p["extraflagssave"]){
		$idValue=$p["radvalue"];
		$flagValue=$p["extraflagvalue"];
		$upExtraflagvalue = $manageMenuObj->updateextraflagvalue($idValue,$flagValue);
		if ($upExtraflagvalue) {
				//$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succMenuUpdation);
				$sessObj->createSession("nextPage",$url_afterMenuUpdation.$selection);
			}

	}

	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------


	$dataRecords=$manageMenuObj->getListing($offset, $limit);
	$dataRecordsSize	= sizeof($dataRecords);
	$fetchAlldataRecords = $manageMenuObj->getAllListing();

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAlldataRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/managemenus.js"; // For Printing JS in Head section
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<link rel="stylesheet" href="libjs/jquery-ui.css">
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>
	<form name="frmManageMenus" action="ManageMenus.php" method="POST">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ></td>
		</tr>
		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
					
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Manage Menus</td>
								</tr>
								<tr>
									<td colspan="2" height="10">&nbsp;</td>
								</tr>
								<tr>
									<td  height="10">&nbsp;</td>
									<td  height="10" align="right" style="padding-bottom:5px">
										<table  cellpadding="0" width="50%" cellspacing="2" border="0" style="background-color:#cccccc">
											<tr>
												<td align="center" nowrap bgcolor="#e8edff"><a onclick="getMainMenu();" href="javascript:void(0)">add/edit Main Menu</a></td>
												<td align="center" nowrap bgcolor="#e8edff"><a onclick="getSubMenu();" href="javascript:void(0)">add/edit Sub Menu</a></td>
												<td align="center" nowrap bgcolor="#e8edff"><a onclick="getMenu();" href="javascript:void(0)">add/edit Menu</a></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2"  >
										<table cellpadding="0"  width="99%" cellspacing="0" border="0">
											<tr >
												<td colspan="3" >
													<table cellpadding="0"  width="99%" cellspacing="0" border="0" >
														<tr>
															<td colspan="3" align="center">
																<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
																<?
																if( sizeof($moduleRecords) > 0 )
																{
																	$i	=	0;
																?>
																<? if($maxpage>1){?>
																	<tr bgcolor="#FFFFFF">
																		<td colspan="11" align="right" style="padding-right:10px;">
																			<div align="right">
																				<?php
																				$nav  = '';
																				for ($page=1; $page<=$maxpage; $page++) {
																					if ($page==$pageNo) {
																							$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																					} else {
																							$nav.= " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																						//echo $nav;
																					}
																				}
																				if ($pageNo > 1) {
																					$page  = $pageNo - 1;
																					$prev  = " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
																				} else {
																					$prev  = '&nbsp;'; // we're on page one, don't print previous link
																					$first = '&nbsp;'; // nor the first page link
																				}

																				if ($pageNo < $maxpage) {
																					$page = $pageNo + 1;
																					$next = " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
																				} else {
																					$next = '&nbsp;'; // we're on the last page, don't print next link
																					$last = '&nbsp;'; // nor the last page link
																				}
																				// print the navigation link
																				$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																				echo $first . $prev . $nav . $next . $last . $summary; 
																				?>	
																				<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
																			</div> 
																		</td>
																	</tr>
																	<? }?>
																	<tr  bgcolor="#f2f2f2" >
																		<!--<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>-->
																		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Main Menu</td>
																		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Sub Menu</td>
																		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px; width:247px " nowrap>Menu</td>
																		<? 
																		/*
																		if($edit==true){?>
																		<!--<td class="listing-head"></td>-->
																		<td class="listing-head"></td>
																		<? }
																		*/?>

																	</tr>
																	<?
																	foreach ($dataRecords as $sir) {
																	
																		$i++;
																		$moduleId	=	$sir[0];
																		$mainMenuName	=	$sir[1];
																		$subMenuName	=	$sir[3];
																		$menuName	=	$sir[5];
																		
																	
																	if($subMenuName!="")
																	{
																	?>
																	<tr  bgcolor="WHITE">
																		<!--<td width="20">
																			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$procurementId;?>" class="chkBox">
																		</td>-->
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" valign="top" bgcolor="#E4DDF8">
																		<?
																		if($mainMenuName!=$oldMainMenu )
																			{
																				echo $mainMenuName;
																			}
																				$oldMainMenu=$mainMenuName;
																		?>
																		</td>
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" valign="top" bgcolor="#E4DDF8">
																		<?
																		if($subMenuName!=$oldSubMenu && $subMenuName!='0')
																		{
																			echo $subMenuName;
																		}
																		
																			$oldSubMenu=$subMenuName;
																		?>
																		</td>
																		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" valign="top" bgcolor="#E4DDF8">
																		<?
																		if($menuName!=$oldMenu )
																		{
																			echo $menuName;
																		}
																			$oldMenu=$menuName;
																		?>
																		</td>
																		<? 
																		/*
																		if($edit==true){
																		?>
																		<td class="listing-item" width="60" align="center"><?php if ($active!=1 && $generatedCount == 0 ){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$procurementId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RMProcurmentOrder.php';"><?php }
																			?>
																		</td>
																		<? } 
																		*/
																		?>
																	</tr>
																	
																	<?
																	}
																	}
																	?>
																	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
																	<input type="hidden" name="editId" value="">
																	<? if($maxpage>1){?>
																	<tr bgcolor="#FFFFFF">
																		<td colspan="11" align="right" style="padding-right:10px;">
																			<div align="right">
																			<?php
																			$nav  = '';
																			for ($page=1; $page<=$maxpage; $page++) {
																			if ($page==$pageNo) {
																					$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																			} else {
																					$nav.= " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																				//echo $nav;
																			}
																			}
																			if ($pageNo > 1) {
																				$page  = $pageNo - 1;
																				$prev  = " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
																			} else {
																				$prev  = '&nbsp;'; // we're on page one, don't print previous link
																				$first = '&nbsp;'; // nor the first page link
																			}

																			if ($pageNo < $maxpage) {
																				$page = $pageNo + 1;
																				$next = " <a href=\"ManageMenus.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
																			} else {
																				$next = '&nbsp;'; // we're on the last page, don't print next link
																				$last = '&nbsp;'; // nor the last page link
																			}
																			// print the navigation link
																			$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
																			echo $first . $prev . $nav . $next . $last . $summary; 
																		  ?>	
																			<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
																		</div> 
																	</td>
																</tr>
																<? }?>
																<?
																}
																else
																{
																?>
																<tr bgcolor="white">
																	<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																</tr>	
																<?
																}
																?>
															</table>	
														</td>
													</tr>
												</table>
											</td>
										</tr>
                      <tr> 
                        <td colspan="2" height="5"></td>
                      </tr>
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>

				<tr><td><table align="center" cellpadding="1" cellspacing="1" style="border:1px solid #999999">
                      <tr >
                        <td colspan="3" align="center"  ><table bgcolor="#999999" width="250" cellpadding="5" cellspacing="1">
                                  <tr bgcolor="white"> 
                                    <td class="fieldName" nowrap="true">Modules:</td>
                                    <td nowrap="true">
					<!--<select name="selModule" onchange="this.form.submit();">-->
					<select name="selModule" onchange="functionLoad(this);">
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
					<!--<select name="selSubModule" onchange="this.form.submit();">-->
						<select name="selSubModule" onchange="functionLoad(this);">
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
                                </table></td>
                        </tr>
                      <tr > 
                        <td>
                        
                          </td>
                        </tr>			
                      <? 
			 if (sizeof($functionRecords)>0) {
				 $i = 0;
		      ?>

			
			
			<? if($err!="" ){?>
			<tr bgcolor="white">
			<td height="30" align="center" class="err1" colspan="3"><? if($err!="" ){?><?=$err;?><?}?></td>
			</tr>
			<?}?>
                      <tr bgcolor="white">
                        <td colspan="4" align="center" >
		<table width="60%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center">
		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">No.</th>
		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Select</th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Function</th>
				 <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Extra Flag</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;" colspan="2">Menu Order </th>
	     </tr>
		<tr bgcolor="white">
			<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Move Up</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px;">Move Down</th>
		</tr>
	
              <?
		for ($j=1;$j<=sizeof($functionRecords);$j++) {
			$rec = $functionRecords[$j-1]; // Get Current Record
			$fRec   = $functionRecords[$j]; //Forward Record
			$pRec   = $functionRecords[$j-2]; // Prev Rec				
			//echo "cId=$rec[0]-$rec[2], Down=$r[0]-$r[2], up=$c[0]-$c[2]<br>";
			//echo "cId=$rec[0], Down=$fRec[0]-$rec[2];$rec[0]-$fRec[2], up=$pRec[0]-$rec[2];$rec[0]-$pRec[2]<br>";
			//foreach ($functionRecords as $fr) {
			$i++;	
			$functionId   = $rec[0];					
			$functionName = $rec[1];					
			$menuOrderId  = $rec[2];
			$menuUp		= "$pRec[0]-$rec[2];$rec[0]-$pRec[2]";	// Pass URL value		
			$menuDown	= "$fRec[0]-$rec[2];$rec[0]-$fRec[2]"; 	
			//echo "UP:$menuUp=>Down:$menuDown<br>";
			/*
			$functionId   = $fr[0];					
			$functionName = $fr[1];					
			$menuOrderId  = $fr[2];			
			*/
			$flagValue=$rec[3];
		?>
              <tr bgcolor="#FFFFFF">	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$j?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><input type="radio" name="sel" id="sel" value=<?=$functionId;?> class="fsaChkbx" /></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$functionName?></td>
				<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$flagValue?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center">
			<? if ($i>1 && $i!=sizeof($functionRecords)) {?>
			<a href="ManageMenus.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuUp=<?=$menuUp?>" class="displayArrow"><img src="images/arrow_u.gif" border="0" title="Move Up"></a>
			<? }?>
			<? if ($i==sizeof($functionRecords)) {?>
			<a href="ManageMenus.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuUp=<?=$menuUp?>" class="displayArrow"><img src="images/arrow_u.gif" border="0" title="Move Up"></a>
			<? }?>
		</td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center">
		<? if ($i==1) {?>
			<a href="ManageMenus.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuDown=<?=$menuDown?>" class="displayArrow"><img src="images/arrow_d.gif" border="0" title="Move Down"></a>
		<? } ?>
		<? if ($i>1 && $i!=sizeof($functionRecords)) {?>
			<a href="ManageMenus.php?selModule=<?=$selModule?>&selSubModule=<?=$selSubModule?>&menuDown=<?=$menuDown?>" class="displayArrow"><img src="images/arrow_d.gif" border="0" title="Move Down"></a>
		<? }?>
		
		</td>
 		</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >		
      </table></td>
                        </tr>
		
		<?php
			if ($p["extraflags"]=="")
						{?>
		<tr bgcolor="white"><TD height="10" align="center" colspan="3"><input type="submit"  name="extraflags" value="Set extra flags"  onclick="return checkboxSel()";/></TD></tr>
		<?php }?>
		<?php
			if ($p["extraflags"]!="")
						{
			$optionval=$p["sel"];
			?>
			<input type="hidden" name="radvalue" value=<?=$optionval;?> />
						<tr bgcolor="white"><TD height="10" align="center">&nbsp;</td><TD height="10" align="center">Enter flag Value<input type="text"  name="extraflagvalue" value="" id="extraflagvalue" /></TD></tr>
						<tr bgcolor="white"><TD height="10" align="center">&nbsp;</td><TD height="10" align="center"><input type="submit"  name="extraflagssave" value="Save " /></TD></tr>

						
			
		<? }} else if($selModule!="" && $selSubModule!="") {
		?>
		<tr bgcolor="white">
			<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>
		<?
			}
		?>      <tr bgcolor="white"> 
                        <td colspan="4" align="center" class="err1"><? if(sizeof($functionRecords)<=0 && $selModule!=""){ echo "No Menus Found";}?></td>
                        </tr>



			</table></td></tr>



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
<script language="javascript">
function checkboxSel()
{
var atLeastOneIsChecked = false;
 //$('input.fsaChkbx:checkbox').each(function () {
	 $('input.fsaChkbx').each(function () {
  if ($(this).is(':checked')) {
   atLeastOneIsChecked = true;
      // Stop .each from processing any more items
      return false;
    }
  });

  	if (!atLeastOneIsChecked){
		alert("Please select a Record");
		return false;
	}
	return true;
}


function textVal()
{
if ((document.getElementById("extraflagvalue").value==""))
{
	alert("Please enter a Value");
	document.getElementById("extraflagvalue").focus();
	return false;
}

}
function functionLoad(formObj)
	{
		//alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}
</script>

<div id="dialog" title="Module"  >
	<!--<p>
	This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
	</p>-->
</div>
<div id="dialog2" title="Add Menu"  >
	<!--<p>
	This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.
	</p>-->
</div>