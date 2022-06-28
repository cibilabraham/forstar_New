<?php
	require("include/include.php");
	require_once("lib/invoice_ajax.php");	

	ob_start();
	
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
		
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	if ($accesscontrolObj->canReEdit()) $reEdit=true;
	
	# Checking day's valid pc enabled/disabled
	$validPCEnabled = $manageconfirmObj->pkgValidPCEnabled();
	# MC Packing Conversion type, AC - Auto convert/ MC - Manually Convert
	$LSToMCConversionType = $manageconfirmObj->getLS2MCConversionType();
	//----------------------------------------------------------	
	
	$ON_LOAD_SAJAX 		= "Y"; 

	$offset = 0;
	// $limit  = 25;
	
	$processTypeList = $manageLotIdObj->fetchAllProcessType();
	
	if(isset($p['cmdAdd']))
	{
		if($p['alpha_code_prefix'])
		{
			$alpha_code_prefix =  strtoupper($p['alpha_code_prefix']);
		}
		else
		{
			$alpha_code_prefix = 'LO';
		}
		// $returnValAlpha = $manageLotIdObj->getAlphaPrefix($alpha_code_prefix);
		$lot_id  = '';
		// if(sizeof($returnValAlpha) > 0)
		// {
			// $newCode = explode($alpha_code_prefix.'00',$returnValAlpha[0]);
			// if(sizeof($newCode) == 2)
			// {
				// $lotIdVal  = (int) $newCode[1] + 1;
				// $lot_id    = $alpha_code_prefix.'00'.$lotIdVal;
			// }
			// else
			// {
				// $lot_id  = $alpha_code_prefix.'00'.rand(100,1000);
			// }
		// }
		// else
		// {
			// $lot_id  = $alpha_code_prefix.'001';
		// }
		$lot_id = $alpha_code_prefix.$p['startNo'];
		
		$checkExsists =  $manageLotIdObj->getAlphaPrefix($alpha_code_prefix,$p['startNo'],$p['endNo']);
		
		$dateFrom  = explode('/',$p['idDateFrom']);
		$date_from = $dateFrom[2].'-'.$dateFrom[1].'-'.$dateFrom[0];
		
		$dateTo  = explode('/',$p['idDateTo']);
		$date_to = $dateTo[2].'-'.$dateTo[1].'-'.$dateTo[0];
				
		$insertArray = array('lot_id '           => $lot_id,
							 'date_from'         => $date_from,
							 'date_to'           => $date_to,
							 'number_from'       => $p['startNo'],
							 'number_to'         => $p['endNo'],
							 'process_type'      => $p['process_type'],
							 'alpha_code_prefix' => $alpha_code_prefix
							 );
		 $manageLotIdObj->addLotId($insertArray);
		 $sessObj->createSession("displayMsg",$msgSuccGenerateLotID);
		 $sessObj->createSession("nextPage",$url_afterGenerateLotID.$selection);
	}
	if(isset($p['cmdUpdate']))
	{
		$dateFrom  = explode('/',$p['idDateFrom']);
		$date_from = $dateFrom[2].'-'.$dateFrom[1].'-'.$dateFrom[0];
		
		$dateTo  = explode('/',$p['idDateTo']);
		$date_to = $dateTo[2].'-'.$dateTo[1].'-'.$dateTo[0];
				
		$updateArray = array('date_from'         => $date_from,
							 'date_to'           => $date_to,
							 'number_from'       => $p['startNo'],
							 'number_to'         => $p['endNo'],
							 'process_type'      => $p['process_type']
							 );
		 $id = $p['updateID'];
		 $manageLotIdObj->updateLotId($updateArray,$id);
		 $sessObj->createSession("displayMsg",$msgSuccUpdateLotID);
		 $sessObj->createSession("nextPage",$url_afterGenerateLotID.$selection);
	}
	$editRecords = array();
	if(isset($p['editId']) && $p['editId'] != '')
	{
		$editRecords = $manageLotIdObj->getEditRecords((int)$p['editId']);
		if(sizeof($editRecords) < 1)
		{
			$sessObj->createSession("displayMsg",$msgNoEditGenerateLotID);
			$sessObj->createSession("nextPage",$url_afterGenerateLotID.$selection);
		}
	}
	if ( $p["cmdDelete"]!="") {
		$rowCount	 =	$p["hidRowCount"];
		$delIds = '';
		for ($i=1; $i<=$rowCount; $i++) {
			$recId	=	$p["delId_".$i];

			if ($recId!="") {
				// Need to check the selected Category is link with any other process
				if($delIds == '') { $delIds.= $recId;}
				else { $delIds.= ','.$recId;}
			}
		}
		// echo $delIds;die;
		if ($delIds) {
			$manageLotIdObj->deleteLotId($delIds);
			$sessObj->createSession("displayMsg",$msg_succDelLotID);
			$sessObj->createSession("nextPage",$url_afterGenerateLotID.$selection);
		} else {
			$errDel	=	$msg_failDelIdRestriction;
		}
		$idGenRecDel	=	false;
	}	
	/* List out the records */
	$filterFunctionType = '';
	
	if(isset($p['filterFunctionType']) && $p['filterFunctionType'] != '')
	{
		$filterFunctionType = $p['filterFunctionType'];
	}
	$idGenRecords	 = $manageLotIdObj->fetchAllPagingRecords($offset, $limit,$filterFunctionType);
	$idGenRecordsSize = sizeof($idGenRecords);
	/* ----------------- */
	
	// print_r($idGenRecords);
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	// print_r($p);
?>
    <table width="60%" align="center" cellspacing="0" cellpadding="0">	
    <tbody><tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
	<!--<tr> 
		<td height="10" align="center" style="color:Maroon;" class="listing-item">
			<strong>Challan restrictions for current financial year.</strong>
		</td>
	</tr>-->
	    <tr> 
      <td height="10" align="center" class="err1"> 
              </td>
    </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	<form method="post" action="Managelotid.php" name="frmManagelotid">
	<?php
		if(isset($p['cmdAddNew']))
		{
	?>
	<tr> 
      <td> <table width="80%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; 
                    Generate New Lot ID                  </td>
                </tr>
                <tr> 
                  <td width="1"></td>
                  <td colspan="2"> <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td height="10"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> 
												<input type="button" onclick="cancel('Managelotid.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" value=" Add " class="button" name="cmdAdd">                        </td>
                                              </tr>
                      <input type="hidden" value="" name="hidIPAddressId">
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="10" colspan="2">
			<table width="200">
				<tbody><tr>
					<td nowrap="true" class="fieldName">* Lot Type:</td>
					<td>
						<select id="process_type" name="process_type" required>
							<option value="">-- Choose Process Type --</option>
							<?php
								if(sizeof($processTypeList) > 0)
								{
									foreach($processTypeList as $process)
									{
										$sel = '';
										if($p['process_type'] == $process[0])
										{
											$sel = 'selected = "selected" ';
										}
										echo '<option '.$sel.' value="'.$process[0].'"> '.$process[1].' </option>';
									}
								}
							?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td nowrap="true" class="fieldName">Lot ID Prefix:</td>
					<td>
						<input type="text" id="alpha_code_prefix" name="alpha_code_prefix" />
					</td>
				</tr>
				
				


	<tr>
	<td colspan="2" class="listing-item">
		<table>
		<tbody><tr>
		<td>
			<fieldset><legend class="listing-item">Lot ID Restriction</legend>
			<table>
			<tbody><tr>
				<td nowrap="" class="fieldName">*Date range: From</td>
				<td>
					<input type="text" autocomplete="off" value="" size="9" id="idDateFrom" name="idDateFrom" required>
				</td>
				<td nowrap="" class="fieldName">To</td>
				<td>
					<input type="text" autocomplete="off" value="" size="9" id="idDateTo" name="idDateTo" required>
				</td>
			</tr>
			<tr>
				<td nowrap="" class="fieldName">*Number accepted: From</td>
				<td><input type="text" maxlength="10" value="" size="10" name="startNo" required></td>
				<td nowrap="" class="fieldName">To</td>
				<td><input type="text" maxlength="10" value="" size="10" name="endNo" required></td>
			</tr>
			</tbody></table>
			</fieldset>
		</td>
		</tr>
		</tbody></table>
	</td>
         </tr>	
	
         </tbody></table>
			</td></tr>
                      
                      <tr> 
                        <td height="10"></td>
                      </tr>
                      <tr> 
						<td align="center" colspan="2">
					
						<input type="button" onclick="cancel('Managelotid.php');" value="Cancel" class="button" name="cmdCancel"> 
                          &nbsp;&nbsp; <input type="submit"  value=" Add " class="button" name="cmdAdd">
						  </td>
                                              </tr>
                      <tr> 
                        <td height="10"></td>
						<td height="10" colspan="2"></td>
                      </tr>
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
	 </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	<?php
		}
		if(isset($p['editId']) && $p['editId'] != '')
		{
	?>
	<tr> 
      <td> <table width="80%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; 
                    Update Lot ID                  </td>
                </tr>
                <tr> 
                  <td width="1"></td>
                  <td colspan="2"> <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td height="10"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> 
												<input type="submit" onclick="return cancel('Managelotid.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" value="Update" class="button" name="cmdUpdate">                        </td>
                                              </tr>
                      <input type="hidden" value="" name="hidIPAddressId">
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="10" colspan="2">
			<table width="200">
				<tbody><tr>
					<td nowrap="true" class="fieldName">* Lot Type:</td>
					<td>
						<select id="process_type" name="process_type" required>
							<option value="">-- Choose Process Type --</option>
							<?php
								if(sizeof($processTypeList) > 0)
								{
									foreach($processTypeList as $process)
									{
										$sel = '';
										if($p['process_type'] == $process[0])
										{
											$sel = 'selected = "selected" ';
										}
										else if($editRecords[6] == $process[0])
										{
											$sel = 'selected = "selected" ';
										}
										echo '<option '.$sel.' value="'.$process[0].'"> '.$process[1].' </option>';
									}
								}
							?>
						</select>
					</td>
				</tr>
				
				<!--<tr>
					<td nowrap="true" class="fieldName">Lot ID Prefix:</td>
					<td>
						<input type="text" id="alpha_code_prefix" name="alpha_code_prefix" value="<?php echo $editRecords[7];?>" />
					</td>
				</tr>-->
				
				


	<tr>
	<td colspan="2" class="listing-item">
		<table>
		<tbody><tr>
		<td>
			<fieldset><legend class="listing-item">Lot ID Restriction</legend>
			<table>
			<tbody><tr>
				<td nowrap="" class="fieldName">*Date range: From</td>
				<td>
				<?php
					$dateFrom  = explode('-',$editRecords[2]);
					$date_from = $dateFrom[2].'/'.$dateFrom[1].'/'.$dateFrom[0];
					$dateTo    = explode('-',$editRecords[3]);
					$date_to   = $dateTo[2].'/'.$dateTo[1].'/'.$dateTo[0];
				?>
					<input type="text" autocomplete="off" value="<?php echo $date_from;?>" size="9" id="idDateFrom" name="idDateFrom" required>
				</td>
				<td nowrap="" class="fieldName">To</td>
				<td>
					<input type="text" autocomplete="off" value="<?php echo $date_to;?>" size="9" id="idDateTo" name="idDateTo" required>
				</td>
			</tr>
			<tr>
				<td nowrap="" class="fieldName">*Number accepted: From</td>
				<td><input type="text" value="<?php echo $editRecords[4];?>" maxlength="10" value="" size="10" name="startNo" required></td>
				<td nowrap="" class="fieldName">To</td>
				<td><input type="text" maxlength="10" value="<?php echo $editRecords[5];?>" size="10" name="endNo" required></td>
			</tr>
			</tbody></table>
			</fieldset>
		</td>
		</tr>
		</tbody></table>
	</td>
         </tr>	
	
         </tbody></table>
			</td></tr>
                      
                      <tr> 
                        <td height="10"></td>
                      </tr>
                      <tr> 
                     <td align="center" colspan="2"> 
					 <input type="hidden" name="updateID" id="updateID" value="<?php echo $editRecords[0];?>" />
					 <input type="submit" onclick="return cancel('Managelotid.php');" value=" Cancel " class="button" name="cmdCancel"> 
                          &nbsp;&nbsp; <input type="submit" value="Update" class="button" name="cmdUpdate"></td>
                                              </tr>
                      <tr> 
                        <td height="10"></td>
						<td height="10" colspan="2"></td>
                      </tr>
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
	 </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	<?php
		}
	?>
	</form>
	
	<form method="post" action="Managelotid.php" name="frmManagelotid">
	
    <tr> 
      <td> <table width="80%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName">&nbsp;Generate LotID </td>
                  <td background="images/heading_bg.gif" class="pageName">
			<table align="right" cellspacing="0" cellpadding="0">	
				<tbody><tr>
					<td nowrap="true" class="listing-item">Lot Type:</td>
					<td nowrap="true">
						<!--<select name="filterFunctionType" id="filterFunctionType" onchange="this.form.submit();">-->
						<select onchange="filterLoad(this);" id="filterFunctionType" name="filterFunctionType">
							<option value="">-- Select All--</option>
							<?php
								if(sizeof($processTypeList) > 0)
								{
									foreach($processTypeList as $process)
									{
										$sel = '';
										if($p['filterFunctionType'] == $process[0])
										{
											$sel = 'selected = "selected" ';
										}
										echo '<option '.$sel.' value="'.$process[0].'"> '.$process[1].' </option>';
									}
								}
							?>
						</select>
					</td>
					<td>&nbsp;</td>
				</tr>
			</tbody></table>
		</td>
                </tr>
		<tr> 
                  <td height="10" colspan="3"></td>
                </tr>
		
                <tr> 
                  <td height="10" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td><input type="submit" onclick="return confirmDelete(this.form,'delId_',3);" name="cmdDelete" class="button" value=" Delete ">                           &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                          &nbsp;
						  <!--<input type="button" onclick="return printWindow('PrintManageChallan.php?filterFunctionType=',700,600);" class="button" name="btnPrint" value=" Print ">-->
						  </td>
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                                <tr> 
                  <td width="1"></td>
                  <td style="padding-left:10px;padding-right:10px;" colspan="2"> 
			<table width="90%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="1">
                                            <tbody><tr bgcolor="#f2f2f2" align="center"> 
                        <td width="20" rowspan="2">
				<input type="checkbox" class="chkBox" onclick="checkAll(this.form,'delId_'); " id="CheckAll" name="CheckAll">
			</td>
                        <td nowrap="" rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head"> ALPHA CODE</td>

						  <td nowrap="" rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Lot Type</td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Date</td>
                        <td colspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Number</td>
			 <td rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Updated Date</td>
				                        <td width="50" rowspan="2" class="listing-head"></td>
			                      </tr>
			<tr bgcolor="#f2f2f2" align="center">
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">From</td>
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">To</td>	
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">From</td>
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">To</td>
			</tr>
			<?php
				if($idGenRecordsSize > 0)
				{
					$i = 1;
					foreach($idGenRecords as $row)
					{
			?>
            <tr bgcolor="WHITE"> 
                <td width="20" height="25">
				<input type="checkbox" class="chkBox" value="<?php echo $row[0];?>" id="delId_<?php echo $i;?>" name="delId_<?php echo $i;?>">
				</td>
                <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[7];?>
				</td>
				<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[9];?>
				</td>
				<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[2];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[3];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[4];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[5];?>
				</td>
				<td nowrap="" align="center" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[8];?>
				</td>
			    <td width="40" align="center" class="listing-item">
				<input type="submit" onclick="assignValue(this.form,<?php echo $row[0];?>,'editId'); assignValue(this.form,'<?php echo $i;?>','editSelectionChange');this.form.action='Managelotid.php';" name="cmdEdit" value="Edit">
				</td>
			</tr>
			<?php
					$i++;
					}
				}
				 else {
			?>
                      <tr bgcolor="white"> 
                        <td colspan="9"  class="err1" height="10" align="center"> 
						No records found
                         </td>
                      </tr>
                      	<?php
				}
			?>
                      	                      
                <input type="hidden" value="<?php echo $idGenRecordsSize;?>" id="hidRowCount" name="hidRowCount">
                <input type="hidden" value="" name="editId">
				<input type="hidden" value="0" name="editSelectionChange">
                 </tbody>
				 </table>
				 </td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td><input type="submit" onclick="return confirmDelete(this.form,'delId_',3);" name="cmdDelete" class="button" value=" Delete ">                           &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                          &nbsp;
						  <!--
						  <input type="button" onclick="return printWindow('PrintManageChallan.php?filterFunctionType=',700,600);" class="button" name="btnPrint" value=" Print ">-->
						  </td>
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3">
			<input type="hidden" value="" name="hidRecId">
			<input type="hidden" value="" name="hidFilterFunctionType">
		  </td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
	</form>
    <tr> 
      <td height="10"></td>
    </tr>
  </tbody></table>
	
 	<script type="text/javascript" language="JavaScript">
	Calendar.setup 
	(	
		{
			inputField  : "idDateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	</script>
	<script type="text/javascript" language="JavaScript">
	Calendar.setup 
	(	
		{
			inputField  : "idDateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	function cancel(fileName)
	{
		var con=confirm('Do you want to cancel?');
		if(con==true)
		{
			window.location = fileName;
		}
	}
	</script>

	<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>