							<table width="35%" align="center" cellpadding="0" cellspacing="0">
							<TR>
							<TD>
										<?php
										$entryHead = "";
										require("template/rbTop.php");
										?>
								<table width="200" align="center">
								<tr>
									<td background="images/heading_bg.gif" class="pageName" align="left" width="100%" colspan="3">&nbsp;IGST</td>
								</tr>
								<tr>
									<td class="fieldName" nowrap>Rate List </td>
									<td>
									<select name="selIGSTRateList" id="selIGSTRateList" onchange="this.form.submit();">
										<option value="">--Select--</option>
<?php
										foreach ($igstRateListRecs as $crl) {
										$rateListId = $crl[0];
										$rateListName = stripSlash($crl[1]);
										$rlStartDate = dateFormat($crl[2]);
										$displayRateList = $rateListName . "&nbsp;(" . $rlStartDate . ")";
										$selected = ($IGSTRateListId == $rateListId) ? "Selected" : "";
?>
											<option value="<?= $rateListId ?>" <?= $selected ?>><?= $displayRateList ?></option>
<? } ?>
										</select>
										</td>
<? if ($del == true && sizeof($igstRateListRecs) > 1) { ?>
										<td>
											<input name="cmdDelIGSTRateList" type="submit" class="button" id="cmdDelIGSTRateList" value="Delete Rate List" title="click here to delete the selected rate list " onclick="return cfmDel();" />
										</td>
<? } ?>
									</tr>
									<tr>
										<td class="fieldName" nowrap>*IGST</td>
										<td class="listing-item" align="left">
										<INPUT NAME="baseIGST" TYPE="text" id="baseIGST" value="<?= $baseIGST; ?>" size="4" style="text-align:right;" onblur="chkCSTChange();" autocomplete="off">&nbsp;%
										<INPUT NAME="hidBaseIGST" TYPE="hidden" id="hidBaseIGST" value="<?= $baseIGST; ?>" size="4" style="text-align:right;" readonly="true">
										</td>
									</tr>
									<tr>
										<td class="fieldName" nowrap="nowrap">Active</td>
										<td nowrap="true" align="left">
										<input name="igstActive" type="checkbox" id="igstActive" value="Y" <?= $igst_Active ?> class="chkBox" onclick="chkCSTChange();">&nbsp;&nbsp;<span style="vertical-align:middle; line-height:normal" class="fieldName"><font size="1">(If Yes, please give tick mark)</font></span>
										<input name="hidIGSTActive" type="hidden" id="hidIGSTActive" value="<?= $igst_Active ?>" readonly="true">
										</td>
									</tr>
									<tr>
										<TD colspan="2">
										<fieldset>
											<legend class="listing-item">Rate List</legend>
											<table>
											<tr>
												<td class="fieldName" nowrap title="Rate list start date" >*Start Date </td>
												<td>
												<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?= ($selStartDate_igst) ? dateFormat($selStartDate_igst) : ""; ?>" size="8" autocomplete="off" <?= $readOnly ?>>
												<INPUT NAME="hidStartDate" TYPE="hidden" id="hidStartDate" value="<?= ($selStartDate_igst) ? dateFormat($selStartDate_igst) : ""; ?>" size="8" autocomplete="off" readonly="true">
												</td>
											</tr>
											</table>
										</fieldset>
										</TD>
									</tr>
						
									<tr>
										<td colspan="3" height="10"></td>
									</tr>
									<tr>
								<? if ($editMode) {
?>
											<td colspan="3" align="center">
												<? if ($edit == true && $isAdmin == true) { ?>&nbsp;&nbsp;
												<input type="submit" name="cmdIGSTSaveChange" class="button" value=" Save GST Changes " onclick=" return validateTaxMaster_gst(document.frmTaxMaster);" ><? } ?>
												&nbsp;<input type="button" name="cmdCancelTax" id="cmdCancelTax" class="button" value=" cancel " onclick="cancelTax();" />
												</td>
<? } else { ?>
											<td align="center">&nbsp;&nbsp;</td>
<? } ?>
										</tr>
										<tr>
											<td colspan="3" height="10" align="center"></td>
										</tr>
										</table>
<?php
										require("template/rbBottom.php");
?>
							</TD>
							</TR>
						</table>