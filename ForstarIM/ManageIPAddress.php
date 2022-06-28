<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if(!$accesscontrolObj->canAccess())
			{ 
				//echo "ACCESS DENIED";
				header ("Location: ErrorPage.php");
				die();	
			}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
//----------------------------------------------------------
	
	# Add Category Start 
	if( $p["cmdAddNew"]!="" ){
		$addMode	=	true;
	}
	
	
	if( $p["cmdAdd"]!="" ){
		
		
		
		$selIP			=	$p["selIP"];	
		if($selIP=='F'){
			$ipAddressFrom 	=	addSlash(trim($p["ipAddress"]));
		}
		else {
			$ipAddressFrom 	=	addSlash(trim($p["ipAddressFrom"]));
			$ipAddressTo 	=	addSlash(trim($p["ipAddressTo"]));
		}
		
		$description	=	addSlash($p["description"]);
		
		if( $ipAddressFrom!="")
		{
	
				$IPAddressRecIns	=	$manageipaddressObj->addIPAddress($ipAddressFrom,$ipAddressTo,$description, $selIP);
				
				if($IPAddressRecIns)
					{
					$sessObj->createSession("displayMsg",$msg_succAddIPAddress);
					$sessObj->createSession("nextPage",$url_afterAddIPAddress);
					}
				else
					{
					$addMode		=	true;
					$err			=	$msg_failAddIPAddress;
					}
				$IPAddressRecIns	=	false;			
		}
			
	}
	

# Edit Address

	if( $p["editId"]!="" ){
		$editId			=	$p["editId"];
		$editMode		=	true;
		
		$ipAddressRec		=	$manageipaddressObj->find($editId);
		$editIPAddressId	=	$ipAddressRec[0];
		$editIPAddressFrom	=	stripSlash($ipAddressRec[1]);
		$editIPAddressTo	=	stripSlash($ipAddressRec[2]);
		$editDescription	=	stripSlash($ipAddressRec[3]);
		$editSelType		=	$ipAddressRec[4];
		
	}
	
	
	if( $p["cmdSaveChange"]!="" ){
		
		$ipAddressId	=	$p["hidIPAddressId"];
		
		$selIP			=	$p["selIP"];	
		if($selIP=='F'){
			$ipAddressFrom 	=	addSlash(trim($p["ipAddress"]));
		}
		else {
			$ipAddressFrom 	=	addSlash(trim($p["ipAddressFrom"]));
			$ipAddressTo 	=	addSlash(trim($p["ipAddressTo"]));
		}
		
		$description	=	addSlash($p["description"]);	
		
		if( $ipAddressFrom!="" && $ipAddressId!="")
		{
	
			$ipAddressRecUptd	=	$manageipaddressObj->updateIPAddress($ipAddressId,$ipAddressFrom,$ipAddressTo,$description, $selIP);
		}
	
		if($ipAddressRecUptd)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateIPAddress);
			$sessObj->createSession("nextPage",$url_afterUpdateIPAddress);
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failUpdateIPAddress;
		}
		$ipAddressRecUptd	=	false;
	}
	
	
	# Delete User
	
	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$ipAddressId	=	$p["delId_".$i];

			if( $ipAddressId!="" )
			{
				
				$ipAddressRecDel		=	$manageipaddressObj->deleteIPAddress($ipAddressId);
				
			}

		}
		if($ipAddressRecDel)
		{
			$sessObj->createSession("displayMsg",$msg_succDelIPAddress);
			$sessObj->createSession("nextPage",$url_afterDelIPAddress);
		}
		else
		{
			$errDel	=	$msg_failDelIPAddress;
		}

		$ipAddressRecDel	=	false;
	}
if( $p["cmdChange"]!=""){
	$ipEnabled	=	($p["ipEnabled"]=="")?0:$p["ipEnabled"];
	$ipAddressEnableUptd	=	$manageipaddressObj->updateIPAddressPrivilege($ipEnabled);
}

