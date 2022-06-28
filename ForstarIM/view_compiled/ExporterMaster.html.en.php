<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmExporterMaster']->toHtmlnoClose();?>
	<table cellspacing="0" align="center" cellpadding="0" width="96%">
		<?php if ($t->err)  {?><tr>
			<td height="40" align="center" class="err1"><?php echo htmlspecialchars($t->err);?></td>
		</tr><?php }?>
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" align="center" width="100%">
					<tr>
						<td>							
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("Exporter Master"));?>
								<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/boxTL.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>					
							<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="70%" align="center">		
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?><tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" align="center" width="75%">
					<tr>
						<td>			
							
							<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setSubBoxHeader'))) echo htmlspecialchars($t->setSubBoxHeader($t->heading));?>
							<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/rbTop.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>
							<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td width="1"></td>
									<td colspan="2">
										<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10"></td>
											</tr>
											<tr>							
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateExporterMaster(document.frmExporterMaster);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateExporterMaster(document.frmExporterMaster);">
												</td><?php }?>
											</tr>
											<?php 
if (!isset($this->elements['hidExporterMasterId']->attributes['value'])) {
    $this->elements['hidExporterMasterId']->attributes['value'] = '';
    $this->elements['hidExporterMasterId']->attributes['value'] .=  htmlspecialchars($t->editId);
}
$_attributes_used = array('value');
echo $this->elements['hidExporterMasterId']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidExporterMasterId']->attributes[$_a]);
}}
?>
											<?php if ($t->editMode)  {?>
											<?php echo $this->elements['data[Exporter][id]']->toHtml();?>
											<?php }?>
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
			<table>
						<tr>
						<TD nowrap style="padding-left:5px; padding-right:5px;" valign="top">
						<table>
						<TR>
						<TD valign="top">
							<?php			
								$entryHead = "";
								require("template/rbTop.php");
							?>
							<table>
							<tr>
								<td class="fieldName" nowrap>*Name</td>
								<td>
									<?php echo $this->elements['data[Exporter][name]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap>*Address</td>
								<td>
									<?php echo $this->elements['data[Exporter][address]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap>*Place</td>
								<td>
									<?php echo $this->elements['data[Exporter][place]']->toHtml();?>
								</td>				
							</tr>
							<tr>
								<td class="fieldName" nowrap>*Pin Code</td>
								<td>
									<?php echo $this->elements['data[Exporter][pin]']->toHtml();?>
								</td>
							</tr>
							</table>
							<?php
								require("template/rbBottom.php");
							?>
							</TD>
							<td>&nbsp;</td>
						<td valign="top">
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
							<table>
							<tr>
								<td class="fieldName" nowrap>*Country</td>
								<td>
									<?php echo $this->elements['data[Exporter][country]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap>*Tel.No</td>
								<td>
									<?php echo $this->elements['data[Exporter][telno]']->toHtml();?>		
								</td>
							</tr>	
							<tr>
								<td nowrap></td>
								<td class="listing-item" style="line-height:normal;font-size:9px;">
									Eg:0471-2222222
								</td>
							</tr>		
							<tr>
								<td class="fieldName" nowrap>Fax No</td>
								<td>
									<?php echo $this->elements['data[Exporter][faxno]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap title="For display exporter code in invoice">*Alpha Code</td>
								<td>
									<?php echo $this->elements['data[Exporter][alpha_code]']->toHtml();?>
									<?php echo $this->elements['hidAlphaCode']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap>*Display Name</td>
								<td>
									<?php echo $this->elements['data[Exporter][display_name]']->toHtml();?>
								</td>
							</tr>











								</table>
							<?php
								require("template/rbBottom.php");
							?>
						</td>
							</TR>
							</table>
						</TD>
					</tr>







							<tr>
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
			<table>
				<TR>
				<TD>
					<table cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblMonitorParam" class="newspaperType">
					<tr align="center">
						<th nowrap style="text-align:center; line-height:normal;">Unit No</th>	
						<th nowrap style="text-align:center;">*Code</th>	
						
						
						<th>&nbsp;</th>
					</tr>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(0));?>
	<!--{foreach:monitorParamRecs,key,mpr}		-->
	<?php if ($this->options['strict'] || (is_array($t->exporterUnitParamRecs)  || is_object($t->exporterUnitParamRecs))) foreach($t->exporterUnitParamRecs as $key => $mpr) {?>
	<tr align="center" class="whiteRow" id="row_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>">
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['monitoringParamId_%s']->attributes['id'])) {
    $this->elements['monitoringParamId_%s']->attributes['id'] = '';
    $this->elements['monitoringParamId_%s']->attributes['id'] .= "monitoringParamId_";
    $this->elements['monitoringParamId_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['monitoringParamId_%s'],
                    isset($key) && isset($this->elements[sprintf('monitoringParamId_%s',$key)]) ? $this->elements[sprintf('monitoringParamId_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('monitoringParamId_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['monitoringParamId_%s']->attributes[$_a]);
}}
?>
			<!--<select name="data[operation_type_id][unit_id]" id="unitId" style="width:75px;"></select>-->
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['headName_%s']->attributes['value'])) {
    $this->elements['headName_%s']->attributes['value'] = '';
    $this->elements['headName_%s']->attributes['value'] .=  htmlspecialchars($mpr->head_name);
}

if (!isset($this->elements['headName_%s']->attributes['id'])) {
    $this->elements['headName_%s']->attributes['id'] = '';
    $this->elements['headName_%s']->attributes['id'] .= "headName_";
    $this->elements['headName_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('value','id');

                $_element = $this->mergeElement(
                    $this->elements['headName_%s'],
                    isset($key) && isset($this->elements[sprintf('headName_%s',$key)]) ? $this->elements[sprintf('headName_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('headName_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['headName_%s']->attributes[$_a]);
}}
?>
		</td>
		
		
		
		<td align="center" class="listing-item">
			<a onclick="setMParamItemStatus('<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item" />
			</a>
			<input type="hidden" value="" id="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
			<input type="hidden" value="N" id="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
			<input type="hidden" value="<?php echo htmlspecialchars($mpr->id);?>" id="monitoringParamEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="monitoringParamEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" readonly="true" />
			<input type="hidden" value="<?php echo htmlspecialchars($mpr->seq_flag);?>" id="mParamSeqFlag_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="mParamSeqFlag_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" readonly="true" />
		</td>
	</tr>
	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	</table>
	<!--  Hidden Fields-->
	<?php 
if (!isset($this->elements['hidTableRowCount']->attributes['value'])) {
    $this->elements['hidTableRowCount']->attributes['value'] = '';
    $this->elements['hidTableRowCount']->attributes['value'] .=  htmlspecialchars($t->monitorParamRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidTableRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidTableRowCount']->attributes[$_a]);
}}
?>
	</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<a href="###" id='addRow' onclick="javascript:addNewMonitorParamItem();" class="link1" title="Click here to add new item."><img SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New </a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
							





					</table>
					</td>
					</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>							
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateExporterMaster(document.frmExporterMaster);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateExporterMaster(document.frmExporterMaster);">
												</td><?php }?>
											</tr>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/rbBottom.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr><?php }?>			
	</table>
		</td>
			</tr>	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?><tr>
		<td colspan="3" height="10"></td>
	</tr><?php }?>
	
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<?php if ($t->listMode)  {?><tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<?php if (!$t->printMode)  {?><tr>
				<td><?php if ($t->del)  {?><input type="submit" value=" Delete " class="button" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?php echo htmlspecialchars($t->ExporterRecSize);?>);"><?php }?>&nbsp;<?php if ($t->add)  {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('ExporterMaster.php?print=y',700,600);"><?php }?>
				<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showDefaultBtn'))) if ($t->showDefaultBtn()) { ?>
				&nbsp;
				<input type="submit" value=" Make Default " class="button" name="cmdDefault" onClick="return confirmExpMakeDefault('delId_', '<?php echo htmlspecialchars($t->ExporterRecSize);?>');">
				<?php }?>
				</td>
			</tr><?php }?>
			</table>
		</td>
		</tr><?php }?>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<?php if ($t->errDel)  {?><tr>
		<td colspan="3" height="15" align="center" class="err1"><?php echo htmlspecialchars($t->errDel);?></td>
	</tr><?php }?>
	<?php if ($t->listMode)  {?><tr>
				<td width="1"></td>
				<td colspan="2">
				<table cellpadding="1" width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->ExporterRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"ExporterMaster.php?");?>
						
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
				<thead>
			
	<tr align="center">
		<?php if (!$t->printMode)  {?><th width="20"><?php echo $this->elements['CheckAll']->toHtml();?></th><?php }?>
		<th align="center" style="padding-left:10px; padding-right:10px;">Alpha Code</th>
		<th align="center" style="padding-left:10px; padding-right:10px;">Display Name</th>
		<th align="center" style="padding-left:10px; padding-right:10px;">Name</th>
		<th style="padding-left:10px; padding-right:10px;">Address</th>
		<th style="padding-left:10px; padding-right:10px;">Tel. No.</th>
		
		<th style="padding-left:10px; padding-right:10px;">Default</th>
		
		<?php if ($t->edit)  {?>
		<?php if (!$t->printMode)  {?><th width="45">&nbsp;</th><?php }?>
		<?php }?>
		<?php if ($t->edit)  {?>
		<?php if (!$t->printMode)  {?><th width="45">&nbsp;</th><?php }?>
		<?php }?>
	</tr>
	</thead>
	<tbody>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(1));?>
	<?php if ($this->options['strict'] || (is_array($t->ExporterRecs)  || is_object($t->ExporterRecs))) foreach($t->ExporterRecs as $icmR) {?>	
	<tr>
		<?php if (!$t->printMode)  {?><td width="20" align="center">
			<input type="checkbox" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][Exporter][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($icmR->id);?>" class="chkBox">
		</td><?php }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->alpha_code);?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
		<a href="###" onMouseover="ShowTip('<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'displayExporterUnitParams'))) echo htmlspecialchars($t->displayExporterUnitParams($icmR->id));?>');" onMouseout="UnTip();" class="link5">
		<?php echo htmlspecialchars($icmR->display_name);?></a></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->name);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getAddress'))) echo $t->getAddress($icmR->address,$icmR->place,$icmR->pin,$icmR->country);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->telno);?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">			
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showDefaultChk'))) if ($t->showDefaultChk($icmR->default_row)) { ?><img src="images/y.png" /><?php }?>
		</td>
		
		<?php if ($t->edit)  {?>
		  <?php if (!$t->printMode)  {?><td class="listing-item" width="45" align="center"><?php 
if (!isset($this->elements['cmdEdit']->attributes['onClick'])) {
    $this->elements['cmdEdit']->attributes['onClick'] = '';
    $this->elements['cmdEdit']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['cmdEdit']->attributes['onClick'] .=  htmlspecialchars($icmR->id);
    $this->elements['cmdEdit']->attributes['onClick'] .= ",'editId');";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdEdit']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdEdit']->attributes[$_a]);
}}
?>
			</td><?php }?>
		<?php }?>
	<?php if ($t->confirm)  {?>
		 <?php if (!$icmR->active)  {?><td class="listing-item" width="45" align="center"><?php 
if (!isset($this->elements['cmdConfirm']->attributes['onClick'])) {
    $this->elements['cmdConfirm']->attributes['onClick'] = '';
    $this->elements['cmdConfirm']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['cmdConfirm']->attributes['onClick'] .=  htmlspecialchars($icmR->id);
    $this->elements['cmdConfirm']->attributes['onClick'] .= ",'confirmId');";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdConfirm']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdConfirm']->attributes[$_a]);
}}
?>
			</td><?php }?>
		
		 <?php if ($icmR->active)  {?><td class="listing-item" width="45" align="center"><?php 
if (!isset($this->elements['btnRlConfirm']->attributes['onClick'])) {
    $this->elements['btnRlConfirm']->attributes['onClick'] = '';
    $this->elements['btnRlConfirm']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['btnRlConfirm']->attributes['onClick'] .=  htmlspecialchars($icmR->id);
    $this->elements['btnRlConfirm']->attributes['onClick'] .= ",'rlconfirmId');";
}
$_attributes_used = array('onClick');
echo $this->elements['btnRlConfirm']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['btnRlConfirm']->attributes[$_a]);
}}
?>
			</td><?php }?>
			<?php }?>
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	<?php 
if (!isset($this->elements['hidRowCount']->attributes['value'])) {
    $this->elements['hidRowCount']->attributes['value'] = '';
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->ExporterRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidRowCount']->attributes[$_a]);
}}
?>
	<?php echo $this->elements['editId']->toHtml();?>
	<?php echo $this->elements['confirmId']->toHtml();?>
	<?php echo $this->elements['rlconfirmId']->toHtml();?>
	</tbody>
	<?php if ($t->displayNavRow)  {?><tr>
		<td colspan="10" style="padding-right:10px" class="navRow">
			<div align="right">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"ExporterMaster.php?");?>
			</div>
		</td>
	</tr><?php }?>
	<?php } else {?>
	<tr><TD align="center"><?php echo htmlspecialchars($t->msg_NoRecs);?></TD></tr>
	<?php }?>
		</table>
		</td>
								</tr><?php }?>
								<?php if ($t->listMode)  {?><tr>
									<td colspan="3" height="5"></td>
								</tr><?php }?>
								<?php if ($t->listMode)  {?><tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<?php if (!$t->printMode)  {?><tr>
												<td><?php if ($t->del)  {?><?php 
if (!isset($this->elements['cmdDelete']->attributes['onClick'])) {
    $this->elements['cmdDelete']->attributes['onClick'] = '';
    $this->elements['cmdDelete']->attributes['onClick'] .= "return confirmDelete(this.form,'delId_',";
    $this->elements['cmdDelete']->attributes['onClick'] .=  htmlspecialchars($t->ExporterRecSize);
    $this->elements['cmdDelete']->attributes['onClick'] .= ");";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdDelete']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdDelete']->attributes[$_a]);
}}
?><?php }?>&nbsp;<?php if ($t->add)  {?><?php echo $this->elements['cmdAddNew']->toHtml();?><?php }?>&nbsp;<?php if ($t->print)  {?><?php echo $this->elements['btnPrint']->toHtml();?><?php }?>
												<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showDefaultBtn'))) if ($t->showDefaultBtn()) { ?>
												&nbsp;
												<?php 
