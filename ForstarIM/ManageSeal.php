<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$userId		=	$sessObj->getValue("userId");
	
	
	$selection 		=	"?pageNo=".$p["pageNo"];

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	# Add Qauality Start 
	

	
	## -------------- Pagination Settings I ------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit;
	## ----------------- Pagination Settings I End ------------	

	#List All Quality 	
	$sealManage	=	$manageSealObj->fetchPagingRecords($offset, $limit);
	$sealManageSize	=	sizeof($sealManage);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($manageSealObj->fetchAllRecords());
	$maxpage	=	ceil($numrows/$limit);
	
	if ($p["btnConfirm"]!="")
	{
		
			$seal_id	=	$p["confirmId"];
			
			
			$sealDet=$manageSealObj->getSealDetail($seal_id);
			
			$alpha	=$manageSealObj->getAlphaPrefix($sealDet[3]);
			$alphacode=$alpha[0];
		 	$sealnumber=$sealDet[2];
		
			$rm_gate_pass_id=$sealDet[1];
			$seal_status='';
			$status="Free";
			$sealRecConfirm = $manageSealObj->insertReleaseSeal($alphacode,$sealnumber,$seal_id,$rm_gate_pass_id,$seal_status,$userId,$status);
			//die();
			if ($seal_id!="") {
				// Checking the selected fish is link with any other process
				$sealRecConfirm = $manageSealObj->releaseSeal($seal_id);
				//die();
			}
	
		
		if ($sealRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succReleaseSealNumber);
			$sessObj->createSession("nextPage",$url_afterReleaseSealNumber.$selection);
			header("Location: ManageSeal.php$selection");
			//$sessObj->createSession("nextPage",$url_afterReleaseSealNumber);
			//$sessObj->createSession("nextPage",$url_afterReleaseSealNumber.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}
	
	
	## ----------------- Pagination Settings II End ------------

	//if ($editMode) $heading = $label_editQuality;
	//else $heading = $label_addQuality;
		
	//$help_lnk="help/hlp_QualityMaster.html";

$ON_LOAD_PRINT_JS	= "libjs/ManageSeal.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmManageSeal" action="ManageSeal.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		
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
								$bxHeader="Manage Seal";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Quality Master</td>
								</tr>-->
								<tr>
									<td colspan="5" height="5"  align='center'>
									<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageSealAll.php',700,600);"/><? } ?>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>

								<tr>
									<td width="1" ></td>
									<td colspan="2"  >
							<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1" >
								<?
								if( sizeof($sealManage) > 0 )
								{
									$i	=	0;
								?>
									<thead>
									<? if($maxpage>1){?>
									<tr><td colspan="6" style="padding-right:10px" class="navRow">
									<div align="right">
											  <?php 				 			  
											 $nav  = '';
									for($page=1; $page<=$maxpage; $page++)
										{
											if ($page==$pageNo)
											{
											$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
											}
											else
											{
									$nav.= " <a href=\"ManageSeal.php?pageNo=$page\" class=\"link1\">$page</a> ";
											//echo $nav;
										}
									}
								if ($pageNo > 1)
									{
									$page  = $pageNo - 1;
									$prev  = " <a href=\"ManageSeal.php?pageNo=$page\"  class=\"link1\"><<</a> ";
									}
									else
									{
									$prev  = '&nbsp;'; // we're on page one, don't print previous link
									$first = '&nbsp;'; // nor the first page link
									}

								if ($pageNo < $maxpage)
									{
									$page = $pageNo + 1;
									$next = " <a href=\"ManageSeal.php?pageNo=$page\"  class=\"link1\">>></a> ";
									}
									else
									{
									$next = '&nbsp;'; // we're on the last page, don't print next link
									$last = '&nbsp;'; // nor the last page link
									}
									// print the navigation link
									$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
									echo $first . $prev . $nav . $next . $last . $summary; 
								  ?>	
								  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
								  </div></td></tr>
								<? }?>
								<tr>
									<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
									<th nowrap >Seal</th>
									<th>Status</th>
									<th>Purpose</th>
									<? if($confirm==true) { ?><th></th><?php } ?>
								</tr>
								</thead>
								<tbody>
									<?
									foreach($sealManage as $fr)
									{
										$i++;
										$sealId		=	$fr[0];
										//$sealId
										$sealVal=$fr[4];
										if($sealVal!="out_seal")
										{
											$historyDetail	=	$manageSealObj->getSealHistory($sealId);
											if(sizeof($historyDetail) > 0)
											{
												 $historymode=1;
											}
											else
											{
												$historymode=0;
											}
										}
										else
										{
											$historymode=0;
										}
											$seal	=	$fr[1];
											$sealDetail	=	$fr[2];
											$number_gen_id	=	$fr[3];
											$alpha	=	$fr[5];
															
											if(($sealDetail!="0") && ($sealDetail!="1") &&($sealDetail!="2"))
											{
											//echo outseal;
												if($sealDetail!="")
												{
													$outseal="Out seal".'-'.$sealDetail;
												}
												else
												{
													$outseal="Out seal";
												}	
											}
											else{
												// echo $sealId;
												 $sealIn	=	$manageSealObj->procurementNumberInseal($sealId);
												 // echo inseal;
											}
														  
														
											?>
											<tr   >
												<td width="20" align="center"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$qualityId;?>" class="chkBox"></td>
												
													<td class="listing-item" nowrap > <?php if($historymode==1) { ?>
												<?php
												$detailsvalue='';
												$displaySealHistory=$manageSealObj->getAllSealNumberData($alpha,$seal);
												if(sizeof($displaySealHistory)>0) {
												$detailsvalue='<table width=100% border=1 cellspacing=0 cellpadding=2><tr bgcolor=#D9F3FF ><th  class=listing-head>Procurement ID</th><th  class=listing-head>Seal type</th><th  class=listing-head>Seal status</th></tr>';
												
												 foreach($displaySealHistory as $displaySeal )
												 {
													$type= $displaySeal[0];
													$status= $displaySeal[1];
													$sealIdVal=$displaySeal[2];
													$sealsIn	=	$manageSealObj->procurementNumberOutseal($sealIdVal);
													$detailsvalue.='<tr bgcolor=#f2f2f2><td class=listing-item>'.$sealsIn[0].'&nbsp;</td><td class=listing-item>'.$type.'&nbsp;</td><td class=listing-item>'.$status.'&nbsp;</td></tr>';
												 } 
												
														
												$detailsvalue.='</table>';
												}
												?>

													<a onMouseOver="ShowTip('<?=$detailsvalue;?>');" onMouseOut="UnTip();"><?php } ?><?=$alpha.$seal;?></td>
												<td class="listing-item" nowrap >
												<?php
												if($sealDetail== '0')
												{
													
													echo "Blocked";
													
												}
												elseif($sealDetail== '1')
												{
													
													echo "Used";
													
												}
												elseif($sealDetail== '2')
												{
												echo "Free";
												}
												else
												{
												echo "Used";
												}
												//echo $sealDetail;
												?></td>
												<td>
												<?php
												if($sealDetail== '0')
												{
													if($sealIn[0]!="")
													{
													echo $sealIn[0];
													}
													
												}
												elseif($sealDetail== '1')
												{
													if($sealIn[0]!="")
													{
													echo "In seal".'-'.$sealIn[0];
													
													}
													
												}
												elseif($sealDetail== '2')
												{
												//echo "Free";
												}
												else
												{
												echo $outseal;
												}
												//echo $sealDetail;
												?>
												
												
												
												</td>
											<? if($confirm==true ) { ?>
											<td align="center"> <?php if( $sealDetail== '0') {?><input type="submit" value="Release seal" name="btnConfirm" onClick="return releaseSeal(this.form,<?=$sealId;?>,'confirmId');" > <?php } ?></td>
											<?php } ?>
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="confirmId"	id="confirmId" value="" >
											
											<? if($maxpage>1){?>
											<tr>
				<td colspan="6" style="padding-right:10px" class="navRow">
				<div align="right">
				  <?php 				 			  
				 $nav  = '';
		for($page=1; $page<=$maxpage; $page++)
			{
				if ($page==$pageNo)
   				{
      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   				}
   				else
   				{
      	$nav.= " <a href=\"ManageSeal.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
	if ($pageNo > 1)
		{
   		$page  = $pageNo - 1;
   		$prev  = " <a href=\"ManageSeal.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	}
		else
		{
   		$prev  = '&nbsp;'; // we're on page one, don't print previous link
   		$first = '&nbsp;'; // nor the first page link
		}

	if ($pageNo < $maxpage)
		{
   		$page = $pageNo + 1;
   		$next = " <a href=\"ManageSeal.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	}
		else
		{
   		$next = '&nbsp;'; // we're on the last page, don't print next link
   		$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div></td></tr>
											<? }?>
											<?
												}
												else
												{
											?>
											<tr>
												<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
		</tbody>
		</table>
									</td>
								</tr>
								<tr >	
									<td colspan="3" height='10'>
										
									</td>
								<tr>
									<td colspan="5" height="5"  align='center'><? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageSealAll.php',700,600);"/><? } ?></td>
								</tr>
								
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
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
	</table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>