$ipEnabled	=	"";
if($manageipaddressObj->isIPEnabled()) $ipEnabled	=	"Checked";
else $msgIPStatus		=	$msg_IPAddressStatus;
#List All IP Address

	$ipAddressRecords			=	$manageipaddressObj->fetchAllRecords();
	$ipAddressRecordsSize			=	sizeof($ipAddressRecords);

	if($editMode)	{
		$heading	=	$label_editIPAddress;
	}
	else{
		$heading	=	$label_addIPAddress;
	}

	//$help_lnk="help/hlp_GradeMaster.html";
	
	if($addMode==true) $ON_LOAD_FN = "return IPAddressSelHide();";
	if($editSelType=='F' && $editMode==true) $ON_LOAD_FN = "return HideRangeIP();";
	if($editSelType=='R' && $editMode==true) $ON_LOAD_FN = "return HideFixedIP();";
	
	$ON_LOAD_PRINT_JS	= "libjs/manageipaddress.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmManageIPAddress" action="ManageIPAddress.php" method="post">

    <table cellspacing="0"  align="center" cellpadding="0" width="60%">
	
    <tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
    <tr> 
      <td height="10" align="center" class="err1" > 
        <? if($err!="" ){?>
        <?=$err;?>
        <?}?>
      </td>
    </tr>
	<tr> 
      <td height="10" align="center" class="err1" > 
        <? if($msgIPStatus!="" ){?>
        <?=$msgIPStatus;?>
        <?}?>
      </td>
    </tr>
    <?
			if( $editMode || $addMode)
			{
		?>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; 
                    <?=$heading;?>
                  </td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdDelCancel" class="button" value=" Cancel " onClick="return cancel('ManageIPAddress.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddIPAddress(document.frmManageIPAddress);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onClick="return cancel('ManageIPAddress.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddIPAddress(document.frmManageIPAddress);">                        </td>
                        <?}?>
                      </tr>
                      <input type="hidden" name="hidIPAddressId" value="<?=$editIPAddressId;?>">
                      
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td  height="10" colspan="2" >
						<table width="200">
                          <tr>
                            <td colspan="2" nowrap class="fieldName">
							<table width="200" align="center">
                              <tr>
                                <td class="fieldName">
								<? 
								$selected ="";
								if($editSelType=='F') $selected = "Checked";
								?>
								<input name="selIP" type="radio" value="F" onclick="ShowFixedIP();" <?=$selected?>> 
                                  Fixed </td>
                                <td class="fieldName">
								<? 
								$selected ="";
								if($editSelType=='R') $selected = "Checked";
								?>
								<input name="selIP" type="radio" value="R" onclick="ShowRangeIP();" <?=$selected?>> 
                                  Range </td>
                              </tr>
                            </table></td>
                            </tr>
                          <tr>
                            <td colspan="2" nowrap class="fieldName">
							<div id="fixedIP" style="display:block">
							<table width="200">
                              <tr>
                                <td class="fieldName" nowrap>* IP Address </td>
                            <td><input name="ipAddress" type="text" id="ipAddress" value="<?=$editIPAddressFrom;?>" size="20" /></td>
                              </tr>
                            </table>
							</div>
							</td>
                            </tr>
                          
                          <tr>
                            <td colspan="2" nowrap class="fieldName">
							<div id="rangeIP" style="display:block">
							<table width="200">
                              <tr>
                                <td class="fieldName" nowrap="nowrap">IP Address: </td>
                                <td class="fieldName">From</td>
								<td><input name="ipAddressFrom" type="text" id="ipAddressFrom" value="<?=$editIPAddressFrom;?>" size="20" /></td>
								<td class="fieldName">To</td>
								<td><input name="ipAddressTo" type="text" id="ipAddressTo" value="<?=$editIPAddressTo;?>" size="20" /></td>
                              </tr>
                            </table>
							</div>							</td>
                            </tr>
                          <tr>
                            <td class="fieldName" nowrap>Description</td>
                            <td><textarea name="description" id="description"><?=$editDescription?></textarea></td>
                          </tr>
                        </table></td>
                      </tr>
                      <? if($editMode==true){?>
                      
					  <? }?>
                      <tr> 
                        <td  height="10" ></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <td align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageIPAddress.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddIPAddress(document.frmManageIPAddress);">                        </td>
                        <?} else{?>
                        <td  colspan="2" align="center"> <input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ManageIPAddress.php');"> 
                          &nbsp;&nbsp; <input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddIPAddress(document.frmManageIPAddress);"></td>
                        <?}?>
                      </tr>
                      <tr> 
                        <td  height="10" ></td>
						<td colspan="2"  height="10" ></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <?
			}
			
			# Listing Grade Starts
		?>
    <tr> 
      <td height="10" align="center" ></td>
    </tr>
    <tr> 
      <td> <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName" >&nbsp;Manage 
                    IP Address </td>
                  <td background="images/heading_bg.gif" class="pageName" ><table cellpadding="0" cellspacing="0" align="right">	
											<tr> 
                        <td class="listing-item" nowrap >&nbsp;</td>
											
                        <td><input name="ipEnabled" type="checkbox" id="ipEnabled" value="1" <?=$ipEnabled?> class="chkBox"></td>
											
                        <td class="listing-item" nowrap>&nbsp;Enable</td>
											
                        <td nowrap>&nbsp;</td>
											
                        <td>&nbsp;<input name="cmdChange" type="submit" class="button" id="cmdChange" value="Change ">
                        &nbsp;</td>
										</tr>
									</table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ipAddressRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageIPAddress.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <?
									if($errDel!="")
									{
								?>
                <tr> 
                  <td colspan="3" height="15" align="center" class="err1"> 
                    <?=$errDel;?>                  </td>
                </tr>
                <?
									}
								?>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                      <?
												if( sizeof($ipAddressRecords) > 0 )
												{
													$i	=	0;
											?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
                        <td class="listing-head" nowrap>IP Address </td>
                        <td class="listing-head" align="center">Description</td>
						<? if($edit==true){?>
                        <td class="listing-head" width="50"></td>
						<? }?>
                      </tr>
                      <?

													foreach($ipAddressRecords as $ipar)
													{
														
														$i++;
														$ipAddressId	=	$ipar[0];
														$IPAddressFrom	=	stripSlash($ipar[1]);
														$IPAddressTo	=	stripSlash($ipar[2]);
														$IPAddress	=	"";
														if($IPAddressTo!=""){
														$IPAddress 	=	$IPAddressFrom."&nbsp;-&nbsp;".$IPAddressTo;
														}
														else {
															$IPAddress 	=	$IPAddressFrom;
														}
														$description	= $ipar[3];
														
											?>
                      <tr  bgcolor="WHITE" > 
                        <td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ipAddressId;?>" ></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$IPAddress;?></td>
                        <td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$description?></td>
						<? if($edit==true){?>
                        <td class="listing-item" align="center" width="40"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ipAddressId;?>,'editId'); assignValue(this.form,'1','editSelectionChange');this.form.action='ManageIPAddress.php';"></td>
						<? }?>
                      </tr>
                      <?
													}
											?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">
					  <input type="hidden" name="editSelectionChange" value="0">
                      <?
												}
												else
												{
											?>
                      <tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center"> 
                          <?=$msgNoRecords;?>                        </td>
                      </tr>
                      <?
												}
											?>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
                <tr > 
                  <td colspan="3"> <table cellpadding="0" cellspacing="0" align="center">
                      <tr> 
                        <td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ipAddressRecordsSize;?>);" > <? }?>
                          &nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?> 
                          &nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintManageIPAddress.php',700,600);"><? }?></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="3" height="5" ></td>
                </tr>
              </table></td>
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