if (!isset($this->elements['cmdDefault']->attributes['onClick'])) {
    $this->elements['cmdDefault']->attributes['onClick'] = '';
    $this->elements['cmdDefault']->attributes['onClick'] .= "return confirmExpMakeDefault('delId_', '";
    $this->elements['cmdDefault']->attributes['onClick'] .=  htmlspecialchars($t->ExporterRecSize);
    $this->elements['cmdDefault']->attributes['onClick'] .= "');";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdDefault']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdDefault']->attributes[$_a]);
}}
?>
												<?php }?>
												</td>
											</tr><?php }?>
										</table>
									</td>
								</tr><?php }?>
								<tr>
									<td colspan="3" height="5"></td>
								</tr>
							</table>
						<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/boxBR.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>					
						</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td height="10">
			<?php echo $this->elements['entryExist']->toHtml();?>
			<?php 
if (!isset($this->elements['pageNo']->attributes['value'])) {
    $this->elements['pageNo']->attributes['value'] = '';
    $this->elements['pageNo']->attributes['value'] .=  htmlspecialchars($t->pageNo);
}
$_attributes_used = array('value');
echo $this->elements['pageNo']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['pageNo']->attributes[$_a]);
}}
?> 
			</td>
		</tr>	
</table>

<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			function addNewMonitorParamItem()
			{
				addNewMonitorParam('tblMonitorParam','','');		
			}
			// Check valid parameter
			validParam();
		</script>
<?php }?>
<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showAddRow'))) if ($t->showAddRow()) { ?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewMonitorParamItem();
	</script>
<?php }?>	
<?php if ($t->monitorParamRecSize)  {?>
	<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->monitorParamRecSize,'fieldId',true);echo (is_object($__tmp) && is_a($__tmp,"PEAR_Error")) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
</script>
<?php }?>
</form>

<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/bottomR.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

