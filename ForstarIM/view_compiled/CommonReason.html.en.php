<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmCommonReason']->toHtmlnoClose();?>
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
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("Common Reason"));?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason_Original.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCommonReason(document.frmCommonReason);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason_Original.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCommonReason(document.frmCommonReason);">
												</td><?php }?>
											</tr>
											<?php 
if (!isset($this->elements['hidCommonReasonId']->attributes['value'])) {
    $this->elements['hidCommonReasonId']->attributes['value'] = '';
    $this->elements['hidCommonReasonId']->attributes['value'] .=  htmlspecialchars($t->editId);
}
$_attributes_used = array('value');
echo $this->elements['hidCommonReasonId']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidCommonReasonId']->attributes[$_a]);
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
				<TD>
					<table border="0">
						<TR>
						<TD valign="top">
						<table>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Account Type</td>
								<td class="listing-item" align="left">
									<?php echo $this->elements['data[CommonReason][cod]']->toHtml();?>
								</td>
							</tr>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Reason</td>
							<td class="listing-item">
								<?php echo $this->elements['data[CommonReason][reason]']->toHtml();?>
								<?php if ($t->editMode)  {?>
								<?php echo $this->elements['data[CommonReason][id]']->toHtml();?>
								<?php }?>
							</td>
							</tr>	
							<tr>
								<td class="fieldName" nowrap>Check List</td>
								<td>
									<?php echo $this->elements['data[CommonReason][check_point]']->toHtml();?> &nbsp;&nbsp;<span class="fieldName" style="vertical-align:middle; line-height:normal"><font size="1">(If Yes, please give tick mark)</font></span>
								</td>
							</tr>						
						</table>
						</TD>						
						</TR>
<tr id="chkPointRow">
		<TD style="padding-left:10px;padding-right:10px;" colspan="2" align="center">
			<table>
				<TR>
					<TD>
							<table cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblCheckList" class="newspaperType">
								<tr align="center">
									<th nowrap style="text-align:center;">Check List</th>
									<th nowrap>Required</th>	
									<th>&nbsp;</th>
								</tr>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(0));?>
	<?php if ($this->options['strict'] || (is_array($t->chkListRecs)  || is_object($t->chkListRecs))) foreach($t->chkListRecs as $key => $clr) {?>		
<tr align="center" class="whiteRow" id="row_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>">
	<td align="center" class="listing-item">
		<input type="text" autocomplete="off" size="38" value="<?php echo htmlspecialchars($clr->name);?>" id="chkListName_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="chkListName_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
	</td>
	<td align="center" class="listing-item">
		<?php 
if (!isset($this->elements['required_%s']->attributes['id'])) {
    $this->elements['required_%s']->attributes['id'] = '';
    $this->elements['required_%s']->attributes['id'] .= "required_";
    $this->elements['required_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['required_%s'],
                    isset($key) && isset($this->elements[sprintf('required_%s',$key)]) ? $this->elements[sprintf('required_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('required_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['required_%s']->attributes[$_a]);
}}
?>
	</td>
	<td align="center" class="listing-item">
		<a onclick="setChkListItemStatus('<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title="" /></a>
		<input type="hidden" value="" id="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
		<input type="hidden" value="N" id="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
		<input type="hidden" value="<?php echo htmlspecialchars($clr->id);?>" id="chkListEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="chkListEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" readonly />
	</td>
 </tr>
	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	</table>
	<!--  Hidden Fields-->
	<?php 
if (!isset($this->elements['hidTableRowCount']->attributes['value'])) {
    $this->elements['hidTableRowCount']->attributes['value'] = '';
    $this->elements['hidTableRowCount']->attributes['value'] .=  htmlspecialchars($t->chkListRecSize);
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
						<a href="###" id='addRow' onclick="javascript:addNewCheckListItem();" class="link1" title="Click here to add new item."><img SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New Check List</a>
					</TD>
				</tr>
			</table>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason_Original.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateCommonReason(document.frmCommonReason);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('CommonReason_Original.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateCommonReason(document.frmCommonReason);">
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
				<td><?php if ($t->del)  {?><input type="submit" value=" Delete " class="button" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?php echo htmlspecialchars($t->comReasonRecSize);?>);"><?php }?>&nbsp;<?php if ($t->add)  {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('CommonReason_Original.php?print=y',700,600);"><?php }?></td>
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
	<?php if ($t->comReasonRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"CommonReason_Original.php?");?>
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
				<thead>
			
	<tr align="center">
		<?php if (!$t->printMode)  {?><th width="20"><?php echo $this->elements['CheckAll']->toHtml();?></th><?php }?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Account Type</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Reason</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Check List</th>
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
	<?php if ($this->options['strict'] || (is_array($t->comReasonRecs)  || is_object($t->comReasonRecs))) foreach($t->comReasonRecs as $icmR) {?>
	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkDefaultEntry'))) if ($t->chkDefaultEntry($icmR->default_entry)) { ?>
	<!--<tr onMouseover="ShowTip('Default Entry');" onMouseout="UnTip();">-->
	<tr title="Default Entry">
	<?php } else {?>
	<tr>
	<?php }?>	
		<?php if (!$t->printMode)  {?><td width="20" align="center">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkDefaultEntry'))) if ($t->chkDefaultEntry($icmR->default_entry)) { ?>
				<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][CommonReason][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="" readonly>
			<?php } else {?>
				<input type="checkbox" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][CommonReason][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($icmR->id);?>" class="chkBox">
			<?php }?>

		</td><?php }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getACType'))) echo htmlspecialchars($t->getACType($icmR->cod));?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?php echo htmlspecialchars($icmR->reason);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'displayChkList'))) if ($t->displayChkList($icmR->id)) { ?>
			<a href="###" onMouseover="ShowTip('<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'displayChkList'))) echo htmlspecialchars($t->displayChkList($icmR->id));?>');" onMouseout="UnTip();"><?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkList'))) echo htmlspecialchars($t->chkList($icmR->check_point));?></a>
			<?php } else {?>
				<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkList'))) echo htmlspecialchars($t->chkList($icmR->check_point));?>
			<?php }?>
			
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
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->comReasonRecSize);
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
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"CommonReason_Original.php?");?>
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
    $this->elements['cmdDelete']->attributes['onClick'] .=  htmlspecialchars($t->comReasonRecSize);
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
			function addNewCheckListItem()
			{
				addNewCheckList('tblCheckList','','');		
			}
	showChkPoint();	
		</script>
	<?php }?>
<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showAddRow'))) if ($t->showAddRow()) { ?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewCheckListItem();
	</script>
<?php }?>	
<?php if ($t->chkListRecSize)  {?> 
	<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->chkListRecSize,'fieldId',true);echo (is_object($__tmp) && is_a($__tmp,"PEAR_Error")) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
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