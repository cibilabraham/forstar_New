<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$selection 	=	"?pageNo=".$p["pageNo"]."&selDistributorFilter=".$p["selDistributorFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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
	/*-----------------------------------------------------------*/

	# Add Category Start 
	if ($p["cmdAddNew"]!="") $addMode  =   true;
	if ($p["cmdCancel"]!="") {
		$addMode   	=  false;
		$editMode	=  false;
	}

	if ($p["selDistributor"]!="")	$selDistributor = $p["selDistributor"];
	if ($p["selProduct"]!="") 	$selProduct	= $p["selProduct"];
	if ($p["indexNo"]!="")		$indexNo	= $p["indexNo"];

	# Add a Record
	if ($p["cmdAdd"]!="") {	
		$selDistributor = $p["selDistributor"];
		$selProduct	= $p["selProduct"];
		$indexNo	= $p["indexNo"];

		# check Product Identifier exist
		$productIdentifierExist = $productIdentifierObj->chkProductIdentifierExist($selDistributor, $selProduct, $cId);

		if ($selDistributor!="" && $selProduct!="" && $indexNo!="" && !$productIdentifierExist) {
			$productIdentifierRecIns = $productIdentifierObj->addProductIdentifier($selDistributor, $selProduct, $indexNo, $userId);
			if ($productIdentifierRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductIdentifier);
				$sessObj->createSession("nextPage",$url_afterAddProductIdentifier.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductIdentifier;
			}
			$productIdentifierRecIns = false;
		} else {
			$addMode = true;
			if ($productIdentifierExist) $err = $msg_failAddProductIdentifier."<br/>The selected records existing in our database.";
			else $err = $msg_failAddProductIdentifier;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$productIdentifierId = $p["hidProductIdentifierId"];
		$selDistributor = $p["selDistributor"];
		$selProduct	= $p["selProduct"];
		$indexNo	= $p["indexNo"];

		# check Product Identifier exist
		$productIdentifierExist = $productIdentifierObj->chkProductIdentifierExist($selDistributor, $selProduct, $productIdentifierId);		

		if ($productIdentifierId!="" && $selDistributor!="" && $selProduct!="" && $indexNo!="" && !$productIdentifierExist) {
			$productIdentifierRecUptd = $productIdentifierObj->updateProductIdentifier($productIdentifierId, $selDistributor, $selProduct, $indexNo);
		}	
		if ($productIdentifierRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductIdentifierUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductIdentifier.$selection);
		} else {
			$editMode	=	true;
			if  ($productIdentifierExist) $err = $msg_failProductIdentifierUpdate."<br/>The selected records existing in our database.";
			else $err = $msg_failProductIdentifierUpdate;
		}
		$productIdentifierRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$productIdentifierRec		= $productIdentifierObj->find($editId);
		$editProductIdentifierId 	= $productIdentifierRec[0];
		$selDistributor	=	stripSlash($productIdentifierRec[1]);
		$selProduct	=	stripSlash($productIdentifierRec[2]);
		$indexNo	=	$productIdentifierRec[3];		
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productIdentifierId	=	$p["delId_".$i];

			if ($productIdentifierId!="") {
				// Need to check , is it link with any other process?
				$productIdentifierRecDel = $productIdentifierObj->deleteProductIdentifier($productIdentifierId);
			}
		}
		if ($productIdentifierRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductIdentifier);
			$sessObj->createSession("nextPage",$url_afterDelProductIdentifier.$selection);
		} else {
			$errDel	=	$msg_failDelProductIdentifier;
		}
		$productIdentifierRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productIdentifierId	=	$p["confirmId"];


			if ($productIdentifierId!="") {
				// Checking the selected fish is link with any other process
				$productRecConfirm = $productIdentifierObj->updateProductIdentifierconfirm($productIdentifierId);
			}

		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmproduct);
			$sessObj->createSession("nextPage",$url_afterDelProductIdentifier.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$productIdentifierId = $p["confirmId"];

			if ($productIdentifierId!="") {
				#Check any entries exist
				
					$productRecConfirm = $productIdentifierObj->updateProductIdentifierReleaseconfirm($productIdentifierId);
				
			}
		}
		if ($productRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmproduct);
			$sessObj->createSession("nextPage",$url_afterDelProductIdentifier.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	


	if ($g["selDistributorFilter"]!="") $selDistributorFilter = $g["selDistributorFilter"];
	else $selDistributorFilter = $p["selDistributorFilter"];

	if ($p["selDistributorFilter"]!=$p["hidSelDistributorFilter"]) {
		$offset	= 0;
	}
	# List all ProductIdentifier
	$productIdentifierRecords = $productIdentifierObj->fetchAllPagingRecords($offset, $limit, $selDistributorFilter);
	$productIdentifierRecordSize	= sizeof($productIdentifierRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllProductIdentifierRecs = $productIdentifierObj->fetchAllRecords($selDistributorFilter);
	$numrows	=  sizeof($fetchAllProductIdentifierRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	#List all Distributor	
	//$distributorFilterResultSetObj = $distributorMasterObj->fetchAllRecords();
	$distributorFilterResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	if ($addMode || $editMode) {	
		# List all Distributor
		//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();
	}

	# Get Active Products
	if ($selDistributor) {
		$sDate = date("Y-m-d");
		//$productPriceRateListId = $manageRateListObj->getRateList("PMRP", $sDate);
		$distMarginRateListId = $distMarginRateListObj->getRateList($selDistributor, $sDate);
		$productRecords = $productIdentifierObj->getActiveProducts($selDistributor, $distMarginRateListId);
	}

	#heading Section
	if ($editMode) $heading	= $label_editProductIdentifier;
	else	       $heading	= $label_addProductIdentifier;

	$ON_LOAD_PRINT_JS	= "libjs/ProductIdentifierMaster.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
	
	<form name="frmProductIdentifierMaster" action="ProductIdentifierMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>	
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Product Identifier Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="35%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php							
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductIdentifierMaster.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductIdentifierMaster(document.frmProductIdentifierMaster);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductIdentifierMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductIdentifierMaster(document.frmProductIdentifierMaster);">												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidProductIdentifierId" value="<?=$editProductIdentifierId;?>">
		<tr><TD height="10"></TD></tr>
	<tr>
		<td colspan="2" nowrap>
		<table width="200">
	<tr>
  		<td class="fieldName" nowrap >*Distributor</td>
		<td>
		   <select name="selDistributor" id="selDistributor"  onchange="<? if ($addMode) {?>this.form.submit();<? } else {?>this.form.editId.value=<?=$editId?>;this.form.submit();<?}?>" <?=$disableField?> style="width:120px;">
                                        <option value="">-- Select --</option>
					<?php	
					while ($dr=$distributorResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorCode = stripSlash($dr[1]);
						$distributorName = stripSlash($dr[2]);	
						$selected = ($selDistributor==$distributorId)?"selected":"";	
					?>
                            		<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
					</select>
					<input type="hidden" name="hidDistributor" value="<?=$selDistributor?>">
		</td>
	</tr>
	<tr>
					<td nowrap class="fieldName" >*Product</td>
					<td nowrap>
                                        <select name="selProduct" id="selProduct">
                                        <option value="">-- Select --</option>
					<?php
					foreach ($productRecords as $pr) {
						$productId 	= $pr[0];		
						$productName	= $pr[1];
						$selected 	= ($selProduct==$productId)?"Selected":"";
					?>
                                        <option value="<?=$productId?>" <?=$selected?>><?=$productName?></option>
					<? }?>
                                        </select></td></tr>
		<tr>
			<TD class="fieldName">*Index No</TD>
			<td>
				<input type="text" name="indexNo" id="indexNo" size="12" value="<?=$indexNo?>" />
			</td>
		</tr>
	                               </table></td>
				  </tr>
					<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductIdentifierMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductIdentifierMaster(document.frmProductIdentifierMaster);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductIdentifierMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductIdentifierMaster(document.frmProductIdentifierMaster);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
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
			}			
			# Listing Category Starts
		?>
	</table>
	</td>
	</tr>		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
<tr>
				<td colspan="3" align="center">
						<table width="20%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="0">
					  <tr>
					<td nowrap="nowrap">
					<table  cellpadding="0" cellspacing="0">	
			<tr>
				<td align="right" nowrap class="listing-item">Distributor&nbsp;</td>
				<td align="right" nowrap valign="top">
				<select name="selDistributorFilter" onChange="this.form.submit();" style="width:120px;">
				 <option value="">-- Select All --</option>
					<?php
					while ($dr=$distributorFilterResultSetObj->getRow()) {
						$distributorId	 = $dr[0];
						$distributorName = stripSlash($dr[2]);		
						$selected =  ($selDistributorFilter==$distributorId)?"Selected":"";
					?>
                                        <option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
					<? }?>
				  </select>&nbsp;
				</td>				
				</tr>
		  </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
	</td>
	</tr>
	</table>
			</td>
		</tr>	
			<!--<tr>
			<td>
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
			<tr>
		<td nowrap="true">
		<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
		<tr>
			<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap style="background-repeat: repeat-x" valign="top" >&nbsp;Product Identifier Master  </td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap valign="top" style="background-repeat: repeat-x">
			</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productIdentifierRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductIdentifierMaster.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
	<tr>
		<td width="1" ></td>
		<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?php
		if ($productIdentifierRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Distributor</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Product</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Index No</th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
			<?php
			foreach ($productIdentifierRecords as $pir) {
				$i++;
				$productIdentifierId = $pir[0];
				$sDistributorName    = $pir[4];
				$sProductName	     = $pir[5];
				$indexNo	     = $pir[3];		
				$active=$pir[6];
			?>
 <tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productIdentifierId;?>" class="chkBox"></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sProductName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$indexNo;?></td>
<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productIdentifierId;?>,'editId');this.form.action='ProductIdentifierMaster.php';" ><? }?></td>
<? }?>
 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$productIdentifierId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$productIdentifierId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
		</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductIdentifierMaster.php?pageNo=$page&selDistributorFilter=$selDistributorFilter\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<?php
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</tbody>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productIdentifierRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductIdentifierMaster.php?selDistributorFilter=<?=$selDistributorFilter?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
<input type="hidden" name="hidSelDistributorFilter" value="<?=$selDistributorFilter?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	</form>

	
	</td>
	</tr>
	</table>
<br><br>
	

	
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>