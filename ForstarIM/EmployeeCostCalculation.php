<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$fishId			=	"";	
	$fishName		=	"";
	$fishCode		=	"";
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------

	
	if ($p["cmdSaveChange"]!="") {
		
		$employeeCnt		=	$p["employeeCnt"];
		for($i=0; $i<$employeeCnt; $i++)
		{
			$costPercent	=	trim($p["costPercent_".$i]);
			$empCostId	=	trim($p["empCostId_".$i]);
			
			if ($costPercent!="" && $empCostId!="") {
				$empRecUptd		=	$employeeCostCalculationObj->updateEmployeeCost($empCostId,$costPercent,$userId);
			}
		
			if ($empRecUptd) {
				$sessObj->createSession("displayMsg",$msg_succEmpCostUpdate);
				$sessObj->createSession("nextPage",$url_afterUpdateEmpCost.$selection);
			} else {
				$editMode	=	true;
				$err		=	$msg_failEmpCostUpdate;
			}
			$empRecUptd	=	false;

		}
		
	}


	
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	

	$employeeRecords=$employeeCostCalculationObj->fetchAllRecords();
	
	
	//$help_lnk="help/hlp_addFishMaster.html";

	$ON_LOAD_PRINT_JS	= "libjs/employeecostcalculation.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>
<form name="frmEmployeeCostCalculation" action="EmployeeCostCalculation.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" > <?=$err;?></td>			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<?
		}
		# Listing Fish Starts
		?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Employee Cost Calculation Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<table width="50%">
											<?
											//	if( $editMode || $addMode) {
											?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php			
																	$entryHead = "Employee Cost Calculation ";
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<td colspan="2" align="center"> 
																					<? if($edit==true){?>&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " >
																					</td>				
																						<?} else{?>
																					 <td align="center">&nbsp;&nbsp;</td>
																						<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<?
																				$i=0;
																				foreach($employeeRecords as $emp)
																				{
																					$empCostId=$emp[0];
																					$empCostName=$emp[1];
																					$empCostPercent=$emp[2];
																					($empCostName=="TOTAL COST")?$readonly="readonly":$readonly="";
																				?>
																				<tr>
																					<td class="fieldName" nowrap ><?=$empCostName?><INPUT TYPE="hidden"  NAME="empCostId_<?=$i?>" size="15" value="<?=$empCostId;?>" ></td>
																					<td><INPUT TYPE="text" id="costPercent_<?=$i?>" NAME="costPercent_<?=$i?>" size="15" value="<?=$empCostPercent;?>" <?=$readonly?> <? if($readonly==""){ ?>onkeyUp="calcEmployeeCost();" onkeypress="return isNumber (event);" <? } ?>></td>
																				</tr>
																				<? 
																				$i++;
																				}
																				?>
																				<input type="hidden" name="employeeCnt" id="employeeCnt" value="<?=$i?>">
																				
																				<tr>
																					<td colspan="2" align="center">
																					<? if($edit==true){?>&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes ">
																					</td>				
																						<?} else{?>
																					 <td align="center">&nbsp;&nbsp;</td>
																						<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
														</tr>
													</table>
													<!-- Form fields end   -->
												</td>
											</tr>	
											<?
											//	}			
												# Listing Fish Starts
											?>
										</table>
									</td>
								</tr>
								
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="4" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>
																
							</table>
						<?php
							include "template/boxBR.php"
						?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
			
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
			<!--<tr><td height="10" align="center"><a href="FishCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr>-->
	</table>
	
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

