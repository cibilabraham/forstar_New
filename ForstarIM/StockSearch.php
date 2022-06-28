<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;



	$selSupplierId		=	$p["selSupplier"];
	
	if($p["selSupplier"]!=""){
		$SupplierStockRecs =	$stocksearchObj->fetchSupplierStockRecords($selSupplierId);
	}


# List all Supplier
	//$supplierRecords	=	$supplierMasterObj->fetchAllRecords("INV");
	$supplierRecords	=	$supplierMasterObj->fetchAllRecordsActivesupplier("INV");
	if($editMode) { 
		$heading	=	$label_editStockSearch;
	}else{
		$heading	=	$label_addStockSearch;
	}
	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
?>

	<form name="frmStockSearch" action="StockSearch.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="60%"  bgcolor="#D3D3D3">
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
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
                                    <tr>
                                      <? if($addMode){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockSearch.php?selSupplier=<?=$selSupplierId?>',700,600);">
&nbsp;&nbsp; </td>
                                      <?}?>
                                    </tr>
                                    <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" />
                                    <tr>
                                      <td nowrap class="fieldName" colspan="4" >&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td colspan="3" nowrap class="fieldName" ><table align="center">
                                                <tr>
                                                  <td class="fieldName">*Supplier</td>
                                                  <td>
                                                      <select name="selSupplier" onchange="this.form.submit();">
                                                        <option value="">--select--</option>
                                                        <?						  
											  foreach($supplierRecords as $sr)
													{
													$supplierId			=	$sr[0];
													$supplierCode			=	stripSlash($sr[1]);
													$supplierName			=	stripSlash($sr[2]);
													$selected ="";
													if($selSupplierId==$supplierId) $selected="selected";
													?>
                                                        <option value="<?=$supplierId?>" <?=$selected;?>>
                                                        <?=$supplierName?>
                                                        </option>
                                                        <? }?>
                                                    </select></td>
													
                                                </tr>
                                                <!--table Here-->
                                            </table></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" >
									  
									  <table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
									  <?
	
								if( sizeof($SupplierStockRecs)){
								
						?>
                                        <tr bgcolor="#f2f2f2">
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Stock Item </td>
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Quoted Price </td>
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Negotiated Price </td>
                                        </tr>
										<?
										foreach($SupplierStockRecs as $ssr){
										
										$quotedPrice	=	$ssr[3];
										$negoPrice		=	$ssr[4];
										$stock			=	$ssr[5];
										
										
										?>
                                        <tr bgcolor="#FFFFFF">
                                          <td class="listing-item">&nbsp;&nbsp;<?=$stock?></td>
                                          <td class="listing-item" align="right"><?=$quotedPrice?>&nbsp;&nbsp;</td>
                                          <td class="listing-item" align="right"><?=$negoPrice?>&nbsp;&nbsp;</td>
                                        </tr>
                                        <? }
										 }?>
                                      </table>
									  
									  </td>
                                    </tr>
									
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    
                                    <tr>
                                      <? if($addMode){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintStockSearch.php?selSupplier=<?=$selSupplierId?>',700,600);">&nbsp;&nbsp; </td>
                                      <?} ?>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td><!-- Form fields end   --></td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
 <SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "schedule",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule", 
			ifFormat    : "%m/%d/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>