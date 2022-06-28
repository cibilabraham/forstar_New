<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmRoleMaster']->toHtmlnoClose();?>
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
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("ROLE MASTER"));?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RoleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRoleMaster(document.frmRoleMaster);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RoleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRoleMaster(document.frmRoleMaster);">
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
			<table width="70%" border="0">
				<tr>
				<TD align="center">
					<table>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Name</td>
							<td class="listing-item">
								<?php 
if (!isset($this->elements['data[m_role][name]']->attributes['onblur'])) {
    $this->elements['data[m_role][name]']->attributes['onblur'] = '';
    $this->elements['data[m_role][name]']->attributes['onblur'] .= "xajax_chkRecExist(document.getElementById('name').value, '";
    $this->elements['data[m_role][name]']->attributes['onblur'] .=  htmlspecialchars($t->editId);
    $this->elements['data[m_role][name]']->attributes['onblur'] .= "');";
}
$_attributes_used = array('onblur');
echo $this->elements['data[m_role][name]']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['data[m_role][name]']->attributes[$_a]);
}}
?>
								<?php if ($t->editMode)  {?>
								<?php echo $this->elements['data[m_role][id]']->toHtml();?>
								<?php }?>
							</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">Description</td>
								<td class="listing-item"><?php echo $this->elements['data[m_role][description]']->toHtml();?></td>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RoleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRoleMaster(document.frmRoleMaster);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RoleMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRoleMaster(document.frmRoleMaster);">
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
				<td><?php if ($t->del)  {?><input type="submit" value=" Delete " class="button" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?php echo htmlspecialchars($t->icmRecSize);?>);"><?php }?>&nbsp;<?php if ($t->add)  {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('RoleMaster.php?print=y',700,600);"><?php }?></td>
			</tr><?php }?>
			</table>
		</td>
		</tr><?php }?>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<?php if ($t->errDel)  {?><tr>
		<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
	</tr><?php }?>
	<?php if ($t->listMode)  {?><tr>
				<td width="1"></td>
				<td colspan="2">
				<table cellpadding="1" width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->icmRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"RoleMaster.php?");?>
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
	<thead>
	<tr align="center">
		<?php if (!$t->printMode)  {?><th width="20"><?php echo $this->elements['CheckAll']->toHtml();?></th><?php }?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Name</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Description</th>		
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
			<input type="checkbox" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][m_role][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($icmR->id);?>" class="chkBox">
		</td><?php }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->name);?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($icmR->description);?></td>
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
	</tbody>
	<?php if ($t->displayNavRow)  {?><tr>
		<td colspan="10" style="padding-right:10px" class="navRow">
			<div align="right">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"RoleMaster.php?");?>
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

