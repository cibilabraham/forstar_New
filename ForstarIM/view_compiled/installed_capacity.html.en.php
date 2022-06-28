<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmInstalledCapacity']->toHtmlnoClose();?>
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
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("INSTALLED CAPACITY"));?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">
												</td><?php }?>
											</tr>
								<?php 
if (!isset($this->elements['hidInstalledCapacityId']->attributes['value'])) {
    $this->elements['hidInstalledCapacityId']->attributes['value'] = '';
    $this->elements['hidInstalledCapacityId']->attributes['value'] .=  htmlspecialchars($t->editInstalledCapacityId);
}
$_attributes_used = array('value');
echo $this->elements['hidInstalledCapacityId']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidInstalledCapacityId']->attributes[$_a]);
}}
?>
										<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
			<table width="70%" border="0">
				<tr>
				<TD>
					<table>
						<TR>
						<TD valign="top">
						<table>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Machinery</td>
							<td class="listing-item">
								<?php 
if (!isset($this->elements['data[InstalledCapacity][name]']->attributes['onblur'])) {
    $this->elements['data[InstalledCapacity][name]']->attributes['onblur'] = '';
    $this->elements['data[InstalledCapacity][name]']->attributes['onblur'] .= "xajax_chkICExist(document.getElementById('machinery').value, '";
    $this->elements['data[InstalledCapacity][name]']->attributes['onblur'] .=  htmlspecialchars($t->editId);
    $this->elements['data[InstalledCapacity][name]']->attributes['onblur'] .= "');";
}
$_attributes_used = array('onblur');
echo $this->elements['data[InstalledCapacity][name]']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['data[InstalledCapacity][name]']->attributes[$_a]);
}}
?>
								<?php if ($t->editMode)  {?>
								<?php echo $this->elements['data[InstalledCapacity][id]']->toHtml();?>
								<?php }?>
							</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">Description</td>
								<td class="listing-item"><?php echo $this->elements['data[InstalledCapacity][description]']->toHtml();?></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Type of Operation</td>
								<td class="listing-item">
									<?php echo $this->elements['data[InstalledCapacity][operation_type_id]']->toHtml();?>
								</td>
							</tr>
						</table>
						</TD>
						<td>&nbsp;</td>
						<td valign="top">
						<table>
							<tr>
								<TD colspan="2" valign="top">
								<table cellpadding="0" cellspacing="2" width="100%">
								<tr align="center">
									<td class="fieldName" nowrap="nowrap" style="text-align:center; line-height:normal;">*Capacity</td>
									<td class="fieldName" nowrap="nowrap" style="text-align:center; line-height:normal;">*Unit</td>
									<td class="fieldName" nowrap="nowrap" style="text-align:center; line-height:normal;">*Per</td>
								</tr>
								<tr align="center">
									<td nowrap="true">
										<?php echo $this->elements['data[InstalledCapacity][capacity]']->toHtml();?>
									</td>
									<td nowrap="true">
										<?php echo $this->elements['data[InstalledCapacity][unit_id]']->toHtml();?>
									</td>
									<td nowrap class="listing-item" style="padding-left:5px;">
										<?php echo $this->elements['data[InstalledCapacity][per_val]']->toHtml();?>&nbsp;HR
									</td>
								</tr>								
								</table>
								</TD>
							</tr>							
							<tr>
								<td class="fieldName" nowrap="nowrap">*Monitor</td>
								<td class="listing-item">
									<?php echo $this->elements['data[InstalledCapacity][monitor]']->toHtml();?>
								</td>
							</tr>
							<!--<tr>
								<td class="fieldName" nowrap="nowrap">*Monitoring Factors</td>
								<td class="listing-item">
									<select name="data[InstalledCapacity][monitoring_parameter_id]" id="monitoringParameter"></select>
								</td>
							</tr>-->
						</table>
						</td>
						</TR>
					</table>
				</TD>
				</tr>
                        </table>
		</td>
	</tr>
	<tr>
		<TD style="padding-left:10px;padding-right:10px; color: Maroon;" colspan="2" align="center" class="listing-item" nowrap>
			Please confirm that the entries are in sequence.
		</TD>
	</tr>
	<tr>
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
			<table>
				<TR>
				<TD>
					<table cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblMonitorParam" class="newspaperType">
					<tr align="center">
						<th nowrap style="text-align:center;">*Head</th>
						<th nowrap style="text-align:center; line-height:normal;">*Monitoring<br> Factor</th>	
						<th nowrap style="text-align:center;">*Start</th>
						<th nowrap style="text-align:center;">Stop</th>
						<th nowrap style="line-height:normal; text-align:center;">Monitoring <br> Interval<br>(HR)</th>
						<th nowrap style="line-height:normal; text-align:center;" id="seqFlagHCol">Sequential<br>To</th>
						<th>&nbsp;</th>
					</tr>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(0));?>
	<?php if ($this->options['strict'] || (is_array($t->monitorParamRecs)  || is_object($t->monitorParamRecs))) foreach($t->monitorParamRecs as $key => $mpr) {?>		
	<tr align="center" class="whiteRow" id="row_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>">
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
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['smpStart_%s']->attributes['id'])) {
    $this->elements['smpStart_%s']->attributes['id'] = '';
    $this->elements['smpStart_%s']->attributes['id'] .= "smpStart_";
    $this->elements['smpStart_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['smpStart_%s'],
                    isset($key) && isset($this->elements[sprintf('smpStart_%s',$key)]) ? $this->elements[sprintf('smpStart_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('smpStart_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['smpStart_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['smpStop_%s']->attributes['id'])) {
    $this->elements['smpStop_%s']->attributes['id'] = '';
    $this->elements['smpStop_%s']->attributes['id'] .= "smpStop_";
    $this->elements['smpStop_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['smpStop_%s'],
                    isset($key) && isset($this->elements[sprintf('smpStop_%s',$key)]) ? $this->elements[sprintf('smpStop_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('smpStop_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['smpStop_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['monitoringInterval_%s']->attributes['id'])) {
    $this->elements['monitoringInterval_%s']->attributes['id'] = '';
    $this->elements['monitoringInterval_%s']->attributes['id'] .= "monitoringInterval_";
    $this->elements['monitoringInterval_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['monitoringInterval_%s'],
                    isset($key) && isset($this->elements[sprintf('monitoringInterval_%s',$key)]) ? $this->elements[sprintf('monitoringInterval_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('monitoringInterval_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['monitoringInterval_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item" id="seqFlagRCol_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>">
			<?php 
if (!isset($this->elements['seqFlag_%s']->attributes['id'])) {
    $this->elements['seqFlag_%s']->attributes['id'] = '';
    $this->elements['seqFlag_%s']->attributes['id'] .= "seqFlag_";
    $this->elements['seqFlag_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['seqFlag_%s'],
                    isset($key) && isset($this->elements[sprintf('seqFlag_%s',$key)]) ? $this->elements[sprintf('seqFlag_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('seqFlag_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['seqFlag_%s']->attributes[$_a]);
}}
?>
<!-- Sequential To   -->
			<?php 
if (!isset($this->elements['seqMParamId_%s']->attributes['id'])) {
    $this->elements['seqMParamId_%s']->attributes['id'] = '';
    $this->elements['seqMParamId_%s']->attributes['id'] .= "seqMParamId_";
    $this->elements['seqMParamId_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['seqMParamId_%s'],
                    isset($key) && isset($this->elements[sprintf('seqMParamId_%s',$key)]) ? $this->elements[sprintf('seqMParamId_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('seqMParamId_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['seqMParamId_%s']->attributes[$_a]);
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
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>							
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('InstalledCapacity.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateInstalledCapacity(document.frmInstalledCapacity);">
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
				<td><?php if ($t->del)  {?><input type="submit" value=" Delete " class="button" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?php echo htmlspecialchars($t->icmRecSize);?>);"><?php }?>&nbsp;<?php if ($t->add)  {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('InstalledCapacity.php?print=y',700,600);"><?php }?></td>
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
	<?php if ($t->icmRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"InstalledCapacity.php?");?>
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
				<thead>
			
	<tr align="center">
		<?php if (!$t->printMode)  {?><th width="20"><?php echo $this->elements['CheckAll']->toHtml();?></th><?php }?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Machinery</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Description</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Type of Operation</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Capacity</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Per</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Monitor</th>
		<!--<th nowrap style="padding-left:10px; padding-right:10px;">Monitoring<br/>Parameters</th>-->
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
	<?php if ($this->options['strict'] || (is_array($t->icmRecs)  || is_object($t->icmRecs))) foreach($t->icmRecs as $icmR) {?>
	<tr>
		<?php if (!$t->printMode)  {?><td width="20" align="center">
			<input type="checkbox" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][InstalledCapacity][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($icmR->id);?>" class="chkBox">
		</td><?php }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">			
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'displayMonitorParams'))) if ($t->displayMonitorParams($icmR->id)) { ?>
			<a href="###" onMouseover="ShowTip('<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'displayMonitorParams'))) echo htmlspecialchars($t->displayMonitorParams($icmR->id));?>');" onMouseout="UnTip();" class="link5"><?php echo htmlspecialchars($icmR->name);?></a>
			<?php } else {?>
				<?php echo htmlspecialchars($icmR->name);?>
			<?php }?>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->description);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->operationtype);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->capacity);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->unitname);?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->per_val);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getMonitor'))) echo htmlspecialchars($t->getMonitor($icmR->monitor));?></td>
		<!--<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.parameter}</td>-->
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
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	<?php 
if (!isset($this->elements['hidRowCount']->attributes['value'])) {
    $this->elements['hidRowCount']->attributes['value'] = '';
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->icmRecSize);
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
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"InstalledCapacity.php?");?>
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
    $this->elements['cmdDelete']->attributes['onClick'] .=  htmlspecialchars($t->icmRecSize);
    $this->elements['cmdDelete']->attributes['onClick'] .= ");";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdDelete']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdDelete']->attributes[$_a]);
}}
?><?php }?>&nbsp;<?php if ($t->add)  {?><?php echo $this->elements['cmdAddNew']->toHtml();?><?php }?>&nbsp;<?php if ($t->print)  {?><?php echo $this->elements['btnPrint']->toHtml();?><?php }?></td>
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