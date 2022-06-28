<?
	require("include/include.php");
	
	$err			=	"";
	$errDel			=	"";
	

	$editDisplayRecId		=	"";
	$displayRecordId		=	"";
	$noRec					=	"";
	
	$editMode		=	true;
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
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	
	$doUpdate = true;
	if( $p["cmdSaveChanges"]!="" )
	{	
		$statusPo = "";
		$statusSi = "";
		$rowCount = $p["RowCount"];
		for( $i=1; $i<=$rowCount; $i++ )
		{
			$id = $p["HidId_".$i];
			$startNumber = $p["StartNumber_".$i];
			$endNumber = $p["EndNumber_".$i];
			$currentNumber = $p["CurrentNumber_".$i];
			$type = $p["HidType_".$i];
			$active = $p["Active_".$i];
			$autoGen = ( $p["AutoGen_".$i]=="" ) ? "N" : "Y";
			if( $active=='N' ) $autoGen = 'Y';
			
				
			if( $endNumber < $currentNumber ) 
			{
				$doUpdate = false;
				$err = $msg_failIdRecordUpdate;
			}
			if( $startNumber > $currentNumber ) $currentNumber = $startNumber;

			if( ( $startNumber!="" && $endNumber!="" ) && $doUpdate )
			{
				$update = $idManagerObj->update($startNumber,$endNumber,$currentNumber, $id, $type,$autoGen);
				if( $update ) $sessObj->createSession( "displayMsg",$msg_succIdRecordUpdate );
				else $err = $msg_failIdRecordUpdate;
			}
		}
	}
	
	# Records 
	$idGenRecords 			=	$idManagerObj->getList();

	$ON_LOAD_PRINT_JS	= "libjs/ManageID.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmDisplayRecord" action="ManageID.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="80%">
	<tr>
		<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
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
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;ID Generation</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" >
								<input type="reset" class='button'  name="cmdSaveChanges" value="Reset" onClick="this.form.reset()">&nbsp;
								<input type="submit" class='button'  name="cmdSaveChanges" value="Save Changes" onClick="return ValidateUpdate(this.form);">
								</td>
							</tr>
							<tr>
								<td width="1" ></td>
							  <td colspan="2" Style="padding-top:10px;padding-bottom:10px;" >
									
									<!-- List Items Start -->
									<table cellpadding="0"  width="95%" cellspacing="1" bgcolor="#999999" border="0" align="center">
										<tr bgcolor="#f2f2f2">
											<td class="listing-head" style="padding-left:10px; padding-right:10px;"  align='center'>Function Name</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align='center'>Starting Number</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align='center'>Ending Number</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align='center'>Current Number</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;" align='center'>Auto Generate</td>
										
										</tr>
	<?
	if( sizeof($idGenRecords) > 0 ) {
		$i = 0;
		while( list(,$idr) = each($idGenRecords) ) {
			$i++;
			$id = $idr[0];
			$type = $idr[1];
			$startNumber = $idr[2];
			$endNumber = $idr[3];
			$currentNumber = $idr[4];
			$generate = $idr[5];
			$active = $idr[6];
			$disable = ( $active=='N' ) ? " disabled readonly " : "";
			$chk = ( $generate=='Y' ) ? "checked" : ""; 
			$screenName = ( $type !="" ) ? $autoIdGenFunctions[$type] : "";
	?>
	<tr bgcolor="white" height="24">
		<td class="fieldName"  align='left' Style="padding-left:5px;" ><?=$screenName;?></td>
		<td class="fieldName"  align='center' ><input type="text" name="StartNumber_<?=$i;?>" id="StartNumber_<?=$i;?>" size="12" value="<?=$startNumber;?>" Style="padding-left:2px;"></td>
		<td class="fieldName"  align='center' ><input type="text" name="EndNumber_<?=$i;?>" id="EndNumber_<?=$i;?>" size="12" value="<?=$endNumber;?>" Style="padding-left:2px;" ></td>
		<td class="fieldName"  align='center' ><?=$currentNumber;?></td>
		<td class="fieldName"  align='center' ><input type="checkbox"  class="chkBox" name="AutoGen_<?=$i;?>" id="AutoGen_<?=$i;?>" value="Y" <?=$disable;?> <?=$chk;?> Style="padding-left:2px;" ></td>
		<input type="hidden" name="CurrentNumber_<?=$i;?>" value="<?=$currentNumber;?>">
		<input type="hidden" name="Active_<?=$i;?>" value="<?=$active;?>">
		<input type="hidden" name="HidId_<?=$i;?>" value="<?=$id;?>">
		<input type="hidden" name="HidType_<?=$i;?>" value="<?=$type;?>">
	</tr>
										<?
												}
											}
										?>
										<input type="hidden" value="<?=$i;?>" name="RowCount" id="RowCount" >
									</table>
									<!-- List Items End  -->

							  </td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" Style="padding-bottom:10px;"  >
								<input type="reset" class='button'  name="cmdSaveChanges" value="Reset" onClick="this.form.reset()">&nbsp;
								<input type="submit" class='button'  name="cmdSaveChanges" value="Save Changes" onClick="return ValidateUpdate(this.form);">
								</td>
							</tr>
					  </table>
					</td>
				</tr>
				
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
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
</form>
	<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>
