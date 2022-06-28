<?
	require("include/include.php");
	require_once('lib/VarianceReport_ajax.php');
	
	
		$selCompanyName=$p["companyName"];
		$selRMSupplierGroup=$p["selRMSupplierGroup"];
		$supplierRecs 			= $rmProcurmentOrderObj->getfilterSupplierList($selRMSupplierGroup);
		$supplierName=$p["supplierName"];
		$pondName=$p["pondName"];
		$rmlotId=$p["rmlotId"];
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	//$genReqNumber	= "";

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	// if (!$accesscontrolObj->canAccess()) {
		////echo "ACCESS DENIED";
		// header("Location: ErrorPage.php");
		// die();
	// }
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
		
	$companyRecords	= $varianceReportObj->fetchAllCompanyName();
	$supplierGroup	= $varianceReportObj->fetchAllSupplierGroupName();
	$pondNameRecords= $varianceReportObj->fetchAllPondName();
	$LotIdRecords= $varianceReportObj->fetchAllLotId();
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	//$rmLotId	= $unitTransferObj->fetchAllRecords();
	//$comapanyDetails	= $billingCompanyObj->fetchAllRecords();
	
	// $transcationNameRecords	= Array('1'=>'PHT Certificate','2'=>'PHT Monitoring','3'=>'RM Procurment order','4'=>'RM TestData','5'=>'RM Receipt Gate Pass','6'=>'Soaking','7'=>'Unit Transfer','8'=>'Weightment Data Sheet','9'=>'RM Weightment After Grading','10'=>'Pre Processing',
	// '11'=>'Frozen Packing','12'=>'','13'=>'','14'=>'','15'=>'','16'=>'','17'=>'','18'=>'','19'=>'','20'=>'',
	// '21'=>'','22'=>'','23'=>'','24'=>'','25'=>'','26'=>'','27'=>'','28'=>'','29'=>'','30'=>'',
	// '31'=>'','32'=>'','33'=>'','34'=>'','35'=>'','36'=>'','37'=>'','38'=>'','39'=>'','40'=>'');
	
	
	//if ($editMode) $heading	=	$label_editSoaking;
	//else $heading	=	$label_addSoaking;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/VarianceReport.js"; // For Printing JS in Head section

	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmVarianceReport" action="" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											
											<input type="hidden" name="hidSoakingId" value="<?=$editsoakngDataId;?>">
											
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			
				<table width="200" align="center">
							<tr>
								   <td class="fieldName" nowrap>*Company Name:</td>
								 
												<td  height="10" ><select name="companyName" id="companyName" >
											  <option value="">--select--</option>
											
                                         
													
										<?php 
										foreach($companyRecords as $cr)
										{
						$companyId		=	$cr[0];
						$companyName	=	stripSlash($cr[1]);
						$selected="";
						if($selCompanyName==$companyId || $editCompanyNameId==$companyId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$companyId?>" <?=$selected?>><?=$companyName?></option>
                                                    <? }
										
										
										?>
										        </select>										      </td>
								</tr>
					
							<tr>
                                	<td class="fieldName" nowrap align='right'>*RM Supplier Group:&nbsp;</td>
                                        <td class="listing-item">
					<select name="selRMSupplierGroup" id="selRMSupplierGroup" onchange="xajax_getSuplier(document.getElementById('selRMSupplierGroup').value,'');">
                                        <option value="">--select--</option>
                                        
													
										<?php 
										foreach($supplierGroup as $sp)
										{
						$supplierGroupId		=	$sp[0];
						$supplierGroup	=	stripSlash($sp[1]);
						$selected="";
						if($selRMSupplierGroup==$supplierGroupId || $editSupplierGroupId==$supplierGroupId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$supplierGroupId?>" <?=$selected?>><?=$supplierGroup?></option>
                                                    <? }
										
										
										?>
                                                  </select></td>
                                                </tr>
						<tr>
					<td nowrap class="fieldName" >*RM Supplier Name</td>
					<td nowrap>
						<select name="supplierName" id="supplierName" >
						<option value="">-- Select --</option>
						<?php 
										foreach($supplierRecs as $sr)
										{
						$supplierNameId		=	$sr[1];
						$supplierNameVal	=	stripSlash($sr[2]);
						$selected="";
						if($supplierName==$supplierNameId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$supplierNameId?>" <?=$selected?>><?=$supplierNameVal?></option>
                                                    <? }
										
										
										?>
						</select>
					</td>
		</tr>
		
					
		
		<tr>
					<td nowrap class="fieldName" >*Farm Name</td>
					<td nowrap>
					<!--<INPUT TYPE="text" NAME="supplierAddress" id="supplierAddress" size="15" value="<?=$supplierGroupName;?>">	-->
						<select name="pondName" id="pondName">
						<option value="">-- Select --</option>
						<?php 
										foreach($pondNameRecords as $sp)
										{
						$pondNameId		=	$sp[0];
						$pondNameValue	=	stripSlash($sp[1]);
						$selected="";
						if($pondName==$pondNameId || $editPondNameId==$pondNameId) echo $selected="Selected";
					  ?>
                                        <option value="<?=$pondNameId?>" <?=$selected?>><?=$pondNameValue?></option>
                                                    <? }
										
										
										?>
						
						</select>
					</td>
		</tr>
							
							<tr>
							   <td class="fieldName" nowrap>*RM Lot Id:</td>
							   <td  height="10" nowrap><select name="rmlotId" id="rmlotId">
											  <option value="">--select--</option>
											  <?php 
										foreach($LotIdRecords as $LotId)
										{
										$Lots	=	$LotId[0];
										$LotsNo	=	$LotId[1];
										$selected="";
						if($rmlotId==$LotsNo || $editLotNoId==$LotsNo) echo $selected="Selected";
					  ?>
                                        <option value="<?=$LotsNo?>" <?=$selected?>><?=$LotsNo?></option>
                                                    <? }
										
										
										?>
											  
										        </select>									      </td>
							   
							</tr>
												
						  
							
												
							 
												
							
                </table>
								  
											  
											  
											  </td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	

												<td colspan="2" align="center">
												<input type="submit" name="search" class="button" value=" Search" onClick="return validateVarianceReport(document.frmVarianceReport);">&nbsp;&nbsp;
																						</td>
												
												
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	

		
				
		
		<tr>
			<td height="10"></td>
		</tr>
		<?php if(isset($_POST['search']))
		{  	
		$companyName=$p["companyName"];
		$SupplierGroup=$p["selRMSupplierGroup"];
		$supplierName=$p["supplierName"];
		$pondName=$p["pondName"];
		$rmlotId=$p["rmlotId"];
		$varianceRecords= $varianceReportObj->getvarianceReport($companyName,$SupplierGroup,$supplierName,$pondName,$rmlotId);
		//$varianceRecords= $varianceReportObj->getvarianceReport();
		?>
		
		<tr>
			<td align="center"> <h2><?php $company=$varianceReportObj->findCompany($companyName);
											echo $company[0];
										?></h2> </td>
		</tr>
		<tr>
			<td>
			<table width="99%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="0" class="print">
				<tbody>
				<tr bgcolor="#f2f2f2" align="center">
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head" colspan="12">RM Procurement</th>
					 <th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head" colspan="7">RM Gatepass</th>
				</tr>
				
				<tr bgcolor="#f2f2f2" align="center">
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">RM LOT ID</th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Procurement ID</th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Date of Entry</th>
					
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">RM Supplier Address</th>
					
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Farm Details</th>
					
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Vehicle No</th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Driver Name</th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Equipment Name</th>
					
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">No of Equipments Issued</th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Diff </th>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Chemical Name</th>
					
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Qty Issued</th>
					
					
				
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Date of Entry</td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">IN seal no</td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Supplier Challan number</td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Supplier challan date</td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Varified by</td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">Unit name</td>
				</tr>
				
				<?php 
				
				
				foreach($varianceRecords as $varrec)
				{
				?>
				
				<tr bgcolor="#f2f2f2" align="center">
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[3];?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[1];?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[2];?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[10];?></td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[12];?></td>
					
					<?php $entryid=$varrec[0];
					$varianceentryRecords= $varianceReportObj->getvarianceReportentry($entryid);
					
					?>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item">
											<?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[3];
											}	
												}
											?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item">
											<?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[4];
											}	
												}
											?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item">
											<?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[5];
											}	
												}
											?></td>
					
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[0];
											}	
												}
											?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[1];
											}	
												}
											?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[6];
											}	
												}
											?></td>
					
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item">
					<?php 
											if(sizeof($varianceentryRecords))
											{
											foreach($varianceentryRecords as $rec)
											{
													echo $rec[2];
											}	
												}
											?></td>
					
					
				
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[6];?></td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[14];?></td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[4];?></td>
					<td  width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[5];?></td>
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[13];?></td>
					
					<td width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $varrec[15];?></td>
					
				</tr>
				<?php
				}
				?>
		
				
				</tbody>
			</table>
			</td>
		</tr>
		<?php
		}
		?>
	</table>


	
	
	
	

	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>